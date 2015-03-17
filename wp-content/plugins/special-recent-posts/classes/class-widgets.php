<?php
/**
 * The Special Recent Posts FREE Widget
 *
 * The SRP Widget Class extended from the default WP one.
 *
 * @author Luca Grandicelli <lgrandicelli@gmail.com>
 * @copyright (C) 2011-2014 Luca Grandicelli
 * @package special-recent-posts-free
 * @version 2.0.4
 * @access public
 */
class WDG_SpecialRecentPostsFree extends WP_Widget {

	// Declaring global plugin values.
	private $plugin_args;

	/**
	 * __construct()
	 *
	 * The main SRP Widget Class constructor
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access public
	 * @return boolean true
	 */
	function __construct() {

		// Initializing parent constructor.
		parent::__construct(

			'wdg_specialrecentpostsfree', // Base ID
			__( 'Special Recent Posts FREE', SRP_TRANSLATION_ID ),
			array(
				'description' => __( 'The Special Recent Posts FREE Edition widget. Drag to configure.', SRP_TRANSLATION_ID ),
				'classname'   => 'widget_specialrecentpostsFree'
			)
			
		);

		// Assigning global plugin option values to local variable.
		$this->plugin_args = get_option( 'srp_plugin_options' );

		// Returning true.
		return true;
	}

	/**
	 * form()
	 *
	 * This function updates the current user values for this widget instance
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access public
	 * @param  array $instance The current widget values set by the user.
	 * @return boolean true
	 */
	function form( $instance ) {
	
		// Outputs the options form on widget panel.
		$this->buildWidgetForm( $instance );
	}

	/**
	 * update()
	 *
	 * This function updates the current user values for this widget instance
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access public
	 * @global $srp_default_widget_values The global plugin values.
	 * @param  array $new_instance The new widget values to be saved.
	 * @param  array $old_instance The old widget values to be replaced.
	 * @return array $instance It returns the current widget instance
	 */
	function update( $new_instance, $old_instance ) {
	
		// Declaring global plugin values.
		global $srp_default_widget_values;
		
		// Processes widget options to be saved.
		$instance = SpecialRecentPostsFree::srp_version_map_check( $old_instance );
		
		// Looping through the entire list of widget options values.
		foreach( $srp_default_widget_values as $k => $v ) {
			
			// Switching through each option.
			switch( $k ) {
				
				case "category_title":
				case "ext_shortcodes_compatibility":
				case "nofollow_links":
				case "post_random":
				case "post_current_hide":
				case "post_date":
				case "show_all_posts":
				case "show_sticky_posts":
				case "string_break_link":
				case "thumbnail_link":
				case "widget_title_hide":
				case "wp_filters_enabled":
				case "widget_title_show_default_wp":
					
					// Fixing all the NULL values coming from unchecked checkboxes.
					$instance[ $k ] = ( !isset( $new_instance[ $k ] ) ) ? 'no' : $new_instance[ $k ];
				break;
				
				case "thumbnail_height":
				case "thumbnail_width":
				
					// Checking if the new value is numeric. Then assign it.
					if ( is_numeric( $new_instance[ $k ] ) ) $instance[ $k ] = trim( $new_instance[ $k ] );
				break;
				
				case "post_content_length":
				case "post_limit":
				case "post_title_length":
				
					// Checking if the new value is numeric and not zero. Then assign it.
					if ( (is_numeric( $new_instance[ $k ] ) ) && ( $new_instance[ $k ] != 0) ) $instance[ $k ] = trim( $new_instance[ $k ] );
				break;
				
				case "post_offset":
					
					// Checking if the new value is numeric and is > zero. Then assign it.
					$instance[ $k ] = ( ( is_numeric( $new_instance[ $k ] ) ) && ( $new_instance[ $k ] > 0) ) ? trim( $new_instance[ $k ] ) : 0;
				break;

				default:
				
					// Default behaviour: for all other options, assign the new value.
					$instance[ $k ] = $new_instance[ $k ];
					
				break;
			}
		}

		// Returning new widget instance.
		return $instance;
	}
	
	/**
	 * widget()
	 *
	 * This is the main function that initializes the SRP rendering process.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access public
	 * @param  array $args The option values args.
	 * @param  array $instance The current widget instance.
	 * @return boolean true
	 */
	function widget( $args, $instance ) {
	
		// Checking Visualization Filter.
		if ( SpecialRecentPostsFree::visualization_check( $instance, 'widget' ) ) {
		
			// Extracting arguments.
			extract( $args, EXTR_SKIP );
			
			// Printing pre-widget stuff.
			echo $before_widget;

			// Checking for 'Use default Wordpress HTML layout for widget title' option value.
			if ( isset( $instance['widget_title_show_default_wp'] ) && 'yes' == $instance['widget_title_show_default_wp'] ) {

				// Checking that this option exists.
				if ( isset( $instance['widget_title_hide'] ) ) {

					// Fetching widget title.
					$widget_title = apply_filters( 'widget_title', $instance['widget_title'] );

					// Checking for "widget title hide" option.
					if ( 'yes' != $instance['widget_title_hide'] ) {

						// Printing default Widget Title HTML layout.
						echo $before_title . $widget_title . $after_title;
					}
					
				}
			}
			
			// Creating an instance of the Special Recent Posts Class.
			$srp = new SpecialRecentPostsFree( $instance, $this->id );
			
			// Checking that the $srp is a valid SRP class object.
			if ( is_object( $srp ) ) {

				// Displaying posts.
				$srp->display_posts( true, 'print' );
			}
			
			// Printing after widget stuff.
			echo $after_widget;
		}

		// Returning true.
		return true;
	}
	
	/**
	 * buildWidgetForm()
	 *
	 * This method build the widget layout.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 * @access public
	 * @global  $srp_default_widget_values The default widget presets.
	 * @param  array $instance The current widget instance.
	 * @return boolean true
	 */
	function buildWidgetForm( $instance ) {
	
		// Loading default widget values.
		global $srp_default_widget_values;
		
		// Loading default plugin presets.
		$plugin_args = get_option( 'srp_plugin_options' );
		
		// Merging default values with instance array, in case this is empty.
		$instance = wp_parse_args( (array) SpecialRecentPostsFree::srp_version_map_check( $instance ), $srp_default_widget_values );
?>

		<!-- BEGIN Widget Accordion -->
		<dl class="srp-wdg-accordion">

			<!-- BEGIN Widget Accordion Header -->
			<div class="srp-widget-header">

				<!-- BEGIN Widget Accordion Header Image -->
				<img src="<?php echo SRP_WIDGET_HEADER; ?>" alt="<?php esc_attr_e( 'The Special Recent Posts FREE logo', SRP_TRANSLATION_ID ); ?>" />
				<!-- END Widget Accordion Header Image -->

				<!-- BEGIN Widget Accordion Header Title -->
				<?php _e( 'Widget Settings', SRP_TRANSLATION_ID ); ?>
				<!-- BEGIN Widget Accordion Header Title -->

			</div>
			<!-- END Widget Accordion Header -->

			<!-- BEGIN Basic Options Tab -->
			<dt class="srp-widget-optionlist-dt-basic">
				<a class="srp-wdg-accordion-item active" href="#1" title="<?php esc_attr_e( 'Basic Options', SRP_TRANSLATION_ID ); ?>">
					<?php _e( 'Basic Options', SRP_TRANSLATION_ID ); ?>
				</a>
			</dt>
			<!-- END Basic Options Tab -->

			<!-- BEGIN Basic Options Content -->
			<dd class="srp-widget-optionlist-dd-basic">

				<!-- BEGIN Basic Options Content List -->
				<ul class="srp-widget-optionlist-basic srp-widget-optionlist">

					<!-- BEGIN Widget Title -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>" class="srp-widget-label">
							<?php _e( 'Widget Title', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Type in the widget title text.',SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<input type="text" id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" value="<?php esc_html_e( $instance['widget_title'] ); ?>" size="30" class="fullwidth" />
						<!-- END Form Field -->

					</li>
					<!-- END Widget Title -->
					
					<!-- BEGIN Post Type -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_type' ); ?>" class="srp-widget-label">
							<?php _e( 'Post Type', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Select what kind of post type to display.',SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<select id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>" class="srp-widget-select">

							<option value="post" <?php selected( $instance['post_type'], 'post' ); ?>>
								<?php _e( 'Posts', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="page" <?php selected( $instance['post_type'], 'page' ); ?>>
								<?php _e( 'Pages', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="revision" <?php selected( $instance['post_type'], 'revision' ); ?>>
								<?php _e( 'Revision', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="any" <?php selected( $instance['post_type'], 'any' ); ?>>
								<?php _e( 'Any Type', SRP_TRANSLATION_ID ); ?>
							</option>

						</select>
						<!-- END Form Field -->

					</li>
					<!-- END Post Type -->
					
					<!-- BEGIN Post Limit -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_limit' ); ?>" class="srp-widget-label">
							<?php _e( 'Post Limit', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Enter the maximum number of posts/pages to display.', SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<input type="text" id="<?php echo $this->get_field_id( 'post_limit' ); ?>" name="<?php echo $this->get_field_name( 'post_limit' ); ?>" value="<?php echo stripslashes( $instance['post_limit'] ); ?>" size="2" />
						<!-- END Form Field -->

					</li>
					<!-- END Post Limit -->

					<!-- BEGIN Show All Posts -->
					<li>

						<!-- BEGIN Form Field -->
						<input type="checkbox" id="<?php echo $this->get_field_id( 'show_all_posts' ); ?>" name="<?php echo $this->get_field_name( 'show_all_posts' ); ?>" value="yes" <?php checked( $instance['show_all_posts'], 'yes' ); ?> />
						<!-- END Form Field -->

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'show_all_posts' ); ?>" class="srp-widget-label-inline">
							<?php _e( 'Show All Posts/Pages', SRP_TRANSLATION_ID ); ?>
						</label><br />
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( "Check this box if you want to show all of your blog's posts and pages. This option will override the 'Post Limit' option above.", SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

						<!-- BEGIN Notebox -->
						<div class="srp-accordion-notebox">
							<?php _e( "NOTE: no pagination will be applied and if you have many entries, your website could be very slow.",SRP_TRANSLATION_ID ); ?>
						</div>
						<!-- END Notebox -->

					</li>
					<!-- END Show All Posts -->

					<!-- BEGIN Show Sticky Posts -->
					<li>

						<!-- BEGIN Form Field -->
						<input type="checkbox" id="<?php echo $this->get_field_id( 'show_sticky_posts' ); ?>" name="<?php echo $this->get_field_name( 'show_sticky_posts' ); ?>" value="yes" <?php checked( $instance['show_sticky_posts'], 'yes' ); ?> />
						<!-- END Form Field -->

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'show_sticky_posts' ); ?>" class="srp-widget-label-inline">
							<?php _e( 'Show Sticky Posts?', SRP_TRANSLATION_ID ); ?></label><br />
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Check this box if you want to show sticky posts.',SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

					</li>
					<!-- END Show Sticky Posts -->

				</ul>
				<!-- END Basic Options Content List -->

			</dd>
			<!-- END Basic Options Content -->
			
			<!-- BEGIN Thumbnails Options Tab -->
			<dt class="srp-widget-optionlist-dt-thumbnails">
				<a class="srp-wdg-accordion-item" href="#2" title="<?php esc_attr_e( 'Thumbnails Options', SRP_TRANSLATION_ID ); ?>">
					<?php _e( 'Thumbnails Options', SRP_TRANSLATION_ID ); ?>
				</a>
			</dt>
			<!-- END Thumbnails Options Tab -->

			<!-- BEGIN Thumbnails Option Content -->
			<dd class="srp-widget-optionlist-dd-thumbnails">

				<!-- BEGIN Thumbnails Options List -->
				<ul class="srp-widget-optionlist-thumbnails srp-widget-optionlist">
					
					<!-- BEGIN Display Thumbnail -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'display_thumbnail' ); ?>" class="srp-widget-label">
							<?php _e( 'Display Thumbnails?', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Choose whether thumbnails should be displayed or not.', SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- BEGIN Description -->

						<!-- BEGIN Form Field -->
						<select id="<?php echo $this->get_field_id( 'display_thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'display_thumbnail' ); ?>" class="srp-widget-select">

							<option value="yes" <?php selected( $instance['display_thumbnail'], 'yes' ); ?>>
								<?php _e( 'Yes', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="no" <?php selected($instance['display_thumbnail'], 'no' ); ?>>
								<?php _e( 'No', SRP_TRANSLATION_ID ); ?>
							</option>

						</select>
						<!-- END Form Field -->

					</li>
					<!-- END Display Thumbnail -->

					<!-- BEGIN Thumbnail Width. -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'thumbnail_width' ); ?>" class="srp-widget-label">
							<?php _e( 'Thumbnail Width', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Enter the thumbnail width in pixel:',SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<input type="text" id="<?php echo $this->get_field_id( 'thumbnail_width' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail_width' ); ?>" value="<?php echo $instance['thumbnail_width']; ?>" size="5" /> px
						<!-- BEGIN Form Field -->

					</li>
					<!-- END Thumbnail Width. -->
					
					<!-- BEGIN Thumbnail Height. -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'thumbnail_height' ); ?>" class="srp-widget-label">
							<?php _e( 'Thumbnail Weight', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Enter the thumbnail height in pixel:',SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<input type="text" id="<?php echo $this->get_field_id( 'thumbnail_height' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail_height' ); ?>" value="<?php echo $instance['thumbnail_height']; ?>" size="5" /> px
						<!-- END Form Field -->

					</li>
					<!-- END Thumbnail Height. -->
					
					<!--BEGIN Link Thumbnail To Post -->
					<li>

						<!-- BEGIN Form Field -->
						<input type="checkbox" id="<?php echo $this->get_field_id( 'thumbnail_link' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail_link' ); ?>" value="yes" <?php checked( $instance['thumbnail_link'], 'yes' ); ?> />
						<!-- END Form Field -->

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'thumbnail_link' ); ?>" class="srp-widget-label-inline">
							<?php _e( 'Link Thumbnail To Post', SRP_TRANSLATION_ID ); ?>
						</label><br />
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Check this box if you want to link the thumbnail to the related post/page.', SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

					</li>
					<!--END Link Thumbnail To Post -->
					
					<!-- BEGIN Thumbnail Rotation -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'thumbnail_rotation' ); ?>" class="srp-widget-label">
							<?php _e( 'Thumbnail Rotation', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Select the thumbnail rotation mode:',SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<select id="<?php echo $this->get_field_id( 'thumbnail_rotation' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail_rotation' ); ?>" class="srp-widget-select">

							<option value="no" <?php selected( $instance['thumbnail_rotation'], 'adaptive' ); ?>>
								<?php _e( 'No Rotation (default)', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="rotate-cw" <?php selected( $instance['thumbnail_rotation'], 'rotate-cw' ); ?>>
								<?php _ex( 'Rotate CW', "CW stands for 'Clockwise'.", SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="rotate-ccw" <?php selected( $instance['thumbnail_rotation'], 'rotate-ccw' ); ?>>
								<?php _ex( 'Rotate CCW', "CCW stands for 'Counterclockwise'.", SRP_TRANSLATION_ID ); ?>
							</option>
						</select>
						<!-- BEGIN Form Field -->

					</li>
					<!-- END Thumbnail Rotation -->

				</ul>
				<!-- END Thumbnails Options List -->

			</dd>
			<!-- END Thumbnails Option Content -->
			
			<!-- BEGIN Post Options Tab -->
			<dt class="srp-widget-optionlist-dt-posts">
				<a class="srp-wdg-accordion-item" href="#3" title="<?php esc_attr_e( 'Post Options', SRP_TRANSLATION_ID ); ?>">
					<?php _e( 'Post Options', SRP_TRANSLATION_ID ); ?>
				</a>
			</dt>
			<!-- END Post Options Tab -->

			<!-- BEGIN Post Options Content -->
			<dd class="srp-widget-optionlist-dd-posts">

				<!-- BEGIN Post Options List -->
				<ul class="srp-widget-optionlist-posts srp-widget-optionlist">
				
					<!-- BEGIN Post Title Length -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_title_length' ); ?>" class="srp-widget-label">
							<?php _e( 'Post Title Length', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Select how many characters or words every post title should be cut after:', SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- BEGIN Description -->

						<!-- BEGIN Form Field -->
						<input type="text" id="<?php echo $this->get_field_id( 'post_title_length' ); ?>" name="<?php echo $this->get_field_name( 'post_title_length' ); ?>" value="<?php esc_html_e( $instance["post_title_length"] ); ?>" size="4" style="float: left;" />
						<!-- END Form Field -->

						<!-- BEGIN Form Field -->
						<select id="<?php echo $this->get_field_id( 'post_title_length_mode' ); ?>" name="<?php echo $this->get_field_name( 'post_title_length_mode' ); ?>" class="srp-widget-select">

							<option value="words" <?php selected( $instance['post_title_length_mode'], 'words' ); ?>>
								<?php _e( 'Words', SRP_TRANSLATION_ID); ?>
							</option>

							<option value="chars" <?php selected( $instance['post_title_length_mode'], 'chars' ); ?>>
								<?php _e( 'Characters', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="fulltitle" <?php selected( $instance['post_title_length_mode'], 'fulltitle' ); ?>>
								<?php _e( 'Use Full Length (no cut)', SRP_TRANSLATION_ID ); ?>
							</option>
							
						</select>
						<!-- END Form Field -->

					</li>
					<!-- END Post Title Length -->
					
					<!-- BEGIN Post Content Type -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_content_type' ); ?>" class="srp-widget-label">
							<?php _e( 'Post Content Type', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Select if you wish to display the normal post content or the post excerpt:',SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<select id="<?php echo $this->get_field_id( 'post_content_type' ); ?>" name="<?php echo $this->get_field_name( 'post_content_type' ); ?>" class="srp-widget-select">

							<option value="content" <?php selected( $instance['post_content_type'], 'content' ); ?>>
								<?php _e( 'Post Content', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="excerpt" <?php selected( $instance['post_content_type'], 'excerpt' ); ?>>
								<?php _e( 'Post Excerpt', SRP_TRANSLATION_ID ); ?>
							</option>
							
						</select>
						<!-- END Form Field -->

					</li>
					<!-- END Post Content Type -->

					
					<!-- BEGIN Post Content Length -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_content_length' ); ?>" class="srp-widget-label">
							<?php _e( 'Post Content Length', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Select how many characters or words every post content should be cut after:',SRP_TRANSLATION_ID ); ?>
						</small><br />			
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<input type="text" id="<?php echo $this->get_field_id( 'post_content_length' ); ?>" name="<?php echo $this->get_field_name( 'post_content_length' ); ?>" value="<?php esc_html_e( $instance['post_content_length'] ); ?>" size="4" style="float: left;" />
						<!-- END Form Field -->

						<!-- BEGIN Form Field -->
						<select id="<?php echo $this->get_field_id( 'post_content_length_mode' ); ?>" name="<?php echo $this->get_field_name( 'post_content_length_mode' ); ?>" class="srp-widget-select">

							<option value="words" <?php selected( $instance['post_content_length_mode'], 'words' ); ?>>
								<?php _e( 'Words', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="chars" <?php selected( $instance['post_content_length_mode'], 'chars' ); ?>>
								<?php _e( 'Characters', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="fullcontent" <?php selected( $instance['post_content_length_mode'], 'fullcontent' ); ?>>
								<?php _e( 'Use full length (no cut)', SRP_TRANSLATION_ID ); ?>
							</option>

						</select>
						<!-- END Form Field -->

					</li>
					<!-- END Post Content Length -->
					
					<!-- BEGIN Posts/Pages Order -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_order' ); ?>" class="srp-widget-label">
							<?php _e( 'Posts/Pages Order', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Select the posts/pages display order:',SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- BEGIN Description -->

						<!-- BEGIN Form Field -->
						<select id="<?php echo $this->get_field_id( 'post_order' ); ?>" name="<?php echo $this->get_field_name( 'post_order' ); ?>" class="srp-widget-select">

							<option value="DESC" <?php selected( $instance['post_order'], 'DESC' ); ?>>
								<?php _ex( 'Latest First (DESC) (default)', "DESC stands for 'Descending Order'", SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="ASC" <?php selected( $instance['post_order'], 'ASC' ); ?>>
								<?php _ex( 'Oldest First (ASC)', "ASC stands for 'Ascending Order'",SRP_TRANSLATION_ID ); ?>
							</option>

						</select>
						<!-- BEGIN Form Field -->

					</li>
					<!-- END Posts/Pages Order -->
					
					<!-- BEGIN Enable Random Mode -->
					<li>

						<!-- BEGIN Form Field -->
						<input type="checkbox" id="<?php echo $this->get_field_id( 'post_random' ); ?>" name="<?php echo $this->get_field_name( 'post_random' ); ?>" value="yes" <?php checked( $instance['post_random'], 'yes' ); ?> />
						<!-- END Form Field -->

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_random' ); ?>" class="srp-widget-label-inline">
							<?php _e( 'Enable Random Mode', SRP_TRANSLATION_ID ); ?>
						</label><br />
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Check this box if you want to randomize the posts order.',SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

					</li>
					<!-- END Enable Random Mode -->

					<!-- BEGIN Enable External Shortcodes Compatibility -->
					<li>

						<!-- BEGIN Form Field -->
						<input type="checkbox" id="<?php echo $this->get_field_id( 'ext_shortcodes_compatibility' ); ?>" name="<?php echo $this->get_field_name( 'ext_shortcodes_compatibility' ); ?>" value="yes" <?php checked( $instance['ext_shortcodes_compatibility'], 'yes' ); ?> />
						<!-- END Form Field -->

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'ext_shortcodes_compatibility' ); ?>" class="srp-widget-label-inline">
							<?php _e( 'Enable External Shortcodes Compatibility', SRP_TRANSLATION_ID ); ?>
						</label><br />
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Check this box if you want SRP to let other plugins shortcodes to work within the post content.',SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

					</li>
					<!-- END Enable External Shortcodes Compatibility -->

					<!-- BEGIN Enable Wordpress Filters -->
					<li>

						<!-- BEGIN Form Field -->
						<input type="checkbox" id="<?php echo $this->get_field_id( 'wp_filters_enabled' ); ?>" name="<?php echo $this->get_field_name( 'wp_filters_enabled' ); ?>" value="yes" <?php checked( $instance['wp_filters_enabled'], 'yes' ); ?> />
						<!-- END Form Field -->

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'wp_filters_enabled' ); ?>" class="srp-widget-label-inline">
							<?php _e( 'Enable Wordpress Filters', SRP_TRANSLATION_ID ); ?>
						</label><br />
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Check this box if you want SRP to apply WP filters before outputting the post content.', SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

					</li>
					<!-- END Enable Wordpress Filters -->

				</ul>
				<!-- END Post Options List -->

			</dd>
			<!-- END Post Options Content -->
			
			<!-- BEGIN Advanced Post Options 1 Tab -->
			<dt class="srp-widget-optionlist-dt-advposts">
				<a class="srp-wdg-accordion-item" href="#4" title="<?php esc_attr_e( 'Advanced Post Options 1', SRP_TRANSLATION_ID ); ?>">
					<?php _e( 'Advanced Post Options 1', SRP_TRANSLATION_ID ); ?>
				</a>
			</dt>
			<!-- END Advanced Post Options 1 Tab -->

			<!-- BEGIN Advanced Post Options 1 Content -->
			<dd class="srp-widget-optionlist-dd-advposts">

				<!-- BEGIN Advanced Post Options 1 List -->
				<ul class="srp-widget-optionlist-advposts srp-widget-optionlist">
					
					<!-- BEGIN No Posts Default Text -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'noposts_text' ); ?>" class="srp-widget-label">
							<?php _e( 'No Posts Default Text', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Type in the default text to display when there are no posts available:', SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<input type="text" id="<?php echo $this->get_field_id( 'noposts_text' ); ?>" name="<?php echo $this->get_field_name( 'noposts_text' ); ?>" value="<?php echo stripslashes( $instance['noposts_text'] ); ?>" size="30" class="fullwidth" />
						<!-- END Form Field -->

					</li>
					<!-- END No Posts Default Text -->
					
					<!-- BEGIN Hide Current Viewed Post -->
					<li>

						<!-- BEGIN Form Field -->
						<input type="checkbox" id="<?php echo $this->get_field_id( 'post_current_hide' ); ?>" name="<?php echo $this->get_field_name( 'post_current_hide' ); ?>" value="yes" <?php checked( $instance['post_current_hide'], 'yes' ); ?> />
						<!-- END Form Field -->

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_current_hide' ); ?>" class="srp-widget-label-inline">
							<?php _e( 'Hide Current Viewed Post', SRP_TRANSLATION_ID ); ?>
						</label><br />
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( "Check this box if you want to hide the current viewed post/page. Useful when SRP is on a sidebar and you're on a single post page.", SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

					</li>
					<!-- END Hide Current Viewed Post -->
					
					<!-- BEGIN Post Offset -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_offset' ); ?>" class="srp-widget-label">
							<?php _e( 'Post Offset', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Enter the number of post/pages to skip from the beginning:', SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<input type="text" id="<?php echo $this->get_field_id( 'post_offset' ); ?>" name="<?php echo $this->get_field_name( 'post_offset' ); ?>" value="<?php echo stripslashes( $instance['post_offset'] ); ?>" size="2" />
						<!-- END Form Field -->

					</li>
					<!-- END Post Offset -->
					
					<!-- BEGIN Post String Break -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'string_break' ); ?>" class="srp-widget-label">
							<?php _e( 'Post String Break', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Enter the text to be displayed as string break just after the end of the post/page content:', SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<input type="text" id="<?php echo $this->get_field_id( 'string_break' ); ?>" name="<?php echo $this->get_field_name( 'string_break' ); ?>" value="<?php echo stripslashes( $instance['string_break'] ); ?>" size="30" class="fullwidth" />
						<!-- END Form Field -->

					</li>
					<!-- END Post String Break -->
					
					<!-- BEGIN Image String Break -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'image_string_break' ); ?>" class="srp-widget-label">
							<?php _e( 'Image String Break', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Enter the absolute URL of a custom image to use as a string break:', SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<input type="text" id="<?php echo $this->get_field_id( 'image_string_break' ); ?>" name="<?php echo $this->get_field_name( 'image_string_break' ); ?>" value="<?php echo stripslashes( $instance['image_string_break'] ); ?>" size="30" class="fullwidth" placeholder="<?php _e( "Example: http://www.test.com/myabsoluteimage.jpg", SRP_TRANSLATION_ID ); ?>" />
						<!-- END Form Field -->

					</li>
					<!-- END Image String Break -->

					<!-- BEGIN Link String/Image Break To Post -->
					<li>

						<!-- BEGIN Form Field -->
						<input type="checkbox" id="<?php echo $this->get_field_id( 'string_break_link' ); ?>" name="<?php echo $this->get_field_name( 'string_break_link' ); ?>" value="yes" <?php checked( $instance['string_break_link'], 'yes' ); ?> />
						<!-- END Form Field -->

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'string_break_link' ); ?>" class="srp-widget-label-inline">
							<?php _e( 'Link String/Image Break To Post?', SRP_TRANSLATION_ID ); ?>
						</label><br />
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Check this box if you want to link the string/image break to the related post/page.', SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

					</li>
					<!-- END Link String/Image Break To Post -->
					
					<!-- BEGIN Post Allowed Tags -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'allowed_tags' ); ?>" class="srp-widget-label">
							<?php _e( 'Post Allowed Tags', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Enter a list of allowed HTML tags to be rendered in the post content. Leave blank for clean text without any markup.', SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<input type="text" id="<?php echo $this->get_field_id( 'allowed_tags' ); ?>" name="<?php echo $this->get_field_name( 'allowed_tags' ); ?>" value="<?php echo stripslashes( $instance['allowed_tags'] ); ?>" size="30" class="fullwidth" />
						<!-- END Form Field -->

						<!-- BEGIN Notebox -->
						<div class="srp-accordion-notebox">
							<?php _e( esc_html('NOTE: When using this option, type in your tags in the following form: <a><span><p>'), SRP_TRANSLATION_ID );?>
						</div>
						<!-- END Notebox -->

					</li>
					<!-- END Post Allowed Tags -->

				</ul>
				<!-- END Advanced Post Options 1 List -->

			</dd>
			<!-- END Advanced Post Options 1 Content -->

			<!-- BEGIN Advanced Post Options 2 Tab -->
			<dt class="srp-widget-optionlist-dt-advposts">
				<a class="srp-wdg-accordion-item" href="#5" title="<?php esc_attr_e( 'Advanced Post Options 2', SRP_TRANSLATION_ID ); ?>">
					<?php _e( 'Advanced Post Options 2', SRP_TRANSLATION_ID ); ?>
				</a>
			</dt>
			<!-- END Advanced Post Options 2 Tab -->

			<!-- BEGIN Advanced Post Options 2 Content -->
			<dd class="srp-widget-optionlist-dd-advposts">

				<!-- BEGIN Advanced Post Options 2 List -->
				<ul class="srp-widget-optionlist-advposts srp-widget-optionlist">

					<!-- BEGIN Add 'rel=nofollow' Attribute On Links -->
					<li>

						<!-- BEGIN Form Field -->
						<input type="checkbox" id="<?php echo $this->get_field_id( 'nofollow_links' ); ?>" name="<?php echo $this->get_field_name( 'nofollow_links' ); ?>" value="yes" <?php checked( $instance['nofollow_links'], 'yes' ); ?> />
						<!-- END Form Field -->

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'nofollow_links' ); ?>" class="srp-widget-label-inline">
							<?php _e( "Add 'rel=nofollow' Attribute On Links?", SRP_TRANSLATION_ID ); ?>
						</label><br />
						<!-- BEGIN Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( "Check this box if you want to use the 'rel=nofollow' attribute on every post/page link.", SRP_TRANSLATION_ID ); ?>
							<a href="http://en.wikipedia.org/wiki/Nofollow" title="nofollow" target="_blank">
								<?php _e( 'Learn more', SRP_TRANSLATION_ID); ?>
							</a>
						</small>
						<!-- BEGIN Description -->

					</li>
					<!-- END Add 'rel=nofollow' Attribute On Links -->

					<!-- BEGIN Post Meta -->
					<li>

						<label class="srp-widget-label"><?php _e('Post Meta', SRP_TRANSLATION_ID); ?></label>

						<!-- BEGIN Form Field -->
						<input type="checkbox" id="<?php echo $this->get_field_id( 'post_date' ); ?>" name="<?php echo $this->get_field_name( 'post_date' ); ?>" value="yes" <?php checked( $instance['post_date'], 'yes' ); ?> />
						<!-- BEGIN Form Field -->

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_date' ); ?>" class="srp-widget-label-inline">
							<?php _e( 'Display post date', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- BEGIN Label -->

					</li>
					<!-- END Post Meta -->
					
					<!-- BEGIN Post Date Format -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'date_format' ); ?>" class="srp-widget-label">
							<?php _e( 'Post Date Format (*)', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Type In the coded format of post dates.',SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<input type="text" id="<?php echo $this->get_field_id( 'date_format' ); ?>" name="<?php echo $this->get_field_name( 'date_format' ); ?>" value="<?php echo stripslashes( $instance['date_format'] ); ?>" size="30" class="fullwidth" /><br />
						<!-- END Form Field -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( '*(F = Month name | j = Day of the month | S = ordinal suffix for the day of the month | Y = Year)', SRP_TRANSLATION_ID ); ?>
						</small>
						<br />
						<small>
							<a href="http://php.net/manual/en/function.date.php" title="Date formatting" target="_blank">
								<?php _e( 'Learn more about date formatting', SRP_TRANSLATION_ID ); ?>
							</a>
						</small>
						<!-- END Description -->

					</li>
					<!-- END Post Date Format -->

				</ul>
				<!-- END Advanced Post Options 2 List -->

			</dd>
			<!-- END Advanced Post Options 2 Content -->

			<!-- BEGIN Filtering Options Tab -->
			<dt class="srp-widget-optionlist-dt-filtering">
				<a class="srp-wdg-accordion-item" href="#6" title="<?php esc_attr_e( 'Filtering Options', SRP_TRANSLATION_ID ); ?>">
					<?php _e( 'Filtering Options', SRP_TRANSLATION_ID ); ?>
				</a>
			</dt>
			<!-- BEGIN Filtering Options Tab -->

			<!-- BEGIN Filtering Options Content -->
			<dd class="srp-widget-optionlist-dd-filtering">

				<!-- BEGIN Filtering Options List -->
				<ul class="srp-widget-optionlist-filtering srp-widget-optionlist">

					<!-- BEGIN Post Status Filter -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_status' ); ?>" class="srp-widget-label">
							<?php _e( 'Post Status Filter', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Select how to filter displayed posts/pages based on their status:', SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- END Description -->

						<!-- BEGIN Form field -->
						<select id="<?php echo $this->get_field_id( 'post_status' ); ?>" name="<?php echo $this->get_field_name( 'post_status' ); ?>" class="srp-widget-select">

							<option value="publish" <?php selected( $instance['post_status'], 'publish' ); ?>>
								<?php _e( 'Published (default)', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="private" <?php selected( $instance['post_status'], 'private' ); ?>>
								<?php _e( 'Private', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="inherit" <?php selected( $instance['post_status'], 'inherit' ); ?>>
								<?php _e( 'Inherit', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="pending" <?php selected( $instance['post_status'], 'pending' ); ?>>
								<?php _e( 'Pending', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="future" <?php selected( $instance['post_status'], 'future' ); ?>>
								<?php _e( 'Future', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="draft" <?php selected( $instance['post_status'], 'draft' ); ?>>
								<?php _e( 'Draft', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="auto-draft" <?php selected( $instance['post_status'], 'auto-draft' ); ?>>
								<?php _e( 'Auto Draft', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="trash" <?php selected( $instance['post_status'], 'trash' ); ?>>
								<?php _e( 'Trash', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="any" <?php selected( $instance['post_status'], 'any' ); ?>>
								<?php _e( 'Any Status', SRP_TRANSLATION_ID ); ?>
							</option>

						</select>
						<!-- END Form field -->

					</li>
					<!-- END Post Status Filter -->
					
					<!-- BEGIN Category Filter -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'category_include' ); ?>" class="srp-widget-label">
							<?php _e( 'Category Filter', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Enter a comma separated list of numeric categories IDs to filter posts by. Leave blank for no specific filtering.', SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

						<!-- BEGIN Form field -->
						<input type="text" id="<?php echo $this->get_field_id( 'category_include' ); ?>" name="<?php echo $this->get_field_name( 'category_include' ); ?>" value="<?php esc_html_e( $instance['category_include'] ); ?>" class="fullwidth" placeholder="<?php _e( "Example: 2, 7, 23", SRP_TRANSLATION_ID ); ?>" />
						<!-- END Form field -->

					</li>
					<!-- END Category Filter -->
					
					<!-- BEGIN Use Category Name As Widget Title -->
					<li>

						<!-- BEGIN Form field -->
						<input type="checkbox" id="<?php echo $this->get_field_id( 'category_title' ); ?>" name="<?php echo $this->get_field_name( 'category_title' ); ?>" value="yes" <?php checked( $instance['category_title'], 'yes' ); ?> />
						<!-- END Form field -->

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'category_title' ); ?>" class="srp-widget-label-inline">
							<?php _e( 'Use Category Name As Widget Title?', SRP_TRANSLATION_ID ); ?>
						</label><br />
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Check this box if you want to use the category name as the widget title when a category filter is on.', SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

						<!-- BEGIN Notebox -->
						<div class="srp-accordion-notebox">
							<?php _e( "In case of multiple categories, SRP will pull out the first category ID title in the list above.", SRP_TRANSLATION_ID ); ?>
						</div>
						<!-- END Notebox -->

					</li>
					<!-- END Use Category Name As Widget Title -->
					
					<!-- BEGIN Posts/Page ID Filter -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_include' ); ?>" class="srp-widget-label">
							<?php _e( 'Posts/Page ID Filter', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Enter a comma separated list of numeric posts/pages IDs to filter by. Leave blank for no specific filtering.', SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

						<!-- BEGIN Form field -->
						<input type="text" id="<?php echo $this->get_field_id( 'post_include' ); ?>" name="<?php echo $this->get_field_name( 'post_include' ); ?>" value="<?php esc_html_e( $instance['post_include'] ); ?>" class="fullwidth" placeholder="<?php _e( "Example: 5, 7, 23", SRP_TRANSLATION_ID ); ?>" />
						<!-- END Form field -->

					</li>
					<!-- END Posts/Page ID Filter -->
					
					<!-- BEGIN Exclude Posts/Pages By IDs -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_exclude' ); ?>" class="srp-widget-label">
							<?php _e( 'Exclude Posts/Pages By IDs', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Enter a comma separated list of numeric posts/pages IDs to exclude. Leave blank for no exclusion.', SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

						<!-- BEGIN Form field -->
						<input type="text" id="<?php echo $this->get_field_id( 'post_exclude' ); ?>" name="<?php echo $this->get_field_name( 'post_exclude' ); ?>" value="<?php esc_html_e( $instance['post_exclude'] ); ?>" class="fullwidth" placeholder="<?php _e( 'Example: 6, 14, 45', SRP_TRANSLATION_ID ); ?>" />
						<!-- END Form field -->

					</li>
					<!-- END Exclude Posts/Pages By IDs -->
					
					<!-- BEGIN Custom Post Type Filter -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'custom_post_type' ); ?>" class="srp-widget-label">
							<?php _e( 'Custom Post Type Filter', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Type here the name of a custom post type you wish to filter posts by:', SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

						<!-- BEGIN Form field -->
						<input type="text" id="<?php echo $this->get_field_id( 'custom_post_type' ); ?>" name="<?php echo $this->get_field_name( 'custom_post_type' ); ?>" value="<?php echo stripslashes( $instance['custom_post_type'] ); ?>" class="fullwidth" placeholder="<?php _e( 'Example: my-custom-post-type', SRP_TRANSLATION_ID ); ?>" />
						<!-- END Form field -->

					</li>
					<!-- END Custom Post Type Filter -->

				</ul>
				<!-- END Filtering Options List -->

			</dd>
			<!-- END Filtering Options Content -->

			<!-- BEGIN Layout Options Tab -->
			<dt class="srp-widget-optionlist-dt-layout">
				<a class="srp-wdg-accordion-item" href="#8" title="<?php esc_attr_e( 'Layout Options', SRP_TRANSLATION_ID ); ?>">
					<?php _e( 'Layout Options', SRP_TRANSLATION_ID ); ?>
				</a>
			</dt>
			<!-- END Layout Options Tab -->

			<!-- BEGIN Layout Options Content -->
			<dd class="srp-widget-optionlist-dd-layout">

				<!-- BEGIN Layout Options List -->
				<ul class="srp-widget-optionlist-layout srp-widget-optionlist">
					
					<!-- BEGIN Default WP Widget Title HTML -->
					<li>

						<!-- BEGIN Form Field -->
						<input type="checkbox" id="<?php echo $this->get_field_id( 'widget_title_show_default_wp' ); ?>" name="<?php echo $this->get_field_name( 'widget_title_show_default_wp' ); ?>" value="yes" <?php checked( $instance['widget_title_show_default_wp'], 'yes' ); ?> />
						<!-- END Form Field -->

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'widget_title_show_default_wp' ); ?>" class="srp-widget-label-inline">
							<?php _e( 'Use Default Wordpress HTML Layout for Widget Title', SRP_TRANSLATION_ID ); ?>
						</label><br />
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
						<?php _e( 'Check this box if you want to show the widget title HTML layout as Wordpress would normally render it.', SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->


						<!-- BEGIN Notebox -->
						<div class="srp-accordion-notebox">
							<?php _e( "If you're experiencing issues with widget titles compatibility, you might want to turn this option on.", SRP_TRANSLATION_ID ); ?>
						</div>
						<!-- END Notebox -->

					</li>
					<!-- END Default WP Widget Title HTML -->

					<!-- BEGIN Widget Title HTML Header -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'widget_title_header' ); ?>" class="srp-widget-label">
							<?php _e( 'Custom Widget Title HTML Header', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Select the type of HTML header to be used to enclose the widget title:', SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<select id="<?php echo $this->get_field_id( 'widget_title_header' ); ?>" name="<?php echo $this->get_field_name( 'widget_title_header' ); ?>" class="srp-widget-select">
							<option value="h1" <?php selected( $instance['widget_title_header'], 'h1' ); ?>>H1</option>
							<option value="h2" <?php selected( $instance['widget_title_header'], 'h2' ); ?>>H2</option>
							<option value="h3" <?php selected( $instance['widget_title_header'], 'h3' ); ?>>H3 <?php _e('(default)', SRP_TRANSLATION_ID ); ?></option>
							<option value="h4" <?php selected( $instance['widget_title_header'], 'h4' ); ?>>H4</option>
							<option value="h5" <?php selected( $instance['widget_title_header'], 'h5' ); ?>>H5</option>
							<option value="h6" <?php selected( $instance['widget_title_header'], 'h6' ); ?>>H6</option>
						</select>
						<!-- END Form Field -->

					</li>
					<!-- END Widget Title HTML Header -->

					<!-- BEGIN Additional Widget Title CSS Classes -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'widget_title_header_classes' ); ?>" class="srp-widget-label">
							<?php _e( 'Additional Widget Title CSS Classes', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Enter a space separated list of additional CSS classes for the custom widget title:', SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<input type="text" id="<?php echo $this->get_field_id( 'widget_title_header_classes' ); ?>" name="<?php echo $this->get_field_name( 'widget_title_header_classes' ); ?>" value="<?php echo stripslashes( $instance['widget_title_header_classes'] ); ?>" class="fullwidth" placeholder="<?php _e( 'Example: myclass1 myclass2', SRP_TRANSLATION_ID ); ?>" />
						<!-- END Form Field -->

					</li>
					<!-- END Additional Widget Title CSS Classes -->

					<!-- BEGIN Hide Widget Title -->
					<li>

						<!-- BEGIN Form Field -->
						<input type="checkbox" id="<?php echo $this->get_field_id( 'widget_title_hide' ); ?>" name="<?php echo $this->get_field_name( 'widget_title_hide' ); ?>" value="yes" <?php checked( $instance['widget_title_hide'], 'yes' ); ?> />
						<!-- END Form Field -->

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'widget_title_hide' ); ?>" class="srp-widget-label-inline">
							<?php _e( 'Hide Widget Title', SRP_TRANSLATION_ID ); ?>
						</label><br />
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Check this box if you want to hide the widget title.', SRP_TRANSLATION_ID ); ?>
						</small>
						<!-- END Description -->

					</li>
					<!-- END Hide Widget Title -->

					<!-- BEGIN Post Title HTML Header -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_title_header' ); ?>" class="srp-widget-label">
							<?php _e( 'Post Title HTML Header', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Select the type of HTML header to be used to enclose the post title:', SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<select id="<?php echo $this->get_field_id( 'post_title_header' ); ?>" name="<?php echo $this->get_field_name( 'post_title_header' ); ?>" class="srp-widget-select">
							<option value="h1" <?php selected( $instance['post_title_header'], 'h1' ); ?>>H1</option>
							<option value="h2" <?php selected( $instance['post_title_header'], 'h2' ); ?>>H2</option>
							<option value="h3" <?php selected( $instance['post_title_header'], 'h3' ); ?>>H3</option>
							<option value="h4" <?php selected( $instance['post_title_header'], 'h4' ); ?>>H4 <?php _e('(default)', SRP_TRANSLATION_ID ); ?></option>
							<option value="h5" <?php selected( $instance['post_title_header'], 'h5' ); ?>>H5</option>
							<option value="h6" <?php selected( $instance['post_title_header'], 'h6' ); ?>>H6</option>
						</select>
						<!-- END Form Field -->

					</li>
					<!-- END Post Title HTML Header -->

					<!-- BEGIN Post Content Display Mode -->
					<li>

						<!-- BEGIN Label -->
						<label for="<?php echo $this->get_field_id( 'post_content_mode' ); ?>" class="srp-widget-label">
							<?php _e( 'Post Content Display Mode', SRP_TRANSLATION_ID ); ?>
						</label>
						<!-- END Label -->

						<!-- BEGIN Description -->
						<small>
							<?php _e( 'Select the content type that should appear on each post:', SRP_TRANSLATION_ID ); ?>
						</small><br />
						<!-- END Description -->

						<!-- BEGIN Form Field -->
						<select id="<?php echo $this->get_field_id( 'post_content_mode' ); ?>" name="<?php echo $this->get_field_name( 'post_content_mode' ); ?>" class="srp-widget-select">

							<option value="thumbonly" <?php selected( $instance['post_content_mode'], 'thumbonly' ); ?>>
								<?php _e( 'Thumbnail Only', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="titleonly" <?php selected( $instance['post_content_mode'], 'titleonly' ); ?>>
								<?php _e( 'Title + Thumbnail', SRP_TRANSLATION_ID ); ?>
							</option>

							<option value="titleexcerpt" <?php selected( $instance['post_content_mode'], 'titleexcerpt' ); ?>>
								<?php _e( 'Title + Thumbnail + Post Content', SRP_TRANSLATION_ID ); ?>
							</option>
							
						</select>
						<!-- END Form Field -->

					</li>
					<!-- END Post Content Display Mode -->

				</ul>
				<!-- END Layout Options List -->

			</dd>
			<!-- END Layout Options Content -->
			
			<!-- BEGIN Credits Options Tab -->
			<dt class="srp-widget-optionlist-dt-credits">
				<a class="srp-wdg-accordion-item" href="#9" title="<?php esc_attr_e( 'Credits', SRP_TRANSLATION_ID ); ?>">
					<?php _e( 'Credits', SRP_TRANSLATION_ID ); ?>
				</a>
			</dt>
			<!-- END Credits Options Tab -->

			<!-- BEGIN Credits Options Content -->
			<dd class="srp-widget-optionlist-dd-credits">

				<!-- BEGIN Credits Options List -->
				<ul class="srp-widget-optionlist-credits srp-widget-optionlist">
					
					<!-- BEGIN Credits Text -->
					<li>

						<p>
							<?php printf( __( 'The Special Recent Posts plugin is created, developed and supported by %1$sLuca Grandicelli%2$s', SRP_TRANSLATION_ID ), '<a href="http://www.lucagrandicelli.co.uk/?ref=author_w" title="Luca Grandicelli | Official Website" target="_blank">', '</a>' ); ?>
						</p>

						<ul class="srp-widget-credits-list">
							
							<li>
								<strong><?php _e( 'Plugin Version:', SRP_TRANSLATION_ID ); ?></strong>
								<br />
								<?php _e( SRP_PLUGIN_VERSION ); ?>
							</li>

							<li>
								<strong><?php _e( 'Latest update:', SRP_TRANSLATION_ID); ?></strong>
								<br />
								<?php _e( 'September 27, 2014', SRP_TRANSLATION_ID ); ?>
							</li>
							
							<li>
								<strong><?php _e( 'Website:', SRP_TRANSLATION_ID ); ?></strong>
								<br />
								<?php printf( '<a href="%1$s" title="%2$s" target="_blank">http://www.specialrecentposts.com/</a>', esc_url( 'http://www.specialrecentposts.com/?ref=uri_w' ), __( 'The Special Recent Posts Official Website.', SRP_TRANSLATION_ID ) );?>
							</li>

							<li>
								<strong><?php _e( 'Customer Support:', SRP_TRANSLATION_ID ); ?></strong>
								<br />
								<?php printf( '<a href="%1$s" title="%2$s" target="_blank">http://wordpress.org/support/plugin/special-recent-posts/</a>', esc_url( 'http://wordpress.org/support/plugin/special-recent-posts/' ), __( 'Visit the online Wordpress.org forum to get instant support.', SRP_TRANSLATION_ID ) );?>
							</li>

							<li>
								<strong><?php _e('Online Documentation & F.A.Q:', SRP_TRANSLATION_ID); ?></strong>
								<br />
								<?php printf( '<a href="%1$s" title="%2$s" target="_blank">http://www.specialrecentposts.com/docs/</a>', esc_url( 'http://www.specialrecentposts.com/docs/?ref=docs_w' ), __( 'Learn how to use SRP. View the online documentation.', SRP_TRANSLATION_ID ) );?>
							</li>

							<li>
								<strong><?php _e( 'Follow Special Recent Posts on:', SRP_TRANSLATION_ID ); ?></strong>
								<br />
								
								<ul class="srp-social-list">

									<li>
										<a class="srp-social-icon-facebook" href="https://www.facebook.com/SpecialRecentPosts/" title="<?php echo esc_attr( __( 'Follow SRP on Facebook', SRP_TRANSLATION_ID ) ); ?>" target="_blank"></a>
									</li>

									<li>
										<a class="srp-social-icon-twitter" href="https://twitter.com/lucagrandicelli" title="<?php echo esc_attr( __( 'Follow Luca Grandicelli on Twitter', SRP_TRANSLATION_ID ) ); ?>" target="_blank"></a>
									</li>

									<li>
										<a class="srp-social-icon-googlep" href="https://google.com/+Specialrecentposts" title="<?php echo esc_attr( __( 'Follow SRP on Google+', SRP_TRANSLATION_ID ) ); ?>" target="_blank"></a>
									</li>

									<li>
										<a class="srp-social-icon-envato" href="http://codecanyon.net/user/lucagrandicelli/?ref=lucagrandicelli" title="<?php echo esc_attr( __( 'Follow Luca Grandicelli on Envato', SRP_TRANSLATION_ID ) ); ?>" target="_blank"></a>
									</li>
									
								</ul>

							</li>
							
						</ul>
					</li>
					<!-- END Credits Text -->

				</ul>
				<!-- END Credits Options List -->

			</dd>
			<!-- END Credits Options Content -->

		</dl>
		<!-- EOF Widget Accordion -->
<?php

		// Returning true.
		return true;
	}
}