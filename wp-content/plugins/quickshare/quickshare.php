<?php
/**
 * Plugin Name: QuickShare
 * Plugin URI: http://celloexpressions.com/plugins/quickshare/
 * Description: Add quick social sharing functions to your content. Challenge social sharing norms with a flexible design and fast performance.
 * Version: 1.4
 * Author: Nick Halsey
 * Author URI: http://celloexpressions.com/
 * Tags: Social, Share, Sharing, Social Sharing, Social Media, Quick, Easy, Lightweight, No JS, Flexible, Customizable, Responsive, Facebook, Twitter, Pinterest, Linkedin, Google+, Tumblr, Email, Reddit, StumbleUpon
 * License: GPL

=====================================================================================
Copyright (C) 2013 Nick Halsey

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with WordPress; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
=====================================================================================
*/

// Set-up Action and Filter Hooks
if ( is_admin() ){
	register_activation_hook(__FILE__, 'cxnh_quickshare_add_defaults');
	register_uninstall_hook(__FILE__, 'cxnh_quickshare_delete_plugin_options');
	add_action('admin_init', 'cxnh_quickshare_init' );
	add_action('admin_menu', 'cxnh_quickshare_add_options_page');
	add_filter( 'plugin_action_links', 'cxnh_quickshare_plugin_action_links', 10, 2 );
}

// Delete options table entries ONLY when plugin deactivated AND deleted
function cxnh_quickshare_delete_plugin_options() {
	delete_option('cxnh_quickshare_options');
}
// Display a Settings link on the main plugins page
function cxnh_quickshare_plugin_action_links( $links, $file ) {
	if ( $file == plugin_basename( __FILE__ ) ) {
		$cxnh_quickshare_links = '<a href="'.get_admin_url().'options-general.php?page=quickshare%2Fquickshare.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $cxnh_quickshare_links );
	}
	return $links;
}

// Define default option settings
function cxnh_quickshare_add_defaults() {
	$tmp = get_option('cxnh_quickshare_options');
    if(!is_array($tmp)) {
		delete_option('cxnh_quickshare_options'); 
		$arr = array(
			//general
			'plugin_version' => '1.4', // currently tracks initially installed plugin version
			'settingspage' => 'design',
			'displaytype' => 'genericons',
			'size' => '',
			'borderradius' => '3',
			'respond_small' => '600', // Android uses this value to distinguish between phone & tablet
			'respond_hide' => '',
			
			//config
			'sharelabel' => 'Share:',
			
			'posts' => 1,
			'pages' => 0,
			'attachments' => 1,
			'everything' => 0,
			'excluded_ids' => '',
			
			'facebook' => 1,
			'twitter' => 1,
			'pintrest' => 1,
			'linkedin' => 1,
			'googleplus' => 1,
			'reddit' => 0,
			'stumbleupon' => 0,
			'email' => 1,
			
			'image' => '',
			'ogmeta' => 1,
			'hidepintrest' => 0,
			
			//common to 2 styles
			'inherit_color' => 0,
			'bgtransparent' => 1,
			'color' => '#2c12ed',
			'hovercolor' => '#bb2255',
			'bgcolor' => '#f1e6b3',
			'customcss' => '',
			
			//icons style
			
			//genericons style
			'monochrome' => 1,
			'monochrome_hover' => 0,
			
			//text style
			'text_icons' => 1,
			'text_icons_color' => 1, // not available if inheriting link colors
			
			//effects
			'effect-spin' => 0,
			'effect-round' => 0,
			'effect-glow' => 0,
			'effect-contract' => 0,
			'effect-expand' => 1
		);
		update_option('cxnh_quickshare_options', $arr);
	}
}

// Init plugin options to white list our options
function cxnh_quickshare_init(){
	register_setting( 'cxnh_quickshare_plugin_options', 'cxnh_quickshare_options', 'cxnh_quickshare_validate_options' );
}

// Add menu page
function cxnh_quickshare_add_options_page(){
	add_options_page('QuickShare Settings', 'QuickShare', 'manage_options', __FILE__, 'cxnh_quickshare_render_form');
}

// Prepare the media uploader
function cxnh_quickshare_admin_scripts(){
	// must be running 3.5+ to use color pickers and image upload
	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script('quickshare-admin', plugins_url("/quickshare-admin.js", __FILE__), array('wp-color-picker', 'jquery'));
	wp_enqueue_style('quickshare',plugins_url('/quickshare.css',__FIlE__));
	wp_enqueue_style('genericons',plugins_url('/genericons/genericons.css',__FILE__));
}
function cxnh_quickshare_admin_head(){
	$options = get_option('cxnh_quickshare_options');
	
	// added from 1.0 to 1.1
	if(!array_key_exists('respond_small',$options)){
		$options['respond_small'] = '';
		$options['respond_hide'] = '';
	}
?>
	<style type="text/css">
		.icons .text-option, .icons .genericons-option, .icons .n-icons-option { display: none; }
		.genericons .text-option, .genericons .icons-option, .genericons .n-genericons-option { display: none; }
		.text .icon-option, .text .genericons-option, .text .n-text-option { display: none; }
		label { clear: both; }
		.hidden { display: none; }
		.quickshare-container {
			max-width: 50%;
			position: fixed;
			z-index: 10;
			right: 10px;
			top: 50%;
			padding: .4em 5px;
			background: #fff;
			cursor: move;
			border: none;
			box-shadow: 0px 1px 1px 0px rgba(0,0,0,0.1);
		}
		@media only screen and (max-width: 900px) {
			.quickshare-container { display: none; }
		}
		.no-js #quickshare_design,
		.no-js #quickshare_config {
			display: block;
		}
		.no-js .quickshare-container {
			display: none;
		}
	<?php 
		if(cxnh_quickshare_getOption('everywhere',$options)){ echo '.display-option { display: none; }'; }
		if($options['settingspage'] == 'design'){ echo '#quickshare_config { display: none; }'; }
		if($options['settingspage'] == 'config'){ echo '#quickshare_design { display: none; }'; }  ?>
	</style>
	<style type="text/css" id="dynamic-custom-options-css">
		.quickshare-text span,
		<?php if(cxnh_quickshare_getOption('text_icons_color',$options)){ echo '.quickshare-text span:before,'; } ?>
		.quickshare-text span:hover,
		.quickshare-genericons.monochrome span:before,
		.quickshare-genericons.monochrome-color span:before {
			<?php if(!cxnh_quickshare_getOption('inherit_color',$options))
				echo 'color: '.cxnh_quickshare_getOption('color',$options).';';
			if(!cxnh_quickshare_getOption('bgtransparent',$options)&&cxnh_quickshare_getOption('displaytype',$options)=='text')
				echo 'background-color: '.cxnh_quickshare_getOption('bgcolor',$options).';';
			?>
		}
		.quickshare-text span:hover,
		<?php if(cxnh_quickshare_getOption('text_icons_color',$options)){ echo '.quickshare-text span:hover:before,'; } ?>
		.quickshare-genericons.monochrome span:hover:before {
			<?php if(!cxnh_quickshare_getOption('inherit_color',$options))
				echo 'color: ' . cxnh_quickshare_getOption('hovercolor',$options) .';';
			?>
		}
		.quickshare-icons span,
		.quickshare-genericons span:before,
		.quickshare-text span {
			border-radius: <?php echo cxnh_quickshare_getOption('borderradius',$options); ?>px;
		}
		<?php echo cxnh_quickshare_getOption('customcss',$options); ?>
	</style>
<?php
}
if (isset($_GET['page']) && $_GET['page'] == 'quickshare/quickshare.php') {
	add_action('admin_enqueue_scripts', 'cxnh_quickshare_admin_scripts');
	add_action('admin_head', 'cxnh_quickshare_admin_head');
}
function cxnh_quickshare_render_form(){
	$options = get_option('cxnh_quickshare_options');

	// added in 1.4
	if(!array_key_exists('excluded_ids',$options))
		$options['excluded_ids'] = '';
?>
<div class="wrap">
	<h2 class="nav-tab-wrapper">
		<a href="javascript:void(0)" id="nav-design" class="nav-tab<?php if($options['settingspage']=='design'){ echo ' nav-tab-active'; } ?>">QuickShare Design</a>
		<a href="javascript:void(0)" id="nav-config" class="nav-tab<?php if($options['settingspage']=='config'){ echo ' nav-tab-active'; } ?>">QuickShare Config</a>
	</h2>
	<form method="post" action="options.php" id="settingsform" class="<?php echo $options['displaytype']; ?>">
	<?php settings_fields('cxnh_quickshare_plugin_options'); ?>
	<table class="form-table" id="quickshare_config">
		<tr>
			<th scope="row">Display On</th>
			<td>
				<label><input name="cxnh_quickshare_options[everywhere]" id="displayeverywhere" type="checkbox" value="1" <?php if (isset($options['everywhere'])) { checked('1', $options['everywhere']); } ?> /> Everywhere the_content() is used (including custom post types)</label><br class="display-option"/>
				<label class="display-option"><input name="cxnh_quickshare_options[posts]" type="checkbox" value="1" <?php if (isset($options['posts'])) { checked('1', $options['posts']); } ?> /> Posts</label><br class="display-option"/>
				<label class="display-option"><input name="cxnh_quickshare_options[pages]" type="checkbox" value="1" <?php if (isset($options['pages'])) { checked('1', $options['pages']); } ?> /> Pages</label><br class="display-option"/>
				<label class="display-option"><input name="cxnh_quickshare_options[attachments]" type="checkbox" value="1" <?php if (isset($options['attachments'])) { checked('1', $options['attachments']); } ?> /> Media Attachments <span style="font-style: italics;">(may not display in some themes if the description field is empty)</span></label>
				<p>If you want to display the QuickShare links anywhere else, use <code>&lt;?php do_quickshare_output( $url, $title, $source, $description, $imgurl ); ?&gt;</code> in your templates.</p>
				<h5>Excluded Posts/Pages:</h5>
				<label><input name="cxnh_quickshare_options[excluded_ids]" type="text" value="<?php echo $options['excluded_ids']; ?>" /> Enter a comma-separated list of post IDs (of any type) that QuickShare won't display on.</label>				
			</td>
		</tr>
		<tr>
			<th scope="row">"Share" Label</th>
			<td>
				<input type="text" name="cxnh_quickshare_options[sharelabel]" value="<?php echo $options['sharelabel']; ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row">Share Types</th>
			<td style="column-count: 2; -webkit-column-count: 2; -moz-column-count: 2;">
				<label><input name="cxnh_quickshare_options[facebook]" type="checkbox" value="1" <?php if (isset($options['facebook'])) { checked('1', $options['facebook']); } ?> /> Facebook</label><br/>
				<label><input name="cxnh_quickshare_options[twitter]" type="checkbox" value="1" <?php if (isset($options['twitter'])) { checked('1', $options['twitter']); } ?> /> Twitter</label><br/>
				<label><input name="cxnh_quickshare_options[pintrest]" type="checkbox" value="1" <?php if (isset($options['pintrest'])) { checked('1', $options['pintrest']); } ?> /> Pinterest</label><br/>
				<label><input name="cxnh_quickshare_options[linkedin]" type="checkbox" value="1" <?php if (isset($options['linkedin'])) { checked('1', $options['linkedin']); } ?> /> Linkedin</label><br/>
				<label><input name="cxnh_quickshare_options[googleplus]" type="checkbox" value="1" <?php if (isset($options['googleplus'])) { checked('1', $options['googleplus']); } ?> /> Google+</label><br/>
				<label><input name="cxnh_quickshare_options[tumblr]" type="checkbox" value="1" <?php if (isset($options['tumblr'])) { checked('1', $options['tumblr']); } ?> /> Tumblr</label><br/>
				<label class="n-genericons-option"><input name="cxnh_quickshare_options[reddit]" type="checkbox" value="1" <?php if (isset($options['reddit'])) { checked('1', $options['reddit']); } ?> /> Reddit</label><br class="n-genericons-option"/>
				<label class="n-genericons-option"><input name="cxnh_quickshare_options[stumbleupon]" type="checkbox" value="1" <?php if (isset($options['stumbleupon'])) { checked('1', $options['stumbleupon']); } ?> /> Stumbleupon</label><br class="n-genericons-option"/>
				<label><input name="cxnh_quickshare_options[email]" type="checkbox" value="1" <?php if (isset($options['email'])) { checked('1', $options['email']); } ?> /> Email</label><br/>
			</td>
		</tr>
		<tr>
			<th scope="row">Image to Share</th>
			<td>
				<label>Default/fallback Image, if none is found in post</label><br/>
				<input type="text" id="upload-image-field" name="cxnh_quickshare_options[image]" value="<?php echo $options['image']; ?>" />
				<input class="img_upload_button button" type="button" value="Upload/Select Image" />
				<br/><br/>
				<label><input name="cxnh_quickshare_options[hidepintrest]" type="checkbox" value="1" <?php if (isset($options['hidepintrest'])) { checked('1', $options['hidepintrest']); } ?> /> Hide Pinterest Sharing if no image is found</label>
			</td>
		</tr>
		<tr>
			<th scope="row">Social Graph Meta<br><em>Strongly Recommended</em></th>
			<td>
				<label><input name="cxnh_quickshare_options[ogmeta]" type="checkbox" value="1" <?php if (isset($options['ogmeta'])) { checked('1', $options['ogmeta']); } ?> /> Add Open Graph Meta Tags (in the html <code>&lt;head&gt;</code>) to single posts and pages using QuickShare (optimizes Facebook and Google+ sharing/SEO). The following properties are specified: <code>og:title, og:url, og:description, og:image, og:site_name</code>.</label>
				<p style="font-style: italic;">You should only disable this option if you know that this information is provided by your theme or another plugin (SEO plugins are likely to add it).</p>
			</td>
		</tr>
	</table>
	<table class="form-table" id="quickshare_design">
		<tr>
			<th scope="row">Size</th>
			<td id="display_size_holder">
				<label><input name="cxnh_quickshare_options[size]" type="radio" value="small" <?php checked('small', $options['size']); ?> /> Small</label><br />
				<label><input name="cxnh_quickshare_options[size]" type="radio" value="" <?php checked('', $options['size']); ?> /> Normal</label><br />
				<label><input name="cxnh_quickshare_options[size]" type="radio" value="large" <?php checked('large', $options['size']); ?> /> Large</label>
			</td>
		</tr>
		<tr>
			<th scope="row">Display Type</th>
			<td id="display_type_holder">
				<label><input name="cxnh_quickshare_options[displaytype]" type="radio" value="icons" <?php checked('icons', $options['displaytype']); ?> onclick="toType('icons')" /> Icons</label><br />
				<label><input name="cxnh_quickshare_options[displaytype]" type="radio" value="genericons" <?php checked('genericons', $options['displaytype']); ?> onclick="toType('genericons')" /> Genericons</label><br />
				<label><input name="cxnh_quickshare_options[displaytype]" type="radio" value="text" <?php checked('text', $options['displaytype']); ?> onclick="toType('text')" /> Text</label>
			</td>
		</tr>
		<tr class="genericons-option">
			<th scope="row">Monochrome Icons</th>
			<td>
				<label><input id="monochrome_genericons" name="cxnh_quickshare_options[monochrome]" type="checkbox" value="1" <?php if (isset($options['monochrome'])) { checked('1', $options['monochrome']); } ?> /> Use a single color for all icons</label><br/>
			</td>
		</tr>
		<tr class="text-option">
			<th scope="row">Show Icons</th>
			<td>
				<label><input id="text_icons" name="cxnh_quickshare_options[text_icons]" type="checkbox" value="1" <?php if (isset($options['text_icons'])) { checked('1', $options['text_icons']); } ?> /> Display Small Icons Before Text</label><br/>
				<label id="text_icons_color"><input name="cxnh_quickshare_options[text_icons_color]" type="checkbox" value="1" <?php if (isset($options['text_icons_color'])) { checked('1', $options['text_icons_color']); } ?> /> icon color matches text color</label>
			</td>
		</tr>
		<tr class="n-icons-option" id="main-color">
			<th scope="row"><span class="text-option">Text</span><span class="genericons-option">Icon</span> Color</th>
			<td>
				<label><input id="inherit_colors" name="cxnh_quickshare_options[inherit_color]" type="checkbox" value="1" <?php if (isset($options['inherit_color'])) { checked('1', $options['inherit_color']); } ?> /> Inherit Link Colors</label><br/>
				<div class="colorwrap" id="maincolor" ><input name="cxnh_quickshare_options[color]" type="text" class="color-field" value="<?php echo $options['color']; ?>" /></div>
			</td>
		</tr>
		<tr class="n-icons-option" id="hover-color">
			<th scope="row"><span class="text-option">Text</span><span class="genericons-option">Icon</span> Hover Color</th>
			<td>
				<label class="genericons-option"><input name="cxnh_quickshare_options[monochrome_hover]" id="monochrome_hover" type="checkbox" value="1" <?php if (isset($options['monochrome_hover'])) { checked('1', $options['monochrome_hover']); } ?> /> Switch to the natural icon color on hover</label><br class="genericons-option" />
				<span id="hovercolorwrap"><input name="cxnh_quickshare_options[hovercolor]" id="hovercolor" type="text" class="color-field" value="<?php echo $options['hovercolor']; ?>" /></span>
			</td>
		</tr>
		<tr class="text-option" id="background-color">
			<th scope="row">Text Background Color</th>
			<td>
				<label><input name="cxnh_quickshare_options[bgtransparent]" id="bgtrans" type="checkbox" value="1" <?php if (isset($options['bgtransparent'])) { checked('1', $options['bgtransparent']); } ?> /> <span class="genericons-option">Transparent</span><span class="icons-option">Default (translucent)</span></label><br/>
				<div class="colorwrap" id="bgcolor" ><input name="cxnh_quickshare_options[bgcolor]" type="text" class="color-field" value="<?php echo $options['bgcolor']; ?>" data-default-color="#ffffff" /></div>
			</td>
		</tr>
		<tr class="advanced n-genericons-option">
			<th scope="row">Rounded Corners</th>
			<td>
				<input name="cxnh_quickshare_options[borderradius]" type="range" min="0" max="32" step="1" value="<?php echo $options['borderradius']; ?>" id="brinput" onchange="updatebr()" /><span id="br-current"><?php echo $options['borderradius']; ?></span>px
			</td>
		</tr>
		<tr>
			<th scope="row">Hover Effects</th>
			<td>
				<label><input name="cxnh_quickshare_options[effect-spin]" id="effect-spin" type="checkbox" value="1" <?php if (isset($options['effect-spin'])) { checked('1', $options['effect-spin']); } ?> /> <span class="n-text-option">Spin</span><span class="text-option">Skew</span></label><br/>
				<label><input name="cxnh_quickshare_options[effect-round]" id="effect-round" type="checkbox" value="1" <?php if (isset($options['effect-round'])) { checked('1', $options['effect-round']); } ?> /> Round</label><br/>
				<label><input name="cxnh_quickshare_options[effect-glow]" id="effect-glow" type="checkbox" value="1" <?php if (isset($options['effect-glow'])) { checked('1', $options['effect-glow']); } ?> /> Glow</label><br/>
				<label><input name="cxnh_quickshare_options[effect-contract]" id="effect-contract" type="checkbox" value="1" <?php if (isset($options['effect-contract'])) { checked('1', $options['effect-contract']); } ?> /> Contract</label><br/>
				<label><input name="cxnh_quickshare_options[effect-expand]" id="effect-expand" type="checkbox" value="1" <?php if (isset($options['effect-expand'])) { checked('1', $options['effect-expand']); } ?> /> Expand</label>
			</td>
		</tr>
		<tr class="advanced">
			<th scope="row">Responsive Design [<a href="javascript:void(0)" id="responsive-help">?</a>]</th>
			<td>
				<p id="responsive-small">Switch to small size on devices/viewports rendering at <input type="number" size="3" style="max-width: 50px;" name="cxnh_quickshare_options[respond_small]" value="<?php echo $options['respond_small']; ?>" />px or less.</p>
				<p>Hide QuickShare entirely on devices/viewports rendering at <input type="number" size="3" style="max-width: 50px;" name="cxnh_quickshare_options[respond_hide]" value="<?php echo $options['respond_hide']; ?>" />px or less.</p>
				<p style="font-style: italic; display: none;" id="responsive-description">Use these options if QuickShare takes up too much space on mobile devices like smartphones and tablets. A value of "600" will generally apply to smartphones but not tablets. A good way to test these values is to view your site on a PC and resize the browser window so that it's only as wide as a tablet or phone.</p>
			</td>
		</tr>
		<tr class="advanced">
			<th scope="row">Custom CSS</th>
			<td>
				<textarea id="customcss" name="cxnh_quickshare_options[customcss]"><?php echo $options['customcss']; ?></textarea>
			</td>
		</tr>
		<tr class="hidden">
			<td colspan="2">
				<input type="hidden" id="nav-input" name="cxnh_quickshare_options[settingspage]" value="<?php echo $options['settingspage']; ?>" />
				<input type="hidden" name="cxnh_quickshare_options[plugin_version]" value="<?php echo $options['plugin_version']; ?>" />
			</td>
		</tr>
		<tr><td colspan="2">
		<div id="quickshare-preview" class="quickshare-container">
		<ul class="<?php echo cxnh_quickshare_get_ulclass(); ?>" id="quickshare-design-preview">
			<li class="quickshare-share"><?php echo cxnh_quickshare_getOption('sharelabel',$options); ?></li> 
			<?php if(cxnh_quickshare_getOption('facebook',$options)){ ?><li><a href="javascript:void(0)" title="Share on Facebook"><span class="quickshare-facebook">Facebook</span></a></li><?php } ?>
			<?php if(cxnh_quickshare_getOption('twitter',$options)){ ?><li><a href="javascript:void(0)" title="Share on Twitter"><span class="quickshare-twitter">Twitter</span></a></li><?php } ?>
			<?php if(cxnh_quickshare_getOption('pintrest',$options)){ ?><li><a href="javascript:void(0)" title="Share on Pinterest"><span class="quickshare-pinterest">Pinterest</span></a></li><?php } ?>
			<?php if(cxnh_quickshare_getOption('linkedin',$options)){ ?><li><a href="javascript:void(0)" title="Share on Linkedin"><span class="quickshare-linkedin">Linkedin</span></a></li><?php } ?>
			<?php if(cxnh_quickshare_getOption('googleplus',$options)){ ?><li><a href="javascript:void(0)" title="Share on Google+"><span class="quickshare-googleplus">Google+</span></a></li><?php } ?>
			<?php if(cxnh_quickshare_getOption('tumblr',$options)){ ?><li><a href="javascript:void(0)" title="Share on Tumblr" ><span class="quickshare-tumblr">Tumblr</span></a></li><?php } ?>
			<?php if(cxnh_quickshare_getOption('reddit',$options)){ ?><li><a href="javascript:void(0)" title="Submit to Reddit"><span class="quickshare-reddit">Reddit</span></a></li><?php } ?>
			<?php if(cxnh_quickshare_getOption('stumbleupon',$options)){ ?><li><a href="javascript:void(0)" title="Share on StumbleUpon"><span class="quickshare-stumbleupon">Stumble Upon</span></a></li><?php } ?>
			<?php if(cxnh_quickshare_getOption('email',$options)){ ?><li><a href="javascript:void(0)" title="Share via Email"><span class="quickshare-email">Email</span></a></li><?php } ?>
		</ul>
		</div>
		</td></tr>
	</table>

	<p class="submit">
		<input type="submit" class="button-primary" value="Save Changes" />
	</p>
	</form>
</div>
<?php
} //options page

// Sanitize and validate input. Accepts an array, return a sanitized array.
function cxnh_quickshare_validate_options($input) {
	// sanitize img url
	$input['imgurl'] = esc_url($input['customcss']);
	
	// sanitize text field
	$input['sharelabel'] = sanitize_text_field($input['sharelabel']); // not allowing html here because it should be a plaintext label and can be formated with custom css (it's already in an <li>)
	
	// sanitize numeric inputs
	$input['borderradius'] = absint($input['borderradius']);
	
//	sanitize_hex_color is only available in the theme customizer...
//	$input['color'] = sanitize_hex_color( $input['color'] );
//	$input['hovercolor'] = sanitize_hex_color( $input['hovercolor'] );
//	$input['bgcolor'] = sanitize_hex_color( $input['bgcolor'] );
	
	// sanitize the excluded_ids field
	if( array_key_exists( 'excluded_ids', $input ) ) {
		$ids_arr = explode( ',', $input['excluded_ids'] );
		$ids_arr_2 = array();
		foreach( $ids_arr as $id )
			$ids_arr_2[] = absint( trim( $id ) );
		$input['excluded_ids'] = implode( ',', array_filter( $ids_arr_2 ) );
	}
	
	// validate css
	//TODO: some form of css validation to minimize user error
	
	return $input;
}

// quick check if array_key_exists for true/false options, otherwise return the option index value
function cxnh_quickshare_getOption( $option, $options = null ) {
	if(!is_array($options))
		$options = get_option('cxnh_quickshare_options');
	if(array_key_exists($option, $options))
		return $options[$option];
	else
		return false;
}

// determine if we should display QuickShare on the current object
function cxnh_quickshare_show_output() {
	$options = get_option('cxnh_quickshare_options');
	global $post;
	global $quickshare_in_excerpt;
	
	$output = false;
	
	if ( is_feed() )
		$output = false;
	elseif ( $quickshare_in_excerpt )
		$output = false;
	elseif ( cxnh_quickshare_getOption('everywhere',$options) )
		$output = true;
	elseif ( cxnh_quickshare_getOption('posts',$options) && get_post_type() == 'post' )
		$output = true;
	elseif ( cxnh_quickshare_getOption('pages',$options) && get_post_type() == 'page' )
		$output = true;
	elseif ( cxnh_quickshare_getOption('attachments',$options) && get_post_type() == 'attachment' ) 
		$output = true;
	
	$xids = cxnh_quickshare_getOption('excluded_ids',$options);
	if($xids) {
		$xids_arr = explode(',',$xids);
		if(in_array($post->ID,$xids_arr))
			$output = false;
	}
	
	unset($quickshare_in_excerpt);
	
	return $output;
}

// All of the functions that display the QuickShare output.
// Several things are run site-wide (such as styles & og meta) because we don't know
// where the user might be using the custom output functions or the shortcode, and
// this data won't hurt anything anyway.
add_action('wp_enqueue_scripts','cxnh_quickshare_styles');
function cxnh_quickshare_styles() {
	wp_enqueue_style('quickshare',plugins_url('/quickshare.css',__FIlE__));
	if(cxnh_quickshare_getOption('displaytype') == 'genericons' || (cxnh_quickshare_getOption('displaytype') == 'text' && cxnh_quickshare_getOption('text_icons')))
		wp_enqueue_style('genericons',plugins_url('/genericons/genericons.css',__FILE__));
}

add_action('wp_head','cxnh_quickshare_head');
function cxnh_quickshare_head() {
	$options = get_option('cxnh_quickshare_options');
	
	// add open graph tags if appropriate
	if( cxnh_quickshare_getOption('ogmeta',$options) && is_singular() ) {
		echo '<meta name="og:title" content="' . get_the_title() . '" />';
		if(cxnh_quickshare_get_post_image()) 
			echo '<meta name="og:image" content="' . cxnh_quickshare_get_post_image() . '" />';
		echo '<meta name="og:url" content="' . get_permalink() . '" />';
		echo '<meta name="og:description" content="' . cxnh_quickshare_get_post_description() . '" />';
		if( !is_front_page() )
			echo '<meta name="og:site_name" content="' . get_bloginfo('name') . '" />'; // to be used if this object/webpage is part of a larger website - not really the case for the homepage
	}
	
	//custom colors, etc. that aren't set with classes
?>
	<style type="text/css">
		.quickshare-text span,
		<?php if(cxnh_quickshare_getOption('text_icons_color',$options)){ echo '.quickshare-text span:before,'; } ?>
		.quickshare-text span:hover,
		.quickshare-genericons.monochrome span:before,
		.quickshare-genericons.monochrome-color span:before {
			<?php if(!cxnh_quickshare_getOption('inherit_color',$options))
				echo 'color: '.cxnh_quickshare_getOption('color',$options).';';
			if(!cxnh_quickshare_getOption('bgtransparent',$options)&&cxnh_quickshare_getOption('displaytype',$options)=='text')
				echo 'background-color: '.cxnh_quickshare_getOption('bgcolor',$options).';';
			?>
		}
		.quickshare-text span:hover,
		<?php if(cxnh_quickshare_getOption('text_icons_color',$options)){ echo '.quickshare-text span:hover:before,'; } ?>
		.quickshare-genericons.monochrome span:hover:before {
			<?php if(!cxnh_quickshare_getOption('inherit_color',$options))
				echo 'color: ' . cxnh_quickshare_getOption('hovercolor',$options) .';';
			?>
		}
		.quickshare-icons span,
		.quickshare-genericons span:before,
		.quickshare-text span {
			border-radius: <?php echo cxnh_quickshare_getOption('borderradius',$options); ?>px;
		}
		
		<?php if(cxnh_quickshare_getOption('respond_small')) { ?>
			@media only screen and (max-width: <?php echo cxnh_quickshare_getOption('respond_small'); ?>px) {
				/* Duplication of class-based small styling from quickshare.css */
				.quickshare-icons span {
					width: 32px !important;
					height: 32px !important;
				}
				.quickshare-genericons span {
					width: 32px !important;
					min-width: 32px !important;
					height: 32px !important;
				}
				.quickshare-genericons span:before {
					width: 32px !important;
					height: 32px !important;
					font-size: 32px !important;
				}
				li.quickshare-share {
					width: auto !important; /* need to add this again in the !important stack */
				}
				.quickshare-text span {
					font-size: .7em !important;
				}
			}
		<?php } if(cxnh_quickshare_getOption('respond_hide')) { ?>
			@media only screen and (max-width: <?php echo cxnh_quickshare_getOption('respond_hide'); ?>px) {
				.quickshare-container { display: none; }
			}		
		<?php } ?>
		
		<?php echo cxnh_quickshare_getOption('customcss',$options); ?>
	</style>
	<?php
}

// add the QuickShare shortcode
add_shortcode( 'quickshare', 'cxnh_do_quickshare_shortcode' );
function cxnh_do_quickshare_shortcode( $atts ){
	return cxnh_quickshare_makeOutput();
}

// cxnh_add_quickshare_optput is the filter that appends quickshare to the_content
add_filter('the_content', 'cxnh_add_quickshare_output',15);

function cxnh_add_quickshare_output( $content ) {
	$options = get_option('cxnh_quickshare_options');
	if( cxnh_quickshare_show_output() ) {
		$sharecode = cxnh_quickshare_makeOutput();
		return $content . $sharecode;
	}
	else
		return $content;
}

// if we're in the_excerpt, set a flag so that we don't display QuickShare
// necessary because WordPress applies the_content filters to the_excerpt
add_filter('get_the_excerpt', 'cxnh_quickshare_inexcerpt', 1);
function cxnh_quickshare_inexcerpt( $excerpt ) {
	global $quickshare_in_excerpt;
	$quickshare_in_excerpt = true;
	
	return $excerpt;
}

// do_quickshare_output() is used for custom QuickShare output in template files
function do_quickshare_output( $url=null, $title=null, $source=null, $description=null, $imgurl=null ) {
	$sharecode = cxnh_quickshare_makeOutput( $url, $title, $source, $description, $imgurl );
	echo $sharecode;
}

// create the actual html for QuickShare, and return it
function cxnh_quickshare_makeOutput( $url=null, $title=null, $source=null, $description=null, $imgurl=null ) {
	$options = get_option('cxnh_quickshare_options');
	global $post;

	//sharing data
	if(!$url)
		$url = get_permalink();
	$url = urlencode($url);
	if(!$imgurl)
		$imgurl = cxnh_quickshare_get_post_image();
	$imgurl = urlencode($imgurl);
	if(!$title)
		$title = get_the_title();
	$title = urlencode($title);
	if(!$source)
		$source = get_bloginfo('name');
	$source = urlencode($source);
	if(!$description)
		$description = cxnh_quickshare_get_post_description();
	$description = urlencode($description);
	
	ob_start();
?>
	<div class="quickshare-container">
	<ul class="<?php echo cxnh_quickshare_get_ulclass(); ?>">
		<li class="quickshare-share"><?php echo cxnh_quickshare_getOption('sharelabel',$options); ?></li> 
		<?php if(cxnh_quickshare_getOption('facebook',$options)){ ?><li><a href="https://facebook.com/sharer.php?u=<?php echo $url; ?>&amp;t=<?php echo $title.'+<+'.$source; ?>" target="_blank" title="Share on Facebook"><span class="quickshare-facebook">Facebook</span></a></li><?php } ?>
		<?php if(cxnh_quickshare_getOption('twitter',$options)){ ?><li><a href="https://twitter.com/share?url=<?php echo $url; ?>" target="_blank" title="Share on Twitter"><span class="quickshare-twitter">Twitter</span></a></li><?php } ?>
		<?php if(cxnh_quickshare_getOption('pintrest',$options) && ($imgurl != urlencode(cxnh_quickshare_getOption('image',$options)) || !cxnh_quickshare_getOption('hidepintrest',$options))){ ?><li><a href="http://pinterest.com/pin/create/button/?url=<?php echo $url; ?>&amp;media=<?php echo $imgurl; ?>&amp;description=<?php echo $description; ?>" target="_blank" title="Share on Pinterest"><span class="quickshare-pinterest">Pinterest</span></a></li><?php } ?>
		<?php if(cxnh_quickshare_getOption('linkedin',$options)){ ?><li><a href="http://linkedin.com/shareArticle?mini=true&amp;url=<?php echo $url; ?>&amp;title=<?php echo $title; ?>&amp;source=<?php echo $source; ?>&amp;summary=<?php echo $description; ?>" title="Share on Linkedin" target="_blank"><span class="quickshare-linkedin">Linkedin</span></a></li><?php } ?>
		<?php if(cxnh_quickshare_getOption('googleplus',$options)){ ?><li><a href="https://plus.google.com/share?url=<?php echo $url; ?>" target="_blank" title="Share on Google+"><span class="quickshare-googleplus">Google+</span></a></li><?php } ?>
		<?php if(cxnh_quickshare_getOption('tumblr',$options)){ ?><li><a href="http://tumblr.com/share/link?url=<?php echo $url; ?>&amp;name=<?php echo $title.'+<+'.$source; ?>&amp;description=<?php echo $description; ?>" title="Share on Tumblr" target="_blank"><span class="quickshare-tumblr">Tumblr</span></a></li><?php } ?>
		<?php if(cxnh_quickshare_getOption('reddit',$options)){ ?><li><a href="http://reddit.com/submit?url=<?php echo $url; ?>&amp;title=<?php echo $title.'+<+'.$source; ?>" title="Submit to Reddit" target="_blank"><span class="quickshare-reddit">Reddit</span></a></li><?php } ?>
		<?php if(cxnh_quickshare_getOption('stumbleupon',$options)){ ?><li><a href="http://stumbleupon.com/submit?url=<?php echo $url; ?>&amp;title=<?php echo $title.'+<+'.$source; ?>" target="_blank" title="Share on StumbleUpon"><span class="quickshare-stumbleupon">Stumble Upon</span></a></li><?php } ?>
		<?php if(cxnh_quickshare_getOption('email',$options)){ ?><li><a href="mailto:?subject=<?php echo $source.':+'.$title; ?>&amp;body=<?php echo $url; ?>" target="_blank" title="Share via Email"><span class="quickshare-email">Email</span></a></li><?php } ?>
	</ul>
	</div>
<?php
	return ob_get_clean();
}

function cxnh_quickshare_get_ulclass() {
		$options = get_option('cxnh_quickshare_options');
		$type = cxnh_quickshare_getOption('displaytype',$options);
		if($type == 'icons'){
			$class = 'quickshare-icons';
		}
		elseif($type =='genericons'){
			$class = 'quickshare-genericons';
			if(cxnh_quickshare_getOption('monochrome',$options)) {
				$class .= ' monochrome';
				if(cxnh_quickshare_getOption('monochrome_hover',$options))
					$class .= '-color';
			}
		}
		else {
			$class = 'quickshare-text';
			if(cxnh_quickshare_getOption('text_icons',$options))
				$class .= ' qs-genericons';
		}
		//effects
		if(cxnh_quickshare_getOption('effect-spin',$options))
			$class .= ' quickshare-effect-spin';
		if(cxnh_quickshare_getOption('effect-round',$options))
			$class .= ' quickshare-effect-round';
		if(cxnh_quickshare_getOption('effect-glow',$options))
			$class .= ' quickshare-effect-glow';
		if(cxnh_quickshare_getOption('effect-expand',$options))
			$class .= ' quickshare-effect-expand';
		if(cxnh_quickshare_getOption('effect-contract',$options))
			$class .= ' quickshare-effect-contract';
		//size
		if(cxnh_quickshare_getOption('size',$options)) // ie, if not "normal"
			$class .= ' quickshare-' . cxnh_quickshare_getOption('size',$options);
		
		return $class;
}

function cxnh_quickshare_get_post_image() {
	global $post;
	$imgdata;
	
	// if there's a featured image, use it
	if(has_post_thumbnail())
		$imgdata = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium' );
	elseif(is_attachment())
		$imgdata = wp_get_attachment_image_src( $post->ID, 'medium' ); // attachment post type, so post id is attachment id
	else {
		// next, try grabbing first attached image
		$args = array(
			'numberposts' => 1,
			'post_parent' => $post->ID,
			'post_type' => 'attachment',
			'post_mime_type' => 'image'
		);
		$attachments = get_children( $args ); //array is keyed by attachment id
		if(!empty($attachments)) {
			$rekeyed_array = array_values($attachments);
			$imgdata = wp_get_attachment_image_src( $rekeyed_array[0]->ID , 'medium' );
		}
		else {
			//finally, look for the first img tag brute-force. Presumably if there's a caption or it's a gallery or anything it should have come up as an attachment
			$result = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches ); // find imag tags, grab srcs
			if($result > 0)
				return $matches[1][0]; // return first img src, no way to select size if we've gotten this deep
		}
	}
	
	if(!empty($imgdata))
		return $imgdata[0]; //image url
	else
		return cxnh_quickshare_getOption('image');
}

function cxnh_quickshare_get_post_description() {
	// essentially a summary of get_the_excerpt(), but avoiding most filters other than custom ones (otherwise results in infinite loop)
	$post = get_post();
	$excerpt = $post->post_excerpt;
	if($excerpt == '')
		$excerpt = $post->post_content;
	$excerpt = wp_trim_words($excerpt, 40); //do this first so we aren't processing a ton of data
	$excerpt = strip_shortcodes( $excerpt );
	$excerpt = strip_tags($excerpt);
	$excerpt = str_replace(']]>', ']]&gt;', $excerpt);
	return $excerpt;
}