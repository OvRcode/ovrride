
.featured-category-cta-<?php echo $id; ?> .cat-heading {
	text-align: <?php echo $settings->heading_text_align; ?>;
	<?php if ($settings->heading_color): ?>
		color: #<?php echo $settings->heading_color; ?>;
	<?php endif; ?>
	margin: <?php echo $settings->heading_margins_top.'px '.$settings->heading_margins_right.'px '.$settings->heading_margins_bottom.'px '.$settings->heading_margins_left.'px '; ?>;
	padding: <?php echo $settings->heading_padding_top.'px '.$settings->heading_padding_right.'px '.$settings->heading_padding_bottom.'px '.$settings->heading_padding_left.'px '; ?>;
}
.featured-category-cta-<?php echo $id; ?> .cat-subheading {
	text-align: <?php echo $settings->subheading_text_align; ?>;
	<?php if ($settings->subheading_color): ?>
		color: #<?php echo $settings->subheading_color; ?>;
	<?php endif; ?>
	margin: <?php echo $settings->subheading_margins_top.'px '.$settings->subheading_margins_right.'px '.$settings->subheading_margins_bottom.'px '.$settings->subheading_margins_left.'px '; ?>;
	padding: <?php echo $settings->subheading_padding_top.'px '.$settings->subheading_padding_right.'px '.$settings->subheading_padding_bottom.'px '.$settings->subheading_padding_left.'px '; ?>;
}
.featured-category-cta-<?php echo $id; ?> .cta-block .feat-cat-hover {
	background: <?php echo $settings->overlay_hover_background; ?>;
	background: #<?php echo $settings->overlay_hover_background; ?>;
}

.featured-category-cta-<?php echo $id; ?> .cta-block:hover .feat-cat-overlay .title-overlay,
.featured-category-cta-<?php echo $id; ?> .cta-block:focus .feat-cat-overlay .title-overlay,
.featured-category-cta-<?php echo $id; ?> .cta-block.hover .feat-cat-overlay .title-overlay {
	background: <?php echo $settings->title_hover_background; ?>;
	background: #<?php echo $settings->title_hover_background; ?>;
	display: <?php echo $settings->heading_on_hover; ?>;
}
.featured-category-cta-<?php echo $id; ?> .cta-block .feat-cat-overlay .title-overlay {
	padding: <?php echo $settings->block_padding_top.'px '.$settings->block_padding_right.'px '.$settings->block_padding_bottom.'px '.$settings->block_padding_left.'px '; ?>;
	background: <?php echo $settings->title_background; ?>;
	background: #<?php echo $settings->title_background; ?>;
}
.featured-category-cta-<?php echo $id; ?> .cta-block:hover .cat-heading,
.featured-category-cta-<?php echo $id; ?> .cta-block:focus .cat-heading,
.featured-category-cta-<?php echo $id; ?> .cta-block.hover .cat-heading {
	<?php if ($settings->heading_hover_color): ?>
		color: #<?php echo $settings->heading_hover_color; ?>;
	<?php endif; ?>
}
.featured-category-cta-<?php echo $id; ?> .cta-block:hover .cat-subheading,
.featured-category-cta-<?php echo $id; ?> .cta-block:focus .cat-subheading,
.featured-category-cta-<?php echo $id; ?> .cta-block.hover .cat-subheading {
	<?php if ($settings->subheading_hover_color): ?>
		color: #<?php echo $settings->subheading_hover_color; ?>;
	<?php endif; ?>
}