<?php
/*
Plugin Name: DevAcademie – Notifications Moodle (popup)
Description: Affiche un point rouge sur “Profil” UNIQUEMENT quand une nouvelle note ou un nouveau feedback Moodle vient d’être ajouté. Le point disparaît après lecture.
Version: 2.0
Author: DevAcademie
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/*======================================================================
A)  Détecter les nouveautés Moodle et les stocker en meta
======================================================================*/
function da_check_moodle_updates() {

    if ( ! is_user_logged_in() ) return;

    $uid_wp = get_current_user_id();
    $email  = wp_get_current_user()->user_email;
    if ( ! $email ) return;

    /* 1. Config API */
    $endpoint = 'https://www.devacademie.org/outlying_formax/webservice/rest/server.php';
    $token    = '8e7dd40bf03a03e3cfff31cb5ed0c0b4';

    /* 2. ID Moodle via email */
    $res = wp_remote_get( add_query_arg( [
        'wstoken'            => $token,
        'wsfunction'         => 'core_user_get_users_by_field',
        'moodlewsrestformat' => 'json',
        'field'              => 'email',
        'values[0]'          => $email,
    ], $endpoint ) );
    if ( is_wp_error( $res ) ) return;
    $body = json_decode( wp_remote_retrieve_body( $res ), true );
    if ( empty( $body[0]['id'] ) ) return;
    $uid_moodle = (int) $body[0]['id'];

    /* 3. Cours inscrits */
    $res = wp_remote_get( add_query_arg( [
        'wstoken'            => $token,
        'wsfunction'         => 'core_enrol_get_users_courses',
        'moodlewsrestformat' => 'json',
        'userid'             => $uid_moodle,
    ], $endpoint ) );
    if ( is_wp_error( $res ) ) return;
    $courses = json_decode( wp_remote_retrieve_body( $res ), true );
    if ( empty( $courses ) ) return;

    /* 4. Notes & feedbacks actuels */
    $current = [];
    foreach ( $courses as $c ) {
        $cid   = $c['id'];
        $cname = $c['fullname'];

        $res = wp_remote_get( add_query_arg( [
            'wstoken'            => $token,
            'wsfunction'         => 'gradereport_user_get_grade_items',
            'moodlewsrestformat' => 'json',
            'userid'             => $uid_moodle,
            'courseid'           => $cid,
        ], $endpoint ) );
        if ( is_wp_error( $res ) ) continue;

        $data = json_decode( wp_remote_retrieve_body( $res ), true );
        if ( empty( $data['usergrades'][0]['gradeitems'] ) ) continue;

        foreach ( $data['usergrades'][0]['gradeitems'] as $it ) {
            $iname = $it['itemname'];
            $g     = $it['gradeformatted'] ?? '';
            $f     = $it['feedback']       ?? '';
            $current[ $cname ][ $iname ] = [ 'g' => $g, 'f' => $f ];
        }
    }
    if ( empty( $current ) ) return;

    /* 5. Comparaison avec la sauvegarde précédente */
    $old = get_user_meta( $uid_wp, 'moodle_saved_grades_data', true );
    if ( ! is_array( $old ) ) $old = [];

    $new_courses = [];
    foreach ( $current as $cours => $items ) {
        $old_items = $old[ $cours ] ?? [];
        foreach ( $items as $iname => $v ) {
            $old_g = $old_items[ $iname ]['g'] ?? '';
            $old_f = $old_items[ $iname ]['f'] ?? '';
            if ( $v['g'] !== $old_g || $v['f'] !== $old_f ) {
                $new_courses[ $cours ] = $cours;  // un seul enregistrement par cours
                break;
            }
        }
    }

    /* 6. Sauvegarde */
    if ( ! empty( $new_courses ) ) {
        update_user_meta( $uid_wp, 'moodle_grade_feedback_notifications', array_values( $new_courses ) );
    }
    update_user_meta( $uid_wp, 'moodle_saved_grades_data', $current );
}
add_action( 'wp', 'da_check_moodle_updates' );

/*======================================================================
B)  Ajoute id="menu-profil-link" au lien “Profil” dans le menu
======================================================================*/
add_filter( 'nav_menu_link_attributes', function ( $atts, $item ) {
    if ( strcasecmp( trim( $item->title ), 'Profil' ) === 0 ) {
        $atts['id'] = 'menu-profil-link';
    }
    return $atts;
}, 10, 2 );

/*======================================================================
C)  Passe les données côté JS (uniquement si nouveauté)
======================================================================*/
add_action( 'wp_enqueue_scripts', function () {

    if ( ! is_user_logged_in() ) return;

    $courses = get_user_meta( get_current_user_id(), 'moodle_grade_feedback_notifications', true );
    if ( empty( $courses ) ) return; // rien de neuf

    wp_enqueue_script( 'jquery' );

    wp_add_inline_script( 'jquery', 'window.daNotifCourses = ' . wp_json_encode( $courses ) . ';', 'before' );
    wp_add_inline_script( 'jquery', 'window.daNotifAjax = {
        url:"'. admin_url( 'admin-ajax.php' ) .'",
        nonce:"'. wp_create_nonce( "da_clear_notif" ) .'"
    };', 'after' );
});

/*======================================================================
D)  AJAX : purge la méta quand la popup est fermée
======================================================================*/
add_action( 'wp_ajax_da_clear_notif', function () {
    check_ajax_referer( 'da_clear_notif', 'nonce' );
    delete_user_meta( get_current_user_id(), 'moodle_grade_feedback_notifications' );
    wp_send_json_success();
});

/*======================================================================
E)  Point rouge + popup
======================================================================*/
add_action( 'wp_footer', function () {

    if ( ! is_user_logged_in() ) return; ?>
    <style>
        .da-notif-dot{
            cursor:pointer;position:absolute;top:-4px;right:-8px;width:10px;height:10px;
            background:red;border-radius:50%;
        }
        #da-notif-box{
            position:absolute;z-index:9999;min-width:220px;padding:10px;
            background:#fff;border:1px solid #ddd;border-radius:6px;
            box-shadow:0 4px 8px rgba(0,0,0,.1);font-size:14px;
        }
        #da-notif-box ul{margin:0;padding:0;list-style:none;}
        #da-notif-box li{margin:6px 0;}
    </style>
    <script>
    (function(){
        if(!window.daNotifCourses||!daNotifCourses.length) return;

        /* Ajoute le dot rouge une fois le DOM chargé */
        document.addEventListener('DOMContentLoaded',function(){
            const link=document.getElementById('menu-profil-link')||
                        Array.from(document.querySelectorAll('a')).find(a=>/profil/i.test(a.textContent));
            if(!link||link.querySelector('.da-notif-dot')) return;

            link.style.position='relative';
            const dot=document.createElement('span');
            dot.className='da-notif-dot';
            link.appendChild(dot);

            dot.addEventListener('click',function(e){
                e.stopPropagation();
                togglePopup(dot);
            });
        });

        /* ouvre / ferme la popup */
        function togglePopup(dot){
            const existing=document.getElementById('da-notif-box');
            if(existing){
                closePopup(dot);
                return;
            }
            const box=document.createElement('div');
            box.id='da-notif-box';
            const ul=document.createElement('ul');
            daNotifCourses.forEach(c=>{
                const li=document.createElement('li');
                li.textContent='Nouvelle note ou feedback pour « '+c+' »';
                ul.appendChild(li);
            });
            box.appendChild(ul);
            document.body.appendChild(box);

            const r=dot.getBoundingClientRect();
            box.style.top =(r.bottom+window.scrollY+8)+'px';
            box.style.left=(r.left+window.scrollX-100)+'px';

            document.addEventListener('click',function extClick(e){
                if(!box.contains(e.target)){ closePopup(dot); }
            },{once:true});
        }

        /* ferme la popup + retire le dot + purge côté serveur */
        function closePopup(dot){
            const box=document.getElementById('da-notif-box');
            if(box) box.remove();
            if(dot) dot.remove();
            if(window.daNotifAjax){
                fetch(window.daNotifAjax.url,{
                    method:'POST',
                    headers:{'Content-Type':'application/x-www-form-urlencoded'},
                    body:'action=da_clear_notif&nonce='+window.daNotifAjax.nonce
                });
            }
        }
    })();
    </script>
<?php });
