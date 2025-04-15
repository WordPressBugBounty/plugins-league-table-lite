<?php
/**
 * Here the REST API endpoint of the plugin are registered.
 *
 * @package league-table-lite
 */

/**
 * This class should be used to work with the REST API endpoints of the plugin.
 */
class Daextletal_Rest {

	/**
	 * The singleton instance of the class.
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * An instance of the shared class.
	 *
	 * @var Daextletal_Shared|null
	 */
	private $shared = null;

	/**
	 * Constructor.
	 */
	private function __construct() {

		// Assign an instance of the shared class.
		$this->shared = Daextletal_Shared::get_instance();

		/**
		 * Add custom routes to the Rest API.
		 */
		add_action( 'rest_api_init', array( $this, 'rest_api_register_route' ) );
	}

	/**
	 * Create a singleton instance of the class.
	 *
	 * @return self|null
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add custom routes to the Rest API.
	 *
	 * @return void
	 */
	public function rest_api_register_route() {

		// Add the GET 'league-table-lite/v1/options' endpoint to the Rest API.
		register_rest_route(
			'league-table-lite/v1',
			'/read-options/',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_daext_league_table_lite_read_options_callback' ),
				'permission_callback' => array( $this, 'rest_api_daext_league_table_lite_read_options_callback_permission_check' ),
			)
		);

		// Add the POST 'league-table-lite/v1/options' endpoint to the Rest API.
		register_rest_route(
			'league-table-lite/v1',
			'/options',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_daext_league_table_lite_update_options_callback' ),
				'permission_callback' => array( $this, 'rest_api_daext_league_table_lite_update_options_callback_permission_check' ),

			)
		);
	}

	/**
	 * Callback for the GET 'league-table-lite/v1/options' endpoint of the Rest API.
	 *
	 * @return WP_REST_Response
	 */
	public function rest_api_daext_league_table_lite_read_options_callback() {

		// Generate the response.
		$response = array();
		foreach ( $this->shared->get( 'options' ) as $key => $value ) {
			$response[ $key ] = get_option( $key );
		}

		// Prepare the response.
		$response = new WP_REST_Response( $response );

		return $response;
	}

	/**
	 * Check the user capability.
	 *
	 * @return true|WP_Error
	 */
	public function rest_api_daext_league_table_lite_read_options_callback_permission_check() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_read_error',
				'Sorry, you are not allowed to read the League Table options.',
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Callback for the POST 'league-table-lite/v1/options' endpoint of the Rest API.
	 *
	 * This method is in the following contexts:
	 *
	 *  - To update the plugin options in the "Options" menu.
	 *
	 * @param object $request The request data.
	 *
	 * @return WP_REST_Response
	 */
	public function rest_api_daext_league_table_lite_update_options_callback( $request ) {

		// Get and sanitize data --------------------------------------------------------------------------------------.

		$options = array();

		// Get and sanitize data --------------------------------------------------------------------------------------.

		// General ----------------------------------------------------------------------------------------------------.
		$options['daextletal_tables_menu_capability']      = $request->get_param( 'daextletal_tables_menu_capability' ) !== null ? sanitize_key( $request->get_param( 'daextletal_tables_menu_capability' ) ) : null;
		$options['daextletal_tools_menu_capability']       = $request->get_param( 'daextletal_tools_menu_capability' ) !== null ? sanitize_key( $request->get_param( 'daextletal_tools_menu_capability' ) ) : null;
		$options['daextletal_maintenance_menu_capability'] = $request->get_param( 'daextletal_maintenance_menu_capability' ) !== null ? sanitize_key( $request->get_param( 'daextletal_maintenance_menu_capability' ) ) : null;
		$options['daextletal_general_javascript_file_url'] = $request->get_param( 'daextletal_general_javascript_file_url' ) !== null ? esc_url_raw( $request->get_param( 'daextletal_general_javascript_file_url' ) ) : null;
		$options['daextletal_general_stylesheet_file_url'] = $request->get_param( 'daextletal_general_stylesheet_file_url' ) !== null ? esc_url_raw( $request->get_param( 'daextletal_general_stylesheet_file_url' ) ) : null;
		$options['daextletal_tablesorter_library_url']     = $request->get_param( 'daextletal_tablesorter_library_url' ) !== null ? esc_url_raw( $request->get_param( 'daextletal_tablesorter_library_url' ) ) : null;
		$options['daextletal_load_google_font_1']          = $request->get_param( 'daextletal_load_google_font_1' ) !== null ? esc_url_raw( $request->get_param( 'daextletal_load_google_font_1' ) ) : null;
		$options['daextletal_load_google_font_2']          = $request->get_param( 'daextletal_load_google_font_2' ) !== null ? esc_url_raw( $request->get_param( 'daextletal_load_google_font_2' ) ) : null;
		$options['daextletal_max_execution_time']          = $request->get_param( 'daextletal_max_execution_time' ) !== null ? intval( $request->get_param( 'daextletal_max_execution_time' ), 10 ) : null;
		$options['daextletal_limit_shortcode_parsing']     = $request->get_param( 'daextletal_limit_shortcode_parsing' ) !== null ? intval( $request->get_param( 'daextletal_limit_shortcode_parsing' ), 10 ) : null;
		$options['daextletal_verify_single_shortcode']     = $request->get_param( 'daextletal_verify_single_shortcode' ) !== null ? intval( $request->get_param( 'daextletal_verify_single_shortcode' ), 10 ) : null;
		$options['daextletal_widget_text_shortcode']       = $request->get_param( 'daextletal_widget_text_shortcode' ) !== null ? intval( $request->get_param( 'daextletal_widget_text_shortcode' ), 10 ) : null;

		// Cell Properties --------------------------------------------------------------------------------------------.
		$options['daextletal_enable_link_cell_property']                          = $request->get_param( 'daextletal_enable_link_cell_property' ) !== null ? intval( $request->get_param( 'daextletal_enable_link_cell_property' ), 10 ) : null;
		$options['daextletal_enable_image_left_cell_property']                    = $request->get_param( 'daextletal_enable_image_left_cell_property' ) !== null ? intval( $request->get_param( 'daextletal_enable_image_left_cell_property' ), 10 ) : null;
		$options['daextletal_enable_image_right_cell_property']                   = $request->get_param( 'daextletal_enable_image_right_cell_property' ) !== null ? intval( $request->get_param( 'daextletal_enable_image_right_cell_property' ), 10 ) : null;

		// Update the options -----------------------------------------------------------------------------------------.
		foreach ( $options as $key => $option ) {
			if ( null !== $option ) {
				update_option( $key, $option );
			}
		}

		$response = new WP_REST_Response( 'Data successfully added.', '200' );

		return $response;
	}

	/**
	 * Check the user capability.
	 *
	 * @return true|WP_Error
	 */
	public function rest_api_daext_league_table_lite_update_options_callback_permission_check() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_update_error',
				'Sorry, you are not allowed to update the League Table options.',
				array( 'status' => 403 )
			);
		}

		return true;
	}
}
