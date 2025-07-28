<?php

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'theme_boost_child';
$plugin->version = 2025041500; // YYYYMMDDXX
$plugin->requires = 2022041900; // Ex : Moodle 4.0
$plugin->dependencies = [
    'theme_boost' => ANY_VERSION
];
