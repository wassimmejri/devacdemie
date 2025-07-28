<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Provides auth_edwiserbridge\external\course_progress_data trait.
 *
 * @package     auth_edwiserbridge
 * @category    external
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */

namespace auth_edwiserbridge\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use core_completion\progress;

// require_once($CFG->libdir.'/externallib.php');

/**
 * Trait implementing the external function auth_edwiserbridge_course_progress_data
 */
trait eb_test_connection {

    /**
     * Request to test connection
     *
     * @param  string $wpurl   wpurl.
     * @param  string $wptoken wptoken.
     *
     * @return array
     */
    public static function eb_test_connection($wpurl, $wptoken, $test_connection = "moodle") {
        $params = self::validate_parameters(
            self::eb_test_connection_parameters(),
            array(
                'wp_url' => $wpurl,
                "wp_token" => $wptoken,
                "test_connection" => $test_connection
            )
        );

        if ($params["test_connection"] == "wordpress") {
            $msg = "Connection Successful";
            $warnings = array();
            
            $defaultvalues = auth_edwiserbridge_get_connection_settings();

            $site_match = false;
            $token_match = false;
            foreach ($defaultvalues['eb_connection_settings'] as $site => $value) {
                if ($value['wp_token'] == $params["wp_token"]) {
                    $token_match = true;
                }
            }
            if (!$token_match) {
                $warnings[] = "Token does not match with the existing moodle connection settings. it may cause test connection issue on moodle end.";
            }
            // get webservice id by token 
            global $DB;
            $serviceid = $DB->get_field('external_tokens', 'externalserviceid', array('token' => $params["wp_token"]));
            $count = auth_edwiserbridge_get_service_list($serviceid);
            if ($count == 1) {
                $status = 0;
                $msg = $count . " Web service functions is missing.";
            } else if ($count > 1) {
                $status = 0;
                $msg = $count . " Web service functions are missing.";
            } else {
                $status = 1;
            }
            return array("status" => $status, "msg" => $msg, "warnings" => $warnings);
        } else {
            $requestdata = array(
                'action'     => "test_connection",
                'secret_key' => $params["wp_token"]
            );

            $apihandler = auth_edwiserbridge_api_handler_instance();
            $response   = $apihandler->connect_to_wp_with_args($params["wp_url"], $requestdata);
            $status = 0;
            $msg    = isset($response["msg"]) ? $response["msg"] : 'Please check WordPress site configuration.';

            if (!$response["error"] && isset($response["data"]->msg) && isset($response["data"]->status) && $response["data"]->status) {
                $status = $response["data"]->status;
                $msg = $response["data"]->msg;
                if (!$status) {
                    $msg = $response["data"]->msg . get_string('wp_test_connection_failed', 'auth_edwiserbridge');
                }
            } else {
                /**
                 * Test connection error messages.
                 * 1. Wrong token don't show detailed message.
                 * 2. Redirection or other isues will show detailed error message.
                 */
                $server_msg = isset( $response["data"]->msg ) ? $response["data"]->msg : 'Please check WordPress Site configuration.';

                $msg = '<div>
                            <div class="eb_connection_short_msg">
                                Test Connection failed, To check more information about issue click <span class="eb_test_connection_log_open"> here </span>.
                            </div>
                            <div class="eb_test_connection_log">
                                <div style="display:flex;">

                                    <div class="eb_connection_err_response">
                                        <h4> An issue was detected. </h4>
                                        <div>Status : Connection  Failed </div>
                                        <div>Url : '. $params['wp_url'] .'/wp-json/edwiser-bridge/wisdmlabs/</div>
                                        <div>Response : '. $server_msg .'</div>
                                        <div>Next Steps: Please refresh the page and try again. If this still doesnt work. Go to <a href="'. $params['wp_url'] .'/wp-admin/admin.php?page=eb-settings&tab=connection" target="_blank"> Connection Settings </a> and click the <strong>Troubleshoot</strong> button . </div>
                                    </div>
                    <div class="eb_admin_templ_dismiss_notice_message">
                                        <span class="eb_test_connection_log_close " style="color:red;"> X </span> 
                                    </div>
                                </div>
                            </div>
                        </div>';
            }

            return array("status" => $status, "msg" => $msg );
        }
    }

    /**
     * Request to test connection parameter.
     */
    public static function eb_test_connection_parameters() {
        return new external_function_parameters(
            array(
                'wp_url'          => new external_value(PARAM_TEXT, get_string('web_service_wp_url', 'auth_edwiserbridge')),
                'wp_token'        => new external_value(PARAM_TEXT, get_string('web_service_wp_token', 'auth_edwiserbridge')),
                'test_connection' => new external_value(PARAM_TEXT, get_string('web_service_test_conn', 'auth_edwiserbridge'), VALUE_DEFAULT, "moodle")
            )
        );
    }

    /**
     * paramters which will be returned from test connection function.
     */
    public static function eb_test_connection_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_TEXT, get_string('web_service_test_conn_status', 'auth_edwiserbridge')),
                'msg'    => new external_value(PARAM_RAW, get_string('web_service_test_conn_msg', 'auth_edwiserbridge')),
                // return warnings as array of strings.
                'warnings' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'warning'),
                    'warnings', VALUE_OPTIONAL
                ) 
            )
        );
    }
}
