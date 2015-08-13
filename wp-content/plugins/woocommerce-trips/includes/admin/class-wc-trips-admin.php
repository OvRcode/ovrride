<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Trips_Admin {
    public function __construct() {
        global $post;

        $post_id = $post->ID;
        add_filter( 'product_type_options', array( $this, 'product_type_options' ) );
        add_filter( 'product_type_selector' , array( $this, 'product_type_selector' ) );
        add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ), 5 );
        add_action( 'woocommerce_product_write_panels', array( $this, 'trip_panels' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'script_style_includes' ) );
        add_action( 'woocommerce_process_product_meta', array( $this,'save_product_data' ), 20 );
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'general_tab' ) );
        // Pickup Post type specific
        add_action( 'add_meta_boxes_pickup_locations', array( $this, 'pickup_locations_meta_boxes' ) );
        add_action( 'save_post', array($this,'save_pickup_meta') );
        add_filter( 'manage_pickup_locations_posts_columns', array($this, 'pickup_columns_head' ) );
        add_action( 'manage_pickup_locations_posts_custom_column', array($this, 'pickup_columns' ), 10, 2 );
        add_action( 'admin_action_wc_trips_duplicate_pickup', array( $this, 'wc_trips_duplicate_pickup'));
        add_filter( 'post_row_actions', array($this, 'wc_trip_pickup_location_duplicate_post_link'), 10, 2 );
        // Ajax
        //add_action( 'wp_ajax_woocommerce_add_bookable_resource', array( $this, 'add_bookable_resource' ) );
        //add_action( 'wp_ajax_woocommerce_remove_bookable_resource', array( $this, 'remove_bookable_resource' ) );

        //add_action( 'wp_ajax_woocommerce_add_bookable_person', array( $this, 'add_bookable_person' ) );
        //add_action( 'wp_ajax_woocommerce_remove_bookable_person', array( $this, 'remove_bookable_person' ) );
    }
    
    public function product_type_options( $options ) {
        $options['virtual']['wrapper_class'] .= ' show_if_trip';
        return $options;
    }
    
    public function product_type_selector( $types ) {
        $types[ 'trip' ] = 'Trips product';
        return $types;
    }
    
    public function add_tab() {
        include( 'views/html-trip-tab.php' );
    }
    public function general_tab() {
		global $post;
		$post_id = $post->ID;
        include( 'views/html-trip-general.php' );
    }
    public function save_product_data() {
        global $wpdb;
        global $post;
        $post_id = $post->ID;
        $product_type = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );

        if ( 'trip' !== $product_type ) {
            return;
        }

        // Save meta from general tab
        $meta_to_save = array(
            '_wc_trip_base_price'               => 'float',
            '_wc_trip_destination'              => 'string',
            '_wc_trip_type'                     => 'string',
            '_wc_trip_start_date'               => 'date',
            '_wc_trip_end_date'                 => 'date',
            '_wc_trip_stock'                    => 'int'
            );
        foreach ( $meta_to_save as $meta_key => $sanitize ) {
            $value = ! empty( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : '';
            switch ( $sanitize ) {
                case 'int' :
                    $value = absint( $value );
                    break;
                case 'float' :
                    $value = floatval( $value );
                    break;
                default :
                    $value = sanitize_text_field( $value );
            }
            update_post_meta( $post_id, $meta_key, $value );
            unset($_POST[ $meta_key ]);
        }
        // Primary packages
        $primary_packages = array();
        $primary_label = ( isset($_POST['_wc_trip_primary_package_label']) ? wc_clean($_POST['_wc_trip_primary_package_label']) : '');
        $packages = ( isset($_POST['wc_trips_primary_package_description']) ? sizeof($_POST['wc_trips_primary_package_description']) : 0 );
        for ( $i = 0; $i < $packages; $i++ ) {
            $primary_packages[$i]['description'] = wc_clean( $_POST['wc_trips_primary_package_description'][$i] );
            $primary_packages[$i]['cost'] = wc_clean( $_POST['wc_trips_primary_package_cost'][$i] );
            if ( isset($_POST['_wc_trip_primary_package_stock']) && $_POST['_wc_trip_primary_package_stock'] ) {
                $primary_packages[$i]['stock'] = wc_clean( $_POST['wc_trips_primary_package_stock'][$i] );
            }
        }
        update_post_meta( $post_id, '_wc_trip_primary_package_label', $primary_label);
        update_post_meta( $post_id, '_wc_trip_primary_package_stock', $_POST['_wc_trip_primary_package_stock']);
        update_post_meta( $post_id, '_wc_trip_primary_packages', $primary_packages );
        
        // Secondary packages
        $secondary_packages = array();
        $secondary_label = ( isset($_POST['_wc_trip_secondary_package_label']) ? wc_clean($_POST['_wc_trip_secondary_package_label']) : '');
        $packages = ( isset($_POST['wc_trips_secondary_package_description']) ? sizeof($_POST['wc_trips_secondary_package_description']) : 0 );
        for ( $i = 0; $i < $packages; $i++ ) {
            $secondary_packages[$i]['description'] = wc_clean( $_POST['wc_trips_secondary_package_description'][$i] );
            $secondary_packages[$i]['cost'] = wc_clean( $_POST['wc_trips_secondary_package_cost'][$i] );
            if ( isset($_POST['_wc_trip_secondary_package_stock']) && $_POST['_wc_trip_secondary_package_stock'] ) {
                $secondary_packages[$i]['stock'] = wc_clean( $_POST['wc_trips_secondary_package_stock'][$i] );
            }
        }
        update_post_meta( $post_id, '_wc_trip_secondary_package_label', $secondary_label );
        update_post_meta( $post_id, '_wc_trip_secondary_package_stock', $_POST['_wc_trip_secondary_package_stock']);
        update_post_meta( $post_id, '_wc_trip_secondary_packages', $secondary_packages );
        
        // Tertiary packages
        $tertiary_packages = array();
        $tertiary_label = ( isset($_POST['_wc_trip_tertiary_package_label']) ? wc_clean($_POST['_wc_trip_tertiary_package_label']) : '');
        $packages = ( isset($_POST['wc_trips_tertiary_package_description']) ? sizeof($_POST['wc_trips_tertiary_package_description']) : 0 );
        for ( $i = 0; $i < $packages; $i++ ) {
            $tertiary_packages[$i]['description'] = wc_clean( $_POST['wc_trips_tertiary_package_description'][$i] );
            $tertiary_packages[$i]['cost'] = wc_clean( $_POST['wc_trips_tertiary_package_cost'][$i] );
            if ( isset($_POST['_wc_trip_tertiary_package_stock']) && $_POST['_wc_trip_tertiary_package_stock'] ) {
                $tertiary_packages[$i]['stock'] = wc_clean( $_POST['wc_trips_tertiary_package_stock'][$i] );
            }
        }
        update_post_meta( $post_id, '_wc_trip_tertiary_package_label', $tertiary_label);
        update_post_meta( $post_id, '_wc_trip_tertiary_package_stock', $_POST['_wc_trip_tertiary_package_stock']);
        update_post_meta( $post_id, '_wc_trip_tertiary_packages', $tertiary_packages );
    }
    
    public function trip_panels() {
        global $post;
        $post_id = $post->ID;
        wp_enqueue_script( 'wc_trips_admin_js' );
        include( 'views/html-trip-primary-packages.php' );
        include( 'views/html-trip-secondary-packages.php' );
        include( 'views/html-trip-tertiary-packages.php' );
        include( 'views/html-trip-pickup-locations.php' );
    }
    
    public function script_style_includes() {
        global $post, $woocommerce, $wp_scripts;
        
        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_style( 'wc_trips_admin_styles', WC_TRIPS_PLUGIN_URL . '/assets/css/trip_admin' . $suffix . '.css', null, WC_TRIPS_VERSION );
        wp_register_script( 'wc_trips_admin_js', WC_TRIPS_PLUGIN_URL . '/assets/js/trips_admin' . $suffix . '.js', array( 'jquery' ) );
        $params = array(
//            'nonce_delete_primary_package'    => wp_create_nonce( 'delete-primary-package' ),
//            'nonce_add_primary_package'       => wp_create_nonce( 'add-primary-package' ),
            'post'                   => isset( $post->ID ) ? $post->ID : '',
            'plugin_url'             => $woocommerce->plugin_url(),
            'ajax_url'               => admin_url( 'admin-ajax.php' )
        );

        wp_localize_script( 'wc_trips_admin_js', 'wc_trips_admin_js_params', $params );
    }
    
    public function pickup_locations_meta_boxes() {
        add_meta_box( 
                'wc_trips_pickup_locations_meta',
                __( 'Location Details' ),
                array($this, 'render_pickup_meta_boxes'),
                'pickup_locations',
                'normal',
                'default'
            );
    }
    
    public function render_pickup_meta_boxes( $post ) {
        $nonce = wp_create_nonce( 'pickup_location'.$post->ID );
        $time = get_post_meta( $post->ID, '_pickup_location_time', true);
        $address = get_post_meta( $post->ID, '_pickup_location_address', true);
        $cross_st = get_post_meta( $post->ID, '_pickup_location_cross_st', true);
        echo <<<META
        <input type="hidden" name="pickup_location_nonce" id="pickup_location_nonce" value="{$nonce}" />
        
        <label for="_pickup_location_time">Time</label>
        <input type="time" name="_pickup_location_time" value="{$time}" />
        
        <label for="_pickup_location_address">Street address</label>
        <input type="text" name="_pickup_location_address" value="{$address}" />
        
        <label for="_pickup_location_cross_st">Cross Streets</label>
        <input type="text" name="_pickup_location_cross_st" value="{$cross_st}" />
META;
    
    }
    
    public function save_pickup_meta( $post_id ) {
        $nonce_check = wp_verify_nonce( $_POST['pickup_location_nonce'], 'pickup_location'.$post_id );
        // Check nonce is valid
        if ( 1 == $nonce_check || 2 == $nonce_check ) {
            // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
            if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
                return $post_id;
            }
            
            // Check permissions
            if ( !current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
            
            update_post_meta( $post_id, '_pickup_location_time', sanitize_text_field( $_POST['_pickup_location_time'] ) );
            update_post_meta( $post_id, '_pickup_location_address', sanitize_text_field( $_POST['_pickup_location_address'] ) );
            update_post_meta( $post_id, '_pickup_location_cross_st', sanitize_texT_field( $_POST['_pickup_location_cross_st'] ) );
        } else {
            return $post_id;
        }
    }
    
    public function pickup_columns_head( $defaults ) {
        // Remove dynamic gallery columns, these columns are not applicable to this post type
        unset( $defaults['dfcg_desc_col'] );
        unset( $defaults['dfcg_image_col']);
        
        // Remove expiration column
        unset( $defaults['expirationdate'] );
        
        // Add Pickup Time column
        // Chop up defaults array to get new column next to title
        $defaultsBeginning = array_slice($defaults, 0, 2, true);
        $defaultsEnd = array_slice($defaults,2,true);
        $defaults = array_merge($defaultsBeginning, array("pickup_time" => 'Pickup Time'), $defaultsEnd);
        
        return $defaults;
    }
    
    public function pickup_columns( $column_name, $post_id ) {
        if ( "pickup_time" == $column_name ){
            $time = get_post_meta( $post_id, '_pickup_location_time', true);
            echo date("g:i a", strtotime($time));
        }
    }
    
    public function wc_trips_duplicate_pickup() {
        global $wpdb;
        
        if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'wc_trips_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
            wp_die('No post to duplicate has been supplied!');
        }
        
        $post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
        
        $post = get_post( $post_id );
        
        $current_user = wp_get_current_user();
        $new_post_author = $current_user->ID;
        
        if ( isset( $post ) && $post != null ) {
            $args = array(
                'comment_status' => $post->comment_status,
                'ping_status'    => $post->ping_status,
                'post_author'    => $new_post_author,
                'post_content'   => $post->post_content,
                'post_excerpt'   => $post->post_excerpt,
                'post_name'      => $post->post_name,
                'post_parent'    => $post->post_parent,
                'post_password'  => $post->post_password,
                'post_status'    => 'draft',
                'post_title'     => $post->post_title,
                'post_type'      => $post->post_type,
                'to_ping'        => $post->to_ping,
                'menu_order'     => $post->menu_order
            );
            
            $new_post_id = wp_insert_post( $args );
            $taxonomies = get_object_taxonomies($post->post_type);
            foreach ($taxonomies as $taxonomy) {
                $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
            }
            
            $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
            if (count($post_meta_infos)!=0) {
                $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                foreach ($post_meta_infos as $meta_info) {
                    $meta_key = $meta_info->meta_key;
                    $meta_value = addslashes($meta_info->meta_value);
                    $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
                }
                $sql_query.= implode(" UNION ALL ", $sql_query_sel);
                $wpdb->query($sql_query);
            }
            
            wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
            exit;
        } else {
            wp_die( 'Pickup Location duplication failed for id:' . $post_id);
        }
    }
    
    function wc_trip_pickup_location_duplicate_post_link( $actions, $post ) {
        $screen = get_current_screen();
    	if (current_user_can('edit_posts') && "pickup_locations" == $screen->post_type) {
    		$actions['duplicate'] = '<a href="admin.php?action=wc_trips_duplicate_pickup&amp;post=' . $post->ID . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
    	}
    	return $actions;
    }
}
new WC_Trips_Admin();