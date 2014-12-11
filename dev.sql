USE `ovrride`;
UPDATE wp_posts SET guid = replace(guid, 'http://ovrride.com', 'http://local.ovrride.com');
UPDATE wp_posts SET guid = replace(guid, 'https://ovrride.com', 'http://local.ovrride.com');
 
UPDATE wp_posts SET post_content = replace(post_content, 'http://ovrride.com', 'http://local.ovrride.com');
UPDATE wp_posts SET post_content = replace(post_content, 'https://ovrride.com', 'http://local.ovrride.com');
 
UPDATE wp_postmeta SET meta_value = replace(meta_value,'http://ovrride.com', 'http://local.ovrride.com');
UPDATE wp_postmeta SET meta_value = replace(meta_value,'https://ovrride.com', 'http://local.ovrride.com');
 
UPDATE wp_options SET option_value = replace(option_value, 'http://ovrride.com', 'http://local.ovrride.com') WHERE option_name = 'home' OR option_name = 'siteurl' OR option_name = 'dashboard_widget_options';
UPDATE wp_options SET option_value = replace(option_value, 'https://ovrride.com', 'http://local.ovrride.com') WHERE option_name = 'home' OR option_name = 'siteurl' OR option_name = 'dashboard_widget_options';


UPDATE wp_options SET option_value = replace(option_value, 'ABkKB4T19IsJKn_UprmSdw', 'SN74mu3IxIEYJz18FP31ag') WHERE option_name = 'wpmandrill';