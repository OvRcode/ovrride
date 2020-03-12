<?php

/**
 * Helper class for overriding core templates with user
 * defined templates.
 *
 * @since 1.5.7.
 */
final class FLBuilderTemplatesOverride {

	/**
	 * Init actions and filters.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function init() {
		// Actions
		add_action( 'fl_builder_admin_settings_templates_form', __CLASS__ . '::render_admin_settings' );
		add_action( 'fl_builder_admin_settings_save', __CLASS__ . '::save_admin_settings' );

		// Filters
		add_filter( 'fl_builder_register_template_post_type_args', __CLASS__ . '::post_type_args' );
		add_filter( 'fl_builder_render_ui_panel', __CLASS__ . '::render_ui_panel' );
		add_filter( 'fl_builder_template_selector_data', __CLASS__ . '::selector_data', 10, 2 );
		add_filter( 'fl_builder_row_templates_data', __CLASS__ . '::row_templates_data' );
		add_filter( 'fl_builder_column_templates_data', __CLASS__ . '::column_templates_data' );
		add_filter( 'fl_builder_module_templates_data', __CLASS__ . '::module_templates_data' );
		add_filter( 'fl_builder_override_apply_node_template', __CLASS__ . '::apply_node', 10, 2 );
		add_filter( 'fl_builder_override_apply_template', __CLASS__ . '::apply', 10, 2 );
	}

	/**
	 * Filters the args for the templates post type.
	 *
	 * @since 1.8
	 * @param array $args
	 * @return array
	 */
	static public function post_type_args( $args ) {
		$args['supports'][] = 'thumbnail';

		return $args;
	}

	/**
	 * Renders the admin settings.
	 *
	 * @since 1.5.7
	 * @return void
	 */
	static public function render_admin_settings() {
		if ( is_network_admin() || ! is_multisite() ) {

			$site_id      = self::get_source_site_id();
			$show_rows    = self::show_rows();
			$show_columns = self::show_columns();
			$show_modules = self::show_modules();

			include FL_BUILDER_TEMPLATES_OVERRIDE_DIR . 'includes/admin-settings-templates-override.php';
		}
	}

	/**
	 * Saves the admin settings.
	 *
	 * @since 1.5.7
	 * @return void
	 */
	static public function save_admin_settings() {
		if ( isset( $_POST['fl-templates-nonce'] ) && wp_verify_nonce( $_POST['fl-templates-nonce'], 'templates' ) ) {

			// Templates override
			if ( is_network_admin() ) {

				$templates_override = sanitize_text_field( $_POST['fl-templates-override'] );

				if ( empty( $templates_override ) ) {
					$templates_override = false;
				} elseif ( ! is_numeric( $templates_override ) ) {
					$templates_override = false;
					FLBuilderAdminSettings::add_error( __( 'Error! Please enter a number for the site ID.', 'fl-builder' ) );
				} elseif ( ! FLBuilderMultisite::blog_exists( $templates_override ) ) {
					$templates_override = false;
					FLBuilderAdminSettings::add_error( __( "Error! A site with that ID doesn't exist.", 'fl-builder' ) );
				}

				update_site_option( '_fl_builder_templates_override', $templates_override );
			} elseif ( ! is_multisite() ) {

				if ( isset( $_POST['fl-templates-override'] ) ) {
					$templates_override = 1;
				} else {
					$templates_override = false;
				}

				update_site_option( '_fl_builder_templates_override', $templates_override );
			}

			// Row and module templates
			if ( is_network_admin() || ! is_multisite() ) {
				update_site_option( '_fl_builder_templates_override_rows', isset( $_POST['fl-templates-override-rows'] ) );
				update_site_option( '_fl_builder_templates_override_columns', isset( $_POST['fl-templates-override-columns'] ) );
				update_site_option( '_fl_builder_templates_override_modules', isset( $_POST['fl-templates-override-modules'] ) );
			}
		}
	}

	/**
	 * Returns the ID of the source site or false.
	 *
	 * @since 1.5.7
	 * @return int|bool
	 */
	static public function get_source_site_id() {
		return get_site_option( '_fl_builder_templates_override', false );
	}

	/**
	 * Checks to see if row templates should be shown in the builder panel.
	 *
	 * @since 1.6.3
	 * @return bool
	 */
	static public function show_rows() {
		return get_site_option( '_fl_builder_templates_override_rows', false );
	}

	/**
	 * Checks to see if column templates should be shown in the builder panel.
	 *
	 * @since 2.1
	 * @return bool
	 */
	static public function show_columns() {
		return get_site_option( '_fl_builder_templates_override_columns', false );
	}

	/**
	 * Checks to see if module templates should be shown in the builder panel.
	 *
	 * @since 1.6.3
	 * @return bool
	 */
	static public function show_modules() {
		return get_site_option( '_fl_builder_templates_override_modules', false );
	}

	/**
	 * Overrides the template selector data. Called from the
	 * fl_builder_template_selector_data filter.
	 *
	 * @since 1.8
	 * @param array $data
	 * @return array
	 */
	static public function selector_data( $data, $type ) {
		if ( 'layout' != $type ) {
			return $data;
		}

		$override = self::get_selector_data();

		return $override ? $override : $data;
	}

	/**
	 * Returns data for overriding core templates in
	 * the template selector.
	 *
	 * @since 1.5.7
	 * @param string $type The type of user template to return.
	 * @return array|bool
	 */
	static public function get_selector_data( $type = 'layout' ) {
		$site_id = self::get_source_site_id();

		if ( $site_id && $type ) {

			if ( is_multisite() ) {
				switch_to_blog( $site_id );
			}

			$user                = FLBuilderModel::get_user_templates( $type );
			$data['templates']   = $user['templates'];
			$data['categorized'] = $user['categorized'];
			$data['groups']      = array();

			foreach ( $data['templates'] as $i => $template ) {

				// Set the template type to core.
				$template['type'] = 'core';

				// User templates don't have a "group" so we're faking groups by using categories.
				$template['group'] = array();

				foreach ( $template['category'] as $cat_slug => $cat_name ) {
					if ( ! isset( $data['groups'][ $cat_slug ] ) ) {
						$data['groups'][ $cat_slug ] = array(
							'name'       => $cat_name,
							'categories' => array(),
						);
					}
					$template['group'][] = $cat_slug;
				}

				$template['category']    = array(
					'none' => '',
				);
				$data['templates'][ $i ] = $template;
			}

			if ( is_multisite() ) {
				restore_current_blog();
			}

			return $data;

		}

		return false;
	}

	/**
	 * Filter to prevent rendering of the UI panel if needed.
	 *
	 * @since 1.8
	 * @param bool $render
	 * @return void
	 */
	static public function render_ui_panel( $render ) {
		if ( FLBuilderModel::is_post_user_template( 'module' ) ) {
			return false;
		}

		return $render;
	}

	/**
	 * Adds user template data for row templates in the UI panel.
	 *
	 * @since 1.8
	 * @param array $data
	 * @return array
	 */
	static public function row_templates_data( $data ) {
		if ( self::get_source_site_id() && self::show_rows() ) {
			$data = self::get_selector_data( 'row' );
		}

		return $data;
	}

	/**
	 * Adds user template data for column templates in the UI panel.
	 *
	 * @since 2.1
	 * @param array $data
	 * @return array
	 */
	static public function column_templates_data( $data ) {
		if ( self::get_source_site_id() && self::show_columns() ) {
			$data = self::get_selector_data( 'column' );
		}

		return $data;
	}

	/**
	 * Adds user template data for module templates in the UI panel.
	 *
	 * @since 1.8
	 * @param array $data
	 * @return array
	 */
	static public function module_templates_data( $data ) {
		if ( self::get_source_site_id() && self::show_modules() ) {
			$data = self::get_selector_data( 'module' );
		}

		return $data;
	}

	/**
	 * Applies a user defined template instead of a core template.
	 * Called from the fl_builder_override_apply_template filter.
	 *
	 * @since 1.6.3
	 * @param bool $override Whether an override has been applied or not.
	 * @param array $args An array of args from the filter.
	 * @return bool
	 */
	static public function apply( $override, $args ) {
		if ( ! $override ) {

			$site_id  = self::get_source_site_id();
			$template = new StdClass();

			if ( $site_id ) {

				if ( is_multisite() ) {
					switch_to_blog( $site_id );
				}

				$template->nodes    = FLBuilderModel::get_layout_data( 'published', $args['index'] );
				$template->settings = FLBuilderModel::get_layout_settings( 'published', $args['index'] );

				if ( is_multisite() ) {
					restore_current_blog();
				}

				return FLBuilderModel::apply_user_template( $template, $args['append'] );
			}
		}

		return $override;
	}

	/**
	 * Applies a node template that is defined as network-wide. Called from
	 * the fl_builder_override_apply_node_template filter.
	 *
	 * @since 1.6.3
	 * @param bool $override Whether an override has been applied or not.
	 * @param array $args An array of args from the filter.
	 * @return bool|object
	 */
	static public function apply_node( $override, $args ) {
		if ( ! $override && ! $args['template_post_id'] && ! $args['template'] ) {

			$site_id  = self::get_source_site_id();
			$template = new StdClass();

			if ( $site_id ) {

				if ( is_multisite() ) {
					switch_to_blog( $site_id );
				}

				$template->nodes    = FLBuilderModel::get_layout_data( 'published', $args['template_id'] );
				$template->settings = FLBuilderModel::get_layout_settings( 'published', $args['template_id'] );
				$template->type     = FLBuilderModel::get_user_template_type( $args['template_id'] );
				$template->global   = false;

				if ( is_multisite() ) {
					restore_current_blog();
				}

				return FLBuilderModel::apply_node_template( $args['template_id'], $args['parent_id'], $args['position'], $template );
			}
		}

		return $override;
	}
}

FLBuilderTemplatesOverride::init();
