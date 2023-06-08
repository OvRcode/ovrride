=== MetaSlider Pro ===

Requires at least: 4.6
Tested up to: 6.2.2
Stable tag: 2.23.0
Requires PHP: 5.6

Extends MetaSlider, adding features such as video slides, layer slides and include additional CSS.

== Changelog ==

The format is based on [Keep a Changelog recommendations](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

= [2.23.0] - 25 May, 2023 =
* FIXED: PHPCS errors, #134;
* FIXED: Background image link not working, #93;
* FIXED: Lazy load and controls not working for YouTube slides created (without saving) in 2.21 and below, #126;
* FIXED: Controls not working for Vimeo slides created (without saving) in 2.21 and below, #125;
* FIXED: Make width settings optional in JavaScript output for Layer Slides, #136;
* ADDED: POT file, #69;
* ADDED: alt text for thumbnail images, #16;
* ADDED: Display Slide ID next to slide type, #91;
* ADDED: Delete permanently option for trashed slides, #139;
* CHANGED: “Add-on” Pack to just “Pro” in terminology, #94;

= [2.22.0] - 04 May, 2023 =

* ADDED: Support for YouTube Shorts, #28;
* FIXED: Default saved settings when adding a new YouTube slide, #59, #105, #118;
* FIXED: Default saved settings when adding a new Vimeo slide, #115;
* FIXED: Background color in play button on focus in Twenty Twenty-One theme in YouTube slides, #112;
* FIXED: Error when adding youtu.be URLs, #106; 
* CHANGED: Vimeo and YouTube text changes, #108, #106;

= [2.21.0] - 07 Mar, 2023 =

* FIXED: Fixed Preview button error, #87, #85;
* FIXED: Fixed the layer slides not finding local videos, #84;

= [2.20.0] - 01 Dec, 2022 =

* FIXED: Fix Post Feed order by The Events Calendar data, #60;
* FIXED: Fix daily constraint feature under schedule tab, #53;
* CHANGED: Change signature of filter "metaslider_post_feed_args" passing `$slider` as the 5th argument, #50;

= [2.19.1] - 02 Nov, 2022 =

* FIXED: Fixed warning in the admin saying the plugin is not compatible with WP 6.1;
* CHANGED: Update simba-plugin-manager-updated to 1.8.15, and plugin-update-checker to 4.13.*;

= [2.19.0] - 11 Oct, 2022 =

* FIXED: Code review improving output escaping and input data sanitization, #51;
* CHANGED: Update simba-plugin-manager-updated to 1.8.15, and plugin-update-checker to 4.13.*;

= [2.18.8] - 26 Sep, 2022 =

* FIXED: Fix Vimeo thumbnails not updating, #44;
* FIXED: Fix YouTube thumbnails not updating, #35;
* FIXED: Fix error 404, file not found: metaslider-pro/modules/layer/assets/codemirror/mode/xml/xml.js, #43;
* CHANGED: Added and updated translations for ES, FR, and IR (Thanks to @wocmultimedia), #30;

= [2.18.7] - 16 Sep, 2022 =

* FIXED: Fix the auto-hiding slides, #39;

= [2.18.6] - 15 Sep, 2022 =

* FIXED: Fix some Italian language strings;
* FIXED: Fix the Schedule tab after MetaSlider v3.27.9;
* FIXED: Fix the taxonomies tab after MetaSlider v3.27.9;
* FIXED: Fix typos in the readme file;
* FIXED: Fix output escaping on the admin notice;
* FIXED: Fix nonce validation on a few ajax requests;

= [2.18.5] - 30 May, 2022 =

* REMOVED: Remove warning about not tested WP version.
* CHANGED: Bump min WP version to v4.6.
* CHANGED: Bump min PHP version to 5.6.
* CHANGED: Change test with WP 6.0.
* CHANGED: Stick library jquery-tube-player on v2.12.2 to fix conflicts while building the file.
* CHANGED: Update Simba Plugin Manager library from vv1.8.9 to v1.8.14.

= [2.18.4] - 26 Jan, 2022 =

* CHANGED: Test with WP 5.9.

= [2.18.3] - 22 Jul, 2021 =

* FIXED: Removes the playlist parameter that YT no longer accepts

= [2.18.2] - 11 Mar, 2021 =

* CHANGED: Updates "Tested To" number to remove WP warning message.

= [2.18.1] - 25 May, 2022 =

* CHANGED: Updated the tested WordPress version to 6.0;

= [2.18.0] - 05 Aug, 2019 =

* FIXED: Updates UI elements and updates icon set (FA was causing issues on some hosts)

= [2.17.0] - 10 Sep, 2019 =

* ADDED: Adds ability to loop Vimeo videos
* CHANGED: Updates check for plugin file location
* CHANGED: Updates jQuery to work with WP 5.5 (while maintaining backwards compatibility)
* FIXED: Add polyfill for WP4.4 wp_add_inline_script
* FIXED: Fix issue where Vimeo settings weren't saving

* UPGRADE NOTE: You can now loop your Vimeo videos endlessly. A recommended update for all.

= [2.16.0] - 23 Dec, 2019 =

* ADDED: Allow daily time constraints on scheduled slides

= [2.15.2] - 05 Dec, 2019 =

* FIXED: Fixes an issue where YouTube URL wouldn't update properly
* FIXED: Fixes an issue where the "lazy load" option on YouTube remained on

= [2.15.1] - 04 Oct, 2019 =

* ADDED: Allows YouTube to be loaded from a different domain
* CHANGED: Adds various UI and RTL enhancements
* CHANGED: Removes internal options from post feed code snippet list
* FIXED: Fixes an issue where the calendar and time helper don't show
* FIXED: Fixes an issue where the post feed slide would not render on initial add

= [2.15.0] - 17 Oct, 2019 =

* CHANGED: Updates classname on layer container to avoid a CSS conflict
* FIXED: Fixes Vimeo issue when slideshow has autoplay disabled
* FIXED: Updates RTL language styles to address layout breaks
* FIXED: Adds additional attribute required by iOS for background video autoplay
* FIXED: Adds origin fix for YouTube videos loading in iframe

= [2.14.0] - 26 Jul, 2019 =

* ADDED: Adds a CSS manager module to allow users to add custom CSS
* CHANGED: Removes is_admin requirement when saving slides
* FIXED: Fixes scheduling query when another theme plugin alters the initial query

= [2.13.2] - 21 Mar, 2019 =

* CHANGED: Removes some ancient code for compatibility with PHP < 5.1
* FIXED: Fixes a bug where some Vimeo video URLs render wrong video because of the wrong regex used
* FIXED: Fixes a bug where Nivo Slider captions disappear

= [2.13.1] - 20 Mar, 2019 =

* FIXED: Fixes a bug where some users will see an error with Youtube

= [2.13.0] - 19 Mar, 2019 =

* ADDED: Adds lazy loading to YouTube videos
* CHANGED: Updates bundled updater class dependency to latest series (1.6.*)
* FIXED: Updates computed ratio on videos to work with locale settings

= [2.12.0] - 13 Dec, 2018 =

* ADDED: Adds the ability to update a video url
* ADDED: Adds the ability to mute a video on start
* FIXED: Updates various Vimeo functionality to match their API changes
* FIXED: Updates various YouTube functionality to match their API changes
* DEPRECATED: Remove showinfo option to hide title on YouTube video (https://developers.google.com/youtube/player_parameters#release_notes_08_23_2018)

= [2.11.0] - 17 Nov, 2018 =

* FIXED: Fixes issue where some themes would break the slides query
* FIXED: Hides private and password protected post slides by default

= [2.10.1] - 30 Oct, 2018 =

* FIXED: Fixes a bug where some slides were not properly being hidden
* FIXED: Fixes a bug where Vimeo slides report a fatal error

= [2.10.0] - 25 Oct, 2018 =

* ADDED: Adds option to make thumbs more responsive
* CHANGED: Now requires MetaSlider base plugin 3.10.0+
* FIXED: Fixes bug where jQuery $ is not defined

= [2.9.2] - 28 Sep, 2018 =

* FIXED: Fixes bug where some slides would not save properly

= [2.9.1] - 26 Sep, 2018 =

* FIXED: Fixes bug where some slides scheduled with the standalone plugin previously would break
* FIXED: Fixes bug where thumbs and filmstrip would show even when not scheduled

= [2.9.0] - 24 Sep, 2018 =

* ADDED: Adds ability to schedule a slide by day of the week
* ADDED: Adds a clock showing the server time on the schedule tab
* CHANGED: Changes schedule query method to remove slow query
* FIXED: Fixes how plugin handles when original schedule class is found
* FIXED: Fixes bug when adding extra JS parameter hooks

= [2.8.0] - 12 Sep, 2018 =

* ADDED: Adds ability to change layer slide background
* ADDED: Adds schedule functionality
* ADDED: Adds the ability to toggle a slide's visibility
* CHANGED: Improves videos ratio code
* FIXED: Adds specificity to the default selector

= [2.7.3] - 15 Dec, 2017 =

* CHANGED: Fixes Youtube slide markup
* FIXED: Adds and updates text-domain for translation support

= [2.7.2] - 28 Nov, 2017 =

* FIXED: Fixes ability to navigate External Slide tabs

= [2.7.1] - 14 Nov, 2017 =

* ADDED: Allow a slide to be restored after deletion
* ADDED: Attempts to make the UX elements more obvious
* CHANGED: Changes the description and team name
* CHANGED: Turkish translation. Provided by Ali Sabri Gök
* CHANGED: Adds better update handling by checking version numbers
* FIXED: Allow for https image URLs

= [2.7.0] =

* ADDED: Add caption field to External Slides
* CHANGED: New Pro slides will be added as a custom post type

= [2.6.8] =

* ADDED: Add 'allowfullscreen' Vimeo attribute

= [2.6.7] =

* FIXED: Force https vimeo urls
* FIXED: Workaround wptexturize and wpautop by removing && from video JavaScript
* FIXED: Apply Smart Pad to post feed slides
* FIXED: Apply metaslider_flex_slider_image_attributes and metaslider_responsive_slider_image_attributes filters to laye slides

= [2.6.6] =

* FIXED: Fix PHP notice (wp-updates.com check)
* FIXED: Fix black thumbnails (YouTube Player Update)
* FIXED: Allow empty i tags in layer editor
* FIXED: Fix PHP notice (deleting vimeo slide)

= [2.6.5] =

* ADDED: Add metaslider_tubeplayer_protocol filter
* REMOVED: Remove TGM Plugin Activation class
* FIXED: Fix bug with multiple post feed slideshows and filmstrip thumbnails
* FIXED: Fix Vantage bug with YouTube first slide autoplay

= [2.6.4] =

* ADDED: Add white as an option for text color in the layer editor
* FIXED: Fix empty color block showing in the color picker

= [2.6.3] =

* CHANGED: Update TGM Plugin Activation class to latest (Security fix)

= [2.6.2] =

* FIXED: Fix post feed captions

= [2.6.1] =

* ADDED: Add URL parameter to metaslider_layer_video_attributes filter
* ADDED: Add ability to use 'metaslider_post_feed_image' custom field to override featured image
* ADDED: Add metaslider_layer_editor_font_sizes and metaslider_layer_editor_colors filters
* FIXED: Fix conflict with slideshow parameters and post feed slides containing slideshows
* FIXED: Fix post feed slide delete button

= [2.6.0] =

* ADDED: Add change slide image option to vimeo, youtube and layer slides
* ADDED: Video background option added to layer slides

= [2.5.1] =

* FIXED: Fix post feed slide warning
* FIXED: Use $ instead of jQuery in dynamic JS
* FIXED: Allow links to be clicked in layers when a background image url is specified
* FIXED: Fix YouTube slide error with R. Slides

= [2.5.0] =

* ADDED: Add {thumb} template tag to post feed
* ADDED: Add External URL slide type
* CHANGED: Move classes from <img> to <li> for flexslider slideshows
* CHANGED: Change autoload from private to public
* FIXED: Hide events without thumbnails from post feed
* FIXED: Refactor method for generating thumbnails

= [2.4.6] =

* ADDED: Add metaslider_flex_slider_filmstrip_parameters filter
* FIXED: Hide layers until slideshow is ready
* FIXED: Re-crop thumbnails when original image is re-cropped

= [2.4.5] =

* ADDED: Add filter for post feed args - see https://gist.github.com/tomhemsley/100930dafd179bae2d1d
* FIXED: Run post feed content filter before the default tags have been parsed
* FIXED: Fix Responsive Slides second animation flash

= [2.4.4] =

* FIXED: Fix layer background link functionality
* FIXED: Return filtered CSS correctly
* FIXED: Don't clone layers without animation
* FIXED: Fix navigation defaulting to thumbnails

= [2.4.3] - internal =

* ADDED: Add actions for youtube & vimeo iframes

= [2.4.2] =

* FIXED: Show warning in layer editor when no layer is selected
* FIXED: Fix layer editor styling

= [2.4.1] =

* FIXED: Fix downscale only setting
* FIXED: Fix layer scaling initiation code

= [2.4.0] =

* ADDED: Theme Editor refactored: Caption text align added
* ADDED: Theme Editor refactored: Caption border radius added
* ADDED: Theme Editor refactored: Custom Prev / Next arrows added
* ADDED: Theme Editor refactored: Enable or disable arrow / bullets / navigation custom styling
* ADDED: Post Feed slides improved: Custom templates added
* ADDED: Post Feed slides improved: WooCommerce support added
* ADDED: Post Feed slides improved: Filters added for output (in line with standard image slides)
* CHANGED: CKEditor updated to 4.4 (IE11 fixes)
* FIXED: Avoid wpautop errors with Layer Slides
* FIXED: Video slides now use jpeg mime type to avoid getID3 errors
* FIXED: Post Feed slides, call wp_reset_query after thumbnail extraction
* FIXED: Layer Editor: process qTranslate shortcodes
* ADDED: Added 'Loop' options for Flex & Nivo Slider
* FIXED: Fix HTTPS video previews
* FIXED: Check slideshow width and height before launching layer editor
* FIXED: Layer Slide scaling JS extracted to it's own jQuery plugin

= [2.3.2] =

* FIXED: Post Feed: Fix Taxonomy restriction

= [2.3.1] =

* FIXED: Menu Order added to Post Feed Slide
* FIXED: Post Content (With Formatting) option added to Post Feed Slide

= [2.3.0] =

* FIXED: Filmstrip navigation option added (Flex Slider)
* FIXED: Layer Scaling options added

= [2.3-beta] - internal=

* ADDED: Layer Slide background link, SEO options
* CHANGED: Tabbed interface on all slides

= [2.2.8] - internal =

* FIXED: Orderby parameter on Post Feed slides

= [2.2.7] - internal =

* CHANGED: Add List item classes to slide types (flexslider only)

= [2.2.6] - internal =

* CHANGED: Add metaslider_post_feed_caption filter

= [2.2.5] - internal =

* FIXED: Vimeo auto play bug (When first slide is set to autoPlay)

= [2.2.4] =

* FIXED: Allow layers to scale up past 100%

= [2.2.3] =

* FIXED: Post Feed/Nivo Slider captions (for MetaSlider 2.6)

= [2.2.2] =

* FIXED: PHP Warnings

= [2.2.1] =

* FIXED: Invalid CSS

= [2.2.0] =

* ADDED: Auto Play setting for YouTube videos
* ADDED: Auto Play setting for Vimeo videos
* FIXED: Force CKEditor to use 'en' lang files
* FIXED: TGM Plugin activation check for MetaSlider Lite

= [2.1.2] - internal =

* FIXED: WPML: Check 'is_plugin_active' function exists before calling

= [2.1.1] - internal =

* CHANGED: Lang files removed from CKEditor to reduce plugin size
* CHANGED: Images in Layers given a max-width
* FIXED: Fix to work with 'SvegliaT buttons' plugin

= [2.1.0] - internal =

* FIXED: YouTube & Vimeo settings
* FIXED: Reset wp_query after post feed to fix comment setting on page

= [2.0.4] =

* FIXED: Responsive layer scaling

= [2.0.3] =

* FIXED: Strict warning for Walker Class compatibility (Since WP3.6 change)

= [2.0.2] =

* CHANGED: "Title & Excerpt" option added for post feed caption
* FIXED: Responsive slider - Pause Vimeo/YouTube when navigating to next slide

= [2.0.1] =

* CHANGED: Responsive Slides output tidied up for YouTube & Vimeo slides
* FIXED: Vimeo HTTPS
* FIXED: Hover Pause is now compatible with YouTube slides (Flex Slider)
* FIXED: Play/Pause video functionality and Auto Play (Flex Slider)

= [2.0.0] =

* ADDED: Thumbnail navigation for Flex & Nivo Slider
* CHANGED: Pro functionality refactored into 'modules'
* CHANGED: Theme editor CSS output tidied up
* FIXED: YouTube thumbnail date
* FIXED: YouTube videos on HTTPS

= [1.2.2] =

* FIXED: Vimeo slideshows not pausing correctly

= [1.2.1] =

* CHANGED: Post Feed limit changed to 'number' input type
* FIXED: Vertical slides with HTML Overlay not working
* FIXED: YouTube & Vimeo slides not saving on some installations

= [1.2.0] =

* ADDED: WYSIWYG Editor Added to HTML Overlay slides
* ADDED: Plugin localized
* FIXED: Post Feeds now only count posts with featured images set

= [1.1.4] =

* FIXED: Fix for YouTube and Vimeo slides when thumbnail download fails

= [1.1.3] =

* FIXED: Youtube debug removed

= [1.1.2] =

* ADDED: Alt text added to HTML Overlay slide type
* REMOVED: "More Slide Types" menu item removed
* FIXED: PHP Short tag fixed
* FIXED: Theme editor CSS fixed
* FIXED: HTML Validation Fixes

= [1.1.1] =

* FIXED: HTML Overlay bug fixed when slideshow has a single slide

= [1.1.0] =

* ADDED: Theme Editor added
* CHANGED: Vimeo thumbnail loader now uses build in WordPress functionality

= [1.0.1] =

* FIXED: Hide overflow on HTML Slides (to stop animations from 'leaking' into other slides)

= [1.0.0] =

* ADDED: Initial Version
