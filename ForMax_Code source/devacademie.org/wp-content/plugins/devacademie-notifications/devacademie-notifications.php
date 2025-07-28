<?php
/*
Plugin Name: DevAcademie – Notifications Moodle (popup)
Description: Point rouge “Profil” : (1) note + feedback publiés ; (2) création/modif devoir ou quiz ; (3) mise à jour du cours. Le point disparaît après lecture.
Version: 3.1.1
Author: DevAcademie
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/*===============================================================
A) Vérifier périodiquement les notes+feedbacks (module assign)
================================================================*/
function da_check_moodle_grades() {

    if ( ! is_user_logged_in() ) return;

    $uid_wp = get_current_user_id();
    $email  = wp_get_current_user()->user_email;
    if ( ! $email ) return;

    $endpoint = 'https://www.devacademie.org/outlying_formax/webservice/rest/server.php';
    $token    = '8e7dd40bf03a03e3cfff31cb5ed0c0b4';

    $res = wp_remote_get( add_query_arg( [
        'wstoken' => $token,
        'wsfunction' => 'core_user_get_users_by_field',
        'field' => 'email',
        'values[0]' => $email,
        'moodlewsrestformat' => 'json',
    ], $endpoint ) );
    if ( is_wp_error( $res ) ) return;
    $body = json_decode( wp_remote_retrieve_body( $res ), true );
    if ( empty( $body[0]['id'] ) ) return;
    $uid_moodle = (int) $body[0]['id'];

    $res = wp_remote_get( add_query_arg( [
        'wstoken' => $token,
        'wsfunction' => 'core_enrol_get_users_courses',
        'userid' => $uid_moodle,
        'moodlewsrestformat' => 'json',
    ], $endpoint ) );
    if ( is_wp_error( $res ) ) return;
    $courses = json_decode( wp_remote_retrieve_body( $res ), true );
    if ( empty( $courses ) ) return;

    $current = [];
    foreach ( $courses as $c ) {
        $cid = $c['id'];
        $cname = $c['fullname'];

        $res = wp_remote_get( add_query_arg( [
            'wstoken' => $token,
            'wsfunction' => 'gradereport_user_get_grade_items',
            'userid' => $uid_moodle,
            'courseid' => $cid,
            'moodlewsrestformat' => 'json',
        ], $endpoint ) );
        if ( is_wp_error( $res ) ) continue;
        $data = json_decode( wp_remote_retrieve_body( $res ), true );
        if ( empty( $data['usergrades'][0]['gradeitems'] ) ) continue;

        foreach ( $data['usergrades'][0]['gradeitems'] as $it ) {
            if ( ( $it['itemmodule'] ?? '' ) !== 'assign' ) continue;
            $iname = $it['itemname'] ?? '';
            if ( $iname === '' ) continue;
            $current[ $cname ][ $iname ] = [
                'g' => trim( $it['gradeformatted'] ?? '' ),
                'f' => trim( $it['feedback']       ?? '' ),
            ];
        }
    }
    if ( empty( $current ) ) return;

    $old = get_user_meta( $uid_wp, 'moodle_saved_grades_data', true );
    $first = ! is_array( $old ) || empty( $old );
    if ( $first ) {
        update_user_meta( $uid_wp, 'moodle_saved_grades_data', $current );
        return;
    }

    $notifications = get_user_meta( $uid_wp, 'moodle_grade_feedback_notifications', true );
    if ( ! is_array( $notifications ) ) $notifications = [];

    foreach ( $current as $cours => $items ) {
        $old_items = $old[ $cours ] ?? [];
        foreach ( $items as $iname => $v ) {
            $ng = $v['g'];   $nf = $v['f'];
            $og = $old_items[ $iname ]['g'] ?? '';
            $of = $old_items[ $iname ]['f'] ?? '';

            if ( $ng !== '' && $ng !== '-' && $nf !== '' && ( $ng !== $og || $nf !== $of ) ) {
                $notifications[] = "Note et feedback publiés dans « {$cours} » ( $iname )";
                break;
            }
        }
    }

    if ( $notifications ) {
        update_user_meta( $uid_wp, 'moodle_grade_feedback_notifications', array_values( array_unique( $notifications ) ) );
    }

    update_user_meta( $uid_wp, 'moodle_saved_grades_data', $current );
}
add_action( 'wp', 'da_check_moodle_grades' );

/*===============================================================
B) Endpoint REST pour les événements (assign/quiz/course)
================================================================*/
add_action( 'rest_api_init', function () {

    register_rest_route( 'moodle/v1', '/event', [
        'methods'  => 'POST',
        'permission_callback' => '__return_true',
        'callback' => function ( WP_REST_Request $req ) {

            $course_name = sanitize_text_field( $req->get_param( 'course_name' ) );
            $event_type  = sanitize_text_field( $req->get_param( 'event_type' ) );

            if ( ! $course_name || ! $event_type ) {
                return new WP_REST_Response( [ 'error' => 'params manquants' ], 400 );
            }

            switch ( $event_type ) {
                case 'assign_created':
                    $msg = "Devoir créé dans « {$course_name} »";
                    break;
                case 'assign_updated':
                    $msg = "Devoir modifié dans « {$course_name} »";
                    break;
                case 'quiz_created':
                    $msg = "Quiz créé dans « {$course_name} »";
                    break;
                case 'quiz_updated':
                    $msg = "Quiz modifié dans « {$course_name} »";
                    break;
                default:
                    $msg = "Cours « {$course_name} » mis à jour";
            }

            $wp_course = get_page_by_title( $course_name, OBJECT, 'cours' );
            if ( ! $wp_course ) return new WP_REST_Response( [ 'error' => 'cours WP introuvable' ], 404 );

            $students = get_users( [
                'meta_key'     => 'cours_inscrit',
                'meta_value'   => '"' . $wp_course->ID . '"',
                'meta_compare' => 'LIKE',
                'fields'       => 'ID',
            ] );

            foreach ( $students as $uid ) {
                $notifs = get_user_meta( $uid, 'moodle_grade_feedback_notifications', true );
                if ( ! is_array( $notifs ) ) $notifs = [];
                $notifs[] = $msg;
                update_user_meta( $uid, 'moodle_grade_feedback_notifications', array_values( array_unique( $notifs ) ) );
            }

            return [ 'ok' => true, 'affected' => count( $students ) ];
        },
    ] );
} );

/*===============================================================
C) Ajouter id="menu-profil-link" au lien « Profil »
================================================================*/
add_filter( 'nav_menu_link_attributes', function ( $atts, $item ) {
    if ( strcasecmp( trim( $item->title ), 'Profil' ) === 0 ) {
        $atts['id'] = 'menu-profil-link';
    }
    return $atts;
}, 10, 2 );

/*===============================================================
D) Passer les messages au JS
================================================================*/
add_action( 'wp_enqueue_scripts', function () {
    if ( ! is_user_logged_in() ) return;
    $msgs = get_user_meta( get_current_user_id(), 'moodle_grade_feedback_notifications', true );
    if ( empty( $msgs ) || ! is_array( $msgs ) ) return;

    wp_enqueue_script( 'jquery' );
    wp_add_inline_script( 'jquery', 'window.daNotifMsgs = ' . wp_json_encode( $msgs ) . ';', 'before' );
    wp_add_inline_script( 'jquery', 'window.daNotifAjax = {
        url:"' . admin_url( 'admin-ajax.php' ) . '",
        nonce:"' . wp_create_nonce( "da_clear_notif" ) . '"
    };', 'after' );
});

/*===============================================================
E) AJAX : purge après lecture
================================================================*/
add_action( 'wp_ajax_da_clear_notif', function () {
    if ( ! check_ajax_referer( 'da_clear_notif', 'nonce', false ) ) {
        wp_send_json_error();
    }
    delete_user_meta( get_current_user_id(), 'moodle_grade_feedback_notifications' );
    wp_send_json_success();
});

/*===============================================================
F) Point rouge + popup
================================================================*/
add_action( 'wp_footer', function () {
    if ( ! is_user_logged_in() ) return; ?>
    <style>
        .da-notif-dot{cursor:pointer;position:absolute;top:-4px;right:-8px;width:10px;height:10px;background:red;border-radius:50%;}
        #da-notif-box{position:absolute;z-index:9999;min-width:240px;padding:10px;background:#fff;border:1px solid #ddd;border-radius:6px;box-shadow:0 4px 8px rgba(0,0,0,.1);font-size:14px;}
        #da-notif-box ul{margin:0;padding:0;list-style:none;}
        #da-notif-box li{margin:6px 0;}
    </style>
    <script>
    (function(){
        if(!window.daNotifMsgs || !daNotifMsgs.length) return;

        let dot, link;
        document.addEventListener('DOMContentLoaded', () => {
            link = document.getElementById('menu-profil-link') ||
                   Array.from(document.querySelectorAll('a')).find(a => /profil/i.test(a.textContent));
            if(!link) return;

            link.style.position = 'relative';
            dot = document.createElement('span');
            dot.className = 'da-notif-dot';
            link.appendChild(dot);

            dot.addEventListener('click', e => {
                e.stopPropagation();
                dot.remove();
                purgeServer();
                openPopup();
            });
        });

        function openPopup(){
            if(document.getElementById('da-notif-box')) return;

            const box = document.createElement('div');
            box.id = 'da-notif-box';
            const ul  = document.createElement('ul');
            daNotifMsgs.forEach(m => {
                const li = document.createElement('li');
                li.textContent = m;
                ul.appendChild(li);
            });
            box.appendChild(ul);
            document.body.appendChild(box);

            const r = link.getBoundingClientRect();
            const boxRect = box.getBoundingClientRect();
            const left = r.left + window.scrollX + (r.width - boxRect.width) / 2;
            box.style.top  = (r.bottom + window.scrollY + 8) + 'px';
            box.style.left = Math.max(8, left) + 'px';

            document.addEventListener('click', closePopup, {once:true});
        }

        function closePopup(){
            const box = document.getElementById('da-notif-box');
            if(box) box.remove();
        }

        function purgeServer(){
            fetch(window.daNotifAjax.url,{
                method:'POST',
                headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body:'action=da_clear_notif&nonce='+encodeURIComponent(window.daNotifAjax.nonce)
            });
        }
    })();
    </script>
<?php });
