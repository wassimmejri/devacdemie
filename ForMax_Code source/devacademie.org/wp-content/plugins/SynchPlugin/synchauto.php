<?php
/**
 * Plugin Name: ForMax
 * Description: Integration entre WordPress avec Moodle 
 * Version: 1.8
 * Author: Nadine et Asma
 */


if (!defined('ABSPATH')) { 
    exit;
}

define('MOODLE_URL', 'https://www.devacademie.org/outlying_formax'); 
define('MOODLE_API_KEY', '28ed7da14ab096cebe3561dd5dc76613'); 



function user_exists_in_moodle($username, $email)
{
    $url = MOODLE_URL . "/webservice/rest/server.php?wstoken=" . MOODLE_API_KEY . "&wsfunction=core_user_get_users&moodlewsrestformat=json";
    $data = [
        'criteria' => [
            ['key' => 'username', 'value' => $username],
            ['key' => 'email', 'value' => $email]
        ]
    ];

    $response = wp_remote_post($url, [
        'body' => http_build_query($data),
        'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
    ]);

    if (is_wp_error($response)) {
        error_log('Erreur lors de la vérification de l’utilisateur : ' . $response->get_error_message());
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    return !empty($body['users']); 
}




function generate_temp_password()
{
    $password = wp_generate_password(12, true);
    if (!preg_match('/\d/', $password)) {
        $password .= rand(0, 9);
    }
    if (!preg_match('/[\*\-\#\&\!\?]/', $password)) {
        $password .= '*';
    }
    return $password;
}




function send_user_to_moodle($user)
{
    if (!$user || user_exists_in_moodle($user->user_login, $user->user_email)) {
        error_log("Utilisateur déjà existant ou invalide : " . $user->user_login);
        return false;
    }

    $user_pass = generate_temp_password();
    $firstname = !empty($user->first_name) ? $user->first_name : 'Prénom';
    $lastname = !empty($user->last_name) ? $user->last_name : 'Nom';

    $data = [
        'users' => [
            [
                'username' => $user->user_login,
                'password' => $user_pass,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $user->user_email,
                'auth' => 'manual',
                'idnumber' => $user->ID,
            ]
        ]
    ];

   
    error_log('Données envoyées à Moodle : ' . print_r($data, true));

    $url = MOODLE_URL . "/webservice/rest/server.php?wstoken=" . MOODLE_API_KEY . "&wsfunction=core_user_create_users&moodlewsrestformat=json";

    
    $params = [];
    foreach ($data['users'] as $index => $userData) {
        foreach ($userData as $key => $value) {
            $params["users[{$index}][{$key}]"] = $value;
        }
    }

    $response = wp_remote_post($url, [
        'body' => $params,
        'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
    ]);

    if (is_wp_error($response)) {
        error_log('Erreur de synchronisation Moodle : ' . $response->get_error_message());
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    error_log('Réponse Moodle pour ' . $user->user_login . ' : ' . $body);

    $response_data = json_decode($body, true);

    if (isset($response_data[0]['id'])) {
        $moodle_user_id = $response_data[0]['id'];
        update_user_meta($user->ID, 'moodle_user_id', $moodle_user_id);
    } else {
        error_log('Erreur : ID Moodle non retourné');
        return false;
    }

  
    $role = (array) $user->roles;

    if (in_array('um_mentor', $role)) {
        $moodle_role = 2; 

        $assign_role_url = MOODLE_URL . "/webservice/rest/server.php?wstoken=" . MOODLE_API_KEY . "&wsfunction=core_role_assign_roles&moodlewsrestformat=json";

        $role_params = [
            'assignments' => [
                [
                    'roleid' => $moodle_role,
                    'userid' => $moodle_user_id,
                    'contextid' => 1, // contexte système (global)
                ]
            ]
        ];

        error_log('Données d\'assignation de rôle envoyées à Moodle : ' . print_r($role_params, true));

        $assign_role_response = wp_remote_post($assign_role_url, [
            'body' => $role_params,
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
        ]);

        if (is_wp_error($assign_role_response)) {
            error_log('Erreur d\'assignation de rôle Moodle : ' . $assign_role_response->get_error_message());
            return false;
        }

        $assign_role_body = wp_remote_retrieve_body($assign_role_response);
        error_log('Réponse d\'assignation de rôle Moodle pour ' . $user->user_login . ' : ' . $assign_role_body);

        $assign_role_response_data = json_decode($assign_role_body, true);
        if (isset($assign_role_response_data['exception'])) {
            error_log('Erreur d\'assignation de rôle Moodle : ' . $assign_role_response_data['message']);
            return false;
        }
    } else {
        error_log("Aucun rôle Moodle assigné à l'utilisateur " . $user->user_login . " (non mentor)");
    }

    return true;
}












function sync_user_after_approval($user_id)
{
    $user = get_userdata($user_id);
    if ($user) {
        send_user_to_moodle($user);
    }
}
add_action('um_after_user_is_approved', 'sync_user_after_approval');



function enqueue_styles() {
    wp_enqueue_style('icode-style', plugin_dir_url(__FILE__) . 'assets/style.css', array(), '1.0');
    error_log('Feuille de style chargée !'); 
}
add_action('wp_enqueue_scripts', 'enqueue_styles');



//barre de recherche

add_shortcode('barre_recherche_cours', 'moodle_course_search_shortcode');
function moodle_course_search_shortcode() {
    ob_start();
    ?>
    <form id="moodle-course-search-form">
        <input type="text" id="moodle-course-search-keyword" placeholder="Entrez un mot-clé" required>
        <button type="submit">Rechercher</button>
    </form>
    <div id="moodle-course-results"></div>

    <script>
    document.getElementById('moodle-course-search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const keyword = document.getElementById('moodle-course-search-keyword').value.trim();
        if (!keyword) return;

        fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=search_moodle_courses&keyword=' + encodeURIComponent(keyword))
            .then(response => response.text())
            .then(html => {
                document.getElementById('moodle-course-results').innerHTML = html;
            });
    });
    </script>
    <?php
    return ob_get_clean();
}


add_action('wp_ajax_search_moodle_courses', 'search_moodle_courses');
add_action('wp_ajax_nopriv_search_moodle_courses', 'search_moodle_courses');

function search_moodle_courses() {
    $keyword = sanitize_text_field($_GET['keyword']);

    $moodle_url = MOODLE_URL . '/webservice/rest/server.php';
    $params = [
        'wstoken' => MOODLE_API_KEY,
        'wsfunction' => 'core_course_search_courses',
        'moodlewsrestformat' => 'json',
        'criterianame' => 'search',
        'criteriavalue' => $keyword,
    ];

    $response = wp_remote_get($moodle_url . '?' . http_build_query($params));

    if (is_wp_error($response)) {
        echo '<p>Erreur de connexion à Moodle.</p>';
        wp_die();
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($data['courses'])) {
        echo '<p>Aucun cours trouvé pour ce mot-clé.</p>';
    } else {
        $output = '<div class="moodle-courses-grid">';
        foreach ($data['courses'] as $course) {
            $category_name = isset($course['categoryname']) ? esc_html($course['categoryname']) : 'Catégorie inconnue';

            $output .= '
                <div class="course-card">
                    <div class="course-content">
                        <p>' . $category_name . '</p>
                        <h3>' . esc_html($course['fullname']) . '</h3>
                        <p>' . wp_kses_post($course['summary']) . '</p>
                        <a href="#" class="inscription-btn" data-action="inscrire_utilisateur" data-course-id="' . esc_attr($course['id']) . '">Inscrivez-vous</a>
                    </div>
                </div>';
        }
        $output .= '</div>';
        echo $output;
    }

    wp_die();
}
add_action('wp_head', function () {
    ?>
   <style>
/* Formulaire de recherche */
#moodle-course-search-form {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin: 30px auto;
    max-width: 600px;
    padding: 10px;
}

#moodle-course-search-keyword {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid #320065;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

#moodle-course-search-keyword:focus {
    outline: none;
    border-color: #5c2d91;
}

#moodle-course-search-form button {
    background-color: #320065;
    color: white;
    border: none;
    padding: 12px 20px;
    font-size: 16px;
    border-radius: 999px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

#moodle-course-search-form button:hover {
    background-color: #5c2d91;
}

/* Grille des cours */
.moodle-courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    padding: 30px 20px;
}

/* Carte de cours */
.course-card {
    background-color: #fff;
    border: 2px solid #320065;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(50, 0, 101, 0.1);
    transition: transform 0.2s ease, box-shadow 0.3s ease;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 16px rgba(50, 0, 101, 0.2);
}

/* Contenu de la carte */
.course-content h3 {
    margin: 10px 0;
    font-size: 20px;
    color: #320065;
}

.course-content p {
    color: #555;
    font-size: 15px;
    line-height: 1.4;
}

/* Bouton inscription */
.inscription-btn {
    display: inline-block;
    margin-top: 15px;
    background-color: #320065;
    color: #fff;
    padding: 10px 18px;
    border-radius: 999px;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.inscription-btn:hover {
    background-color: #5c2d91;
}

/* Catégories de cours */
.moodle-category-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
    padding: 20px;
}

.moodle-category {
    padding: 8px 16px;
    border-radius: 999px;
    font-weight: bold;
    cursor: pointer;
    background-color: #f5f0fa;
    color: #320065;
    border: 1px solid #320065;
    transition: background-color 0.2s ease, color 0.2s ease;
}

.moodle-category:hover {
    background-color: #320065;
    color: white;
}

/* Zone de résultats */
#moodle-course-results,
#moodle-courses {
    margin: 20px auto;
    max-width: 1200px;
}
</style>

    <?php
});


//category
add_shortcode('moodle_course_browser', 'display_moodle_course_browser');

function display_moodle_course_browser() {
    $categories = get_moodle_categories_data();

    if (is_string($categories)) {
        return $categories; 
    }

    $colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6610f2', '#e83e8c', '#fd7e14', '#20c997', '#6f42c1'];

    $output = '<div class="moodle-category-container">';
	
    foreach ($categories as $category) {
        $color = $colors[array_rand($colors)];
        $style = "border: 2px solid $color; color: $color;";
        $output .= '<span class="moodle-category" data-id="' . esc_attr($category['id']) . '" style="' . esc_attr($style) . '">' . esc_html($category['name']) . '</span>';
    }
    $output .= '</div>';

  
    $output .= '<div id="moodle-courses">' . afficher_cours_moodle() . '</div>';

    $output .= '<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".moodle-category").forEach(function(category) {
                category.addEventListener("click", function(e) {
                    e.preventDefault();
                    const categoryId = this.getAttribute("data-id");
                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", "' . admin_url('admin-ajax.php') . '", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            document.getElementById("moodle-courses").innerHTML = xhr.responseText;
                        } else {
                            console.error("Erreur AJAX: " + xhr.statusText);
                        }
                    };

                    xhr.onerror = function() {
                        console.error("Erreur de connexion AJAX.");
                    };

                    xhr.send("action=get_courses_by_category&category_id=" + categoryId);
                });
            });
        });
    </script>';

    return $output;
}


function get_moodle_categories_data() {
    $url = MOODLE_URL . "/webservice/rest/server.php?wstoken=" . MOODLE_API_KEY . "&wsfunction=core_course_get_categories&moodlewsrestformat=json";
    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        return 'Erreur lors de la récupération des catégories de Moodle';
    }

    $categories = json_decode(wp_remote_retrieve_body($response), true);
    if (!is_array($categories) || empty($categories)) {
        return 'Aucune catégorie trouvée.';
    }

    return $categories;
}


add_action('wp_ajax_get_courses_by_category', 'get_courses_by_category_ajax');

function get_courses_by_category_ajax() {
    if (!isset($_POST['category_id'])) {
        echo '<p>Catégorie invalide.</p>';
        wp_die();
    }

    $categoryid = intval($_POST['category_id']);

    $url = MOODLE_URL . "/webservice/rest/server.php";
    $params = [
        'wstoken' => MOODLE_API_KEY,
        'wsfunction' => 'core_course_get_courses_by_field',
        'moodlewsrestformat' => 'json',
        'field' => 'category',
        'value' => $categoryid
    ];
    $url .= '?' . http_build_query($params);

    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        echo '<p>Erreur de communication avec Moodle.</p>';
        wp_die();
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    $courses = $data['courses'] ?? [];

    if (empty($courses)) {
        echo '<p>Aucun cours trouvé dans cette catégorie.</p>';
        wp_die();
    }

    echo render_moodle_courses_html($courses);
    wp_die();
}


function afficher_cours_moodle() {
    $url = MOODLE_URL . '/webservice/rest/server.php?wstoken=' . MOODLE_API_KEY . '&wsfunction=core_course_get_courses_by_field&moodlewsrestformat=json';
    $response = wp_remote_get($url);
    

    if (is_wp_error($response)) {
        return '<p style="color: red;">Erreur de connexion à Moodle.</p>';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    $courses = $data['courses'] ?? [];

    if (empty($courses)) {
        return '<p>Aucun cours trouvé.</p>';
    }

    return render_moodle_courses_html($courses);
}

function render_moodle_courses_html($courses) {
    $output = '<div class="moodle-courses-grid">';
	

    foreach ($courses as $course) {
        if (!empty($course['id']) && $course['id'] != 1) {
            $course_image = 'https://via.placeholder.com/300x200'; 

          
            if (!empty($course['courseimage'])) {
                $course_image = $course['courseimage'];
            }
            
            elseif (!empty($course['overviewfiles'][0]['fileurl'])) {
                $course_image = $course['overviewfiles'][0]['fileurl'] . '?token=' . MOODLE_API_KEY;
            }

           
            $category_name = !empty($course['categoryname']) ? esc_html($course['categoryname']) : 'Catégorie inconnue';

            $output .= '
                <div class="course-card">
                    <div class="course-image">
                        <img src="' . esc_url($course_image) . '" alt="Image du cours">
                    </div>
                    <div class="course-content">
                        <p>' . $category_name . '</p>
                        <h3>' . esc_html($course['fullname']) . '</h3>
                        <p>' . wp_kses_post($course['summary']) . '</p>
                        <a href="#" class="inscription-btn" data-action="inscrire_utilisateur" data-course-id="' . esc_attr($course['id']) . '">Inscrivez-vous</a>
                    </div>
                </div>';
        }
    }

    $output .= '</div>';

    return $output;
}




add_action('wp_enqueue_scripts', 'enqueue_moodle_browser_script');
function enqueue_moodle_browser_script() {
    wp_enqueue_script(
        'moodle-course-browser',
        plugin_dir_url(__FILE__) . 'assets/code.js',
        [],
        null,
        true
    );

    wp_localize_script('moodle-course-browser', 'moodle_ajax', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
}
















function cours_moodle() {
    $moodle_url = MOODLE_URL . '/webservice/rest/server.php';
    $token = MOODLE_API_KEY;
    $function = 'core_course_get_courses_by_field';

    $url = "$moodle_url?wstoken=$token&wsfunction=$function&moodlewsrestformat=json";

    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        return '<p style="color: red;">Erreur de connexion à Moodle.</p>';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);


    if (empty($data['courses'])) {
        return '<p>Aucun cours trouvé.</p>';
    }

    $output = '<div class="moodle-courses-grid">';

    foreach ($data['courses'] as $course) {
        if (!empty($course['id']) && $course['id'] != 1) {
            $course_image = 'https://via.placeholder.com/300x200'; // Image par défaut
            if (!empty($course['courseimage'])) {
                $course_image = $course['courseimage'];
            } elseif (!empty($course['overviewfiles'][0]['fileurl'])) {
                $course_image = $course['overviewfiles'][0]['fileurl'] . "?token=$token";
            }

            
            $category_name = !empty($course['categoryname']) ? esc_html($course['categoryname']) : 'Catégorie inconnue';

            $output .= '
                <div class="course-card">
                    <div class="course-image">
                        <img src="' . esc_url($course_image) . '" alt="Image du cours">
                    </div>
                    <div class="course-content">
                        <p>' . $category_name . '</p>
                        <h3>' . esc_html($course['fullname']) . '</h3>
                        <p>' . wp_kses_post($course['summary']) . '</p>
                    </div>
                </div>';
        }
    }

    $output .= '</div>';

    return $output;
}
add_shortcode('courses', 'cours_moodle');






function moodle_categories_styles() {
    echo '
    <style>
    .moodle-category-container {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
    }

    .moodle-category {
        padding: 8px 20px;
        border-radius: 999px;
        font-weight: 500;
        font-size: 14px;
        border: 2px solid;
        background-color: white;
        transition: all 0.3s ease;
        cursor: pointer;
        white-space: nowrap;
        display: inline-block;
        margin: 5px;
    }

    
    </style>
    ';
}
add_action('wp_head', 'moodle_categories_styles');











































function code_enqueue_js() {
    wp_enqueue_script(
        'icode-js', 
        plugin_dir_url(__FILE__) . 'assets/code.js',
        [],
        null,
        true
    );

    
    wp_localize_script('icode-js', 'moodle_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'code_enqueue_js');





function inscrire_utilisateur_moodle() {
    
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Veuillez vous connecter pour vous inscrire.'], 401);
    }

    $current_user = wp_get_current_user();
    if (!$current_user->ID) {
        wp_send_json_error(['message' => 'Utilisateur non valide.'], 403);
    }


    $lock_key = 'moodle_enrol_lock_' . $current_user->ID . '_' . $_POST['course_id'];
    if (get_transient($lock_key)) {
        wp_send_json_error(['message' => 'redirection en cours...']);
    }
    set_transient($lock_key, true, 30); 

    
    if (!isset($_POST['course_id']) || empty($_POST['course_id'])) {
        wp_send_json_error(['message' => 'ID du cours manquant.'], 400);
    }

    $course_id = absint($_POST['course_id']);
    if ($course_id === 0) {
        wp_send_json_error(['message' => 'ID du cours invalide.'], 400);
    }

    error_log('Tentative d\'inscription - User WP: ' . $current_user->ID . ', Cours: ' . $course_id);

 
    $moodle_user_id = get_moodle_user_id($current_user->user_email);
    if (!$moodle_user_id) {
        error_log('Erreur: Utilisateur Moodle introuvable pour email: ' . $current_user->user_email);
        wp_send_json_error(['message' => 'Impossible de trouver votre compte Moodle. Veuillez contacter l\'administrateur.'], 404);
    }


    $enrol_result = enrol_user_to_moodle_course($moodle_user_id, $course_id);
    if (!$enrol_result) {
        error_log('Échec inscription - User Moodle: ' . $moodle_user_id . ', Cours: ' . $course_id);
        wp_send_json_error(['message' => 'Erreur lors de l\'inscription au cours. Veuillez réessayer.'], 500);
    }

   
    $sso_url = get_moodle_sso_login_url($current_user->ID);
    if (!$sso_url) {
        wp_send_json_error(['message' => 'Erreur de génération du lien SSO.']);
    }

   
    $redirect_url = $sso_url . '&wantsurl=' . urlencode(MOODLE_URL . '/course/view.php?id=' . $course_id);
    error_log('Inscription réussie - Redirection vers: ' . $redirect_url);

  
    wp_send_json_success([
        'message' => 'Bienvenue dans votre cours...',
        'redirect_url' => esc_url_raw($redirect_url),
        'course_id' => $course_id
    ]);
}


function get_moodle_user_id($email) {
    $url = MOODLE_URL . '/webservice/rest/server.php';
    $params = [
        'wstoken' => MOODLE_API_KEY,
        'wsfunction' => 'core_user_get_users_by_field',
        'moodlewsrestformat' => 'json',
        'field' => 'email',
        'values[0]' => $email
    ];

    $response = wp_remote_post($url, [
        'body' => $params,
        'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
    ]);

    if (is_wp_error($response)) {
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    return $body[0]['id'] ?? false;
}

function enrol_user_to_moodle_course($user_id, $course_id) {
    $url = MOODLE_URL . '/webservice/rest/server.php';
    $params = [
        'wstoken' => MOODLE_API_KEY,
        'wsfunction' => 'enrol_manual_enrol_users',
        'moodlewsrestformat' => 'json',
        'enrolments[0][roleid]' => 5, 
        'enrolments[0][userid]' => $user_id,
        'enrolments[0][courseid]' => $course_id
    ];

    $response = wp_remote_post($url, [
        'body' => $params,
        'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
    ]);

    return !is_wp_error($response);
}



function get_moodle_sso_login_url($wp_user_id) {
   
    $user = get_user_by('ID', $wp_user_id);
    if (!$user) {
        error_log('Erreur: Utilisateur WordPress introuvable ID: ' . $wp_user_id);
        return false;
    }

   
    $moodle_user = get_moodle_user_by_email($user->user_email);
    if (!$moodle_user || empty($moodle_user['username'])) {
        error_log('Erreur: Impossible de trouver l\'utilisateur Moodle pour email: ' . $user->user_email);
        return false;
    }

  
    $params = [
        'wstoken' => MOODLE_API_KEY,
        'wsfunction' => 'auth_userkey_request_login_url',
        'user[username]' => $moodle_user['username'],
        'moodlewsrestformat' => 'json'
    ];

    $url = MOODLE_URL . '/webservice/rest/server.php?' . http_build_query($params);
    error_log('Requête API Moodle SSO: ' . str_replace(MOODLE_API_KEY, '***', $url));

 
    $response = wp_remote_get($url, [
        'timeout' => 15,
        'sslverify' => false 
    ]);

    if (is_wp_error($response)) {
        error_log('Erreur API Moodle: ' . $response->get_error_message());
        return false;
    }

   
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    if (!empty($body['exception'])) {
        error_log('Erreur Moodle: ' . $body['message'] . ' | Debug: ' . ($body['debuginfo'] ?? 'N/A'));
        return false;
    }

    return $body['loginurl'] ?? false;
}
function get_moodle_user_by_email($email) {
    $params = [
        'wstoken' => MOODLE_API_KEY,
        'wsfunction' => 'core_user_get_users_by_field',
        'field' => 'email',
        'values[0]' => $email,
        'moodlewsrestformat' => 'json'
    ];

    $response = wp_remote_post(MOODLE_URL . '/webservice/rest/server.php', [
        'body' => $params
    ]);

    if (!is_wp_error($response)) {
        $users = json_decode(wp_remote_retrieve_body($response), true);
        return $users[0] ?? false;
    }

    return false;
}




add_action('wp_ajax_inscrire_utilisateur_moodle', 'inscrire_utilisateur_moodle');
add_action('wp_ajax_nopriv_inscrire_utilisateur_moodle', 'inscrire_utilisateur_moodle');






























function get_user_courses_from_moodle($user_id_wp) {
   
    $user = get_userdata($user_id_wp);
    if (!$user) return '<p>Utilisateur non trouvé.</p>';

    $username = $user->user_login;


    $url_user = MOODLE_URL . "/webservice/rest/server.php?wstoken=" . MOODLE_API_KEY .
        "&wsfunction=core_user_get_users&moodlewsrestformat=json";
    $response_user = wp_remote_post($url_user, [
        'body' => http_build_query(['criteria' => [['key' => 'username', 'value' => $username]]]),
        'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
    ]);

    $body_user = json_decode(wp_remote_retrieve_body($response_user), true);
    if (empty($body_user['users'][0]['id'])) return '<p>Utilisateur Moodle non trouvé.</p>';
    $moodle_user_id = $body_user['users'][0]['id'];

 
    $url_courses = MOODLE_URL . "/webservice/rest/server.php?wstoken=" . MOODLE_API_KEY .
        "&wsfunction=core_enrol_get_users_courses&moodlewsrestformat=json&userid=$moodle_user_id";
    $response_courses = wp_remote_get($url_courses);
    $courses = json_decode(wp_remote_retrieve_body($response_courses), true);

    if (empty($courses)) return '<p>Aucun cours trouvé.</p>';

 
    $output = '<div class="moodle-courses-grid">';
    foreach ($courses as $course) {
        $output .= '
            <div class="course-card">
                <div class="course-content">
                    <h3>' . esc_html($course['fullname']) . '</h3>
                    <p>' . wp_kses_post($course['summary']) . '</p>
<a href="#" class="voir-cours-btn inscription-btn" data-action="voir_cours" data-course-id="' . esc_attr($course['id']) . '">Voir le cours</a>
                </div>
            </div>';
    }
    $output .= '</div>';

    return $output;
}


add_shortcode('cours_apprenant', function() {
    if (!is_user_logged_in()) {
        return '<p>Veuillez vous connecter pour voir vos cours.</p>';
    }
    return get_user_courses_from_moodle(get_current_user_id());
});




function redirect_utilisateur_deja_inscrit() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Veuillez vous connecter.'], 401);
    }

    $current_user = wp_get_current_user();
    if (!$current_user->ID) {
        wp_send_json_error(['message' => 'Utilisateur non valide.'], 403);
    }

    if (!isset($_POST['course_id']) || empty($_POST['course_id'])) {
        wp_send_json_error(['message' => 'ID du cours manquant.'], 400);
    }

    $course_id = absint($_POST['course_id']);
    if ($course_id === 0) {
        wp_send_json_error(['message' => 'ID du cours invalide.'], 400);
    }

    $sso_url = get_moodle_sso_login_url($current_user->ID);

    if ($sso_url) {
        $redirect_url = $sso_url . '&wantsurl=' . urlencode(MOODLE_URL . '/course/view.php?id=' . $course_id);

     
        wp_send_json_success([
            'message' => 'Redirection réussie vers votre cours Moodle !', 
            'redirect_url' => $redirect_url
        ]);
    } else {
        wp_send_json_error(['message' => 'Erreur de génération du lien SSO.']);
    }
}



add_action('wp_ajax_redirect_utilisateur_deja_inscrit', 'redirect_utilisateur_deja_inscrit');
add_action('wp_ajax_nopriv_redirect_utilisateur_deja_inscrit', 'redirect_utilisateur_deja_inscrit');

function redirection_vers_cours_sans_inscription() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Veuillez vous connecter.'], 401);
    }

    $user = wp_get_current_user();
    if (!$user || !$user->ID) {
        wp_send_json_error(['message' => 'Utilisateur non valide.'], 403);
    }

    if (empty($_POST['course_id'])) {
        wp_send_json_error(['message' => 'ID du cours manquant.'], 400);
    }

    $course_id = absint($_POST['course_id']);
    if (!$course_id) {
        wp_send_json_error(['message' => 'ID du cours invalide.'], 400);
    }

    $username = $user->user_login;
    $sso_url = MOODLE_URL . '/auth/sso/login.php?user=' . urlencode($username);
    $redirect_url = $sso_url . '&wantsurl=' . urlencode(MOODLE_URL . '/course/view.php?id=' . $course_id);

    wp_send_json_success([
        'message' => 'Redirection vers le cours en cours...',
        'redirect_url' => $redirect_url
    ]);
}
add_action('wp_ajax_voir_cours_sans_inscription', 'redirection_vers_cours_sans_inscription');
add_action('wp_ajax_nopriv_voir_cours_sans_inscription', 'redirection_vers_cours_sans_inscription');







































function is_course_completed($user_id, $course_id) {
    $params = [
        'wstoken' => MOODLE_API_KEY,
        'wsfunction' => 'core_completion_get_course_completion_status',
        'moodlewsrestformat' => 'json',
        'courseid' => $course_id,
        'userid' => $user_id
    ];

    $response = wp_remote_post(MOODLE_URL . '/webservice/rest/server.php', [
        'body' => $params
    ]);

    if (is_wp_error($response)) {
        return false;
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($data['completionstatus']['completed']) && $data['completionstatus']['completed'] == true) {
        return true;
    }

    return false;
}

function get_cours_termines($user_id) {
    $params = [
        'wstoken' => MOODLE_API_KEY,
        'wsfunction' => 'core_completion_get_courses_with_completion_status',
        'moodlewsrestformat' => 'json',
        'userid' => $user_id
    ];

    $response = wp_remote_post(MOODLE_URL . '/webservice/rest/server.php', [
        'body' => $params
    ]);

    if (is_wp_error($response)) {
        return [];
    }

    $courses_data = json_decode(wp_remote_retrieve_body($response), true);

    $cours_termines = [];

    if (!empty($courses_data['courses'])) {
        foreach ($courses_data['courses'] as $course) {
            if (!empty($course['completed']) && $course['completed'] == true) {
                $cours_termines[] = $course;
            }
        }
    }

    return $cours_termines;
}

function get_user_courses_moodle($user_id) {
    $params = [
        'wstoken' => MOODLE_API_KEY,
        'wsfunction' => 'core_enrol_get_users_courses',
        'moodlewsrestformat' => 'json',
        'userid' => $user_id
    ];

    $response = wp_remote_post(MOODLE_URL . '/webservice/rest/server.php', [
        'body' => $params
    ]);

    if (is_wp_error($response)) {
        return [];
    }

    return json_decode(wp_remote_retrieve_body($response), true);
}


function cours_terminer() {
    if (!is_user_logged_in()) {
        return '<p>Vous devez être connecté pour voir vos cours terminés.</p>';
    }

    $current_user = wp_get_current_user();
    $user_moodle = get_moodle_user_by_email($current_user->user_email);

    if (!$user_moodle || empty($user_moodle['id'])) {
        return '<p>Impossible de trouver votre compte Moodle.</p>';
    }

    $user_id = $user_moodle['id'];

    $all_courses = get_user_courses_moodle($user_id);
    $completed_courses = [];

    if (!empty($all_courses)) {
        foreach ($all_courses as $course) {
            if (is_course_completed($user_id, $course['id'])) {
                $completed_courses[] = $course;
            }
        }
    }

    if (empty($completed_courses)) {
        return '<p>Aucun cours terminé pour le moment.</p>';
    }

    $output = '<div class="moodle-courses-grid">';
    foreach ($completed_courses as $cours) {
        $output .= '
            <div class="course-card">
                <div class="course-content">
                    <h3>' . esc_html($cours['fullname']) . '</h3>
                    <span class="terminer-btn">Cours terminé</span>
                </div>
            </div>';
    }
    $output .= '</div>';

    return $output;
}
add_shortcode('cours_termines', 'cours_terminer');

















function get_moodle_user_courses($user_id) {
    
    $params = [
        'wstoken' => MOODLE_API_KEY,
        'wsfunction' => 'core_enrol_get_users_courses',
        'moodlewsrestformat' => 'json',
        'userid' => $user_id
    ];

    
    $response = wp_remote_post(MOODLE_URL . '/webservice/rest/server.php', [
        'body' => $params
    ]);

    if (is_wp_error($response)) {
        return array(); 
    }

 
    $body = wp_remote_retrieve_body($response);
    $courses = json_decode($body, true);

    if (isset($courses['exception'])) {
        return array();
    }

    return $courses; 
}

function moodle_user_courses() {
    if (!is_user_logged_in()) {
        return '<p>Veuillez vous connecter pour voir vos cours.</p>';
    }

    $user = wp_get_current_user();
    $username = $user->user_login;

    $url = MOODLE_URL . "/webservice/rest/server.php?wstoken=" . MOODLE_API_KEY .
        "&wsfunction=core_user_get_users&moodlewsrestformat=json";

    $response = wp_remote_post($url, [
        'body' => http_build_query(['criteria' => [['key' => 'username', 'value' => $username]]]),
        'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
    ]);

    if (is_wp_error($response)) {
        return '<p>Erreur de communication avec Moodle.</p>';
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($body['users'][0]['id'])) {
        return '<p>Aucun compte Moodle lié à votre profil.</p>';
    }

    $moodle_user_id = $body['users'][0]['id'];

    $courses = get_moodle_user_courses($moodle_user_id);

    if (empty($courses)) {
        return '<p>Vous n\'êtes inscrit à aucun cours pour le moment.</p>';
    }

    $output = '<div class="courses-grid">';
    $output .= '<script>var moodleCharts = [];</script>';

    foreach ($courses as $index => $course) {
        $output .= '<div class="course-card"><div class="course-content">';
        $output .= '<h3>' . esc_html($course['fullname']) . '</h3>';

        $grade = get_moodle_user_grade($moodle_user_id, $course['id']);

        if ($grade !== null) {
            $canvas_id = 'gradeChart_' . $index;
            $output .= '<div style="width: 120px; height: 120px; margin: 10px auto;">
                            <canvas id="' . esc_attr($canvas_id) . '"></canvas>
                        </div>';
            $output .= '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    var ctx = document.getElementById("' . esc_js($canvas_id) . '").getContext("2d");
                    moodleCharts.push(new Chart(ctx, {
                        type: "doughnut",
                        data: {
                            datasets: [{
                                data: [' . esc_js($grade) . ', ' . esc_js(100 - $grade) . '],
                                backgroundColor: ["#f48500", "#eeeeee"],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            cutout: "70%",
                            plugins: {
                                tooltip: { enabled: false },
                                legend: { display: false }
                            }
                        },
                        plugins: [{
                            id: "centerText",
                            beforeDraw(chart) {
                                const {width, height, ctx} = chart;
                                ctx.restore();
                                const fontSize = (height / 5).toFixed(2);
                                ctx.font = fontSize + "px Arial";
                                ctx.textBaseline = "middle";
                                ctx.textAlign = "center";
                                const text = "' . esc_js(round($grade)) . '%";
                                ctx.fillStyle = "#333";
                                ctx.fillText(text, width / 2, height / 2);
                                ctx.save();
                            }
                        }]
                    }));
                });
            </script>';
        } else {
            $output .= '<div><em>Pas de note disponible</em></div>';
        }

        $output .= '</div></div>';
    }

    $output .= '</div>';

    return $output;
}
add_shortcode('user_courses', 'moodle_user_courses');






function get_moodle_user_grade($user_id, $course_id) {
    $params = [
        'wstoken' => MOODLE_API_KEY,
        'wsfunction' => 'gradereport_user_get_grade_items',
        'moodlewsrestformat' => 'json',
        'userid' => $user_id,
        'courseid' => $course_id
    ];

    $response = wp_remote_post(MOODLE_URL . '/webservice/rest/server.php', [
        'body' => $params
    ]);

    if (is_wp_error($response)) {
        return null;
    }

    $body = wp_remote_retrieve_body($response);
    $grades = json_decode($body, true);

    if (isset($grades['exception'])) {
        return null;
    }


    if (isset($grades['usergrades'][0]['gradeitems'])) {
        foreach ($grades['usergrades'][0]['gradeitems'] as $item) {
            if (!empty($item['itemtype']) && $item['itemtype'] === 'course') {
                return $item['graderaw']; 
            }
        }
    }

    return null; 
}
function enqueue_chartjs() {
    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_chartjs');



function charger_style_profil_utilisateur() {
    wp_enqueue_style(
        'style-profil-utilisateur',
        plugins_url('assets/style.css', __FILE__),
        array(),
        '1.0'
    );
}
add_action('wp_enqueue_scripts', 'charger_style_profil_utilisateur');


function get_country_name_from_code($code) {
    $countries = [
        'AF' => 'Afghanistan',
        'ZA' => 'Afrique du Sud',
        'AL' => 'Albanie',
        'DZ' => 'Algérie',
        'AD' => 'Andorre',
        'AO' => 'Angola',
        'TN' => 'Tunisie', 
        'FR' => 'France',
        'US' => 'États-Unis',
       
    ];
    return $countries[$code] ?? $code; 
}
/*
function afficher_profil_utilisateur() {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $roles = $user->roles;

       
        $role_affiche = 'Utilisateur';
        if (in_array('um_apprenant', $roles)) {
            $role_affiche = 'Apprenant';
        } elseif (in_array('um_mentor', $roles)) {
            $role_affiche = 'Mentor';
        }

       
        $date_inscription = date_i18n('d F Y', strtotime($user->user_registered));


        $moodle_url = MOODLE_URL . '/webservice/rest/server.php';
        $token = MOODLE_API_KEY;
        $username = $user->user_login;

     
        $args = [
            'method' => 'GET',
            'timeout' => 15,
        ];

        $url = add_query_arg([
            'wstoken' => $token,
            'wsfunction' => 'core_user_get_users',
            'moodlewsrestformat' => 'json',
            'criteria[0][key]' => 'username',
            'criteria[0][value]' => $username,
        ], $moodle_url);

        $response = wp_remote_get($url, $args);
        $moodle_data = json_decode(wp_remote_retrieve_body($response), true);

     
        $moodle_user = $moodle_data['users'][0] ?? null;
        $prenom = $moodle_user['firstname'] ?? 'Non disponible';
        $nom = $moodle_user['lastname'] ?? 'Non disponible';
        $moodle_username = $moodle_user['username'] ?? 'Non disponible';
        $pays_code = $moodle_user['country'] ?? 'Non disponible';
        $timezone = $moodle_user['timezone'] ?? 'Non disponible';

     
        $pays = get_country_name_from_code($pays_code);

    
        if ($timezone === '99') {
            $timezone = 'Heure du serveur (par défaut)';
        }

        ob_start();
        ?>
        <div class="profil-utilisateur">
            <h3>Bienvenue, <?php echo esc_html($user->display_name); ?> !</h3>
            <p><strong>Nom complet :</strong> <?php echo esc_html($prenom . ' ' . $nom); ?></p>
            <p><strong>Nom d'utilisateur  :</strong> <?php echo esc_html($moodle_username); ?></p>
            <p><strong>Pays :</strong> <?php echo esc_html($pays); ?></p>
            <p><strong>Fuseau horaire :</strong> <?php echo esc_html($timezone); ?></p>
            <p><strong>Rôle :</strong> <?php echo esc_html($role_affiche); ?></p>
            <p><strong>Participe à DevAcademie depuis le :</strong> <?php echo esc_html($date_inscription); ?></p>
        </div>
        <?php
        return ob_get_clean();
    } else {
        return '<p>Vous devez être connecté pour voir votre profil.</p>';
    }
}
add_shortcode('profil_utilisateur', 'afficher_profil_utilisateur');

*/
function afficher_profil_utilisateur() {
    if (!is_user_logged_in()) {
        return '<p>Vous devez être connecté pour voir votre profil.</p>';
    }

    $user = wp_get_current_user();

    // Vérification basique
    if (empty($user) || !isset($user->ID)) {
        return '<p>Erreur lors de la récupération du profil utilisateur.</p>';
    }

    // Nom affiché, fallback si display_name est vide
    $nom_affiche = !empty($user->display_name) ? $user->display_name : $user->user_login;

    $roles = $user->roles;

    $role_affiche = 'Utilisateur';
    if (in_array('um_apprenant', $roles)) {
        $role_affiche = 'Apprenant';
    } elseif (in_array('um_mentor', $roles)) {
        $role_affiche = 'Mentor';
    } elseif (in_array('um_admin', $roles)) {
        $role_affiche = 'Admin';
    }

    $date_inscription = date_i18n('d F Y', strtotime($user->user_registered));

    // --- partie Moodle inchangée ---
    $moodle_url = MOODLE_URL . '/webservice/rest/server.php';
    $token = MOODLE_API_KEY;
    $username = $user->user_login;

    $args = [
        'method' => 'GET',
        'timeout' => 15,
    ];

    $url = add_query_arg([
        'wstoken' => $token,
        'wsfunction' => 'core_user_get_users',
        'moodlewsrestformat' => 'json',
        'criteria[0][key]' => 'username',
        'criteria[0][value]' => $username,
    ], $moodle_url);

    $response = wp_remote_get($url, $args);
    $moodle_data = json_decode(wp_remote_retrieve_body($response), true);

    $moodle_user = $moodle_data['users'][0] ?? null;
    $prenom = $moodle_user['firstname'] ?? 'Non disponible';
    $nom = $moodle_user['lastname'] ?? 'Non disponible';
    $moodle_username = $moodle_user['username'] ?? 'Non disponible';
    $pays_code = $moodle_user['country'] ?? 'Non disponible';
    $timezone = $moodle_user['timezone'] ?? 'Non disponible';
    $pays = get_country_name_from_code($pays_code);

    if ($timezone === '99') {
        $timezone = 'pas disponible';
    }

    ob_start();
    ?>
    <div class="profil-utilisateur" style="position: relative;">
<h3 style="font-weight: bold;">Bienvenue, <?php echo esc_html($user->display_name); ?> !</h3>

        <p style="font-style: italic; margin-bottom: 20px;">
            Merci de bien vouloir vérifier et mettre à jour les informations de votre profil pour garantir leur exactitude.
        </p>

        <p><strong>Nom complet :</strong> <?php echo esc_html($prenom . ' ' . $nom); ?></p>
        <p><strong>Nom d'utilisateur :</strong> <?php echo esc_html($moodle_username); ?></p>
        <p><strong>Pays :</strong> <?php echo esc_html($pays); ?></p>
        <p><strong>Fuseau horaire :</strong> <?php echo esc_html($timezone); ?></p>
        <p><strong>Rôle :</strong> <?php echo esc_html($role_affiche); ?></p>
        <p><strong>Participe à DevAcademie depuis le :</strong> <?php echo esc_html($date_inscription); ?></p>

        <div style="margin-top: 20px;">
            <a href="<?php echo esc_url(site_url('/index.php/modifier-profil')); ?>" 
               class="button modifier-profil-btn"
               style="
                   background-color: #f3ad22;
                   color: white;
                   display: inline-block;
                   border-radius: 999px;
                   font-weight: 500;
                   font-size: 16px;
                   padding: 8px 16px;
                   text-decoration: none;
               ">
                Modifier mon profil
            </a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('profil_utilisateur', 'afficher_profil_utilisateur');

function form_modifier_profil() {
    // Démarrer la session (si pas déjà démarrée)
    if (!session_id()) {
        session_start();
    }

    $message_success = '';

    // Variables à initialiser, exemple
    $prenom = '';
    $nom = '';
    $email = '';
    $pays_actuel = '';
    $timezone_actuel = '';
    $countries = [ 'FR' => 'France', 'US' => 'États-Unis', 'DE' => 'Allemagne' ]; // exemple
    $timezones = timezone_identifiers_list();

    // Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['profil_modification_nonce']) && wp_verify_nonce($_POST['profil_modification_nonce'], 'modifier_profil')) {
        // Récupérer et valider les champs POST
        $prenom = sanitize_text_field($_POST['prenom'] ?? '');
        $nom = sanitize_text_field($_POST['nom'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $pays_actuel = sanitize_text_field($_POST['pays'] ?? '');
        $timezone_actuel = sanitize_text_field($_POST['timezone'] ?? '');

        // TODO : Mettre à jour le profil utilisateur ici (update_user_meta ou wp_update_user)
        // Par exemple :
        $user_id = get_current_user_id();
        if ($user_id) {
            wp_update_user([
                'ID' => $user_id,
                'user_email' => $email,
                'display_name' => $prenom . ' ' . $nom,
            ]);
            update_user_meta($user_id, 'first_name', $prenom);
            update_user_meta($user_id, 'last_name', $nom);
            update_user_meta($user_id, 'country', $pays_actuel);
            update_user_meta($user_id, 'timezone', $timezone_actuel);

            // Mettre le message de succès en session
            $_SESSION['profil_modification_success'] = 'Profil mis à jour avec succès !';
            
            // Redirection pour éviter le double POST (rafraîchissement de la page)
            wp_redirect(esc_url_raw(remove_query_arg('updated')));
            exit;
        }
    }

    // Récupérer les valeurs actuelles du profil pour affichage
    $user = wp_get_current_user();
    if (!$prenom) $prenom = get_user_meta($user->ID, 'first_name', true);
    if (!$nom) $nom = get_user_meta($user->ID, 'last_name', true);
    if (!$email) $email = $user->user_email;
    if (!$pays_actuel) $pays_actuel = get_user_meta($user->ID, 'country', true);
    if (!$timezone_actuel) $timezone_actuel = get_user_meta($user->ID, 'timezone', true);

    // Vérifier si message succès en session
    if (!empty($_SESSION['profil_modification_success'])) {
        $message_success = $_SESSION['profil_modification_success'];
        unset($_SESSION['profil_modification_success']); // supprimer après affichage
    }

    ob_start();
    ?>

    <?php if ($message_success): ?>
        <div id="popup-success" style="
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #d6c9f8; /* violet clair */
            color: #320065; /* texte violet foncé */
            border: 2px solid #320065;
            padding: 15px 25px;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(50, 0, 101, 0.4);
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.5s ease;
        ">
            <?php echo esc_html($message_success); ?>
        </div>

        <script>
            setTimeout(() => {
                const popup = document.getElementById('popup-success');
                if (popup) {
                    popup.style.opacity = '0';
                    setTimeout(() => {
                        popup.style.display = 'none';
                    }, 500);
                }
            }, 4000); // disparaît après 4 secondes
        </script>
    <?php endif; ?>

    <div style="
        max-width: 620px;
        margin: 40px auto;
        padding: 25px 30px 40px 30px;
        border: 2px solid #320065;
        border-radius: 18px;
        background-color: #fff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
    ">
        <h2 style="
            color: #320065;
            text-align: center;
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 28px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        ">
            Modifier mon profil
        </h2>

        <form method="post" class="modifier-profil-form" style="
            max-width: 600px;
            margin: 0 auto;
            padding: 0;
            background-color: #f9f9f9;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        ">
            <!-- tes champs ici -->
            <p>
                <label for="prenom" style="display:block; font-weight: 500;">Prénom :</label>
                <input type="text" name="prenom" id="prenom" value="<?php echo esc_attr($prenom); ?>" required style="
                    width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 20px;
                    border-radius: 10px; border: 1px solid #ccc;">
            </p>

            <p>
                <label for="nom" style="display:block; font-weight: 500;">Nom :</label>
                <input type="text" name="nom" id="nom" value="<?php echo esc_attr($nom); ?>" required style="
                    width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 20px;
                    border-radius: 10px; border: 1px solid #ccc;">
            </p>

            <p>
                <label for="email" style="display:block; font-weight: 500;">E-mail :</label>
                <input type="email" name="email" id="email" value="<?php echo esc_attr($email); ?>" required style="
                    width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 20px;
                    border-radius: 10px; border: 1px solid #ccc;">
            </p>

            <p>
                <label for="pays" style="display:block; font-weight: 500;">Pays :</label>
                <select name="pays" id="pays" style="
                    width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 20px;
                    border-radius: 10px; border: 1px solid #ccc;">
                    <?php foreach ($countries as $code => $label): ?>
                        <option value="<?php echo esc_attr($code); ?>" <?php selected($pays_actuel, $code); ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <label for="timezone" style="display:block; font-weight: 500;">Fuseau horaire :</label>
                <select name="timezone" id="timezone" style="
                    width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 30px;
                    border-radius: 10px; border: 1px solid #ccc;">
                    <?php foreach ($timezones as $tz): ?>
                        <option value="<?php echo esc_attr($tz); ?>" <?php selected($timezone_actuel, $tz); ?>>
                            <?php echo esc_html($tz); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <?php wp_nonce_field('modifier_profil', 'profil_modification_nonce'); ?>

            <div style="text-align: right;">
                <input type="submit" value="Enregistrer les modifications" style="
                    background-color: #f3ad22;
                    color: white;
                    padding: 12px 24px;
                    border-radius: 999px;
                    font-weight: 600;
                    font-size: 16px;
                    border: none;
                    cursor: pointer;
                    transition: background-color 0.3s ease, transform 0.2s ease;
                " 
                onmouseover="this.style.backgroundColor='#e69e1f'; this.style.transform='scale(1.03)';"
                onmouseout="this.style.backgroundColor='#f3ad22'; this.style.transform='scale(1)';">
            </div>
        </form>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('form_modifier_profil', 'form_modifier_profil');



function get_moodle_countries() {
    return [
        'FR' => 'France',
        'BE' => 'Belgique',
        'CH' => 'Suisse',
        'CA' => 'Canada',
        'US' => 'États-Unis',
        'MA' => 'Maroc',
        'TN' => 'Tunisie',
        'DZ' => 'Algérie',
        'SN' => 'Sénégal',
      
    ];
}






//dash admin



function get_moodle_users_simple() {
    $url = "https://www.devacademie.org/outlying_formax/webservice/rest/server.php"
         . "?wstoken=8e7dd40bf03a03e3cfff31cb5ed0c0b4"
         . "&wsfunction=core_user_get_users"
         . "&moodlewsrestformat=json"
         . "&criteria[0][key]=email"
         . "&criteria[0][value]=%";

    $response = file_get_contents($url);
    if ($response === FALSE) return [];

    $body = json_decode($response, true);
    return isset($body['users']) ? $body['users'] : [];
}


function get_wordpress_role_by_email($email) {
    $user = get_user_by('email', $email);
    if (!$user || empty($user->roles)) return 'Aucun rôle';

    $translated_roles = [];

    foreach ($user->roles as $role) {
        switch ($role) {
            case 'um_apprenant':
                $translated_roles[] = 'Apprenant';
                break;
            case 'um_mentor':
                $translated_roles[] = 'Mentor';
                break;
            default:
                $translated_roles[] = ucfirst(str_replace('_', ' ', $role));
        }
    }

    return implode(', ', $translated_roles);
}


// Générer le tableau HTML avec rôle WordPress
/*function display_moodle_users_simple() {
    $users = get_moodle_users_simple();
    if (empty($users)) return "<p>Aucun utilisateur trouvé.</p>";

    $output = "<table border='1' cellpadding='5' cellspacing='0'>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prenom</th>
                        <th>Email</th>
                        <th>Rôle WordPress</th>
                        <th>Premier accès</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>";

    foreach ($users as $user) {
        $lastname = htmlspecialchars($user['lastname']);
        $firstname = htmlspecialchars($user['firstname']);
        $email = htmlspecialchars($user['email']);
        $wp_user = get_user_by('email', $email);

        if (!$wp_user || empty($wp_user->roles)) {
            continue; // Aucun utilisateur WordPress ou pas de rôle
        }

        // Ne garder que ceux qui ont um_apprenant ou um_mentor
        if (!in_array('um_apprenant', $wp_user->roles) && !in_array('um_mentor', $wp_user->roles)) {
            continue;
        }

        // Traduire les rôles
        $translated_roles = [];
        foreach ($wp_user->roles as $role) {
            switch ($role) {
                case 'um_apprenant':
                    $translated_roles[] = 'Apprenant';
                    break;
                case 'um_mentor':
                    $translated_roles[] = 'Mentor';
                    break;
                default:
                    $translated_roles[] = ucfirst(str_replace('_', ' ', $role));
            }
        }

        $firstaccess = !empty($user['firstaccess'])
            ? date('Y-m-d H:i:s', $user['firstaccess'])
            : 'Jamais connecté';

        
        $output .= "<tr>
                        <td>{$lastname}</td>
                        <td>{$firstname}</td>
                        <td>{$email}</td>
                        <td>" . implode(', ', $translated_roles) . "</td>
                        <td>{$firstaccess}</td>
                        <td>
                            <a href='" . site_url('/index.php/update-user') . "?email={$email}'>Mettre à jour</a>
                        </td>
                    </tr>";
    }

    $output .= "</tbody></table>";
    return $output;
}
*/











function display_moodle_users_simple() {
    // Récupération des paramètres GET
    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $role_filter = isset($_GET['role']) ? sanitize_text_field($_GET['role']) : '';
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 10;

    $all_users = get_moodle_users_simple();
    if (empty($all_users)) return "<p>Aucun utilisateur trouvé.</p>";

    // Filtrage par recherche
    $filtered_users = array_filter($all_users, function ($user) use ($search, $role_filter) {
        $email = $user['email'];
        $wp_user = get_user_by('email', $email);
        if (!$wp_user || empty($wp_user->roles)) return false;

        // Filtrage par rôle UM
        $is_valid_role = in_array('um_apprenant', $wp_user->roles) || in_array('um_mentor', $wp_user->roles);
        if (!$is_valid_role) return false;

        // Filtrage par texte
        $match = empty($search)
            || stripos($user['firstname'], $search) !== false
            || stripos($user['lastname'], $search) !== false
            || stripos($user['email'], $search) !== false;

        // Filtrage par rôle sélectionné
        if (!empty($role_filter)) {
            if ($role_filter === 'apprenant' && !in_array('um_apprenant', $wp_user->roles)) return false;
            if ($role_filter === 'mentor' && !in_array('um_mentor', $wp_user->roles)) return false;
        }

        return $match;
    });

    // Pagination
    $total_users = count($filtered_users);
    $total_pages = ceil($total_users / $per_page);
    $offset = ($current_page - 1) * $per_page;
    $paged_users = array_slice($filtered_users, $offset, $per_page);

    // Début HTML
    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Styles existants */
        .users-container { width: 95%; max-width: 1200px; margin: 30px auto; font-family: 'Segoe UI', Roboto, sans-serif; }
        .header-controls { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .header-controls form { display: flex; gap: 10px; flex-wrap: wrap; }
        .search-input, .role-filter { padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc; }
        .add-user-btn { background-color: #f48500; color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; }
        .add-user-btn:hover { background-color: #d66f00; }
        .pagination { text-align: center; margin-top: 20px; }
        .pagination a { margin: 0 5px; text-decoration: none; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px; color: #320065; }
        .pagination a.current { background-color: #320065; color: white; }
        .moodle-users-table { width: 100%; border-collapse: collapse; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 10px; overflow: hidden; }
        .moodle-users-table thead { background-color: #320065; color: white; text-transform: uppercase; }
        .moodle-users-table th, .moodle-users-table td { padding: 14px 16px; border-bottom: 1px solid #ddd; text-align: left; }
        .action-buttons a { margin-right: 10px; text-decoration: none; color: #0073aa; font-size: 16px; transition: color 0.3s; }
        .action-buttons .fa-pen { color: #ffc107; }

        /* Responsive tableau */
        @media (max-width: 768px) {
          .moodle-users-table,
          .moodle-users-table thead,
          .moodle-users-table tbody,
          .moodle-users-table th,
          .moodle-users-table td,
          .moodle-users-table tr {
            display: block;
          }

          .moodle-users-table thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
          }

          .moodle-users-table tr {
            margin: 0 0 1rem 0;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
          }

          .moodle-users-table td {
            border: none;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 50%;
            text-align: left;
            white-space: normal;
          }

          .moodle-users-table td:before {
            position: absolute;
            top: 14px;
            left: 15px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            font-weight: bold;
            color: #320065;
            content: attr(data-label);
          }

          /* Adapter les boutons actions */
          .action-buttons {
            text-align: right;
            padding-top: 10px;
          }
        }
    </style>

    <div class="users-container">
        <div class="header-controls">
            <form method="get">
                <input type="hidden" name="paged" value="1" />
                <input type="text" name="search" value="<?php echo esc_attr($search); ?>" placeholder="Rechercher..." class="search-input" />
                <select name="role" class="role-filter">
                    <option value="">Tous les rôles</option>
                    <option value="apprenant" <?php selected($role_filter, 'apprenant'); ?>>Apprenant</option>
                    <option value="mentor" <?php selected($role_filter, 'mentor'); ?>>Mentor</option>
                </select>
                <button type="submit" class="add-user-btn"><i class="fas fa-search"></i> Filtrer</button>
            </form>

            <a href="<?php echo site_url('/index.php/cree-user'); ?>" class="add-user-btn">
                <i class="fas fa-plus"></i> Ajouter un utilisateur
            </a>
        </div>

        <table class="moodle-users-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Premier accès</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($paged_users as $user):
                $lastname = htmlspecialchars($user['lastname']);
                $firstname = htmlspecialchars($user['firstname']);
                $email = htmlspecialchars($user['email']);
                $wp_user = get_user_by('email', $email);
                if (!$wp_user || empty($wp_user->roles)) continue;

                $translated_roles = [];
                foreach ($wp_user->roles as $role) {
                    if ($role === 'um_apprenant') $translated_roles[] = 'Apprenant';
                    elseif ($role === 'um_mentor') $translated_roles[] = 'Mentor';
                }

                $firstaccess = !empty($user['firstaccess']) ? date('Y-m-d H:i:s', $user['firstaccess']) : 'Jamais connecté';
            ?>
                <tr>
                    <td data-label="Nom"><?php echo $lastname; ?></td>
                    <td data-label="Prénom"><?php echo $firstname; ?></td>
                    <td data-label="Email"><?php echo $email; ?></td>
                    <td data-label="Rôle"><?php echo implode(', ', $translated_roles); ?></td>
                    <td data-label="Premier accès"><?php echo $firstaccess; ?></td>
                    <td data-label="Actions" class="action-buttons">
                        <a href="<?php echo site_url('/index.php/update-user?email=' . urlencode($email)); ?>" title="Modifier"><i class="fas fa-pen"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++):
                    $url = add_query_arg([
                        'search' => $search,
                        'role' => $role_filter,
                        'paged' => $i
                    ]);
                ?>
                    <a href="<?php echo esc_url($url); ?>" class="<?php echo ($i === $current_page) ? 'current' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php

    return ob_get_clean();
}

function register_moodle_users_simple_shortcode() {
    add_shortcode('users', 'display_moodle_users_simple');
}






add_action('init', 'register_moodle_users_simple_shortcode');
/*
function update_user_form_shortcode() {
    if (isset($_GET['email'])) {
        $email = sanitize_email($_GET['email']);
        $user = get_user_by('email', $email);

        if ($user) {
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
                $updated_firstname = sanitize_text_field($_POST['firstname']);
                $updated_lastname = sanitize_text_field($_POST['lastname']);
                $updated_email = sanitize_email($_POST['email']);
                $updated_role = sanitize_text_field($_POST['role']);

                // Mise à jour dans WordPress
                wp_update_user([
                    'ID' => $user->ID,
                    'first_name' => $updated_firstname,
                    'last_name' => $updated_lastname,
                    'user_email' => $updated_email
                ]);

                
                $user->set_role($updated_role);

                $moodle_user_id = get_moodle_user_id_by_email($updated_email); 

                if ($moodle_user_id) {
                    $course_creator_role_id = 2; 
                    $contextid = 1; 
                    $token = MOODLE_API_KEY;
                    $url_base = MOODLE_URL . "/webservice/rest/server.php?wstoken=$token&moodlewsrestformat=json";

                    if ($updated_role === 'um_mentor') {
                        
                        $params = [
                            'assignments' => [
                                [
                                    'roleid' => $course_creator_role_id,
                                    'userid' => $moodle_user_id,
                                    'contextid' => $contextid
                                ]
                            ]
                        ];
                        wp_remote_post("$url_base&wsfunction=core_role_assign_roles", [
                            'method' => 'POST',
                            'body' => $params
                        ]);
                    } else {
                        
                        $params = [
                            'unassignments' => [
                                [
                                    'roleid' => $course_creator_role_id,
                                    'userid' => $moodle_user_id,
                                    'contextid' => $contextid
                                ]
                            ]
                        ];
                        wp_remote_post("$url_base&wsfunction=core_role_unassign_roles", [
                            'method' => 'POST',
                            'body' => $params
                        ]);
                    }
                }

                echo "<p>Les informations ont été mises à jour avec succès.</p>";
            }

            // Formulaire
            ob_start();
            ?>
            <form method="post" style="display: flex; flex-direction: column; gap: 10px; max-width: 400px;">
                <label>Prénom :
                    <input type="text" name="firstname" value="<?php echo esc_attr($user->first_name); ?>" required>
                </label>

                <label>Nom :
                    <input type="text" name="lastname" value="<?php echo esc_attr($user->last_name); ?>" required>
                </label>

                <label>Email :
                    <input type="email" name="email" value="<?php echo esc_attr($user->user_email); ?>" required>
                </label>

                <label>Rôle :
                    <select name="role" required>
                        <option value="um_apprenant" <?php selected('um_apprenant', $user->roles[0]); ?>>Apprenant</option>
                        <option value="um_mentor" <?php selected('um_mentor', $user->roles[0]); ?>>Mentor</option>
                    </select>
                </label>

                <input type="submit" name="update_user" value="Mettre à jour">
            </form>
            <?php
            return ob_get_clean();
        } else {
            return "<p>L'utilisateur n'a pas été trouvé.</p>";
        }
    }
    return "<p>Aucun utilisateur sélectionné.</p>";
}

*/
function update_user_form_shortcode() {
    if (isset($_GET['email'])) {
        $email = sanitize_email($_GET['email']);
        $user = get_user_by('email', $email);

        if ($user) {
            $success_message = '';

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
                $updated_firstname = sanitize_text_field($_POST['firstname']);
                $updated_lastname = sanitize_text_field($_POST['lastname']);
                $updated_email = sanitize_email($_POST['email']);
                $updated_role = sanitize_text_field($_POST['role']);

                wp_update_user([
                    'ID' => $user->ID,
                    'first_name' => $updated_firstname,
                    'last_name' => $updated_lastname,
                    'user_email' => $updated_email
                ]);

                $user->set_role($updated_role);

                $moodle_user_id = get_moodle_user_id_by_email($updated_email);

                if ($moodle_user_id) {
                    $course_creator_role_id = 2;
                    $contextid = 1;
                    $token = MOODLE_API_KEY;
                    $url_base = MOODLE_URL . "/webservice/rest/server.php?wstoken=$token&moodlewsrestformat=json";

                    if ($updated_role === 'um_mentor') {
                        $params = [
                            'assignments' => [
                                [
                                    'roleid' => $course_creator_role_id,
                                    'userid' => $moodle_user_id,
                                    'contextid' => $contextid
                                ]
                            ]
                        ];
                        wp_remote_post("$url_base&wsfunction=core_role_assign_roles", [
                            'method' => 'POST',
                            'body' => $params
                        ]);
                    } else {
                        $params = [
                            'unassignments' => [
                                [
                                    'roleid' => $course_creator_role_id,
                                    'userid' => $moodle_user_id,
                                    'contextid' => $contextid
                                ]
                            ]
                        ];
                        wp_remote_post("$url_base&wsfunction=core_role_unassign_roles", [
                            'method' => 'POST',
                            'body' => $params
                        ]);
                    }
                }

$success_message = "<p class='update-toast toast-success'>Les informations ont été mises à jour avec succès.</p>";
            }

            ob_start();
            ?>
            <style>
				.update-toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 14px 20px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    z-index: 9999;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    animation: fadeIn 0.5s ease, fadeOut 0.5s ease 3.5s forwards;
    opacity: 1;
}

.toast-success {
    background-color: #f3eaff;
    color: #320065;
    border: 2px solid #320065;
}

.toast-error {
    background-color: #ffe9e9;
    color: #9e0000;
    border: 2px solid #9e0000;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeOut {
    to { opacity: 0; transform: translateY(20px); }
}

            .update-user-form-container {
                max-width: 600px;
                margin: 50px auto;
                padding: 30px;
                border: 2px solid #320065;
                border-radius: 12px;
                background-color: #fff;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                font-family: 'Roboto', sans-serif;
            }

            .update-user-form-container h2 {
                text-align: center;
                color: #320065;
                margin-bottom: 25px;
            }

            .update-user-form label {
                display: flex;
                flex-direction: column;
                font-weight: 600;
                color: #333;
                margin-bottom: 15px;
            }

            .update-user-form input[type="text"],
            .update-user-form input[type="email"],
            .update-user-form select {
                padding: 10px 12px;
                border: 1px solid #ccc;
                border-radius: 8px;
                font-size: 16px;
                margin-top: 6px;
            }

            .update-user-form input[type="submit"] {
                padding: 12px 20px;
                background-color: #ff8000;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                margin-top: 10px;
                transition: background-color 0.3s ease;
                align-self: flex-end;
            }

            .update-user-form input[type="submit"]:hover {
                background-color: #e66d00;
            }

            .update-success {
                background-color: #f3eaff;
                color: #320065;
                border: 2px solid #320065;
                padding: 12px 18px;
                border-radius: 10px;
                margin-bottom: 20px;
                font-weight: 600;
                text-align: center;
            }
            </style>

            <div class="update-user-form-container">
                <h2>Mise à jour du profil utilisateur</h2>
                <?php echo $success_message; ?>
                <form method="post" class="update-user-form">
                    <label>Prénom :
                        <input type="text" name="firstname" value="<?php echo esc_attr($user->first_name); ?>" required>
                    </label>

                    <label>Nom :
                        <input type="text" name="lastname" value="<?php echo esc_attr($user->last_name); ?>" required>
                    </label>

                    <label>Email :
                        <input type="email" name="email" value="<?php echo esc_attr($user->user_email); ?>" required>
                    </label>

                    <label>Rôle :
                        <select name="role" required>
                            <option value="um_apprenant" <?php selected('um_apprenant', $user->roles[0]); ?>>Apprenant</option>
                            <option value="um_mentor" <?php selected('um_mentor', $user->roles[0]); ?>>Mentor</option>
                        </select>
                    </label>

                    <input type="submit" name="update_user" value="Mettre à jour">
                </form>
            </div>
            <?php
            return ob_get_clean();
        } else {
            return "<p>L'utilisateur n'a pas été trouvé.</p>";
        }
    }
    return "<p>Aucun utilisateur sélectionné.</p>";
}

function get_moodle_user_id_by_email($email) {
    $token = MOODLE_API_KEY; 
    $domainname = MOODLE_URL; 
    $functionname = 'core_user_get_users';

    $params = [
        'criteria' => [
            [
                'key' => 'email',
                'value' => $email
            ]
        ]
    ];

    $url = $domainname . "/webservice/rest/server.php?wstoken=$token&wsfunction=$functionname&moodlewsrestformat=json";

    $response = wp_remote_post($url, [
        'method' => 'POST',
        'body' => $params,
    ]);

    if (is_wp_error($response)) {
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (!empty($body['users']) && isset($body['users'][0]['id'])) {
        return $body['users'][0]['id'];
    }

    return false;
}

add_shortcode('update_user_form', 'update_user_form_shortcode');







function create_user_and_sync_to_moodle($username, $first_name, $last_name, $email, $role)
{
    $password = generate_temp_password();

    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        return ['success' => false, 'message' => 'Erreur lors de la création de l\'utilisateur WordPress.'];
    }

    wp_update_user([
        'ID'         => $user_id,
        'first_name' => $first_name,
        'last_name'  => $last_name,
    ]);

    $user = new WP_User($user_id);
    if ($role == 'apprenant') {
        $user->set_role('um_apprenant');
    } elseif ($role == 'mentor') {
        $user->set_role('um_mentor');
    }

    $sync_result = send_user_to_moodle($user);

    if ($sync_result) {
        return ['success' => true, 'message' => 'Utilisateur créé et synchronisé avec Moodle.'];
    } else {
        return ['success' => false, 'message' => 'Erreur lors de la synchronisation avec Moodle.'];
    }
}

function custom_user_creation_form_shortcode() {
    ob_start();

    $popup_message = '';
    $popup_success = false;

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cuc_submit'])) {
        $username   = sanitize_user($_POST['cuc_username']);
        $first_name = sanitize_text_field($_POST['cuc_first_name']);
        $last_name  = sanitize_text_field($_POST['cuc_last_name']);
        $email      = sanitize_email($_POST['cuc_email']);
        $role       = sanitize_text_field($_POST['cuc_role']);

        $errors = new WP_Error();

        if (username_exists($username)) {
            $errors->add('username_exists', 'Ce nom d’utilisateur existe déjà.');
        }

        if (email_exists($email)) {
            $errors->add('email_exists', 'Cet e-mail est déjà utilisé.');
        }

        if (empty($username) || empty($email)) {
            $errors->add('missing_fields', 'Le nom d’utilisateur et l’e-mail sont obligatoires.');
        }

        if (empty($errors->errors)) {
            $result = create_user_and_sync_to_moodle($username, $first_name, $last_name, $email, $role);
            $popup_message = esc_js($result['message']);
            $popup_success = $result['success'];
        } else {
            $popup_message = esc_js(implode(' ', $errors->get_error_messages()));
            $popup_success = false;
        }
    }
    ?>
    <div class="user-form-container">
        <form method="post" class="user-form" novalidate>
            <h2>Ajouter un utilisateur</h2>

            <div class="field-row">
                <div class="field">
                    <label for="cuc_first_name">Prénom</label>
                    <input type="text" name="cuc_first_name" id="cuc_first_name" placeholder="Ex : Ben Ali">
                </div>
                <div class="field">
                    <label for="cuc_last_name">Nom</label>
                    <input type="text" name="cuc_last_name" id="cuc_last_name" placeholder="Ex : Farid">
                </div>
            </div>

            <div class="field">
                <label for="cuc_username">Nom d'utilisateur</label>
                <input type="text" name="cuc_username" id="cuc_username" placeholder="Ex : BFarid" required>
            </div>

            <div class="field">
                <label for="cuc_email">Adresse e-mail</label>
                <input type="email" name="cuc_email" id="cuc_email" placeholder="Ex : benali.farid@gmail.com" required>
            </div>

            <div class="field">
                <label for="cuc_role">Rôle de l'utilisateur</label>
                <select name="cuc_role" id="cuc_role" required>
                    <option value="" disabled selected>Choisir un rôle</option>
                    <option value="apprenant">Apprenant</option>
                    <option value="mentor">Mentor</option>
                </select>
            </div>

            <div class="button-wrapper">
                <button type="submit" name="cuc_submit">Créer l'utilisateur</button>
            </div>
        </form>
    </div>

    <style>
    .user-form-container {
        max-width: 600px;
        margin: 40px auto;
        padding: 30px;
        background: #ffffff;
        border: 1px solid #ccc;
        border-radius: 16px;
        box-shadow: 0 8px 18px rgba(0,0,0,0.05);
        font-family: "Segoe UI", Roboto, sans-serif;
    }

    .user-form h2 {
        font-size: 24px;
        color: #320065;
        margin-bottom: 25px;
        text-align: center;
    }

    .field {
        margin-bottom: 20px;
    }

    .field label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        color: #333;
    }

    .field input,
    .field select {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
        background: #f9f9f9;
        transition: border-color 0.3s ease;
    }

    .field input:focus,
    .field select:focus {
        border-color: #320065;
        outline: none;
    }

    .field-row {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .field-row .field {
        flex: 1;
        min-width: 48%;
    }

    .button-wrapper {
        text-align: center;
        margin-top: 20px;
    }

    .user-form button {
        padding: 12px 24px;
        background-color: #f48500;
        color: #fff;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        min-width: 180px;
    }

    .user-form button:hover {
        background-color: #d76e00;
    }

    @media screen and (max-width: 480px) {
        .field-row {
            flex-direction: column;
        }

        .field-row .field {
            min-width: 100%;
        }
    }

    /* Popup message */
    #custom-popup-message {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #e0d7f8;
        color: #320065;
        padding: 15px 25px;
        border: 2px solid #320065;
        border-radius: 10px;
        font-weight: 600;
        z-index: 9999;
        min-width: 250px;
        box-shadow: 0 0 10px rgba(50, 0, 101, 0.5);
    }
    </style>

    <div id="custom-popup-message"></div>

    <script>
    (function(){
        var message = '<?php echo $popup_message; ?>';
        var success = <?php echo json_encode($popup_success); ?>;

        if(message){
            var popup = document.getElementById('custom-popup-message');
            popup.textContent = message;

            if(success){
                popup.style.backgroundColor = '#e0d7f8'; // violet clair
                popup.style.color = '#320065';
                popup.style.borderColor = '#320065';
            } else {
                popup.style.backgroundColor = '#f8d7da'; // rouge clair
                popup.style.color = '#721c24';
                popup.style.borderColor = '#721c24';
            }

            popup.style.display = 'block';

            setTimeout(function(){
                popup.style.display = 'none';
            }, 5000);
        }
    })();
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('form_create_user', 'custom_user_creation_form_shortcode');


add_shortcode('redirect_by_um_role', 'redirect_by_um_role');
function redirect_by_um_role() {
    if (!is_user_logged_in()) {
        return '<p>Non connecté.</p>';
    }

    $user = wp_get_current_user();
    $roles = $user->roles;

    if (in_array('um_apprenant', $roles)) {
        $redirect_url = site_url('/profil-2');
    } elseif (in_array('um_mentor', $roles)) {
        $redirect_url = site_url('/espace-mentor');
    } elseif (in_array('um_admin', $roles)) {
        $redirect_url = site_url('/admin_dash');
	}else {
        $redirect_url = site_url('/');
    }

    return '<script>
        if (window.opener) {
            window.opener.location.href = "' . esc_url($redirect_url) . '";
            window.close();
        } else {
            window.location.href = "' . esc_url($redirect_url) . '";
        }
    </script>';
}







function afficher_cours_moodle_avec_inscrits() {
    $moodle_url = MOODLE_URL . '/webservice/rest/server.php';
    $token = MOODLE_API_KEY;

    $url = "$moodle_url?wstoken=$token&wsfunction=core_course_get_courses_by_field&moodlewsrestformat=json";
    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        return '<p style="color: red;">Erreur de connexion à Moodle.</p>';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (empty($data['courses'])) {
        return '<p>Aucun cours trouvé.</p>';
    }

    // Filtrer les cours avec id != 1
    $courses = array_filter($data['courses'], function($course) {
        return !empty($course['id']) && $course['id'] != 1;
    });

    ob_start();
    ?>

    <style>
        .moodle-courses-section {
            padding: 40px 20px;
            background: #f9f9fb;
            max-width: 1300px;
            margin: auto;
            font-family: "Segoe UI", sans-serif;
        }

        .moodle-courses-title {
            text-align: center;
            font-size: 30px;
            font-weight: 700;
            color: #2e065f;
            margin-bottom: 10px;
        }

        .moodle-courses-subtitle {
            text-align: center;
            color: #666;
            font-size: 16px;
            margin-bottom: 40px;
        }

        .moodle-courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .course-card {
            background: #f0e8ff; /* violet très clair */
            border-radius: 12px;
            border: 1px solid #ddd;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.04);
            padding: 24px;
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
            cursor: pointer;
            text-decoration: none !important;
            color: inherit;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .course-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border-color: #b35e00;
        }

        .course-category {
            font-size: 13px;
            font-weight: 500;
            color: #888;
            margin-bottom: 8px;
        }

        .course-title {
            font-size: 18px;
            font-weight: 600;
            color: #320065;
            margin-bottom: 16px;
        }

        .course-badge {
            background-color: #320065;
            color: white;
            padding: 6px 12px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: bold;
            align-self: flex-start;
        }

        /* Bouton Voir plus */
        .voir-plus-btn {
            display: block;
            margin: 30px auto 0 auto;
            padding: 12px 30px;
            background-color: #f48500;
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .voir-plus-btn:hover {
            background-color: #b35e00;
        }
    </style>

    <div class="moodle-courses-section">
        <h2 class="moodle-courses-title">📘 Cours disponibles sur Moodle</h2>
        <p class="moodle-courses-subtitle">Cliquez sur une carte pour accéder directement au cours sur Moodle.</p>

        <div id="moodleCoursesGrid" class="moodle-courses-grid">
            <?php
            $index = 0;
            foreach ($courses as $course) {
                $index++;
                $course_id = $course['id'];
                $category_name = !empty($course['categoryname']) ? esc_html($course['categoryname']) : 'Catégorie inconnue';

                $enrolled_url = "$moodle_url?wstoken=$token&wsfunction=core_enrol_get_enrolled_users&moodlewsrestformat=json&courseid=$course_id";
                $enrolled_response = wp_remote_get($enrolled_url);
                $nb_inscrits = 0;

                if (!is_wp_error($enrolled_response)) {
                    $enrolled_body = wp_remote_retrieve_body($enrolled_response);
                    $enrolled_users = json_decode($enrolled_body, true);
                    if (is_array($enrolled_users)) {
                        $nb_inscrits = count($enrolled_users);
                    }
                }

                $moodle_course_link = MOODLE_URL . "/course/view.php?id=$course_id";

                // Cacher les cours après le 8e par défaut
                $hidden_class = ($index > 8) ? 'hidden-course' : '';

                echo '
                <a class="course-card ' . $hidden_class . '" href="' . esc_url($moodle_course_link) . '" target="_blank" title="Voir ce cours sur Moodle" data-index="' . $index . '">
                    <div class="course-category">' . $category_name . '</div>
                    <div class="course-title">' . esc_html($course['fullname']) . '</div>
                    <div class="course-badge">' . $nb_inscrits . ' inscrit' . ($nb_inscrits > 1 ? 's' : '') . '</div>
                </a>';
            }
            ?>
        </div>

        <?php if (count($courses) > 8): ?>
        <button id="voirPlusBtn" class="voir-plus-btn">Voir plus</button>
        <?php endif; ?>
    </div>

    <script>
        (function(){
            const voirPlusBtn = document.getElementById('voirPlusBtn');
            if (!voirPlusBtn) return;

            const hiddenCourses = document.querySelectorAll('.hidden-course');
            let affiches = 8; // déjà affichés

            voirPlusBtn.addEventListener('click', () => {
                let count = 0;
                for(let i = 0; i < hiddenCourses.length; i++) {
                    if (hiddenCourses[i].style.display === 'none' || hiddenCourses[i].style.display === '') {
                        hiddenCourses[i].style.display = 'flex';
                        count++;
                    }
                    if(count >= 4) break;
                }
                affiches += count;

                // Si plus rien à afficher, on cache le bouton
                if (affiches >= <?php echo count($courses); ?>) {
                    voirPlusBtn.style.display = 'none';
                }
            });

            // Initialement cacher les cours > 8
            hiddenCourses.forEach(el => el.style.display = 'none');
        })();
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('course_stats', 'afficher_cours_moodle_avec_inscrits');


function afficher_cours_mentor_moodle() {
    if (!is_user_logged_in()) {
        return 'Veuillez vous connecter pour voir vos cours.';
    }

    $user_id = get_current_user_id();

    $sso_login_url = get_moodle_sso_login_url($user_id);
    if (!$sso_login_url) {
        return 'Impossible d\'obtenir l\'URL de connexion SSO Moodle.';
    }

    $moodle_token = MOODLE_API_KEY;
    $moodle_domain = MOODLE_URL;
    $function_name = 'core_course_get_courses_by_field';

    $url = $moodle_domain . '/webservice/rest/server.php?wstoken=' . $moodle_token .
           '&wsfunction=' . $function_name . '&moodlewsrestformat=json';

    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        return 'Erreur de connexion à Moodle.';
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    if (!isset($data['courses'])) {
        return 'Aucun cours trouvé.';
    }

    $cours_du_mentor = [];

    foreach ($data['courses'] as $cours) {
        if (isset($cours['id']) && !empty($cours['id'])) {
            $url_context = $moodle_domain . '/webservice/rest/server.php?wstoken=' . $moodle_token .
                '&wsfunction=core_enrol_get_enrolled_users&courseid=' . $cours['id'] .
                '&moodlewsrestformat=json';

            $reponse_users = wp_remote_get($url_context);
            if (!is_wp_error($reponse_users)) {
                $utilisateurs = json_decode(wp_remote_retrieve_body($reponse_users), true);
                foreach ($utilisateurs as $utilisateur) {
                    if ($utilisateur['id'] == $moodle_user_id = get_user_meta($user_id, 'moodle_user_id', true)) {
                        foreach ($utilisateur['roles'] as $role) {
                            if (in_array($role['shortname'], ['editingteacher', 'teacher'])) {
                                $cours_du_mentor[] = $cours;
                                break 2;
                            }
                        }
                    }
                }
            }
        }
    }

    if (empty($cours_du_mentor)) {
        return 'Aucun cours associé à votre compte.';
    }

    
    $output = '<div class="moodle-courses-grid">';
    foreach ($cours_du_mentor as $cours) {
        $redirect_url = $sso_login_url . '&wantsurl=' . urlencode($moodle_domain . '/course/view.php?id=' . $cours['id']);

        $output .= '
            <div class="course-card">
                <div class="course-content">
                    <h3>' . esc_html($cours['fullname']) . '</h3>
                    <a class="course-link" href="' . esc_url($redirect_url) . '" target="_blank" style="
                         background-color: #f3ad22;
                         color: white;
                         padding: 8px 16px;
                         margin-top: 10px;
                         display: inline-block;
                         border-radius: 999px;
                         font-weight: 500;
                         font-size: 14px;
                         text-decoration: none;
                       "   >Mettre a jour le cours</a>
                </div>
            </div>';
    }
    $output .= '</div>';

    return $output;
}
add_shortcode('moodle_mentor_courses', 'afficher_cours_mentor_moodle');
































//code asma 


add_action('init', function () {
    add_rewrite_tag('%mentor_sso%', '1');
    add_rewrite_rule('^mentor-to-moodle-sso/?$', 'index.php?mentor_sso=1', 'top');
});


if (!function_exists('get_moodle_sso_login_url')) {
    function get_moodle_sso_login_url($wp_user_id) {
        $user = get_user_by('ID', $wp_user_id);
        if (!$user) return false;

     
        $moodle_user = get_moodle_user_by_email($user->user_email);
        if (!$moodle_user || empty($moodle_user['username'])) return false;

      
        $params = [
            'wstoken' => MOODLE_API_KEY,
            'wsfunction' => 'auth_userkey_request_login_url',
            'user[username]' => $moodle_user['username'],
            'moodlewsrestformat' => 'json'
        ];

        $url = MOODLE_URL . '/webservice/rest/server.php?' . http_build_query($params);
        $response = wp_remote_get($url, ['timeout' => 15, 'sslverify' => false]);

        if (is_wp_error($response)) return false;

        $body = json_decode(wp_remote_retrieve_body($response), true);
        return $body['loginurl'] ?? false;
    }
}


add_action('template_redirect', function () {
    if (get_query_var('mentor_sso') != 1) return;

    $user = wp_get_current_user();
    if (!$user || !is_user_logged_in()) {
        wp_redirect(wp_login_url());
        exit;
    }

  
    $login_url = get_moodle_sso_login_url($user->ID);
    if ($login_url) {
        wp_redirect($login_url);
    } else {
        wp_die("Erreur : Impossible d'obtenir l'URL de connexion SSO Moodle.");
    }
    exit;
});


add_shortcode('mentor_create_course_btn', function () {
    if (!is_user_logged_in()) return '';

    $sso_url = esc_url(add_query_arg([
        'mentor_sso' => '1'
    ], site_url('/mentor-to-moodle-sso')));
return '<div style="text-align: center;">
    <a href="' . $sso_url . '" class="wp-block-button__link" 
    style="background-color: #ff8000; 
           color: white; 
           padding: 12px 30px; 
           border-radius: 30px; 
           text-decoration: none; 
           font-family: \'Roboto\', sans-serif; 
           font-size: 18px; 
           font-weight: 600; 
           border: none; 
           box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); 
           transition: all 0.3s ease;
           display: inline-block;
           margin: 40px auto;">
        Proposer un cours
    </a>
</div>';
});




/*

add_shortcode('moodle_user_grades', function () {
    if (!is_user_logged_in()) return 'Veuillez vous connecter pour voir vos résultats.';

    $user = wp_get_current_user();
    $email = $user->user_email;

    $moodle_url = 'https://www.devacademie.org/outlying_formax/webservice/rest/server.php';
    $token = '8e7dd40bf03a03e3cfff31cb5ed0c0b4';
    $course_id = 3;

    // Étape 1 : Récupération de l'utilisateur Moodle
    $params_user = [
        'wstoken' => $token,
        'wsfunction' => 'core_user_get_users_by_field',
        'moodlewsrestformat' => 'json',
        'field' => 'email',
        'values[0]' => $email,
    ];

    $response_user = wp_remote_get($moodle_url . '?' . http_build_query($params_user));
    $data_user = json_decode(wp_remote_retrieve_body($response_user), true);

    if (empty($data_user) || isset($data_user['exception'])) {
        return 'Utilisateur Moodle introuvable.';
    }

    $userid = $data_user[0]['id'];

    // Étape 2 : Récupération des notes
    $params_notes = [
        'wstoken' => $token,
        'wsfunction' => 'gradereport_user_get_grade_items',
        'moodlewsrestformat' => 'json',
        'userid' => $userid,
        'courseid' => $course_id,
    ];

    $response_notes = wp_remote_get($moodle_url . '?' . http_build_query($params_notes));
    $data_notes = json_decode(wp_remote_retrieve_body($response_notes), true);

    if (is_wp_error($response_notes) || empty($data_notes['usergrades'])) {
        return "Aucune note trouvée.";
    }

    // Notification de nouvelle note via hash
    $user_id = get_current_user_id();
    $old_hash = get_user_meta($user_id, 'moodle_last_grades_hash', true);
    $new_hash = md5(json_encode($data_notes['usergrades'][0]['gradeitems'] ?? []));

    if (!$old_hash || $old_hash !== $new_hash) {
        update_user_meta($user_id, 'moodle_new_grade_notification', 1);
        update_user_meta($user_id, 'moodle_last_grades_hash', $new_hash);
    }

    return afficher_notes_html_depuis_moodle($data_notes);
});
if (!function_exists('afficher_notes_html_depuis_moodle')) {
    function afficher_notes_html_depuis_moodle($data) {

        function nettoyer_html($texte) {
            // 1. Décoder les entités HTML
            $texte = html_entity_decode($texte, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            // 2. Supprimer toutes les balises HTML
            $texte = preg_replace('/<[^>]*>/', '', $texte);
            
            return trim($texte);
        }

        if (!empty($data['usergrades'])) {
            $output = '';

            foreach ($data['usergrades'] as $user) {
                $output .= "<h3>Résultats pour : " . nettoyer_html($user['userfullname']) . "</h3>";

                if (!empty($user['gradeitems'])) {
                    $output .= "<ul>";
                    foreach ($user['gradeitems'] as $item) {
                        $output .= "<li>";
                        $output .= "<strong>" . nettoyer_html($item['itemname']) . "</strong> : ";
                        $output .= nettoyer_html($item['gradeformatted']) . " (" . nettoyer_html($item['percentageformatted']) . ")";
                        if (!empty($item['feedback'])) {
                            $output .= "<br><em>Feedback :</em> " . nettoyer_html($item['feedback']);
                        }
                        $output .= "</li>";
                    }
                    $output .= "</ul>";
                }
            }

            return $output;
        } else {
            return "Aucune note trouvée.";
        }
    }
}

*/

add_shortcode('moodle_user_grades', function () {
    if (!is_user_logged_in()) return 'Veuillez vous connecter pour voir vos résultats.';

    $user = wp_get_current_user();
    $email = $user->user_email;

    $moodle_url = 'https://www.devacademie.org/outlying_formax/webservice/rest/server.php';
    $token = '8e7dd40bf03a03e3cfff31cb5ed0c0b4';

    $params_user = [
        'wstoken' => $token,
        'wsfunction' => 'core_user_get_users_by_field',
        'moodlewsrestformat' => 'json',
        'field' => 'email',
        'values[0]' => $email,
    ];

    $response_user = wp_remote_get($moodle_url . '?' . http_build_query($params_user));
    $data_user = json_decode(wp_remote_retrieve_body($response_user), true);

    if (empty($data_user) || isset($data_user['exception'])) {
        return 'Utilisateur Moodle introuvable.';
    }

    $userid = $data_user[0]['id'];
    $userfullname = $data_user[0]['fullname'];

    $params_courses = [
        'wstoken' => $token,
        'wsfunction' => 'core_enrol_get_users_courses',
        'moodlewsrestformat' => 'json',
        'userid' => $userid,
    ];

    $response_courses = wp_remote_get($moodle_url . '?' . http_build_query($params_courses));
    $data_courses = json_decode(wp_remote_retrieve_body($response_courses), true);

    if (empty($data_courses)) {
        return 'Aucun cours trouvé pour cet utilisateur.';
    }

    $all_usergrades = [];

    foreach ($data_courses as $course) {
        $params_notes = [
            'wstoken' => $token,
            'wsfunction' => 'gradereport_user_get_grade_items',
            'moodlewsrestformat' => 'json',
            'userid' => $userid,
            'courseid' => $course['id'],
        ];

        $response_notes = wp_remote_get($moodle_url . '?' . http_build_query($params_notes));
        $data_notes = json_decode(wp_remote_retrieve_body($response_notes), true);

        if (!empty($data_notes['usergrades'][0]['gradeitems'])) {
            $has_note_with_feedback = false;

            foreach ($data_notes['usergrades'][0]['gradeitems'] as $item) {
                if (!empty($item['gradeformatted']) && !empty($item['feedback'])) {
                    $has_note_with_feedback = true;
                    break;
                }
            }

            if ($has_note_with_feedback) {
                $data_notes['usergrades'][0]['coursename'] = $course['fullname'];
                $data_notes['usergrades'][0]['userfullname'] = $userfullname;
                $all_usergrades[] = $data_notes['usergrades'][0];
            }
        }
    }

    $user_id = get_current_user_id();
    $old_hash = get_user_meta($user_id, 'moodle_last_grades_hash', true);
    $new_hash = md5(json_encode($all_usergrades));

    if (!$old_hash || $old_hash !== $new_hash) {
        update_user_meta($user_id, 'moodle_new_grade_notification', 1);
        update_user_meta($user_id, 'moodle_last_grades_hash', $new_hash);
    }

    return afficher_notes_html_depuis_moodle($all_usergrades, $userfullname);
});

if (!function_exists('afficher_notes_html_depuis_moodle')) {
    function afficher_notes_html_depuis_moodle($usergrades_array, $userfullname) {

        function nettoyer_html($texte) {
            $texte = html_entity_decode($texte, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $texte = preg_replace('/<[^>]*>/', '', $texte);
            return trim($texte);
        }

        if (!empty($usergrades_array)) {
            $output = '<div class="moodle-grades-container">';
            $output .= '<h2>Résultats pour : ' . nettoyer_html($userfullname) . '</h2><hr>';

            foreach ($usergrades_array as $usergrade) {
                $output .= "<h3>Cours : " . nettoyer_html($usergrade['coursename'] ?? 'Inconnu') . "</h3>";
                
                $initial_quiz = [];
                $final_quiz = [];
                $autres_notes = [];

                if (!empty($usergrade['gradeitems'])) {
                    foreach ($usergrade['gradeitems'] as $item) {
                        $item_name = strtolower(nettoyer_html($item['itemname']));

                        if (strpos($item_name, 'initial') !== false || strpos($item_name, 'début') !== false) {
                            $initial_quiz[] = $item;
                        } elseif (strpos($item_name, 'final') !== false || strpos($item_name, 'fin') !== false) {
                            $final_quiz[] = $item;
                        } elseif (!empty($item['gradeformatted']) && !empty($item['feedback'])) {
                            $autres_notes[] = $item;
                        }
                    }

                    $output .= "<ul>";

                    if (!empty($initial_quiz)) {
                        $output .= "<li><strong>Quiz Initial :</strong><ul>";
                        foreach ($initial_quiz as $quiz) {
                            $note = !empty($quiz['gradeformatted']) ? nettoyer_html($quiz['gradeformatted']) : "Non noté";
                            $pourcentage = !empty($quiz['percentageformatted']) ? " (" . nettoyer_html($quiz['percentageformatted']) . ")" : "";
                            $feedback = !empty($quiz['feedback']) ? "<br><em>Feedback :</em> " . nettoyer_html($quiz['feedback']) : "";
                            $output .= "<li>" . nettoyer_html($quiz['itemname']) . " : <strong>$note</strong>$pourcentage $feedback</li>";
                        }
                        $output .= "</ul></li>";
                    }

                    if (!empty($final_quiz)) {
                        $output .= "<li><strong>Quiz Final :</strong><ul>";
                        foreach ($final_quiz as $quiz) {
                            $note = !empty($quiz['gradeformatted']) ? nettoyer_html($quiz['gradeformatted']) : "Non noté";
                            $pourcentage = !empty($quiz['percentageformatted']) ? " (" . nettoyer_html($quiz['percentageformatted']) . ")" : "";
                            $feedback = !empty($quiz['feedback']) ? "<br><em>Feedback :</em> " . nettoyer_html($quiz['feedback']) : "";
                            $output .= "<li>" . nettoyer_html($quiz['itemname']) . " : <strong>$note</strong>$pourcentage $feedback</li>";
                        }
                        $output .= "</ul></li>";
                    }

                    if (!empty($autres_notes)) {
                        $output .= "<li><strong>Autres Notes avec Feedback :</strong><ul>";
                        foreach ($autres_notes as $item) {
                            $output .= "<li><strong>" . nettoyer_html($item['itemname']) . "</strong> : ";
                            $output .= nettoyer_html($item['gradeformatted']) . " (" . nettoyer_html($item['percentageformatted']) . ")";
                            $output .= "<br><em>Feedback :</em> " . nettoyer_html($item['feedback']) . "</li>";
                        }
                        $output .= "</ul></li>";
                    }

                    $output .= "</ul><hr>";
                }
            }

            $output .= '</div>';
            return $output;
        } else {
            return "<div class='moodle-error'>Aucune note avec feedback trouvée.</div>";
        }
    }
}

add_action('wp_head', function () {
    ?>
    <style>
        .moodle-grades-container {
            background: #faf9ff;
            border: 1px solid #d6c7f0;
            padding: 25px;
            margin: 20px 40px;
            border-radius: 12px;
            font-family: "Segoe UI", Roboto, sans-serif;
        }

        .moodle-grades-container h2 {
            margin-top: 0;
            font-size: 24px;
            color: #320065;
        }

        .moodle-grades-container h3 {
            font-size: 20px;
            color: #703DD1;
            border-left: 6px solid #320065;
            padding-left: 12px;
            margin-bottom: 10px;
        }

        .moodle-grades-container ul {
            list-style-type: none;
            padding-left: 0;
        }

        .moodle-grades-container li {
            padding: 12px;
            margin-bottom: 10px;
            background-color: #ffffff;
            border-left: 5px solid #f48500;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }

        .moodle-grades-container li strong {
            color: #320065;
        }

        .moodle-grades-container em {
            color: #7f8c8d;
            display: block;
            margin-top: 5px;
            font-size: 0.95em;
        }

        .moodle-error {
            background: #fff2e6;
            padding: 15px;
            border: 1px solid #f48500;
            border-left: 6px solid #f48500;
            border-radius: 10px;
            color: #320065;
            font-weight: bold;
            margin: 20px 40px;
        }
    </style>
    <?php
});



function get_moodle_userid_by_email($email) {
    $url = MOODLE_URL . '/webservice/rest/server.php';
    $functionname = 'core_user_get_users';
    $params = array(
        'criteria' => array(
            array(
                'key' => 'email',
                'value' => $email
            )
        )
    );
    $url .= "?wstoken=" . MOODLE_API_KEY . "&wsfunction=" . $functionname . "&moodlewsrestformat=json";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        return false;
    }

    curl_close($curl);
    $result = json_decode($response, true);

    if (!empty($result['users'][0]['id'])) {
        return $result['users'][0]['id'];
    }
    return false;
}
function get_moodle_certificates($userid) {
    $url = MOODLE_URL . "/webservice/rest/server.php";
    $functionname = 'local_certificatesapi_get_user_certificates';

    $params = array('userid' => $userid);
    $url .= "?wstoken=" . MOODLE_API_KEY . "&wsfunction=" . $functionname . "&moodlewsrestformat=json";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        return ['error' => 'Erreur CURL : ' . curl_error($curl)];
    }

    curl_close($curl);
    $result = json_decode($response, true);

    if (isset($result['exception'])) {
        return ['error' => 'Erreur Moodle : ' . $result['message']];
    }

    return $result;
}

function shortcode_moodle_certificates() {
    if (!is_user_logged_in()) {
        return 'Vous devez être connecté pour voir vos certificats.';
    }

    $current_user = wp_get_current_user();
    $user_email = $current_user->user_email;

    $moodle_userid = get_moodle_userid_by_email($user_email);
    if (!$moodle_userid) {
        return 'Utilisateur non trouvé dans Moodle.';
    }

    $certificates = get_moodle_certificates($moodle_userid);

    if (!is_array($certificates) || isset($certificates['error'])) {
        return isset($certificates['error']) ? $certificates['error'] : 'Aucun certificat trouvé.';
    }

    $cert_list = isset($certificates['certificates']) ? $certificates['certificates'] : $certificates;

    if (empty($cert_list)) {
        return 'Aucun certificat trouvé.';
    }

//     $output = '<h3>Mes certificats</h3>';

    foreach ($cert_list as $cert) {
        if (!isset($cert['name'], $cert['timecreated'], $cert['cmid'])) {
            continue;
        }

        $date = date('d/m/Y', $cert['timecreated']);
        $cmid = intval($cert['cmid']);

        $cert_url = home_url("/?certificat_cmid={$cmid}");

        $output .= "<div>
            <strong>Nom :</strong> 
            <a href='" . esc_url($cert_url) . "' target='_blank'>{$cert['name']}</a>
        </div>
        <div><strong>Date de création :</strong> {$date}</div><br>";
    }

    return $output;
}
add_shortcode('moodle_certificates', 'shortcode_moodle_certificates');


add_action('init', function () {
    if (isset($_GET['certificat_cmid'])) {
        $cmid = intval($_GET['certificat_cmid']);

        if (!is_user_logged_in()) {
            wp_redirect(wp_login_url());
            exit;
        }

        $user = wp_get_current_user();
        $sso_url = get_moodle_sso_login_url($user->ID);

        if ($sso_url) {
            $final_url = MOODLE_URL . "/mod/customcert/view.php?id={$cmid}&downloadown=1";

            $final_redirect = add_query_arg('wantsurl', urlencode($final_url), $sso_url);

            wp_redirect($final_redirect);
            exit;
        } else {
            wp_die("Impossible d’obtenir une URL de connexion SSO Moodle.");
        }
    }
});






add_shortcode('notification_moodle_cours_globale', function() {
    if (!is_user_logged_in()) return 'Pas connecté.';

    $user_id = get_current_user_id();
    $user_meta = get_user_meta($user_id);
    $updated_courses = [];
    $debug = [];

    foreach ($user_meta as $key => $value) {
        if (strpos($key, 'moodle_notification_course_') === 0) {
            $debug[] = "Clé trouvée : $key, Valeur : " . (isset($value[0]) ? $value[0] : 'vide');

            if (!empty($value[0]) && $value[0] == '1') {
                $course_id = (int) str_replace('moodle_notification_course_', '', $key);
                $title = get_the_title($course_id);

                if ($title) {
                    $updated_courses[] = esc_html($title);
                    update_user_meta($user_id, $key, 0);
                }
            }
        }
    }

    if (!empty($updated_courses)) {
        $course_list = implode(', ', $updated_courses);
        return '<div class="notice" style="padding:10px; background:#d1e7dd; color:#0f5132; border-left:5px solid #0f5132; margin:15px 0;">
                     Les cours suivants ont été mis à jour récemment : <strong>' . $course_list . '</strong>
                </div>';
    }

    return '<pre>' . implode("\n", $debug) . '</pre>';
});
add_action('rest_api_init', function() {
    register_rest_route('moodle/v1', '/cours-modifie', [
        'methods' => 'POST',
        'callback' => 'notif_moodle_maj_callback',
        'permission_callback' => '__return_true',
    ]);
});
function get_devoirs_corriges() {
    global $wpdb;
    $result = $wpdb->get_var("
        SELECT COUNT(*) FROM wp_devoirs 
        WHERE statut = 'corrigé' 
        AND DATE(date_correction) >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    return $result;
} 

add_shortcode('notification_moodle', function () {
    if (!is_user_logged_in()) return '';

    $modified_course = get_transient('moodle_course_modified');

    if ($modified_course) {
        return '<div class="notice" style="padding:10px; background:#d1e7dd; color:#0f5132; border-left:5px solid #0f5132; margin:15px 0;">
                    Le  <strong>' . esc_html($modified_course) . '</strong> a été mis à jour récemment.
                </div>';
    }

    return '';
});

function set_course_modified_handler($request) {
    $params = $request->get_json_params();
    
    if (!isset($params['course_name']) || empty($params['course_name'])) {
        return new WP_Error('missing_course_name', 'Le nom du cours est manquant.', array('status' => 400));
    }

    $course_name = sanitize_text_field($params['course_name']);

    set_transient('moodle_course_modified', $course_name, 600);

    update_option('last_modified_course', $course_name);

    return rest_ensure_response(array(
        'status' => 'success',
        'message' => "Notification enregistrée pour le cours : $course_name"
    ));
}

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/set_course_modified', array(
        'methods' => 'POST',
        'callback' => 'set_course_modified_handler',
        'permission_callback' => '__return_true'
    ));
});

function notify_course_updated($course_name) {
    $url = 'https://devacademie.org/wp-json/custom/v1/set_course_modified';
    $data = json_encode(['course_name' => $course_name]);

    $options = [
        'http' => [
            'header'  => "Content-type: application/json",
            'method'  => 'POST',
            'content' => $data,
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    error_log(" Notification envoyée à WordPress : $course_name");
    error_log(" Réponse WordPress : $result");
}


class SynchPlugin {
    public static function notify_wordpress($coursename) {
        notify_course_updated($coursename);
    }
}$output = '
<style>


.certificate-card {
    background: #fff;
    border-left: 5px solid #ff7c00;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 8px;
}

.certificate-card strong {
    color: #333;
    font-size: 18px;
}

.certificate-card a {
    color: #006699;
    text-decoration: none;
    font-weight: bold;
}

.certificate-card a:hover {
    text-decoration: underline;
}

.certificate-card em {
    display: block;
    margin-top: 10px;
    font-style: italic;
    color: #666;
}
</style>


';
if (!isset($cert_list) || !is_array($cert_list)) {
    $cert_list = [];
}

foreach ($cert_list as $cert) {
    if (!isset($cert['name'], $cert['timecreated'])) continue;

    $date = date('d/m/Y', $cert['timecreated']);
    $nom = $cert['name'];

    $output .= '
    <div class="certificate-card">
        <strong>Nom :</strong> <a href="#">' . htmlspecialchars($nom) . '</a>
        <em>Date de création : ' . $date . '</em>
    </div>';
}
$output .= '</div>';



add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/user_role', array(
        'methods' => 'GET',
        'callback' => function (WP_REST_Request $request) {
            $email = $request->get_param('email');
            if (!$email) {
                return new WP_Error('no_email', 'Email manquant', array('status' => 400));
            }
            $user = get_user_by('email', $email);
            if (!$user) {
                return array('exists' => false);
            }
            $roles = $user->roles;
            $is_admin = in_array('administrator', $roles);
            return array(
                'exists' => true,
                'is_admin' => $is_admin,
            );
        },
        'permission_callback' => '__return_true',
    ));
});
/************************************************************************cyrine********************************************************************/
 add_shortcode('dashboard_moodle_stats', function() {
    $moodle_url = MOODLE_URL . '/webservice/rest/server.php';
    $token = MOODLE_API_KEY;

    // Récupération des cours Moodle
    $url = "$moodle_url?wstoken=$token&wsfunction=core_course_get_courses_by_field&moodlewsrestformat=json";
    $response = wp_remote_get($url);
    $cours = 0;

    if (!is_wp_error($response)) {
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (!empty($data['courses'])) {
            foreach ($data['courses'] as $c) {
                if ($c['id'] != 1) $cours++;
            }
        }
    }

    // Utilisateurs WordPress
    $total_users = count_users()['total_users'];
    $mentors = count(get_users(['role' => 'um_mentor']));
    $apprenants = count(get_users(['role' => 'um_apprenant']));

    // Couleurs violet / orange et dégradés
    $colors = [
        '#320065', // violet foncé
        '#f48500', // orange foncé
        '#5a1a85', // violet moyen (dégradé)
        '#ffae42', // orange clair (dégradé)
        '#7642a8', // violet clair (dégradé)
        '#ffc278'  // orange très clair (dégradé)
    ];

    ob_start(); ?>

    <style>
        .moodle-dashboard-charts {
            display: flex;
            gap: 20px;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .moodle-dashboard-charts canvas {
            max-width: 400px;
            max-height: 400px;
        }
    </style>

    <div class="wrap">
        <h2>📊 Statistiques Dev@cadémie</h2>
        <div class="moodle-dashboard-charts">
            <canvas id="chartBar"></canvas>
            <canvas id="chartDonut"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const bar = document.getElementById('chartBar');
        const donut = document.getElementById('chartDonut');

        const labels = ['Cours', 'Utilisateurs', 'Mentors', 'Apprenants'];
        const data = [<?= $cours ?>, <?= $total_users ?>, <?= $mentors ?>, <?= $apprenants ?>];

        // Couleurs violet / orange et dégradés
        const colors = <?= json_encode($colors); ?>;

        new Chart(bar, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Quantité',
                    data: data,
                    backgroundColor: colors.slice(0, data.length),
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        new Chart(donut, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, data.length),
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: { 
                    legend: { 
                        position: 'right',
                        labels: { color: '#320065', font: { size: 14, weight: 'bold' } }
                    } 
                }
            }
        });
    </script>

    <?php
    return ob_get_clean();
});
function shortcode_user_reg_chart() {
    global $wpdb;

    // Récupérer les inscriptions des 7 derniers jours
    $results = $wpdb->get_results("
        SELECT DATE(user_registered) as reg_date, COUNT(ID) as count
        FROM {$wpdb->users}
        WHERE user_registered >= CURDATE() - INTERVAL 6 DAY
        GROUP BY reg_date
        ORDER BY reg_date ASC
    ");

    $dates = [];
    $counts = [];
    for ($i = 6; $i >= 0; $i--) {
        $day = date('Y-m-d', strtotime("-$i days"));
        $dates[] = $day;
        $counts[$day] = 0;
    }
    foreach ($results as $row) {
        $counts[$row->reg_date] = (int)$row->count;
    }

    $counts_data = [];
    foreach ($dates as $date) {
        $counts_data[] = $counts[$date];
    }

    ob_start();
    ?>
    <div style="max-width:600px; margin: 0 auto; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <h2 style="color: #320065; text-align: center; margin-bottom: 20px;">Activité des inscriptions utilisateurs (7 derniers jours)</h2>
        <canvas id="userRegChart" style="max-width:100%;height:250px;"></canvas>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    const ctx = document.getElementById('userRegChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: [{
                label: 'Inscriptions par jour',
                data: <?php echo json_encode($counts_data); ?>,
                backgroundColor: 'rgba(50, 0, 101, 0.7)',
                borderColor: 'rgba(50, 0, 101, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {beginAtZero:true, stepSize: 1}
            }
        }
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('user_reg_chart', 'shortcode_user_reg_chart');


// Shortcode de message de bienvenue pour les formateurs
function message_bienvenue() {
    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();
        $message = "<h2>Bienvenue <u>" . esc_html($user->display_name) . "</u> sur votre espace !</h2>";
        return $message;
    } else {
        return "<p>Veuillez vous connecter pour accéder à votre espace.</p>";
    }
}
add_shortcode('message_bienvenue', 'message_bienvenue');



/**********ASMA****************/
class NotifyWPPlugin {

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_notification_script']);
    }

    public function register_rest_routes() {
        register_rest_route('notifywp/v1', '/notify', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'receive_notification'],
            'permission_callback' => function() {
                return true;
            },
        ]);

        register_rest_route('notifywp/v1', '/user-notifications', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'get_user_notifications'],
            'permission_callback' => function() {
                return is_user_logged_in();
            }
        ]);

        register_rest_route('notifywp/v1', '/mark-read', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'mark_notifications_read'],
            'permission_callback' => function() {
                return is_user_logged_in();
            }
        ]);
    }

    public function receive_notification($request) {
        $params = $request->get_json_params();

        // ✅ Correction ici : parenthèse fermante
        if (empty($params['user_id'])) {
            return new WP_Error('missing_user_id', 'User ID is required', ['status' => 400]);
        }

        $user_id = intval($params['user_id']);
        $message = sanitize_text_field($params['message'] ?? 'Nouvelle notification');
        $course_id = intval($params['course_id'] ?? 0);
        $type = sanitize_text_field($params['type'] ?? 'feedback');

        $notifications = get_user_meta($user_id, '_user_notifications', true);
        if (!is_array($notifications)) {
            $notifications = [];
        }

        $notifications[] = [
            'id' => uniqid(),
            'message' => $message,
            'course_id' => $course_id,
            'type' => $type,
            'read' => false,
            'date' => current_time('mysql'),
        ];

        update_user_meta($user_id, '_user_notifications', $notifications);

        return new WP_REST_Response(['success' => true, 'message' => 'Notification enregistrée'], 200);
    }

    public function get_user_notifications($request) {
        $user_id = get_current_user_id();
        if (!$user_id) {
            return new WP_Error('not_logged_in', 'Utilisateur non connecté', ['status' => 401]);
        }

        $notifications = get_user_meta($user_id, '_user_notifications', true);
        if (!is_array($notifications)) {
            $notifications = [];
        }

        $unread_notifications = array_filter($notifications, function($n) {
            return !$n['read'];
        });

        return new WP_REST_Response(array_values($unread_notifications), 200);
    }

    public function mark_notifications_read($request) {
        $user_id = get_current_user_id();
        if (!$user_id) {
            return new WP_Error('not_logged_in', 'Utilisateur non connecté', ['status' => 401]);
        }

        $notifications = get_user_meta($user_id, '_user_notifications', true);
        if (!is_array($notifications)) {
            return new WP_REST_Response(['success' => true], 200);
        }

        foreach ($notifications as &$notification) {
            $notification['read'] = true;
        }

        update_user_meta($user_id, '_user_notifications', $notifications);

        return new WP_REST_Response(['success' => true], 200);
    }

    public function enqueue_notification_script() {
        if (!is_user_logged_in()) return;

        wp_enqueue_script(
            'notifywp-js',
            plugin_dir_url(__FILE__) . 'assets/js/notifywp.js',
            ['jquery'],
            filemtime(plugin_dir_path(__FILE__) . 'assets/js/notifywp.js'),
            true
        );

        wp_localize_script('notifywp-js', 'notifywpData', [
            'nonce' => wp_create_nonce('wp_rest'),
            'restUrl' => rest_url('notifywp/v1/'),
            'userId' => get_current_user_id()
        ]);
    }
}

new NotifyWPPlugin();

// 📡 Appel depuis Moodle
function moodle_send_notification_to_wp($user_id, $message, $course_id = 0, $type = 'feedback') {
    $wp_rest_url = 'https://www.devacademie.org/outlying_formax/wp-json/notifywp/v1/notify';

    $body = [
        'user_id' => $user_id,
        'message' => $message,
        'course_id' => $course_id,
        'type' => $type
    ];

    $args = [
        'body' => json_encode($body),
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'timeout' => 15
    ];

    $response = wp_remote_post($wp_rest_url, $args);

    if (is_wp_error($response)) {
        error_log('Erreur lors de l\'envoi de la notification: ' . $response->get_error_message());
        return false;
    }

    return true;
}