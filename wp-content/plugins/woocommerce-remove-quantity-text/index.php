<?php
/*
Plugin Name: Woocommerce Remove Quantity Text
Description:  Remove the Qty from the product page in Woocommerce. according to Product Type.
Version: 1.0.0
tag: 1.0.0
Author: Ravi Bhushan Raiya
License: GPL2
*/
if(!class_exists('WooRemoveQtytext')) :

// DEFINE PLUGIN ID
define('WOOREMOVEQTYTEXT_ID', 'wooremoveqtytext-options');
// DEFINE PLUGIN NICK
define('WOOREMOVEQTYTEXT_NICK', 'WooCommerce Remove Qty Text Options');

    class WooRemoveQtytext
    {
		/** function/method
		* Usage: return absolute file path
		* Arg(1): string
		* Return: string
		*/
		public static function file_path($file)
		{
			return ABSPATH.'wp-content/plugins/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).$file;
		}
		/** function/method
		* Usage: hooking the plugin options/settings
		* Arg(0): null
		* Return: void
		*/
		public static function register()
		{
			register_setting(WOOREMOVEQTYTEXT_ID.'_options', 'wooremoveqtytext_variable');
			register_setting(WOOREMOVEQTYTEXT_ID.'_options', 'wooremoveqtytext_grouped');
			register_setting(WOOREMOVEQTYTEXT_ID.'_options', 'wooremoveqtytext_external');
			register_setting(WOOREMOVEQTYTEXT_ID.'_options', 'wooremoveqtytext_default');
		}
		/** function/method
		* Usage: hooking (registering) the plugin menu
		* Arg(0): null
		* Return: void
		*/
		public static function menu()
		{
			// Create menu tab
			add_options_page(WOOREMOVEQTYTEXT_NICK.' Plugin Options', WOOREMOVEQTYTEXT_NICK, 'manage_options', WOOREMOVEQTYTEXT_ID.'_options', array('WooRemoveQtytext', 'options_page'));
		}
		/** function/method
		* Usage: show options/settings form page
		* Arg(0): null
		* Return: void
		*/
		public static function options_page()
		{ 
			if (!current_user_can('manage_options')) 
			{
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}
			
			$plugin_id = WOOREMOVEQTYTEXT_ID;
			// display options page
			include(self::file_path('options.php'));
		}
		/** function/method
		* Usage: Remove Qty Field
		* Arg(1): string
		* Return: string
		*/

		public static function wc_remove_all_quantity_fields( $return, $product ) {
		  switch ($product->product_type) :
				case "variable" :
					if(get_option('wooremoveqtytext_variable')=='yes')
						 return true;
					else
						return false;
					break;
				case "grouped" :
					 if(get_option('wooremoveqtytext_grouped')=='yes')
						 return true;
					else
						return false;
					break;
				case "external" :
					if(get_option('wooremoveqtytext_external')=='yes')
						 return true;
					else
						return false;
					break;
				default :
					if(get_option('wooremoveqtytext_default')=='yes')
						 return true;
					else
						return false;
					break;
			endswitch;
		}
		
    }
	
	if ( is_admin() )
	{
		add_action('admin_init', array('WooRemoveQtytext', 'register'));
		add_action('admin_menu', array('WooRemoveQtytext', 'menu'));
	}
	//add_filter('the_content', array('kkPluginOptions', 'content_with_quote'));
	add_filter( 'woocommerce_is_sold_individually', array('WooRemoveQtytext', 'wc_remove_all_quantity_fields'), 10, 2);

endif;


?>