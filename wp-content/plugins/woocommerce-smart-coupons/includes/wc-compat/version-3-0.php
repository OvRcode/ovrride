<?php
if ( !defined( 'ABSPATH' ) ) exit;
if ( !class_exists( 'SA_WC_Compatibility_3_0' ) ) {

/**
 * Compatibility class for WooCommerce 3.0.0+
 * 
 * @version 1.0.0
 * @since 3.0.0 28-March-2017
 *
 */
	class SA_WC_Compatibility_3_0 extends SA_WC_Compatibility_2_6 {

		/**
		 * Is WooCommerce Greater Than And Equal To 3.0.0
		 * 
		 * @return boolean 
		 */
		public static function is_wc_gte_30() {
			return self::is_wc_greater_than( '2.6.14' );
		}

	}

}