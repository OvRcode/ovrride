<?php
/**
Plugin Name: Gravity Forms Picatcha Add-On
Plugin URI: http://www.picatcha.com
Description: Integrates Gravity Forms with Picatcha, enabling users to prevent spam by adding a Picatcha field to their forms.
Version: 2.0
Author: rocketgenius
Author URI: http://www.rocketgenius.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

if (class_exists("GFForms")) {
GFForms::include_addon_framework();

class GFPicatcha extends GFAddOn{

    protected $_version = "2.0";
    protected $_min_gravityforms_version = "1.8.3";
    protected $_slug = "gravityformspicatcha";
    protected $_path = "gravityformspicatcha/picatcha.php";
    protected $_full_path = __FILE__;
    protected $_url = "http://www.gravityforms.com";
    protected $_title = "Picatcha Add-On";
    protected $_short_title = "Picatcha";

    // Members plugin integration
    protected $_capabilities = array("gravityforms_picatcha", "gravityforms_picatcha_uninstall");

    // Permissions
    protected $_capabilities_settings_page = "gravityforms_picatcha";
    protected $_capabilities_uninstall = "gravityforms_picatcha_uninstall";
    protected $_enable_rg_autoupgrade = true;

    private static $_instance = null;

    public static function get_instance() {
        if (self::$_instance == null) {
            self::$_instance = new GFPicatcha();
        }

        return self::$_instance;
    }

    private function __clone() { } /* do nothing */

    protected function init_admin(){

        parent::init_admin();
        
        //Adds Picatcha field button to form editor
        add_filter("gform_add_field_buttons", array($this, 'add_picatcha_field'));

        //Formats Picatcha field type title
        add_filter('gform_field_type_title', array($this, 'field_title'));

        //Adds form editor script
        add_action("gform_editor_js", array($this, "editor_script"));

        //Adds basic field settings
        add_action("gform_field_standard_settings", array($this, "standard_field_settings"), 10,2);

        //Registers Picatcha tooltips
        add_filter('gform_tooltips', array($this, 'tooltips'));

        //displays the captcha input
        add_action("gform_field_input" ,array($this, "picatcha_input"), 10, 5);

    }

    protected function init_frontend(){

        parent::init_frontend();

        //including the picatchalib
        if (!function_exists('_picatcha_http_post')) {
            require_once('picatcha/picatchalib.php');
        }

        //Validate the Picatcha field
        add_filter("gform_field_validation", array($this, "validate_form"), 10, 4);

        //displays the captcha input
        add_action("gform_field_input" ,array($this, "picatcha_input"), 10, 5);

        //Enqueue the picatcha script on the front end. Needs to use pre_enqueue_scripts since this needs to be enqueued before any other script
        add_action("gform_pre_enqueue_scripts", array($this,"enqueue_picatcha_script"), 10,2);
    }

    protected function init_ajax(){

       //displays the captcha input
       add_action("gform_field_input" ,array($this, "picatcha_input"), 10, 5);

   }

    protected function styles(){

        $protocol = GFCommon::is_ssl() ? "https" : "http";

        $styles = array(
            array(  "handle" => "gform_picatcha_style",
                    "src" => $protocol . "://api.picatcha.com/static/client/picatcha.css",
                    "version" => $this->_version,
                    "enqueue" => array("field_types" => array("picatcha"))
                )
        );

        return array_merge(parent::styles(), $styles);
    }

    public function enqueue_picatcha_script($form, $ajax){

        if(!is_array(rgar($form, "fields")))
            return;

        $protocol = GFCommon::is_ssl() ? "https" : "http";

        //cycle through the fields to see if picatcha is being used
        foreach( $form['fields'] as $field){
            if( ($field['type']=='picatcha') ){

                wp_enqueue_script("gform_picatcha_script", "{$protocol}://api.picatcha.com/static/client/picatcha.js", null, $this->_version);
                break;
            }
        }
    }

    // ------- Plugin settings -------
    public function plugin_settings_fields(){
        return array(
            array(
                "title" => __("Picatcha Settings", "gravityformspicatcha"),
                "description" => sprintf( __('Picatcha\'s image CAPTCHA system provides better security and better user experience for your forms than existing text CAPTCHAs. If you don\'t have a Picatcha account, you can %1$s sign up for one here.%2$s', 'gravityformspicatcha'),
                    '<a href="http://picatcha.com/signup/" target="_blank">', '</a>' ),
                "fields" => array(
                    array(
                        "name"=> "publicKey",
                        "label" => __("Public Key", "gravityformspicatcha"),
                        "type" => "text",
                        "class" => "medium",
                        "feedback_callback" => array($this, "has_valid_keys")
                        ),
                    array(
                        "name"=> "privateKey",
                        "label" => __("Private Key", "gravityformspicatcha"),
                        "type" => "text",
                        "class" => "medium",
                        "feedback_callback" => array($this, "has_valid_keys")
                        )
                    )
                )
            );
    }

    public function has_valid_keys(){

        //reads main addon settings
        $settings = $this->get_plugin_settings();

        $is_valid_public_key = !rgempty("publicKey", $settings) && $this->is_valid_key("pub", rgar($settings,"publicKey"));
        $is_valid_private_key = !rgempty("privateKey", $settings) && $this->is_valid_key("pri", rgar($settings,"privateKey"));

        return $is_valid_public_key && $is_valid_private_key;
    }

    public function is_valid_key($type, $key){
        $protocol = GFCommon::is_ssl() ? "https" : "http";
        $url = "{$protocol}://api.picatcha.com/vk?{$type}={$key}";
        $raw_response = wp_remote_request($url, array("timeout" => 4, "sslverify" => false));

        if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code'])
            return false;
        else
        {
            $result = json_decode($raw_response['body']);
            return $result->s;
        }
    }

    public function add_picatcha_field($field_groups){
    //script to name the widget
    ?>
    <script type="text/javascript">
      function AddPicatchaField(){
        for(var i=0; i<form.fields.length; i++){
          if(form.fields[i].type == 'picatcha'){
            alert("<?php _e("Only one Picatcha Image Captcha field can be added to the form.", "gravityformspicatcha");?>");
            return
          }
        }
        StartAddField('picatcha');
      }

      function SetDefaultValues_picatcha(field) {
          field.label = "Picatcha";
          field['displayOnly'] = true;
          field['noDuplicates'] = true;
          field['type'] = 'picatcha';
          field['field_picatcha_format'] = '2';
          field['field_picatcha_color'] = '#2a1f19';
          field['field_picatcha_imgSize'] = '75';
          field['field_picatcha_noise_type'] = '0';
          field['field_picatcha_noise_level'] = '0';
          field['field_picatcha_lang'] = 'en';
          field['field_picatcha_lang_override'] = '0';
          return field;
      }
    </script>
    <?php
    foreach($field_groups as &$group){
        if($group["name"] == "advanced_fields"){
            $group["fields"][] = array("class"=>"button", "value" => __("Picatcha", "gravityformspicatcha"), "onclick" => "AddPicatchaField()");
            break;
        }
    }
    return $field_groups;
}

    //sets the title of the widget
    public function field_title($type) {

        if ($type == 'picatcha')
            return __('Picatcha', 'gravityformspicatcha');

        return $type;
    }

    public function picatcha_input($input, $field, $value, $lead_id, $form_id){

      if ($field["type"] == "picatcha"){
        //Public
        if(IS_ADMIN){
            if($this->has_valid_keys())
                $input = "<img src='". $this->get_base_url() . '/images/picatcha.png' ."' />";
            else
                $input = "<div class='captcha_message'>" . __("To use the Picatcha field you must first do the following:", "gravityformspicatcha") . "</div><div class='captcha_message'>1 - <a href='http://www.picatcha.com/signup' target='_blank'>" . sprintf(__("Sign up%s for a free Picatcha account", "gravityformspicatcha"), "</a>") . "</div><div class='captcha_message'>2 - " . sprintf(__("Enter your Picatcha keys in the %ssettings page%s", "gravityformspicatcha"), "<a href='?page=gf_settings&addon=Picatcha&subview=Picatcha Add-On'>", "</a>") . "</div>";
        }
        else{
            $settings = $this->get_plugin_settings();
            $input = $this->picatcha_get_html($form_id, $settings["publicKey"], NULL, $field['field_picatcha_format'], $field['field_picatcha_color'], 1, $field['field_picatcha_imgSize'], $field["field_picatcha_noise_level"], $field["field_picatcha_noise_type"],$field["field_picatcha_lang"],$field['field_picatcha_lang_override']);
        }
      }

      return $input;
    }
    
    public function picatcha_get_html($form_id, $pubkey, $error = NULL, $format='2', $style='#2a1f19', $link = '1', $IMG_SIZE = '75', $NOISE_LEVEL = 0, $NOISE_TYPE = 0, $lang = 'en', $langOverride = '0') {

        $elm_id = 'picatcha';

        $script = 'Picatcha.PUBLIC_KEY="'.$pubkey.'";' .
            'Picatcha.setCustomization({"format":"'.$format.'","color":"'.$style.'","link":"'.$link.'","image_size":"'.$IMG_SIZE.'","lang":"'.$lang.'","langOverride":"'.$langOverride.'","noise_level":"'.$NOISE_LEVEL.'","noise_type":"'.$NOISE_TYPE.'"});'.
            'Picatcha.create("'.$elm_id.'",{});';

        GFFormDisplay::add_init_script($form_id, "picatcha", GFFormDisplay::ON_PAGE_RENDER, $script);

        $html = '';
        if ( $error != NULL ) {
            $html .= '<div id="' . $elm_id . '_error">' . $error . '</div>';
        }
        $html .= '<div id="' . $elm_id . '"></div>';
        return $html;
    }

    public function editor_script(){
      ?>
      <script type='text/javascript'>
        //Add in the settings
        jQuery(document).ready(function($){
            fieldSettings["picatcha"] = ".error_message_setting, .conditional_logic_field_setting, .css_class_setting, .label_setting, .picatcha_color, .picatcha_format, .picatcha_image_size, .picatcha_language, .picatcha_language_override, .picatcha_noise_level, .picatcha_noise_type";
        });

        //load in the settings
        jQuery(document).bind("gform_load_field_settings", function(event, field, form){

            if(field["type"] != "picatcha")
                return;

            //theme color
            jQuery("#picatcha_color").attr("value", field["field_picatcha_color"]);

            //captcha format
            jQuery("#picatcha_format").attr("value", field["field_picatcha_format"]);

            //image size
            jQuery("#picatcha_imgSize").attr("value", field["field_picatcha_imgSize"]);

            //language
            jQuery("#picatcha_lang").attr("value", field["field_picatcha_lang"]);

            //language override
            jQuery("#picatcha_langOverride").attr("checked", field["field_picatcha_lang_override"]);

            //noise type
            jQuery("#picatcha_noise_type").attr("value", field["field_picatcha_noise_type"]);

            //noise level
            jQuery("#picatcha_noise_level").attr("value", field["field_picatcha_noise_level"]);

            ToggleNoiseType(true);
        });

        function SetPicatchaColor(){
            SetFieldProperty('field_picatcha_color', jQuery("#picatcha_color").val());
        }

        function ToggleNoiseType(isInit){
            var hideType = jQuery("#picatcha_noise_level").val() == "0";
            var speed = "";
            if(!isInit)
                speed = "slow";

            if(hideType){
                jQuery(".picatcha_noise_type").hide(speed);
                jQuery("#picatcha_noise_type").val("");
            }
            else{
                jQuery(".picatcha_noise_type").show(speed);
            }
        }
      </script>
      <?php
    }

    // Adds custom settings to the standard (properties?) tab
    public function standard_field_settings($position, $form_id){

      if($position == 50){
        ?>
          <li class="picatcha_color field_setting">
            <label for="picatcha_color"><?php _e("Theme Color","gravityformspicatcha")?> <?php gform_tooltip("picatcha_color") ?></label>
            <?php GFFormDetail::color_picker("picatcha_color", "SetPicatchaColor") ?>
          </li>

          <li class="picatcha_format field_setting">
            <label for="picatcha_format"><?php _e("Number of Images", "gravityformspicatcha")?> <?php gform_tooltip("picatcha_images") ?></label>
            <select id="picatcha_format" onchange="SetFieldProperty('field_picatcha_format', this.value)">
              <option value="1">6</option>
              <option value="2">8</option>
              <option value="3">10</option>
              <option value="4">12</option>
            </select><br />
          </li>

          <li class="picatcha_image_size field_setting">
            <label for="picatcha_imgSize"><?php _e("Image Size", "gravityformspicatcha")?> <?php gform_tooltip("picatcha_image_size") ?></label>
            <select id="picatcha_imgSize" onchange="SetFieldProperty('field_picatcha_imgSize',this.value)">
              <option value="50"><?php _e("Small", "gravityformspicatcha") ?></option>
              <option value="60"><?php _e("Medium", "gravityformspicatcha") ?></option>
              <option value="75"><?php _e("Large", "gravityformspicatcha") ?></option>
            </select><br />
          </li>

          <li class="picatcha_noise_level field_setting" style="float: left;">
            <label for="picatcha_noise_level"><?php _e("Noise", "gravityformspicatcha")?> <?php gform_tooltip("picatcha_noise") ?></label>
            <select id="picatcha_noise_level" onchange="SetFieldProperty('field_picatcha_noise_level',this.value); ToggleNoiseType(false);">
              <option value="0"><?php _e("Off", "gravityformspicatcha") ?></option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10 - <?php _e("Maximum", "gravityformspicatcha") ?></option>
            </select><br />
          </li>

          <li class="picatcha_noise_type field_setting">
            <label for="picatcha_noise_type">&nbsp;</label>
            <select id="picatcha_noise_type" onchange="SetFieldProperty('field_picatcha_noise_type', this.value)">
              <option value=""><?php _e("Select noise type", "gravityformspicatcha")?></option>
              <option value="0"><?php _e("Random", "gravityformspicatcha")?></option>
              <option value="1"><?php _e("Shadow", "gravityformspicatcha")?></option>
              <option value="2"><?php _e("Pixelation", "gravityformspicatcha")?></option>
            </select>
          </li>

          <li class="picatcha_language field_setting" style="clear:both">
            <label for="picatcha_lang"><?php _e("Language","gravityformspicatcha")?> <?php gform_tooltip("picatcha_language") ?></label>
            <select id="picatcha_lang" onchange="SetFieldProperty('field_picatcha_lang',this.value)">
                <option value="en">English</option>
                <option value="es">Español</option>
                <option value="fr">Français</option>
                <option value="de">Deutch</option>
                <option value="hi">हिंदी</option>
                <option value="is">??slenska</option>
                <option value="ru">Pу????кий</option>
                <option value="zh">中国</option>
                <option value="ar">العربية</option>
                <option value="tl">Filipino</option>
                <option value="it">Italiano</option>
                <option value="vi">Việt</option>
                <option value="nl">Nederlands</option>
                <option value="pt">Português</option>
                <option value="tr">Türkçe</option>
                <option value="sk">Slovenských</option>
            </select><br />
          </li>

          <li class="picatcha_language_override field_setting">
            <input type="checkbox" id="picatcha_langOverride" onclick="SetFieldProperty('field_picatcha_lang_override', this.checked)" />
            <label for="picatcha_langOverride" class="inline"><?php _e("Allow users to select language","gravityformspicatcha")?></label>
          </li>


        <?php
      }
    }

    public function validate_form($result, $value, $form, $field){

      if ($field['type'] == 'picatcha'){

        //only check if the rest of the form is valid
        if($result["is_valid"]){

            $settings = $this->get_plugin_settings();
            //check if the picatcha is correct
            $response = picatcha_check_answer($settings["privateKey"],
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'], $_POST['picatcha']['token'], $_POST['picatcha']['r']);

            if ($response->is_valid == true){
                $result["is_valid"] = true;
            }
            else{
                //not sure if this is necessary..
                $result["is_valid"] = false;
                $result["message"] = rgempty("errorMessage", $field) ? __("Incorrect. Please select the correct images.") : rgar($field, "errorMessage");
            }
        }
      }
      return $result;
    }

    //Adds feed tooltips to the list of tooltips
    public function tooltips($tooltips){
        $picatcha_tooltips = array(
            "picatcha_color" => "<h6>" . __("Theme Color", "gravityformspicatcha") . "</h6>" . __("Select the primary theme color for the Picatcha field using the color picker.", "gravityformspicatcha"),
            "picatcha_image_size" => "<h6>" . __("Image Size", "gravityformspicatcha") . "</h6>" . __("Choose what size images you would like Picatcha to display: Small, Medium or Large.", "gravityformspicatcha"),
            "picatcha_noise" => "<h6>" . __("Noise", "gravityformspicatcha") . "</h6>" . __("Control the amount of noise that will be applied to the Picatcha images as an image filter.", "gravityformspicatcha"),
            "picatcha_language" => "<h6>" . __("Language", "gravityformspicatcha") . "</h6>" . __("Select which language Picatcha should use when presenting the field to the user.", "gravityformspicatcha"),
            "picatcha_images" => "<h6>" . __("Number of Images", "gravityformspicatcha") . "</h6>" . __("Select how many Picatcha images you would like the user to choose from.", "gravityformspicatcha")
        );
        return array_merge($tooltips, $picatcha_tooltips);
    }
    
    protected function upgrade($previous_version) {
    	$previous_is_pre_addon_framework = version_compare($previous_version, "1.3", "<");

        if ($previous_is_pre_addon_framework) {
            $old_settings = get_option("gf_picatcha_settings");
        	$new_settings = array("publicKey" => $old_settings["public_key"], "privateKey" => $old_settings["private_key"]);
			parent::update_plugin_settings($new_settings);
        }
    }

}

GFPicatcha::get_instance();

}

?>
