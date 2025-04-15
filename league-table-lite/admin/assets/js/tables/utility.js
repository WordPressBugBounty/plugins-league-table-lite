/**
 * This files includes utility methods.
 *
 * @package league-table-lite
 */

(function ($) {

	'use strict';

	// Global Object.
	window.DAEXTLETAL = {};

	/**
	 * Utility Methods
	 */
	window.DAEXTLETAL.utility = {

		/**
		 * Get the table id from the hidden fields available in the page.
		 */
		get_table_id: function () {

			'use strict';

			let table_id = null;

			if ($( '#temporary-table-id' ).length) {
				table_id = $( '#temporary-table-id' ).val();
			} else {
				table_id = $( '#update-id' ).val();
			}

			return parseInt( table_id, 10 );

		},

		/*
		Initialize the handsontable table
		*/
		initialize_handsontable: function () {

			'use strict';

			let order_by         = null;
			let daextletal_max_rows    = null;
			let daextletal_max_columns = null;
			let daextletal_data        = [];

			/**
			 * If the form is in edit mode retrieve the data of the table based on the table id, otherwise initialize an
			 * empty table.
			 */
			if (parseInt( $( '#update-id' ).val() ) > 0) {

				// Prepare ajax request.
				const data = {
					'action': 'daextletal_retrieve_table_data',
					'security': DAEXTLETAL_PARAMETERS.nonce,
					'table_id': $( '#update-id' ).val(),
				};

				// Set ajax in synchronous mode.
				jQuery.ajaxSetup( {async: false} );

				// Send ajax request.
				$.post(
					DAEXTLETAL_PARAMETERS.ajax_url,
					data,
					function (response_json) {

						'use strict';

						// Initialize the table with the retrieved data.
						const response_obj = JSON.parse( response_json );

						const data_content_obj = response_obj['data_content'];

						order_by = response_obj['order_by'];

						$.each(
							data_content_obj,
							function (index, value) {

								'use strict';

								daextletal_data.push( value );

							}
						);

						daextletal_max_rows    = parseInt( $( '#rows' ).val() ) + 1;
						daextletal_max_columns = parseInt( $( '#columns' ).val() );

					}
				);

				// Set ajax in asynchronous mode.
				jQuery.ajaxSetup( {async: true} );

			} else {

				// Initialize an empty table.
				daextletal_data = [
				[
				'Label 1',
				'Label 2',
				'Label 3',
				'Label 4',
				'Label 5',
				'Label 6',
				'Label 7',
				'Label 8',
				'Label 9',
				'Label 10'],
				[0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				[0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				];

				daextletal_max_rows    = 11;
				daextletal_max_columns = 10;

			}

			// Instantiate the handsontable table.
			const daextletal_container = document.getElementById( 'daextletal-table' );

			// If the daextletal_container DOM doesn't exist, return.
			if (daextletal_container === null) {
				return;
			}

			window.DAEXTLETAL.states.daextletal_hot = new Handsontable(
				daextletal_container,
				{

					afterSelection: function (row_index, column_index) {

						'use strict';

						window.DAEXTLETAL.utility.retrieve_cell_properties( row_index, column_index );

					},

					data: daextletal_data,

					// Set the new maximum number of rows and columns.
					maxRows: daextletal_max_rows,
					maxCols: daextletal_max_columns,

					width: 866,  // Set the desired width.
					stretchH: 'all', // This ensures that columns stretch to fill the table width.

					contextMenu: {
						items: {

							'insert_row_above': {
								name: objectL10n.insert_row_above,
								disabled: function () {

									'use strict';

									if (

									// The first row is selected.
									window.DAEXTLETAL.states.daextletal_hot.getSelected()[0][0] === 0 ||

									// The maximum number of rows has been reached.
									window.DAEXTLETAL.states.daextletal_hot.countRows() > 10000

									) {
										return true;
									}

								},
								callback: function (key, options) {

									'use strict';

									window.DAEXTLETAL.contextMenu.insert_row_above( options[0].start.row );

								},
							},

							'insert_row_below': {
								disabled: function () {

									'use strict';

									if (

									// The maximum number of rows has been reached.
									window.DAEXTLETAL.states.daextletal_hot.countRows() > 10000

									) {
										return true;
									}
								},
								name: objectL10n.insert_row_below,
								callback: function (key, options) {

									'use strict';

									window.DAEXTLETAL.contextMenu.insert_row_below( options[0].start.row );

								},
							},

							'sep_1': {name: '---------'},

							'insert_column_left': {
								disabled: function () {

									'use strict';

									if (

									// The maximum number of rows has been reached.
									window.DAEXTLETAL.states.daextletal_hot.countCols() >= 40

									) {
										return true;
									}

								},
								name: objectL10n.insert_column_left,
								callback: function (key, options) {

									'use strict';

									window.DAEXTLETAL.contextMenu.insert_column_left( options[0].start.col );

								},
							},

							'insert_column_right': {
								disabled: function () {

									'use strict';

									if (

									// The maximum number of rows has been reached.
									window.DAEXTLETAL.states.daextletal_hot.countCols() >= 40

									) {
										return true;
									}

								},
								name: objectL10n.insert_column_right,
								callback: function (key, options) {

									'use strict';

									window.DAEXTLETAL.contextMenu.insert_column_right( options[0].start.col );

								},
							},

							'sep_2': {name: '---------'},

							'remove_row': {
								name: objectL10n.remove_row,
								disabled: function () {

									'use strict';

									if (window.DAEXTLETAL.states.daextletal_hot.getSelected()[0][0] === 0 ||
									window.DAEXTLETAL.states.daextletal_hot.countRows() === 2) {
										return true;
									}

								},
								callback: function (key, options) {

									'use strict';

									window.DAEXTLETAL.contextMenu.remove_row( options[0].start.row );

								},
							},

							'remove_column': {
								name: objectL10n.remove_column,
								disabled: function () {

									'use strict';

									if (window.DAEXTLETAL.states.daextletal_hot.countCols() === 1) {
										return true;
									}

								},
								callback: function (key, options) {

									'use strict';

									window.DAEXTLETAL.contextMenu.remove_column( options[0].start.col );

								},
							},

							'sep_3': {name: '---------'},

							'copy': {
								name: objectL10n.copy_data,
							},

							'cut': {
								name: objectL10n.cut_data,
							},

							'paste': {
								name: objectL10n.paste_data,
								callback: function (key, options) {

									'use strict';

									$( '.dialog-alert[data-id="dialog-keyboard-shortcut"]' ).dialog( 'open' );

								},
							},

							'sep_4': {name: '---------'},

							'reset_data': {
								name: objectL10n.reset_data,
								callback: function (key, options) {
									if (window.DAEXTLETAL.utility.valid_cell_number( options[0] )) {
										window.DAEXTLETAL.contextMenu.reset_data( options[0] );
									}
								},
							},

							'reset_cell_properties': {
								name: objectL10n.reset_cell_properties,
								callback: function (key, options) {

									'use strict';

									if (window.DAEXTLETAL.utility.valid_cell_number( options[0] )) {
										window.DAEXTLETAL.contextMenu.reset_cell_properties( options[0] );
										window.DAEXTLETAL.utility.retrieve_cell_properties( options[0].start.row, options[0].start.col );
									}

								},
							},

							'reset_data_and_cell_properties': {
								name: objectL10n.reset_data_and_cell_properties,
								callback: function (key, options) {

									'use strict';

									if (window.DAEXTLETAL.utility.valid_cell_number( options[0] )) {
										window.DAEXTLETAL.contextMenu.reset_data( options[0] );
										window.DAEXTLETAL.contextMenu.reset_cell_properties( options[0] );
										window.DAEXTLETAL.utility.retrieve_cell_properties( options[0].start.row, options[0].start.col );
									}

								},
							},

						},
					},

				}
			);

			/*
			* Add the select element in the #order-by-* field from 1 to 5 based on the current number of columns.
			* Use the data that come from the ajax request to select the proper items
			*/
			const number_of_columns = parseInt( $( '#columns' ).val() );

			// Generate the select element options.
			let option_elements = '';
			for (let t = 1; t <= number_of_columns; t++) {
				option_elements += '<option value="' + t + '">' + objectL10n.column + ' ' + t + '</option>';
			}

			// add the select element options inside the select boxes.
			$( '#order-by' ).append( option_elements );

			if (order_by !== null) {

				// If we are in edit mode select the proper element.
				$('#order-by').val(order_by);

			}else{

				// If we are in create new table mode select the first element.
				$('#order-by').val(1);

			}

		},

		/*
		* Save the table. Used by the #save -> click event listener
		*/
		save_table: function (reload_menu) {

			'use strict';

			// Get form data.
			const temporary_table_id = $( '#temporary-table-id' ).length ? $( '#temporary-table-id' ).val() : null;
			const name               = $( '#name' ).val();
			const description        = $( '#description' ).val();
			const type               = $( '#type' ).val();
			const rows               = $( '#rows' ).val();
			const columns            = $( '#columns' ).val();

			// Sorting.
			const enable_sorting        = $( '#enable-sorting' ).prop( 'checked' ) ? 1 : 0;
			const enable_manual_sorting = $( '#enable-manual-sorting' ).prop( 'checked' ) ? 1 : 0;
			const show_position         = $( '#show-position' ).prop( 'checked' ) ? 1 : 0;
			const position_side         = $( '#position-side' ).val();
			const position_label        = $( '#position-label' ).val();
			const number_format         = $( '#number-format' ).val();
			const order_desc_asc      = $( '#order-desc-asc' ).val();
			const order_by            = $( '#order-by' ).val();
			const order_data_type     = $( '#order-data-type' ).val();
			const order_date_format   = $( '#order-date-format' ).val();

			// Style.
			const table_layout               = $( '#table-layout' ).val();
			const table_width                = $( '#table-width' ).val();
			const table_width_value          = $( '#table-width-value' ).val();
			const table_minimum_width        = $( '#table-minimum-width' ).val();
			const column_width               = $( '#column-width' ).val();
			const column_width_value         = $( '#column-width-value' ).val();
			const table_margin_top           = $( '#table-margin-top' ).val();
			const table_margin_bottom        = $( '#table-margin-bottom' ).val();
			const enable_container           = $( '#enable-container' ).prop( 'checked' ) ? 1 : 0;
			const container_width            = $( '#container-width' ).val();
			const container_height           = $( '#container-height' ).val();
			const show_header                = $( '#show-header' ).prop( 'checked' ) ? 1 : 0;
			const header_font_size           = $( '#header-font-size' ).val();
			const header_font_family         = $( '#header-font-family' ).val();
			const header_font_weight         = $( '#header-font-weight' ).val();
			const header_font_style          = $( '#header-font-style' ).val();
			const header_background_color    = $( '#header-background-color' ).val();
			const header_font_color          = $( '#header-font-color' ).val();
			const header_link_color          = $( '#header-link-color' ).val();
			const header_border_color        = $( '#header-border-color' ).val();
			const header_position_alignment  = $( '#header-position-alignment' ).val();
			const body_font_size             = $( '#body-font-size' ).val();
			const body_font_family           = $( '#body-font-family' ).val();
			const body_font_weight           = $( '#body-font-weight' ).val();
			const body_font_style            = $( '#body-font-style' ).val();
			const even_rows_background_color = $( '#even-rows-background-color' ).val();
			const odd_rows_background_color  = $( '#odd-rows-background-color' ).val();
			const even_rows_font_color       = $( '#even-rows-font-color' ).val();
			const even_rows_link_color       = $( '#even-rows-link-color' ).val();
			const odd_rows_font_color        = $( '#odd-rows-font-color' ).val();
			const odd_rows_link_color        = $( '#odd-rows-link-color' ).val();
			const rows_border_color          = $( '#rows-border-color' ).val();

			// Autoalignment.
			const autoalignment_priority                = $( '#autoalignment-priority' ).val();
			const autoalignment_affected_rows_left      = $( '#autoalignment-affected-rows-left' ).val();
			const autoalignment_affected_rows_center    = $( '#autoalignment-affected-rows-center' ).val();
			const autoalignment_affected_rows_right     = $( '#autoalignment-affected-rows-right' ).val();
			const autoalignment_affected_columns_left   = $( '#autoalignment-affected-columns-left' ).val();
			const autoalignment_affected_columns_center = $( '#autoalignment-affected-columns-center' ).val();
			const autoalignment_affected_columns_right  = $( '#autoalignment-affected-columns-right' ).val();

			// Responsive.
			const tablet_breakpoint        = $( '#tablet-breakpoint' ).val();
			const hide_tablet_list         = $( '#hide-tablet-list' ).val();
			const tablet_header_font_size  = $( '#tablet-header-font-size' ).val();
			const tablet_body_font_size    = $( '#tablet-body-font-size' ).val();
			const tablet_hide_images       = $( '#tablet-hide-images' ).prop( 'checked' ) ? 1 : 0;
			const phone_breakpoint         = $( '#phone-breakpoint' ).val();
			const hide_phone_list          = $( '#hide-phone-list' ).val();
			const phone_header_font_size   = $( '#phone-header-font-size' ).val();
			const phone_body_font_size     = $( '#phone-body-font-size' ).val();
			const phone_hide_images        = $( '#phone-hide-images' ).prop( 'checked' ) ? 1 : 0;

			// Advanced.
			const enable_cell_properties   = $( '#enable-cell-properties' ).prop( 'checked' ) ? 1 : 0;

			// Save the table data available as a JavaScript value in a JSON string.
			const table_data = JSON.stringify( {data: window.DAEXTLETAL.states.daextletal_hot.getData()} );

			// Prepare ajax request.
			const data = {

				'action': 'daextletal_save_data',
				'security': DAEXTLETAL_PARAMETERS.nonce,
				'table_id': window.DAEXTLETAL.utility.get_table_id(),

				// General.
				'temporary_table_id': temporary_table_id,
				'name': name,
				'description': description,
				'rows': rows,
				'columns': columns,
				'table_data': table_data,

				// Sorting.
				'enable_sorting': enable_sorting,
				'enable_manual_sorting': enable_manual_sorting,
				'show_position': show_position,
				'position_side': position_side,
				'position_label': position_label,
				'number_format': number_format,
				'order_desc_asc': order_desc_asc,
				'order_by': order_by,
				'order_data_type': order_data_type,
				'order_date_format': order_date_format,

				// Style.
				'table_layout': table_layout,
				'table_width': table_width,
				'table_width_value': table_width_value,
				'table_minimum_width': table_minimum_width,
				'column_width': column_width,
				'column_width_value': column_width_value,
				'table_margin_top': table_margin_top,
				'table_margin_bottom': table_margin_bottom,
				'enable_container': enable_container,
				'container_width': container_width,
				'container_height': container_height,
				'show_header': show_header,
				'header_font_size': header_font_size,
				'header_font_family': header_font_family,
				'header_font_weight': header_font_weight,
				'header_font_style': header_font_style,
				'header_background_color': header_background_color,
				'header_font_color': header_font_color,
				'header_link_color': header_link_color,
				'header_border_color': header_border_color,
				'header_position_alignment': header_position_alignment,
				'body_font_size': body_font_size,
				'body_font_family': body_font_family,
				'body_font_weight': body_font_weight,
				'body_font_style': body_font_style,
				'even_rows_background_color': even_rows_background_color,
				'odd_rows_background_color': odd_rows_background_color,
				'even_rows_font_color': even_rows_font_color,
				'even_rows_link_color': even_rows_link_color,
				'odd_rows_font_color': odd_rows_font_color,
				'odd_rows_link_color': odd_rows_link_color,
				'rows_border_color': rows_border_color,

				// Autoalignment.
				'autoalignment_priority': autoalignment_priority,
				'autoalignment_affected_rows_left': autoalignment_affected_rows_left,
				'autoalignment_affected_rows_center': autoalignment_affected_rows_center,
				'autoalignment_affected_rows_right': autoalignment_affected_rows_right,
				'autoalignment_affected_columns_left': autoalignment_affected_columns_left,
				'autoalignment_affected_columns_center': autoalignment_affected_columns_center,
				'autoalignment_affected_columns_right': autoalignment_affected_columns_right,

				// Responsive.
				'tablet_breakpoint': tablet_breakpoint,
				'hide_tablet_list': hide_tablet_list,
				'tablet_header_font_size': tablet_header_font_size,
				'tablet_body_font_size': tablet_body_font_size,
				'tablet_hide_images': tablet_hide_images,
				'phone_breakpoint': phone_breakpoint,
				'hide_phone_list': hide_phone_list,
				'phone_header_font_size': phone_header_font_size,
				'phone_body_font_size': phone_body_font_size,
				'phone_hide_images': phone_hide_images,

				// Advanced.
				'enable_cell_properties': enable_cell_properties,
			};

			const validation_result = window.DAEXTLETAL.utility.table_is_valid( data );
			if (validation_result === true) {

				// Set ajax in synchronous mode.
				jQuery.ajaxSetup( {async: false} );

				// Send ajax request.
				$.post(
					DAEXTLETAL_PARAMETERS.ajax_url,
					data,
					function (data) {

						'use strict';

						if (reload_menu === true) {

							// Reload the dashboard menu.
							window.location.replace( DAEXTLETAL_PARAMETERS.admin_url + 'admin.php?page=daextletal-tables' );

						}

					}
				);

				// Set ajax in asynchronous mode.
				jQuery.ajaxSetup( {async: true} );

				return true;

			} else {

				return validation_result;

			}

		},

		/**
		 * The reason why this function is useful is that using specific keyword shortcut while editing the Handsontable
		 * might be confusing or generates problems.
		 *
		 * The following keyboard shortcuts are disabled:
		 *
		 * - CTRL+M (merge/unmerge selected cells)
		 * - CTRL+Enter (fill all selected cells with edited cell's value)
		 * - CTRL+Z (undo)
		 * - CTRL+Y (undo)
		 *
		 * The following keyboard shortcuts are enabled:
		 *
		 * - CTRL+C (copy cell's content)
		 * - CTRL+V (paste cell's content)
		 * - CTRL+X (cut cell's content)
		 *
		 * Keyboard shortcuts in Handsontable: https://handsontable.com/docs/7.0.2/tutorial-keyboard-navigation.html
		 */
		disable_specific_keyboard_shortcuts: function () {

			'use strict';

			$( '#daextletal-table-td' ).on(
				'keydown',
				function (e) {

					'use strict';

					if ( ((e.ctrlKey || e.metaKey) && e.keyCode === 77) || // CTRL+M.
					((e.ctrlKey || e.metaKey) && e.keyCode === 13) || // CTRL+Enter.
					((e.ctrlKey || e.metaKey) && e.keyCode === 90) || // CTRL+Z.
					((e.ctrlKey || e.metaKey) && e.keyCode === 89) // CTRL+Y.
					) {
						e.preventDefault();
						$( '.dialog-alert[data-id="specific-shortcut-disabled"]' ).dialog( 'open' );
						return false;
					}

				}
			);

		},

		/*
		Verifies if the data of the table are valid
		*/
		table_is_valid: function (data) {

			'use strict';

			// Init variables.
			let fields_with_errors_a = [];

			// Define regex patterns ----------------------------------------------------------------------------------.
			const digits_regex                    = /^\s*\d+\s*$/;
			const font_family_regex               = /^([A-Za-z0-9-\'", ]*)$/;
			const list_of_comma_separated_numbers = /^(\s*(\d+\s*,\s*)+\d+\s*|\s*\d+\s*)$/;
			const hex_rgb_regex                   = /^#(?:[0-9a-fA-F]{3}){1,2}$/;

			// Validate data ------------------------------------------------------------------------------------------.

			// Basic Info.
			if (data.name.trim().length < 1 || data.name.trim().length > 255) {
				fields_with_errors_a.push( objectL10n.name );}
			if (data.description.trim().length > 255) {
				fields_with_errors_a.push( objectL10n.description );
			}
			if ( ! data.rows.match( digits_regex ) || parseInt( data.rows, 10 ) < 1 || parseInt( data.rows, 10 ) >
			10000) {
				fields_with_errors_a.push( objectL10n.rows );}
			if ( ! data.columns.match( digits_regex ) || parseInt( data.columns, 10 ) < 1 || parseInt( data.columns, 10 ) >
			40) {
				fields_with_errors_a.push( objectL10n.columns );}

			// Sorting Options.
			if (data.position_label.trim().length < 1 || data.position_label.trim().length > 255) {
				fields_with_errors_a.push( objectL10n.position_label );
			}

			// Style Options.
			if ( ! data.table_width_value.match( digits_regex ) || parseInt( data.table_width_value, 10 ) < 1 ||
			parseInt( data.table_width_value, 10 ) > 999999) {
				fields_with_errors_a.push( objectL10n.table_width_value );}
			if ( ! data.table_minimum_width.match( digits_regex ) || parseInt( data.table_minimum_width, 10 ) < 0 ||
			parseInt( data.table_minimum_width, 10 ) > 999999) {
				fields_with_errors_a.push( objectL10n.table_minimum_width );}
			if (( ! data.column_width_value.match( list_of_comma_separated_numbers ) && data.column_width_value.trim().length >
			0) || data.column_width_value.trim().length > 2000) {
				fields_with_errors_a.push( objectL10n.column_width_value );
			}
			if ( ! data.container_width.match( digits_regex ) || parseInt( data.container_width, 10 ) < 0 ||
			parseInt( data.container_width, 10 ) > 999999) {
				fields_with_errors_a.push( objectL10n.container_width );}
			if ( ! data.container_height.match( digits_regex ) || parseInt( data.container_height, 10 ) < 0 ||
			parseInt( data.container_height, 10 ) > 999999) {
				fields_with_errors_a.push( objectL10n.container_height );}
			if ( ! data.table_margin_top.match( digits_regex ) || parseInt( data.table_margin_top, 10 ) < 0 ||
			parseInt( data.table_margin_top, 10 ) > 999999) {
				fields_with_errors_a.push( objectL10n.table_margin_top );}
			if ( ! data.table_margin_bottom.match( digits_regex ) || parseInt( data.table_margin_bottom, 10 ) < 0 ||
			parseInt( data.table_margin_bottom, 10 ) > 999999) {
				fields_with_errors_a.push( objectL10n.table_margin_bottom );}
			if ( ! data.header_font_size.match( digits_regex ) || parseInt( data.header_font_size, 10 ) < 0 ||
			parseInt( data.header_font_size, 10 ) > 999999) {
				fields_with_errors_a.push( objectL10n.header_font_size );}
			if ( ! data.header_font_family.match( font_family_regex ) || data.header_font_family.trim().length < 1 ||
			data.header_font_family.trim().length > 255) {
				fields_with_errors_a.push( objectL10n.header_font_family );}
			if ( ! data.header_background_color.match( hex_rgb_regex )) {
				fields_with_errors_a.push( objectL10n.header_background_color );
			}
			if ( ! data.header_font_color.match( hex_rgb_regex )) {
				fields_with_errors_a.push( objectL10n.header_font_color );}
			if ( ! data.header_link_color.match( hex_rgb_regex )) {
				fields_with_errors_a.push( objectL10n.header_link_color );}
			if ( ! data.header_border_color.match( hex_rgb_regex )) {
				fields_with_errors_a.push( objectL10n.header_border_color );}
			if ( ! data.body_font_size.match( digits_regex ) || parseInt( data.body_font_size, 10 ) < 0 ||
			parseInt( data.body_font_size, 10 ) > 999999) {
				fields_with_errors_a.push( objectL10n.body_font_size );}
			if ( ! data.body_font_family.match( font_family_regex )) {
				fields_with_errors_a.push( objectL10n.body_font_family );}
			if ( ! data.even_rows_background_color.match( hex_rgb_regex )) {
				fields_with_errors_a.push( objectL10n.even_rows_background_color );
			}
			if ( ! data.odd_rows_background_color.match( hex_rgb_regex )) {
				fields_with_errors_a.push( objectL10n.odd_rows_background_color );
			}
			if ( ! data.even_rows_font_color.match( hex_rgb_regex )) {
				fields_with_errors_a.push( objectL10n.even_rows_font_color );}
			if ( ! data.odd_rows_font_color.match( hex_rgb_regex )) {
				fields_with_errors_a.push( objectL10n.odd_rows_font_color );}
			if ( ! data.even_rows_link_color.match( hex_rgb_regex )) {
				fields_with_errors_a.push( objectL10n.even_rows_link_color );}
			if ( ! data.odd_rows_link_color.match( hex_rgb_regex )) {
				fields_with_errors_a.push( objectL10n.odd_rows_link_color );}
			if ( ! data.rows_border_color.match( hex_rgb_regex )) {
				fields_with_errors_a.push( objectL10n.rows_border_color );}

			// Autoalignment Options.
			if (( ! data.autoalignment_affected_rows_left.match( list_of_comma_separated_numbers ) &&
			data.autoalignment_affected_rows_left.trim().length > 0) ||
			data.autoalignment_affected_rows_left.trim().length > 2000) {
				fields_with_errors_a.push( objectL10n.autoalignment_affected_rows_left );
			}
			if (( ! data.autoalignment_affected_rows_center.match( list_of_comma_separated_numbers ) &&
			data.autoalignment_affected_rows_center.trim().length > 0) ||
			data.autoalignment_affected_rows_center.trim().length > 2000) {
				fields_with_errors_a.push( objectL10n.autoalignment_affected_rows_center );
			}
			if (( ! data.autoalignment_affected_rows_right.match( list_of_comma_separated_numbers ) &&
			data.autoalignment_affected_rows_right.trim().length > 0) ||
			data.autoalignment_affected_rows_right.trim().length > 2000) {
				fields_with_errors_a.push( objectL10n.autoalignment_affected_rows_right );
			}
			if (( ! data.autoalignment_affected_columns_left.match( list_of_comma_separated_numbers ) &&
			data.autoalignment_affected_columns_left.trim().length > 0) ||
			data.autoalignment_affected_columns_left.trim().length > 110) {
				fields_with_errors_a.push( objectL10n.autoalignment_affected_columns_left );
			}
			if (( ! data.autoalignment_affected_columns_center.match( list_of_comma_separated_numbers ) &&
			data.autoalignment_affected_columns_center.trim().length > 0) ||
			data.autoalignment_affected_columns_center.trim().length > 110) {
				fields_with_errors_a.push( objectL10n.autoalignment_affected_columns_center );
			}
			if (( ! data.autoalignment_affected_columns_right.match( list_of_comma_separated_numbers ) &&
			data.autoalignment_affected_columns_right.trim().length > 0) ||
			data.autoalignment_affected_columns_right.trim().length > 110) {
				fields_with_errors_a.push( objectL10n.autoalignment_affected_columns_right );
			}

			// Responsive Options.
			if ( ! data.tablet_breakpoint.match( digits_regex ) || parseInt( data.tablet_breakpoint, 10 ) < 0 ||
			parseInt( data.tablet_breakpoint, 10 ) > 999999) {
				fields_with_errors_a.push( objectL10n.tablet_breakpoint );}
			if ( ! data.hide_tablet_list.match( list_of_comma_separated_numbers ) && data.hide_tablet_list.trim().length >
			0) {
				fields_with_errors_a.push( objectL10n.hide_tablet_list );}
			if ( ! data.tablet_header_font_size.match( digits_regex ) || parseInt( data.tablet_header_font_size, 10 ) < 0 ||
			parseInt( data.tablet_header_font_size, 10 ) > 999999) {
				fields_with_errors_a.push( objectL10n.tablet_header_font_size );
			}
			if ( ! data.tablet_body_font_size.match( digits_regex ) || parseInt( data.tablet_body_font_size, 10 ) < 0 ||
			parseInt( data.tablet_body_font_size, 10 ) > 999999) {
				fields_with_errors_a.push( objectL10n.tablet_body_font_size );
			}
			if ( ! data.phone_breakpoint.match( digits_regex ) || parseInt( data.phone_breakpoint, 10 ) < 0 ||
			parseInt( data.phone_breakpoint, 10 ) > 999999) {
				fields_with_errors_a.push( objectL10n.phone_breakpoint );}
			if ( ! data.hide_phone_list.match( list_of_comma_separated_numbers ) && data.hide_phone_list.trim().length >
			0) {
				fields_with_errors_a.push( objectL10n.hide_phone_list );}
			if ( ! data.phone_header_font_size.match( digits_regex ) || parseInt( data.phone_header_font_size, 10 ) < 0 ||
			parseInt( data.phone_header_font_size, 10 ) > 999999) {
				fields_with_errors_a.push( objectL10n.phone_header_font_size );
			}
			if ( ! data.phone_body_font_size.match( digits_regex ) || parseInt( data.phone_body_font_size, 10 ) < 0 ||
			parseInt( data.phone_body_font_size, 10 ) > 999999) {
				fields_with_errors_a.push( objectL10n.phone_body_font_size );
			}

			if (fields_with_errors_a.length > 0) {

				return fields_with_errors_a;

			} else {

				return true;

			}

		},

		/*
		* Update the number of rows on the handsontable and on the "data" db table
		*/
		update_rows: function () {

			'use strict';

			/**
			 * Reset the spreadsheet clipboard because after performing this method certain references to cell properties
			 * might be lost.
			 */
			window.DAEXTLETAL.states.synthetic_clipboard = null;

			// Change the number of rows.
			const current_number_of_rows = window.DAEXTLETAL.states.daextletal_hot.countRows() - 1;
			if ($( '#rows' ).val() < 1) {
				$( '#rows' ).val( 1 );}
			let new_number_of_rows = parseInt( $( '#rows' ).val(), 10 );

			// Do not allow to enter more rows than 10000 rows.
			if (new_number_of_rows > 10000) {
				new_number_of_rows = 10000;
				$( '#rows' ).val( 10000 );
			}

			if (new_number_of_rows > current_number_of_rows) {

				// Set the new maximum number of rows.
				window.DAEXTLETAL.states.daextletal_hot.updateSettings(
					{
						maxRows: (new_number_of_rows + 1),
					}
				);

				let cells_to_add        = [];
				const row_difference    = new_number_of_rows - current_number_of_rows;
				const count_rows_result = window.DAEXTLETAL.states.daextletal_hot.countRows();
				const count_cols_result = window.DAEXTLETAL.states.daextletal_hot.countCols();

				for (let i = 1; i <= row_difference; i++) {

						// Initialize with 0 all the cells of the new row.
					for (let t = 1; t <= count_cols_result; t++) {
						cells_to_add.push( [count_rows_result + i - 1, (t - 1), 0] );
					}

				}

				// Create the new rows.
				window.DAEXTLETAL.states.daextletal_hot.alter( 'insert_row', null, row_difference );

				// Use the setDataAtCell() method one single time with a two-dimensional array to avoid performance issues.
				window.DAEXTLETAL.states.daextletal_hot.setDataAtCell( cells_to_add );

			} else if (new_number_of_rows < current_number_of_rows) {

				const row_difference = current_number_of_rows - new_number_of_rows;

				window.DAEXTLETAL.states.daextletal_hot.alter( 'remove_row', null, row_difference );

				// Set the new maximum number of rows.
				window.DAEXTLETAL.states.daextletal_hot.updateSettings(
					{
						maxRows: (new_number_of_rows + 1),
					}
				);

			}

			// Create or remove the new rows in the 'data' db table with an asynchronous ajax request -----------------.
			if (new_number_of_rows != current_number_of_rows) {

				const data = {
					'action': 'daextletal_add_remove_rows',
					'security': DAEXTLETAL_PARAMETERS.nonce,
					'table_id': window.DAEXTLETAL.utility.get_table_id(),
					'current_number_of_rows': current_number_of_rows,
					'new_number_of_rows': new_number_of_rows,
					'current_number_of_columns': window.DAEXTLETAL.states.daextletal_hot.countCols(),
				};

				// Set ajax in synchronous mode.
				jQuery.ajaxSetup( {async: false} );

				// Send ajax request.
				$.post(
					DAEXTLETAL_PARAMETERS.ajax_url,
					data,
					function (data_json) {

						'use strict';

						window.DAEXTLETAL.utility.refresh_cell_properties_highlight();

					}
				);

				// Set ajax in asynchronous mode.
				jQuery.ajaxSetup( {async: true} );

			}

		},

		/**
		 * Update the number of columns on the handsontable and on the "data" db table.
		 */
		update_columns: function () {

			'use strict';

			/**
			 * Reset the spreadsheet clipboard because after performing this method certain references to cell properties
			 * might be lost.
			 */
			window.DAEXTLETAL.states.synthetic_clipboard = null;

			const current_number_of_columns = window.DAEXTLETAL.states.daextletal_hot.countCols();
			if ($( '#columns' ).val() < 1) {
				$( '#columns' ).val( 1 );}
			let new_number_of_columns = parseInt( $( '#columns' ).val(), 10 );

			// Do not allow to enter more columns than 40 columns.
			if (new_number_of_columns > 40) {
				new_number_of_columns = 40;
				$( '#columns' ).val( 40 );
			}

			if (new_number_of_columns > current_number_of_columns) {

				// Set the new maximum number of columns.
				window.DAEXTLETAL.states.daextletal_hot.updateSettings(
					{
						maxCols: new_number_of_columns,
					}
				);

				// Add the new columns.
				let cells_to_add        = [];
				const column_difference = new_number_of_columns - current_number_of_columns;
				const count_rows_result = window.DAEXTLETAL.states.daextletal_hot.countRows();
				const count_cols_result = window.DAEXTLETAL.states.daextletal_hot.countCols();

				for (let i = 1; i <= column_difference; i++) {

					for (let t = 1; t <= count_rows_result; t++) {

						if (t == 1) {
								// In row 1 add the default label text.
								cells_to_add.push( [0, (count_cols_result + i - 1), 'Label ' + parseInt( count_cols_result + i, 10 )] );
						} else {
							// From row 2 initialize with 0 all the cells of the new column.
							cells_to_add.push( [(t - 1), (count_cols_result + i - 1), 0] );
						}

					}

				}

				// Create the new columns.
				window.DAEXTLETAL.states.daextletal_hot.alter( 'insert_col', null, column_difference );

				// Use the setDataAtCell() method one single time with a two-dimensional array to avoid performance issues.
				window.DAEXTLETAL.states.daextletal_hot.setDataAtCell( cells_to_add );

			} else if (new_number_of_columns < current_number_of_columns) {

				const column_difference = current_number_of_columns - new_number_of_columns;

				window.DAEXTLETAL.states.daextletal_hot.alter( 'remove_col', null, column_difference );

				// Set the new maximum number of columns.
				window.DAEXTLETAL.states.daextletal_hot.updateSettings(
					{
						maxCols: new_number_of_columns,
					}
				);

			}

			// Create or remove the new columns in the 'data' db table with an asynchronous ajax request --------------.
			if (new_number_of_columns != current_number_of_columns) {

				const data = {
					'action': 'daextletal_add_remove_columns',
					'security': DAEXTLETAL_PARAMETERS.nonce,
					'table_id': window.DAEXTLETAL.utility.get_table_id(),
					'new_number_of_columns': new_number_of_columns,
				};

				// Set ajax in synchronous mode.
				jQuery.ajaxSetup( {async: false} );

				// Send ajax request.
				$.post(
					DAEXTLETAL_PARAMETERS.ajax_url,
					data,
					function (data_json) {

						'use strict';

						window.DAEXTLETAL.utility.refresh_cell_properties_highlight();

					}
				);

				// Set ajax in asynchronous mode.
				jQuery.ajaxSetup( {async: true} );

			}

		},

		/**
		 * Retrieves and displays the properties of the cell.
		 *
		 * If there are no properties associated with a cell the default values will be displayed.
		 *
		 * @param row Int The row of the cell
		 * @param column Int The column of the cell
		 */
		retrieve_cell_properties: function (row, column) {

			'use strict';

			const table_id             = window.DAEXTLETAL.utility.get_table_id();
			let data_obj               = null;
			let cell_properties_exists = null;

			// Prepare ajax request.
			const data = {
				'action': 'daextletal_retrieve_cell_properties',
				'security': DAEXTLETAL_PARAMETERS.nonce,
				'table_id': table_id,
				'row': row,
				'column': column,
			};

			// Send ajax request.
			$.post(
				DAEXTLETAL_PARAMETERS.ajax_url,
				data,
				function (data_json) {

					'use strict';

					try {

						data_obj = JSON.parse( data_json );

						cell_properties_exists = true;

					} catch (e) {

						// Set the default cell properties.
						data_obj = {
							'table_id': table_id,
							'row_index': row,
							'column_index': column,
							'link': '',
							'image_left': '',
							'image_right': '',
						};

						cell_properties_exists = false;

					}

					// Update the cell properties in the sidebar.
					window.DAEXTLETAL.utility.update_cell_properties_in_sidebar( data_obj, cell_properties_exists );

				}
			);

		},

		/**
		 * Update or reset the cell properties of a cell in the "cell" db table.
		 */
		update_reset_cell_properties: function (task) {

			'use strict';

			/**
			 * Reset the spreadsheet clipboard because after performing this method certain references to cell properties
			 * might be lost.
			 */
			window.DAEXTLETAL.states.synthetic_clipboard = null;

			const table_id                      = window.DAEXTLETAL.utility.get_table_id();
			const row_index                     = $( '#cell-property-row-index' ).val();
			const column_index                  = $( '#cell-property-column-index' ).val();
			const link                          = $( '#cell-property-link' ).val();
			const image_left                    = $( '#cell-property-image-left' ).val();
			const image_right                   = $( '#cell-property-image-right' ).val();

			// Prepare ajax request.
			const data = {
				'action': 'daextletal_update_reset_cell_properties',
				'security': DAEXTLETAL_PARAMETERS.nonce,
				'task': task,
				'table_id': table_id,
				'row_index': row_index,
				'column_index': column_index,
				'link': link,
				'image_left': image_left,
				'image_right': image_right,
			};

			switch (task) {

				case 'update-cell-properties':

					// Update cell properties -------------------------------------------------------------------------.
					if (window.DAEXTLETAL.utility.cell_properties_is_valid( data )) {

						// Set ajax in synchronous mode.
						jQuery.ajaxSetup( {async: false} );

						// Send ajax request.
						$.post(
							DAEXTLETAL_PARAMETERS.ajax_url,
							data,
							function (response) {

								'use strict';

								if (response.trim() == 'success') {

									// Show success message.
									if ($( '#update-cell-properties' ).attr( 'data-action' ) == 'update') {
										$( '#cell-properties-added-updated-message p' ).text( objectL10n.cell_properties_updated_message );
									} else {
										$( '#cell-properties-added-updated-message p' ).text( objectL10n.cell_properties_added_message );
									}

									// Hide error message.
									$( '#cell-properties-error-message' ).hide();

									$( '#cell-properties-added-updated-message' ).show();
									clearTimeout( window.DAEXTLETAL.states.cell_properties_message_timeout_handler );
									window.DAEXTLETAL.states.cell_properties_message_timeout_handler = setTimeout(
										function () {

											'use strict';

											$( '#cell-properties-added-updated-message' ).hide();

										},
										3000
									);

									// Show the proper button with the proper text.
									$( '#update-cell-properties' ).attr( 'data-action', 'update' );
									$( '#update-cell-properties' ).val( objectL10n.update_cell_properties );
									$( '#reset-cell-properties' ).show();

									// Highlight the cell properties.
									window.DAEXTLETAL.utility.refresh_cell_properties_highlight();

								}

							}
						);

						// Set ajax in asynchronous mode.
						jQuery.ajaxSetup( {async: true} );

					}

				break;

				case 'reset-cell-properties':

					// Reset cell properties --------------------------------------------------------------------------.
					$.post(
						DAEXTLETAL_PARAMETERS.ajax_url,
						data,
						function (response) {

							'use strict';

							if (response.trim() == 'success') {

									// Set the default cell properties.
									const data_obj = {
										'table_id': table_id,
										'row_index': row_index,
										'column_index': column_index,
										'link': '',
										'image_left': '',
										'image_right': '',
								};

									// Update the cell properties in the sidebar.
									window.DAEXTLETAL.utility.update_cell_properties_in_sidebar( data_obj, false );

									// Hide error message.
									$( '#cell-properties-error-message' ).hide();

									// Show success message.
									$( '#cell-properties-added-updated-message p' ).text( objectL10n.cell_properties_reset_message );
									$( '#cell-properties-added-updated-message' ).show();
									clearTimeout( window.DAEXTLETAL.states.cell_properties_message_timeout_handler );
									window.DAEXTLETAL.states.cell_properties_message_timeout_handler = setTimeout(
										function () {

											'use strict';

											$( '#cell-properties-added-updated-message' ).hide();

										},
										3000
									);

									// Highlight the cell properties.
									window.DAEXTLETAL.utility.refresh_cell_properties_highlight();

							}

						}
					);

				break;

			}

		},

		cell_properties_is_valid: function (data) {

			'use strict';

			// Init variables.
			let fields_with_errors_a = [];

			// Define patterns ----------------------------------------------------------------------------------------.
			const hex_rgb_regex                   = /^#(?:[0-9a-fA-F]{3}){1,2}$/;
			const url_regex                       = /^https?:\/\/.+$/;
			const list_of_comma_separated_numbers = /^(\s*(\d+\s*,\s*)+\d+\s*|\s*\d+\s*)$/;

			// Validate data ------------------------------------------------------------------------------------------.
			if (( ! data.link.match( url_regex ) && data.link.trim().length > 0) || data.link.trim().length >
			2083) {
				fields_with_errors_a.push( objectL10n.link );}
			if (( ! data.image_left.match( url_regex ) && data.image_left.trim().length > 0) || data.image_left.trim().length >
			2083) {
				fields_with_errors_a.push( objectL10n.image_left );}
			if (( ! data.image_right.match( url_regex ) && data.image_right.trim().length > 0) || data.image_right.trim().length >
			2083) {
				fields_with_errors_a.push( objectL10n.image_right );}

			if (fields_with_errors_a.length > 0) {

				// Hide the added/updated message if it's shown.
				$( '#cell-properties-added-updated-message' ).hide();

				// Show error message.
				$( '#cell-properties-error-message p' ).
				html(
					objectL10n.cell_properties_error_partial_message + ' <strong>' + fields_with_errors_a.join( ', ' ) +
					'</strong>'
				);
				$( '#cell-properties-error-message' ).show();

				return false;

			} else {

				// Hide the error message if it's shown.
				$( '#cell-properties-error-message' ).hide();

				return true;

			}

		},

		/**
		 * Initializes Select2 on all the select elements
		 */
		initialize_select2: function () {

			'use strict';

			let select2_elements = [];
			select2_elements.push( '#position-side' );
			select2_elements.push( '#number-format' );
			select2_elements.push( '#cell-property-font-weight' );
			select2_elements.push( '#cell-property-font-style' );
			select2_elements.push( '#cell-property-alignment' );
			select2_elements.push( '#cell-property-formula' );
			select2_elements.push( '#order-by' );
			select2_elements.push( '#order-desc-asc' );
			select2_elements.push( '#table-layout' ),
			select2_elements.push( '#table-width' );
			select2_elements.push( '#column-width' );
			select2_elements.push( '#header-font-weight' );
			select2_elements.push( '#header-font-style' );
			select2_elements.push( '#header-position-alignment' );
			select2_elements.push( '#body-font-weight' );
			select2_elements.push( '#body-font-style' );
			select2_elements.push( '#autoalignment-priority' );
			select2_elements.push( '#order-data-type' );
			select2_elements.push( '#order-date-format' );
			select2_elements.push( '#formula-average-round' );

			jQuery( select2_elements.join( ',' ) ).select2();

		},

		/*
		* Update the cell properties displayed in the sidebar based on the cell properties provided in the data_obj
		*
		* @param data_obj Object The object that includes the data properties
		* @param cell_properties_exists Bool A flag which indicates if the cell properties of this cell exists (the cell
		* properties comes from the 'cell' db table) or don't exist (the cell properties have been generated from the
		* default values)
		*/
		update_cell_properties_in_sidebar: function (data_obj, cell_properties_exists) {

			'use strict';

			// Count the number of enabled cell properties.
			let one_cell_property_exists = false;
			$( 'div.daext-form-cell-properties div' ).each(
				function () {

					'use strict';

					if ($( this ).css( 'display' ) !== 'none') {
						one_cell_property_exists = true;
					}

				}
			);

			// Display the sidebar container if at least on cell property exists.
			if (one_cell_property_exists) {

				// Show the sidebar container (Set the display property to flex).
				$( '#sidebar-container' ).css( 'display', 'flex' );

			}

			// Set the title of the cell properties section.
			if (data_obj.row_index == 0) {
				$( '#cell-properties-title' ).text( 'Header ' + (parseInt( data_obj.column_index, 10 ) + 1) );
			} else {
				$( '#cell-properties-title' ).
				text( 'Body ' + parseInt( data_obj.row_index, 10 ) + ':' + (parseInt( data_obj.column_index, 10 ) + 1) );
			}

			// Initialize the hidden fields used to store the row and column indexes.
			$( '#cell-property-row-index' ).val( data_obj.row_index );
			$( '#cell-property-column-index' ).val( data_obj.column_index );

			const wpColorPickerConfig = {
				'palettes': []
			};

			// Link.
			$( '#cell-property-link' ).val( data_obj.link );

			// Image Left.
			$( '#cell-property-image-left' ).val( data_obj.image_left );
			if (data_obj.image_left.length > 0) {
				$( '#cell-property-image-left' ).prev().find( 'img' ).attr( 'src', data_obj.image_left ).show();
				$( '#cell-property-image-left' ).next().text( $( '#cell-property-image-left' ).next().attr( 'data-remove' ) );
				$( '#cell-property-image-left' ).next().attr( 'data-set-remove', 'remove' );
				$( '#cell-property-image-left' ).next().next().hide();
			} else {
				$( '#cell-property-image-left' ).prev().find( 'img' ).attr( 'src', '' ).hide();
				$( '#cell-property-image-left' ).next().text( $( '#cell-property-image-left' ).next().attr( 'data-set' ) );
				$( '#cell-property-image-left' ).next().attr( 'data-set-remove', 'set' );
				$( '#cell-property-image-left' ).next().next().show();
			}

			// Image Right.
			$( '#cell-property-image-right' ).val( data_obj.image_right );
			if (data_obj.image_right.length > 0) {
				$( '#cell-property-image-right' ).prev().find( 'img' ).attr( 'src', data_obj.image_right ).show();
				$( '#cell-property-image-right' ).next().text( $( '#cell-property-image-right' ).next().attr( 'data-remove' ) );
				$( '#cell-property-image-right' ).next().attr( 'data-set-remove', 'remove' );
				$( '#cell-property-image-right' ).next().next().hide();
			} else {
				$( '#cell-property-image-right' ).prev().find( 'img' ).attr( 'src', '' ).hide();
				$( '#cell-property-image-right' ).next().text( $( '#cell-property-image-right' ).next().attr( 'data-set' ) );
				$( '#cell-property-image-right' ).next().attr( 'data-set-remove', 'set' );
				$( '#cell-property-image-right' ).next().next().show();
			}

			// Show the proper button with the proper text.
			if (cell_properties_exists) {
				$( '#update-cell-properties' ).attr( 'data-action', 'update' );
				$( '#update-cell-properties' ).val( objectL10n.update_cell_properties );
				$( '#reset-cell-properties' ).show();
			} else {
				$( '#update-cell-properties' ).attr( 'data-action', 'add' );
				$( '#update-cell-properties' ).val( objectL10n.add_cell_properties );
				$( '#reset-cell-properties' ).hide();
			}

			// Row Slots.
			$( '#cell-property-row-slots' ).val( data_obj.row_slots );

			// Column Slots.
			$( '#cell-property-column-slots' ).val( data_obj.column_slots );

		},

		/**
		 * Update the selection of a specific select2 field (field_selector) based on the provided value (selected_value)
		 */
		update_select2_field: function (field_selector, selected_value) {

			'use strict';

			$( field_selector + ' option' ).prop( 'selected', false );
			$( field_selector + ' option[value=' + selected_value + ']' ).attr( 'selected', 'selected' );
			$( field_selector ).trigger( 'change' );

		},

		/**
		 * Update the options available in the '#order-by-*' fields based on the number of columns defined in the #columns
		 * field. The current selected option will be maintained if the selected column still exists.
		 */
		update_order_by: function () {

			'use strict';

			const number_of_columns = parseInt( $( '#columns' ).val(), 10 );

				// Get the current value of the select element.
				const current_value = $( '#order-by' ).val();

				// Delete the option elements.
				$( '#order-by' + ' option' ).remove();

				// Add the option elements based on the current number of columns.
				let option_elements = '';
				for (let t = 1; t <= number_of_columns; t++) {
					option_elements += '<option value="' + t + '">' + objectL10n.column + ' ' + t + '</option>';
				}
				$( '#order-by' ).append( option_elements );

				// Apply the stored current value if the column still exists, otherwise select the first element.
				if (current_value <= number_of_columns) {
					$( '#order-by' ).val( current_value );
				} else {
					$( '#order-by' ).val( 1 );
				}

				// Update select2.
				$( '#order-by' ).trigger( 'change' );

		},

		/**
		 * Moves the cell properties section on the bottom of the table section when the screen width goes below a specific
		 * value.
		 */
		responsive_sidebar_container: function () {

			'use strict';

			if ($( '#wpcontent' ).width() < 1560) {

				$( '.daext-form-container' ).addClass( 'table-container-below-breakpoint' );

			} else {

				$( '.daext-form-container' ).removeClass( 'table-container-below-breakpoint' );

			}

		},

		/**
		 * Verify the maximum number of cell properties involved in the context menu task. If the number of cell is smaller
		 * or equal to 100 return true, otherwise generate an alert message and return false.
		 *
		 * @param options An object with included data about the selection performed on the Handsontable table.
		 * @return bool
		 */
		valid_cell_number: function (options) {

			'use strict';

			const number_of_rows   = options.end.row - options.start.row + 1;
			const number_of_column = options.end.col - options.start.col + 1;
			if (number_of_rows * number_of_column <= 100) {
				return true;
			} else {
				$( '.dialog-alert[data-id="valid-cell-number"]' ).dialog( 'open' );
				return false;
			}

		},

		/**
		 * The "has-cell-properties" class (used to highlight the cells with a specific background color) is added where the
		 * cell properties exist.
		 *
		 * The following operations are performed:
		 *
		 * 1 - A list with the indexes of the cell properties is retrieved with an ajax request
		 * 2 - The cell meta are removed from all the cell
		 * 3 - The "has-cell-properties" class is added with to the cell meta
		 */
		refresh_cell_properties_highlight: function () {

			'use strict';

			// Prepare ajax request.
			const data = {
				'action': 'daextletal_get_cell_properties_index',
				'security': DAEXTLETAL_PARAMETERS.nonce,
				'table_id': window.DAEXTLETAL.utility.get_table_id(),
			};

			// Send ajax request.
			$.post(
				DAEXTLETAL_PARAMETERS.ajax_url,
				data,
				function (data_json) {

					'use strict';

					try {

						const data_a = JSON.parse( data_json );

						// Remove the 'has-cell-properties' class from all the cells.
						const number_of_rows    = window.DAEXTLETAL.states.daextletal_hot.countRows();
						const number_of_columns = window.DAEXTLETAL.states.daextletal_hot.countCols();
						for (let i = 0; i < number_of_rows; i++) {
							for (let t = 0; t < number_of_columns; t++) {
									window.DAEXTLETAL.states.daextletal_hot.setCellMeta( i, t, 'className', '' );
							}
						}

						// Add the 'has-cell-properties' class to the celle where there are cell properties.
						if (data_a.length > 0) {
							$.each(
								data_a,
								function (index, value) {

									'use strict';

									window.DAEXTLETAL.states.daextletal_hot.setCellMeta(
										value['row_index'],
										value['column_index'],
										'className',
										'has-cell-properties'
									);
								}
							);
						}

						window.DAEXTLETAL.states.daextletal_hot.render();

					} catch (e) {

						// Do nothing.

					}

				}
			);

		},

		/**
		 * Add the cell properties of the selected cells in the state used to store
		 * the copied cell properties.
		 */
		add_cell_properties_to_state: function (options, source) {

			'use strict';

			// Get the table id.
			const table_id = window.DAEXTLETAL.utility.get_table_id();

			// Prepare ajax request.
			const data = {
				'action': 'daextletal_retrieve_cell_properties_multiple',
				'security': DAEXTLETAL_PARAMETERS.nonce,
				'table_id': table_id,
				'row_start': options.start['row'],
				'column_start': options.start['col'],
				'row_end': options.end['row'],
				'column_end': options.end['col']
			};

			// Send ajax request.
			$.post(
				DAEXTLETAL_PARAMETERS.ajax_url,
				data,
				function (data_json) {

					'use strict';

					try {

						// Save the cell properties in the copied cell properties state.
						window.DAEXTLETAL.states.copiedCellProperties.data   = JSON.parse( data_json );
						window.DAEXTLETAL.states.copiedCellProperties.source = source;

						// If this task has been generated from a "Cut Cell Properties"
						// operation remove the cell properties from the selected cells.
						if (source === 'cut') {
							window.DAEXTLETAL.utility.reset_cell_properties( options );
						}

					} catch (e) {

						// Invalid JSON data.

					}

				}
			);

		},

		/**
		 * Reset the cell properties of the selected cells.
		 *
		 * @param options
		 */
		reset_cell_properties: function (options) {

			'use strict';

			/**
			 * Reset the spreadsheet clipboard because after performing this method certain references to cell properties
			 * might be lost.
			 */
			window.DAEXTLETAL.states.synthetic_clipboard = null;

			// Prepare ajax request.
			const data = {
				'action': 'daextletal_reset_cell_properties',
				'security': DAEXTLETAL_PARAMETERS.nonce,
				'table_id': window.DAEXTLETAL.utility.get_table_id(),
				'options': JSON.stringify( options ),
			};

			// Set ajax in synchronous mode.
			jQuery.ajaxSetup( {async: false} );

			// Send ajax request.
			$.post(
				DAEXTLETAL_PARAMETERS.ajax_url,
				data,
				function () {

					'use strict';

					window.DAEXTLETAL.utility.refresh_cell_properties_highlight();

				}
			);

			// Set ajax in asynchronous mode.
			jQuery.ajaxSetup( {async: true} );

		}

	};

}(window.jQuery));