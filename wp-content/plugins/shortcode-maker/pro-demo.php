<?php
/*add new shortcodes demo*/
function smpro_new_shortcode_items( $items ) {
    $items['button_group'] = array(
        'section' => 'Content',
        'label' => __( 'Button Group (Pro)', 'sm' ),
        'pro' => true
    );
    $items['card'] = array(
        'section' => 'Content',
        'label' => __( 'Card (Pro)', 'sm' ),
        'pro' => true
    );
    $items['card_group'] = array(
        'section' => 'Content',
        'label' => __( 'Card Group (Pro)', 'sm' ),
        'pro' => true
    );
    $items['jumbotron'] = array(
        'section' => 'Content',
        'label' => __( 'Jumbotron (Pro)', 'sm' ),
        'pro' => true
    );
    $items['list_group'] = array(
        'section' => 'Content',
        'label' => __( 'List Group (Pro)', 'sm' ),
        'pro' => true
    );
    $items['carousel'] = array(
        'section' => 'Content',
        'label' => __( 'Carousel (Pro)', 'sm' ),
        'pro' => true
    );
    return $items;
}
add_filter( 'smps_shortcode_items', 'smpro_new_shortcode_items' );

/*tabs*/
add_action( 'sm_bottom_settings_button_group', function () {
    ?>
    <p class="alert alert-danger"><?php _e( 'Pro Features', 'sm' ); ?></p>
    <div class="form-group">
        <label><?php _e( 'Layout Type', 'sm' ); ?></label>
        <select class="form-control" disabled>
            <option><?php _e( 'Select Layout Type', 'sm' ); ?></option>
        </select>
    </div>
    <?php
});

/*tabs*/
add_action( 'sm_bottom_settings_tabs', function () {
    ?>
    <p class="alert alert-danger"><?php _e( 'Pro Features', 'sm' ); ?></p>
    <div class="form-group">
        <label><?php _e( 'Layout Type', 'sm' ); ?></label>
        <select class="form-control" disabled>
            <option><?php _e( 'Select Layout Type', 'sm' ); ?></option>
        </select>
    </div>
    <?php
});
/*alert*/
add_action( 'sm_bottom_settings_alert', function() {
    ?>
    <p class="alert alert-danger"><?php _e( 'Pro Features', 'sm' ); ?></p>
    <div class="form-group">
        <div class="mb10">
            <label><?php _e( 'Heading Text', 'sm' ); ?></label>
            <input type="text" class="form-control" disabled>
        </div>
        <div class="mb10">
            <label><?php _e( 'Footer Text', 'sm' ); ?></label>
            <input type="text" class="form-control" disabled>
        </div>
    </div>
    <?php
});
/*button*/
add_action( 'sm_bottom_settings_button', function() {
    ?>
    <div class="form-group">
        <p class="alert alert-danger"><?php _e( 'Pro Features', 'sm' ); ?></p>
        <div class="mb10">
            <label><input disabled type="checkbox"><?php _e( 'Is Outlined ?', 'sm' ); ?></label>
        </div>
        <div class="mb10">
            <label><input disabled type="checkbox"><?php _e( 'Is Active ?', 'sm' ); ?></label>
        </div>
        <div class="mb10">
            <label><input disabled type="checkbox"><?php _e( 'Is Disabled ?', 'sm' ); ?></label>
        </div>
    </div>
    <?php
});
