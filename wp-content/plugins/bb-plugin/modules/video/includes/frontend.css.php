<?php if ( $module->video_aspect_ratio() ) : ?>
.fl-node-<?php echo $id; ?> .fl-wp-video {
	padding-bottom: <?php echo $module->video_aspect_ratio(); ?>%;
}
<?php endif; ?>
