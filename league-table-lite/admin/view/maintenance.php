<?php
/**
 * The file used to display the "Maintenance" menu in the admin area.
 *
 * @package league-table-lite
 */

$this->menu_elements->capability = get_option( $this->shared->get( 'slug' ) . '_maintenance_menu_capability' );
$this->menu_elements->context    = null;
$this->menu_elements->display_menu_content();
