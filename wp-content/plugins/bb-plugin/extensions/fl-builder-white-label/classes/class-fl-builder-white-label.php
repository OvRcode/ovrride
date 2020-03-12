<?php

/**
 * White labeling for the builder.
 *
 * @since 1.8
 */
final class FLBuilderWhiteLabel {

	/**
	 * @return void
	 */
	static public function init() {
		add_filter( 'all_plugins', __CLASS__ . '::plugins_page' );
		add_filter( 'wp_prepare_themes_for_js', __CLASS__ . '::themes_page' );
		add_filter( 'all_themes', __CLASS__ . '::network_themes_page' );
		add_filter( 'update_right_now_text', __CLASS__ . '::admin_dashboard_page' );
		add_filter( 'gettext', __CLASS__ . '::theme_gettext', 10, 3 );
		add_filter( 'gettext', __CLASS__ . '::plugin_gettext', 10, 3 );
		add_filter( 'fl_plugin_info_data', __CLASS__ . '::fl_plugin_info', 10, 2 );
		add_action( 'customize_render_section', __CLASS__ . '::theme_customizer' );
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::updates_core' );
		add_filter( 'fl_updater_icon', __CLASS__ . '::update_icon_branding', 11, 3 );

		if ( is_admin() && isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], array( 'fl-builder-settings', 'fl-builder-multisite-settings' ) ) ) {
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::enqueue_scripts' );
			add_filter( 'fl_builder_admin_settings_nav_items', __CLASS__ . '::admin_settings_nav_items' );
			add_action( 'fl_builder_admin_settings_render_forms', __CLASS__ . '::admin_settings_render_forms' );
			add_action( 'fl_builder_admin_settings_save', __CLASS__ . '::save_branding_settings' );
			add_action( 'fl_builder_admin_settings_save', __CLASS__ . '::save_help_button_settings' );
		}

	}

	/**
	 * Enqueue white label settings scripts and styles.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function enqueue_scripts() {
		// Styles
		wp_enqueue_style( 'fl-builder-white-label-settings', FL_BUILDER_WHITE_LABEL_URL . 'css/fl-builder-white-label-settings.css', array(), FL_BUILDER_VERSION );

		// Scripts
		wp_enqueue_script( 'fl-builder-white-label-settings', FL_BUILDER_WHITE_LABEL_URL . 'js/fl-builder-white-label-settings.js', array(), FL_BUILDER_VERSION );
	}

	/**
	 * Adds the white label nav items to the admin settings.
	 *
	 * @since 1.8
	 * @param array $nav_items
	 * @return array
	 */
	static public function admin_settings_nav_items( $nav_items ) {
		$nav_items['branding'] = array(
			'title'    => __( 'Branding', 'fl-builder' ),
			'show'     => is_network_admin() || ! FLBuilderAdminSettings::multisite_support(),
			'priority' => 650,
		);

		$nav_items['help-button'] = array(
			'title'    => __( 'Help Button', 'fl-builder' ),
			'show'     => is_network_admin() || ! FLBuilderAdminSettings::multisite_support(),
			'priority' => 651,
		);

		return $nav_items;
	}

	/**
	 * Renders the admin settings white label forms.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function admin_settings_render_forms() {
		include FL_BUILDER_WHITE_LABEL_DIR . 'includes/admin-settings-branding.php';
		include FL_BUILDER_WHITE_LABEL_DIR . 'includes/admin-settings-help-button.php';
	}

	/**
	 * Saves the branding settings.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	static public function save_branding_settings() {
		if ( isset( $_POST['fl-branding-nonce'] ) && wp_verify_nonce( $_POST['fl-branding-nonce'], 'branding' ) ) {

			// Get the plugin branding data.
			$branding      = wp_kses_post( $_POST['fl-branding'] );
			$branding_icon = sanitize_text_field( $_POST['fl-branding-icon'] );

			// Get the theme branding data.
			$theme_data = array(
				'name'           => wp_kses_post( $_POST['fl-theme-branding-name'] ),
				'description'    => wp_kses_post( $_POST['fl-theme-branding-description'] ),
				'company_name'   => wp_kses_post( $_POST['fl-theme-branding-company-name'] ),
				'company_url'    => sanitize_text_field( $_POST['fl-theme-branding-company-url'] ),
				'screenshot_url' => sanitize_text_field( $_POST['fl-theme-branding-screenshot-url'] ),
			);

			// Save the data.
			FLBuilderModel::update_admin_settings_option( '_fl_builder_branding', $branding, false );
			FLBuilderModel::update_admin_settings_option( '_fl_builder_branding_icon', $branding_icon, false );
			FLBuilderModel::update_admin_settings_option( '_fl_builder_theme_branding', $theme_data, false );
		}
	}

	/**
	 * Checks if white label is enabled. Will check for the default branding
	 * and Page Builder since Page Builder was the original default.
	 *
	 * @since 2.1
	 * @return bool
	 */
	static public function is_white_labeled() {
		$defaults = array(
			self::get_default_branding(),
		);

		$is_white_labeled = ! in_array( self::get_branding(), $defaults );

		return apply_filters( 'fl_builder_is_white_labeled', $is_white_labeled );
	}

	/**
	 * Returns the default branding.
	 *
	 * @since 2.1
	 * @return string
	 */
	static public function get_default_branding() {
		return apply_filters( 'fl_builder_default_branding', __( 'Beaver Builder', 'fl-builder' ) );
	}

	/**
	 * Returns the custom branding string.
	 *
	 * @since 1.3.1
	 * @return string
	 */
	static public function get_branding() {
		$value = FLBuilderModel::get_admin_settings_option( '_fl_builder_branding', false );

		return ! $value ? self::get_default_branding() : stripcslashes( $value );
	}

	/**
	 * Returns the default branding icon.
	 *
	 * @since 2.1
	 * @return string
	 */
	static public function get_default_branding_icon() {
		return apply_filters( 'fl_builder_default_branding_icon', FL_BUILDER_URL . 'img/beaver.png' );
	}

	/**
	 * Returns the custom branding icon URL.
	 *
	 * @since 1.3.7
	 * @return string
	 */
	static public function get_branding_icon() {
		$value = FLBuilderModel::get_admin_settings_option( '_fl_builder_branding_icon', false );

		return false === $value ? self::get_default_branding_icon() : $value;
	}

	/**
	 * Returns the custom branding data for the builder theme.
	 *
	 * @since 1.6.4.3
	 * @return array
	 */
	static public function get_theme_branding() {
		$value    = FLBuilderModel::get_admin_settings_option( '_fl_builder_theme_branding', false );
		$defaults = array(
			'name'           => '',
			'description'    => '',
			'company_name'   => '',
			'company_url'    => '',
			'screenshot_url' => '',
		);

		$value = wp_parse_args( $value, $defaults );

		foreach ( $value as $k => $v ) {
			$value[ $k ] = wp_specialchars_decode( stripslashes( wp_strip_all_tags( $v ) ) );
		}
		return $value;
	}

	/**
	 * Saves the help button settings.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	static public function save_help_button_settings() {
		if ( isset( $_POST['fl-help-button-nonce'] ) && wp_verify_nonce( $_POST['fl-help-button-nonce'], 'help-button' ) ) {

			$settings                   = FLBuilderModel::get_help_button_defaults();
			$settings['enabled']        = isset( $_POST['fl-help-button-enabled'] ) ? true : false;
			$settings['tour']           = isset( $_POST['fl-help-tour-enabled'] ) ? true : false;
			$settings['video']          = isset( $_POST['fl-help-video-enabled'] ) ? true : false;
			$settings['knowledge_base'] = isset( $_POST['fl-knowledge-base-enabled'] ) ? true : false;
			$settings['forums']         = isset( $_POST['fl-forums-enabled'] ) ? true : false;

			// Disable everything if the main button is disabled.
			if ( ! $settings['enabled'] ) {
				$settings['tour']           = false;
				$settings['video']          = false;
				$settings['knowledge_base'] = false;
				$settings['forums']         = false;
			}

			// Clean the video embed.
			$video_embed = wp_kses( $_POST['fl-help-video-embed'], array(
				'iframe' => array(
					'src'                   => array(),
					'frameborder'           => array(),
					'webkitallowfullscreen' => array(),
					'mozallowfullscreen'    => array(),
					'allowfullscreen'       => array(),
				),
			));

			// Save the video embed.
			if ( ! empty( $video_embed ) && ! stristr( $video_embed, 'iframe' ) ) {
				FLBuilderAdminSettings::add_error( __( 'Error! Please enter an iframe for the video embed code.', 'fl-builder' ) );
			} elseif ( ! empty( $video_embed ) ) {
				$settings['video_embed'] = $video_embed;
			}

			// Save the knowledge base URL.
			if ( ! empty( $_POST['fl-knowledge-base-url'] ) ) {
				$settings['knowledge_base_url'] = sanitize_text_field( $_POST['fl-knowledge-base-url'] );
			}

			// Save the forums URL.
			if ( ! empty( $_POST['fl-forums-url'] ) ) {
				$settings['forums_url'] = sanitize_text_field( $_POST['fl-forums-url'] );
			}

			// Make sure we have at least one help feature enabled.
			if ( $settings['enabled'] && ! $settings['tour'] && ! $settings['video'] && ! $settings['knowledge_base'] && ! $settings['forums'] ) {
				FLBuilderAdminSettings::add_error( __( 'Error! You must have at least one feature of the help button enabled.', 'fl-builder' ) );
				return;
			}

			FLBuilderModel::update_admin_settings_option( '_fl_builder_help_button', $settings, false );
		}
	}

	/**
	 * Returns the settings for the builder's help button.
	 *
	 * @since 1.4.9
	 * @return array
	 */
	static public function get_help_button_settings() {
		$value    = FLBuilderModel::get_admin_settings_option( '_fl_builder_help_button', false );
		$defaults = apply_filters( 'fl_builder_help_button_defaults', FLBuilderModel::get_help_button_defaults() );

		return false === $value ? $defaults : $value;
	}

	/**
	 * White labels the builder on the plugins page.
	 *
	 * @since 1.0
	 * @param array $plugins An array data for each plugin.
	 * @return array
	 */
	static public function plugins_page( $plugins ) {
		$branding = self::get_branding();
		$key      = FLBuilderModel::plugin_basename();

		if ( isset( $plugins[ $key ] ) && $branding !== $plugins[ $key ]['Name'] && self::is_white_labeled() ) {
			$plugins[ $key ]['Name']       = $branding;
			$plugins[ $key ]['Title']      = $branding;
			$plugins[ $key ]['Author']     = '';
			$plugins[ $key ]['AuthorName'] = '';
			$plugins[ $key ]['PluginURI']  = '';
		}

		return $plugins;
	}

	/**
	 * White labels the builder theme on the themes page.
	 *
	 * @since 1.6.4.3
	 * @param array $themes An array data for each theme.
	 * @return array
	 */
	static public function themes_page( $themes ) {
		$theme_data = self::get_theme_branding();

		// we might be using a child theme an bb-theme might be disabled on multisite
		if ( ! empty( $theme_data['name'] ) ) {
			foreach ( $themes as $theme_key => $theme ) {
				if ( isset( $theme['parent'] ) && 'Beaver Builder Theme' == $theme['parent'] ) {
					$themes[ $theme_key ]['parent'] = $theme_data['name'];
				}
			}
		}

		if ( isset( $themes['bb-theme'] ) ) {
			if ( ! empty( $theme_data['name'] ) ) {
				$themes['bb-theme']['name'] = $theme_data['name'];
			}
			if ( ! empty( $theme_data['description'] ) ) {
				$themes['bb-theme']['description'] = $theme_data['description'];
			}
			if ( ! empty( $theme_data['company_name'] ) ) {
				$company_url                        = empty( $theme_data['company_url'] ) ? '#' : $theme_data['company_url'];
				$themes['bb-theme']['author']       = $theme_data['company_name'];
				$themes['bb-theme']['authorAndUri'] = '<a href="' . $company_url . '">' . $theme_data['company_name'] . '</a>';
			}
			if ( ! empty( $theme_data['screenshot_url'] ) ) {
				$themes['bb-theme']['screenshot'] = array( $theme_data['screenshot_url'] );
			}
		}

		return $themes;
	}

	/**
	 * Replace branded theme screenshot in updates-core.php
	 * @since 2.0.3
	 */
	static public function updates_core() {
		global $pagenow;

		if ( 'update-core.php' == $pagenow ) {
			$theme_data = self::get_theme_branding();
			$theme_root = get_theme_root_uri();
			$default    = sprintf( '%s/bb-theme/screenshot.png', $theme_root );
			$branded    = $theme_data['screenshot_url'];
			if ( $branded ) {
				wp_add_inline_script( 'updates', "var _fl_default = '$default', _fl_branded = '$branded';jQuery('#update-themes-table .plugins tr').each(function(){img = jQuery(this).find('.updates-table-screenshot');if( img.attr('src') === _fl_default ) {img.attr('src', _fl_branded)}})" );
			}
		}
	}

	/**
	 * White labels the builder theme on the network admin themes page.
	 *
	 * @since 1.8.1
	 * @param array $themes An array data for each theme.
	 * @return array
	 */
	static public function network_themes_page( $themes ) {
		if ( isset( $themes['bb-theme'] ) && is_network_admin() ) {

			$theme_data         = self::get_theme_branding();
			$network_theme_data = array();

			if ( ! empty( $theme_data['name'] ) ) {

				$network_theme_data['Name'] = $theme_data['name'];

				foreach ( $themes as $theme_key => $theme ) {
					if ( isset( $theme['parent'] ) && 'Beaver Builder Theme' == $theme['parent'] ) {
						$themes[ $theme_key ]['parent'] = $theme_data['name'];
					}
				}
			}
			if ( ! empty( $theme_data['description'] ) ) {
				$network_theme_data['Description'] = $theme_data['description'];
			}
			if ( ! empty( $theme_data['company_name'] ) ) {
				$company_url                     = empty( $theme_data['company_url'] ) ? '#' : $theme_data['company_url'];
				$network_theme_data['Author']    = $theme_data['company_name'];
				$network_theme_data['AuthorURI'] = $company_url;
				$network_theme_data['ThemeURI']  = $company_url;
			}

			if ( count( $network_theme_data ) > 0 ) {
				$reflectionobject = new ReflectionObject( $themes['bb-theme'] );
				$headers          = $reflectionobject->getProperty( 'headers' );
				$headers->setAccessible( true );

				$headers_sanitized = $reflectionobject->getProperty( 'headers_sanitized' );
				$headers_sanitized->setAccessible( true );

				// Set white labelled theme data
				$headers->setValue( $themes['bb-theme'], $network_theme_data );
				$headers_sanitized->setValue( $themes['bb-theme'], $network_theme_data );

				// Reset back to private
				$headers->setAccessible( false );
				$headers_sanitized->setAccessible( false );
			}
		}
		return $themes;
	}

	/**
	 * White labels the builder theme on the dashboard 'At a Glance' metabox
	 *
	 * @since 1.8.2
	 * @param array $themes An array data for each theme.
	 * @return array
	 */
	static public function admin_dashboard_page( $content ) {
		$theme_data = self::get_theme_branding();

		if ( is_admin() && 'Beaver Builder Theme' == wp_get_theme() && ! empty( $theme_data['name'] ) ) {
			return sprintf( $content, get_bloginfo( 'version', 'display' ), $theme_data['name'] );
		}

		return $content;
	}

	/**
	 * White labels the builder theme using the gettext filter
	 * to cover areas that we can't access like the Customizer.
	 *
	 * @since 1.6.4.4
	 * @return string
	 */
	static public function theme_gettext( $text, $original, $domain ) {
		if ( is_admin() && 'Beaver Builder Theme' == $original ) {

			$theme_data = self::get_theme_branding();

			if ( ! empty( $theme_data['name'] ) ) {
				$text = $theme_data['name'];
			}
		}

		return $text;
	}

	/**
	 * White labels the builder plugin using the gettext filter
	 * to cover areas that we can't access like the Customizer.
	 *
	 * @since 1.10.8.2
	 * @return string
	 */
	static public function plugin_gettext( $text, $original, $domain ) {
		global $pagenow;

		if ( is_admin()
			&& 'update-core.php' == $pagenow
			&& 'Beaver Builder Plugin (Lite Version)' !== $original
			&& false !== strpos( $original, 'Beaver Builder Plugin' ) ) {
			if ( self::is_white_labeled() ) {
				$text = self::get_branding();
			}
		}
		return $text;
	}

	/**
	 * White labels the plugin update lightbox.
	 *
	 * @since 1.10.8.2
	 * @return string
	 */
	static public function fl_plugin_info( $info, $response ) {
		if ( false !== strpos( $info->name, 'Beaver Builder Plugin' ) ) {
			if ( self::is_white_labeled() ) {
				$info->name = self::get_branding();
			}
		}
		return $info;
	}

	/**
	 * White labels the builder theme using the `customize_render_section` hook
	 * to cover areas that we can't access like the Customizer.
	 *
	 * @since 1.8.4
	 * @return string   Only return if theme branding has been filled up.
	 */
	static public function theme_customizer( $instance ) {
		if ( 'Beaver Builder Theme' == $instance->title ) {

			$theme_data = self::get_theme_branding();

			if ( isset( $theme_data['name'] ) && ! empty( $theme_data['name'] ) ) {
				// @codingStandardsIgnoreLine
				return $instance->title = $theme_data['name'];
			}
		}
	}

	/**
	 * White label the plugin icons on core-updates page.
	 * @since 2.0.5
	 */
	static public function update_icon_branding( $icons, $response, $settings ) {
		if ( in_array( $settings['slug'], array( 'bb-plugin', 'bb-theme-builder' ) ) ) {
			if ( self::is_white_labeled() ) {
				$icons = array(
					'1x'      => self::get_branding_icon(),
					'2x'      => self::get_branding_icon(),
					'default' => self::get_branding_icon(),
				);
			}
		}
		return $icons;
	}
}

FLBuilderWhiteLabel::init();
