<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';  // Utiliser mysqli (car MySQL 8.0 dans Docker)
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'db';      // Le nom du service MySQL dans docker-compose
$CFG->dbname    = 'sc3ighb1166_moodle';  // Nom de la base créée dans MySQL
$CFG->dbuser    = 'root';    // Utilisateur root (comme dans docker-compose)
$CFG->dbpass    = 'rootpass';// Mot de passe root défini dans docker-compose
$CFG->prefix    = 'mdl1p_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '3306',
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_general_ci',
);

$CFG->wwwroot   = 'http://localhost:8888'; // L’URL où Moodle est accessible (localhost:8888 exposé)
$CFG->dataroot  = '/bitnami/moodledata';  // Volume monté dans le container Moodle (path interne)
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');
$CFG->debug = 32767;  // Affiche toutes les erreurs
$CFG->debugdisplay = 1;
// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
