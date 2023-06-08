<?php

if (! defined('ABSPATH')) {
    die('No direct access.');
}

/**
 * Class to handle ajax endpoints, specifically used by vue components
 * If possible, keep logic here to a minimum.
 */
class MetaSliderPro_Api
{

    /**
     * Theme instance
     *
     * @var object
     * @see get_instance()
     */
    protected static $instance = null;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Setup
     */
    public function setup()
    {
    }

    /**
     * Used to access the instance
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register routes for admin ajax. Even if not used these can still be available.
     */
    public function register_admin_ajax_hooks()
    {
        // CSS Manager
        add_action('wp_ajax_ms_save_extra_css', array(self::$instance, 'save_extra_css'));
        add_action('wp_ajax_ms_get_extra_css', array(self::$instance, 'get_extra_css'));
    }

    /**
     * Helper function to verify access
     *
     * @return boolean
     */
    public function can_access()
    {
        $capability = apply_filters('metaslider_capability', 'edit_others_posts');
        return current_user_can($capability);
    }

    /**
     * Helper function to return an access denied response
     *
     * @return array
     */
    public function deny_access()
    {
        wp_send_json_error(array(
            'message' => __('You do not have access to this resource.', 'ml-slider')
        ), 401);
    }

    /**
     * Helper function to get data from the request
     * (supports rest & admin-ajax)
     * Does not handle any validation
     *
     * @param object $request The request
     * @param object $parameters The wanted parameters
     * @return array
     */
    public function get_request_data($request, $parameters)
    {
        $results = array();
        foreach ($parameters as $param) {
            if (method_exists($request, 'get_param')) {
                $results[$param] = $request->get_param($param);
            } else {
                $results[$param] = isset($_REQUEST[$param]) ? stripslashes_deep($_REQUEST[$param]) : null;
            }
        }

        return $results;
    }

    /**
     * Saves a string of CSS set to the slideshow
     *
     * @param object $request The request
     * @return string|mixed
     */
    public function save_extra_css($request)
    {
        if (! $this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('slideshow_id', 'css'));

        if (! is_string($data['css'])) {
            return wp_send_json_error(array(
                'message' => __('The request format was not valid.', 'ml-slider')
            ), 415);
        }

        // Save CSS to the database - the return on this function is not useful (i.e. returns false if data is identical)
        update_post_meta(absint($data['slideshow_id']), 'metaslider_extra_slideshow_css', $data['css']);

        // Important to send back what was saved, as it's used to determine if saving is needed
        return wp_send_json_success($data['css'], 200);
    }

    /**
     * Get extra CSS
     *
     * @param object $request The request
     * @return string|mixed
     */
    public function get_extra_css($request)
    {
        if (! $this->can_access()) {
            return $this->deny_access();
        }

        $data = $this->get_request_data($request, array('slideshow_id', 'css'));

        // Get the custom CSS. Will be an empty string if not set
        $css = (string)get_post_meta(absint($data['slideshow_id']), 'metaslider_extra_slideshow_css', true);

        return wp_send_json_success($css, 200);
    }
}

if (class_exists('WP_REST_Controller')) :
    /**
     * Class to handle REST route api endpoints.
     */
    class MetaSliderPro_REST_Controller extends WP_REST_Controller
    {

        /**
         * Namespace and version for the API
         *
         * @var string
         */
        protected $namespace = 'metaslider-pro/v1';

        /**
         * Constructor
         */
        public function __construct()
        {
            add_action('rest_api_init', array($this, 'register_routes'));
            $this->api = MetaSliderPro_Api::get_instance();
            $this->api->setup();
        }

        /**
         * Register routes
         */
        public function register_routes()
        {
            register_rest_route($this->namespace, '/slideshow/extra-css', array(
                array(
                    'methods' => 'GET',
                    'callback' => array($this->api, 'get_extra_css'),
                    'permission_callback' => array($this->api, 'can_access'),
                )
            ));
            register_rest_route($this->namespace, '/slideshow/extra-css', array(
                array(
                    'methods' => 'POST',
                    'callback' => array($this->api, 'save_extra_css'),
                    'permission_callback' => array($this->api, 'can_access'),
                )
            ));
        }
    }
endif;
