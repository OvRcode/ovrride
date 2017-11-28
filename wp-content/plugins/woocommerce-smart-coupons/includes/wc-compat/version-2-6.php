<?php
if ( ! defined( 'ABSPATH' ) ) { exit;
}
if ( ! class_exists( 'SA_WC_Compatibility_2_6' ) ) {

	/**
	 * Compatibility class for WooCommerce 2.6+
	 *
	 * @version 1.0.0
	 * @since 2.6 14-June-2016
	 */
	class SA_WC_Compatibility_2_6 extends SA_WC_Compatibility_2_5 {

		/**
		 * Is WooCommerce Greater Than And Equal To 2.6
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_26() {
			return self::is_wc_greater_than( '2.5.5' );
		}

	}

}
