<?php
/**
 * Plugin Name: Woo360 Modules
 * Plugin URI: http://www.marketing360.com/
 * Description: Custom Modules for Woo360 Builder.
 * Version: 0.1.3
 * Author: Madwire
 */
define( 'WOO360_MODULES_DIR', plugin_dir_path( __FILE__ ) );
define( 'WOO360_MODULES_URL', plugins_url( '/', __FILE__ ) );
define( 'CB_CUSTOM_MODULE_DIR', plugin_dir_path( __FILE__ ) );
define( 'CB_CUSTOM_MODULE_URL', plugins_url( '/', __FILE__ ) );


// load custom modules for woo360 builder
function woo360_load_modules() {
    if ( class_exists( 'FLBuilder' ) ) {
        
        //general helper for modules
        require_once 'modules/helper.php';
    
        require_once 'modules/woo360-gravity-form/woo360-gravity-form.php';    
        require_once 'modules/woo360-cart-login/woo360-cart-login.php';
        
        // still testing the featured product module
        // require_once 'modules/woo360-featured-product/woo360-featured-product.php';

        // Require custom 'media file' field type
        require_once 'includes/BB-PDF-field-modified/zestsms-pdf.php';
        require_once 'includes/BB-date-Field-master/bb_date_field.php';
        require_once 'includes/responsive-borders-helper.php';

        // Gallery and image modules
        require_once 'cb-shade/cb-shade.php';
        require_once 'cb-poise/cb-poise.php';
        require_once 'cb-spotlight/cb-spotlight.php';
        require_once 'cb-broadside/cb-broadside.php';
        require_once 'cb-slice/cb-slice.php';
        require_once 'cb-caption/cb-caption.php';
        require_once 'cb-drawerfolio/cb-drawerfolio.php';

        if( class_exists( 'FLIconModule' ) ){
            require_once 'cb-link-list/cb-link-list.php';
            require_once 'cb-button-list/cb-button-list.php';
        }
        require_once 'cb-simple-events/cb-simple-events.php';
    
    }
}
add_action( 'init', 'woo360_load_modules' );


// load plugin update checker functionality
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'http://woo360-updates.madwirebuild4.com/?action=get_metadata&slug=woo360-modules',
	__FILE__, //Full path to the main plugin file or functions.php.
	'woo360-modules'
);

// add custom keyboard shortcut for woo360 builder
add_filter( 'fl_builder_keyboard_shortcuts', function( $shortcuts ) {
    $shortcuts['goToAdminDash'] = array(
        'label' => __( 'Go To Admin Dashboard', 'woo360-modules'),
        'keyCode' => 'mod+d'
        );
    return $shortcuts;
});

// enque scripts for keyboard shortcut
add_action( 'wp_enqueue_scripts', function() {
    // Check if Page Builder is active 
    if ( class_exists('FLBuilderModel') && FLBuilderModel::is_builder_active() ) {
        // add js for custom keyboard shortcut 'open admin dash ctrl+d'
        wp_enqueue_script('go-to-admin-dash', 
            WOO360_MODULES_URL . '/keyboard-shortcuts/js/go-to-admin-dash.js', 
            array('fl-builder-min') );
    }
});
