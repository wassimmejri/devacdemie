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
 * Edwiser Bridge - WordPress and Moodle integration..
 * File used to register all the services we are adding externally.
 *
 * @package     auth_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'eb_create_service' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'eb_create_service',
        'description'   => 'Create web service',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'eb_get_course_progress' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'eb_get_course_progress',
        'description'   => 'Get course wise progress',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'eb_test_connection' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'eb_test_connection',
        'description'   => 'Test connection with WordPress',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'eb_validate_token' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'eb_validate_token',
        'description'   => 'Validate if token is matching and user has access to the service',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'eb_get_site_data' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'eb_get_site_data',
        'description'   => 'Get site wise synchronization settings',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'eb_get_users' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'eb_get_users',
        'description'   => 'Get Users',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'eb_link_service' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'eb_link_service',
        'description'   => 'Link web service',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'eb_get_service_info' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'eb_get_service_info',
        'description'   => 'Get service information',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'eb_get_edwiser_plugins_info' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'eb_get_edwiser_plugins_info',
        'description'   => 'Get plugins information',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'edwiserbridge_local_get_course_enrollment_method' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'edwiserbridge_local_get_course_enrollment_method',
        'description'   => 'Get course enrollment methods',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'edwiserbridge_local_update_course_enrollment_method' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'edwiserbridge_local_update_course_enrollment_method',
        'description'   => 'Update course enrollment method',
        'type'          => 'read',
        'ajax'          => true,
    ),
    // Setup Wizard
    'edwiserbridge_local_setup_wizard_save_and_continue' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'edwiserbridge_local_setup_wizard_save_and_continue',
        'description'   => 'Setup wizard save and continue functionality',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'edwiserbridge_local_enable_plugin_settings' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'edwiserbridge_local_enable_plugin_settings',
        'description'   => 'Enables default plugin settings.',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'edwiserbridge_local_setup_test_connection' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'edwiserbridge_local_setup_test_connection',
        'description'   => 'Enables default plugin settings.',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'edwiserbridge_local_get_mandatory_settings' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'edwiserbridge_local_get_mandatory_settings',
        'description'   => 'Gets all mandatory settings for edwiser bridge.',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'eb_get_courses' => array(
        'classname'     => 'auth_edwiserbridge\external\api',
        'methodname'    => 'eb_get_courses',
        'description'   => 'Get Courses',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'wdm_sso_verify_token' => array(
        'classname' => 'auth_sso_token_verify_external',
        'methodname' => 'wdm_sso_verify_token',
        'classpath' => 'auth/edwiserbridge/externallib.php',
        'description' => 'Return boolean value true if token matches otherwise false.',
        'type' => 'read',
    ),
    'eb_manage_cohort_enrollment' => array(
        'classname'   => 'auth_wdmgroupregistration_external',
        'methodname'  => 'eb_manage_cohort_enrollment',
        'classpath'   => 'auth/edwiserbridge/externallib.php',
        'description' => 'Return boolean value true if cohort is enrolled and false if failed.',
        'type'        => 'read',
    ),
    'eb_delete_cohort' => array(
            'classname'   => 'auth_wdmgroupregistration_external',
            'methodname'  => 'eb_delete_cohort',
            'classpath'   => 'auth/edwiserbridge/externallib.php',
            'description' => 'Return boolean value true if cohort is enrolled and false if failed.',
            'type'        => 'read',
    ),
    'eb_manage_user_cohort_enrollment' => array(
            'classname'   => 'auth_wdmgroupregistration_external',
            'methodname'  => 'eb_manage_user_cohort_enrollment',
            'classpath'   => 'auth/edwiserbridge/externallib.php',
            'description' => 'Return boolean value true if cohort is enrolled and false if failed.',
            'type'        => 'read',
    )
);
