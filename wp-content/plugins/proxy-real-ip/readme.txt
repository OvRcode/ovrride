=== Plugin Name ===
Contributors: samparsons
Donate link: http://sjparsons.com/
Tags: proxy, load balancer, IP Address
Requires at least: 3.0.1
Tested up to: 3.3.1
Stable tag: 1.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Proxy Real IP is a simple plugin that sets user's IP addresses correctly when running WordPress behind a load balancer or other proxy.

== Description ==

If you're running WordPress behind a proxy, chances are that the IP address of the user browsing your site won't show up, instead WordPress will see the IP address of your proxy server. Proxy Real IP sets this straight by looking at the HTTP headers that the proxy sends.

Proxy Real IP looks in the following HTTP headers:
X-FORWARDED-FOR, X-FORWARDED, FORWARDED-FOR, FORWARDED, X-REAL-IP

Proxy Real IP sets the user's IP address to the first HTTP header defined that matches a simple regular expression of an IP address (123.123.123.123).

Proxy Real IP also sets the $_SERVER['HTTPS'] variable correctly when running behind a proxy so that WordPress can determine whether or not the proxy connection is over HTTPS. It looks to the HTTP_X_FORWARDED_PROTO PHP server variable to determine this.


== Installation ==

Installation is simple. 

1. Upload `proxy-real-ip.php` to the `/wp-content/plugins/` directory or the `/wp-content/mu-plugins/` directory if you're running a Multisite installation and want to force load the plugin for all sites.
2.  If you uploaded it to `/wp-content/plugins/`, then activate the plugin through the 'Plugins' menu in WordPress.

There are no settings for this plugin in the WordPress admin area. It should just work out of the box.


== Changelog ==

= 1.1 =
* 2012-12-27 Added HTTPS detection.

= 1.0 =
* 2012-09-25 Initial version launched. Started with some ideas from http://wordpress.org/extend/plugins/real-ip/ which was no longer updated.
