=== Drip - Marketing Automation for WooCommerce ===
Contributors: getdrip
Tags: ecommerce, emailmarketing, marketingautomation, emailmarketingautomation, woocommerce, drip
Requires at least: 4.6
Tested up to: 6.3.1
Stable tag: 1.1.7
Requires PHP: 5.6
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Build long-lasting relationships with perfectly personalized email and onsite marketing automation.

== Description ==

Drip is a marketing automation platform that helps WooCommerce stores drive repeat purchases and brand loyalty. Sync your WooCommerce store with Drip and experience all of these loyalty-driving benefits:

- Target people based on purchase history, purchase frequency, and products viewed.
- Insert products from your store directly into visual emails.
- Activate ecommerce workflows like abandoned cart, win-back, welcome series, and more.
- Understand the true impact of your email strategy with revenue reporting.

### Intuitive email builder for highly-deliverable email campaigns.

Build on-brand emails that stand out in every inbox. Use Drip’s point-and-click email builder to add your store products directly into emails, promote top-sellers to newcomers, and send dynamic content like cart URLs.

### Visual workflow builder that runs multi-channel automations.

Customize our WooCommerce-ready workflow templates to match your brand and set them into motion. Automate marketing campaigns across email, Onsite, and social media channels using data from your WooCommerce store.

### Deliver spot-on messages with dynamic segmentation.

Drip’s powerful segmentation combines store, visitor, and marketing data so you can create dynamic segments and connect with your (potential) customers like never before.

### Best-in-class forms, popups, and quizzes to collect emails and zero-party data.

Design onsite journeys that guide first-time visitors toward becoming potential customers. Then convert them into actual customers who turn into repeat fans for life. Enjoy flexibility and customization beyond comparison with our drag-and-drop campaign builder.

### Free migration + unmatched customer support.

Drip’s support team is here for you from day 1 to 1,001.

When you’re ready to make the switch, we’ll migrate all the important stuff from your old platform to Drip, offer personalized advice on how to up your email marketing game, and so much more—at no additional cost.

Install the official Drip for WooCommerce plugin. See why thousands of ecommerce brands across the globe trust Drip to drive repeat purchases and brand loyalty on autopilot.

== FAQs ==

=== Do you offer a free trial? ===

Yes, we offer a free 14-day trial for new users. Sign up for a free trial today (no credit card needed): [https://www.getdrip.com/signup/basic](https://www.getdrip.com/signup/basic)

=== How much does Drip cost? ===

Our plans start at $39/mo. We offer free migration on all plans. Find your monthly cost on our pricing page: [https://www.drip.com/pricing](https://www.drip.com/pricing)

=== How do I install the Drip for WooCommerce plugin? ===

Find installation instructions in our help center: [https://help.drip.com/hc/en-us/articles/4424695659277-Integration-Instructions](https://help.drip.com/hc/en-us/articles/4424695659277-Integration-Instructions)

=== How can I contact Drip support? ===

Our Support Team is available via email between 9 am – 5 pm CST and 8 am – 8 pm CET Monday through Friday at support@drip.com.

=== Are there technical requirements for the plugin? ===

Make sure you are running the latest version of WooCommerce before installing the integration.

=== Development ===

The philosophy behind this plugin is to do as little as possible in it, and as much as possible in a microservice run by Drip. This allows us to ship fixes for our customers without their having to upgrade a plugin. So often a bug will need to be fixed in the microservice rather than in this plugin. If you do indeed find a bug in the plugin, feel free to submit a [Pull Request in our GitHub repo](https://github.com/DripEmail/drip-woocommerce/).

== Changelog ==
= NEXT =

* Your change here!

= 1.1.7 =

* Implement `drip_set_snippet_script_type` and `drip_set_snippet_script_additional_attributes` filters to add custom attributes in Drip's JS Snippet

= 1.1.6 =

* Use a more robust method for detecting product view events

= 1.1.5 =

* Fix bug when calling get_cart function before WP finishes loading

= 1.1.4 =

* HPOS Compatible for new WooCommerce versions

= 1.1.3 =

* Fix a bug that caused an error when viewing a product with an empty price

= 1.1.2 =

* Add occurred_at timestamp to cart events to avoid timing issues when there are queue backups or other issues.
* Fixed bug in viewed a product snippet insertion where we were adding context params when unneeded. (SumoTTo)
* Changed context param to be 'edit' instead of a custom value in a number of places.

= 1.1.1 =

* Fix bug that prevented product image_urls from appearing in "Viewed a product" events

= 1.1.0 =

* Add option to have Email Marketing checkbox selected by default
* Allow for checkbox string to be translated. (BenceSzalai)

= 1.0.4 =

* Fix bug that auto-subscribed everyone just by virtue of placing an order.

= 1.0.3 =

* Fix bug that recorded prices incorrectly on view product events

= 1.0.2 =

* Fix bug that allowed only a small number of items to be added to the cart at one time

= 1.0.1 =

* Fix bug that affected displaying sign up for email marketing during checkout

= 1.0.0 =

* First production-ready release

= 0.0.5 =

* Update default text values for display

= 0.0.4 =

* Add option for subscriber sign-up during the checkout process

= 0.0.3 =

* Allow carts to be associated based on being identified in JS.
* Clarified wording on the purpose of the account id field in the settings.
* Explicitly identify customers upon order completion.
* Officially rename plugin to "drip"

= 0.0.2 =

* Linter fixes to prep for WP Plugin Directory submission.
* Rename from drip-woocommerce to drip.
* Send Viewed a Product events via the JS API.

= 0.0.1 =
* Initial release

== Screenshots ==

1. Dynamic Segmentation
2. Ecommerce Templates
3. Email Builder
4. Multi-Channel Automation
5. Onsite Marketing
