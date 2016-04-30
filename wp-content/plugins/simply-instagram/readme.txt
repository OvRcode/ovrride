=== Simply Instagram ===
Contributors: rollybueno
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BUDCX2S6SJ3ZG
Tags: instagram, instagram images, instagram gallery, photos, widgets, gallery
Requires at least: 3.0
Tested up to: 4.4.1
Stable tag: 1.3.3

Simply Instagram displays your photos from Instagram either using Widget or Short Code.

== Description ==

The Simply Instagram plugin display your Instagram photos in three Endpoints that Instagram offers through shortcode for Post and Page or using Widget. 

This plugin requires your Instagram ID and Access Token. Major update on version 1.1 is the on page Instagram Authorization. After activating the plugin, go to Settings -> Simply Instagram then login to your Instagram account and authorize this plugin to access your Instagram data. If you authorize Simply Instagram, it will retrieve your ID and Access Token. 

This plugin is using prettyPhoto and Masonry.

Features:

* Custom slideshow theme.
* Personalize media viewer.
* Option to use prettyPhoto or built-in media viewer.
* Option to display photo`s statistics, caption and photographer.
* Flexible admin settings.
* Shortcode generator.
* User defined autoplay slideshow.
* Supports short code integration.
* Custom images display.
* Option to display trending photos on Instagram, your current uploads, latest feed, like photos, followers and following.
* Option to display your profile in widget.
* Follow @username function which allows your visitor to follow you in instant.
* Option to display photo caption when using prettyPhoto slideshow. A major drawback for Simply Instagram earlier version is prettyPhoto inability to handle long photo caption.
* User defined animation speed when using slideshow.

Shortcode Documentation( for guidelines only since v1.2.3 has shortcode generator ready in admin settings ):

Attributes:

	1.) Endpoints - Main attributes. Short code will never work with this attribute.
		a.) Users - This endpoint is used to access your Basic Information.
		b.) Media - A global endpoint. It will retrieve media API response.

	2.) Type - Types of which endpoint supports. It must corresponds with endpoints in order to work.
		a.) Self Feed - Retrieve your feed based on given output allowed. It includes photos of person you are following. This type is strictly for "users" endpoint only.
		b.) Recent Media - Retrieve your latest uploads on Instagram based on given output allowed. This type is strictly for "users" endpoint only.
		c.) Likes - Retrieve photos you like/love. This type is strictly for "users" endpoint only.		

	3.) Size - Photo's resolution. As defined by Instagram, it has 3 values:
		a.) Thumbnail - Thumbnail size of photo. Exactly 150 x 150.
		b.) Low Resolution - Exactly 306 x 306. This value is the default for Simply Instagram Wordpress Plugin.
		c.) Standard Resolution - The largest image with highest quality. Dimension is 612 x 612.

Shortcode and Widget samples, please visit these:
<ul>
<li><strong><a href="http://rollybueno.info/simply-instagram-wordpress-plugin-demo/">Demo on displaying your liked photos</a></strong> = This demo will display your latest 20 liked photo across Instagram platform.</li>
<li><strong><a href="http://rollybueno.info/simply-instagram-recent-media-shortcode/">Demo on displaying your photos</a>&nbsp;</strong> = This demo will display your latest uploads.</li>
<li><strong><a href="http://rollybueno.info/simply-instagram-self-feed-shortcode-demo/">Demo on displaying your photo feed</a>&nbsp;</strong> = This demo will display your latest feed of people you are following.</li>
<li><strong><a href="http://rollybueno.info/simply-instagram-currently-popular/">Demo on displaying currently popular photos</a>&nbsp;</strong> = This demo will display all currently popular photos</li>
</ul>
== Screenshots ==

1. Widget with complete bio. Follow and Unfollow button added in v1.2.0.
2. Most popular widget.
3. Recent media widget.
4. Sample using shortcode in page.
5. Sample of Facebook theme gallery.
6. Latest uploads widget.

== Installation ==

Installation is same as usual.

Through server:

1. Unzip and Upload all files to a sub directory in "/wp-content/plugins/".
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Choose either to use Widget or Shortcode
4. Add 'Simply Instagram' widget to Your sidebar via 'Appearance' > 'Widgets' menu in WordPress.
5. Choose your appropriate settings.

Through admin:

1. Upload zip and activate plugin.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Choose either to use Widget or Shortcode
4. Add 'Simply Instagram' widget to Your sidebar via 'Appearance' > 'Widgets' menu in WordPress.
5. Choose your appropriate settings.

== Frequently Asked Questions ==

= How to make this plugin works? =

Simply go to Settings -> Simply Instagram. Click on Instagram Login and authorize "Simply Instagram" application. This plugin works on either Widget or shortcode.

= This plugin only returns blank page =

In the case you have seen blank page output, it simply means that Simply Instagram cannot communicate with Instagram API. In most cases, the source of this error is wrong access token and ID. Make sure you have authorize Simply Instagram to use you account.

= I have set display higher than 20 but only shows 20 photos or below =

If the plugin display lower than 20 photos, it most likely that the endpoint that you are accessing has lower return than your display setting otherwise, Instagram only returns 20 images per request. More than that, it will separate the rest in another return using pagination URL.

= Photo, Followers and Followings stats displayed in two lines =

If you theme's sidebar width is less than 200px, the personal statistics will not fit in one line. Kindly adjust your theme's sidebar width.

=  "Follow @username" button not working after my follower login to Instagram =

If no access token have recorded, your follower( a person who decides to follow your Instagram feed ) will be redirected to secured login page of Instagram in order to get their access token and will be returned to original page. They, however can only use Follow button if access token is present. For the v1.2.0, the follow button is very basic. I am planning to use jQuery ajax for Relationship endpoint very soon. If you have any question, please contact me at plugin homepage.

= "Follow @username" button redirect to Error 404 after my follower login =

When your follower were redirect to 404 page after login to Instagram for following you, the plugin can't read your URL. Kindly add forward slash to your permalinks settings.

= I received an error "the page you're looking for doesn't exist. Try the menu." when authorizing =

If this thing happens, simply because Rollybueno.info( which is my official website ) delay in responding. You can go back to admin menu and retry after few minutes.

== Other Notes ==

For using shortcode, please refer to shortcode documentation. Important tips in using shortcode attributes: 

* All attributes must be in lowercase.
* All two string attributes must use dash( - ) except photo resolution which use unserscore( _ ). Sample "recent-media" or "low_resolution".

Please follow the tips in order the shortcode to work.

If you have patch for the improvement of this plugin or you have suggestion for added features, kindly inform me using Support forum. I'm glad to include that in next release and I will credit you.

== Changelog ==

= 1.3.3 =

* Fix on depreciated constructor class

= 1.3.2 =

* Bug fix on CSS option in admin tab
* Bug fix on image resolution and size
* Drop Single Viewer display option

= 1.3.1 =

* WCAG 2.0 Compatibility
* API response code on admin
* I18n Compatible

= 1.2.7 =

* IMPORTANT: Security update on vulnerable prettyPhoto version
* Updated widget description in html paragraph
* Added jquery tool tipster plugin for better hovering description
* Redesign front-photo class
* Redesign profile photo and follow button
* New widget setting for opening photo directly on Instagram
* Supported video player on prettyPhoto in widget
* New polaroid photo presentation
* New design and fix for single image viewer
* New API caching module

= 1.2.6 =

* Fix logout bug
* Fix pixalated thumbnail

= 1.2.5 =

* Fix inconsistent photo display by prioritizing PHP cURL over wp_remote_get()
* Fix admin interface
* Proper widget decsription
* Updated CSS property for Masonry box and front-photo class
* Add new option for Instagram viewer
* New personalize CSS option control
* Add access token and User ID for debugging request
* Use standard wordpress storing options method for access token and user id

= 1.2.4 =

* Update Media viewer css
* Fix bug on number of photo to be displayed
* Fix bug on “Show Description” option
* Update proper shortcode method

= 1.2.3 =

* Added Masonry.
* Update follow button css.
* Added personalize media viewer.
* Added option to use prettyPhoto or built-in media viewer.
* Added option to display photo?s statistics, caption and photographer.
* Added flexible admin setting.
* Added shortcode generator.
* Added option to display photo caption when using prettyPhoto slideshow. A major drawback for Simply Instagram earlier version is prettyPhoto inability to handle long photo caption.

= 1.2.2 =

* Limit photo description to 200 words to avoid prettyPhoto stop responding.
* Include condition of photo sizes for bandwidth saving ( Patch by AndrewLeCody )

= 1.2.1 =

* Fix error on global variable.
* Fix WP_Error in function

= 1.2.0 =

* Added Follow button

= 1.1.3 =

* Fix error on Return URI mismatch in Instagram API

= 1.1.2 =

* Fix return uri error

= 1.1.1 =

* Added missing images for prettyPhoto slideshow

= 1.1. =

* Updates on Instagram authorization on page. If you are updating lower version than 1.1, you need to relogin in settings page.

= 1.0.3 =

* Add .noConflict() to avoid slideshow error
* Fix "$ is not a function error"

= 1.0.2 =

* Fix Permission error in setting page

= 1.0 =

* First and initial release.