<?php

class Slider_Widget extends WP_Widget {

	
	function __construct() {
		parent::__construct(
			'Slider_Widget', // Base ID
			__('EnjoyInstagram - Carousel', 'text_domain'), // Name
			array( 'description' => __( 'A Foo Widget', 'text_domain' ), ) // Args
		);
	}
	
	

	
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$number_images_in_slide = apply_filters( 'widget_content', $instance['number_images_in_slide'] );
		$navigation_y_n = apply_filters( 'widget_content', $instance['navigation_y_n'] );
		$user_or_hashtag = apply_filters( 'widget_content', $instance['user_or_hashtag'] );


		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		do_shortcode('[enjoyinstagram_mb_widget n="'.$instance['number_images_in_slide'].'" id="'.$args['widget_id'].'" n_y_n="'.$instance['navigation_y_n'].'" u_or_h="'.$instance['user_or_hashtag'].'"]');
		echo $args['after_widget'];
	}
	
	

	
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		
		$instance = wp_parse_args( (array) $instance, array( 
		'number_images_in_slide' => '4',
		'navigation_y_n' => 'false',
		'user_or_hashtag' => 'user'
		
		 ) );
		
		?>
        
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
        
        <p>
		<label for="<?php echo $this->get_field_id( 'number_images_in_slide' ); ?>"><?php _e( 'Images displayed at a time:' ); ?></label><br /> 
        <select name="<?php echo $this->get_field_name( 'number_images_in_slide' ); ?>" id="<?php echo $this->get_field_id( 'number_images_in_slide' ); ?>">
<option value="1" <?php if ($instance['number_images_in_slide']=='1') echo "selected='selected'";?>>1</option>
<option value="2" <?php if ($instance['number_images_in_slide']=='2') echo "selected='selected'";?>>2</option>
<option value="3" <?php if ($instance['number_images_in_slide']=='3') echo "selected='selected'";?>>3</option>
<option value="4" <?php if ($instance['number_images_in_slide']=='4') echo "selected='selected'";?>>4</option>
<option value="5" <?php if ($instance['number_images_in_slide']=='5') echo "selected='selected'";?>>5</option>
<option value="6" <?php if ($instance['number_images_in_slide']=='6') echo "selected='selected'";?>>6</option>
<option value="7" <?php if ($instance['number_images_in_slide']=='7') echo "selected='selected'";?>>7</option>
<option value="8" <?php if ($instance['number_images_in_slide']=='8') echo "selected='selected'";?>>8</option>
<option value="9" <?php if ($instance['number_images_in_slide']=='9') echo "selected='selected'";?>>9</option>
<option value="10" <?php if ($instance['number_images_in_slide']=='10') echo "selected='selected'";?>>10</option>
</select>
		
		</p>
        
        <p>
		<label for="<?php echo $this->get_field_id( 'navigation_y_n' ); ?>"><?php _e( 'Navigation buttons:' ); ?></label><br /> 
        <select name="<?php echo $this->get_field_name( 'navigation_y_n' ); ?>" id="<?php echo $this->get_field_id( 'navigation_y_n' ); ?>">
<option value="true" <?php if ($instance['navigation_y_n']=='true') echo "selected='selected'";?>>Yes</option>
<option value="false" <?php if ($instance['navigation_y_n']=='false') echo "selected='selected'";?>>No</option>
</select>
		
		</p>
        
        <p>
        Show pics: <br />
<input type="radio" name="<?php echo $this->get_field_name( 'user_or_hashtag' ); ?>" <?php if ($instance['user_or_hashtag']=='user') echo "checked";?> value="user">of Your Profile<br></p>
<p>
<input type="radio" name="<?php echo $this->get_field_name( 'user_or_hashtag' ); ?>" <?php if ($instance['user_or_hashtag']=='hashtag') echo "checked";?> value="hashtag">by Hashtag
              <br /></p>
              <!--
# <input type="text" id="enjoyinstagram_hashtag" value="<?php echo get_option('enjoyinstagram_hashtag'); ?>" name="enjoyinstagram_hashtag" disabled/>
                            <span class="description"></span>
                            -->
      
		<?php 
	}

	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['number_images_in_slide'] = ( ! empty( $new_instance['number_images_in_slide'] ) ) ? strip_tags( $new_instance['number_images_in_slide'] ) : '';
		$instance['navigation_y_n'] = ( ! empty( $new_instance['navigation_y_n'] ) ) ? strip_tags( $new_instance['navigation_y_n'] ) : '';
		$instance['user_or_hashtag'] = ( ! empty( $new_instance['user_or_hashtag'] ) ) ? strip_tags( $new_instance['user_or_hashtag'] ) : '';

		return $instance;
	}

} 

function register_Slider_Widget() {
    register_widget( 'Slider_Widget' );
}
add_action( 'widgets_init', 'register_Slider_Widget' );



?>