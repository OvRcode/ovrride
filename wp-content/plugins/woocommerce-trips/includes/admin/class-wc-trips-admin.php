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

        //Destination Post type specific
        add_filter( 'manage_destinations_posts_columns', array($this, 'destination_columns_head' ) );
        add_action( 'add_meta_boxes_destinations', array( $this, 'destinations_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_destination_meta' ) );

        // Ajax
        add_action( 'wp_ajax_woocommerce_add_pickup_location', array( $this, 'add_pickup_location' ) );
        add_action( 'wp_ajax_woocommerce_remove_pickup_location', array( $this, 'remove_pickup_location' ) );
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
        $args = array('post_type' => 'destinations',
                      'posts_per_page' => '-1',
                      'post_status' => 'publish',
                      'orderby' => 'title',
                      'order' => 'ASC');
        $destinations       = get_posts( $args );

        $stock              = get_post_meta( $post_id, '_stock', true );
        $base_price         = get_post_meta( $post_id, '_wc_trip_base_price', true );
        $saved_destination  = get_post_meta( $post_id, '_wc_trip_destination', true );
        $trip_type          = get_post_meta( $post_id, '_wc_trip_type', true );
        $start_date         = get_post_meta( $post_id, '_wc_trip_start_date', true );
        $end_date           = get_post_meta( $post_id, '_wc_trip_end_date', true );
        $stock_status       = get_post_meta( $post_id, '_stock_status', true);
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

        $trip_type = $_POST['_wc_trip_type'];

        // Save meta from general tab
        $meta_to_save = array(
            '_wc_trip_base_price'               => 'float',
            '_wc_trip_destination'              => 'string',
            '_wc_trip_type'                     => 'string',
            '_wc_trip_start_date'               => 'date',
            '_wc_trip_end_date'                 => 'date',
            '_wc_trip_stock'                    => 'int',
            '_wc_trip_stock_status'             => 'stockStatus',
            '_wc_trip_includes'                 => 'html',
            '_wc_trip_rates'                    => 'html',
            '_wc_trip_routes'                   => 'html',
            '_wc_trip_partners'                 => 'html',
            '_wc_trip_flight_times'             => 'html',
            '_wc_trip_pics'                     => 'html'
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
                case 'stockStatus':
                    $value = ( $value == "instock" || $value == "outofstock" ? $value : '');
                    break;
                case 'html':
                    $value = $value;
                    break;
                default :
                    $value = sanitize_text_field( $value );
            }
            if ( "_wc_trip_stock" == $meta_key ) {
                update_post_meta( $post_id, "_stock", $value );
            } else if ( "_wc_trip_stock_status" == $meta_key ) {
                update_post_meta( $post_id, "_stock_status", $value );
            } else {
                update_post_meta( $post_id, $meta_key, $value );
            }

            unset($_POST[ $meta_key ]);
        }
        // Primary packages
        $primary_packages = array();
        $primary_label = ( isset($_POST['_wc_trip_primary_package_label']) ? wc_clean($_POST['_wc_trip_primary_package_label']) : '');
        $packages = ( isset($_POST['wc_trips_primary_package_description']) ? sizeof($_POST['wc_trips_primary_package_description']) : 0 );
        for ( $i = 0; $i < $packages; $i++ ) {
            $primary_packages[$i]['description'] = wc_clean( $_POST['wc_trips_primary_package_description'][$i] );
            $primary_packages[$i]['cost'] = wc_clean( $_POST['wc_trips_primary_package_cost'][$i] );
            if ( FALSE !== strpos("$") ) {
                $primary_packages[$i]['cost'] = str_replace("$","",$primary_packages[$i]['cost']);
            }
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

        if ( "beach_bus" === $trip_type ) {
          $packages = ( isset($_POST['wc_trips_package_description']) ? sizeof($_POST['wc_trips_package_description']) : 0 );
          for( $i = 0; $i < $packages; $i++ ){
            $packages_packages[$i]['description'] = wc_clean( $_POST['wc_trips_package_description'][$i] );
            $packages_packages[$i]['cost'] = wc_clean( $_POST['wc_trips_package_cost'][$i] );
          }
          update_post_meta( $post_id, '_wc_trip_primary_package_label', "Package" );
          update_post_meta( $post_id, '_wc_trip_primary_package_stock', "no" );
          update_post_meta( $post_id, '_wc_trip_primary_packages', $packages_packages);

          $packages = ( isset($_POST['wc_trips_to_beach_description']) ? sizeof($_POST['wc_trips_to_beach_description']) : 0 );
          for( $i = 0; $i < $packages; $i++ ){
            $to_beach_packages[$i]['description'] = wc_clean( $_POST['wc_trips_to_beach_description'][$i] );
            $to_beach_packages[$i]['cost'] = wc_clean( $_POST['wc_trips_to_beach_cost'][$i] );
            $to_beach_packages[$i]['stock'] = wc_clean( $_POST['wc_trips_to_beach_stock'][$i] );
          }
          update_post_meta( $post_id, '_wc_trip_secondary_package_label', "To Beach" );
          update_post_meta( $post_id, '_wc_trip_secondary_package_stock', "yes" );
          update_post_meta( $post_id, '_wc_trip_secondary_packages', $to_beach_packages);

          $packages = ( isset($_POST['wc_trips_from_beach_description']) ? sizeof($_POST['wc_trips_from_beach_description']) : 0 );
          for( $i = 0; $i < $packages; $i++ ){
            $from_beach_packages[$i]['description'] = wc_clean( $_POST['wc_trips_from_beach_description'][$i] );
            $from_beach_packages[$i]['cost'] = wc_clean( $_POST['wc_trips_from_beach_cost'][$i] );
            $from_beach_packages[$i]['stock'] = wc_clean( $_POST['wc_trips_from_beach_stock'][$i] );
          }
          update_post_meta( $post_id, '_wc_trip_tertiary_package_label', "From Beach" );
          update_post_meta( $post_id, '_wc_trip_tertiary_package_stock', "yes" );
          update_post_meta( $post_id, '_wc_trip_tertiary_packages', $from_beach_packages);
        }
    }

    public function trip_panels() {
        global $post;
        $post_id = $post->ID;
        wp_enqueue_script( 'wc_trips_admin_js' );
        include( 'views/html-trip-primary-packages.php' );
        include( 'views/html-trip-secondary-packages.php' );
        include( 'views/html-trip-tertiary-packages.php' );
        include( 'views/html-trip-pickup-locations.php' );
        include( 'views/html-trip-includes.php' );
        include( 'views/html-trip-rates.php' );
        include( 'views/html-trip-flight-times.php' );
        include( 'views/html-trip-pics.php' );
        include( 'views/html-trip-routes.php' );
        include( 'views/html-trip-partners.php' );
        include( 'views/html-trip-packages.php' );
        include( 'views/html-trip-to_beach.php' );
        include( 'views/html-trip-from_beach.php' );
    }

    public function script_style_includes() {
        global $post, $woocommerce, $wp_scripts;

        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_style( 'wc_trips_admin_styles', WC_TRIPS_PLUGIN_URL . '/assets/css/trip_admin' . $suffix . '.css', null, WC_TRIPS_VERSION );
        wp_register_script( 'wc_trips_admin_js', WC_TRIPS_PLUGIN_URL . '/assets/js/trips_admin' . $suffix . '.js', array( 'jquery' ) );
        $params = array(
            'nonce_add_pickup_location' => wp_create_nonce( 'add_pickup_location' ),
            'nonce_remove_pickup_location' => wp_create_nonce( 'remove_pickup_location' ),
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
        $nonce      = wp_create_nonce( 'pickup_location'.$post->ID );
        $time       = date("H:i", strtotime(get_post_meta( $post->ID, '_pickup_location_time', true)));
        $address    = get_post_meta( $post->ID, '_pickup_location_address', true);
        $cross_st   = get_post_meta( $post->ID, '_pickup_location_cross_st', true);
        $cost       = get_post_meta( $post->ID, '_pickup_location_cost', true);
        echo <<<META
        <input type="hidden" name="pickup_location_nonce" id="pickup_location_nonce" value="{$nonce}" />

        <label for="_pickup_location_time">Time</label>
        <input type="time" name="_pickup_location_time" value="{$time}" />

        <label for="_pickup_location_address">Street address</label>
        <input type="text" name="_pickup_location_address" value="{$address}" />

        <label for="_pickup_location_cross_st">Cross Streets</label>
        <input type="text" name="_pickup_location_cross_st" value="{$cross_st}" />
        <br /><br />
        <label for="_pickup_location_cost">Cost ($)</label>
        <input type="text" name="_pickup_location_cost" value="{$cost}" />
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
            update_post_meta( $post_id, '_pickup_location_cross_st', sanitize_text_field( $_POST['_pickup_location_cross_st'] ) );
            update_post_meta( $post_id, '_pickup_location_cost', sanitize_texT_field($_POST['_pickup_location_cost']) );
        } else {
            return $post_id;
        }
    }

    public function destination_columns_head( $defaults ) {
        unset( $defaults['dfcg_image_col'] );
        unset( $defaults['dfcg_desc_col'] );

        unset( $defaults['expirationdate'] );

        return $defaults;
    }

    public function destinations_meta_boxes() {
        add_meta_box(
                'destination_trail_map',
                'Destination Info',
                array( $this, 'destination_fields'),
                'destinations',
                'normal'
            );
    }

    public function save_destination_meta( $post_id ) {
        $nonce_check = wp_verify_nonce( $_POST['destination_attachment_nonce'], plugin_basename(__FILE__));
        if ( 1 == $nonce_check || 2 == $nonce_check ) {
            // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
            if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
                return $post_id;
            }

            // Check permissions
            if ( !current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }

            update_post_meta( $post_id, '_trail_map', sanitize_text_field( $_POST['upload_trail_map'] ) );
            update_post_meta( $post_id, '_contact', sanitize_text_field( $_POST['_contact'] ) );
            update_post_meta( $post_id, '_contact_phone', sanitize_text_field( $_POST['_contact_phone'] ) );
            update_post_meta( $post_id, '_rep', sanitize_text_field( $_POST['_rep'] ) );
            update_post_meta( $post_id, '_rep_phone', sanitize_text_field( $_POST['_rep_phone'] ) );
        } else {
            return $post_id;
        }
    }

    public function destination_fields( $post ) {
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('destinations_upload', WC_TRIPS_PLUGIN_URL . '/assets/js/destinations.js', array('jquery','media-upload','thickbox'));
        wp_nonce_field(plugin_basename(__FILE__), 'destination_attachment_nonce');
        $map = get_post_meta( $post->ID, '_trail_map', true);
        $contact = get_post_meta( $post->ID, '_contact', true);
        $contactPhone = get_post_meta( $post->ID, '_contact_phone', true);
        $rep = get_post_meta( $post->ID, '_rep', true);
        $repPhone = get_post_meta( $post->ID, '_rep_phone', true);
        $html = <<<TRAILMAP
             <label>Trail Map</label><input id="upload_trail_map" type="text" size="36" name="upload_trail_map" value="{$map}" />
             <input id="upload_trail_map_button" type="button" value="Select Trail Map" />
             <br />
             <label>Contact: </label><input type="text" size="36" name="_contact" value="{$contact}" />
             <br />
             <label>Contact Phone: </label><input type="text" size="20" name="_contact_phone" value="{$contactPhone}" />
             <br />
             <label>Rep: </label><input type="text" size="36" name="_rep" value="{$rep}" />
             <br />
             <label>Rep Phone: </label><input type="text" size="20" name="_rep_phone" value="{$repPhone}" />
TRAILMAP;
        echo $html;
    }

    public function pickup_columns_head( $defaults ) {
        // Remove dynamic gallery columns, these columns are not applicable to this post type
        unset( $defaults['dfcg_desc_col'] );
        unset( $defaults['dfcg_image_col'] );

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
            if ( ! $time || "" === $time) {
                echo "";
            } else {
                echo date("g:i a", strtotime($time));
            }
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

    public function wc_trip_pickup_location_duplicate_post_link( $actions, $post ) {
        $screen = get_current_screen();
    	if (current_user_can('edit_posts') && "pickup_locations" == $screen->post_type) {
    		$actions['duplicate'] = '<a href="admin.php?action=wc_trips_duplicate_pickup&amp;post=' . $post->ID . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
    	}
    	return $actions;
    }

    public function add_pickup_location() {
        check_ajax_referer( "add_pickup_location", 'nonce');

        $post_id          = intval( $_POST['post_id'] );
        $pickup_count     = intval( $_POST['pickupCount'] );
        $new_pickup_id    = intval( $_POST['new_pickup_id'] );
        $new_pickup_name  = wc_clean( $_POST['new_pickup_name'] );
        $existing_pickups = get_post_meta( $post_id, '_wc_trip_pickups', true);

        if ( ! $existing_pickups){
            $existing_pickups = array();
        }

        header( 'Content-Type: application/json charset=utf-8');

        if ( in_array( $new_pickup_id, $existing_pickups) ) {
            die( json_encode( array( 'error' => 'Pickup already linked to this trip') ) );
        }

        if ( ! $new_pickup_id ) {
            $pickup = array(
                'post_title' => $new_pickup_name,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_type' => 'pickup_locations'
            );
            $pickup_id = wp_insert_post( $pickup );
        } else {
            $pickup_id = $new_pickup_id;
        }

        if ( $pickup_id ) {
            // Update pickups on trip
            $updated_pickups = array_merge($existing_pickups, array(0 => $pickup_id));
            update_post_meta( $post_id, '_wc_trip_pickups',$updated_pickups);

            // Send HTML back to JS
            ob_start();
            $location_id = $pickup_id;
            $location = get_post( $location_id );
            $count = $pickup_count;
            $location_time = get_post_meta( $location_id, '_pickup_location_time ', true);
            if ( $location_time ) {
                $location_time = " - " . date("g:i a", strtotime($location_time));
            }
            include( 'views/html-trip-pickup-location.php' );
            die( json_encode( array( 'html' => ob_get_clean() ) ) );
        }

        die( json_encode( array( 'error' => 'Unable to add pickup location' ) ) );
    }

    public function remove_pickup_location() {

        check_ajax_referer( "remove_pickup_location", 'nonce');

        header( 'Content-Type: application/json charset=utf-8');

        $post_id = absint( $_POST['post_id'] );
        $location_id = absint( $_POST['location_id'] );
        $locations = get_post_meta( $post_id, '_wc_trip_pickups', true);
        $removed = FALSE;

        if ( gettype($locations) !== "array" ) {
            $removed = TRUE;
            /* Location was never saved but somehow made it to the UI
            and no locations have been saved to this trip
            */
        } else {
            if ( in_array($location_id, $locations) ) {
                unset( $locations[array_search( $location_id, $locations )] );
                $removed = TRUE;
                if ( ! update_post_meta( $post_id, '_wc_trip_pickups', $locations) ) {
                    $removed = FALSE;
                }
            }
        }

        if ( $removed ) {
            die( json_encode( array( 'removed' => TRUE ) ) );
        } else {
            die( json_encode( array( 'error' => 'unable to remove location' ) ) );
        }
    }
}
new WC_Trips_Admin();
