/**
 * This file is used to handle the context menu events of the handsontable instance.
 *
 * @package league-table-lite
 */

(function ($) {

	'use strict';

	/**
	 * This object includes the methods used in the callbacks of the handsontable context menu items.
	 *
	 * Note that the handsontable context menu is defined in the utility::initialize_handsontable().
	 *
	 * Ref: https://handsontable.com/docs/7.0.2/ContextMenu.html
	 */
	window.DAEXTLETAL.contextMenu = {

		/**
		 * Insert row above context menu handler.
		 *
		 * @param row The index of the row placed on the bottom of the one that should be added.
		 */
		insert_row_above: function (row) {

			'use strict';

			/**
			 * Reset the spreadsheet clipboard because after performing this method certain references to cell properties
			 * might be lost.
			 */
			window.DAEXTLETAL.states.synthetic_clipboard = null;

			// Increase by 1 the maximum number of rows.
			const number_of_rows = window.DAEXTLETAL.states.daextletal_hot.countRows();
			window.DAEXTLETAL.states.daextletal_hot.updateSettings(
				{
					maxRows: number_of_rows + 1,
				}
			);

			// Add row in the spreadsheet.
			window.DAEXTLETAL.states.daextletal_hot.alter( 'insert_row', row, 1 );

			/**
			 * Perform and ajax request and:
			 *
			 * - update the "row" field of "_table"
			 * - update all the subsequent "row_index" in "_data"
			 * - add a new record in "_data" with the missing "row_index"
			 * - update all the subsequent "row_index" in "_cell"
			 */
				// Prepare ajax request.
			const data = {
				'action': 'daextletal_insert_row_above',
				'security': DAEXTLETAL_PARAMETERS.nonce,
				'table_id': window.DAEXTLETAL.utility.get_table_id(),
				'row': row,
			};

			// Set ajax in synchronous mode.
			jQuery.ajaxSetup( {async: false} );

			// Use the setDataAtCell() method one single time with a two-dimensional array to avoid performance issues.
			const number_of_columns = window.DAEXTLETAL.states.daextletal_hot.countCols();
			let cells_to_modify     = [];
			for (let i = 0; i < number_of_columns; i++) {
				cells_to_modify.push( [row, i, 0] );
			}
			window.DAEXTLETAL.states.daextletal_hot.setDataAtCell( cells_to_modify );

			// Send ajax request.
			$.post(
				DAEXTLETAL_PARAMETERS.ajax_url,
				data,
				function () {

					'use strict';

					// Update the value of the rows fields.
					$( '#rows' ).val( (parseInt( $( '#rows' ).val(), 10 ) + 1) );

				}
			);

			// Set ajax in asynchronous mode.
			jQuery.ajaxSetup( {async: true} );

		},

		/**
		 * Insert row below context menu handler.
		 *
		 * @param row Int The index of the row placed on the top of the one that should be added.
		 */
		insert_row_below: function (row) {

			'use strict';

			/**
			 * Reset the spreadsheet clipboard because after performing this method certain references to cell properties
			 * might be lost.
			 */
			window.DAEXTLETAL.states.synthetic_clipboard = null;

			// Increase by 1 the maximum number of rows.
			const number_of_rows = window.DAEXTLETAL.states.daextletal_hot.countRows();
			window.DAEXTLETAL.states.daextletal_hot.updateSettings(
				{
					maxRows: number_of_rows + 1,
				}
			);

			// Remove row from the spreadsheet.
			window.DAEXTLETAL.states.daextletal_hot.alter( 'insert_row', row + 1, 1 );

			// Prepare ajax request.
			const data = {
				'action': 'daextletal_insert_row_below',
				'security': DAEXTLETAL_PARAMETERS.nonce,
				'table_id': window.DAEXTLETAL.utility.get_table_id(),
				'row': row,
			};

			// Set ajax in synchronous mode.
			jQuery.ajaxSetup( {async: false} );

			// Use the setDataAtCell() method one single time with a two-dimensional array to avoid performance issues.
			const number_of_columns = window.DAEXTLETAL.states.daextletal_hot.countCols();
			let cells_to_modify     = [];
			for (let i = 0; i < number_of_columns; i++) {
				cells_to_modify.push( [row + 1, i, 0] );
			}
			window.DAEXTLETAL.states.daextletal_hot.setDataAtCell( cells_to_modify );

			// Send ajax request.
			$.post(
				DAEXTLETAL_PARAMETERS.ajax_url,
				data,
				function () {

					'use strict';

					// Update the value of the rows fields.
					$( '#rows' ).val( (parseInt( $( '#rows' ).val(), 10 ) + 1) );

				}
			);

			// Set ajax in asynchronous mode.
			jQuery.ajaxSetup( {async: true} );

		},

		/**
		 * Insert column left context menu event handler.
		 *
		 * @param column Int The index of the column placed on the right of the one that should be added.
		 */
		insert_column_left: function (column) {

			'use strict';

			/**
			 * Reset the spreadsheet clipboard because after performing this method certain references to cell properties
			 * might be lost.
			 */
			window.DAEXTLETAL.states.synthetic_clipboard = null;

			// Increase by 1 the maximum number of rows.
			const number_of_columns = window.DAEXTLETAL.states.daextletal_hot.countCols();
			window.DAEXTLETAL.states.daextletal_hot.updateSettings(
				{
					maxCols: number_of_columns + 1,
				}
			);

			// Add the column in the spreadsheet.
			window.DAEXTLETAL.states.daextletal_hot.alter( 'insert_col', column, 1 );

			// Use the setDataAtCell() method one single time with a two-dimensional array to avoid performance issues.
			const number_of_rows = window.DAEXTLETAL.states.daextletal_hot.countRows();
			let cells_to_modify  = [];
			cells_to_modify.push( [0, column, 'New Label '] );
			for (let i = 1; i < number_of_rows; i++) {
				cells_to_modify.push( [i, column, 0] );
			}
			window.DAEXTLETAL.states.daextletal_hot.setDataAtCell( cells_to_modify );

			/**
			 * Perform and ajax request and:
			 *
			 * - update the "row" field of "_table"
			 * - update all the subsequent "row_index" in "_data"
			 * - add a new record in "_data" with the missing "row_index"
			 * - update all the subsequent "row_index" in "_cell"
			 */
				// Prepare ajax request.
			const data = {
				'action': 'daextletal_insert_column_left',
				'security': DAEXTLETAL_PARAMETERS.nonce,
				'table_id': window.DAEXTLETAL.utility.get_table_id(),
				'column': column,
			};

			// Set ajax in synchronous mode.
			jQuery.ajaxSetup( {async: false} );

			// Send ajax request.
			$.post(
				DAEXTLETAL_PARAMETERS.ajax_url,
				data,
				function () {

					// Update the value of the rows fields.
					$( '#columns' ).val( (parseInt( $( '#columns' ).val(), 10 ) + 1) );

				}
			);

			// Set ajax in asynchronous mode.
			jQuery.ajaxSetup( {async: true} );

		},

		/**
		 * Insert column left context menu handler.
		 *
		 * @param column Int The index of the column placed on the left of the one that should be added.
		 */
		insert_column_right: function (column) {

			'use strict';

			/**
			 * Reset the spreadsheet clipboard because after performing this method certain references to cell properties
			 * might be lost.
			 */
			window.DAEXTLETAL.states.synthetic_clipboard = null;

			// Increase by 1 the maximum number of rows.
			const number_of_columns = window.DAEXTLETAL.states.daextletal_hot.countCols();
			window.DAEXTLETAL.states.daextletal_hot.updateSettings(
				{
					maxCols: number_of_columns + 1,
				}
			);

			// Add the column in the spreadsheet.
			window.DAEXTLETAL.states.daextletal_hot.alter( 'insert_col', column + 1, 1 );

			// Use the setDataAtCell() method one single time with a two-dimensional array to avoid performance issues.
			const number_of_rows = window.DAEXTLETAL.states.daextletal_hot.countRows();
			let cells_to_modify  = [];
			cells_to_modify.push( [0, column + 1, 'New Label '] );
			for (let i = 1; i < number_of_rows; i++) {
				cells_to_modify.push( [i, column + 1, 0] );
			}
			window.DAEXTLETAL.states.daextletal_hot.setDataAtCell( cells_to_modify );

			// Prepare ajax request.
			const data = {
				'action': 'daextletal_insert_column_right',
				'security': DAEXTLETAL_PARAMETERS.nonce,
				'table_id': window.DAEXTLETAL.utility.get_table_id(),
				'column': column,
			};

			// Set ajax in synchronous mode.
			jQuery.ajaxSetup( {async: false} );

			// Send ajax request.
			$.post(
				DAEXTLETAL_PARAMETERS.ajax_url,
				data,
				function () {

					// Update the value of the rows fields.
					$( '#columns' ).val( (parseInt( $( '#columns' ).val(), 10 ) + 1) );

				}
			);

			// Set ajax in asynchronous mode.
			jQuery.ajaxSetup( {async: true} );

		},

		/**
		 * Remove row context menu handler.
		 *
		 * @param row Int The row that should be removed.
		 */
		remove_row: function (row) {

			'use strict';

			/**
			 * Reset the spreadsheet clipboard because after performing this method certain references to cell properties
			 * might be lost.
			 */
			window.DAEXTLETAL.states.synthetic_clipboard = null;

			// Remove row from the spreadsheet.
			window.DAEXTLETAL.states.daextletal_hot.alter( 'remove_row', row, 1 );

			// Decrease by 1 the maximum number of rows.
			const number_of_rows = window.DAEXTLETAL.states.daextletal_hot.countRows();
			window.DAEXTLETAL.states.daextletal_hot.updateSettings(
				{
					maxRows: number_of_rows,
				}
			);

			/**
			 * Perform and ajax request and:
			 *
			 * - update the "row" field of "_table"
			 * - remove the row from "_data" (remove the record and update the "row_index" of the other records
			 * - update the cell properties with the new indexes in "_cell"
			 */
				// Prepare ajax request.
			const data = {
				'action': 'daextletal_remove_row',
				'security': DAEXTLETAL_PARAMETERS.nonce,
				'table_id': window.DAEXTLETAL.utility.get_table_id(),
				'row': row,
			};

			// Set ajax in synchronous mode.
			jQuery.ajaxSetup( {async: false} );

			// Send ajax request.
			$.post(
				DAEXTLETAL_PARAMETERS.ajax_url,
				data,
				function () {

					'use strict';

					// Update the value of the rows fields.
					$( '#rows' ).val( (parseInt( $( '#rows' ).val(), 10 ) - 1) );

				}
			);

			// Set ajax in asynchronous mode.
			jQuery.ajaxSetup( {async: true} );

		},

		/**
		 * Remove column context menu handler.
		 *
		 * @param column Int The column that should be removed.
		 */
		remove_column: function (column) {

			'use strict';

			/**
			 * Reset the spreadsheet clipboard because after performing this method certain references to cell properties
			 * might be lost.
			 */
			window.DAEXTLETAL.states.synthetic_clipboard = null;

			// Remove column from the spreadsheet.
			window.DAEXTLETAL.states.daextletal_hot.alter( 'remove_col', column, 1 );

			// Decrease by 1 the maximum number of rows.
			const number_of_columns = window.DAEXTLETAL.states.daextletal_hot.countCols();
			window.DAEXTLETAL.states.daextletal_hot.updateSettings(
				{
					maxCols: number_of_columns,
				}
			);

			// Prepare ajax request.
			const data = {
				'action': 'daextletal_remove_column',
				'security': DAEXTLETAL_PARAMETERS.nonce,
				'table_id': window.DAEXTLETAL.utility.get_table_id(),
				'column': column,
			};

			// Set ajax in synchronous mode.
			jQuery.ajaxSetup( {async: false} );

			// Send ajax request.
			$.post(
				DAEXTLETAL_PARAMETERS.ajax_url,
				data,
				function () {

					'use strict';

					// Update the value of the rows fields.
					$( '#columns' ).val( (parseInt( $( '#columns' ).val(), 10 ) - 1) );

				}
			);

			// Set ajax in asynchronous mode.
			jQuery.ajaxSetup( {async: true} );

		},

		/**
		 * Paste the cell data in the position provided in the "options" parameter with the information included in
		 * the synthetic clipboard.
		 *
		 * @param options Object An object with included information about the selection of the cells.
		 */
		paste_cell_data: function (options) {

			'use strict';

			let cells_to_modify = [];
			let current_row     = 0;
			let current_col     = 0;
			let row_counter     = 0;
			let col_counter     = 0;

			$.each(
				window.DAEXTLETAL.states.synthetic_clipboard,
				function (index, value) {

					if (current_row < value.relative_index_row) {
						row_counter++;
						col_counter = 0;
					} else {
						if (current_col < value.relative_index_col) {
							col_counter++;
						}
					}

					cells_to_modify.push(
						[
						options.start.row + row_counter,
						options.start.col + col_counter,
						value.data,
						]
					);

					current_row = value.relative_index_row;
					current_col = value.relative_index_col;

				}
			);

			// Remove from cells_to_modify the cells that are outside the table.
			const count_rows_result = window.DAEXTLETAL.states.daextletal_hot.countRows();
			const count_cols_result = window.DAEXTLETAL.states.daextletal_hot.countCols();
			let indexes_to_remove   = [];

			$.each(
				cells_to_modify,
				function (index, value) {
					if (value[0] >= count_rows_result || value[1] >= count_cols_result) {
						indexes_to_remove.push( index );
					}
				}
			);

			for (let i = indexes_to_remove.length - 1; i >= 0; i--) {
				cells_to_modify.splice( indexes_to_remove[i], 1 );
			}

			// Put the data in the handsontable.
			window.DAEXTLETAL.states.daextletal_hot.setDataAtCell( cells_to_modify );

		},

		/**
		 * Remove the cell properties included in the selection.
		 *
		 * @param options Object An object with included information about the selection.
		 */
		reset_cell_properties: function (options) {

			'use strict';

			// Remove the cell properties from the selected cells.
			window.DAEXTLETAL.utility.reset_cell_properties( options );

		},

		/**
		 * Set 0 to all the cells included in the selection.
		 *
		 * @param options Object An object with information about the selection.
		 */
		reset_data: function (options) {

			'use strict';

			let data;

			for (let i = options.start.row; i <= options.end.row; i++) {
				for (let t = options.start.col; t <= options.end.col; t++) {
					if (i === 0) {
						data = 'New Label';
					} else {
						data = 0;
					}
					window.DAEXTLETAL.states.daextletal_hot.setDataAtCell( i, t, data );
				}
			}

		},

		/**
		 *
		 * Copy the cell properties of the selected cells in the state used to store
		 * the copied cell properties.
		 *
		 * @param options
		 */
		copy_cell_properties: function (options) {

			'use strict';

			// Copy the selected cell properties in the copied cell properties state.
			window.DAEXTLETAL.utility.add_cell_properties_to_state( options, 'copy' );

		},

		cut_cell_properties: function (options) {

			'use strict';

			// Copy the selected cell properties in the copied cell properties state.
			window.DAEXTLETAL.utility.add_cell_properties_to_state( options, 'cut' );

		},

		/**
		 * With an ajax request assign the cell properties in the global variable
		 * that contain them to the actual cells.
		 *
		 * @param options
		 */
		paste_cell_properties: function (options) {

			'use strict';

			// Get the table id.
			const table_id = window.DAEXTLETAL.utility.get_table_id();

			// Prepare ajax request.
			const data = {
				'action': 'daextletal_update_cell_properties_multiple',
				'security': DAEXTLETAL_PARAMETERS.nonce,
				'table_id': table_id,
				'row_start': options.start['row'],
				'column_start': options.start['col'],
				'copied_cell_properties': JSON.stringify( window.DAEXTLETAL.states.copiedCellProperties.data )
			};

			// Send ajax request.
			$.post(
				DAEXTLETAL_PARAMETERS.ajax_url,
				data,
				function (result) {

					'use strict';

					/**
					 * Clear the copied cell properties state if the data where from a cut operation.
					 */
					if (window.DAEXTLETAL.states.copiedCellProperties.source === 'cut') {
						window.DAEXTLETAL.states.copiedCellProperties.data   = null;
						window.DAEXTLETAL.states.copiedCellProperties.source = null;
					}

					// Refresh the cell properties.
					window.DAEXTLETAL.utility.refresh_cell_properties_highlight();

					// Retrieves and displays the properties of the selected cell.
					window.DAEXTLETAL.utility.retrieve_cell_properties( options.start.row, options.start.col );

				}
			);

		},

		/**
		 * Fill the synthetic clipboard with the indexes included in the selection by using the "options" Object and with
		 * the data of the cells retrieved from the handsontable.
		 *
		 * @param options Object An object with information about the selection.
		 */
		fill_synthetic_clipboard: function (options) {

			'use strict';

			window.DAEXTLETAL.states.synthetic_clipboard = [];

			let row_counter = 0;
			let col_counter = 0;

			for (let i = options.start.row; i <= options.end.row; i++) {
				for (let t = options.start.col; t <= options.end.col; t++) {
					window.DAEXTLETAL.states.synthetic_clipboard.push(
						{
							relative_index_row: row_counter,
							relative_index_col: col_counter,
							absolute_index_row: i,
							absolute_index_col: t,
							data: window.DAEXTLETAL.states.daextletal_hot.getDataAtCell( i, t ),
						}
					);
					col_counter++;
				}
				row_counter++;
			}

		},

	};

})( window.jQuery );