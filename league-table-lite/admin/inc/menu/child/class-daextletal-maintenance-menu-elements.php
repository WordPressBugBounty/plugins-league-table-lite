<?php
/**
 * Class used to implement the back-end functionalities of the "Maintenance" menu.
 *
 * @package league-table-lite
 */

/**
 * Class used to implement the back-end functionalities of the "Maintenance" menu.
 */
class Daextletal_Maintenance_Menu_Elements extends Daextletal_Menu_Elements {

	/**
	 * Constructor.
	 *
	 * @param object $shared The shared class.
	 * @param string $page_query_param The page query parameter.
	 * @param string $config The config parameter.
	 */
	public function __construct( $shared, $page_query_param, $config ) {

		parent::__construct( $shared, $page_query_param, $config );

		$this->menu_slug      = 'maintenance';
		$this->slug_plural    = 'maintenance';
		$this->label_singular = __( 'Maintenance', 'league-table-lite');
		$this->label_plural   = __( 'Maintenance', 'league-table-lite');
	}

	/**
	 * Process the add/edit form submission of the menu. Specifically the following tasks are performed:
	 *
	 * 1. Sanitization
	 * 2. Validation
	 * 3. Database update
	 *
	 * @return void
	 */
	public function process_form() {

		// Preliminary operations ---------------------------------------------------------------------------------------------.
		global $wpdb;

		if ( isset( $_POST['form_submitted'] ) ) {

			// Nonce verification.
			check_admin_referer( 'daextletal_execute_task', 'daextletal_execute_task_nonce' );

			// Sanitization ---------------------------------------------------------------------------------------------------.
			$data['task'] = isset( $_POST['task'] ) ? intval( $_POST['task'], 10 ) : null;

			// Validation -----------------------------------------------------------------------------------------------------.

			$invalid_data_message = '';
			$invalid_data         = false;

			if ( false === $invalid_data ) {

				switch ( $data['task'] ) {

					// Delete Data.
					case 0:
						// Delete data in the '_table', '_data', and '_cell' db tables.
						global $wpdb;

						// phpcs:ignore WordPress.DB.DirectDatabaseQuery
						$query_result_table = $wpdb->query( "DELETE FROM {$wpdb->prefix}daextletal_table WHERE temporary = 0" );

						// phpcs:ignore WordPress.DB.DirectDatabaseQuery
						$query_result_table_temporary = $wpdb->query( "DELETE FROM {$wpdb->prefix}daextletal_table WHERE temporary = 1" );

						// phpcs:ignore WordPress.DB.DirectDatabaseQuery
						$query_result_data = $wpdb->query( "DELETE FROM {$wpdb->prefix}daextletal_data" );

						// phpcs:ignore WordPress.DB.DirectDatabaseQuery
						$query_result_cell = $wpdb->query( "DELETE FROM {$wpdb->prefix}daextletal_cell" );

						if ( false !== $query_result_table ) {

							if ( $query_result_table > 0 ) {

								$this->shared->save_dismissible_notice(
									intval(
										$query_result_table,
										10
									) . ' ' . __( 'tables have been successfully deleted.', 'league-table-lite'),
									'updated'
								);

							} else {

								$this->shared->save_dismissible_notice(
									__( 'There are no table data.', 'league-table-lite'),
									'error'
								);

							}
						}

						break;

					// Reset Options.
					case 1:
						// Set the default values of the options.
						$this->shared->reset_plugin_options();

						$this->shared->save_dismissible_notice(
							__( 'The plugin options have been successfully set to their default values.', 'league-table-lite'),
							'updated'
						);

						break;

				}
			}
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

			<div class="daextletal-main-form">

				<form id="form-maintenance" method="POST"
						action="admin.php?page=<?php echo esc_attr( $this->shared->get( 'slug' ) ); ?>-maintenance"
						autocomplete="off">

					<div class="daextletal-main-form__daext-form-section">

						<div class="daextletal-main-form__daext-form-section-body">

							<input type="hidden" value="1" name="form_submitted">

							<?php wp_nonce_field( 'daextletal_execute_task', 'daextletal_execute_task_nonce' ); ?>

							<?php

							// Task.
							$this->select_field(
								'task',
								'Task',
								__( 'The task that should be performed.', 'league-table-lite'),
								array(
									'0' => __( 'Delete Table Data', 'league-table-lite'),
									'1' => __( 'Reset Plugin Options', 'league-table-lite'),
								),
								null,
								'main'
							);

							?>

							<!-- submit button -->
							<div class="daext-form-action">
								<input id="execute-task" class="daextletal-btn daextletal-btn-primary" type="submit"
										value="<?php esc_attr_e( 'Execute Task', 'league-table-lite'); ?>">
							</div>

						</div>

					</div>

				</form>

			</div>

		</div>

		<!-- Dialog Confirm -->
		<div id="dialog-confirm" title="<?php esc_attr_e( 'Maintenance Task', 'league-table-lite'); ?>" class="daext-display-none">
			<p><?php esc_html_e( 'Do you really want to proceed?', 'league-table-lite'); ?></p>
		</div>

		<?php
	}
}
