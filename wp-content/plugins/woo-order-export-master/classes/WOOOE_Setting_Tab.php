<?php
if(!defined('ABSPATH')){
    exit;
}

if( !class_exists('WOOE_Setting_Tab', false) ){

    class WOOOE_Setting_Tab extends WC_Settings_Page{

        //Constructor
        function __construct() {

            $this->id       = 'woooe';
            $this->label    = __( 'Order Export', 'woooe' );
            parent::__construct();

            add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 200 );
            add_filter( 'woocommerce_settings_tabs_woooe', array( $this, 'settings' ) );
            add_filter( 'woocommerce_update_options_woooe', array($this, 'update_settings') );
            add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
            add_action( 'woocommerce_admin_field_export_button', array( $this, 'export_button' ) );
            add_action( 'woocommerce_admin_field_woooe_reorder', array( $this, 'woooe_reorder' ) );
            add_action( 'woocommerce_admin_field_woooe_field_filter', array( $this, 'woooe_field_filter' ) );
            add_action( 'woocommerce_settings_saved', array( $this, 'save' ) );
        }

        /*
         * Adds setting tab to woocommerce setting page
         */
        function add_settings_tab( $settings_tabs ){
            $settings_tabs['woooe'] = __( 'Order Export', 'woooe' );
            return $settings_tabs;
        }

        /*
         * Add settings fields to settings tab
         */
        function settings() {

            global $current_section, $woooe;

            if(in_array( $current_section, array('', 'general')) ){
                woocommerce_admin_fields( $woooe->get_settings('general') );
            }

            if( 'advanced' == $current_section ){
                woocommerce_admin_fields( $woooe->get_settings('advanced') );
            }

            do_action('woooe_settings_section');
        }

        /*
         * Save settings
         */
        function update_settings(){
            global $current_section, $woooe;

            if(in_array( $current_section, array('', 'general')) ){
                woocommerce_update_options( $woooe->get_settings('general') );
            }

            if( 'advanced' == $current_section ){
                woocommerce_update_options( $woooe->get_settings('advanced') );
            }
        }

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
                $sections['general'] = __('General', 'woooe');
                $sections['advanced'] = __('Advanced', 'woooe');
		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	    /**
	     * Output sections.
	     */
	    public function output_sections() {
		    global $current_section;

		    $current_section = empty( $current_section ) ? 'general' : $current_section;
		    $sections        = $this->get_sections();

		    if ( empty( $sections ) || 1 === sizeof( $sections ) ) {
			    return;
		    }

		    echo '<ul class="subsubsub">';

		    $array_keys = array_keys( $sections );

		    foreach ( $sections as $id => $label ) {
			    echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
		    }

		    echo '</ul><br class="clear" />';
	    }

	    /*
		 * Renders export button
		 */
	    function export_button( $value ) {
		    ?>

            <tr valign="top">
            <th></th>
            <td class="forminp">
                <input class="button btn" id="<?php echo $value['id']; ?>" type="button"
                       value="<?php echo $value['name']; ?>"/>
                <div id="woooe-error-msg"></div>
                <div id="woooe-loader" style="margin-top: 10px; display: none;">
                    <img src="<?php echo WOOOE_BASE_URL . '/assets/img/ajaxloader.gif' ?>" style="vertical-align: middle;" alt="<?php _e( 'Please wait...', 'woooe' ) ?>"/>
                    <span><?php _e( 'Please do not refresh or close this page.', 'woooe' ); ?></span>
                </div>
            </td>
            </tr><?php
	    }

        /*
         * Reorder/Rename fields
         */
        function woooe_reorder(){

            $reorder_options = get_option('woooe_reorder_rename', array()); ?>

            <tr>
                <td style="padding-left: 0;" class="forminp" colspan="2">
                    <section class="woooe-reorder-section"><?php

                        if(!empty($reorder_options)){

                            foreach($reorder_options as $id=>$name){?>

                                <div class="reorder-row">
                                    <div class="rename">
                                        <input type="text" name="woooe_field_names[]" value="<?php echo stripslashes($name); ?>" autocomplete="off" />
                                        <input type="hidden" name="woooe_field_ids[]" value="<?php echo $id; ?>" />
                                    </div>
                                    <div class="reorder">
                                        <a href="#" title="<?php _e('Move Up', 'woooe') ?>" class="dashicons dashicons-arrow-up-alt2 woooe-move woooe-up"></a>
                                        <a href="#" title="<?php _e('Move Down', 'woooe') ?>" class="dashicons dashicons-arrow-down-alt2 woooe-move woooe-down"></a>
                                    </div>
                                </div><?php

                            }
                        }else{?>
                            <p><?php _e('There are no fields to export.', 'woooe'); ?></p><?php
                        }?>
                    </section>
                </td>
            </tr><?php
        }

	    /**
         * Filter fields based on choice.
         * Make them hide/show.
	     */
	    function woooe_field_filter( $args ) {

	        if( empty( $args['filters'] ) || !is_array( $args['filters'] ) ){
	            return;
            }

	        $btn_classes = 'button current field-filter';
	        ?>

            <ul id="wooe-fields-filter"><?php

                foreach( $args['filters'] as $key=>$val ) {?>
                    <li><a data-filter="<?php echo sanitize_title($val) ?>" class="<?php echo $btn_classes; ?>" href="#"><?php echo $val; ?></a></li><?php
                    $btn_classes = str_replace( 'current ', '', $btn_classes );
                }?>
            </ul><?php

        }


        /*
         * Save the settings
         */
        function save(){

            global $current_section, $current_tab, $woooe;

            if('woooe' === $current_tab){

                if( ('advanced' === $current_section) ){

                    $field_ids      = !empty($_POST['woooe_field_ids']) ? $_POST['woooe_field_ids'] : array();
                    $field_names    = !empty($_POST['woooe_field_names']) ? $_POST['woooe_field_names'] : array();

                    $reorder_settings = array_combine($field_ids, $field_names);
                    $reorder_settings = array_map(function($element){return sanitize_text_field($element);}, $reorder_settings);

                    $update = update_option('woooe_reorder_rename', $reorder_settings, false);
                }

                //General section can be empty as well for `woooe` page.
                if( empty($current_section) || ('general' === $current_section) ){

                    $reorder_settings = get_option('woooe_reorder_rename', array());
                    $post_values = !empty($_POST) ? ($_POST) : array();

                    $fields_to_export = WOOOE_Data_Handler::fields_to_export(true);
                    $total_fields = wp_list_pluck($fields_to_export, 'name', 'id');

                    $updated_fields_to_export = array_intersect_key($total_fields, $post_values);
                    $to_add = array_diff_key($updated_fields_to_export, $reorder_settings);
                    $to_remove = array_diff_key($reorder_settings, $updated_fields_to_export);

                    foreach($to_add as $k=>$v){
                        $reorder_settings[$k] = $v;
                    }

                    foreach($to_remove as $k=>$v){
                        unset($reorder_settings[$k]);
                    }

                    $update = update_option('woooe_reorder_rename', $reorder_settings, false);
                }
            }
        }
    }

    return new WOOOE_Setting_Tab();
}