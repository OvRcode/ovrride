<?php
/**
 * Maintain Global Coupon's record
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_Global_Coupons' ) ) {

	/**
	 * Class for handling global coupons
	 */
	class WC_SC_Global_Coupons {

		/**
		 * Variable to hold instance of WC_SC_Global_Coupons
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'admin_init', array( $this, 'set_global_coupons' ) );
			add_action( 'save_post', array( $this, 'update_global_coupons' ), 10, 2 );
			add_action( 'deleted_post', array( $this, 'sc_delete_global_coupons' ) );
			add_action( 'trashed_post', array( $this, 'sc_delete_global_coupons' ) );
			add_action( 'untrashed_post',  array( $this, 'sc_untrash_global_coupons' ) );
			add_action( 'future_to_publish',  array( $this, 'future_to_publish_global_coupons' ) );

		}

		/**
		 * Get single instance of WC_SC_Global_Coupons
		 *
		 * @return WC_SC_Global_Coupons Singleton object of WC_SC_Global_Coupons
		 */
		public static function get_instance() {
			// Check if instance is already exists
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Set global coupons in options table for faster fetching
		 */
		public function set_global_coupons() {

			global $wpdb;

			$global_coupons = get_option( 'sc_display_global_coupons' );

			$current_sc_version = get_option( 'sa_sc_db_version', '' );				//code for updating the db - for autoload related fix

			if ( $global_coupons === false ) {
				$wpdb->query( 'SET SESSION group_concat_max_len=999999' );
				$wpdb->query("INSERT INTO {$wpdb->prefix}options (option_name, option_value, autoload)
								SELECT 'sc_display_global_coupons',
									GROUP_CONCAT(id SEPARATOR ','),
									'no'
								FROM {$wpdb->prefix}posts
								WHERE post_type = 'shop_coupon'
									AND post_status = 'publish'");

				$wpdb->query("UPDATE {$wpdb->prefix}options
								SET option_value = (SELECT GROUP_CONCAT(post_id SEPARATOR ',')
													FROM {$wpdb->prefix}postmeta
													WHERE meta_key = 'customer_email'
														AND CAST(meta_value AS CHAR) = 'a:0:{}'
														AND FIND_IN_SET(post_id, (SELECT option_value FROM (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'sc_display_global_coupons') as temp )) > 0 )
								WHERE option_name = 'sc_display_global_coupons'");

				$wpdb->query("UPDATE {$wpdb->prefix}options
								SET option_value = (SELECT GROUP_CONCAT(post_id SEPARATOR ',')
													FROM {$wpdb->prefix}postmeta
													WHERE meta_key = 'sc_is_visible_storewide'
														AND CAST(meta_value AS CHAR) = 'yes'
														AND FIND_IN_SET(post_id, (SELECT option_value FROM (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'sc_display_global_coupons') as temp )) > 0 )
								WHERE option_name = 'sc_display_global_coupons'");

				$wpdb->query("UPDATE {$wpdb->prefix}options
								SET option_value = (SELECT GROUP_CONCAT(post_id SEPARATOR ',')
													FROM {$wpdb->prefix}postmeta
													WHERE meta_key = 'auto_generate_coupon'
														AND CAST(meta_value AS CHAR) != 'yes'
														AND FIND_IN_SET(post_id, (SELECT option_value FROM (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'sc_display_global_coupons') as temp )) > 0 )
								WHERE option_name = 'sc_display_global_coupons'");

				$wpdb->query("UPDATE {$wpdb->prefix}options
								SET option_value = (SELECT GROUP_CONCAT(post_id SEPARATOR ',')
													FROM {$wpdb->prefix}postmeta
													WHERE meta_key = 'discount_type'
														AND CAST(meta_value AS CHAR) != 'smart_coupon'
														AND FIND_IN_SET(post_id, (SELECT option_value FROM (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'sc_display_global_coupons') as temp )) > 0 )
								WHERE option_name = 'sc_display_global_coupons'");
			} elseif ( ( empty( $current_sc_version ) || version_compare( $current_sc_version, '3.3.6', '<' ) ) && $global_coupons !== false ) {

				$wpdb->query("UPDATE {$wpdb->prefix}options 
								SET autoload = 'no'
								WHERE option_name = 'sc_display_global_coupons'");

				update_option( 'sa_sc_db_version', '3.3.6', 'no' );
			}

		}

		/**
		 * function to update list of global coupons
		 *
		 * @param int    $post_id
		 * @param string $action
		 */
		public function sc_update_global_coupons( $post_id, $action = 'add' ) {
			if ( empty( $post_id ) ) { return;
			}
			if ( 'shop_coupon' != get_post_type( $post_id ) ) { return;
			}

			$coupon_meta = get_post_meta( $post_id );
			$coupon_status = get_post_status( $post_id );

			$global_coupons_list = get_option( 'sc_display_global_coupons' );
			$global_coupons = ( ! empty( $global_coupons_list ) ) ? explode( ',',$global_coupons_list ) : array();
			$key = array_search( $post_id, $global_coupons );

			if ( ( $coupon_status == 'publish'
					&& ( ! empty( $coupon_meta['customer_email'][0] ) && $coupon_meta['customer_email'][0] == serialize( array() ) )
					&& ( ! empty( $coupon_meta['sc_is_visible_storewide'][0] ) && $coupon_meta['sc_is_visible_storewide'][0] == 'yes' )
					&& ( ! empty( $coupon_meta['auto_generate_coupon'][0] ) && $coupon_meta['auto_generate_coupon'][0] != 'yes' )
					&& ( ! empty( $coupon_meta['discount_type'][0] ) && $coupon_meta['discount_type'][0] != 'smart_coupon' ) )
				|| ( $coupon_status == 'trash' && $action == 'delete' ) ) {

				if ( $action == 'add' && $key === false ) {
					$global_coupons[] = $post_id;
				} elseif ( $action == 'delete' && $key !== false ) {
					unset( $global_coupons[ $key ] );
				}
			} else {
				if ( $key !== false ) {
					unset( $global_coupons[ $key ] );
				}
			}

			update_option( 'sc_display_global_coupons', implode( ',',$global_coupons ), 'no' );
		}

		/**
		 * function to update list of global coupons on trash / delete coupon
		 *
		 * @param int $post_id
		 */
		public function sc_delete_global_coupons( $post_id ) {
			if ( empty( $post_id ) ) { return;
			}
			if ( 'shop_coupon' != get_post_type( $post_id ) ) { return;
			}

			$this->sc_update_global_coupons( $post_id , 'delete' );
		}

		/**
		 * function to update list of global coupons on untrash coupon
		 *
		 * @param int $post_id
		 */
		public function sc_untrash_global_coupons( $post_id ) {
			if ( empty( $post_id ) ) { return;
			}
			if ( 'shop_coupon' != get_post_type( $post_id ) ) { return;
			}

			$this->sc_update_global_coupons( $post_id );
		}

		/**
		 * Update global coupons data for sheduled coupons
		 * @param  WP_Post $post 
		 */
		public function future_to_publish_global_coupons( $post = null ) {
			$post_id = ( ! empty( $post->ID ) ) ? $post->ID : 0;

			if ( empty( $post ) || empty( $post_id ) ) { return;
			}
			if ( $post->post_type != 'shop_coupon' ) { return;
			}

			$this->sc_update_global_coupons( $post_id );
		}

		/**
		 * Update global coupons on saving coupon
		 *
		 * @param  int $post_id
		 * @param  object $post
		 */
		public function update_global_coupons( $post_id, $post ) {
			if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) { return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return;
			}
			if ( is_int( wp_is_post_revision( $post ) ) ) { return;
			}
			if ( is_int( wp_is_post_autosave( $post ) ) ) { return;
			}
			if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) ) { return;
			}
			if ( ! current_user_can( 'edit_post', $post_id ) ) { return;
			}
			if ( $post->post_type != 'shop_coupon' ) { return;
			}

			$this->sc_update_global_coupons( $post_id );

		}



	}

}

WC_SC_Global_Coupons::get_instance();
