<?php
/**
 * Smart Coupons Initialize
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_Act_Deact' ) ) {

	/**
	 * Class for handling actions to be performed during initialization
	 */
	class WC_SC_Act_Deact {

		/**
		 * Database changes required for Smart Coupons
		 *
		 * Add option 'smart_coupon_email_subject' if not exists
		 * Enable 'Auto Generation' for Store Credit (discount_type: 'smart_coupon') not having any customer_email
		 * Disable 'apply_before_tax' for all Store Credit (discount_type: 'smart_coupon')
		 */
		static function smart_coupon_activate() {
			global $wpdb, $blog_id;

			if ( is_multisite() ) {
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}", 0 );
			} else {
				$blog_ids = array( $blog_id );
			}

			if ( ! get_option( 'smart_coupon_email_subject' ) ) {
				add_option( 'smart_coupon_email_subject' );
			}

			foreach ( $blog_ids as $blog_id ) {

				if ( ( file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) && ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) ) {

					$wpdb_obj = clone $wpdb;
					$wpdb->blogid = $blog_id;
					$wpdb->set_prefix( $wpdb->base_prefix );

					$query = "SELECT postmeta.post_id FROM {$wpdb->prefix}postmeta as postmeta WHERE postmeta.meta_key = 'discount_type' AND postmeta.meta_value = 'smart_coupon' AND postmeta.post_id IN
							(SELECT p.post_id FROM {$wpdb->prefix}postmeta AS p WHERE p.meta_key = 'customer_email' AND p.meta_value = 'a:0:{}') ";

					$results = $wpdb->get_col( $query );

					foreach ( $results as $result ) {
						update_post_meta( $result, 'auto_generate_coupon', 'yes' );
					}
					// To disable apply_before_tax option for Gift Certificates / Store Credit.
					$post_id_tax_query = "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = 'discount_type' AND meta_value = 'smart_coupon'";

					$tax_post_ids = $wpdb->get_col( $post_id_tax_query );

					foreach ( $tax_post_ids as $tax_post_id ) {
						update_post_meta( $tax_post_id, 'apply_before_tax', 'no' );
					}

					$wpdb = clone $wpdb_obj;
				}
			}

			if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
			    set_transient( '_smart_coupons_activation_redirect', 1, 30 );
			}

		}

		/**
		 * Database changes required for Smart Coupons
		 *
		 * Delete option 'sc_display_global_coupons' if exists
		 */
		static function smart_coupon_deactivate() {
			if ( get_option( 'sc_display_global_coupons' ) !== false ) {
				delete_option( 'sc_display_global_coupons' );
			}
			if ( ( get_option( 'sc_flushed_rules' ) == false ) || ( get_option( 'sc_flushed_rules' ) == 'found' ) ) {
				delete_option( 'sc_flushed_rules' );
			}
		}

	}

}
