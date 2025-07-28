<?php



defined('MOODLE_INTERNAL') || die();



function theme_boost_child_get_main_scss_content($theme) {

    global $CFG;



    // Récupère le SCSS de Boost (preset par défaut)

    $boostscss = file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');



    // Ajoute ton SCSS perso s’il existe

    $customscsspath = __DIR__ . '/scss/custom.scss';

    $customscss = file_exists($customscsspath) ? file_get_contents($customscsspath) : '';



    return $boostscss . "\n\n" . $customscss;

}



function theme_boost_child_css_postprocess($css, $theme) {

    return $css;

}

