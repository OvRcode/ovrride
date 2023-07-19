<?php
/*
 * Plugin Name: Product Expiry for WooCommerce
 * Plugin URI: https://webcodingplace.com/product-expiry-for-woocommerce/
 * Description: Provide expiry date for your products and get notified before expire
 * Version: 2.4
 * Author: WebCodingPlace
 * Author URI: https://webcodingplace.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: product-expiry-for-woocommerce
 * Domain Path: /languages
*/

if( ! defined('ABSPATH' ) ){
	exit;
}

define( 'WOOPE_PATH', untrailingslashit(plugin_dir_path( __FILE__ )) );
define( 'WOOPE_URL', untrailingslashit(plugin_dir_url( __FILE__ )) );

class WOO_Product_Expiry {

	function __construct(){
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'create_expiry_tab' ) );
        add_action( 'woocommerce_product_data_panels', array( $this, 'display_expiry_fields' ) );
        add_action( 'woocommerce_variation_options_pricing', array($this, 'display_expiry_fields_variable'), 10, 3 );
        add_action( 'woocommerce_save_product_variation', array($this, 'save_variation_fields'), 10, 2 );
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_fields' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
        add_action( 'admin_menu', array( $this, 'admin_settings_page' ) );
        add_filter( 'restrict_manage_posts', array($this, 'expiry_filter_admin'), 10 );
        add_filter( 'parse_query', array($this, 'expiry_filter_results'), 10, 1 );
        add_action( 'woo_expiry_schedule_action', array($this, 'schedule_action'), 10, 1);
        add_action( 'plugins_loaded', array($this, 'woope_load_plugin_textdomain' ) );

        $settings = get_option( 'woope_admin_settings' );

        $single_hook = (isset($settings['single_hook']) && $settings['single_hook'] != '') ? $settings['single_hook'] : 'woocommerce_before_add_to_cart_button' ;
        $archive_hook = (isset($settings['archive_hook']) && $settings['archive_hook'] != '') ? $settings['archive_hook'] : '' ;

        // Display Date
        add_action( $single_hook, array( $this, 'display_expiry_date' ) );
        add_action( $archive_hook, array( $this, 'display_expiry_date' ) );

        // Admin Columns and Quick Edit
        add_filter( 'manage_product_posts_columns', array($this, 'product_column_head'));
        add_action( 'manage_product_posts_custom_column', array($this, 'product_column_content'), 10, 2);
        add_action( 'woocommerce_product_quick_edit_start', array($this, 'show_woope_quick_edit') );
        add_action( 'woocommerce_product_quick_edit_save', array($this, 'save_woope_quick_edit') );

        add_action( 'wp_ajax_woope_save_admin_settings', array($this, 'save_settings' ) );

        // Display in Order Email
        add_action( 'woocommerce_order_item_meta_start', array($this, 'order_email_display_date'), 10, 3 );

        // Display in Order Admin
        add_action( 'woocommerce_checkout_create_order_line_item', array($this, 'order_admin_display_date'), 10, 4 );

        // Frontend get variation data
        add_filter( 'woocommerce_available_variation', array($this, 'date_field_variation_data') );

        // Front Script
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_front_script') );

        // Settings btn
        add_filter( 'plugin_action_links', array($this, 'settings_quick_btn'), 10, 2 );

        add_shortcode( 'expiry_date', array($this, 'display_expiry_date_sc') );
	}

    function woope_load_plugin_textdomain(){
        load_plugin_textdomain( 'product-expiry-for-woocommerce', FALSE, basename( WOOPE_PATH ) . '/languages/' );
    }

    function enqueue_front_script(){
        if (is_product()) {
            wp_enqueue_script( 'wccs-custom-script', WOOPE_URL . '/js/front.js', array('jquery'));
        }
    }

    function settings_quick_btn($links, $file){
        if ( strpos( $file, 'product-expiry-for-woocommerce.php' ) !== false ) {
            $settings_url = admin_url( 'edit.php?post_type=product&page=products_expiry_settings' );
            $new_links = array(
                'woope_settings' => '<a href="'.$settings_url.'">'.__( 'Settings', 'product-expiry-for-woocommerce' ).'</a>',
            );
            
            $links = array_merge( $links, $new_links );
        }

        return $links;
    }

    function display_expiry_date_sc($atts){
        $sc = shortcode_atts( array(
            'before' => '',
            'after' => '',
        ), $atts );

        $default = array(
            'date_format'   =>  get_option( 'date_format' ),
        );

        $savedSettings = get_option( 'woope_admin_settings', $default );

        $product = wc_get_product();
        $expiryDate = $product->get_meta('woo_expiry_date');
        if ($expiryDate != '') {
            $dateFormat = $savedSettings['date_format'];
            $formattedDate = date_i18n($dateFormat . ' ' . get_option('time_format'), strtotime($expiryDate));
            ob_start();
                echo esc_attr($sc['before']);
                echo $formattedDate;
                echo esc_attr($sc['after']);
            return ob_get_clean();
        }
    }

    function date_field_variation_data( $variations ) {
        if (isset($variations[ 'variation_id' ]) && get_post_meta( $variations[ 'variation_id' ], 'woo_expiry_date', true ) != '') {

            $default = array(
                'date_format'   =>  get_option( 'date_format' ),
                'display'       =>  'enable',
                'markup'   =>  __( 'Expiry Date: %date%', 'product-expiry-for-woocommerce' ),
            );

            $savedSettings = get_option( 'woope_admin_settings', $default );

            if($savedSettings['display'] == 'enable'){
                $woope_date = get_post_meta( $variations[ 'variation_id' ], 'woo_expiry_date', true );
                $woope_note = get_post_meta( $variations[ 'variation_id' ], 'woo_expiry_note', true );
                if ($woope_note != '') {
                    $variations['woope_text'] = $woope_note;
                } elseif ($woope_date != '') {
                    $dateFormat = $savedSettings['date_format'];
                    $formattedDate = date($dateFormat, strtotime($woope_date));
                    $text = str_replace("%date%", $formattedDate, $savedSettings['markup']);
                    $variations['woope_text'] = $text;   
                }
            }
        }

       return $variations;
    }

    function order_email_display_date( $item_id, $item, $order ) {

        if ( ! is_wc_endpoint_url() && $item->is_type('line_item') ) {
            $expiryDate = get_post_meta( $item->get_product_id(), 'woo_expiry_date', true );
            $expiryNote = get_post_meta( $item->get_product_id(), 'woo_expiry_note', true );

            if ( ! empty($expiryDate) ) {
                $default = array(
                    'date_format'   =>  get_option( 'date_format' ),
                    'orderdetails'       =>  'disable',
                    'markup'   =>  __( 'Expiry Date: %date%', 'product-expiry-for-woocommerce' ),
                );

                $savedSettings = get_option( 'woope_admin_settings', $default );

                if($savedSettings['orderdetails'] == 'enable'){
                    $dateFormat = $savedSettings['date_format'];
                    $formattedDate = date($dateFormat, strtotime($expiryDate));
                    $text = str_replace("%date%", $formattedDate, $savedSettings['markup']);
                    if ($expiryNote != '') {
                        $text = $expiryNote;
                    }
                    echo '<div class="woope-notice">'.$text.'</div>';
                }
            }
        }
    }

    function order_admin_display_date($item, $cart_item_key, $values, $order){
        $expiryDate = get_post_meta( $item->get_product_id(), 'woo_expiry_date', true );
        $expiryNote = get_post_meta( $item->get_product_id(), 'woo_expiry_note', true );

        if ( ! empty($expiryDate) ) {
            $default = array(
                'date_format'   =>  get_option( 'date_format' ),
                'orderdetailsadmin'       =>  'disable',
                'markup'   =>  __( 'Expiry Date: %date%', 'product-expiry-for-woocommerce' ),
            );

            $savedSettings = get_option( 'woope_admin_settings', $default );

            if($savedSettings['orderdetailsadmin'] == 'enable'){
                $dateFormat = $savedSettings['date_format'];
                $formattedDate = date($dateFormat, strtotime($expiryDate));
                $text = str_replace("%date%", $formattedDate, $savedSettings['markup']);
                if ($expiryNote != '') {
                    $text = $expiryNote;
                }
                $item->update_meta_data( 'exp', $text );
            }
        }
    }

    function admin_settings_page(){
        add_submenu_page(
            'edit.php?post_type=product',
            __( 'Product Expiry Settings', 'product-expiry-for-woocommerce' ),
            __( 'Expiry Settings', 'product-expiry-for-woocommerce' ),
            'manage_options',
            'products_expiry_settings',
            array($this, 'render_status_page')
        );
    }

    function render_status_page(){
        include_once WOOPE_PATH. '/menu/settings.php';
    }

    function product_column_head($defaults){
        $defaults['woope_tab'] = __( 'Exp', 'product-expiry-for-woocommerce' );
        return $defaults;
    }

    function product_column_content($column_name, $product_ID){
        if ($column_name == 'woope_tab') {        
            $expiry_date = get_post_meta( $product_ID, 'woo_expiry_date', true );
            if ($expiry_date != '') {
                $date_format = get_option( 'date_format' );
                echo '<span id="expid-'.$product_ID.'" data-expdate="'.$expiry_date.'">'.date($date_format, strtotime($expiry_date)).'</span>';
            } else {
                echo '<span id="expid-'.$product_ID.'" title="'.__( 'No Date Set', 'product-expiry-for-woocommerce' ).'" class="dashicons dashicons-warning"></span>';
            }
            wc_enqueue_js( "
                  $('#the-list').on('click', '.editinline', function() {
                     var post_id = $(this).closest('tr').attr('id');
                     post_id = post_id.replace('post-', '');
                     var custom_field = $('#expid-' + post_id).data('expdate');
                     $('input[name=\'woo_expiry_date\']', '.inline-edit-row').val(custom_field);
                    });
            " );
        }
    }

    function show_woope_quick_edit(){
       global $post;
       $expiry_date = get_post_meta( $post->ID, 'woo_expiry_date', true );
       ?>
       <label>
          <span class="title"><?php _e( 'Exp Date', 'product-expiry-for-woocommerce' ) ?></span>
          <span class="input-text-wrap">
             <input type="text" name="woo_expiry_date" class="text" value="<?php echo $expiry_date; ?>" placeholder="YYYY-MM-DD">
          </span>
       </label>
       <br class="clear" />
       <?php
    }

    function save_woope_quick_edit($product){
        $post_id = $product->get_id();
        if ( isset( $_REQUEST['woo_expiry_date'] ) ) {
            $custom_field = $_REQUEST['woo_expiry_date'];
            update_post_meta( $post_id, 'woo_expiry_date', wc_clean( $custom_field ) );
        }
    }

    function save_settings(){
        if (isset($_REQUEST)) {
            $settings = array(
                'single_hook'   =>  sanitize_text_field( $_REQUEST['single_hook'] ),
                'archive_hook'   =>  sanitize_text_field( $_REQUEST['archive_hook'] ),
                'date_format'   =>  sanitize_text_field( $_REQUEST['date_format'] ),
                'notify_emails'   =>  sanitize_text_field( $_REQUEST['notify_emails'] ),
                'display'   =>  sanitize_text_field( $_REQUEST['display'] ),
                'orderdetails'   =>  sanitize_text_field( $_REQUEST['orderdetails'] ),
                'orderdetailsadmin'   =>  sanitize_text_field( $_REQUEST['orderdetailsadmin'] ),
                'markup'   =>  wp_kses_post($_REQUEST['markup']),
            );
            if (update_option( 'woope_admin_settings', $settings )) {
                echo __( 'Settings Saved!', 'product-expiry-for-woocommerce' );
            }
            do_action( 'wpml_register_single_string', 'product-expiry-for-woocommerce', 'date-markup', sanitize_text_field( $_REQUEST['markup'] ) );
        }

        die(0);
    }

    function schedule_action($post_id){
        $woo_expiry_action = get_post_meta( $post_id, 'woo_expiry_action', true );
        if ($woo_expiry_action != '' && $woo_expiry_action == 'draft') {

            wp_update_post(array(
                'ID'    =>  $post_id,
                'post_status'   =>  'draft'
            ));
        } elseif ($woo_expiry_action != '' && $woo_expiry_action == 'out') {

            // 1. Updating the stock quantity
            update_post_meta( $post_id, '_stock', 0);

            // 2. Updating the stock quantity
            update_post_meta( $post_id, '_stock_status', wc_clean( 'outofstock' ) );

            // 3. Updating post term relationship
            wp_set_post_terms( $post_id, 'outofstock', 'product_visibility', true );
        }

        $savedSettings = get_option( 'woope_admin_settings' );

        if (isset($savedSettings['notify_emails']) && $savedSettings['notify_emails'] != '') {
            $site_title = get_bloginfo();
            $admin_email = apply_filters( "woope_admin_email", get_bloginfo('admin_email') );

            $from_title = apply_filters( 'woope_email_sender_title', $site_title );
            $from_email = apply_filters( 'woope_email_sender_email', $admin_email );

            $headers = array();
            $headers[] = "From: {$from_title} <{$from_email}>";
            $headers[] = "Content-Type: text/html";
            $headers[] = "MIME-Version: 1.0\r\n";

            $headers = apply_filters( 'woope_email_headers', $headers );

            $subject = __( 'Product Expired!', 'product-expiry-for-woocommerce' );
            $message = __( 'The following product is expired', 'product-expiry-for-woocommerce' );
            $message .= ' ';
            $message .= get_the_title( $post_id );

            wp_mail( $savedSettings['notify_emails'], $subject, $message, $headers );
        }
    }

	/**
	* Add the new tab to the $tabs array
	* @param   $tabs
	* @since   1.0.0
	*/
    public function create_expiry_tab( $tabs ) {
        $tabs['woopetab'] = array(
            'label'         => __( 'Product Expiry', 'product-expiry-for-woocommerce' ),
            'target'        => 'woo_product_expiry',
        );
        return $tabs;
    }

	/**
	* Display fields for the new panel
	* @since   1.0.0
	*/
    public function display_expiry_fields() { ?>

    <div id='woo_product_expiry' class='panel woocommerce_options_panel'>
        <div class="options_group">
            <?php
                woocommerce_wp_text_input(
                    array(
                        'id'        => 'woo_expiry_date',
                        'label'     => __( 'Expiry Date', 'product-expiry-for-woocommerce' ),
                        'type'      => 'text',
                        'desc_tip'  => __( 'Provide the date of expiry in YYYY-MM-DD format', 'product-expiry-for-woocommerce' ),
                        'description'  => __( 'Provide the date of expiry in YYYY-MM-DD format', 'product-expiry-for-woocommerce' )
                    )
                );
                woocommerce_wp_text_input(
                    array(
                        'id'        => 'woo_expiry_note',
                        'label'     => __( 'Expiry Note', 'product-expiry-for-woocommerce' ),
                        'type'      => 'text',
                        'desc_tip'  => __( 'Provide text to display instead of the exp date', 'product-expiry-for-woocommerce' ),
                        'description'  => __( 'Provide text to display instead of the exp date', 'product-expiry-for-woocommerce' )
                    )
                );
                woocommerce_wp_select(
                    array(
                        'id'        => 'woo_expiry_action',
                        'label'     => __( 'Action', 'product-expiry-for-woocommerce' ),
                        'options'   => array(
                            '' => __( 'Nothing', 'product-expiry-for-woocommerce' ),
                            'draft' => __( 'Make it Draft', 'product-expiry-for-woocommerce' ),
                            'out' => __( 'Out of stock', 'product-expiry-for-woocommerce' ),
                        ),
                        'desc_tip'  => __( 'What to do when this product expires?', 'product-expiry-for-woocommerce' ),
                        'description'  => __( 'What to do when this product expires?', 'product-expiry-for-woocommerce' )
                    )
                );
            ?>
        </div>
    </div>

    <?php }

    /**
    * Display fields for variable products
    * @since   1.0.0
    */
    public function display_expiry_fields_variable( $loop, $variation_data, $variation ) { ?>
        <div class="options_group form-row form-row-full coming-soon-variation">
            <?php
                woocommerce_wp_text_input(
                    array(
                        'id'        => '_woope_exp_date[' . $variation->ID . ']',
                        'label'     => __( 'Expiry Date', 'product-expiry-for-woocommerce' ),
                        'type'      => 'text',
                        'class'    => 'wccs-variation',
                        'wrapper_class'    => 'form-row form-row-first',
                        'desc_tip'  => true,
                        'description'  => __( 'Provide the date of expiry in YYYY-MM-DD format', 'product-expiry-for-woocommerce' ),
                        'value' => get_post_meta( $variation->ID, 'woo_expiry_date', true )
                    )
                );
                woocommerce_wp_text_input(
                    array(
                        'id'        => '_woope_exp_note[' . $variation->ID . ']',
                        'label'     => __( 'Expiry Note', 'product-expiry-for-woocommerce' ),
                        'type'      => 'text',
                        'class'    => 'wccs-variation',
                        'wrapper_class'    => 'form-row form-row-first',
                        'desc_tip'  => true,
                        'description'  => __( 'Provide the text to display instead of exp date', 'product-expiry-for-woocommerce' ),
                        'value' => get_post_meta( $variation->ID, 'woo_expiry_note', true )
                    )
                );

                woocommerce_wp_select(
                    array(
                        'id'        => '_woope_exp_action[' . $variation->ID . ']',
                        'label'     => __( 'Action', 'product-expiry-for-woocommerce' ),
                        'class'    => 'wccs-variation',
                        'wrapper_class'    => 'form-row form-row-last',
                        'options'   => array(
                            '' => __( 'Nothing', 'product-expiry-for-woocommerce' ),
                            'draft' => __( 'Make it Draft', 'product-expiry-for-woocommerce' ),
                            'out' => __( 'Out of stock', 'product-expiry-for-woocommerce' ),
                        ),
                        'desc_tip'  => true,
                        'description'  => __( 'What to do when this product expires?', 'product-expiry-for-woocommerce' ),
                        'value' => get_post_meta( $variation->ID, 'woo_expiry_action', true )
                    )
                );
            ?>
        </div>
    <?php }

    function save_variation_fields($post_id){
        if (isset($_POST['_woope_exp_date'][ $post_id ])) {
            update_post_meta( $post_id, 'woo_expiry_date', esc_attr( $_POST['_woope_exp_date'][ $post_id ] ) );
        }
        if (isset($_POST['_woope_exp_action'][ $post_id ])) {
            update_post_meta( $post_id, 'woo_expiry_action', esc_attr( $_POST['_woope_exp_action'][ $post_id ] ) );
        }
        if (isset($_POST['_woope_exp_note'][ $post_id ])) {
            update_post_meta( $post_id, 'woo_expiry_note', esc_attr( $_POST['_woope_exp_note'][ $post_id ] ) );
        }

        // Schedule Action
        if (
            isset($_POST['_woope_exp_date'][ $post_id ]) &&
            $_POST['_woope_exp_date'][ $post_id ] != '' &&
            isset($_POST['_woope_exp_action'][ $post_id ]) &&
            $_POST['_woope_exp_action'][ $post_id ] != ''
        ) {
            $scheduleOn = strtotime("+1 day", strtotime($_POST['_woope_exp_date'][ $post_id ]));
            wp_clear_scheduled_hook( 'woo_expiry_schedule_action', array($post_id) );
            wp_schedule_single_event( $scheduleOn, 'woo_expiry_schedule_action', array($post_id) );
        }

    }

    /**
     * Save the custom fields using CRUD method
     * @param $post_id
     * @since 1.0.0
     */
    public function save_fields( $post_id ) {

        $product = wc_get_product( $post_id );

        // Save the woo_expiry_date setting
        $woo_expiry_date = isset( $_POST['woo_expiry_date'] ) ? sanitize_text_field($_POST['woo_expiry_date']) : '';
        $woo_expiry_note = isset( $_POST['woo_expiry_note'] ) ? sanitize_text_field($_POST['woo_expiry_note']) : '';
        $woo_expiry_action = isset( $_POST['woo_expiry_action'] ) ? sanitize_text_field($_POST['woo_expiry_action']) : '';

        $product->update_meta_data( 'woo_expiry_date', sanitize_text_field( $woo_expiry_date ) );
        $product->update_meta_data( 'woo_expiry_note', sanitize_text_field( $woo_expiry_note ) );
        $product->update_meta_data( 'woo_expiry_action', sanitize_text_field( $woo_expiry_action ) );

        $scheduleOn = strtotime("+1 day", strtotime($woo_expiry_date));

        if ($woo_expiry_date != '' && $woo_expiry_action != '') {
            wp_clear_scheduled_hook( 'woo_expiry_schedule_action', array($post_id) );
            wp_schedule_single_event( $scheduleOn, 'woo_expiry_schedule_action', array($post_id) );
        }

        $product->save();

    }

    function display_expiry_date(){
        $default = array(
            'single_hook'   =>  '',
            'archive_hook'  =>  '',
            'date_format'   =>  get_option( 'date_format' ),
            'notify_emails' =>  '',
            'display'       =>  'enable',
            'markup'   =>  __( 'Expiry Date: %date%', 'product-expiry-for-woocommerce' ),
        );

        $savedSettings = get_option( 'woope_admin_settings', $default );

        if($savedSettings['display'] == 'enable'){
            $product = wc_get_product();
            $expiryDate = $product->get_meta('woo_expiry_date');
            $expiryNote = $product->get_meta('woo_expiry_note');
            if($expiryNote != ''){
                echo '<p class="woope-notice">'.$expiryNote.'</p>';
            } elseif ($expiryDate != '') {
                $dateFormat = $savedSettings['date_format'];
                $formattedDate = date($dateFormat, strtotime($expiryDate));
                $text = str_replace("%date%", $formattedDate, $savedSettings['markup']);
                $text = apply_filters('wpml_translate_single_string', $text, 'product-expiry-for-woocommerce', 'date-markup' );
                echo '<p class="woope-notice">'.$text.'</p>';
            }
            if ($product->is_type('variable')) {
                echo '<p class="woope-variable-notice"></p>';
            }
        }
    }

    function admin_scripts($check){
        global $post;

        if ( $check == 'post-new.php' || $check == 'post.php' ) {
            if (isset($post->post_type) && 'product' === $post->post_type) {
				wp_enqueue_script( 'product-expiry-for-woocommerce', WOOPE_URL.'/js/trigger-date-picker.js', array('wc-admin-product-meta-boxes') );
            }
        }

        if($check == 'product_page_products_expiry_settings'){
            wp_enqueue_script( 'woope-admin', WOOPE_URL.'/js/admin.js' );
        }
    }


    function expiry_filter_admin($post_type){
        if ('product' == $post_type) {
            $selected = (isset($_GET['expiry_period'])) ? $_GET['expiry_period'] : '' ;
            ?>
                <select name="expiry_period" id="filter-by-expiry">
                    <option value=""><?php _e( 'Filter by Expiry', 'product-expiry-for-woocommerce' ) ?></option>
                    <option value="this_month" <?php selected( $selected, 'this_month'); ?>><?php _e( 'Expiring this Month', 'product-expiry-for-woocommerce' ) ?></option>
                    <option value="next_month" <?php selected( $selected, 'next_month'); ?>><?php _e( 'Expiring next Month', 'product-expiry-for-woocommerce' ) ?></option>
                    <option value="three_months" <?php selected( $selected, 'three_months'); ?>><?php _e( 'Expiring within 3 Months', 'product-expiry-for-woocommerce' ) ?></option>
                    <option value="expired" <?php selected( $selected, 'expired'); ?>><?php _e( 'Already Expired', 'product-expiry-for-woocommerce' ) ?></option>
                </select>
            <?php
        }
    }

    function expiry_filter_results($query){

        //modify the query only if it admin and main query.
        if( !(is_admin() AND $query->is_main_query()) ){
          return $query;
        }

        //we want to modify the query for the targeted custom post and filter option
        if( !('product' === $query->query['post_type']) ){
          return $query;
        }

        if (isset($_GET['expiry_period']) && $_GET['expiry_period'] != '') {
            $exp_period = sanitize_text_field( $_GET['expiry_period'] );
            if ($exp_period == 'this_month') {
                $today = date('Y-m-d');
                $last_day_this_month  = date('Y-m-t');
                $query->query_vars['meta_query'][] = array(
                    'key'     => 'woo_expiry_date',
                    'value'   => array( $today, $last_day_this_month ),
                    'type'    => 'DATE',
                    'compare' => 'BETWEEN',
                );
            }
            if ($exp_period == 'next_month') {
                $next_month_start = date("Y-m-01", strtotime( '+1 month' ));
                $next_month_end = date("Y-m-t", strtotime( '+1 month' ));
                $query->query_vars['meta_query'][] = array(
                    'key'     => 'woo_expiry_date',
                    'value'   => array( $next_month_start, $next_month_end ),
                    'type'    => 'DATE',
                    'compare' => 'BETWEEN',
                );
            }
            if ($exp_period == 'three_months') {
                $today = date('Y-m-d');
                $third_month_end = date("Y-m-t", strtotime( '+3 month' ));
                $query->query_vars['meta_query'][] = array(
                    'key'     => 'woo_expiry_date',
                    'value'   => array( $today, $third_month_end ),
                    'type'    => 'DATE',
                    'compare' => 'BETWEEN',
                );
            }
            if ($exp_period == 'expired') {
                $query->query_vars['meta_query'][] = array(
                    'key'     => 'woo_expiry_date',
                    'value'   => date('Y-m-d'),
                    'type'    => 'DATE',
                    'compare' => '<=',
                );
            }
        }

        return $query;
    }
}
new WOO_Product_Expiry();