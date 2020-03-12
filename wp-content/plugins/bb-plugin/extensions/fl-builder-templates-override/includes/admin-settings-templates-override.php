<h4><?php _e( 'Override Core Templates', 'fl-builder' ); ?></h4>
<?php if ( is_network_admin() ) : ?>
<p><?php _e( 'Enter the ID of a site on the network whose templates should override core builder templates. Leave this field blank if you do not wish to override core templates.', 'fl-builder' ); ?></p>
<p>
	<input type="text" name="fl-templates-override" value="<?php echo ( $site_id ) ? $site_id : ''; ?>" size="5" />
</p>
<?php elseif ( ! is_multisite() ) : ?>
<p><?php _e( 'Use this setting to override core builder templates with your templates.', 'fl-builder' ); ?></p>
<p>
	<label>
		<input type="checkbox" name="fl-templates-override" value="1" <?php checked( $site_id, 1 ); ?> />
		<span><?php _e( 'Override Core Templates', 'fl-builder' ); ?></span>
	</label>
</p>
<?php endif; ?>
<div class="fl-templates-override-nodes">
	<p><?php _e( 'Show saved row, column and module categories as sections in the page builder sidebar. A new section will be created for category.', 'fl-builder' ); ?></p>
	<p>
		<label>
			<input type="checkbox" name="fl-templates-override-rows" value="1" <?php checked( $show_rows, 1 ); ?> />
			<span><?php _e( 'Show Saved Row Categories?', 'fl-builder' ); ?></span>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="fl-templates-override-columns" value="1" <?php checked( $show_columns, 1 ); ?> />
			<span><?php _e( 'Show Saved Column Categories?', 'fl-builder' ); ?></span>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="fl-templates-override-modules" value="1" <?php checked( $show_modules, 1 ); ?> />
			<span><?php _e( 'Show Saved Module Categories?', 'fl-builder' ); ?></span>
		</label>
	</p>
</div>
