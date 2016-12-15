<?php

if( !class_exists('CC_Product_Page') ) :

class CC_Product_Page {

    public function __construct() {
        add_action( 'admin_menu' , array( $this, 'add_product_page' ) );
        add_action( 'admin_print_scripts' , array( $this, 'print_product_page_scripts' ) );
    }

    public function add_product_page() {
        add_menu_page ( 'Cybercraft Products', 'Cybercraft Products', 'manage_options', __FILE__, array( $this, 'build_plugin_page' ) );
    }

    function build_plugin_page() {
        if ( ! function_exists( 'plugins_api' ) ) {
            $admin_path = trailingslashit( str_replace( site_url() . '/', ABSPATH, get_admin_url() ) );
            require_once( $admin_path . 'includes/plugin-install.php' );
        }
        $call_api = plugins_api( 'query_plugins', array( 'author' => 'cybercraftit', 'fields' => array( 'banners' => true ) ) );
        if ( is_wp_error( $call_api ) ) {
            echo '<pre>' . print_r( $call_api->get_error_message(), true ) . '</pre>';
        } else {
            //echo base64_encode(json_encode( $call_api ));
            echo '<div class="cc-product-wrapper">';
            foreach( $call_api->plugins as $number => $plugin ) {
                ?>
                <div class="cc-product-holder">
                    <img src="<?php echo isset( $plugin->banners['low'] ) ? $plugin->banners['low'] : '' ?>" width="100%" alt="Banner"/>
                    <h3><a href="https://wordpress.org/plugins/<?php echo $plugin->slug; ?>" target="_blank"><?php echo $plugin->name ?></a></h3>
                    <p><?php echo $plugin->short_description; ?></p>
                    <a class="btn btn-visit" href="https://wordpress.org/plugins/<?php echo $plugin->slug; ?>" target="_blank"">Visit plugin page</a>
                </div>
                <?php
            }
            echo '</div>';

        }
    }

    /**
     * scripts and styles of product page
     */
    function print_product_page_scripts( $hook ) {
        ?>
        <style>
            .cc-product-holder a {
                text-decoration: none;
            }
            .cc-product-wrapper{
                overflow: hidden;
            }
            .cc-product-holder{
                border: 1px solid #cccccc;
                border-radius: 3px;
                width: 300px;
                height: 300px;
                float: left;
                padding: 10px 10px;
                margin: 3px;

            }
            .cc-product-holder .btn-visit{
                padding: 7px 10px;
                background: #05b93a;
                border-radius: 3px;
                color: #ffffff;
            }
        </style>
        <?php
    }

    public static function init() {
        new CC_Product_Page();
    }
}

CC_Product_Page::init();

endif;