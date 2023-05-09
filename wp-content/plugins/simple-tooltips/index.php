<?php

/*
Plugin Name: Simple Tooltips
Description: Easily add tooltips to your wordpress site. You can define tooltip color settings in <strong>Settings > Simple Tooltips</strong>
Version: 2.1.3
Author: Justin Saad
Author URI: http://www.clevelandwebdeveloper.com
License: GPL2
*/

$plugin_label = "Simple Tooltips";
$plugin_slug = "simple_tooltips";

class simple_tooltips {

    public function __construct(){
    	
		global $plugin_label, $plugin_slug;
		$this->plugin_slug = $plugin_slug;
		$this->plugin_label = $plugin_label;
		$this->custom_tooltips = array();
		
		//enqueue color picker
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_color_picker') );
		
		//plugin row links
		add_filter( 'plugin_row_meta', array($this,'plugin_row_links'), 10, 2 );
		
		add_shortcode('simple_tooltip', array($this, 'addShortcodeHandler'));
		
        if(is_admin()){
		    add_action('admin_menu', array($this, 'add_plugin_page'));
		    add_action('admin_init', array($this, 'page_init'));
			//add Settings link to plugin page
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this, 'add_plugin_action_links') );
			//custom jquery for admin page
			add_action('admin_footer', array($this,'motech_tooltips_admin_jquery'));			
		}

		add_filter( 'tiny_mce_version', array($this, 'my_refresh_mce'));
		
		//add_action('init', array($this, 'add_custom_button')); 
		
		add_action('init', array($this, 'load_tooltips')); //loads on wordpress init
		
		add_action('init', array($this, 'add_custom_button')); 
		
    }
	
	function enqueue_color_picker( $hook_suffix ) {
		// first check that $hook_suffix is appropriate for your admin page
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'my-script-handle', plugins_url('motech-color-picker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
	}

    public function add_plugin_page(){
        // This page will be under "Settings"
		add_options_page('Settings Admin', $this->plugin_label, 'manage_options', $this->plugin_slug.'-setting-admin', array($this, 'create_admin_page'));
    }
	
    public function print_section_info(){ //section summary info goes here
		print 'Set the default settings for your tooltips. For instructions on how to use the tooltips, <a href="http://www.clevelandwebdeveloper.com/wordpress-plugins/simple-tooltips/" target="_blank">visit the plugin page &raquo;</a>';
    }
	
    public function get_donate_button(){ ?>
	<style type="text/css">
	#wpbody {min-width: 900px;}
	.motechdonate{border: 1px solid #DADADA; background:white; font-family: tahoma,arial,helvetica,sans-serif;font-size: 12px;overflow: hidden;padding: 5px;position: absolute;right: 0;text-align: center;top: 0;width: 275px; box-shadow:0px 0px 8px rgba(153, 153, 153, 0.81);z-index:9;}
	<?php if(function_exists('is_rtl')) : ?>
		<?php if(is_rtl()) : ?>
			.motechdonate {right:auto;left:0;}
		<?php endif ?>
	<?php endif ?>	
	.motechdonate form{display:block;}
	#motech_top_banner {background: rgb(221, 215, 215);margin: -5px;margin-bottom: 7px;padding-bottom: 4px;line-height: 16px;font-size: 18px;text-indent: 6px;}
	.motechdonate ul {padding-left:16px;}
	.motechdonate li {list-style-type: disc;list-style-position: outside;}
	</style>
    <div class="motechdonate">
        <div style="width: 276px; text-align: left;">
        	<div id="motech_top_banner">Ways to say thanks</div>
        <div style="overflow: hidden; width: 276px; text-align: left; float: left;">
        <div>A lot of effort went into the development of this plugin. You can say 'Thank You' by doing any of the following</div>
        <ul>
        <li>Donate a few dollars to my company The Motech Network to help with future development and updates.
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input name="cmd" value="_s-xclick" type="hidden"><input name="hosted_button_id" value="9TL57UDBAB7LU" type="hidden"><input type="hidden" name="no_shipping" value="1"><input type="hidden" name="item_name" value="The Motech Network Plugin Support - <?php echo $this->plugin_label ?>" /><input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" type="image"> <img src="https://www.paypal.com/en_US/i/scr/pixel.gif" alt="" border="0" height="1" width="1"></form>        
        </li>
        <li>Follow me on Twitter <a href="https://twitter.com/ClevelandWebDev" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @ClevelandWebDev</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></li>

        <li>Follow me on Google+<br />
            <!-- Place this tag where you want the widget to render. -->
            <div class="g-follow" data-annotation="none" data-height="24" data-href="//plus.google.com/111016169202309022990" data-rel="author"></div>
            
            <!-- Place this tag after the last widget tag. -->
            <script type="text/javascript">
              (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
              })();
            </script>
        </li>
         <li>Connect with me on <a href="http://www.linkedin.com/in/ClevelandWebDeveloper/" target="_blank">LinkedIn</a></li>
         <li><a href="http://wordpress.org/support/view/plugin-reviews/simple-tooltips" target="_blank" title="Rate it">Rate it</a> on WordPress.org</li>
         <li>Blog about it & link to the <a href="http://www.clevelandwebdeveloper.com/wordpress-plugins/simple-tooltips/" target="_blank">plugin page</a></li>
         <li>Check out my other <a href="http://www.clevelandwebdeveloper.com/wordpress-plugins/" target="_blank">WordPress plugins</a></li>
         <li><a href="mailto:info@clevelandwebdeveloper.com" target="_blank">Email me</a> to say thanks. If you can let me know where my plugins are being used 'in the wild' I always appreciate that.</li>
        </ul>
        <div>Thanks in advance for your support.</div>
        <div style="font-style:italic;">-Justin</div>
        </div>
        </div>
	</div>    
    
    <?php

    }
	
    public function create_admin_page(){
        ?>
		<div class="wrap" style="position:relative;">
        	<?php $this->get_donate_button() ?>
		    <?php screen_icon(); ?>
		    <h2><?php echo $this->plugin_label ?></h2>			
		    <form method="post" action="options.php">
		        <?php
	            // This prints out all hidden setting fields
			    settings_fields($this->plugin_slug.'_option_group');	
			    do_settings_sections($this->plugin_slug.'-setting-admin');
			?>
		        <?php submit_button(); ?>
		    </form>
		</div>
	<?php
    }
    
    public function page_init(){
        	
		//create settings section
        add_settings_section(
		    $this->plugin_slug.'_setting_section',
		    'Configuration',
		    array($this, 'print_section_info'),
		    $this->plugin_slug.'-setting-admin'
		);	
		
		//add color picker text input field
		$field_slug = "background_color";
		$field_label = "Background Color:";
		$field_id = $this->plugin_slug.'_'.$field_slug;
		register_setting($this->plugin_slug.'_option_group', $field_id);
		add_settings_field(
		    $field_id,
		    $field_label, 
		    array($this, 'create_a_text_input'), //callback function for text input
		    $this->plugin_slug.'-setting-admin',
		    $this->plugin_slug.'_setting_section',
		    array(								// The array of arguments to pass to the callback.
				"id" => $field_id, //sends field id to callback
				"desc" => 'Choose your tooltip background color above', //description of the field (optional)
				"default" => '#000000', //sets the default field value (optional), when grabbing this option value later on remember to use get_option(option_name, default_value) so it will return default value if no value exists yet
				"class" => "motech-color-field" //designate this as color field
			)			
		);
		
		//add color picker text input field
		$field_slug = "text_color";
		$field_label = "Text Color:";
		$field_id = $this->plugin_slug.'_'.$field_slug;
		register_setting($this->plugin_slug.'_option_group', $field_id);
		add_settings_field(
		    $field_id,
		    $field_label, 
		    array($this, 'create_a_text_input'), //callback function for text input
		    $this->plugin_slug.'-setting-admin',
		    $this->plugin_slug.'_setting_section',
		    array(								// The array of arguments to pass to the callback.
				"id" => $field_id, //sends field id to callback
				"desc" => 'Choose your tooltip text color above', //description of the field (optional)
				"default" => '#ffffff', //sets the default field value (optional), when grabbing this option value later on remember to use get_option(option_name, default_value) so it will return default value if no value exists yet
				"class" => "motech-color-field" //designate this as color field
			)			
		);
		
		//add text input field
		$field_slug = "max_width";
		$field_label = "Max Width:";
		$field_id = $this->plugin_slug.'_'.$field_slug;
		register_setting($this->plugin_slug.'_option_group', $field_id);
		add_settings_field(
		    $field_id,
		    $field_label, 
		    array($this, 'create_a_text_input'), //callback function for text input
		    $this->plugin_slug.'-setting-admin',
		    $this->plugin_slug.'_setting_section',
		    array(								// The array of arguments to pass to the callback.
				"id" => $field_id, //sends field id to callback
				"desc" => 'Set the maximum pixel width of the tooltip bubble. Default is 250.', //description of the field (optional)
				"default" => '250', //sets the default field value (optional), when grabbing this option value later on remember to use get_option(option_name, default_value) so it will return default value if no value exists yet
			)			
		);
		
		//add text input field
		$field_slug = "opacity";
		$field_label = "Opacity:";
		$field_id = $this->plugin_slug.'_'.$field_slug;
		register_setting($this->plugin_slug.'_option_group', $field_id);
		add_settings_field(
		    $field_id,
		    $field_label, 
		    array($this, 'create_a_text_input'), //callback function for text input
		    $this->plugin_slug.'-setting-admin',
		    $this->plugin_slug.'_setting_section',
		    array(								// The array of arguments to pass to the callback.
				"id" => $field_id, //sends field id to callback
				"desc" => 'Set the opacity of the tooltip bubble. This should be a number <br>between 0 and 1. 0 makes the bubble invisible and 1 makes <br>the bubble totally solid. Default is .95', //description of the field (optional)
				"default" => '.95', //sets the default field value (optional), when grabbing this option value later on remember to use get_option(option_name, default_value) so it will return default value if no value exists yet
			)			
		);
		
		//add select input field
		$field_slug = "position";
		$field_label = "Position:";
		$field_id = $this->plugin_slug.'_'.$field_slug;
		$options = array(
								array("label" => "Center", "value" => "center"),
								array("label" => "Left", "value" => "left"),
								array("label" => "Right", "value" => "right"),
		);
		register_setting($this->plugin_slug.'_option_group', $field_id);
		add_settings_field(	
			$field_id,						
			$field_label,							
			array($this, 'create_a_select_input'), //callback function for radio input
		    $this->plugin_slug.'-setting-admin',
		    $this->plugin_slug.'_setting_section',
		    array(								// The array of arguments to pass to the callback.
				"id" => $field_id, //sends select field id to callback
				"desc" => 'The tooltip\'s position, relative to the trigger element.', //description of the select field (optional)
				"default" => 'center', //sets the default field value (optional), when grabbing this field value later on remember to use get_option(option_name, default_value) so it will return default value if no value exists yet
				"select_options" => $options //sets select option data
			)				
		);
		
		//add checkbox field
		$field_slug = "disable_on_mobile";
		$field_label = "Disable On Mobile";
		$field_id = $this->plugin_slug.'_'.$field_slug;
		register_setting($this->plugin_slug.'_option_group', $field_id);
		add_settings_field(
		    $field_id,
		    $field_label, 
		    array($this, 'create_a_checkbox'), //callback function for checkbox
		    $this->plugin_slug.'-setting-admin',
		    $this->plugin_slug.'_setting_section',
		    array(								// The array of arguments to pass to the callback.
				"id" => $field_id, //sends field id to callback
				"desc" => 'Check this box to disable tooltips on mobile devices.', //description of the field (optional)
				"default" => '0' //sets the default field value (optional), when grabbing this option value later on remember to use get_option(option_name, default_value) so it will return default value if no value exists yet
				
			)			
		);		
		
		//add text input field
		$field_slug = "menu_selectors";
		$field_label = "Menu Selectors (Advanced):";
		$field_id = $this->plugin_slug.'_'.$field_slug;
		register_setting($this->plugin_slug.'_option_group', $field_id);
		add_settings_field(
		    $field_id,
		    $field_label, 
		    array($this, 'create_a_text_input'), //callback function for text input
		    $this->plugin_slug.'-setting-admin',
		    $this->plugin_slug.'_setting_section',
		    array(								// The array of arguments to pass to the callback.
				"id" => $field_id, //sends field id to callback
				"desc" => '<span style="width:340px;display:block;">This is for advanced users. If you want to use the tooltips on your WordPress menus, you can enable this by entering in the css selectors for your menus. For example, if your menu has the class "nav_menu", enter in ".nav_menu" to enable tooltips for all menus where class="nav_menu". You can enter multiple selectors in a comma seperated list. If you don\'t know what this means, or you don\'t want tooltips on your menus, just leave it empty. <a href="http://www.clevelandwebdeveloper.com/wordpress-plugins/simple-tooltips/#tooltips_in_menu" target="_blank">Get Help &raquo;</a></span>', //description of the field (optional)
				"default" => '', //sets the default field value (optional), when grabbing this option value later on remember to use get_option(option_name, default_value) so it will return default value if no value exists yet
			)			
		);

	
    } //end of page_init function

    
	/**
	 * This following set of functions handle all input field creation
	 * 
	 */
	function create_a_checkbox($args) {
		$html = '<input type="checkbox" id="'  . $args["id"] . '" name="'  . $args["id"] . '" value="1" ' . checked(1, get_option($args["id"], $args["default"]), false) . '/>'; 
		
		// Here, we will take the desc argument of the array and add it to a label next to the checkbox
		$html .= '<label for="'  . $args["id"] . '">&nbsp;&nbsp;'  . $args["desc"] . '</label>'; 
		
		echo $html;
		
	} // end create_a_checkbox
	
	function create_a_text_input($args) {
		//grab placeholder if there is one
		if(isset($args["placeholder"])) {
			$placeholder_html = "placeholder=\"".$args['placeholder']."\"";
		} else {
			$placeholder_html = "";
		}
		if(!empty($args["class"])){
			$class = $args["class"];
		}else{
			$class = "";
		}
		// Render the output
		echo '<input type="text" '  . $placeholder_html . ' id="'  . $args["id"] . '" class="'.$class.'" name="'  . $args["id"] . '" value="' . get_option($args["id"], $args["default"]) . '" />';
		if(isset($args["desc"])) {
			echo "<p class='description'>".$args["desc"]."</p>";
		}
		
	} // end create_a_text_input
	
	function create_a_textarea_input($args) {
		//grab placeholder if there is one
		if($args[placeholder]) {
			$placeholder_html = "placeholder=\"".$args[placeholder]."\"";
		}	
		// Render the output
		echo '<textarea '  . $placeholder_html . ' id="'  . $args[id] . '"  name="'  . $args[id] . '" rows="5" cols="50">' . get_option($args[id], $args["default"]) . '</textarea>';
		if($args[desc]) {
			echo "<p class='description'>".$args[desc]."</p>";
		}		
	}
	
	function create_a_radio_input($args) {
	
		$radio_options = $args[radio_options];
		$html = "";
		if($args[desc]) {
			$html .= $args[desc] . "<br>";
		}
		foreach($radio_options as $radio_option) {
			$html .= '<input type="radio" id="'  . $args[id] . '_' . $radio_option[value] . '" name="'  . $args[id] . '" value="'.$radio_option[value].'" ' . checked($radio_option[value], get_option($args[id], $args["default"]), false) . '/>';
			$html .= '<label for="'  . $args[id] . '_' . $radio_option[value] . '"> '.$radio_option[label].'</label><br>';
		}
		
		echo $html;
	
	} // end create_a_radio_input callback

	function create_a_select_input($args) {
	
		$select_options = $args["select_options"];
		$html = "";
		if(!empty($args["desc"])) {
			$html .= $args["desc"] . "<br>";
		}
		$html .= '<select id="'  . $args["id"] . '" name="'  . $args["id"] . '">';
			foreach($select_options as $select_option) {
				$html .= '<option value="'.$select_option["value"].'" ' . selected( $select_option["value"], get_option($args["id"], $args["default"]), false) . '>'.$select_option["label"].'</option>';
			}
		$html .= '</select>';
		
		echo $html;
	
	} // end create_a_select_input callback
	
	//add plugin action links logic
	function add_plugin_action_links( $links ) {
	 
		return array_merge(
			array(
				'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page='.$this->plugin_slug.'-setting-admin">Settings</a>'
			),
			$links
		);
	 
	}


	function load_tooltips() {
		if (!is_admin()) {
			wp_enqueue_script('simple_tooltips_base', plugins_url( 'zebra_tooltips.js' , __FILE__ ), array('jquery'), false, true);
			wp_enqueue_style('simple_tooltips_style', plugins_url( 'zebra_tooltips.css' , __FILE__ ));
			add_action('wp_footer', array($this, 'tooltip_data'), 100);
		}
	} 
	
	function tooltip_data() {
		?>
        <?php
		if(function_exists('wp_is_mobile')){
			if ( wp_is_mobile() ) {
				$on_mobile = 1;
			} else {
				$on_mobile = 0;	
			}
		} else { //old version of wp, wp_is_mobile doesn't exist, so assume user not on mobile
			$on_mobile = 0;
		}
		?>
        <?php if (!( ( get_option('simple_tooltips_disable_on_mobile', 0) == 1 ) and ($on_mobile == 1) )) : //only load on non mobile, if set to disable on mobile ?>
			<?php
            $selectors_field = get_option('simple_tooltips_menu_selectors', '');
            ?>
            
                <script type="text/javascript">
                    jQuery(function() {
                        <?php if (!empty($selectors_field)) : ?>
                            <?php
                            $pieces = explode(",", $selectors_field);
                            foreach($pieces as $piece) {
                                $selectors_string .= $piece . " .tooltips > a,";
                            }
                            $selectors_string = substr($selectors_string, 0, -1);
                            ?>
                            jQuery('<?php echo $selectors_string ?>').each(function () {
                                jQuery(this).addClass('tooltips').closest('li').removeClass('tooltips');
                            });
                        <?php endif ?>
                        
                        jQuery(".tooltips img").closest(".tooltips").css("display", "inline-block");
                    
                        new jQuery.Zebra_Tooltips(jQuery('.tooltips').not('.custom_m_bubble'), {
                            'background_color':     '<?php echo get_option('simple_tooltips_background_color', '#000000') ?>',
                            'color':				'<?php echo get_option('simple_tooltips_text_color', '#ffffff') ?>',
                            'max_width':  <?php echo get_option('simple_tooltips_max_width', 250) ?>,
                            'opacity':    <?php echo get_option('simple_tooltips_opacity', .95) ?>, 
                            'position':    '<?php echo get_option('simple_tooltips_position', 'center') ?>'
                        });
                        
                        <?php //add customized tooltips
                        $custom_tooltips = $this->custom_tooltips;
                        $custom_tooltips = array_map("unserialize", array_unique(array_map("serialize", $custom_tooltips)));
                        //$custom_tooltips = array_unique($custom_tooltips);
                        foreach($custom_tooltips as $custom_tooltip) { ?>
                            <?php
                            
                                //first get default values
                                $bgcolor = get_option('simple_tooltips_background_color', '#000000');
                                $color = get_option('simple_tooltips_text_color', '#ffffff');
                                $max_width = get_option('simple_tooltips_max_width', 250);
                                $opacity =  get_option('simple_tooltips_opacity', .95);
                                $position =  get_option('simple_tooltips_position', 'center');
                                
                                $special_classes = "";
                                //now override custom values, if there are any. the order here matters
                                if(isset($custom_tooltip["bubblewidth"])){
                                    $max_width = $custom_tooltip["bubblewidth"];
                                    $special_classes .= " bubblewidth_".$custom_tooltip["bubblewidth"];
                                }
                                if(isset($custom_tooltip["bubblebgcolor"])){
                                    $bgcolor = $custom_tooltip["bubblebgcolor"];
                                    $special_classes .= " bubblebgcolor_".$custom_tooltip["bubblebgcolor"];
                                }
                                if(isset($custom_tooltip["bubbleopacity"])){
                                    $opacity = $custom_tooltip["bubbleopacity"];
                                    $special_classes .= " bubbleopacity_".$custom_tooltip["bubbleopacity"];
                                }
                                if(isset($custom_tooltip["bubbleposition"])){
                                    $position = $custom_tooltip["bubbleposition"];
                                    $special_classes .= " bubbleposition_".$custom_tooltip["bubbleposition"];
                                }
                                if(isset($custom_tooltip["bubblecolor"])){
                                    $color = $custom_tooltip["bubblecolor"];
                                    $special_classes .= " bubblecolor_".$custom_tooltip["bubblecolor"];
                                }							
                            
                            
                            ?>
    
                            new jQuery.Zebra_Tooltips(jQuery('[class="tooltips <?php echo $special_classes ?> custom_m_bubble"]'), {
                                'background_color':     '<?php echo $bgcolor ?>',
                                'color':				'<?php echo $color ?>',
                                'max_width':  <?php echo $max_width ?>,
                                'opacity':    <?php echo $opacity ?>, 
                                'position':    '<?php echo $position ?>'
                            });
                            
                            <?php
                        }
                        ?>
                    
                    });
                </script>        
		<?php endif;
	}
	
	public function plugin_row_links($links, $file) {
		$plugin = plugin_basename(__FILE__);
		if ($file == $plugin) // only for this plugin
				return array_merge( $links,
			array( '<a target="_blank" href="http://www.linkedin.com/in/ClevelandWebDeveloper/">' . __('Find me on LinkedIn' ) . '</a>' ),
			array( '<a target="_blank" href="http://twitter.com/ClevelandWebDev">' . __('Follow me on Twitter') . '</a>' )
		);
		return $links;
	}
	
	function my_refresh_mce($ver) {
	  $ver += 6;
	  return $ver;
	}
	
	function add_custom_button() {
	   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		 return;
	   if ( get_user_option('rich_editing') == 'true') {
		 add_filter('mce_external_plugins', array($this, 'add_custom_tinymce_plugin'));
		 add_filter('mce_buttons', array($this, 'register_custom_button'));
	   }
	}
	
	function register_custom_button($buttons) {
	   array_push($buttons, "|", get_class($this));
	   return $buttons;
	}
	
	function add_custom_tinymce_plugin($plugin_array) {
	   //use this in a plugin
	   $plugin_array[get_class($this)] = plugins_url( 'editor_plugin.js' , __FILE__ );
	   //use this in a theme
	   //$plugin_array[get_class($this)] = get_bloginfo('template_url').'/editor_plugin.js';
	   return $plugin_array;
	}
	
	// add the shortcode handler 
	function addShortcodeHandler($atts, $trigger = null) {
			extract(shortcode_atts(array(
										 "content" => "",
										 "style" => "",
										 "bubblewidth" => "",
										 "bubbleopacity" => "",
										 "bubblebgcolor" => "",
										 "bubbleposition" => "",
										 "bubblecolor" => "",
										 ), $atts));
/*			if ($height > 0 ) {
				$spacer_css = "padding-top: " . $height . ";";
			} elseif($height < 0) {
				$spacer_css = "margin-top: " . $height . ";";
			}
			return '<span style="display:block;clear:both;height: 0px;'.$spacer_css.'"></span>';*/
			$custom_tooltips = $this->custom_tooltips;
			$new_custom_tooltip = array();
			
			$special_class = "";
			if($bubblewidth != "") {
				$new_custom_tooltip["bubblewidth"] = $bubblewidth; //add to array
				$special_class .= " bubblewidth_".$bubblewidth;
				$custom_tooltip_active = 1;
			}
			if($bubblebgcolor != "") {
				$new_custom_tooltip["bubblebgcolor"] = $bubblebgcolor;
				$special_class .= " bubblebgcolor_" . $bubblebgcolor;
				$custom_tooltip_active = 1;
			}
			if($bubbleopacity != "") {
				$new_custom_tooltip["bubbleopacity"] = $bubbleopacity;
				$special_class .= " bubbleopacity_" . $bubbleopacity;
				$custom_tooltip_active = 1;
			}
			if($bubbleposition != "") {
				$new_custom_tooltip["bubbleposition"] = $bubbleposition;
				$special_class .= " bubbleposition_" . $bubbleposition;
				$custom_tooltip_active = 1;
			}
			if($bubblecolor != "") {
				$new_custom_tooltip["bubblecolor"] = $bubblecolor;
				$special_class .= " bubblecolor_" . $bubblecolor;
				$custom_tooltip_active = 1;
			}
			
			
			if((isset($custom_tooltip_active)) && ($custom_tooltip_active == 1)) {
				$custom_tooltips[] = $new_custom_tooltip;
				$this->custom_tooltips = $custom_tooltips;
				$special_class .= " custom_m_bubble";
			}
			$html = '';
			$html .= '<span class="tooltips '.$special_class.'" style="'.$style.'" title="'.htmlspecialchars(do_shortcode($content)).'">'.do_shortcode($trigger).'</span>';
			return $html;
	}

	function motech_tooltips_admin_jquery() {
		if (isset($_GET['page']) && $_GET['page'] == $this->plugin_slug.'-setting-admin') { //if we are on our admin page
			?>
				<script>
					jQuery(function() {	
						//jquery for color picker, fixes a bug in later wp versions
						jQuery('tr.motech-color-field').removeClass('motech-color-field');
					});
				</script>
            <?php
		}
	}

		
} //end class

new simple_tooltips();