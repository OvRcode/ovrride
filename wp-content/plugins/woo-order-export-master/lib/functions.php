<?php
/*
 * Get all order statuses in an array.
*/

if(!function_exists('woooe_order_statuses')){
    function woooe_order_statuses() {

        //Get all valid statuses
        $statuses = wc_get_order_statuses();
        $fields = array();

        foreach($statuses as $key=>$status){

            $field = array(
                'name' => $status,
                'type' => 'checkbox',
                'id' => 'wooe_order_status_'. $key
            );

            array_push($fields, $field);
        }

        return $fields;

    }
}

/*
 * Add section start to order statuses section.
 */
if(!function_exists('woooe_order_statuses_section_start')){
    function woooe_order_statuses_section_start($fields){

        $status_section_start = array(
                                'name'     => __( 'Select order statuses to export', 'woooe' ),
                                'type'     => 'title',
                                'desc'     => '',
                                'id'       => 'woooe_title'
                            );

        array_unshift($fields, $status_section_start);

        return $fields;
    }
}

/*
 * Add section end to order statuses section.
 */
if(!function_exists('woooe_order_statuses_section_end')){
    function woooe_order_statuses_section_end($fields){

        $status_section_end = array(
                            'type' => 'sectionend',
                            'id' => 'woooe_title'
                        );

        array_push($fields, $status_section_end);

        return $fields;
    }
}

/*
 * Add settings page link
 */
if(!function_exists('woooe_action_link')){
    function woooe_action_link($links){
        $settings_url = add_query_arg(array('page'=>'wc-settings', 'tab'=>'woooe'), admin_url('admin.php'));
        $settings_link = array(sprintf('<a href="%s">'.__('Settings', 'woooe').'</a>', $settings_url));
        return array_merge($settings_link, $links );
    }

}

/*
 * Re-buld reordering
 */
if(!function_exists('woooe_rebuild_reordering')){

    function woooe_rebuild_reordering(){

        $update = false;

        //Get reorder fields
        $reorder_settings = get_option('woooe_reorder_rename', array());

        //Get exportable fields
        $fields_to_export = WOOOE_Data_Handler::fields_to_export(true);
        $total_fields = wp_list_pluck($fields_to_export, 'name', 'id');

        foreach($reorder_settings as $key=>$val){

            if(!array_key_exists($key, $total_fields)){
                unset($reorder_settings[$key]);
                $update = true;
            }
        }

        if($update){
            $update = update_option('woooe_reorder_rename', $reorder_settings, 'no');
        }
    }
    add_action('woooe_rebuild_reordering', 'woooe_rebuild_reordering');
}

/*
 * Format prices
 */
if(!function_exists('woooe_format_price')){
    
    function woooe_format_price($price = '', $currency = ''){
        
        $price = wc_price($price, array('currency'=>$currency));
        
        //Strip tags from returned value
        $price = strip_tags($price);
        
        //Decode html entities so as to view currency symbols properly
        $price = html_entity_decode($price);
        
        return $price;
    }
}

/**
 * Display add-on purchase notice.
 */
if(!function_exists('woooe_addon_notice')){

    function woooe_addon_notice(){
        global $woooe_addon;
        
        $hide_notice = get_transient('woooe_addon_notice_hide');

        if(!$hide_notice){
            /*
             * Show this notice if new version (3.4) of add-on plugin
             * is not installed and older version is not activated.
             */
            if((!is_object($woooe_addon) || !is_a($woooe_addon, 'OE_ADDON')) && !is_plugin_active('woocommerce-simply-order-export-add-on/main.php')){
                require trailingslashit(WOOOE_BASE). 'views/woooe-addon.php';
            }
        }
    }
    add_action('admin_notices', 'woooe_addon_notice');
}

/**
 * Show notice for old add-on plugins.
 * Ask to update to the newer version of add-on.
 */
if(!function_exists('woooe_update_addon')){

    function woooe_update_addon(){
        /*
         * Show this notice only if older version is activated.
         */
        if(is_plugin_active('woocommerce-simply-order-export-add-on/main.php')){
            require trailingslashit(WOOOE_BASE). 'views/html-notice-addon-update.php';
        }
    }
    add_action('admin_notices', 'woooe_update_addon');
}

/**
 * Set the transient for dismissing the notice.
 */
if ( ! function_exists( 'woooe_dismiss_addon_notice' ) ) {

	function woooe_dismiss_addon_notice() {

		$set = set_transient( 'woooe_addon_notice_hide', 1, HOUR_IN_SECONDS * 24 );

		if ( ! $set ) {
			wp_send_json_error( __( 'Something went wrong. Please try again.', 'woooe' ) );
		}

		wp_send_json_success();
	}

	add_action( 'wp_ajax_addon_notice_dismiss', 'woooe_dismiss_addon_notice' );
}

/**
 * Stores a random hash for use in openssl encrypt functions.
 */
if ( ! function_exists( 'woooe_set_random_hash' ) ) {

	function woooe_set_random_hash() {

		$woooe_salt = wp_generate_password();
		update_option( 'woooe_salt', $woooe_salt );

		return $woooe_salt;
	}
}

/**
 * Retrieves random hash for use in openssl encrypt functions.
 */
if ( ! function_exists( 'woooe_get_random_hash' ) ) {

	function woooe_get_random_hash() {

		$woooe_salt = get_option( 'woooe_salt' );

		if ( empty( $woooe_salt ) ) {
			$woooe_salt = woooe_set_random_hash();
		}

		return $woooe_salt;
	}
}

/**
 * If the page is getting redirected at downloading csv,
 * generate a new salt and save in database
 */
if( !function_exists('woooe_fix_redirect') ){

    function woooe_fix_redirect(){

        if(!empty($_GET['woooe_fix_redirect']) && wp_verify_nonce($_GET['woooe_fix_redirect'], 'woooe_fix_redirect') ){
            $woooe_salt = woooe_set_random_hash();
            add_settings_error('woooe_fix', 'woooe_redirect_fix', __('Redrection issue fixed. Please try again downloading export file.'), 'updated');
        }
    }
    add_action('admin_init', 'woooe_fix_redirect');
}

/*
 * Button to fix redirection issue while downloading export file.
 */
if( !function_exists('woooe_fix_redirect_tool') ){
    
    function woooe_fix_redirect_tool(){?>
        <div class="card">
            <h2 class="title"><?php _e( 'WooCommerce Simply Order Export', 'woooe' ) ?></h2>
            <?php settings_errors('woooe_fix'); ?>
            <p><?php _e('Click this button if page getting redirect while downloading export file.', 'woooe'); ?></p>
            <p>
                <a class="button" href="<?php echo add_query_arg('woooe_fix_redirect', wp_create_nonce('woooe_fix_redirect') ,admin_url('tools.php')) ?>">
                <?php _e('Fix Redirection', 'woooe');?>
                </a>
            </p>
	</div><?php
    }
    add_action('tool_box', 'woooe_fix_redirect_tool');
}

/**
 * Runs the script for update. If any update of the plugin has update script available,
 * it would seek for the script and if script is available, it will run the script.
 */
if( !function_exists('woooe_core_run_update_scripts') ){

	function woooe_core_run_update_scripts() {

                global $woooe;

		// Run this block if current user is woocommerce manager and is in admin/dashboard.
		if( is_admin() && current_user_can('manage_woocommerce') ){

			$db_version = get_option( 'woooe_version', '3.0.0' ); // Check what was the last version
			$upgrade_ran = $upgrade_ran_new = get_option( 'woooe_upgrade_ran', array() );

			$update_scripts = array(); // Array of version numbers for which update scripts are present.

			if( version_compare( $db_version, $woooe->version, '<' ) ) {

                            foreach( $update_scripts as $script ) {

                                // Check if this update script has already been executed, if it is, do not execute it again.
                                if( (version_compare( $script, $woooe->version, '<=' )) && ( !in_array( $script, $upgrade_ran ) ) ) {

                                        require_once trailingslashit( WOOOE_BASE ).'lib/update/woooe-'.$script.'.php';
                                        array_push( $upgrade_ran_new, $script );
                                }
                            }

                            $diff = array_diff( $upgrade_ran_new, $upgrade_ran );

                            if( !empty( $diff ) ){
                                update_option( 'woooe_upgrade_ran', $upgrade_ran_new );
                            }

                            update_option( 'woooe_version', $woooe->version );
			}
		}
	}
}