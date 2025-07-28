<?php

namespace local_notifywp;

defined('MOODLE_INTERNAL') || die();

class observer {
    public static function grade_updated(\core\event\grade_updated $event) {
        global $DB;

        $userid = $event->relateduserid;
        $courseid = $event->courseid;

        // Récupérer le nom du cours
        $coursename = $DB->get_field('course', 'fullname', ['id' => $courseid]);

        // Message personnalisé
        $message = "Une note et un feedback ont été ajoutés au cours \"$coursename\"";

        // Envoi vers WordPress
        self::send_webhook_to_wordpress($userid, $courseid, $message);
    }

    private static function send_webhook_to_wordpress($userid, $courseid, $message) {
        $webhook_url = 'https://www.devacademie.org/wp-json/notifywp/v1/notify';
        $api_key = '28ed7da14ab096cebe3561dd5dc76613';

        $payload = json_encode([
            'user_id' => $userid,
            'course_id' => $courseid,
            'message' => $message,
            'api_key' => $api_key,
        ]);

        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-type: application/json",
                'content' => $payload,
                'timeout' => 5,
            ]
        ];

        $context = stream_context_create($options);
        @file_get_contents($webhook_url, false, $context);
    }
}
