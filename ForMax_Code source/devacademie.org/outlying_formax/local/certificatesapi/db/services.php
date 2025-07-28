<?php



$functions = [
    'local_certificatesapi_get_user_certificates' => [
        'classname' => 'local_certificatesapi_external',
        'methodname' => 'get_user_certificates',
        'classpath' => 'local/certificatesapi/externallib.php',
        'description' => 'Récupère les certificats de l’utilisateur',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => '',
    ],
];


$services = [
    'Certificates API Service' => [
        'functions' => ['local_certificatesapi_get_user_certificates'],
        'restrictedusers' => 0, // 0 = ouvert à tous ceux qui ont un token
        'enabled' => 1,
    ],
];
