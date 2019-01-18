<?php
/*
Plugin Name: WooCommerce Direct Checkout
Plugin URI: http://terrytsang.com/shop/shop/woocommerce-direct-checkout/
Description: Allow you to implement direct checkout (skip cart page) for WooCommerce
Version: 1.1.2
Author: Terry Tsang
Author URI: http://shop.terrytsang.com
*/

/*  Copyright 2012-2015 Terry Tsang (email: terrytsang811@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Define plugin name
define('wc_plugin_name_direct_checkout', 'WooCommerce Direct Checkout');

// Define plugin version
define('wc_version_direct_checkout', '1.1.2');



if(!class_exists('WooCommerce_Direct_Checkout')){
	class WooCommerce_Direct_Checkout{

		public static $plugin_prefix;
		public static $plugin_url;
		public static $plugin_path;
		public static $plugin_basefile;

		/**
		 * Gets things started by adding an action to initialize this plugin once
		 * WooCommerce is known to be active and initialized
		 */
		public function __construct(){
			load_plugin_textdomain('wc-direct-checkout', false, dirname(plugin_basename(__FILE__)) . '/languages/');
			
			WooCommerce_Direct_Checkout::$plugin_prefix = 'wc_direct_checkout_';
			WooCommerce_Direct_Checkout::$plugin_basefile = plugin_basename(__FILE__);
			WooCommerce_Direct_Checkout::$plugin_url = plugin_dir_url(WooCommerce_Direct_Checkout::$plugin_basefile);
			WooCommerce_Direct_Checkout::$plugin_path = trailingslashit(dirname(__FILE__));
			
			$this->textdomain = 'wc-direct-checkout';
			
			$this->options_direct_checkout = array(
				'direct_checkout_enabled' => '',
				'direct_checkout_cart_button_text' => '',
				'direct_checkout_exclude_external' => '',
				'direct_checkout_cart_redirect_url' => '',
				'direct_checkout_continue_enabled' => ''
			);

			$this->saved_options_direct_checkout = array();
			
			add_action('woocommerce_init', array(&$this, 'init'));
		}

		/**
		 * Initialize extension when WooCommerce is active
		 */
		public function init(){
			
			//add menu link for the plugin (backend)
			add_action( 'admin_menu', array( &$this, 'add_menu_direct_checkout' ) );
			
			if(get_option('direct_checkout_enabled'))
			{
				//unset all related options to disabled / not active
				update_option('woocommerce_cart_redirect_after_add', 'no');
				update_option('woocommerce_enable_ajax_add_to_cart', 'no');
				
				add_filter('single_add_to_cart_text', array( &$this, 'custom_cart_button_text') );
				add_filter('add_to_cart_text', array( &$this, 'custom_cart_button_text') );
				
				add_filter('woocommerce_product_single_add_to_cart_text', array( &$this, 'custom_cart_button_text') );
				add_filter('woocommerce_product_add_to_cart_text', array( &$this, 'custom_cart_button_text') );

				add_filter('woocommerce_add_to_cart_redirect', array( &$this, 'custom_add_to_cart_redirect') );

				if(get_option('direct_checkout_continue_enabled'))
					add_action( 'woocommerce_after_add_to_cart_button', array( &$this, 'direct_checkout_continue_button') );
					
			}
		}
		
		/**
		 * Set custom add to cart redirect
		 */
		function custom_add_to_cart_redirect() {
			$direct_checkout_cart_redirect_url	= get_option( 'direct_checkout_cart_redirect_url' );
			
			if($direct_checkout_cart_redirect_url != "")
				return $direct_checkout_cart_redirect_url; // Replace with the url of your choosing
			else 
				return get_permalink(get_option('woocommerce_checkout_page_id'));
		}

		/**
		 * Set continue shopping button for single product
		 */
		function direct_checkout_continue_button() {
			global $woocommerce, $post, $product;
			global $wp_query;

			$postID = $wp_query->post->ID;
			
			$direct_checkout_continue_enabled = get_option( 'direct_checkout_continue_enabled' );
			$single_product_title = strip_tags($post->post_title);

			if($direct_checkout_continue_enabled == "1"){
				$additional_button_text = __( 'Continue Shopping', $this->textdomain );
				$additional_button_url = get_permalink(get_option('woocommerce_shop_page_id'));
			} 

			$target_blank = $direct_checkout_continue_enabled ? '' : ' target="_blank"';
			
			if( $direct_checkout_continue_enabled ){
				$html_button = '<a href="'.$additional_button_url.'" title="'.$single_product_title.'" style="margin:5px 0" class="button alt"'.$target_blank.'>'.$additional_button_text.'</a>';
				echo $html_button;
			}	
		}
		
		/**
		 * Set custom add to cart text
		 */
		function custom_cart_button_text() {
			global $post;
			
			
			
			$direct_checkout_cart_button_text = get_option( 'direct_checkout_cart_button_text' ) ? get_option( 'direct_checkout_cart_button_text' )  : "Add to cart";
			$direct_checkout_exclude_external = get_option( 'direct_checkout_exclude_external' );
			
			if($direct_checkout_exclude_external){
				
				if( function_exists('get_product') ){
					$product = get_product( $post->ID );
						
					if( !$product->is_type( 'external' ) && $direct_checkout_cart_button_text && $direct_checkout_cart_button_text != ""){
						return __($direct_checkout_cart_button_text, $this->textdomain);
					} else {
						$button_text = get_post_meta( $post->ID, '_button_text', true ) ? get_post_meta( $post->ID, '_button_text', true ) : 'Buy product';
						return __($button_text, $this->textdomain);
					}
				}
				
			} else {
				if($direct_checkout_cart_button_text && $direct_checkout_cart_button_text != "")
					return __($direct_checkout_cart_button_text, $this->textdomain);
			}
		
		}
		
		/**
		 * Add a menu link to the woocommerce section menu
		 */
		function add_menu_direct_checkout() {
			$wc_page = 'woocommerce';
			$comparable_settings_page = add_submenu_page( $wc_page , __( 'Direct Checkout', $this->textdomain ), __( 'Direct Checkout', $this->textdomain ), 'manage_options', 'wc-direct-checkout', array(
					&$this,
					'settings_page_direct_checkout'
			));
		}
		
		/**
		 * Create the settings page content
		 */
		public function settings_page_direct_checkout() {
		
			// If form was submitted
			if ( isset( $_POST['submitted'] ) )
			{
				check_admin_referer( $this->textdomain );

				$this->saved_options_direct_checkout['direct_checkout_enabled'] = ! isset( $_POST['direct_checkout_enabled'] ) ? '1' : $_POST['direct_checkout_enabled'];
				$this->saved_options_direct_checkout['direct_checkout_continue_enabled'] = ! isset( $_POST['direct_checkout_continue_enabled'] ) ? '1' : $_POST['direct_checkout_continue_enabled'];
				$this->saved_options_direct_checkout['direct_checkout_cart_button_text'] = ! isset( $_POST['direct_checkout_cart_button_text'] ) ? 'Add to cart' : $_POST['direct_checkout_cart_button_text'];
				$this->saved_options_direct_checkout['direct_checkout_exclude_external'] = ! isset( $_POST['direct_checkout_exclude_external'] ) ? '1' : $_POST['direct_checkout_exclude_external'];
				$this->saved_options_direct_checkout['direct_checkout_cart_redirect_url'] = ! isset( $_POST['direct_checkout_cart_redirect_url'] ) ? '' : $_POST['direct_checkout_cart_redirect_url'];
					
				foreach($this->options_direct_checkout as $field => $value)
				{
					$option_direct_checkout = get_option( $field );
		
					if($option_direct_checkout != $this->saved_options_direct_checkout[$field])
						update_option( $field, $this->saved_options_direct_checkout[$field] );
				}
					
				// Show message
				echo '<div id="message" class="updated fade"><p>' . __( 'You have saved WooCommerce Direct Checkout options.', $this->textdomain ) . '</p></div>';
			}
		
			$direct_checkout_enabled			= get_option( 'direct_checkout_enabled' );
			$direct_checkout_continue_enabled		= get_option( 'direct_checkout_continue_enabled' );
			$direct_checkout_cart_button_text	= get_option( 'direct_checkout_cart_button_text' ) ? get_option( 'direct_checkout_cart_button_text' ) : 'Add to Cart';
			$direct_checkout_exclude_external	= get_option( 'direct_checkout_exclude_external' );
			$direct_checkout_cart_redirect_url	= get_option( 'direct_checkout_cart_redirect_url' );
			
			$checked_enabled = '';
			$checked_enabled_exclude  = '';
			$checked_continue_enabled = '';
		
			if($direct_checkout_enabled)
				$checked_enabled = 'checked="checked"';
			
			if($direct_checkout_exclude_external)
				$checked_enabled_exclude = 'checked="checked"';

			if($direct_checkout_continue_enabled)
				$checked_continue_enabled = 'checked="checked"';
		
			$actionurl = $_SERVER['REQUEST_URI'];
			$nonce = wp_create_nonce( $this->textdomain );
		
		
			// Configuration Page
		
			?>
		<div id="icon-options-general" class="icon32"></div>
		<h3><?php _e( 'Direct Checkout Options', $this->textdomain); ?></h3>
		
		
		<table style="width:90%;padding:5px;border-collapse:separate;border-spacing:5px;vertical-align:top;">
		<tr>
			<td colspan="2">Checking out is the most important and key part of placing an order online, and many users end up abandoning their order at the end. This plugin will simplify the checkout process, leading to an immediate increase in sales.</td>
		</tr>
		<tr>
			<td width="70%" style="vertical-align:top;">
				<form action="<?php echo $actionurl; ?>" method="post">
				<table>
						<tbody>
							<tr>
								<td colspan="2">
									<table class="widefat auto" cellspacing="2" cellpadding="5" border="0">
										<tr>
											<td width="30%"><?php _e( 'Enable', $this->textdomain ); ?></td>
											<td>
												<input class="checkbox" name="direct_checkout_enabled" id="direct_checkout_enabled" value="0" type="hidden">
												<input class="checkbox" name="direct_checkout_enabled" id="direct_checkout_enabled" value="1" type="checkbox" <?php echo $checked_enabled; ?>>
											</td>
										</tr>
										<tr>
											<td width="30%"><?php _e( 'Add Continue Shopping Button', $this->textdomain ); ?></td>
											<td>
												<input class="checkbox" name="direct_checkout_continue_enabled" id="direct_checkout_continue_enabled" value="0" type="hidden">
												<input class="checkbox" name="direct_checkout_continue_enabled" id="direct_checkout_continue_enabled" value="1" type="checkbox" <?php echo $checked_continue_enabled; ?>>
											</td>
										</tr>
										<tr>
											<td width="30%"><?php _e( 'Custom Add to Cart Text', $this->textdomain ); ?></td>
											<td>
												<input name="direct_checkout_cart_button_text" id="direct_checkout_cart_button_text" value="<?php echo $direct_checkout_cart_button_text; ?>" />
												<input class="checkbox" name="direct_checkout_exclude_external" id="direct_checkout_exclude_external" value="0" type="hidden">
												<input class="checkbox" name="direct_checkout_exclude_external" id="direct_checkout_exclude_external" value="1" type="checkbox" <?php echo $checked_enabled_exclude; ?> type="checkbox">&nbsp;<i>Exclude External Product</i>
											</td>
										</tr>
										<tr>
											<td width="30%"><?php _e( 'Redirect to Page', $this->textdomain ); ?><br /><span style="color:#ccc;"><?php _e( '(Default will be checkout page if not set)', $this->textdomain ); ?></span></td>
											<td>
												<select name="direct_checkout_cart_redirect_url">
												<option value=""><?php echo esc_attr( __( 'Select page' ) ); ?></option> 
												 <?php 
												  $pages = get_pages(); 
												  foreach ( $pages as $page ) {
													if($direct_checkout_cart_redirect_url == get_permalink( $page->ID ))
												  		$option = '<option value="' . get_permalink( $page->ID ) . '" selected="selected">';
													else 
														$option = '<option value="' . get_permalink( $page->ID ) . '">';
													$option .= $page->post_title;
													$option .= '</option>';
													echo $option;
												  }
												 ?>
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan=2">
									<input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options', $this->textdomain); ?>" id="submitbutton" />
									<input type="hidden" name="submitted" value="1" /> 
									<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $nonce; ?>" />
								</td>
							</tr>
						</tbody>
				</table>
				</form>
			
			</td>
			
			<td width="30%" style="background:#ececec;padding:10px 5px;" valign="top">
				<div style="float:left;width:200px;">
					<a href="https://twitter.com/terrytsang811" class="twitter-follow-button" data-show-count="false" data-lang="en">Follow @terrytsang811</a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
				</div>
				<div>
					<div id="fb-root"></div>
					<script>(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) return;
					  js = d.createElement(s); js.id = id;
					  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=354404357988395";
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));</script>
					<div class="fb-like" data-href="http://wordpress.org/plugins/woocommerce-direct-checkout/" data-layout="button" data-action="like" data-show-faces="false" data-share="false"></div>
				</div>
				<p><b>WooCommerce Direct Checkout</b> is a FREE woocommerce plugin developed by <a href="http://shop.terrytsang.com" target="_blank" title="Terry Tsang - a PHP Developer and Wordpress Consultant">Terry Tsang</a>. This plugin aims to add direct checkout for WooCommerce.</p>
				
				<?php
					$get_pro_image = WooCommerce_Direct_Checkout::$plugin_url . '/images/direct-checkout-pro-version.png';
				?>
				<div align="center"><a href="http://terrytsang.com/shop/shop/woocommerce-direct-checkout-pro/" target="_blank" title="WooCommerce Direct Checkout PRO"><img src="<?php echo $get_pro_image; ?>" border="0" /></a></div>
				<div aling="center"><i>PRO version additional features: Individual product override settings and additional button option.</i></div>
				<h3>Get More Plugins</h3>
			
				<p><a href="http://shop.terrytsang.com" target="_blank" title="Premium &amp; Free Extensions/Plugins for E-Commerce by Terry Tsang">Go to My Site</a> to get more free and premium extensions/plugins for your ecommerce sites.</p>
			
				<h3>Spreading the Word</h3>

				<ul style="list-style:none">If you find this plugin helpful, you can:	
					<li>- Write and review about it in your blog</li>
					<li>- Rate it on <a href="http://wordpress.org/extend/plugins/woocommerce-direct-checkout/" target="_blank">wordpress plugin page</a></li>
					<li>- Share on your social media<br />
					<a href="http://www.facebook.com/sharer.php?u=http://terrytsang.com/shop/shop/woocommerce-direct-checkout/&amp;t=WooCommerce Direct Checkout" title="Share this WooCommerce Direct Checkout on Facebook" target="_blank"><img src="http://terrytsang.com/shop/images/social_facebook.png" alt="Share this WooCommerce Direct Checkout plugin on Facebook"></a> 
					<a href="https://twitter.com/intent/tweet?url=http%3A%2F%2Fterrytsang.com%2Fshop%2Fshop%2Fwoocommerce-direct-checkout%2F&text=WooCommerce Direct Checkout - &via=terrytsang811" target="_blank"><img src="http://terrytsang.com/shop/images/social_twitter.png" alt="Tweet about WooCommerce Direct Checkout plugin"></a>
					<a href="http://linkedin.com/shareArticle?mini=true&amp;url=http://terrytsang.com/shop/shop/woocommerce-direct-checkout/&amp;title=WooCommerce Direct Checkout plugin" title="Share this WooCommerce Direct Checkout plugin on LinkedIn" target="_blank"><img src="http://terrytsang.com/shop/images/social_linkedin.png" alt="Share this WooCommerce Direct Checkout plugin on LinkedIn"></a>
					</li>
					<li>- Or make a donation</li>
				</ul>

				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LJWSJDBBLNK7W" target="_blank"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" alt="" /></a>

				<h3>Thank you for your support!</h3>
			</td>
			
		</tr>
		</table>
		
		
		<br />
		
	<?php
		}
		
		/**
		 * Get the setting options
		 */
		function get_options() {
			
			foreach($this->options_direct_checkout as $field => $value)
			{
				$array_options[$field] = get_option( $field );
			}
				
			return $array_options;
		}

		
	}//end class
		
}//if class does not exist

$woocommerce_direct_checkout = new WooCommerce_Direct_Checkout();
?>