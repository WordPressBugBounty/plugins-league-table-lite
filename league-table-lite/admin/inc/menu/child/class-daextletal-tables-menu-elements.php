<?php
/**
 * Class used to implement the back-end functionalities of the "Tables" menu.
 *
 * @package league-table-lite
 */

/**
 * Class used to implement the back-end functionalities of the "Tools" menu.
 */
class Daextletal_Tables_Menu_Elements extends Daextletal_Menu_Elements {

	/**
	 * Constructor.
	 *
	 * @param object $shared The shared class.
	 * @param string $page_query_param The page query parameter.
	 * @param string $config The config parameter.
	 */
	public function __construct( $shared, $page_query_param, $config ) {

		parent::__construct( $shared, $page_query_param, $config );

		$this->menu_slug      = 'table';
		$this->slug_plural    = 'tables';
		$this->label_singular = __( 'Table', 'league-table-lite' );
		$this->label_plural   = __( 'Tables', 'league-table-lite' );
		$this->primary_key    = 'id';
		$this->db_table       = 'table';
	}

	/**
	 * Display the form.
	 *
	 * @return void
	 */
	public function display_custom_content() {

		?>

		<div class="daextletal-admin-body">

			<div id="table-error" class="error settings-error notice below-h2"><p></p></div>

			<?php

			// Display the dismissible notices.
			$this->shared->display_dismissible_notices();

			?>

			<?php

			// Initialize variables -----------------------------------------------------------------------------------------------.
			$dismissible_notice_a = array();

			// Preliminary operations ---------------------------------------------------------------------------------------------.
			global $wpdb;

			// Sanitization ---------------------------------------------------------------------------------------------.

			// Actions.
		    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not necessary for view operations.
			$data['edit_id'] = isset( $_GET['edit_id'] ) ? intval( $_GET['edit_id'], 10 ) : null;

			// Filter and search data.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Not necessary for view operations.
			$data['s'] = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : null;

			// If the temporary tables are more than 100 clear the older (first inserted) temporary table.
			$this->shared->delete_older_temporary_table();

			// Get the table data.
			$display_form = true;
			if ( 0 !== isset( $data['edit_id'] ) && intval( $data['edit_id'], 10 ) ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$table_obj = $wpdb->get_row(
					$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}daextletal_table WHERE temporary = 0 AND id = %d", $data['edit_id'] )
				);
				if ( null === $table_obj ) {
					$display_form = false;
				}
			}

			?>

			<!-- output -->

				<?php if ( null === $data['edit_id'] ) : ?>
					<form action="admin.php" method="get">
						<div class="daextletal-crud-table-search-form">
							<div class="daext-search-form">
								<input type="hidden" name="page" value="daextletal-tables">
								<?php
								if ( ! is_null( $data['s'] ) && strlen( trim( $data['s'] ) ) > 0 ) {
									$search_string = $data['s'];
								} else {
									$search_string = '';
								}
								?>
								<input class="daextletal-crud-table-search-form__post-search-input" type="text" name="s"
										value="<?php echo esc_attr( stripslashes( $search_string ) ); ?>" autocomplete="off" maxlength="255">
								<input class="button daextletal-admin-page-button" type="submit" value="<?php esc_attr_e( 'Search Tables', 'league-table-lite' ); ?>">
							</div>
						</div>
					</form>
				<?php endif; ?>

				<?php
				if (
						0 === intval( $this->shared->get_number_of_tables(), 10 )
						&& null === $data['edit_id']
				) :
					?>

					<div class="daextletal-crud-table__no-items-found-message"><?php esc_html_e( 'Nothing to show yet! Add some items by clicking the Add New button.', 'league-table-lite' ); ?></div>

				<?php endif; ?>

					<?php

					if ( null === $data['edit_id'] ) :

						// Create the query part used to filter the results when a search is performed.
						if ( ! is_null( $data['s'] ) ) {
							$filter = $wpdb->prepare( 'AND (id LIKE %s OR name LIKE %s OR description LIKE %s)', '%' . $data['s'] . '%', '%' . $data['s'] . '%', '%' . $data['s'] . '%' );
						} else {
							$filter = '';
						}

						// Retrieve the total number of tables.
						global $wpdb;

						// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Filter is already prepared.
						// phpcs:disable WordPress.DB.DirectDatabaseQuery
						$total_items = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}daextletal_table WHERE temporary = 0 $filter" );
						// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						// phpcs:enable WordPress.DB.DirectDatabaseQuery

						// Initialize the pagination class.
						require_once $this->shared->get( 'dir' ) . '/admin/inc/class-daextletal-pagination.php';
						$pag = new Daextletal_Pagination( $this->shared );
						$pag->set_total_items( $total_items );// Set the total number of items.
						$pag->set_record_per_page( 10 ); // Set records per page.
						$pag->set_target_page( 'admin.php?page=' . $this->shared->get( 'slug' ) . '-tables' );// Set target page.
						$pag->set_current_page();// set the current page number from $_GET.

						?>

						<!-- Query the database -->
						<?php
						$query_limit = $pag->query_limit();

						// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $filter and $query_limit are already prepared.
						// phpcs:disable WordPress.DB.DirectDatabaseQuery
						$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}daextletal_table WHERE temporary = 0 $filter ORDER BY id DESC $query_limit", ARRAY_A );
						// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $filter and $query_limit are already prepared.
						// phpcs:enable WordPress.DB.DirectDatabaseQuery

						?>

						<?php if ( count( $results ) > 0 ) : ?>

							<div class="daextletal-crud-table">

								<!-- list of tables -->
								<table class="daextletal-crud-table__daext-items">
									<thead>
									<tr>
										<th>
											<input type="checkbox" class="daextletal-cb-select-all">
										</th>
										<th>
											<div>
												<div><?php esc_html_e( 'Name', 'league-table-lite' ); ?></div>
											</div>
										</th>
										<th>
											<div><?php esc_html_e( 'Description', 'league-table-lite' ); ?></div>
										</th>
										<th>
											<div><?php esc_html_e( 'Shortcode', 'league-table-lite' ); ?></div>
										</th>
									</tr>
									</thead>
									<tbody>

									<?php foreach ( $results as $result ) : ?>
										<tr>
											<td>
												<input type="checkbox"
														class="daextletal-bulk-action-checkbox"
														id="cb-select-<?php echo esc_attr( $result['id'] ); ?>"
														value="<?php echo esc_attr( $result['id'] ); ?>" name="post[]">
											</td>
											<td>
												<a class="daextletal-crud-table__item-name" href="admin.php?page=<?php echo esc_attr( $this->shared->get( 'slug' ) ); ?>-tables&edit_id=<?php echo esc_attr( $result['id'] ); ?>">
													<?php echo esc_html( stripslashes( $result['name'] ) ); ?>
												</a>
												<div class="daextletal-crud-table__row-actions">
													<div class="daextletal-crud-table__row-actions-single-action">
														<a href="admin.php?page=<?php echo esc_attr( $this->shared->get( 'slug' ) ); ?>-tables&edit_id=<?php echo esc_attr( $result['id'] ); ?>">Edit</a>
													</div>
													<div>&nbsp|&nbsp</div>
													<div class="daextletal-crud-table__row-actions-single-action">
														<form method="POST"
																action="admin.php?page=<?php echo esc_attr( $this->shared->get( 'slug' ) ); ?>-tables">
															<?php wp_nonce_field( 'daextletal_clone_table_' . intval( $result['id'], 10 ), 'daextletal_clone_table_nonce' ); ?>
															<input type="hidden" name="clone_id"
																	value="<?php echo esc_attr( $result['id'] ); ?>">
															<button type="submit" value="">Duplicate</button>
														</form>
													</div>
													<div>&nbsp|&nbsp</div>
													<div class="daextletal-crud-table__row-actions-single-action">
														<form method="POST"
																id="form-delete-<?php echo intval( $result['id'], 10 ); ?>"
																action="admin.php?page=<?php echo esc_attr( $this->shared->get( 'slug' ) ); ?>-tables">
															<?php wp_nonce_field( 'daextletal_delete_table_' . intval( $result['id'], 10 ), 'daextletal_delete_table_nonce' ); ?>
															<input type="hidden"
																	value="<?php echo esc_attr( $result['id'] ); ?>"
																	name="delete_id">
															<button type="submit" value="">Delete</button>
														</form>
													</div>
												</div>
											</td>
											<td><?php echo strlen( trim( $result['description'] ) ) > 0 ? esc_html( stripslashes( $result['description'] ) ) : esc_html__( 'N/A', 'league-table-lite' ); ?></td>
											<td><?php echo '[lt id="' . intval( $result['id'], 10 ) . '"]'; ?></td>
										</tr>
									<?php endforeach; ?>

									</tbody>
									<tfoot>
									<tr>
										<th>
											<input type="checkbox" class="daextletal-cb-select-all">
										</th>
										<th>
											<div>
												<div><?php esc_html_e( 'Name', 'league-table-lite' ); ?></div>
											</div>
										</th>
										<th>
											<div><?php esc_html_e( 'Description', 'league-table-lite' ); ?></div>
										</th>
										<th>
											<div><?php esc_html_e( 'Shortcode', 'league-table-lite' ); ?></div>
										</th>
									</tr>
									</tfoot>
								</table>

							</div>


							<?php if ( $pag->total_items > 0 ) : ?>
								<div class="daextletal-crud-table-controls">

									<!-- Bulk Actions -->
									<div class="daextletal-crud-table-controls__bulk-actions">
										<form method="POST" action="admin.php?page=<?php echo esc_attr( $this->shared->get( 'slug' ) ); ?>-<?php echo esc_attr( $this->slug_plural ); ?>">
											<select name="bulk_action" id="bulk_action">
												<option value=""><?php esc_html_e( 'Bulk actions', 'league-table-lite' ); ?></option>
												<option value="delete"><?php esc_html_e( 'Delete', 'league-table-lite' ); ?></option>
											</select>
											<?php wp_nonce_field( 'daextletal_bulk_action_' . $this->menu_slug, 'daextletal_bulk_action_' . $this->menu_slug . '_nonce' ); ?>
											<input id="bulk-action-selected-items" type="hidden" name="bulk-action-selected-items" value="">
											<input id="daextletal-submit-bulk-action" type="submit" class="button daextletal-admin-page-button" value="<?php esc_html_e( 'Apply', 'league-table-lite' ); ?>">
										</form>
									</div>

									<!-- Display the pagination -->
									<div class="daextletal-crud-table-controls__pagination-container">
										<?php if ( $pag->total_items > 0 ) : ?>
											<div class="daextletal-crud-table-controls__daext-tablenav">
											<span class="daextletal-crud-table-controls__daext-displaying-num"><?php echo esc_html( $pag->total_items ); ?>&nbsp<?php esc_attr_e( 'items', 'league-table-lite' ); ?></span>
												<?php $pag->show(); ?>
											</div>
										<?php endif; ?>
									</div>

								</div>
							<?php endif; ?>

						<?php endif; ?>

					<?php endif; ?>

					<?php if ( null !== $data['edit_id'] ) : ?>

					<div class="daextletal-main-form">

						<?php if ( $display_form ) : ?>

						<div class="daext-form-container">

							<div class="daext-form daext-form-table">

								<div class="daextletal-main-form__daext-form-section" data-id="main">

									<div class="daextletal-main-form__daext-form-section-body" data-section-id="main">

										<?php

										if ( 0 !== $data['edit_id'] ) {

											// Edit mode.

											$default_values = $table_obj;
											echo '<input type="hidden" id="update-id" value="' . esc_attr( $table_obj->id ) . '"/>';

										} else {

											// Add mode.

											// Create a new stadard object to be used as default values.
											$default_values = new stdClass();

											// Main.
											$default_values->name    = '';
											$default_values->rows    = '10';
											$default_values->columns = '10';

											// Sorting.
											$default_values->enable_sorting        = '0';
											$default_values->enable_manual_sorting = '0';
											$default_values->show_position         = '0';
											$default_values->position_side         = 'left';
											$default_values->position_label        = '#';
											$default_values->order_desc_asc        = '0';
											$default_values->order_by              = '';
											$default_values->order_data_type       = 'auto';
											$default_values->order_date_format     = 'ddmmyyyy';

											// Style.
											$default_values->table_layout               = '0';
											$default_values->table_width                = '0';
											$default_values->table_width_value          = '400';
											$default_values->table_minimum_width        = '0';
											$default_values->column_width               = '0';
											$default_values->column_width_value         = '100';
											$default_values->enable_container           = 0;
											$default_values->container_width            = '400';
											$default_values->container_height           = '400';
											$default_values->table_margin_top           = 20;
											$default_values->table_margin_bottom        = 20;
											$default_values->show_header                = 1;
											$default_values->header_font_size           = 11;
											$default_values->header_font_family         = "'Open Sans', Helvetica, Arial, sans-serif";
											$default_values->header_font_weight         = '400';
											$default_values->header_font_style          = 'normal';
											$default_values->header_position_alignment  = 'center';
											$default_values->header_background_color    = '#C3512F';
											$default_values->header_font_color          = '#FFFFFF';
											$default_values->header_link_color          = '#FFFFFF';
											$default_values->header_border_color        = '#B34A2A';
											$default_values->body_font_size             = '11';
											$default_values->body_font_family           = "'Open Sans', Helvetica, Arial, sans-serif";
											$default_values->body_font_weight           = '400';
											$default_values->body_font_style            = 'normal';
											$default_values->even_rows_background_color = '#FFFFFF';
											$default_values->odd_rows_background_color  = '#FCFCFC';
											$default_values->even_rows_font_color       = '#666666';
											$default_values->odd_rows_font_color        = '#666666';
											$default_values->even_rows_link_color       = '#C3512F';
											$default_values->odd_rows_link_color        = '#C3512F';
											$default_values->rows_border_color          = '#E1E1E1';

											// Autoalignment.
											$default_values->autoalignment_priority                = 'rows';
											$default_values->autoalignment_affected_rows_left      = '';
											$default_values->autoalignment_affected_rows_center    = '';
											$default_values->autoalignment_affected_rows_right     = '';
											$default_values->autoalignment_affected_columns_left   = '';
											$default_values->autoalignment_affected_columns_center = '';
											$default_values->autoalignment_affected_columns_right  = '';

											// Responsive.
											$default_values->tablet_breakpoint       = '989';
											$default_values->hide_tablet_list        = '';
											$default_values->tablet_header_font_size = 11;
											$default_values->tablet_body_font_size   = 11;
											$default_values->tablet_hide_images      = 0;
											$default_values->phone_breakpoint        = '479';
											$default_values->hide_phone_list         = '';
											$default_values->phone_header_font_size  = 11;
											$default_values->phone_body_font_size    = 11;
											$default_values->phone_hide_images       = 0;

											// Advanced.
											$default_values->enable_cell_properties = 1;
											$default_values->description            = '';
											$default_values->number_format          = '1';

											// Create temporary table in db table.
											global $wpdb;

											// phpcs:disable WordPress.DB.DirectDatabaseQuery
											$result = $wpdb->query(
												$wpdb->prepare(
													"INSERT INTO {$wpdb->prefix}daextletal_table SET
							                        temporary = %d,
							                        name = %s,
							                        `rows` = %d,
							                        columns = %d",
													1,
													'[TEMPORARY]',
													10,
													10
												)
											);

											// Get the automatic id of the inserted element.
											$temporary_table_id = $wpdb->insert_id;

											// Initialize the data based on the initial number of rows and columns.
											$this->shared->initialize_table_data( $temporary_table_id, 11, 10 );

											echo '<input type="hidden" id="temporary-table-id" value="' . esc_attr( $temporary_table_id ) . '"/>';

										}

										?>

										<!-- Name -->
										<?php
										$this->daextletal_input_field(
											'name',
											__( 'Name', 'league-table-lite' ),
											__( 'Enter the name of the table.', 'league-table-lite' ),
											'',
											stripslashes( $default_values->name ),
											255,
											false,
											'main'
										);
										?>

										<!-- Rows -->
										<?php
										$this->daextletal_input_field(
											'rows',
											__( 'Rows', 'league-table-lite' ),
											__( 'Define the number of rows.', 'league-table-lite' ),
											'',
											intval( $default_values->rows, 10 ),
											5,
											false,
											'main'
										);
										?>

										<!-- Columns -->
										<?php
										$this->daextletal_input_field(
											'columns',
											__( 'Columns', 'league-table-lite' ),
											__( 'Define the number of columns.', 'league-table-lite' ),
											'',
											intval( $default_values->columns, 10 ),
											2,
											false,
											'main'
										);
										?>

										<!-- Data -->
										<?php $this->daextletal_embedded_spreadsheet(); ?>

									</div>

								</div>

								<!-- Sorting Options ---------------------------------------------- -->
								<div class="daextletal-main-form__daext-form-section" data-id="sorting-options">

									<div class="daextletal-main-form__section-header group-trigger"
										data-trigger-target="sorting-options">
										<div class="daextletal-main-form__section-header-title">
											<?php $this->shared->echo_icon_svg( 'switch-vertical-02' ); ?>
											<div class="daextletal-main-form__section-header-title-text">Sorting</div>
										</div>
										<div class="daextletal-main-form__section-header-toggle">
											<?php $this->shared->echo_icon_svg( 'chevron-down' ); ?>
										</div>
									</div>

									<div class="daextletal-main-form__daext-form-section-body"
										data-section-id="sorting-options">

										<!-- Separator -->
										<?php

										$this->separator(
											'general-sorting-options',
											__( 'General Sorting Options', 'league-table-lite' ),
											__( 'Configure global sorting behavior, including enabling sorting features and defining if users can interactively sort table columns.', 'league-table-lite' ),
											'sorting-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Enable Sorting -->
										<?php
										$this->daextletal_toggle_field(
											'enable-sorting',
											__( 'Enable Sorting', 'league-table-lite' ),
											__( 'Sort the table based on the criteria defined in this section.', 'league-table-lite' ),
											$default_values->enable_sorting,
											'sorting-options',
											'general-sorting-options'
										);
										?>

										<!-- Enable Manual Sorting -->
										<?php
										$this->daextletal_toggle_field(
											'enable-manual-sorting',
											__( 'Enable Manual Sorting', 'league-table-lite' ),
											__( 'Allow users to manually sort the table by clicking on the column headers. Please note that manual sorting will be disabled if the "Enable Sorting" option is turned off.', 'league-table-lite' ),
											$default_values->enable_manual_sorting,
											'sorting-options',
											'general-sorting-options'
										);
										?>

										</div>

										<!-- Separator -->
										<?php

										$this->separator(
											'position-settings',
											__( 'Position Column', 'league-table-lite' ),
											__( 'Add a dedicated column to display the position or ranking of each row and configure its placement and header label.', 'league-table-lite' ),
											'sorting-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Show Position -->
										<?php
										$this->daextletal_toggle_field(
											'show-position',
											__( 'Show Position', 'league-table-lite' ),
											__( 'Automatically generate the position column.', 'league-table-lite' ),
											$default_values->show_position,
											'sorting-options',
											'position-settings'
										);
										?>

										<!-- Position Side -->
										<?php
										$this->daextletal_select_field(
											'position-side',
											__( 'Position Side', 'league-table-lite' ),
											__( 'Choose whether the position column should appear on the left or right side of the table.', 'league-table-lite' ),
											array(
												'left'  => __( 'Left', 'league-table-lite' ),
												'right' => __( 'Right', 'league-table-lite' ),
											),
											$default_values->position_side,
											'sorting-options',
											'position-settings'
										);
										?>

										<!-- Position Label -->
										<?php
										$this->daextletal_input_field(
											'position-label',
											__( 'Position Label', 'league-table-lite' ),
											__( 'Enter the text to be displayed in the header of the position column.', 'league-table-lite' ),
											'',
											stripslashes( $default_values->position_label ),
											255,
											false,
											'sorting-options',
											'position-settings'
										);
										?>

										</div>

										<!-- Separator -->
										<?php

										$this->separator(
											'sorting-priorities',
											__( 'Sorting Priorities', 'league-table-lite' ),
											__( 'Define up to five levels of column-based sorting. Each priority level lets you choose the column, order direction, data type, and if needed, the date format.', 'league-table-lite' ),
											'sorting-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

											<!-- Order Desc Asc -->
											<?php
											$this->daextletal_select_field(
												'order-desc-asc',
												__( 'Sorting Order', 'league-table-lite' ),
												__( 'Select the sorting order assigned to the column.', 'league-table-lite' ),
												array(
													'0' => __( 'Disabled', 'league-table-lite' ),
													'1' => __( 'Descending', 'league-table-lite' ),
													'2' => __( 'Ascending', 'league-table-lite' ),
												),
												$default_values->{'order_desc_asc'},
												'sorting-options',
												'sorting-priorities'
											);
											?>

											<!-- Order By -->
											<?php
											$this->daextletal_select_field(
												'order-by',
												__( 'Order By', 'league-table-lite' ),
												__( 'Select the column to which the sorting order should be applied.', 'league-table-lite' ),
												array(),
												'', // Add the default value here.
												'sorting-options',
												'sorting-priorities'
											);
											?>

											<!-- Order Data Type -->
											<?php
											$this->daextletal_select_field(
												'order-data-type',
												__( 'Order Data Type', 'league-table-lite' ),
												__( 'Select the data type for the column that determines the sorting order. Note: If you choose "Auto", the data type will be automatically detected by the sorting system.', 'league-table-lite' ),
												array(
													'auto' => __( 'Auto', 'league-table-lite' ),
													'text' => __( 'Text', 'league-table-lite' ),
													'digit' => __( 'Digit', 'league-table-lite' ),
													'percent' => __( 'Percent', 'league-table-lite' ),
													'currency' => __( 'Currency', 'league-table-lite' ),
													'url'  => __( 'URL', 'league-table-lite' ),
													'time' => __( 'Time', 'league-table-lite' ),
													'isoDate' => __( 'ISO Date', 'league-table-lite' ),
													'usLongDate' => __( 'US Long Date', 'league-table-lite' ),
													'shortDate' => __( 'Short Date', 'league-table-lite' ),
												),
												$default_values->{'order_data_type'},
												'sorting-options',
												'sorting-priorities'
											);
											?>

											<!-- Order Date Format -->
											<?php
											$this->daextletal_select_field(
												'order-date-format',
												__( 'Order Date Format', 'league-table-lite' ),
												__( 'Use this option to define the data format for the column that determines the sorting order. Note: This setting will only be applied if the corresponding "Order Data Type" option is set to "Short Date".', 'league-table-lite' ),
												array(
													'ddmmyyyy' => __( 'DDMMYYYY', 'league-table-lite' ),
													'yyyymmdd' => __( 'YYYYMMDD', 'league-table-lite' ),
													'mmddyyyy' => __( 'MMDDYYYY', 'league-table-lite' ),
												),
												$default_values->{'order_date_format'},
												'sorting-options',
												'sorting-priorities'
											);

											?>

										</div>

									</div>

								</div>

								<!-- Style Options ---------------------------------------------- -->
								<div class="daextletal-main-form__daext-form-section" data-id="style-options">

									<div class="daextletal-main-form__section-header group-trigger"
										data-trigger-target="style-options">
										<div class="daextletal-main-form__section-header-title">
											<?php $this->shared->echo_icon_svg( 'table' ); ?>
											<div class="daextletal-main-form__section-header-title-text">Style</div>
										</div>
										<div class="daextletal-main-form__section-header-toggle">
											<?php $this->shared->echo_icon_svg( 'chevron-down' ); ?>
										</div>
									</div>

									<div class="daextletal-main-form__daext-form-section-body"
										data-section-id="style-options">

										<!-- Separator -->
										<?php

										$this->separator(
											'table-layout',
											__( 'Table Layout', 'league-table-lite' ),
											__( 'Configure the table layout algorithm, overall width, and how column dimensions are determined.', 'league-table-lite' ),
											'style-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Table Layout -->
										<?php
										$this->daextletal_select_field(
											'table-layout',
											__( 'Algorithm Selection', 'league-table-lite' ),
											__( 'Choose the algorithm used for table layout.', 'league-table-lite' ),
											array(
												'0' => __( 'Auto', 'league-table-lite' ),
												'1' => __( 'Fixed', 'league-table-lite' ),
											),
											$default_values->table_layout,
											'style-options',
											'table-layout'
										);
										?>

										<!-- Table Width -->
										<?php
										$this->daextletal_select_field(
											'table-width',
											__( 'Table Width', 'league-table-lite' ),
											__( 'With "Full Width", the table expands to the container\'s width; with "Auto", it adjusts based on content; and with "Specified Value", it follows the value entered in the "Table Width Value" field.', 'league-table-lite' ),
											array(
												'0' => __( 'Full Width', 'league-table-lite' ),
												'1' => __( 'Auto', 'league-table-lite' ),
												'2' => __( 'Specified Value', 'league-table-lite' ),
											),
											$default_values->table_width,
											'style-options',
											'table-layout'
										);
										?>

										<!-- Table Width Value -->
										<?php
										$this->daextletal_input_field(
											'table-width-value',
											__( 'Table Width Value', 'league-table-lite' ),
											__( 'Define the table width. This option is applied only if "Table Width" is set to "Specified Value".', 'league-table-lite' ),
											'',
											intval( $default_values->table_width_value, 10 ),
											6,
											false,
											'style-options',
											'table-layout'
										);
										?>

										<!-- Table Width Value -->
										<?php
										$this->daextletal_input_field(
											'table-minimum-width',
											__( 'Table Minimum Width', 'league-table-lite' ),
											__( 'Specify the minimum width for the table.', 'league-table-lite' ),
											'',
											intval( $default_values->table_minimum_width, 10 ),
											6,
											false,
											'style-options',
											'table-layout'
										);
										?>

										<!-- Column Width -->
										<?php
										$this->daextletal_select_field(
											'column-width',
											__( 'Column Width', 'league-table-lite' ),
											__( 'Choose whether column widths should be determined automatically or set based on the values in the "Column Width Value" field.', 'league-table-lite' ),
											array(
												'0' => __( 'Auto', 'league-table-lite' ),
												'1' => __( 'Specified Value', 'league-table-lite' ),
											),
											$default_values->column_width,
											'style-options',
											'table-layout'
										);
										?>

										<!-- Column Width Value -->
										<?php
										$this->daextletal_input_field(
											'column-width-value',
											__( 'Column Width Value', 'league-table-lite' ),
											__( 'Enter a comma-separated list of column widths. If only one value is provided, it will be applied to all columns.', 'league-table-lite' ),
											'',
											intval( $default_values->column_width_value, 10 ),
											2000,
											false,
											'style-options',
											'table-layout'
										);
										?>

										</div>

										<!-- Separator -->
										<?php

										$this->separator(
											'container',
											__( 'Container', 'league-table-lite' ),
											__( 'Wrap the table in a scrollable container. Useful for handling tables that exceed the dimensions of their parent element.', 'league-table-lite' ),
											'style-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Enable Container -->
										<?php
										$this->daextletal_toggle_field(
											'enable-container',
											__( 'Enable Container', 'league-table-lite' ),
											__( 'Enable this option to wrap the table in a container. When enabled, and with appropriate values set in the "Container Width" and "Container Height" options, the table can display horizontal and/or vertical scrollbars.', 'league-table-lite' ),
											$default_values->enable_container,
											'sorting-options',
											'container'
										);
										?>

										<!-- Container Width -->
										<?php
										$this->daextletal_input_field(
											'container-width',
											__( 'Container Width', 'league-table-lite' ),
											__( 'Enter the container width, or set it to 0 for automatic sizing.', 'league-table-lite' ),
											'',
											intval( $default_values->container_width, 10 ),
											6,
											false,
											'style-options',
											'container'
										);
										?>

										<!-- Container Height -->
										<?php
										$this->daextletal_input_field(
											'container-height',
											__( 'Container Height', 'league-table-lite' ),
											__( 'Enter the container height, or set it to 0 for automatic sizing.', 'league-table-lite' ),
											'',
											intval( $default_values->container_height, 10 ),
											6,
											false,
											'style-options',
											'container'
										);
										?>

										</div>

										<!-- Separator -->
										<?php

										$this->separator(
											'margin',
											__( 'Margins', 'league-table-lite' ),
											__( 'Set spacing above and below the table for better separation from surrounding elements.', 'league-table-lite' ),
											'style-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Table Margin Top -->
										<?php
										$this->daextletal_input_range_field(
											'table-margin-top',
											__( 'Table Margin Top', 'league-table-lite' ),
											__( 'Set the top margin of the table.', 'league-table-lite' ),
											$default_values->table_margin_top,
											'style-options',
											'margin',
											0,
											200,
											1
										)
										?>

										<!-- Table Margin Bottom -->
										<?php
										$this->daextletal_input_range_field(
											'table-margin-bottom',
											__( 'Table Margin Bottom', 'league-table-lite' ),
											__( 'Set the bottom margin of the table.', 'league-table-lite' ),
											$default_values->table_margin_bottom,
											'style-options',
											'margin',
											0,
											200,
											1
										)
										?>

										</div>

										<!-- Separator -->
										<?php

										$this->separator(
											'table-header-styles',
											__( 'Table Header Styles', 'league-table-lite' ),
											__( 'Customize the appearance of the table header, including fonts, alignment, and background color.', 'league-table-lite' ),
											'style-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Show Header -->
										<?php
										$this->daextletal_toggle_field(
											'show-header',
											__( 'Show Header', 'league-table-lite' ),
											__( 'Display the table header.', 'league-table-lite' ),
											$default_values->show_header,
											'sorting-options',
											'table-header-styles'
										);
										?>

										<!-- Header Font Size -->
										<?php
										$this->daextletal_input_range_field(
											'header-font-size',
											__( 'Header Font Size', 'league-table-lite' ),
											__( 'Set the font size for the text in the table header.', 'league-table-lite' ),
											$default_values->header_font_size,
											'style-options',
											'table-header-styles',
											1,
											40,
											1
										)
										?>

										<!-- Header Font Family -->
										<?php
										$this->daextletal_input_field(
											'header-font-family',
											__( 'Header Font Family', 'league-table-lite' ),
											__( 'Choose the font family for the text in the table header.', 'league-table-lite' ),
											"'Open Sans', Helvetica, Arial, sans-serif",
											stripslashes( $default_values->header_font_family ),
											255,
											false,
											'style-options',
											'table-header-styles'
										);
										?>

										<!-- Header Font Weight -->
										<?php
										$this->daextletal_select_field(
											'header-font-weight',
											__( 'Header Font Weight', 'league-table-lite' ),
											__( 'Choose the font weight for the text in the table header."', 'league-table-lite' ),
											array(
												'100' => __( '100', 'league-table-lite' ),
												'200' => __( '200', 'league-table-lite' ),
												'300' => __( '300', 'league-table-lite' ),
												'400' => __( '400', 'league-table-lite' ),
												'500' => __( '500', 'league-table-lite' ),
												'600' => __( '600', 'league-table-lite' ),
												'700' => __( '700', 'league-table-lite' ),
												'800' => __( '800', 'league-table-lite' ),
												'900' => __( '900', 'league-table-lite' ),
											),
											$default_values->header_font_weight,
											'style-options',
											'table-header-styles'
										);
										?>

										<!-- Header Font Style -->
										<?php
										$this->daextletal_select_field(
											'header-font-style',
											__( 'Header Font Style', 'league-table-lite' ),
											__( 'Choose the font style for the text in the table header.', 'league-table-lite' ),
											array(
												'normal'  => __( 'Normal', 'league-table-lite' ),
												'italic'  => __( 'Italic', 'league-table-lite' ),
												'oblique' => __( 'Oblique', 'league-table-lite' ),
											),
											$default_values->header_font_style,
											'style-options',
											'table-header-styles'
										);
										?>

										<!-- Header Position Alignment -->
										<?php
										$this->daextletal_select_field(
											'header-position-alignment',
											__( 'Header Position Alignment', 'league-table-lite' ),
											__( 'Set the text alignment for the header of the position column.', 'league-table-lite' ),
											array(
												'left'   => __( 'Left', 'league-table-lite' ),
												'center' => __( 'Center', 'league-table-lite' ),
												'right'  => __( 'Right', 'league-table-lite' ),
											),
											$default_values->header_position_alignment,
											'style-options',
											'table-header-styles'
										);
										?>

										<!-- Header Background Color -->
										<?php
										$this->daextletal_color_picker(
											'header-background-color',
											__( 'Header Background Color', 'league-table-lite' ),
											__( 'Choose the background color for the table header.', 'league-table-lite' ),
											'',
											$default_values->header_background_color,
											7,
											false,
											'style-options',
											'table-header-styles'
										);
										?>

										<!-- Header Font Color -->
										<?php
										$this->daextletal_color_picker(
											'header-font-color',
											__( 'Header Font Color', 'league-table-lite' ),
											__( 'Choose the text color for the table header.', 'league-table-lite' ),
											'',
											$default_values->header_font_color,
											7,
											false,
											'style-options',
											'table-header-styles'
										);
										?>

										<!-- Header Link Color -->
										<?php
										$this->daextletal_color_picker(
											'header-link-color',
											__( 'Header Link Color', 'league-table-lite' ),
											__( 'Choose the text color for the links in the table header.', 'league-table-lite' ),
											'',
											$default_values->header_link_color,
											7,
											false,
											'style-options',
											'table-header-styles'
										);
										?>

										<!-- Header Border Color -->
										<?php
										$this->daextletal_color_picker(
											'header-border-color',
											__( 'Header Border Color', 'league-table-lite' ),
											__( 'Choose the border color for the table header.', 'league-table-lite' ),
											'',
											$default_values->header_border_color,
											7,
											false,
											'style-options',
											'table-header-styles'
										);
										?>

										</div>

										<!-- Separator -->
										<?php

										$this->separator(
											'table-body-styles',
											__( 'Table Body Styles', 'league-table-lite' ),
											__( 'Customize the appearance of the table body, including fonts, row striping, and border colors.', 'league-table-lite' ),
											'style-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Body Font Size -->
										<?php
										$this->daextletal_input_range_field(
											'body-font-size',
											__( 'Body Font Size', 'league-table-lite' ),
											__( 'Set the font size for the text in the table body.', 'league-table-lite' ),
											$default_values->body_font_size,
											'style-options',
											'table-body-styles',
											1,
											40,
											1
										)
										?>

										<!-- Body Font Family -->
										<?php
										$this->daextletal_input_field(
											'body-font-family',
											__( 'Body Font Family', 'league-table-lite' ),
											__( 'Choose the font family for the text in the table body.', 'league-table-lite' ),
											'',
											stripslashes( $default_values->body_font_family ),
											255,
											false,
											'style-options',
											'table-body-styles'
										);
										?>

										<!-- Body Font Weight -->
										<?php
										$this->daextletal_select_field(
											'body-font-weight',
											__( 'Body Font Weight', 'league-table-lite' ),
											__( 'Choose the font weight for the text in the table body.', 'league-table-lite' ),
											array(
												'100' => __( '100', 'league-table-lite' ),
												'200' => __( '200', 'league-table-lite' ),
												'300' => __( '300', 'league-table-lite' ),
												'400' => __( '400', 'league-table-lite' ),
												'500' => __( '500', 'league-table-lite' ),
												'600' => __( '600', 'league-table-lite' ),
												'700' => __( '700', 'league-table-lite' ),
												'800' => __( '800', 'league-table-lite' ),
												'900' => __( '900', 'league-table-lite' ),
											),
											$default_values->body_font_weight,
											'style-options',
											'table-body-styles'
										);
										?>

										<!-- Body Font Style -->
										<?php
										$this->daextletal_select_field(
											'body-font-style',
											__( 'Body Font Style', 'league-table-lite' ),
											__( 'Choose the font style for the text in the table body.', 'league-table-lite' ),
											array(
												'normal'  => __( 'Normal', 'league-table-lite' ),
												'italic'  => __( 'Italic', 'league-table-lite' ),
												'oblique' => __( 'Oblique', 'league-table-lite' ),
											),
											$default_values->body_font_style,
											'style-options',
											'table-body-styles'
										);
										?>

										<!-- Even Rows Bg Color -->
										<?php
										$this->daextletal_color_picker(
											'even-rows-background-color',
											__( 'Even Rows Background Color', 'league-table-lite' ),
											__( 'Choose the background color for even-numbered rows.', 'league-table-lite' ),
											'',
											$default_values->even_rows_background_color,
											7,
											false,
											'style-options',
											'table-body-styles'
										);
										?>

										<!-- Odd Rows Background Color -->
										<?php
										$this->daextletal_color_picker(
											'odd-rows-background-color',
											__( 'Odd Rows Background Color', 'league-table-lite' ),
											__( 'Choose the background color for odd-numbered rows.', 'league-table-lite' ),
											'',
											$default_values->odd_rows_background_color,
											7,
											false,
											'style-options',
											'table-body-styles'
										);
										?>

										<!-- Even Rows Font Color -->
										<?php
										$this->daextletal_color_picker(
											'even-rows-font-color',
											__( 'Even Rows Font Color', 'league-table-lite' ),
											__( 'Choose the text color for even-numbered rows.', 'league-table-lite' ),
											'',
											$default_values->even_rows_font_color,
											7,
											false,
											'style-options',
											'table-body-styles'
										);
										?>

										<!-- Odd Rows Font Color -->
										<?php
										$this->daextletal_color_picker(
											'odd-rows-font-color',
											__( 'Odd Rows Font Color', 'league-table-lite' ),
											__( 'Choose the text color for odd-numbered rows.', 'league-table-lite' ),
											'',
											$default_values->odd_rows_font_color,
											7,
											false,
											'style-options',
											'table-body-styles'
										);
										?>

										<!-- Even Rows Link Color -->
										<?php
										$this->daextletal_color_picker(
											'even-rows-link-color',
											__( 'Even Rows Link Color', 'league-table-lite' ),
											__( 'Choose the text link color for even-numbered rows.', 'league-table-lite' ),
											'',
											$default_values->even_rows_link_color,
											7,
											false,
											'style-options',
											'table-body-styles'
										);
										?>

										<!-- Odd Rows Link Color -->
										<?php
										$this->daextletal_color_picker(
											'odd-rows-link-color',
											__( 'Odd Rows Link Color', 'league-table-lite' ),
											__( 'Choose the text link color for odd-numbered rows.', 'league-table-lite' ),
											'',
											$default_values->odd_rows_link_color,
											7,
											false,
											'style-options',
											'table-body-styles'
										);
										?>

										<!-- Rows Border Color -->
										<?php
										$this->daextletal_color_picker(
											'rows-border-color',
											__( 'Rows Border Color', 'league-table-lite' ),
											__( 'Choose the border color for the table rows.', 'league-table-lite' ),
											'',
											$default_values->rows_border_color,
											7,
											false,
											'style-options',
											'table-body-styles'
										);
										?>

										</div>

									</div>

								</div>

								<!-- Autoalignment Options ---------------------------------------------- -->
								<div class="daextletal-main-form__daext-form-section" data-id="autoalignment-options">

									<div class="daextletal-main-form__section-header group-trigger"
										data-trigger-target="autoalignment-options">
										<div class="daextletal-main-form__section-header-title">
											<?php $this->shared->echo_icon_svg( 'align-horizontal-centre-01' ); ?>
											<div class="daextletal-main-form__section-header-title-text">
												Autoalignment
											</div>
										</div>
										<div class="daextletal-main-form__section-header-toggle">
											<?php $this->shared->echo_icon_svg( 'chevron-down' ); ?>
										</div>
									</div>

									<div class="daextletal-main-form__daext-form-section-body"
										data-section-id="autoalignment-options">

										<!-- Separator -->
										<?php

										$this->separator(
											'autoalignment-priority',
											__( 'Autoalignment Priority', 'league-table-lite' ),
											__( 'Define whether row-based or column-based alignments should take priority when both are applied to the same cell.', 'league-table-lite' ),
											'autoalignment-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Autoalignment Priority -->
										<?php
										$this->daextletal_select_field(
											'autoalignment-priority',
											__( 'Priority Selection', 'league-table-lite' ),
											__( 'Select the autoalignment category to prioritize.', 'league-table-lite' ),
											array(
												'rows'    => __( 'Rows', 'league-table-lite' ),
												'columns' => __( 'Columns', 'league-table-lite' ),
											),
											$default_values->autoalignment_priority,
											'autoalignment-options',
											'autoalignment-priority'
										);
										?>

										</div>

										<!-- Separator -->
										<?php

										$this->separator(
											'row-autoalignment',
											__( 'Row Autoalignment', 'league-table-lite' ),
											__( 'Automatically align content in specific rows. Assign left, center, or right alignment by entering the corresponding row indexes.', 'league-table-lite' ),
											'autoalignment-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Autoalignment Affected Rows Left -->
										<?php
										$this->daextletal_input_field(
											'autoalignment-affected-rows-left',
											__( 'Affected Rows (Left)', 'league-table-lite' ),
											__( 'Enter a comma-separated list of row indexes where left alignment should be applied.', 'league-table-lite' ),
											'',
											stripslashes( $default_values->autoalignment_affected_rows_left ),
											2000,
											false,
											'autoalignment-options',
											'row-autoalignment'
										);
										?>

										<!-- Autoalignment Affected Rows Center -->
										<?php
										$this->daextletal_input_field(
											'autoalignment-affected-rows-center',
											__( 'Affected Rows (Center)', 'league-table-lite' ),
											__( 'Enter a comma-separated list of row indexes where center alignment should be applied.', 'league-table-lite' ),
											'',
											stripslashes( $default_values->autoalignment_affected_rows_center ),
											2000,
											false,
											'autoalignment-options',
											'row-autoalignment'
										);
										?>

										<!-- Autoalignment Affected Rows Right -->
										<?php
										$this->daextletal_input_field(
											'autoalignment-affected-rows-right',
											__( 'Affected Rows (Right)', 'league-table-lite' ),
											__( 'Enter a comma-separated list of row indexes where right alignment should be applied.', 'league-table-lite' ),
											'',
											stripslashes( $default_values->autoalignment_affected_rows_right ),
											2000,
											false,
											'autoalignment-options',
											'row-autoalignment'
										);
										?>

										</div>

										<!-- Separator -->
										<?php

										$this->separator(
											'column-autoalignment',
											__( 'Column Autoalignment', 'league-table-lite' ),
											__( 'Automatically align content in specific columns. Assign left, center, or right alignment by entering the corresponding column indexes.', 'league-table-lite' ),
											'autoalignment-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Autoalignment Affected Columns Left -->
										<?php
										$this->daextletal_input_field(
											'autoalignment-affected-columns-left',
											__( 'Affected Columns (Left)', 'league-table-lite' ),
											__( 'Enter a comma-separated list of column indexes where left alignment should be applied.', 'league-table-lite' ),
											'',
											stripslashes( $default_values->autoalignment_affected_columns_left ),
											110,
											false,
											'autoalignment-options',
											'column-autoalignment'
										);
										?>

										<!-- Autoalignment Affected Columns Center -->
										<?php
										$this->daextletal_input_field(
											'autoalignment-affected-columns-center',
											__( 'Affected Columns (Center)', 'league-table-lite' ),
											__( 'Enter a comma-separated list of column indexes where center alignment should be applied.', 'league-table-lite' ),
											'',
											stripslashes( $default_values->autoalignment_affected_columns_center ),
											110,
											false,
											'autoalignment-options',
											'column-autoalignment'
										);
										?>

										<!-- Autoalignment Affected Columns Right -->
										<?php
										$this->daextletal_input_field(
											'autoalignment-affected-columns-right',
											__( 'Affected Columns (Right)', 'league-table-lite' ),
											__( 'Enter a comma-separated list of column indexes where right alignment should be applied.', 'league-table-lite' ),
											'',
											stripslashes( $default_values->autoalignment_affected_columns_right ),
											110,
											false,
											'autoalignment-options',
											'column-autoalignment'
										);
										?>

										</div>

									</div>

								</div>

								<!-- Responsive Options ---------------------------------------------- -->
								<div class="daextletal-main-form__daext-form-section" data-id="responsive-options">

									<div class="daextletal-main-form__section-header group-trigger"
										data-trigger-target="responsive-options">
										<div class="daextletal-main-form__section-header-title">
											<?php $this->shared->echo_icon_svg( 'monitor-02' ); ?>
											<div class="daextletal-main-form__section-header-title-text">Responsive
											</div>
										</div>
										<div class="daextletal-main-form__section-header-toggle">
											<?php $this->shared->echo_icon_svg( 'chevron-down' ); ?>
										</div>
									</div>

									<div class="daextletal-main-form__daext-form-section-body"
										data-section-id="responsive-options">

										<!-- Separator -->
										<?php

										$this->separator(
											'tablet-settings',
											__( 'Tablet Settings', 'league-table-lite' ),
											__( 'Configure how the table appears on tablet devices when the viewport width falls below the defined tablet breakpoint.', 'league-table-lite' ),
											'responsive-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Tablet Breakpoint -->
										<?php
										$this->daextletal_input_field(
											'tablet-breakpoint',
											__( 'Tablet Breakpoint', 'league-table-lite' ),
											__( 'Set the viewport width value below which the device will be considered a tablet.', 'league-table-lite' ),
											'',
											intval( $default_values->tablet_breakpoint, 10 ),
											6,
											false,
											'responsive-options',
											'tablet-settings'
										);
										?>

										<!-- Hide Tablet List -->
										<?php
										$this->daextletal_input_field(
											'hide-tablet-list',
											__( 'Tablet Hide List', 'league-table-lite' ),
											__( 'Enter a comma-separated list of column indexes to hide when the device is classified as a tablet.', 'league-table-lite' ),
											'',
											stripslashes( $default_values->hide_tablet_list ),
											110,
											false,
											'responsive-options',
											'tablet-settings'
										);
										?>

										<!-- Tablet Header Font Size -->
										<?php
										$this->daextletal_input_range_field(
											'tablet-header-font-size',
											__( 'Tablet Header Font Size', 'league-table-lite' ),
											__( 'Set the font size for the text in the table header when the device is classified as a tablet.', 'league-table-lite' ),
											$default_values->tablet_header_font_size,
											'responsive-options',
											'tablet-settings',
											1,
											40,
											1
										)
										?>

										<!-- Tablet Body Font Size -->
										<?php
										$this->daextletal_input_range_field(
											'tablet-body-font-size',
											__( 'Tablet Body Font Size', 'league-table-lite' ),
											__( 'Set the font size for the text in the table body when the device is classified as a tablet.', 'league-table-lite' ),
											$default_values->tablet_body_font_size,
											'responsive-options',
											'tablet-settings',
											1,
											40,
											1
										)
										?>

										<!-- Tablet Hide Images -->
										<?php
										$this->daextletal_toggle_field(
											'tablet-hide-images',
											__( 'Tablet Hide Images', 'league-table-lite' ),
											__( 'Hide the images included in the cells when the device is classified as a tablet.', 'league-table-lite' ),
											$default_values->tablet_hide_images,
											'sorting-options',
											'tablet-settings'
										);
										?>

										</div>

										<!-- Separator -->
										<?php

										$this->separator(
											'phone-settings',
											__( 'Phone Settings', 'league-table-lite' ),
											__( 'Configure how the table appears on phone devices when the viewport width falls below the defined phone breakpoint.', 'league-table-lite' ),
											'responsive-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Phone Breakpoint -->
										<?php
										$this->daextletal_input_field(
											'phone-breakpoint',
											__( 'Phone Breakpoint', 'league-table-lite' ),
											__( 'Set the viewport width value below which the device will be considered a phone.', 'league-table-lite' ),
											'',
											intval( $default_values->phone_breakpoint, 10 ),
											6,
											false,
											'responsive-options',
											'phone-settings'
										);
										?>

										<!-- Hide Phone List -->
										<?php
										$this->daextletal_input_field(
											'hide-phone-list',
											__( 'Phone Hide List', 'league-table-lite' ),
											__( 'Enter a comma-separated list of column indexes to hide when the device is classified as a phone.', 'league-table-lite' ),
											'',
											stripslashes( $default_values->hide_phone_list ),
											110,
											false,
											'responsive-options',
											'phone-settings'
										);
										?>

										<!-- Phone Header Font Size -->
										<?php
										$this->daextletal_input_range_field(
											'phone-header-font-size',
											__( 'Phone Header Font Size', 'league-table-lite' ),
											__( 'Set the font size for the text in the table header when the device is classified as a phone.', 'league-table-lite' ),
											$default_values->phone_header_font_size,
											'responsive-options',
											'phone-settings',
											1,
											40,
											1
										)
										?>

										<!-- Phone Body Font Size -->
										<?php
										$this->daextletal_input_range_field(
											'phone-body-font-size',
											__( 'Phone Body Font Size', 'league-table-lite' ),
											__( 'Set the font size for the text in the table body when the device is classified as a phone.', 'league-table-lite' ),
											$default_values->phone_body_font_size,
											'responsive-options',
											'phone-settings',
											1,
											40,
											1
										)
										?>

										<!-- Phone Hide Images -->
										<?php
										$this->daextletal_toggle_field(
											'phone-hide-images',
											__( 'Phone Hide Images', 'league-table-lite' ),
											__( 'Hide the images included in the cells when the device is classified as a phone.', 'league-table-lite' ),
											$default_values->tablet_hide_images,
											'sorting-options',
											'phone-settings'
										);
										?>

										</div>

									</div>
								</div>

								<!-- Advanced Options ---------------------------------------------- -->
								<div class="daextletal-main-form__daext-form-section" data-id="advanced-options">

									<div class="daextletal-main-form__section-header group-trigger"
										data-trigger-target="advanced-options">
										<div class="daextletal-main-form__section-header-title">
											<?php $this->shared->echo_icon_svg( 'settings-01' ); ?>
											<div class="daextletal-main-form__section-header-title-text">Advanced
											</div>
										</div>
										<div class="daextletal-main-form__section-header-toggle">
											<?php $this->shared->echo_icon_svg( 'chevron-down' ); ?>
										</div>
									</div>

									<div class="daextletal-main-form__daext-form-section-body"
										data-section-id="advanced-options">

										<!-- Separator -->
										<?php

										$this->separator(
											'performance',
											__( 'Performance', 'league-table-lite' ),
											__( 'Control settings that impact the rendering speed and performance of the table.', 'league-table-lite' ),
											'advanced-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Enable Cell Properties -->
										<?php
										$this->daextletal_toggle_field(
											'enable-cell-properties',
											__( 'Enable Cell Properties', 'league-table-lite' ),
											__( 'Apply the cell properties. Disabling this option will reduce the render time in the browser and the time required to generate the table on the server.', 'league-table-lite' ),
											$default_values->enable_cell_properties,
											'advanced-options',
											'performance'
										);
										?>

										</div>

										<!-- Separator -->
										<?php

										$this->separator(
											'table-metadata',
											__( 'Table Metadata', 'league-table-lite' ),
											__( 'Define metadata such as the description, which help identify and describe the table.', 'league-table-lite' ),
											'advanced-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Description -->
										<?php
										$this->daextletal_input_field(
											'description',
											__( 'Description', 'league-table-lite' ),
											__( 'Enter the description of the table.', 'league-table-lite' ),
											'',
											esc_attr( stripslashes( $default_values->description ) ),
											255,
											false,
											'advanced-options',
											'table-metadata'
										);
										?>

										</div>

										<!-- Separator -->
										<?php

										$this->separator(
											'sorting-behavior',
											__( 'Sorting Behavior', 'league-table-lite' ),
											__( 'Customize how data is sorted in the table, including how numeric values are interpreted based on their format.', 'league-table-lite' ),
											'advanced-options'
										);

										?>

										<div class="daextletal-main-form__subsection-container">

										<!-- Number Format -->
										<?php
										$this->daextletal_select_field(
											'number-format',
											__( 'Number Format', 'league-table-lite' ),
											__( 'Select the decimal separator used for fractional numbers. This setting affects the sorting of the "Currency" data type.', 'league-table-lite' ),
											array(
												'0' => __( 'Comma (EU)', 'league-table-lite' ),
												'1' => __( 'Point (US)', 'league-table-lite' ),
											),
											$default_values->number_format,
											'sorting-options',
											'sorting-behavior'
										);
										?>

										</div>

									</div>

								</div>

							</div>

							<div id="sidebar-container" <?php echo $this->shared->are_all_cell_properties_disabled() ? 'class="display-none-important"' : ''; ?>>
								<div class="daext-form daext-form-cell-properties">

									<div class="daextletal-main-form__daext-form-section" data-id="cell-properties">

										<div class="daextletal-main-form__daext-form-section-body"
										     data-section-id="cell-properties">

											<h3 class="daext-form-title"
											    id="cell-properties-title"><?php esc_html_e( 'Body', 'league-table-lite' ); ?>
												&nbsp1:1</h3>

											<!-- Cell Index Hidden Fields -->
											<input type="hidden" id="cell-property-row-index" value="1">
											<input type="hidden" id="cell-property-column-index" value="0">

											<!-- Link -->
											<?php
											$enable_link_cell_property = 1 === intval( get_option( $this->shared->get( 'slug' ) . '_enable_link_cell_property' ), 10 );
											$this->daextletal_input_field(
												'cell-property-link',
												__( 'Link', 'league-table-lite' ),
												__( 'Enter an URL to link the text of the cell to a specific destination.', 'league-table-lite' ),
												'',
												'',
												2083,
												false,
												'cell-properties',
												null,
												$enable_link_cell_property
											);
											?>

											<!-- Image Left -->
											<?php
											$enable_image_left_cell_property = 1 === intval( get_option( $this->shared->get( 'slug' ) . '_enable_image_left_cell_property' ), 10 );
											$this->daextletal_image_uploader_field(
												'cell-property-image-left',
												__( 'Image Left', 'league-table-lite' ),
												__( 'Select an image that should be placed on the left of the text.', 'league-table-lite' ),
												'cell-properties',
												null,
												$enable_image_left_cell_property
											);
											?>

											<!-- Image Right -->
											<?php
											$enable_image_right_cell_property = 1 === intval( get_option( $this->shared->get( 'slug' ) . '_enable_image_right_cell_property' ), 10 );
											$this->daextletal_image_uploader_field(
												'cell-property-image-right',
												__( 'Image Right', 'league-table-lite' ),
												__( 'Select an image that should be placed on the right of the text.', 'league-table-lite' ),
												'cell-properties',
												null,
												$enable_image_right_cell_property
											);
											?>

											<!-- submit button -->
											<div class="daext-form-action">
												<input id="update-cell-properties" data-action=""
												       class="update-reset-cell-properties daextletal-btn daextletal-btn-secondary"
												       type="submit"
												       value="<?php esc_attr_e( 'Update Cell Properties', 'league-table-lite' ); ?>">
												<input id="reset-cell-properties"
												       class="update-reset-cell-properties daextletal-btn daextletal-btn-secondary"
												       type="submit"
												       value="<?php esc_attr_e( 'Reset Cell Properties', 'league-table-lite' ); ?>">
											</div>

										</div>

									</div>

								</div>

								<div id="cell-properties-error-message" class="error settings-error notice below-h2">
									<p></p>
								</div>
								<div id="cell-properties-added-updated-message"
								     class="updated settings-error notice below-h2">
									<p></p></div>

							</div>

						</div>

						<?php endif; ?>

					</div>

			<?php endif; ?>

			<!-- Dialog Keyboard Shortcut -->
			<div class="dialog-alert daext-display-none" data-id="dialog-keyboard-shortcut"
				title="<?php esc_attr_e( 'Please use the keyboard shortcut', 'league-table-lite' ); ?>">
				<p><?php esc_html_e( 'Due to security reason, modern browsers disallow to read from the system clipboard:', 'league-table-lite' ); ?></p>
				<p><?php echo 'https://www.w3.org/TR/clipboard-apis/#privacy'; ?></p>
				<p><?php esc_html_e( 'Please use Ctrl+V (Windows/Linux) or Command+V (Mac) to perform this operation.', 'league-table-lite' ); ?></p>
			</div>

			<!-- Valid Cell Number -->
			<div class="dialog-alert daext-display-none" data-id="valid-cell-number"
				title="<?php esc_attr_e( 'Please reduce the number of select cells', 'league-table-lite' ); ?>">
				<p><?php esc_html_e( 'For performance reasons, the maximum number of cells allowed in this context menu operation is equal to 100.', 'league-table-lite' ); ?></p>
				<p><?php esc_html_e( 'Please reduce the number of selected cells to perform this operation.', 'league-table-lite' ); ?></p>
			</div>

			<!-- Specific Shortcut Disabled -->
			<div class="dialog-alert daext-display-none" data-id="specific-shortcut-disabled"
				title="<?php esc_attr_e( 'Please use the context menu', 'league-table-lite' ); ?>">
				<p><?php esc_html_e( 'Specific keyboard shortcuts are disabled on the spreadsheet editor.', 'league-table-lite' ); ?></p>
				<p><?php esc_html_e( 'Please click the right mouse button and use the context menu.', 'league-table-lite' ); ?></p>
			</div>

		</div>

		<?php
	}

		/**
		 * Check if the item is deletable. If not, return the message to be displayed.
		 *
		 * @param int $item_id The ID of the item.
		 *
		 * @return array
		 */
	public function item_is_deletable( $item_id ) {

		$is_deletable               = true;
		$dismissible_notice_message = null;

		return array(
			'is_deletable'               => $is_deletable,
			'dismissible_notice_message' => $dismissible_notice_message,
		);
	}

	/**
	 * Generate an HTML input field.
	 *
	 * @param string $name The HTML element name.
	 * @param string $label The displayed name of the field.
	 * @param string $description The displayed description of the field.
	 * @param string $placeholder The placeholder of the field.
	 * @param string $value The value of the field.
	 * @param int    $maxlength The maximum length of the field.
	 * @param bool   $required If the field is required.
	 * @param string $section_id The id of the section.
	 * @param string $subsection_id The id of the subsection.
	 * @param bool   $display If the field should be displayed.
	 *
	 * @return void
	 */
	private function daextletal_input_field(
		$name = '',
		$label = '',
		$description = '',
		$placeholder = '',
		$value = null,
		$maxlength = null,
		$required = false,
		$section_id = null,
		$subsection_id = null,
		$display = true
	) {

		?>

		<div class="daextletal-main-form__daext-form-field <?php echo $display ? '' : 'display-none'; ?>" valign="top" data-section-id="<?php echo esc_attr( $section_id ); ?>" <?php echo null !== $subsection_id ? 'data-subsection-id="' . esc_attr( $subsection_id ) . '"' : ''; ?>>
			<div>
				<label for="title"><?php echo esc_html( $label ); ?><?php echo $required ? ' <span class="daextletal-required">*</span>' : ''; ?></label>
			</div>
			<div>
				<input type="text" id="<?php echo esc_attr( $name ); ?>" maxlength="<?php echo esc_attr( $maxlength ); ?>"
						size="30"
						placeholder="<?php echo esc_attr( $placeholder ); ?>"
						name="<?php echo esc_attr( $name ); ?>"
					<?php
					if ( ! is_null( $value ) ) {
						echo 'value="' . esc_attr( $value ) . '"';
					}
					?>
				/>
			</div>
			<?php if ( '' !== $description ) : ?>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>

		<?php
	}

	/**
	 * Generate an HTML textarea field.
	 *
	 * @param string $name The HTML element name.
	 * @param string $label The displayed name of the field.
	 * @param string $description The displayed description of the field.
	 * @param string $placeholder The placeholder of the field.
	 * @param string $value The value of the field.
	 * @param int    $maxlength The maximum length of the field.
	 * @param bool   $required If the field is required.
	 * @param string $section_id The id of the section.
	 * @param string $subsection_id The id of the subsection.
	 * @param bool   $display If the field should be displayed.
	 *
	 * @return void
	 */
	private function daextletal_textarea_field(
		$name = '',
		$label = '',
		$description = '',
		$placeholder = '',
		$value = null,
		$maxlength = null,
		$required = false,
		$section_id = null,
		$subsection_id = null,
		$display = true
	) {

		?>

		<div class="daextletal-main-form__daext-form-field <?php echo $display ? '' : 'display-none'; ?>" valign="top" data-section-id="<?php echo esc_attr( $section_id ); ?>" <?php echo null !== $subsection_id ? 'data-subsection-id="' . esc_attr( $subsection_id ) . '"' : ''; ?>>
		<div>
			<label for="title"><?php echo esc_html( $label ); ?><?php echo $required ? ' <span class="daextletal-required">*</span>' : ''; ?></label>
		</div>
		<div>
			<textarea type="text" id="<?php echo esc_attr( $name ); ?>" maxlength="<?php echo esc_attr( $maxlength ); ?>"
					size="30"
					placeholder="<?php echo esc_attr( $placeholder ); ?>"
					name="<?php echo esc_attr( $name ); ?>"

			><?php echo esc_html( $value ); ?></textarea>
		</div>
		<?php if ( '' !== $description ) : ?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
		</div>

		<?php
	}

	/**
	 * Generate an HTML input field.
	 *
	 * @param string $name The HTML element name.
	 * @param string $label The displayed name of the field.
	 * @param string $description The displayed description of the field.
	 * @param string $placeholder The placeholder of the field.
	 * @param string $value The value of the field.
	 * @param int    $maxlength The maximum length of the field.
	 * @param bool   $required If the field is required.
	 * @param string $section_id The id of the section.
	 * @param string $subsection_id The id of the subsection.
	 * @param bool   $display If the field should be displayed.
	 *
	 * @return void
	 */
	private function daextletal_color_picker(
		$name = '',
		$label = '',
		$description = '',
		$placeholder = '',
		$value = null,
		$maxlength = null,
		$required = false,
		$section_id = null,
		$subsection_id = null,
		$display = true
	) {

		?>

		<div id="<?php echo esc_attr( $name ) . '-container'; ?>" class="daextletal-main-form__daext-form-field <?php echo $display ? '' : 'display-none'; ?>" valign="top" data-section-id="<?php echo esc_attr( $section_id ); ?>" <?php echo null !== $subsection_id ? 'data-subsection-id="' . esc_attr( $subsection_id ) . '"' : ''; ?>>
			<div>
				<label for="title"><?php echo esc_html( $label ); ?><?php echo $required ? ' <span class="daextletal-required">*</span>' : ''; ?></label>
			</div>
			<div>
				<input type="text" id="<?php echo esc_attr( $name ); ?>" maxlength="<?php echo esc_attr( $maxlength ); ?>"
						size="30"
						placeholder="<?php echo esc_attr( $placeholder ); ?>"
						class="wp-color-picker"
						name="<?php echo esc_attr( $name ); ?>"
					<?php
					if ( ! is_null( $value ) ) {
						echo 'value="' . esc_attr( $value ) . '"';
					}
					?>
				/>
			</div>
			<?php if ( '' !== $description ) : ?>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>

		<?php
	}

	/**
	 * Generate an HTML input field.
	 *
	 * @param string $name The HTML element name.
	 * @param string $label The displayed name of the field.
	 * @param string $description The displayed description of the field.
	 * @param string $value The value of the field.
	 * @param string $section_id The id of the section.
	 * @param string $subsection_id The id of the subsection.
	 * @param bool   $display If the field should be displayed.
	 *
	 * @return void
	 */
	private function daextletal_toggle_field(
		$name = '',
		$label = '',
		$description = '',
		$value = null,
		$section_id = null,
		$subsection_id = null,
		$display = true
	) {

		?>

		<div class="daextletal-main-form__daext-form-field <?php echo $display ? '' : 'display-none'; ?>" valign="top" data-section-id="<?php echo esc_attr( $section_id ); ?>" <?php echo null !== $subsection_id ? 'data-subsection-id="' . esc_attr( $subsection_id ) . '"' : ''; ?>>
			<div class="switch-container">
				<div class="switch-left">
					<label class="switch">
						<input id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>"
								type="checkbox" <?php checked( intval( $value, 10 ), 1 ); ?>>
						<span class="slider round"></span>
					</label>
				</div>
				<div class="switch-right">
					<div><label for="title"><?php echo esc_html( $label ); ?></label></div>
				</div>
			</div>
			<?php if ( '' !== $description ) : ?>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>

		<?php
	}

	/**
	 * Generate an HTML input field.
	 *
	 * @param string $name The HTML element name.
	 * @param string $label The displayed name of the field.
	 * @param string $description The displayed description of the field.
	 * @param string $value The value of the field.
	 * @param string $section_id The id of the section.
	 * @param string $subsection_id The id of the subsection.
	 * @param string $min The minimum value of the range.
	 * @param string $max The maximum value of the range.
	 * @param string $step The step of the range.
	 * @param bool   $display If the field should be displayed.
	 *
	 * @return void
	 */
	private function daextletal_input_range_field(
		$name = '',
		$label = '',
		$description = '',
		$value = null,
		$section_id = null,
		$subsection_id = null,
		$min = null,
		$max = null,
		$step = null,
		$display = true
	) {

		?>

		<div class="daextletal-main-form__daext-form-field <?php echo $display ? '' : 'display-none'; ?>" valign="top" data-section-id="<?php echo esc_attr( $section_id ); ?>" <?php echo null !== $subsection_id ? 'data-subsection-id="' . esc_attr( $subsection_id ) . '"' : ''; ?>>
			<div><label for="title"><?php echo esc_html( $label ); ?></label></div>
			<div>
				<input
						type="range"
						id="<?php echo esc_attr( $name ); ?>"
						maxlength="100"
						size="30"
						name="<?php echo esc_attr( $name ); ?>"
						min="<?php echo esc_attr( $min ); ?>"
						max="<?php echo esc_attr( $max ); ?>"
						step="<?php echo esc_attr( $step ); ?>"
						data-range-sync-id="<?php echo esc_attr( $name ); ?>"
					<?php
					if ( ! is_null( $value ) ) {
						echo 'value="' . esc_attr( $value ) . '"';
					}
					?>
				/>
				<input
						class="inputNumber"
						type="number"
						min="<?php echo esc_attr( $min ); ?>"
						max="<?php echo esc_attr( $max ); ?>"
						value="<?php echo esc_attr( $value ); ?>"
						data-range-sync-id="<?php echo esc_attr( $name ); ?>"
				/>
			</div>
			<?php if ( '' !== $description ) : ?>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>

		<?php
	}

	/**
	 * Generate an HTML select field.
	 *
	 * @param string $name The HTML element name.
	 * @param string $label The displayed name of the field.
	 * @param string $description The displayed description of the field.
	 * @param string $options The options of the field.
	 * @param string $value The value of the field.
	 * @param string $section_id The id of the section.
	 * @param string $subsection_id The id of the subsection.
	 * @param string $display Whether the field should be displayed or hidden using the display property.
	 *
	 * @return void
	 */
	private function daextletal_select_field(
		$name = '',
		$label = '',
		$description = '',
		$options = null,
		$value = null,
		$section_id = null,
		$subsection_id = null,
		$display = true
	) {

		?>

		<div class="daextletal-main-form__daext-form-field <?php echo $display ? '' : 'display-none'; ?>" valign="top" data-section-id="<?php echo esc_attr( $section_id ); ?>" <?php echo null !== $subsection_id ? 'data-subsection-id="' . esc_attr( $subsection_id ) . '"' : ''; ?>>
			<div><label for="title"><?php echo esc_html( $label ); ?></label></div>
			<div>
				<select id="<?php echo esc_attr( $name ); ?>"
						name="<?php echo esc_attr( $name ); ?>">
					<?php
					foreach ( $options as $key => $option ) {

						/**
						 * Convert the key and value to integers if they are numeric. This prevents data types
						 * comparison issues in the next if statement that should use the identical operator.
						 */
						if ( is_numeric( $key ) && is_numeric( $value ) ) {
							$key   = intval( $key, 10 );
							$value = intval( $value, 10 );
						}

						echo '<option value="' . esc_attr( $key ) . '"';
						if ( $value === $key ) {
							echo 'selected';
						}
						echo '>' . esc_html( $option ) . '</option>';
					}
					?>
				</select>
			</div>
			<?php if ( '' !== $description ) : ?>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>

		<?php
	}

	/**
	 * Generate an HTML image uploader field.
	 *
	 * @param string $name The HTML element name.
	 * @param string $label The displayed name of the field.
	 * @param string $description The displayed description of the field.
	 * @param string $section_id The id of the section.
	 * @param string $subsection_id The id of the subsection.
	 * @param string $display Whether the field should be displayed or hidden using the display property.
	 *
	 * @return void
	 */
	private function daextletal_image_uploader_field(
		$name = '',
		$label = '',
		$description = '',
		$section_id = null,
		$subsection_id = null,
		$display = true
	) {

		?>

		<div class="daextletal-main-form__daext-form-field <?php echo $display ? '' : 'display-none'; ?>" valign="top" data-section-id="<?php echo esc_attr( $section_id ); ?>" <?php echo null !== $subsection_id ? 'data-subsection-id="' . esc_attr( $subsection_id ) . '"' : ''; ?>
			id="<?php echo esc_attr( $name ); ?>-container">
			<div>
				<label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
			</div>
			<div>
				<div class="image-uploader">
					<div class="image-container">
						<?php // phpcs:ignore -- PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- The image is not an attachment image. ?>
						<img class="selected-image" src="" style="display: none">
					</div>
					<input type="hidden" id="<?php echo esc_attr( $name ); ?>"
							maxlength="2083">
					<a class="button_add_media" data-set-remove="set"
						data-set="<?php esc_attr_e( 'Set image', 'league-table-lite' ); ?>"
						data-remove="<?php esc_attr_e( 'Remove Image', 'league-table-lite' ); ?>"><?php esc_html_e( 'Set image', 'league-table-lite' ); ?></a>
					<p class="description"><?php echo esc_html( $description ); ?></p>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Echo the placeholder HTML elements for the Handsontable spreadsheet.
	 *
	 * @return void
	 */
	private function daextletal_embedded_spreadsheet() {

		?>

		<div id="daextletal-data-embedded-spreadsheet-container" class="daextletal-main-form__daext-form-field" valign="top"
			data-section-id="main">
			<div>
				<label for="title"><?php esc_html_e( 'Data', 'league-table-lite' ); ?></label>
			</div>
			<div id="daextletal-table-td">
				<div id="daextletal-table"></div>
			</div>
			<p class="description"><?php esc_html_e( 'Enter and manage table data.', 'league-table-lite' ); ?></p>
		</div>

		<?php
	}

	/**
	 * Generate an HTML textarea field.
	 *
	 * @param string $name The HTML element name.
	 * @param string $title The displayed title of the separator.
	 * @param string $description The displayed description of the separator.
	 * @param string $section_id The id of the section.
	 *
	 * @return void
	 */
	public function separator(
		$name = '',
		$title = '',
		$description = '',
		$section_id = null
	) {

		?>

		<div class="daextletal-main-form__daext-form-field" valign="top" data-section-id="<?php echo esc_attr( $section_id ); ?>">
			<div class="separator">
				<div class="separator-title"><?php echo esc_html( $title ); ?></div>
				<div class="separator-description"><?php echo esc_html( $description ); ?></div>
			</div>
		</div>

		<?php
	}
}


