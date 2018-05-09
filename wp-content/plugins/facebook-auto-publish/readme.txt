=== WP Facebook Auto Publish ===
Contributors: f1logic
Donate link: https://xyzscripts.com/donate/
Tags:  facebook, facebook auto publish,wp facebook auto publish,publish post to facebook, add link to facebook, facebook publishing, post to facebook, post to fb, social media auto publish, social media publishing, social network auto publish, social media, social network
Requires at least: 3.0																				
Tested up to: 4.9.5
Stable tag: 1.4.9
License: GPLv2 or later

Publish posts automatically to Facebook page or profile.

== Description ==

A quick look into WP Facebook Auto Publish :

	★ Publish message to Facebook with image
	★ Attach post or share link  to Facebook
	★ Filter items  to be published based on categories
	★ Filter items to be published based on custom post types
	★ Enable or disable wordpress page publishing
	★ Customizable  message formats for Facebook


= WP Facebook Auto Publish Features in Detail =

The WP Facebook Auto Publish lets you publish posts automatically from your blog to Facebook. You can publish your posts to Facebook as simple text message, text message with image or as attached link to your blog. The plugin supports filtering posts based on  custom post-types as well as categories.

The prominent features of  the WP Facebook Auto Publish plugin are highlighted below.

= Supported Mechanisms =

The various mechanisms of posting to Facebook are listed below. 

    Simple text message
    Text message with image
    Share a link to your blog post
    Attach your blog post
    Post to Facebook profile page
    Post to specific pages on Facebook

= Filter Settings =

The plugin offers multiple kinds of filters for contents to be published automatically.

    Enable or disable publishing of wordpress pages
    Filter posts to be published based on categories
    Filtering based on custom post types

= Message Format Settings =

The supported post elements which can be published are given below.

    Post title 
    Post description
    Post excerpt
    Permalink
    Blog title
    User nicename
    Post ID
    Post publish date


= About =

WP Facebook Auto Publish is developed and maintained by [XYZScripts](https://xyzscripts.com/ "xyzscripts.com"). For any support, you may [contact us](https://xyzscripts.com/support/ "XYZScripts Support").

★ [WP Facebook Auto Publish User Guide](http://help.xyzscripts.com/docs/facebook-auto-publish/user-guide/ "WP Facebook Auto Publish User Guide")
★ [WP Facebook Auto Publish FAQ](http://help.xyzscripts.com/docs/facebook-auto-publish/faq/ "WP Facebook Auto Publish FAQ")

== Installation ==

★ [WP Facebook Auto Publish User Guide](http://help.xyzscripts.com/docs/facebook-auto-publish/user-guide/ "WP Facebook Auto Publish User Guide")
★ [WP Facebook Auto Publish FAQ](http://help.xyzscripts.com/docs/facebook-auto-publish/faq/ "WP Facebook Auto Publish FAQ")

1. Extract `facebook-auto-publish.zip` to your `/wp-content/plugins/` directory.
2. In the admin panel under plugins activate WP Facebook Auto Publish.
3. You can configure the settings from WP Facebook Auto Publish menu. (Make sure to Authorize Facebook application after saving the settings.)
4. Once these are done, posts should get automatically published based on your filter settings.

If you need any further help, you may contact our [support desk](https://xyzscripts.com/support/ "XYZScripts Support").

== Frequently Asked Questions ==

★ [WP Facebook Auto Publish User Guide](http://help.xyzscripts.com/docs/facebook-auto-publish/user-guide/ "WP Facebook Auto Publish User Guide")
★ [WP Facebook Auto Publish FAQ](http://help.xyzscripts.com/docs/facebook-auto-publish/faq/ "WP Facebook Auto Publish FAQ")

= 1. The WP Facebook Auto Publish is not working properly. =

Please check the wordpress version you are using. Make sure it meets the minimum version recommended by us. Make sure all files of the `wp facebook auto publish` plugin are uploaded to the folder `wp-content/plugins/`


= 2. Can I post to Facebook pages instead of profile ? =

Yes, you can select the pages to which you need to publish after authorizing Facebook application.


= 3. How do I restrict auto publish to certain categories ? =

Yes, you can specify the categories which need to be auto published from settings page.


= 4. Why do I have to create applications in Facebook ? =

When you create your own applications, it ensures that the posts to Facebook are not shared with any message like "shared via xxx"


= 5. Which  all data fields can I send to Facebook ? =

You may use post title, content, excerpt, permalink, site title and user nicename for auto publishing.


= 6. Why do I see SSL related errors in logs ? =

SSL peer verification may not be functioning in your server. Please turn off SSL peer verification in settings of plugin and try again.


= More questions ? =

[Drop a mail](https://xyzscripts.com/support/ "XYZScripts Support") and we shall get back to you with the answers.


== Screenshots ==

1. This is the Facebook configuration section.
2. Publishing options while creating a post.

== Changelog ==

= WP Facebook Auto Publish 1.4.9 =
* Removed Facebook deprecated permission 'publish_actions'
* Applied wordpress time format in {POST_PUBLISH_DATE}

= WP Facebook Auto Publish 1.4.8 =
* Plugin name changed to WP Facebook Auto Publish, as per wordpress guidelines 

= Facebook Auto Publish 1.4.7 =
* Fixed facebook authorisation issue
* Minor bug fixes
* UI updated

= Facebook Auto Publish 1.4.6 =
* Added USER_DISPLAY_NAME in message formats
* Minor security issues fixed

= Facebook Auto Publish 1.4.5 =
* Compatibility with facebook API V 2.11 added
* Minor bug fixes

= Facebook Auto Publish 1.4.4 =
* Removed Caption from {POST_CONTENT}
* Fixed Facebook image selection issue for 'Share a link to your blog post' and 'Attach your blog post' 
* Fixed ssl peer verification in wp_remote_get/wp_remote_post calls

= Facebook Auto Publish 1.4.3 =
* Added POST_ID and POST_PUBLISH_DATE in message formats

= Facebook Auto Publish 1.4.2 =
* Fixed facebook boost unavailable issue
* Nonce added
* Prevented direct access to plugin files
* Data validation updated
* App album related issue fixed

= Facebook Auto Publish 1.4.1 =
* utf-8 decoding issue fixed
* Visual composer compatiblity issue fixed
* Minor bugs fixed

= Facebook Auto Publish 1.4 =
* Facebook api updated(requires PHP version 5.4 or higher)

= Facebook Auto Publish 1.3.1 =
* Fixed custom post types autopublish issue	
* Fixed duplicate autopublish issue

= Facebook Auto Publish 1.3 =
* Added option to enable/disable utf-8 decoding before publishing	
* Removed unwanted configuration related to 'future_to_publish' hook
* Removed unwanted setting "Facebook user id"
* Postid added in autopublish logs
* Updated auto publish mechanism using transition_post_status hook
* Open graph meta tags will be prefered for facebook attachments

= Facebook Auto Publish 1.2.4 =
* Added option to enable/disable "future_to_publish" hook for handling auto publish of scheduled posts	
* Added options to enable/disable "the_content", "the_excerpt", "the_title" filters on content to be auto-published
* Resolved issue in fetching facebook pages in settings page (in case of more than 100 pages)
* Inline edit of posts will work according to the value set for "Default selection of auto publish while editing posts/pages" 
* Latest five auto publish logs are maintained

= Facebook Auto Publish 1.2.3 =
* Fixed category display issue
* Removed outdated facebook scopes from authorization

= Facebook Auto Publish 1.2.2 =
* Bug fix for duplicate publishing of scheduled posts

= Facebook Auto Publish 1.2.1 =
* Fixed auto publish related bug in post edit
* Fixed message format bug in auto publish
* Updated Facebook authorization

= Facebook Auto Publish 1.2 =
* Option to configure auto publish settings while editing posts/pages
* General setting to enable/disable post publishing
* Added auto publish for scheduled post
* Fixed issue related to \" in auto publish

= Facebook Auto Publish 1.1.2 =
* Fixed auto-publish of scheduled post

= Facebook Auto Publish 1.1.1 =
* Added compatibility with wordpress 3.9.1
* Facebook API V 2.0 compatibility added
* Compatibility with bitly plugin

= Facebook Auto Publish 1.1 =
* View logs for last published post
* Option to enable/disable SSL peer verification
* Option to reauthorize the application

= Facebook Auto Publish 1.0.2 =
* Bug fixed for &amp;nbsp; in post

= Facebook Auto Publish 1.0.1 =
* Default image fetch logic for auto publish updated.
* Thumbnail image logic updated.

= Facebook Auto Publish 1.0 =
* First official launch.

== Upgrade Notice ==

= Facebook Auto Publish 1.0.1 =
If you had issues  with default image used for auto publishing, you may apply this upgrade.

= Facebook Auto Publish 1.0 =
First official launch.

== More Information ==

★ [WP Facebook Auto Publish User Guide](http://help.xyzscripts.com/docs/facebook-auto-publish/ "WP Facebook Auto Publish User Guide")
★ [WP Facebook Auto Publish FAQ](http://help.xyzscripts.com/docs/facebook-auto-publish/faq/ "WP Facebook Auto Publish FAQ")

= Troubleshooting =

Please read the FAQ first if you are having problems.

= Requirements =

    WordPress 3.0+
    PHP 5.4+ 

= Feedback =

We would like to receive your feedback and suggestions about WP Facebook Auto Publish plugin. You may submit them at our [support desk](https://xyzscripts.com/support/ "XYZScripts Support").
