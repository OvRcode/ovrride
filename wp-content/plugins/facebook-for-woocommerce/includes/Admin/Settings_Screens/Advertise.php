<?php
// phpcs:ignoreFile
/**
 * Copyright (c) Facebook, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package FacebookCommerce
 */

namespace WooCommerce\Facebook\Admin\Settings_Screens;

defined( 'ABSPATH' ) or exit;

use WooCommerce\Facebook\API;
use WooCommerce\Facebook\Locale;
use WooCommerce\Facebook\Admin\Abstract_Settings_Screen;

/**
 * The Advertise settings screen object.
 */
class Advertise extends Abstract_Settings_Screen {

	/** @var string screen ID */
	const ID = 'advertise';

	/**
	 * Advertise settings constructor.
	 *
	 * @since 2.2.0
	 */
	public function __construct() {
		$this->id    = self::ID;
		$this->label = __( 'Advertise', 'facebook-for-woocommerce' );
		$this->title = __( 'Advertise', 'facebook-for-woocommerce' );

		$this->add_hooks();
	}


	/**
	 * Adds hooks.
	 *
	 * @since 2.2.0
	 */
	private function add_hooks() {
		add_action( 'admin_head', array( $this, 'output_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}


	/**
	 * Enqueues assets for the current screen.
	 *
	 * @internal
	 *
	 * @since 2.2.0
	 */
	public function enqueue_assets() {
		if ( ! $this->is_current_screen_page() ) {
			return;
		}
		wp_enqueue_style( 'wc-facebook-admin-advertise-settings', facebook_for_woocommerce()->get_plugin_url() . '/assets/css/admin/facebook-for-woocommerce-advertise.css', array(), \WC_Facebookcommerce::VERSION );
	}


	/**
	 * Outputs the LWI Ads script.
	 *
	 * @internal
	 *
	 * @since 2.1.0-dev.1
	 */
	public function output_scripts() {
		$connection_handler = facebook_for_woocommerce()->get_connection_handler();
		if ( ! $connection_handler || ! $connection_handler->is_connected() || ! $this->is_current_screen_page() ) {
			return;
		}

		?>
		<script>
			window.fbAsyncInit = function() {
				FB.init( {
					appId            : '<?php echo esc_js( $connection_handler->get_client_id() ); ?>',
					autoLogAppEvents : true,
					xfbml            : true,
					version          : '<?php echo esc_js( API::API_VERSION )?>',
				} );
			};
		</script>
		<?php
	}


	/**
	 * Gets the LWI Ads configuration to output the FB iframes.
	 *
	 * @since 2.2.0
	 *
	 * @return array
	 */
	private function get_lwi_ads_configuration_data() {

		$connection_handler = facebook_for_woocommerce()->get_connection_handler();

		if ( ! $connection_handler || ! $connection_handler->is_connected() ) {
			return array();
		}

		return array(
			'business_config' => array(
				'business' => array(
					'name' => $connection_handler->get_business_name(),
				),
			),
			'setup'           => array(
				'external_business_id' => $connection_handler->get_external_business_id(),
				'timezone'             => $this->parse_timezone( wc_timezone_string(), wc_timezone_offset() ),
				'currency'             => get_woocommerce_currency(),
				'business_vertical'    => 'ECOMMERCE',
			),
			'repeat'          => false,
		);
	}


	/*
	 * Converts the given timezone string to a name if needed.
	 *
	 * @since 2.2.0
	 *
	 * @param string $timezone_string Timezone string
	 * @param int|float $timezone_offset Timezone offset
	 * @return string timezone string
	 */
	private function parse_timezone( $timezone_string, $timezone_offset = 0 ) {

		// no need to look for the equivalent timezone
		if ( false !== strpos( $timezone_string, '/' ) ) {
			return $timezone_string;
		}

		// look up the timezones list based on the given offset
		$timezones_list = timezone_abbreviations_list();

		foreach ( $timezones_list as $timezone ) {
			foreach ( $timezone as $city ) {
				if ( isset( $city['offset'], $city['timezone_id'] ) && (int) $city['offset'] === (int) $timezone_offset ) {
					return $city['timezone_id'];
				}
			}
		}

		// fallback to default timezone
		return 'Etc/GMT';
	}


	/**
	 * Gets the LWI Ads SDK URL.
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */
	private function get_lwi_ads_sdk_url() {

		$locale = get_user_locale();

		if ( ! Locale::is_supported_locale( $locale ) ) {
			$locale = Locale::DEFAULT_LOCALE;
		}

		return "https://connect.facebook.net/{$locale}/sdk.js";
	}


	/**
	 * Renders the screen HTML.
	 *
	 * The contents of the Facebook box will be populated by the LWI Ads script through iframes.
	 *
	 * @since 2.2.0
	 */
	public function render() {

		$connection_handler = facebook_for_woocommerce()->get_connection_handler();

		if ( ! $connection_handler || ! $connection_handler->is_connected() ) {

			printf(
				/* translators: Placeholders: %1$s - opening <a> HTML link tag, %2$s - closing </a> HTML link tag */
				esc_html__( 'Please %1$sconnect your store%2$s to Facebook to create ads.', 'facebook-for-woocommerce' ),
				'<a href="' . esc_url( add_query_arg( array( 'tab' => Connection::ID ), facebook_for_woocommerce()->get_settings_url() ) ) . '">',
				'</a>'
			);

			return;
		}

		$fbe_extras = wp_json_encode( $this->get_lwi_ads_configuration_data() );

		?>
		<script async defer src="<?php echo esc_url( $this->get_lwi_ads_sdk_url() ); ?>"></script>
		<div
			class="fb-lwi-ads-creation"
			data-hide-manage-button="true"
			data-fbe-extras="<?php echo esc_attr( $fbe_extras ); ?>"
			data-fbe-scopes="manage_business_extension"
			data-fbe-redirect-uri="https://mariner9.s3.amazonaws.com/"
			data-title="<?php esc_attr_e( 'If you are connected to Facebook but cannot display ads, please contact Facebook support.', 'facebook-for-woocommerce' ); ?>"></div>
		<div
			class="fb-lwi-ads-insights"
			data-fbe-extras="<?php echo esc_attr( $fbe_extras ); ?>"
			data-fbe-scopes="manage_business_extension"
			data-fbe-redirect-uri="https://mariner9.s3.amazonaws.com/"></div>
		<?php

		parent::render();
	}


	/**
	 * Gets the screen settings.
	 *
	 * @since 2.2.0
	 *
	 * @return array
	 */
	public function get_settings() {
		return array();
	}
}
