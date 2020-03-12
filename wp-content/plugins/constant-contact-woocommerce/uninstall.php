<?php
/**
 * Handle plugin uninstall processes.
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Database
 * @since   2019-10-10
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

/**
 * Delete abandoned carts table.
 *
 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @since  2019-10-10
 */
function cc_delete_abandoned_carts_table() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'cc_abandoned_carts';
	$wpdb->query(
		//@codingStandardsIgnoreStart
		"DROP TABLE IF EXISTS {$table_name}"
		//@codingStandardsIgnoreEnd
	);

	delete_option( 'cc_abandoned_carts_db_version' );
}

cc_delete_abandoned_carts_table();
