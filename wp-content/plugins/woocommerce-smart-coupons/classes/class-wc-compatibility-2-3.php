<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'Smart_Coupons_WC_Compatibility_2_3' ) ) {
	
	/**
	 * Class to check for WooCommerce version & return variables accordingly
	 *
	 */
	class Smart_Coupons_WC_Compatibility_2_3 extends Smart_Coupons_WC_Compatibility {

		/**
		 * Is WooCommerce Greater Than And Equal To 2.3
		 * 
		 * @return boolean 
		 */
		public static function is_wc_gte_23() {
			return self::is_wc_greater_than( '2.2.11' );
		}

		/**
		 * Is WooCommerce Greater Than And Equal To 2.2
		 * 
		 * @return boolean 
		 */
		public static function is_wc_gte_22() {
			return self::is_wc_greater_than( '2.1.12' );
		}

		/**
		 * Is WooCommerce Greater Than And Equal To 2.1
		 * 
		 * @return boolean 
		 */
		public static function is_wc_gte_21() {
			return self::is_wc_greater_than( '2.0.20' );
		}

		/**
		 * Is WooCommerce Greater Than And Equal To 2.0
		 * 
		 * @return boolean 
		 */
		public static function is_wc_gte_20() {
			return self::is_wc_greater_than( '1.6.6' );
		}

	}

}