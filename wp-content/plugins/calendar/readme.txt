=== Calendar ===
Contributors: KieranOShea
Donate link: http://www.kieranoshea.com
Tags: calendar, dates, times, events
Requires at least: 4.3.1
Tested up to: 4.3.1
Stable tag: 1.3.5

A simple but effective Calendar plugin for WordPress that allows you to 
manage your events and appointments and display them to the world.

== Description ==

A simple but effective Calendar plugin for WordPress that allows you to 
manage your events and appointments and display them to the world on your 
website.

Features:

*   Monthly view of events
*   Mouse-over details for each event
*   Events can have a timestamp (optional)
*   Events can display their author (optional)
*   Events can span more than one day
*   Multiple events per day possible
*   Events can repeat on a weekly, monthly (set numerical day), monthly (set textual day) or yearly basis
*   Repeats can occur indefinitely or a limited number of times
*   Easy to use events manager in admin dashboard
*   Sidebar function/Widget to show todays events
*   Sidebar function/Widget to show upcoming events
*   Lists of todays events can be displayed in posts or pages
*   Lists of upcoming events can be displayed in posts or pages
*   Comprehensive options panel for admin
*   Modifiable CSS using the options panel
*   Optional drop down boxes to quickly change month and year
*   User groups other than admin can be permitted to manage events
*   Events can be placed into categories
*   A calendar of events for just one of more categories can be displayed
*   Categories system can be switched on or off
*   Pop up javascript calendars help the choosing of dates
*   Events can be links pointing to a location of your choice
*   Full internationalisation is possible
*   Comaptible with WordPress MU

== Installation ==

The installation is extremely simple and straightforward. It only takes a second.

= Installing =

1. Upload the whole calendar directory into your WordPress plugins directory.
2. Activate the plugin on your WordPress plugins page
3. Configure Calendar using the following pages in the admin panel: **Calendar -> Manage Events**, **Calendar -> Manage Categories**, and **Calendar -> Calendar Options**
4. Edit or create a page on your blog which includes the text `{CALENDAR}` and visit
   the page you have edited or created. You should see your calendar in action.

= Upgrading from 1.2 or later =

1. Deactivate the plugin (you will not lose any events)
2. Remove your current calendar directory from the WordPress plugins directory
2. Upload the whole calendar directory into your WordPress plugins directory.
3. Activate the plugin on your WordPress plugins page
4. Configure Calendar using the following pages in the admin panel: **Calendar -> Manage Events**, **Calendar -> Manage Categories**, and **Calendar -> Calendar Options**
5. Edit or create a page on your blog which includes the text `{CALENDAR}` and visit
   the page you have edited or created page. You should see your calendar in action.

= Upgrading from 1.1 =

1. Deactivate the plugin (you will not lose any events)
2. Remove the Rewrite rules from your .htaccess file that you added 
   when you first installed Calendar.
3. Delete plugins/calendar.php, wp-admin/edit-calendar.php, wp-calendar.php
4. Upload the whole calendar directory into your WordPress plugins directory.
5. Activate the plugin on your WordPress plugins page
6. Configure Calendar using the following pages in the admin panel: **Calendar -> Manage Events**, **Calendar -> Manage Categories**, and **Calendar -> Calendar Options**
7. Edit or create a page on your blog which includes the text `{CALENDAR}` and visit
   the page you have edited or created page. You should see your calendar in action.

= Uninstalling =

1. Deactivate the plugin on the plugins page of your blog dashboard
2. Delete the uploaded files for the plugin
3. Delete the following database tables **wp_calendar**, **wp_calendar_config** and **wp_calendar_categories**
4. Remove the text `{CALENDAR}` from the page you were using to show calendar, or delete that page

== Frequently Asked Questions ==

= Where can I get support for the plugin? =

Support is primarily available on [Kieran O'Shea's forum](http://www.kieranoshea.com/forum/viewtopic.php?f=13&t=10 "Kieran O'Shea's forum"). 
A wealth of questions and answers are available here along with a copy of this FAQ. The support forum is located away from WordPress because 
at the time the original plugin was authored, the WordPress support forums didn't provide an easy way to be notified of posts and manage things. 
Recently things have changed in this regard and so the WordPress support forums for Calendar are also monitored from time to time.

= Where can I request a feature? = 

Please use the support forums mentioned above, adding your request to the feature requests thread

= I like it, can I make a donation? = 

Of course. Simply visit [Kieran O'Shea's website](http://www.kieranoshea.com/forum/viewtopic.php?f=13&t=10 "Kieran O'Shea's website") and use 
the PayPal donate link in the sidebar on the right hand side

= I get 404 errors =   

This issue has been fixed in the new version. Please upgrade.

= I can't add events in the admin panel. I use IE6. =

This issue has been fixed in the new version. Please upgrade.

= My .htaccess file keeps getting over-written =

.htaccess modifications are not required in the new version. Please upgrade.

= How do I put upcoming/todays events in my sidebar? I've enabled them in the admin panel but I can't see them. =

This is because you need to ensure they are visible in your theme first.

To do this under WordPress 2.8 there are a pair of widgets that you can add to your side bar in the admin panel. Position them how you want.

Under WordPress 2.0 or where your theme does not support widgets you need to modify the sidebar.php file of your theme to include the following code as appropriate:
`
<?php echo todays_events(); ?>
<?php echo upcoming_events(); ?>
`

= How do I place an instance of calendar in a post or page? =

You need to place the tag
`{CALENDAR}`
someplace in the post or page using the editor. This tag will be replaced with an instance of Calendar when the page is viewed.

= The calendar doesn't look right with my theme. How can I fix this? =

Unlike the days of version 1.1 you shouldn't find version 1.2 overflows your theme or anything like that, but you might find the font size unsuitable or that the colours aren't appropriate.

If this is the case you can use the Calendar options page to modify the CSS. Every element of Calendar has a CSS class, and different types of table cell (with a day, without etc) have their own CSS class also. These are detailed in the default CSS style sheet.

I cannot help you to make alterations to these yourself, but any online guide concerning CSS should be able to help you with this.

= I've added the todays events and upcoming events widgets (or functions in the case of WordPress 2.0) to my sidebar and switched on the features in the config screen but still can't see them in my sidebar. What is going wrong? =

Are you sure you have added any events or that ones you have added are in the future or today? The todays events widget only shows if there are events today and the upcoming events widget only shows if there are events in the number of future days as specified in the configuration screen.

= I've allowed a user group other than admin to manage events but now they can see and modify the events created by admin. =

This is intentional. Many people requested that they wanted users other than admin to be able to manage events and this is what this feature allows. You must of course trust the group of users you grant access to do sensible things. If they are not doing sensible things, you might want to review their ability to access the manage events screen by changing the user group that can do this.

= I deleted a user but events they had added are still showing but as the admin user. =

This is normal. It was reasoned during development that because events are usually useful to everyone, the deletion of a user doesn't mean that you necessarily want events they added to be deleted also. Because events now have to be associated with a user, deleting a user who added events causes their events to be allocated to the admin user. If you don't want these events anymore then you have to manually delete them. You might find it easier to do this while the user is still active because you will be able to see their events more clearly on the manage events screen.

= I had nice pretty links on version 1.1 which were easy to navigate. Where have they gone in later versions? =

Over 80% of support questions had to do with users not being able to setup their .htaccess file correctly to enable pretty links. Because only pretty links were allowed this prevented them from using Calendar.

I've taken a step in the opposite direction with the later releases and removed pretty links entirely. This permits a one-click install for users. Those with experience will be able to modify the plugin slightly and add .htaccess rules to re-enable pretty permalinks.

Sadly for those that liked the links but don't know how to re-enable them you are out of luck. I don't code this plugin for profit and so I had to think of the time I was spending on support questions rather than features in this particular case. I like pretty links and use them on all my sites, but they are non-trivial to setup and I don't want to be giving lessons every day on what to do rather than getting on with my own work. Sorry.

= Can I remove the link to your website from underneath the calendar? =

Yes. Calendar is released under the GPL which permits you to copy, modify and redistribute the plugin as you see fit after you have downloaded it. So long as credit to me, Kieran O'Shea, is maintained within the source then you can remove the link and make any other changes you want.

Please be aware however that I will only provide support to users who's calendar (or site footer) is sporting the link to my website.

= How can I change the day the week starts on? =

Calendar conforms to the main WordPress setting as to what day the week starts on. If you go to the settings area of the WordPress admin panel and change what day the week starts on in there, Calendar will change also.

= I've enabled categories but am confused as to how they work. Can you help? =

When you install Calendar you will note that there exists one category, entitled "General" into which all new events are placed. If you enable categories you will notice that events suddenly show up highlighted in the same colour that the "General" category is set to and that a key denoting the name of the category and its colour shows up below the calendar. This means that if you create a new category with a different name and colour and place event(s) into it they will show up in the new colour. This allows you to see easily what type of events are scheduled.

= Why can't I delete the "General" category? =

Since the introduction of categories each event must be placed in a category. For this reason you are prevented from deleting the "General" event category so that events always have at least one category in which to sit. You can however change the name and colour of the "General" category to suit your site or turn off categories completely. Note that turning off categories will not prevent the display of the category in which an event sits in the admin panel, it will merely obliterate all references to categories on public instances of Calendar.

= Can I make event titles/mouse-overs links? =

Yes. You simply need to fill in the full URL (including the http:// bit) in the field entitled "Event Link (Optional)". This will then turn the event title into a link to the URL of your choice. Leaving this field blank will disable the feature for the event.

= Why are categories disabled by default? =

This is mainly for people who are upgrading. Because there is only one category by default, enabling them immediately upon upgrade would result in upgraders suddenly seeing all their events (perhaps hundreds or even thousands) listed under the same type, which would look a bit silly. Disabling it by default allows non-interested parties to keep using Calendar the way they have and interested parties to put events in the right categories before enabling the category system.

= I'm currently using [Event Calendar](http://wpcal.firetree.net/ "Event Calendar") and wish to change to Calendar. Why, when both plugins are activated (as I migrate), does only one plugin's upcoming events widget display? =

The issue here is that there is a naming conflict between the functions in the two plugins. Due to the fact the aforementioned calendar is no longer developed or supported by the author and users are unlikely to want to use two types of calendar at once, I will not be working around this issue. You should simply be aware that showing both widgets at once while you are migrating is not possible.

= My theme was designed using Artiseer and the mouse-overs on Calendar are partially hidden in the sidebar. How can I fix this? =

Firstly you should install Atristeer 2.3.0.23326 or later as the issue should be fixed in this version. Secondly, if this version fails to fix the issue then you should find the CSS classes named as below (or similar) and remove the z-index attribute from them
`.BlockContent
.Block-body
.Block
.BlockContent-body`
Thirdly, if you found yourself having to implement the above fix then don't ask for support - the problem is still with Atristeer and you should contact them about the issue. An update on their software and my support for it is posted here [Artisteer Calendar Issue](http://www.kieranoshea.com/forum/viewtopic.php?f=13&t=195 "Artisteer Calendar Issue")

= I see mention of a development version of Calendar. Where can I download this? =

The development version is Calendar as you know it along with ongoing fixes etc. that I've either already implemented and not released or am in the process of implementing. Some users find that a particular bug they have is already fixed in this version and so might wish to install it. It is available for download from WordPress on the following URL

[Calendar Development Version](http://downloads.wordpress.org/plugin/calendar.zip "Calendar Development Version")

= I have no time on my events when I view them in the calendar, even though I entered one when I added my events. What is going on? =

Check to make sure that the form field is not blank on WordPress/GeneralSettings/DateFormat if you have set it to custom.

= I've upgraded Calendar and my mouse-overs have gone all funny. How do I fix this? =

To bring Calendar into full compliance with W3C web standards, some changes were made to the HTML which in turn required some changes to the styles. In order to activate these new styles you will need to tick the box on the Calendar options page to repair the Calendar style. Be warned that if you have made style changes in the box, you will lose them - be sure to back them up so you can edit them back in after the restore.

= I've authored a custom theme but when I include Calendar on a page the styles are missing and it looks odd. =

You've not called wp_head() in your theme's header and/or wp_footer() in your theme's footer. Add these calls and the Calendar will spring to life.

== Screenshots ==

1. Calendar being used on a blog page
2. Widgets showing in the sidebar
3. The event management screen of calendar
4. The category management screen of calendar
5. The options screen of Calendar

== Changelog ==

= 1.3.5 =
*   Fixed bug with date switcher not showing the current month when said month happened to be September
*   Corrected error thrown when numerical date in URL arguments was being incorrectly cast to string
*   Fixed lack of header on POT file and updated the same to include some missing translation strings
*   Improved README file with full FAQ and proper spacing between list items
*   Included assets which support the new WordPress repository GUI, namely a background and an icon
*   Fixed issue whereby accented characters couldn't be used in titles and descriptions
*   Added action hooks for add, edit and delete events and a filter for event links (requested by user lexhair)

= 1.3.4 =
*   Enabled short codes for displaying the calendar in posts/pages
*   Enabled use of calendar short codes in the text widgets to permit multiple calendar widget instances via short codes
*   Fixed dollar sign not displaying properly in event descriptions
*   Removed references to deprecated MySQL functions, switching instead to wpdb prepare functions

= 1.3.3 =
*   Fixed XSS security issue (thanks to Charlie Eriksen via Secunia SVCRP for the report)

= 1.3.2 =
*   Ensured manage calendar JavaScript only loads on manage calendar page in admin panel
*   Switched to GPL compatible JavaScript date picker
