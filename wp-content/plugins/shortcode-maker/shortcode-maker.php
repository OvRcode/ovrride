<?php
/*
 * Plugin Name: Shortcode Maker
 * Description: A plugin to to let users make shortcodes of their own and use them in wp editor
 * Plugin URI: http://cybercraftit.com/product/shortcode-maker/
 * Author URI: http://cybercraftit.com/
 * Author: Mithu A Quayium
 * Text Domain: shortcode-maker
 * Domain Path: /languages
 * Version: 3.0.1
 * License: GPL2
 */
/**
 * Copyright (c) YEAR Mithu A Quayium (email: cemithu06@gmail.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class shortcode_maker{

	private $shorcode_array = array();

    function __construct(){

        $this->shorcode_array = get_option('shortcode_list');

        add_action('init',array($this,'register_shortcode'));
		add_action('save_post',array($this,'save_post_func'));

        add_action('init',array($this,'shortcode_add'));

        add_action('admin_init', array($this, 'sm_shortcode_button'));
        add_action('admin_footer', array($this, 'sm_get_shortcodes'));
		add_action('wp_trash_post',array($this,'remove_shortcode'));

        add_action( 'admin_head', array( $this , 'shortcode_array_js' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_styles' ) );
        //ajax
        add_action( 'wp_ajax_show_shortcodes', array( $this, 'render_shortcode_modal' ) );
        add_action( 'wp_ajax_sm_get_shortcode_atts', array( $this, 'get_shortcode_atts_panel' ) );

        add_action( 'init', array($this, 'load_textdomain') );


        $this->includes();
	}

    function includes(){
        require_once dirname(__FILE__).'/cc-products-page.php';
        require_once dirname(__FILE__).'/shortcode-field.php';
    }

    /**
     * Load the translation file for current language.
     *
     * @since version 2.2
     */
    function load_textdomain() {
        load_plugin_textdomain( 'shortcode-maker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }


    /**
     * Convert shortcode array to js
     */
    function shortcode_array_js() {
        ?>
        <script>
            var shortcode_array = '<?php echo json_encode($this->shorcode_array); ?>';
        </script>
    <?php
    }

    /**
     * Render the shortcode modal
     */
    function render_shortcode_modal() {
        $shortcode_array =  json_decode( stripslashes( $_POST['shortcode_array'] ), true );
        //is_array( $shortcode_array ) ? '' : $shortcode_array = array();
        ?>

        <div id="sm-modal" class="modal">

            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">×</span>
                <h3><?php _e( 'Shortcodes - Shortcode Maker' , 'shortcode-maker' ); ?></h3>
                <hr/>
                <?php
                echo '<div class="sm_shortcode_list">';
                    echo '<ul>';
                    foreach( $shortcode_array as $id => $shortcode ) {
                        ?>
                        <li data-id="<?php echo $id; ?>">
                            <?php echo $shortcode; ?>
                        </li>
                    <?php
                    }
                    echo '</ul>';
                echo '</div>';
                ?>
            </div>

        </div>
        <?php
        exit;
    }

    /**
     * get shortcode attribute panel
     */
    public function get_shortcode_atts_panel() {
        $shortcode_atts = get_post_meta( $_POST['shortcode_id'], 'sm_shortcode_atts' , true );
        if( !empty( $shortcode_atts ) ) : ?>

        <div id="sm-modal-atts" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">×</span>
                <h3><?php _e( 'Attributes for ', 'shortcode-maker' ); ?><?php echo $_POST['tag']; ?></h3>
                <hr/>
                <?php
                echo '<div class="sm_shortcode_atts">';
                ?>
                <script>
                    var shortcode_atts = '<?php echo json_encode( SM_Shortcode_Field::convert_to_js_meta( $shortcode_atts ) );?>';
                </script>
                <ul>
                    <li v-for="( key, attr ) in shortcode_atts">
                        {{ attr.name }}
                        <input type="text" v-model="attr.value" name="shortcode_atts[{{ key }}][value]">
                    </li>
                </ul>

                <button class="shortcode_atts_ok "><?php _e( 'Okay', 'shortcode-maker' ) ?></button>
                <button class="shortcode_atts_cancel "><?php _e( 'Cancel', 'shortcode-maker' ) ?></button>
                <?php
                echo '</div>';
                ?>
                <script>
                    var sm_attrs = new Vue({
                        el: '#wpwrap',
                        data : {
                            shortcode_atts : JSON.parse(shortcode_atts)
                        },
                        methods : {
                            add_attr_box : function(){
                                this.shortcode_atts.push({
                                    name : 'Attribute name',
                                    value : 'Attribute value'
                                });
                            }
                        }
                    });
                </script>
            </div>
        </div>
        <?php endif; ?>
        <?php
        exit;
    }

    /**
     * Register shortcode
     */
	function register_shortcode(){
		$labels = array(
				'name' => _x('Shortcode', 'post type general name', 'shortcode-maker'),
				'singular_name' => _x('Shortcode', 'post type singular name','shortcode-maker'),
				'menu_name' => _x( 'Shortcode', 'admin menu', 'shortcode-maker'),
				'name_admin_bar' => _x( 'Shortcode', 'add new on admin bar', 'shortcode-maker'),
				'add_new' => _x('Add New Shortcode', 'Shortcode' , 'shortcode-maker' ),
				'add_new_item' => __('Add New Shortcode', 'shortcode-maker'),
				'edit_item' => __('Edit Shortcode', 'shortcode-maker'),
				'new_item' => __('New Shortcode' , 'shortcode-maker' ),
				'view_item' => __('View Shortcode', 'shortcode-maker' ),
				'all_items' => __( 'All Shortcode', 'shortcode-maker' ),
				'search_items' => __('Search Shortcode', 'shortcode-maker' ),
				'not_found' =>  __('Nothing found', 'shortcode-maker' ),
				'not_found_in_trash' => __('Nothing found in Trash', 'shortcode-maker' ),
				'parent_item_colon' => '',
				
			);
			$args = array(
				'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'sm_shortcode'),
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => 3,
			'supports' => array(
				'title',
				'editor',
				),
		
			);
			register_post_type( 'sm_shortcode' , $args );
	}

    /**
     * shortcode_list option updated
     * @param $post_id
     */
	function save_post_func($post_id){

		if( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'sm_shortcode'){
			$this->shorcode_array[$post_id] = get_the_title($post_id);
			update_option('shortcode_list',$this->shorcode_array);
			
		}
	}

    /**
     * Add shortocode
     */
	function shortcode_add(){

		if(!is_array($this->shorcode_array))return;

		foreach($this->shorcode_array as $each_shortcode){
			add_shortcode($each_shortcode,array($this,'shortcode_content'));
		}
	}

    /**
     * Eacch shortcode definition
     * @param $atts
     * @param null $content
     * @param $tag
     * @return mixed
     */
    function shortcode_content($atts,$content = NULL,$tag){

        if(in_array($tag,$this->shorcode_array)){

            $post_id = array_search($tag,$this->shorcode_array);
            $default_atts = get_post_meta( $post_id, 'sm_shortcode_atts', true );

            $atts = shortcode_atts(
                $default_atts,
                $atts, $tag );

            $search = array();
            $replace = array();

            foreach( $atts as $att_name => $att_value ) {
                $search[] = '%' . $att_name . '%';
                $replace[] =  $att_value ;
            }

            if( !$content ) {
                $content = do_shortcode( get_post($post_id)->post_content );
            }

            return str_replace( $search, $replace, $content );
		}
	}


    /**
     * Remove the shortcode if
     * shortcode is deleted
     * @param $post_id
     */
	function remove_shortcode($post_id){

        if(get_post_type($post_id) == 'sm_shortcode'){
            unset($this->shorcode_array[$post_id]);
            update_option('shortcode_list',$this->shorcode_array);
        }
	}


	function sm_shortcode_button()
    {
        if( current_user_can('edit_posts') &&  current_user_can('edit_pages') )
        {
            add_filter( 'mce_external_plugins', array($this, 'sm_add_buttons' ));
            add_filter( 'mce_buttons', array($this, 'sm_register_buttons' ));
        }
    }
	function sm_add_buttons( $plugin_array )
    {
		if(get_bloginfo('version') >= 3.9){
	        $plugin_array['pushortcodes'] = plugin_dir_url( __FILE__ ) . 'js/shortcode-tinymce-button.js';
		}else{
			$plugin_array['pushortcodes'] = plugin_dir_url( __FILE__ ) . 'js/shortcode-tinymce-button-older.js';
		}


        return $plugin_array;
    }
	function sm_register_buttons( $buttons )
    {
        array_push( $buttons, 'separator', 'pushortcodes' );
        return $buttons;
    }
	function sm_get_shortcodes()
    {
        global $shortcode_tags;

        echo '<script type="text/javascript">
        var shortcodes_button = new Array();';

        $count = 0;

        foreach($shortcode_tags as $tag => $code)
        {
            echo "shortcodes_button[{$count}] = '{$tag}';";
            $count++;
        }

        echo '</script>';
    }

    /**
     * Add scripts and styles
     */
    public function admin_enqueue_scripts_styles() {
        global $post;
        wp_enqueue_style( 'sm-style', plugins_url('css/style.css',__FILE__) );
        wp_enqueue_script( 'sm-vue-js', plugins_url('js/vue.js',__FILE__) );

        if( isset( $post->ID ) && get_post_type( $post->ID ) == 'sm_shortcode' ) {
            wp_enqueue_script( 'sm-script-js', plugins_url('js/script.js',__FILE__), array( 'sm-vue-js' ) );
        }

    }
}
new shortcode_maker;

// add plugin upgrade notification
add_action('in_plugin_update_message-shortcode-maker/index.php', 'showUpgradeNotification', 10, 2);
function showUpgradeNotification($currentPluginMetadata, $newPluginMetadata){
    // check "upgrade_notice"
    if (isset($newPluginMetadata->upgrade_notice) && strlen(trim($newPluginMetadata->upgrade_notice)) > 0){
        echo '<p style="background-color: #d54e21; padding: 10px; color: #f9f9f9; margin-top: 10px"><strong>Important Upgrade Notice:</strong></p> ';
        echo esc_html($newPluginMetadata->upgrade_notice), '</p>';
    }
}