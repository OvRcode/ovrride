<select name="{{data.name}}"<# if ( data.field.className ) { #> {{data.field.className}}<# } #>>
	<?php foreach ( FLBuilderLoop::post_types() as $slug => $type ) : ?>
	<option value="<?php echo $slug; ?>"<# if ( data.value === '<?php echo $slug; ?>' ) { #> selected="selected"<# } #>><?php echo $type->labels->name; ?></option>
	<?php endforeach; ?>
</select>
