<?php
$crop_position = get_post_meta( $this->slide->ID, 'ml-slider_crop_position', true );
if ( ! $crop_position ) {
    $crop_position = 'center-center';
}
?>
<div class="row has-right-field">
    <label>
        <?php esc_html_e( 'Crop Position', 'ml-slider' ); ?>
    </label>
    <select class="crop_position" name="attachment[<?php 
    echo esc_attr( $this->slide->ID ); ?>][crop_position]">
        <?php
        $options = array(
            'left-top'      => esc_html__( 'Top Left', 'ml-slider' ),
            'center-top'    => esc_html__( 'Top Center', 'ml-slider' ),
            'right-top'     => esc_html__( 'Top Right', 'ml-slider' ),
            'left-center'   => esc_html__( 'Center Left', 'ml-slider' ),
            'center-center' => esc_html__( 'Center Center', 'ml-slider' ),
            'right-center'  => esc_html__( 'Center Right', 'ml-slider' ),
            'left-bottom'   => esc_html__( 'Bottom Left', 'ml-slider' ),
            'center-bottom' => esc_html__( 'Bottom Center', 'ml-slider' ),
            'right-bottom'  => esc_html__( 'Bottom Right', 'ml-slider' ),
        );

        foreach ( $options as $value => $label ) :
        ?>
            <option value="<?php echo esc_attr( $value ); ?>" <?php 
            selected( $crop_position, $value ); ?>>
                <?php echo esc_html( $label ); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

