<?php
/**
 * The plugin functions.
 *
 * @package    Aus Forminator Addon Userinfo
 * @subpackage Hooks for Forminator plugin
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
/**
 * Add user info as meta data
 */
function aus__forminator_add_custom_meta($entry, $module_id, $field_data_array ) {
	if ( !is_user_logged_in() ) {
        return;
    }

	$user_id = get_current_user_id();
	$user = get_userdata($user_id);

	$added_data_array = array();

	if ($user) {
		$added_data_array = array(
			array(
				'name'  => '_aus_user_user_login',
				'value' => $user->user_login,
			),
			array(
				'name'  => '_aus_user_fullname',
				'value' => $user->first_name  . "" . $user->last_name,
			),
			array(
				'name'  => '_aus_user_displayname',
				'value' => $user->display_name,
			),
			array(
				'name'  => '_aus_user_email',
				'value' => $user->user_email,
			),
		);
	}

	
	$added_data_array[] = array(
		'name'  => '_aus_user_id',
		'value' => $user_id,
	);
	
	$entry->set_fields( $added_data_array );
}
add_action( 'forminator_custom_form_submit_before_set_fields', 'aus__forminator_add_custom_meta', 10, 3 );

/**
 * Add fields mapper to include user info in meta data
 */
function aus__forminator_add_fields_mappers($fields_mappers, $model) {

	$user_id = get_current_user_id();
	$user = get_userdata($user_id);

	if ($user && in_array('administrator', $user->roles)) {
		global $aus__forminator_custom_metas;
		foreach ($aus__forminator_custom_metas as $meta) {
			$fields_mappers[] = array(
				'meta_key' => $meta['key'],
				'label' => $meta['label'],
				'type' => 'text',
			);
		}
	}
	return $fields_mappers;
}
add_action( 'forminator_fields_mappers', 'aus__forminator_add_fields_mappers', 10, 2);

/**
 * Add where query so that non-admin users can see only their own submissions.
 */
function aus__forminator_add_query_entries_where($where, $args) {
	$user_id = get_current_user_id();
	$user = get_userdata($user_id);
	if (!($user && in_array('administrator', $user->roles))) {
		// $where .= " AND entries.user_id = " . $user_id;
		$where .= " AND metas.meta_key = '_aus_user_id' AND metas.meta_value = " . $user_id;
	}
	return $where;
}
add_action( 'forminator_query_entries_where', 'aus__forminator_add_query_entries_where', 10, 2);