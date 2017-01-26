<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package _tk
 */

$title = wp_title( '|', false, 'right' );
$ogImage = get_site_url( null, 'wp-content/themes/ovr2016/includes/images/ovr_og.png');
if (  is_front_page() || is_archive() || is_home() ) {
	$ogDescription = "Lead by New York’s most experienced Snowboard industry veterans, the OvRride team helps riders and skiers escape from the concrete canyons of NYC to the pristine peaks of America’s North East and beyond. Our goal in mind; to OvRride the Everyday!";
} else {
	global $post;
	$ogDescription = wp_strip_all_tags(do_shortcode($post->post_content));
	if ( is_woocommerce() ) {
		$ogDescription = esc_attr(substr(preg_replace("/(.* Description:\W{0,4})/", "", $ogDescription), 0, 300));
	} else {
		$ogDescription = esc_attr(substr(get_the_excerpt($post), 0, 300));
	}
}

if ( "" == $ogDescription ) {
	$ogDescription = $title;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta property="og:title" content="<?php echo $title; ?>">
	<meta property="og:type" content="website">
	<meta property="og:url" content="<?php echo get_permalink(); ?>">
	<meta property="og:image" content="<?php echo $ogImage; ?>">
	<meta property="og:image:height" content="214">
	<meta property="og:image:width" content="406">
	<meta property="og:image:type" content="image/png">
	<meta property="og:description" content="<?php echo $ogDescription; ?>">

	<title><?php wp_title( '|', true, 'right' ); ?></title>

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php do_action( 'before' ); ?>

<nav class="site-navigation">
<?php // substitute the class "container-fluid" below if you want a wider content area ?>
	<div class="">
		<div class="row">
			<div class="site-navigation-inner col-sm-12">
				<div class="navbar navbar-inverse">
					<!-- The WordPress Menu goes here -->
					<?php wp_nav_menu(
						array(
							'theme_location' 	=> 'main-no-collapse',
							'depth'             => 0,
							'container'         => 'div',
							'container_id'      => 'navbar-header',
							'container_class'   => 'navbar-header',
							'menu_class' 		=> 'nav navbar-nav',
							'fallback_cb' 		=> 'wp_bootstrap_navwalker::fallback',
							'menu_id'			=> 'main-no-collapse',
							'walker' 			=> new wp_bootstrap_navwalker()
						)
					); ?>
					<?php wp_nav_menu(
						array(
							'theme_location' 	=> 'main-collapse',
							'depth'             => 0,
							'container'         => 'div',
							'container_id'      => 'navbar-collapse',
							'container_class'   => 'collapse navbar-collapse',
							'menu_class' 		=> 'nav navbar-nav',
							'fallback_cb' 		=> 'wp_bootstrap_navwalker::fallback',
							'menu_id'			=> 'main-collapse',
							'walker' 			=> new wp_bootstrap_navwalker()
						)
					); ?>
					<div id="navbar-collapse-button" class="navbar-header">
						<!-- .navbar-toggle is used as the toggle for collapsed navbar content -->
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
							<span class="sr-only"><?php _e('Toggle navigation','_tk') ?> </span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>
				</div><!-- .navbar -->
			</div>
		</div>
	</div><!-- .container -->
</nav><!-- .site-navigation -->

<div class="main-content">
<?php // substitute the class "container-fluid" below if you want a wider content area ?>
	<div class="container-fluid">
		<div class="row">
			<div id="content" class="main-content-inner col-sm-12">
				<?php if ( is_active_sidebar( 'events' ) ) : ?>
				  <div class="row">
				    <div class="col-sm-12 hidden-xs">
				      <?php dynamic_sidebar( 'events' ); ?>
				    </div>
				  </div>
				<?php endif; ?>
