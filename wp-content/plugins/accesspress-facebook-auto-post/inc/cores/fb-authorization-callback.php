<?php
defined('ABSPATH') or die('No script kiddies please!');
//die('123');
$code = sanitize_text_field($_GET['code']);
$account_details = get_option('afap_settings');
//$this->print_array($account_details);
$app_id = $account_details['application_id'];
$app_secret = $account_details['application_secret'];
$redirect_url = admin_url('admin-post.php?action=afap_callback_authorize');
$api_version = 'v2.0';
$param_url = urlencode($redirect_url);
$token_url = "https://graph.facebook.com/" . $api_version . "/oauth/access_token?"
        . "client_id=" . $app_id . "&redirect_uri=" . $param_url
        . "&client_secret=" . $app_secret . "&code=" . $code;

$params = null;
$access_token = "";
$response = wp_remote_get($token_url);
$body = wp_remote_retrieve_body($response);
$body_response = json_decode($body);
if ($body != '') {
    parse_str($body, $params);
    if (isset($params['access_token']) || isset($body_response->access_token)) {
        $access_token = $body_response->access_token;
        $offset = 0;
        $limit = 100;
        $data = array();
        $fbid = $account_details['facebook_user_id'];
        $pp = wp_remote_get("https://graph.facebook.com/" . $api_version . "/me/accounts?access_token=$access_token&limit=$limit&offset=$offset");

        $body = json_decode($pp['body']);
        $pages = $body->data;
        //$this->print_array($pages);die();
        if (empty($pages)) {
            $pages = array();
        } else {
            $new_pages = array();
            foreach ($pages as $page) {
                $new_pages[$page->id] = $page;
            }
            $pages = $new_pages;
        }
        $account_extra_details = array();
        $account_extra_details['authorize_status'] = 1;
        $account_extra_details['pages'] = $pages;
        $account_extra_details['access_token'] = $access_token;
        update_option('afap_extra_settings', $account_extra_details);
        $_SESSION['afap_message'] = __('Account authorized successfully.Now you can go for posting.', 'accesspress-facebook-auto-post');
        $redirect_url = admin_url() . 'admin.php?page=afap';
        // die($redirect_url);
        wp_redirect($redirect_url);
        //header('Location:'.  admin_url());
        exit();
    } else {
        $this->print_array($body);
    }
} else {
    $this->print_array($body);
}


