<?php
/**
 * Smart Coupons Shortcode
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_Shortcode' ) ) {

	/**
	 * Class for handling Smart Coupons Shortcode
	 */
	class WC_SC_Shortcode {

		/**
		 * Variable to hold instance of WC_SC_Shortcode
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'smart_coupon_shortcode_button_init' ) );	// Use 'admin_enqueue_scripts' instead of 'init' // Credit: Jonathan Desrosiers <jdesrosiers@linchpinagency.com>
			add_action( 'init', array( $this, 'register_smart_coupon_shortcode' ) );
			add_action( 'after_wp_tiny_mce', array( $this, 'smart_coupons_after_wp_tiny_mce' ) );

		}

		/**
		 * Get single instance of WC_SC_Shortcode
		 *
		 * @return WC_SC_Shortcode Singleton object of WC_SC_Shortcode
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
		 * Add Smart Coupons shortcode button in WP editor
		 */
		public function smart_coupon_shortcode_button_init() {

			if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) && get_user_option( 'rich_editing' ) == 'true' ) {
				return;
			}

			if ( ! wp_script_is( 'wpdialogs' ) ) {
				wp_enqueue_script( 'wpdialogs' );
			}

			if ( ! wp_style_is( 'wp-jquery-ui-dialog' ) ) {
				wp_enqueue_style( 'wp-jquery-ui-dialog' );
			}

			add_filter( 'mce_external_plugins', array( $this, 'smart_coupon_register_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'smart_coupon_add_tinymce_button' ) );

		}

		/**
		 * Add Smart Coupon short code button in TinyMCE
		 *
		 * @param array $plugin_array existing plugin
		 * @return array $plugin array with SMart Coupon shortcode
		 */
		public function smart_coupon_register_tinymce_plugin( $plugin_array ) {
			$plugin_array['sc_shortcode_button'] = plugins_url( 'assets/js/sc_shortcode.js', WC_SC_PLUGIN_FILE );
			return $plugin_array;
		}

		/**
		 * Add Smart coupon shortcode button in TinyMCE
		 *
		 * @param array $button existing button
		 * @return array $button whith smart coupons shortcode button
		 */
		public function smart_coupon_add_tinymce_button( $buttons ) {
			$buttons[] = 'sc_shortcode_button';
			return $buttons;
		}

		/**
		 * Register shortcode for Smart Coupons
		 */
		public function register_smart_coupon_shortcode() {
			add_shortcode( 'smart_coupons', array( $this, 'execute_smart_coupons_shortcode' ) );
		}

		/**
		 * Execute Smart Coupons shortcode
		 *
		 * @param array $atts
		 * @return HTML code for coupon to be displayed
		 */
		public function execute_smart_coupons_shortcode( $atts ) {
			ob_start();
			global $wpdb;

			$current_user   = wp_get_current_user();
			$customer_id    = $current_user->ID;

			extract( shortcode_atts( array(
				'coupon_code'                   => '',
				'discount_type'                 => 'smart_coupon',
				'coupon_amount'                 => '',
				'individual_use'                => 'no',
				'product_ids'                   => '',
				'exclude_product_ids'           => '',
				'usage_limit'                   => '',
				'usage_limit_per_user'          => '',
				'limit_usage_to_x_items'        => '',
				'expiry_date'                   => '',
				'apply_before_tax'              => 'no',
				'free_shipping'                 => 'no',
				'product_categories'            => '',
				'exclude_product_categories'    => '',
				'minimum_amount'                => '',
				'maximum_amount'                => '',
				'exclude_sale_items'            => 'no',
				'auto_generate'                 => 'no',
				'coupon_prefix'                 => '',
				'coupon_suffix'                 => '',
				'customer_email'                => '',
				'coupon_style'                  => '',
				'disable_email'                 => 'no',
			), $atts ) );

			$_coupon_code   = $coupon_code;
			$_discount_type = $discount_type;
			$_coupon_amount = $coupon_amount;
			$_expiry_date   = $expiry_date;
			$_free_shipping = $free_shipping;

			if ( empty( $_coupon_code ) && empty( $_coupon_amount ) ) {
				return;		// Minimum requirement for shortcode is either $_coupon_code or $_coupon_amount
			}

			if ( empty( $customer_email ) ) {

				if ( ! ($current_user instanceof WP_User) ) {
					$current_user   = wp_get_current_user();
					$customer_email = ( isset( $current_user->user_email ) ) ? $current_user->user_email : '';
				} else {
					$customer_email = ( ! empty( $current_user->data->user_email ) ) ? $current_user->data->user_email : '';
				}
			}

		   	if ( ! empty( $_coupon_code ) && ! empty( $customer_email ) ) {
				$coupon_exists = $wpdb->get_var("SELECT ID
													FROM {$wpdb->prefix}posts AS posts
														LEFT JOIN {$wpdb->prefix}postmeta AS postmeta
														ON ( postmeta.post_id = posts.ID )
													WHERE posts.post_title = '" . strtolower( $_coupon_code ) . "'
													AND posts.post_type = 'shop_coupon'
													AND posts.post_status = 'publish'
													AND postmeta.meta_key = 'customer_email'
													AND postmeta.meta_value LIKE '%{$customer_email}%'");
			} else {
				$coupon_exists = null;
			}

			$_expiry_date = '';

			$all_discount_types = wc_get_coupon_types();

			if ( $coupon_exists == null ) {

				if ( ! empty( $_coupon_code ) ) {
					$coupon = new WC_Coupon( $_coupon_code );

					if ( $this->is_wc_gte_30() ) {
						$coupon_id        = $coupon->get_id();
						$coupon_amount    = $coupon->get_amount();
						$is_free_shipping = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
						$discount_type    = $coupon->get_discount_type();
						$expiry_date      = $coupon->get_date_expires();
						$coupon_code      = $coupon->get_code();
					} else {
						$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
						$coupon_amount    = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
						$discount_type    = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						$expiry_date      = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : '';
						$coupon_code      = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
					}

					if ( ! empty( $discount_type ) ) {

						$is_auto_generate = get_post_meta( $coupon_id, 'auto_generate_coupon', true );
						$is_disable_email_restriction = get_post_meta( $coupon_id, 'sc_disable_email_restriction', true );

						if ( ( empty( $is_disable_email_restriction ) || $is_disable_email_restriction == 'no' ) && ( empty( $is_auto_generate ) || $is_auto_generate == 'no' ) ) {
							$existing_customer_emails = get_post_meta( $coupon_id, 'customer_email', true );
							$existing_customer_emails[] = $customer_email;
							update_post_meta( $coupon_id, 'customer_email', $existing_customer_emails );
						}

						if ( ! empty( $is_auto_generate ) && $is_auto_generate == 'yes' ) {

							if ( $current_user->ID == 0 ) {
								if ( $discount_type == 'smart_coupon' ) {
									return;		// Don't generate & don't show coupon if coupon of the shortcode is store credit & user is guest, otherwise it'll lead to unlimited generation of coupon
								} else {
									$new_generated_coupon_code = $coupon_code;
								}
							} else {

								$shortcode_generated_coupon = $this->get_shortcode_generated_coupon( $current_user, $coupon );

								if ( empty( $shortcode_generated_coupon ) ) {
									$generated_coupon_details = apply_filters( 'generate_smart_coupon_action', $customer_email, $coupon_amount, '', $coupon );
									$last_element = end( $generated_coupon_details[ $customer_email ] );
									$new_generated_coupon_code = $last_element['code'];
									$this->save_shortcode_generated_coupon( $new_generated_coupon_code, $current_user, $coupon );
								} else {
									$new_generated_coupon_code = $shortcode_generated_coupon;
								}
							}
						} else {

							$new_generated_coupon_code = $_coupon_code;

						}
					}
				}

				if ( ( ! empty( $_coupon_code ) && empty( $discount_type ) ) || ( empty( $_coupon_code ) ) ) {

					if ( empty( $current_user->ID ) && ( $_discount_type == 'smart_coupon' || $discount_type == 'smart_coupon' ) ) {
						return;		// It'll prevent generation of unlimited coupons for guest
					}

					if ( empty( $coupon ) ) {
						$coupon = null;
					}

					$shortcode_generated_coupon = $this->get_shortcode_generated_coupon( $current_user, $coupon );

					if ( empty( $shortcode_generated_coupon ) ) {

						if ( empty( $_coupon_code ) ) {
							$_coupon_code = $this->generate_unique_code( $customer_email );
							$_coupon_code = $coupon_prefix . $_coupon_code . $coupon_suffix;
						}

						$coupon_args = array(
							'post_title'    => strtolower( $_coupon_code ),
							'post_content'  => '',
							'post_status'   => 'publish',
							'post_author'   => 1,
							'post_type'     => 'shop_coupon',
						);

						$new_coupon_id = wp_insert_post( $coupon_args );
						if ( ! empty( $expiry_days ) ) {
							$_expiry_date = date( 'Y-m-d', strtotime( "+$expiry_days days" ) );
						}

						// Add meta for coupons
						update_post_meta( $new_coupon_id, 'discount_type', $_discount_type );
						update_post_meta( $new_coupon_id, 'coupon_amount', $_coupon_amount );
						update_post_meta( $new_coupon_id, 'individual_use', $individual_use );
						update_post_meta( $new_coupon_id, 'minimum_amount', $minimum_amount );
						update_post_meta( $new_coupon_id, 'maximum_amount', $maximum_amount );
						update_post_meta( $new_coupon_id, 'product_ids', array() );
						update_post_meta( $new_coupon_id, 'exclude_product_ids', array() );
						update_post_meta( $new_coupon_id, 'usage_limit', $usage_limit );
						update_post_meta( $new_coupon_id, 'expiry_date', $_expiry_date );
						update_post_meta( $new_coupon_id, 'customer_email', array( $customer_email ) );
						update_post_meta( $new_coupon_id, 'apply_before_tax', $apply_before_tax );
						update_post_meta( $new_coupon_id, 'free_shipping', $_free_shipping );
						update_post_meta( $new_coupon_id, 'product_categories', array() );
						update_post_meta( $new_coupon_id, 'exclude_product_categories', array() );
						update_post_meta( $new_coupon_id, 'sc_disable_email_restriction', $disable_email );

						$new_generated_coupon_code = $_coupon_code;
						$this->save_shortcode_generated_coupon( $new_generated_coupon_code, $current_user, $coupon );

					} else {

						$new_generated_coupon_code = $shortcode_generated_coupon;

					}
				}
			} else {

				$new_generated_coupon_code = $_coupon_code;

			}

			$new_coupon_generated = false;
			if ( ! empty( $new_generated_coupon_code ) ) {
				$coupon = new WC_Coupon( $new_generated_coupon_code );
				$new_coupon_generated = true;
			}

			if ( $new_coupon_generated ) {
				if ( $this->is_wc_gte_30() ) {
					$coupon_id        = $coupon->get_id();
					$coupon_amount    = $coupon->get_amount();
					$is_free_shipping = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
					$discount_type    = $coupon->get_discount_type();
					$expiry_date      = $coupon->get_date_expires();
					$coupon_code      = $coupon->get_code();
				} else {
					$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
					$coupon_amount    = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
					$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
					$discount_type    = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					$expiry_date      = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : '';
					$coupon_code      = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
				}
			}

			$coupon_post = get_post( $coupon_id );

			switch ( $discount_type ) {
				case 'smart_coupon':
					$coupon_type = __( 'Store Credit', WC_SC_TEXT_DOMAIN );
					$_coupon_amount = wc_price( $coupon_amount );
					break;

				case 'fixed_cart':
					$coupon_type = __( 'Cart Discount', WC_SC_TEXT_DOMAIN );
					$_coupon_amount = wc_price( $coupon_amount );
					break;

				case 'fixed_product':
					$coupon_type = __( 'Product Discount', WC_SC_TEXT_DOMAIN );
					$_coupon_amount = wc_price( $coupon_amount );
					break;

				case 'percent_product':
					$coupon_type = __( 'Product Discount', WC_SC_TEXT_DOMAIN );
					$_coupon_amount = $coupon_amount . '%';
					break;

				case 'percent':
					$coupon_type = ( $this->is_wc_gte_30() ) ? __( 'Percentage Discount', WC_SC_TEXT_DOMAIN ) : __( 'Cart Discount', WC_SC_TEXT_DOMAIN );
					$_coupon_amount = $coupon_amount . '%';
					break;

				default:
					$default_coupon_type = ( ! empty( $all_discount_types[ $discount_type ] ) ) ? $all_discount_types[ $discount_type ] : ucwords( str_replace( array( '_', '-' ), ' ', $discount_type ) );
					$coupon_type = apply_filters( 'wc_sc_coupon_type', $default_coupon_type, $coupon, $all_discount_types );
					$_coupon_amount = apply_filters( 'wc_sc_coupon_amount', $coupon_amount, $coupon );
					break;

			}
			if ( ! empty( $_coupon_amount ) || $_coupon_amount != 0 ) {
				$discount_text = $_coupon_amount . ' ' . $coupon_type;
				if ( $is_free_shipping == 'yes' ) {
					$discount_text .= __( ' &amp; ', WC_SC_TEXT_DOMAIN );
				}
			} else {
				$discount_text = '';
			}
			if ( $is_free_shipping == 'yes' ) {
				$discount_text .= __( 'Free Shipping', WC_SC_TEXT_DOMAIN );
			}
			$discount_text = wp_strip_all_tags( $discount_text );

			echo '<div class="coupon-container ' . $atts['coupon_style'] . '" style="cursor:inherit">
						<div class="coupon-content ' . $atts['coupon_style'] . '">
							<div class="discount-info">';
			if ( ! empty( $discount_text ) ) {
				echo $discount_text;
			}
			echo '</div>';

			echo '<div class="code">' . $new_generated_coupon_code . '</div>';

			$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );
			if ( ! empty( $coupon_post->post_excerpt ) && $show_coupon_description == 'yes' ) {
				echo '<div class="discount-description">' . $coupon_post->post_excerpt . '</div>';
			}

			$_expiry_date = get_post_meta( $coupon_id, 'expiry_date', true );

			if ( ! empty( $_expiry_date ) ) {
				$_expiry_date_text = $this->get_expiration_format( strtotime( $_expiry_date ) );
				echo ' <div class="coupon-expire">' . $_expiry_date_text . '</div>';
			} else {
				echo ' <div class="coupon-expire">' . __( 'Never Expires ', WC_SC_TEXT_DOMAIN ) . '</div>';
			}

			echo '</div>
				</div>';

			return ob_get_clean();
		}

		/**
		 * Function to check whether to generate a new coupon through shortcode for current user
		 * Don't create if it is already generated.
		 *
		 * @param WP_User   $current_user
		 * @param WC_Coupon $coupon
		 * @return string $code
		 */
		public function get_shortcode_generated_coupon( $current_user = null, $coupon = null ) {

			$max_in_a_session = get_option( '_sc_max_coupon_generate_in_a_session', 1 );
			$max_per_coupon_per_user = get_option( '_sc_max_coupon_per_coupon_per_user', 1 );

			if ( $this->is_wc_gte_30() ) {
				$coupon_code = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
			} else {
				$coupon_code = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
			}

			$code = ( ! empty( $coupon_code ) ) ? $coupon_code : 0;

			if ( ! empty( $current_user->ID ) ) {

				$generated_coupons = get_user_meta( $current_user->ID, '_sc_shortcode_generated_coupons', true );

				if ( ! empty( $generated_coupons[ $code ] ) && count( $generated_coupons[ $code ] ) >= $max_per_coupon_per_user ) {
					return end( $generated_coupons[ $code ] );
				}
			}

			$session_shortcode_coupons = WC()->session->get( '_sc_session_shortcode_generated_coupons' );

			if ( ! empty( $session_shortcode_coupons[ $code ] ) && count( $session_shortcode_coupons[ $code ] ) >= $max_in_a_session ) {
				return end( $session_shortcode_coupons[ $code ] );
			}

			return false;

		}

		/**
		 * Function to save shortcode generated coupon details
		 *
		 * @param string    $code
		 * @param WP_User   $current_user
		 * @param WC_Coupon $coupon
		 */
		public function save_shortcode_generated_coupon( $new_code, $current_user, $coupon ) {

			if ( $this->is_wc_gte_30() ) {
				$coupon_code = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
			} else {
				$coupon_code = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
			}

			$code = ( ! empty( $coupon_code ) ) ? $coupon_code : 0;

			$session_shortcode_coupons = WC()->session->get( '_sc_session_shortcode_generated_coupons' );

			if ( empty( $session_shortcode_coupons ) ) {
				$session_shortcode_coupons = array();
			}
			if ( empty( $session_shortcode_coupons[ $code ] ) ) {
				$session_shortcode_coupons[ $code ] = array();
			}
			if ( ! in_array( $new_code, $session_shortcode_coupons[ $code ] ) ) {
				$session_shortcode_coupons[ $code ][] = $new_code;
				WC()->session->set( '_sc_session_shortcode_generated_coupons', $session_shortcode_coupons );
			}

			if ( ! empty( $current_user->ID ) ) {
				$generated_coupons = get_user_meta( $current_user->ID, '_sc_shortcode_generated_coupons', true );
				if ( empty( $generated_coupons ) ) {
					$generated_coupons = array();
				}
				if ( empty( $generated_coupons[ $code ] ) ) {
					$generated_coupons[ $code ] = array();
				}
				if ( ! in_array( $new_code, $generated_coupons[ $code ] ) ) {
					$generated_coupons[ $code ][] = $new_code;
					update_user_meta( $current_user->ID, '_sc_shortcode_generated_coupons', $generated_coupons );
				}
			}

		}

		/**
		 * Smart coupon button after TinyMCE
		 */
		public function smart_coupons_after_wp_tiny_mce( $mce_settings ) {
			$this->sc_attributes_dialog();
		}

		/**
		 * Smart Coupons dialog content for shortcode
		 *
		 * @static
		 */
		public function sc_attributes_dialog() {

			wp_enqueue_style( 'coupon-style' );

			?>
			<div style="display:none;">
				<form id="sc_coupons_attributes" tabindex="-1" style="background-color: #F5F5F5;">
				<?php wp_nonce_field( 'internal_coupon_shortcode', '_ajax_coupon_shortcode_nonce', false ); ?>

				<script type="text/javascript">
					jQuery(function(){
						jQuery('input#search-coupon-field').on('keyup',function() {

							jQuery('div#search-results ul').empty();
							var searchString = jQuery(this).val().trim();

							if ( searchString.length == 0 ) {
								jQuery('#default-text').html('<?php _e( 'No search term specified.', 'wc_smart_coupons' ); ?>');
								return true;
							}
							if ( searchString.length == 1 ) {
								jQuery('#default-text').html('<?php _e( 'Enter more than one character to search.', 'wc_smart_coupons' ); ?>');
								return true;
							}

							jQuery.ajax({
								url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
								method: 'GET',
								afterTypeDelay: 100,
								data: {
									action        : 'smart_coupons_json_search',
									security      : '<?php echo wp_create_nonce( 'search-coupons' ); ?>',
									term          : searchString
								},
								dataType: 'json',
								success: function( response ) {
									if ( response ) {
										jQuery('#default-text').html('<?php _e( 'Click to select coupon code.', 'wc_smart_coupons' ); ?>');
									} else {
										jQuery('#default-text').html('<?php _e( 'No coupon code found.', 'wc_smart_coupons' ); ?>');
										return;
									}
									jQuery.each(response, function (i, val) {

										jQuery('div#search-results ul').append('<li class="'+i+'">'+ i +val.substr(val.indexOf('(')-1)+'</li>');
									});
								}
							});
						});

						jQuery('div#sc_shortcode_cancel a').on('click', function() {
							emptyAllFormElement();
							jQuery('.ui-dialog-titlebar-close').trigger('click');
						});

						function emptyAllFormElement() {
							jQuery('#search-coupon-field').val('');
							jQuery('#default-text').html('<?php _e( 'No search term specified.', 'wc_smart_coupons' ); ?>');
							jQuery('#search-results ul').empty();
						}

						jQuery('div#search-results ul li').live('click', function() {
							var couponCode = jQuery(this).attr('class');
							jQuery('input#search-coupon-field').val(couponCode);
						});

						jQuery('input#sc_shortcode_submit').on('click', function() {

							var couponShortcode = '[smart_coupons';
							var couponCode      = jQuery('#search-coupon-field').val();
							var coupon_border   = jQuery('select#coupon-border').find('option:selected').val();
							var coupon_color    = jQuery('select#coupon-color').find('option:selected').val();
							var coupon_size     = jQuery('select#coupon-size').find('option:selected').val();
							var coupon_style    = coupon_border+' '+coupon_color+' '+coupon_size;

							if ( couponCode != undefined && couponCode != '' ) {
								couponShortcode += ' coupon_code="'+couponCode.trim()+'"';
							}
							if ( coupon_style != undefined && coupon_style != '' ) {
								couponShortcode += ' coupon_style="'+coupon_style+'"';
							}

							couponShortcode += ']';
							tinyMCE.execCommand("mceInsertContent", false, couponShortcode);
							emptyAllFormElement();
							jQuery('.ui-dialog-titlebar-close').trigger('click');

						});

						//Shortcode Styles
						apply_preview_style();

						jQuery('select').on('change', function() {
							apply_preview_style();
						});

						function apply_preview_style() {
							var coupon_border   = jQuery('select#coupon-border').find('option:selected').val();
							var coupon_color    = jQuery('select#coupon-color').find('option:selected').val();
							var coupon_size     = jQuery('select#coupon-size').find('option:selected').val();

							jQuery('div.coupon-container').removeClass().addClass('coupon-container previews');
							jQuery('div.coupon-container').addClass(coupon_color+' '+coupon_size);

							jQuery('div.coupon-content').removeClass().addClass('coupon-content');
							jQuery('div.coupon-content').addClass(coupon_border+' '+coupon_size+' '+coupon_color);
						}

				});

				</script>

					<div id="coupon-selector">
						<div id="coupon-option">
							<div>
								<label><span><?php _e( 'Coupon code', WC_SC_TEXT_DOMAIN ); ?></span><input id="search-coupon-field" type="text" name="search_coupon_code" placeholder="<?php _e( 'Search coupon...', WC_SC_TEXT_DOMAIN )?>"/></label>
							</div>
							<div id="search-panel">
								<div id="search-results">
									<div id="default-text"><?php _e( 'No search term specified.', WC_SC_TEXT_DOMAIN ); ?></div>
									<ul></ul>
								</div>
							</div>
							<div>
								<div>
									<label><span><?php _e( 'Color', WC_SC_TEXT_DOMAIN ); ?></span>
										<select id="coupon-color" name="coupon-color">
											<option value="green" selected="selected"><?php _e( 'Light Green', WC_SC_TEXT_DOMAIN ) ?></option>
											<option value="blue"><?php _e( 'Light Blue', WC_SC_TEXT_DOMAIN ) ?></option>
											<option value="red"><?php _e( 'Light Red', WC_SC_TEXT_DOMAIN ) ?></option>
											<option value="yellow"><?php _e( 'Light Yellow', WC_SC_TEXT_DOMAIN ) ?></option>
										</select>
									</label>
								</div>
							   <div>
									<label><span><?php _e( 'Border', WC_SC_TEXT_DOMAIN ); ?></span>
										<select id="coupon-border" name="coupon-border">
											<option value="dashed" selected="selected">- - - - - - - - -</option>
											<option value="dotted">-----------------</option>
											<option value="solid">––––––––––</option>
											<option value="groove">––––––––––</option>
											<option value="none">         </option>
										</select>
									</label>
								</div>
								<div>
									<label><span><?php _e( 'Size', WC_SC_TEXT_DOMAIN ); ?></span>
										<select id="coupon-size" name="coupon-size">
											<option value="small"><?php _e( 'Small', WC_SC_TEXT_DOMAIN ) ?></option>
											<option value="medium" selected="selected"><?php _e( 'Medium', WC_SC_TEXT_DOMAIN ) ?></option>
											<option value="large"><?php _e( 'Large', WC_SC_TEXT_DOMAIN ) ?></option>
										</select>
									</label>
								</div>
							</div>

						</div>
					</div>
					<div class="coupon-preview">
						<div class="preview-heading">
							<?php _e( 'Preview', WC_SC_TEXT_DOMAIN ) ?>
						</div>
						<div class="coupon-container">
							<div class="coupon-content">
								<div class="discount-info"><?php _e( 'XX Discount type', WC_SC_TEXT_DOMAIN ) ?></div>
								<div class="code"><?php _e( 'coupon-code', WC_SC_TEXT_DOMAIN ) ?></div>
								<?php
								$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );
								if ( $show_coupon_description == 'yes' ) {
									echo '<div class="discount-description">' . __( 'Description', WC_SC_TEXT_DOMAIN ) . '</div>';
								}
								?>
								<div class="coupon-expire"><?php _e( 'Expires on xx date', WC_SC_TEXT_DOMAIN ) ?></div>

							</div>
						</div>
					</div>
					<div class="submitbox">
						<div id="sc_shortcode_update">
							<input type="button" value="<?php esc_attr_e( 'Insert Shortcode', WC_SC_TEXT_DOMAIN ); ?>" class="button-primary" id="sc_shortcode_submit" name="sc_shortcode_submit">
						</div>
						<div id="sc_shortcode_cancel">
							<a class="submitdelete deletion" href="#"><?php _e( 'Cancel', WC_SC_TEXT_DOMAIN ); ?></a>
						</div>
					</div>
				</form>
			</div>
			<?php
		}

	}

}

WC_SC_Shortcode::get_instance();
