<?php
/*
 * 
Plugin Name: Order / Coupon / Subscription Export Import Plugin for WooCommerce (BASIC)
Plugin URI: https://wordpress.org/plugins/order-import-export-for-woocommerce/
Description: Export and Import Order detail including line items, From and To your WooCommerce Store.
Author: WebToffee
Author URI: https://www.webtoffee.com/product/woocommerce-order-coupon-subscription-export-import/
Version: 2.1.5
Text Domain: order-import-export-for-woocommerce
WC tested up to: 6.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/


if ( !defined( 'ABSPATH' ) || !is_admin() ) {
	return;
}


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define ( 'WT_O_IEW_PLUGIN_BASENAME', plugin_basename(__FILE__) );
define ( 'WT_O_IEW_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define ( 'WT_O_IEW_PLUGIN_URL', plugin_dir_url(__FILE__));
define ( 'WT_O_IEW_PLUGIN_FILENAME', __FILE__);
if ( ! defined( 'WT_IEW_PLUGIN_ID_BASIC' ) ) {
    define ( 'WT_IEW_PLUGIN_ID_BASIC', 'wt_import_export_for_woo_basic');
}
define ( 'WT_O_IEW_PLUGIN_NAME','Order/Coupon Import Export for WooCommerce');
define ( 'WT_O_IEW_PLUGIN_DESCRIPTION','Import and Export Order/Coupon From and To your WooCommerce Store.');

if ( ! defined( 'WT_IEW_DEBUG_BASIC' ) ) {
    define ( 'WT_IEW_DEBUG_BASIC', false );
}
if ( !defined( 'WT_IEW_DEBUG_BASIC_TROUBLESHOOT' ) ) {
	define( 'WT_IEW_DEBUG_BASIC_TROUBLESHOOT', 'https://www.webtoffee.com/finding-php-error-logs/' );
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WT_O_IEW_VERSION', '2.1.5' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wt-import-export-for-woo-activator.php
 */
function activate_wt_import_export_for_woo_basic_order() {
//        if(!class_exists( 'WooCommerce' )){
//            deactivate_plugins(basename(__FILE__));
//            wp_die(__("WooCommerce is not installed/actived. it is required for this plugin to work properly. Please activate WooCommerce."), "", array('back_link' => 1));
//        }
//        if(is_plugin_active('wt-import-export-for-woo-order/wt-import-export-for-woo-order.php') || is_plugin_active('wt-import-export-for-woo-coupon/wt-import-export-for-woo-coupon.php')){           
//            deactivate_plugins( basename( __FILE__ ) );            
//            wp_die(
//                    '<p>'.__("Is everything fine? You already have the Premium version installed in your website. For any issues, kindly raise a ticket via <a target='_blank' href='https://www.webtoffee.com/support/'>support</a>")
//                    . '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'go back') . '</a>'
//            );
//        }          
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wt-import-export-for-woo-activator.php';
	Wt_Import_Export_For_Woo_Basic_Activator_Order::activate();
}



/* Checking WC is actived or not */
if ( !function_exists( 'is_plugin_active' ) ) {
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

add_action( 'plugins_loaded', 'wt_order_basic_check_for_woocommerce' );

if ( !function_exists( 'wt_order_basic_check_for_woocommerce' ) ) {

	function wt_order_basic_check_for_woocommerce() {


		if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) || !defined( 'WC_VERSION' ) ) {
			add_action( 'admin_notices', 'wt_wc_missing_warning_order_basic' );
		}
		if ( !function_exists( 'wt_wc_missing_warning_order_basic' ) ) {

			function wt_wc_missing_warning_order_basic() {

				$install_url = wp_nonce_url( add_query_arg( array( 'action' => 'install-plugin', 'plugin' => 'woocommerce', ), admin_url( 'update.php' ) ), 'install-plugin_woocommerce' );
				$class		 = 'notice notice-error';
				$post_type	 = 'order';
				$message	 = sprintf( __( 'The <b>WooCommerce</b> plugin must be active for <b>%s / Coupon / Subscription Export Import Plugin for WooCommerce (BASIC)</b> plugin to work.  Please <a href="%s" target="_blank">install & activate WooCommerce</a>.' ), ucfirst( $post_type ), esc_url( $install_url ) );
				printf( '<div class="%s"><p>%s</p></div>', esc_attr( $class ), ( $message ) );
			}

		}
	}
}

	/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wt-import-export-for-woo-deactivator.php
 */
function deactivate_wt_import_export_for_woo_basic_order() {
        
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wt-import-export-for-woo-deactivator.php';
	Wt_Import_Export_For_Woo_Basic_Deactivator_Order::deactivate();
}

register_activation_hook( __FILE__, 'activate_wt_import_export_for_woo_basic_order' );
register_deactivation_hook( __FILE__, 'deactivate_wt_import_export_for_woo_basic_order' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wt-import-export-for-woo.php';

$advanced_settings = get_option('wt_iew_advanced_settings', array());
$ier_get_max_execution_time = (isset($advanced_settings['wt_iew_maximum_execution_time']) && $advanced_settings['wt_iew_maximum_execution_time'] != '') ? $advanced_settings['wt_iew_maximum_execution_time'] : ini_get('max_execution_time');

if (strpos(@ini_get('disable_functions'), 'set_time_limit') === false) {
        @set_time_limit($ier_get_max_execution_time);
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wt_import_export_for_woo_basic_order() {

    if ( ! defined( 'WT_IEW_BASIC_STARTED' ) ) {
        define ( 'WT_IEW_BASIC_STARTED', 1);
	$plugin = new Wt_Import_Export_For_Woo_Basic();
	$plugin->run();
    }

}
/** this added for a temporary when a plugin update with the option upload zip file. need to remove this after some version release */
if ( !get_option( 'wt_o_iew_is_active' ) ) {
	update_option( 'wt_order_show_legacy_menu', 1 );
	activate_wt_import_export_for_woo_basic_order();
}

if ( get_option( 'wt_o_iew_is_active' ) ) {
    run_wt_import_export_for_woo_basic_order();       
}

/* Plugin page links */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wt_oiew_plugin_action_links_basic_order' );

function wt_oiew_plugin_action_links_basic_order( $links ) {

	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wt_import_export_for_woo_basic' ) . '">' . __( 'Settings' ) . '</a>',
		'<a href="https://www.webtoffee.com/order-coupon-subscription-export-import-plugin-woocommerce-user-guide/" target="_blank">' . __( 'Documentation' ) . '</a>',
		'<a href="https://wordpress.org/support/plugin/order-import-export-for-woocommerce/" target="_blank">' . __( 'Support' ) . '</a>',
		'<a href="https://www.webtoffee.com/product/woocommerce-order-coupon-subscription-export-import/?utm_source=free_plugin_listing&utm_medium=order_imp_exp_basic&utm_campaign=Order_Import_Export&utm_content=' . WT_O_IEW_VERSION . '" style="color:#3db634;">' . __('Premium Upgrade') . '</a>'
	);
	if ( array_key_exists( 'deactivate', $links ) ) {
		$links[ 'deactivate' ] = str_replace( '<a', '<a class="wforderimpexp-deactivate-link"', $links[ 'deactivate' ] );
	}
	return array_merge( $plugin_links, $links );
}

/*
 *  Displays update information for a plugin. 
 */
function wt_order_import_export_for_woocommerce_update_message( $data, $response )
{
    if(isset( $data['upgrade_notice']))
    {
		add_action( 'admin_print_footer_scripts','wt_order_imex_basic_plugin_screen_update_js');
        printf(
        '<div class="update-message wt-update-message">%s</div>',
           $data['upgrade_notice']
        );
    }
}
add_action( 'in_plugin_update_message-order-import-export-for-woocommerce/order-import-export-for-woocommerce.php', 'wt_order_import_export_for_woocommerce_update_message', 10, 2 );

if(!function_exists('wt_order_imex_basic_plugin_screen_update_js'))
{
    function wt_order_imex_basic_plugin_screen_update_js()
    {
        ?>
        <script>
            ( function( $ ){
                var update_dv=$( '#order-import-export-for-woocommerce-update');
                update_dv.find('.wt-update-message').next('p').remove();
                update_dv.find('a.update-link:eq(0)').click(function(){
                    $('.wt-update-message').remove();
                });
            })( jQuery );
        </script>
        <?php
    }
}


// uninstall feedback catch
include_once plugin_dir_path( __FILE__ ) . 'includes/class-wf-orderimpexp-plugin-uninstall-feedback.php';


// Add dismissble banner for legacy menu
include_once plugin_dir_path( __FILE__ ) . 'includes/class-wt-order-legacy-menu-moved.php';
$order_legacy_menu					 = new wt_order_legacy_menu_moved( 'order' );
$order_legacy_menu->plugin_title	 = "Order Import Export";
$order_legacy_menu->old_menu		 = "WooCommerce > Order Im-EX";
$order_legacy_menu->banner_message	 = sprintf( __( "We have introduced a new main menu %sWebToffee Import Export(basic)%s for the %s plugin. Click the button below or dismiss this banner to remove the old menu from %s." ), '<b>', '</b>', $order_legacy_menu->plugin_title, $order_legacy_menu->old_menu );
$order_legacy_menu->old_menu_params	 = array(
	array( 'parent_slug' => 'woocommerce', 'menu_title' => 'Order Im-EX', 'capability' => 'import' )
);

include_once 'class-wt-order-review-request.php';

// Add dismissible server info for file restrictions
include_once plugin_dir_path( __FILE__ ) . 'includes/class-wt-non-apache-info.php';
$inform_server_secure = new wt_inform_server_secure('order');
$inform_server_secure->plugin_title = "Order Import Export";
$inform_server_secure->banner_message = sprintf(__("The <b>%s</b> plugin uploads the imported file into <b>wp-content/webtoffee_import</b> folder. Please ensure that public access restrictions are set in your server for this folder."), $inform_server_secure->plugin_title);


add_action( 'wt_order_addon_basic_help_content', 'wt_order_import_export_basic_help_content' );

function wt_order_import_export_basic_help_content() {
	if ( defined( 'WT_IEW_PLUGIN_ID_BASIC' ) ) {
    ?>
        <li>
            <img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/sample-csv.png">
            <h3><?php _e( 'Sample Order CSV' ); ?></h3>
            <p><?php _e( 'Familiarize yourself with the sample CSV.' ); ?></p>
            <a target="_blank" href="https://www.webtoffee.com/wp-content/uploads/2021/03/Order_SampleCSV.csv" class="button button-primary">
            <?php _e( 'Get Order CSV' ); ?>        
            </a>
        </li>
    <?php
	}
}

add_action( 'wt_coupon_addon_basic_help_content', 'wt_coupon_import_export_basic_help_content' );

function wt_coupon_import_export_basic_help_content() {
	if ( defined( 'WT_IEW_PLUGIN_ID_BASIC' ) ) {
    ?>
        <li>
            <img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/sample-csv.png">
            <h3><?php _e( 'Sample Coupon CSV' ); ?></h3>
            <p><?php _e( 'Familiarize yourself with the sample CSV.' ); ?></p>
            <a target="_blank" href="https://www.webtoffee.com/wp-content/uploads/2016/09/Coupon_Sample_CSV.csv" class="button button-primary">
            <?php _e( 'Get Coupon CSV' ); ?>        
            </a>
        </li>
    <?php
	}
}

add_action( 'wt_order_addon_basic_gopro_content', 'wt_order_addon_basic_gopro_content' );

function wt_order_addon_basic_gopro_content() {
	if ( defined( 'WT_IEW_PLUGIN_ID_BASIC' ) ) {
    ?>
                <div class="wt-ier-coupon wt-ier-order wt-ier-gopro-cta wt-ierpro-features" style="display: none;">
                    <ul class="ticked-list wt-ierpro-allfeat">
                        <li><?php _e('Supports CSV/XML file formats.'); ?></li>
                        <li><?php _e('Export and import subscription orders'); ?></li>
                        <li><?php _e('Import and export custom fields and hidden metadata.'); ?></li>                         
                        <li><?php _e('Run scheduled exports via FTP/SFTP.'); ?></li>
                        <li><?php _e('Run scheduled imports via URL/FTP/SFTP.'); ?></li>
                        <li><?php _e('Tested compatibility with various third-party plugins.'); ?></li>
                    </ul>    
                    <div class="wt-ierpro-btn-wrapper"> 
                        <a href="<?php echo "https://www.webtoffee.com/product/order-import-export-plugin-for-woocommerce/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Order_Import_Export&utm_content=".WT_O_IEW_VERSION; ?>" target="_blank"  class="wt-ierpro-outline-btn"><?php _e('UPGRADE TO PREMIUM'); ?></a>
                    </div>
                    <p style="padding-left:25px;"><b><a href="<?php echo admin_url('admin.php?page=wt_import_export_for_woo_basic#wt-pro-upgrade'); ?>" target="_blank"><?php _e('Get more import export addons >>'); ?></a></b></p>
                </div>
    <?php
	}
}
