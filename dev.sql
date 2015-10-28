USE `ovrride`;
UPDATE wp_posts SET guid = replace(guid, 'http://ovrride.com', 'http://local.ovrride.com');
UPDATE wp_posts SET guid = replace(guid, 'https://ovrride.com', 'https://local.ovrride.com');
 
UPDATE wp_posts SET post_content = replace(post_content, 'http://ovrride.com', 'http://local.ovrride.com');
UPDATE wp_posts SET post_content = replace(post_content, 'https://ovrride.com', 'https://local.ovrride.com');
 
UPDATE wp_postmeta SET meta_value = replace(meta_value,'http://ovrride.com', 'http://local.ovrride.com');
UPDATE wp_postmeta SET meta_value = replace(meta_value,'https://ovrride.com', 'https://local.ovrride.com');
 
UPDATE wp_options SET option_value = replace(option_value, 'http://ovrride.com', 'http://local.ovrride.com') WHERE option_name = 'home' OR option_name = 'siteurl' OR option_name = 'dashboard_widget_options';
UPDATE wp_options SET option_value = replace(option_value, 'https://ovrride.com', 'https://local.ovrride.com') WHERE option_name = 'home' OR option_name = 'siteurl' OR option_name = 'dashboard_widget_options';

UPDATE wp_options SET option_value = 'a:7:{s:7:"api_key";s:22:"SN74mu3IxIEYJz18FP31ag";s:9:"from_name";s:8:"OvR Ride";s:13:"from_username";s:16:"info@ovrride.com";s:8:"reply_to";s:16:"info@ovrride.com";s:10:"trackopens";s:1:"1";s:11:"trackclicks";s:1:"1";s:4:"tags";s:0:"";}' WHERE option_name = 'wpmandrill';

UPDATE mysql.user SET Host = '%' WHERE User = 'root' AND Host = 'localhost';
DELETE From mysql.user WHERE User = 'root' AND Host <> '%';
FLUSH PRIVILEGES;