<?php
/**
 * Plugin Name: WooCommerce Product Add-ons
 * Plugin URI: https://woocommerce.com/products/product-add-ons/
 * Description: Add extra options to products which your customers can select from, when adding to the cart, with an optional fee for each extra option. Add-ons can be checkboxes, a select box, or custom text input.
 * Version: 3.0.5
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Requires at least: 3.8
 * Tested up to: 5.0
 * WC tested up to: 3.5
 * WC requires at least: 2.6
 * Text Domain: woocommerce-product-addons
 * Domain Path: /languages/
 * Copyright: © 2018 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Woo: 18618:147d0077e591e16db9d0d67daeb8c484
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.Files.FileName

/**
 * Required functions.
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates.
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '147d0077e591e16db9d0d67daeb8c484', '18618' );

/**
 * WooCommerce fallback notice.
 *
 * @since 4.1.2
 * @return string
 */
function woocommerce_product_addons_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Product Add-ons requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-product-addons' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

register_activation_hook( __FILE__, 'woocommerce_product_addons_activation' );

/**
 * Activation.
 *
 * @since 3.0.0
 */
function woocommerce_product_addons_activation() {
	set_transient( 'wc_pao_activation_notice', true, 60 );
	set_transient( 'wc_pao_pre_wc_30_notice', true, 60 );
}

add_action( 'plugins_loaded', 'woocommerce_product_addons_init', 9 );

function woocommerce_product_addons_init() {
	load_plugin_textdomain( 'woocommerce-product-addons', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_product_addons_missing_wc_notice' );
		return;
	}

	if ( ! class_exists( 'WC_Product_Addons' ) ) :
		define( 'WC_PRODUCT_ADDONS_VERSION', '3.0.5' );
		define( 'WC_PRODUCT_ADDONS_MAIN_FILE', __FILE__ );
		define( 'WC_PRODUCT_ADDONS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
		define( 'WC_PRODUCT_ADDONS_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

		/**
		 * Main class.
		 */
		class WC_Product_Addons {

			protected $groups_controller;

			/**
			 * Constructor.
			 */
			public function __construct() {
				$this->init();
				add_action( 'init', array( $this, 'init_post_types' ), 20 );
				add_action( 'init', array( 'WC_Product_Addons_install', 'init' ) );
				add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
				add_action( 'admin_notices', array( $this, 'notices' ) );
			}

			/**
			 * Initializes plugin classes.
			 *
			 * @version 2.9.0
			 */
			public function init() {
				if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '<' ) ) {
					require_once( dirname( __FILE__ ) . '/legacy/class-wc-product-addons-helper.php' );
				} else {
					require_once( dirname( __FILE__ ) . '/includes/class-wc-product-addons-helper.php' );
				}

				// Pre 3.0 conversion helper to be remove in future.
				require_once( dirname( __FILE__ ) . '/includes/updates/class-wc-product-addons-3-0-conversion-helper.php' );

				require_once( dirname( __FILE__ ) . '/includes/class-wc-product-addons-install.php' );

				if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '<' ) ) {
					// Core (models)
					require_once( dirname( __FILE__ ) . '/legacy/groups/class-product-addon-group-validator.php' );
					require_once( dirname( __FILE__ ) . '/legacy/groups/class-product-addon-global-group.php' );
					require_once( dirname( __FILE__ ) . '/legacy/groups/class-product-addon-product-group.php' );
					require_once( dirname( __FILE__ ) . '/legacy/groups/class-product-addon-groups.php' );
				} else {
					// Core (models)
					require_once( dirname( __FILE__ ) . '/includes/groups/class-wc-product-addons-group-validator.php' );
					require_once( dirname( __FILE__ ) . '/includes/groups/class-wc-product-addons-global-group.php' );
					require_once( dirname( __FILE__ ) . '/includes/groups/class-wc-product-addons-product-group.php' );
					require_once( dirname( __FILE__ ) . '/includes/groups/class-wc-product-addons-groups.php' );
				}

				// Admin
				if ( is_admin() ) {
					require_once( dirname( __FILE__ ) . '/includes/admin/class-wc-product-addons-privacy.php' );

					// Handle WooCommerce 3.0 compatibility.
					if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '<' ) ) {
						require_once( dirname( __FILE__ ) . '/legacy/admin/class-product-addon-admin.php' );
						require_once( dirname( __FILE__ ) . '/legacy/admin/class-product-addon-admin-legacy.php' );

						$GLOBALS['Product_Addon_Admin'] = new Product_Addon_Admin_Legacy();
					} else {
						require_once( dirname( __FILE__ ) . '/includes/admin/class-wc-product-addons-admin.php' );

						$GLOBALS['Product_Addon_Admin'] = new WC_Product_Addons_Admin();
					}
				}

				// Handle WooCommerce 3.0 compatibility.
				if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '<' ) ) {
					require_once( dirname( __FILE__ ) . '/legacy/includes/class-product-addon-display.php' );
					require_once( dirname( __FILE__ ) . '/legacy/includes/class-product-addon-cart.php' );
					require_once( dirname( __FILE__ ) . '/legacy/includes/class-product-addon-ajax.php' );

					require_once( dirname( __FILE__ ) . '/legacy/includes/class-product-addon-display-legacy.php' );
					require_once( dirname( __FILE__ ) . '/legacy/includes/class-product-addon-cart-legacy.php' );
					require_once( dirname( __FILE__ ) . '/legacy/includes/class-wc-addons-ajax.php' );

					$GLOBALS['Product_Addon_Display'] = new Product_Addon_Display_Legacy();
					$GLOBALS['Product_Addon_Cart']    = new Product_Addon_Cart_Legacy();
					new WC_Addons_Ajax();
				} else {
					require_once( dirname( __FILE__ ) . '/includes/class-wc-product-addons-display.php' );
					require_once( dirname( __FILE__ ) . '/includes/class-wc-product-addons-cart.php' );
					require_once( dirname( __FILE__ ) . '/includes/class-wc-product-addons-ajax.php' );

					$GLOBALS['Product_Addon_Display'] = new WC_Product_Addons_Display();
					$GLOBALS['Product_Addon_Cart']    = new WC_Product_Addons_Cart();
					new WC_Product_Addons_Cart_Ajax();
				}
			}

			/**
			 * Init post types used for addons.
			 */
			public function init_post_types() {
				register_post_type(
					'global_product_addon',
					array(
						'public'              => false,
						'show_ui'             => false,
						'capability_type'     => 'product',
						'map_meta_cap'        => true,
						'publicly_queryable'  => false,
						'exclude_from_search' => true,
						'hierarchical'        => false,
						'rewrite'             => false,
						'query_var'           => false,
						'supports'            => array( 'title' ),
						'show_in_nav_menus'   => false,
					)
				);

				register_taxonomy_for_object_type( 'product_cat', 'global_product_addon' );
			}

			/**
			 * Initialize the REST API
			 *
			 * @since 2.9.0
			 * @param WP_Rest_Server $wp_rest_server
			 */
			public function rest_api_init( $wp_rest_server ) {
				require_once( dirname( __FILE__ ) . '/includes/api/wc-product-add-ons-groups-controller-v1.php' );
				$this->groups_controller = new WC_Product_Add_Ons_Groups_Controller();
				$this->groups_controller->register_routes();
			}

			/**
			 * Plugin action links
			 */
			public function action_links( $links ) {
				$plugin_links = array(
					'<a href="https://woocommerce.com/my-account/tickets/">' . __( 'Support', 'woocommerce-product-addons' ) . '</a>',
					'<a href="https://docs.woocommerce.com/document/product-add-ons/">' . __( 'Documentation', 'woocommerce-product-addons' ) . '</a>',
				);
				return array_merge( $plugin_links, $links );
			}

			/**
			 * On activation.
			 * Runs on activation. Assigns a notice message to a WordPress option.
			 */
			public function notices() {
				$show_activate_notice = get_transient( 'wc_pao_activation_notice' );

				if ( $show_activate_notice ) {
					echo '<div class="notice is-dismissible updated"><p><strong>' . __( 'WooCommerce Product Add-ons is ready to go!', 'woocommerce-product-addons' ) . '</strong></p><p>' . __( 'Create an add-on that applies to every product, or apply it to specific categories. Create an add-on for an individual product by editing the product.', 'woocommerce-product-addons' ) . '</p><p><a href="' . esc_url( admin_url() ) . 'edit.php?post_type=product&page=addons" class="button button-primary">' . __( 'Create add-ons', 'woocommerce-product-addons' ) . '</a>&nbsp;&nbsp;<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=product" class="button">' . __( 'Find products', 'woocommerce-product-addons' ) . '</a></p></div>';

					delete_transient( 'wc_pao_activation_notice' );
				}

				$show_pre_wc_30_notice = get_transient( 'wc_pao_pre_wc_30_notice' );

				if ( $show_pre_wc_30_notice && version_compare( WC_VERSION, '3.0', '<' ) ) {
					echo '<div class="notice error is-dismissible updated"><p><strong>' . __( 'New features of the Product Add-ons 3.0 versions are not available for WooCommerce less than 3.0. To use this new features, please update your WooCommerce version to the latest version.', 'woocommerce-product-addons' ) . '</strong></p></div>';

					delete_transient( 'wc_pao_pre_wc_30_notice' );
				}
			}
		}

		new WC_Product_Addons();

	endif;
}
