<?php
/**
 * Template Name: Blog
 *
 * @package ovr2016
 */

get_header();
?>
<div class="col-sm-12 col-md-10 col-md-offset-1 mainBackground">
  <div class="col-sm-10 col-sm-push-2 col-md-10">
    <article>

		<?php // Display blog posts on any page @ http://m0n.co/l
		$temp = $wp_query; $wp_query= null;
		$wp_query = new WP_Query(); $wp_query->query('showposts=10' . '&paged='.$paged);
		while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
      <div class="row ovr_post">
        <div class="col-sm-4">
          <div class="ovr_post_thumb">
            <a href="<?php the_permalink(); ?>" title="Read more">
              <?php echo get_the_post_thumbnail(get_the_ID(), array( 250,250)); ?>
            </a>
          </div>
        </div>
        <div class="col-sm-8 noleftpad">
          <div class="ovr_post_title">
  		      <h1><a href="<?php the_permalink(); ?>" title="Read more"><?php the_title(); ?></a></h1>
          </div>
          <div class="ovr_post_date">
            <?php echo get_the_date(); ?>
          </div>
        </div>
        <div class="ovr_post_excerpt">
		        <?php the_excerpt(); ?>
            <a href="<?php the_permalink();"" ?>">Read More</a>
        </div>
      </div>
		<?php endwhile; ?>
    <div class="row">
		<?php if ($paged > 1) { ?>
		  <nav id="nav-posts">
        <div class="col-xs-6">
			   <div class="prev"><?php next_posts_link('&laquo; Previous Posts'); ?></div>
       </div>
       <div class="col-xs-6">
			   <div class="next"><?php previous_posts_link('Newer Posts &raquo;'); ?></div>
       </div>
		  </nav>
      </div>
		  <?php } else { ?>
      <div class="col-xs-12">
		  <nav id="nav-posts">
			   <div class="prev"><?php next_posts_link('&laquo; Previous Posts'); ?></div>
		  </nav>
      </div>
		  <?php } ?>
    </article>
    </div>
		<?php wp_reset_postdata(); ?>


  <div class="col-sm-2 col-sm-pull-10 col-md-2">
    <?php get_sidebar(); ?>
  </div>
</div>
<?php
get_footer();
?>
