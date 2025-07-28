<?php

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // Ajout d'un champ pour le CSS personnalisé
    $settings->add(new admin_setting_configtextarea(
        'theme_boost_child/customcss',
        get_string('customcss', 'theme_boost'),
        get_string('customcssdesc', 'theme_boost'),
        '',
        PARAM_RAW
    ));
}
