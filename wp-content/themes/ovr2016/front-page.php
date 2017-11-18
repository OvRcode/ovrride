<?php
/**
 * Template Name: Front Page
 *
 * @package ovr2016
 */

get_header();
?>

<?php if ( is_active_sidebar( 'ovr-banner-ad' ) ) : ?>
  <div class="row">
    <div class="col-sm-12 col-md-10 col-md-offset-1 ovr-banner-ad">
      <?php dynamic_sidebar( 'ovr-banner-ad' ); ?>
    </div>
  </div>
<?php endif; ?>
<div class="col-sm-12 col-md-10 col-md-offset-1 mainBackground">
  <div class="row">
  <?php if ( is_active_sidebar( 'feature-main' ) ) :?>
      <div class="col-sm-12 col-md-8 feature-main">
        <?php dynamic_sidebar( 'feature-main' ); ?>
      </div>
  <?php endif; ?>
  <?php if ( is_active_sidebar( 'feature-top') ) :?>
    <div class="col-sm-12 col-md-4 feature-right">
      <div class="row">
        <?php dynamic_sidebar( 'feature-top' ); ?>
      </div>
      <div class="row">
        <?php if ( is_active_sidebar('feature-bottom') ) :?>
          <?php dynamic_sidebar( 'feature-bottom' ); ?>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
  </div>
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
