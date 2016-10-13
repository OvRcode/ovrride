<?php
/**
 * Template Name: Home Page
 *
 * @package ovr2016
 */

get_header();
?>

<?php if ( is_active_sidebar( 'banner-ad' ) ) : ?>
  <div class="row">
    <div class="col-sm-12">
      <?php dynamic_sidebar( 'banner-ad' ); ?>
    </div>
  </div>
<?php endif; ?>
<div class="col-lg-10 col-lg-offset-1 mainBackground">
  <div class="row">
    <h4>Box?</h4>
  </div>
</div><!-- Grey Background-->
<?php get_footer(); ?>
