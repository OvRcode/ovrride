<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'woocommerce/woocommerce.php' ) ){
	return;
}

class ACUI_WooCommerce{
	private $all_virtual;

	function __construct(){
		add_filter( 'acui_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_action( 'acui_documentation_after_plugins_activated', array( $this, 'documentation' ) );
		add_action( 'post_acui_import_single_user', array( $this, 'sync_wc_customer' ), 10, 4 );
		add_action( 'after_acui_import_users', array( $this, 'clear_transients' ) );
		add_filter( 'acui_import_email_body_before_wpautop', array( $this, 'include_overrides_email' ), 10, 5 );
		add_action( 'acui_email_wildcards_list_elements', array( $this, 'new_wildcards_email' ) );
        add_filter( 'acui_force_reset_password_edit_profile_url', array( $this, 'force_reset_password_edit_profile_url' ) );
        add_filter( 'acui_force_reset_password_redirect_condition', array( $this, 'force_reset_password_redirect_condition' ) );
        add_action( 'wp_head', array( $this, 'force_reset_password_notice' ) );
        add_action( 'woocommerce_save_account_details', array( $this, 'force_reset_save_account_details' ) );
	}

	function fields(){
		return array(
			'billing_first_name', // Billing Address Info
			'billing_last_name',
			'billing_company',
			'billing_address_1',
			'billing_address_2',
			'billing_city',
			'billing_state',
			'billing_postcode',
			'billing_country',
			'billing_email',
			'billing_phone',

			'shipping_first_name', // Shipping Address Info
			'shipping_last_name',
			'shipping_company',
			'shipping_address_1',
			'shipping_address_2',
			'shipping_city',
			'shipping_state',
			'shipping_postcode',
			'shipping_country',
		);
	}

	function restricted_fields( $acui_restricted_fields ){
		return array_merge( $acui_restricted_fields, $this->fields() );
	}

	function documentation(){
	?>
		<tr valign="top">
			<th scope="row"><?php _e( "WooCommerce is activated", 'import-users-from-csv-with-meta' ); ?></th>
			<td><?php _e( "You can use those labels if you want to set data adapted to the WooCommerce default user columns", 'import-users-from-csv-with-meta' ); ?>
				<ol>
					<li>billing_first_name</li>
					<li>billing_last_name</li>
					<li>billing_company</li>
					<li>billing_address_1</li>
					<li>billing_address_2</li>
					<li>billing_city</li>
					<li>billing_postcode</li>
					<li>billing_country</li>
					<li>billing_state</li>
					<li>billing_phone</li>
					<li>billing_email</li>
					<li>shipping_first_name</li>
					<li>shipping_last_name</li>
					<li>shipping_company</li>
					<li>shipping_address_1</li>
					<li>shipping_address_2</li>
					<li>shipping_city</li>
					<li>shipping_postcode</li>
					<li>shipping_country</li>
					<li>shipping_state</li>
				</ol>
			</td>
		</tr>
		<?php
	}

	function sync_wc_customer( $headers, $data, $user_id, $role ){
		if( !in_array( 'customer', $role ) || !class_exists( 'WC_Customer' ) )
			return;

		$customer = new WC_Customer( $user_id );
		$customer->save();
	}

	function clear_transients(){
		if( !class_exists( 'WC_Customer' ) )
			return;

		wc_delete_product_transients();
		wc_delete_shop_order_transients();
		delete_transient( 'wc_count_comments' );
	}

	function include_overrides_email( $body, $headers, $data, $created, $user_id ){
		$user_data = get_user_by( 'ID', $user_id );
		$reset_key = get_password_reset_key( $user_data );

		$body = str_replace( "**woocommercelostpasswordurl**", wc_lostpassword_url(), $body );

		$woocommerce_password_reset_url = esc_url( add_query_arg( array( 'key' => $reset_key, 'id' => $user_id ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) ) );
		$body = str_replace( "**woocommercepasswordreseturl**", $woocommerce_password_reset_url, $body );
	
		$woocommerce_password_reset_url_link = wp_sprintf( '<a href="%s">%s</a>', $woocommerce_password_reset_url, __( 'Password reset link', 'import-users-from-csv-with-meta' ) );
		$body = str_replace( "**woocommercepasswordreseturllink**", $woocommerce_password_reset_url_link, $body );

		return $body;
	}

	function new_wildcards_email(){
		?>
		<li>**woocommercelostpasswordurl** = <?php _e( 'WooCommerce lost password url', 'import-users-from-csv-with-meta' ); ?></li>
		<li>**woocommercepasswordreseturl** = <?php _e( 'WooCommerce password reset url', 'import-users-from-csv-with-meta' ); ?>
		<li>**woocommercepasswordreseturllink** = <?php _e( 'WooCommerce password reset url with HTML link', 'import-users-from-csv-with-meta' ); ?>
		<?php
	}

    function force_reset_password_edit_profile_url(){
        return wc_customer_edit_account_url() . '#password_current';
    }

    function force_reset_password_redirect_condition( $condition ){
		global $wp;
		return home_url( $wp->request ) . "/" == wc_customer_edit_account_url();
	}

    function force_reset_password_notice(){
        if ( get_user_meta( get_current_user_id(), 'acui_force_reset_password', true ) && !wc_has_notice( apply_filters( 'acui_force_reset_password_message', __( 'Please change your password', 'import-users-from-csv-with-meta' ) ), 'error' ) ) {
            wc_add_notice( apply_filters( 'acui_force_reset_password_message', __( 'Please change your password', 'import-users-from-csv-with-meta' ) ), 'error' );
        }
    }

    function force_reset_save_account_details( $user_id ){
        $pass1 = $pass2 = '';

		if ( isset( $_POST['password_1'] ) )
			$pass1 = $_POST['password_1'];

		if ( isset( $_POST['password_2'] ) )
			$pass2 = $_POST['password_2'];

		if ( $pass1 != $pass2 || empty( $pass1 ) || empty( $pass2 ) || false !== strpos( stripslashes( $pass1 ), "\\" ) )
			return;

		delete_user_meta( $user_id, 'acui_force_reset_password' );
    }
}

new ACUI_WooCommerce();