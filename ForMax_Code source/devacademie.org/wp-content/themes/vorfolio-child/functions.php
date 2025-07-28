<?php
/**
 * Functions file for the Vorfolio Child Theme
 *
 * @package VorfolioChild
 */

// Sécurité : empêcher l'accès direct au fichier
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Quitter si accès direct
}

// ✅ Charger les styles du thème parent et du thème enfant (version corrigée)
function vorfolio_child_enqueue_styles() {
	wp_enqueue_style(
		'vorfolio-style',
		get_template_directory_uri() . '/style.css'
	);

	wp_enqueue_style(
		'vorfolio-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array('vorfolio-style'),
		filemtime(get_stylesheet_directory() . '/style.css')
	);
}
add_action('wp_enqueue_scripts', 'vorfolio_child_enqueue_styles');


// ✅ Shortcode test : [hello_asma_and_nadine]
function shortcode_hello_asma_and_nadine() {
	return '<p style="font-weight:bold; color:#2c3e50;">Hello from outlYing 👋</p>';
}
add_shortcode('hello_asma_and_nadine', 'shortcode_hello_asma_and_nadine');
/**************************************/
// Shortcode affichant l'icône utilisateur avec menu déroulant
function shortcode_icone_utilisateur_devacademie() {
    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        $user_info = get_userdata($user_id);
        $first_letter = strtoupper(substr($user_info->display_name, 0, 1));
        $user_email = $user_info->user_email;

        ob_start();
        ?>
      

        <div class="devacademie-user-menu" tabindex="0" aria-haspopup="true" aria-expanded="false">
            <div class="initiale"><?php echo esc_html($first_letter); ?></div>
            <div class="dropdown" aria-hidden="true">
                <div class="user-email" style="margin-bottom: 8px;"><?php echo esc_html($user_email); ?></div>

                <?php if (in_array('um_admin', $user_info->roles)) : ?>
                    <a href="<?php echo esc_url(site_url('/page/')); ?>">Mon profil</a>
                    <a href="<?php echo esc_url(site_url('/admin_dash/')); ?>">Tableau de bord</a>
                    <a href="<?php echo esc_url(site_url('/cree-user/')); ?>">Utilisateurs</a>
                    <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>">Se déconnecter</a>

                <?php elseif (in_array('um_mentor', $user_info->roles)) : ?>
                    <a href="<?php echo esc_url(site_url('/page/')); ?>">Mon profil</a>
                    <a href="<?php echo esc_url(site_url('/mentor-dashboard/')); ?>">Tableau de bord</a>
                    <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>">Se déconnecter</a>

                <?php elseif (in_array('um_apprenant', $user_info->roles)) : ?>
                    <a href="<?php echo esc_url(site_url('/profil')); ?>" id="menu-profil-link">Profil</a>
				

                    <a href="<?php echo esc_url(site_url('/all-cours/')); ?>">Mes formations</a>
                    <a href="<?php echo esc_url(site_url('/cours_inscrit/')); ?>">Mes cours</a>
                    <a href="<?php echo esc_url(site_url('/cours_terminer/')); ?>">Cours terminés</a>
                    <a href="<?php echo esc_url(site_url('/progres/')); ?>">Progression</a>
                    <a href="<?php echo esc_url(site_url('/mes-certificats/')); ?>">Certificats</a>
                    <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>">Se déconnecter</a>

                <?php else : ?>
                    <a href="<?php echo esc_url(site_url('/profil/')); ?>">Mon espace</a>
                    <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>">Se déconnecter</a>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    return '';
}
add_shortcode( 'icone_profil_devacademie', 'shortcode_icone_utilisateur_devacademie' );

// Ajout du script JS inline pour le menu déroulant, chargé sur le front-end
function devacademie_enqueue_user_menu_script() {
    if ( is_user_logged_in() ) {
        // On ajoute le jQuery, puis le script inline
        wp_enqueue_script('jquery');
        $js_code = <<<JS
jQuery(document).ready(function($) {
    $('.devacademie-user-menu .initiale').click(function(e) {
        e.stopPropagation();
        $(this).siblings('.dropdown').toggle();
    });

    $(document).click(function() {
        $('.devacademie-user-menu .dropdown').hide();
    });

    $('.devacademie-user-menu .dropdown').click(function(e) {
        e.stopPropagation();
    });
});
JS;
        wp_add_inline_script('jquery', $js_code);
    }
}
add_action('wp_enqueue_scripts', 'devacademie_enqueue_user_menu_script');


 /************************************/

// ✅ Shortcode bouton Déconnexion : [deconnexion]
function mon_bouton_deconnexion() {
	if (is_user_logged_in()) {
		echo '<style>
			.deconnexion-button {
				font-family: "Roboto", sans-serif;
				font-size: 16px;
				font-weight: 500;
				background-color: #F48500;
				color: white;
				padding: 8px 30px;
				border-radius: 25px;
				border: none;
				text-decoration: none;
				display: inline-block;
			}
			.deconnexion-button:hover {
				background-color: #F48500;
				color: #fff;
				border: 1px solid #AC197F;
			}
		</style>';
		$logout_url = wp_logout_url(home_url());
		return '<a href="' . $logout_url . '" class="deconnexion-button"><span>Déconnexion</span></a>';
	}
	return '';
}
add_shortcode('deconnexion', 'mon_bouton_deconnexion');


// ✅ API REST : Liste des utilisateurs autorisés
add_action('rest_api_init', function () {
	register_rest_route('devacademie/v1', '/utilisateurs-autorises', array(
		'methods' => 'GET',
		'callback' => 'get_utilisateurs_autorises',
		'permission_callback' => '__return_true'
	));
});

function get_utilisateurs_autorises() {
	$roles = array('administrator', 'mentor', 'formateur', 'teacher', 'student');
	$users = get_users(array('role__in' => $roles));

	$emails = array_map(function($user) {
		return $user->user_email;
	}, $users);

	return $emails;
}


// // ✅ Enregistrement d’un menu personnalisé
// function custom_register_menus() {
// 	register_nav_menu('menu-lateral', __('Menu Latéral'));
// }
// add_action('after_setup_theme', 'custom_register_menus');


// // ✅ Chargement d’un script pour le menu latéral (menu.js)
// function custom_enqueue_menu_script() {
// 	wp_enqueue_script('side-menu', get_stylesheet_directory_uri() . '/menu.js', [], false, true);
// }
// add_action('wp_enqueue_scripts', 'custom_enqueue_menu_script');

  /**
 * Injecte le CSS du menu horizontal directement dans le head
 */
add_action('wp_head', function(){
    echo '<style type="text/css">
        /* Structure du nav */
        .main-navigation {
          background-color: #fff;
          padding: 10px 0;
          text-align: center;
          box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        /* Liste horizontale */
        .main-menu {
          list-style: none;
          margin: 0;
          padding: 0;
    flex-direction: column;  /* ← changement ici */
          justify-content: center;
          gap: 30px;
        }
        /* Liens */
        .main-menu li a {
          display: inline-block;
          padding: 10px 15px;
          color: #333;
          text-decoration: none;
          font-weight: 500;
          transition: color 0.3s;
        }
        /* Effet hover */
        .main-menu li a:hover {
          color: #F48500;
        }
        /* Si tu gardes le drawer, force-le invisible */
        #sideMenu.side-menu,
        #menuToggle,
        #closeBtn {
          display: none !important;
        }
    </style>';
});
$total_cours_moodle = isset($nb_cours) ? $nb_cours : 'N/A';
$last_update_moodle = isset($last_update) ? $last_update : 'Non disponible';
$html = '
<style>
    .gestion-cours-box {
        background: linear-gradient(135deg, #e3f2fd, #ffffff);
        border: 1px solid #bbdefb;
        border-radius: 16px;
        padding: 24px;
        max-width: 600px;
        margin: 20px auto;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        font-family: "Segoe UI", "Roboto", sans-serif;
        color: #333;
    }

    .gestion-cours-box h2 {
        font-size: 24px;
        font-weight: bold;
        color: #000038;
        margin-bottom: 20px;
        border-bottom: 2px solid #90caf9;
        padding-bottom: 10px;
    }

    .gestion-cours-box p {
        font-size: 16px;
        margin: 12px 0;
    }

    .gestion-cours-box strong {
        color: #000038;
    }
</style>

<div class="gestion-cours-box">
    <h2> Cours en gestion</h2>
    <p><strong>Nombre total de cours :</strong> ' . $total_cours_moodle . '</p>
    <p><strong>Dernière mise à jour :</strong> ' . $last_update_moodle . '</p>
</div>';

function cours_en_gestion_shortcode() {
  if ( !is_user_logged_in() ) {
      return "<p>Veuillez vous connecter pour voir vos cours.</p>";
  }

  $user_id = get_current_user_id();

  // Récupérer les cours de l'utilisateur (post type : cours)
  $args = array(
      'post_type'      => 'cours',
      'author'         => $user_id,
      'posts_per_page' => -1,
      'post_status'    => array('publish', 'draft')
  );

  $query = new WP_Query($args);
  $nb_cours = $query->found_posts;

  // Dernière mise à jour locale (WordPress)
  $last_update = '';
  if ($nb_cours > 0) {
      $last_post = wp_list_sort($query->posts, 'post_modified', 'DESC')[0];
      $last_update = date_i18n('d/m/Y H:i', strtotime($last_post->post_modified));
  }

  // =========================
  // Appel API Moodle
  // =========================
  $total_cours_moodle = 'Erreur';
  $last_update_moodle = 'Erreur';

  $response = wp_remote_get("https://www.devacademie.org/outlying_formax/webservice/rest/server.php?wstoken=d9f995bbf743c8be492d0de432fb1750&moodlewsrestformat=json&wsfunction=core_course_get_courses");
  if (!is_wp_error($response)) {
      $body = wp_remote_retrieve_body($response);
      $data = json_decode($body, true);
      
      if (is_array($data)) {
          // ✅ Filtrer les cours visibles uniquement
          $cours_visibles = array_filter($data, function($cours) {
              return isset($cours['visible']) && $cours['visible'] == 1;
          });

          $total_cours_moodle = count($cours_visibles);

          // Trouver la dernière mise à jour parmi les cours visibles
          $derniere_maj = 0;
          foreach ($cours_visibles as $cours) {
              if (isset($cours['timemodified']) && $cours['timemodified'] > $derniere_maj) {
                  $derniere_maj = $cours['timemodified'];
              }
          }

          if ($derniere_maj > 0) {
              $last_update_moodle = date("d/m/Y H:i", $derniere_maj);
          } else {
              $last_update_moodle = "Aucune";
          }
      }
  }

  // =========================
  // HTML à afficher
  // =========================
  $html = '<div class="gestion-cours-box">';
  $html .= '<h2> Cours en gestion</h2>';

 
  // Infos Moodle

  $html .= '<p><strong>Nombre total de cours  :</strong> ' . $total_cours_moodle . '</p>';
  $html .= '<p><strong>Dernière mise à jour  :</strong> ' . $last_update_moodle . '</p>';

  $html .= '</div>';
  $html = '
  <style>
      .gestion-cours-box {
          background-color: #f9f9f9;
          border: 1px solid #ddd;
          border-radius: 10px;
          padding: 24px;
          max-width: 580px;
          margin: 30px auto;
          font-family: "Segoe UI", "Roboto", sans-serif;
          color: #333;
      }
  
      .gestion-cours-box h2 {
          font-size: 22px;
          font-weight: 600;
          color:#000038;
          margin-bottom: 20px;
          border-bottom: 2px solidrgb(243, 125, 78);
          padding-bottom: 6px;
      }
  
      .gestion-cours-box p {
          font-size: 16px;
          margin: 12px 0;
      }
  
      .gestion-cours-box .label,
      .gestion-cours-box .value {
          font-weight: 600;
          color: #000; /* noir pour texte et valeur */
      }
  </style>
  
  <div class="gestion-cours-box">
      <h2>Cours en gestion</h2>
      <p><span class="label">Nombre total de cours :</span> <span class="value">' . esc_html($total_cours_moodle) . '</span></p>
      <p><span class="label">Dernière mise à jour :</span> <span class="value">' . esc_html($last_update_moodle) . '</span></p>
  </div>';
  
  wp_reset_postdata();
  return $html;
}
add_shortcode('cours_en_gestion', 'cours_en_gestion_shortcode');


add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/set_course_modified', array(
        'methods' => 'POST',
        'callback' => 'custom_set_course_modified',
        'permission_callback' => '__return_true', // autoriser tout le monde (pour les tests)
    ));
});

function custom_set_course_modified(WP_REST_Request $request) {
    $coursename = $request->get_param('course_name');
    
    // Exemple d'enregistrement dans les logs ou autre action
    error_log("Cours modifié reçu depuis Moodle : " . $coursename);

    // Tu peux ici insérer une notification WordPress, une base de données, etc.

    return new WP_REST_Response([
        'success' => true,
        'message' => "Cours reçu : $coursename"
    ], 200);
}

// Shortcode de message de bienvenue pour les formateurs

// Shortcode de message de bienvenue pour les formateurs
function message_bienvenue_formateur() {
    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();
        $message = '
        <style>
            .bienvenue-formateur {
                background-color: #f3e8ff; /* violet clair */
                border-left: 6px solid #320065; /* violet foncé */
                padding: 1rem 1.5rem;
                font-family: "Segoe UI", Roboto, sans-serif;
                font-size: 1.5rem;
                color: #320065;
                border-radius: 8px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.05);
                animation: fadeIn 0.6s ease;
                margin: 1.5rem 0;
            }
            .bienvenue-formateur u {
                text-decoration: underline dotted #320065;
            }
            .connexion-requise {
                background-color: #f3e8ff;
                border-left: 6px solid #320065;
                color: #320065;
                font-size: 1.1rem;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                font-family: "Segoe UI", Roboto, sans-serif;
                box-shadow: 0 2px 6px rgba(0,0,0,0.05);
                animation: fadeIn 0.6s ease;
                margin: 1.5rem 0;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
        <h2 class="bienvenue-formateur">Bienvenue <u>' . esc_html($user->display_name) . '</u> sur votre espace formateur</h2>';
        return $message;
    } else {
        return '
        <style>
            .connexion-requise {
                background-color: #f3e8ff;
                border-left: 6px solid #320065;
                color: #320065;
                font-size: 1.1rem;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                font-family: "Segoe UI", Roboto, sans-serif;
                box-shadow: 0 2px 6px rgba(0,0,0,0.05);
                animation: fadeIn 0.6s ease;
                margin: 1.5rem 0;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
        <p class="connexion-requise">Veuillez vous connecter pour accéder à votre espace formateur.</p>';
    }
}
add_shortcode('message_formateur', 'message_bienvenue_formateur');

// Fonction pour récupérer les devoirs non corrigés



// Fonction pour supprimer les emojis
function remove_emojis($text) {
    return preg_replace('/[\x{1F600}-\x{1F64F}|\x{1F300}-\x{1F5FF}|\x{1F680}-\x{1F6FF}|\x{1F1E0}-\x{1F1FF}|\x{2600}-\x{26FF}|\x{2700}-\x{27BF}]/u', '', $text);
}


// Shortcode WordPress [devoirs_non_corriges]

// Fonction principale pour récupérer les devoirs non corrigés
function get_devoirs_non_corriges_moodle() {
    $token = 'd9f995bbf743c8be492d0de432fb1750';
    $domain = 'https://www.devacademie.org/outlying_formax';
    $url_assignments = "$domain/webservice/rest/server.php?wstoken=$token&moodlewsrestformat=json&wsfunction=mod_assign_get_assignments";
    $response = wp_remote_get($url_assignments);

    if (is_wp_error($response)) {
        return '<div style="color: red;">Erreur de connexion à Moodle</div>';
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    $output = '';

    if (!empty($body['courses'])) {
        $hasUncorrected = false;

        foreach ($body['courses'] as $course) {
            $courseBlock = '';
            $courseHasUncorrected = false;

            foreach ($course['assignments'] as $assignment) {
                $submissions_url = "$domain/webservice/rest/server.php?wstoken=$token&moodlewsrestformat=json&wsfunction=mod_assign_get_submissions&assignmentids[0]=" . $assignment['id'];
                $submissions_response = wp_remote_get($submissions_url);
                $submissions = json_decode(wp_remote_retrieve_body($submissions_response), true);

                $assignmentBlock = '';
                $hasUncorrectedAssignment = false;

                if (!empty($submissions['assignments'][0]['submissions'])) {
                    foreach ($submissions['assignments'][0]['submissions'] as $submission) {
                        if (
                            isset($submission['status']) && $submission['status'] === 'submitted' &&
                            isset($submission['gradingstatus']) && $submission['gradingstatus'] === 'notgraded'
                        ) {
                            $userid = $submission['userid'];
                            $username = get_user_fullname_from_moodle($userid, $token, $domain);

                            $hasUncorrected = true;
                            $courseHasUncorrected = true;
                            $hasUncorrectedAssignment = true;

                            $assignmentBlock .= '<div style="padding: 5px 10px; color: #333; border-left: 3px solid #f48500; margin-bottom: 5px; background-color: #fff8e1; border-radius: 5px;">
                                Soumission non corrigée pour : <strong>' . esc_html($username) . '</strong>
                            </div>';
                        }
                    }
                }

                if ($hasUncorrectedAssignment) {
                    $courseBlock .= '<div style="margin-bottom: 15px; padding: 10px; background-color: #ffffff; border-left: 4px solid #000038; border-radius: 6px;">
                        <h5 style="margin: 0 0 5px; color: #333;">' . esc_html(remove_emojis($assignment['name'])) . '</h5>' . $assignmentBlock . '
                    </div>';
                }
            }

            if ($courseHasUncorrected) {
                $output .= '<div style="margin-bottom: 30px; padding: 15px; background-color: #d9d2e9; border: 1px solid #320065; border-radius: 10px;">
                    <h4 style="color: #000038; margin-bottom: 10px;">Cours : ' . esc_html(remove_emojis($course['fullname'])) . '</h4>' . $courseBlock . '
                </div>';
            }
        }

        if (!$hasUncorrected) {
            $output .= '<p>Aucun devoir à corriger pour le moment.</p>';
        }
    } else {
        $output .= '<p>Aucun devoir trouvé ou accès refusé.</p>';
    }

    return $output;
}

// Récupérer le nom complet d’un utilisateur Moodle
function get_user_fullname_from_moodle($userid, $token, $domain) {
    $url = "$domain/webservice/rest/server.php?wstoken=$token&moodlewsrestformat=json&wsfunction=core_user_get_users_by_field&field=id&values[0]=$userid";
    $response = wp_remote_get($url);
    if (is_wp_error($response)) return "Utilisateur ID $userid";
    $body = json_decode(wp_remote_retrieve_body($response), true);
    return (!empty($body[0])) ? $body[0]['firstname'] . ' ' . $body[0]['lastname'] : "Utilisateur ID $userid";
}

// Enregistrement de l’option avec titre inclus
function save_devoirs_non_corriges() {
    $titre = '<div style="margin-bottom: 20px; text-align: center;">
        <h3 style="color: #000038; font-size: 26px; border-bottom: 3px solid #000038; display: inline-block; padding-bottom: 8px; margin: 0;">
             Devoirs non corrigés
        </h3>
    </div>';

    $assignments = get_devoirs_non_corriges_moodle();
    $full_content = '<div style="padding: 20px; background-color: #f7fafd; border-radius: 10px;">' . $titre . $assignments . '</div>';
    update_option('moodle_ungraded_assignments', $full_content);
}

// Shortcode WordPress
// Shortcode WordPress : affichage en temps réel
function mon_shortcode_notifications() {
    $titre = '<div style="margin-bottom: 20px; text-align: center;">
        <h3 style="color:#000038; font-size: 26px; border-bottom: 3px solid #000038; display: inline-block; padding-bottom: 8px; margin: 0;">
             Devoirs non corrigés
        </h3>
    </div>';

    $assignments = get_devoirs_non_corriges_moodle();
    $full_content = '<div style="padding: 20px; background-color: #f7fafd; border-radius: 10px;">' . $titre . $assignments . '</div>';
    return $full_content;
}
add_shortcode('devoirs_non_corriges', 'mon_shortcode_notifications');


// Forcer la mise à jour avec les paramètres ?forcer_moodle=1 ou ?test_moodle=1
add_action('init', function () {
    if (isset($_GET['forcer_moodle'])) {
        save_devoirs_non_corriges();
        echo 'Données Moodle mises à jour.';
        exit;
    }
    if (isset($_GET['test_moodle'])) {
        echo mon_shortcode_notifications();
        exit;
    }
});










function ajouter_style_certificat_moodle_modernise() {
    ?>
    <style>
		
    h1.entry-title, h1.page-title, h1,
    h3.entry-title, h3.page-title, h3 {

    }

    .certificats-title {
        text-align: center;
        font-size: 32px;
        font-weight: 700;
        color: #002244;
        margin-top: 40px;
        margin-bottom: 35px;
        font-family: 'Segoe UI', sans-serif;
        position: relative;
    }

    .certificats-title::after {
        content: "";
        display: block;
        width: 60px;
        height: 3px;
        background: #f48500;
        margin: 10px auto 0;
        border-radius: 2px;
    }

    .certificats-wrapper {
        max-width: 1200px;
        margin: 0 auto 60px;
        padding: 0 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .moodle-certificate {
        background: #f9fbff;
        padding: 30px 20px;
        border-radius: 20px;
        border-left: 6px solid #f48500;
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        text-align: center;
        font-family: 'Segoe UI', sans-serif;
        position: relative;
    }

    .moodle-certificate:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 30px rgba(0, 0, 0, 0.15);
    }

    .moodle-certificate h2 {
        font-size: 22px;
        color: #002244;
        font-weight: bold;
        margin-bottom: 10px;
        text-transform: uppercase;
        border-bottom: 2px solid #f48500;
        display: inline-block;
        padding-bottom: 4px;
    }

    .certificat-nom {
        font-size: 18px;
        color:  #000038;
        font-weight: bold;
        margin: 12px 0 10px;
    }

    .certificat-nom::after {
        content: "";
        display: block;
        width: 40px;
        height: 2px;
        background: #f48500;
        margin: 10px auto;
        opacity: 0.6;
    }

    .certificat-date {
        font-size: 15px;
        color: #555;
        font-style: italic;
    }
    </style>
    <?php
}
add_action('wp_head', 'ajouter_style_certificat_moodle_modernise');

function ajouter_script_certificat_moodle_modernise() {
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const allDivs = Array.from(document.querySelectorAll("div"));
        const wrapper = document.createElement("div");
        wrapper.className = "certificats-wrapper";

        const title = document.createElement("div");
        title.className = "certificats-title";


        for (let i = 0; i < allDivs.length; i++) {
            const el = allDivs[i];
            if (el.textContent.includes("Nom :")) {
                const dateDiv = allDivs[i + 1];
                if (dateDiv && dateDiv.textContent.includes("Date de création")) {
                    const card = document.createElement("div");
                    card.className = "moodle-certificate";

                    const titre = document.createElement("h2");
                    titre.textContent = "Certificat de Réussite";

                    const nomCertificat = document.createElement("div");
                    nomCertificat.className = "certificat-nom";
                    nomCertificat.innerHTML = el.innerHTML;

                    const dateCertificat = document.createElement("div");
                    dateCertificat.className = "certificat-date";
                    dateCertificat.innerHTML = dateDiv.innerHTML;

                    card.appendChild(titre);
                    card.appendChild(nomCertificat);
                    card.appendChild(dateCertificat);

                    wrapper.appendChild(card);
                    el.remove();
                    dateDiv.remove();
                }
            }
        }

        if (wrapper.children.length > 0) {
            const container = document.querySelector(".wp-site-blocks");
            if (container) {
                container.appendChild(title);
                container.appendChild(wrapper);
            }
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'ajouter_script_certificat_moodle_modernise');

















/******************************* SIRINE CODE ****************************************************/
function redirection_par_role_um() {

    if ( is_admin() || wp_doing_ajax() ) {
        return;
    }

    if ( is_user_logged_in() && is_page('redirection-role') ) {
        $user = wp_get_current_user();
        $roles = $user->roles;

        if ( in_array( 'um_apprenant', $roles ) ) {
            wp_redirect( home_url( '/profil-2' ) );
            exit;
        } elseif ( in_array( 'um_mentor', $roles ) ) {
            wp_redirect( home_url( '/mentor-dashboard' ) );
            exit;
        } elseif ( in_array( 'um_admin', $roles ) ) {
            wp_redirect( home_url( '/admin_dash' ) );
            exit;
        } else {
            wp_redirect( home_url( '/' ) );
            exit;
        }
    }
}
add_shortcode( 'rediriger_role', 'redirection_par_role_um' );

add_action( 'wp_footer', 'remove_facebook_hash' );
function remove_facebook_hash() {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (window.location.hash === '#_=_') {
                if (history.replaceState) {
                    history.replaceState(null, null, window.location.href.split('#')[0]);
                } else {
                    window.location.hash = '';
                }
            }
        });
    </script>
    <?php
}

/******************************* SIRINE CODE ****************************************************/
add_action('wp_enqueue_scripts', function () {
    if (is_user_logged_in()) {
        wp_enqueue_script(
            'notifywp-js',
            plugin_dir_url(__FILE__) . 'notifywp.js',
            [],
            null,
            true
        );

        wp_localize_script('notifywp-js', 'notifywpData', [
            'restUrl' => esc_url_raw(rest_url('notifywp/v1/')),
            'nonce'   => wp_create_nonce('wp_rest')
        ]);
    }
});

// Crée une notification test une fois pour chaque utilisateur connecté
add_action('init', function () {
    if (is_user_logged_in() && !get_user_meta(get_current_user_id(), '_fake_notif_created', true)) {
        $notif = [
            'id' => uniqid('notif_'),
            'message' => '🎉 Bienvenue sur le site ! Ceci est une notification test.',
            'date' => current_time('mysql'),
            'read' => false,
        ];

        $notifications = get_user_meta(get_current_user_id(), '_user_notifications', true);
        if (!is_array($notifications)) {
            $notifications = [];
        }

        $notifications[] = $notif;

        update_user_meta(get_current_user_id(), '_user_notifications', $notifications);
        update_user_meta(get_current_user_id(), '_fake_notif_created', true);

        error_log('Notification test ajoutée pour user ' . get_current_user_id());
    }
});

// Déclare l’API REST
add_action('rest_api_init', function () {
    register_rest_route('notifywp/v1', '/user-notifications', [
        'methods' => 'GET',
        'callback' => function () {
            $user_id = get_current_user_id();
            if (!$user_id) {
                return new WP_REST_Response([], 403);
            }

            $notifs = get_user_meta($user_id, '_user_notifications', true);
            if (!is_array($notifs)) $notifs = [];

            // Retourne uniquement les notifications non lues
            $unread = array_values(array_filter($notifs, fn($n) => empty($n['read'])));

            return new WP_REST_Response($unread, 200);
        },
        'permission_callback' => function () {
            return is_user_logged_in();
        }
    ]);

    register_rest_route('notifywp/v1', '/mark-read', [
        'methods' => 'POST',
        'callback' => function () {
            $user_id = get_current_user_id();
            if (!$user_id) {
                return new WP_REST_Response([], 403);
            }

            $notifs = get_user_meta($user_id, '_user_notifications', true);
            if (!is_array($notifs)) $notifs = [];

            foreach ($notifs as &$notif) {
                $notif['read'] = true;
            }

            update_user_meta($user_id, '_user_notifications', $notifs);
            return new WP_REST_Response(['success' => true], 200);
        },
        'permission_callback' => function () {
            return is_user_logged_in();
        }
    ]);
});