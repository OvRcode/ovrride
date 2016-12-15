<?php
/*
Plugin Name:    Widget Shortcode
Description:    Output widgets using a simple shortcode.
Author:         Hassan Derakhshandeh
Version:        0.3.0
Text Domain:    widget-shortcode
Domain Path:    /languages

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Widget_Shortcode {

	private static $instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return	A single instance of this class.
	 * @since 0.2.4
	 */
	public static function get_instance() {
		return null == self::$instance ? self::$instance = new self : self::$instance;
	}

	private function __construct() {
		add_shortcode( 'widget', array( $this, 'shortcode' ) );
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 5 );
		add_action( 'widgets_init', array( $this, 'arbitrary_sidebar' ), 20 );
		add_action( 'in_widget_form', array( $this, 'in_widget_form' ), 10, 3 );
		add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
		add_filter( 'mce_buttons', array( $this, 'mce_buttons' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'editor_parameters' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'editor_parameters' ) );
	}

	/**
	 * output a widget using 'widget' shortcode.
	 *
	 * Requires the widget ID.
	 * You can overwrite widget args: before_widget, before_title, after_title, after_widget
	 *
	 * @example [widget id="text-1"]
	 * @since 0.1
	 */
	public function shortcode( $atts, $content = null ) {
		$atts['echo'] = false;
		return $this->do_widget( $atts );
	}

	/**
	 * Registers arbitrary widget area
	 *
	 * Although you can use the widget shortcode for any widget in any widget area,
	 * you can use this arbitrary widget area for your widgets, since they don't show up
	 * in the front-end.
	 *
	 * @since 0.1
	 * @return void
	 */
	function arbitrary_sidebar() {
		register_sidebar( array(
			'name' => __( 'Arbitrary', 'widget-shortcode' ),
			'description'	=> __( 'This widget area can be used for [widget] shortcode.', 'widget-shortcode' ),
			'id' => 'arbitrary',
			'before_widget' => '',
			'after_widget'	=> '',
		) );
	}

	/**
	 * Shows the shortcode for the widget
	 *
	 * @since 0.1
	 * @return void
	 */
	function in_widget_form( $widget, $return, $instance ) {
		echo '<p>' . __( 'Shortcode', 'widget-shortcode' ) . ': ' . ( ( $widget->number == '__i__' ) ? __( 'Please save this first.', 'widget-shortcode' ) : '<input type="text" value="' . esc_attr( '[widget id="'. $widget->id .'"]' ) . '" readonly="readonly" class="widefat" onclick="this.select()" />' ) . '</p>';
	}

	/**
	 * Load translation files
	 *
	 * @since 0.2.4
	 */
	function i18n() {
		load_plugin_textdomain( 'widget-shortcode', false, '/languages' );
	}

	/**
	 * Returns an array of all widgets as the key, their position as the value
	 *
	 * @since 0.2.2
	 * @return array
	 */
	function get_widgets_map() {
		$sidebars_widgets = wp_get_sidebars_widgets();
		$widgets_map = array();
		if ( ! empty( $sidebars_widgets ) )
			foreach( $sidebars_widgets as $position => $widgets )
				if( ! empty( $widgets) )
					foreach( $widgets as $widget )
						$widgets_map[$widget] = $position;

		return $widgets_map;
	}

	/**
	 * Get widget options
	 *
	 * @since 0.2.4
	 */
	public function get_widget_options( $widget_id ) {
		global $wp_registered_widgets;
		if( isset( $wp_registered_widgets[ $widget_id ] ) ) {
			preg_match( '/(\d+)/', $widget_id, $number );
			$options = get_option( $wp_registered_widgets[ $widget_id ]['callback'][0]->option_name );
			$instance = $options[$number[0]];
		}

		return isset( $instance ) ? $instance : array();
	}

	/**
	 * Displays a widget
	 *
	 * @param mixed args
	 * @since 0.2
	 * @return string widget output
	 */
	function do_widget( $args ) {
		global $_wp_sidebars_widgets, $wp_registered_widgets, $wp_registered_sidebars;

		extract( shortcode_atts( array(
			'id' => '',
			'title' => true, /* whether to display the widget title */
			'container_tag' => 'div',
			'container_class' => 'widget %2$s',
			'container_id' => '%1$s',
			'title_tag' => 'h2',
			'title_class' => 'widgettitle',
			'echo' => true
		), $args, 'widget' ) );

		/*
		 * @note: for backward compatibility: allow overriding widget args through the shortcode parameters
		 */
		$widget_args = shortcode_atts( array(
			'before_widget' => '<' . $container_tag . ' id="' . $container_id . '" class="' . $container_class . '">',
			'before_title' => '<' . $title_tag . ' class="' . $title_class . '">',
			'after_title' => '</' . $title_tag . '>',
			'after_widget' => '</' . $container_tag . '>',
		), $args );
		extract( $widget_args );

		if( empty( $id ) || ! isset( $wp_registered_widgets[$id] ) )
			return;

		// get the widget instance options
		preg_match( '/(\d+)/', $id, $number );
		$options = ( ! empty( $wp_registered_widgets ) && ! empty( $wp_registered_widgets[$id] ) ) ? get_option( $wp_registered_widgets[$id]['callback'][0]->option_name ) : array();
		$instance = isset( $options[$number[0]] ) ? $options[$number[0]] : array();
		$class = get_class( $wp_registered_widgets[$id]['callback'][0] );

		// maybe the widget is removed or de-registered
		if( ! $class )
			return;

		/* build the widget args that needs to be filtered through dynamic_sidebar_params */
		$params = array(
			0 => array(
				'name' => '',
				'id' => '',
				'description' => '',
				'before_widget' => $before_widget,
				'before_title' => $before_title,
				'after_title' => $after_title,
				'after_widget' => $after_widget,
				'widget_id' => $id,
				'widget_name' => $wp_registered_widgets[$id]['name']
			),
			1 => array(
				'number' => $number[0]
			)
		);

		// if feasable, use sidebar's parameters
		$widgets_map = $this->get_widgets_map();
		if( isset( $widgets_map[$id] ) ) {
			$params[0]['name'] = $wp_registered_sidebars[ $widgets_map[$id] ]['name'];
			$params[0]['id'] = $wp_registered_sidebars[ $widgets_map[$id] ]['id'];
			$params[0]['description'] = $wp_registered_sidebars[ $widgets_map[$id] ]['description'];
		}

		$params = apply_filters( 'dynamic_sidebar_params', $params );

		$show_title = ( '0' === $title || 'no' === $title || false === $title ) ? false : true;
		if( ! $show_title ) {
			$params[0]['before_title'] = '<!-- widget_shortcode_before_title -->';
			$params[0]['after_title'] = '<!-- widget_shortcode_after_title -->';
		} elseif( is_string( $title ) && strlen( $title ) > 0 ) {
			$instance['title'] = $title;
		}

		// Substitute HTML id and class attributes into before_widget
		$classname_ = '';
		foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
			if ( is_string( $cn ) )
				$classname_ .= '_' . $cn;
			elseif ( is_object($cn) )
				$classname_ .= '_' . get_class( $cn );
		}
		$classname_ = ltrim( $classname_, '_' );
		$params[0]['before_widget'] = sprintf( $params[0]['before_widget'], $id, $classname_ );

		// render the widget
		ob_start();
		echo '<!-- Widget Shortcode -->';
		the_widget( $class, $instance, $params[0] );
		echo '<!-- /Widget Shortcode -->';
		$content = ob_get_clean();

		// supress the title if we wish
		if( ! $show_title ) {
			$content = preg_replace( '/<!-- widget_shortcode_before_title -->(.*?)<!-- widget_shortcode_after_title -->/', '', $content );
		}

		if( $echo !== true )
			return $content;

		echo $content;
	}

	function mce_external_plugins( $plugins ) {
		$plugins['widgetShortcode'] = plugins_url( 'assets/tinymce.js', __FILE__ );

		return $plugins;
	}

	function mce_buttons( $mce_buttons ) {
		array_push( $mce_buttons, 'separator', 'widgetShortcode' );
		return $mce_buttons;
	}

	function editor_parameters() {
		global $wp_registered_widgets;

		$widgets = array();
		$all_widgets = $this->get_widgets_map();
		if( ! empty( $all_widgets ) ) {
			foreach( $all_widgets as $id => $position ) {
				if( $position == 'arbitrary' ) {
					$title = isset( $wp_registered_widgets[$id]['name'] ) ? $wp_registered_widgets[$id]['name'] : '';
					$options = $this->get_widget_options( $id );
					if( isset( $options['title'] ) && ! empty( $options['title'] ) ) {
						$title = join( ': ', array( $title, $options['title'] ) );
					}
					$widgets[] = array(
						'id' => $id,
						'title' =>  $title,
					);
				}
			}
		}
		wp_localize_script( 'editor', 'widgetShortcode', array(
			'title' => __( 'Widget Shortcode', 'widget-shortcode' ),
			'widgets' => $widgets,
			'image' => plugins_url( 'assets/widget-icon.png', __FILE__ ),
		) );
	}
}
Widget_Shortcode::get_instance();