<?php

	$cat = $settings->category;
	if(get_term_by( 'id', $cat, 'product_cat' ) ){
		$term = get_term_by( 'id', $cat, 'product_cat' );
    	$name = $term->name;
    	$desc = $term->description;
    	$image = wp_get_attachment_url($cat);
    	$cat_link = get_term_link($term,'product_cat');
	}
		$heading_style = $settings->heading_type;
		$subheading_style = $settings->subheading_type;
		$heading_pos = $settings->heading_position;
		$subheading_pos = $settings->subheading_position;

		if ($settings->image) {
			$image = $settings->image_src;
		}

?>
<div class="featured-category-cta featured-category-cta-<?php echo $id; ?>">
	<a href="<?php echo $cat_link; ?>" class="cta-block <?php echo $settings->test_hover; ?> cta-block-<?php echo $name; ?>">
		<div class="cta-inner">
			<?php if ($heading_pos == 'above'): ?>
				<div class="feat-cat-title title-above">
					<?php 
						echo '<'.$heading_style.' class="cat-heading">';
							echo ($settings->heading) ? $settings->heading : $name;
						echo '</'.$heading_style.'>';

						if ($subheading_pos == 'under') {
							echo '<'.$subheading_style.' class="cat-subheading">';
								echo ($settings->subheading) ? $settings->subheading : $desc;
							echo '</'.$subheading_style.'>';
						}
					?>
				</div>
			<?php endif; ?>
			<div class="feat-cat-overlay">
				<img src="<?php echo $image; ?>" alt="<?php echo $name; ?>">
				<div class="feat-cat-hover">
					<?php if ($subheading_pos == 'hover'): ?>
						<div class="feat-cat-subtitle">
							<?php
								echo '<'.$subheading_style.' class="cat-subheading">';
									echo ($settings->subheading) ? $settings->subheading : $desc;
								echo '</'.$subheading_style.'>';
							?>
						</div>
					<?php endif; ?>
				</div>
				<?php if ($heading_pos != 'above' && $heading_pos != 'below'): ?>
					<div class="feat-cat-title title-overlay overlay-<?php echo $heading_pos; ?>">
						<?php 
							echo '<'.$heading_style.' class="cat-heading">';
								echo ($settings->heading) ? $settings->heading : $name;
							echo '</'.$heading_style.'>';

							if ($subheading_pos == 'under') {
								echo '<'.$subheading_style.' class="cat-subheading">';
									echo ($settings->subheading) ? $settings->subheading : $desc;
								echo '</'.$subheading_style.'>';
							}
						?>
					</div>
				<?php endif; ?>
			</div>
			<?php if ($heading_pos == 'below'): ?>
				<div class="feat-cat-title title-below">
					<?php 
						echo '<'.$heading_style.' class="cat-heading">';
							echo ($settings->heading) ? $settings->heading : $name;
						echo '</'.$heading_style.'>';

						if ($subheading_pos == 'under') {
							echo '<'.$subheading_style.' class="cat-subheading">';
								echo ($settings->subheading) ? $settings->subheading : $desc;
							echo '</'.$subheading_style.'>';
						}
					?>
				</div>
			<?php endif; ?>
			<?php if ($subheading_pos == 'below'): ?>
				<div class="feat-cat-subtitle">
					<?php
						echo '<'.$subheading_style.' class="cat-subheading">';
							echo ($settings->subheading) ? $settings->subheading : $desc;
						echo '</'.$subheading_style.'>';
					?>
				</div>
			<?php endif; ?>
		</div>
	</a>
</div>