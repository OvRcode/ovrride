<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package _tk
 */

get_header(); ?>

	<?php if ( have_posts() ) : ?>
		<div class="container-fluid">
			<div class="col-md-10 col-md-offset-1 col-sm-12 mainBackground">
				<div class="col-sm-9 pull-right">
		<header>
			<h2 class="page-title"><?php printf( __( 'Search Results for: %s', '_tk' ), '<span>' . get_search_query() . '</span>' ); ?></h2>
		</header><!-- .page-header -->

		<?php // start the loop. ?>
		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'search' ); ?>

		<?php endwhile; ?>

		<?php _tk_pagination(); ?>

	<?php else : ?>

		<?php get_template_part( 'no-results', 'search' ); ?>

	<?php endif; // end of loop. ?>
	</div>
	<div class="col-sm-3 push-left">
<?php get_sidebar(); ?>
</div>
</div>
</div>
<?php get_footer(); ?>
