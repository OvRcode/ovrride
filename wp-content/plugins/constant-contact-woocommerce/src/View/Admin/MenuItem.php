<?php
/**
 * Adds a the Constant Contact menu item to the WooCommerce menu.
 *
 * @since 2019-04-16
 * @package ccforwoo-view-admin
 */

namespace WebDevStudios\CCForWoo\View\Admin;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * MenuItem Class
 *
 * @since 2019-04-16
 * @version 0.0.1
 */
class MenuItem extends Service {
	/**
	 * Register WP hooks.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function register_hooks() {
		add_action( 'admin_menu', [ $this, 'add_cc_woo_admin_submenu' ], 100 );
		add_action( 'admin_menu', [ $this, 'add_cc_woo_admin_menu' ], 100 );
	}

	/**
	 * Add the CC Woo Submenu Item.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function add_cc_woo_admin_submenu() {
		add_submenu_page(
			'woocommerce',
			esc_html__( 'Constant Contact', 'ccwoo' ),
			esc_html__( 'Constant Contact', 'ccwoo' ),
			'manage_woocommerce',
			'cc-woo-settings',
			[ $this, 'redirect_to_cc_woo' ]
		);
	}

	/**
	 * Add the CC Woo Menu Item.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function add_cc_woo_admin_menu() {
		add_menu_page(
			esc_html__( 'Constant Contact', 'ccwoo' ),
			esc_html__( 'Constant Contact', 'ccwoo' ),
			'manage_woocommerce',
			'cc-woo-settings',
			[ $this, 'redirect_to_cc_woo' ],
			'dashicons-email',
			56
		);
	}

	/**
	 * Redirect the user to the CC-Woo options page.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function redirect_to_cc_woo() {
		wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=cc_woo' ) );
		exit;
	}
}
