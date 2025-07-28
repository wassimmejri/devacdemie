<?php
namespace local_coursenotify;

defined('MOODLE_INTERNAL') || die();

class observer {

    public static function send_notification($courseid, $type) {
        global $DB;

        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
        $coursename = $course->fullname;

        $client = new \core\http_client();

        try {
           
            $response = $client->post(
                'https://devacademie.org/wp-json/custom/v1/set_course_modified',
                [
                    'body' => json_encode([
                        'course_id'         => $course->id,   // <-- on envoie l'ID
                        'course_name'       => $coursename,
                        'modification_type' => $type
                    ]),
                    'headers' => ['Content-Type' => 'application/json']
                ]
            );
            

            // ✅ Log de confirmation
            $body = $response->getBody()->getContents();
            error_log("✅ WP Notif envoyée pour : $coursename [$type] | Réponse : $body");

        } catch (\Exception $e) {
            error_log("❌ Erreur d'envoi à WP : " . $e->getMessage());
        }
    }

    public static function assignment_updated(\core\event\base $event) {
        self::send_notification($event->courseid, 'devoir modifié');
    }

    public static function quiz_updated(\core\event\base $event) {
        self::send_notification($event->courseid, 'quiz modifié');
    }

    public static function course_updated(\core\event\base $event) {
        global $DB;

        $userid = $event->userid;
        $context = \context_course::instance($event->courseid);
        $is_teacher = false;

        // 🔍 Vérifie dans le contexte du cours
        $roles = get_user_roles($context, $userid, true);
        foreach ($roles as $role) {
            if (in_array($role->shortname, ['editingteacher', 'teacher'])) {
                $is_teacher = true;
                break;
            }
        }

        // 🔍 Vérifie aussi dans le contexte système si aucun rôle trouvé dans le cours
        if (!$is_teacher) {
            $syscontext = \context_system::instance();
            $sysroles = get_user_roles($syscontext, $userid, true);
            foreach ($sysroles as $role) {
                if (in_array($role->shortname, ['editingteacher', 'teacher'])) {
                    $is_teacher = true;
                    break;
                }
            }
        }

        if ($is_teacher) {
            self::send_notification($event->courseid, 'cours modifié');
        }
    }
}
