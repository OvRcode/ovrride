<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Quark
 * @since Quark 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content row clearfix" role="main">

		<div class="col grid_12_of_12">

			<article id="post-0" class="post error404 no-results not-found">
				<header class="entry-header">
					<h1 class="entry-title"><i class="fa fa-thumbs-down fa-lg"></i> <?php _e( '&nbsp; Four Oh Fizzle, Oh Dip! <br> Now that wasn&rsquo;t supposed to happen!', 'quark' ); ?></h1>
				</header>
				<div class="entry-content clearfix">
					<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Maybe searching can help.', 'quark' ); ?></p>
					<?php get_search_form(); ?>
				</div><!-- /.entry-content -->
			</article><!-- /#post -->

		</div> <!-- /.col.grid_12_of_12 -->

	</div> <!-- /#primary.site-content.row -->

<?php get_footer(); ?>
