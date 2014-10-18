<?php
/*
| ----------------------------------------------------------
| File        : class-main.php
| Project     : Special Recent Posts FREE Edition plugin for Wordpress
| Version     : 1.9.9
| Description : This is the main plugin class which handles
|               the core of the Special Recent Post PRO plugin.
| Author      : Luca Grandicelli
| Author URL  : http://www.lucagrandicelli.com
| Plugin URL  : http://www.specialrecentposts.com
| Copyright (C) 2011-2012  Luca Grandicelli
| ----------------------------------------------------------
*/

class SpecialRecentPostsFree {

/*
| ---------------------------------------------
| CLASS PROPERTIES
| ---------------------------------------------
*/

	// Declaring default plugin options array.
	private $plugin_args;
	
	// Declaring widget instance options array.
	private $widget_args;
	
	// Declaring Single Post ID (the current one displayed when in single post view).
	private $singleID;
	
	// Defining Cache Folder Base Path.
	private $cache_basepath;
	
	// Defining upload dir for multi-site hack.
	private $uploads_dir;
	
	// Defining standard available wp image sizes.
	private $wp_thumb_sizes;
	
	// Defining current widget instance id.
	private $widget_id;

	// Defining global post thumbnail html outputs.
	private $postThumbsOutput = array();

/*
| ---------------------------------------------
| CLASS CONSTRUCTOR & DECONSTRUCTOR
| ---------------------------------------------
*/
	// Class Constructor.
	// In this section we define the plugin global admin values and assign the selected widget values.
	public function __construct($args = array(), $widget_id = NULL) {

		// Setting up uploads dir for multi-site hack.
		$this->uploads_dir = wp_upload_dir();
		
		// Including global default widget values.
		global $srp_default_widget_values;
		
		// Setting up plugin options to be available throughout the plugin.
		$this->plugin_args = get_option('srp_plugin_options');
		
		// Setting up standard available wordpress sizes.
		$this->wp_thumb_sizes = array ('thumbnail', 'medium', 'large', 'full');
		
		// Double check if $args is an array.
		$args = (!is_array($args)) ? array() : SpecialRecentPostsFree::srp_version_map_check($args);
		
		// Setting up widget options to be available throughout the plugin.
		$this->widget_args = array_merge($srp_default_widget_values, $args);
		
		// Setting up post/page ID when on a single post/page.
		if (is_single() || is_page()) {
		
			// Including global $post object.
			global $post;
			
			// Assigning post ID.
			$this->singleID = $post->ID;
		}
		
		// Setting up Cache Folder Base Path.
		$this->cache_basepath = SRP_CACHE_DIR;
		
		// Setting up current widget instance id.
		$this->widget_id = ($widget_id) ? $widget_id : "";
	}
	
	// Class Deconstructor.
	public function __deconstruct() {}

/*
| ---------------------------------------------
| STATIC METHODS
| ---------------------------------------------
*/

	// This method handles all the actions for the plugin initialization.
	static function install_plugin() {
		
		// Loading text domain for translations.
		load_plugin_textdomain(SRP_TRANSLATION_ID, false, dirname(plugin_basename(__FILE__)) . SRP_LANG_FOLDER);
		
		// Doing a global database options check.
		SpecialRecentPostsFree::srp_dboptions_check();
	}
	
	// This method handles all the actions for the plugin uninstall process.
	static function uninstall_plugin() {
		
		// Deleting main WP Option.
		delete_option('srp_plugin_options');
	}
	
	/*
	| ---------------------------------------------
	| This method handles the visualization filter.
	| It returns true if the widget is allowed to be displayed
	| on the current page/post.
	| ---------------------------------------------
	*/
	static function visualizationCheck($instance, $call) {
		
		// Declaring global plugin values.
		global $srp_default_widget_values;
		
		// Checking source call.
		switch ($call) {
			
			case "phpcall":
			case "shortcode":
				$new_instance = array_merge($srp_default_widget_values, $instance);
			break;
			
			case "widget":
				$new_instance = $instance;
			break;
		}
		
		// Return True.
		return true;
	}
	
	/*
	| -------------------------------------------------------------------------
	| This method does a version check of old database options,
	| updating and passign existing values to new ones.
	| -------------------------------------------------------------------------
	*/
	static function srp_dboptions_check() {
		
		// Importing global default options array.
		global $srp_default_plugin_values;
		
		// Retrieving current db options.
		$srp_old_plugin_options = get_option('srp_plugin_options');
		
		// Checking if plugin db options exist and performing version comparison.
		if (isset($srp_old_plugin_options)) {

			if (version_compare($srp_old_plugin_options["srp_version"], SRP_PLUGIN_VERSION, '<')) {
			
				// Looping through available list of plugin values.
				foreach($srp_default_plugin_values as $k => $v) {
				
					// Checking for plugin options that haven't changed name since last version. In this case, assign the old value to the current new key.
					if ((isset($srp_old_plugin_options[$k])) && ($k != "srp_version")) $srp_default_plugin_values[$k] = $srp_old_plugin_options[$k];
				}
				
				// Deleting the old entry in the DB.
				delete_option('srp_plugin_options');
				
				// Re-creating a new entry in the database with the new values.
				add_option('srp_plugin_options', $srp_default_plugin_values);
			}
			
		} else {
		
			// First install. Creating WP Option with default values.
			add_option('srp_plugin_options', $srp_default_plugin_values);
		}
	}
	
	/*
	| -------------------------------------------------------------------------
	| This method does a version map check for old option arrays,
    | assigning old values to new ones.
	| -------------------------------------------------------------------------
	*/
	static function srp_version_map_check($oldargs) {
		
		// Including global version map super array.
		global $srp_version_map;
		
		if ( (is_array($oldargs)) && (!empty($oldargs))) {
		
			// Mapping eventual old parameters versions.
			foreach($oldargs as $oldargs_key => $oldargs_value) {
				
				// Checking if old parameter exists in the version map array, and if its name is different than the relative new one.
				if ( (array_key_exists($oldargs_key, $srp_version_map)) && ($oldargs_key != $srp_version_map[$oldargs_key]) ) {
					
					// Creating a new parameter key with the old parameter value, to respect options names.
					$oldargs[$srp_version_map[$oldargs_key]] = $oldargs_value;
					
					// Deleting old parameter key.
					unset($oldargs[$oldargs_key]);
				}
			}
			
		} else {
			
			// If $oldargs is not an array or it's empty, redefine it as a new empty array.
			$oldargs = array();
		}
		
		// Returning updated $args.
		return $oldargs;
	}

/*
| ---------------------------------------------
| CLASS MAIN METHODS
| ---------------------------------------------
*/
	/*
	| -----------------------------------------------------------------------
	| This is the main method for image manipulation. Every fetched image is
    | stored in the cache folder then displayed on screen.
    | Here lies the core of PHP Thumbnailer Class which takes care of all
    | image resizements and manipulations.
	| -----------------------------------------------------------------------
	*/
	private function generateGdImage($post, $image_origin, $image_to_render, $cached_image, $image_width, $image_height, $image_rotation) {

		// Adjust image path by clipping eventual (back)slashes.
		//if (($image_path[0] == "/") || ($image_path[0] == "\\")) $image_path = substr($image_path, 1);

		// Sometimes empty values can be posted to this funcion due to bad database arrays. In any case, exit this function returning false.
		if (!$image_to_render) return false;
		
		// Checking if we're processing a featured image or a first-post image.
		if ($image_origin == "firstimage") {
			
			// Building image path.
			$image_path = $_SERVER["DOCUMENT_ROOT"] . $image_to_render;

			// Building image path.
			$image_path = (is_multisite() && isset($blog_id) && $blog_id > 0) ? getcwd() . $image_to_render : $_SERVER["DOCUMENT_ROOT"] . $image_to_render;
			
		} else {
		
			// Featured image path doesn't need to be processed because it's already a physical path.
			$image_path = $image_to_render;
		}
		
		// Checking if original image exists and can be properly read. If is not, throw an error.
		if ( (!is_file($image_path)) || (!file_exists($image_path))) {
		
			// Checking if "Log Errors on Screen" option is on.
			if ($this->plugin_args["srp_log_errors_screen"] == "yes") {
			
				// Displaying informations about the original file where the error has been found.
				echo __("Problem detected on post ID: $post->ID on file: ", SRP_TRANSLATION_ID) . $image_path . "<br />";
			}
			
			// Return false.
			return false;
		}
		
		/*
		| ---------------------------------------------
		| IMAGE PROCESS
		| ---------------------------------------------
		*/
		
		// Put the whole image process in a Try&Catch block.
		try {

			// Initializing PHP Thumb Class.
			$thumb = PhpThumbFactory::create($image_path);
		
			// Resizing thumbnail with adaptive mode.
			$thumb->adaptiveResize($image_width, $image_height);

			// Checking for rotation value.
			if (isset($image_rotation)) {

				// Checking for display mode.
				switch($image_rotation) {
					
					// No rotation. Do nothing.
					case "no":
					break;
					
					// Rotating CW.
					case "rotate-cw":
						
						// rotating image CW.
						$thumb->rotateImage('CW');
					break;
					
					// Rotating CCW.
					case "rotate-ccw":
					
						// rotating image CCW.
						$thumb->rotateImage('CCW');
					break;
				}
			}

			// Saving generated image in the cache folder.
			$thumb->save($cached_image);
			
			// Checking if thumbnail has been properly saved.
			return (file_exists($cached_image)) ? TRUE : FALSE;
			
		} catch (Exception $e) {

			// Handling catched errors.
			echo $e->getMessage() . "<br />" . __("Problem detected on file: ", SRP_TRANSLATION_ID) . $image_path . "<br />";
			
			// Return false.
			return false;
		}
	}
	
	/*
	| -----------------------------------------------------------------------
	| This is the main method to display the default "no-image" thumbnail.
	| -----------------------------------------------------------------------
	*/
	private function displayDefaultThumb($thumb_width, $thumb_height) {
		
		// Checking if a custom thumbnail url has been provided.
		$noimage_url = ($this->plugin_args['srp_noimage_url'] != '') ? $this->plugin_args['srp_noimage_url'] : SRP_PLUGIN_URL . SRP_DEFAULT_THUMB;

		// Returning default thumbnail image.
		return '<img src="' . $noimage_url . '" class="srp-widget-thmb" width="' . $thumb_width . '" height="' . $thumb_height . '" alt="' . __('No thumbnail available') . '" />';
	}

	/*
	| ---------------------------------------------------------------------------------
	| This is the main method which retrieves the first image url in the post content.
	| ---------------------------------------------------------------------------------
	*/
	private function getFirstImageUrl($post, $thumb_width, $thumb_height, $post_title) {
		
		// Using REGEX to find the first occurrence of an image tag in the post content.
		$output = preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $post->post_content, $matches);
		
		if (!empty($output)) {

			// Image has been found. Analyize and extract the image src url.
			$first_img = $matches[1][0];
			
		} else {

			// No images found in the post content. Display default 'no-image' thumbnail image.
			return ($this->displayDefaultThumb($this->widget_args["thumbnail_width"], $this->widget_args["thumbnail_height"]));
		}
		
		// Parsing image URL.
		$parts = parse_url($first_img);
		
		// Getting the image basename pathinfo.
		$first_img_obj = pathinfo(basename($first_img));
		
		// Building the associated cached image URL.
		$imgabs_cache = $this->cache_basepath . base64_encode(urlencode($this->widget_args["thumbnail_width"] . $this->widget_args["thumbnail_height"] . $this->widget_args["thumbnail_rotation"] . $first_img_obj["filename"])) . "." . $first_img_obj["extension"];
		
		// Building image path.
		$image_to_render = $parts["path"];

		// Checking if this is a multisite blog, then adjust image paths.
		if(is_multisite()) {

			// Retrieving global multi site info.
			global $current_blog, $blog_id;

			// Is this is a network's blog.
			if (isset($blog_id) && $blog_id > 0) {
				$imageParts = explode('/files/', $image_to_render);
				if (isset($imageParts[1])) {

					// Fetching multisite image path.
					$image_to_render = '/wp-content/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
				}
			}
		}
		
		// Checking if the thumbnail already exists. In this case, simply render it. Otherwise generate it.
		if ( (file_exists(SRP_PLUGIN_DIR . $imgabs_cache)) || ($this->generateGdImage($post, 'firstimage', $image_to_render, SRP_PLUGIN_DIR . $imgabs_cache, $thumb_width, $thumb_height, $this->widget_args["thumbnail_rotation"])) ) {
			
			// Building thumbnail image tag.
			return '<img src="' . SRP_PLUGIN_URL . $imgabs_cache . '" class="srp-widget-thmb" width="' . $this->widget_args["thumbnail_width"] . '" height="' . $this->widget_args["thumbnail_height"] . '" alt="' . $post_title . '" />';
		
		} else {
		
			// If some errors are generated from the thumbnail generation process, display the default no-image placeholder.
			return ($this->displayDefaultThumb($this->widget_args["thumbnail_width"], $this->widget_args["thumbnail_height"]));
		}
	}

	/*
	| -----------------------------------------------------------------------
	| This is the main method to fetch the post thumbnail.
	| -----------------------------------------------------------------------
	*/
	private function displayThumb($post) {
		
		// Checking if featured thumbnails setting is active, if the current post has one and if it exists as file.
		if (function_exists('has_post_thumbnail') && has_post_thumbnail($post->ID)) {
			
			// Fetching Thumbnail ID.
			$thumbnail_id = get_post_thumbnail_id($post->ID);
			
			// Checking if current featured thumbnail comes from the NExtGen Plugin.
			if(stripos($thumbnail_id,'ngg-') !== false && class_exists('nggdb')){
			
				try {
				
					// Creating New NextGen Class instance.
					$nggdb = new nggdb();
					
					// Fetching NGG thumbnail object.
					$nggImage = $nggdb->find_image(str_replace('ngg-','',$thumbnail_id));
					
					// Retrieving physical path of NGG thumbnail image.
					$featured_physical_path = $nggImage->imagePath;
					
					// Fetching NGG thumbnail image URL.
					$featured_thumb_url = $nggImage->imageURL;
				
				} catch (Exception $e) {
				
				}

			}else{
				// Retrieving featured image attachment src.
				$featured_thumb_attachment = wp_get_attachment_image_src($thumbnail_id, 'large');
				
				// Retrieving physical path of featured image.
				$featured_physical_path = get_attached_file($thumbnail_id);

				// Retrieving featured image url.
				$featured_thumb_url = $featured_thumb_attachment[0];
			}

			// Parsing featured image url.
			$featured_thumb_url_obj = parse_url($featured_thumb_url);
			
			// Retrieving featured image basename.
			$featured_thumb_basename = pathinfo(basename($featured_thumb_url));			
			
			// Building featured image cached path.
			$featured_thumb_cache = $this->cache_basepath . base64_encode(urlencode($this->widget_args["thumbnail_width"] . $this->widget_args["thumbnail_height"] . $this->widget_args["thumbnail_rotation"] . $featured_thumb_basename["filename"])) . "." . $featured_thumb_basename["extension"];
			
			// Checking if the thumbnail already exists. In this case, simply render it. Otherwise generate it.
			if ( (file_exists(SRP_PLUGIN_DIR . $featured_thumb_cache)) || ($this->generateGdImage($post, 'featured', $featured_physical_path, SRP_PLUGIN_DIR . $featured_thumb_cache, $this->widget_args["thumbnail_width"], $this->widget_args["thumbnail_height"], $this->widget_args["thumbnail_rotation"]))) {
			
				// Return cached image as source (URL path).
				$featured_thumb_src = SRP_PLUGIN_URL . $featured_thumb_cache;
				
				// Generating Image HTML Tag.
				$featured_htmltag = '<img src="' . $featured_thumb_src . '" class="srp-widget-thmb" width="' . $this->widget_args["thumbnail_width"] . '" height="' . $this->widget_args["thumbnail_height"] . '" alt="' . $post->post_title . '" />';
			
			} else {
			
				// No featured image has been found. Trying to fetch the first image tag from the post content.
				$featured_htmltag = $this->getFirstImageUrl($post, $this->widget_args["thumbnail_width"], $this->widget_args["thumbnail_height"], $post->post_title);
			}

			// Checking if thumbnail should be linked to post.
			if ('yes' == $this->widget_args['thumbnail_link']) {
			
				// Building featured image link tag.
				$featured_temp_content  = $this->srp_create_tag('a', $featured_htmltag, array('class' => 'srp-widget-thmblink', 'href' => get_permalink($post->ID), 'title' => $post->post_title));
			
			} else {
			
				// Displaying post thumbnail without link.
				$featured_temp_content = $featured_htmltag;
			}
			
		} else {
			
			// No featured image has been found. Trying to fetch the first image tag from the post content.
			$featured_htmltag = $this->getFirstImageUrl($post, $this->widget_args["thumbnail_width"], $this->widget_args["thumbnail_height"], $post->post_title);
			
			// Checking if returned image is real or it is a false value due to skip_noimage_posts option enabled.
			if ($featured_htmltag) {
			
				// Checking if thumbnail should be linked to post.
				if ('yes' == $this->widget_args['thumbnail_link']) {
				
					// Building image tag.
					$featured_temp_content = $this->srp_create_tag('a', $featured_htmltag, array('class' => 'srp-widget-thmblink', 'href' => get_permalink($post->ID), 'title' => $post->post_title));
					
				} else {
				
					// Displaying post thumbnail without link.
					$featured_temp_content = $featured_htmltag;
				}
			} else {
			
				// Return false.
				return false;
			}
		}
		
		// Return all the image process.
		return $featured_temp_content;
	}
	
	/*
	| ----------------------------------------------------------------
	| This is the main method to extract and elaborate post excerpt.
	| ----------------------------------------------------------------
	*/
	private function extractContent($post, $content_type) {
		
		// Loading default plugin values.
		$content_length        = $this->widget_args['post_content_length'];
		$content_length_mode   = $this->widget_args['post_content_length_mode'];
		
		// Checking for post content "cut mode".
		switch($content_length_mode) {
		
			case 'words':
				
				// Switching through content type.
				switch($content_type) {
				
					case "content":
						// Sanitizing post content.
						$sanitized_string = $this->srp_sanitize($post->post_content);
					break;
					
					case "excerpt":
						// Sanitizing excerpt.
						$sanitized_string = $this->srp_sanitize($post->post_excerpt);
					break;
				}
				
				// Making a tag clean copy of the excerpt to calculate the total num of characters from words.
				$stripped_string = strip_tags($sanitized_string);
				
				// In order to cut by words without truncating html tags, we need to first calculate the approximate num of characters equal to the number of specified words limit .
				// This is done by the method substrWords() with the $mode parameter set to "count". Instead of returning the cutted string, it will return the num of characters that will be passed to the truncate_text() method as character limit. 
				return $this->srp_truncate_text($sanitized_string, $this->substrWords($stripped_string, $content_length, "count"), '', true);
				
			break;
			
			case 'chars':
				
				// Switching through content type.
				switch($content_type) {
					
					case "content":
						// Retrieving text from post content using 'characters cut'.
						//return mb_substr($this->srp_sanitize($post->post_content), 0, $content_length, 'UTF-8');
						return $this->srp_truncate_text($this->srp_sanitize($post->post_content), $content_length);
					break;
					
					case "excerpt":
						// Return normal excerpt using 'characters cut'.
						return $this->srp_truncate_text($this->srp_sanitize($post->post_excerpt), $content_length);
					break;
				}
				
			break;
			
			case 'fullcontent':
			
				// Switching through content type.
				switch($content_type) {
					
					case "content":
						// Retrieving text from post content using 'characters cut'.
						return $this->srp_sanitize($post->post_content);
					break;
					
					case "excerpt":
						// Return normal excerpt using 'characters cut'.
						return $this->srp_sanitize($post->post_excerpt);
					break;
				}
				
			break;
		}
	}
	
	/*
	| --------------------------------------------------------------
	| This is the main method to extract and elaborate post title.
	| --------------------------------------------------------------
	*/
	private function extractTitle($post) {
		
		// Loading default plugin values.
		$title_length        = $this->widget_args['post_title_length'];
		$title_length_mode   = $this->widget_args['post_title_length_mode'];
		$output_title        = "";
		
		// Checking for "cut mode".
		switch($title_length_mode) {
		
			case 'words':
			
				// Return normal title using 'words cut'.
				$output_title = $this->substrWords($this->srp_sanitize($post->post_title), $title_length);
			break;
			
			case 'chars':
			
				// Return normal title using 'characters cut'.
				$output_title = mb_substr($this->srp_sanitize($post->post_title), 0, $title_length, 'UTF-8');
			break;
			
			case 'fulltitle':
			
				// Return normal title using 'characters cut'.
				return $this->srp_sanitize($post->post_title);
			break;
		}
		
		// Returning title.
		return $output_title;
	}

	/*
	| -------------------------------------------------------------------------
	| This is the main method to retrieve posts.
	| -------------------------------------------------------------------------
	*/
	private function getPosts() {
	
		// Defining args array.
		$args = array (
			'post_type'   => $this->widget_args["post_type"],
			'numberposts' => ($this->widget_args["post_limit"] * $this->plugin_args["srp_global_post_limit"]),
			'post_status' => $this->widget_args["post_status"]
		);
		
		// Checking for Compatibility Mode.
		if ($this->plugin_args["srp_compatibility_mode"] == 'yes') {
			
			// Compatibility mode filter. This might cause unknown problems. Deactivate it just in case.
			$args["suppress_filters"] = false;
		}
		
		// Checking for post order option.
		switch ($this->widget_args["post_order"]) {
			
			case "ASC":
			case "DESC":
				
				// Ordering posts by ASC/DESC order
				$args["order"] = $this->widget_args["post_order"];
			break;
			
			default:
			
				// Default behaviour: ordering by DESC.
				$args["order"] = "DESC";
			break;
		}
		
		// Checking for custom post type option.
		if ($this->widget_args["custom_post_type"] != '') {
			
			// Filtering result posts by category ID.
			$args["post_type"] = $this->widget_args["custom_post_type"];
		}

		// Checking if category filter is applied.
		if ($this->widget_args["category_include"] != '') {

			// Filtering result posts by category ID.
			$args["category"] = $this->widget_args["category_include"];

		}
		
		// Checking if "post current hide" option is enabled.
		if ($this->widget_args["post_current_hide"] == 'yes') {
		
			// Filtering current post from visualization.
			$args["exclude"] = $this->singleID;
		}
		
		// Check if post offset option is enabled.
		if ($this->widget_args["post_offset"] != 0) {
		
			// Applying post offset.
			$args["offset"] = $this->widget_args["post_offset"];
		}
		
		// Checking if exclude posts option is applied.
		if (!empty($this->widget_args["post_exclude"])) {
			
			// Excluding result posts by post IDs.
			$args["exclude"] = $this->widget_args["post_exclude"];
		}
		
		// Checking if include posts option is applied.
		if (!empty($this->widget_args["post_include"])) {
			
			// Including result posts by post IDs.
			$args["include"] = $this->widget_args["post_include"];
		}

		// Calling built-in Wordpress 'get_posts' function.
		$result_posts = get_posts($args);
		
		// Checking if result posts array is empty.
		if (empty($result_posts)) {
		
			// No recent posts available. Return empty array.
			return $result_posts;
		}
		
		// Looping through result posts for image check.
		// If "skip posts with no image" option is enabled, re-build entire array, matching the post limit.
		foreach($result_posts as $k => $v) {

			// Filling thumb list with post IDs as keys.
			$currentThumbHtml = $this->displayThumb($v);

			// If the current post has no images and the "skip posts with no image" option is enabled, remove the post from the list.
			if (!$currentThumbHtml) {

				unset($result_posts[$k]);

			} else {

				// Push the current post thumbnail HTML in the global thumbnail output list.
				$this->postThumbsOutput[$v->ID] = $currentThumbHtml;
			}
		}

		// Fixing issues that let included IDs override the max number of post displayed.
		$output_array = array_slice($result_posts, 0, $this->widget_args["post_limit"]);

		// Checking if random posts option is on.
		if ($this->widget_args["post_random"] == "yes") {
			
			// Shuffling the result array.
			shuffle($output_array);
		}

		// Return result array.
		return $output_array;
	}
	
	/*
	| -------------------------------------------------------------------------
	| This is the main method to display posts.
	| -------------------------------------------------------------------------
	*/
	public function displayPosts($widget_call = NULL, $return_mode) {
	
		// Declaring global $post variable.
		global $post;
		
		// Building special HTML comment with current SRP version.
		$srp_content  = "<!-- BOF Special Recent Posts FREE Edition ver" . SRP_PLUGIN_VERSION . " -->";
		
		// Checking for "widget title hide" option.
		if ('yes' != $this->widget_args["widget_title_hide"]) {
		
			// Checking if SRP is displaying a category filter result and if it should use the linked category title.
			if ($this->widget_args["category_title"] == "yes") {
				
				// Fetching Category ID.
				if ($this->widget_args["category_include"] != '') {

					$thisCategoryId = $this->widget_args["category_include"];

				} else if($this->widget_args["category_autofilter"] == 'yes') {

					// Fetching category link.
					$thisCategory = get_the_category();
					$thisCategoryId = $thisCategory[0]->cat_ID;
				}
				
				$srp_category_link = get_category_link($thisCategoryId);
				
				// Building category title HTML.
				$category_title_link = $this->srp_create_tag('a', get_cat_name($thisCategoryId), array('class' => 'srp-widget-title-link', 'href' => $srp_category_link, 'title' => get_cat_name($thisCategoryId)));
				
				// Preparing widget title classes.
				$categoryAdditionalClasses = array('class' => 'widget-title srp-widget-title');

				if ($this->widget_args['widget_title_header_classes'] != '') {
					$categoryAdditionalClasses['class'] .= ' ' . $this->widget_args['widget_title_header_classes'];
				}

				// Building widget title.
				$srp_content .= $this->srp_create_tag($this->widget_args['widget_title_header'], $category_title_link, $categoryAdditionalClasses);
				
			} else {
				
				// Preparing widget title classes.
				$widgetTitleAdditionalClasses = array('class' => 'widget-title srp-widget-title');
				
				// Handling additional widget title classes.
				if ($this->widget_args['widget_title_header_classes'] != '') {
					$widgetTitleAdditionalClasses['class'] .= ' ' . $this->widget_args['widget_title_header_classes'];
				}

				// Building normal widget title HTML.
				$srp_content .= $this->srp_create_tag($this->widget_args['widget_title_header'], $this->srp_sanitize($this->widget_args["widget_title"]), $widgetTitleAdditionalClasses);
			}
		}
		
		// Opening Widget Container.
		$srp_content .= "<div class=\"srp-widget-container\">";
		
		// Fetching recent posts.
		$recent_posts = $this->getPosts();
		
		// Checking if posts are available.
		if (empty($recent_posts)) {
		
			// No posts available. Displaying "no posts" message.
			$srp_content .= $this->srp_create_tag('p', $this->srp_sanitize($this->widget_args['noposts_text']));
			
		} else {
			
			// Defining global column counter.
			$post_colrow_counter = 0;
			
			// Defining global post counter.
			$post_global_counter = 0;
			
			// Recent posts are available. Cyclying through result posts.
			foreach($recent_posts as $post) {
				
				// Adding +1 to global post counter.
				$post_global_counter++;
				
				// Adding +1 to post column counter.
				$post_colrow_counter++;
			
				// Preparing access to all post data.
				setup_postdata($post);
				
				// Fetching post image.
				$post_thumb_content = $this->postThumbsOutput[$post->ID];
				
				// Checking if current post has at least an image. If not, and Post Noimage Skip option is enabled, skip it.
				if (!$post_thumb_content)
					continue;
				
				// Opening single post container.
				$srp_content .= "<div id=\"" . $this->widget_id . "-srp-singlepost-" . $post_global_counter . "\" class=\"srp-widget-singlepost\">";

				// Checking if thumbnail option is on.
				if ($this->widget_args["display_thumbnail"] == 'yes') {
					
					// Setting up post title HTML attributes
					$ptitle_heading_atts = array('class' => 'srp-post-title');

					// Building linked post title HTML
					$ptitlelink  =  $this->srp_create_tag('a', $this->extractTitle($post), array('class' => 'srp-post-title-link', 'href' => get_permalink($post->ID), 'title' => $post->post_title));
					$srp_content .= $this->srp_create_tag('h4', $ptitlelink, $ptitle_heading_atts);
					
					// Opening container for thumbnail image.
					$srp_content .= $this->srp_create_tag('div', $post_thumb_content, array('class' => 'srp-thumbnail-box'));
					
				}
				
				// Checking for "no content at all" option. In this case, leave the content-box empty.
				if ('thumbonly' != $this->widget_args['post_content_mode']) {
				
					// Opening container for Content Box.
					$srp_content .= "<div class=\"srp-content-box\">";
					
					// Checking if "post_date" option is on.
					if ('yes' == $this->widget_args["post_date"]) {
					
						// Switching betweeb date formats.
						$date_format_mode = get_the_time($this->widget_args['date_format']);
						
						// Building post date container.
						$srp_content .= $this->srp_create_tag('p', $date_format_mode, array('class' => 'srp-widget-date'));
					}
					
					// Checking for Post Content Option.
					if ('titleexcerpt' == $this->widget_args["post_content_mode"]) {
						
						// Building post excerpt container.
						$srp_content .= "<p class=\"srp-widget-excerpt\">";

						// Fetching post excerpt.
						$srp_content .= $this->extractContent($post, $this->widget_args["post_content_type"]);
						
						// Checking if "image string break" option is set.
						if ($this->widget_args['image_string_break'] != "") {
							
							// Building HTML image tag for the image string break.
							$image_string_break = '<img src="' . $this->srp_sanitize($this->widget_args['image_string_break']) . '" class="srp-widget-stringbreak-image" alt="' . $post->post_title . '" />';
							
							// Checking if "string break link" option is on.
							if ('yes' == $this->widget_args['string_break_link']) {
							
								// Building image string break link HTML tag.
								$srp_content .= $this->srp_create_tag('a', $image_string_break, array('class' => 'srp-widget-stringbreak-link-image', 'href' => get_permalink($post->ID), 'title' => $post->post_title));
							
							} else {
							
								// Fetching the image string break URL.
								$srp_content .= $image_string_break;
							}
						
						} elseif ($this->widget_args['string_break'] != "") {
						
							// Using a text stringbreak. Checking if string break should be linked to post.
							if ('yes' == $this->widget_args['string_break_link']) {
							
								// Building string break link HTML tag.					
								$srp_content .= $this->srp_create_tag('a', $this->srp_sanitize($this->widget_args['string_break']), array('class' => 'srp-widget-stringbreak-link', 'href' => get_permalink($post->ID), 'title' => $post->post_title));
								
							} else {
								
								// Building string break HTML without link.
								$srp_content .= $this->srp_create_tag('span', $this->srp_sanitize($this->widget_args['string_break']), array('class' => 'srp-widget-stringbreak'));
							}
						}
						
						// Closing post excerpt container.
						$srp_content .= "</p>";
					}

					// EOF Content Box.
					$srp_content .= "</div>";
					
					// Adding a clear property for eventual floating elements.
					$srp_content .= $this->srp_create_tag('div', null, array('style' => 'clear:both; height: 0px;'));
				}
				
				// Closing Single Post Container.
				$srp_content .= "</div>";

				// Here we stop the visualization process to the max number of posts provided in the widget option panel.
				if ($post_global_counter == $this->widget_args["post_limit"]) break;
				
			} // EOF foreach cycle.
			
			// Resetting $post data array.
			wp_reset_postdata();
			
		} // EOF Empty posts check.
		
		// Adding a clear property for eventual floating elements.
		$srp_content .= $this->srp_create_tag('div', null, array('style' => 'clear:both; height: 0px;'));
		
		// Closing Widget Container.
		$srp_content .= "</div>";
		
		// Closing Special Recent Post PRO Version comment.
		$srp_content .= "<!-- EOF Special Recent Posts FREE Edition ver" . SRP_PLUGIN_VERSION . " -->";
		
		// Switching through display return mode.
		switch($return_mode) {
		
			// Display HTML on screen.
			case"print":
				echo $srp_content;
			break;
			
			// Return HTML.
			case "return":
				return $srp_content;
			break;
		}
	}

/*
| -------------------------------------------------------------------------
| UTILITY METHODS
| In this section we collect several general utility methods.
| -------------------------------------------------------------------------
*/

	/*
	| -------------------------------------------------------------------------
	| This is the main method to build HTML tags.
	| -------------------------------------------------------------------------
	*/
	private function srp_create_tag($tagname, $tag_content = NULL, $tag_attrs = NULL) {
	
		// Defining DOM root.
		$tagdom = new DOMDocument('1.0');
		
		// Creating tag element.
		$tag = $tagdom->createElement($tagname, htmlentities($tag_content, ENT_QUOTES, "UTF-8"));
	
		// Checking if attributes array is empty.
		if (!empty($tag_attrs) && (isset($tag_attrs)) ) {
		
			// Looping through attributes.
			foreach ($tag_attrs as $att_name => $att_value) {
			
				// Setting attribute.
				$tag->setAttribute($att_name, $att_value);
			}
			
			// If the tag is a link (<a>), do the "nofollow_links" optio check. If it's enables, add the nofollow attribute.
			if ( ($tagname == "a") && ($this->widget_args["nofollow_links"] == 'yes') ) $tag->setAttribute('rel', 'nofollow');
		}
		
		// Appending created tag to DOM root.
		$tagdom->appendChild($tag);
		
		// Saving HTML.
		$taghtml = trim($tagdom->saveHTML());

		// Cleaning DOM Root.
		unset($tagdom);
		
		// Return the HTML tag.
		return htmlspecialchars_decode($taghtml);
	}

	/*
	| -------------------------------------------------------------------------
	| This the main method to sanitize strings output.
	| -------------------------------------------------------------------------
	*/
	private function srp_sanitize($string) {
		
		// We need to remove all the exceeding stuff. Removing shortcodes and slashes.
		$temp_output = trim(stripslashes(strip_shortcodes($string)));
		
		// Applying qTranslate Filter if this exists.
		if (function_exists('qtrans_useCurrentLanguageIfNotFoundShowAvailable')) {
			$temp_output = qtrans_useCurrentLanguageIfNotFoundShowAvailable($temp_output);
		}
		
		// If "allowed_tags" option is on, keep them separated from strip_tags.
		if (!empty($this->widget_args["allowed_tags"])) {
			
			// Handling the <br /> tag.
			$this->widget_args["allowed_tags"] = str_replace('<br />', '<br>', $this->widget_args["allowed_tags"]);
			
			// Stripping tags except the ones specified.
			return strip_tags($temp_output, htmlspecialchars_decode($this->widget_args["allowed_tags"]));
			
		} else {
		
			// Otherwise completely strip tags from text.
			return strip_tags($temp_output);
		}
	}
	
	/*
	| -------------------------------------------------------------------------
	| This method uses the same logic of PHP function 'substr',
	| but works with words instead of characters.
	| -------------------------------------------------------------------------
	*/
	private function substrWords($str, $n, $mode = 'return') {
		
		// Checking if max length is equal to original string length. In that case, return the string without making any 'cut'.
		if (str_word_count($str, 0) > $n) {

			// Uses PHP 'str_word_count' function to extract total words and put them into an array.
			$w = explode(" ", $str);
			
			// Let's cut the array using our max length variable ($n).
			array_splice($w, $n);
			
			// Switch mode.
			switch($mode) {
			
				case "return":
					// Re-converting array to string and return.
					return implode(" ", $w);
				breaK;
				
				case "count":
					// Return count.
					return strlen(utf8_decode(implode(" ", $w)));
				break;
			}
			
		} else {
			
			// Switch mode.
			switch($mode) {
				case "return":
					// Return string as it is, without making any 'cut'.
					return $str;
				breaK;
				
				case "count":
					// Return count.
					return strlen(utf8_decode($str));
				break;
			}
		}
	}
	
	/*
	| -------------------------------------------------------------------------
	| This method truncates a string preserving html tags integrity.
	| Only works on characters. (Credits: http://jsfromhell.com)
	| -------------------------------------------------------------------------
	*/
	
	private function srp_truncate_text($s, $l, $e = '') {
	
		// Defining Internal counter.
		$i = 0;
		
		// Dafining array for tags collecting.
		$tags = array();
		
		// Checking if source string is HTML.
		if (!empty($this->widget_args["allowed_tags"])) {
			
			// Regex to find tags.
			preg_match_all('/<[^>]+>([^<]*)/', $s, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
			
			// Looping inside the string.
			foreach ($m as $o) {
				
				// Check if chars limit is equal or superior the string length.
				if (($o[0][1] - $i) >= $l) {
					break;
				}
				
				// Trimming the string.
				$t = mb_substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 0, 1, 'utf-8');
				
				// Repairing HTML tags.
				if($t[0] != '/') {
					$tags[] = $t;
					
				} elseif (end($tags) == substr($t, 0, 1, 'utf-8')) {
					
					array_pop($tags);
				}
				
				$i += $o[1][1] - $o[0][1];
			}
		}
		
		// Return result string.
		return mb_substr($s, 0, $l = min(strlen($s),  $l + $i), 'utf-8') . (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '') . (strlen($s) > $l ? $e : '');
	}
} // EOF Class.