<#

var defaults = {
	style: '',
	color: '',
	width: {
		top: '',
		right: '',
		bottom: '',
		left: '',
	},
	radius: {
		top_left: '',
		top_right: '',
		bottom_left: '',
		bottom_right: '',
	},
	shadow: {
		color: '',
		horizontal: '',
		vertical: '',
		blur: '',
		spread: '',
	},
};

var value = '' === data.value ? defaults : jQuery.extend( true, defaults, data.value );

var style = wp.template( 'fl-builder-field-select' )( {
	name: data.name + '[][style]',
	value: value.style,
	field: {
		options: {
			'': '<?php esc_attr_e( 'Default', 'fl-builder' ); ?>',
			'none': '<?php esc_attr_e( 'None', 'fl-builder' ); ?>',
			'solid': '<?php esc_attr_e( 'Solid', 'fl-builder' ); ?>',
			'dashed': '<?php esc_attr_e( 'Dashed', 'fl-builder' ); ?>',
			'dotted': '<?php esc_attr_e( 'Dotted', 'fl-builder' ); ?>',
			'double': '<?php esc_attr_e( 'Double', 'fl-builder' ); ?>',
		},
	},
} );

var color = wp.template( 'fl-builder-field-color' )( {
	name: data.name + '[][color]',
	value: value.color,
	field: {
		className: 'fl-border-field-color',
		show_reset: true,
		show_alpha: true,
	},
} );

var width = wp.template( 'fl-builder-field-dimension' )( {
	name: data.name,
	rootName: data.name,
	names: {
		top: data.name + '[][width][top]',
		right: data.name + '[][width][right]',
		bottom: data.name + '[][width][bottom]',
		left: data.name + '[][width][left]',
	},
	values: {
		top: value.width.top,
		right: value.width.right,
		bottom: value.width.bottom,
		left: value.width.left,
	},
	field: {
		units: [ 'px' ],
		slider: true,
	},
} );

var radius = wp.template( 'fl-builder-field-dimension' )( {
	name: data.name,
	rootName: data.name,
	names: {
		top_left: data.name + '[][radius][top_left]',
		top_right: data.name + '[][radius][top_right]',
		bottom_left: data.name + '[][radius][bottom_left]',
		bottom_right: data.name + '[][radius][bottom_right]',
	},
	values: {
		top_left: value.radius.top_left,
		top_right: value.radius.top_right,
		bottom_left: value.radius.bottom_left,
		bottom_right: value.radius.bottom_right,
	},
	field: {
		units: [ 'px' ],
		slider: true,
		keys: {
			top_left: '<?php esc_attr_e( 'Left', 'fl-builder' ); ?>',
			top_right: '<?php esc_attr_e( 'Right', 'fl-builder' ); ?>',
			bottom_left: '<?php esc_attr_e( 'Left', 'fl-builder' ); ?>',
			bottom_right: '<?php esc_attr_e( 'Right', 'fl-builder' ); ?>',
		},
	},
} );

var shadow = wp.template( 'fl-builder-field-shadow' )( {
	name: data.name + '[][shadow]',
	value: value.shadow,
	field: {
		show_spread: true,
	},
} );

#>
<div class="fl-compound-field fl-border-field">
	<div class="fl-compound-field-section fl-border-field-section-general">
		<div class="fl-compound-field-section-toggle">
			<i class="dashicons dashicons-arrow-right-alt2"></i>
			<?php _e( 'General', 'fl-builder' ); ?>
		</div>
		<div class="fl-compound-field-row">
			<div class="fl-compound-field-setting fl-border-field-style" data-property="border-style">
				<label class="fl-compound-field-label">
					<?php _e( 'Style', 'fl-builder' ); ?>
				</label>
				{{{style}}}
			</div>
			<div class="fl-compound-field-setting fl-border-field-color" data-property="border-color">
				<label class="fl-compound-field-label">
					<?php _e( 'Color', 'fl-builder' ); ?>
				</label>
				{{{color}}}
			</div>
		</div>
		<div class="fl-compound-field-row">
			<div class="fl-compound-field-setting fl-border-field-width" data-property="border-width">
				<label class="fl-compound-field-label">
					<?php _e( 'Width', 'fl-builder' ); ?>
				</label>
				{{{width}}}
			</div>
		</div>
	</div>
	<div class="fl-compound-field-section fl-border-field-section-radius">
		<div class="fl-compound-field-section-toggle">
			<i class="dashicons dashicons-arrow-right-alt2"></i>
			<?php _e( 'Radius &amp; Shadow', 'fl-builder' ); ?>
		</div>
		<div class="fl-compound-field-row">
			<div class="fl-compound-field-setting fl-border-field-radius" data-property="border-radius">
				<label class="fl-compound-field-label">
					<?php _e( 'Radius', 'fl-builder' ); ?>
				</label>
				{{{radius}}}
			</div>
		</div>
		<div class="fl-compound-field-row">
			<div class="fl-compound-field-setting fl-border-field-shadow" data-property="box-shadow">
				<label class="fl-compound-field-label">
					<?php _e( 'Box Shadow', 'fl-builder' ); ?>
				</label>
				{{{shadow}}}
			</div>
		</div>
	</div>
</div>
