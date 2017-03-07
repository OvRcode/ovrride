<?php

class Smps_Simple_Light {

    /**
     * @var Singleton The reference the *Singleton* instance of this class
     */
    private static $instance;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function settings() {
        return array(
            'name' => 'Simple Light',
            'items' => apply_filters('simple_light_shortcode_items',array(
                'tabs' => 'Tabs',
                'accordion' => 'Accordion',
                'table' => 'Table',
                'panel' => 'Panel',
                'alert' => 'Alert',
                'heading' => 'Heading',
                'quote' => 'Quote',
                'button' => 'Button',
                //'social_media_button' => 'Social Media Button'
            ))
        );
    }

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_styles' ) );
        $this->includes();
    }

    public function includes() {
        add_action( 'init',function () {
            include_once 'definitions.php';
            include_once 'admin-panel.php';
        });

        add_action( 'admin_head', function () {
            include 'settings-template.php';
        });
    }

    function wp_enqueue_scripts_styles( $hook ) {
        wp_enqueue_style( 'simple-light-css' , plugins_url('assets/css/simple-light.css',__FILE__) );
        wp_enqueue_script( 'simple-light-js' , plugins_url('assets/js/simple-light.js',__FILE__), array( 'jquery' ) );
    }

    function admin_enqueue_scripts_styles( $hook ) {
        if( in_array( $hook, array(
            'post-new.php',
            'post.php'
        ) ) ) {
            wp_enqueue_script( 'simple-light-settings-template' , plugins_url('assets/js/settings-templates.js',__FILE__), array( 'jquery' ) );
        }
    }


}

Smps_Simple_Light::get_instance();