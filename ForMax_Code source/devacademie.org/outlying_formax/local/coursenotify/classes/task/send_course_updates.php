<?php
namespace local_coursenotify\task;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/filelib.php');

class send_course_updates extends \core\task\scheduled_task {
    public function get_name() {
        return get_string('coursenotifytask', 'local_coursenotify');
    }

    public function execute() {
        global $DB;

        $since = time() - 600; // 10 minutes
        $courses = $DB->get_records_select('course', 'timemodified > ?', [$since]);

        foreach ($courses as $course) {
            $coursename = $course->fullname;

            $data = json_encode(['course_name' => $coursename]);
            $headers = ['Content-Type: application/json'];

            $response = download_file_content(
                'https://devacademie.org/wp-json/custom/v1/set_course_modified',
                null, $headers, true, 300, false, 'POST', $data
            );

            mtrace("Notification envoyée pour le cours : $coursename");
            error_log("🔁 WP Notif pour $coursename : " . var_export($response, true));
        }
    }
}
