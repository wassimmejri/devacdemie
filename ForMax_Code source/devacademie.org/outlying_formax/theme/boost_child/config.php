<?php



defined('MOODLE_INTERNAL') || die();



$THEME->name = 'boost_child';



$THEME->parents = ['boost'];


$THEME->editor_sheets = [];



// $THEME->scss = function($theme) {

//     return theme_boost_get_main_scss_content($theme);

// };

$THEME->scss = function($theme) {

    return theme_boost_child_get_main_scss_content($theme);

};



$THEME->layouts = [

    'base' => array(

        'file' => 'columns2.php',

        'regions' => array('side-pre'),

        'defaultregion' => 'side-pre',

    ),

    'standard' => array(

        'file' => 'columns2.php',

        'regions' => array('side-pre'),

        'defaultregion' => 'side-pre',

    ),

    'course' => array(

        'file' => 'columns2.php',

        'regions' => array('side-pre'),

        'defaultregion' => 'side-pre',

    ),

];



$THEME->enable_dock = false;

$THEME->yuicssmodules = array();

$THEME->rendererfactory = 'theme_overridden_renderer_factory';

$THEME->csspostprocess = 'theme_boost_child_css_postprocess';

$THEME->sheets = ['style'];

$THEME->settings = 'settings.php';



