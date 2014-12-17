<?php
ob_start();
class Ncr_Settings_Page {

	public static function initialize() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu_page' ) );
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
		<h2>No CAPTCHA reCAPTCHA</h2>

		<p>Protect WordPress login, registration and comment form with the new No CAPTCHA reCAPTCHA</p>

		<?php
		if ( isset( $_GET['settings-updated'] ) && ( $_GET['settings-updated'] ) ) {
			echo '<div id="message" class="updated"><p><strong>Settings saved. </strong></p></div>';
		}
		?>
		<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

		<!-- main content -->
		<div id="post-body-content">

		<div class="meta-box-sortables ui-sortable">

		<form method="post">

		<div class="postbox">

			<div title="Click to toggle" class="handlediv"><br></div>
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
								<?php _e( 'Used for displaying the CAPTCHA. Grab it <a href="https://www.google.com/recaptcha/admin" target="_blank">Here</a>', 'ncr-captcha' ); ?>

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
								<?php _e( 'Used for communication between your site and Google. Grab it <a href="https://www.google.com/recaptcha/admin" target="_blank">Here</a>', 'ncr-captcha' ); ?>
							</p>
						</td>
					</tr>
				</table>
				<p>
					<?php wp_nonce_field( 'ncr_settings_nonce' ); ?>
					<input class="button-primary" type="submit" name="settings_submit"
					       value="Save All Changes">
				</p>
			</div>
		</div>

		<div class="postbox">

			<div title="Click to toggle" class="handlediv"><br></div>
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
					       value="Save All Changes">
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
								<option value="light" <?php selected( 'light', $theme ); ?>>Light</option>
								<option value="dark" <?php selected( 'dark', $theme ); ?>>Dark</option>
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
								$languages = array(
									__( 'Auto Detect', 'ncr-captcha' )         => '',
									__( 'English', 'ncr-captcha' )             => 'en',
									__( 'Arabic', 'ncr-captcha' )              => 'ar',
									__( 'Bulgarian', 'ncr-captcha' )           => 'bg',
									__( 'Catalan Valencian', 'ncr-captcha' )   => 'ca',
									__( 'Czech', 'ncr-captcha' )               => 'cs',
									__( 'Danish', 'ncr-captcha' )              => 'da',
									__( 'German', 'ncr-captcha' )              => 'de',
									__( 'Greek', 'ncr-captcha' )               => 'el',
									__( 'British English', 'ncr-captcha' )     => 'en_gb',
									__( 'Spanish', 'ncr-captcha' )             => 'es',
									__( 'Persian', 'ncr-captcha' )             => 'fa',
									__( 'French', 'ncr-captcha' )              => 'fr',
									__( 'Canadian French', 'ncr-captcha' )     => 'fr_ca',
									__( 'Hindi', 'ncr-captcha' )               => 'hi',
									__( 'Croatian', 'ncr-captcha' )            => 'hr',
									__( 'Hungarian', 'ncr-captcha' )           => 'hu',
									__( 'Indonesian', 'ncr-captcha' )          => 'id',
									__( 'Italian', 'ncr-captcha' )             => 'it',
									__( 'Hebrew', 'ncr-captcha' )              => 'iw',
									__( 'Jananese', 'ncr-captcha' )            => 'ja',
									__( 'Korean', 'ncr-captcha' )              => 'ko',
									__( 'Lithuanian', 'ncr-captcha' )          => 'lt',
									__( 'Latvian', 'ncr-captcha' )             => 'lv',
									__( 'Dutch', 'ncr-captcha' )               => 'nl',
									__( 'Norwegian', 'ncr-captcha' )           => 'no',
									__( 'Polish', 'ncr-captcha' )              => 'pl',
									__( 'Portuguese', 'ncr-captcha' )          => 'pt',
									__( 'Romanian', 'ncr-captcha' )            => 'ro',
									__( 'Russian', 'ncr-captcha' )             => 'ru',
									__( 'Slovak', 'ncr-captcha' )              => 'sk',
									__( 'Slovene', 'ncr-captcha' )             => 'sl',
									__( 'Serbian', 'ncr-captcha' )             => 'sr',
									__( 'Swedish', 'ncr-captcha' )             => 'sv',
									__( 'Thai', 'ncr-captcha' )                => 'th',
									__( 'Turkish', 'ncr-captcha' )             => 'tr',
									__( 'Ukrainian', 'ncr-captcha' )           => 'uk',
									__( 'Vietnamese', 'ncr-captcha' )          => 'vi',
									__( 'Simplified Chinese', 'ncr-captcha' )  => 'zh_cn',
									__( 'Traditional Chinese', 'ncr-captcha' ) => 'zh_tw'
								);

								foreach ( $languages as $key => $value ) {
									echo "<option value='$value'" . selected( $value, $language, true ) . ">$key</option>";
								}
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
					       value="Save All Changes">
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
						<div style="text-align: center; margin: auto">Made with lots of love by <br> <a
								href="http://w3guy.com"><strong>Agbonghama Collins</strong></a></div>
					</div>
				</div>

				<div class="postbox">
					<div class="handlediv"><br></div>
					<h3 class="hndle" style="text-align: center;">
						<span><?php _e( 'Support Plugin', 'ncr-captcha' ); ?></span>
					</h3><div class="inside">
						<div style="text-align: center; margin: auto">
							<p>
								Is this plugin useful for you? If so, please help support its ongoing development and improvement with a <a href="https://flattr.com/submit/auto?user_id=tech4sky&url=https%3A%2F%2Fwordpress.org%2Fplugins%2Fno-captcha-recaptcha%2F" target="_blank">donation</a>.</p>
						<p>Or, if you are short on funds, there are other ways you can help out:</p>
						<ul>
							<li>Leave a positive review on the plugin's <a href="https://wordpress.org/support/view/plugin-reviews/no-captcha-recaptcha">WordPress listing</a></li>
							<li>Vote "Works" on the plugin's <a href="https://wordpress.org/plugins/no-captcha-recaptcha/#compatibility" target="_blank">WordPress listing</a></li>
							<li><a href="http://twitter.com/home?status=I%20love%20this%20WordPress%20plugin!%20http://wordpress.org/plugins/no-captcha-recaptcha/" target="_blank">Share your thoughts on Twitter</a></li>
						</ul></div>
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

			wp_redirect( '?page=ncr-config&settings-updated=true' );
		}
	}
}