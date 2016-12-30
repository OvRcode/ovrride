<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
wp_enqueue_script('ovr_shop_js', THEME_DIR_URI . '/includes/js/ovr_shop.min.js', array('jquery'), "1.0", true);

get_header( 'alt' ); ?>

<div class="container-fluid">


	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
		?>
	<div class="col-sm-12 col-md-10 col-md-offset-1 mainBackground">
		<div class="row">
			<div class="col-sm-12 shop_buttons">
				<a class="button" href="/product-category/gear/">Gear</a>
				<a class="button" href="/product-tag/gift-certificate-2/">Gift Certificates</a>
				<a class="button" href="/product-category/high-five-2/">High Fives</a>
				<a class="button" href="/shop/">Book A Trip</a>
			</div>
		</div>
		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) &&
		"Shop" != woocommerce_page_title(FALSE)) : ?>
				<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>

		<?php endif; ?>

		<?php do_action( 'woocommerce_archive_description' ); ?>

		<?php if ( have_posts() ) : ?>

			<?php
				/**
				 * woocommerce_before_shop_loop hook
				 *
				 * @hooked woocommerce_result_count - 20
				 * @hooked woocommerce_catalog_ordering - 30
				 */
				do_action( 'woocommerce_before_shop_loop' );
				?>

				<?php woocommerce_product_loop_start(); ?>

				<?php woocommerce_product_subcategories(); ?>

				<?php $i=0; ?>
				<div class="row">
					<?php while ( have_posts() ) : the_post(); ?>
						<div class="col-xs-5 col-md-3">
							<?php wc_get_template_part( 'content', 'product' ); ?>
						</div>
						<?php
						$i++;
						if ($i%4==0) {
							echo '</div><div class="row">';
						}

						?>
					<?php endwhile; // end of the loop. ?>
				</div>
				<?php woocommerce_product_loop_end(); ?>

				<?php
				/**
				 * woocommerce_after_shop_loop hook
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
				?>

			<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

				<?php wc_get_template( 'loop/no-products-found.php' ); ?>

			<?php endif; ?>

			<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
		?>
	</div>
</div>

	<?php get_footer( 'alt' ); ?>
