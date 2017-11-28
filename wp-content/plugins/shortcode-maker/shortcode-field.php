<?php

class SM_Shortcode_Field {

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10, 2 );
        add_action( 'save_post', array( $this, 'save_shortcode_data' ) );
    }


    public function add_meta_box( $post_type, $post ) {

        if( $post_type != 'sm_shortcode' ) return;

        add_meta_box(
            'sm-extra-attr',
            __( 'Shortcode Attributes','shortcode-maker' ),
            array( $this, 'render_shortcode_attributes_meta_box' ),
            $post_type,
            'normal',
            'default'
        );
    }

    public function render_shortcode_attributes_meta_box( $post ) {

        $shortcode_atts = get_post_meta( $post->ID, 'sm_shortcode_atts', true );
        !is_array( $shortcode_atts ) ? $shortcode_atts = array() : '';
        $shortcode_atts = self::convert_to_js_meta($shortcode_atts);
        ?>
        <script>
            var shortcode_atts = <?php echo json_encode($shortcode_atts);?>;
        </script>
        <div class="bs-container">
            <p class="alert alert-info"><?php _e( 'Note : Provide attibute name withous space inside . You can use these shortcode attributes in content. To do this, add the attributes in the content in wp editor like this
                %attributes_name%', 'shortcode-maker' );?>
                </p>
            <a href="javascript:" class="btn btn-primary sm-add-attr mb10" @click="add_attr_box()"><?php _e( 'Add Attribute', 'shortcode-maker' ); ?></a>

            <div v-for="( key, attr ) in shortcode_atts" class="panel panel-default mt10">
                <div class="panel-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-5">
                                <input type="text" v-model="attr.name" name="shortcode_atts[{{ key }}][name]" class="form-control mb5 col-sm-5" >
                            </div>
                            <div class="col-sm-5">
                                <input type="text" v-model="attr.value" name="shortcode_atts[{{ key }}][value]" class="form-control mb5 col-sm-5">
                            </div>
                            <div class="col-sm-2">
                                <a href="javascript:" class="btn btn-danger btn-sm" @click="remove_attr(key)" ><?php _e( 'Remove' , 'shortcode-maker' ); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public static function convert_to_js_meta( $obj ){
        $js_meta = array();

        foreach( $obj as $name => $value ) {
            $js_meta[] = array(
                'name' => $name,
                'value' => $value
            );
        }

        return $js_meta;
    }


    public static function convert_to_post_meta( $obj ) {

        $post_meta = array();

        if( !empty( $obj ) && is_array( $obj ) ) {

            foreach( $obj as $key => $value ){
                $post_meta[$value['name']] = $value['value'];
            }
        }
        return $post_meta;
    }

    public function save_shortcode_data( $post_id ) {
        if( !isset( $_POST['shortcode_atts'] ) ) return;
        $post_meta = self::convert_to_post_meta( $_POST['shortcode_atts'] );
        update_post_meta( $post_id, 'sm_shortcode_atts', $post_meta );
    }
}

new SM_Shortcode_Field();