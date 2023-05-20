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

use WooCommerce\Facebook\Admin\Abstract_Settings_Screen;
use WooCommerce\Facebook\Framework\Api\Exception as ApiException;
use WooCommerce\Facebook\Framework\Helper;
use WooCommerce\Facebook\Framework\Plugin\Exception as PluginException;
use WooCommerce\Facebook\Locale;

/**
 * The Messenger settings screen object.
 */
class Messenger extends Abstract_Settings_Screen {


	/** @var string screen ID */
	const ID = 'messenger';


	/**
	 * Connection constructor.
	 */
	public function __construct() {
		$this->id    = self::ID;
		$this->label = __( 'Messenger', 'facebook-for-woocommerce' );
		$this->title = __( 'Messenger', 'facebook-for-woocommerce' );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'woocommerce_admin_field_messenger_locale', array( $this, 'render_locale_field' ) );
		add_action( 'woocommerce_admin_field_messenger_greeting', array( $this, 'render_greeting_field' ) );
	}


	/**
	 * Enqueues the assets.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function enqueue_assets() {
		// TODO: empty for now, until we add more robust Messenger settings {CW 2020-06-17}
	}


	/**
	 * Renders the custom locale field.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $field field data
	 */
	public function render_locale_field( $field ) {

		$configured_locale = get_option( \WC_Facebookcommerce_Integration::SETTING_MESSENGER_LOCALE, '' );;
		$supported_locales = Locale::get_supported_locales();
		if ( ! empty( $supported_locales[ $configured_locale ] ) ) {
			$configured_locale = $supported_locales[ $configured_locale ];
		}
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
				<p>
					<?php echo esc_html( $configured_locale ); ?>
				</p>
			</td>
		</tr>
		<?php
	}


	/**
	 * Renders the custom greeting field.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $field field data
	 */
	public function render_greeting_field( $field ) {
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
				<p>
					<?php
					printf(
						/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
						esc_html__( '%1$sClick here%2$s to manage your Messenger greeting and colors.', 'facebook-for-woocommerce' ),
						'<a href="' . esc_url( facebook_for_woocommerce()->get_connection_handler()->get_manage_url() ) . '" target="_blank">',
						'</a>'
					);
					?>
				</p>
			</td>
		</tr>
		<?php
	}


	/**
	 * Gets the screen settings.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_settings() {
		$is_enabled = get_option( \WC_Facebookcommerce_Integration::SETTING_ENABLE_MESSENGER, 'no' );
		$settings = array(
			array(
				'title' => __( 'Messenger', 'facebook-for-woocommerce' ),
				'type'  => 'title',
			),
			array(
				'id'      => \WC_Facebookcommerce_Integration::SETTING_ENABLE_MESSENGER,
				'title'   => __( 'Enable Messenger', 'facebook-for-woocommerce' ),
				'type'    => 'checkbox',
				'desc'    => __( 'Enable and customize Facebook Messenger on your store', 'facebook-for-woocommerce' ),
				'default' => 'no',
				'value'   => $is_enabled,
			),
		);
		// only add the static configuration display if messenger is enabled
		if ( 'yes' === $is_enabled ) {
			$settings[] = array(
				'title' => __( 'Language', 'facebook-for-woocommerce' ),
				'type'  => 'messenger_locale',
			);
			$settings[] = array(
				'title' => __( 'Greeting & Colors', 'facebook-for-woocommerce' ),
				'type'  => 'messenger_greeting',
			);
		}
		$settings[] = array( 'type' => 'sectionend' );
		return $settings;
	}


	/**
	 * Gets the "disconnected" message.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_disconnected_message() {
		return sprintf(
			/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
			__( 'Please %1$sconnect to Facebook%2$s to enable and manage Facebook Messenger.', 'facebook-for-woocommerce' ),
			'<a href="' . esc_url( facebook_for_woocommerce()->get_connection_handler()->get_connect_url() ) . '">',
			'</a>'
		);
	}

	/**
	 * Saves the settings.
	 *
	 * This is overridden to pull the latest from FBE and update that remotely via API
	 *
	 * @since 2.0.0
	 *
	 * @throws PluginException
	 */
	public function save() {
		$plugin               = facebook_for_woocommerce();
		$external_business_id = $plugin->get_connection_handler()->get_external_business_id();
		try {
			// first get the latest configuration details
			$response = $plugin->get_api()->get_business_configuration( $external_business_id );
			$configuration = $response->get_messenger_configuration();
			if ( ! $configuration ) {
				throw new ApiException( 'Could not retrieve latest messenger configuration' );
			}
			$update          = false;
			$setting_enabled = wc_string_to_bool( Helper::get_posted_value( \WC_Facebookcommerce_Integration::SETTING_ENABLE_MESSENGER ) );
			// only consider updating if the setting has changed
			if ( $setting_enabled !== $configuration->is_enabled() ) {
				$update = true;
			}
			// also consider updating if the site's URL was removed from approved URLs
			if ( ! in_array( home_url( '/' ), $configuration->get_domains(), true ) ) {
				$update = true;
			}
			// make the API call if settings have changed
			if ( $update ) {
				$configuration->set_enabled( $setting_enabled );
				$configuration->add_domain( home_url( '/' ) );

				try {
					$plugin->get_api()->update_messenger_configuration( $external_business_id, $configuration );
					update_option( \WC_Facebookcommerce_Integration::SETTING_ENABLE_MESSENGER, wc_bool_to_string( $configuration->is_enabled() ) );
					if ( $default_locale = $configuration->get_default_locale() ) {
						update_option( \WC_Facebookcommerce_Integration::SETTING_MESSENGER_LOCALE, $default_locale );
					}
				} catch ( ApiException $exception ) {
					// always log this error, regardless of debug setting
					$plugin->log( 'Could not display messenger settings. ' . $exception->getMessage() );
				}

				delete_transient( 'wc_facebook_business_configuration_refresh' );
			}
			// save any real settings
			parent::save();
		} catch ( ApiException $exception ) {
			// always log this error, regardless of debug setting
			$plugin->log( 'Could not update remote messenger settings. ' . $exception->getMessage() );
			throw new PluginException( __( 'Please try again.', 'facebook-for-woocommerce' ) );
		}
	}
}
