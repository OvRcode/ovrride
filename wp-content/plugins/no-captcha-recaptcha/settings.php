<?php
ob_start();
class Ncr_Settings_Page {

	public static function initialize() {
		if( function_exists( 'is_plugin_active_for_network' )
		  && is_plugin_active_for_network( basename( dirname( __FILE__ ) ) . '/no-captcha-recaptcha.php' ) ){
			add_action( 'network_admin_menu', array( __CLASS__, 'register_menu_page' ) );
		} else {
			add_action( 'admin_menu', array( __CLASS__, 'register_menu_page' ) );
		}
	}


	public static function register_menu_page() {

		add_menu_page(
			'No CAPTCHA reCAPTCHA',
			'No CAPTCHA reCAPTCHA',
			'manage_options',
			'ncr-config',
			array(
				__CLASS__,
				'settings_page',
			),
			'dashicons-awards',
			'80.0215'
		);

	}

	public static function settings_page() {

		$ncr_options = get_option( 'ncr_options' ); //captcha_login
		$site_key    = isset( $ncr_options['site_key'] ) ? $ncr_options['site_key'] : '';
		$secrete_key = isset( $ncr_options['secrete_key'] ) ? $ncr_options['secrete_key'] : '';

		$captcha_login        = isset( $ncr_options['captcha_login'] ) ? $ncr_options['captcha_login'] : '';
		$captcha_registration = isset( $ncr_options['captcha_registration'] ) ? $ncr_options['captcha_registration'] : '';
		$captcha_comment      = isset( $ncr_options['captcha_comment'] ) ? $ncr_options['captcha_comment'] : '';

		$theme         = isset( $ncr_options['theme'] ) ? $ncr_options['theme'] : '';
		$language      = isset( $ncr_options['language'] ) ? $ncr_options['language'] : '';
		$error_message = isset( $ncr_options['error_message'] ) ? $ncr_options['error_message'] : '';

		// call to save the setting options
		self::save_options();
		?>
		<style>
			input[type='text'], textarea, select {
				width: 600px;
			}
		</style>
		<div class="wrap">

		<div id="icon-options-general" class="icon32"></div>
		<h2><?php _e( 'No CAPTCHA reCAPTCHA', 'ncr-captcha'); ?></h2>

		<p><?php _e( 'Protect WordPress login, registration and comment form with the new No CAPTCHA reCAPTCHA', 'ncr-captcha'); ?></p>

		<?php
		if ( isset( $_GET['settings-updated'] ) && ( $_GET['settings-updated'] ) ) {
			echo '<div id="message" class="updated"><p><strong>' . __('Settings saved', 'ncr-captcha') . '</strong></p></div>';
		}
		?>
		<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

		<!-- main content -->
		<div id="post-body-content">

		<div class="meta-box-sortables ui-sortable">

		<form method="post">

		<div class="postbox">

			<div title="<?php _e( 'Click to toggle', 'ncr-captcha'); ?>" class="handlediv"><br></div>
			<h3 class="hndle"><span><?php _e( 'reCAPTCHA Keys', 'ncr-captcha' ); ?></span></h3>

			<div class="inside">
				<table class="form-table">
					<tr>
						<th scope="row"><label
								for="site-key"><?php _e( 'Site key', 'ncr-captcha' ); ?></label>
						</th>
						<td>
							<input id="site-key" type="text" name="ncr_options[site_key]"
							       value="<?php echo $site_key; ?>">

							<p class="description">
								<?php
									_e( 'Used for displaying the CAPTCHA.', 'ncr-captcha' );
									echo ' ';
									// escape the URL properly
									$url = 'https://www.google.com/recaptcha/admin';
									printf( wp_kses( __( 'Grab it <a href="%s" target="_blank">Here</a>', 'ncr-captcha' ), array(  'a' => array( 'href' => array(), 'target' => array('_blank') ) ) ), esc_url( $url ) );
								?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label
								for="secrete-key"><?php _e( 'Secret key', 'ncr-captcha' ); ?></label>
						</th>
						<td>
							<input id="secrete-key" type="text" name="ncr_options[secrete_key]"
							       value="<?php echo $secrete_key; ?>">

							<p class="description">
								<?php
									_e( 'Used for communication between your site and Google.', 'ncr-captcha' );
									echo ' ';
									// escape the URL properly
									$url = 'https://www.google.com/recaptcha/admin';
									printf( wp_kses( __( 'Grab it <a href="%s" target="_blank">Here</a>', 'ncr-captcha' ), array(  'a' => array( 'href' => array(), 'target' => array('_blank') ) ) ), esc_url( $url ) );
								?>
							</p>
						</td>
					</tr>
				</table>
				<p>
					<?php wp_nonce_field( 'ncr_settings_nonce' ); ?>
					<input class="button-primary" type="submit" name="settings_submit"
					       value="<?php _e( 'Save All Changes', 'ncr-captcha' ); ?>">
				</p>
			</div>
		</div>

		<div class="postbox">

			<div title="<?php _e( 'Click to toggle', 'ncr-captcha'); ?>" class="handlediv"><br></div>
			<h3 class="hndle"><span><?php _e( 'Display Settings', 'ncr-captcha' ); ?></span></h3>

			<div class="inside">
				<table class="form-table">
					<tr>
						<th scope="row"><label for="login"><?php _e( 'Login Form', 'ncr-captcha' ); ?></label>
						</th>
						<td>
							<input id="login" type="checkbox" name="ncr_options[captcha_login]"
							       value="yes" <?php checked( $captcha_login, 'yes' ) ?>>

							<p class="description">
								<?php _e( 'Check to enable CAPTCHA in login form', 'ncr-captcha' ); ?>

							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label
								for="registration"><?php _e( 'Registration Form', 'ncr-captcha' ); ?></label>
						</th>
						<td>
							<input id="registration" type="checkbox" name="ncr_options[captcha_registration]"
							       value="yes" <?php checked( $captcha_registration, 'yes' ) ?>>

							<p class="description">
								<?php _e( 'Check to enable CAPTCHA in WordPress registration form', 'ncr-captcha' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="comment"><?php _e( 'Comment Form', 'ncr-captcha' ); ?></label>
						</th>
						<td>
							<input id="comment" type="checkbox" name="ncr_options[captcha_comment]"
							       value="yes" <?php checked( $captcha_comment, 'yes' ) ?>>

							<p class="description">
								<?php _e( 'Check to enable CAPTCHA in WordPress comment system', 'ncr-captcha' ); ?>
							</p>
						</td>
					</tr>
				</table>
				<p>
					<?php wp_nonce_field( 'ncr_settings_nonce' ); ?>
					<input class="button-primary" type="submit" name="settings_submit"
					       value="<?php _e( 'Save All Changes', 'ncr-captcha' ); ?>">
				</p>
			</div>
		</div>


		<div class="postbox">

			<div class="handlediv"><br></div>
			<h3 class="hndle"><span><?php _e( 'General Settings', 'ncr-captcha' ); ?></span>
			</h3>

			<div class="inside">
				<table class="form-table">
					<tr>
						<th scope="row"><label
								for="theme"><?php _e( 'Theme', 'ncr-captcha' ); ?></label></th>
						<td>
							<select id="theme" name="ncr_options[theme]">
								<option value="light" <?php selected( 'light', $theme ); ?>><?php _e( 'Light', 'ncr-captcha' ); ?></option>
								<option value="dark" <?php selected( 'dark', $theme ); ?>><?php _e( 'Dark', 'ncr-captcha' ); ?></option>
							</select>

							<p class="description">
								<?php _e( 'The theme colour of the widget.', 'ncr-captcha' ); ?>
							</p>
						</td>
					</tr>
				</table>

				<table class="form-table">
					<tr>
						<th scope="row"><label
								for="theme"><?php _e( 'Language', 'ncr-captcha' ); ?></label>
						</th>
						<td>
							<select id="theme" name="ncr_options[language]">
								<?php
									printf(
										'<option value="" %s>%s</option>',
										selected( '', $language, false ),
										__( 'Auto Detect', 'ncr-captcha' )
									);

									/** WordPress Translation Install API */
									require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );

									// display the list of available languages in WP core
									$available_languages = get_available_languages();
									$available_translations = wp_get_available_translations();
									foreach ( $available_languages as $l ) {
										printf(
											'<option value="%s" lang="%s"%s>%s</option>',
											esc_attr( $l ),
											esc_attr( current( $available_translations[$l]['iso'] ) ),
											selected( $l, $language, false ),
											esc_html( $available_translations[$l]['native_name'] )
										);
									}
									printf(
										'<option value="en_US" lang="en"%s>English (United States)</option>',
										selected( 'en_US', $language, false )
									);
								?>
							</select>

							<p class="description">
								<?php _e( 'Forces the widget to render in a specific language', 'ncr-captcha' ); ?>
							</p>
						</td>
					</tr>
				</table>
				<table class="form-table">
					<tr>
						<th scope="row"><label
								for="message"><?php _e( 'Error Message', 'ncr-captcha' ); ?></label>
						</th>
						<td>
							<input id="message" type="text" name="ncr_options[error_message]"
							       value="<?php echo $error_message; ?>">

							<p class="description">
								<?php _e( 'Message or text to display when CAPTCHA is ignored or the test is failed.', 'ncr-captcha' ); ?>
							</p>
						</td>
					</tr>
				</table>
				<p>
					<?php wp_nonce_field( 'settings_nonce' ); ?>
					<input class="button-primary" type="submit" name="settings_submit"
					       value="<?php _e( 'Save All Changes', 'ncr-captcha' ); ?>">
				</p>
			</div>
		</div>
		</form>
		</div>
		</div>
		<div id="postbox-container-1" class="postbox-container">

			<div class="meta-box-sortables">

				<div class="postbox">
					<div class="handlediv"><br></div>
					<h3 class="hndle" style="text-align: center;">
						<span><?php _e( 'Developer', 'ncr-captcha' ); ?></span>
					</h3>

					<div class="inside">
						<div style="text-align: center; margin: auto"><?php _e( 'Made with lots of love by', 'ncr-captcha' );?> <br>
						<?php /* translators: plugin author name */ ?>
						 <a href="http://w3guy.com"><strong><?php _e( 'Agbonghama Collins', 'ncr-captcha' );?></strong></a></div>
					</div>
				</div>

				<div class="postbox">
					<div class="handlediv"><br></div>
					<h3 class="hndle" style="text-align: center;">
						<span><?php _e( 'Support Plugin', 'ncr-captcha' ); ?></span>
					</h3><div class="inside">
						<div style="text-align: center; margin: auto">
							<?php 
								// escape the URLs properly
								$flattr_url = 'https://flattr.com/submit/auto?user_id=tech4sky&url=https%3A%2F%2Fwordpress.org%2Fplugins%2Fno-captcha-recaptcha%2F';
								$review_url = 'https://wordpress.org/support/view/plugin-reviews/no-captcha-recaptcha';
								$compatibility_url = 'https://wordpress.org/plugins/no-captcha-recaptcha/#compatibility';
								$twitter_url = 'http://twitter.com/home?status=I%20love%20this%20WordPress%20plugin!%20http://wordpress.org/plugins/no-captcha-recaptcha/';
							?>
							<p><?php printf( wp_kses( __( 'Is this plugin useful for you? If so, please help support its ongoing development and improvement with a <a href="%s" target="_blank">donation</a>.', 'ncr-captcha' ), array(  'a' => array( 'href' => array(), 'target' => array('_blank') ) ) ), esc_url( $flattr_url ) ); ?></p>
							<p><?php _e( 'Or, if you are short on funds, there are other ways you can help out:', 'ncr-captcha' ); ?></p>
							<ul>
								<li><?php printf( wp_kses( __( 'Leave a positive review on the plugin\'s <a href="%s">WordPress listing</a>', 'ncr-captcha' ), array(  'a' => array( 'href' => array(), 'target' => array('_blank') ) ) ), esc_url( $review_url ) ); ?></li>
								<li><?php printf( wp_kses( __( 'Vote "Works" on the plugin\'s <a href="%s" target="_blank">WordPress listing</a>', 'ncr-captcha' ), array(  'a' => array( 'href' => array(), 'target' => array('_blank') ) ) ), esc_url( $compatibility_url ) ); ?></li>
								<li><?php printf( wp_kses( __( '<a href="%s" target="_blank">Share your thoughts on Twitter</a>', 'ncr-captcha' ), array(  'a' => array( 'href' => array(), 'target' => array('_blank') ) ) ), esc_url( $twitter_url ) ); ?></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
		<br class="clear">
		</div>
		</div>
	<?php
	}


	public static function save_options() {
		if ( isset( $_POST['settings_submit'] ) && check_admin_referer( 'settings_nonce', '_wpnonce' ) ) {

			$saved_options = $_POST['ncr_options'];

			update_option( 'ncr_options', $saved_options );

			wp_redirect( '?page=ncr-config&settings-updated=true' ); exit;
		}
	}
}
