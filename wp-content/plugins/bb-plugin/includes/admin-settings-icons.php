<div id="fl-icons-form" class="fl-settings-form">

	<h3 class="fl-settings-form-header"><?php _e( 'Icon Settings', 'fl-builder' ); ?></h3>

	<?php

	if ( FLBuilderAdminSettings::multisite_support() && ! is_network_admin() ) {

		global $blog_id;

		if ( BLOG_ID_CURRENT_SITE == $blog_id ) {
			?>
			<p><?php _e( 'Icons for the main site must be managed in the network admin.', 'fl-builder' ); ?></p>
			</div>
			<?php
			return;
		}
	}

	?>

	<form id="icons-form" action="<?php FLBuilderAdminSettings::render_form_action( 'icons' ); ?>" method="post">

		<?php if ( FLBuilderAdminSettings::multisite_support() && ! is_network_admin() ) : ?>
		<label>
			<input class="fl-override-ms-cb" type="checkbox" name="fl-override-ms" value="1" <?php echo ( get_option( '_fl_builder_enabled_icons' ) ) ? 'checked="checked"' : ''; ?> />
			<?php _e( 'Override network settings?', 'fl-builder' ); ?>
		</label>
		<?php endif; ?>

		<div class="fl-settings-form-content">
			<?php /* translators: %s: docs link */ ?>
			<p><?php printf( __( 'Enable or disable icon sets using the options below or upload a custom icon set. Instructions on how to generate your own custom icon sets can be read %s.', 'fl-builder' ), sprintf( '<a href="https://kb.wpbeaverbuilder.com/article/110-enable-disable-or-import-new-icon-sets" target="_blank">%s</a>', _x( 'here', 'Link text', 'fl-builder' ) ) ); ?></p>

			<?php

			$enabled_icons = FLBuilderModel::get_enabled_icons();
			$icon_sets     = FLBuilderIcons::get_sets_for_current_site();

			foreach ( $icon_sets as $key => $set ) {
				$checked = in_array( $key, $enabled_icons ) ? ' checked' : '';
				?>
				<p>
					<label>
						<input type="checkbox" name="fl-enabled-icons[]" value="<?php echo $key; ?>" <?php echo $checked; ?>>
						<?php echo ' ' . $set['name']; ?>
						<?php if ( 'core' != $set['type'] ) : ?>
						<a href="javascript:void(0);" class="fl-delete-icon-set" data-set="<?php echo $key; ?>"><?php _ex( 'Delete', 'Plugin setup page: Delete icon set.', 'fl-builder' ); ?></a>
						<?php endif; ?>
					</label>
				</p>
				<?php
			}

			?>

		</div>
		<p class="submit">
			<input type="button" name="fl-upload-icon" class="button" value="<?php esc_attr_e( 'Upload Icon Set', 'fl-builder' ); ?>" />
			<input type="submit" name="fl-save-icons" class="button-primary" value="<?php esc_attr_e( 'Save Icon Settings', 'fl-builder' ); ?>" />
			<input type="hidden" name="fl-new-icon-set" value="" />
			<input type="hidden" name="fl-delete-icon-set" value="" />
			<?php wp_nonce_field( 'icons', 'fl-icons-nonce' ); ?>
		</p>
	</form>
</div>
