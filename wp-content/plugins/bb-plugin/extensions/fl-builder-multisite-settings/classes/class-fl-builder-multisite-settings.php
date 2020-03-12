<?php

/**
 * Network admin settings for the page builder.
 *
 * @since 1.0
 */
final class FLBuilderMultisiteSettings {

	/**
	 * Initializes the network admin settings page for multisite installs.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init() {
		add_action( 'admin_init', __CLASS__ . '::admin_init' );
		add_action( 'network_admin_menu', __CLASS__ . '::menu' );
		add_filter( 'fl_builder_activate_redirect_url', __CLASS__ . '::activate_redirect_url' );
	}

	/**
	 * Sets the activate redirect url to the network admin settings.
	 *
	 * @since 1.8
	 * @return string
	 */
	static public function activate_redirect_url( $url ) {
		if ( current_user_can( 'manage_network_plugins' ) ) {
			return network_admin_url( '/settings.php?page=fl-builder-multisite-settings#license' );
		}

		return $url;
	}

	/**
	 * Save network admin settings and enqueue scripts.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function admin_init() {
		if ( is_network_admin() && isset( $_REQUEST['page'] ) && 'fl-builder-multisite-settings' == $_REQUEST['page'] ) {
			add_action( 'admin_enqueue_scripts', 'FLBuilderAdminSettings::styles_scripts' );
			FLBuilderAdminSettings::save();
		}
	}

	/**
	 * Renders the network admin menu for multisite installs.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function menu() {
		$title = FLBuilderModel::get_branding();
		$cap   = 'manage_network_plugins';
		$slug  = 'fl-builder-multisite-settings';
		$func  = 'FLBuilderAdminSettings::render';

		add_submenu_page( 'settings.php', $title, $title, $cap, $slug, $func );
	}
}

FLBuilderMultisiteSettings::init();
