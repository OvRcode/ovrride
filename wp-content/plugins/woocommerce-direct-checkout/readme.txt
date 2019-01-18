=== Plugin Name ===
Contributors: terrytsang
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=terry@terrytsang.com&item_name=Donation+for+TerryTsang+Wordpress+WebDev
Plugin Name: WooCommerce Custom Direct Checkout
Plugin URI:  http://terrytsang.com/shop/shop/woocommerce-direct-checkout/
Tags: woocommerce, custom fields, direct, checkout, e-commerce
Requires at least: 3.0
Tested up to: 4.9.8
Stable tag: 1.1.2
Version: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

You can skip shopping cart page and implement add to cart button redirect to checkout page or you can redirect to other existing page.

== Description ==

A WooCommerce plugin that aims to simplify the checkout process, leading to an immediate increase in sales.

In WooCommerce Settings Panel, there will be a new submenu link called 'Direct Checkout' where you can:

*   Enabled / Disabled the direct checkout option
*   Add "Continue Shopping" button to product page
*   Change "Add to cart" to any text (Option to exclude external product)
*   Update "Redirect to Page" option

= Features =

*   Implement add to cart button redirect to checkout page pattern
*   2 languages available : English UK (en_GB) and Chinese (zh_CN)

= IMPORTANT NOTES =
*   Do use POEdit and open 'wc-direct-checkout.pot' file and save the file as wc-direct-checkout-[language code].po, then put that into languages folder for this plugin.
*   Please uncheck the option "Enable AJAX add to cart buttons on archives" at WooCommerce > Settings > Catalog to make the rediection working without ajax.

= GET PRO VERSION =
*   [WooCommerce Direct Checkout PRO](http://terrytsang.com/shop/shop/woocommerce-direct-checkout-pro/) - Added Per Product Setings and Additional Button. 

= In addition to these features, over 20 WooCommerce extensions are available: =
* [Facebook Share Like Button](http://terrytsang.com/shop/shop/woocommerce-facebook-share-like-button/) - add Facebook Share and Like button at product page.
* [Custom Checkout Options](http://terrytsang.com/shop/shop/woocommerce-custom-checkout-options/) - implement customization for entire checkout process.
* [Social Buttons PRO](http://terrytsang.com/shop/shop/woocommerce-social-buttons-pro/) - additional 9 social share buttons where you can engage more audience.
* [Extra Fee Option PRO](http://terrytsang.com/shop/shop/woocommerce-extra-fee-option-pro/) - add multiple extra fee for any order with multiple options.
* [Custom Product Tabs](http://terrytsang.com/shop/shop/woocommerce-custom-product-tabs/) - add multiple tabs to WooCommerce product page.
* [Facebook Social Plugins](http://terrytsang.com/shop/shop/woocommerce-facebook-social-plugins/) - implement Facebook Social Plugins that let the users liked, commented or shared your site's contents.
* [Custom Payment Method](http://terrytsang.com/shop/shop/woocommerce-custom-payment-method/) - customise the custom payment method with flexible options.
* [Custom Shipping Method](http://terrytsang.com/shop/shop/woocommerce-custom-shipping-method/) - define own settings for custom shipping method.
* [Donation/Tip Checkout](http://terrytsang.com/shop/shop/woocommerce-donation-tip-checkout/) - add donation/tip amount option for their customers at WooCommerce checkout page.
* [Product Badge](http://terrytsang.com/shop/shop/woocommerce-product-badge/) - add mulitple badges to the products.
* [Facebook Connect Checkout](http://terrytsang.com/shop/shop/woocommerce-facebook-login-checkout/) - implement Facebook Login so that new customers can sign in woocommerce site by using their Facebook account.
* [Product Catalog](http://terrytsang.com/shop/shop/woocommerce-product-catalog/) - turn WooCommerce into a product catalog with a few clicks.
* [Coming Soon Product](http://terrytsang.com/shop/shop/woocommerce-coming-soon-product/) - show 'Coming Soon' default message and countdown clock for pre launch product

and many more...

= Free & Popular WooCommerce Bundle extensions: =
* [WooCommerce Free Extensions Bundle](http://terrytsang.com/shop/shop/woocommerce-free-extensions-bundle/) - 5 free plugins in 1 download
* [WooCommerce Popular Extensions Bundle](http://terrytsang.com/shop/shop/woocommerce-popular-extensions-bundle/) - 5 unlimited licenses premium plugins with only $99

== Installation ==

1. Upload the entire *woocommerce-direct-checkout* folder to the */wp-content/plugins/* directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce Settings panel at left sidebar menu and update the options at Tab *Direct Checkout* there.
4. That's it. You're ready to go and cheers!

== Screenshots ==

1. [screenhot-1.png] Screenshot Admin WooCommerce Settings - Direct Checkout options
2. [screenhot-2.png] Screenshot Frontend WooCommerce - Catalog page
3. [screenhot-3.png] Screenshot Frontend WooCommerce - Product page

== Changelog ==

= 1.1.1 =

* Updated deprecated function from 'add_to_cart_redirect' to 'woocommerce_add_to_cart_redirect'

= 1.1.0 =

* Updated continue shopping button css
* Updated pro version additional features

= 1.0.10 =

* Removed WooCommerce installation checking

= 1.0.9 =

* Fixed narrow form layout bugs
 
= 1.0.8 =

* Add Continue Shopping button at product page
* Fixed missing variable warning

= 1.0.7 =

* Updated PRO version download link

= 1.0.6 =

* Added 'Exclude external product' checkbox for Custom Add to Cart Text option

= 1.0.5 =

* Updated "Add to cart" filter with WooCommere v2.1.x
* Fixed table width problem for option settings page

= 1.0.4 =

* Added pro version link 

= 1.0.3 =

* Updated reademe file for instruction and notes


= 1.0.2 =

* Update 'Redirect to Page' option
* Updated table style for Firefox display bugs

= 1.0.1 =

* Add 'Redirect to Page' option, let user choose redirecting to any page after add to cart pressed
* Updated wrong hyperlink

= 1.0.0 =

* Initial Release
* Customize add to cart text and change add to cart function directly to checkout page

