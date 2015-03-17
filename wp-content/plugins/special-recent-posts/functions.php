<?php
/**
 * srp_widgets_init()
 *
 * This function handles the plugin widget registration.
 *
 * @author Luca Grandicelli <lgrandicelli@gmail.com>
 * @copyright (C) 2011-2014 Luca Grandicelli
 * @package special-recent-posts-free
 * @version 2.0.4
 * @return boolean true
 */
function srp_widgets_init() {

	// Registering SRP widget.
	register_widget( 'WDG_SpecialRecentPostsFree' );

	// Returning true.
	return true;
}

/**
 * srp_check_plugin_compatibility()
 *
 * This function does a compatibility test to ensure that all the SRP requirements are met.
 *
 * @author Luca Grandicelli <lgrandicelli@gmail.com>
 * @copyright (C) 2011-2014 Luca Grandicelli
 * @package special-recent-posts-free
 * @version 2.0.4
 * @return boolean true
 */
function srp_check_plugin_compatibility() {
	
	// Storing all the potential error messages.
	$errorMessages = array(
		'phpversion'      => __( "Special Recent Posts FREE Error! You're running an old version of PHP. In order for this plugin to work, you must enable your server with PHP support version 5.0.0+. Please contact your hosting/housing company support, and check how to enable it.", SRP_TRANSLATION_ID),
		'gd_info'         => __( "Special Recent Posts FREE Error! GD libraries are not supported by your server. Please contact your hosting/housing company support, and check how to enable it. Without these libraries, thumbnails can't be properly resized and displayed.", SRP_TRANSLATION_ID),
		'post-thumbnails' => __( "Special Recent Posts FREE Warning! Your theme doesn't support post thumbnail. The plugin will keep on working with first post images only. To enable post thumbnail support, please check the Wordpress documentation", SRP_TRANSLATION_ID),
		'cache-exists'    => __( "Special Recent Posts FREE Warning! The Cache folder does not exist!. In order to use caching functionality you have to manually create a folder named 'cache' under the special-recent-posts/ directory.", SRP_TRANSLATION_ID),
		'cache-writable'  => __( "Special Recent Posts FREE Warning! The Cache folder is not writable. In order to use caching functionality you have to set the correct writing permissions on special-recent-posts/cache/ folder. E.G: 0755 or 0775", SRP_TRANSLATION_ID)
	);

	// Checking current PHP version.
    if ( '-1' == version_compare( phpversion(), SRP_REQUIRED_PHPVER )  ) {

    	// Setting up new Error object.
		$error_phpversion = new WP_Error( 'broke', $errorMessages['phpversion'] );

		// Checking the correct form of the WP Error object.
		if ( is_wp_error( $error_phpversion ) ) {

			// Displaying the error message through the WP notice system.
			printf( '<div class="error">%s</div>', $error_phpversion->get_error_message() );
		}
    }
	
	// Checking for GD libraries support (required for the PHP Thumbnailer Class to work).
	if ( !function_exists( 'gd_info' ) ) {
		
		// Setting up new Error object.
		$error_gd_info = new WP_Error( 'broke', $errorMessages['gd_info'] );

		// Checking the correct form of the WP Error object.
		if ( is_wp_error( $error_gd_info ) ) {

			// Displaying the error message through the WP notice system.
			printf( '<div class="error">%s</div>', $error_gd_info->get_error_message() );
		}
	}
	
	// Checking if the active WP theme support featured thumbnails.
	if ( !current_theme_supports( 'post-thumbnails' ) ) {
		
		// Setting up new Error object.
		$error_post_thumbnails = new WP_Error( 'broke', $errorMessages['post-thumbnails'] );

		// Checking the correct form of the WP Error object.
		if ( is_wp_error( $error_post_thumbnails ) ) {

			// Displaying the error message through the WP notice system.
			printf( '<div class="error">%s</div>', $error_post_thumbnails->get_error_message() );
		}
	}
	
	// Checking if cache folder exixts and it's writable.
	if ( !file_exists( SRP_PLUGIN_DIR . SRP_CACHE_DIR ) ) {

		// Setting up new Error object.
		$error_cache_exists = new WP_Error( 'broke', $errorMessages['cache-exists'] );

		// Checking the correct form of the WP Error object.
		if ( is_wp_error( $error_cache_exists ) ) {

			// Displaying the error message through the WP notice system.
			printf( '<div class="error">%s</div>', $error_cache_exists->get_error_message() );
		}
		
	} else if ( !is_writable( SRP_PLUGIN_DIR . SRP_CACHE_DIR) ) {
		
		// Setting up new Error object.
		$error_cache_writable = new WP_Error( 'broke', $errorMessages['cache-writable'] );

		// Checking the correct form of the WP Error object.
		if ( is_wp_error( $error_cache_writable ) ) {

			// Displaying the error message through the WP notice system.
			printf( '<div class="error">%s</div>', $error_cache_writable->get_error_message() );
		}
	}

	// Returning true.
	return true;
}

/**
 * srp_admin_menu()
 *
 * This function builds the SRP menu items in the administration main WP left menu.
 *
 * @author Luca Grandicelli <lgrandicelli@gmail.com>
 * @copyright (C) 2011-2014 Luca Grandicelli
 * @package special-recent-posts-free
 * @version 2.0.4
 * @return boolean true
 */
function srp_admin_menu() {
	
	// Creating top level benu entry
	add_menu_page( 'Special Recent Posts FREE - ' . __( 'General Settings', SRP_TRANSLATION_ID ), 'SRP FREE', 'install_plugins', 'srp-free-settings', 'srp_admin_menu_options', SRP_PLUGIN_URL . SRP_IMAGES_FOLDER . 'wp-menu-icon.png' );

	// Creating sub-menu
	add_submenu_page( 'srp-free-settings', 'Special Recent Posts FREE - ' . __( 'General Settings', SRP_TRANSLATION_ID ), __( 'General Settings', SRP_TRANSLATION_ID ), 'install_plugins', 'srp-free-general-settings', 'srp_admin_menu_options' );
	
	// Returning true.
	return true;
}

/**
 * srp_admin_enqueue_scripts()
 *
 * This function registers all the needed stylesheets & JS scripts in order for the SRP plugin to work.
 *
 * @author Luca Grandicelli <lgrandicelli@gmail.com>
 * @copyright (C) 2011-2014 Luca Grandicelli
 * @package special-recent-posts-free
 * @version 2.0.4
 * @param string $hook The current viewed page hook.
 * @return boolean true
 */
function srp_admin_enqueue_scripts( $hook ) {

	// Switching through the different $hook cases.
	switch ( $hook ) {

		// The WP widget page
		case 'widgets.php':

			// Enqueuing admin stylesheet.
			wp_enqueue_style( 'srp-admin-stylesheet', SRP_ADMIN_CSS );
			
			// Enqueuing custom JS init script.
			wp_enqueue_script( 'srp-custom-js-init'  , SRP_JS_INIT,  array('jquery'), SRP_PLUGIN_VERSION, true );

			
		break;

		// The SRP admin settings page.
		case 'toplevel_page_srp-free-settings':
		case 'srp-free_page_srp-free-general-settings':

			// Enqueuing admin stylesheet.
			wp_enqueue_style( 'srp-admin-stylesheet', SRP_ADMIN_CSS );
			
			// Enqueuing custom JS init script.
			wp_enqueue_script( 'srp-custom-js-init'  , SRP_JS_INIT,  array('jquery'), SRP_PLUGIN_VERSION, true );

			// Enqueuing the ACE Code highliter plugin JS library.
			wp_enqueue_script( 'ace-code-highlighter-js', SRP_PLUGIN_URL . SRP_JS_FOLDER . 'plugins/ace/ace.js', '', '1.0.0', true );

			// Enqueuing the ACE Code highliter plugin JS mode library.
			wp_enqueue_script( 'ace-mode-js', SRP_PLUGIN_URL . SRP_JS_FOLDER . 'plugins/ace/mode-css.js', array( 'ace-code-highlighter-js' ), '1.0.0', true );

			// Enqueuing the ACE Code highliter custom JS script.
			wp_enqueue_script( 'ace-custom-css-js', SRP_PLUGIN_URL . SRP_JS_FOLDER . 'plugins/ace/srp-ace-css.js', array( 'jquery', 'ace-code-highlighter-js' ), '1.0.0', true );

		break;


		default:

			// Returning true.
			return true;
		break;
	}

}

/**
 * srp_wp_head()
 *
 * This function loads styles & scripts in the WP theme <head>
 *
 * @author Luca Grandicelli <lgrandicelli@gmail.com>
 * @copyright (C) 2011-2014 Luca Grandicelli
 * @package special-recent-posts-free
 * @version 2.0.4
 * @return boolean true
 */
function srp_wp_head() {
	
	// First of all, let's do a database check.
	SpecialRecentPostsFree::srp_dboptions_check();
	
	// Importing global default options array.
	$srp_current_options = get_option( 'srp_plugin_options' );
	
	// Checking if the SRP built-in stylesheet is enabled.
	if ( 'yes' != $srp_current_options['srp_disable_theme_css'] ) {
		
		// Registering front end stylesheet.
		wp_register_style( 'srp-layout-stylesheet' , SRP_LAYOUT_CSS );
		
		// Enqueuing stylesheet.
		wp_enqueue_style( 'srp-layout-stylesheet' );

		// Checking if there is some custom CSS code available.
		if ( !empty( $srp_current_options['srp_custom_css'] ) ) {

			// Outputting custom CSS.
			echo "<style type='text/css'>" . $srp_current_options['srp_custom_css'] . '</style>';

		}
	}

	// Returning true.
	return true;
}

/**
 * srp_admin_menu_options()
 *
 * This function builds the plugin admin page.
 *
 * @author Luca Grandicelli <lgrandicelli@gmail.com>
 * @copyright (C) 2011-2014 Luca Grandicelli
 * @package special-recent-posts-free
 * @version 2.0.4
 * @return boolean true
 */
function srp_admin_menu_options() {

	// Checking if we have the user has the 'install_plugins' permission enabled.
	if ( !current_user_can( 'install_plugins' ) ) {

		// Abort script with message.
		wp_die( __( 'You do not have sufficient permissions to access this page.', SRP_TRANSLATION_ID ) );
	}

	// Updating and validating data/POST Check.
	srp_update_data( $_POST );
	
	// Importing global default options array.
	$srp_current_options = get_option( 'srp_plugin_options' );
?>

<!-- BEGIN SRP admin page -->
<div class="wrap">

	<!-- BEGIN WP Hack: fake H2 for WP notifications -->
	<h2></h2>
	<!-- END WP Hack: fake H2 for WP notifications -->

	<!-- BEGIN SRP admin container -->
	<div id="srp-admin-container">

		<!-- BEGIN SRP PRO Promo -->
		<div class="srp-notify-upgrade">

			<!-- BEGIN SRP PRO Logo -->
			<img src="<?php echo SRP_PLUGIN_URL . SRP_IMAGES_FOLDER; ?>srp-pro-logo-notify-upgrade.png" alt="<?php esc_attr_e('The Special Recent Posts PRO logo', SRP_TRANSLATION_ID); ?>" />
			<!-- END SRP PRO Logo -->

			<!-- BEGIN SRP PRO Promo Description -->
			<div>
				<h3>
				<?php _e( 'Special Recent Posts PRO Edition v3 has been released!', SRP_TRANSLATION_ID ); ?> <a href='http://codecanyon.net/item/special-recent-posts-pro-edition/552356?ref=lucagrandicelli' target="_blank" title="<?php echo esc_attr( __('Upgrade now to Special Recent Posts PRO Edition v3', SRP_TRANSLATION_ID) ); ?>">Upgrade Now!</a>
				</h3>
				<p>
				<?php printf( __( 'Now finally with %1$sPagination Support%2$s, %1$sResponsive Multi Layouts%2$s, %1$sCustom Post Types & Taxonomy Management%2$s, %1$sAuto Update Notifications%2$s<br />and much more up to %1$s120 customization options available%2$s. Now translated in multiple languages. %3$sDiscover all the new features%4$s</a>', SRP_TRANSLATION_ID ), '<strong>', '</strong>', '<a href="http://codecanyon.net/item/special-recent-posts-pro-edition/552356?ref=lucagrandicelli" target="_blank" title="' . esc_attr( __('Discover all the new features of Special Recent Posts PRO Edition v3', SRP_TRANSLATION_ID) ) . '">', '</a>' ); ?>
				</p>
			</div>
			<!-- END SRP PRO Promo Description -->

		</div>
		<!-- END SRP PRO Promo -->
		
		<!-- BEGIN SRP admin page header -->
		<div class="srp-admin-panel-header">

			<!-- BEGIN SRP admin page header logo -->
			<img src="<?php echo SRP_PLUGIN_URL . SRP_IMAGES_FOLDER; ?>widget-header-logo.png" alt="<?php esc_attr_e('The Special Recent Posts FREE logo', SRP_TRANSLATION_ID); ?>" />
			<!-- END SRP admin page header logo -->

			<!-- BEGIN SRP admin page header title -->
			<h2>
				<?php _e( 'Special Recent Posts FREE - General Settings', SRP_TRANSLATION_ID ); ?>
			</h2>
			<!-- END SRP admin page header title -->

		</div>
		<!-- END SRP admin page header -->
		
		<!-- BEGIN SRP admin page header description -->
		<div class="srp-settings-description">
			<?php printf( __( '%1$sWelcome to the SRP FREE General Settings Page.%2$s%3$sIn this area you can configure all the main settings for the Special Recent Posts FREE plugin. Please keep in mind that these are basic options; further special customization options apply for each widget instance, shortcode or PHP code to ensure an high level of customization. Go to your Wordpress widgets page and drag the Special Recent Posts FREE widget onto one of your draggable areas to see all the additional options available.', SRP_TRANSLATION_ID ), '<strong>', '</strong>', '<br />' ); ?>
		</div>
		<!-- END SRP admin page header description -->
			
		<!-- BEGIN menu tabs container -->
		<div id="srp-tabs-container">

			<!-- BEGIN menu tabs -->
			<ul class="srp-tabs-menu">

				<!-- BEGIN 'General Settings' menu tab -->
				<li class="srp-settings-tab current">
					<a href="#srp-tab-1" title="<?php esc_attr_e( 'General Settings', SRP_TRANSLATION_ID ); ?>">
						<?php _e( 'General Settings', SRP_TRANSLATION_ID ); ?>
					</a>
				</li>
				<!-- END 'General Settings' menu tab -->

				<!-- BEGIN 'Custom CSS Editor' menu tab -->
				<li class="srp-settings-tab">
					<a href="#srp-tab-2" title="<?php esc_attr_e( 'Custom CSS Editor', SRP_TRANSLATION_ID ); ?>">
						<?php _e( 'Custom Css Editor', SRP_TRANSLATION_ID ); ?>
					</a>
				</li>
				<!-- END 'Custom CSS Editor' menu tab -->

				<!-- BEGIN 'Cache Settings' menu tab -->
				<li class="srp-settings-tab">
					<a href="#srp-tab-3" title="<?php esc_attr_e( 'Cache Settings', SRP_TRANSLATION_ID ); ?>">
						<?php _e( 'Cache Settings', SRP_TRANSLATION_ID ); ?>
					</a>
				</li>
				<!-- END 'Cache Settings' menu tab -->

				<!-- BEGIN 'Plugin Notes' menu tab -->
				<li class="srp-settings-tab">
					<a href="#srp-tab-4" title="<?php esc_attr_e( 'Plugin Notes', SRP_TRANSLATION_ID ); ?>">
						<?php _e( 'Plugin Notes', SRP_TRANSLATION_ID ); ?>
					</a>
				</li>
				<!-- END 'Plugin Notes' menu tab -->

			</ul>
			<!-- EOF admin Tabs -->

			<!-- BEGIN tab element -->
			<div class="srp-tab">

				<!-- BEGIN 'General Settings' tab content -->
				<div id="srp-tab-1" class="metabox-holder srp-tab-content">

					<!-- BEGIN options form -->
					<form id="srp_admin_form" name="srp_admin_form" action="" method="POST">

						<!-- BEGIN options form hidden values-->
						<input type="hidden" value="yes" name="srp_dataform">
						<input type="hidden" value="<?php echo $srp_current_options['srp_version']; ?>" name="srp_version">
						<!-- END options form hidden values-->

						<?php settings_fields( 'srp_admin_form' ); ?>

						<!-- BEGIN options form entries -->
						<ul class="srp-settings-list">

							<!-- BEGIN 'Compatibility Mode' -->
							<li>

								<!-- BEGIN 'Compatibility Mode' label -->
								<label for="srp_compatibility_mode">
									<?php _e( 'Compatibility Mode', SRP_TRANSLATION_ID ); ?>
								</label>
								<!--END  'Compatibility Mode' label -->

								<!-- BEGIN 'Compatibility Mode' description -->
								<div class="srp-label-description">
									<?php _e( 'This option enables some compatibility features in order to work seamlessly with other plugins. If you are experiencing conflict problems, you might want to disable this option switching it to no.', SRP_TRANSLATION_ID ); ?>
								</div>
								<!-- END 'Compatibility Mode' description -->

								<!-- BEGIN 'Compatibility Mode' value -->
								<select id="srp_compatibility_mode" name="srp_compatibility_mode">

									<option value="yes" <?php selected( $srp_current_options['srp_compatibility_mode'], 'yes' ); ?>>
										<?php _e( 'Enabled', SRP_TRANSLATION_ID ); ?>
									</option>

									<option value="no" <?php selected( $srp_current_options['srp_compatibility_mode'], 'no' ); ?>>
										<?php _e( 'Disabled', SRP_TRANSLATION_ID ); ?>
									</option>

								</select>
								<!-- END 'Compatibility Mode' value -->

							</li>
							<!-- END 'Compatibility Mode' -->

							<!-- BEGIN 'Log Errors On Screen' -->
							<li>

								<!-- BEGIN 'Log Errors On Screen' label -->
								<label for="srp_log_errors_screen">
									<?php _e( 'Log Errors On Screen', SRP_TRANSLATION_ID ); ?>
								</label>
								<!-- BEGIN 'Log Errors On Screen' label -->

								<!-- BEGIN 'Log Errors On Screen' description -->
								<div class="srp-label-description">
									<?php _e( 'This option enables the SRP error logging system. Switch this to yes if you want to log potential errors or warnings on screen.', SRP_TRANSLATION_ID ); ?>
								</div>
								<!-- END 'Log Errors On Screen' description -->

								<!-- BEGIN 'Log Errors On Screen' value -->
								<select id="srp_log_errors_screen" name="srp_log_errors_screen">

									<option value="yes" <?php selected( $srp_current_options['srp_log_errors_screen'], 'yes'); ?>>
										<?php _e( 'Yes', SRP_TRANSLATION_ID ); ?>
									</option>

									<option value="no" <?php selected( $srp_current_options['srp_log_errors_screen'], 'no'); ?>>
										<?php _e( 'No', SRP_TRANSLATION_ID ); ?>
									</option>

								</select>
								<!-- END 'Log Errors On Screen' value -->

							</li>
							<!-- END 'Log Errors On Screen' -->
							
							<!-- BEGIN 'No-Posts Image Placeholder' -->
							<li>

								<!-- BEGIN 'No-Posts Image Placeholder' label -->
								<label for="srp_noimage_url">
									<?php _e( 'No-Posts Image Placeholder', SRP_TRANSLATION_ID ); ?>
								</label>
								<!-- END 'No-Posts Image Placeholder' label -->

								<!-- BEGIN 'No-Posts Image Placeholder' description -->
								<div class="srp-label-description">
									<?php _e( 'This is the default image that appears when no other images are available inside a post. You can use the one you prefer by simply typing in the full URL of the image. If you leave this field empty, the default no-image placeholder will be loaded instead.', SRP_TRANSLATION_ID ); ?>
								</div>
								<!-- END 'No-Posts Image Placeholder' description -->

								<!-- BEGIN 'No-Posts Image Placeholder' value -->
								<input type="text" id="srp_noimage_url" name="srp_noimage_url" value="<?php echo esc_url( $srp_current_options['srp_noimage_url'] ); ?>" size="90" /><br />
								<small><?php _e( 'Default size: 200px x 200px.', SRP_TRANSLATION_ID ); ?></small>
								<!-- END 'No-Posts Image Placeholder' value -->

							</li>
							<!-- BEGIN 'No-Posts Image Placeholder' -->

							<!-- BEGIN 'Thumbnail Image Quality Ratio' -->
							<li>

								<!-- BEGIN 'Thumbnail Image Quality Ratio' label -->
								<label for="srp_thumbnail_jpeg_quality">
									<?php _e( 'JPEG Image Quality Ratio', SRP_TRANSLATION_ID ); ?>
								</label>
								<!-- END 'Thumbnail Image Quality Ratio' label -->

								<!-- BEGIN 'Thumbnail Image Quality Ratio' description -->
								<div class="srp-label-description">
									<?php _e( "This options sets the JPEG quaility ratio when the 'Custom Thumbnails Option' is enabled. Numerical values are accepted only. 100 is the maximum quality, but 80 is an acceptable compromise between file size and image quality. Range: 0 to 100.", SRP_TRANSLATION_ID ); ?>
								</div>
								<!-- END 'Thumbnail Image Quality Ratio' description -->

								<!-- BEGIN 'Thumbnail Image Quality Ratio' value -->
								<input type="text" id="srp_thumbnail_jpeg_quality" name="srp_thumbnail_jpeg_quality" value="<?php echo stripslashes( $srp_current_options['srp_thumbnail_jpeg_quality'] ); ?>" size="2" maxlength="3" />
								<!-- END 'Thumbnail Image Quality Ratio' value -->

							</li>
							<!-- END 'Thumbnail Image Quality Ratio' -->

							<!-- BEGIN 'Disable Plugin CSS' -->
							<li>

								<!-- BEGIN 'Disable Plugin CSS' label -->
								<label for="srp_disable_theme_css">
									<?php _e( 'Disable Plugin CSS', SRP_TRANSLATION_ID ); ?>
								</label>
								<!-- END 'Disable Plugin CSS' label -->

								<!-- BEGIN 'Disable Plugin CSS' description -->
								<div class="srp-label-description">
									<?php _e( "This option enables/disables the built-in widget stylesheet. Set this option to 'yes' if you wish to use your own CSS.", SRP_TRANSLATION_ID ); ?>
								</div>
								<!-- END 'Disable Plugin CSS' description -->

								<!-- BEGIN 'Disable Plugin CSS' value -->
								<select id="srp_disable_theme_css" name="srp_disable_theme_css">

									<option value="yes" <?php selected( $srp_current_options['srp_disable_theme_css'], 'yes' ); ?>><?php _e( 'Yes', SRP_TRANSLATION_ID ); ?>
									</option>

									<option value="no" <?php selected( $srp_current_options['srp_disable_theme_css'], 'no' ); ?>><?php _e( 'No', SRP_TRANSLATION_ID ); ?>
									</option>

								</select>
								<!-- END 'Disable Plugin CSS' value -->

							</li>
							<!-- END 'Disable Plugin CSS' -->

						</ul>
						<!-- END options form entries -->

						<!-- BEGIN form submit button -->
						<input type="submit" name="submit" class="button-primary" value="<?php _e('Save Options', SRP_TRANSLATION_ID); ?>" />
						<!-- END form submit button -->

				</div>
				<!-- END 'General Settings' tab content -->

				<!-- BEGIN 'Custom CSS Editor' tab content -->
				<div id="srp-tab-2" class="metabox-holder srp-tab-content">

					<!-- BEGIN 'Custom CSS Editor' container -->
				    <div class="wrap">

				        <div id="icon-themes" class="icon32"></div>

				        <!-- BEGIN 'Custom CSS Editor' title -->
				        <h2>
				        	<?php _e( 'Custom CSS Editor', SRP_TRANSLATION_ID ); ?>
				        </h2>
				        <!-- END 'Custom CSS Editor' title -->

				        <!-- BEGIN 'Custom CSS Editor' ACE plugin placeholder -->
			            <div id="custom_css_container">
			                <div name="srp_custom_css" id="srp_custom_css" style="border: 1px solid #DFDFDF; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px; width: 100%; height: 400px; position: relative;"></div>
			            </div>
			            <!-- END 'Custom CSS Editor' ACE plugin placeholder -->

			            <!-- BEGIN code textarea -->
			            <?php $csseditor_introtext = '/*' . PHP_EOL . __( 'Welcome to the Special Recent Posts Custom CSS editor!', SRP_TRANSLATION_ID) . PHP_EOL . __("Please add all your custom CSS here and avoid modifying the core plugin files, since that'll make upgrading the plugin problematic. Your custom CSS will be loaded in your <head> section of your wordpress theme, which means that your rules will take precedence. Just add your CSS here for what you want to change, you don't need to copy all the plugin's stylesheet content.", SRP_TRANSLATION_ID ) . PHP_EOL . '*/'; ?>
			            <textarea id="srp_custom_css_textarea" name="srp_custom_css" style="display: none;"><?php echo ( $srp_current_options['srp_custom_css'] == 'Default CSS Comment' ) ? $csseditor_introtext : $srp_current_options['srp_custom_css']; ?></textarea>
			            <!-- END code textarea -->

			            <!-- BEGIN form submit button -->
			            <p>
			            	<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', SRP_TRANSLATION_ID ) ?>" />
			            </p>
			            <!-- END form submit button -->

				        </form>
				        <!-- END options form -->

				    </div>
				    <!-- END 'Custom CSS Editor' container -->

				</div>
				<!-- END 'Custom CSS Editor' tab content -->

				<!-- BEGIN 'Empty Cache Folder' tab content -->
				<div id="srp-tab-3" class="metabox-holder srp-tab-content">

					<!-- BEGIN options form -->
					<form id="srp-cache-flush-form" action="" method="POST">

						<!-- BEGIN options form entries -->
						<ul class="srp-settings-list">

							<!-- BEGIN 'Empty Cache Folder' -->
							<li>

								<!-- BEGIN 'Empty Cache Folder' label -->
								<label for="srp_cache_flush">
									<?php _e( 'Empty Cache Folder', SRP_TRANSLATION_ID ); ?>
								</label>
								<!-- END 'Empty Cache Folder' label -->

								<!-- BEGIN 'Empty Cache Folder' description -->
								<div class="srp-label-description">
									<?php _e( 'Click this button to empty the custom thumbnails cache folder.', SRP_TRANSLATION_ID ); ?>
								</div>
								<!-- END 'Empty Cache Folder' description -->

								<!-- BEGIN 'Empty Cache Folder' submit button -->
								<input type="hidden" value="yes" name="srp_cache_flush">
								<input type="submit" value="<?php _e( 'Empty Cache Folder', SRP_TRANSLATION_ID ); ?>" class="button-primary srp-empty-cache-btn">
								<!-- END 'Empty Cache Folder' submit button -->

							</li>
							<!-- BEGIN 'Empty Cache Folder' -->

						</ul>
						<!-- END options form entries -->

					</form>
					<!-- END options form -->
					
				</div>
				<!-- END 'Empty Cache Folder' tab content -->

				<!-- BEGIN 'Plugin Notes' tab content -->
				<div id="srp-tab-4" class="metabox-holder srp-tab-content srp-tab-plugin-info">

					<!-- BEGIN 'Plugin Notes' title -->
					<h3>
						<?php _e( 'Special Recent Posts FREE Edition', SRP_TRANSLATION_ID ); ?>
					</h3>
					<!-- END 'Plugin Notes' title -->

					<!-- BEGIN 'Plugin Notes' notes -->
					<dl>
						
						<!-- BEGIN Plugin Version -->
						<dt>
							<?php _e( 'Plugin Version:', SRP_TRANSLATION_ID ); ?>
						</dt>
						<dd>
							<?php echo SRP_PLUGIN_VERSION; ?>
						</dd>
						<!-- END Plugin Version -->

						<!-- BEGIN Latest update -->
						<dt>
							<?php _e( 'Latest update:', SRP_TRANSLATION_ID ); ?>
						</dt>
						<dd>
							<?php _e( 'September 27, 2014', SRP_TRANSLATION_ID ); ?>
						</dd>
						<!-- BEGIN Latest update -->

						<!-- BEGIN Website -->
						<dt>
							<?php _e( 'Website:', SRP_TRANSLATION_ID ); ?>
						</dt>
						<dd>
							<?php printf( '<a href="%1$s" title="%2$s" target="_blank">http://www.specialrecentposts.com/</a>', esc_url( 'http://www.specialrecentposts.com/?ref=uri_ps' ), __( 'The Special Recent Posts Official Website.', SRP_TRANSLATION_ID ) );?>
						</dd>
						<!-- BEGIN Website -->

						<!-- BEGIN Customer Support -->
						<dt>
							<?php _e( 'Customer Support & F.A.Q:', SRP_TRANSLATION_ID ); ?>
						</dt>
						<dd>
							<?php printf( '<a href="%1$s" title="%2$s" target="_blank">http://wordpress.org/support/plugin/special-recent-posts/</a>', esc_url( 'http://wordpress.org/support/plugin/special-recent-posts/' ), __( 'Visit the online Wordpress.org forum to get instant support.', SRP_TRANSLATION_ID ) );?>
						</dd>
						<!-- BEGIN Customer Support -->

						<!-- BEGIN Online Documentation & F.A.Q -->
						<dt>
							<?php _e( 'Online Documentation:', SRP_TRANSLATION_ID ); ?>
						</dt>
						<dd>
						<?php printf( '<a href="%1$s" title="%2$s" target="_blank">http://www.specialrecentposts.com/docs/</a>', esc_url( 'http://www.specialrecentposts.com/docs/?ref=docs_ps' ), __( 'Learn how to use SRP. View the online documentation.', SRP_TRANSLATION_ID ) );?>
						</dd>
						<!-- BEGIN Online Documentation & F.A.Q -->

						<!-- BEGIN Social Menu -->
						<dt>
							<?php _e( 'Follow Special Recent Posts on:', SRP_TRANSLATION_ID ); ?>
						</dt>
						<dd>
							<ul class="srp-social-list">

								<li>
									<a class="srp-social-icon-facebook" href="https://www.facebook.com/SpecialRecentPosts/" title="<?php echo esc_attr( __( 'Follow SRP on Facebook', SRP_TRANSLATION_ID ) ); ?>" target="_blank"></a>
								</li>

								<li>
									<a class="srp-social-icon-twitter" href="https://twitter.com/lucagrandicelli" title="<?php echo esc_attr( __( 'Follow Luca Grandicelli on Twitter', SRP_TRANSLATION_ID ) ); ?>" target="_blank"></a>
								</li>

								<li>
									<a class="srp-social-icon-googlep" href="https://google.com/+Specialrecentposts" title="<?php echo esc_attr( __( 'Follow SRP on Google+', SRP_TRANSLATION_ID ) ); ?>" target="_blank"></a>
								</li>

								<li>
									<a class="srp-social-icon-envato" href="http://codecanyon.net/user/lucagrandicelli" title="<?php echo esc_attr( __( 'Follow Luca Grandicelli on Envato', SRP_TRANSLATION_ID ) ); ?>" target="_blank"></a>
								</li>
								
							</ul>
						</dd>
						<!-- END Social Menu -->

					</dl>
					<!-- BEGIN 'Plugin Notes' notes -->

					<!-- BEGIN 'Plugin Notes' credits note -->
					<p>
						<?php printf( __( 'The Special Recent Posts plugin is created, developed and supported by %1$sLuca Grandicelli%2$s', SRP_TRANSLATION_ID ), '<a href="http://www.lucagrandicelli.co.uk/?ref=author_ps" title="Luca Grandicelli | Official Website" target="_blank">', '</a>' ); ?>
					</p>
					<!-- END 'Plugin Notes' credits note -->
					
				</div>
				<!-- END 'Plugin Notes' tab content -->

			</div>
			<!-- END rab element -->

		</div>
		<!-- END menu tabs container -->

	</div>
	<!-- END SRP admin container -->

</div>
<!-- END SRP admin page -->

<?php
	
	// Returning true.
	return true;
}

/*
| -------------------------------------------------------
| This is the main function to update form option data.
| -------------------------------------------------------
*/
/**
 * srp_update_data()
 *
 * This function builds the plugin admin page.
 *
 * @author Luca Grandicelli <lgrandicelli@gmail.com>
 * @copyright (C) 2011-2014 Luca Grandicelli
 * @package special-recent-posts-free
 * @version 2.0.4
 * @global $srp_default_plugin_values The default plugin presets.
 * @param array $data The $_POST data.
 * @return boolean true
 */
function srp_update_data( $data ) {

	// Checking that $_POST data exists.
	if ( isset( $_POST['srp_dataform'] ) ) {
	
		// Loading global default plugin presets.
		global $srp_default_plugin_values;
	
		// Removing the "srp_dataform" $_POST entry.
		unset( $data['srp_dataform'] );
		
		// Removing the "submit" $_POST entry.
		unset( $data['submit'] );
		
		// Validating text fields.		
		foreach ( $data as $k => $v ) {
			
			// Assigning global default value to noimage placeholder field, if this is empty.
			
			// Checking if the no-image placeholder field is empty.
			if ( ( empty( $v ) ) && ( 'srp_noimage_url' == $k ) ) {

				// Assigning the default value to the no-image placeholder.
				$data[ $k ] = $srp_default_plugin_values[ $k ];
			}

			// If the current processed field is the Custom CSS, strip some slashes from it.
			if( 'srp_custom_css' ==  $k ) {

				// Stripping slashes off the Custom CSS field.
				$data[ $k ] = stripslashes($v);
			}
		}
		
		// Updating WP Option with the new $_POST data.
		update_option( 'srp_plugin_options', $data );

		// Displaying the "save settings" message.
		echo '<div id="message" class="updated"><p><strong>' . __( 'Settings Saved', SRP_TRANSLATION_ID ) . '</strong></p></div>';
	}
	
	// Checking for Cache Flush Option.
	if ( isset( $_POST['srp_cache_flush'] ) && 'yes' == $_POST['srp_cache_flush'] ) {
		
		// Setting up cache folder path.
		$mydir = SRP_PLUGIN_DIR . SRP_CACHE_DIR;
		
		// Initializing directory class.
		$d = dir( $mydir ); 
		
		// Reading cache folder content.
		while( $entry = $d->read() ) { 
			
			// Checking if the cache directory is empty.
			if ( '.' != $entry && '..' != $entry ) { 
				
				// Deleting files.
				unlink( SRP_PLUGIN_DIR  . SRP_CACHE_DIR . $entry );
			} 
		}
		
		// Closing file connection.
		$d->close();
		
		// Displaying status message.
		echo '<div id="message" class="updated"><p><strong>' . __( 'Cache Folder Cleaned', SRP_TRANSLATION_ID ) . '</strong></p></div>';
	}

	// Returning true.
	return true;
}