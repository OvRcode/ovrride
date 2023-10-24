<?php
/**
 * Management for plugin settings
 *
 * @package Drip_Woocommerce
 */

defined( 'ABSPATH' ) || die( 'Executing outside of the WordPress context.' );

/**
 * Management for plugin settings
 */
class Drip_Woocommerce_Settings {
	const NAME                         = 'woocommerce-settings-drip';
	const ACCOUNT_ID_KEY               = 'account_id';
	const MARKETING_CONFIG_KEY         = 'drip_enable_signup';
	const MARKETING_CONFIG_TEXT        = 'drip_signup_text';
	const DEFAULT_MARKETING_CONFIG_KEY = 'drip_enable_signup_default';

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 */
	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_settings_drip', __CLASS__ . '::settings_tab' );
		add_filter( 'woocommerce_settings_groups', __CLASS__ . '::settings_group' );
		add_filter( 'woocommerce_settings-drip', __CLASS__ . '::settings_group_options' );
		add_action( 'woocommerce_update_options_settings_drip', __CLASS__ . '::settings_update' );
	}


	/**
	 * Add a new settings tab to the WooCommerce settings tabs array.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
	 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['settings_drip'] = __( 'Drip', self::NAME );
		return $settings_tabs;
	}


	/**
	 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	 *
	 * @uses woocommerce_admin_fields()
	 * @uses self::get_settings()
	 */
	public static function settings_tab() {
		woocommerce_admin_fields( self::get_settings() );
	}

	/**
	 * Register the `drip` settings group.
	 *
	 * @param array $locations Locations within the settings group.
	 */
	public static function settings_group( $locations ) {
		$locations[] = array(
			'id'          => 'drip',
			'label'       => __( 'Drip', self::NAME ),
			'description' => __( 'Drip Settings', self::NAME ),
		);
		return $locations;
	}

	/**
	 * Register options under `drip` settings group.
	 *
	 * @param array $settings Settings group to register.
	 */
	public static function settings_group_options( $settings ) {
		$settings[] = array(
			'id'          => self::ACCOUNT_ID_KEY,
			'option_key'  => self::ACCOUNT_ID_KEY,
			'label'       => __( 'Account ID', self::NAME ),
			'description' => __( 'Drip Account ID', self::NAME ),
			'default'     => '',
			'type'        => 'number',
		);

		$settings[] = array(
			'id'          => self::MARKETING_CONFIG_KEY,
			'option_key'  => self::MARKETING_CONFIG_KEY,
			'label'       => __( 'Email Marketing', self::NAME ),
			'description' => __( 'If checked, includes an sign up option during checkout.', self::NAME ),
			'default'     => 'yes',
			'type'        => 'checkbox',
		);

		$settings[] = array(
			'id'          => self::DEFAULT_MARKETING_CONFIG_KEY,
			'option_key'  => self::DEFAULT_MARKETING_CONFIG_KEY,
			'label'       => __( 'Email Marketing checked by default', self::NAME ),
			'description' => __( 'If checked, includes an sign up option during checkout that is checked by default.', self::NAME ),
			'default'     => 'no',
			'type'        => 'checkbox',
		);

		$settings[] = array(
			'id'          => self::MARKETING_CONFIG_TEXT,
			'option_key'  => self::MARKETING_CONFIG_TEXT,
			'label'       => __( 'Default Text', self::NAME ),
			'description' => __( 'The text displayed next to the subscription checkbox.', self::NAME ),
			'default'     => __( 'Send me news, announcements, and discounts.', self::NAME ),
			'type'        => 'text',
		);
		return $settings;
	}

	/**
	 * Persists the settings updated to wp options
	 */
	public static function settings_update() {
		woocommerce_update_options( self::get_settings() );
	}

	/**
	 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
	 *
	 * @return array Array of settings for @see woocommerce_admin_fields() function.
	 */
	public static function get_settings() {
		$drip_settings = new Drip_Woocommerce_Settings();
		$settings      = array(
			'section_title'                    => array(
				'id'   => 'wc_settings_drip_section_title',
				'name' => __( 'Drip', self::NAME ),
				'type' => 'title',
				'desc' => '',
			),
			self::ACCOUNT_ID_KEY               => array(
				'id'                => self::ACCOUNT_ID_KEY,
				'name'              => __( 'Account ID', self::NAME ),
				'type'              => 'number',
				'desc'              => __( 'Drip Account ID is populated when your store is successfully connected to Drip.', self::NAME ),
				'custom_attributes' => array( 'readonly' => 'readonly' ),
			),
			self::MARKETING_CONFIG_KEY         => array(
				'id'                => self::MARKETING_CONFIG_KEY,
				'name'              => __( 'Email Marketing', self::NAME ),
				'type'              => 'checkbox',
				'desc'              => __( 'Show a sign up option at checkout.', self::NAME ),
				'default'           => 'yes',
				'custom_attributes' => $drip_settings->custom_attributes(),
			),
			self::DEFAULT_MARKETING_CONFIG_KEY => array(
				'id'                => self::DEFAULT_MARKETING_CONFIG_KEY,
				'name'              => __( 'Pre-Select Email Marketing Checkbox', self::NAME ),
				'type'              => 'checkbox',
				'desc'              => __( '<a href="https://my.drip.com/search?query=gdpr" target="_blank">Selecting may have GDPR implications</a>', self::NAME ),
				'default'           => 'no',
				'custom_attributes' => $drip_settings->custom_attributes(),
			),

			self::MARKETING_CONFIG_TEXT        => array(
				'id'                => self::MARKETING_CONFIG_TEXT,
				'name'              => __( 'Default Text', self::NAME ),
				'type'              => 'text',
				'desc'              => __( 'Text that will appear next to the sign up checkbox', self::NAME ),
				'default'           => __( 'Send me news, announcements, and discounts.', self::NAME ),
				'custom_attributes' => $drip_settings->custom_attributes(),
			),
			'section_end'                      => array(
				'type' => 'sectionend',
				'id'   => 'wc_settings_drip_section_end',
			),
		);

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		return apply_filters( 'wc_settings_drip_settings', $settings );
	}

	/**
	 * Return an array for custom_attributes based on a successful integration
	 *
	 * @return array for custom_attributes
	 */
	private function custom_attributes() {
		if ( $this->is_integrated() ) {
			return array();
		} else {
			return array(
				'disabled' => 'disabled',
				'readonly' => 'readonly',
			);
		}
	}

	/**
	 * Returns a boolean value indicating if the Drip account is integrated or not
	 *
	 * @return bool
	 */
	private function is_integrated() {
		// this only works because at this point we're in a callback from a woocommerce
		// action, so WC_Admin_Settings has been initialized...........................
		return (bool) WC_Admin_Settings::get_option( self::ACCOUNT_ID_KEY );
	}
}
