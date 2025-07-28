<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\course_updated',
        'callback'  => '\local_coursenotify\observer::course_updated',
    ],
    [
        'eventname' => '\mod_assign\event\assessable_uploaded',
        'callback'  => '\local_coursenotify\observer::assignment_updated',
    ],
    [
        'eventname' => '\mod_assign\event\submission_created',
        'callback'  => '\local_coursenotify\observer::assignment_updated',
    ],
    [
        'eventname' => '\mod_assign\event\submission_updated',
        'callback'  => '\local_coursenotify\observer::assignment_updated',
    ],
    [
        'eventname' => '\mod_assign\event\submission_deleted',
        'callback'  => '\local_coursenotify\observer::assignment_updated',
    ],
    // Quiz
    [
        'eventname' => '\mod_quiz\event\attempt_submitted',
        'callback'  => '\local_coursenotify\observer::quiz_updated',
    ],
    [
        'eventname' => '\mod_quiz\event\attempt_deleted',
        'callback'  => '\local_coursenotify\observer::quiz_updated',
    ],
    [
        'eventname' => '\mod_quiz\event\attempt_started',
        'callback'  => '\local_coursenotify\observer::quiz_updated',
    ],
];
