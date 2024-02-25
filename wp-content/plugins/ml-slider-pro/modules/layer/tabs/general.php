<?php
$imageHelper = new MetaSliderImageHelper(
    $this->slide->ID,
    $this->settings['width'],
    $this->settings['height'],
    isset( $this->settings['smartCrop'] ) ? $this->settings['smartCrop'] : 'false'
);

$background_url = $imageHelper->get_image_url();
$url            = get_post_meta( $this->slide->ID, 'ml-slider_url', true );
$target         = get_post_meta( $this->slide->ID, 'ml-slider_new_window', true ) == 'true' ? true : false;
?>
<div class="thumb-col-settings">
    <?php echo $this->get_admin_slide_thumb(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    <div>
        <button class="openLayerEditor w-full inline-flex items-center justify-center px-4 py-2 border border-transparent font-medium rounded-md transition ease-in-out duration-150 md:w-auto md:text-sm md:leading-5 bg-orange hover:bg-orange-darker active:bg-orange-darkest text-white" data-thumb="<?php 
        echo esc_attr( $background_url ); ?>" data-width="<?php 
        echo esc_attr( $this->settings['width'] ); ?>" data-height="<?php 
        echo esc_attr( $this->settings['height'] ); ?>" data-editor_id="editor<?php 
        echo esc_attr( $this->slide->ID ); ?>" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="/*! width: 16px; *//*! height: 10px; */" class="w-6 -ml-1 pr-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"></path>
            </svg>
            <?php esc_html_e( 'Launch Layer Editor', 'ml-slider-pro' ); ?>
        </button>
        <div class="rawEdit"></div> <!-- vantage backwards compatibility -->
        <div class="row mb-2 mt-4">
            <label>
                <?php esc_html_e( 'Background Image Link', 'ml-slider-pro' ); ?>
            </label>
        </div>
        <div class="row has-right-checkbox">
            <div>
                <input class="url" type="text" name="attachment[<?php 
                echo esc_attr( $this->slide->ID ); ?>][url]" placeholder="<?php 
                esc_attr_e( 'URL', 'ml-slider-pro' ); ?>" value="<?php 
                echo esc_attr( $url ); ?>" />
            </div>
            <div class="input-label">
                <label>
                    <?php 
                    esc_html_e( 'New window', 'ml-slider' );
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $this->info_tooltip( __(
                        'Open link in a new window', 
                        'ml-slider'
                    ) );
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $this->switch_button( 
                        'attachment[' . esc_attr( $this->slide->ID ) . '][new_window]', 
                        (bool) $target,
                        array(),
                        'mr-0 ml-2'
                    );
                    ?>
                </label>
            </div>
        </div>
    </div>
</div>