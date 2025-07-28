<?php
// ✅ Activer l'affichage des erreurs pour déboguer le HTTP ERROR 500
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ✅ Ne démarre pas de session Moodle (évite redirection)
define('NO_MOODLE_COOKIES', true);

// ✅ Inclure le fichier config.php de Moodle
require_once(__DIR__ . '/config.php');


// ✅ Définir la réponse en JSON
header('Content-Type: application/json');

// ✅ Récupérer l’e-mail depuis l’URL
$email = isset($_GET['email']) ? trim($_GET['email']) : '';

if (empty($email)) {
    echo json_encode(['error' => 'Email manquant']);
    exit;
}

global $DB;
$exists_in_moodle = false;

try {
    $user = $DB->get_record('user', ['email' => $email, 'deleted' => 0]);
    if ($user) {
        $exists_in_moodle = true;
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Erreur DB Moodle', 'details' => $e->getMessage()]);
    exit;
}

// ✅ Appel de l’API REST WordPress
$wpurl = 'https://www.devacademie.org/wp-json/custom/v1/user_role?email=' . urlencode($email);
$wp_response = @file_get_contents($wpurl);
$wp_data = json_decode($wp_response, true);

// ✅ Vérifier la réponse WordPress
$exists_in_wp = $wp_data['exists'] ?? false;
$is_admin = $wp_data['is_admin'] ?? false;

// ✅ Réponse finale
echo json_encode([
    'exists_in_moodle' => $exists_in_moodle,
    'exists_in_wp' => $exists_in_wp,
    'is_admin' => $is_admin
]);
?>
