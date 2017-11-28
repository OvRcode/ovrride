=== Plugin Name ===
Contributors: Collizo4sky
Donate link: https://mailoptin.io
Tags: recaptcha, login, comment form, registration form, security, no captcha recaptcha, buddypress, comments, spam, registration, captcha, spammers, bot, registration
Requires at least: 4.0
Requires PHP: 5.4
Tested up to: 4.8.1
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Protect WordPress login, registration, comment and BuddyPress registration forms with Google's No CAPTCHA reCAPTCHA.

== Description ==

A simple plugin for adding the new No CAPTCHA reCAPTCHA by Google to WordPress login, registration and comment system as well as BuddyPress registration form to protect against spam.

**Features**

*   Option to activate CAPTCHA in login, registration, comment and BuddyPress registration forms.
*   Choose a theme for the CAPTCHA.
*   Auto-detects the user's language.


### Plugins you will like
* **[No CAPTCHA reCAPTCHA for WooCommerce](https://wordpress.org/plugins/no-captcha-recaptcha-for-woocommerce/)**: Protect WooCommerce login, registration and password reset form against spam using Google's No CAPTCHA reCAPTCHA.
* **[ProfilePress](https://wordpress.org/plugins/ppress/)**: A shortcode based WordPress form builder that makes building custom login, registration and password reset forms stupidly simple. [More info here](http://profilepress.net)
* **[MailOptin](https://wordpress.org/plugins/mailoptin/)** - The best WordPress email optin forms, email automation & newsletters plugin in the market.

== Installation ==

Installing No CAPTCHA reCAPTCHA is just like any other WordPress plugin.
Navigate to your WordPress “Plugins” page, inside of your WordPress dashboard, and follow these instructions:

1. In the search field enter **No CAPTCHA reCAPTCHA**. Click "Search Plugins", or hit Enter.
1. Select **No CAPTCHA reCAPTCHA** and click either "Details" or "Install Now".
1. Once installed, click "Activate".

== Frequently Asked Questions ==

Any question? post it in the support forum.

== Screenshots ==

1. Add your reCAPTCHA keys.
2. Select where to activate.
3. Plugin general settings.
4. CAPTCHA in action at comment form
5. CAPTCHA in action at registration form
6. CAPTCHA in action at login form
7. CAPTCHA in action at buddypress registration form

== Changelog ==

= 1.2.1 =
* Fixed bug where logged user couldnt submit comments

= 1.2 =
* Add margin bottom spacing before submit button.
* Changed default error message to "Please confirm you are not a robot"
* Moved captcha before comment submit button.


= 1.1.2 =
* Force delete comments that fails captcha test.

= 1.1.1 =
* Remove overzealous activation check.
* Bump minimum requirement to 4.0

= 1.1 =
* Added integration to BuddyPress registration.

= 1.0.3 =
* allow the plugin to be activated network-wide, and in this case display the settings page on the network admin side.
* make the plugin fully translation-ready, and provide the french translation
* remove warnings that display on first activation, when all options are not set
* list the available language for CAPTCHA from the WordPress language packs. To add another language, simply install a new core language pack. This way, the list will only show the languages you are interested in.

= 1.0.2 =
* Fix error where Captcha could be bypassed by disabling Javascript

= 1.0.1 =
* Fixed header already sent error

= 1.0 =
* Initial commit
