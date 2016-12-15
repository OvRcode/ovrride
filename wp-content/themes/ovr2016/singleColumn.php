<?php
/**
 * Template Name: Single Column
 *
 * @package ovr2016
 */

get_header();
?>
<div class="container-fluid">
  <div class="col-sm-12 col-md-10 col-md-offset-1 mainBackground">
    <div class="row">
      <div class="col-sm-12">
        <h1><?php the_title(); ?></h1>
      </div>
    </div>
    <?php echo apply_filters('the_content', do_shortcode( shortcode_unautop($post->post_content) ) ); ?>
  </div>
</div>
<?php get_footer(); ?>
