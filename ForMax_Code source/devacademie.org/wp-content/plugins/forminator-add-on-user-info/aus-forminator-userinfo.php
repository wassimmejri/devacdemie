<?php
/**
 * Plugin Name: Forminator Add-on : User Info
 * Requires Plugins: forminator
 * Version: 1.0.0
 * Description: Log user information on submission and enables [view-only-own-submissions for non-admin users] functon.
 * Author: auslee986
 * Author URI: https://profiles.wordpress.org/auslee986/
 * Requires at least: 6.4
 * Tested up to: 6.6
 * Stable tag: 1.0.0
 * Requires PHP: 7.4
 * Text Domain: forminator-add-on-user-info
 * License: GPL v3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Aus Forminator Addon Userinfo
 */

/*
Copyright 2024-2028 Auslee (https://profiles.wordpress.org/auslee986/)
Author – Auslee

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 3 – GPLv3) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$aus__forminator_custom_metas = array(
	array(
		'key' => '_aus_user_email',
		'label' => 'User Email',
	),
	array(
		'key' => '_aus_user_user_login',
		'label' => 'User Login',
	),
	array(
		'key' => '_aus_user_fullname',
		'label' => 'User Full Name',
	),
	array(
		'key' => '_aus_user_displayname',
		'label' => 'User Display Name',
	),
	array(
		'key' => '_aus_user_id',
		'label' => 'User ID',
	),
);

// Include Hooks.
include plugin_dir_path( __FILE__ ) . 'inc/hooks__forminator.php';
