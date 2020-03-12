<?php

$settings = FLBuilderWhiteLabel::get_help_button_settings();

?>
<div id="fl-help-button-form" class="fl-settings-form">

	<h3 class="fl-settings-form-header"><?php _e( 'Help Button Settings', 'fl-builder' ); ?></h3>

	<form id="help-button-form" action="<?php FLBuilderAdminSettings::render_form_action( 'help-button' ); ?>" method="post">

		<div class="fl-settings-form-content">

			<p>
				<label>
					<input type="checkbox" name="fl-help-button-enabled" value="1" <?php checked( $settings['enabled'], 1 ); ?> />
					<span><?php _e( 'Enable Help Button', 'fl-builder' ); ?></span>
				</label>
			</p>

			<div class="fl-help-button-settings">

				<h4><?php _e( 'Help Tour', 'fl-builder' ); ?></h4>
				<p>
					<label>
						<input type="checkbox" name="fl-help-tour-enabled" value="1" <?php checked( $settings['tour'], 1 ); ?> />
						<span><?php _e( 'Enable Help Tour', 'fl-builder' ); ?></span>
					</label>
				</p>

				<h4><?php _e( 'Help Video', 'fl-builder' ); ?></h4>
				<p>
					<label>
						<input type="checkbox" name="fl-help-video-enabled" value="1" <?php checked( $settings['video'], 1 ); ?> />
						<span><?php _e( 'Enable Help Video', 'fl-builder' ); ?></span>
					</label>
				</p>
				<p class="fl-help-video-embed">
					<label><?php _e( 'Help Video Embed Code', 'fl-builder' ); ?></label>
				</p>
				<p class="fl-help-video-embed">
					<input type="text" name="fl-help-video-embed" class="regular-text" value="<?php echo htmlspecialchars( $settings['video_embed'] ); ?>" />
				</p>

				<h4><?php _e( 'Knowledge Base', 'fl-builder' ); ?></h4>
				<p>
					<label>
						<input type="checkbox" name="fl-knowledge-base-enabled" value="1" <?php checked( $settings['knowledge_base'], 1 ); ?> />
						<span><?php _e( 'Enable Knowledge Base', 'fl-builder' ); ?></span>
					</label>
				</p>
				<p class="fl-knowledge-base-url">
					<label><?php _e( 'Knowledge Base URL', 'fl-builder' ); ?></label>
				</p>
				<p class="fl-knowledge-base-url">
					<input type="text" name="fl-knowledge-base-url" class="regular-text" value="<?php echo esc_url_raw( $settings['knowledge_base_url'] ); ?>" />
				</p>

				<h4><?php _e( 'Contact Support', 'fl-builder' ); ?></h4>
				<p>
					<label>
						<input type="checkbox" name="fl-forums-enabled" value="1" <?php checked( $settings['forums'], 1 ); ?> />
						<span><?php _e( 'Enable Contact Support', 'fl-builder' ); ?></span>
					</label>
				</p>
				<p class="fl-forums-url">
					<label><?php _e( 'Contact Support URL', 'fl-builder' ); ?></label>
				</p>
				<p class="fl-forums-url">
					<input type="text" name="fl-forums-url" class="regular-text" value="<?php echo esc_url_raw( $settings['forums_url'] ); ?>" />
				</p>

			</div>

		</div>
		<p class="submit">
			<input type="submit" name="update" class="button-primary" value="<?php esc_attr_e( 'Save Help Button Settings', 'fl-builder' ); ?>" />
			<?php wp_nonce_field( 'help-button', 'fl-help-button-nonce' ); ?>
		</p>
	</form>
</div>
