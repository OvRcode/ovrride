<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package _tk
 */
global $post;

$title = wp_title( '|', false, 'right' );
if ( has_post_thumbnail( $post->ID ) || ! is_woocommerce()) {
	$ogImage = wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'thumbnail' );
} else {
	$ogImage = get_site_url( null, 'wp-content/themes/ovr2016/includes/images/ovr_og.png');
}
if (  is_front_page() || is_archive() || is_home() ) {
	$ogDescription = esc_attr(wp_strip_all_tags(get_option("about_ovr", "OvRride"), true));
	} else {
	$ogDescription = get_post($post, 'OBJECT', 'display');
	if ( is_woocommerce() ) {
		$ogDescription = htmlentities(wp_strip_all_tags( do_shortcode($ogDescription->post_content ) ) );
	} else if ( isset( $ogDescription->post_excerpt ) && "" !== $ogDescription->post_excerpt) {
		$ogDescription = htmlentities( wp_strip_all_tags( do_shortcode( $ogDescription->post_excerpt ) ) );
	} else {
		$ogDescription = $title;
	}
	$ogDescription = substr($ogDescription, 0, 300);
}
if (!isset($description)) {
	$description = "";
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
	<meta property="og:image:height" content="406">
	<meta property="og:image:width" content="406">
	<meta property="og:image:type" content="image/png">
	<meta property="og:description" content="<?php echo $ogDescription; ?>">
	<meta name="description" content="<?php echo $description; ?>">
	<title><?php wp_title( '|', true, 'right' ); ?></title>

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-MXZNP92');</script>
	<!-- End Google Tag Manager -->
	
	<!-- Event snippet for Booked conversion page -->
	<!-- Global site tag (gtag.js) - Google AdWords: 828851634 -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=AW-828851634"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'AW-828851634');
	  gtag('event', 'conversion', {'send_to': 'AW-828851634/W6_DCIf6oHoQsoudiwM'});
	</script>
	
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MXZNP92"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	
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
