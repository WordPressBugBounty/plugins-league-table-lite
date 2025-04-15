<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package league-table-lite
 */

/**
 * This class should be used to work with the administrative side of WordPress.
 */
class Daextletal_Admin {

	/**
	 * The instance of this class.
	 *
	 * @var self
	 */
	protected static $instance = null;

	/**
	 * The instance of the plugin info.
	 *
	 * @var Daextletal_Shared
	 */
	private $shared = null;

	/**
	 * The screen id for the tables menu.
	 *
	 * @var string
	 */
	private $screen_id_tables = null;

	/**
	 * The screen id of the "Tools" menu.
	 *
	 * @var null
	 */
	private $screen_id_tools = null;

	/**
	 * The screen id of the "Maintenance" menu.
	 *
	 * @var null
	 */
	private $screen_id_maintenance = null;

	/**
	 * The screen id for the options menu.
	 *
	 * @var string
	 */
	private $screen_id_options = null;

	/**
	 * Instance of the class used to generate the back-end menus.
	 *
	 * @var null
	 */
	private $menu_elements = null;

	/**
	 * The constructor.
	 */
	private function __construct() {

		// Assign an instance of the plugin info.
		$this->shared = Daextletal_Shared::get_instance();

		// Load admin stylesheets and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the admin menu.
		add_action( 'admin_menu', array( $this, 'me_add_admin_menu' ) );

		// This hook is triggered during the creation of a new blog.
		add_action( 'wpmu_new_blog', array( $this, 'new_blog_create_options_and_tables' ), 10, 6 );

		// This hook is triggered during the deletion of a blog.
		add_action( 'delete_blog', array( $this, 'delete_blog_delete_options_and_tables' ), 10, 1 );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce non-necessary for menu selection.
		$page_query_param = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : null;

		// Require and instantiate the class used to register the menu options.
		if ( null !== $page_query_param ) {

			$config = array(
				'admin_toolbar' => array(
					'items'      => array(
						array(
							'link_text' => __( 'Tables', 'league-table-lite' ),
							'link_url'  => admin_url( 'admin.php?page=daextletal-tables' ),
							'icon'      => 'list',
							'menu_slug' => 'daextletal-table',
						),
						array(
							'link_text' => __( 'Tools', 'league-table-lite' ),
							'link_url'  => admin_url( 'admin.php?page=daextletal-tools' ),
							'icon'      => 'tool-02',
							'menu_slug' => 'daextletal-tool',
						),
						array(
							'link_text' => __( 'Maintenance', 'league-table-lite' ),
							'link_url'  => admin_url( 'admin.php?page=daextletal-maintenance' ),
							'icon'      => 'settings-01',
							'menu_slug' => 'daextletal-maintenance',
						),
					),
					'more_items' => array(
						array(
							'link_text' => __( 'Options', 'league-table-lite' ),
							'link_url'  => admin_url( 'admin.php?page=daextletal-options' ),
							'pro_badge' => false,
						),
					),
				),
			);

			// The parent class.
			require_once $this->shared->get( 'dir' ) . 'admin/inc/menu/class-daextletal-menu-elements.php';

			// Use the correct child class based on the page query parameter.
			if ( 'daextletal-tables' === $page_query_param ) {
				require_once $this->shared->get( 'dir' ) . 'admin/inc/menu/child/class-daextletal-tables-menu-elements.php';
				$this->menu_elements = new Daextletal_Tables_Menu_Elements( $this->shared, $page_query_param, $config );
			}
			if ( 'daextletal-tools' === $page_query_param ) {
				require_once $this->shared->get( 'dir' ) . 'admin/inc/menu/child/class-daextletal-tools-menu-elements.php';
				$this->menu_elements = new Daextletal_Tools_Menu_Elements( $this->shared, $page_query_param, $config );
			}
			if ( 'daextletal-maintenance' === $page_query_param ) {
				require_once $this->shared->get( 'dir' ) . 'admin/inc/menu/child/class-daextletal-maintenance-menu-elements.php';
				$this->menu_elements = new Daextletal_Maintenance_Menu_Elements( $this->shared, $page_query_param, $config );
			}
			if ( 'daextletal-options' === $page_query_param ) {
				require_once $this->shared->get( 'dir' ) . 'admin/inc/menu/child/class-daextletal-options-menu-elements.php';
				$this->menu_elements = new Daextletal_Options_Menu_Elements( $this->shared, $page_query_param, $config );
			}

		}

	}

	/**
	 * Return an instance of this class.
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
	 * Enqueue admin-specific styles.
	 *
	 * @return void
	 */
	public function enqueue_admin_styles() {

		$screen = get_current_screen();

		// Menu tables.
		if ( $screen->id === $this->screen_id_tables ) {

			wp_enqueue_style( $this->shared->get( 'slug' ) . '-framework-menu', $this->shared->get( 'url' ) . 'admin/assets/css/framework-menu/main.css', array(), $this->shared->get( 'ver' ) );

			// jQuery UI Dialog.
			wp_enqueue_style(
				$this->shared->get( 'slug' ) . '-jquery-ui-dialog',
				$this->shared->get( 'url' ) . 'admin/assets/css/jquery-ui-dialog.css',
				array(),
				$this->shared->get( 'ver' )
			);

			wp_enqueue_style( $this->shared->get( 'slug' ) . '-handsontable-full', $this->shared->get( 'url' ) . 'admin/assets/inc/handsontable/handsontable.full.min.css', array(), $this->shared->get( 'ver' ) );
			wp_enqueue_style( 'wp-color-picker' );

			// Select2.
			wp_enqueue_style(
				$this->shared->get( 'slug' ) . '-select2',
				$this->shared->get( 'url' ) . 'admin/assets/inc/select2/css/select2.min.css',
				array(),
				$this->shared->get( 'ver' )
			);

		}

		// Menu Tools.
		if ( $screen->id === $this->screen_id_tools ) {

			wp_enqueue_style( $this->shared->get( 'slug' ) . '-framework-menu', $this->shared->get( 'url' ) . 'admin/assets/css/framework-menu/main.css', array(), $this->shared->get( 'ver' ) );

		}

		// Menu Maintenance.
		if ( $screen->id === $this->screen_id_maintenance ) {

			wp_enqueue_style( $this->shared->get( 'slug' ) . '-framework-menu', $this->shared->get( 'url' ) . 'admin/assets/css/framework-menu/main.css', array(), $this->shared->get( 'ver' ) );

			// Select2.
			wp_enqueue_style(
				$this->shared->get( 'slug' ) . '-select2',
				$this->shared->get( 'url' ) . 'admin/assets/inc/select2/css/select2.min.css',
				array(),
				$this->shared->get( 'ver' )
			);

			// jQuery UI Dialog.
			wp_enqueue_style(
				$this->shared->get( 'slug' ) . '-jquery-ui-dialog',
				$this->shared->get( 'url' ) . 'admin/assets/css/jquery-ui-dialog.css',
				array(),
				$this->shared->get( 'ver' )
			);

		}

		// Menu Options.
		if ( $screen->id === $this->screen_id_options ) {

			wp_enqueue_style( $this->shared->get( 'slug' ) . '-framework-menu', $this->shared->get( 'url' ) . 'admin/assets/css/framework-menu/main.css', array( 'wp-components' ), $this->shared->get( 'ver' ) );

		}
	}

	/**
	 * Enqueue admin-specific javascript.
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts() {

		$screen = get_current_screen();

		$wp_localize_script_data = array(
			'deleteText'         => esc_html__( 'Delete', 'league-table-lite' ),
			'cancelText'         => esc_html__( 'Cancel', 'league-table-lite' ),
			'chooseAnOptionText' => esc_html__( 'Choose an Option ...', 'league-table-lite' ),
			'closeText'          => esc_html__( 'Close', 'league-table-lite' ),
			'postText'           => esc_html__( 'Post', 'league-table-lite' ),
			'itemsText'          => esc_html__( 'items', 'league-table-lite' ),
			'dateTooltipText'    => esc_html__( 'The date of the feedback.', 'league-table-lite' ),
			'ratingTooltipText'  => esc_html__( 'The rating received by the feedback.', 'league-table-lite' ),
			'commentTooltipText' => esc_html__( 'The comment associated with the feedback.', 'league-table-lite' ),
		);

		// Menu tables.
		if ( $screen->id === $this->screen_id_tables ) {

			wp_enqueue_script( $this->shared->get( 'slug' ) . '-handsontable-full', $this->shared->get( 'url' ) . 'admin/assets/inc/handsontable/handsontable.full.min.js', array( 'jquery' ), $this->shared->get( 'ver' ), true );
			wp_enqueue_script( $this->shared->get( 'slug' ) . '-tables-menu-utility', $this->shared->get( 'url' ) . 'admin/assets/js/tables/utility.js', array( 'jquery', 'jquery-ui-dialog' ), $this->shared->get( 'ver' ), true );
			wp_enqueue_script( $this->shared->get( 'slug' ) . '-tables-menu-context-menu', $this->shared->get( 'url' ) . 'admin/assets/js/tables/context-menu.js', array( 'jquery', 'jquery-ui-dialog', 'daextletal-tables-menu-utility' ), $this->shared->get( 'ver' ), true );
			wp_enqueue_script( $this->shared->get( 'slug' ) . '-init', $this->shared->get( 'url' ) . 'admin/assets/js/tables/init.js', array( 'jquery', 'jquery-ui-dialog', 'daextletal-tables-menu-utility' ), $this->shared->get( 'ver' ), true );
			wp_enqueue_script(
				$this->shared->get( 'slug' ) . '-select2',
				$this->shared->get( 'url' ) . 'admin/assets/inc/select2/js/select2.min.js',
				array( 'jquery' ),
				$this->shared->get( 'ver' ),
				true
			);
			wp_enqueue_script( $this->shared->get( 'slug' ) . '-wp-color-picker-init', $this->shared->get( 'url' ) . 'admin/assets/js/wp-color-picker-init.js', array( 'jquery', 'wp-color-picker' ), $this->shared->get( 'ver' ), true );
			wp_enqueue_media();
			wp_enqueue_script( $this->shared->get( 'slug' ) . '-media-uploader', $this->shared->get( 'url' ) . 'admin/assets/js/media-uploader.js', array( 'jquery' ), $this->shared->get( 'ver' ), true );

			// Pass the objectL10n object to this javascript file.
			wp_localize_script(
				$this->shared->get( 'slug' ) . '-tables-menu-utility',
				'objectL10n',
				array(
					'column'                               => wp_strip_all_tags( __( 'Column', 'league-table-lite' ) ),
					'name'                                 => wp_strip_all_tags( __( 'Name', 'league-table-lite' ) ),
					'description'                          => wp_strip_all_tags( __( 'Description', 'league-table-lite' ) ),
					'rows'                                 => wp_strip_all_tags( __( 'Rows', 'league-table-lite' ) ),
					'columns'                              => wp_strip_all_tags( __( 'Columns', 'league-table-lite' ) ),
					'position_label'                       => wp_strip_all_tags( __( 'Position Label', 'league-table-lite' ) ),
					'table_width_value'                    => wp_strip_all_tags( __( 'Table Width Value', 'league-table-lite' ) ),
					'table_minimum_width'                  => wp_strip_all_tags( __( 'Table Minimum Width', 'league-table-lite' ) ),
					'column_width_value'                   => wp_strip_all_tags( __( 'Column Width Value', 'league-table-lite' ) ),
					'container_width'                      => wp_strip_all_tags( __( 'Container Width', 'league-table-lite' ) ),
					'container_height'                     => wp_strip_all_tags( __( 'Container Height', 'league-table-lite' ) ),
					'table_margin_top'                     => wp_strip_all_tags( __( 'Table Margin Top', 'league-table-lite' ) ),
					'table_margin_bottom'                  => wp_strip_all_tags( __( 'Table Margin Bottom', 'league-table-lite' ) ),
					'header_font_size'                     => wp_strip_all_tags( __( 'Header Font Size', 'league-table-lite' ) ),
					'header_font_family'                   => wp_strip_all_tags( __( 'Header Font Family', 'league-table-lite' ) ),
					'header_background_color'              => wp_strip_all_tags( __( 'Header Background Color', 'league-table-lite' ) ),
					'header_font_color'                    => wp_strip_all_tags( __( 'Header Font Color', 'league-table-lite' ) ),
					'header_link_color'                    => wp_strip_all_tags( __( 'Header Link Color', 'league-table-lite' ) ),
					'header_border_color'                  => wp_strip_all_tags( __( 'Header Border Color', 'league-table-lite' ) ),
					'body_font_size'                       => wp_strip_all_tags( __( 'Body Font Size', 'league-table-lite' ) ),
					'body_font_family'                     => wp_strip_all_tags( __( 'Body Font Family', 'league-table-lite' ) ),
					'even_rows_background_color'           => wp_strip_all_tags( __( 'Even Rows Background Color', 'league-table-lite' ) ),
					'odd_rows_background_color'            => wp_strip_all_tags( __( 'Odd Rows Background Color', 'league-table-lite' ) ),
					'even_rows_font_color'                 => wp_strip_all_tags( __( 'Even Rows Font Color', 'league-table-lite' ) ),
					'odd_rows_font_color'                  => wp_strip_all_tags( __( 'Odd Rows Font Color', 'league-table-lite' ) ),
					'even_rows_link_color'                 => wp_strip_all_tags( __( 'Even Rows Link Color', 'league-table-lite' ) ),
					'odd_rows_link_color'                  => wp_strip_all_tags( __( 'Odd Rows Link Color', 'league-table-lite' ) ),
					'rows_border_color'                    => wp_strip_all_tags( __( 'Rows Border Color', 'league-table-lite' ) ),
					'autoalignment_affected_rows_left'     => wp_strip_all_tags( __( 'Affected Rows (Left)', 'league-table-lite' ) ),
					'autoalignment_affected_rows_center'   => wp_strip_all_tags( __( 'Affected Rows (Center)', 'league-table-lite' ) ),
					'autoalignment_affected_rows_right'    => wp_strip_all_tags( __( 'Affected Rows (Right)', 'league-table-lite' ) ),
					'autoalignment_affected_columns_left'  => wp_strip_all_tags( __( 'Affected Columns (Left)', 'league-table-lite' ) ),
					'autoalignment_affected_columns_center' => wp_strip_all_tags( __( 'Affected Columns (Center)', 'league-table-lite' ) ),
					'autoalignment_affected_columns_right' => wp_strip_all_tags( __( 'Affected Columns (Right)', 'league-table-lite' ) ),
					'tablet_breakpoint'                    => wp_strip_all_tags( __( 'Tablet Breakpoint', 'league-table-lite' ) ),
					'hide_tablet_list'                     => wp_strip_all_tags( __( 'Tablet Hide List', 'league-table-lite' ) ),
					'tablet_header_font_size'              => wp_strip_all_tags( __( 'Tablet Header Font Size', 'league-table-lite' ) ),
					'tablet_body_font_size'                => wp_strip_all_tags( __( 'Tablet Body Font Size', 'league-table-lite' ) ),
					'phone_breakpoint'                     => wp_strip_all_tags( __( 'Phone Breakpoint', 'league-table-lite' ) ),
					'hide_phone_list'                      => wp_strip_all_tags( __( 'Phone Hide List', 'league-table-lite' ) ),
					'phone_header_font_size'               => wp_strip_all_tags( __( 'Phone Header Font Size', 'league-table-lite' ) ),
					'phone_body_font_size'                 => wp_strip_all_tags( __( 'Phone Body Font Size', 'league-table-lite' ) ),
					'text_color'                           => wp_strip_all_tags( __( 'Text Color', 'league-table-lite' ) ),
					'background_color'                     => wp_strip_all_tags( __( 'Background Color', 'league-table-lite' ) ),
					'link'                                 => wp_strip_all_tags( __( 'Link', 'league-table-lite' ) ),
					'link_color'                           => wp_strip_all_tags( __( 'Link Color', 'league-table-lite' ) ),
					'image_left'                           => wp_strip_all_tags( __( 'Image Left', 'league-table-lite' ) ),
					'image_right'                          => wp_strip_all_tags( __( 'Image Right', 'league-table-lite' ) ),
					'update_cell_properties'               => wp_strip_all_tags( __( 'Update Cell Properties', 'league-table-lite' ) ),
					'add_cell_properties'                  => wp_strip_all_tags( __( 'Add Cell Properties', 'league-table-lite' ) ),
					'cell_properties_added_message'        => wp_strip_all_tags( __( 'The cell properties have been successfully added.', 'league-table-lite' ) ),
					'cell_properties_updated_message'      => wp_strip_all_tags( __( 'The cell properties have been successfully updated.', 'league-table-lite' ) ),
					'cell_properties_reset_message'        => wp_strip_all_tags( __( 'The cell properties have been successfully deleted.', 'league-table-lite' ) ),
					'cell_properties_error_partial_message' => wp_strip_all_tags( __( 'Please enter valid values in the following fields:', 'league-table-lite' ) ),
					'table_success'                        => wp_strip_all_tags( __( 'The table has been successfully updated.', 'league-table-lite' ) ),
					'table_error_partial_message'          => wp_strip_all_tags( __( 'Please enter valid values in the following fields:', 'league-table-lite' ) ),
					'insert_row_above'                     => wp_strip_all_tags( __( 'Insert Row Above', 'league-table-lite' ) ),
					'insert_row_below'                     => wp_strip_all_tags( __( 'Insert Row Below', 'league-table-lite' ) ),
					'insert_column_left'                   => wp_strip_all_tags( __( 'Insert Column Left', 'league-table-lite' ) ),
					'insert_column_right'                  => wp_strip_all_tags( __( 'Insert Column Right', 'league-table-lite' ) ),
					'remove_row'                           => wp_strip_all_tags( __( 'Remove Row', 'league-table-lite' ) ),
					'remove_column'                        => wp_strip_all_tags( __( 'Remove Column', 'league-table-lite' ) ),
					'copy_data'                            => wp_strip_all_tags( __( 'Copy Data', 'league-table' ) ),
					'cut_data'                             => wp_strip_all_tags( __( 'Cut Data', 'league-table' ) ),
					'paste_data'                           => wp_strip_all_tags( __( 'Paste Data', 'league-table' ) ),
					'copy_to_spreadsheet_clipboard'        => wp_strip_all_tags( __( 'Copy to Spreadsheet Clipboard', 'league-table-lite' ) ),
					'paste_spreadsheet_clipboard_cell_data' => wp_strip_all_tags( __( 'Paste Spreadsheet Clipboard Cell Data', 'league-table-lite' ) ),
					'paste_spreadsheet_clipboard_cell_properties' => wp_strip_all_tags( __( 'Paste Spreadsheet Clipboard Cell Properties', 'league-table-lite' ) ),
					'paste_spreadsheet_clipboard_cell_data_and_cell_properties' => wp_strip_all_tags( __( 'Paste Spreadsheet Clipboard Cell Data and Cell Properties', 'league-table-lite' ) ),
					'reset_data'                           => wp_strip_all_tags( __( 'Reset Data', 'league-table-lite' ) ),
					'reset_cell_properties'                => wp_strip_all_tags( __( 'Reset Cell Properties', 'league-table-lite' ) ),
					'reset_data_and_cell_properties'       => wp_strip_all_tags( __( 'Reset Data and Cell Properties', 'league-table-lite' ) ),
					'delete'                               => wp_strip_all_tags( __( 'Delete', 'league-table-lite' ) ),
					'cancel'                               => wp_strip_all_tags( __( 'Cancel', 'league-table-lite' ) ),
				)
			);

			// Store the JavaScript parameters in the window.DAEXTLETAL_PARAMETERS object.
			$initialization_script  = 'window.DAEXTLETAL_PARAMETERS = {';
			$initialization_script .= 'ajax_url: "' . admin_url( 'admin-ajax.php' ) . '",';
			$initialization_script .= 'nonce: "' . wp_create_nonce( 'daextletal' ) . '",';
			$initialization_script .= 'admin_url: "' . get_admin_url() . '"';
			$initialization_script .= '};';
			if ( false !== $initialization_script ) {
				wp_add_inline_script( $this->shared->get( 'slug' ) . '-tables-menu-utility', $initialization_script, 'before' );
			}

			wp_enqueue_script( $this->shared->get( 'slug' ) . '-menu', $this->shared->get( 'url' ) . 'admin/assets/js/framework-menu/menu.js', array( 'jquery' ), $this->shared->get( 'ver' ), true );

		}

		// Menu tools.
		if ( $screen->id === $this->screen_id_tools ) {

			wp_enqueue_script( $this->shared->get( 'slug' ) . '-menu', $this->shared->get( 'url' ) . 'admin/assets/js/framework-menu/menu.js', array( 'jquery' ), $this->shared->get( 'ver' ), true );

		}

		// Menu Maintenance.
		if ( $screen->id === $this->screen_id_maintenance ) {

			// Select2.
			wp_enqueue_script(
				$this->shared->get( 'slug' ) . '-select2',
				$this->shared->get( 'url' ) . 'admin/assets/inc/select2/js/select2.min.js',
				array( 'jquery' ),
				$this->shared->get( 'ver' ),
				true
			);

			wp_enqueue_script( $this->shared->get( 'slug' ) . '-menu', $this->shared->get( 'url' ) . 'admin/assets/js/framework-menu/menu.js', array( 'jquery' ), $this->shared->get( 'ver' ), true );

			// Maintenance Menu.
			wp_enqueue_script(
				$this->shared->get( 'slug' ) . '-menu-maintenance',
				$this->shared->get( 'url' ) . 'admin/assets/js/menu-maintenance.js',
				array( 'jquery', 'jquery-ui-dialog', $this->shared->get( 'slug' ) . '-select2' ),
				$this->shared->get( 'ver' ),
				true
			);
			wp_localize_script(
				$this->shared->get( 'slug' ) . '-menu-maintenance',
				'objectL10n',
				$wp_localize_script_data
			);

		}

		// Menu Options.
		if ( $screen->id === $this->screen_id_options ) {

			// Store the JavaScript parameters in the window.DAEXTLETAL_PARAMETERS object.
			$initialization_script  = 'window.DAEXTLETAL_PARAMETERS = {';
			$initialization_script .= 'options_configuration_pages: ' . wp_json_encode( $this->shared->menu_options_configuration() );
			$initialization_script .= '};';

			wp_enqueue_script(
				$this->shared->get( 'slug' ) . '-menu-options',
				$this->shared->get( 'url' ) . 'admin/react/options-menu/build/index.js',
				array( 'wp-element', 'wp-api-fetch', 'wp-i18n', 'wp-components' ),
				$this->shared->get( 'ver' ),
				true
			);

			wp_add_inline_script( $this->shared->get( 'slug' ) . '-menu-options', $initialization_script, 'before' );

			wp_enqueue_script( $this->shared->get( 'slug' ) . '-menu', $this->shared->get( 'url' ) . 'admin/assets/js/framework-menu/menu.js', array( 'jquery' ), $this->shared->get( 'ver' ), true );

		}

		// Enqueue scripts in all the post types of the post editor.
		$args               = array(
			'show_ui' => true,
		);
		$post_types_with_ui = get_post_types( $args );
		unset( $post_types_with_ui['attachment'] );

		if ( in_array( $screen->id, $post_types_with_ui, true ) && current_user_can( get_option( $this->shared->get( 'slug' ) . '_tables_menu_capability' ) ) ) {

			// Store the JavaScript parameters in the window.DAEXTLETAL_PARAMETERS object.
			$this->shared->add_global_javascript_parameters( 'post' );

			/**
			 * When the editor file is loaded (only in the post editor) add the names and IDs of all the restrictions as
			 * json data in a property of the window.DAEXTREBL_PARAMETERS object.
			 *
			 * These data are used to populate the "Restrictions" selector available in the inspector of all the blocks.
			 */
			global $wpdb;

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$table_a = $wpdb->get_results(
				"SELECT id, name FROM {$wpdb->prefix}daextletal_table WHERE temporary = 0 ORDER BY id DESC",
				ARRAY_A
			);

			$table_a_alt   = array();
			$table_a_alt[] = array(
				'value' => '0',
				'label' => __( 'None', 'league-table-lite' ),
			);
			foreach ( $table_a as $key => $value ) {
				$table_a_alt[] = array(
					'value' => intval( $value['id'], 10 ),
					'label' => stripslashes( $value['name'] ),
				);
			}

			// Store the JavaScript parameters in the window.DAEXTREBL_PARAMETERS object.
			$initialization_script  = 'window.DAEXTLETAL_PARAMETERS = {';
			$initialization_script .= 'tables: ' . wp_json_encode( $table_a_alt );
			$initialization_script .= '};';
			wp_add_inline_script( $this->shared->get( 'slug' ) . '-editor-js', $initialization_script, 'before' );

		}

	}

	/**
	 * Plugin activation.
	 *
	 * @param bool $networkwide Whether to activate the plugin for all sites in the network.
	 *
	 * @return void
	 */
	public static function ac_activate( $networkwide ) {

		/**
		 * Create options and tables for all the sites in the network.
		 */
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			/**
			 * If this is a "Network Activation" create the options and tables
			 * for each blog.
			 */
			if ( $networkwide ) {

				// Get the current blog id.
				global $wpdb;
				$current_blog = $wpdb->blogid;

				// create an array with all the blog ids.
				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ); // phpcs:ignore

				// Iterate through all the blogs.
				foreach ( $blogids as $blog_id ) {

					// Switch to the iterated blog.
					switch_to_blog( $blog_id );

					// Create options and tables for the iterated blog.
					self::ac_initialize_options();
					self::ac_create_database_tables();

				}

				// Switch to the current blog.
				switch_to_blog( $current_blog );

			} else {

				/**
				 * If this is not a "Network Activation" create options and
				 * tables only for the current blog.
				 */
				self::ac_initialize_options();
				self::ac_create_database_tables();

			}
		} else {

			/**
			 * If this is not a multisite installation create options and
			 * tables only for the current blog.
			 */
			self::ac_initialize_options();
			self::ac_create_database_tables();

		}
	}

	/**
	 * Create the options and tables for the newly created blog.
	 *
	 * @param int $blog_id The id of the new blog.
	 *
	 * @return void
	 */
	public function new_blog_create_options_and_tables( $blog_id ) {

		global $wpdb;

		/**
		 * If the plugin is "Network Active" create the options and tables for
		 * this new blog.
		 */
		if ( is_plugin_active_for_network( 'league-table/init.php' ) ) {

			// Get the id of the current blog.
			$current_blog = $wpdb->blogid;

			// Switch to the blog that is being activated.
			switch_to_blog( $blog_id );

			// Create options and database tables for the new blog.
			$this->ac_initialize_options();
			$this->ac_create_database_tables();

			// Switch to the current blog.
			switch_to_blog( $current_blog );

		}
	}

	/**
	 * Delete options and tables for the deleted blog.
	 *
	 * @param int $blog_id The id of the blog that is being deleted.
	 *
	 * @return void
	 */
	public function delete_blog_delete_options_and_tables( $blog_id ) {

		global $wpdb;

		// Get the id of the current blog.
		$current_blog = $wpdb->blogid;

		// Switch to the blog that is being activated.
		switch_to_blog( $blog_id );

		// Create options and database tables for the new blog.
		$this->un_delete_options();
		$this->un_delete_database_tables();

		// Switch to the current blog.
		switch_to_blog( $current_blog );
	}

	/**
	 * Initialize plugin options.
	 *
	 * @return void
	 */
	public static function ac_initialize_options() {

		if ( intval( get_option( 'daextletal_options_version' ), 10 ) < 2 ) {

			// Assign an instance of Daextletal_Shared.
			$shared = Daextletal_Shared::get_instance();

			foreach ( $shared->get( 'options' ) as $key => $value ) {
				add_option( $key, $value );
			}

			// Update options version.
			update_option( 'daextletal_options_version', '2' );

		}

	}

	/**
	 * Create the plugin database tables.
	 *
	 * @return void
	 */
	public static function ac_create_database_tables() {

		// Check database version and create the database.
		if ( intval( get_option( 'daextletal_database_version' ), 10 ) < 2 ) {

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			// Create *prefix*_table.
			global $wpdb;
			$sql = "CREATE TABLE {$wpdb->prefix}daextletal_table (
                  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                  temporary TINYINT(1) UNSIGNED DEFAULT 0,
                  name VARCHAR(255) DEFAULT 'Table Name',
                  description VARCHAR(255) DEFAULT 'Table Description',
                  `rows` INT UNSIGNED DEFAULT 10,
                  columns INT UNSIGNED DEFAULT 10,
                  show_position TINYINT(1) UNSIGNED DEFAULT 0,
                  position_side VARCHAR(5) DEFAULT 'left',
                  order_by INT UNSIGNED DEFAULT 1,
                  order_desc_asc TINYINT(1) UNSIGNED DEFAULT 0,
                  order_data_type VARCHAR(10) DEFAULT 'auto',
                  order_date_format VARCHAR(8) DEFAULT 'ddmmyyyy',
                  table_layout TINYINT(1) UNSIGNED DEFAULT 0,
                  table_width INT UNSIGNED DEFAULT 0,
                  table_width_value INT UNSIGNED DEFAULT 400,
                  table_minimum_width INT UNSIGNED DEFAULT 0,
                  column_width TINYINT(1) UNSIGNED DEFAULT 0,
                  column_width_value VARCHAR(2000) DEFAULT '100',
                  table_margin_top INT UNSIGNED DEFAULT 20,
                  table_margin_bottom INT UNSIGNED DEFAULT 20,
                  enable_container TINYINT(1) UNSIGNED DEFAULT 0,
                  container_width INT UNSIGNED DEFAULT 400,
                  container_height INT UNSIGNED DEFAULT 400,
                  header_background_color VARCHAR(7) DEFAULT '#C3512F',
                  header_font_color VARCHAR(7) DEFAULT '#FFFFFF',
                  header_link_color VARCHAR(7) DEFAULT '#FFFFFF',
                  even_rows_background_color VARCHAR(7) DEFAULT '#FFFFFF',
                  even_rows_font_color VARCHAR(7) DEFAULT '#666666',
                  even_rows_link_color VARCHAR(7) DEFAULT '#C3512F',
                  odd_rows_background_color VARCHAR(7) DEFAULT '#FCFCFC',
                  odd_rows_font_color VARCHAR(7) DEFAULT '#666666',
                  odd_rows_link_color VARCHAR(7) DEFAULT '#C3512F',
                  header_border_color VARCHAR(7) DEFAULT '#B34A2A',
                  header_position_alignment VARCHAR(6) DEFAULT 'center',
                  rows_border_color VARCHAR(7) DEFAULT '#E1E1E1',
                  phone_breakpoint INT UNSIGNED DEFAULT 479,
                  tablet_breakpoint INT UNSIGNED DEFAULT 989,
                  position_label VARCHAR(255) DEFAULT '#',
                  number_format TINYINT(1) UNSIGNED DEFAULT 0,
                  enable_sorting TINYINT(1) UNSIGNED DEFAULT 0,
                  enable_manual_sorting TINYINT(1) UNSIGNED DEFAULT 0,
                  show_header TINYINT(1) UNSIGNED DEFAULT 1,
                  header_font_size INT UNSIGNED DEFAULT 11,
                  header_font_family VARCHAR(255) DEFAULT '''Open Sans'', Helvetica, Arial, sans-serif',
                  header_font_weight VARCHAR(3) DEFAULT 400,
                  header_font_style VARCHAR(7) DEFAULT 'normal',
                  body_font_size INT UNSIGNED DEFAULT 11,
                  body_font_family VARCHAR(255) DEFAULT '''Open Sans'', Helvetica, Arial, sans-serif',
                  body_font_weight VARCHAR(3) DEFAULT 400,
                  body_font_style VARCHAR(7) DEFAULT  'normal',
                  autoalignment_priority VARCHAR(7) DEFAULT 'rows',
                  autoalignment_affected_rows_left VARCHAR(2000) DEFAULT '',
                  autoalignment_affected_rows_center VARCHAR(2000) DEFAULT '',
                  autoalignment_affected_rows_right VARCHAR(2000) DEFAULT '',
                  autoalignment_affected_columns_left VARCHAR(110) DEFAULT '',
                  autoalignment_affected_columns_center VARCHAR(110) DEFAULT '',
                  autoalignment_affected_columns_right VARCHAR(110) DEFAULT '',
                  hide_tablet_list VARCHAR(110) DEFAULT '',
                  hide_phone_list VARCHAR(110) DEFAULT '',
                  phone_header_font_size INT UNSIGNED DEFAULT 11,
                  phone_body_font_size INT UNSIGNED DEFAULT 11,
                  phone_hide_images TINYINT(1) UNSIGNED DEFAULT 0,
                  tablet_header_font_size INT UNSIGNED DEFAULT 11,
                  tablet_body_font_size INT UNSIGNED DEFAULT 11,
                  tablet_hide_images TINYINT(1) UNSIGNED DEFAULT 0,
                  enable_cell_properties TINYINT(1) UNSIGNED DEFAULT 1,
                  PRIMARY KEY (id)
            )
            COLLATE = utf8_general_ci
            ";

			dbDelta( $sql );

			// Create *prefix*_data.
			$sql = "CREATE TABLE {$wpdb->prefix}daextletal_data (
              id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
              table_id BIGINT UNSIGNED NOT NULL,
              row_index BIGINT UNSIGNED,
              content LONGTEXT,
              PRIMARY KEY (id)
            )
            COLLATE = utf8_general_ci
            ";

			dbDelta( $sql );

			// Create *prefix*_cell.
			$sql = "CREATE TABLE {$wpdb->prefix}daextletal_cell (
              id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
              table_id BIGINT UNSIGNED NOT NULL,
              row_index BIGINT UNSIGNED NOT NULL,
              column_index INT UNSIGNED NOT NULL,
              link VARCHAR(2083) DEFAULT '',
              image_left VARCHAR(2083) DEFAULT '',
              image_right VARCHAR(2083) DEFAULT '',
              PRIMARY KEY (id)
            )
            COLLATE = utf8_general_ci
            ";

			dbDelta( $sql );

			// Update database version.
			update_option( 'daextletal_database_version', '2' );

		}
	}

	/**
	 * Plugin delete.
	 *
	 * @return void
	 */
	public static function un_delete() {

		// Delete options and tables for all the sites in the network.
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			// Get the current blog id.
			global $wpdb;
			$current_blog = $wpdb->blogid;

			// Create an array with all the blog ids.
			$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ); // phpcs:ignore

			// Iterate through all the blogs.
			foreach ( $blogids as $blog_id ) {

				// Switch to the iterated blog.
				switch_to_blog( $blog_id );

				// Create options and tables for the iterated blog.
				self::un_delete_options();
				self::un_delete_database_tables();

			}

			// Switch to the current blog.
			switch_to_blog( $current_blog );

		} else {

			// If this is not a multisite installation delete options and tables only for the current blog.
			self::un_delete_options();
			self::un_delete_database_tables();

		}
	}

	/**
	 * Delete plugin options.
	 *
	 * @return void
	 */
	public static function un_delete_options() {

		// Assign an instance of Daextletal_Shared.
		$shared = Daextletal_Shared::get_instance();

		foreach ( $shared->get( 'options' ) as $key => $value ) {
			delete_option( $key );
		}
	}

	/**
	 * Delete plugin database tables.
	 *
	 * @return void
	 */
	public static function un_delete_database_tables() {

		// Assign an instance of Daextletal_Shared.
		$shared = Daextletal_Shared::get_instance();

		global $wpdb;

		$table_name = $wpdb->prefix . $shared->get( 'slug' ) . '_table';
		$sql        = "DROP TABLE $table_name";
		$wpdb->query( $sql ); // phpcs:ignore

		$table_name = $wpdb->prefix . $shared->get( 'slug' ) . '_data';
		$sql        = "DROP TABLE $table_name";
		$wpdb->query( $sql ); // phpcs:ignore

		$table_name = $wpdb->prefix . $shared->get( 'slug' ) . '_cell';
		$sql        = "DROP TABLE $table_name";
		$wpdb->query( $sql ); // phpcs:ignore
	}

	/**
	 * Register the admin menu.
	 *
	 * @return void
	 */
	public function me_add_admin_menu() {

		$icon_svg = '<?xml version="1.0" encoding="UTF-8"?>
		<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 40 40">
		  <defs>
		    <style>
		      .st0 {
		        fill: none;
		        stroke: #98a3b3;
		        stroke-miterlimit: 10;
		        stroke-width: 2px;
		      }
		
		      .st1 {
		        fill: #98a3b3;
		      }
		
		    </style>
		  </defs>
		  <g id="render">
		    <path class="st1" d="M34,5H6c-1.7,0-3,1.3-3,3v24c0,1.7,1.3,3,3,3h28c1.7,0,3-1.3,3-3V8c0-1.7-1.3-3-3-3ZM6,7h28c.6,0,1,.4,1,1v4H5v-4c0-.6.4-1,1-1ZM27,14v5h-6v-5h6ZM19,19h-6v-5h6v5ZM11,19h-6v-5h6v5ZM11,21v5h-6v-5h6ZM13,21h6v5h-6v-5ZM19,28v5h-6v-5h6ZM21,28h6v5h-6v-5ZM21,26v-5h6v5h-6ZM29,21h6v5h-6v-5ZM29,19v-5h6v5h-6ZM5,32v-4h6v5h-5c-.6,0-1-.4-1-1ZM34,33h-5v-5h6v4c0,.6-.4,1-1,1Z"/>
		  </g>
		</svg>';

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Base64 encoding is used to embed the SVG in the HTML.
		$icon_svg = 'data:image/svg+xml;base64,' . base64_encode( $icon_svg );

		add_menu_page(
			esc_html__( 'LT', 'league-table-lite' ),
			esc_html__( 'League Table', 'league-table-lite' ),
			get_option( $this->shared->get( 'slug' ) . '_tables_menu_capability' ),
			$this->shared->get( 'slug' ) . '-tables',
			array( $this, 'me_display_menu_tables' ),
			$icon_svg
		);

		$this->screen_id_tables = add_submenu_page(
			$this->shared->get( 'slug' ) . '-tables',
			esc_html__( 'LT - Tables', 'league-table-lite' ),
			esc_html__( 'Tables', 'league-table-lite' ),
			get_option( $this->shared->get( 'slug' ) . '_tables_menu_capability' ),
			$this->shared->get( 'slug' ) . '-tables',
			array( $this, 'me_display_menu_tables' )
		);

		$this->screen_id_tools = add_submenu_page(
			$this->shared->get( 'slug' ) . '-tables',
			esc_html__( 'LT - Tools', 'league-table-lite' ),
			esc_html__( 'Tools', 'league-table-lite' ),
			get_option( $this->shared->get( 'slug' ) . '_tools_menu_capability' ),
			$this->shared->get( 'slug' ) . '-tools',
			array( $this, 'me_display_menu_tools' )
		);

		$this->screen_id_maintenance = add_submenu_page(
			$this->shared->get( 'slug' ) . '-tables',
			esc_html__( 'LT - Maintenance', 'league-table-lite' ),
			esc_html__( 'Maintenance', 'league-table-lite' ),
			get_option( $this->shared->get( 'slug' ) . '_maintenance_menu_capability' ),
			$this->shared->get( 'slug' ) . '-maintenance',
			array( $this, 'me_display_menu_maintenance' )
		);

		$this->screen_id_options = add_submenu_page(
			$this->shared->get( 'slug' ) . '-tables',
			esc_html__( 'LT - Options', 'league-table-lite' ),
			esc_html__( 'Options', 'league-table-lite' ),
			'manage_options',
			$this->shared->get( 'slug' ) . '-options',
			array( $this, 'me_display_menu_options' )
		);
	}

	/**
	 * Includes the tables view.
	 *
	 * @return void
	 */
	public function me_display_menu_tables() {
		include_once 'view/tables.php';
	}

	/**
	 * Includes the Tools view.
	 *
	 * @return void
	 */
	public function me_display_menu_tools() {
		include_once 'view/tools.php';
	}

	/**
	 * Includes the Maintenance view.
	 *
	 * @return void
	 */
	public function me_display_menu_maintenance() {
		include_once 'view/maintenance.php';
	}

	/**
	 * Includes the options view.
	 *
	 * @return void
	 */
	public function me_display_menu_options() {
		include_once 'view/options.php';
	}

	/**
	 * If the temporary tables are more than 100 clear the older (first inserted) temporary table.
	 *
	 *  This method is used to avoid un unlimited number of temporary table stored in the 'table', 'data' and 'cell' db
	 *  tables.
	 *
	 *  By deleting all the temporary tables (and not only the last one like this method does) wouldn't be possible to
	 *  work on multiple tabs on the 'Tables' menu without being unable to save the table associated with the first
	 *  opened tabs.
	 *
	 *  With this method a maximum of 100 tabs can be opened on the 'Table' menu to create tables at the same time. If
	 *  101 tabs are for example opened, in the first of these 101 tabs the data of the table will not be saved because
	 *  the temporary data are deleted.
	 *
	 * @return void
	 */
	public function delete_old_temporary_table() {

		// Get all the temporary tables as an array.
		global $wpdb;
		$safe_sql          = "SELECT * FROM {$wpdb->prefix}daextletal_table WHERE temporary = 1 ORDER BY id";
		$temporary_table_a = $wpdb->get_results( $safe_sql, ARRAY_A ); // phpcs:ignore

		// verify if the temporary tables are more than 100.
		if ( count( $temporary_table_a ) > 100 ) {

			// get the id of the older (first inserted) table.
			$older_id = $temporary_table_a[0]['id'];

			// delete the older (first inserted) temporary table.
			global $wpdb;
			$safe_sql = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}daextletal_table WHERE id = %d", $older_id );
			$result     = $wpdb->query( $safe_sql ); // phpcs:ignore

			// delete all the data associated with the older (first inserted) temporary table.
			$safe_sql = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}daextletal_data WHERE table_id = %d", $older_id );
			$result     = $wpdb->query( $safe_sql ); // phpcs:ignore

			// delete all the cells associated with the older (first inserted) temporary table.
			$safe_sql = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}daextletal_cell WHERE table_id = %d", $older_id );
			$result     = $wpdb->query( $safe_sql ); // phpcs:ignore

		}
	}

}
