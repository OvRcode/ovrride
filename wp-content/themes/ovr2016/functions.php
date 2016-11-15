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

  wp_enqueue_script( 'ovr_footer_script', THEME_DIR_URI . '/includes/js/ovr-footer.js', array('jquery'), false, true);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( '_tk-keyboard-image-navigation', THEME_DIR_URI . '/includes/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202', true );
	}

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
		'id'            => 'banner-ad',
		'description'   => 'Banner Ad at top of home page',
	) );
  register_sidebar( array(
		'name'          => 'Upcoming Events',
		'id'            => 'events',
		'description'   => 'Spot for upcoming events widget (on every page)',
	) );
  register_sidebar( array(
		'name'          => 'Top Featured Story',
		'id'            => 'top-feature',
		'description'   => 'Top story on home page',
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

function add_theme_menu_item()
{
	add_menu_page("OvR Settings", "OvR Settings", "manage_options", "ovr-settings", "ovr_settings_page", null, 56);
}

add_action("admin_menu", "add_theme_menu_item");

function ovr_settings_page()
{
    ?>
	    <div class="wrap">
	    <h1>OvR Theme Settings</h1>
	    <form method="post" action="options.php">
	        <?php
	            settings_fields("section");
	            do_settings_sections("theme-options");
	            submit_button();
	        ?>
	    </form>
		</div>
	<?php
}

function display_twitter_element()
{
	?>
    	<input type="text" name="twitter_url" id="twitter_url" value="<?php echo get_option('twitter_url'); ?>" />
    <?php
}

function display_facebook_element()
{
	?>
    	<input type="text" name="facebook_url" id="facebook_url" value="<?php echo get_option('facebook_url'); ?>" />
    <?php
}
function display_instagram_element()
{
	?>
    	<input type="text" name="instagram_url" id="instagram_url" value="<?php echo get_option('instagram_url'); ?>" />
    <?php
}
function display_youtube_element()
{
	?>
    	<input type="text" name="youtube_url" id="youtube_url" value="<?php echo get_option('youtube_url'); ?>" />
    <?php
}
function display_theme_panel_fields()
{
	add_settings_section("section", "Footer Settings", null, "theme-options");

	add_settings_field("twitter_url", "Twitter Profile Url", "display_twitter_element", "theme-options", "section");
  add_settings_field("facebook_url", "Facebook Profile Url", "display_facebook_element", "theme-options", "section");
  add_settings_field("youtube_url", "Youtube Chanel Url", "display_youtube_element", "theme-options", "section");
  add_settings_field("instagram_url", "Instagram Profile Url", "display_instagram_element", "theme-options", "section");

  register_setting("section", "twitter_url");
  register_setting("section", "facebook_url");
  register_setting("section", "youtube_url");
  register_setting("section", "instagram_url");
}

add_action("admin_init", "display_theme_panel_fields");
