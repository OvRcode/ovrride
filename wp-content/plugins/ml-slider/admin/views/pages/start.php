<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<div id="metaslider-ui">
<div class="metaslider-start mt-16">
	<div class="metaslider-welcome">
		<div class="welcome-panel-content items-center">
			<h2><?php esc_html_e('Thanks for using MetaSlider, the WordPress slideshow plugin', 'ml-slider'); ?></h2>
		</div>
		<div class="welcome-panel-content" style="min-height:270px;">
			<div class="ms-panel-container">
				<div class="">
					<div>
						<h3 class="ms-heading"><?php esc_html_e('Create a slideshow with your images', 'ml-slider'); ?></h3>
						<p><?php esc_html_e('To get started, drag and drop your images below.', 'ml-slider'); ?></p>
					</div>
					<div>
							<metaslider-dragdrop-import></metaslider-dragdrop-import>
							<?php
								$max_upload_size = wp_max_upload_size();
								if (!$max_upload_size) $max_upload_size = 0;

								printf(esc_html('Maximum upload file size: %s.' ), esc_html(size_format($max_upload_size)));

								/*
								TODO: Maybe add a button to show the media library uploader
								<p><a class="button button-primary button-hero install-now" href="#">Open media library</a></p>
								<p><a href="#"><?php// _e('Learn more about this tool', 'ml-slider'); ?></a></p>
								*/ ?>
					</div>
				</div>
				<div class="">

					<div>
						<h3 class="ms-heading"><?php esc_html_e('Create a slideshow with sample images', 'ml-slider'); ?></h3>
						<p><?php esc_html_e('Choose one of our demos with sample images, or a blank slideshow with no images.', 'ml-slider'); ?></p>
					</div>

					<div class="try-gutenberg-action">
						<select id="sampleslider-options">
							<option value="<?php echo esc_url(wp_nonce_url(admin_url("admin-post.php?action=metaslider_create_slider"), "metaslider_create_slider")); ?>"><?php esc_html_e('Blank Slideshow', 'ml-slider'); ?></option>
							<option value="<?php echo esc_url(wp_nonce_url(admin_url("admin-post.php?action=metaslider_create_slider&metaslider_add_sample_slides"), "metaslider_create_slider")); ?>"><?php esc_html_e('Image Slideshow', 'ml-slider'); ?></option>
							<option value="<?php echo esc_url(wp_nonce_url(admin_url("admin-post.php?action=metaslider_create_slider&metaslider_add_sample_slides=carousel"), "metaslider_create_slider")); ?>"><?php esc_html_e('Carousel Slideshow', 'ml-slider'); ?></option>
							<option value="<?php echo esc_url(wp_nonce_url(admin_url("admin-post.php?action=metaslider_create_slider&metaslider_add_sample_slides=withcaption"), "metaslider_create_slider")); ?>"><?php esc_html_e('Carousel Slideshow with Captions', 'ml-slider'); ?></option>
						</select>
						<button id="sampleslider-btn" class="button button-primary"><?php esc_html_e('Create a Slideshow', 'ml-slider'); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<?php // TODO: I think after here maybe we can add images from their media library, or perhaps from an external image API
