<?php
/**
 * SpecialRecentPostsFREE
 *
 * This is the main plugin class which handles the core of the Special Recent Post plugin.
 *
 * @author Luca Grandicelli <lgrandicelli@gmail.com>
 * @copyright (C) 2011-2014 Luca Grandicelli
 * @package special-recent-posts-free
 * @version 2.0.4
 * @access public
 */
class SpecialRecentPostsFree {

	/**
     * The default plugin presets
     * @var $plugin_args
     */
	private $plugin_args;
	
	/**
     * The widget instance values.
     * @var $widget_args
     */
	private $widget_args;

	/**
     * The current post ID when in single post mode.
     * @var $singleID
     */
	private $singleID;
	
	/**
     * The Cache folder basepath.
     * @var $cache_basepath
     */
	private $cache_basepath;
	
	/**
     * The upload dir for wp multi-site compatibility.
     * @var $uploads_dir
     */
	private $uploads_dir;
	
	/**
     * The current sidget instance ID.
     * @var $widget_id
     */
	private $widget_id;

	/**
	 * __construct()
	 *
	 * The main SRP Class constructor
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access public
	 * @global $srp_default_widget_values The global default widget presets.
	 * @global $post The global $post WP object.
	 * @param array $args The widget instance configuration values.
	 * @param string The current Widget ID.
	 * @return boolean true
	 */
	public function __construct( $args = array(), $widget_id = NULL ) {

		// Setting up uploads dir for multi-site hack.
		$this->uploads_dir = wp_upload_dir();
		
		// Including global default widget values.
		global $srp_default_widget_values;
		
		// Setting up plugin options to be available throughout the plugin.
		$this->plugin_args = get_option( 'srp_plugin_options' );
		
		// Double check if $args is an array.
		$args = ( !is_array( $args ) ) ? array() : SpecialRecentPostsFree::srp_version_map_check( $args );
		
		// Setting up widget options to be available throughout the plugin.
		$this->widget_args = array_merge( $srp_default_widget_values, $args );
		
		// Setting up post/page ID when on a single post/page.
		if ( is_single() || is_page() ) {
		
			// Including global $post object.
			global $post;
			
			// Assigning post ID.
			$this->singleID = $post->ID;
		}
		
		// Setting up Cache Folder Base Path.
		$this->cache_basepath = SRP_CACHE_DIR;
		
		// Setting up current widget instance id.
		$this->widget_id = ( $widget_id ) ? $widget_id : false;

		// Returning true.
		return true;
	}
	
	/**
	 * __deconstruct()
	 *
	 * The main SRP Class deconstructor
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access public
	 */
	public function __deconstruct() {}

	/**
	 * install_plugin()
	 *
	 * This method handles all the actions for the plugin initialization.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access public
	 */
	static function install_plugin() {
		
		// Doing a global database options check.
		SpecialRecentPostsFree::srp_dboptions_check();
	}
	
	/**
	 * uninstall_plugin()
	 *
	 * This method handles all the actions for the plugin uninstall process.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access public
	 */
	static function uninstall_plugin() {
		
		// Deleting SRP saved option values.
		delete_option( 'srp_plugin_options' );
	}

	/**
	 * visualizationCheck()
	 *
	 * This method handles the visualization filter.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @global $srp_default_widget_values the global default plugin presets.
	 * @access public
	 * @return It returns true if the widget is allowed to be displayed on the current page/post. Otherwise false.
	 */
	static function visualization_check( $instance, $call ) {
		
		// Declaring global plugin values.
		global $srp_default_widget_values;
		
		// Checking source call.
		switch ( $call ) {
			
			case "phpcall":
			case "shortcode":

				// Merging current widget user values with the default presets.
				$new_instance = array_merge( $srp_default_widget_values, $instance );

			break;
			
			case "widget":

				// Just coping the current widget options.
				$new_instance = $instance;

			break;
		}
		
		// Returning True.
		return true;
	}
	
	/**
	 * srp_dboptions_check()
	 *
	 * This method does a version check of old database options, updating and passign existing values to new ones.
	 * This function is needed for compatibility with previous versions, without overwriting the old user values.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @global $srp_default_widget_values the global default plugin presets.
	 * @access public
	 * @return boolean true.
	 */
	static function srp_dboptions_check() {
		
		// Importing global default options array.
		global $srp_default_plugin_values;
		
		// Retrieving current db options.
		$srp_old_plugin_options = get_option( 'srp_plugin_options' );
		
		// Checking if plugin db options exist.
		if ( isset( $srp_old_plugin_options ) ) {

			// Performing version comparison.
			if ( version_compare( $srp_old_plugin_options['srp_version'], SRP_PLUGIN_VERSION, '<' ) ) {
			
				// Looping through each available plugin value.
				foreach( $srp_default_plugin_values as $k => $v ) {
				
					// Checking for plugin options that haven't changed name since last version.
					if ( ( isset( $srp_old_plugin_options[ $k ] ) ) && ( 'srp_version' != $k ) ) {	

						// In this case, assign the old value to the current new key.
						$srp_default_plugin_values[ $k ] = $srp_old_plugin_options[ $k ];
					}
				}
				
				// Deleting the old entry in the DB.
				delete_option( 'srp_plugin_options' );
				
				// Re-creating a new entry in the database with the new values.
				add_option( 'srp_plugin_options', $srp_default_plugin_values );
			}
			
		} else {
		
			// First install. Creating WP Option with default values.
			add_option( 'srp_plugin_options', $srp_default_plugin_values );
		}

		// Returning true.
		return true;
	}
	
	/**
	 * srp_version_map_check()
	 *
	 * This method does a version map check for old option arrays, assigning old values to new ones.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @global $srp_version_map The global version map super array.
	 * @access public
	 * @return array $oldargs The updated plugin values.
	 */
	static function srp_version_map_check($oldargs) {
		
		// Including global version map super array.
		global $srp_version_map;
		
		// Checking that old plugin values array exists and is not empty.
		if ( ( is_array( $oldargs ) ) && ( !empty( $oldargs ) ) ) {
		
			// Mapping possible old parameters versions.
			foreach( $oldargs as $oldargs_key => $oldargs_value ) {
				
				// Checking if old parameter exists in the version map array, and if its name is different than the relative new one.
				if ( ( array_key_exists( $oldargs_key, $srp_version_map ) ) && ( $oldargs_key != $srp_version_map[ $oldargs_key ] ) ) {
					
					// Creating a new parameter key with the old parameter value, to respect options names.
					$oldargs[ $srp_version_map[ $oldargs_key ] ] = $oldargs_value;
					
					// Deleting old parameter key.
					unset( $oldargs[ $oldargs_key ] );
				}
			}
			
		} else {
			
			// If $oldargs is not an array or it's empty, redefine it as a new empty array.
			$oldargs = array();
		}
		
		// Returning updated $args.
		return $oldargs;
	}

	/**
	 * generate_gd_image()
	 *
	 * This is the main method for the image manipulation.
	 * Every fetched image is stored in the cache folder then displayed on screen.
	 * Here lies the core of PHP Thumbnailer Class which takes care of all image resizements and manipulations.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @param $post The global WP post object
	 * @param $image_origin The original image source.
	 * @param $image_to_render The final image to be rendered and saved.
	 * @param $cached_image The cached image name.
	 * @param $image_width The thumbnail image width.
	 * @param $image_height The thumbnail image height.
	 * @param $image_rotation The image rotation mode.
	 * @access private
	 * @return mixed It could return a file handler to the saved thumbnail, or false in case some error has shown up, or an exception.
	 */
	private function generate_gd_image($post, $image_origin, $image_to_render, $cached_image, $image_width, $image_height, $image_rotation) {

		// Adjust image path by clipping eventual (back)slashes.
		//if (($image_path[0] == "/") || ($image_path[0] == "\\")) $image_path = substr($image_path, 1);
		
		// Removing querystring from image to save. This fixed the Jetpack Photon Issue.
		$cached_image = preg_replace( '/\?.*/', '', $cached_image );

		// Sometimes empty values can be posted to this funcion due to bad database arrays. In any case, exit this function returning false.
		if ( !$image_to_render ) return false;
		
		// Checking if we're processing a featured image or a first-post image.
		if ( 'firstimage' == $image_origin ) {
			
			// Building image path.
			$image_path = $_SERVER['DOCUMENT_ROOT'] . $image_to_render;

			// Building image path.
			$image_path = ( is_multisite() && isset( $blog_id ) && $blog_id > 0 ) ? getcwd() . $image_to_render : $_SERVER['DOCUMENT_ROOT'] . $image_to_render;
			
		} else {
		
			// Featured image path doesn't need to be processed because it's already a physical path.
			$image_path = $image_to_render;
		}
		
		// Checking if original image exists and can be properly read. If is not, throw an error.
		if ( ( !is_file( $image_path ) ) || ( !file_exists( $image_path ) ) ) {
		
			// Checking if "Log Errors on Screen" option is on.
			if ( 'yes' == $this->plugin_args['srp_log_errors_screen'] ) {
			
				// Displaying informations about the original file where the error has been found.
				printf( __( 'Problem detected on post ID: %d on file %s', SRP_TRANSLATION_ID ) , $post->ID, $image_path );
				echo '<br />';
			}
			
			// Return false.
			return false;
		}
		
		// Putting the whole image process in a Try&Catch block.
		try {

			// Setting up Thumbnail Image Quality Ratio.
			$phpThumbArgs = array( 'jpegQuality' => $this->plugin_args['srp_thumbnail_jpeg_quality'] );

			// Initializing PHP Thumb Class.
			$thumb = PhpThumbFactory::create( $image_path, $phpThumbArgs );
		
			// Resizing thumbnail with adaptive mode.
			$thumb->adaptiveResize( $image_width, $image_height );

			// Checking for rotation value.
			if ( isset( $image_rotation ) ) {

				// Checking for display mode.
				switch( $image_rotation ) {
					
					// No rotation. Do nothing.
					case 'no':
					break;
					
					// Rotating CW.
					case 'rotate-cw':
						
						// Rotating image CW.
						$thumb->rotateImage( 'CW' );

					break;
					
					// Rotating CCW.
					case 'rotate-ccw':
					
						// Rotating image CCW.
						$thumb->rotateImage( 'CCW' );

					break;
				}
			}

			// Saving generated image in the cache folder.
			$thumb->save( $cached_image );
			
			// Checking if thumbnail has been properly saved.
			return ( file_exists( $cached_image ) ) ? true : false;
			
		} catch ( Exception $e ) {

			// Handling catched errors.
			echo $e->getMessage() . '<br />' . __( 'Problem detected on file:', SRP_TRANSLATION_ID ) . $image_path . '<br />';
			
			// Returning false.
			return false;
		}
	}
	
	/**
	 * display_default_thumb()
	 *
	 * This is the main method to display the default "no image" placeholder.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @param $thumb_width The thumbnail width.
	 * @param $thumb_height The thumbnail height.
	 * @access private
	 * @return string the 'no image' HTML tag.
	 */
	private function display_default_thumb( $thumb_width, $thumb_height ) {
		
		// Checking if a custom thumbnail url has been provided.
		$noimage_url = ( !empty( $this->plugin_args['srp_noimage_url'] ) ) ? $this->plugin_args['srp_noimage_url'] : SRP_DEFAULT_THUMB;

		// Returning default thumbnail image.
		return '<img src="' . $noimage_url . '" class="srp-post-thumbnail" width="' . $thumb_width . '" height="' . $thumb_height . '" alt="' . esc_attr( __( 'No thumbnail available', SRP_TRANSLATION_ID ) ) . '" />';
	}

	/**
	 * get_first_image_url()
	 *
	 * This method retrieves the first image url in the post content.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @param $post The global WP post object.
	 * @param $thumb_width The thumbnail width.
	 * @param $thumb_height The thumbnail height.
	 * @param $post_title The current post title.
	 * @access private
	 * @return mixed It could return the HTML code for the first image found, generate a new thumbnail and returning it, or generate the 'no image' placeholder.
	 */
	private function get_first_image_url( $post, $thumb_width, $thumb_height, $post_title ) {
		
		// Using REGEX to find the first occurrence of an image tag in the post content.
		$output = preg_match_all( '/<img [^>]*src=["|\']([^"|\']+)/i', $post->post_content, $matches );
		
		// Checking if REGEX has found something.
		if ( !empty( $output ) ) {

			// Image has been found. Analyize and extract the image src url.
			$first_img = $matches[1][0];
			
		} else {

			// No images found in the post content. Display default 'no-image' thumbnail image.
			return ( $this->display_default_thumb( $this->widget_args['thumbnail_width'], $this->widget_args['thumbnail_height'] ) );
		}
		
		// Parsing image URL.
		$parts = parse_url( $first_img );
		
		// Getting the image basename pathinfo.
		$first_img_obj = pathinfo( basename( $first_img ) );

		// Removing querystring from image to save. This fixed the Jetpack Photon Issue.
		$first_img_obj['extension'] = preg_replace( '/\?.*/', '', $first_img_obj['extension'] );
		
		// Building the associated cached image URL.
		$imageNameToSave = $this->cache_basepath . 'srpthumb-p' . $post->ID .  '-' . $this->widget_args['thumbnail_width'] . 'x' . $this->widget_args['thumbnail_height'] . '-' . $this->widget_args['thumbnail_rotation'] . '.' . $first_img_obj['extension'];

		// Building image path depending wheter this is a multi site WP or not.
		$image_to_render = $parts['path'];

		// Checking if this is a multisite blog, then adjust image paths.
		if ( is_multisite() ) {

			// Retrieving global multi site info.
			global $current_blog, $blog_id;

			// Is this is a network's blog.
			if ( isset( $blog_id ) && $blog_id > 0 ) {

				// Fetching image path parts.
				$imageParts = explode( '/files/', $image_to_render );

				// Checking if image exists.
				if ( isset( $imageParts[1] ) ) {

					// Fetching multisite image path.
					$image_to_render = '/wp-content/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
				}
			}
		}
		
		// Checking if the thumbnail already exists. In this case, simply render it. Otherwise generate it.
		if ( ( file_exists( SRP_PLUGIN_DIR . $imageNameToSave ) ) || ( $this->generate_gd_image( $post, 'firstimage', $image_to_render, SRP_PLUGIN_DIR . $imageNameToSave, $thumb_width, $thumb_height, $this->widget_args['thumbnail_rotation'] ) ) ) {
			
			// Building thumbnail image tag.
			return '<img src="' . SRP_PLUGIN_URL . $imageNameToSave . '" class="srp-post-thumbnail" alt="' . esc_attr( $post_title ) . '" />';
		
		} else {
		
			// If some errors are generated from the thumbnail generation process, display the default no-image placeholder.
			return ( $this->display_default_thumb( $this->widget_args['thumbnail_width'], $this->widget_args['thumbnail_height'] ) );
		}
	}

	/**
	 * display_thumb()
	 *
	 * This method displays the post thumbnail.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @param $post The global WP post object.
	 * @access private
	 * @return mixed It could return the HTML code for the post thumbnail or false in case of some error.
	 */
	private function display_thumb( $post ) {
		
		// Checking if featured thumbnails setting is active, if the current post has one and if it exists as file.
		if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post->ID ) ) {
			
			// Fetching Thumbnail ID.
			$thumbnail_id = get_post_thumbnail_id( $post->ID );
			
			// Checking if current featured thumbnail comes from the NExtGen Plugin.
			if( stripos( $thumbnail_id, 'ngg-' ) !== false && class_exists( 'nggdb' ) ) {
			
				try {
				
					// Creating New NextGen Class instance.
					$nggdb = new nggdb();
					
					// Fetching NGG thumbnail object.
					$nggImage = $nggdb->find_image( str_replace( 'ngg-', '', $thumbnail_id ) );
					
					// Retrieving physical path of NGG thumbnail image.
					$featured_physical_path = $nggImage->imagePath;
					
					// Fetching NGG thumbnail image URL.
					$featured_thumb_url = $nggImage->imageURL;
				
				} catch ( Exception $e ) {}

			} else {

				// Retrieving featured image attachment src.
				$featured_thumb_attachment = wp_get_attachment_image_src( $thumbnail_id, 'large' );
				
				// Retrieving physical path of featured image.
				$featured_physical_path = get_attached_file( $thumbnail_id );

				// Retrieving featured image url.
				$featured_thumb_url = $featured_thumb_attachment[0];
			}

			// Parsing featured image url.
			$featured_thumb_url_obj = parse_url( $featured_thumb_url );
			
			// Retrieving featured image basename.
			$featured_thumb_basename = pathinfo( basename( $featured_thumb_url ) );

			// Removing querystring from image to save. This fixed the Jetpack Photon Issue.
			$featured_thumb_basename['extension'] = preg_replace( '/\?.*/', '', $featured_thumb_basename['extension'] );
			
			// Building featured image cached path.
			$featured_thumb_cache = $this->cache_basepath . 'srpthumb-p' . $post->ID .  '-' . $this->widget_args['thumbnail_width'] . 'x' . $this->widget_args['thumbnail_height'] . '-' . $this->widget_args['thumbnail_rotation'] . '.' . $featured_thumb_basename['extension'];

			// Checking if the thumbnail already exists. In this case, simply render it. Otherwise generate it.
			if ( ( file_exists( SRP_PLUGIN_DIR . $featured_thumb_cache ) ) || ( $this->generate_gd_image( $post, 'featured', $featured_physical_path, SRP_PLUGIN_DIR . $featured_thumb_cache, $this->widget_args['thumbnail_width'], $this->widget_args['thumbnail_height'], $this->widget_args['thumbnail_rotation'] ) ) ) {
			
				// Return cached image as source (URL path).
				$featured_thumb_src = SRP_PLUGIN_URL . $featured_thumb_cache;
				
				// Generating Image HTML Tag.
				$featured_htmltag = '<img src="' . $featured_thumb_src . '" class="srp-post-thumbnail" alt="' . esc_attr( $post->post_title ) . '" />';

			} else {
			
				// No featured image has been found. Trying to fetch the first image tag from the post content.
				$featured_htmltag = $this->get_first_image_url( $post, $this->widget_args['thumbnail_width'], $this->widget_args['thumbnail_height'], $post->post_title );
			}

			// Checking if thumbnail should be linked to post.
			if ( 'yes' == $this->widget_args['thumbnail_link'] ) {
			
				// Building featured image link tag.
				$featured_temp_content  = $this->srp_create_tag( 'a', $featured_htmltag, array( 'class' => 'srp-post-thumbnail-link', 'href' => get_permalink( $post->ID ), 'title' => $post->post_title ) );
			
			} else {
			
				// Displaying post thumbnail without link.
				$featured_temp_content = $featured_htmltag;
			}
			
		} else {
			
			// No featured image has been found. Trying to fetch the first image tag from the post content.
			$featured_htmltag = $this->get_first_image_url( $post, $this->widget_args['thumbnail_width'], $this->widget_args['thumbnail_height'], $post->post_title );
			
			// Checking if returned image is real or it is a false value due to skip_noimage_posts option enabled.
			if ( $featured_htmltag ) {
			
				// Checking if thumbnail should be linked to post.
				if ( 'yes' == $this->widget_args['thumbnail_link'] ) {
				
					// Building image tag.
					$featured_temp_content = $this->srp_create_tag( 'a', $featured_htmltag, array( 'class' => 'srp-post-thumbnail-link', 'href' => get_permalink( $post->ID ), 'title' => $post->post_title ) );
					
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
	
	/**
	 * extract_content()
	 *
	 * This method extracts the post content.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @param $post The global WP post object.
	 * @param $content_type The type of post content to display.
	 * @param $post_global_counter The global post counter.
	 * @access private
	 * @return string The post content text.
	 */
	private function extract_content( $post, $content_type ) {
		
		// Loading default plugin values.
		$content_length      = $this->widget_args['post_content_length'];
		$content_length_mode = $this->widget_args['post_content_length_mode'];
		
		// Checking for post content "cut mode".
		switch( $content_length_mode ) {
		
			case 'words':
				
				// Switching through content type.
				switch( $content_type ) {
				
					case 'content':

						// Sanitizing post content.
						$sanitized_string = $this->srp_sanitize( $post->post_content );

					break;
					
					case 'excerpt':

						// Sanitizing excerpt.
						$sanitized_string = $this->srp_sanitize( $post->post_excerpt );

					break;
				}
				
				// Making a tag clean copy of the excerpt to calculate the total num of characters from words.
				$stripped_string = strip_tags( $sanitized_string );
				
				// In order to cut by words without truncating html tags, we need to first calculate the approximate num of characters equal to the number of specified words limit.
				// This is done by the method substrWords() with the $mode parameter set to "count". Instead of returning the cutted string, it will return the num of characters that will be passed to the truncate_text() method as character limit. 
				return $this->srp_truncate_text( $sanitized_string, $this->substrWords( $stripped_string, $content_length, 'count' ), '', true );
				
			break;
			
			case 'chars':
				
				// Switching through content type.
				switch( $content_type ) {
					
					case 'content':

						// Retrieving text from post content using 'characters cut'.
						return $this->srp_truncate_text( $this->srp_sanitize( $post->post_content ), $content_length );

					break;
					
					case 'excerpt':

						// Return normal excerpt using 'characters cut'.
						return $this->srp_truncate_text( $this->srp_sanitize( $post->post_excerpt ), $content_length );

					break;
				}
				
			break;
			
			case 'fullcontent':
			
				// Switching through content type.
				switch( $content_type ) {
					
					case 'content':

						// Retrieving text from post content using 'characters cut'.
						return $this->srp_sanitize( $post->post_content );

					break;
					
					case 'excerpt':

						// Return normal excerpt using 'characters cut'.
						return $this->srp_sanitize( $post->post_excerpt );

					break;
				}
				
			break;
		}
	}
	
	/**
	 * extract_title()
	 *
	 * This method extracts the post title.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @param $post The global WP post object.
	 * @access private
	 * @return string The post title text.
	 */
	private function extract_title( $post ) {
		
		// Loading default plugin values.
		$title_length      = $this->widget_args['post_title_length'];
		$title_length_mode = $this->widget_args['post_title_length_mode'];
		$output_title      = "";
		
		// Checking for 'cut' mode.
		switch($title_length_mode) {
		
			case 'words':
			
				// Return normal title using 'words cut'.
				$output_title = $this->substrWords( $this->srp_sanitize( $post->post_title ), $title_length );

			break;
			
			case 'chars':
			
				// Return normal title using 'characters cut'.
				$output_title = mb_substr( $this->srp_sanitize( $post->post_title ), 0, $title_length, 'UTF-8' );

			break;
			
			case 'fulltitle':
			
				// Return normal title using 'characters cut'.
				$output_title = $this->srp_sanitize( $post->post_title );
			break;
		}
		
		// Returning title.
		return $output_title;
	}

	/**
	 * generate_widget_title()
	 *
	 * This method generates the widget title.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access private
	 * @return string It returns the widget title.
	 */
	private function generate_widget_title() {

		// Preparing the widget title HTML.
		$widgetTitleHTML = '';

		// Checking for 'Use default Wordpress HTML layout for widget title' option value.
		if ( 'yes' == $this->widget_args['widget_title_show_default_wp']) return;

		// Checking for "widget title hide" option.
		if ( 'yes' != $this->widget_args['widget_title_hide'] ) {
		
			// Checking if SRP is displaying a category filter result and if it should use the linked category title.
			if ( 'yes' == $this->widget_args['category_title'] && !empty( $this->widget_args["category_include"] ) ) {
				
				// Assiging category ID.
				$thisCategoryId = $this->widget_args["category_include"];
				
				// Fetching category link.
				$srp_category_link = get_category_link( $thisCategoryId );
				
				// Building category title HTML.
				$category_title_link = $this->srp_create_tag( 'a', get_cat_name( $thisCategoryId ), array( 'class' => 'srp-widget-title-link', 'href' => esc_url( $srp_category_link ), 'title' => esc_attr( get_cat_name( $thisCategoryId) ) ) );
				
				// Preparing widget title classes.
				$categoryAdditionalClasses = array( 'class' => 'widget-title srp-widget-title' );

				// Checking for additional CSS widget header classes.
				if ( !empty( $this->widget_args['widget_title_header_classes'] ) ) {

					// Appending additional CSS widget header classes.
					$categoryAdditionalClasses['class'] .= ' ' . $this->widget_args['widget_title_header_classes'];
				}

				// Building widget title.
				$widgetTitleHTML .= $this->srp_create_tag( $this->widget_args['widget_title_header'], $category_title_link, $categoryAdditionalClasses );
				
			} else {
				
				// Preparing widget title classes.
				$widgetTitleAdditionalClasses = array( 'class' => 'widget-title srp-widget-title' );
				
				// Checking for additional CSS widget header classes.
				if ( !empty( $this->widget_args['widget_title_header_classes'] ) ) {

					// Appending additional CSS widget header classes.
					$widgetTitleAdditionalClasses['class'] .= ' ' . $this->widget_args['widget_title_header_classes'];
				}

				// Building normal widget title HTML.
				$widgetTitleHTML .= $this->srp_create_tag( $this->widget_args['widget_title_header'], esc_html( $this->widget_args['widget_title']), $widgetTitleAdditionalClasses );
			}
		}

		// Returning the widget title.
		return $widgetTitleHTML;
	}

	/**
	 * generate_post_title()
	 *
	 * This method generates the post title.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access private
	 * @param  object $post The current WP post object.
	 * @return string It returns the post title.
	 */
	private function generate_post_title( $post ) {

		// Setting up post title HTML attributes
		$ptitle_heading_atts = array( 'class' => 'srp-post-title' );

		// Setting up post title link HTML attributes
		$ptitle_link_atts = array(
			'class' => 'srp-post-title-link',
			'href'  => get_permalink( $post->ID ),
			'title' => esc_attr( $post->post_title )
		);

		// Building linked post title HTML
		$ptitlelink = $this->srp_create_tag( 'a', $this->extract_title( $post ), $ptitle_link_atts );

		// Returning the post title.
		return $this->srp_create_tag( $this->widget_args['post_title_header'], $ptitlelink, $ptitle_heading_atts );
	}

	/**
	 * generate_post_date()
	 *
	 * This method generates the post date.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access private
	 * @param  object $post The current WP post object.
	 * @return string It returns the post date.
	 */
	private function generate_post_date( $post ) {

		// Switching betweeb date formats.
		$date_format_mode = get_the_time( $this->widget_args['date_format'], $post );
		
		// Returning the post date.
		return $this->srp_create_tag( 'p', $date_format_mode, array( 'class' => 'srp-post-date' ) );
	}

	/**
	 * generate_post_excerpt()
	 *
	 * This method generates the post content text.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access private
	 * @param  object $post The current WP post object.
	 * @return string It returns the post content text.
	 */
	private function generate_post_excerpt( $post ) {

		// Setting up the post content text.
		$postExcerptHTML = '';

		// Building post excerpt container.
		$postExcerptHTML .= '<div class="srp-post-content">';

		// Fetching post excerpt.
		$postExcerptHTML .= $this->extract_content( $post, $this->widget_args['post_content_type'] );
		
		// Checking if "image string break" option is set.
		if ( !empty( $this->widget_args['image_string_break'] ) ) {
			
			// Building HTML image tag for the image string break.
			$image_string_break = '<img src="' . esc_url( $this->widget_args['image_string_break'] ) . '" class="srp-post-stringbreak-image" alt="' . esc_attr( $post->post_title ) . '" />';
			
			// Checking if "string break link" option is on.
			if ( 'yes' == $this->widget_args['string_break_link'] ) {
			
				// Building image string break link HTML tag.
				$postExcerptHTML .= $this->srp_create_tag( 'a', $image_string_break, array( 'class' => 'srp-post-stringbreak-link-image', 'href' => get_permalink( $post->ID ), 'title' => $post->post_title ) );
			
			} else {
			
				// Fetching the image string break URL.
				$postExcerptHTML .= $image_string_break;
			}
		
		} elseif ( !empty( $this->widget_args['string_break'] ) ) {
		
			// Using a text stringbreak. Checking if string break should be linked to post.
			if ( 'yes' == $this->widget_args['string_break_link'] ) {
			
				// Building string break link HTML tag.					
				$postExcerptHTML .= $this->srp_create_tag( 'a', esc_html( $this->widget_args['string_break'] ), array( 'class' => 'srp-post-stringbreak-link', 'href' => get_permalink( $post->ID ), 'title' => $post->post_title ) );
				
			} else {
				
				// Building string break HTML without link.
				$postExcerptHTML .= $this->srp_create_tag( 'span', esc_html( $this->widget_args['string_break'] ), array( 'class' => 'srp-post-stringbreak' ) );
			}
		}
		
		// Closing post excerpt container.
		$postExcerptHTML .= '</div>';

		// Returning the post content text.
		return $postExcerptHTML;
	}

	/**
	 * generate_posts()
	 *
	 * This method fetches all the WP posts based on the widget settings, using the WP_query method.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access private
	 * @return object It returns the WP_query object containing the fetched posts.
	 */
	private function generate_posts() {
		
		// Defining widget args array.
		$args = array();

		/**
		 * ********************************************************
		 * DEFAULT OPTIONS
		 * ********************************************************
		 */

		// Checking for 'Compatibility Mode' option.
		if ( 'yes' == $this->plugin_args["srp_compatibility_mode"] ) {
			
			// Compatibility mode filter. This might cause unknown problems. Deactivate it just in case.
			$args['suppress_filters'] = false;
		}

		// Ignore sticky posts
		$args['ignore_sticky_posts'] = 1;


		/**
		 * ********************************************************
		 * BASIC OPTIONS
		 * ********************************************************
		 */
		
		// Post Type
		$args['post_type'] = $this->widget_args["post_type"];

		// Checking for Post Type
		if ( 'page' == $args['post_type'] ) {

			// Post Parent
			$args['post_parent'] = 0;
		}

		// Post per Page
		$args['posts_per_page'] = ( 'yes' == $this->widget_args['show_all_posts'] ) ? -1 : $this->widget_args['post_limit'];

		// Checking for 'Show Sticky Posts' option.
		if ('yes' == $this->widget_args["show_sticky_posts"]) {
			
			// Ignore Sticky Posts.
			$args['ignore_sticky_posts'] = 0;
		}
		
		/**
		 * ********************************************************
		 * POST OPTIONS
		 * ********************************************************
		 */
		
		// Checking for 'Post Order' option.
		switch ( $this->widget_args['post_order'] ) {
			
			case "ASC":
			case "DESC":
				
				// Ordering posts by ASC/DESC order
				$args['order'] = $this->widget_args['post_order'];

			break;
			
			default:
			
				// Default behaviour: ordering by DESC.
				$args['order'] = 'DESC';

			break;
		}

		// Checking for 'Random Mode' option.
		if ( 'yes' == $this->widget_args['post_random'] ) {
			
			// Applying random order by.
			$args['orderby'] = 'rand';
		}
		
		/**
		 * ********************************************************
		 * ADVANCED POST OPTIONS 1
		 * ********************************************************
		 */
		
		// Checking for 'Hide Current Viewed Post' option.
		if ( ( 'yes' == $this->widget_args["post_current_hide"] ) && ( is_single() || is_page() ) ) {
		
			// Filtering current post from visualization.
			$args['post__not_in'] = array( $this->singleID );
		}

		// Checking for 'Post Offset' option.
		if ( 0 !== $this->widget_args['post_offset'] ) {
		
			// Applying Post Offset.
			$args['offset'] = $this->widget_args['post_offset'];
		}
		
		/**
		 * ********************************************************
		 * FILTERING OPTIONS
		 * ********************************************************
		 */
		
		// Checking for 'Category Filter' option.
		if ( !empty( $this->widget_args["category_include"] ) ) {

			// Category Filter
			$args['cat'] = $this->widget_args['category_include'];

		}

		// Checking for 'Posts/Page ID Filter' option.
		if ( !empty( $this->widget_args['post_include'] ) ) {
			
			// Including result posts by post IDs.
			$args['post__in'] = explode( ',', $this->widget_args['post_include'] );
		}

		// Checking for 'Exclude Posts/Pages By IDs' option.
		if ( !empty( $this->widget_args['post_exclude'] ) ) {
			
			// Excluding result posts by post IDs.
			$args['post__not_in'] = explode( ',', $this->widget_args['post_exclude'] );
		}

		// Checking for 'Custom Post Type' option.
		if ( !empty( $this->widget_args['custom_post_type'] ) ) {
			
			// Setting post type as custom post type.
			$args['post_type'] = explode( ',', $this->widget_args['custom_post_type'] );
		}

		// Post Status
		$args['post_status'] = $this->widget_args["post_status"];

		// WP_querying the database.
		$result_posts = new wp_query( $args );
		
		// Checking if the result posts array is empty.
		if ( !$result_posts->have_posts() ) {
		
			// No posts available. Return false.
			return false;
		}

		// Returning result array.
		return $result_posts;
	}
	
	/**
	 * display_posts()
	 *
	 * This method generates the SRP layout HTML.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access private
	 * @param  string $widget_call This variable determines how the SRP engine is invoked.
	 * @param  string $return_mode This variable determines how the SRP output should be rendered.
	 * @return string It returns the SRP layout HTML.
	 */
	public function display_posts( $widget_call = NULL, $return_mode ) {

		// Initializing SRP content.
		$srp_content = '';

		// Generating the widget title.
		$srp_content .= $this->generate_widget_title();
		
		// Building special HTML comment with current SRP version.
		$srp_content  .= '<!-- BEGIN Special Recent Posts FREE Edition v' . SRP_PLUGIN_VERSION . ' -->';
		
		// Opening widget container.
		$srp_content .= '<div class="srp-widget-container">';
		
		// Generating posts via WP_query.
		$recent_posts = $this->generate_posts();
		
		// Checking if posts are available.
		if ( !$recent_posts ) {
		
			// No posts available. Displaying "no posts" message.
			$srp_content .= $this->srp_create_tag( 'p', trim( esc_html( $this->widget_args['noposts_text'] ) ), array( 'class' => 'srp-noposts-text', 'title' => esc_attr( $this->widget_args['noposts_text'] ) ) );
			
		} else {
			
			// Defining global column counter.
			$post_colrow_counter = 0;
			
			// Defining global post counter.
			$post_global_counter = 0;
			
			// Recent posts are available. Cyclying through result posts.
			while( $recent_posts->have_posts() ):
				
				// Switch to next post.
				$recent_posts->next_post();

				// Adding +1 to global post counter.
				$post_global_counter = $recent_posts->current_post + 1;
				
				// Adding +1 to post column counter.
				$post_colrow_counter++;
				
				// Fetching post image.
				$post_thumb_content = $this->display_thumb( $recent_posts->post );
				
				// Checking if current post has at least an image. If not, and Post Noimage Skip option is enabled, skip it.
				if (!$post_thumb_content) continue;

				// Compiling single post id.
				$srp_post_id = ( !$this->widget_id ) ? 'srp-singlepost-' . $post_global_counter : $this->widget_id . '-srp-singlepost-' . $post_global_counter;
				
				// Opening single post container.
				$srp_content .= '<div id="' . $srp_post_id . '" class="srp-widget-singlepost">';

				$srp_content .= '<div class="srp-post-content-container">';

				// Checking if thumbnail option is on.
				if ( 'yes' == $this->widget_args['display_thumbnail'] ) {
					
					// Opening container for thumbnail image.
					$srp_content .= $this->srp_create_tag( 'div', $post_thumb_content, array( 'class' => 'srp-thumbnail-box' ) );
				}
				
				// Checking for "no content at all" option. In this case, leave the content-box empty.
				if ( 'thumbonly' != $this->widget_args['post_content_mode'] ) {
				
					// Opening container for Content Box.
					$srp_content .= '<div class="srp-content-box">';
					
					// Generating the post title.
					$srp_content .= $this->generate_post_title( $recent_posts->post );
					
					// Checking if "post_date" option is on.
					if ( 'yes' == $this->widget_args['post_date'] ) {

						// Generating the post date.
						$srp_content .= $this->generate_post_date( $recent_posts->post );
					}
					
					// Checking for Post Content Option.
					if ( 'titleexcerpt' == $this->widget_args['post_content_mode'] ) {

						// Generating the post content text.
						$srp_content .= $this->generate_post_excerpt( $recent_posts->post );
					}

					// END Content Box.
					$srp_content .= "</div>";
					
				}
				
				// Closing Content Container.
				$srp_content .= '</div>';
				
				// Closing Single Post Container.
				$srp_content .= '</div>';
				
			endwhile; // EOF foreach cycle.
			
			// Resetting $post data array.
			wp_reset_query();
			wp_reset_postdata();
			
		} // END Empty posts check.
		
		// Closing widget container.
		$srp_content .= '</div>';
		
		// Closing Special Recent Post FREE Version comment.
		$srp_content .= '<!-- END Special Recent Posts FREE Edition v' . SRP_PLUGIN_VERSION . ' -->';

		// Checking if the 'External Shortcodes Compatibility' option is enabled.
		if ( 'yes' == $this->widget_args['ext_shortcodes_compatibility'] ) {

			// Executing external shortcodes before outputting the content.
			$srp_content = do_shortcode( $srp_content );
		}

		// Checking if the 'WP Filters Enabled' option is enabled.
		if ( 'yes' == $this->widget_args['wp_filters_enabled'] ) {

			// Executing external shortcodes before outputting the content.
			$srp_content = apply_filters( 'the_content', $srp_content );
		}
		
		// Switching through display return mode.
		switch( $return_mode ) {
		
			// Display HTML on screen.
			case 'print':

				echo $srp_content;

			break;
			
			// Return HTML.
			case 'return':

				return $srp_content;

			break;
		}
	}

	/**
	 * srp_create_tag()
	 *
	 * This method creates an HTML tag.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access private
	 * @param string $tagname The HTML tag name.
	 * @param string $tag_content The HTML tag content
	 * @param array $tag_attrs The HTML tag content
	 * @return string It returns or prints the tag HTML.
	 */
	private function srp_create_tag( $tagname, $tag_content = NULL, $tag_attrs = NULL ) {
	
		// Defining DOM root.
		$tagdom = new DOMDocument( '1.0' );
		
		// Creating tag element.
		$tag = $tagdom->createElement( $tagname, htmlentities( $tag_content, ENT_QUOTES, 'UTF-8' ) );
	
		// Checking if attributes array is empty.
		if ( !empty( $tag_attrs ) && ( isset( $tag_attrs ) ) ) {
		
			// Looping through attributes.
			foreach ( $tag_attrs as $att_name => $att_value ) {
			
				// Setting attribute.
				$tag->setAttribute( $att_name, $att_value );
			}
			
			// If the tag is a link (<a>), do the "nofollow_links" optio check. If it's enables, add the nofollow attribute.
			if ( ( 'a' == $tagname ) && ( 'yes' == $this->widget_args['nofollow_links'] ) ) $tag->setAttribute( 'rel', 'nofollow' );
		}
		
		// Appending created tag to DOM root.
		$tagdom->appendChild( $tag );
		
		// Saving HTML.
		$taghtml = trim( $tagdom->saveHTML() );

		// Cleaning DOM Root.
		unset( $tagdom );
		
		// Returning the HTML tag.
		return htmlspecialchars_decode( $taghtml );
	}

	/**
	 * srp_sanitize()
	 *
	 * This method sanitizes strings output.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access private
	 * @param string $string The string to sanitize.
	 * @return string It returns a sanitized string.
	 */
	private function srp_sanitize( $string ) {
		
		// Checking for External Shortcodes Compatibility option.
		// If it's enabled, let's not remove any shortcode found within the post content.
		if ( 'no' == $this->widget_args['ext_shortcodes_compatibility'] ) $string = strip_shortcodes( $string );
		
		// We need to remove all the exceeding stuff. Removing shortcodes and slashes.
		$temp_output = trim( stripslashes( $string ) );

		// Checking for the qTranslate filter.
		if ( function_exists( 'qtrans_useCurrentLanguageIfNotFoundShowAvailable' ) ) {

			// Applying qTranslate Filter if this exists.
			$temp_output = qtrans_useCurrentLanguageIfNotFoundShowAvailable( $temp_output );
		}
		
		// If "allowed_tags" option is on, keep them separated from strip_tags.
		if ( !empty( $this->widget_args['allowed_tags'] ) ) {
			
			// Handling the <br /> tag.
			$this->widget_args['allowed_tags'] = str_replace( '<br />', '<br>', $this->widget_args['allowed_tags'] );
			
			// Stripping tags except the ones specified.
			return strip_tags( $temp_output, htmlspecialchars_decode( $this->widget_args['allowed_tags'] ) );
			
		} else {
		
			// Otherwise completely strip tags from text.
			return strip_tags( $temp_output );
		}
	}
	
	/**
	 * substr_words()
	 *
	 * This method uses the same logic of PHP function 'substr' but works with words instead of characters.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access private
	 * @param string $string The string to search in
	 * @param int $n A counter.
	 * @param string $mode The search mode.
	 * @return mixed It could return the words count or the string found.
	 */
	private function substrWords( $str, $n, $mode = 'return' ) {
		
		// Counting words.
		$words_count = count( preg_split( '~[^\p{L}\p{N}\']+~u', $str ) );
		
		// Checking if max length is equal to original string length. In that case, return the string without making any 'cut'.
		if ( $words_count > $n ) {

			// Uses PHP 'count and preg_split' function to extract total words and put them into an array.
			$w = explode( ' ', $str );
			
			// Let's cut the array using our max length variable ($n).
			array_splice( $w, $n );
			
			// Switch mode.
			switch( $mode ) {
			
				case 'return':

					// Re-converting array to string and return.
					return implode( ' ', $w );

				break;
				
				case 'count':

					// Return count.
					return strlen( utf8_decode( implode(' ', $w ) ) );

				break;
			}
			
		} else {
			
			// Switch mode.
			switch( $mode ) {

				case "return":

					// Return string as it is, without making any 'cut'.
					return $str;

				break;
				
				case "count":

					// Return count.
					// 
					return strlen( utf8_decode( $str ) );

				break;
			}
		}
	}
	
	/**
	 * srp_truncate_text()
	 *
	 * This method truncates a string preserving html tags integrity.
	 * Only works on characters. (Credits: http://jsfromhell.com)
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access private
	 * @param string $text The text to be truncated.
	 * @param int $length The desired text length.
	 * @param string $suffix The possible string suffix.
	 * @param boolean $isHTML Checks whether the string is HTML or not.
	 * @return mixed It could return the words count or the string found.
	 * @see http://jsfromhell.com
	 */
	private function srp_truncate_text( $text, $length, $suffix = '', $isHTML = true ){

		// Defining Internal counter.
		$i = 0;

		// Dafining array for tags collection.
		$tags = array();

		// Checking if "Allowed Tags" option is enabled.
		if ( !empty( $this->widget_args['allowed_tags'] ) ) {

			// Checking if source string is HTML.
			if( $isHTML ){ 

				// Regex to find tags.
				preg_match_all( '/<[^>]+>([^<]*)/', $text, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER );

				// Looping inside the string.
				foreach( $m as $o ){

					// Check if chars limit is equal or superior the string length.
					if( $o[0][1] - $i >= $length ) break;

					// Trimming the string.
					$t = mb_substr( strtok( $o[0][0], " \t\n\r\0\x0B>" ), 1 );

					// Repairing HTML tags.
					if( $t[0] != '/' ) $tags[] = $t;
					elseif( end( $tags ) == mb_substr( $t, 1 ) ) array_pop( $tags ); $i += $o[1][1] - $o[0][1];
				}
			}
		}

		// Composing Result String.
		$output = mb_substr( $text, 0, $length = min( strlen( $text), $length + $i ) ) . ( count( $tags = array_reverse( $tags ) ) ? '' : '' );

		if ( strlen( $text ) > $length ) {
			$output = mb_substr( $output, -4, 4 ) == '' ? $output = mb_substr( $output, 0, ( strlen( $output) - 4 ) ).$suffix.'' : $output .= $suffix;
		}

		// Returning Result String.
		return $output; 

	}
} // END SpecialRecentPostsFree Class