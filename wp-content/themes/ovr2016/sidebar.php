<?php
/**
 * The sidebar containing the main widget area
 *
 * @package _tk
 */
?>


	<?php do_action( 'before_sidebar' ); ?>
	<?php //if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>
	<aside id="search" class="widget widget_search sidebar_search">
		<?php get_search_form(); ?>
	</aside>

	<aside id="archives" class="widget widget_archive">
		<h3 class="widget-title"><?php _e( 'Archives', '_tk' ); ?></h3>
			<ul>
				<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
			</ul>
	</aside>
