=== WooCommerce Simply Order Export ===
Contributors: wpgurudev
Donate link: http://sharethingz.com
Tags: woocommerce, order, export, csv, duration, woocommerce-order, woocommerce-order-export
Requires at least: 4.5.0
Tested up to: 5.3.0
Stable tag: 3.0.13
License: GPLv2 or later (of-course)
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Export WooCommerce order details in csv format.

== Description ==
This plugin exports data of WooCommerce orders, which may include Customer Name, Product details along with quantity, name and variation details, Amount paid., Customer Email and Order status. The data will be exported in the form of csv file.

You can add more fields to export and extend the functionality by using hooks. It has extensive options in woocommerce setting page, you can select which fields will be present in csv file.

= Features =

* Easy to install and setup
* Very simplified and clean UI.
* Exports each product in order in separate line.
* Easily customizable.
* Reorder fields.
* Exports csv file containing information of WooCommerce orders
* Exports orders between certain duration, you can select start and end date.
* Exports item quantity, product name along with variation details.
* Options for fields which you want to export
* Strong support.
* For customization according to your need contact: https://www.facebook.com/shrthngz
* Very lighweight code.
* Translation ready code.
* Contribution in translating plugin to different languages is strongly encouraged.

= Fields in free plugin =

* Order ID
* Product Name
* Order Status
* Order Number
* Order Date
* Order Total
* Order Currency
* Prices Include Tax (yes/no)
* Order Total Tax
* Order Shipping Total
* Order Shipping Tax
* Customer Name
* Customer's Billing Email

= Fields added by add-on plugin =

* Product ID
* Product Quantity
* Variation Attributes
* Product URL
* Product Categories
* Product SKU
* Billing First Name
* Billing Last Name
* Billing Address Line 1
* Billing Address Line 2
* Billing City
* Billing State
* Billing Country
* Billing Postcode
* Billing Company
* Billing Phone Number
* Shipping First Name
* Shipping Last Name
* Shipping Address Line 1
* Shipping Address Line 2
* Shipping City
* Shipping State
* Shipping Country
* Shipping Postcode
* Shipping Method
* Payment Method
* Payment Method Title
* Transaction ID
* Customer IP Address
* Customer User Agent
* Customer Notes
* Coupon Codes
* Coupon Discounts
* Cart Discount
* Cart Discount Tax

= Features in WooCommerce Simply Order Export Add-on =

* All fields related to order.
* Schedule the order export for future.
* Export will repeat itself after specified interval of time automatically.
* Link for scheduled exported file will be sent over to email.

> **Purchase Add-on**
>
> Get your WooCommerce Simply Order Export Add-on from [this link](http://sharethingz.com/woocommerce-simply-order-export-add-on/?utm_source=readme&utm_medium=plugin&utm_campaign=wsoe)
>
> Documentation of this add-on is present at [WooCommerce Simply Order Export Add-on Documentation](https://github.com/ankitrox/WooCommerce-Simply-Order-Export-Add-on-Doc/blob/master/README.md)

== Installation ==
Install WooCommerce Simply Order Export from the 'Plugins' section in your dashboard (Plugins > Add New > Search for WooCommerce Simply Order Export ).

Place the downloaded plugin directory into your wordpress plugin directory.

Activate it through the 'Plugins' section.

== Screenshots ==

1. WooCommerce Simply Order Export setting page.
2. Advanced options.

== Frequently Asked Questions ==

= Export button not working, any problem ? =

1. Please check if you have installed latest version of woocommerce along with woocommerce simply order export plugin.
2. Deactivate all plugins except woocommerce and woocommerce simply order export plugins, switch to default theme and check if issue still persists or not?
3. If issue persists, consider contacting [here](http://sharethingz.com/contact/?utm_source=readme&utm_medium=plugin&utm_campaign=wsoe)

= How to export orders with specific statuses ? =

Go to advanced options and then check statuses you want to export.

= How to add more fields to csv ? =

Please use woooe_exportable_fields hooks for performing this activity. Little WordPress programming knowledge is necessary for accomplishing this.

You can also opt for woocommerce simply order export add-on, it is available [here](http://sharethingz.com/woocommerce-simply-order-export-add-on/?utm_source=readme&utm_medium=plugin&utm_campaign=wsoe). It adds all the fields related to orders and allows users to reorder the fields using drag and drop interface.

== Changelog ==

= 3.0.13 =
* UI Improvements.
* Docblock changes.
* Compatibility checks.

= 3.0.12 =
* Feature: Added field filters to show/hide required fields.

= 3.0.11 =
* Fix: Redirection to dashboard when trying to download export file.

= 3.0.10 =
* Added 'woooe_items_filtering' filter to filter out items in an order based on categories and tags.

= 3.0.9 =
* Hide add-on notice for a day once dismissed.
* Stripslashes when column names contains apostrophe.

= 3.0.8 =
* return settings array on woocommerce_get_settings_pages filter.

= 3.0.7 =
* Display warning message when no records are found for a given date range.

= 3.0.6 =
* Fixed fatal error for including settings page.

= 3.0.5 =
* Fixed error.

= 3.0.4 =
* Fixed warning for settings_pages filter.

= 3.0.3 =
* Fixed add-on notice bug.

= 3.0.2 =
* Added order currency field.
* Added prices_include_tax field.
* Error handling and reporting.
* Exception handling.

= 3.0.0 =
* Changed complete anatomy of plugin.
* Added more fields for orders.
* WooCommerce settings API used for all fields.
* Reordering of fields.

= 2.1.9 =
* Fix: Fatal error, cannot convert object to array.

= 2.1.8 =
* Fix: Fixed warning for has_meta function while fetching variation details.
* Fix: Fixed license renewal warning.

= 2.1.7 =
* Compatibility: WooCommerce 3.0 compatibility checks and fixes.

= 2.1.6 =
* Modification: Changed main class structure to adopt factory method.
* Modification: Changed settings API for plugin, now can be requested directly by calling instantiated object.
* Feature: License checker and renewal reminder class for premium plugins.

= 2.1.5 =
* Fix: Fatal error - Can’t use function return value in write context.

= 2.1.4 =
* Feature: Ability to import and export settings of the plugin.

= 2.1.3 =
* Bug fix: Fixed duplicate record entries in export.

= 2.1.2 =
* Bug fix: Iterator value in do while loop.

= 2.1.1 =
* Bug fix: Duplicate records are added for last iteration.

= 2.1.0 =
* Fix: Memory leak issue, fetching data in chunks instead of all data at once.
* Hook: Addition of wsoe_chunk_size hook for data fetching chunk size. Default is 20 records per query.
* UI: Added spinner beside export button, so that user will get to know operation is in progress.

= 2.0.8 =
* Minor fix: Removed product name check for adding fields in csv to solve 500 internal errors.
* Miscellaneous: Code cleanup. Removed unnecessary blocks of code.

= 2.0.7 =
* Minor fix: php support for older than 5.4.0 added for html_entity_decode function

= 2.0.6 =
* Minor fix: Invalid argument in foreach warning fixed.

= 2.0.5 =
* Minor fix: wsoe_after_value_* hook

= 2.0.4 =
* Feature Add: Introduced wsoe_after_heading_* action to add headings for non-sanitized meta keys.
* Feature Add: Introduced wsoe_after_value_* action to add values for non-sanitized meta keys.

= 2.0.3 =
* Minor fix: Priority changed for wsoe_fix_weird_chars

= 2.0.2 =
* Bug Fix: weird characters issue on MS-Excel resolved.
* Added extra setting field for character set fix for Excel.
* Compatibility test with WordPress 4.4 and latest woocommerce passed.

= 2.0.1 =
* Bug Fix: htaccess missing notice.

= 2.0.0 =
* Feature: Files created will be preserved and kept in separate folder.
* Bug Fix: Content-Encoding added in header while downloading file to avoid strange characters.
* Feature: Advanced options values are now persistent and can be saved for future use.
* Bug Fix: "Hide Notice" button fix.
* Compatibility: Order export scheduler and logger plugin compatibility.
* Added Portuguese translation.
* Feature: Formatted prices.
* Bug Fix: Removed deprecated functions.

= 1.2.5 =
* Bug Fix: admin help class class_exists missing parameter.

= 1.2.4 =
* Bug Fix: Getting order number instead of order post ID.
* Usability: Added usability instructions in help tab on plugin settings page.
* Added parameters to wpg_add_values_to_csv filter.
* .po file updated.

= 1.2.3 =
* Feature Added: Each prouct for order in a different row.
* Feature Added: Quantity and Variation added as separate fields.

= 1.2.2 =
* Added 'wsoe_query_args' filter to filter query arguments.
* Added 'Settings' action link on plugin page, so that users can easily navigate to order export settings page.

= 1.2.1 =
* Added dismissible notice for WooCommerce Simply Order Export Add-On.

= 1.2.0 =
* Fixed capability issue to download csv. Given manage_woocommerce capability.
* Added support for wsoe add-on.

= 1.1.6 =
* Fixed variation issue for WooCommerce 2.3.7.

= 1.1.5 =
* Fixed variation details bug in csv.

= 1.1.4 =
* Added custom delimiter option.
* wpg_delimiter filter to override user-defined delimiter.

= 1.1.3 =
* Changed hooks position.
* Order object passed as parameter to wpg_before_csv_write hook.

= 1.1.2 =
* Fixed script enqueue bug.

= 1.1.1 =
* Added advanced options in settings page.
* UI improvements.

= 1.1.0 =
* Fixed Phone number export bug.
* Made plugin translation ready.
* Main file changed to class based structure.
* Added po file for translation contribution.

= 1.0.3 =
* Fixed "invalid request" js error.

= 1.0.2 =
* Added nonce to export request to make it more secure.
* Renamed file order_export_process to order-export-process.
* Check for ABSPATH constant at start of files to avoid data leaks.
* Check for WooCommerce installation in main plugin file.

= 1.0.1 =
* Added 'wpg_order_statuses' hook to filter post status in export query.

= 1.0.0 =
* First release.