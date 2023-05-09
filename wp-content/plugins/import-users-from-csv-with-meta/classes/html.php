<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

class ACUI_HTML{
    private static $instance;
    var $settings;

    function __construct(){
    }

    private static function is_instantiated() {
		if ( ! empty( self::$instance ) && ( self::$instance instanceof ACUI_HTML ) ) {
			return true;
		}

		return false;
	}

    private static function setup_instance() {
		self::$instance = new ACUI_HTML;
	}

    static function instance() {
		if ( self::is_instantiated() ) {
			return self::$instance;
		}

		self::setup_instance();

		return self::$instance;
	}


    function sanitize_key( $key ) {
        $raw_key = $key;
        return preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );
    }

	function year_dropdown( $name = 'year', $selected = 0, $years_before = 5, $years_after = 0 ) {
		$current     = date( 'Y' );
		$start_year  = $current - absint( $years_before );
		$end_year    = $current + absint( $years_after );
		$selected    = empty( $selected ) ? date( 'Y' ) : $selected;
		$options     = array();

		while ( $start_year <= $end_year ) {
			$options[ absint( $start_year ) ] = $start_year;
			$start_year++;
		}

		$output = $this->select( array(
			'name'             => $name,
			'selected'         => $selected,
			'options'          => $options,
			'show_option_all'  => false,
			'show_option_none' => false
		) );

		return $output;
	}

	function month_dropdown( $name = 'month', $selected = 0 ) {
		$month   = 1;
		$options = array();
		$selected = empty( $selected ) ? date( 'n' ) : $selected;

		while ( $month <= 12 ) {
			$options[ absint( $month ) ] = edd_month_num_to_name( $month );
			$month++;
		}

		$output = $this->select( array(
			'name'             => $name,
			'selected'         => $selected,
			'options'          => $options,
			'show_option_all'  => false,
			'show_option_none' => false
		) );

		return $output;
	}

	function select( $args = array() ) {
		$defaults = array(
            'echo'             => true,
			'options'          => array(),
			'name'             => null,
			'class'            => '',
			'id'               => '',
			'selected'         => array(),
			'chosen'           => false,
			'placeholder'      => null,
			'multiple'         => false,
			'show_option_all'  => _x( 'All', 'all dropdown items', 'import-users-from-csv-with-meta' ),
			'show_option_none' => _x( 'None', 'no dropdown items', 'import-users-from-csv-with-meta' ),
			'data'             => array(),
			'readonly'         => false,
			'disabled'         => false,
		);

		$args = wp_parse_args( $args, $defaults );

        if( empty( $args['id'] ) )
            $args['id'] = $args['name'];

		$data_elements = '';
		foreach ( $args['data'] as $key => $value ) {
			$data_elements .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
		}

		if( $args['multiple'] ) {
			$multiple = ' MULTIPLE';
		} else {
			$multiple = '';
		}

		if( $args['placeholder'] ) {
			$placeholder = $args['placeholder'];
		} else {
			$placeholder = '';
		}

		if ( isset( $args['readonly'] ) && $args['readonly'] ) {
			$readonly = ' readonly="readonly"';
		} else {
			$readonly = '';
		}

		if ( isset( $args['disabled'] ) && $args['disabled'] ) {
			$disabled = ' disabled="disabled"';
		} else {
			$disabled = '';
		}

		$class  = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
		$output = '<select' . $disabled . $readonly . ' name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $this->sanitize_key( $args['id'] ) ) . '" class="acui-select ' . $class . '"' . $multiple . ' data-placeholder="' . $placeholder . '"'. $data_elements . '>';

		if ( ! isset( $args['selected'] ) || ( is_array( $args['selected'] ) && empty( $args['selected'] ) ) || ! $args['selected'] ) {
			$selected = "";
		}

		if ( $args['show_option_all'] ) {
			if ( $args['multiple'] && ! empty( $args['selected'] ) ) {
				$selected = selected( true, in_array( 0, (array) $args['selected'] ), false );
			} else {
				$selected = selected( $args['selected'], 0, false );
			}
			$output .= '<option value="all"' . $selected . '>' . esc_html( $args['show_option_all'] ) . '</option>';
		}

		if ( ! empty( $args['options'] ) ) {
			if ( $args['show_option_none'] ) {
				if ( $args['multiple'] ) {
					$selected = selected( true, in_array( "", $args['selected'] ), false );
				} elseif ( isset( $args['selected'] ) && ! is_array( $args['selected'] ) && ! empty( $args['selected'] ) ) {
					$selected = selected( $args['selected'], "", false );
				}
				$output .= '<option value=""' . $selected . '>' . esc_html( $args['show_option_none'] ) . '</option>';
			}

			foreach ( $args['options'] as $key => $option ) {
				if ( $args['multiple'] && is_array( $args['selected'] ) ) {
					$selected = selected( true, in_array( (string) $key, $args['selected'] ), false );
				} elseif ( isset( $args['selected'] ) && ! is_array( $args['selected'] ) ) {
					$selected = selected( $args['selected'], $key, false );
				}

				$output .= '<option value="' . esc_attr( $key ) . '"' . $selected . '>' . esc_html( $option ) . '</option>';
			}
		}

		$output .= '</select>';

        if( $args['echo'] )
            echo $output;

		return $output;
	}

	function checkbox( $args = array() ) {
		$defaults = array(
            'label'    => '',
            'echo'     => true,
            'array'    => false,
            'compare_value' => 1, 
			'name'     => null,
			'current'  => 1,
			'class'    => '',
			'options'  => array(
				'disabled' => false,
				'readonly' => false
            ),
			'description' => '',
		);

		$args = wp_parse_args( $args, $defaults );
        $output = '';

		$class = 'acui-checkbox ' . implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
		$options = '';
		if ( ! empty( $args['options']['disabled'] ) ) {
			$options .= ' disabled="disabled"';
		} elseif ( ! empty( $args['options']['readonly'] ) ) {
			$options .= ' readonly';
		}

        if( !empty( $args['label'] ) ){
            $output .= '<label class="' . $class . '"';
            
            if( !$args['array'] )
                $output .= ' for="' . esc_attr( $args['name'] ) . '"';

            $output .= '>' . $args['label'];
        }

        if( $args['array'] )
            $output .= '<input type="checkbox"' . $options . ' name="' . esc_attr( $args['name'] ) . '" class="' . $class . '" value="' . $args['current'] .'" ' . checked( in_array( $args['current'], $args['compare_value'] ), 1, false ) . ' />';            
        else
            $output .= '<input type="checkbox"' . $options . ' name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['name'] ) . '" class="' . $class . '" value="' . $args['current'] . '" ' . checked( $args['compare_value'], $args['current'], false ) . ' />';

        if( !empty( $args['label'] ) )
            $output .= '</label>';

		if( !empty( $args['description'] ) )
            $output .= '<div class="description">' . $args['description'] . '</div>';

        if( $args['echo'] )
            echo $output;

		return $output;
	}

	function text( $args = array() ) {
		$defaults = array(
            'echo'         => true,
			'id'           => '',
			'name'         => isset( $name )  ? $name  : 'text',
			'value'        => isset( $value ) ? $value : null,
			'label'        => isset( $label ) ? $label : null,
			'desc'         => isset( $desc )  ? $desc  : null,
			'placeholder'  => '',
			'class'        => 'regular-text',
			'disabled'     => false,
			'autocomplete' => '',
			'data'         => false,
            'type'         => 'text',
            'readonly'     => false,
			'required'	   => false,
		);

		$args = wp_parse_args( $args, $defaults );

        if( empty( $args['id'] ) )
            $args['id'] = $args['name'];

        if ( isset( $args['readonly'] ) && $args['readonly'] ) {
            $readonly = ' readonly="readonly"';
        } else {
            $readonly = '';
        }

		if ( isset( $args['required'] ) && $args['required'] ) {
            $required = ' required="required"';
        } else {
            $required = '';
        }

		$class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
		$disabled = '';
		if( $args['disabled'] ) {
			$disabled = ' disabled="disabled"';
		}

		$data = '';
		if ( ! empty( $args['data'] ) ) {
			foreach ( $args['data'] as $key => $value ) {
				$data .= 'data-' . $this->sanitize_key( $key ) . '="' . esc_attr( $value ) . '" ';
			}
		}

		$output = '<span id="acui-' . $this->sanitize_key( $args['name'] ) . '-wrap">';
			if ( ! empty( $args['label'] ) ) {
				$output .= '<label class="acui-label" for="' . $this->sanitize_key( $args['id'] ) . '">' . esc_html( $args['label'] ) . '</label>';
			}

			if ( ! empty( $args['desc'] ) ) {
				$output .= '<span class="acui-description">' . esc_html( $args['desc'] ) . '</span>';
			}

			$output .= '<input type="' . esc_attr( $args['type'] ) . '" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] )  . '" autocomplete="' . esc_attr( $args['autocomplete'] )  . '" value="' . esc_attr( $args['value'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" class="' . $class . '" ' . $data . '' . $disabled . '' . $readonly . '' . $required . '/>';

		$output .= '</span>';

        if( $args['echo'] )
            echo $output;

		return $output;
	}

	function textarea( $args = array() ) {
		$defaults = array(
			'echo'        => true,
			'name'        => 'textarea',
			'value'       => null,
			'label'       => null,
			'desc'        => null,
			'class'       => 'large-text',
			'disabled'    => false
		);

		$args = wp_parse_args( $args, $defaults );

		$class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
		$disabled = '';
		if( $args['disabled'] ) {
			$disabled = ' disabled="disabled"';
		}

		$output = '<span id="acui-' . $this->sanitize_key( $args['name'] ) . '-wrap">';

			if ( ! empty( $args['label'] ) ) {
				$output .= '<label class="acui-label" for="' . $this->sanitize_key( $args['name'] ) . '">' . esc_html( $args['label'] ) . '</label>';
			}

			$output .= '<textarea name="' . esc_attr( $args['name'] ) . '" id="' . $this->sanitize_key( $args['name'] ) . '" class="' . $class . '"' . $disabled . '>' . esc_attr( $args['value'] ) . '</textarea>';

			if ( ! empty( $args['desc'] ) ) {
				$output .= '<span class="acui-description">' . esc_html( $args['desc'] ) . '</span>';
			}

		$output .= '</span>';

		if( $args['echo'] )
            echo $output;

		return $output;
	}
}

function ACUIHTML(){
    return ACUI_HTML::instance();
}