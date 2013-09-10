<?php
/**
 * Template Name: No-Title + Sidebar + Cal + Event
 *
 * Description: Displays a full-width page, with no sidebar. This template is great for pages
 * containing large amounts of content.
 *
 * @package Quark
 * @since Quark 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content row clearfix" role="main">
    	
         <div class="col grid_3_of_12">
            <div class="col grid_12_of_12" style="margin: 0px 0px 45px 0px;">
            	<?php 
					$id = 1384;
					$p = get_page($id);
					echo apply_filters('the_content', $p->post_content);
				?>
                <h1 style="margin-top:5px;" class="book-btn"><a href="http://ovrride.com/events/">VIEW FULL CALENDAR</a></h1>
            </div>
            <div class="col grid_12_of_12" style="margin:0px;">
            	<?php 
					$id = 1387;
					$p = get_page($id);
					echo apply_filters('the_content', $p->post_content);
				?>
            </div>    
        </div>
        
		<div class="col grid_9_of_12">

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', 'busstop' ); ?>
					<?php comments_template( '', false ); ?>
				<?php endwhile; // end of the loop. ?>

			<?php endif; // end have_posts() check ?>

		</div> <!-- /.col.grid_9_of_12 -->
	</div><!-- /#primary.site-content.row -->

<?php get_footer(); ?>
