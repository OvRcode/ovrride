<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="maincontentcontainer">
 *
 * @package Quark
 * @since Quark 1.0
 */
?><!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->


<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<meta http-equiv="cleartype" content="on">

	<!-- Responsive and mobile friendly stuff -->
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
    <meta name="viewport" content="device-width; minimum-scale=1; initial-scale=1; maximum-scale=1; user-scalable=no;"/>

	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

  <!-- Facebook Open Graph Properties -->
  <?php if (stristr($_SERVER["HTTP_USER_AGENT"],'facebook') !== false) { ?>
    <meta property="og:title" content="<?php wp_title(); ?>" />
    <meta property="og:description" content="<?php bloginfo('description'); ?>" />
    <meta property="og:url" content="<?php bloginfo('url'); ?>" />
    <meta property="og:type" content="website" />
    <meta property="fb:admins" content="123456789" />
    <meta property="og:image" content="<?php bloginfo('url'); ?>/wp-content/uploads/2013/05/ovr-logo.jpg" />
  <?php } ?>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div class="visuallyhidden"><a href="#primary" title="<?php esc_attr_e( 'Skip to main content', 'quark' ); ?>"><?php _e( 'Skip to main content', 'quark' ); ?></a></div>

<div id="wrapper" class="hfeed site">

	<div id="headercontainer">

		<header id="masthead" class="site-header row clearfix" role="banner">
			<div class="col grid_3_of_12 site-title">
				<h1>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" rel="home">
						<?php 
						$headerImg = get_header_image();
						if( !empty( $headerImg ) ) { ?>
							<img src="<?php header_image(); ?>" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="" />
						<?php } 
						else {
							echo get_bloginfo( 'name' );
						} ?>
					</a>
				</h1>
			</div> <!-- /.col.grid_5_of_12 -->

      <div class="col grid_9_of_12">
        <div class="social-media-icons">
          <ul style="margin-top:5px;" id="topnav">
            <li class="ovr_soc_icon">
              <a href="/contact-us/" title="Contact Us"><i class="icon-envelope icon-large"></i></a>
            </li>
            <li class="ovr_soc_icon">
              <a href="https://www.facebook.com/ovrride" title="Like Us on Facebook" target="_blank"><i class="icon-facebook-sign icon-large"></i></a>
            </li>
            <li class="ovr_soc_icon">
              <a href="http://twitter.com/ovrride" title="Follow Us on Twitter" target="_blank"><i class="icon-twitter-sign icon-large"></i></a>
            </li>
            <li class="ovr_soc_icon">
              <a href="http://instagram.com/ovrride" title="Follow Us on Instagram" target="_blank"><i class="icon-instagram icon-large"></i></a>
            </li>
            <li class="ovr_soc_icon_end">
              <a href="http://www.youtube.com/user/ovrrideTV" title="Watch Us on YouTube" target="_blank"><i class="icon-youtube-sign icon-large"></i></a>
            </li>
            <li>
              <a href="/cart">CART</a>
            </li>
            <li class="ovr_login_split">|</li>
            <li>
				<?php wp_loginout(); ?>
            </li>
          </ul>
          <div class="top-search">
            <?php echo do_shortcode('[widget id="search-3"]'); ?>
          </div>
        </div>

				<nav id="site-navigation" class="main-navigation" role="navigation">
					<h3 class="menu-toggle assistive-text"><?php _e( 'Menu', 'quark' ); ?></h3>
					<div class="assistive-text skip-link"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'quark' ); ?>"><?php _e( 'Skip to content', 'quark' ); ?></a></div>
					<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
				</nav> <!-- /.site-navigation.main-navigation -->
			</div> <!-- /.col.grid_7_of_12 -->
		</header> <!-- /#masthead.site-header.row -->

	</div> <!-- /#headercontainer -->
	
	<div id="maincontentcontainer">
