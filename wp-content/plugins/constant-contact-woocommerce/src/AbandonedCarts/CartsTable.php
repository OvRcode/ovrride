<?php
/**
 * Class to handle creation of abandoned carts table.
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\AbandonedCarts
 * @since   1.2.0
 */

namespace WebDevStudios\CCForWoo\AbandonedCarts;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class CartsTable
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\AbandonedCarts
 * @since   1.2.0
 */
class CartsTable extends Service {

	/**
	 * Current version of abandoned carts table.
	 *
	 * @since 1.2.0
	 */
	const DB_VERSION = '1.4';

	/**
	 * Option name for abandoned carts db version.
	 *
	 * @since 1.2.0
	 */
	const DB_VERSION_OPTION_NAME = 'cc_abandoned_carts_db_version';

	/**
	 * Abandoned carts table name.
	 *
	 * @since 1.2.0
	 */
	const TABLE_NAME = 'cc_abandoned_carts';

	/**
	 * Register hooks with WordPress.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 */
	public function register_hooks() {
		add_action( 'admin_init', [ $this, 'update_db_check' ] );
	}

	/**
	 * Create abandoned carts table.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 */
	public function create_table() {
		global $wpdb;

		$table_name = self::get_table_name();

		$sql = "CREATE TABLE {$table_name} (
			cart_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL DEFAULT 0,
			user_email varchar(200) NOT NULL DEFAULT '',
			cart_contents longtext NOT NULL,
			cart_updated datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			cart_updated_ts int(11) unsigned NOT NULL DEFAULT 0,
			cart_created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			cart_created_ts int(11) unsigned NOT NULL DEFAULT 0,
			cart_hash char(32) NOT NULL DEFAULT '',
			PRIMARY KEY (cart_id),
			UNIQUE KEY cart_hash (cart_hash)
		) {$wpdb->get_charset_collate()}";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		add_option( self::DB_VERSION_OPTION_NAME, self::DB_VERSION );
	}

	/**
	 * Update abandoned carts table.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 */
	protected function update_table() {
		global $wpdb;

		$table_name = self::get_table_name();

		// phpcs:disable WordPress.DB.PreparedSQL -- Okay use of unprepared variable for table name in SQL.
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) ) {

			// Update `cart_hash` field for all records in versions older than 1.4.
			if ( floatval( self::DB_VERSION_OPTION_NAME ) < 1.4 ) {
				$wpdb->query(
					"UPDATE {$table_name}
					SET cart_hash = HEX(cart_hash)"
				);
			}

			// Any data updates would be performed here.
			update_option( self::DB_VERSION_OPTION_NAME, self::DB_VERSION );
		}
		// phpcs:enable
	}

	/**
	 * Check if table exists and is up-to-date.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 */
	public function update_db_check() {
		if ( self::DB_VERSION !== get_site_option( self::DB_VERSION_OPTION_NAME ) ) {
			$this->create_table();
			$this->update_table();
		}
	}

	/**
	 * A simple utility for grabbing the full table name, including the WPDB table prefix.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 *
	 * @return string
	 */
	public static function get_table_name() : string {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_NAME;
	}
}
