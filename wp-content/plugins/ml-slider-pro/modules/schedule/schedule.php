<?php

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Adds a schedule funcionality to each tab
 */
class MetaSliderPro_Schedule_Slides
{
    /**
     * Construct
     */
    public function __construct()
    {
        add_action('init', array($this, 'schedule_slides'), 20);
        add_action('metaslider_register_admin_scripts', array($this, 'load_scripts'));
    }

    /**
     * Load JS
     */
    public function load_scripts()
    {
        wp_enqueue_script(
            'ms-moment-js',
            plugins_url('assets/moment/min/moment.min.js', __FILE__),
            array(),
            METASLIDERPRO_VERSION
        );
        $this->wp_add_inline_script(
            'ms-moment-js',
            sprintf(
                'window.jQuery(function($) {
            window.metaslider.app.EventManager.$on(["metaslider/app-loaded", "metaslider/slides-created"], function(e) {
                $(".ms-time-helper").each(function(time) {
                    var _this = $(this)
                    var interval = 0
                    window.moment && setInterval(function() {
                        interval += 1000
                        _this.prop("title", _this.data("now-text") + "<br>" + window.moment(_this.data("time")).add(interval, "ms").format("YYYY-MM-DD [%s] HH:mm:ss"))
                    }, 1000);
                    if (!window.moment) {
                        _this.remove()
                    }
                })
            })
        })
        ',
                _x('at', 'As in "Your slide will display Tuesday at 5pm"', 'ml-slider-pro')
            )
        );
    }

    /**
     * Schedule slides - Adds actions + filters
     */
    public function schedule_slides()
    {
        // Bypasses external plugin - MetaSlider Schedule Slides plugin to give preference to this version.
        if (
            class_exists('Meta_Slider_Schedule_Slides') && method_exists(
                'Meta_Slider_Schedule_Slides',
                'get_instance'
            )
        ) {
            remove_action('init', array(Meta_Slider_Schedule_Slides::get_instance(), 'init'), 30);
        } elseif (class_exists('Meta_Slider_Schedule_Slides')) {
            // This happens when MSSS is an older version. We could run this anyway but probably
            // better to not mess with activating/deactivating plugins unless necessary
            $schedule_slides_standalone = $this->is_plugin_installed('metaslider_schedule_slides');
            if (is_admin() && current_user_can('activate_plugins') && is_plugin_active($schedule_slides_standalone)) {
                // Deactivate the plugin
                deactivate_plugins($schedule_slides_standalone);

                // Show a warning
                require_once(METASLIDERPRO_PATH . 'inc/admin-notice.php');
                new Metaslider_Admin_Notice(
                    'notice-error',
                    __(
                        'Plugin <strong>Deactivated</strong>! MetaSlider Schedule Slides is no longer needed as we have added its functionality to MetaSlider Pro. Activating the plugin will cause a conflict on your site.',
                        'ml-slider-pro'
                    )
                );

                // Redirect if on the MetaSlider page
                if (isset($_REQUEST['page']) && 'metaslider' === $_REQUEST['page']) {
                    wp_safe_redirect(admin_url("admin.php?page=metaslider"));
                    exit;
                }
            }
        }

        add_filter('metaslider_slide_tabs', array($this, 'slide_admin_tab'), 10, 4);

        if (! is_admin()) {
            // Alter the main slideshow query and adjust the visibility of each slide
            add_filter('metaslider_get_slides_query', array($this, 'adjust_all_slides_visibility'), 10, 2);
        }

        add_action('metaslider_save_slide', array($this, 'save_settings'), 10, 3);
        add_action('metaslider-slide-edit-buttons', array($this, 'hide_slide_button'), 10, 2);
    }

    /**
     * Determine if a single slide thumbnail should be visible.
     *
     * @param string $list_item The slide html markup
     * @param object $post The slide post object
     * @return string
     */
    public function adjust_slide_visibility_get_string($list_item, $post)
    {
        $slide['id'] = $post->ID;
        $html = $this->adjust_slide_visibility($list_item, $slide);

        // This should return a string only, while adjust_slide_visibility() may return an empty array
        return is_string($html) ? $html : '';
    }

    /**
     * Loops through each slide and returns the query without the hidden slides.
     *
     * @param object $slides_query The wp query
     * @param string $slideshow_id The slide post object
     * @return object
     */
    public function adjust_all_slides_visibility($slides_query, $slideshow_id)
    {
        $slides_to_hide = array();
        foreach ($slides_query->posts as $slide) {
            $slide_array['id'] = $slide->ID;

            // Determine the slides that we want to hide
            if (! (bool)$this->adjust_slide_visibility(true, $slide_array)) {
                $slides_to_hide[] = $slide->ID;
            }
        }

        // If no slides were determined to hide, return the original query
        if (empty($slides_to_hide)) {
            return $slides_query;
        }

        // Return a new wp_query with just the slides needed
        $args = $slides_query->query_vars;
        $args['post__not_in'] = $slides_to_hide;

        // WP will set defaults using fill_query_vars() and when duplicating
        // a query will then cause issues. For example, the default ['s' => ''] will
        // set is_search() to true as they just use isset() to test it.
        $args = array_filter($args, array($this, 'remove_empty_vars'));

        return new WP_Query($args);
    }

    /**
     * Determine if a single slide should be visible.
     * The $html param can be used to determine truthiness whether
     * the slide should be displayed
     *
     * @param string|array|bool $html The slide html object
     * @param array $slide The slide parameters
     * @return array|string|bool
     */
    public function adjust_slide_visibility($html, $slide)
    {
        $slide_id = $slide['id'];

        // If it's hidden, remove slide from list
        $is_hidden = $this->option_is_enabled(get_post_meta($slide_id, '_meta_slider_slide_is_hidden', true));
        if (filter_var($is_hidden, FILTER_VALIDATE_BOOLEAN)) {
            return array();
        }

        // If it's not scheduled, return markup
        $is_scheduled = $this->option_is_enabled(get_post_meta($slide_id, '_meta_slider_slide_is_scheduled', true));
        if (! $is_scheduled || ! filter_var($is_scheduled, FILTER_VALIDATE_BOOLEAN)) {
            return $html;
        }

        $now = current_time("timestamp");

        // If it's scheduled, determine if it's in or out of bounds
        $time_start = get_post_meta($slide_id, '_meta_slider_slide_scheduled_start', true);
        $time_end = get_post_meta($slide_id, '_meta_slider_slide_scheduled_end', true);

        if ($time_start && $time_end) {
            // If we are not inside the schedule, hide the slide
            if ((strtotime($time_start) <= $now) && ($now <= strtotime($time_end))) {
                // We are inside the schedule. do nothing
            } else {
                return array();
            }
        }

        // Check if it's available today. 0 = Sunday, etc ('w' gets a 1-6 representation of the day)
        $visible_days = get_post_meta($slide_id, '_meta_slider_slide_scheduled_days', true);
        // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
        if (! is_array($visible_days) || ! in_array(date('w', $now), $visible_days)) {
            return array();
        }

        // If this is checked (or not set), there is no time constraint
        $all_day = $this->option_is_enabled(get_post_meta($slide_id, '_meta_slider_slide_all_day', true));

        //check if slide should be shown or hidden within day constraint
        $constraint_time_show = $this->option_is_enabled(
            get_post_meta($slide_id, '_meta_slider_slide_constraint_time_show', true)
        );
        $constraint_time_start = get_post_meta($slide_id, '_meta_slider_slide_constraint_time_start', true);
        $constraint_time_end = get_post_meta($slide_id, '_meta_slider_slide_constraint_time_end', true);

        if (filter_var($constraint_time_show, FILTER_VALIDATE_BOOLEAN)) {
            //show slide
            //check if all day
            if ($all_day || filter_var($all_day, FILTER_VALIDATE_BOOLEAN)) {
                return $html;
            } else {
                //if within time constraint, show
                if ((strtotime($constraint_time_start, $now) <= $now) && (strtotime($constraint_time_end, $now) >= $now)) {
                    return $html;
                } else {
                    return array();
                }
            }
        } else {
            //hide slide
            //check if all day
            if ($all_day || filter_var($all_day, FILTER_VALIDATE_BOOLEAN)) {
                return array();
            } else {
                //if within time constraint, hide
                if ((strtotime($constraint_time_start, $now) <= $now) && (strtotime($constraint_time_end, $now) >= $now)) {
                    return array();
                } else {
                    return $html;
                }
            }
        }
    }

    /**
     * Add admin Tab
     *
     * @param array $tabs Exising tabs
     * @param object $slide Slide
     * @param object $slider Slider
     * @param array $settings Slider Settings
     * @return array
     */
    public function slide_admin_tab($tabs, $slide, $slider, $settings)
    {
        ob_start();
        $this->slide_admin_tab_controls($slide);
        $content = ob_get_contents();
        ob_end_clean();

        $tabs['schedule'] = array(
            'title' => __('Schedule', 'ml-slider-pro'),
            'content' => $content
        );

        return $tabs;
    }

    /**
     * Renders the Schedule tab Controls
     *
     * @param object $post Slide post object
     */
    public function slide_admin_tab_controls($post)
    {
        $hide_slide = $this->option_is_enabled(get_post_meta($post->ID, '_meta_slider_slide_is_hidden', true));
        $admin_title = get_post_meta($post->ID, '_meta_slider_slide_admin_title', true);
        $is_scheduled = $this->option_is_enabled(get_post_meta($post->ID, '_meta_slider_slide_is_scheduled', true));
        $schedule_start = get_post_meta($post->ID, '_meta_slider_slide_scheduled_start', true);
        $schedule_end = get_post_meta($post->ID, '_meta_slider_slide_scheduled_end', true);
        $days_scheduled = get_post_meta($post->ID, '_meta_slider_slide_scheduled_days', true);

        $all_day = $this->option_is_enabled(get_post_meta($post->ID, '_meta_slider_slide_all_day', true));
        $constraint_time_show = get_post_meta($post->ID, '_meta_slider_slide_constraint_time_show', true);
        $constraint_time_start = get_post_meta($post->ID, '_meta_slider_slide_constraint_time_start', true);
        $constraint_time_end = get_post_meta($post->ID, '_meta_slider_slide_constraint_time_end', true);

        $path = trailingslashit(plugin_dir_path(__FILE__)) . 'tabs/';
        include $path . 'schedule-tab.php';
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function option_is_enabled($value)
    {
        return $value === 'yes' || $value === 'on' || $value === true || $value == 1;
    }

    public function sanitize_fields_array($fields)
    {
        if (isset($fields['hide_slide'])) {
            $fields['hide_slide'] = sanitize_text_field($fields['hide_slide']);
        }

        if (isset($fields['schedule'])) {
            $fields['schedule'] = sanitize_text_field($fields['schedule']);
        }

        if (isset($fields['from'])) {
            foreach ($fields['from'] as &$item) {
                $item = sanitize_text_field($item);
            }
        }

        if (isset($fields['to'])) {
            foreach ($fields['to'] as &$item) {
                $item = sanitize_text_field($item);
            }
        }

        if (isset($fields['days'])) {
            foreach ($fields['days'] as &$item) {
                $item = sanitize_text_field($item);
            }
        }

        if (isset($fields['all_day'])) {
            $fields['all_day'] = sanitize_text_field($fields['all_day']);
        }

        if (isset($fields['show_during_constraint'])) {
            $fields['show_during_constraint'] = sanitize_text_field($fields['show_during_constraint']);
        }

        if (isset($fields['constraint_from'])) {
            foreach ($fields['constraint_from'] as &$item) {
                $item = sanitize_text_field($item);
            }
        }

        if (isset($fields['constraint_to'])) {
            foreach ($fields['constraint_to'] as &$item) {
                $item = sanitize_text_field($item);
            }
        }

        return $fields;
    }

    /**
     * Saving the new settings
     *
     * @param int $slide_id Slide ID
     * @param int $slider_id Slider ID
     * @param array $fields Fields saved
     */
    public function save_settings($slide_id, $slider_id, $fields)
    {
        $fields = $this->sanitize_fields_array($fields);

        update_post_meta(
            $slide_id,
            '_meta_slider_slide_is_hidden',
            isset($fields['hide_slide']) && $fields['hide_slide'] === 'on'
        );
        update_post_meta(
            $slide_id,
            '_meta_slider_slide_is_scheduled',
            isset($fields['schedule']) && $fields['schedule'] === 'on'
        );

        if (isset($fields['schedule'])) {
            $start_date = $fields['from']['date'] . ' ' . $fields['from']['hh'] . ':' . $fields['from']['mn'] . ':' . $fields['from']['ss'];
            $end_date = $fields['to']['date'] . ' ' . $fields['to']['hh'] . ':' . $fields['to']['mn'] . ':' . $fields['to']['ss'];
            update_post_meta($slide_id, '_meta_slider_slide_scheduled_start', sanitize_text_field($start_date));
            update_post_meta($slide_id, '_meta_slider_slide_scheduled_end', sanitize_text_field($end_date));

            if (isset($fields['days'])) {
                update_post_meta($slide_id, '_meta_slider_slide_scheduled_days', array_keys($fields['days']));
            }

            update_post_meta(
                $slide_id,
                '_meta_slider_slide_all_day',
                isset($fields['all_day']) && $fields['all_day'] === 'on'
            );
            update_post_meta(
                $slide_id,
                '_meta_slider_slide_constraint_time_show',
                isset($fields['show_during_constraint']) && $fields['show_during_constraint'] === 'on'
            );

            $constraint_start_time_incoming = $fields['constraint_from']['hh'] . ':' . $fields['constraint_from']['mn'] . ':00';
            $constraint_end_time_incoming = $fields['constraint_to']['hh'] . ':' . $fields['constraint_to']['mn'] . ':00';
            update_post_meta(
                $slide_id,
                '_meta_slider_slide_constraint_time_start',
                sanitize_text_field($constraint_start_time_incoming)
            );
            update_post_meta(
                $slide_id,
                '_meta_slider_slide_constraint_time_end',
                sanitize_text_field($constraint_end_time_incoming)
            );
        }
    }

    /**
     * Display the Hide slide button in the slide header
     *
     * @param string $slide_type Slide type (e.g. 'image')
     * @param object $slide_id Slide ID
     */
    public function hide_slide_button($slide_type, $slide_id)
    {
        $hide_slide = $this->option_is_enabled(get_post_meta($slide_id, '_meta_slider_slide_is_hidden', true));
        ?>

        <button type="button" title="<?php
        _e('Hide slide', 'ml-slider-pro'); ?>" class="hide-slide toolbar-button alignright tipsy-tooltip-top">
            <input class="hide-slide" type="checkbox" name="attachment[<?php
            echo esc_attr($slide_id); ?>][hide_slide]" <?php
            echo($hide_slide ? 'checked="checked"' : ''); ?>>
            <svg class="feather feather-eye" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round" aria-hidden="true" data-reactid="501">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <svg class="feather feather-eye-off" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round" aria-hidden="true" data-reactid="496">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
        </button>
        <?php
    }

    /**
     * Used to filter out empty strings/arrays with array_filter()
     *
     * @param mixed $item The item being tested
     * @return bool - Will return whether empty on arrays/strings
     */
    protected function remove_empty_vars($item)
    {
        // If it's an array and not empty, keep it (return true)
        if (is_array($item)) {
            return ! empty($item);
        }

        // If it's a string and not '', keep it (return true)
        if (is_string($item)) {
            return ('' !== trim($item));
        }

        // Not likely to get this far but just in case, keep everything else
        return true;
    }

    /**
     * Will be truthy if the plugin is installed
     *
     * @param string $name name of the plugin 'ml-slider'
     * @return bool|string - will return path, ex. 'ml-slider/ml-slider.php'
     */
    protected function is_plugin_installed($name)
    {
        if (! function_exists('get_plugins')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        foreach (get_plugins() as $plugin => $data) {
            if ($data['TextDomain'] == $name) {
                return $plugin;
            }
        }
        return false;
    }

    /**
     * Polyfill to handle the wp_add_inline_script() function.
     *
     * @param string $handle The script identifier
     * @param string $data The script to add, without <script> tags
     * @param string $position Whether to output before or after
     *
     * @return object|bool
     */
    public function wp_add_inline_script($handle, $data, $position = 'after')
    {
        if (function_exists('wp_add_inline_script')) {
            return wp_add_inline_script($handle, $data, $position);
        }
        global $wp_scripts;
        if (! $data) {
            return false;
        }

        // First fetch any existing scripts
        $script = $wp_scripts->get_data($handle, 'data');

        // Append to the end
        $script .= $data;

        return $wp_scripts->add_data($handle, 'data', $script);
    }
}