<?php
/**
 * Class used to implement the back-end functionalities of the "Tools" menu.
 *
 * @package league-table-lite
 */

/**
 * Class used to implement the back-end functionalities of the "Tools" menu.
 */
class Daextletal_Tools_Menu_Elements extends Daextletal_Menu_Elements {

	/**
	 * Constructor.
	 *
	 * @param object $shared The shared class.
	 * @param string $page_query_param The page query parameter.
	 * @param string $config The config parameter.
	 */
	public function __construct( $shared, $page_query_param, $config ) {

		parent::__construct( $shared, $page_query_param, $config );

		$this->menu_slug      = 'tool';
		$this->slug_plural    = 'tools';
		$this->label_singular = __( 'Tool', 'league-table-lite');
		$this->label_plural   = __( 'Tools', 'league-table-lite');
	}

	/**
	 * Process the add/edit form submission of the menu. Specifically the following tasks are performed:
	 *
	 *  1. Sanitization
	 *  2. Validation
	 *  3. Database update
	 *
	 * @return false|void
	 */
	public function process_form() {

		// Process the xml file upload. (import) ----------------------------------------------------------------------.
		if ( isset( $_FILES['file_to_upload'] ) &&
			isset( $_FILES['file_to_upload']['name'] )
		) {

			// Nonce verification.
			check_admin_referer( 'daextletal_tools_import', 'daextletal_tools_import_nonce' );

			//phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- The sanitization is performed with sanitize_uploaded_file().
			$file_data = $this->shared->sanitize_uploaded_file(
				array(
					'name'     => $_FILES['file_to_upload']['name'],
					'type'     => $_FILES['file_to_upload']['type'],
					'tmp_name' => $_FILES['file_to_upload']['tmp_name'],
					'error'    => $_FILES['file_to_upload']['error'],
					'size'     => $_FILES['file_to_upload']['size'],
				)
			);
			//phpcs:enable

			if ( 1 !== preg_match( '/^.+\.xml$/', $file_data['name'] ) ) {
				return;
			}

			if ( file_exists( $file_data['tmp_name'] ) ) {

				$counter = 0;

				global $wpdb;

				// Read xml file.
				$xml = simplexml_load_file( $file_data['tmp_name'] );

				$table_a = $xml->table;

				foreach ( $table_a as $single_table ) {

					// Convert object to array.
					$single_table_a = get_object_vars( $single_table );

					// Replace empty objects with empty strings to prevent notices on the next insert() method.
					$single_table_a = $this->shared->replace_empty_objects_with_empty_strings( $single_table_a );

					// Remove the id key.
					unset( $single_table_a['id'] );

					// Save the data key for later use and remove the data key from the main array.
					$data_a = get_object_vars( $single_table_a['data'] );
					unset( $single_table_a['data'] );

					// Save the cell key for later use or set its value to null if there are no cell.
					if ( '' !== $single_table_a['cell'] ) {
						$cell_a = get_object_vars( $single_table_a['cell'] );
					} else {
						$cell_a = null;
					}

					// Remove the cell key from the main array.
					unset( $single_table_a['cell'] );

					$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . '_table';
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery
					$wpdb->insert(
						$table_name,
						$single_table_a
					);
					$inserted_table_id = $wpdb->insert_id;

					// Add the data -----------------------------------------------------------------------------------.
					$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . '_data';
					$record_a   = $data_a['record'];

					if ( is_array( $record_a ) ) {

						/**
						 * If this table has multiple rows $record_a is an array filled with objects of type
						 * [SimpleXMLElement]. Each object is converted to an array and is then passed to the
						 * $wpdb->insert method and inserted in the database
						 */
						foreach ( $record_a as $single_record ) {

							$single_record_a = get_object_vars( $single_record );

							// Replace empty objects with empty strings to prevent notices on the next insert() method.
							$single_record_a = $this->shared->replace_empty_objects_with_empty_strings( $single_record_a );

							// Remove the id key.
							unset( $single_record_a['id'] );

							// Set the table_id based on the id inserted during the creation of the table.
							$single_record_a['table_id'] = $inserted_table_id;

							// phpcs:ignore WordPress.DB.DirectDatabaseQuery
							$wpdb->insert(
								$table_name,
								$single_record_a
							);

						}
					} else {

						/**
						 * If this table has a single row $record_a is an object of type [SimpleXMLElement] and is
						 * converted to an array and passed to the $wpdb->insert method and inserted in the database.
						 */
						$single_record_a = get_object_vars( $record_a );

						// Replace empty objects with empty strings to prevent notices on the next insert() method.
						$single_record_a = $this->shared->replace_empty_objects_with_empty_strings( $single_record_a );

						// Remove the id key.
						unset( $single_record_a['id'] );

						// Set the table_id based on the id inserted during the creation of the table.
						$single_record_a['table_id'] = $inserted_table_id;

						// phpcs:ignore WordPress.DB.DirectDatabaseQuery
						$wpdb->insert(
							$table_name,
							$single_record_a
						);

					}

					// Add the cell -----------------------------------------------------------------------------------.
					$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . '_cell';

					// Add the cell only if there are the proper data.
					if ( null !== $cell_a && isset( $cell_a['record'] ) ) {

						$record_a = $cell_a['record'];

						if ( is_array( $record_a ) ) {

							/**
							 * If this table has multiple rows $record_a is an array filled with objects of type
							 * [SimpleXMLElement]. Each object is converted to an array and is then passed to the
							 * $wpdb->insert method and inserted in the database.
							 */
							foreach ( $record_a as $single_record ) {

								$single_record_a = get_object_vars( $single_record );

								/**
								 * Replace empty objects with empty strings to prevent notices on the next insert()
								 * method.
								 */
								$single_record_a = $this->shared->replace_empty_objects_with_empty_strings( $single_record_a );

								// Remove the id key.
								unset( $single_record_a['id'] );

								// Set the table_id based on the id inserted during the creation of the table.
								$single_record_a['table_id'] = $inserted_table_id;

								// phpcs:ignore WordPress.DB.DirectDatabaseQuery
								$wpdb->insert(
									$table_name,
									$single_record_a
								);

							}
						} else {

							/**
							 * If this table has a single row $record_a is an object of type [SimpleXMLElement] and is
							 * converted to an array and passed to the $wpdb->insert method and inserted in the
							 * database.
							 */
							$single_record_a = get_object_vars( $record_a );

							// Replace empty objects with empty strings to prevent notices on the next insert() method.
							$single_record_a = $this->shared->replace_empty_objects_with_empty_strings( $single_record_a );

							// Remove the id key.
							unset( $single_record_a['id'] );

							// Set the table_id based on the id inserted during the creation of the table.
							$single_record_a['table_id'] = $inserted_table_id;

							// phpcs:ignore WordPress.DB.DirectDatabaseQuery
							$wpdb->insert(
								$table_name,
								$single_record_a
							);

						}
					}

					++$counter;

				}

				$this->shared->save_dismissible_notice(
					$counter . ' ' . __( 'tables have been added.', 'league-table-lite'),
					'updated'
				);

			}
		}

		// Process the export button click. (export) ------------------------------------------------------------------.

		/**
		 * Intercept requests that come from the "Export" button of the "Tools -> Export" menu and generate the
		 * downloadable XML file
		 */
		if ( isset( $_POST['daextletal_export'] ) ) {

			// Nonce verification.
			check_admin_referer( 'daextletal_tools_export', 'daextletal_tools_export' );

			// Verify capability.
			if ( ! current_user_can( get_option( $this->shared->get( 'slug' ) . '_tools_menu_capability' ) ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'league-table-lite') );
			}

			// Get the data from the table db.
			global $wpdb;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$table_a = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}daextletal_table WHERE temporary = 0 ORDER BY id ASC", ARRAY_A );

			// If there are data generate the csv header and the content.
			if ( count( $table_a ) > 0 ) {

				// Generate the header of the XML file.
				header( 'Content-Encoding: UTF-8' );
				header( 'Content-type: text/xml; charset=UTF-8' );
				header( 'Content-Disposition: attachment; filename=league-table-' . time() . '.xml' );
				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );

				// Generate initial part of the XML file.
				echo '<?xml version="1.0" encoding="UTF-8" ?>';
				echo '<root>';
				echo '<plugin_edition>free</plugin_edition>';

				// Set column content.
				foreach ( $table_a as $table ) {

					echo '<table>';

					// Get all the indexes of the $table array.
					$table_keys = array_keys( $table );

					// Cycle through all the indexes of $table and create all the tags related to this record.
					foreach ( $table_keys as $key ) {

						echo '<' . esc_attr( $key ) . '>' . esc_attr( $table[ $key ] ) . '</' . esc_attr( $key ) . '>';

					}

					// Add the data associated with this table from the data db table ---------------------------------.
					$table_id = $table['id'];
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery
					$data_a = $wpdb->get_results(
						$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}daextletal_data WHERE table_id = %d ORDER BY id ASC", $table_id ),
						ARRAY_A
					);

					echo '<data>';

					/**
					 * Create all the tags of the table data enclosed in the <data> tag, each single record is enclosed
					 * in the <record> tag
					 */
					foreach ( $data_a as $data ) {

						echo '<record>';

						// Get all the indexes of the $data array.
						$data_keys = array_keys( $data );

						foreach ( $data_keys as $data_key ) {

							echo '<' . esc_attr( $data_key ) . '>' . esc_attr( $data[ $data_key ] ) . '</' . esc_attr( $data_key ) . '>';

						}

						echo '</record>';

					}

					echo '</data>';

					// Add the cells associated with this table from the cell db table --------------------------------.
					$table_id = $table['id'];
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery
					$data_a = $wpdb->get_results(
						$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}daextletal_cell WHERE table_id = %d ORDER BY id ASC", $table_id ),
						ARRAY_A
					);

					echo '<cell>';

					/**
					 * Create all the tags of the table cell enclosed in the <cell> tag, each single record is enclosed
					 * in the <record> tag
					 */
					foreach ( $data_a as $data ) {

						echo '<record>';

						// Get all the indexes of the $data array.
						$data_keys = array_keys( $data );

						foreach ( $data_keys as $data_key ) {

							echo '<' . esc_attr( $data_key ) . '>' . esc_attr( $data[ $data_key ] ) . '</' . esc_attr( $data_key ) . '>';

						}

						echo '</record>';

					}

					echo '</cell>';

					echo '</table>';

				}

				// Generate the final part of the XML file.
				echo '</root>';

			} else {
				return false;
			}

			die();

		}
	}

	/**
	 * Display the form.
	 *
	 * @return void
	 */
	public function display_custom_content() {

		?>

		<div class="daextletal-admin-body">

			<?php

			// Display the dismissible notices.
			$this->shared->display_dismissible_notices();

			?>

			<div class="daextletal-tools-menu">

				<div class="daextletal-main-form">

					<div class="daextletal-main-form__wrapper-half">

						<div class="daextletal-main-form__daext-form-section">

							<div class="daextletal-main-form__section-header">
								<div class="daextletal-main-form__section-header-title">
									<?php $this->shared->echo_icon_svg( 'log-out-04' ); ?>
									<div class="daextletal-main-form__section-header-title-text"><?php esc_html_e( 'Export', 'league-table-lite'); ?></div>
								</div>
							</div>

							<div class="daextletal-main-form__daext-form-section-body">

								<!-- Export form -->

								<div>
									<?php
									esc_html_e(
										'Click the Export button to generate an XML file that includes all the tables.',
										'league-table-lite'
									);
									?>
								</div>
								<div>
									<?php esc_html_e( 'Note that you can import the resulting file in the Tools menu of the ', 'league-table-lite' ); ?>
									<a href="https://daext.com/league-table/" target="_blank"><?php esc_html_e( 'Pro Version', 'league-table-lite' ); ?></a> <?php esc_html_e( 'to quickly transition between the two plugin editions.', 'league-table-lite' ); ?>
								</div>

								<!-- the data sent through this form are handled by the export_xml_controller() method called with the WordPress init action -->
								<form method="POST"
										action="admin.php?page=<?php echo esc_attr( $this->shared->get( 'slug' ) ); ?>-<?php echo esc_attr( $this->slug_plural ); ?>">

									<div class="daext-widget-submit">
										<?php wp_nonce_field( 'daextletal_tools_export', 'daextletal_tools_export' ); ?>
										<input name="daextletal_export" class="daextletal-btn daextletal-btn-primary" type="submit"
												value="<?php esc_attr_e( 'Export', 'league-table-lite'); ?>"
											<?php
											if ( ! $this->shared->exportable_data_exists() ) {
												echo 'disabled="disabled"';
											}
											?>
										>
									</div>

								</form>

							</div>

						</div>

					</div>

				</div>

			</div>

		</div>

		<?php
	}
}
