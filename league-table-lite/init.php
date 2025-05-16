<?php
/**
 * Plugin Name: League Table
 * Description: Generates tables in your WordPress blog. (Lite version)
 * Version: 1.19
 * Author: DAEXT
 * Author URI: https://daext.com
 * Text Domain: league-table-lite
 * License: GPLv3
 *
 * @package league-table-lite
 */

// Prevent direct access to this file.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// Set constants.
define( 'DAEXTLETAL_EDITION', 'FREE' );

// Shared across public and admin.
require_once plugin_dir_path( __FILE__ ) . 'shared/class-daextletal-shared.php';

// Rest API.
require_once plugin_dir_path( __FILE__ ) . 'inc/class-daextletal-rest.php';
add_action( 'plugins_loaded', array( 'Daextletal_Rest', 'get_instance' ) );

require_once plugin_dir_path( __FILE__ ) . 'public/class-daextletal-public.php';
add_action( 'plugins_loaded', array( 'Daextletal_Public', 'get_instance' ) );

// Perform the Gutenberg related activities only if Gutenberg is present.
if ( function_exists( 'register_block_type' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'blocks/src/init.php';
}

// Admin.
require_once plugin_dir_path( __FILE__ ) . 'admin/class-daextletal-admin.php';

// If it's the admin area and this is not an AJAX request, create a new singleton instance of the admin class.
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	add_action( 'plugins_loaded', array( 'daextletal_Admin', 'get_instance' ) );
}

// Activate.
register_activation_hook( __FILE__, array( 'Daextletal_Admin', 'ac_activate' ) );

// Update the plugin db tables and options if they are not up-to-date.
Daextletal_Admin::ac_create_database_tables();
Daextletal_Admin::ac_initialize_options();

// Register AJAX actions.
if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

	// Admin.
	require_once plugin_dir_path( __FILE__ ) . 'class-daextletal-ajax.php';
	add_action( 'plugins_loaded', array( 'daextletal_Ajax', 'get_instance' ) );

}

/**
 * Customize the action links in the "Plugins" menu.
 *
 * @param array $actions An array of plugin action links.
 *
 * @return mixed
 */
function daextletal_customize_action_links( $actions ) {
	$actions[] = '<a href="https://daext.com/league-table/" target="_blank">' . esc_html__( 'Buy the Pro Version', 'league-table-lite' ) . '</a>';
	return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'daextletal_customize_action_links' );

/**
 * Load the plugin text domain for translation.
 *
 * @return void
 */
function daextletal_load_plugin_textdomain() {
	load_plugin_textdomain( 'league-table-lite', false, 'league-table-lite/lang/' );
}

add_action( 'init', 'daextletal_load_plugin_textdomain' );
