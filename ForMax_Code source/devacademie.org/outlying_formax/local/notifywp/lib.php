<?php
defined('MOODLE_INTERNAL') || die();

function local_wordpressnotify_send_feedback($user_id, $course_id, $feedback) {
    $wp_url = 'https://votre-site-wordpress.com/wp-json/notifywp/v1/notify';
    
    $message = "Nouveau feedback reçu pour le cours: " . format_string($course_id);
    if (!empty($feedback)) {
        $message .= " - " . substr(strip_tags($feedback), 0, 100) . "...";
    }
    
    $data = [
        'user_id' => $user_id,
        'message' => $message,
        'course_id' => $course_id,
        'type' => 'feedback'
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($wp_url, false, $context);
    
    return $result !== false;

}
