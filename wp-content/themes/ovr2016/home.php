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
<div class="col-sm-12 col-md-10 col-md-offset-1 mainBackground">
  <?php if ( is_active_sidebar( 'top-feature' ) ) :?>
    <div class="row">
      <div class="col-sm-12">
        <?php dynamic_sidebar( 'top-feature' ); ?>
      </div>
    </div>
  <?php endif; ?>
  <?php if ( is_active_sidebar('first-row-left') || is_active_sidebar('first-row-right') ): ?>
    <div class="row">
      <?php if ( is_active_sidebar( 'first-row-left' ) ): ?>
        <div class="col-sm-6">
          <?php dynamic_sidebar('first-row-left' ); ?>
        </div>
      <?php endif; ?>
      <?php if ( is_active_sidebar( 'first-row-right' ) ): ?>
        <div class="col-sm-6">
          <?php dynamic_sidebar( 'first-row-right' ); ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <?php if ( is_active_sidebar('second-row-left') || is_active_sidebar('second-row-right') ): ?>
    <div class="row">
      <?php if ( is_active_sidebar( 'second-row-left' ) ): ?>
        <div class="col-sm-6">
          <?php dynamic_sidebar('second-row-left' ); ?>
        </div>
      <?php endif;?>
      <?php if ( is_active_sidebar( 'second-row-right' ) ): ?>
        <div class="col-sm-6">
          <?php dynamic_sidebar('second-row-right' ); ?>
        </div>
      <?php endif;?>
    </div>
  <?php endif; ?>
</div><!-- Grey Background-->
<?php get_footer(); ?>
