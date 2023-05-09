=== Import and export users and customers ===
Contributors: carazo, hornero
Donate link: https://codection.com/go/donate-import-users-from-csv-with-meta/
Tags: csv, import, export, importer, exporter, meta data, meta, user, users, user meta,  editor, profile, custom, fields, delimiter, update, insert, automatically, cron
Requires at least: 3.4
Tested up to: 6.2
Stable tag: 1.22.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Import and export users and customers using CSV files including custom user meta and roles. Integrations with lots of other plugins. Frontend upload, cron import and much more.

== Description ==

**Try it out on your free dummy site: Click here => [https://demo.tastewp.com/import-users-from-csv-with-meta](https://demo.tastewp.com/import-users-from-csv-with-meta)**

Clean and easy-to-use import and export users and customer plugin, for WordPress and WooCommerce. It includes custom user meta to be included automatically from a CSV file and delimitation auto-detector. It also is able to send a mail to each user imported and all the meta data imported is ready to edit into user profile.

*	Import CSV file with users directly to your WordPress or customers into WooCommerce
*	Import thousends of users or customers in only some seconds
*   Export users or customers to a CSV file, filtering by role or registered date
*	You can also import meta-data like data from WooCommerce customers
*	You can assign roles while importing
*	Send a mail to every new user, this mails can be saved as templates and are fully customizable, before sending you can test it
*	Use your own 
*	You can also update users if the user is already in your WordPress
*	Create a cron task to import users periodically
*	Edit the metadata (you will be able to edit the metadata imported using metakeys directly in the profile of each user)
*	Extend the plugin using the hooks we provide
*   Compatible with WPML [read the documentation](https://wpml.org/documentation/plugins-compatibility/import-users-from-csv-with-meta-and-wpml/) to see how you can translate the front-end import and export users page and send translated email notifications to users

Moreover this plugin is compatible with many other plugins to be able to import and include them data, subscriptions, memberships, etc. Take a look:

*	WooCommerce: to import the customer data
*	WooCommerce Memberships: to import memberships
*	WooCommerce Subscriptions: to create subscriptions associated with users while they are being imported
*	BuddyPress: to import custom BuddyPress avatars, fields, groups and roles
*   Advanced Custom Fields: to import data to the fields you define there
*	Paid Membership Pro: to import memberships
*	Indeed Ultimate Membership Pro: to import memberships
*   Paid Member Subscriptions: to import memberships
*	Allow Multiple Accounts: plugin will allow the same rules importing than this plugin
*	Groups: to assign users to groups while importing
*	New User Approve: you can import users and approbe/wait for approve them
*	Users Group: to assign users to groups while importing
*	WP LMS Course: to enroll users in the courses while importing
*	WP Members: to import memberships
*	WP Users Group: to assign users to groups while importing
*	WooCommerce Membership by RightPress: to create memberships while users are being imported
*   WP Private Content Plus: To import and export the groups to which users are assigned

If you have some problem or doubt:

*	Read our documentation
*	Ask anything in support forum, we try to give the best support

In Codection we have more plugins, please take a look to them.

*	[RedSys Gateway for WooCommerce Pro a plugin to connect your WooCommerce to RedSys](https://codection.com/producto/redsys-gateway-for-woocommerce) (premium)
*	[Ceca Gateway for WooCommerce Pro a plugin to connect your WooCommerce to Ceca](https://codection.com/producto/ceca-gateway-for-woocommerce-pro/) (premium)
*	[RedSys Button for WordPress a plugin to receive payments using RedSys in WordPress without using WooCommerce](https://codection.com/producto/redsys-button-wordpress/) (premium)
*	[RedSys Gateway for Contact Form 7 a plugin to receive payments using RedSys in WordPress using the popular contact plugin Contact Form 7](https://codection.com/producto/redsys-gateway-for-contact-form-7/) (premium)
*	[Ceca Gateway for Contact Form 7 a plugin to receive payments using Ceca in WordPress using the popular contact plugin Contact Form 7](https://codection.com/producto/ceca-gateway-for-contact-form-7/) (premium)
*	[RedSys Gateway for WP Booking Calendar Pro a plugin to receive payments using RedSys in WordPress using WP Booking Calendar Pro](https://codection.com/producto/redsys-gateway-for-wp-booking-calendar-pro/) (premium)
*	[RedSys Gateway for Goodlayers Tourmaster Pro a plugin to receive payments using RedSys in WordPress using Goodlayers Tourmaster Pro](https://codection.com/producto/redsys-gateway-for-goodlayers-tourmaster-pro/) (premium)
*	[Clean Login a plugin to create your own register, log in, lost password and update profile forms](https://wordpress.org/plugins/clean-login/) (free)
*   [Products Restricted Users for WooCommerce a plugin to restrict product visibility by user](https://wordpress.org/plugins/woo-products-restricted-users/) (free)
*   [First payment date for WooCommerce Subscriptions a plugin to set a first payment date in membership sites with WooCommerce Subscriptions](https://wordpress.org/plugins/first-payment-date-for-woocommerce-subscriptions/) (free)
*   [Payment Schedule for WooCommerce Subscriptions](https://import-wp.com/payment-schedule-for-woocommerce-subscriptions) (premium)

### **Basics**

*   Import users and customers from a CSV easily
*   And also extra profile information with the user meta data (included in the CSV with your custom fields)
*   Just upload the CSV file (one included as example)
*   All your users will be created/updated with the updated information, and of course including the user meta
*   Autodetect delimiter compatible with `comma , `, `semicolon ; ` and `bar | `
*	Export users and customers choosing delimiters and using some filters
*	Create a cron task to do the import periodically in order to integrate WordPress with an external system
*	Interaction with lots of other plugins like WooCommerce, BuddyPress, Paid Membership Pro, WooCommerce Memebership, WooCommerce Subscriptions and many others
*	Import users from frontend using a shortcode

### **Usage**

Once the plugin is installed you can use it. Go to Tools menu and there, there will be a section called _Insert users from CSV_. Just choose your CSV file and go!

### **CSV generation**

You can generate CSV file with all users inside it, using a standar spreadsheet software like: Microsoft Excel, LibreOffice Calc, OpenOffice Calc or Gnumeric.

You have to create the file filled with information (or take it from another database) and you will only have to choose CSV file when you "Save as..." the file. As example, a CSV file is included with the plugin.

### **Some considerations**

Plugin will automatically detect:

* Charset and set it to **UTF-8** to prevent problems with non-ASCII characters.
* It also will **auto detect line-ending** to prevent problems with different OS.
* Finally, it will **detect the delimiter** being used in CSV file

== Screenshots ==

1. Plugin link from dashboard
2. Plugin page
3. CSV file structure
4. Users imported
5. Extra profile information (user meta)

== Changelog ==

= 1.22.4 =
*   Fixed a bug in export when all columns were selected

= 1.22.3 =
*   When choosing columns to export, the spaces at the beginning and end of each chosen column are now removed to avoid problems such as "user_login, user_email" not returning data in the second column because the " user_email" column did not exist as such.

= 1.22.2 =
*   Fixed a problem that appeared when you chose to delete users not present in the CSV and at the same time checked do not update users. Users that already existed were not updated and were not marked not to delete, so they were deleted incorrectly.

= 1.22.1 =
*   Addon for MelaPress Login Security improved
*   Transient with users created, updated, deleted and ignored added after import

= 1.22 =
*   Ready for WordPress 6.2
*   New addon for MelaPress Login Security, now when a user is registered and this plugin is activated, if the option of "Reset password in first login" of the MelaPress Login Security is activated, it will be applied the same for new users imported as it were created manually
*   Changed delimiter to import checkbox values in BuddyPress/BuddyBoss, instead of using commas, now the delimiter is ## to avoid problems with values that include commas

= 1.21.7 =
*   New filter added to allow to override the default roles that cannot be updated by the plugin
*   Fixed problem that makes appears title "Extra profile information" when there were no fields to show

= 1.21.6 =
*   Changed the way that BuddyBoss is detected in BuddyPress Addon to avoid problems with paths

= 1.21.5 =
*   New hooks added in documentation
*   Documentation tab improved

= 1.21.4 =
*   Included a new way to manage the passwordreseturl wildcard in emails

= 1.21.3 =
*   Fixed warning about not defined default value in settings class
*   Now the date filter when exporting users is inclusive, so that the days selected in the search are included in the search and not excluded as before
*   Fixed a problem in export that sometimes generate problems different number of columns in headers and in every user
*   Fixed a problem with the keys to reset password of users included in the emails

= 1.21.2 =
*   Improved compatibility with ACF, now the fields of type image or file, generate in the export a new field, with the suffix _url where to show the url of the content and not only the identifier of the attachment in the database as before that was not representative.
*   Improved user interface

= 1.21.1 =
*   Fixed a problem when sending email content, which caused the password to always appear as if it had not been changed even though it really had

= 1.21 =
*   Export results included after export is done, including if some value has been altered because it can contains some spreadsheet formula characters at the beginning
*   You can use now all the wildcards in subjects that were available in body
*   Fixed some issues when exporting data using filtered columns, now source_user_id is filled correctly

= 1.20.6 =
*   Fixed a problem with BuddyBoss when exporting users if groups were getting used

= 1.20.5 =
*   Improved the way the plugin sanitize values when exporting data to avoid any formula (for spreadsheets) to be exported

= 1.20.4 =
*   Improved error handling to avoid the use of wp_die in some error handling and allow for better UX.

= 1.20.3.1 =
*   When choosing a file to import in the frontend import, the name of the file appears, this is something that was lost in yesterday's graphical improvement of this part of the import

= 1.20.3 =
*   Changed the way the button to choose files in the import from front end is displayed to make it easier to apply styles to it
*   Improved error detection when importing users from the front end

= 1.20.2 =
*   Improved section on forcing password reset, including new explanations in the documentation and a tool to reset user data in case a redirection loop problem is encountered

= 1.20.1 =
*   Fixed an issue that caused the new import screen to be displayed as part of the import results screen
*   Improved the way this plugin displays with the other WordPress export tools
*   Notice improved when an email exists in the system with other username

= 1.20 =
*   New settings API in plugin
*   Settings API working in backend import (in other tabs it will be working in next versions)
*   Fixed a mispelling in texts
*   New hook to manage if a email is sent to an user (https://wordpress.org/support/topic/stop-sending-mail-for-a-specific-role/#post-15932034)

= 1.19.3.1 =
*   Added a message when an email already exists in the system but is used by a different user than the one indicated in the CSV
*   Fixed error in documentation when WooCommerce Subscriptions was active

= 1.19.3 =
*   Now you can choose to delete roles when importing (creating or updating) users
*   Fixed problem with select2.js in homepage tab

= 1.19.2.7 =
*   Fixed issue in BuddyPress integration when exporting data

= 1.19.2.6 =
*   Default values in select change to user the "safer" choice when assigning default values

= 1.19.2.5 =
*   Ready for WordPress 6.0
*   Fixed a problem when no selecting a default role (https://wordpress.org/support/topic/default-role-and-update/#post-15626130) and roles was not being updated
*   Roles are now translated when showing

= 1.19.2.4 =
*   Fixed a notice in the new user page if you are using custom fields created by the plugin

= 1.19.2.3 =
*   Improved the WooCommerce addon when working with force reset new password

= 1.19.2.2 =
*   Fixed an issue in the export function when using the BuddyPress addon and not all columns are exported.

= 1.19.2.1 =
*   Escaped all exists to prevent any XSS execution after CSV import

= 1.19.2 =
*   New hooks added to override the button text in both shortcodes import and export
*   New addon for WP Private Content Plus to import and export the groups to which users are assigned
*   Revised WPML support

= 1.19.1.10 =
*   New hook added in export before deleting the file from the server

= 1.19.1.9 =
*   Improved some labels to avoid misunderstandings with email options, thanks to @blakemiller

= 1.19.1.8 =
*   Fixed warnings in "Meta keys" tab

= 1.19.1.7 =
*   New hooks added to homepage tab to include more options using addons

= 1.19.1.6 =
*   New hooks to filter username and password of every users being imported

= 1.19.1.5 =
*   Improved BuddyBoss compatibility, now when we include class-bp-xprofile-group.php, we check if BuddyPress is the plugin active or BuddyBoss

= 1.19.1.4 =
*   Changed appearence of some buttons in the right panel
*   Included new strings to localize 

= 1.19.1.3 =
*   Improved ACF addon, now you can use relationships with IDs in addition to slugs 

= 1.19.1.2 =
*   Fixed fatal error 

= 1.19.1.1 =
*   Fixed warning in screen with import results
*   Ko-fi donation link added

= 1.19.1 =
*   Export now allow to choose which columns export also in "Export" tab and not only in frontend

= 1.19 =
*   New class to create diffent HTML elements to standarize plugin code
*   Different fixes in export function to avoid errors
*   New secondary tab section prepared

= 1.18.4.4 =
*   Force version update

= 1.18.4.3 =
*   New hook added: do_action( 'acui_after_import_users', $users_created, $users_updated, $users_deleted, $users_ignored ); with 3 variables passed with a list of user IDs with users created, updated, deleted and ignored in the process
*   Fixed bad error thrown when empty role was selected in error

= 1.18.4.2 =
*   Fixed bug in batch_exporter when using PHP 8

= 1.18.4.1 =
*   Fixed bug in batch_exporter that could create fatal errors on executing

= 1.18.4 =
*   Improved problem when deleting users, if errors happens but they are notices, we can delete now. Many of the users who have problems with deleting users not present in CSV, was created by the old conditional that checked any kind of error (including notices).

= 1.18.3 =
*   Problem solved converting data that has a format date but that is not wanted to be converted, to timestamps when exporting
*   Fixed problems in standard import, in very big databases, there was a problem creating the list of users to assign deleted posts, now this list is created and managed using select2 and AJAX to improve performance and usability

= 1.18.2.3 =
*   Problem solved converting timestamps when exporting
*   If an error raise in the server while exporting, instead only showing the error in the console, we throws an alert to improve user experience

= 1.18.2.2 =
*   Included a note to prevent misunderstandings when testing emails

= 1.18.2.1 =
*   Tested up to 5.8.1
*   Fixed problem with roles export

= 1.18.2 =
*   New hooks added to manage extra profile fields
*   Problem solved in BuddyPress addon

= 1.18.1 =
*   Fixed problem after 1.18 when exporting users

= 1.18 =
*   Export in backend and frontend now works using step by step process using client calls to avoid gateway timeouts and other kind of timing limits in very long process
*   Addon for WP User Manager improved to avoid redirection loop

= 1.17.9 =
*   Export now can be ordered using an attribute in the shortcode

= 1.17.8.4 =
*   Bug fixed in WP User Manager addon

= 1.17.8.3 =
*   Export shortcode parameter column now also defines the order of the columns

= 1.17.8.2 =
*   Password documentation updated
*   New hooks added for filtering from and to user_registered date in export acui_export_user_registered_from_date and acui_export_user_registered_to_date

= 1.17.8.1 =
*   Ready for WordPress 5.8

= 1.17.8 =
*   Array with string keys now can be imported using this syntax inside your CSV cell: key1=>value1::key2=>value2::key3=>value3
+   Improved the way that "Extra profile information" is shown in users profiles to be able to show arrays without notices

= 1.17.7 =
*   New option in export to prevent problems when exporting serialized values: serialized values sometimes can have problems being displayed in Microsoft Excel or LibreOffice, we can double encapsulate this kind of data but you would not be able to import this data beucase instead of serialized data it would be managed as strings

= 1.17.6.3 =
*   Fixed bug in WP User Manager addon

= 1.17.6.2 =
*   Objects in CSV can now be printed using serialization

= 1.17.6.1 =
*   Force users to reset their passwords is also compatible with WP User Manager forms
*   Improved the way data to replace is searched preparing the emails
*   Improved the way some data is shown to prevent notices from array to string conversions

= 1.17.6 =
*   Now you can filter the columns that are going to be exported using the shortcode and the attribute columns

= 1.17.5.7 =
*   Email templates are being sent translated in the current WPML language if column locale is not set
*   Warning fixed in ACF addon

= 1.17.5.6 =
*   Frontend force reset password fixed

= 1.17.5.5 =
*   Frontend settings GUI improved
*   Force users to reset their passwords after login also available for frontend import
*   Fixed issue created in 1.17.5.4 saving options in frontend when import started

= 1.17.5.4 =
*   Solved this issue https://wordpress.org/support/topic/password-gets-changed/
*   Solved this issue https://wordpress.org/support/topic/users-without-an-email-address-are-imported/
*   Others issues solved

= 1.17.5.3 =
*   You can now force the users to reset their passwords after login if you have changed the password in the import
*   Some code improvements

= 1.17.5.2 =
*   New hooks into shortcode form to enable include actions from there

= 1.17.5.1 =
*   New action class introduced to make easier to use options into the plugin
*   Path to file in homepage tab, now it is saved to prevent to rewrite it in every import

= 1.17.5 =
*   Fixed problems importing avatar from WP User Avatar
*   Avatars using WP User Avatar can now be exported
*   Some code improvements

= 1.17.4.4 =
*   Problems importing BuddyPress Groups solved

= 1.17.4.3 =
*   BuddyPress member type import fixed
*   Little improvement in export GUI

= 1.17.4.2 =
*   Frontend import email now can have a list of custom recipients, different to admin email

= 1.17.4.1 =
*   Process import results shown in a table at the end of process

= 1.17.4 =
*   New shortcode to export users

= 1.17.3.6 =
*   Fixed problem importing ACF multiple select field type, thanks to @lpointet

= 1.17.3.5 =
*   Fixed warning on export

= 1.17.3.4 =
*   Tested up to 5.7
*   New method to fix error when a WP_Error appear into an array, when the array is being printed

= 1.17.3.3 =
*   Improved messages when deleting users not present in CSV
*   Fixed error when a WP_Error appear into an array, when the array is being printed

= 1.17.3.2 =
*   Improved BuddyPress group management when importing, now you can remove users from a group
*   Improved BuddyPress import, now you can use group ids and not only group slugs

= 1.17.3.1 =
*   New filter to override default permission_callback in rest-api method to call cron

= 1.17.3 =
*   New feature added actions, now you can assign posts while you are importing users
*   Code improvements

= 1.17.2.1 =
*   Addon included for WP User Manager - WPUM Groups
*   BuddyPress addon improved

= 1.17.2 =
*   New addon included for WPML
*   Email templates are being sent translated if you use the "locale" column in your CSV, so every user will receive the email translated in their own langauge

= 1.17.1.6 =
*   Warning solved, it appears sometimes importing in strtolower operation over roles

= 1.17.1.5 =
*   Bugs fixed exporting users

= 1.17.1.4 =
*   Roles are always managed as small letters to minimize problems writing them
*   Fixed bug exporting metadata that are objects
*   Included new filter in prepare export value

= 1.17.1.3 =
*   Fixed bug in mail templates when wp_editor is disabled

= 1.17.1.2 =
*   In multisite, default role is subscriber if this is not set

= 1.17.1.1 =
*   In multisite, user is added to current blog with role subscriber if user choose to no update roles but the user does not exist there

= 1.17.1 =
*   New errors, warnings and notices reporting methods
*   DataTable used to improve data visualization

= 1.17.0.4 =
*   Included an addon for LearnDash to explain how to proceed with an import of students 

= 1.17.0.3 =
*   Fixed problem with BuddyPress addon and BP_Groups_Member class

= 1.17.0.2 =
*   New version released

= 1.17.0.1 =
*   Bug fixed importing users

= 1.17 =
*   Many code changes, making it simpler to include more features and make it easier to debug in a future
*   Export bug fixed: the plugin exports an empty role column that breaks the CSV
*   You can now test cron task from the "Cron" settings tab   

= 1.16.4.1 =
*   Fixed problem in "Mail options" that does not allow to remove attachments

= 1.16.4 =
*   You can choose what to do with users that being updated, their email has changed: you can update it, skip this user, or create a new user with a prefix

= 1.16.3.6 =
*   When you are exporting data we scape every first data if it starts with a +, -, =, and @ including a \ to prevent any unwanted formula execution in a spreadsheet that will be working with the CSV

= 1.16.3.5 =
*   New option in standard import to choose if passwords should be updated when updating users

= 1.16.3.4 =
*   Export data can now be ordered alphabetically

= 1.16.3.3 =
*   Extra profile fields now can be used also when registering a new user

= 1.16.3.2 =
*   Username now can be empty, in this case, we generate random usernames
*   Code improvements

= 1.16.3.1 =
*   BuddyPress/BuddyBoss avatar can now be imported
*   Code improvements

= 1.16.3 =
*   Now you can use HTML emails
*	Code improvements

= 1.16.2 =
*	Email sending function created
*   Test email button included

= 1.16.1.5 =
*	Fixed problem importing ACF textarea and other type fields

= 1.16.1.4 =
*	Fixed problem importing ACF text fields

= 1.16.1.3 =
*	BuddyPress member type is now included in the export

= 1.16.1.2 =
*	Usability improve when using delete users not present in CSV (change role options is disabled because they can't run)
*   Performance optimization, if user is deleted, it cannot be tried to change role

= 1.16.1.1 =
*	New wildcards included in emails for WooCommerce: woocommercelostpasswordurl, woocommercepasswordreseturl and woocommercepasswordreseturllink

= 1.16.1 =
*	Multisite check and fix issues
*   Addon to include compatibility included Paid Member Subscriptions by Cozmoslabs thanks to Marian Lowe

= 1.16 =
*	Code is being rewritten to make it easy to update
*   New filter added to override the necessary capatibily to use the plugin

= 1.15.9.2 =
*	We try to make the plugin compatible with BuddyPress related themes and plugins that uses their functions but they are not BuddyPress really
*   Problems exporting file when a site is behind CloudFare fixed

= 1.15.9.1 =
*	You can now export data from BuddyPress groups
*   BuddyPress addon is now a class instead of different methods

= 1.15.9 =
*	You can now export data from BuddyPress fields

= 1.15.8.1 =
*	Added shipping_phone as non date key to avoid datetime conversion

= 1.15.8 =
*	REST API endpoint added to execute cron calling the site remotely

= 1.15.7.4 =
*	Problems with apostrophes solved

= 1.15.7.3 =
*	Tested up to WordPress 5.5
*	Improvements in timestamp data export

= 1.15.7.2 =
*	UTF-8 fixed exporting users

= 1.15.7.1 =
*	Bug fixed: after importing new customers, new WooCommerce tables was not populated properly and they were not shown in the "Customers" list into WooCommerce, thanks for reporting @movementoweb (https://wordpress.org/support/topic/usuarios-importados-con-rol-de-cliente-customer-no-se-muestran-en-woocommerce/)

= 1.15.7 =
*	Addon included to import data defined by WooCommerce Custom Fields by Rightpress

= 1.15.6.8 =
*	Cron import fixed. It failed because of get_editable_roles was not declared in cron import
*	Check if role exists in order to show a better message when importing

= 1.15.6.7 =
*	A non-user admin could delete himself automatically deleting users not present in CSV (thanks again to @nonprofitweb https://wordpress.org/support/topic/non-admin-user-can-delete-self/#post-12950734)
*	Improved "Users only can import users with a role that they allowed to edit" thanks also to @nonprofitweb
*	Forms has now classes to be able to customize the way they are shown using CSV thanks again to @nonprofitweb

= 1.15.6.6 =
*	Added multiple hooks to filter all about emails being sent when importing
*	Included new variables in hooks that already exists in emails being sent when importing

= 1.15.6.5 =
*	Users only can import users with a role that they allowed to edit (thanks to @nonprofitweb https://wordpress.org/support/topic/import-user-with-higher-role/)

= 1.15.6.4 =
*	Now you can use variables also in Subject, thanks to @vbarrier (https://wordpress.org/support/topic/use-variables-also-in-subject/)

= 1.15.6.3 =
*	Problems with roles being updated that should not be updated in multisite fixed

= 1.15.6.2 =
*	Export checkbox included to avoid conversion to date format to prevent problem with some data converted that should not be converted

= 1.15.6.1 =
*	ACF addon now append values instead of replacing it

= 1.15.6 =
*	ACF compatibility included

= 1.15.5.13 =
*	var_dump forgotten
* 	Up to version updated

= 1.15.5.12 =
*	Bug fixed in BuddyPress importer
*	Little improvement in extra profile fields

= 1.15.5.11 =
*	Deletion process performance improved
*	Now you can specify if only want to delete users of specified role using a new attribute in the frontend import

= 1.15.5.10 =
*	Extra profile fields now can be reseted

= 1.15.5.9 =
*	Array to string conversion fixed in emails being sent
*	Problems importing data from WooCommerce Membership fixed (thanks to grope-ecomedia)

= 1.15.5.8 =
*	Export improved to avoid more data to be exported as date
*	Tested up to WordPress 4.0

= 1.15.5.7 =
*	List of hooks created and included

= 1.15.5.6 =
*	In export, now user id is called "source_user_id" to avoid problems importing

= 1.15.5.5 =
*	Groups can be now imported by their name instead only of their ids

= 1.15.5.4 =
*	Bug fixed in frontend import, roles being updated when it shouldn't be updated thanks to @widevents for reporting (https://wordpress.org/support/topic/change-role-of-users-that-are-not-present-in-the-csv-works-without-ckeckbox-on/)

= 1.15.5.3 =
*	Email notification can be sent to administrator of the website when someone use the frontend importer
*	Code improvement

= 1.15.5.2 =
*	Bug found on export fixed

= 1.15.5.1 =
*	MailPoet addon included (in previous we forgot to include the file)

= 1.15.5 =
*	MailPoet addon added to include users in list when they are being imported

= 1.15.4.4 =
*	New filter to override message when file is bad formed

= 1.15.4.3 =
*	Duplicate email error thrown updating users

= 1.15.4.2 =
*	New filters added

= 1.15.4.1 =
*	Double email fixed

= 1.15.4 =
*   WooCommerce Subscriptions importer included to create subscriptions at the same time you are importing users
*	New hooks added
*	Code improved

= 1.15.3.6 =
*   New shortcut into WordPress importer and exporter default tools to find this one easier

= 1.15.3.5 =
*   Bug fixed in "Mail options"

= 1.15.3.4 =
*	You can now use WordPress default user edited and created emails when importing users
*	WooCommerce Membership by RightPress compatibility included, now you can assign users to their plan while they are being imported

= 1.15.3.3 =
*	WooCommerce Membership addon improved, now you can only assign users to a plan using the membership_plan_id

= 1.15.3.2 =
*	Bug fixed

= 1.15.3.1 =
*	Role included in export
*   test.csv improved
*	Different code improvements

= 1.15.3 =
*	Multisite compatiility improved now you can assign users to blogs after import

= 1.15.2.3 =
*	Fixes some issues exporting date and time values

= 1.15.2.2 =
*	Part of the code of frontend has been rewritten to improve readibility
*	New options in frontend upload

= 1.15.2.1 =
*	Changed name into repository to describe better what plugin does
*	Frontend shortcode now accepts role parameter

= 1.15.2 =
*	You can now select which delimiter is used in the CSV exports and also you can choose the date time format in date time and timestamps date

= 1.15.1.1 =
*	You can now filter users that are being exported by role and user registered date

= 1.15.1 =
*	New tab with a list of all meta keys found in the database, with the type of it and an example to be able to use it in your imports

= 1.15.0.1 =
*	Only users who can create users, can export them

= 1.15 =
*	Export option included

= 1.14.3.10 =
*	Changed the way HTML emails are declared to prevent problems with plugins like WP HTML Mail

= 1.14.3.9 =
*	Included compatibility with WP User Avatar to import avatar images while the import is being executed, thanks to the support of cshaffstall.com

= 1.14.3.8 =
*	user_login and show_admin_bar_front are included as WordPress core fields to avoid showing it as custom meta_keys thanks to 
Michael Finkenberger

= 1.14.3.7 =
*	New filters added to custom message shown in log or regular import
*	Last roles used are remembered in import, to avoid you have to choose all time different roles

= 1.14.3.6 =
*	Fixed a problem with a nonce that was bad named

= 1.14.3.5 =
*	Removed some tags when printing log in cron job
*	Improved error message with Customer Area Addon

= 1.14.3.4 =
*	Fixed other problem thanks to @alexgav (https://wordpress.org/support/topic/issue-in-cron-import-tab/)

= 1.14.3.3 =
*	Fixed some problems thanks to @alexgav (https://wordpress.org/support/topic/issue-in-cron-import-tab/)

= 1.14.3.2 =
*	Added CSS to fix table mobile view

= 1.14.3.1 =
*	Problems uploading users from fronted fixed

= 1.14.3 =
*	Filter added to fix CSV files upload problems

= 1.14.2.12 =
*	Typos fixed thanks to https://wordpress.org/support/topic/typo-in-the-settings-page/

= 1.14.2.11 =
*	Problem using "Yes, add new roles and not override existing one" was fixed

= 1.14.2.10 =
*	Change period was not working if you did not deactivate first the cron job, now it is solved and you can do it without deactivating cron job

= 1.14.2.9 =
*	Role default problem fixed thanks for all the one who notice the bug

= 1.14.2.8 =
*	Addon to import groups of Customer Area Managed Area included

= 1.14.2.7 =
*	Removed old code parts from SMTP settings that now are not available and could create warnings

= 1.14.2.6 =
*	Problem fixed deleting old CSV files

= 1.14.2.5 =
*	Problem fixed in cron job

= 1.14.2.4 =
*	HTML problems fixed

= 1.14.2.3 =
*	Global variable with url of plugin removed

= 1.14.2.2 =
*	More nonces included

= 1.14.2.1 =
*	Directory traversal attack prevented

= 1.14.2 =
*	Authenticated Media Deletion Vulnerability fixed in acui_bulk_delete_attachment
*	Nonces incorporated in different AJAX and forms to improve security
*	Media type of media deleted check to avoid problems deleting files
*	SMTP configuration removed completely, we recommend to use a SMTP plugin if you need it in the future, this part was deprecated some versions ago
*	plugins_url() now is well called so images, files and other assets will be shown properly in all cases
*	Data is sanitized always to prevent security and user problems

= 1.14.1.3 =
*	XSS problem fixed when displaying data imported

= 1.14.1.2 =
*	New Spanish hosting partner included
*	Link added to new review

= 1.14.1.1 =
*	We have changed some empty() check to === '' check, to avoid problems with values like blank spaces (thanks to https://wordpress.org/support/topic/not-importing-fields-with-just-spaces/#post-11597406)

= 1.14.1 =
*	Compatibility with Groups plugin (https://es.wordpress.org/plugins/groups/)

= 1.14.0.9 =
*	WP-CLI does not manage our previous version
*	New tab "New features" added

= 1.14.0.8.1 =
*	Bug fixed

= 1.14.0.8 =
*	**passwordreseturllink** shows reset password url with a link in HTML

= 1.14.0.7 =
*	Filter improved to avoid strange characters in emails

= 1.14.0.6 =
*	Notice fixed from last change

= 1.14.0.5 =
*	Role now is not required when importing

= 1.14.0.4 =
*	Fix to save email options bug (that appeared in last version)

= 1.14.0.3 =
*	Security fixes to prevent Reflected Cross Site Scripting (XSS) and Cross Site Request Forgery (CSRF), thanks to Application Security for reporting

= 1.14.0.2 =
*	get_users used memory improved filtering fields returned, thanks to @shortcutsolutions (https://wordpress.org/support/topic/import-page-no-longer-has-submit-button/#post-11309862)

= 1.14.0.1 =
*	Echo removed from class to prevent message on activation

= 1.14 =
*	Options management improved
*	GUI improved
*	Now you can change the role of users that are not in the CSV file thanks to California Advocates Management Services

= 1.13.2 =
*	Attachments in email templates and mail options now can be deleted thanks to Joel Frankwick

= 1.13.1 =
*	Email templates loads also the attachment in Mail options when they are selected thanks to Joel Frankwick

= 1.13 =
*	Now you can delete users that are not in the CSV file, not only when you are doing an import based on a cron task, but also when you do it from the dashboard or with the shortcode in the frontend thanks to mojosolo.com
*	Documentation improved
*	Bug fixed
*	Tested up to 5.1

= 1.12.6.2 =
*	Notices fixed
*	Some file deleted and some urls fixed

= 1.12.6.1 =
*	Plugin is now compatible with plugins that change login url, thanks to @2candela2 (https://wordpress.org/support/topic/make-it-compatible-with-plugins-that-change-login-url/)

= 1.12.6 =
*	wpml-config.xml added to improve compatibility with WPML
*	Warnings fixed

= 1.12.5.1 =
*	Fixed some files that were not included in the trunk

= 1.12.5 =
*	New addon added thanks to @egraznov in order to make possible to import data from LifterLMS

= 1.12.4 =
*	New hooks added to make possible to include new tabs from an addon

= 1.12.3.1 =
*	Fatal error fixed in frontend tab

= 1.12.3 =
*	Integration with Paid Membership Pro improved thanks to @joneiseman (https://wordpress.org/support/topic/import-paid-membership-pro-fields-not-working/#post-11110984)

= 1.12.2.3 =
*	Readme updated

= 1.12.2.2 =
*	Readme fixed

= 1.12.2.1 =
*	SMTP settings removed, old link was yet placed and caused misunderstandings, thanks to @paulabender for the notice
*	Tested up to WordPress 5.0.3

= 1.12.2 =
*	Extra check to avoid problems with CSV bad formed
*	Plugin can now manage attachments in email sending and email templates thanks to Immersedtechnologies
*	Part of the code has been rewritten using classes
*	We have changed the way of detecting the delimiter (thanks to @chaskinsuk https://wordpress.org/support/topic/detect-delimiter-fails-with-serialized-data/)

= 1.12.1 =
*	Filter added to avoid script inside values of each cells to prevent XSS attacks, thanks for reporting Slawek Zytko

= 1.12 =
*	Plugin can now manage email templates for managing mails which are sent to users thanks to Immersedtechnologies

= 1.11.3.17 =
*	Documentation improved with some notes about roles management thanks to @stephenfourie (https://wordpress.org/support/topic/user-role-always-set-as-administrator/)

= 1.11.3.16 =
*	Redeclaration of str_getcsv removed, this is not necessary because of all new PHP versions contains it

= 1.11.3.15 =
*	Filters included for auto password generated
*	Tested up to WordPress 5.0

= 1.11.3.14 =
*	Empty email check added thanks to @malcolm-oph (https://wordpress.org/support/topic/blank-email-field-in-csv-data-not-detected/)

= 1.11.3.13 =
*	Mail address with data of users can now be overriden thanks to a new filter

= 1.11.3.12 =
*	Plugin is now compatible with Vimeo Sync Membership thanks to Justin Snavely

= 1.11.3.11 =
*	Now you can use the WordPress loaded schedules in the cron import instead of the three default one thanks to PM2S
*	Mail cron sending fixed issues

= 1.11.3.10 =
*	New hooks added thanks to Joel Frankwick in order to make possible to change default wp_mail() headers

= 1.11.3.9 =
*	New hooks added thanks to @malcolm-oph (https://wordpress.org/support/topic/using-filters-to-add-data-columns/)

= 1.11.3.8.1 =
*	Fixed bug thanks to @xenator for discovering the bug (https://wordpress.org/support/topic/uncaught-error-while-importing-users/#post-10618130)

= 1.11.3.8 =
*	Fixed mail sending in frontend import
*	Now you can activate users with WP Members in frontend import
*	Some fixes and warnings added

= 1.11.3.7 =
*	Fixes and improvements thanks to @malcolm-oph

= 1.11.3.6 =
*	Role import working in cron jobs

= 1.11.3.5 =
*	SMTP tab hidden for user which are not using this option

= 1.11.3.4 =
*	Bug fixed: thanks to @oldfieldmike for reporting and fixing a bug present when BuddyPress was active (https://wordpress.org/support/topic/bp_xprofile_group/#post-10265833)

= 1.11.3.3 =
*	Added compatibility to import levels from Indeed Ultimate Membership Pro
*	Fixed role problems when importing

= 1.11.3.2 =
*	Patreon link included and some other improvements to make easier support this develop
*	Deprecated notices included about SMTP settings in this plugin

= 1.11.3.1 =
*	Thanks to Sebastian Mellmann(@xenator) a bug have been solved in password management in new users

= 1.11.3 =
*	Thanks to @xenator you can now import users with Allow Multiple Accounts with same Mail via cron

= 1.11.2 =
*	Problem with WordPress default emails fixed

= 1.11.1 =
*	Sidebar changed
*	Readme completed

= 1.11 =
*	You can now import users from the frontend using a shortcode thanks to Nelson Artz Group GmbH & Co. KG

= 1.10.13 =
*	You can now import User Groups (https://wordpress.org/plugins/user-groups/) and assign them to the users

= 1.10.12 =
*	You can now import WP User Groups (https://es.wordpress.org/plugins/wp-user-groups/) and assign them to the users thanks to the support of Arturas & Luis, Lda.

= 1.10.11.1 =
*	Debug notice shown fixed (thanks for submiting the bug @anieves (https://wordpress.org/support/topic/problem-using-wp-members-with-import-users-from-csv-2/#post-10035037)

= 1.10.11 =
*	Administrator are not deleted in cron task
*	Some hashed passwords was not being imported correctly because of wp_unslash() function into wp_insert_user(), issue fixed

= 1.10.10 =
*	Thanks to Attainable Adventure Cruising Ltd now the system to import passwords hashed directly from the CSV has been fixed
*	Thanks to Kevin Price-Ward and Peri Lane now the system does not include the default role when creating a new user
*	Plugin tested up to WordPress 4.9.4

= 1.10.9.1 =
*	Thanks to @lucile-agence-pulsi for reporting a bug (https://wordpress.org/support/topic/show-extra-profile-fields/) now this is solved

= 1.10.9 =
*	Thanks to the support of Studio MiliLand (http://www.mililand.com) we can now import data to Paid Membership Pro Plugin

= 1.10.8.2 =
*	Thanks to @Carlos Herrera we can now import date fields from BuddyPress

= 1.10.8.1 =
*	Bug fixed

= 1.10.8 =
*	New system for include addons
* 	You can now import data from WooCommerce Membership thanks to Lukas from Kousekmusic.cz
*	Tested up to WordPress 4.9

= 1.10.7.5 =
* 	Bug solved in cron import, now mails not being sent to user who are being updated unless you activate those mails

= 1.10.7.4 =
* 	Plugin now remember if user has selected or not mail sending when doing a manual import, to select by default this option next time

= 1.10.7.3 =
* 	Some of the plugins options are disabled by default to prevent unwanted mail sending

= 1.10.7.2 =
* 	Improve email notification disable

= 1.10.7.1 =
* 	Sending mail in standard import bug solved, thanks to @manverupl for the error report.

= 1.10.7 =
*	New feature thanks to Todd Zaroban (@tzarob) now you can choose if override or not current roles of each user when you are updating them
*	Problem solved in repeated email module thanks to @damienper (https://wordpress.org/support/topic/error-in-email_repeated-php/)
* 	Problem solved in mail sending with cron thanks to @khansadi (https://wordpress.org/support/topic/no-email-is-sent-to-new-users-when-created-via-corn-import/)

= 1.10.6.9 =
*	Thanks to Peri Lane from Apis Productions you can now import roles from CSV. Read documentation to see the way to work.

= 1.10.6.8.1 =
*	Thanks to @fiddla for debugging all this, as update_option with a value equals to true is saved as 1 in the database, we couldn't use the ==! or === operator to see if the option was active or not. Sorry for so many updates those days with this problems and thanks for the debugging

= 1.10.6.8 =
*	Bug fixed (now yes) when moving file including date and time thanks to @fiddla

= 1.10.6.7 =
*	Bug fixed when moving file including date and time

= 1.10.6.6 =
*	Bug fixed thanks to @ov3rfly (https://wordpress.org/support/topic/wrong-path-to-users-page-after-import/)
*	Documentation also included in home page of the plugins thanks to suggestions and threads in forum

= 1.10.6.5 =
*	If multisite is enabled it adds the user to the blog thanks to Rudolph Koegelenberg
*	Tested up to 4.8

= 1.10.6.4 =
*	Documentation fixed: if user id is present in the CSV but not in the database, it cannot be used to create a new user

= 1.10.6.3 =
*	New hook added do_action('post_acui_import_single_user', $headers, $data, $user_id );

= 1.10.6.2 =
*	Added documentation about locale and BuddyPress Extendend Profile
*	Header changed to avoid any problem about plugin header

= 1.10.6.1 =
*	Fix error in importer.php about delete users (https://wordpress.org/support/topic/wp_delete_user-undefined/#post-8925051)

= 1.10.6 =
*	Now you can hide the extra profile fields created with the plugin thanks to Steph O'Brien (Ruddy Good)

= 1.10.5 =
*	Now you can import list of elements using :: as separator and it can also be done in BuddyPress profile fields thanks to Jon Eiseman
*	Fixes in SMTP settings
*	SMTP settings now is a new tab

= 1.10.4 =
*	Now you can assign BuddyPress groups and assign roles in import thanks to TNTP (tntp.org)
* 	Import optimization
*	Readme fixed

= 1.10.3.1 =
*	Bug fixed in SMTP settings page

= 1.10.3 =
*	Plugin is now prepared for internacionalization using translate.wordpress.org

= 1.10.2.2 =
*	German translation fixed thanks to @mfgmicha
*	locale now is considered a data from WordPress user so it won't be shown in profiles

= 1.10.2.1 =
*	German translation fixed thanks to @barcelo
*	System compatibility updated

= 1.10.2 =
* 	New User Approve support fixed thanks to @stephanemartinw (https://wordpress.org/support/topic/new-user-approve-support/#post-8749012)

= 1.10.1 =
* 	Plugin can now import serialized data.
*	New filter added: $data[$i] = apply_filters( 'pre_acui_import_single_user_single_data', $data[$i], $headers[$i], $i); now you can manage each single data for each user, maybe easier to use than pre_acui_import_single_user_data


= 1.9.9.9 =
* 	Now you can automatically rename file after move it. Then you won't lost any file you have imported (thanks to @charlesgodwin)

= 1.9.9.8 =
* 	Password bug fixed. Now it works as it should (like it is explained in documentation)

= 1.9.9.7 =
* 	Bug fixed in importer now value 0 is not considered as empty thanks to @lafare (https://wordpress.org/support/topic/importing-values-equal-to-0/#post-8609191)

= 1.9.9.6 =
* 	From now we are going to keep old versions available in repository
*	We don't delete loaded columns (and fields) when you deactivate the plugin
*	Password is not auto generated when updating user in order to avoid problems (missing password column and update create new passwords and use to create problems)

= 1.9.9.5 =
*	Now you can set the email to empty in each row thanks to @sternhagel

= 1.9.9.4 =
*	German language added thanks to Wolfgang Kleinrath
*	Added conditional to avoid error on mail sending

= 1.9.9.3 =
*	Now you can choose if you want to not assign a role to users when you are making an import cron

= 1.9.9.2 =
*	Now you can choose if you want to assign to some user the posts of the users that can be deleted in cron task

= 1.9.9.1 =
*	French translation added thanks to @momo-fr

= 1.9.9 =
*	Plugin now is localized using i18n thanks to code provided by Toni Ginard @toniginard

= 1.9.8.1 =
*	Bug fixed in cron import, nonce conditional check, thanks to Ville Kokkala for showing the bug

= 1.9.8 =
*	Password reset url is now available to include in body email thanks to Mary Wheeler (https://wordpress.org/support/users/ransim/)

= 1.9.7 =
*	Thanks to Bruce MacPherson we can now choose if we don't want update users roles when importing data if user exist
*	Clearer English thanks to Bruce MacPherson

= 1.9.6 =
*	Thanks to Jason Lewis we can now choose if we don't want update users when importing data if user exist

= 1.9.5 =
*	Important security fixes added thanks to pluginvulnerabilities.com

= 1.9.4.6 =
*	New filter added, thanks to @burton-nerd

= 1.9.4.5 =
*	Renamed function to avoid collisions thanks to the message of Jason Lewis

= 1.9.4.4 =
*	Fix for the last one, we set true where it was false and vice versa

= 1.9.4.3 =
*	We try to make it clear to choose if mails (the one we talked in 1.9.4.2) are being sent or not

= 1.9.4.2 =
*	Automatic WordPress emails sending deactivated by default when user is created or updated, thanks to Peter Gariepy

= 1.9.4.1 =
*	wpautop added again

= 1.9.4 =
*	user_pass can be imported directly hashed thanks to Bad Yogi

= 1.9.3 =
*	Now you can move file after cron, thanks to Yme Brugts for supporting this new feature

= 1.9.2 =
*	New hook added, thanks to borkenkaefer

= 1.9.1 =
*	Fix new feature thanks to bixul ( https://wordpress.org/support/topic/problems-with-user-xxx-error-invalid-user-id?replies=3#post-8572766 )

= 1.9 =
*	New feature thanks to Ken Hagen - V3 Insurance Partners LLC, now you can import users directly with his ID or update it using his user ID, please read documentation tab for more information about it
*	New hooks added thank to the idea of borkenkaefer, in the future we will include more and better hooks (actions and filters)
*	Compatibility with New User Approve fixed

= 1.8.9 =
*	Lost password link included in the mail template thanks to alex@marckdesign.net

= 1.8.8 =
*	Checkbox included in order to avoid sending mail accidentally on password change or user updated.

= 1.8.7.4 =
*	Documentation updated.

= 1.8.7.3 =
*	Autoparagraph in email text to solve problem about all text in the same line.
*	Tested up to 4.5.1

= 1.8.7.2 =
*	Bug in delete_user_meta solved thanks for telling us lizzy2surge

= 1.8.7.1 =
*	Bug in HTML mails solved

= 1.8.7 =
*	You can choose between plugin mail settings or WordPress mail settings, thanks to Awaken Solutions web design (http://www.awakensolutions.com/)

= 1.8.6 =
*	Bug detected in mailer settings, thanks to Carlos (satrebil@gmail.com)

= 1.8.5 =
*	Include code changed, after BuddyPress adaptations we break the SMTP settings when activating

= 1.8.4 =
*	Labels for mail sending were creating some misunderstandings, we have changed it

= 1.8.3 =
*	Deleted var_dump message to debug left accidentally

= 1.8.2 =
*	BuddyPress fix in some installation to avoid a fatal error

= 1.8.1 =
*	Now you have to select at least a role, we want to prevent the problem of "No roles selected"
*	You can import now BuddyPress fields with this plugin thanks to André Ihlar

= 1.8 =
*	Email template has an own custom tab thanks to Amanda Ruggles
*	Email can be sent when you are doing a cron import thanks to Amanda Ruggles

= 1.7.9 =
*	Now you can choose if you want to send the email to all users or only to creted users (not to the updated one) thanks to Remy Medranda
*	Compatibility with New User Approve (https://es.wordpress.org/plugins/new-user-approve/) included thanks to Remy Medranda

= 1.7.8 =
*	Metadata can be sent in the mail thanks to Remy Medranda

= 1.7.7 =
*	Bad link fixed and new links added to the plugin

= 1.7.6 =
*	Capability changed from manage_options to create_users, this is a better capatibily to this plugin

= 1.7.5 =
*	Bug solved when opening tabs, it were opened in incorrect target
*	Documentation for WooCommerce integration included

= 1.7.4 =
*	Bug solved when saving path to file in Cron Import (thanks to Robert Zantow for reporting)
*	New tabs included: Shop and Need help
*	Banner background from WordPress.org updated

= 1.7.3 =
*	Users which are not administrator now can edit his extra fields thanks to downka (https://wordpress.org/support/topic/unable-to-edit-imported-custom-profile-fields?replies=1#post-7595520)

= 1.7.2 =
*	Plugin is now compatible with WordPress Access Areas plugin (https://wordpress.org/plugins/wp-access-areas/) thanks to Herbert (http://remark.no/)
*	Added some notes to clarify the proper working of the plugin.

= 1.7.1 =
*	Bug solved. Thanks for reporting this bug: https://wordpress.org/support/topic/version-17-just-doesnt-work?replies=3#post-7538427

= 1.7 =
*	New GUI based on tabs easier to use
*	Thanks to Michael Lancey ( Mckenzie Chase Management, Inc. ) we can now provide all this new features:	
*	File can now be refered using a path and not only uploading.
*	You can now create a scheduled event to import users regularly.

= 1.6.4 =
*	Bugs detected and solved thanks to a message from Periu Lane and others users, the problem was a var bad named.

= 1.6.3 =
*	Default action for empty values now is: leave old value, in this way we prevent unintentional deletions of meta data.
*	Included donate link in plugin.

= 1.6.2 =
*	Thanks to Carmine Morra (carminemorra.com) for reporting problems with <p> and <br/> tags in body of emails.

= 1.6.1 =
*	Thanks to Matthijs Mons: now this plugin is able to work with Allow Multiple Accounts (https://wordpress.org/plugins/allow-multiple-accounts/) and allow the possibility of register/update users with same email instead as using thme in this case as a secondary reference to the user as the username.

= 1.6 =
*	Now options that are only useful if some other plugin is activated, they will only show when those plugins were activated
* 	Thanks to Carmine Morra (carminemorra.com) for supporting the next two big features:
*	New role manager: instead of using a select list, you can choose roles now using checkboxes and you can choose more than one role per user
* 	SMTP server: you can send now from your WordPress directly or using a external SMTP server (almost all SMTP config and SMTP sending logic are based in the original one from WP Mail SMTP - https://wordpress.org/plugins/wp-mail-smtp/). When the plugin finish sending mail, reset the phpmailer to his previous state, so it won't break another SMTP mail plugin.
* 	And this little one, you can use **email** in mail body to send to users their email (as it existed before: **loginurl**, **username**, **password**)

= 1.5.2 =
* 	Thanks to idealien, if we use username to update users, the email can be updated as the rest of the data and metadata of the user and we silence the email changing message generated by core.

= 1.5.1 =
* 	Thanks to Mitch ( mitch AT themilkmob DOT org ) for reporting the bug, now headers do not appears twice.

= 1.5 =
* 	Thanks to Adam Hunkapiller ( of dreambridgepartners.com ) have supported all this new functionalities.
*	You can choose the mail from and the from name of the mail sent.
*	Mail from, from name, mail subject and mail body are now saved in the system and reused anytime you used the plugin in order to make the mail sent easier.
*	You can include all this fields in the mail: "user_nicename", "user_url", "display_name", "nickname", "first_name", "last_name", "description", "jabber", "aim", "yim", "user_registered" if you used it in the CSV and you indicate it the mail body in this way **FIELD_NAME**, for example: **first_name**

= 1.4.2 =
* 	Due to some support threads, we have add a different background-color and color in rows that are problematic: the email was found in the system but the username is not the same

= 1.4.1 =
* 	Thanks to Peri Lane for supporting the new functionality which make possible to activate users at the same time they are being importing. Activate users as WP Members plugin (https://wordpress.org/plugins/wp-members/) consider a user is activated

= 1.4 =
* 	Thanks to Kristopher Hutchison we have add an option to choose what you want to do with empty cells: 1) delete the meta-data or 2) ignore it and do not update, previous to this version, the plugin update the value to empty string

= 1.3.9.4 =
* 	Previous version does not appear as updated in repository, with this version we try to fix it

= 1.3.9.3 =
* 	In WordPress Network, admins can now use the plugin and not only superadmins. Thanks to @jephperro

= 1.3.9.2 =
* 	Solved some typos. Thanks to Jonathan Lampe

= 1.3.9.1 =
* 	JS bug fixed, thanks to Jess C

= 1.3.9 =
* 	List of old CSV files created in order to prevent security problems.
* 	Created a button to delete this files directly in the plugin, you can delete one by one or you can do a bulk delete.

= 1.3.8 =
* 	Fixed a problem with iterator in columns count. Thanks to alysko for their message: https://wordpress.org/support/topic/3rd-colums-ignored?replies=1

= 1.3.7 =
* 	After upload, CSV file is deleted in order to prevent security issues.

= 1.3.6 =
* 	Thanks to idealien for telling us that we should check also if user exist using email (in addition to user login). Now we do this double check to prevent problems with users that exists but was registered using another user login. In the table we show this difference, the login is not changed, but all the rest of data is updated.

= 1.3.5 =
* 	Bug in image fixed
*	Title changed

= 1.3.4 =
* 	Warning with sends_mail parameter fixed
*	Button to donate included

= 1.3.3 =
* 	Screenshot updated, now it has the correct format. Thank to gmsb for telling us the problem with screenshout outdated

= 1.3.2 =
* 	Thanks to @jRausell for solving a bug with a count and an array

= 1.3.1 =
* 	WooCommerce fields integration into profile
*	Duplicate fields detection into profile
*	Thanks to @derwentx to give us the code to make possible to include this new features

= 1.3 =
*	This is the biggest update in the history of this plugin: mails and passwords generation have been added.
*	Thanks to @jRausell to give us code to start with mail sending functionality. We have improved it and now it is available for everyone.
*	Mails are customizable and you can choose 
*	Passwords are also generated, please read carefully the documentation in order to avoid passwords lost in user updates.

= 1.2.3 =
*	Extra format check done at the start of each row.

= 1.2.2 =
*	Thanks to twmoore3rd we have created a system to detect email collisions, username collision are not detected because plugin update metadata in this case

= 1.2.1 =
*	Thanks to Graham May we have fixed a problem when meta keys have a blank space and also we have improved plugin security using filter_input() and filter_input_array() functions instead of $_POSTs

= 1.2 =
*	From this version, plugin can both insert new users and update new ones. Thanks to Nick Gallop from Weston Graphics.

= 1.1.8 =
*	Donation button added.

= 1.1.7 =
*	Fixed problems with \n, \r and \n\r inside CSV fields. Thanks to Ted Stresen-Reuter for his help. We have changed our way to parse CSV files, now we use SplFileObject and we can solve this problem.

= 1.2 =
*	From this version, plugin can both insert new users and update new ones. Thanks to Nick Gallop from Weston Graphics.

= 1.1.8 =
*	Donation button added.

= 1.1.7 =
*	Fixed problems with \n, \r and \n\r inside CSV fields. Thanks to Ted Stresen-Reuter for his help. We have changed our way to parse CSV files, now we use SplFileObject and we can solve this problem.

= 1.1.6 =
*	You can import now user_registered but always in the correct format Y-m-d H:i:s

= 1.1.5 =
*	Now plugins is only shown to admins. Thanks to flegmatiq and his message https://wordpress.org/support/topic/the-plugin-name-apears-in-dashboard-menu-of-non-aministrators?replies=1#post-6126743

= 1.1.4 =
*	Problem solved appeared in 1.1.3: sometimes array was not correctly managed.

= 1.1.3 =
*	As fgetscsv() have problems with non UTF8 characters we changed it and now we had problems with commas inside fields, so we have rewritten it using str_getcsv() and declaring the function in case your current PHP version doesn't support it.

= 1.1.2 =
*	fgetscsv() have problems with non UTF8 characters, so we have changed it for fgetcsv() thanks to a hebrew user who had problems.

= 1.1.1 =
*	Some bugs found and solved managing custom columns after 1.1.0 upgrade.
*	If you have problems/bugs about custom headers, you should deactivate the plugin and then activate it and upload a CSV file with the correct headers again in order to solve some problems.

= 1.1.0 =
*	WordPress user profile default info is now saved correctly, the new fields are: "user_nicename", "user_url", "display_name", "nickname", "first_name", "last_name", "description", "jabber", "aim" and "yim"
* 	New CSV example created.
*	Documentation adapted to new functionality.

= 1.0.9 =
*   Bug with some UTF-8 strings, fixed.

= 1.0.8 =
*   The list of roles is generated reading all the roles avaible in the system, instead of being the default always.

= 1.0.7 =
*   Issue: admin/super_admin change role when file is too large. Two checks done to avoid it.

= 1.0.6 =
*   Issue: Problems detecting extension solved (array('csv' => 'text/csv') added)

= 1.0.5 =
*   Issue: Existing users role change, fixed

= 1.0.0 =
*   First release

== Upgrade Notice ==

= 1.0 =
*   First installation

== Frequently Asked Questions ==

= Columns position =

You should fill the first two columns with the next values: Username, Email.

The next columns are totally customizable and you can use whatever you want. All rows must contains same columns. User profile will be adapted to the kind of data you have selected. If you want to disable the extra profile information, please deactivate this plugin after make the import.

= id column =

You can use a column called id in order to make inserts or updates of an user using the ID used by WordPress in the wp_users table. We have two different cases:

*	If id doesn't exist in your users table: WordPress core does not allow us insert it, so it will throw an error of kind: invalid_user_id
*	If id exists: plugin check if username is the same, if yes, it will update the data, if not, it ignores the cell to avoid problems

= Passwords =

We can use a column called "Password" to manage a string that contains user passwords. We have different options for this case:

*	If you don't create a column for passwords: passwords will be generated automatically
*	If you create a column for passwords: if cell is empty, password won't be updated; if cell has a value, it will be used

= Serialized data =

Plugin can import serialized data. You have to use the serialized string directly in the CSV cell in order the plugin will be able to understand it as an serialized data instead as any other string.

= Lists =

Plugin can import lists as an array. Use this separator: :: two colons, inside the cell in order to split the string in a list of items.

= WordPress default profile data =

You can use those labels if you want to set data adapted to the WordPress default user columns (the ones who use the function wp_update_user)

*	user_nicename: A string that contains a URL-friendly name for the user. The default is the user's username.
*	user_url: A string containing the user's URL for the user's web site.
*	display_name: A string that will be shown on the site. Defaults to user's username. It is likely that you will want to change this, for both appearance and security through 	*	obscurity (that is if you don't use and delete the default admin user).
*	nickname: The user's nickname, defaults to the user's username.
* 	first_name: The user's first name.
*	last_name: The user's last name.
*	description: A string containing content about the user.
*	jabber: User's Jabber account.
*	aim: User's AOL IM account.
*	yim: User's Yahoo IM account.
*	user_registered: Using the WordPress format for this kind of data Y-m-d H:i:s.

= Multiple imports =

You can upload as many files as you want, but all must have the same columns. If you upload another file, the columns will change to the form of last file uploaded.

= Export =

You can export a file with all your users data using "Export" tab. There you will be able to find some filters and options to prepare your export.

= Hooks: actions and filter =
If you want to extend this plugin or use this plugin with any other, [here you have a list with all hooks available in the plugin](https://codection.com/import-users-csv-meta/listado-de-hooks-de-import-and-exports-users-and-customers/).

= This plugin saved me a lot of time and work. Where can I donate? =

Thanks, donations help us to continue improving our plugins and allow us to give the best support in the forums [Donate Here via PayPal.](https://codection.com/go/donate-import-users-from-csv-with-meta/)     

= I'm not sure I will be able to import all users with correct data and roles. Will you do it for me? =

Of course! In Codection we help you to import, migrate, synchronized, update or any other operation you will need to do with your users. Contact us at contacto@codection.com for more information.

= Free and premium support =

You can get:

*	Free support [in WordPress forums](https://wordpress.org/support/plugin/import-users-from-csv-with-meta)
*	Premium support [writing directly to contacto@codection.com](mailto:contacto@codection.com).

= Customizations, addons, develops... =
[Write us directly to contacto@codection.com](mailto:contacto@codection.com).

== Installation ==

### **Installation**

*   Install **Import and export users and customers** automatically through the WordPress Dashboard or by uploading the ZIP file in the _plugins_ directory.
*   Then, after the package is uploaded and extracted, click&nbsp;_Activate Plugin_.

Now going through the points above, you should now see a new&nbsp;_Import users from CSV_&nbsp;menu item under Tool menu in the sidebar of the admin panel, see figure below of how it looks like.

[Plugin link from dashboard](http://ps.w.org/import-users-from-csv-with-meta/assets/screenshot-1.png)

If you get any error after following through the steps above please contact us through item support comments so we can get back to you with possible helps in installing the plugin and more.

Please read documentation before start using this plugin.
