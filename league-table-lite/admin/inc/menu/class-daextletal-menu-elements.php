<?php
/**
 * Parent class used to create the admin pages.
 *
 * @package league-table-lite
 */

/**
 * Parent class used to create the admin pages.
 */
class Daextletal_Menu_Elements {

	/**
	 * The capability required to access the menu.
	 *
	 * @var string
	 */
	public $capability = null;

	/**
	 * The context of the menu.
	 *
	 * @var string
	 */
	public $context = null;

	/**
	 * Array with general menu data, like toolbar menu items.
	 *
	 * @var self
	 */
	public $config = null;

	/**
	 * An instance of the shared class.
	 *
	 * @var Daextletal_Shared
	 */
	public $shared = null;

	/**
	 * The menu slug.
	 *
	 * @var null
	 */
	public $menu_slug = null;

	/**
	 * The plural version of the slug.
	 *
	 * @var null
	 */
	public $slug_plural = null;

	/**
	 * The singular version of the displayed menu label.
	 *
	 * @var null
	 */
	public $label_singular = null;

	/**
	 * The plural version of the displayed menu label.
	 *
	 * @var null
	 */
	public $label_plural = null;

	/**
	 * The primary key of the database table associated with the managed back-end page.
	 *
	 * @var null
	 */
	public $primary_key = null;

	/**
	 * The name of the database table associated with the managed back-end page.
	 *
	 * @var null
	 */
	public $db_table = null;

	/**
	 * The list of columns to display in the table.
	 *
	 * @var null
	 */
	public $list_table_columns = null;

	/**
	 * The list of database table fields that can be searched using the menu search field.
	 *
	 * @var null
	 */
	public $searchable_fields = null;

	/**
	 * The default values of the echoed form fields.
	 *
	 * @var null
	 */
	public $default_values = null;

	/**
	 * The instance of the class.
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * The constructor.
	 *
	 * @param Daextletal_Shared $shared An instance of the shared class.
	 * @param string      $page_query_param The query parameter used to identify the current page.
	 * @param array       $config The configuration array.
	 */
	public function __construct( $shared, $page_query_param, $config ) {

		// assign an instance of the plugin info.
		$this->shared = $shared;

		$this->config = $config;

		add_action( 'admin_init', array( $this, 'handle_duplicate' ), 10 );
		add_action( 'admin_init', array( $this, 'handle_delete' ), 10 );
		add_action( 'admin_init', array( $this, 'handle_bulk_actions' ), 10 );

		// check if this instance has the method "process_form".
		if ( method_exists( $this, 'process_form' ) ) {
			add_action( 'admin_init', array( $this, 'process_form' ), 10 );
		}
	}

	/**
	 * Get the singleton instance of the class.
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
	 * Display the header bar.
	 *
	 * @return void
	 */
	public function header_bar() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Nonce not required for data visualization.
		$action  = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : 'list';
		$edit_id = isset( $_GET['edit_id'] ) ? absint( $_GET['edit_id'] ) : null;
		// phpcs:enable

		if ( 'new' === $action ) {
			$page_title = __( 'Add New', 'league-table-lite' ) . ' ' . $this->label_singular;
		} elseif ( null !== $edit_id ) {
			$page_title = __( 'Edit', 'league-table-lite' ) . ' ' . $this->label_singular;
		} else {
			$page_title = $this->label_plural;
		}

		?>

		<div class="daextletal-header-bar">

			<div class="daextletal-header-bar__left">
				<div class="daextletal-header-bar__page-title"><?php echo esc_html( $page_title ); ?></div>
				<?php if ( 'list' === $action && 'crud' === $this->context && null === $edit_id ) : ?>
					<a href="<?php echo esc_url( get_admin_url() . 'admin.php?page=daextletal-' . $this->slug_plural . '&edit_id=0' ); ?>"
						class="daextletal-button daextletal-header-bar__add-new-button">
						<?php $this->shared->echo_icon_svg( 'plus' ); ?>
						<div class="daextletal-header-bar__add-new-button-text"><?php esc_html_e( 'Add New', 'league-table-lite' ); ?></div>
					</a>
				<?php endif; ?>
			</div>

			<div class="daextletal-header-bar__right">
				<?php if ( 'new' === $action || null !== $edit_id ) : ?>
					<a href="#" id="save" class="daextletal-btn daextletal-btn-primary"><?php esc_html_e( 'Save Changes', 'league-table-lite' ); ?></a>
				<?php endif; ?>
			</div>

		</div>

		<?php
	}

	/**
	 * Display the header of a section of the menu. The header includes the section name and a toggle to open and close
	 * the caption.
	 *
	 * @param string $label The displayed name of the section.
	 * @param string $section_id The alphanumeric id of the section.
	 * @param string $icon_id The id of the icon to display.
	 *
	 * @return void
	 */
	public function section_header( $label, $section_id, $icon_id = null ) {

		?>

		<div class="daextletal-main-form__section-header group-trigger" data-trigger-target="<?php echo esc_attr( $section_id ); ?>">
			<div class="daextletal-main-form__section-header-title">
				<?php $this->shared->echo_icon_svg( $icon_id ); ?>
				<div class="daextletal-main-form__section-header-title-text"><?php echo esc_html( $label ); ?></div>
			</div>
			<div class="daextletal-main-form__section-header-toggle">
				<?php $this->shared->echo_icon_svg( 'chevron-down' ); ?>
			</div>
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
	 *
	 * @return void
	 */
	public function input_field(
		$name = '',
		$label = '',
		$description = '',
		$placeholder = '',
		$value = null,
		$maxlength = null,
		$required = false,
		$section_id = null
	) {

		?>

		<div class="daextletal-main-form__daext-form-field" valign="top" data-section-id="<?php echo esc_attr( $section_id ); ?>">
			<div><label for="title"><?php echo esc_html( $label ); ?><?php echo $required ? ' <span class="daextletal-required">*</span>' : ''; ?></label></div>
			<div>
				<input type="text" id="<?php echo esc_attr( $name ); ?>" maxlength="<?php echo esc_attr( $maxlength ); ?>" size="30"
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
	 * Generate an HTML select field.
	 *
	 * @param string $name The HTML element name.
	 * @param string $label The displayed name of the field.
	 * @param string $description The displayed description of the field.
	 * @param string $options The options of the field.
	 * @param string $value The value of the field.
	 * @param string $section_id The id of the section.
	 *
	 * @return void
	 */
	public function select_field(
		$name = '',
		$label = '',
		$description = '',
		$options = null,
		$value = null,
		$section_id = null
	) {

		?>

		<div class="daextletal-main-form__daext-form-field" valign="top" data-section-id="<?php echo esc_attr( $section_id ); ?>">
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
							echo 'selected';}
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
	 * Generate an HTML select multiple field.
	 *
	 * @param string $name The HTML element name.
	 * @param string $label The displayed name of the field.
	 * @param string $description The displayed description of the field.
	 * @param string $options The options of the field.
	 * @param string $value The value of the field.
	 * @param string $section_id The id of the section.
	 *
	 * @return void
	 */
	public function select_multiple_field(
		$name = '',
		$label = '',
		$description = '',
		$options = null,
		$value = null,
		$section_id = null
	) {

		$value_a = maybe_unserialize( $value );

		?>

		<div class="daextletal-main-form__daext-form-field" valign="top" data-section-id="<?php echo esc_attr( $section_id ); ?>">
			<div><label for="title"><?php echo esc_html( $label ); ?></label></div>
			<div>
				<select id="<?php echo esc_attr( $name ); ?>"
						name="<?php echo esc_attr( $name ); ?>[]" multiple>
					<?php
					foreach ( $options as $key => $option ) {

						/**
						 * Convert the key and value to integers if they are numeric. This prevents data types
						 * comparison issues in the next if statement that should use the identical operator.
						 */
						if ( is_array( $value_a ) ) {
							foreach ( $value_a as $value_a_value ) {
								if ( is_numeric( $value_a_value ) && is_numeric( $key ) ) {
									$value_a_value = intval( $value_a_value, 10 );
									$key           = intval( $key, 10 );
								}
								if ( $value_a_value === $key ) {
									$selected = 'selected';
									break;
								} else {
									$selected = '';
								}
							}
						} else {
							$selected = '';
						}

						echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected );
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
	 * Generate an HTML input field.
	 *
	 * @param string $name The HTML element name.
	 * @param string $label The displayed name of the field.
	 * @param string $description The displayed description of the field.
	 * @param string $value The value of the field.
	 * @param string $section_id The id of the section.
	 *
	 * @return void
	 */
	public function toggle_field(
		$name = '',
		$label = '',
		$description = '',
		$value = null,
		$section_id = null
	) {

		?>

		<div class="daextletal-main-form__daext-form-field" valign="top" data-section-id="<?php echo esc_attr( $section_id ); ?>">
			<div class="switch-container">
				<div class="switch-left">
					<label class="switch">
						<input id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" type="checkbox" <?php checked( intval( $value, 10 ), 1 ); ?>>
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
	 * @param string $min The minimum value of the range.
	 * @param string $max The maximum value of the range.
	 *
	 * @return void
	 */
	public function input_range_field(
		$name = '',
		$label = '',
		$description = '',
		$value = null,
		$section_id = null,
		$min = null,
		$max = null
	) {

		?>

		<div class="daextletal-main-form__daext-form-field" valign="top" data-section-id="<?php echo esc_attr( $section_id ); ?>">
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
	 * Display the admin toolbar. Which is the top section of the plugin admin menus.
	 *
	 * @return void
	 */
	public function display_admin_toolbar() {

		?>

		<div class="daextletal-admin-toolbar">
			<div class="daextletal-admin-toolbar__left-section">
				<div class="daextletal-admin-toolbar__menu-items">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=daextletal-tables' ) ); ?>" class="daextletal-admin-toolbar__plugin-logo">
						<?php // phpcs:ignore -- PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- The image is not an attachment image. ?>
						<img src="<?php echo esc_url( $this->shared->get( 'url' ) . 'admin/assets/img/plugin-logo.svg' ); ?>" alt="League Table" />
					</a>
					<?php

					foreach ( $this->config['admin_toolbar']['items'] as $key => $item ) {

						?>

						<a href="<?php echo esc_attr( $item['link_url'] ); ?>" class="daextletal-admin-toolbar__menu-item <?php echo 'daextletal-' . $this->menu_slug === $item['menu_slug'] ? 'is-active' : ''; ?>">
							<div class="daextletal-admin-toolbar__menu-item-wrapper">
								<?php $this->shared->echo_icon_svg( $item['icon'] ); ?>
								<div class="daextletal-admin-toolbar__menu-item-text"><?php echo esc_html( $item['link_text'] ); ?></div>
							</div>
						</a>

						<?php

					}

					?>

					<div class="daextletal-admin-toolbar__menu-item daextletal-admin-toolbar__menu-item-more">
						<div class="daextletal-admin-toolbar__menu-item-wrapper">
							<?php $this->shared->echo_icon_svg( 'grid-01' ); ?>
							<div class="daextletal-admin-toolbar__menu-item-text"><?php esc_html_e( 'More', 'league-table-lite' ); ?></div>
							<?php $this->shared->echo_icon_svg( 'chevron-down' ); ?>
						</div>
						<ul class="daextletal-admin-toolbar__pop-sub-menu">

							<?php

							foreach ( $this->config['admin_toolbar']['more_items'] as $key => $more_item ) {

								?>

								<li>
									<a href="<?php echo esc_attr( $more_item['link_url'] ); ?>" <?php echo 1 === intval( $more_item['pro_badge'], 10 ) ? 'target="_blank"' : ''; ?>>
										<?php echo '<div class="daextletal-admin-toolbar__more-item-item-text">' . esc_html( $more_item['link_text'] ) . '</div>'; ?>
										<?php

										if ( true === isset( $more_item['pro_badge'] ) && $more_item['pro_badge'] ) {
											echo '<div class="daextletal-admin-toolbar__pro-badge">' . esc_html__( 'PRO', 'league-table-lite' ) . '</div>';
										}

										?>
									</a>
								</li>

								<?php

							}

							?>

						</ul>
					</div>
				</div>
			</div>
			<div class="daextletal-admin-toolbar__right-section">
				<!-- Display the upgrade button in the Free version. -->
				<?php if ( constant( 'DAEXTLETAL_EDITION' ) === 'FREE' ) : ?>
				<a href="https://daext.com/league-table/" target="_blank" class="daextletal-admin-toolbar__upgrade-button">
					<?php $this->shared->echo_icon_svg( 'diamond-01' ); ?>
					<div class="daextletal-admin-toolbar__upgrade-button-text"><?php esc_html_e( 'Unlock Extra Features with LT Pro', 'league-table-lite' ); ?></div>
				</a>
				<?php endif; ?>
				<a href="https://daext.com" target="_blank" class="daextletal-admin-toolbar__daext-logo-container">
				<?php // phpcs:ignore -- PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- The image is not an attachment image. ?>
				<img class="daextletal-admin-toolbar__daext-logo" src="<?php echo esc_url( $this->shared->get( 'url' ) . 'admin/assets/img/daext-logo.svg' ); ?>" alt="DAEXT" />
				</a>
			</div>
		</div>

		<?php
	}

	/**
	 * Display a section with that includes information on the Pro version. Note that the Pro Features section is
	 * displayed only in the free version.
	 *
	 * @return void
	 */
	public function display_pro_features() {

		if ( constant( 'DAEXTLETAL_EDITION' ) !== 'FREE' ) {
			return;
		}

		?>

		<div class="daextletal-admin-body">

			<div class="daextletal-pro-features">

				<div class="daextletal-pro-features__wrapper">

					<div class="daextletal-pro-features__left">
						<div class="daextletal-pro-features__title">
							<div class="daextletal-pro-features__title-text"><?php esc_html_e( 'Unlock Advanced Features with League Table Pro', 'league-table-lite' ); ?></div>
							<div class="daextletal-pro-features__pro-badge"><?php esc_html_e( 'PRO', 'league-table-lite' ); ?></div>
						</div>
						<div class="daextletal-pro-features__description">
							<?php
							esc_html_e(
								'Sort tables using up to five criteria, merge table cells, apply formulas, customize cell styles and alignments, highlight ranking positions, add custom HTML, display table captions, create table backups, and more!',
								'league-table-lite'
							);
							?>
						</div>
						<div class="daextletal-pro-features__buttons-container">
							<a class="daextletal-pro-features__button-1" href="https://daext.com/league-table/" target="_blank">
								<div class="daextletal-pro-features__button-text">
									<?php esc_html_e( 'Learn More', 'league-table-lite' ); ?>
								</div>
								<?php $this->shared->echo_icon_svg( 'arrow-up-right' ); ?>
							</a>
							<a class="daextletal-pro-features__button-2" href="https://daext.com/league-table/#pricing" target="_blank">
								<div class="daextletal-pro-features__button-text">
									<?php esc_html_e( 'View Pricing & Upgrade', 'league-table-lite' ); ?>
								</div>
								<?php
								$this->shared->echo_icon_svg( 'arrow-up-right' );
								?>
							</a>
						</div>
					</div>
					<div class="daextletal-pro-features__right">

						<?php

						$pro_features_data_a = array(
							array(
								'icon'        => 'layout-grid-02',
								'name_part_1' => 'Spreadsheet',
								'name_part_2' => 'Editor',
							),
							array(
								'icon'        => 'columns-01',
								'name_part_1' => 'Sortable',
								'name_part_2' => 'Columns',
							),
							array(
								'icon'        => 'palette',
								'name_part_1' => 'Customizable',
								'name_part_2' => 'Style',
							),
							array(
								'icon'        => 'phone-01',
								'name_part_1' => 'Responsive',
								'name_part_2' => 'Design',
							),
							array(
								'icon'        => 'calculator',
								'name_part_1' => 'Cell',
								'name_part_2' => 'Properties',
							),
							array(
								'icon'        => 'share-05',
								'name_part_1' => 'Exportable',
								'name_part_2' => 'Data',
							),
						);

						foreach ( $pro_features_data_a as $key => $pro_feature_data ) {

							?>

							<div class="daextletal-pro-features__single-feature">
								<div class="daextletal-pro-features__single-feature-wrapper">
									<?php $this->shared->echo_icon_svg( $pro_feature_data['icon'] ); ?>
									<div class="daextletal-pro-features__single-feature-name">
										<?php echo esc_html( $pro_feature_data['name_part_1'] ); ?>
										<br>
										<?php echo esc_html( $pro_feature_data['name_part_2'] ); ?>
									</div>
								</div>
							</div>

							<?php

						}

						?>

					</div>

				</div>

				<div class="daextletal-pro-features__footer-wrapper">
					<div class="daextletal-pro-features__footer-wrapper-inner">
						<div class="daextletal-pro-features__footer-wrapper-left">
							<?php esc_html_e( 'Built for WordPress creators by the DAEXT team', 'league-table-lite' ); ?>
						</div>
						<a class="daextletal-pro-features__footer-wrapper-right" href="https://daext.com/products/" target="_blank">
							<div class="daextletal-pro-features__footer-wrapper-right-text">
								<?php esc_html_e( 'More Tools from DAEXT', 'league-table-lite' ); ?>
							</div>
							<?php $this->shared->echo_icon_svg( 'arrow-up-right' ); ?>
						</a>
					</div>
				</div>

			</div>

		</div>

		<?php
	}

	/**
	 * Handle the duplication of an item.
	 *
	 * In details, when the $_POST['clone_id'] is set, the method will duplicate the corresponding item in the
	 * database.
	 *
	 * @return void
	 */
	public function handle_duplicate() {

		$data             = array();
		$data['clone_id'] = isset( $_POST['clone_id'] ) ? intval( $_POST['clone_id'], 10 ) : null;

		// clone an item.
		if ( ! is_null( $data['clone_id'] ) ) {

			// Nonce verification.
			check_admin_referer( 'daextletal_clone_' . $this->menu_slug . '_' . $data['clone_id'], 'daextletal_clone_' . $this->menu_slug . '_nonce' );

			global $wpdb;

			// clone the table.
			$table_name   = $wpdb->prefix . $this->shared->get( 'slug' ) . '_table';
			$result       = $this->shared->duplicate_record( $table_name, 'id', $data['clone_id'] );
			$table_id_new = $result['last_inserted_id'];

			// Clone the rows.
			$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . '_data';
			$safe_sql   = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}daextletal_data WHERE table_id = %d", $data['clone_id'] );
			$rows       = $wpdb->get_results( $safe_sql, ARRAY_A ); // phpcs:ignore
			foreach ( $rows as $row ) {

				// Retrieve the record to duplicate.
				$safe_sql = $wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}daextletal_data WHERE id = %d",
					$row['id']
				);
				$record   = $wpdb->get_row( $safe_sql, ARRAY_A ); // phpcs:ignore

				// Remove the primary key from the record.
				unset( $record['id'] );

				// Update the table_id.
				$record['table_id'] = $table_id_new;

				// Insert the record into the database.
				$query_result = $wpdb->insert( $table_name, $record ); // phpcs:ignore

			}

			// Clone the cells.
			$table_name = $wpdb->prefix . $this->shared->get( 'slug' ) . '_cell';
			$safe_sql   = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}daextletal_cell WHERE table_id = %d", $data['clone_id'] );
			$cells      = $wpdb->get_results( $safe_sql, ARRAY_A ); // phpcs:ignore
			foreach ( $cells as $cell ) {

				// Retrieve the record to duplicate.
				$safe_sql = $wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}daextletal_cell WHERE id = %d",
					$cell['id']
				);
				$record   = $wpdb->get_row( $safe_sql, ARRAY_A ); // phpcs:ignore

				// Remove the primary key from the record.
				unset( $record['id'] );

				// Update the table_id.
				$record['table_id'] = $table_id_new;

				// Insert the record into the database.
				$query_result = $wpdb->insert( $table_name, $record ); // phpcs:ignore

			}

			$this->shared->save_dismissible_notice(
				__( 'The item has been successfully duplicated.', 'league-table-lite' ),
				'updated'
			);

		}
	}

	/**
	 * Handle the deletion of an item.
	 *
	 * In details, when the $_POST['delete_id'] is set, the method will delete the corresponding item from the
	 * database.
	 *
	 * @return void
	 */
	public function handle_delete() {

		$data              = array();
		$data['delete_id'] = isset( $_POST['delete_id'] ) ? intval( $_POST['delete_id'], 10 ) : null;

		// delete an item.
		if ( ! is_null( $data['delete_id'] ) ) {

			// Nonce verification.
			check_admin_referer( 'daextletal_delete_' . $this->menu_slug . '_' . $data['delete_id'], 'daextletal_delete_' . $this->menu_slug . '_nonce' );

			// Check deletion conditions.
			$result = $this->item_is_deletable( $data['delete_id'] );

			// prevent deletion if the item is not deletable.
			if ( ! $result['is_deletable'] ) {

				$this->shared->save_dismissible_notice(
					$result['dismissible_notice_message'],
					'error'
				);

			} else {

				global $wpdb;

				// Sanitize the db table name.
				$db_table_name = $wpdb->prefix . 'daextletal_' . $this->db_table;

				// Sanitize the field name.
				$primary_key = sanitize_key( $this->primary_key );

				// Delete this table.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$query_result_1 = $wpdb->query(
					$wpdb->prepare( "DELETE FROM {$wpdb->prefix}daextletal_table WHERE id = %d ", $data['delete_id'] )
				);

				// Delete all the rows of this table.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$query_result_2 = $wpdb->query(
					$wpdb->prepare( "DELETE FROM {$wpdb->prefix}daextletal_data WHERE table_id = %d ", $data['delete_id'] )
				);

				// Delete all the cells of this table.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$query_result_3 = $wpdb->query(
					$wpdb->prepare( "DELETE FROM {$wpdb->prefix}daextletal_cell WHERE table_id = %d ", $data['delete_id'] )
				);

				if ( false !== $query_result_1 && false !== $query_result_2 && false !== $query_result_3 ) {
					$this->shared->save_dismissible_notice(
						__( 'The item has been successfully deleted.', 'league-table-lite' ),
						'updated'
					);
				}
			}
		}
	}

	/**
	 * Handles the processing of bulk actions.
	 *
	 * @return void
	 */
	public function handle_bulk_actions() {

		$bulk_action = isset( $_POST['bulk_action'] ) ? sanitize_text_field( wp_unslash( $_POST['bulk_action'] ) ) : null;

		if ( 'delete' === $bulk_action ) {

			$delete_id = ( isset( $_POST['bulk-action-selected-items'] ) && ! empty( $_POST['bulk-action-selected-items'] ) ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['bulk-action-selected-items'] ) ) ) : null;

			if ( ! is_array( $delete_id ) ) {
				return;
			}

			// Convert all the $delete_id values to numeric values with base 10 using intval.
			$delete_id = array_map(
				function ( $value ) {
					return intval( $value, 10 );
				},
				$delete_id
			);

			if ( ! is_null( $delete_id ) ) {

				// Nonce verification.
				check_admin_referer( 'daextletal_bulk_action_' . $this->menu_slug, 'daextletal_bulk_action_' . $this->menu_slug . '_nonce' );

				$delete_id_deletable     = array();
				$delete_id_non_deletable = array();

				// Create two new array that includes deletable and non-deletable items.
				foreach ( $delete_id as $key => $value ) {

					$result = $this->item_is_deletable( $value );

					if ( $result['is_deletable'] ) {
						$delete_id_deletable[] = $value;
					} else {
						$delete_id_non_deletable[] = $value;
					}
				}

				// Delete the items.
				global $wpdb;

				if ( count( $delete_id_deletable ) > 0 ) {

					$deleted_items_count = 0;

					foreach ( $delete_id_deletable as $delete_id ) {
						// Delete the item in the "_table" menu where the id field matches the current $delete_id.
						$query_result_table = $wpdb->query(
							$wpdb->prepare( "DELETE FROM {$wpdb->prefix}daextletal_table WHERE id = %d", $delete_id )
						);

						if ( isset( $query_result_table ) && false !== $query_result_table ) {

							// Get the number of deleted items with $wpdb.
							$deleted_items_count = $deleted_items_count + $wpdb->rows_affected;

						}

						// Delete the items in the "_data" db table where the table_id matches the current $delete_id.
						$wpdb->query(
							$wpdb->prepare( "DELETE FROM {$wpdb->prefix}daextletal_data WHERE table_id = %d", $delete_id )
						);

						// Delete the items in the "_cell" db table where the table_id matches the current $delete_id.
						$wpdb->query(
							$wpdb->prepare( "DELETE FROM {$wpdb->prefix}daextletal_cell WHERE table_id = %d", $delete_id )
						);
					}

				}

				$this->shared->save_dismissible_notice(
					$deleted_items_count . ' ' . __( 'items have been successfully deleted.', 'league-table-lite' ),
					'updated'
				);

			}
		}
	}

	/**
	 * Verify the provided user capability.
	 *
	 * Die with a message if the user does not have the required capability.
	 *
	 * @return void
	 */
	public function verify_user_capability() {

		if ( ! current_user_can( $this->capability ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'league-table-lite' ) );
		}
	}

	/**
	 * Displays the content of the admin menu.
	 *
	 * @return void
	 */
	public function display_menu_content() {

		// Verify user capability.
		$this->verify_user_capability();

		// Display the Admin Toolbar.
		$this->display_admin_toolbar();

		// Display the Header Bar.
		$this->header_bar();

		// Display the custom body content defined in the menu child class.
		$this->display_custom_content();

		// Display the Pro features section.
		$this->display_pro_features();
	}

}
