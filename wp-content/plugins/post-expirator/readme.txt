=== Post Expirator ===
Contributors: axelseaa
Donate link: http://aaron.axelsen.us/donate
Tags: expire, posts, pages, schedule
Requires at least: 4.0
Tested up to: 4.8
Stable tag: 2.3.1.1

Allows you to add an expiration date to posts which you can configure to either delete the post, change it to a draft, or update the 
post categories.

== Description ==

The Post Expirator plugin allows the user to set expiration dates for both posts and pages.  There are a number of different ways that the posts can expire:

* Draft
* Delete
* Trash
* Private
* Stick
* Unstick
* Categories: Replace
* Categories: Add
* Categories: Remove

For each expiration event, a custom cron job will be schedule which will help reduce server overhead for busy sites.

The expiration date can be displayed within the actual post by using the [postexpirator] tag.  The format attribute will override the plugin 
default display format.  See the [PHP Date Function](http://us2.php.net/manual/en/function.date.php) for valid date/time format options. 

NOTE: This plugin REQUIRES that WP-CRON is setup and functional on your webhost.  Some hosts do not support this, so please check and confirm if you run into issues using the plugin.

Plugin homepage [WordPress Post Expirator](http://postexpirator.tuxdocs.net).

New! [Feature Requests](http://postexpirator.uservoice.com) Please enter all feature requests here.  Requests entered via the plugin website or support forum may be missed.

**[postexpirator] shortcode attributes**

* type - defaults to full - valid options are full,date,time
* dateformat - format set here will override the value set on the settings page
* timeformat - format set here will override the value set on the settings page 

This plugin is fully compatible with WordPress Multisite Mode.

== Installation ==

This section describes how to install the plugin and get it working.

1. Unzip the plugin contents to the `/wp-content/plugins/post-expirator/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Adding expiration date to a post
2. Viewing the exipiration dates on the post overview screen
3. Settings screen

== Changelog ==

**Version 2.3.1**

* Fix: Fixed PHP Error that snuck in on some installations.

**Version 2.3.0**

* New: Email notification upon post expiration.  A global email can be set, blog admins can be selected and/or specific users based on post type can be notified.
* New: Expiration Option Added - Stick/Unstick post is now available.
* New: Expiration Option Added - Trash post is now available.
* New: Added custom actions that can be hooked into when expiration events are scheduled / unscheduled.
* Fix: Minor HTML Code Issues

**Version 2.2.2**

* Fix: Quick Edit did not retain the expire type setting, and defaulted back to "Draft".  This has been resolved.

**Version 2.2.1**

* Fix: Fixed issue with bulk edit not correctly updating the expiration date.

**Version 2.2.0**

* New: Quick Edit - setting expiration date and toggling post expiration status can now be done via quick edit.
* New: Bulk Edit - changing expiration date on posts that already are configured can now be done via bulk edit.
* New: Added ability to order by Expiration Date in dashboard.
* New: Adjusted formatting on defaults page.  Multiple post types are now displayed cleaner.
* Fix: Minor Code Cleanup

**Version 2.1.4**

* Fix: PHP Strict errors with 5.4+
* Fix: Removed temporary timezone conversion - now using core functions again

**Version 2.1.3**

* Fix: Default category selection now saves correctly on default settings screen

**Version 2.1.2**

* Security: Added form nonce for protect agaisnt possible CSRF
* Security: Fixed XSS issue on settings pages
* New: Added check to show if WP_CRON is enabled on diagnostics page
* Fix: Minor Code Cleanup

**Version 2.1.1**

* New: Added the option to disable post expirator for certain post types if desired
* Fix: Fixed php warning issue cause when post type defaults are not set

**Version 2.1.0**

* New: Added support for heirarchical custom taxonomy
* New: Enhanced custom post type support
* Fix: Updated debug function to be friendly for scripted calls 
* Fix: Change to only show public custom post types on defaults screen
* Fix: Removed category expiration options for 'pages', which is currently unsupported
* Fix: Some date calls were getting "double" converted for the timezone pending how other plugins handled date - this issue should now be resolved

**Version 2.0.1**

* Removes old scheduled hook - this was not done completely in the 2.0.0 upgrade
* Old option cleanup

**Version 2.0.0**

This is a major update of the core functions of this plugin.  All current plugins and settings should be upgraded to the new formats and work as expected.  Any posts currently schedule to be expirated in the future will be automatically upgraded to the new format.

* New: Improved debug calls and logging
* New: Added the ability to expire to a "private" post
* New: Added the ability to expire by adding or removing categories.  The old way of doing things is now known as replacing categories
* New: Revamped the expiration process - the plugin no longer runs on an minute, hourly, or other schedule.  Each expiration event schedules a unique event to run, conserving system resources and making things more efficient
* New: The type of expiration event can be selected for each post, directly from the post editing screen
* New: Ability to set defaults for each post type (including custom posts)
* New: Renamed expiration-date meta value to _expiration-date
* New: Revamped timezone handling to be more correct with WordPress standards and fix conflicts with other plugins
* New: 'Expires' column on post display table now uses the default date/time formats set for the blog
* Fix: Removed kses filter calls when then schedule task runs that was causing code entered as unfiltered_html to be removed
* Fix: Updated some calls of date to now use date_i18n
* Fix: Most (if not all) php error/warnings should be addressed
* Fix: Updated wpdb calls in the debug class to use wpdb_prepare correctly
* Fix: Changed menu capability option from "edit_plugin" to "manage_options"

**Version 1.6.2**

* Added the ability to configure the post expirator to be enabled by default for all new posts
* Changed some instances of mktime to time
* Fixed missing global call for MS installs

**Version 1.6.1**

* Tweaked error messages, removed clicks for reset cron event
* Switched cron schedule functions to use "current_time('timestamp')"
* Cleaned up default values code
* Added option to allow user to select any cron schedule (minute, hourly, twicedaily, daily) - including other defined schedules
* Added option to set default expiration duration - options are none, custom, or publish time
* Code cleanup - php notice

**Version 1.6**

* Fixed invalid html
* Fixed i18n issues with dates
* Fixed problem when using "Network Activate" - reworked plugin activation process
* Replaced "Upgrade" tab with new "Diagnostics" tab
* Reworked expire logic to limit the number of sql queries needed
* Added debugging
* Various code cleanup

**Version 1.5.4**

* Cleaned up deprecated function calls

**Version 1.5.3**

* Fixed bug with sql expiration query (props to Robert & John)

**Version 1.5.2**

* Fixed bug with shortcode that was displaying the expiration date in the incorrect timezone
* Fixed typo on settings page with incorrect shortcode name

**Version 1.5.1**

* Fixed bug that was not allow custom post types to work

**Version 1.5**

* Moved Expirator Box to Sidebar and cleaned up meta code
* Added ability to expire post to category

**Version 1.4.3**

* Fixed issue with 3.0 multisite detection

**Version 1.4.2**

* Added post expirator POT to /languages folder
* Fixed issue with plugin admin navigation
* Fixed timezone issue on plugin options screen

**Version 1.4.1**

* Added support for custom post types (Thanks Thierry)
* Added i18n support (Thanks Thierry)
* Fixed issue where expiration date was not shown in the correct timezone in the footer
* Fixed issue where on some systems the expiration did not happen when scheduled

**Version 1.4**

NOTE: After upgrading, you may need to reset the cron schedules.  Following onscreen notice if prompted.  Previously scheduled posts will not be updated, they will be deleted referncing the old timezone setting.  If you wish to update them, you will need to manually update the expiration time.

* Fixed compatability issues with Wordpress - plugin was originally coded for WPMU - should now work on both
* Added ability to schedule post expiration by minute
* Fixed timezone - now uses the same timezone as configured by the blog

**Version 1.3.1**

* Fixed sporadic issue of expired posts not being removed

**Version 1.3**

* Expiration date is now retained across all post status changes
* Modified date/time format options for shortcode postexpirator tag
* Added the ability to add text automatically to the post footer if expiration date is set

**Version 1.2.1**

* Fixed issue with display date format not being recognized after upgrade

**Version 1.2**

* Changed wording from "Expiration Date" to "Post Expirator" and moved the configuration options to the "Settings" tab.
* Added shortcode tag [postexpirator] to display the post expiration date within the post
** Added new setting for the default format
* Fixed bug where expiration date was removed when a post was auto saved

**Version 1.1**

* Expired posts retain expiration date

**Version 1.0**

* Initial Release

== Upgrade Notice ==

= 2.2.0 =
Quick Edit/Bulk Edit Added. Sortable Expiration Date Fields Added

= 2.1.4 =
Fixed PHP Strict errors with 5.4+
Removed temporary timezone conversion functions


= 2.1.3 =
Default category selection now saves correctly on default settings screen

= 2.1.2 =
Important Update - Security Fixes - See Changelog

= 2.0.1 =
Removes old scheduled hook - this was not done completely in the 2.0.0 upgrade

= 2.0.0 =
This is a major update of the core functions of this plugin.  All current plugins and settings should be upgraded to the new formats and work as expected.  Any posts currently schedule to be expirated in the future will be automatically upgraded to the new format.

= 1.6.1 =
Tweaked error messages, added option to allow user to select cron schedule and set default exiration duration

= 1.6 =
Fixed invalid html
Fixed i18n issues with dates
Fixed problem when using "Network Activate" - reworked plugin activation process
Replaced "Upgrade" tab with new "Diagnostics" tab
Reworked expire logic to limit the number of sql queries needed
Added debugging

= 1.5.4 =
Cleaned up deprecated function calls

= 1.5.3 =
Fixed bug with sql expiration query (props to Robert & John)

= 1.5.2 =
Fixed shortcode timezone issue
