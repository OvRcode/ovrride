<?php

$vid_data = $module->get_data();
$preload  = FLBuilderModel::is_builder_active() && ! empty( $vid_data->poster ) ? ' preload="none"' : '';
$schema   = $module->get_structured_data( $module );

?>
<div class="fl-video fl-<?php echo ( 'media_library' == $settings->video_type ) ? 'wp' : 'embed'; ?>-video"<?php $schema ? FLBuilder::print_schema( ' itemscope itemtype="https://schema.org/VideoObject"' ) : ''; ?>>
	<?php

	global $wp_embed;
	if ( $schema ) {
		echo $schema;
	}

	if ( $vid_data && 'media_library' == $settings->video_type ) {
		echo '<meta itemprop="url" content="' . $vid_data->url . '" />';
		echo ( ! $schema ) ? '<meta itemprop="thumbnail" content="' . $vid_data->poster . '" />' : '';
		echo '[video width="100%" height="100%" ' . $vid_data->extension . '="' . $vid_data->url . '"' . $vid_data->video_webm . ' poster="' . $vid_data->poster . '"' . $vid_data->autoplay . $vid_data->loop . $preload . '][/video]';
	} elseif ( 'embed' == $settings->video_type ) {
		echo $wp_embed->autoembed( $settings->embed_code );
	}
	?>
</div>
