<?php


class Grid_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'Grid_Widget', // Base ID
			__('EnjoyInstagram - Grid', 'text_domain'), // Name
			array( 'description' => __( 'A Foo Widget', 'text_domain' ), ) // Args
		);
	}
	
	

	
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$number_cols_in_grid = apply_filters( 'widget_content', $instance['number_cols_in_grid'] );
		$number_rows_in_grid = apply_filters( 'widget_content', $instance['number_rows_in_grid'] );
		$user_or_hashtag_in_grid = apply_filters( 'widget_content', $instance['user_or_hashtag_in_grid'] );



		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
do_shortcode('[enjoyinstagram_mb_grid_widget id="'.$args['widget_id'].'" n_c="'.$instance['number_cols_in_grid'].'" n_r="'.$instance['number_rows_in_grid'].'" u_or_h="'.$instance['user_or_hashtag_in_grid'].'"]');
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
		'number_cols_in_grid' => '4',
		'number_rows_in_grid' => '2',
		'user_or_hashtag_in_grid' => 'user'
		
		 ) );
		
		?>
        <p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
        <p>
		<label for="<?php echo $this->get_field_id( 'number_cols_in_grid' ); ?>"><?php _e( 'Number of Columns:' ); ?></label><br /> 
        <select name="<?php echo $this->get_field_name( 'number_cols_in_grid' ); ?>" id="<?php echo $this->get_field_id( 'number_cols_in_grid' ); ?>">
<option value="1" <?php if ($instance['number_cols_in_grid']=='1') echo "selected='selected'";?>>1</option>
<option value="2" <?php if ($instance['number_cols_in_grid']=='2') echo "selected='selected'";?>>2</option>
<option value="3" <?php if ($instance['number_cols_in_grid']=='3') echo "selected='selected'";?>>3</option>
<option value="4" <?php if ($instance['number_cols_in_grid']=='4') echo "selected='selected'";?>>4</option>
<option value="5" <?php if ($instance['number_cols_in_grid']=='5') echo "selected='selected'";?>>5</option>
<option value="6" <?php if ($instance['number_cols_in_grid']=='6') echo "selected='selected'";?>>6</option>
<option value="7" <?php if ($instance['number_cols_in_grid']=='7') echo "selected='selected'";?>>7</option>
<option value="8" <?php if ($instance['number_cols_in_grid']=='8') echo "selected='selected'";?>>8</option>
<option value="9" <?php if ($instance['number_cols_in_grid']=='9') echo "selected='selected'";?>>9</option>
<option value="10" <?php if ($instance['number_cols_in_grid']=='10') echo "selected='selected'";?>>10</option>
</select>
		
		</p>
        <p>
		<label for="<?php echo $this->get_field_id( 'number_rows_in_grid' ); ?>"><?php _e( 'Number of Rows:' ); ?></label><br /> 
        <select name="<?php echo $this->get_field_name( 'number_rows_in_grid' ); ?>" id="<?php echo $this->get_field_id( 'number_rows_in_grid' ); ?>">
<option value="1" <?php if ($instance['number_rows_in_grid']=='1') echo "selected='selected'";?>>1</option>
<option value="2" <?php if ($instance['number_rows_in_grid']=='2') echo "selected='selected'";?>>2</option>
<option value="3" <?php if ($instance['number_rows_in_grid']=='3') echo "selected='selected'";?>>3</option>
<option value="4" <?php if ($instance['number_rows_in_grid']=='4') echo "selected='selected'";?>>4</option>
<option value="5" <?php if ($instance['number_rows_in_grid']=='5') echo "selected='selected'";?>>5</option>
<option value="6" <?php if ($instance['number_rows_in_grid']=='6') echo "selected='selected'";?>>6</option>
<option value="7" <?php if ($instance['number_rows_in_grid']=='7') echo "selected='selected'";?>>7</option>
<option value="8" <?php if ($instance['number_rows_in_grid']=='8') echo "selected='selected'";?>>8</option>
<option value="9" <?php if ($instance['number_rows_in_grid']=='9') echo "selected='selected'";?>>9</option>
<option value="10" <?php if ($instance['number_rows_in_grid']=='10') echo "selected='selected'";?>>10</option>
</select>
		
		</p>
          <p>
          Show pics:
          <br />
<input type="radio" name="<?php echo $this->get_field_name( 'user_or_hashtag_in_grid' ); ?>" <?php if ($instance['user_or_hashtag_in_grid']=='user') echo "checked";?> value="user">of Your Profile<br></p>
<p>
<input type="radio" name="<?php echo $this->get_field_name( 'user_or_hashtag_in_grid' ); ?>" <?php if ($instance['user_or_hashtag_in_grid']=='hashtag') echo "checked";?> value="hashtag">by Hashtag
              <br /></p>
             
		<?php 
	}

	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['number_cols_in_grid'] = ( ! empty( $new_instance['number_cols_in_grid'] ) ) ? strip_tags( $new_instance['number_cols_in_grid'] ) : '';
		$instance['number_rows_in_grid'] = ( ! empty( $new_instance['number_rows_in_grid'] ) ) ? strip_tags( $new_instance['number_rows_in_grid'] ) : '';
		$instance['user_or_hashtag_in_grid'] = ( ! empty( $new_instance['user_or_hashtag_in_grid'] ) ) ? strip_tags( $new_instance['user_or_hashtag_in_grid'] ) : '';

		return $instance;
	}

} 

function register_Grid_Widget() {
    register_widget( 'Grid_Widget' );
}
add_action( 'widgets_init', 'register_Grid_Widget' );



?>