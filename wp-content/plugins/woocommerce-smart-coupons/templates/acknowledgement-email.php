<?php
/**
 * Acknowledgement Email Content
 *
 * @author      StoreApps
 * @version     1.3.0
 * @package     woocommerce-smart-coupons/templates/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $store_credit_label;

if ( ! isset( $email ) ) {
	$email = null;
}

if ( has_action( 'woocommerce_email_header' ) ) {
	do_action( 'woocommerce_email_header', $email_heading, $email );
} else {
	if ( function_exists( 'wc_get_template' ) ) {
		wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	} else {
		woocommerce_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	}
}

$receivers_email = array_unique( $receivers_detail );

if ( empty( $email_scheduled_details ) ) {
	/* translators: 1. Receiver's count 2. Singular/Plural label for store credit(s) 3. Receiver name 4. Receiver details */
	$message = __( 'You have successfully sent %1$d %2$s %3$s %4$s', 'woocommerce-smart-coupons' );
} else {
	/* translators: 1. Receiver's count 2. Gift Card/s 3. Receiver name 4. Receiver details */
	$message         = __( 'You have scheduled to send %1$d %2$s %3$s %4$s', 'woocommerce-smart-coupons' );
	$receivers_email = array_map(
		function( $email ) use ( $email_scheduled_details ) {
			// Filter for time format of acknowledgement email.
			$time_format = apply_filters( 'wc_sc_acknowledgement_email_time_format', get_option( 'date_format', 'Y-m-d' ) . ' ' . get_option( 'time_format', 'H:i' ) );
			// Check if the scheduled timestamps are available.
			if ( isset( $email_scheduled_details[ $email ] ) && is_array( $email_scheduled_details[ $email ] ) ) {
					$scheduled_times = array_map(
						function( $time ) use ( $time_format ) {
							// Convert to date format.
							$time = get_date_from_gmt( gmdate( 'c', $time ), $time_format );
							return date_i18n( $time_format, strtotime( $time ) );
						},
						$email_scheduled_details[ $email ]
					);
					// Concat scheduled times to comma separated times.
					return $email . ' ' . __( 'on', 'woocommerce-smart-coupons' ) . ' ' . implode( ', ', $scheduled_times );
			}
			return $email;
		},
		$receivers_email
	);
}

$singular    = ( ! empty( $store_credit_label['singular'] ) ) ? ucwords( $store_credit_label['singular'] ) : __( 'Gift card', 'woocommerce-smart-coupons' );
$plural      = ( ! empty( $store_credit_label['plural'] ) ) ? ucwords( $store_credit_label['plural'] ) : __( 'Gift cards', 'woocommerce-smart-coupons' );
$coupon_type = ( $receiver_count > 1 ) ? $plural : $singular;

if ( 'yes' === $contains_core_coupons ) {
	$coupon_type = _n( 'Coupon', 'Coupons', $receiver_count, 'woocommerce-smart-coupons' );
}

$is_receiver_name = ! empty( $gift_certificate_receiver_name );

echo esc_html( sprintf( $message, $receiver_count, strtolower( $coupon_type ), ( ( ! empty( $gift_certificate_receiver_name ) || ! empty( $receivers_email ) ) ? __( 'to', 'woocommerce-smart-coupons' ) . ' ' . $gift_certificate_receiver_name : '' ), ( true === $is_receiver_name ? '(' : '' ) . implode( ', ', $receivers_email ) . ( true === $is_receiver_name ? ')' : '' ) ) );

if ( has_action( 'woocommerce_email_footer' ) ) {
	do_action( 'woocommerce_email_footer', $email );
} else {
	if ( function_exists( 'wc_get_template' ) ) {
		wc_get_template( 'emails/email-footer.php' );
	} else {
		woocommerce_get_template( 'emails/email-footer.php' );
	}
}
