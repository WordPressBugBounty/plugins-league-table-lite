<?php
/**
 * Enqueue the Gutenberg block assets for the backend.
 *
 * @package league-table-lite
 */

// Prevent direct access to this file.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * Enqueue the Gutenberg block assets for the backend.
 *
 * 'wp-blocks': includes block type registration and related functions.
 * 'wp-element': includes the WordPress Element abstraction for describing the structure of your blocks.
 */
function daextletal_editor_assets() {

	$shared = daextletal_Shared::get_instance();

	// Styles ---------------------------------------------------------------------------------------------------------.

	// Block.
	wp_enqueue_style(
		'daextletal-editor-css',
		plugins_url( 'css/editor.css', __DIR__ ),
		array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
		$shared->get( 'ver' )
	);

	// Scripts --------------------------------------------------------------------------------------------------------.

	// Block.
	wp_enqueue_script(
		'daextletal-editor-js', // Handle.
		plugins_url( '/build/index.js', __DIR__ ), // We register the block here.
		array( 'wp-blocks', 'wp-element' ), // Dependencies.
		$shared->get( 'ver' ),
		true // Enqueue the script in the footer.
	);

	/*
	 * Add the translations associated with this script in the JED/json format.
	 *
	 * Reference: https://make.wordpress.org/core/2018/11/09/new-javascript-i18n-support-in-wordpress/
	 *
	 * Argument 1: Handler
	 * Argument 2: Domain
	 * Argument 3: Location where the JED/json file is located.
	 *
	 * Note that:
	 *
	 * - The JED/json file should be named [domain]-[locale]-[handle].json to be actually detected by WordPress.
	 * - The JED/json file is generated with https://github.com/mikeedwards/po2json from the .po file
	 */
	wp_set_script_translations( 'daextletal-editor-js', 'daextletal', $shared->get( 'dir' ) . 'blocks/lang' );
}

add_action( 'enqueue_block_editor_assets', 'daextletal_editor_assets' );

/**
 * Dynamic Block Server Component
 *
 * For more info:
 *
 * https://wordpress.org/gutenberg/handbook/blocks/creating-dynamic-blocks/
 *
 * @param array $attributes The block attributes.
 *
 * @return false|string
 */
function daextletal_table_render( $attributes ) {

	if ( isset( $attributes['tableId'] ) ) {
		$public = daextletal_Public::get_instance();
		return $public->display_league_table( array( 'id' => $attributes['tableId'] ) );
	}
}

register_block_type(
	'daextletal/table',
	array(
		'render_callback' => 'daextletal_table_render',
	)
);
