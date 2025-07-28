<?php
defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/externallib.php");

class local_certificatesapi_external extends external_api {

    public static function get_user_certificates_parameters() {
        return new external_function_parameters([
            'userid' => new external_value(PARAM_INT, 'ID de l’utilisateur Moodle')
        ]);
    }

    public static function get_user_certificates($userid) {
        global $DB;

        $params = self::validate_parameters(self::get_user_certificates_parameters(), ['userid' => $userid]);

        $sql = "SELECT c.id AS certificateid, c.name, i.timecreated, cm.id AS cmid
                FROM {customcert_issues} i
                JOIN {customcert} c ON i.customcertid = c.id
                JOIN {course_modules} cm ON cm.instance = c.id
                JOIN {modules} m ON m.id = cm.module
                WHERE i.userid = ?
                AND m.name = 'customcert'";

        $certificates = $DB->get_records_sql($sql, [$userid]);

        $result = [];
        foreach ($certificates as $cert) {
            $result[] = [
                'certificateid' => $cert->certificateid,
                'name' => $cert->name,
                'timecreated' => $cert->timecreated,
                'cmid' => $cert->cmid,
            ];
        }

        return ['certificates' => $result]; // Important : structuré avec 'certificates' => []
    }

    public static function get_user_certificates_returns() {
        return new external_single_structure([
            'certificates' => new external_multiple_structure(
                new external_single_structure([
                    'certificateid' => new external_value(PARAM_INT, 'ID du certificat'),
                    'name' => new external_value(PARAM_TEXT, 'Nom du certificat'),
                    'timecreated' => new external_value(PARAM_INT, 'Date de création'),
                    'cmid' => new external_value(PARAM_INT, 'Course module ID'),
                ])
            )
        ]);
    }
}
