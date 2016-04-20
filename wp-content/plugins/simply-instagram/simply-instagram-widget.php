<?php
/**
* Showing self feed in
* widget. 
*/
class instagram_self_feed extends WP_Widget {
	/** constructor */
	function __construct() {
		parent::__construct( /* Base ID */'instagram_self_feed', /* Name */__('Simply Instagram: Latest Feed', 'simply-instagram'), array( 'description' => __('Display your latest feeds. This will display your photo and includes media of people you\'re following.', 'simply-instagram') ) );
	}
	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$widget_title = $instance['widget_title'];
		$short_desc = $instance[ 'short_desc' ];
		$max_display = $instance[ 'max_display' ];
		
		echo $before_widget;
		echo $before_title . $widget_title . $after_title;
		echo '<p>' . $short_desc . '</p>' . '<div class="clear"></div>';
		
		echo sInstShowWidgetData( sInstGetSelfFeed( access_token() ), $max_display, $instance[ 'size' ], "sIntSelfFeed", $instance['display_caption'], $instance['open_instagram'] );
		
?>
	<script type="text/javascript" charset="utf-8">
	  jQuery(document).ready(function(){
	    jQuery("a[rel^='sIntSelfFeed']").prettyPhoto({
	    	autoplay_slideshow: <?php echo $instance[ 'auto_play' ];?>,
	    	social_tools: false,
	    	theme: '<?php echo $instance[ 'theme' ];?>',
	    	});
	    jQuery('.si-tooltip').tooltipster();
	  });
	</script>
<?php		
		echo $after_widget;
	}
	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['widget_title'] = strip_tags($new_instance['widget_title']);
		$instance['short_desc'] = strip_tags($new_instance['short_desc']);
		$instance['open_instagram'] = strip_tags($new_instance['open_instagram']);
		$instance['max_display'] = strip_tags($new_instance['max_display']);
		$instance['theme'] = strip_tags($new_instance['theme']);
		$instance['auto_play'] = strip_tags($new_instance['auto_play']);
		$instance['size'] = strip_tags($new_instance['size']);
		$instance['display_caption'] = strip_tags($new_instance['display_caption']);
		
		return $instance;
	}
	/** @see WP_Widget::form */
	function form( $instance ) {
		
		if ( $instance ) {
			$widget_title = esc_attr( $instance[ 'widget_title' ] );
			$short_desc = esc_attr( $instance[ 'short_desc' ] );
			$open_instagram = esc_attr( $instance[ 'open_instagram' ] );
			$max_display = esc_attr( $instance[ 'max_display' ] );
			$theme = esc_attr( $instance[ 'theme' ] );
			$auto_play = esc_attr( $instance[ 'auto_play' ] );
			$size = esc_attr( $instance[ 'size' ] );
			$display_caption = esc_attr( $instance[ 'display_caption' ] );
		}
		else {
			$widget_title = __( 'My Feed', 'text_domain' );
			$short_desc = __( 'My latest feed', 'short_desc' );
			$open_instagram = __( 'false', 'open_instagram' );
			$max_display = __( '9', 'max_display' );
			$theme = __( 'default', 'theme' );
			$auto_play = __( 'true', 'auto_play' );
			$size = __( '125', 'size' );
			$display_caption = __( 'true', 'display_caption' );
		}
		
		?>
		
		<p>
		<label for="<?php echo $this->get_field_id('widget_title'); ?>"><?php _e('Widget Title:', 'simply-instagram'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('widget_title'); ?>" name="<?php echo $this->get_field_name('widget_title'); ?>" type="text" value="<?php echo $widget_title; ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('short_desc'); ?>"><?php _e('Short Description:', 'simply-instagram'); ?></label> 
		<textarea rows="10" class="widefat" id="<?php echo $this->get_field_id('short_desc'); ?>" name="<?php echo $this->get_field_name('short_desc'); ?>"><?php echo $short_desc; ?></textarea>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('open_instagram'); ?>"><?php _e('View at Instagram:', 'simply-instagram'); ?></label>		
		<select class="widefat" id="<?php echo $this->get_field_id('open_instagram'); ?>" name="<?php echo $this->get_field_name('open_instagram'); ?>">
		 <option value="true" <?php selected( $open_instagram, "true" ); ?>><?php echo __('Yes', 'simply-instagram'); ?></option>
		 <option value="false" <?php selected( $open_instagram, "false" ); ?>><?php echo __('No', 'simply-instagram'); ?></option>
		</select>
		<span style="font-style: italic; font-size: 11px;"><?php echo __('Photo will be open in Instagram on new tab.', 'simply-instagram'); ?></span>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('max_display'); ?>"><?php _e('No. of Photos:', 'simply-instagram'); ?></label> 
		<input class="widefat" class="widefat" id="<?php echo $this->get_field_id('max_display'); ?>" name="<?php echo $this->get_field_name('max_display'); ?>" type="text" value="<?php echo $max_display; ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('display_caption'); ?>"><?php _e('Display Photo Caption:', 'simply-instagram'); ?></label>		
		<select class="widefat" id="<?php echo $this->get_field_id('display_caption'); ?>" name="<?php echo $this->get_field_name('display_caption'); ?>">
		 <option value="true" <?php selected( $display_caption, "true" ); ?>><?php echo __('Yes', 'simply-instagram'); ?></option>
		 <option value="false" <?php selected( $display_caption, "false" ); ?>><?php echo __('No', 'simply-instagram'); ?></option>
		</select>
		<span style="font-style: italic; font-size: 11px;"><?php echo __('prettyPhoto sometimes unresponsive on long photo description and this is the major drawback in previous version of Simply Instagram. Turn this feature off when it does.', 'simply-instagram'); ?></span>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('theme'); ?>"><?php _e('Theme:', 'simply-instagram'); ?></label> 
		<select class="widefat" id="<?php echo $this->get_field_id('theme'); ?>" name="<?php echo $this->get_field_name('theme'); ?>">	
		 <option value="pp_default" <?php selected( $theme, "pp_default" ); ?>>Default</option>
		 <option value="facebook" <?php selected( $theme, "facebook" ); ?>>Facebook</option>
		 <option value="dark_rounded" <?php selected( $theme, "dark_rounded" ); ?>>Dark Round</option>
		 <option value="dark_square" <?php selected( $theme, "dark_square" ); ?>>Dark Square</option>
		 <option value="light_rounded" <?php selected( $theme, "light_rounded" ); ?>>Light Round</option>
		 <option value="light_square" <?php selected( $theme, "light_square" ); ?>>Light Square</option>
		</select>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('auto_play'); ?>"><?php _e('Auto Play Slideshow:', 'simply-instagram'); ?></label> 
		<select class="widefat" id="<?php echo $this->get_field_id('auto_play'); ?>" name="<?php echo $this->get_field_name('auto_play'); ?>">
		 <option value="true" <?php selected( $auto_play, "true" ); ?>><?php echo __('Yes', 'simply-instagram'); ?></option>
		 <option value="false" <?php selected( $auto_play, "false" ); ?>><?php echo __('No', 'simply-instagram'); ?></option>
		</select>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Picture Size:', 'simply-instagram'); ?></label> 
		<input class="widefat" class="widefat" id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>" type="text" value="<?php echo $size; ?>" />
		</p>
		
		<?php		
	}
}

/**
 * User widget
*/
class instagram_user_info extends WP_Widget {
	/** constructor */
	function __construct() {
		parent::__construct( /* Base ID */'instagram_user_info', /* Name */__('Simply Instagram: User Info', 'simply-instagram'), array( 'description' => __('Display user info.', 'simply-instagram') ) );
	}
	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$short_desc = '';
		echo $before_widget;
		echo $before_title . $instance['widget_title'] . $after_title;
		echo '<p>' . $short_desc . '</p>';
		
		$info = sInstGetInfo( user_id(), access_token() );
				
		sInstShowInfo( $info, array( 'name' => 'true', 'bio' => 'true', 'website' => 'true', 'media' => 'true', 'followers' => 'true', 'following' => 'true', 'profile_pic' => 'true' ), $instance['profile_width'] );
		
		if( $instance['followers'] == true ):
			if( $instance['followers_desc'] ):
				echo "<p>" . $instance['followers_desc'] . "</p>";
			else:
				echo '<div class="clear"></div>';
			endif;
			
			$fllwrsdata = sInstDiplayFollowData( sInstGetFollowers( user_id(), access_token() , array( 'name' => 'true', 'bio' => 'true', 'website' => 'true', 'media' => 'true', 'followers' => 'true', 'following' => 'true', 'profile_pic' => 'true' ) ), "25", $instance['followers_profile_width'], false );
						
			?>
				<script type="text/javascript" charset="utf-8">
				  //jQuery.noConflict();
				  jQuery(document).ready(function(){
				     jQuery('.si-tooltip').tooltipster();
				  });
				</script>
			<?php
		endif;
		
		if( $instance['following'] == true ):
			if( $instance['following_desc'] ):
				echo "<p>" . $instance['following_desc'] . "</p>";
			else:
				echo '<div class="clear"></div>';
			endif;
			sInstDiplayFollowData( sInstGetFollowing( user_id(), access_token() , array( 'name' => 'true', 'bio' => 'true', 'website' => 'true', 'media' => 'true', 'followers' => 'true', 'following' => 'true', 'profile_pic' => 'true' ) ), "25", $instance['followers_profile_width'], false );
			?>
				<script type="text/javascript" charset="utf-8">
				  //jQuery.noConflict();
				  jQuery(document).ready(function(){
				     jQuery('.si-tooltip').tooltipster();
				  });
				</script>
			<?php
		endif;
		
		if( $instance['latest_feed'] == true ):
			if( $instance['latest_feed_desc'] ):
				echo "<p>" . $instance['latest_feed_desc'] . "</p>";
			else:
				echo '<div class="clear"></div>';
			endif;
			$customRel = "latestfeed";
			sInstShowWidgetData( sInstGetSelfFeed( access_token() ), "25", $instance['latest_photo_profile_width'], $customRel, $instance['display_caption'], $instance['open_instagram'] );
			?>
				<script type="text/javascript" charset="utf-8">
				  //jQuery.noConflict();
				  jQuery(document).ready(function(){
				    jQuery("a[rel^='<?php echo $customRel; ?>']").prettyPhoto({
				    	autoplay_slideshow: 'true',
				    	social_tools:false,
				    	theme: 'pp_default',
				    	});
				     jQuery('.si-tooltip').tooltipster();
				  });
				</script>
			<?php
		endif;
		
		if( $instance['latest_photo'] == true ):
			if( $instance['latest_photo_desc'] ):
				echo "<p>" . $instance['latest_photo_desc'] . "</p>";
			else:
				echo '<div class="clear"></div>';
			endif;
			$customRel = "latestphoto";			
			sInstShowWidgetData( sInstGetRecentMedia( user_id(), access_token() ), "25", $instance['latest_photo_profile_width'], $customRel, $instance['display_caption'], $instance['open_instagram'] );
			?>
				<script type="text/javascript" charset="utf-8">
				  jQuery(document).ready(function(){
				    jQuery("a[rel^='<?php echo $customRel; ?>']").prettyPhoto({
				    	autoplay_slideshow: 'true',
				    	social_tools:false,
				    	theme: 'pp_default',
				    	});
				     jQuery('.si-tooltip').tooltipster();
				  });
				</script>
			<?php
		endif;
		
		if( $instance['liked_photo'] == true ):
			if( $instance['liked_photo_desc'] ):
				echo "<p>" . $instance['liked_photo_desc'] . "</p>";
			else:
				echo '<div class="clear"></div>';
			endif;
			$customRel = "likedphoto";			
			sInstShowWidgetData( sInstGetLikes( access_token() ), "25", $instance['liked_photo_width'], $customRel, $instance['display_caption'], $instance['open_instagram'] );
			?>
				<script type="text/javascript" charset="utf-8">
				  jQuery(document).ready(function(){
				    jQuery("a[rel^='<?php echo $customRel; ?>']").prettyPhoto({
				    	autoplay_slideshow: 'true',
				    	social_tools:false,
				    	theme: 'pp_default',
				    	});
				     jQuery('.si-tooltip').tooltipster();
				  });
				</script>
			<?php
		endif;
				
		echo $after_widget;
	}
	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['widget_title'] = strip_tags($new_instance['widget_title']);
		$instance['profile_width'] = strip_tags($new_instance['profile_width']);
		$instance['open_instagram'] = strip_tags($new_instance['open_instagram']);
		
		$instance['followers'] = strip_tags($new_instance['followers']);
		$instance['followers_desc'] = strip_tags($new_instance['followers_desc']);
		$instance['followers_profile_width'] = strip_tags($new_instance['followers_profile_width']);
		
		$instance['following'] = strip_tags($new_instance['following']);
		$instance['following_profile_width'] = strip_tags($new_instance['following_profile_width']);
		$instance['following_desc'] = strip_tags($new_instance['following_desc']);
		
		$instance['latest_feed'] = strip_tags($new_instance['latest_feed']);
		$instance['latest_feed_desc'] = strip_tags($new_instance['latest_feed_desc']);
		$instance['latest_feed_desc_profile_width'] = strip_tags($new_instance['latest_feed_desc_profile_width']);
		
		$instance['latest_photo'] = strip_tags($new_instance['latest_photo']);
		$instance['latest_photo_desc'] = strip_tags($new_instance['latest_photo_desc']);
		$instance['latest_photo_profile_width'] = strip_tags($new_instance['latest_photo_profile_width']);
		
		$instance['liked_photo'] = strip_tags($new_instance['liked_photo']);
		$instance['liked_photo_desc'] = strip_tags($new_instance['liked_photo_desc']);
		$instance['liked_photo_width'] = strip_tags($new_instance['liked_photo_width']);
		
		$instance['display_caption'] = strip_tags($new_instance['display_caption']);
		
		return $instance;
	}
	/** @see WP_Widget::form */
	function form( $instance ) {
		
		if ( $instance ) {
			$widget_title = esc_attr( $instance[ 'widget_title' ] );
			$profile_width = esc_attr( $instance[ 'profile_width' ] );
			$open_instagram = esc_attr( $instance[ 'open_instagram' ] );
			
			$followers = ( !empty( $instance['followers'] ) ) ? esc_attr( $instance[ 'followers' ] ) : false;
			$followers_desc = esc_attr( $instance[ 'followers_desc' ] );
			$followers_profile_width = esc_attr( $instance[ 'followers_profile_width' ] );
			
			$following = esc_attr( $instance[ 'following' ] );
			$following_profile_width = esc_attr( $instance[ 'following_profile_width' ] );
			$following_desc = esc_attr( $instance[ 'following_desc' ] );
			
			$latest_feed = esc_attr( $instance[ 'latest_feed' ] );
			$latest_feed_desc = esc_attr( $instance[ 'latest_feed_desc' ] );
			$latest_feed_desc_profile_width = esc_attr( $instance[ 'latest_feed_desc_profile_width' ] );
			
			$latest_photo = esc_attr( $instance[ 'latest_photo' ] );
			$latest_photo_desc = esc_attr( $instance[ 'latest_photo_desc' ] );
			$latest_photo_profile_width = esc_attr( $instance[ 'latest_photo_profile_width' ] );
			
			$liked_photo = esc_attr( $instance[ 'liked_photo' ] );
			$liked_photo_desc = esc_attr( $instance[ 'liked_photo_desc' ] );
			$liked_photo_width = esc_attr( $instance[ 'liked_photo_width' ] );
			
			$display_caption = esc_attr( $instance[ 'display_caption' ] );
		}
		else {
			$widget_title = __( 'Instagram Info', 'text_domain' );
			$profile_width = __( '150', 'text_domain' );
			$open_instagram = __( 'false', 'open_instagram' );
			
			$followers = __( true, 'text_domain' );
			$followers_desc = __( 'My followers', 'text_domain' );
			$followers_profile_width = __( '50', 'text_domain' );
			
			$following = __( 'true', 'text_domain' );
			$following_profile_width = __( '50', 'text_domain' );
			$following_desc = __( 'I\'m following them', 'text_domain' );
			
			$latest_feed = __( 'true', 'text_domain' );
			$latest_feed_desc = __( 'My latest feed', 'text_domain' );
			$latest_feed_desc_profile_width = __( '50', 'text_domain' );
			
			$latest_photo = __( 'true', 'text_domain' );
			$latest_photo_desc = __( 'My latest uploads', 'text_domain' );
			$latest_photo_profile_width = __( '50', 'text_domain' );
			
			$liked_photo = __( 'true', 'text_domain' );
			$liked_photo_desc = __( 'Photos I love', 'text_domain' );
			$liked_photo_width = __( '50', 'text_domain' );
			
			$display_caption = __( 'true', 'display_caption' );
		}
		?>
		
		<p>
		<label for="<?php echo $this->get_field_id('widget_title'); ?>"><?php _e('Widget Title:', 'simply-instagram'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('widget_title'); ?>" name="<?php echo $this->get_field_name('widget_title'); ?>" type="text" value="<?php echo $widget_title; ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('profile_width'); ?>"><?php _e('Profile Picture Width:', 'simply-instagram'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('profile_width'); ?>" name="<?php echo $this->get_field_name('profile_width'); ?>" type="text" value="<?php echo $profile_width; ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('open_instagram'); ?>"><?php _e('View at Instagram:', 'simply-instagram'); ?></label>		
		<select class="widefat" id="<?php echo $this->get_field_id('open_instagram'); ?>" name="<?php echo $this->get_field_name('open_instagram'); ?>">
		 <option value="true" <?php selected( $open_instagram, "true" ); ?>><?php echo __('Yes', 'simply-instagram'); ?></option>
		 <option value="false" <?php selected( $open_instagram, "false" ); ?>><?php echo __('No', 'simply-instagram'); ?></option>
		</select>
		<span style="font-style: italic; font-size: 11px;">Photo will be open in Instagram on new tab.</span>
		</p>
		
		<p>
		<input class="checkbox" type="checkbox" <?php checked( (bool) $followers, true ); ?> id="<?php echo $this->get_field_id( 'followers' ); ?>" name="<?php echo $this->get_field_name( 'followers' ); ?>" />
		<label for="<?php echo $this->get_field_id('followers'); ?>"><?php _e('Include my followers thumbnail', 'simply-instagram'); ?></label> 
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('followers_desc'); ?>"><?php _e('Followers Description:', 'simply-instagram'); ?></label> 
		<textarea rows="10" class="widefat" id="<?php echo $this->get_field_id('followers_desc'); ?>" name="<?php echo $this->get_field_name('followers_desc'); ?>"><?php echo $followers_desc; ?></textarea>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('followers_profile_width'); ?>"><?php _e('Picture Width:', 'simply-instagram'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('followers_profile_width'); ?>" name="<?php echo $this->get_field_name('followers_profile_width'); ?>" type="text" value="<?php echo $followers_profile_width; ?>" />
		</p>
		
		<p>
		<input class="checkbox" type="checkbox" <?php checked( (bool) $following, true ); ?> id="<?php echo $this->get_field_id( 'following' ); ?>" name="<?php echo $this->get_field_name( 'following' ); ?>" />
		<label for="<?php echo $this->get_field_id('following'); ?>"><?php _e('Include thumbnail user I\'m following', 'simply-instagram'); ?></label> 
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('following_desc'); ?>"><?php _e('Description:', 'simply-instagram'); ?></label> 
		<textarea rows="10" class="widefat" id="<?php echo $this->get_field_id('following_desc'); ?>" name="<?php echo $this->get_field_name('following_desc'); ?>"><?php echo $following_desc; ?></textarea>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('following_profile_width'); ?>"><?php _e('Following Profile Width:', 'simply-instagram'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('following_profile_width'); ?>" name="<?php echo $this->get_field_name('following_profile_width'); ?>" type="text" value="<?php echo $following_profile_width; ?>" />
		</p>
		
		<p>
		<input class="checkbox" type="checkbox" <?php checked( (bool) $latest_feed, true ); ?> id="<?php echo $this->get_field_id( 'latest_feed' ); ?>" name="<?php echo $this->get_field_name( 'latest_feed' ); ?>" />
		<label for="<?php echo $this->get_field_id('latest_feed'); ?>"><?php _e('Include my latest feed', 'simply-instagram'); ?></label> 
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('latest_feed_desc'); ?>"><?php _e('Description:', 'simply-instagram'); ?></label> 
		<textarea rows="10" class="widefat" id="<?php echo $this->get_field_id('latest_feed_desc'); ?>" name="<?php echo $this->get_field_name('latest_feed_desc'); ?>"><?php echo $latest_feed_desc; ?></textarea>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('latest_feed_desc_profile_width'); ?>"><?php _e('Recent Feed Width:', 'simply-instagram'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('latest_feed_desc_profile_width'); ?>" name="<?php echo $this->get_field_name('latest_feed_desc_profile_width'); ?>" type="text" value="<?php echo $latest_feed_desc_profile_width; ?>" />
		</p>
		
		<p>
		<input class="checkbox" type="checkbox" <?php checked( (bool) $latest_photo, true ); ?> id="<?php echo $this->get_field_id( 'latest_photo' ); ?>" name="<?php echo $this->get_field_name( 'latest_photo' ); ?>" />
		<label for="<?php echo $this->get_field_id('latest_photo'); ?>"><?php _e('Include my recent media', 'simply-instagram'); ?></label> 
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('latest_photo_desc'); ?>"><?php _e('Description:', 'simply-instagram'); ?></label> 
		<textarea rows="10" class="widefat" id="<?php echo $this->get_field_id('latest_photo_desc'); ?>" name="<?php echo $this->get_field_name('latest_photo_desc'); ?>"><?php echo $latest_photo_desc; ?></textarea>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('latest_photo_profile_width'); ?>"><?php _e('Recent Media Width:', 'simply-instagram'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('latest_photo_profile_width'); ?>" name="<?php echo $this->get_field_name('latest_photo_profile_width'); ?>" type="text" value="<?php echo $latest_photo_profile_width; ?>" />
		</p>
		
		<p>
		<input class="checkbox" type="checkbox" <?php checked( (bool) $liked_photo, true ); ?> id="<?php echo $this->get_field_id( 'liked_photo' ); ?>" name="<?php echo $this->get_field_name( 'liked_photo' ); ?>" />
		<label for="<?php echo $this->get_field_id('liked_photo'); ?>"><?php _e('Include photos I liked', 'simply-instagram'); ?></label> 
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('liked_photo_desc'); ?>"><?php _e('Description:', 'simply-instagram'); ?></label> 
		<textarea rows="10" class="widefat" id="<?php echo $this->get_field_id('liked_photo_desc'); ?>" name="<?php echo $this->get_field_name('liked_photo_desc'); ?>"><?php echo $liked_photo_desc; ?></textarea>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('liked_photo_width'); ?>"><?php _e('Liked Photo Width:', 'simply-instagram'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('liked_photo_width'); ?>" name="<?php echo $this->get_field_name('liked_photo_width'); ?>" type="text" value="<?php echo $liked_photo_width; ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('display_caption'); ?>"><?php _e('Display Photo Caption:', 'simply-instagram'); ?></label>		
		<select class="widefat" id="<?php echo $this->get_field_id('display_caption'); ?>" name="<?php echo $this->get_field_name('display_caption'); ?>">
		 <option value="true" <?php selected( $display_caption, "true" ); ?>><?php echo __('Yes', 'simply-instagram'); ?></option>
		 <option value="false" <?php selected( $display_caption, "false" ); ?>><?php echo __('No', 'simply-instagram'); ?></option>
		</select>
		<span style="font-style: italic; font-size: 11px;"><?php echo __('prettyPhoto sometimes unresponsive on long photo description and this is the major drawback in previous version of Simply Instagram. Turn this feature off when it does.', 'simply-instagram'); ?></span>
		</p>
	
	<?php
	}
	
}

/**
* Showing currently most popular images through
* widget. 
*/
class instagram_most_popular extends WP_Widget {	
	/** constructor */
	function __construct() {
		parent::__construct( 
					'instagram_most_popular', /* Base ID */
					__('Simply Instagram: Currently Popular', 'simply-instagram'), /* Name */
					array( 
						'description' => __('Display currently popular photos in Instagram server.', 'simply-instagram') 
					)
				);
	}
	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$widget_title = $instance['widget_title'];
		$short_desc = $instance[ 'short_desc' ];
		
		echo $before_widget;
		echo $before_title . $widget_title . $after_title;
		echo '<p>' . $short_desc . '</p>' . '<div class="clear"></div>';
		
		echo sInstShowWidgetData( sInstGetMostPopular( "popular", access_token() ), $instance[ 'max_display' ], $instance[ 'size' ], "sInstMostPopular", $instance['display_caption'], $instance['open_instagram'] );
	?>
	<script type="text/javascript" charset="utf-8">
	  jQuery(document).ready(function(){
	    jQuery("a[rel^='sInstMostPopular']").prettyPhoto({
	    	autoplay_slideshow: <?php echo $instance[ 'auto_play' ];?>,
	    	social_tools:false,
	    	theme: '<?php echo $instance[ 'theme' ];?>',
	    	});
	    jQuery('.si-tooltip').tooltipster();
	  });
	</script>
	<?php	
		echo $after_widget;
	}
	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['widget_title'] = strip_tags($new_instance['widget_title']);
		$instance['short_desc'] = strip_tags($new_instance['short_desc']);
		$instance['open_instagram'] = strip_tags($new_instance['open_instagram']);
		$instance['max_display'] = strip_tags($new_instance['max_display']);
		$instance['theme'] = strip_tags($new_instance['theme']);
		$instance['auto_play'] = strip_tags($new_instance['auto_play']);
		$instance['size'] = strip_tags($new_instance['size']);
		$instance['display_caption'] = strip_tags($new_instance['display_caption']);
		
		return $instance;
	}
	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ) {
			$widget_title = esc_attr( $instance[ 'widget_title' ] );
			$short_desc = esc_attr( $instance[ 'short_desc' ] );
			$open_instagram = esc_attr( $instance[ 'open_instagram' ] );
			$max_display = esc_attr( $instance[ 'max_display' ] );
			$theme = esc_attr( $instance[ 'theme' ] );
			$auto_play = esc_attr( $instance[ 'auto_play' ] );
			$size = esc_attr( $instance[ 'size' ] );
			$display_caption = esc_attr( $instance[ 'display_caption' ] );
		}
		else {
			$widget_title = __( 'Currently Popular', 'text_domain' );
			$short_desc = __( 'These are currently popular', 'short_desc' );
			$open_instagram = __( 'false', 'open_instagram' );
			$max_display = __( '9', 'max_display' );
			$theme = __( 'default', 'theme' );
			$auto_play = __( 'true', 'auto_play' );
			$size = __( '125', 'size' );
			$display_caption = __( 'true', 'display_caption' );
		}
		?>
		
		<p>
		<label for="<?php echo $this->get_field_id('widget_title'); ?>"><?php _e('Widget Title:', 'simply-instagram'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('widget_title'); ?>" name="<?php echo $this->get_field_name('widget_title'); ?>" type="text" value="<?php echo $widget_title; ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('short_desc'); ?>"><?php _e('Short Description:', 'simply-instagram'); ?></label> 
		<textarea rows="10" class="widefat" id="<?php echo $this->get_field_id('short_desc'); ?>" name="<?php echo $this->get_field_name('short_desc'); ?>"><?php echo $short_desc; ?></textarea>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('open_instagram'); ?>"><?php _e('View at Instagram:', 'simply-instagram'); ?></label>		
		<select class="widefat" id="<?php echo $this->get_field_id('open_instagram'); ?>" name="<?php echo $this->get_field_name('open_instagram'); ?>">
		 <option value="true" <?php selected( $open_instagram, "true" ); ?>><?php echo __('Yes', 'simply-instagram'); ?></option>
		 <option value="false" <?php selected( $open_instagram, "false" ); ?>><?php echo __('No', 'simply-instagram'); ?></option>
		</select>
		<span style="font-style: italic; font-size: 11px;"><?php echo __('Photo will be open in Instagram on new tab.', 'simply-instagram'); ?></span>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('max_display'); ?>"><?php _e('No. of Photos:', 'simply-instagram'); ?></label> 
		<input class="widefat" class="widefat" id="<?php echo $this->get_field_id('max_display'); ?>" name="<?php echo $this->get_field_name('max_display'); ?>" type="text" value="<?php echo $max_display; ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('display_caption'); ?>"><?php _e('Display Photo Caption:', 'simply-instagram'); ?></label>		
		<select class="widefat" id="<?php echo $this->get_field_id('display_caption'); ?>" name="<?php echo $this->get_field_name('display_caption'); ?>">
		 <option value="true" <?php selected( $display_caption, "true" ); ?>><?php echo __('Yes', 'simply-instagram'); ?></option>
		 <option value="false" <?php selected( $display_caption, "false" ); ?>><?php echo __('No', 'simply-instagram'); ?></option>
		</select>
		<span style="font-style: italic; font-size: 11px;"><?php echo __('prettyPhoto sometimes unresponsive on long photo description and this is the major drawback in previous version of Simply Instagram. Turn this feature off when it does.', 'simply-instagram'); ?></span>
		</p>

		
		<p>
		<label for="<?php echo $this->get_field_id('theme'); ?>"><?php _e('Theme:', 'simply-instagram'); ?></label> 
		<select class="widefat" id="<?php echo $this->get_field_id('theme'); ?>" name="<?php echo $this->get_field_name('theme'); ?>">	
		 <option value="pp_default" <?php selected( $theme, "pp_default" ); ?>>Default</option>
		 <option value="facebook" <?php selected( $theme, "facebook" ); ?>>Facebook</option>
		 <option value="dark_rounded" <?php selected( $theme, "dark_rounded" ); ?>>Dark Round</option>
		 <option value="dark_square" <?php selected( $theme, "dark_square" ); ?>>Dark Square</option>
		 <option value="light_rounded" <?php selected( $theme, "light_rounded" ); ?>>Light Round</option>
		 <option value="light_square" <?php selected( $theme, "light_square" ); ?>>Light Square</option>
		</select>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('auto_play'); ?>"><?php _e('Auto Play Slideshow:', 'simply-instagram'); ?></label> 
		<select class="widefat" id="<?php echo $this->get_field_id('auto_play'); ?>" name="<?php echo $this->get_field_name('auto_play'); ?>">
		 <option value="true" <?php selected( $auto_play, "true" ); ?>><?php echo __('Yes', 'simply-instagram'); ?></option>
		 <option value="false" <?php selected( $auto_play, "false" ); ?>><?php echo __('No', 'simply-instagram'); ?></option>
		</select>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Picture Size:', 'simply-instagram'); ?></label> 
		<input class="widefat" class="widefat" id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>" type="text" value="<?php echo $size; ?>" />
		</p>
		
		<?php
	}
}
/**
* Showing recent media feed images through
* widget. 
*/
class instagram_recent_media extends WP_Widget {
	
	/** constructor */
	function __construct() {
		parent::__construct( /* Base ID */'instagram_recent_media', /* Name */__('Simply Instagram: My Latest Photo', 'simply-instagram'), array( 'description' => __('Display exclusively your latest uploaded photos.', 'simply-instagram') ) );
	}
	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$widget_title = $instance['widget_title'];
		$short_desc = $instance[ 'short_desc' ];
		
		echo $before_widget;
		echo $before_title . $widget_title . $after_title;
		echo '<p>' . $short_desc . '</p>' . '<div class="clear"></div>';
		
		echo sInstShowWidgetData( sInstGetRecentMedia( user_id(), access_token() ), $instance[ 'max_display' ], $instance[ 'size' ], "sInstRecentMediaWid", $instance['display_caption'], $instance['open_instagram'] );
	?>
	<script type="text/javascript" charset="utf-8">	  
	  jQuery(document).ready(function(){
	    jQuery("a[rel^='sInstRecentMediaWid']").prettyPhoto({
	    	autoplay_slideshow: <?php echo $instance[ 'auto_play' ];?>,
	    	social_tools:false,
	    	theme: '<?php echo $instance[ 'theme' ];?>',
	    	});
	    jQuery('.si-tooltip').tooltipster();
	  });
	</script>
	<?php	
		echo $after_widget;
	}
	
	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['widget_title'] = strip_tags($new_instance['widget_title']);
		$instance['short_desc'] = strip_tags($new_instance['short_desc']);
		$instance['open_instagram'] = strip_tags($new_instance['open_instagram']);
		$instance['max_display'] = strip_tags($new_instance['max_display']);
		$instance['theme'] = strip_tags($new_instance['theme']);
		$instance['auto_play'] = strip_tags($new_instance['auto_play']);
		$instance['size'] = strip_tags($new_instance['size']);
		$instance['display_caption'] = strip_tags($new_instance['display_caption']);
		
		return $instance;
	}
	
	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ) {
			$widget_title = esc_attr( $instance[ 'widget_title' ] );
			$short_desc = esc_attr( $instance[ 'short_desc' ] );
			$open_instagram = esc_attr( $instance[ 'open_instagram' ] );
			$max_display = esc_attr( $instance[ 'max_display' ] );
			$theme = esc_attr( $instance[ 'theme' ] );
			$auto_play = esc_attr( $instance[ 'auto_play' ] );
			$size = esc_attr( $instance[ 'size' ] );
			$display_caption = esc_attr( $instance[ 'display_caption' ] );
		}
		else {
			$widget_title = __( 'Recent Media', 'text_domain' );
			$short_desc = __( 'My Latest photos', 'short_desc' );
			$open_instagram = __( 'false', 'open_instagram' );
			$max_display = __( '9', 'max_display' );
			$theme = __( 'default', 'theme' );
			$auto_play = __( 'true', 'auto_play' );
			$size = __( '125', 'size' );
			$display_caption = __( 'true', 'display_caption' );
		}
		?>
		
		<p>
		<label for="<?php echo $this->get_field_id('widget_title'); ?>"><?php _e('Widget Title:', 'simply-instagram'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('widget_title'); ?>" name="<?php echo $this->get_field_name('widget_title'); ?>" type="text" value="<?php echo $widget_title; ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('short_desc'); ?>"><?php _e('Short Description:', 'simply-instagram'); ?></label> 
		<textarea rows="10" class="widefat" id="<?php echo $this->get_field_id('short_desc'); ?>" name="<?php echo $this->get_field_name('short_desc'); ?>"><?php echo $short_desc; ?></textarea>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('open_instagram'); ?>"><?php _e('View at Instagram:', 'simply-instagram'); ?></label>		
		<select class="widefat" id="<?php echo $this->get_field_id('open_instagram'); ?>" name="<?php echo $this->get_field_name('open_instagram'); ?>">
		 <option value="true" <?php selected( $open_instagram, "true" ); ?>><?php echo __('Yes', 'simply-instagram'); ?></option>
		 <option value="false" <?php selected( $open_instagram, "false" ); ?>><?php echo __('No', 'simply-instagram'); ?></option>
		</select>
		<span style="font-style: italic; font-size: 11px;"><?php echo __('Photo will be open in Instagram on new tab.', 'simply-instagram'); ?></span>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('max_display'); ?>"><?php _e('No. of Photos:', 'simply-instagram'); ?></label> 
		<input class="widefat" class="widefat" id="<?php echo $this->get_field_id('max_display'); ?>" name="<?php echo $this->get_field_name('max_display'); ?>" type="text" value="<?php echo $max_display; ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('display_caption'); ?>"><?php _e('Display Photo Caption:', 'simply-instagram'); ?></label>		
		<select class="widefat" id="<?php echo $this->get_field_id('display_caption'); ?>" name="<?php echo $this->get_field_name('display_caption'); ?>">
		 <option value="true" <?php selected( $display_caption, "true" ); ?>><?php echo __('Yes', 'simply-instagram'); ?></option>
		 <option value="false" <?php selected( $display_caption, "false" ); ?>><?php echo __('No', 'simply-instagram'); ?></option>
		</select>
		<span style="font-style: italic; font-size: 11px;"><?php echo __('prettyPhoto sometimes unresponsive on long photo description and this is the major drawback in previous version of Simply Instagram. Turn this feature off when it does.', 'simply-instagram'); ?></span>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('theme'); ?>"><?php _e('Theme:'); ?></label> 
		<select class="widefat" id="<?php echo $this->get_field_id('theme'); ?>" name="<?php echo $this->get_field_name('theme'); ?>">	
		 <option value="pp_default" <?php selected( $theme, "pp_default" ); ?>>Default</option>
		 <option value="facebook" <?php selected( $theme, "facebook" ); ?>>Facebook</option>
		 <option value="dark_rounded" <?php selected( $theme, "dark_rounded" ); ?>>Dark Round</option>
		 <option value="dark_square" <?php selected( $theme, "dark_square" ); ?>>Dark Square</option>
		 <option value="light_rounded" <?php selected( $theme, "light_rounded" ); ?>>Light Round</option>
		 <option value="light_square" <?php selected( $theme, "light_square" ); ?>>Light Square</option>
		</select>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('auto_play'); ?>"><?php _e('Auto Play Slideshow:', 'simply-instagram'); ?></label> 
		<select class="widefat" id="<?php echo $this->get_field_id('auto_play'); ?>" name="<?php echo $this->get_field_name('auto_play'); ?>">
		 <option value="true" <?php selected( $auto_play, "true" ); ?>><?php echo __('Yes', 'simply-instagram'); ?></option>
		 <option value="false" <?php selected( $auto_play, "false" ); ?>><?php echo __('No', 'simply-instagram'); ?></option>
		</select>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Picture Size:', 'simply-instagram'); ?></label> 
		<input class="widefat" class="widefat" id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>" type="text" value="<?php echo $size; ?>" />
		</p>
		
		<?php
	}
}	

/**
* Register the widget using hook.
*/
add_action( 'widgets_init', create_function( '', 'register_widget("instagram_most_popular");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("instagram_user_info");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("instagram_self_feed");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("instagram_recent_media");' ) );

?>