/**
 * This file is used to initialize the tables area.
 *
 * @package league-table-lite
 */

(function ($) {

	'use strict';

	// This object is used to save all the variable states of the menu.
	window.DAEXTLETAL.states = {

		daextletal_hot: false,
		synthetic_clipboard: null,
		cell_properties_message_timeout_handler: null,
		table_message_timeout_handler: null,
		tableToDelete: null,
		copiedCellProperties: {
			data: null,
			source: null
		},

	};

	bindEventListeners();

	/**
	 * Bind the event listeners.
	 */
	function bindEventListeners() {

		'use strict';

		$( document ).ready(
			function () {

				'use strict';

				window.DAEXTLETAL.utility.initialize_handsontable();
				window.DAEXTLETAL.utility.initialize_select2();
				window.DAEXTLETAL.utility.responsive_sidebar_container();
				window.DAEXTLETAL.utility.disable_specific_keyboard_shortcuts();
				window.DAEXTLETAL.utility.refresh_cell_properties_highlight();

				$( document.body ).on(
					'click',
					'#save' ,
					function () {

						'use strict';

						const reload_menu       = parseInt( $( this ).data( 'reload-menu' ), 10 ) == 1 ? true : false;
						const validation_result = window.DAEXTLETAL.utility.save_table( reload_menu );

						// Show error message.
						if (validation_result === true) {

							location.href = location.href.replace( /&edit_id=\d+\b/, '' );

						} else {

							// Display error message.
							$( '#table-error p' ).
							html( objectL10n.table_error_partial_message + ' <strong>' + validation_result.join( ', ' ) + '</strong>' );
							$( '#table-error' ).show();

						}

					}
				);

				$( document.body ).on(
					'click',
					'#close' ,
					function () {

						'use strict';

						// Reload the dashboard menu.
						window.location.replace( DAEXTLETAL_PARAMETERS.admin_url + 'admin.php?page=daextletal-tables' );

					}
				);

				$( '#rows' ).on(
					'change',
					function () {

						'use strict';

						window.DAEXTLETAL.utility.update_rows();

					}
				);

				$( '#columns' ).on(
					'change',
					function () {

						'use strict';

						window.DAEXTLETAL.utility.update_columns();
						window.DAEXTLETAL.utility.update_order_by();

					}
				);

				$( document.body ).on(
					'click',
					'.update-reset-cell-properties' ,
					function () {

						'use strict';

						const element_id = $( this ).attr( 'id' );

						window.DAEXTLETAL.utility.update_reset_cell_properties( element_id );

					}
				);

				$( document.body ).on(
					'click',
					'.group-trigger' ,
					function () {

						'use strict';

						// Open and close the various sections of the tables area.
						const target = $( this ).attr( 'data-trigger-target' );
						$( '.' + target ).toggle();
						$( this ).find( '.expand-icon' ).toggleClass( 'arrow-down' );

					}
				);

				jQuery( window ).on(
					'resize',
					function () {

						'use strict';

						window.DAEXTLETAL.utility.responsive_sidebar_container();

					}
				);

				$(
					function () {

						'use strict';

						$( '.dialog-alert' ).dialog(
							{
								autoOpen: false,
								resizable: false,
								height: 'auto',
								width: 340,
								modal: true,
								buttons: [
								{
									tabIndex: -1,
									text: 'Close',
									click: function () {

										'use strict';

										$( this ).dialog( 'close' );

									},
								},
								],
							}
						);
					}
				);

				// Dialog Confirm -------------------------------------------------------------------------------------.
				$(
					function () {

						'use strict';

						$( '#dialog-confirm' ).dialog(
							{
								autoOpen: false,
								resizable: false,
								height: 'auto',
								width: 340,
								modal: true,
								buttons: {
									[objectL10n.delete]: function () {

										'use strict';

										$( '#form-delete-' + window.DAEXTLETAL.states.tableToDelete ).submit();

									},
									[objectL10n.cancel]: function () {

										'use strict';

										$( this ).dialog( 'close' );

									},
								},
							}
						);

					}
				);

				// Click event handler on the delete button.
				$( document.body ).on(
					'click',
					'.menu-icon.delete' ,
					function () {

						'use strict';

						event.preventDefault();
						window.DAEXTLETAL.states.tableToDelete = $( this ).prev().val();
						$( '#dialog-confirm' ).dialog( 'open' );

					}
				);

			}
		);

	}

}(window.jQuery));