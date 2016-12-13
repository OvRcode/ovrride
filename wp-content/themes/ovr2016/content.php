<?php
/**
 * @package _tk
 */
?>


<?php // Styling Tip!

// Want to wrap for example the post content in blog listings with a thin outline in Bootstrap style?
// Just add the class "panel" to the article tag here that starts below.
// Simply replace post_class() with post_class('panel') and check your site!
// Remember to do this for all content templates you want to have this,
// for example content-single.php for the post single view. ?>
<div class="row">
	<div class="col-sm-4">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header>
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
			<div class="ovr_post_excerpt">
					<?php the_excerpt(); ?>
					<a href="<?php the_permalink();"" ?>">Read More</a>
			</div>
		</div>

	</article><!-- #post-## -->
</div>
