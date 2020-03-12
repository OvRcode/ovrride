<?php

/**
 * Misc functions for compatibility with other plugins.
 */

/**
 * Support for tinyPNG.
 *
 * Runs cropped photos stored in cache through tinyPNG.
 */
function fl_builder_tinypng_support( $cropped_path, $editor ) {

	if ( class_exists( 'Tiny_Settings' ) ) {
		try {
			$settings = new Tiny_Settings();
			$settings->xmlrpc_init();
			$compressor = $settings->get_compressor();
			if ( $compressor ) {
				$compressor->compress_file( $cropped_path['path'], false, false );
			}
		} catch ( Exception $e ) {
			//
		}
	}
}
add_action( 'fl_builder_photo_cropped', 'fl_builder_tinypng_support', 10, 2 );

/**
 * Support for WooCommerce Memberships.
 *
 * Makes sure builder content isn't rendered for protected posts.
 */
function fl_builder_wc_memberships_support() {

	if ( function_exists( 'wc_memberships_is_post_content_restricted' ) ) {

		function fl_builder_wc_memberships_maybe_render_content( $do_render, $post_id ) {

			if ( wc_memberships_is_post_content_restricted() ) {

				// check if user has access to restricted content
				if ( ! current_user_can( 'wc_memberships_view_restricted_post_content', $post_id ) ) {
					$do_render = false;
				} elseif ( ! current_user_can( 'wc_memberships_view_delayed_post_content', $post_id ) ) {
					$do_render = false;
				}
			}

			return $do_render;
		}
		add_filter( 'fl_builder_do_render_content', 'fl_builder_wc_memberships_maybe_render_content', 10, 2 );
	}
}
add_action( 'plugins_loaded', 'fl_builder_wc_memberships_support', 11 );

/**
 * Support for Option Tree.
 *
 * Older versions of Option Tree don't declare the ot_get_media_post_ID
 * function on the frontend which is needed for the media uploader and
 * throws an error if it doesn't exist.
 */
function fl_builder_option_tree_support() {

	if ( ! function_exists( 'ot_get_media_post_ID' ) ) {

		function ot_get_media_post_ID() { // @codingStandardsIgnoreLine

			// Option ID
			$option_id = 'ot_media_post_ID';

			// Get the media post ID
			$post_id = get_option( $option_id, false );

			// Add $post_ID to the DB
			if ( false === $post_id ) {

				global $wpdb;

				// Get the media post ID
				$post_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE `post_title` = 'Media' AND `post_type` = 'option-tree' AND `post_status` = 'private'" );

				// Add to the DB
				add_option( $option_id, $post_id );
			}

			return $post_id;
		}
	}
}
add_action( 'after_setup_theme', 'fl_builder_option_tree_support' );

/**
 * If FORCE_SSL_ADMIN is enabled but the frontend is not SSL fixes a CORS error when trying to upload a photo.
 * `add_filter( 'fl_admin_ssl_upload_fix', '__return_false' );` will disable.
 *
 * @since 1.10.2
 */
function fl_admin_ssl_upload_fix() {
	if ( defined( 'FORCE_SSL_ADMIN' ) && ! is_ssl() && is_admin() && FLBuilderAJAX::doing_ajax() ) {
		if ( isset( $_POST['action'] ) && 'upload-attachment' === $_POST['action'] && true === apply_filters( 'fl_admin_ssl_upload_fix', true ) ) {
			force_ssl_admin( false );
		}
	}
}
add_action( 'plugins_loaded', 'fl_admin_ssl_upload_fix', 11 );

/**
 * Disable support Buddypress pages since it's causing conflicts with `the_content` filter
 *
 * @param bool $is_editable Wether the post is editable or not
 * @param $post The post to check from
 * @return bool
 */
function fl_builder_bp_pages_support( $is_editable, $post = false ) {

	// Frontend check
	if ( ! is_admin() && class_exists( 'BuddyPress' ) && ! bp_is_blog_page() ) {
		$is_editable = false;
	}

	// Admin rows action link check and applies to page list
	if ( is_admin() && class_exists( 'BuddyPress' ) && $post && 'page' == $post->post_type ) {

		$bp = buddypress();
		if ( $bp->pages ) {
			foreach ( $bp->pages as $page ) {
				if ( $post->ID == $page->id ) {
					$is_editable = false;
					break;
				}
			}
		}
	}

	return $is_editable;
}
add_filter( 'fl_builder_is_post_editable', 'fl_builder_bp_pages_support', 11, 2 );

/**
 * There is an issue with Jetpack Photon and circle cropped photo module
 * returning the wrong image sizes from the bb cache folder.
 * This filter disables photon for circle cropped photo module images.
 */
function fl_photo_photon_exception( $val, $src, $tag ) {

	// Make sure its a bb cached image.
	if ( false !== strpos( $src, 'bb-plugin/cache' ) ) {

		// now make sure its a circle cropped image.
		if ( false !== strpos( basename( $src ), '-circle' ) ) {
			return apply_filters( 'fl_photo_photon_exception', true );
		}
	}
	// return original val
	return $val;
}
add_filter( 'jetpack_photon_skip_image', 'fl_photo_photon_exception', 10, 3 );

/**
 * WordPress pre 4.5 we need to make sure that ui-core|widget|mouse are loaded before sortable.
 */
function fl_before_sortable_enqueue_callback() {

	if ( version_compare( get_bloginfo( 'version' ), '4.5', '<' ) ) {
		wp_deregister_script( 'jquery-ui-widget' );
		wp_deregister_script( 'jquery-ui-mouse' );
		wp_deregister_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-core', site_url( '/wp-includes/js/jquery/ui/core.min.js' ), array( 'jquery' ), '1.8.12' );
		wp_enqueue_script( 'jquery-ui-widget', site_url( '/wp-includes/js/jquery/ui/widget.min.js' ), array( 'jquery' ), '1.8.12' );
		wp_enqueue_script( 'jquery-ui-mouse', site_url( '/wp-includes/js/jquery/ui/mouse.min.js' ), array( 'jquery' ), '1.8.12' );
	}
}
add_action( 'fl_before_sortable_enqueue', 'fl_before_sortable_enqueue_callback' );

/**
 * Try to unserialize data normally.
 * Uses a preg_callback to fix broken data caused by serialized data that has broken offsets.
 *
 * @since 1.10.6
 * @param string $data unserialized string
 * @return array
 */
function fl_maybe_fix_unserialize( $data ) {
	// @codingStandardsIgnoreStart
	$unserialized = @unserialize( $data );
	// @codingStandardsIgnoreEnd
	if ( ! $unserialized ) {
		$unserialized = unserialize( preg_replace_callback( '!s:(\d+):"(.*?)";!', 'fl_maybe_fix_unserialize_callback', $data ) );
	}
	return $unserialized;
}

/**
 * Callback function for fl_maybe_fix_unserialize()
 *
 * @since 1.10.6
 */
function fl_maybe_fix_unserialize_callback( $match ) {
	return ( strlen( $match[2] ) == $match[1] ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
}

/**
 * Filter rendered module content and if safemode is active safely display a message.
 * @since 1.10.7
 */
function fl_builder_render_module_content_filter( $contents, $module ) {
	if ( isset( $_GET['safemode'] ) && FLBuilderModel::is_builder_active() ) {
		return sprintf( '<h3>[%1$s] %2$s %3$s</h3>', __( 'SAFEMODE', 'fl-builder' ), $module->name, __( 'module', 'fl-builder' ) );
	} else {
		return $contents;
	}
}

add_filter( 'fl_builder_render_module_content', 'fl_builder_render_module_content_filter', 10, 2 );

/**
 * Duplicate posts plugin fixes when cloning BB template.
 *
 * @since 1.10.8
 * @param int $meta_id The newly added meta ID
 * @param int $object_id ID of the object metadata is for.
 * @param string $meta_key Metadata key
 * @param string $meta_value Metadata value
 * @return void
 */
function fl_builder_template_meta_add( $meta_id, $object_id, $meta_key, $meta_value ) {
	global $pagenow;

	if ( 'admin.php' != $pagenow ) {
		return;
	}

	if ( ! isset( $_REQUEST['action'] ) || 'duplicate_post_save_as_new_post' != $_REQUEST['action'] ) {
		return;
	}

	$post_type = get_post_type( $object_id );
	if ( 'fl-builder-template' != $post_type || '_fl_builder_template_id' != $meta_key ) {
		return;
	}

	// Generate new template ID;
	$template_id = FLBuilderModel::generate_node_id();

	update_post_meta( $object_id, '_fl_builder_template_id', $template_id );
}
add_action( 'added_post_meta', 'fl_builder_template_meta_add', 10, 4 );

/**
 * Stop bw-minify from optimizing when builder is open.
 * @since 1.10.9
 */
function fl_bwp_minify_is_loadable_filter( $args ) {
	if ( FLBuilderModel::is_builder_active() ) {
		return false;
	}
	return $args;
}
add_filter( 'bwp_minify_is_loadable', 'fl_bwp_minify_is_loadable_filter' );

/**
* Fixes an issue on search archives if one of the results contains same shortcode
* as is currently trying to render.
*
* @since 1.10.9
* @param bool $render Render shortcode.
* @param array $attrs Shortcode attributes.
* @param array $args Passed to FLBuilder::render_query
* @return bool
*/
function fl_builder_insert_layout_render_search( $render, $attrs, $args ) {
	global $post, $wp_query;

	if ( is_search() && is_object( $post ) && is_array( $wp_query->posts ) ) {
		foreach ( $wp_query->posts as $queried_post ) {
			if ( $post->ID === $queried_post->ID ) {
				preg_match( '#(?<=fl_builder_insert_layout).*[id|slug]=[\'"]?([0-9a-z-]+)#', $post->post_content, $matches );
				if ( isset( $matches[1] ) ) {
					return false;
				}
			}
		}
	}
	return $render;
}
add_action( 'fl_builder_insert_layout_render', 'fl_builder_insert_layout_render_search', 10, 3 );

/**
* Fixes ajax issues with Event Espresso plugin when builder is open.
* @since 2.1
*/
function fl_ee_suppress_notices() {
	if ( FLBuilderModel::is_builder_active() ) {
		add_filter( 'FHEE__EE_Front_Controller__display_errors', '__return_false' );
	}
}
add_action( 'wp', 'fl_ee_suppress_notices' );

/**
 * Stops ee from outputting HTML into our ajax responses.
 * @since 2.1
 */
function fl_ee_before_ajax() {
	add_filter( 'FHEE__EE_Front_Controller__display_errors', '__return_false' );
}
add_action( 'fl_ajax_before_call_action', 'fl_ee_before_ajax' );


/**
* Plugin Enjoy Instagram loads its js and css on all frontend pages breaking the builder.
* @since 2.0.1
*/
add_action( 'template_redirect', 'fix_aggiungi_script_instafeed_owl', 1000 );
function fix_aggiungi_script_instafeed_owl() {
	if ( FLBuilderModel::is_builder_active() ) {
		remove_action( 'wp_enqueue_scripts', 'aggiungi_script_instafeed_owl' );
	}
}

/**
* Siteground cache captures shutdown and breaks our dynamic js loading.
* @since 2.0.4.2
*/
add_action( 'plugins_loaded', 'fl_fix_sg_cache', 9 );
function fl_fix_sg_cache() {
	if ( isset( $_GET['fl_builder_load_settings_config'] ) ) {
		remove_action( 'plugins_loaded', 'sg_cachepress_start' );
	}
}

/**
 * Remove Activemember360 shortcodes from saved post content to stop them rendering twice.
 * @since 2.0.6
 */
add_filter( 'fl_builder_editor_content', 'fl_activemember_shortcode_fix' );
function fl_activemember_shortcode_fix( $content ) {
	return preg_replace( '#\[mbr.*?\]#', '', $content );
}

/**
 * Remove iMember360 shortcodes from saved post content to stop them rendering twice.
 * @since 2.0.6
 */
add_filter( 'fl_builder_editor_content', 'fl_imember_shortcode_fix' );
function fl_imember_shortcode_fix( $content ) {
	return preg_replace( '#\[i4w.*?\]#', '', $content );
}

/**
 * Fix javascript issue caused by nextgen gallery when adding modules in the builder.
 * @since 2.0.6
 */
add_action( 'plugins_loaded', 'fl_fix_nextgen_gallery' );
function fl_fix_nextgen_gallery() {
	if ( isset( $_GET['fl_builder'] ) || isset( $_POST['fl_builder_data'] ) || FLBuilderAJAX::doing_ajax() ) {
		if ( ! defined( 'NGG_DISABLE_RESOURCE_MANAGER' ) ) {
			define( 'NGG_DISABLE_RESOURCE_MANAGER', true );
		}
	}
}

/**
 * Fix Tasty Recipes compatibility issues with the builder.
 * @since 2.0.6
 */
add_action( 'template_redirect', 'fl_fix_tasty_recipes' );
function fl_fix_tasty_recipes() {
	if ( FLBuilderModel::is_builder_active() ) {
		remove_action( 'wp_enqueue_editor', array( 'Tasty_Recipes\Assets', 'action_wp_enqueue_editor' ) );
		remove_action( 'media_buttons', array( 'Tasty_Recipes\Editor', 'action_media_buttons' ) );
	}
}

/**
 * Dequeue GeneratePress fa5 js when builder is open.
 * @since 2.1
 */
add_action( 'template_redirect', 'fl_fix_generatepress_fa5' );
function fl_fix_generatepress_fa5() {
	if ( FLBuilderModel::is_builder_active() ) {
		add_filter( 'generate_fontawesome_essentials', '__return_true' );
	}
}

/**
 * Try to render Ninja Forms JS templates when rendering an AJAX layout
 * in case the layout includes one of their shortcodes. This won't do
 * anything if no templates need to be rendered.
 * @since 2.1
 */
add_filter( 'fl_builder_ajax_layout_response', 'fl_render_ninja_forms_js' );
function fl_render_ninja_forms_js( $response ) {
	if ( class_exists( 'NF_Display_Render' ) ) {
		ob_start();
		NF_Display_Render::output_templates();
		$response['html'] .= ob_get_clean();
	}
	return $response;
}

/**
 * Turn off Hummingbird minification
 * @since 2.1
 */
add_action( 'template_redirect', 'fl_fix_hummingbird' );
function fl_fix_hummingbird() {
	if ( FLBuilderModel::is_builder_active() ) {
		add_filter( 'wp_hummingbird_is_active_module_minify', '__return_false', 500 );
	}
}

/**
 * Fix Enjoy Instagram feed on website with WordPress Widget and Shortcode issues with the builder.
 * @since 2.0.6
 */
add_action( 'template_redirect', 'fl_fix_enjoy_instagram' );
function fl_fix_enjoy_instagram() {
	if ( FLBuilderModel::is_builder_active() ) {
		remove_action( 'wp_head', 'funzioni_in_head' );
	}
}

/**
 * Fix Event Calendar widget not loading assets when added as a widget module.
 * @since 2.1.5
 */
add_action( 'tribe_events_pro_widget_render', 'fl_tribe_events_pro_widget_render_fix', 10, 3 );
function fl_tribe_events_pro_widget_render_fix( $class, $args, $instance ) {
	if ( isset( $args['widget_id'] ) && false !== strpos( $args['widget_id'], 'fl_builder_widget' ) ) {
		if ( class_exists( 'Tribe__Events__Pro__Mini_Calendar' ) ) {
			if ( method_exists( Tribe__Events__Pro__Mini_Calendar::instance(), 'register_assets' ) ) {
				Tribe__Events__Pro__Mini_Calendar::instance()->register_assets();
			} else {
				if ( class_exists( 'Tribe__Events__Pro__Widgets' ) && method_exists( 'Tribe__Events__Pro__Widgets', 'enqueue_calendar_widget_styles' ) ) {
					Tribe__Events__Pro__Widgets::enqueue_calendar_widget_styles();
				}
			}
		}
	}
}

/**
 * Fix for Enfold theme always loading wp-mediaelement
 * @since 2.1.5
 */
add_filter( 'avf_enqueue_wp_mediaelement', 'fl_builder_not_load_mediaelement', 10, 2 );
function fl_builder_not_load_mediaelement( $condition, $options ) {
	if ( FLBuilderModel::is_builder_active() ) {
		$condition = true;
	}
	return $condition;
}

/**
 * Fix issue with Templator plugin.
 * @since 2.1.6
 */
add_action( 'template_redirect', 'fl_builder_fix_templator' );
function fl_builder_fix_templator() {
	if ( FLBuilderModel::is_builder_active() && class_exists( 'Templator_Import' ) ) {
		remove_action( 'media_buttons', array( Templator_Import::get_instance(), 'import_template_button' ) );
	}
}

/**
 * Fix issue with Prevent Direct Access Gold.
 * @since 2.1.6
 */
add_action( 'template_redirect', 'fl_builder_fix_protector_gold' );
function fl_builder_fix_protector_gold() {
	if ( FLBuilderModel::is_builder_active() && class_exists( 'Prevent_Direct_Access_Gold' ) && ! function_exists( 'get_current_screen' ) ) {
		function get_current_screen() {
			$args         = new StdClass;
			$args->id     = 'Beaver';
			$args->action = 'Builder';
			return $args;
		}
	}
}

/**
 * Fix issue with WPMUDEV Smush It.
 * @since 2.1.6
 */
add_action( 'template_redirect', 'fl_builder_fix_smush_it' );
function fl_builder_fix_smush_it() {
	if ( FLBuilderModel::is_builder_active() ) {
		add_filter( 'wp_smush_enqueue', '__return_false' );
	}
}

/**
 * Whitelist files in bb-theme and bb-theme-builder in PHPCompatibility Checker plugin.
 * @since 2.1.6
 */
add_filter( 'phpcompat_whitelist', 'fl_builder_bbtheme_compat_fix' );
function fl_builder_bbtheme_compat_fix( $folders ) {

	// Theme
	$folders[] = '*/bb-theme/includes/vendor/Less/*';
	// Themer
	$folders[] = '*/bb-theme-builder/includes/post-grid-default-html.php';
	$folders[] = '*/bb-theme-builder/includes/post-grid-default-css.php';
	// bb-plugin
	$folders[] = '*/bb-plugin/includes/ui-field*.php';
	$folders[] = '*/bb-plugin/includes/ui-settings-form*.php';
	// lite
	$folders[] = '*/beaver-builder-lite-version/includes/ui-field*.php';
	$folders[] = '*/beaver-builder-lite-version/includes/ui-settings-form*.php';
	return $folders;
};

/**
 * Remove wpbb post:content from post_content as it causes inception.
 * @since 2.1.7
 */
add_filter( 'fl_builder_editor_content', 'fl_theme_post_content_fix' );
function fl_theme_post_content_fix( $content ) {
	return preg_replace( '#\[wpbb\s?post:content.*\]#', '', $content );
}

/**
 * Remove Popup-Maker post-type from admin settings post-types.
 * @since 2.1.7
 */
add_filter( 'fl_builder_admin_settings_post_types', 'fl_builder_admin_settings_post_types_popup' );
function fl_builder_admin_settings_post_types_popup( $types ) {
	if ( class_exists( 'Popup_Maker' ) && isset( $types['popup'] ) ) {
		unset( $types['popup'] );
	}
	return $types;
}

/**
 * If short description is blank and there is a layout in the product content
 * css will not be enqueued because woocommerce adds the css to the json+ld
 * @since 2.1.7
 */
add_filter( 'woocommerce_product_get_short_description', 'fl_fix_woo_short_description' );
function fl_fix_woo_short_description( $content ) {

	global $post, $fl_woo_description_fix;

	// if there is a short description no need to carry on.
	if ( '' !== $content ) {
		return $content;
	}

	// if the product content contains a layout shortcode then extract any css to add to footer later.
	if ( isset( $post->post_content ) && false !== strpos( $post->post_content, '[fl_builder_insert_layout' ) ) {
		$dummy   = do_shortcode( $post->post_content );
		$scripts = preg_match_all( "#<link rel='stylesheet'.*#", $dummy, $out );
		if ( is_array( $out ) ) {
			if ( ! is_array( $fl_woo_description_fix ) ) {
				$fl_woo_description_fix = array();
			}
			foreach ( $out[0] as $script ) {
				$fl_woo_description_fix[] = $script;
			}
		}
		// now we will use the content as the short description.
		$content = strip_shortcodes( wp_strip_all_tags( $post->post_content ) );
	}
	return $content;
}

/**
 * Footer action for fl_fix_woo_short_description to print foundf css.
 * @since 2.1.7
 */
add_action( 'wp_footer', 'fl_fix_woo_short_description_footer' );
function fl_fix_woo_short_description_footer() {
	global $fl_woo_description_fix;
	if ( is_array( $fl_woo_description_fix ) && ! empty( $fl_woo_description_fix ) ) {
		echo implode( "\n", $fl_woo_description_fix );
	}
}

/**
 * Fix fatal error on adding Themer layouts and Templates with seopress.
 * @since 2.1.8
 */
add_action( 'save_post', 'fl_fix_seopress', 9 );
function fl_fix_seopress() {
	if ( isset( $_POST['fl-template'] ) ) {
		remove_action( 'save_post', 'seopress_bulk_quick_edit_save_post' );
	}
}

/**
 * SiteGround Optimizer is known to break the builder.
 * @since 2.1.7
 */
if ( isset( $_GET['fl_builder'] ) ) {
	$options = array(
		'optimize_html',
		'optimize_javascript',
		'optimize_javascript_async',
		'remove_query_strings',
		'fix_insecure_content',
		'optimize_css',
		'combine_css',
		'optimize_javascript',
	);
	foreach ( $options as $option ) {
		add_filter( "option_siteground_optimizer_$option", '__return_false' );
	}
}

/**
 * Enlighter stops builder from loading.
 * @since 2.2
 */
add_filter( 'enlighter_startup', 'fl_enlighter_frontend_editing' );
function fl_enlighter_frontend_editing( $enabled ) {
	if ( isset( $_GET['fl_builder'] ) ) {
		return false;
	}
	return $enabled;
}

/**
 * Set sane settings for SSL
 * @since 2.2.1
 */
function fl_set_curl_safe_opts( $handle ) {
	curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, 1 );
	curl_setopt( $handle, CURLOPT_SSL_VERIFYHOST, 2 );
	curl_setopt( $handle, CURLOPT_CAINFO, ABSPATH . WPINC . '/certificates/ca-bundle.crt' );
	return $handle;
}

/**
 * Remove Sumo JS when builder is open.
 * @since 2.2.1
 */
add_filter( 'option_sumome_site_id', 'fl_fix_sumo' );
function fl_fix_sumo( $option ) {
	if ( isset( $_GET['fl_builder'] ) ) {
		return false;
	}
	return $option;
}

/**
 * Fix icon issues with Frontend Dashboard version 1.3.4+
 * @since 2.2.3
 */
add_action( 'template_redirect', 'fix_frontend_dashboard_plugin', 1000 );
function fix_frontend_dashboard_plugin() {
	if ( FLBuilderModel::is_builder_active() ) {
		remove_action( 'wp_enqueue_scripts', 'fed_script_front_end', 99 );
	}
}

/**
 * Add data-no-lazy to photo modules in themer header area.
 * Fixes wp-rocket lazy load issue with shrink header.
 * @since 2.2.3
 */
add_action( 'fl_theme_builder_before_render_header', 'fix_lazyload_header_start' );
function fix_lazyload_header_start() {
	add_filter( 'fl_builder_photo_attributes', 'fix_lazyload_header_attributes' );
}
function fix_lazyload_header_attributes( $attrs ) {
	return $attrs . ' data-no-lazy="1"';
}
add_action( 'fl_theme_builder_after_render_header', 'fix_lazyload_header_end' );
function fix_lazyload_header_end() {
	remove_filter( 'fl_builder_photo_attributes', 'fix_lazyload_header_attributes' );
}

/**
 * Fix JS error caused by UM-Switcher plugin
 * @since 2.2.3
 */
add_action( 'template_redirect', 'fl_fix_um_switcher' );
function fl_fix_um_switcher() {
	if ( isset( $_GET['fl_builder'] ) ) {
		remove_action( 'wp_footer', 'umswitcher_profile_subscription_expiration_footer' );
	}
}

/**
 * Fix pipedrive chat popup
 * @since 2.2.4
 */
add_action( 'template_redirect', 'fl_fix_pipedrive' );
function fl_fix_pipedrive() {
	if ( isset( $_GET['fl_builder'] ) ) {
		remove_action( 'wp_head', 'pipedrive_add_embed_code' );
	}
}

/**
 * Fix post type switcher
 * @since 2.2.4
 */
add_action( 'admin_init', 'fl_fix_posttypeswitcher' );
function fl_fix_posttypeswitcher() {
	global $pagenow;
	$disable = false;
	if ( 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'fl-theme-layout' === $_GET['post_type'] ) {
		$disable = true;
	}
	if ( 'post.php' === $pagenow && isset( $_GET['post'] ) && ( 'fl-theme-layout' === get_post_type( $_GET['post'] ) || 'fl-builder-template' === get_post_type( $_GET['post'] ) ) ) {
		$disable = true;
	}
	if ( $disable ) {
		add_filter( 'pts_allowed_pages', '__return_empty_array' );
	}
}

/**
 * Fixes for Google Reviews Business Plugin widget
 * @since 2.2.4
 */
add_action( 'widgets_init', 'fix_google_reviews_business_widget', 11 );
function fix_google_reviews_business_widget() {
	if ( isset( $_GET['fl_builder'] ) ) {
		unregister_widget( 'Goog_Reviews_Pro' );
	}
}
/**
 * Fixes for Google Reviews Business Plugin shortcode
 * @since 2.2.4
 */
add_action( 'init', 'fix_google_reviews_business_shortcode' );
function fix_google_reviews_business_shortcode() {
	if ( isset( $_GET['fl_builder'] ) ) {
		remove_shortcode( 'google-reviews-pro' );
	}
}

/**
 * Fix pagination on category archive layout.
 * @since 2.2.4
 */
function fl_theme_builder_cat_archive_post_grid( $query ) {
	if ( ! $query ) {
		return;
	}

	if ( ! class_exists( 'FLThemeBuilder' ) ) {
		return;
	}

	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( ! $query->is_archive || ! $query->is_category ) {
		return;
	}

	$args       = array(
		'post_type'   => 'fl-theme-layout',
		'post_status' => 'publish',
		'fields'      => 'ids',
		'meta_query'  => array(
			'relation' => 'OR',
			array(
				'key'     => '_fl_theme_builder_locations',
				'value'   => 'general:site',
				'compare' => 'LIKE',
			),
			array(
				'key'     => '_fl_theme_builder_locations',
				'value'   => 'taxonomy:category',
				'compare' => 'LIKE',
			),
			array(
				'key'     => '_fl_theme_builder_locations',
				'value'   => 'general:archive',
				'compare' => 'LIKE',
			),
		),
	);
	$post_grid  = null;
	$object     = null;
	$exclusions = array();

	if ( $query->get( 'cat' ) ) {
		$term = get_term( $query->get( 'cat' ), 'category' );
	} elseif ( $query->get( 'category_name' ) ) {
		$term = get_term_by( 'slug', $query->get( 'category_name' ), 'category' );
	}

	if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
		$term_id              = (int) $term->term_id;
		$object               = 'taxonomy:category:' . $term_id;
		$args['meta_query'][] = array(
			'key'     => '_fl_theme_builder_locations',
			'value'   => $object,
			'compare' => 'LIKE',
		);
	}

	$layout_query = new WP_Query( $args );
	if ( $layout_query->post_count > 0 ) {

		foreach ( $layout_query->posts as $i => $post_id ) {
			$exclusions = FLThemeBuilderRulesLocation::get_saved_exclusions( $post_id );
			$exclude    = false;

			if ( $object && in_array( $object, $exclusions ) ) {
				$exclude = true;
			} elseif ( in_array( 'taxonomy:category', $exclusions ) ) {
				$exclude = true;
			} elseif ( in_array( 'general:archive', $exclusions ) ) {
				$exclude = true;
			}

			if ( ! $exclude ) {
				$data = FLBuilderModel::get_layout_data( 'published', $post_id );
				if ( ! empty( $data ) ) {

					foreach ( $data as $node_id => $node ) {

						if ( 'module' != $node->type ) {
							continue;
						}

						if ( ! isset( $node->settings->type ) || 'post-grid' != $node->settings->type ) {
							continue;
						}

						// Check for `post-grid` with custom query source.
						if ( 'custom_query' == $node->settings->data_source ) {
							$post_grid = FLBuilderLoop::custom_query( $node->settings );
							break;
						}
					}
				}
			}

			if ( $post_grid ) {
				break;
			}
		}
	}

	return $post_grid;
}

/**
 * Remove sorting from download type if EDD is active.
 * @since 2.2.5
 */
add_filter( 'fl_builder_admin_edit_sort_blocklist', 'fl_builder_admin_edit_sort_blocklist_edd' );
function fl_builder_admin_edit_sort_blocklist_edd( $blocklist ) {
	$types = FLBuilderModel::get_post_types();
	if ( in_array( 'download', $types ) && class_exists( 'Easy_Digital_Downloads' ) ) {
		$blocklist[] = 'download';
	}
	return $blocklist;
}

/**
	* Remove BB Template types from Gute Editor suggested urls
	* @since 2.2.5
	*/
add_action( 'pre_get_posts', 'fl_gute_links_fix' );
function fl_gute_links_fix( $query ) {
	if ( defined( 'REST_REQUEST' ) && $query->is_search() ) {
		$types = (array) $query->get( 'post_type' );
		$key   = array_search( 'fl-builder-template', $types, true );
		if ( $key ) {
			unset( $types[ $key ] );
			$query->set( 'post_type', $types );
		}
	}
}

/**
 * Cookie-bot js destroys the UI when set to auto mode.
 * @since 2.2.6
 */
add_filter( 'option_cookiebot-nooutput', function( $arg ) {
	if ( isset( $_GET['fl_builder'] ) ) {
		return true;
	}
	return $arg;
});
