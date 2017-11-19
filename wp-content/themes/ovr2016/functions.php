<?php
/**
 * _tk functions and definitions
 *
 * @package _tk
 */

 /**
  * Store the theme's directory path and uri in constants
  */
 define('THEME_DIR_PATH', get_template_directory());
 define('THEME_DIR_URI', get_template_directory_uri());

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 750; /* pixels */

if ( ! function_exists( '_tk_setup' ) ) :
/**
 * Set up theme defaults and register support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function _tk_setup() {
	global $cap, $content_width;

	// Add html5 behavior for some theme elements
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );

    // This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	/**
	 * Add default posts and comments RSS feed links to head
	*/
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Thumbnails on posts and pages
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	*/
	add_theme_support( 'post-thumbnails' );

	/**
	 * Enable support for Post Formats
	*/
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	/**
	 * Setup the WordPress core custom background feature.
	*/
	add_theme_support( 'custom-background', apply_filters( '_tk_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
		) ) );

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on _tk, use a find and replace
	 * to change '_tk' to the name of your theme in all the template files
	*/
	load_theme_textdomain( '_tk', THEME_DIR_PATH . '/languages' );

	/**
	 * This theme uses wp_nav_menu() in one location.
	*/
	register_nav_menus( array(
		'primary'  => __( 'Header bottom menu', '_tk' ),
		) );

}
endif; // _tk_setup
add_action( 'after_setup_theme', '_tk_setup' );

/**
 * Register widgetized area and update sidebar with default widgets
 */
function _tk_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', '_tk' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
		) );
}
add_action( 'widgets_init', '_tk_widgets_init' );

/**
 * Enqueue scripts and styles
 */
function _tk_scripts() {

	// Import the necessary TK Bootstrap WP CSS additions
	wp_enqueue_style( '_tk-bootstrap-wp', THEME_DIR_URI . '/includes/css/bootstrap-wp.css' );

	// load bootstrap css
	wp_enqueue_style( '_tk-bootstrap', THEME_DIR_URI . '/includes/resources/bootstrap/css/bootstrap.min.css' );

	// load Font Awesome css
	wp_enqueue_style( '_tk-font-awesome', THEME_DIR_URI . '/includes/css/font-awesome.min.css', false, '4.7.0' );

	// load _tk styles
	wp_enqueue_style( '_tk-style', get_stylesheet_uri() );

	// load bootstrap js
	wp_enqueue_script('_tk-bootstrapjs', THEME_DIR_URI . '/includes/resources/bootstrap/js/bootstrap.min.js', array('jquery') , false, true);

	// load bootstrap wp js
	wp_enqueue_script( '_tk-bootstrapwp', THEME_DIR_URI . '/includes/js/bootstrap-wp.js', array('jquery'), false, true );

	wp_enqueue_script( '_tk-skip-link-focus-fix', THEME_DIR_URI . '/includes/js/skip-link-focus-fix.js', array(), '20130115', true );

  wp_enqueue_script( 'ovr_footer_script', THEME_DIR_URI . '/includes/js/ovr-footer.min.js', array('jquery'), 1.1, true);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( '_tk-keyboard-image-navigation', THEME_DIR_URI . '/includes/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202', true );
	}
  //wp_enqueue_script( 'ovr_google_analytics', THEME_DIR_URI . '/includes/js/ga.js', false, false, true);
  //wp_localize_script( 'ovr_google_analytics', 'ovr', array( 'ga_id' => get_option("google_analytics_id") ) );
  wp_enqueue_script( 'ovr_pingdom_js', THEME_DIR_URI . '/includes/js/pingdom.js', false, false, false);
  wp_localize_script( 'ovr_pingdom_js', 'ovr', array('pingdom_id' => get_option("pingdom_id") ) );
  wp_enqueue_style( 'ovr_site_style', THEME_DIR_URI . '/includes/css/master.min.css', array('_tk-bootstrap', '_tk-bootstrap-wp','_tk-font-awesome' ), "1.5.1");
}
add_action( 'wp_enqueue_scripts', '_tk_scripts' );

/**
 * Implement the Custom Header feature.
 */
require THEME_DIR_PATH . '/includes/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require THEME_DIR_PATH . '/includes/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require THEME_DIR_PATH . '/includes/extras.php';

/**
 * Customizer additions.
 */
require THEME_DIR_PATH . '/includes/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require THEME_DIR_PATH . '/includes/jetpack.php';

/**
 * Load custom WordPress nav walker.
 */
require THEME_DIR_PATH . '/includes/bootstrap-wp-navwalker.php';

/**
 * Adds WooCommerce support
 */
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
	add_theme_support( 'woocommerce' );
}

/**
 * Add Theme widget areas
 */
 function register_widget_areas() {
	register_sidebar( array(
		'name'          => 'Top Banner Ad',
		'id'            => 'ovr-banner-ad',
		'description'   => 'Banner Ad at top of home page',
	) );
  register_sidebar( array(
		'name'          => 'Upcoming Events',
		'id'            => 'events',
		'description'   => 'Spot for upcoming events widget (on every page)',
	) );
  register_sidebar( array(
		'name'          => 'Feature Main',
		'id'            => 'feature-main',
		'description'   => 'featured slider on home page',
	) );
  register_sidebar( array(
    'name'          => 'Feature Top',
    'id'            =>  'feature-top',
    'description'   =>  'Top of column that sits next to slider'
  ) );
  register_sidebar( array(
    'name'          =>  'Feature Bottom',
    'id'            =>  'feature-bottom',
    'description'   =>  'Bottom of column that sits next to slider'
  ) );
  register_sidebar( array(
		'name'          => 'First Row, left side',
		'id'            => 'first-row-left',
		'description'   => 'Left Half of first row below feature',
	) );
  register_sidebar( array(
		'name'          => 'First Row, right side',
		'id'            => 'first-row-right',
		'description'   => 'Right Half of first row below feature',
	) );
  register_sidebar( array(
		'name'          => 'Second Row, left side',
		'id'            => 'second-row-left',
		'description'   => 'Left Half of second row',
	) );
  register_sidebar( array(
		'name'          => 'Second Row, right side',
		'id'            => 'second-row-right',
		'description'   => 'Right Half of second row',
	) );
  register_sidebar( array(
    'name'          => 'Third Row, left side',
    'id'            => 'third-row-left',
    'description'   => 'Left Half of third row',
  ) );
  register_sidebar( array(
    'name'          => 'Third Row, right side',
    'id'            => 'third-row-right',
    'description'   => 'Right Half of third row',
  ) );
  register_sidebar( array(
    'name'          => 'Fourth Row, left side',
    'id'            => 'fourth-row-left',
    'description'   => 'Left Half of fourth row',
  ) );
  register_sidebar( array(
    'name'          => 'Fourth Row, right side',
    'id'            => 'fourth-row-right',
    'description'   => 'Right Half of fourth row',
  ) );
  register_sidebar( array(
    'name'          => 'Footer: Newsletter',
    'id'            => 'footer-newsletter',
    'description'   => 'Contents of newsletter tile'
  ) );

}
add_action( 'widgets_init', 'register_widget_areas' );
;
/* DISABLE RSS FEEDS */
function itsme_disable_feed() {
wp_die( __( 'No feed available, please visit the <a href="'. esc_url( home_url( '/' ) ) .'">homepage</a>!' ) );
}

add_action('do_feed', 'itsme_disable_feed', 1);
add_action('do_feed_rdf', 'itsme_disable_feed', 1);
add_action('do_feed_rss', 'itsme_disable_feed', 1);
add_action('do_feed_rss2', 'itsme_disable_feed', 1);
add_action('do_feed_atom', 'itsme_disable_feed', 1);
add_action('do_feed_rss2_comments', 'itsme_disable_feed', 1);
add_action('do_feed_atom_comments', 'itsme_disable_feed', 1);

remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );

/* Add theme settings to admin page */
function theme_settings_page(){}

function add_theme_menu_item() {
	add_menu_page("OvR Settings", "OvR Settings", "manage_options", "ovr-settings", "ovr_settings_page", null, 56);
}

function ovr_settings_page() {

  ob_start();
    settings_fields("section");
    do_settings_sections("theme-options");
    submit_button();
    $settings_form = ob_get_contents();
  ob_end_clean();

  echo <<<SETTINGSPAGE
	    <div class="wrap">
	    <h1>OvR Theme Settings</h1>
	    <form method="post" action="options.php">
	         {$settings_form}
	    </form>
		</div>
SETTINGSPAGE;
}

function display_twitter_element() {
  $twitter_url = get_option('twitter_url');
  echo "<input type='text' name='twitter_url' id='twitter_url' value='{$twitter_url}' />";
}

function display_facebook_element() {
	$facebook_url = get_option('facebook_url');
  echo "<input type='text' name='facebook_url' id='facebook_url' value='{$facebook_url}' />";
}

function display_instagram_element() {
	$instagram_url = get_option('instagram_url');
  echo "<input type='text' name='instagram_url' id='instagram_url' value='{$instagram_url}' />";
}

function display_youtube_element() {
  $youtube_url = get_option('youtube_url');
  echo "<input type='text' name='youtube_url' id='youtube_url' value='{$youtube_url}' />";
}

function display_google_maps_api_element() {
  $google_maps_api = get_option('google_maps_api');
  echo "<input type='text' name='google_maps_api' id='google_maps_api' value='{$google_maps_api}' />";
}
function display_about_element() {
  $about_ovr = get_option('about_ovr');
  echo "<textarea rows='10' cols='100' name='about_ovr' id='about_ovr'>{$about_ovr}</textarea>";
}
function display_google_analytics_element() {
  $analytics_id = get_option("google_analytics_id");
  echo "<input type='text' name='google_analytics_id' id='google_analytics_id' value='{$analytics_id}' />";
}
function display_pingdom_element() {
  $pingdom_id = get_option("pingdom_id");
  echo "<input type='text' name='pingdom_id' id='pingdom_id' value='{$pingdom_id}' />";
}
function display_theme_panel_fields()
{
	add_settings_section("section", "Footer Settings", null, "theme-options");

	add_settings_field("twitter_url", "Twitter Profile Url", "display_twitter_element", "theme-options", "section");
  add_settings_field("facebook_url", "Facebook Profile Url", "display_facebook_element", "theme-options", "section");
  add_settings_field("youtube_url", "Youtube Chanel Url", "display_youtube_element", "theme-options", "section");
  add_settings_field("instagram_url", "Instagram Profile Url", "display_instagram_element", "theme-options", "section");
  add_settings_field("google_maps_api", "Google Maps API Key", "display_google_maps_api_element", "theme-options", "section");
  add_settings_field("google_analytics_id", "Google Analytics ID", "display_google_analytics_element", "theme-options", "section");
  add_settings_field("pingdom_id", "Pingdom Monitoring ID", "display_pingdom_element", "theme-options", "section");
  add_settings_field("about_text", "About OvR Text", "display_about_element", "theme-options", "section");
  register_setting("section", "twitter_url");
  register_setting("section", "facebook_url");
  register_setting("section", "youtube_url");
  register_setting("section", "instagram_url");
  register_setting("section", "google_maps_api");
  register_setting("section", "google_analytics_id");
  register_setting("section", "pingdom_id");
  register_setting("section", "about_ovr");
}

add_action("admin_init", "display_theme_panel_fields");
add_action("admin_menu", "add_theme_menu_item");

/* Sort Shop by trip start date */
//_wc_trip_start_date
function ovr_add_postmeta_ordering_args( $sort_args ) {

	$orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
	switch( $orderby_value ) {

		// Name your sortby key whatever you'd like; must correspond to the $sortby in the next function
		case 'date':
			$sort_args['orderby']  = 'meta_value_num';
			// Sort by meta_value because we're using alphabetic sorting
			$sort_args['order']    = 'asc';
      //$sort_args['meta_type'] = 'DATETIME';
			$sort_args['meta_key'] = '_wc_trip_sort_date';
			// use the meta key you've set for your custom field, i.e., something like "location" or "_wholesale_price"
			break;
	}

	return $sort_args;
}

// Add trip starting date to frontend
function ovr_add_new_postmeta_orderby( $sortby ) {
  // Ratings are disabled anyway, remove the sort option
  unset($sortby['rating']);

	$sortby['date'] = __( 'Sort by trip date', 'woocommerce' );

	return $sortby;
}
add_filter( 'woocommerce_get_catalog_ordering_args', 'ovr_add_postmeta_ordering_args' );
add_filter( 'woocommerce_default_catalog_orderby_options', 'ovr_add_new_postmeta_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'ovr_add_new_postmeta_orderby' );

// 20 Products per page
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 20;' ), 20 );
// Remove sorting drop dropdown-menu
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

// Make sure products other than trips have date field to show during sort
add_action( 'after_setup_theme', 'activate_theme_cron' );

function activate_theme_cron() {
    if (! wp_next_scheduled ( 'sort_date_check_hourly' )) {
	     wp_schedule_event(time(), 'hourly', 'sort_date_check_hourly');
    }
}

add_action('sort_date_check_hourly', 'sort_date_check');

function sort_date_check() {
	global $wpdb;
  // Find all published products, insert dummy _wc_trip_sort_date
  // Will not overwrite existing sort dates
  $wpdb->query("INSERT IGNORE INTO wp_postmeta (`post_id`,`meta_key`,`meta_value`)
  SELECT `ID` AS `post_id`, '_wc_trip_sort_date' AS `meta_key`, '30000000' AS `meta_value`
  FROM `wp_posts` WHERE `post_type` = 'product' AND `post_status` = 'publish'");
}
//Reposition WooCommerce breadcrumb
function woocommerce_remove_breadcrumb(){
remove_action(
    'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
}
add_action(
    'woocommerce_before_main_content', 'woocommerce_remove_breadcrumb'
);

/* Bootstrap accordion shortcodes */
function AccordionOpen_func( $atts ) {
  $id = ( isset($atts['id']) && "" !== $atts['id'] ? $atts['id'] : "accordionID");

  return "<div class='panel-group' id='{$id}'>";
}

function AccordionQuestion_func( $atts ) {
  $id = ( isset($atts['id']) && "" !== $atts['id'] ? $atts['id'] : "accordionID");
  $question = ( isset($atts['question']) && "" !== $atts['question'] ? $atts['question'] : 0);
  $title = ( isset($atts['title']) && "" !== $atts['title'] ? $atts['title'] : 'Nada');

  return <<<QOPEN
    <div class="panel panel-default ">
      <div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse" data-parent="#{$id}" data-target="#question{$question}">
        <h4 class="panel-title">
          <a class="ing">Q: {$title}</a>
        </h4>
      </div>
QOPEN;
}
function AccordionAnswer_func( $atts ) {
  $question = ( isset($atts['question']) && "" !== $atts['question'] ? $atts['question'] : 0);
  return <<<AOPEN
    <div id="question{$question}" class="panel-collapse collapse" style="height: 0px;">
      <div class="panel-body">
        <h5><span class="label label-primary">Answer</span></h5>
AOPEN;
}
function AccordionAnswerClose_func() {
  return "</div></div></div>";
}
function AccordionClose_func() {
  return "</div>";
}
add_shortcode( 'AccordionOpen', 'AccordionOpen_func' );
add_shortcode( 'AccordionQuestion', 'AccordionQuestion_func');
add_shortcode( 'AccordionAnswer', 'AccordionAnswer_func');
add_shortcode( 'AccordionAnswerClose', 'AccordionAnswerClose_func');
add_shortcode( 'AccordionClose', 'AccordionClose_func');

add_filter( 'wp_mail_from', function() {
    return 'wordpress@ovrride.com';
} );

// Remove order again button
remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );

// check for empty-cart get param to clear the cart
add_action( 'init', 'woocommerce_clear_cart_url' );
function woocommerce_clear_cart_url() {
  global $woocommerce;

	if ( isset( $_GET['empty-cart'] ) ) {
		$woocommerce->cart->empty_cart();
	}
}


// Remove company field from checkout
add_filter( 'woocommerce_checkout_fields' , 'ovr_checkout_fields' );
function ovr_checkout_fields( $fields ) {
	unset($fields['billing']['billing_company']);
	unset($fields['shipping']['shipping_company']);

	return $fields;
}

add_filter('wp_nav_menu_items', 'ovr_menu_mods', 10, 2);

function ovr_menu_mods($items, $args) {
  if ( strpos($args->container_class, "collapse") === FALSE ) {
    return $items;
  }
  ob_start();
  wp_loginout('index.php');
  $loginoutlink = ob_get_contents();
  ob_end_clean();
  $items .= '<li class="hidden-sm hidden-xs"><a>|</a></li><li><a href="/cart" title="Cart"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a></li>';
  $items .= '<li>'. $loginoutlink .'</li>';
  return $items;
}

function ovr_login_style() {
    wp_enqueue_style( 'custom-login', get_template_directory_uri() . '/includes/css/login.min.css' );
}
add_action( 'login_enqueue_scripts', 'ovr_login_style' );

function ovr_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'ovr_logo_url' );


function register_ovr_menus() {
  register_nav_menus(
    array(
      'main-no-collapse' => __( 'Main Menu No Collapse' ),
      'main-collapse' => __( 'Main Menu Collapse' )
    )
  );
}
add_action( 'init', 'register_ovr_menus' );

add_filter( 'woocommerce_product_add_to_cart_text' , 'custom_woocommerce_product_add_to_cart_text' );

/**
 * custom_woocommerce_template_loop_add_to_cart
*/
function custom_woocommerce_product_add_to_cart_text() {
	global $product;

	$product_type = $product->product_type;

	switch ( $product_type ) {
		case 'trip':
      if ( $product->get_stock_quantity() > 0 && $product->get_stock_quantity() < 30 ) {
        return "Only " . $product->get_stock_quantity() . " left";
      } else if ( ! $product->is_in_stock() ) {
        return "Sold Out";
      } else {
        return "Book now";
      }
			return __( 'Book Now', 'woocommerce' );
		break;
		case 'simple':
			return __( 'Add to cart', 'woocommerce' );
		break;
		default:
			return __( 'Read more', 'woocommerce' );
	}

}

/* Parallelize host names */
function parallelize_hostnames($url, $id) {
  $base_url = explode(":",site_url());
  $base_url[1] = substr($base_url[1],2);
  $subdomains = array('static1.'.$base_url[1],'static2.'.$base_url[1]);
  $hostname = $subdomains[mt_rand(0, count($subdomains) - 1)];
  $url = str_replace(parse_url(get_bloginfo('url'), PHP_URL_HOST), $hostname, $url);
  return $url;
}
add_filter('wp_get_attachment_url', 'parallelize_hostnames', 10, 2);
