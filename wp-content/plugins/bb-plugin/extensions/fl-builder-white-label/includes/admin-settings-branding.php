<div id="fl-branding-form" class="fl-settings-form">

	<form class="fl-settings-form-content" action="<?php FLBuilderAdminSettings::render_form_action( 'branding' ); ?>" method="post">

		<h3 class="fl-settings-form-header"><?php _e( 'Plugin Branding', 'fl-builder' ); ?></h3>
		<p><?php _e( 'White label the Beaver Builder plugin using the settings below.', 'fl-builder' ); ?></p>

		<h4><?php _e( 'Plugin Name', 'fl-builder' ); ?></h4>
		<input type="text" name="fl-branding" value="<?php echo esc_html( FLBuilderWhiteLabel::get_branding() ); ?>" class="regular-text" />

		<h4><?php _e( 'Plugin Icon URL', 'fl-builder' ); ?></h4>
		<input type="text" name="fl-branding-icon" value="<?php echo esc_html( FLBuilderWhiteLabel::get_branding_icon() ); ?>" class="regular-text" />

		<br /><br />

		<h3 class="fl-settings-form-header"><?php _e( 'Theme Branding', 'fl-builder' ); ?></h3>
		<p><?php _e( 'White label the Beaver Builder theme using the settings below.', 'fl-builder' ); ?></p>

		<?php $theme_data = FLBuilderWhiteLabel::get_theme_branding(); ?>

		<h4><?php _e( 'Theme Name', 'fl-builder' ); ?></h4>
		<input type="text" name="fl-theme-branding-name" value="<?php echo stripslashes_deep( esc_html( $theme_data['name'] ) ); ?>" class="regular-text" />

		<h4><?php _e( 'Theme Description', 'fl-builder' ); ?></h4>
		<input type="text" name="fl-theme-branding-description" value="<?php echo stripslashes_deep( esc_html( $theme_data['description'] ) ); ?>" class="regular-text" />

		<h4><?php _e( 'Theme Company Name', 'fl-builder' ); ?></h4>
		<input type="text" name="fl-theme-branding-company-name" value="<?php echo stripslashes_deep( esc_html( $theme_data['company_name'] ) ); ?>" class="regular-text" />

		<h4><?php _e( 'Theme Company URL', 'fl-builder' ); ?></h4>
		<input type="text" name="fl-theme-branding-company-url" value="<?php echo esc_html( $theme_data['company_url'] ); ?>" class="regular-text" />

		<h4><?php _e( 'Theme Screenshot URL', 'fl-builder' ); ?></h4>
		<input type="text" name="fl-theme-branding-screenshot-url" value="<?php echo esc_html( $theme_data['screenshot_url'] ); ?>" class="regular-text" />

		<p class="submit">
			<input type="submit" name="update" class="button-primary" value="<?php esc_attr_e( 'Save Branding', 'fl-builder' ); ?>" />
			<?php wp_nonce_field( 'branding', 'fl-branding-nonce' ); ?>
		</p>
	</form>

</div>
