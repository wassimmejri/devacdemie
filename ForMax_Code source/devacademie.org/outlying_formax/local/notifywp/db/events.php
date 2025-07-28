<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\mod_feedback\event\response_submitted',
        'callback'  => 'local_wpnotify_observer::feedback_response_submitted',
    ],
];
