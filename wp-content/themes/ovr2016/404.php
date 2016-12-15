<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package _tk
 */

get_header(); ?>

	<?php // add the class "panel" below here to wrap the content-padder in Bootstrap style ;) ?>
		<section class="content-padder error-404 not-found">
			<div class="col-xs-5 col-xs-offset-3 panel">
				<h1>Four Oh Fizzle, Oh Dip! <br>Now that wasn’t supposed to happen!</h1>
				<img src="<?php echo get_template_directory_uri()."/includes/images/bail.png";?>">
				<header>
					<h2 class="page-title"><?php _e( 'Looks like we\'re going to have to bail on this one!', '_tk' ); ?></h2>
				</header><!-- .page-header -->

				<div class="page-content">

					<p><?php _e( 'Nothing could be found at this location. Maybe try a search?', '_tk' ); ?></p>

					<?php get_search_form(); ?>

				</div><!-- .page-content -->
			</div>
			</section><!-- .content-padder -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
