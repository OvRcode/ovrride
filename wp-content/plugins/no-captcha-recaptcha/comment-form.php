<?php

class Ncr_Comment_Captcha extends Ncr_No_Captcha_Recaptcha
{

    /** @var string captcha errors */
    private static $captcha_error;

    public static function initialize()
    {

        // initialize if login is activated
        if (isset(self::$plugin_options['captcha_comment']) && self::$plugin_options['captcha_comment'] == 'yes') {

            // add captcha header script to WordPress header
            add_action('wp_head', array(__CLASS__, 'header_script'));

            // adds the captcha to the comment form
            add_action('comment_form_after_fields', array(__CLASS__, 'display_captcha'));

            // authenticate the captcha answer
            add_filter('preprocess_comment', array(__CLASS__, 'validate_captcha_comment_field'));

            // redirect location for comment
            add_filter('comment_post_redirect', array(__CLASS__, 'redirect_fail_captcha_comment'), 10, 2);
        }
    }


    /**
     * Add query string to the comment redirect location
     *
     * @param $location string location to redirect to after comment
     * @param $comment object comment object
     *
     * @return string
     */
    public static function redirect_fail_captcha_comment($location, $comment)
    {
        if (!empty(self::$captcha_error)) {

            // delete the failed captcha comment
            wp_delete_comment(absint($comment->comment_ID), true);

            // add failed query string for @parent::display_captcha to display error message
            $location = add_query_arg('captcha', 'failed', $location);

            // remove the obnoxious comment string i.e comment-15
            $deleted_comment_id = strstr($location, '#');
            $location = str_replace($deleted_comment_id, '#comments', $location);

        }

        return $location;
    }

    /**
     * Verify the captcha answer
     *
     * @param $commentdata object comment object
     *
     * @return object
     */
    public static function validate_captcha_comment_field($commentdata)
    {

        if (is_user_logged_in()) return $commentdata;

        if (!isset($_POST['g-recaptcha-response']) || !(self::captcha_verification())) {
            self::$captcha_error = 'failed';
        }

        return $commentdata;
    }

}