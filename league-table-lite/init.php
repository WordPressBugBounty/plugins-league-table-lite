<?php
/**
 * Plugin Name: League Table
 * Description: Generates tables in your WordPress blog. (Lite version)
 * Version: 1.16
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

// Shared across public and admin.
require_once plugin_dir_path( __FILE__ ) . 'shared/class-daextletal-shared.php';

require_once plugin_dir_path( __FILE__ ) . 'public/class-daextletal-public.php';
add_action( 'plugins_loaded', array( 'Daextletal_Public', 'get_instance' ) );

// Admin.
if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-daextletal-admin.php' );

	// If this is not an AJAX request, create a new singleton instance of the admin class.
	if(! defined( 'DOING_AJAX' ) || ! DOING_AJAX ){
		add_action( 'plugins_loaded', array( 'Daextletal_Admin', 'get_instance' ) );
	}

	// Activate the plugin using only the class static methods.
	register_activation_hook( __FILE__, array( 'Daextletal_Admin', 'ac_activate' ) );

}

// Ajax.
if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

	// Admin.
	require_once plugin_dir_path( __FILE__ ) . 'class-daextletal-ajax.php';
	add_action( 'plugins_loaded', array( 'Daextletal_Ajax', 'get_instance' ) );

}

/**
 * Customize the action links in the "Plugins" menu.
 *
 * @param array $actions An array of plugin action links.
 *
 * @return mixed
 */
function daextletal_customize_action_links( $actions ) {
	$actions[] = '<a href="https://daext.com/league-table/">' . esc_html__( 'Buy the Pro Version', 'league-table-lite' ) . '</a>';
	return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'daextletal_customize_action_links' );
