<?php
/*
| ----------------------------------------------------
| File        : class-widgets.php
| Project     : Special Recent Posts FREE Edition plugin for Wordpress
| Version     : 1.9.9
| Description : This is the widget main class.
| Author      : Luca Grandicelli
| Author URL  : http://www.lucagrandicelli.com
| Plugin URL  : http://www.specialrecentposts.com
| Copyright (C) 2011-2012  Luca Grandicelli
| ----------------------------------------------------
*/

class WDG_SpecialRecentPostsFree extends WP_Widget {

	// Declaring global plugin values.
	private $plugin_args;

/*
| ---------------------------------------------
| CLASS CONSTRUCTOR & DECONSTRUCTOR
| ---------------------------------------------
*/
	// Class Constructor.
	// In this section we define the widget global values.
	function WDG_SpecialRecentPostsFree() {
	
		// Setting up widget options.
        $widget_ops = array (
            'classname'   => 'widget_specialrecentpostsFree',
            'description' => __('The Special Recent Posts FREE Edition widget. Drag to configure.', SRP_TRANSLATION_ID)
        );
		
        // Assigning widget options.
		$this->WP_Widget('WDG_SpecialRecentPostsFree', 'Special Recent Posts FREE', $widget_ops);
		
		// Assigning global plugin option values to local variable.
		$this->plugin_args = get_option('srp_plugin_options');
	}

/*
| ---------------------------------------------
| WIDGET FORM DISPLAY METHOD
| ---------------------------------------------
*/
	// Main form widget method.
	function form($instance) {
	
		// Outputs the options form on widget panel.
		$this->buildWidgetForm($instance);
	}

/*
| ---------------------------------------------
| WIDGET UPDATE & MAIN METHODS
| ---------------------------------------------
*/
	// Main method for widget update process.
	function update($new_instance, $old_instance) {
	
		// Declaring global plugin values.
		global $srp_default_widget_values;
		
		// Processes widget options to be saved.
		$instance = SpecialRecentPostsFree::srp_version_map_check($old_instance);
		
		// Looping through the entire list of plugin options.
		foreach($srp_default_widget_values as $k => $v) {
			
			// Switching through each option.
			switch($k) {
			
				case "post_random":
				case "thumbnail_link":
				case "category_title":
				case "post_current_hide":
				case "post_date":
				case "widget_title_hide":
				case "nofollow_links":
				case "string_break_link":
					
					// Fix all the NULL values coming from unchecked checkboxes.
					$instance[$k] = (!isset($new_instance[$k])) ? "no" : $new_instance[$k];
				break;
				
				case "thumbnail_width":
				case "thumbnail_height":
				
					// Checking if the new value is numeric. Then assign it.
					if (is_numeric($new_instance[$k])) $instance[$k] = trim($new_instance[$k]);
				break;
				
				case "post_limit":
				case "post_content_length":
				case "post_title_length":
				
					// Checking if the new value is numeric and is not zero. Then assign it.
					if ( (is_numeric($new_instance[$k])) && ($new_instance[$k] != 0) ) $instance[$k] = trim($new_instance[$k]);
				break;
				
				case "post_offset":
					
					// Checking if the new value is numeric and is > of zero. Then assign it.
					$instance[$k] = ( (is_numeric($new_instance[$k])) && ($new_instance[$k] > 0) ) ? trim($new_instance[$k]) : 0;
				break;

				default:
				
					// Default behaviour: for all the other options, assign the new value.
					$instance[$k] = $new_instance[$k];
				break;
			}
		}

		// Return new widget instance.
		return $instance;
	}
	
	/*
	| ---------------------------------------------
	| Main widget method. Main logic lies here.
	| ---------------------------------------------
	*/
	function widget($args, $instance) {
	
		// Checking Visualization filter.
		if (SpecialRecentPostsFree::visualizationCheck($instance, 'widget')) {
		
			// Extracting arguments.
			extract($args, EXTR_SKIP);
			
			// Printing pre-widget stuff.
			echo $before_widget;
			
			// Creating an instance of Special Recent Posts Class.
			$srp = new SpecialRecentPostsFree($instance, $this->id);
			
			// Displaying posts.
			if (is_object($srp)) $srp->displayPosts(true, 'print');
			
			// Printing after widget stuff.
			echo $after_widget;
		}
	}
	
	/*
	| --------------------------------------------------
	| This method generates the shortcode and PHP code
	| from the current widget values.
	| --------------------------------------------------
	*/
	function srp_generate_code($instance, $code_mode) {
	
		// Switching between "shortcode" or "php code".
		switch($code_mode) {
		
			case "shortcode":
			
				// Defining global widget values.
				global $srp_default_widget_values;
				
				// Opening shortcode.
				$shortcode_code = "[srp";				
				
				// Looping through list of available widget values.
				foreach($instance as $key=>$value) {
				
					// Checking if the current set value is different than the default one.
					if (($srp_default_widget_values[$key] != $value)) {
					
						// If it's so, put the new key=>value in the shortcode.
						$shortcode_code .= " " . $key . "=\"" . $value . "\"";
					}
				}
				
				// Closing shortcode.
				$shortcode_code .= "]";
				
				// Return the shortcode.
				return $shortcode_code;
			break;
			
			case "php":
			
				// Defining global widget values.
				global $srp_default_widget_values;
				
				// Opening PHP code.
				$phpcode_code = "&lt;?php\n";
				
				// Building PHP $args.
				$phpcode_code .= "\$args = array(\n";		
				
				// Looping through list of available widget values.
				foreach($instance as $key=>$value) {
				
					// Checking if the current set value is different than the default one.
					if (($srp_default_widget_values[$key] != $value)) {
					
						// If it's so, put the new key=>value in the PHP code.
						$phpcode_code .= "\"" . $key . "\" => \"" . $value . "\",";
					}
				}
				
				// Right trimming the last comma from the $args list.
				$phpcode_code = rtrim($phpcode_code, ',');
				
				// Closing PHP code.
				$phpcode_code .= ");\n";
				$phpcode_code .= "special_recent_posts(\$args);\n";
				$phpcode_code .= "?&gt;\n";
				
				// Return PHP code.
				return $phpcode_code;
			break;
		}
	}
	
	/*
	| --------------------------------------------------
	| This method builds the widget admin form.
	| --------------------------------------------------
	*/
	function buildWidgetForm($instance) {
	
		// Loading default widget values.
		global $srp_default_widget_values;
		
		// Loading default plugin settings.
		$plugin_args = get_option('srp_plugin_options');
		
		// Merging default values with instance array, in case this is empty.
		$instance = wp_parse_args( (array) SpecialRecentPostsFree::srp_version_map_check($instance), $srp_default_widget_values);
?>

	<!-- BOF Widget Accordion -->
	<img class="srp_accordion_widget_header_image" src="<?php echo SRP_PLUGIN_URL . SRP_WIDGET_HEADER; ?>" alt="Special Recent Posts FREE Edition v<?php echo SRP_PLUGIN_VERSION;?>"/>
	<dl class="srp-wdg-accordion">
	
		<!-- BOF Basic Options -->
		<dt class="srp-widget-optionlist-dt-basic">
			<a class="srp-wdg-accordion-item accordion-active-link" href="#1" title="<?php _e('Basic Options', SRP_TRANSLATION_ID); ?>" name="1"><?php _e('Basic Options', SRP_TRANSLATION_ID); ?></a>
		</dt>
		<dd class="srp-widget-optionlist-dd-basic">
			<ul class="srp-widget-optionlist-basic srp-widget-optionlist">

				<!-- BOF Widget Title Option. -->
				<li>
					<label for="<?php echo $this->get_field_id('widget_title'); ?>" class="srp-widget-label"><?php _e('Widget title', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Enter the text for the main widget title.',SRP_TRANSLATION_ID); ?></small>
					<input type="text" id="<?php echo $this->get_field_id('widget_title'); ?>" name="<?php echo $this->get_field_name('widget_title'); ?>" value="<?php echo htmlspecialchars($instance["widget_title"], ENT_QUOTES); ?>" size="30" class="fullwidth" />
				</li>
				<!-- EOF Widget Title Option. -->
				
				<!-- BOF Post Type Display. -->
				<li>
					<label for="<?php echo $this->get_field_id('post_type'); ?>" class="srp-widget-label"><?php _e('Display posts or pages?', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Select whether to display posts or pages.',SRP_TRANSLATION_ID); ?></small><br />
					<select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="srp-widget-select">
						<option value="post" <?php selected($instance["post_type"], 'post'); ?>><?php _e('Posts', SRP_TRANSLATION_ID); ?></option>
						<option value="page" <?php selected($instance["post_type"], 'page'); ?>><?php _e('Pages', SRP_TRANSLATION_ID); ?></option>
					</select>
				</li>
				<!-- EOF Post Type Display. -->
				
				<!-- BOF Max number of posts Option. -->
				<li>
					<label for="<?php echo $this->get_field_id('post_limit'); ?>" class="srp-widget-label"><?php _e('Max number of posts/pages to display?', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Enter the maximum number of posts/pages to display.', SRP_TRANSLATION_ID); ?></small><br />
					<input type="text" id="<?php echo $this->get_field_id('post_limit'); ?>" name="<?php echo $this->get_field_name('post_limit'); ?>" value="<?php echo stripslashes($instance['post_limit']); ?>" size="5" />
				</li>
				<!-- EOF Max number of posts Option. -->
			</ul>
		</dd>
		<!-- EOF Basic Options -->
		
		<!-- BOF Thumbnails Options -->
		<dt class="srp-widget-optionlist-dt-thumbnails">
			<a class="srp-wdg-accordion-item" href="#2" title="<?php _e('Thumbnails Options', SRP_TRANSLATION_ID); ?>" name="2"><?php _e('Thumbnails Options', SRP_TRANSLATION_ID); ?></a>
		</dt>
		<dd class="srp-widget-optionlist-dd-thumbnails">
			<ul class="srp-widget-optionlist-thumbnails srp-widget-optionlist">
				
				<!-- BOF Thumbnail Option. -->
				<li>
					<label for="<?php echo $this->get_field_id('display_thumbnail'); ?>" class="srp-widget-label"><?php _e('Display thumbnails?', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Select if thumbnails should be displayed or not.', SRP_TRANSLATION_ID); ?></small><br />
					<select id="<?php echo $this->get_field_id('display_thumbnail'); ?>" name="<?php echo $this->get_field_name('display_thumbnail'); ?>" class="srp-widget-select">
						<option value="yes" <?php selected($instance["display_thumbnail"], 'yes'); ?>><?php _e('Yes', SRP_TRANSLATION_ID); ?></option>
						<option value="no" <?php selected($instance["display_thumbnail"], 'no'); ?>><?php _e('No', SRP_TRANSLATION_ID); ?></option>
					</select>
				</li>
				<!-- EOF Thumbnail Option. -->

				<!-- BOF Thumbnail Width. -->
				<li>
					<label for="<?php echo $this->get_field_id('thumbnail_width'); ?>" class="srp-widget-label"><?php _e('Thumbnail width', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Enter the thumbnail width in pixel:',SRP_TRANSLATION_ID); ?></small><br />
					<input type="text" id="<?php echo $this->get_field_id('thumbnail_width'); ?>" name="<?php echo $this->get_field_name('thumbnail_width'); ?>" value="<?php echo htmlspecialchars($instance["thumbnail_width"], ENT_QUOTES); ?>" size="8" />px
				</li>
				<!-- EOF Thumbnail Width. -->
				
				<!-- BOF Thumbnail Height. -->
				<li>
					<label for="<?php echo $this->get_field_id('thumbnail_height'); ?>" class="srp-widget-label"><?php _e('Thumbnail height', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Enter the thumbnail height in pixel:',SRP_TRANSLATION_ID); ?></small><br />
					<input type="text" id="<?php echo $this->get_field_id('thumbnail_height'); ?>" name="<?php echo $this->get_field_name('thumbnail_height'); ?>" value="<?php echo htmlspecialchars($instance["thumbnail_height"], ENT_QUOTES); ?>" size="8" />px
				</li>
				<!-- EOF Thumbnail Height. -->
				
				<!--BOF Thumbnail Link Mode -->
				<li>
					<input type="checkbox" id="<?php echo $this->get_field_id('thumbnail_link'); ?>" name="<?php echo $this->get_field_name('thumbnail_link'); ?>" value="yes" <?php checked($instance["thumbnail_link"], 'yes'); ?> />
					<label for="<?php echo $this->get_field_id('thumbnail_link'); ?>" class="srp-widget-label-inline"><?php _e('Link thumbnail to post', SRP_TRANSLATION_ID); ?></label><br />
					<small><?php _e('Check this box if you want to link the thumbnail to the related post/page.', SRP_TRANSLATION_ID); ?></small>
				</li>
				<!--EOF Thumbnail Link Mode -->			
				
				<!-- BOF Thumbnail Display Mode. -->
				<li>
					<label for="<?php echo $this->get_field_id('thumbnail_rotation'); ?>" class="srp-widget-label"><?php _e('Rotate thumbnail?', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Select the thumbnails rotation mode:',SRP_TRANSLATION_ID); ?></small>
					<select id="<?php echo $this->get_field_id('thumbnail_rotation'); ?>" name="<?php echo $this->get_field_name('thumbnail_rotation'); ?>" class="srp-widget-select">
						<option value="no" <?php selected($instance["thumbnail_rotation"], 'adaptive'); ?>><?php _e('No rotation (Default)', SRP_TRANSLATION_ID); ?></option>
						<option value="rotate-cw" <?php selected($instance["thumbnail_rotation"], 'rotate-cw'); ?>><?php _e('Rotate CW', SRP_TRANSLATION_ID); ?></option>
						<option value="rotate-ccw" <?php selected($instance["thumbnail_rotation"], 'rotate-ccw'); ?>><?php _e('Rotate CCW', SRP_TRANSLATION_ID); ?></option>
					</select>
				</li>
				<!-- EOF Thumbnail Display Mode. -->
			</ul>
		</dd>
		<!-- EOF Thumbnails Options -->
		
		<!-- BOF Post Options -->
		<dt class="srp-widget-optionlist-dt-posts">
			<a class="srp-wdg-accordion-item" href="#3" title="<?php _e('Posts Options', SRP_TRANSLATION_ID); ?>" name="3"><?php _e('Posts Options', SRP_TRANSLATION_ID); ?></a>
		</dt>
		<dd class="srp-widget-optionlist-dd-posts">
			<ul class="srp-widget-optionlist-posts srp-widget-optionlist">
			
				<!-- BOF Title Max Text Size. -->
				<li>
					<label for="<?php echo $this->get_field_id('post_title_length'); ?>" class="srp-widget-label"><?php _e('Cut title text after:', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Select after how many characters or words every post title should be cut:',SRP_TRANSLATION_ID); ?></small><br />
					<input type="text" id="<?php echo $this->get_field_id('post_title_length'); ?>" name="<?php echo $this->get_field_name('post_title_length'); ?>" value="<?php echo htmlspecialchars($instance["post_title_length"], ENT_QUOTES); ?>" size="4" />
					<select id="<?php echo $this->get_field_id('post_title_length_mode'); ?>" name="<?php echo $this->get_field_name('post_title_length_mode'); ?>" class="srp-widget-select">
						<option value="words" <?php selected($instance["post_title_length_mode"], 'words'); ?>><?php _e('Words', SRP_TRANSLATION_ID); ?></option>
						<option value="chars" <?php selected($instance["post_title_length_mode"], 'chars'); ?>><?php _e('Characters', SRP_TRANSLATION_ID); ?></option>
						<option value="fulltitle" <?php selected($instance["post_title_length_mode"], 'fulltitle'); ?>><?php _e('Use full title (no cut)', SRP_TRANSLATION_ID); ?></option>
					</select>
				</li>
				<!-- EOF Title Max Text Size. -->
				
				<!-- BOF Post content type. -->
				<li>
					<label for="<?php echo $this->get_field_id('post_content_type'); ?>" class="srp-widget-label"><?php _e('Select post content type', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Select if you wish to display the normal post content or the post excerpt:',SRP_TRANSLATION_ID); ?></small><br />
					<select id="<?php echo $this->get_field_id('post_content_type'); ?>" name="<?php echo $this->get_field_name('post_content_type'); ?>" class="srp-widget-select">
						<option value="content" <?php selected($instance["post_content_type"], 'content'); ?>><?php _e('Post content', SRP_TRANSLATION_ID); ?></option>
						<option value="excerpt" <?php selected($instance["post_content_type"], 'excerpt'); ?>><?php _e('Post excerpt', SRP_TRANSLATION_ID); ?></option>
					</select>
				</li>
				<!-- EOF Post content type. -->

				
				<!-- BOF Post Excerpt Max Text Size. -->
				<li>
					<label for="<?php echo $this->get_field_id('post_content_length'); ?>" class="srp-widget-label"><?php _e('Cut post content after:', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Select after how many characters or words every post content should be cut:',SRP_TRANSLATION_ID); ?></small><br />					
					<input type="text" id="<?php echo $this->get_field_id('post_content_length'); ?>" name="<?php echo $this->get_field_name('post_content_length'); ?>" value="<?php echo htmlspecialchars($instance["post_content_length"], ENT_QUOTES); ?>" size="4" />
					<select id="<?php echo $this->get_field_id('post_content_length_mode'); ?>" name="<?php echo $this->get_field_name('post_content_length_mode'); ?>" class="srp-widget-select">
						<option value="words" <?php selected($instance["post_content_length_mode"], 'words'); ?>><?php _e('Words', SRP_TRANSLATION_ID); ?></option>
						<option value="chars" <?php selected($instance["post_content_length_mode"], 'chars'); ?>><?php _e('Characters', SRP_TRANSLATION_ID); ?></option>
						<option value="fullcontent" <?php selected($instance["post_content_length_mode"], 'fullcontent'); ?>><?php _e('Use the full content', SRP_TRANSLATION_ID); ?></option>
					</select>
				</li>
				<!-- EOF Post Excerpt Max Text Size. -->
				
				<!-- BOF Post Order Display Option. -->
				<li>
					<label for="<?php echo $this->get_field_id('post_order'); ?>" class="srp-widget-label"><?php _e('Select posts/pages order:', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Select the posts/pages display order:',SRP_TRANSLATION_ID); ?></small><br />
					<select id="<?php echo $this->get_field_id('post_order'); ?>" name="<?php echo $this->get_field_name('post_order'); ?>" class="srp-widget-select">
						<option value="DESC" <?php selected($instance["post_order"], 'DESC'); ?>><?php _e('Latest first (DESC)', SRP_TRANSLATION_ID); ?></option>
						<option value="ASC" <?php selected($instance["post_order"], 'ASC'); ?>><?php _e('Oldest first (ASC)', SRP_TRANSLATION_ID); ?></option>
					</select>
				</li>
				<!-- EOF Post Order Display Option. -->
				
				<!-- BOF Random Posts Option. -->
				<li>
					<input type="checkbox" id="<?php echo $this->get_field_id('post_random'); ?>" name="<?php echo $this->get_field_name('post_random'); ?>" value="yes" <?php checked($instance["post_random"], 'yes'); ?> />
					<label for="<?php echo $this->get_field_id('post_random'); ?>" class="srp-widget-label-inline"><?php _e('Enable random mode', SRP_TRANSLATION_ID); ?></label><br />
					<small><?php _e('Check this box if you want to randomize the posts order.',SRP_TRANSLATION_ID); ?></small>
				</li>
				<!-- EOF Random Posts Option. -->
				
				<!-- BOF Display Content Option. -->
				<li>
					<label for="<?php echo $this->get_field_id('post_content_mode'); ?>" class="srp-widget-label"><?php _e('Content display mode', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Select the content type that should appear on each post:',SRP_TRANSLATION_ID); ?></small><br />
					<select id="<?php echo $this->get_field_id('post_content_mode'); ?>" name="<?php echo $this->get_field_name('post_content_mode'); ?>" class="srp-widget-select">
						<option value="thumbonly" <?php selected($instance["post_content_mode"], 'thumbonly'); ?>><?php _e('Thumbnail only', SRP_TRANSLATION_ID); ?></option>
						<option value="titleonly" <?php selected($instance["post_content_mode"], 'titleonly'); ?>><?php _e('Title + Thumbnail', SRP_TRANSLATION_ID); ?></option>
						<option value="titleexcerpt" <?php selected($instance["post_content_mode"], 'titleexcerpt'); ?>><?php _e('Title + Thumbnail + Post text', SRP_TRANSLATION_ID); ?></option>
					</select>
				</li>
				<!-- EOF Display Content Option. -->
			</ul>
		</dd>
		<!-- EOF Post Options -->
		
		<!-- BOF Advanced post options 1 -->
		<dt class="srp-widget-optionlist-dt-advposts">
			<a class="srp-wdg-accordion-item" href="#4" title="<?php _e('Advanced Posts Options 1', SRP_TRANSLATION_ID); ?>" name="4"><?php _e('Advanced Posts Options 1', SRP_TRANSLATION_ID); ?></a>
		</dt>
		<dd class="srp-widget-optionlist-dd-advposts">
			<ul class="srp-widget-optionlist-advposts srp-widget-optionlist">
				
				<!-- BOF No posts message. -->
				<li>
					<label for="<?php echo $this->get_field_id('noposts_text'); ?>" class="srp-widget-label"><?php _e('No posts default text', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Enter the default text to display when there are no posts available:',SRP_TRANSLATION_ID); ?></small><br />
					<input type="text" id="<?php echo $this->get_field_id('noposts_text'); ?>" name="<?php echo $this->get_field_name('noposts_text'); ?>" value="<?php echo stripslashes($instance['noposts_text']); ?>" size="30" class="fullwidth"/>
				</li>
				<!-- EOF No posts message. -->
				
				<!-- BOF Current Post Hide Option. -->
				<li>
					<input type="checkbox" id="<?php echo $this->get_field_id('post_current_hide'); ?>" name="<?php echo $this->get_field_name('post_current_hide'); ?>" value="yes" <?php checked($instance["post_current_hide"], 'yes'); ?> />
					<label for="<?php echo $this->get_field_id('post_current_hide'); ?>" class="srp-widget-label-inline"><?php _e('Hide current post from list?', SRP_TRANSLATION_ID); ?></label><br />
					<small><?php _e('Check this box if you want to hide the current viewed post/page.', SRP_TRANSLATION_ID); ?></small>
				</li>
				<!-- EOF Single Post Hide Option. -->
				
				<!-- BOF Posts Offset Option.. -->
				<li>
					<label for="<?php echo $this->get_field_id('post_offset'); ?>" class="srp-widget-label"><?php _e('Post offset', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Enter the number of post/pages to skip from the beginning:', SRP_TRANSLATION_ID); ?></small><br />
					<input type="text" id="<?php echo $this->get_field_id('post_offset'); ?>" name="<?php echo $this->get_field_name('post_offset'); ?>" value="<?php echo stripslashes($instance['post_offset']); ?>" size="5" />
				</li>
				<!-- EOF Posts Offset Option.. -->
				
				<!-- BOF Post String Break Option. -->
				<li>
					<label for="<?php echo $this->get_field_id('string_break'); ?>" class="srp-widget-label"><?php _e('Post string break', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Enter the text to be displayed as string break just after the end of the post/page title:', SRP_TRANSLATION_ID); ?></small>
					<input type="text" id="<?php echo $this->get_field_id('string_break'); ?>" name="<?php echo $this->get_field_name('string_break'); ?>" value="<?php echo stripslashes($instance['string_break']); ?>" size="30" class="fullwidth" />
				</li>
				<!-- EOF Post String Break Option. -->
				
				<!-- BOF Image String Break Option. -->
				<li>
					<label for="<?php echo $this->get_field_id('image_string_break'); ?>" class="srp-widget-label"><?php _e('Image string break', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Enter the absolute URL of a custom image to use as string break:', SRP_TRANSLATION_ID); ?></small>
					<input type="text" id="<?php echo $this->get_field_id('image_string_break'); ?>" name="<?php echo $this->get_field_name('image_string_break'); ?>" value="<?php echo stripslashes($instance['image_string_break']); ?>" size="30" class="fullwidth" /><br />
				</li>
				<!-- EOF Image String Break Option. -->
				
				
				<!-- BOF String Break Link Option. -->
				<li>
					<input type="checkbox" id="<?php echo $this->get_field_id('string_break_link'); ?>" name="<?php echo $this->get_field_name('string_break_link'); ?>" value="yes" <?php checked($instance["string_break_link"], 'yes'); ?> />
					<label for="<?php echo $this->get_field_id('string_break_link'); ?>" class="srp-widget-label-inline"><?php _e('Link string/image break to post?', SRP_TRANSLATION_ID); ?></label><br />
					<small><?php _e('Check this box if you want to link the string/image break to the related post/page.', SRP_TRANSLATION_ID); ?></small>
				</li>
				<!-- EOF String Break Link Option. -->
				
				<!-- BOF Allowed Tags Option. -->
				<li>
					<label for="<?php echo $this->get_field_id('allowed_tags'); ?>" class="srp-widget-label"><?php _e('Post allowed tags', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Enter a list of allowed HTML tags to be rendered in the post content visualization. Leave blank for clean text without any markup.', SRP_TRANSLATION_ID); ?></small>
					<input type="text" id="<?php echo $this->get_field_id('allowed_tags'); ?>" name="<?php echo $this->get_field_name('allowed_tags'); ?>" value="<?php echo stripslashes($instance['allowed_tags']); ?>" size="30" class="fullwidth" /><br />
					<small><?php _e(htmlspecialchars('E.G: <a><p>'), SRP_TRANSLATION_ID); ?></small>
				</li>
				<!-- EOF Allowed Tags Option. -->
			</ul>
		</dd>
		<!-- EOF Advanced post options 1 -->

		<!-- BOF Advanced post options 2 -->
		<dt class="srp-widget-optionlist-dt-advposts">
			<a class="srp-wdg-accordion-item" href="#5" title="<?php _e('Advanced Posts Options 2', SRP_TRANSLATION_ID); ?>" name="5"><?php _e('Advanced Posts Options 2', SRP_TRANSLATION_ID); ?></a>
		</dt>
		<dd class="srp-widget-optionlist-dd-advposts">
			<ul class="srp-widget-optionlist-advposts srp-widget-optionlist">

				<!-- BOF No-Follow option link switcher. -->
				<li>
					<input type="checkbox" id="<?php echo $this->get_field_id('nofollow_links'); ?>" name="<?php echo $this->get_field_name('nofollow_links'); ?>" value="yes" <?php checked($instance["nofollow_links"], 'yes'); ?> />
					<label for="<?php echo $this->get_field_id('nofollow_links'); ?>" class="srp-widget-label-inline"><?php _e('Add nofollow attribute?', SRP_TRANSLATION_ID); ?></label><br />
					<small><?php _e('Check this box if you want to use the \'rel=nofollow\' attribute on every post/page link.', SRP_TRANSLATION_ID); ?>
					<a href="http://en.wikipedia.org/wiki/Nofollow" title="nofollow" target="_blank"><?php _e('Learn more', SRP_TRANSLATION_ID); ?></a></small>
				</li>
				<!-- EOF No-Follow option link switcher. -->

				<!-- BOF Meta Data. -->
				<li>
					<label for="<?php echo $this->get_field_id('meta_data'); ?>" class="srp-widget-label"><?php _e('Choose post meta to display', SRP_TRANSLATION_ID); ?></label>
					<input type="checkbox" id="<?php echo $this->get_field_id('post_date'); ?>" name="<?php echo $this->get_field_name('post_date'); ?>" value="yes" <?php checked($instance["post_date"], 'yes'); ?> />
					<small><?php _e('Display post date', SRP_TRANSLATION_ID); ?></small><br />
				</li>
				<!-- EOF Meta Data. -->
				
				<!-- BOF Date Content option. -->
				<li>
					<label for="<?php echo $this->get_field_id('date_format'); ?>" class="srp-widget-label"><?php _e('Post date format (*)', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Type here the coded format of post dates.',SRP_TRANSLATION_ID); ?></small>
					<input type="text" id="<?php echo $this->get_field_id('date_format'); ?>" name="<?php echo $this->get_field_name('date_format'); ?>" value="<?php echo stripslashes($instance['date_format']); ?>" size="30" class="fullwidth" /><br />
					<small><?php _e('*(F = Month name | j = Day of the month | S = ordinal suffix for the day of the month | Y = Year)', SRP_TRANSLATION_ID); ?></small><br />
					<small><a href="http://php.net/manual/en/function.date.php" title="Date formatting" target="_blank"><?php _e('Learn more about date formatting', SRP_TRANSLATION_ID); ?></a></small>
				</li>
				<!-- EOF Date Content option. -->
			</ul>
		</dd>
		<!-- EOF Advanced post options 2 -->

		<!-- BOF Filtering Options -->
		<dt class="srp-widget-optionlist-dt-filtering">
			<a class="srp-wdg-accordion-item" href="#5" title="<?php _e('Filtering Options', SRP_TRANSLATION_ID); ?>" name="5"><?php _e('Filtering Options', SRP_TRANSLATION_ID); ?></a>
		</dt>
		<dd class="srp-widget-optionlist-dd-filtering">
			<ul class="srp-widget-optionlist-filtering srp-widget-optionlist">
				
				<!-- BOF Include Categories Option. -->
				<li>
					<label for="<?php echo $this->get_field_id('category_include'); ?>" class="srp-widget-label"><?php _e('Include categories', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Enter a comma separated list of numeric categories IDs to include. Leave blank for no specific inclusion. <strong>ATTENTION:</strong> including specific categories will automatically exclude all the others.', SRP_TRANSLATION_ID); ?></small>
					<input type="text" id="<?php echo $this->get_field_id('category_include'); ?>" name="<?php echo $this->get_field_name('category_include'); ?>" value="<?php echo htmlspecialchars($instance["category_include"], ENT_QUOTES); ?>" class="fullwidth" />
				</li>
				<!-- EOF Include Categories Option. -->
				
				<!-- BOF Category Title option. -->
				<li>
					<input type="checkbox" id="<?php echo $this->get_field_id('category_title'); ?>" name="<?php echo $this->get_field_name('category_title'); ?>" value="yes" <?php checked($instance["category_title"], 'yes'); ?> />
					<label for="<?php echo $this->get_field_id('category_title'); ?>" class="srp-widget-label-inline"><?php _e('Use category title?', SRP_TRANSLATION_ID); ?></label><br />
					<small><?php _e('Check this box if you want to use the category name as widget title when a category filter is on.', SRP_TRANSLATION_ID); ?></small>
				</li>
				<!-- EOF Category Title option. -->
				
				<!-- BOF Include Posts Option. -->
				<li>
					<label for="<?php echo $this->get_field_id('post_include'); ?>" class="srp-widget-label"><?php _e('Include posts/pages IDs', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Enter a comma separated list of numeric posts/pages IDs to include. Leave blank for no specific inclusion. <strong>ATTENTION:</strong> including specific posts will automatically exclude all the others.', SRP_TRANSLATION_ID); ?></small>
					<input type="text" id="<?php echo $this->get_field_id('post_include'); ?>" name="<?php echo $this->get_field_name('post_include'); ?>" value="<?php echo htmlspecialchars($instance["post_include"], ENT_QUOTES); ?>" class="fullwidth" />
				</li>
				<!-- EOF Include Posts Option. -->
				
				<!-- BOF Exclude Posts Option. -->
				<li>
					<label for="<?php echo $this->get_field_id('post_exclude'); ?>" class="srp-widget-label"><?php _e('Exclude posts/pages IDs', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Enter a comma separated list of numeric posts/pages IDs to exclude. Leave blank for no exclusion.', SRP_TRANSLATION_ID); ?></small>
					<input type="text" id="<?php echo $this->get_field_id('post_exclude'); ?>" name="<?php echo $this->get_field_name('post_exclude'); ?>" value="<?php echo htmlspecialchars($instance["post_exclude"], ENT_QUOTES); ?>" class="fullwidth" />
				</li>
				<!-- EOF Exclude Posts Option. -->
				
				<!-- BOF Custom Post Types Option. -->
				<li>
					<label for="<?php echo $this->get_field_id('custom_post_type'); ?>" class="srp-widget-label"><?php _e('Filter posts by custom post type', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Type here the name of a custom post type you wish to filter posts by:'); ?></small>
					<input type="text" id="<?php echo $this->get_field_id('custom_post_type'); ?>" name="<?php echo $this->get_field_name('custom_post_type'); ?>" value="<?php echo stripslashes($instance['custom_post_type']); ?>" class="fullwidth" /><br />
					<small><?php _e('NOTICE: If you specify a custom post type, all previous posts options will be overrided.'); ?></small>
				</li>
				<!-- EOF Custom Post Types Option. -->

				<!-- BOF Post Status Mode. -->
				<li>
					<label for="<?php echo $this->get_field_id('post_status'); ?>" class="srp-widget-label"><?php _e('Post status', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Select how to filter displayed posts/pages based on their status:'); ?></small><br />
					<select id="<?php echo $this->get_field_id('post_status'); ?>" name="<?php echo $this->get_field_name('post_status'); ?>" class="srp-widget-select">
						<option value="publish" <?php selected($instance["post_status"], 'publish'); ?>><?php _e('Published (Default)', SRP_TRANSLATION_ID); ?></option>
						<option value="private" <?php selected($instance["post_status"], 'private'); ?>><?php _e('Private', SRP_TRANSLATION_ID); ?></option>
						<option value="inherit" <?php selected($instance["post_status"], 'inherit'); ?>><?php _e('Inherit', SRP_TRANSLATION_ID); ?></option>
						<option value="pending" <?php selected($instance["post_status"], 'pending'); ?>><?php _e('Pending', SRP_TRANSLATION_ID); ?></option>
						<option value="future" <?php selected($instance["post_status"], 'future'); ?>><?php _e('Future', SRP_TRANSLATION_ID); ?></option>
						<option value="draft" <?php selected($instance["post_status"], 'draft'); ?>><?php _e('Draft', SRP_TRANSLATION_ID); ?></option>
						<option value="trash" <?php selected($instance["post_status"], 'trash'); ?>><?php _e('Trash', SRP_TRANSLATION_ID); ?></option>
					</select>
				</li>
				<!-- EOF Post Status Mode. -->
			</ul>
		</dd>
		<!-- EOF Filtering Options -->

		<!-- BOF Layout options -->
		<dt class="srp-widget-optionlist-dt-layout">
			<a class="srp-wdg-accordion-item" href="#8" title="<?php _e('Layout Options', SRP_TRANSLATION_ID); ?>" name="8"><?php _e('Layout Options', SRP_TRANSLATION_ID); ?></a>
		</dt>
		<dd class="srp-widget-optionlist-dd-layout">
			<ul class="srp-widget-optionlist-layout srp-widget-optionlist">
				
				<!-- BOF Widget Title Header -->
				<li>
					<label for="<?php echo $this->get_field_id('widget_title_header'); ?>" class="srp-widget-label"><?php _e('Widget title header', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Select the type of HTML header to be used to enclose the widget title:'); ?></small><br />
					<select id="<?php echo $this->get_field_id('widget_title_header'); ?>" name="<?php echo $this->get_field_name('widget_title_header'); ?>" class="srp-widget-select">
						<option value="h1" <?php selected($instance["widget_title_header"], 'h1'); ?>><?php _e('H1', SRP_TRANSLATION_ID); ?></option>
						<option value="h2" <?php selected($instance["widget_title_header"], 'h2'); ?>><?php _e('H2', SRP_TRANSLATION_ID); ?></option>
						<option value="h3" <?php selected($instance["widget_title_header"], 'h3'); ?>><?php _e('H3 (Default)', SRP_TRANSLATION_ID); ?></option>
						<option value="h4" <?php selected($instance["widget_title_header"], 'h4'); ?>><?php _e('H4', SRP_TRANSLATION_ID); ?></option>
						<option value="h5" <?php selected($instance["widget_title_header"], 'h5'); ?>><?php _e('H5', SRP_TRANSLATION_ID); ?></option>
						<option value="h6" <?php selected($instance["widget_title_header"], 'h6'); ?>><?php _e('H6', SRP_TRANSLATION_ID); ?></option>
					</select>
				</li>
				<!-- EOF Widget Title Header -->

				<!-- BOF Widget Title Header Classes -->
				<li>
					<label for="<?php echo $this->get_field_id('widget_title_header_classes'); ?>" class="srp-widget-label"><?php _e('Type additional widget title header classes.', SRP_TRANSLATION_ID); ?></label>
					<small><?php _e('Enter a space separated list of additional css classes for this widget title header:'); ?></small>
					<input type="text" id="<?php echo $this->get_field_id('widget_title_header_classes'); ?>" name="<?php echo $this->get_field_name('widget_title_header_classes'); ?>" value="<?php echo stripslashes($instance['widget_title_header_classes']); ?>" class="fullwidth" /><br />
					<small><?php _e('Example: class1 class2 class3 ...'); ?></small>
				</li>
				<!-- EOF Widget Title Header Classes -->

				<!-- BOF Widget Title Hide Option. -->
				<li>
					<input type="checkbox" id="<?php echo $this->get_field_id('widget_title_hide'); ?>" name="<?php echo $this->get_field_name('widget_title_hide'); ?>" value="yes" <?php checked($instance["widget_title_hide"], 'yes'); ?> />
					<label for="<?php echo $this->get_field_id('widget_title_hide'); ?>" class="srp-widget-label-inline"><?php _e('Hide widget title', SRP_TRANSLATION_ID); ?></label><br />
					<small><?php _e('Check this box if you want to hide the widget title.',SRP_TRANSLATION_ID); ?></small>
				</li>
				<!-- EOF Widget Title Hide Option. -->
			</ul>
		</dd>
		<!-- EOF Layout options -->
		
		<!-- BOF Credits options -->
		<dt class="srp-widget-optionlist-dt-credits">
			<a class="srp-wdg-accordion-item" href="#10" title="<?php _e('Credits', SRP_TRANSLATION_ID); ?>" name="10"><?php echo _e('Credits', SRP_TRANSLATION_ID); ?></a>
		</dt>
		<dd class="srp-widget-optionlist-dd-credits">
			<ul class="srp-widget-optionlist-credits srp-widget-optionlist">
				
				<!-- BOF Credits text. -->
				<li>
					<?php _e('<p>The <strong>Special Recent Posts FREE Edition</strong> plugin is created, developed and supported by <a href="http://www.lucagrandicelli.com" title="Luca Grandicelli Website" target="_blank">Luca Grandicelli</a></p>', SRP_TRANSLATION_ID); ?>
						<strong><?php _e('SRP Version: ' . SRP_PLUGIN_VERSION . '</strong>', SRP_TRANSLATION_ID); ?>
				</li>
				<li>
					<p>
						<strong>Plugin Homepage</strong><br />
						<a href="http://www.specialrecentposts.com" target="_blank">specialrecentposts.com</a>
					</p>
					<p>
						<strong>SRP Help Desk</strong><br />
						<a href="http://www.specialrecentposts.com/support/" target="_blank">specialrecentposts.com/support</a>
					</p>
					<p>
						<strong>SRP on Twitter</strong><br />
						<a href="http://twitter.com/srpplugin" target="_blank">@srpplugin</a>
					</p>
					<p>
						<strong>SRP on Facebook</strong><br />
						<a href="http://www.facebook.com/SpecialRecentPosts" target="_blank">Special Recent Posts</a>
					</p>
						
				</li>
				<!-- EOF Credits text. -->
			</ul>
		</dd>
		<!-- EOF Credits options. -->
	</dl>
	<!-- EOF Widget Accordion -->
<?php
	}
}