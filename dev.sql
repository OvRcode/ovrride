USE `ovrride`;
UPDATE wp_posts SET guid = replace(guid, 'http://ovrride.com', 'http://local.ovrride.com');
UPDATE wp_posts SET guid = replace(guid, 'https://ovrride.com', 'https://local.ovrride.com');

UPDATE wp_posts SET post_content = replace(post_content, 'http://ovrride.com', 'http://local.ovrride.com');
UPDATE wp_posts SET post_content = replace(post_content, 'https://ovrride.com', 'https://local.ovrride.com');

UPDATE wp_postmeta SET meta_value = replace(meta_value,'http://ovrride.com', 'http://local.ovrride.com');
UPDATE wp_postmeta SET meta_value = replace(meta_value,'https://ovrride.com', 'https://local.ovrride.com');

UPDATE wp_options SET option_value = replace(option_value, 'http://ovrride.com', 'http://local.ovrride.com') WHERE option_name = 'home' OR option_name = 'siteurl' OR option_name = 'dashboard_widget_options';
UPDATE wp_options SET option_value = replace(option_value, 'https://ovrride.com', 'https://local.ovrride.com') WHERE option_name = 'home' OR option_name = 'siteurl' OR option_name = 'dashboard_widget_options';

UPDATE wp_options SET option_value = ' a:5:{s:10:"from_email";s:16:"info@ovrride.com";s:9:"from_name";s:7:"OvRride";s:16:"enable_sparkpost";b:0;s:14:"sending_method";s:3:"api";s:15:"enable_tracking";b:1;'WHERE option_name = 'sp_settings' LIMIT 1;

UPDATE mysql.user SET Host = '%' WHERE User = 'root' AND Host = 'localhost';
DELETE From mysql.user WHERE User = 'root' AND Host <> '%';
FLUSH PRIVILEGES;
