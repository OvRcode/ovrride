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
        <div class="col-sm-6 leftCol">
          <?php dynamic_sidebar('first-row-left' ); ?>
        </div>
        <div class="col-sm-6 rightCol">
          <?php dynamic_sidebar( 'first-row-right' ); ?>
        </div>
    </div>
  <?php endif; ?>
  <?php if ( is_active_sidebar('second-row-left') || is_active_sidebar('second-row-right') ): ?>
    <div class="row">
        <div class="col-sm-6 leftCol">
          <?php dynamic_sidebar('second-row-left' ); ?>
        </div>
        <div class="col-sm-6 rightCol">
          <?php dynamic_sidebar('second-row-right' ); ?>
        </div>
    </div>
  <?php endif; ?>
  <?php if ( is_active_sidebar('third-row-left') || is_active_sidebar('third-row-right') ): ?>
    <div class="row">
        <div class="col-sm-6 leftCol">
          <?php dynamic_sidebar('third-row-left' ); ?>
        </div>
        <div class="col-sm-6 rightCol">
          <?php dynamic_sidebar('third-row-right' ); ?>
        </div>
    </div>
  <?php endif; ?>
  <?php if ( is_active_sidebar('fourth-row-left') || is_active_sidebar('fourth-row-right') ): ?>
    <div class="row">
        <div class="col-sm-6 leftCol">
          <?php dynamic_sidebar('fourth-row-left' ); ?>
        </div>
        <div class="col-sm-6 rightCol">
          <?php dynamic_sidebar('fourth-row-right' ); ?>
        </div>
    </div>
  <?php endif; ?>
</div><!-- Grey Background-->
<?php get_footer(); ?>
