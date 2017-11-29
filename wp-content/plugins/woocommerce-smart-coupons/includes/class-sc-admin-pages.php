<?php
/**
 * Smart Coupons Admin Pages
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_Admin_Pages' ) ) {

	/**
	 * Class for handling admin pages of Smart Coupons
	 */
	class WC_SC_Admin_Pages {

		/**
		 * Variable to hold instance of WC_SC_Admin_Pages
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			add_filter( 'views_edit-shop_coupon', array( $this, 'smart_coupons_views_row' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'generate_coupon_styles_and_scripts' ) );
			add_action( 'admin_notices', array( $this, 'woocommerce_show_import_message' ) );

			add_action( 'admin_menu', array( $this, 'woocommerce_coupon_admin_menu' ) );
			add_action( 'admin_head', array( $this, 'woocommerce_coupon_admin_head' ) );

			add_action( 'admin_footer', array( $this, 'smart_coupons_script_in_footer' ) );
			add_action( 'admin_init', array( $this, 'woocommerce_coupon_admin_init' ) );

			add_action( 'smart_coupons_display_views', array( $this, 'smart_coupons_display_views' ) );

			if ( isset( $_GET['import'] ) && $_GET['import'] == 'woocommerce_smart_coupon_csv' ||
				isset( $_GET['page'] ) && $_GET['page'] == 'woocommerce_smart_coupon_csv_import' ) {
				ob_start();
			}

		}

		/**
		 * Get single instance of WC_SC_Admin_Pages
		 *
		 * @return WC_SC_Admin_Pages Singleton object of WC_SC_Admin_Pages
		 */
		public static function get_instance() {
			// Check if instance is already exists
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param $function_name string
		 * @param $arguments array of arguments passed while calling $function_name
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) { return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * function to trigger an additional hook while creating different views
		 *
		 * @param array $views available views
		 * @return array $views
		 */
		public function smart_coupons_views_row( $views = null ) {

			global $typenow;

			if ( $typenow == 'shop_coupon' ) {
				do_action( 'smart_coupons_display_views' );
			}

			return $views;

		}

		/**
		 * function to add tabs to access Smart Coupons' feature
		 */
		public function smart_coupons_display_views() {
			?>
			<div id="smart_coupons_tabs">
				<h2 class="nav-tab-wrapper">
					<?php
						echo '<a href="' . trailingslashit( admin_url() ) . 'edit.php?post_type=shop_coupon" class="nav-tab nav-tab-active">' . __( 'Coupons', WC_SC_TEXT_DOMAIN ) . '</a>';
						echo '<a href="' . trailingslashit( admin_url() ) . 'admin.php?page=woocommerce_smart_coupon_csv_import" class="nav-tab">' . __( 'Bulk Generate / Import Coupons', WC_SC_TEXT_DOMAIN ) . '</a>';
						echo '<a href="' . trailingslashit( admin_url() ) . 'admin.php?page=woocommerce_smart_coupon_csv_import&tab=send_certificate" class="nav-tab">' . __( 'Send Store Credit', WC_SC_TEXT_DOMAIN ) . '</a>';
					?>
				</h2>
			</div>
			<?php
		}

		/**
		 * Function to include styles & script for 'Generate Coupon' page
		 */
		public function generate_coupon_styles_and_scripts() {
			global $pagenow, $wp_scripts;
			if ( empty( $pagenow ) || $pagenow != 'admin.php' ) { return;
			}
			if ( empty( $_GET['page'] ) || $_GET['page'] != 'woocommerce_smart_coupon_csv_import' ) { return;
			}

			$suffix         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			$locale  = localeconv();
			$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';

			wp_enqueue_style( 'woocommerce_admin_menu_styles', WC()->plugin_url() . '/assets/css/menu.css', array(), WC()->version );
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC()->version );
			wp_enqueue_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );

			$woocommerce_admin_params = array(
				'i18n_decimal_error'                => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', 'woocommerce' ), $decimal ),
				'i18n_mon_decimal_error'            => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', 'woocommerce' ), wc_get_price_decimal_separator() ),
				'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', 'woocommerce' ),
				'i18_sale_less_than_regular_error'  => __( 'Please enter in a value less than the regular price.', 'woocommerce' ),
				'decimal_point'                     => $decimal,
				'mon_decimal_point'                 => wc_get_price_decimal_separator(),
				'strings' => array(
					'import_products' => __( 'Import', 'woocommerce' ),
					'export_products' => __( 'Export', 'woocommerce' ),
				),
				'urls' => array(
					'import_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ),
					'export_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ),
				),
			);

			$woocommerce_admin_meta_boxes_params = array(
				'remove_item_notice'            => __( 'Are you sure you want to remove the selected items? If you have previously reduced this item\'s stock, or this order was submitted by a customer, you will need to manually restore the item\'s stock.', 'woocommerce' ),
				'i18n_select_items'             => __( 'Please select some items.', 'woocommerce' ),
				'i18n_do_refund'                => __( 'Are you sure you wish to process this refund? This action cannot be undone.', 'woocommerce' ),
				'i18n_delete_refund'            => __( 'Are you sure you wish to delete this refund? This action cannot be undone.', 'woocommerce' ),
				'i18n_delete_tax'               => __( 'Are you sure you wish to delete this tax column? This action cannot be undone.', 'woocommerce' ),
				'remove_item_meta'              => __( 'Remove this item meta?', 'woocommerce' ),
				'remove_attribute'              => __( 'Remove this attribute?', 'woocommerce' ),
				'name_label'                    => __( 'Name', 'woocommerce' ),
				'remove_label'                  => __( 'Remove', 'woocommerce' ),
				'click_to_toggle'               => __( 'Click to toggle', 'woocommerce' ),
				'values_label'                  => __( 'Value(s)', 'woocommerce' ),
				'text_attribute_tip'            => __( 'Enter some text, or some attributes by pipe (|) separating values.', 'woocommerce' ),
				'visible_label'                 => __( 'Visible on the product page', 'woocommerce' ),
				'used_for_variations_label'     => __( 'Used for variations', 'woocommerce' ),
				'new_attribute_prompt'          => __( 'Enter a name for the new attribute term:', 'woocommerce' ),
				'calc_totals'                   => __( 'Calculate totals based on order items, discounts, and shipping?', 'woocommerce' ),
				'calc_line_taxes'               => __( 'Calculate line taxes? This will calculate taxes based on the customers country. If no billing/shipping is set it will use the store base country.', 'woocommerce' ),
				'copy_billing'                  => __( 'Copy billing information to shipping information? This will remove any currently entered shipping information.', 'woocommerce' ),
				'load_billing'                  => __( 'Load the customer\'s billing information? This will remove any currently entered billing information.', 'woocommerce' ),
				'load_shipping'                 => __( 'Load the customer\'s shipping information? This will remove any currently entered shipping information.', 'woocommerce' ),
				'featured_label'                => __( 'Featured', 'woocommerce' ),
				'prices_include_tax'            => esc_attr( get_option( 'woocommerce_prices_include_tax' ) ),
				'round_at_subtotal'             => esc_attr( get_option( 'woocommerce_tax_round_at_subtotal' ) ),
				'no_customer_selected'          => __( 'No customer selected', 'woocommerce' ),
				'plugin_url'                    => WC()->plugin_url(),
				'ajax_url'                      => admin_url( 'admin-ajax.php' ),
				'order_item_nonce'              => wp_create_nonce( 'order-item' ),
				'add_attribute_nonce'           => wp_create_nonce( 'add-attribute' ),
				'save_attributes_nonce'         => wp_create_nonce( 'save-attributes' ),
				'calc_totals_nonce'             => wp_create_nonce( 'calc-totals' ),
				'get_customer_details_nonce'    => wp_create_nonce( 'get-customer-details' ),
				'search_products_nonce'         => wp_create_nonce( 'search-products' ),
				'grant_access_nonce'            => wp_create_nonce( 'grant-access' ),
				'revoke_access_nonce'           => wp_create_nonce( 'revoke-access' ),
				'add_order_note_nonce'          => wp_create_nonce( 'add-order-note' ),
				'delete_order_note_nonce'       => wp_create_nonce( 'delete-order-note' ),
				'calendar_image'                => WC()->plugin_url() . '/assets/images/calendar.png',
				'post_id'                       => '',
				'base_country'                  => WC()->countries->get_base_country(),
				'currency_format_num_decimals'  => wc_get_price_decimals(),
				'currency_format_symbol'        => get_woocommerce_currency_symbol(),
				'currency_format_decimal_sep'   => esc_attr( wc_get_price_decimal_separator() ),
				'currency_format_thousand_sep'  => esc_attr( wc_get_price_thousand_separator() ),
				'currency_format'               => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
				'rounding_precision'            => WC_ROUNDING_PRECISION,
				'tax_rounding_mode'             => WC_TAX_ROUNDING_MODE,
				'product_types'                 => array_map( 'sanitize_title', get_terms( 'product_type', array( 'hide_empty' => false, 'fields' => 'names' ) ) ),
				'i18n_download_permission_fail' => __( 'Could not grant access - the user may already have permission for this file or billing email is not set. Ensure the billing email is set, and the order has been saved.', 'woocommerce' ),
				'i18n_permission_revoke'        => __( 'Are you sure you want to revoke access to this download?', 'woocommerce' ),
				'i18n_tax_rate_already_exists'  => __( 'You cannot add the same tax rate twice!', 'woocommerce' ),
				'i18n_product_type_alert'       => __( 'Your product has variations! Before changing the product type, it is a good idea to delete the variations to avoid errors in the stock reports.', 'woocommerce' ),
			);

			if ( ! wp_script_is( 'wc-admin-coupon-meta-boxes' ) ) {
				wp_enqueue_script( 'wc-admin-coupon-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-coupon' . $suffix . '.js', array( 'woocommerce_admin', 'wc-enhanced-select', 'wc-admin-meta-boxes' ), WC()->version );
				wp_localize_script( 'wc-admin-meta-boxes', 'woocommerce_admin_meta_boxes', $woocommerce_admin_meta_boxes_params );
				wp_enqueue_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), WC()->version );
				wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $woocommerce_admin_params );
			}

		}

		/**
		 * Function to show import message
		 */
		public function woocommerce_show_import_message() {
			global $pagenow,$typenow;

			if ( ! isset( $_GET['show_import_message'] ) ) { return;
			}

			if ( isset( $_GET['show_import_message'] ) && $_GET['show_import_message'] == true ) {
				if ( 'edit.php' == $pagenow && 'shop_coupon' == $typenow ) {

					$imported = ( ! empty( $_GET['imported'] )) ? $_GET['imported'] : 0;
					$skipped = ( ! empty( $_GET['skipped'] )) ? $_GET['skipped'] : 0;

					echo '<div id="message" class="updated fade"><p>
							' . sprintf( __( 'Import complete - imported <strong>%1$s</strong>, skipped <strong>%2$s</strong>', WC_SC_TEXT_DOMAIN ), $imported, $skipped ) . '
					</p></div>';
				}
			}
		}

		/**
		 * Function to include script in admin footer
		 */
		public function smart_coupons_script_in_footer() {

			global $pagenow, $wp_scripts;
			if ( empty( $pagenow ) || $pagenow != 'admin.php' ) { return;
			}
			if ( empty( $_GET['page'] ) || $_GET['page'] != 'woocommerce_smart_coupon_csv_import' ) { return;
			}

			?>
			<script type="text/javascript">
				jQuery(function(){
					jQuery(document).on('ready', function(){
						var element = jQuery('li#toplevel_page_woocommerce ul li').find('a[href="edit.php?post_type=shop_coupon"]');
						element.addClass('current');
						element.parent().addClass('current');
					});
				});
			</script>
			<?php

		}

		/**
		 * funtion to register the coupon importer
		 */
		public function woocommerce_coupon_admin_init() {

			if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
				register_importer( 'woocommerce_smart_coupon_csv', __( 'WooCommerce Coupons', WC_SC_TEXT_DOMAIN ), __( 'Import <strong>coupons</strong> to your store using CSV file.', WC_SC_TEXT_DOMAIN ), array( $this, 'coupon_importer' ) );
			}

			if ( ! empty( $_GET['action'] ) && ( $_GET['action'] == 'sent_gift_certificate' ) && ! empty( $_GET['page'] ) && ( $_GET['page'] == 'woocommerce_smart_coupon_csv_import' ) ) {
				$email = $_POST['smart_coupon_email'];
				$amount = $_POST['smart_coupon_amount'];
				$message = stripslashes( $_POST['smart_coupon_message'] );
				$this->send_gift_certificate( $email, $amount, $message );
			}
		}

		/**
		 * Function to process & send gift certificate
		 *
		 * @param string $email comma separated email address
		 * @param float  $amount coupon amount
		 * @param string $message optional
		 */
		public function send_gift_certificate( $email, $amount, $message = '' ) {

			$emails = explode( ',', $email );

			foreach ( $emails as $email ) {

				$email = trim( $email );

				if ( count( $emails ) == 1 && ( ! $email || ! is_email( $email ) ) ) {

					$location = admin_url( 'admin.php?page=woocommerce_smart_coupon_csv_import&tab=send_certificate&email_error=yes' );

				} elseif ( count( $emails ) == 1 && ( ! $amount || ! is_numeric( $amount ) ) ) {

					$location = admin_url( 'admin.php?page=woocommerce_smart_coupon_csv_import&tab=send_certificate&amount_error=yes' );

				} elseif ( is_email( $email ) && is_numeric( $amount ) ) {

					$coupon_title = $this->generate_smart_coupon( $email, $amount, null, null, 'smart_coupon', null, $message );

					$location = admin_url( 'admin.php?page=woocommerce_smart_coupon_csv_import&tab=send_certificate&sent=yes' );

				}
			}

			wp_safe_redirect( $location );
		}

		/**
		 * Funtion to perform importing of coupon from csv file
		 */
		public function coupon_importer() {

			if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) { return;
			}

			// Load Importer API
			require_once ABSPATH . 'wp-admin/includes/import.php';

			if ( ! class_exists( 'WP_Importer' ) ) {

				$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

				if ( file_exists( $class_wp_importer ) ) {
					require $class_wp_importer;
				}
			}

			// includes
			require 'class-wc-csv-coupon-import.php' ;
			require 'class-wc-coupon-parser.php' ;

			$wc_csv_coupon_import = new WC_CSV_Coupon_Import();

			$wc_csv_coupon_import->dispatch();

		}

		/**
		 * Function to add submenu page for Coupon CSV Import
		 */
		public function woocommerce_coupon_admin_menu() {
			add_submenu_page( 'woocommerce', __( 'Smart Coupon', WC_SC_TEXT_DOMAIN ), __( 'Smart Coupon', WC_SC_TEXT_DOMAIN ), 'manage_woocommerce', 'woocommerce_smart_coupon_csv_import', array( $this, 'admin_page' ) );
		}

		/**
		 * Function to remove submenu link for Smart Coupons
		 */
		public function woocommerce_coupon_admin_head() {
			remove_submenu_page( 'woocommerce', 'woocommerce_smart_coupon_csv_import' );
		}

		/**
		 * funtion to show content on the Coupon CSV Importer page
		 */
		public function admin_page() {

			$tab = ( ! empty( $_GET['tab'] ) ? ( $_GET['tab'] == 'send_certificate' ? 'send_certificate': 'import' ) : 'generate_bulk_coupons' );

			?>

			<div class="wrap woocommerce">
				<h2>
					<?php echo __( 'Coupons', WC_SC_TEXT_DOMAIN ); ?>
					<a href="<?php echo trailingslashit( admin_url() ) . 'post-new.php?post_type=shop_coupon'; ?>" class="add-new-h2"><?php echo __( 'Add Coupon', WC_SC_TEXT_DOMAIN ); ?></a>
				</h2>
				<div id="smart_coupons_tabs">
					<h2 class="nav-tab-wrapper">
						<a href="<?php echo admin_url( 'edit.php?post_type=shop_coupon' ) ?>" class="nav-tab"><?php _e( 'Coupons', WC_SC_TEXT_DOMAIN ); ?></a>
						<a href="<?php echo admin_url( 'admin.php?page=woocommerce_smart_coupon_csv_import' ) ?>" class="nav-tab <?php echo ($tab == 'generate_bulk_coupons') ? 'nav-tab-active' : ''; ?>"><?php _e( 'Bulk Generate / Import Coupons', WC_SC_TEXT_DOMAIN ); ?></a>
						<a href="<?php echo admin_url( 'admin.php?page=woocommerce_smart_coupon_csv_import&tab=send_certificate' ) ?>" class="nav-tab <?php echo ($tab == 'send_certificate') ? 'nav-tab-active' : ''; ?>"><?php _e( 'Send Store Credit', WC_SC_TEXT_DOMAIN ); ?></a>
					</h2>
				</div>
				<?php
				if ( ! function_exists( 'mb_detect_encoding' ) && $_GET['tab'] != 'send_certificate' ) {
					echo '<div class="message error"><p>' . sprintf( __( '%1$s Please install and enable PHP extension %2$s', WC_SC_TEXT_DOMAIN ), '<strong>' . __( 'Required', WC_SC_TEXT_DOMAIN ) . ':</strong> ', '<code>mbstring</code>' ) . '<a href="http://www.php.net/manual/en/mbstring.installation.php" target="_blank">' . __( 'Click here', WC_SC_TEXT_DOMAIN ) . '</a> ' . __( 'for more details.', WC_SC_TEXT_DOMAIN ) . '</p></div>';
				}

				switch ( $tab ) {
					case 'send_certificate' :
						$this->admin_send_certificate();
						break;
					case 'import' :
						$this->admin_import_page();
						break;
					default :
						$this->admin_generate_bulk_coupons_and_export();
						break;
				}
				?>

			</div>
			<?php

		}

		/**
		 * Coupon Import page content
		 */
		public function admin_import_page() {
			?>
			<div class="tool-box">
				<h3 class="title"><?php _e( 'Bulk Upload / Import Coupons using CSV file', WC_SC_TEXT_DOMAIN ); ?></h3>
				<p class="description"><?php _e( 'Upload a CSV file & click \'Import\' . Importing requires <code>post_title</code> column.', WC_SC_TEXT_DOMAIN ); ?></p>
				<p class="submit"><a class="button" href="<?php echo admin_url( 'admin.php?import=woocommerce_smart_coupon_csv' ); ?>"><?php _e( 'Import Coupons', WC_SC_TEXT_DOMAIN ); ?></a> </p>
			</div>
			<?php
		}

		/**
		 * Send Gift Certificate page content
		 */
		public function admin_send_certificate() {

			if ( ! empty( $_GET['sent'] ) && $_GET['sent'] == 'yes' ) {
				echo '<div id="message" class="updated fade"><p><strong>' . __( 'Store Credit / Gift Card sent successfully.', WC_SC_TEXT_DOMAIN ) . '</strong></p></div>';
			}

			?>
			<div class="tool-box">

				<h3 class="title"><?php _e( 'Send Store Credit / Gift Card', WC_SC_TEXT_DOMAIN ); ?></h3>
				<p class="description"><?php _e( 'Click "Send" to send Store Credit / Gift Card. *All field are compulsary.', WC_SC_TEXT_DOMAIN ); ?></p>

				<form action="<?php echo admin_url( 'admin.php?page=woocommerce_smart_coupon_csv_import&action=sent_gift_certificate' ); ?>" method="post">

					<table class="form-table">
						<tr>
							<th>
								<label for="smart_coupon_email"><?php _e( 'Email ID', WC_SC_TEXT_DOMAIN ); ?> *</label>
							</th>
							<td>
								<input type="text" name="smart_coupon_email" id="email" class="input-text" style="width: 100%;" />
							</td>
							<td>
								<?php
								if ( ! empty( $_GET['email_error'] ) && $_GET['email_error'] == 'yes' ) {
									echo '<div id="message" class="error fade"><p><strong>' . __( 'Invalid email address.', WC_SC_TEXT_DOMAIN ) . '</strong></p></div>';
								}
								?>
								<span class="description"><?php _e( 'Use comma "," as separator for multiple e-mail ids', WC_SC_TEXT_DOMAIN ); ?></span>
							</td>
						</tr>

						<tr>
							<th>
								<label for="smart_coupon_amount"><?php _e( 'Coupon Amount', WC_SC_TEXT_DOMAIN ); ?> *</label>
							</th>
							<td>
								<input type="number" min="0.01" step="any" name="smart_coupon_amount" id="amount" placeholder="<?php _e( '0.00', WC_SC_TEXT_DOMAIN ); ?>" class="input-text" style="width: 30%;" />
							</td>
							<td>
								<?php
								if ( ! empty( $_GET['amount_error'] ) && $_GET['amount_error'] == 'yes' ) {
									echo '<div id="message" class="error fade"><p><strong>' . __( 'Invalid amount.', WC_SC_TEXT_DOMAIN ) . '</strong></p></div>';
								}
								?>
							</td>
						</tr>

						<?php
							$message = '';
							$editor_args = array(
								'textarea_name' => 'smart_coupon_message',
								'textarea_rows' => 10,
								'editor_class' => 'wp-editor-message',
								'media_buttons' => true,
								'tinymce' => false,
							);
						?>

						<tr>
							<th>
								<label for="smart_coupon_message"><?php _e( 'Message', WC_SC_TEXT_DOMAIN ); ?></label>
							</th>
							<td colspan="2">
								<?php wp_editor( $message, 'edit_smart_coupon_message', $editor_args ); ?>
							</td>
						</tr>

					</table>

					<?php submit_button( __( 'Send', WC_SC_TEXT_DOMAIN ) ); ?>

				</form>
			</div>
			<?php
		}

		/**
		 * Form to show 'Auto generate Bulk Coupons' with other fields
		 */
		public function admin_generate_bulk_coupons_and_export() {

			global $woocommerce, $woocommerce_smart_coupon, $post;

			$empty_reference_coupon = get_option( 'empty_reference_smart_coupons' );

			if ( $empty_reference_coupon === false ) {
				$args = array(
							'post_status' => 'auto-draft',
							'post_type' => 'shop_coupon',
						);
				$reference_post_id = wp_insert_post( $args );
				update_option( 'empty_reference_smart_coupons', $reference_post_id );
			} else {
				$reference_post_id = $empty_reference_coupon;
			}

			$post = get_post( $reference_post_id );

			if ( empty( $post ) ) {
				$args = array(
							'post_status' => 'auto-draft',
							'post_type' => 'shop_coupon',
						);
				$reference_post_id = wp_insert_post( $args );
				update_option( 'empty_reference_smart_coupons', $reference_post_id );
				$post = get_post( $reference_post_id );
			}

			if ( ! class_exists( 'WC_Meta_Box_Coupon_Data' ) ) {
				require_once WC()->plugin_path() . '/includes/admin/meta-boxes/class-wc-meta-box-coupon-data.php';
			}

			$upload_url     = wp_upload_dir();
			$upload_path    = $upload_url['path'];
			$assets_path    = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

			if ( isset( $_POST['generate_and_import'] ) && ! empty( $_POST['smart_coupons_generate_action'] ) && $_POST['smart_coupons_generate_action'] == 'sc_export_and_import' ) {

				$this->export_coupon( $_POST, '', '' );
			}
			?>

			<script type="text/javascript">
				jQuery(function(){

					jQuery('input#generate_and_import').on('click', function(){

						if( jQuery('input#no_of_coupons_to_generate').val() == "" ){
							jQuery("div#message").removeClass("updated fade").addClass("error fade");
							jQuery('div#message p').html( "<?php _e( 'Please enter a valid value for Number of Coupons to Generate', WC_SC_TEXT_DOMAIN ); ?>");
							return false;
						} else {
							jQuery("div#message").removeClass("error fade").addClass("updated fade").hide();
							return true;
						}
					});

				});
			</script>

			<div id="message"><p></p></div>
			<div class="tool-box">

				<h3 class="title"><?php _e( 'Generate Coupons', WC_SC_TEXT_DOMAIN ); ?> | <small><a href="<?php echo trailingslashit( admin_url() ) . 'admin.php?import=woocommerce_smart_coupon_csv' ?>"><?php echo __( 'Import Coupons', WC_SC_TEXT_DOMAIN ); ?></a></small></h3>
				<p class="description"><?php _e( 'You can bulk generate coupons using options below.', WC_SC_TEXT_DOMAIN ); ?></p>

				<style type="text/css">
					.coupon_actions {
						margin-left: 14px;
					}
					#smart-coupon-action-panel p label {
						width: 18em;
					}
					#smart-coupon-action-panel {
						width: 100% !important;
					}
				</style>

				<form id="generate_coupons" action="<?php echo admin_url( 'admin.php?import=woocommerce_smart_coupon_csv&step=2' ); ?>" method="post">
					<?php wp_nonce_field( 'import-woocommerce-coupon' ); ?>
					<div id="poststuff">
						<div id="woocommerce-coupon-data" class="postbox " >
							<h3><span class="coupon_actions"><?php echo __( 'Action', WC_SC_TEXT_DOMAIN ); ?></span></h3>
							<div class="inside">
								<div class="panel-wrap">
									<div id="smart-coupon-action-panel" class="panel woocommerce_options_panel">

										<p class="form-field">
											<label><?php echo __( 'Generate coupons &', WC_SC_TEXT_DOMAIN ); ?></label>
											<input type="radio" name="smart_coupons_generate_action" value="add_to_store" id="add_to_store" checked="checked"/>&nbsp;
											<strong><?php echo __( 'Add to store', WC_SC_TEXT_DOMAIN ); ?></strong>
											<span class="description"><?php _e( 'Adds generated coupons to store.', WC_SC_TEXT_DOMAIN ); ?></span>
										</p>

										<p class="form-field">
											<label><?php echo '&nbsp;'; ?></label>
											<input type="radio" name="smart_coupons_generate_action" value="sc_export_and_import" id="sc_export_and_import" />&nbsp;
											<strong><?php echo __( 'Export to CSV', WC_SC_TEXT_DOMAIN ); ?></strong>
											<span class="description"><?php _e( 'Downloads a .CSV file, which can be used for importing', WC_SC_TEXT_DOMAIN ); ?></span>
										</p>

										<p class="form-field">
											<label><?php echo '&nbsp;'; ?></label>
											<input type="radio" name="smart_coupons_generate_action" value="woo_sc_is_email_imported_coupons" id="woo_sc_is_email_imported_coupons" />&nbsp;
											<strong><?php echo __( 'Email to customer', WC_SC_TEXT_DOMAIN ); ?></strong>
											<span class="description"><?php _e( 'Send generated coupon codes to respective customer via email as well', WC_SC_TEXT_DOMAIN ); ?></span>
										</p>

										<p class="form-field">
											<label for="no_of_coupons_to_generate"><?php _e( 'Number of Coupons to Generate ', WC_SC_TEXT_DOMAIN ); ?> *</label>
											<input type="number" name="no_of_coupons_to_generate" id="no_of_coupons_to_generate" placeholder="<?php _e( '10', WC_SC_TEXT_DOMAIN ); ?>" class="short" min="1" />
										</p>

									</div>
								</div>
							</div>
						</div>
						<div id="woocommerce-coupon-data" class="postbox " >
							<h3><span class="coupon_actions"><?php echo __( 'Coupon Data', WC_SC_TEXT_DOMAIN ); ?></span></h3>
							<div class="inside">
								<?php WC_Meta_Box_Coupon_Data::output( $post ); ?>
							</div>
						</div>
					</div>

					<p class="submit"><input id="generate_and_import" name="generate_and_import" type="submit" class="button button-primary" value="<?php _e( 'Apply', WC_SC_TEXT_DOMAIN ); ?>" /></p>

				</form>
			</div>
			<?php

		}

		

	}

}

WC_SC_Admin_Pages::get_instance();
