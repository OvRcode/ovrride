<div class="row delay">
    <div class="ms-switch-button">
        <label>
            <input
                class="delay-slide mr-0"
                type="checkbox"
                id="delay-select-<?php
                echo esc_attr($post->ID) ?>"
                name="attachment[<?php
                esc_attr_e( $post->ID ) ?>][delay]"
                value="on"
                <?php
                echo $is_delayed ? 'checked="checked"' : ''; ?>
            >
            <span></span>
        </label>
    </div><label class="delay-slide" for="delay-select-<?php
    echo esc_attr($post->ID) ?>">
        <?php 
        esc_html_e( 'Custom delay for this slide', 'ml-slider-pro' ) 
        ?><span class="dashicons dashicons-info tipsy-tooltip-top" original-title="<?php esc_attr_e(
            'Requires the Auto play setting to be enabled for this slideshow', 
            'ml-slider-pro'
        ) ?>" style="line-height: 1.2em;"></span>
    </label>

    <div class="delay_wrapper_settings relative bg-gray-lightest border border-gray-light border-l-8 rtl:border-r-8 rtl:border-l-0 rtl:pl-0 rtl:pr-4 hide-if-notchecked mt-2 p-5"<?php 
        echo ! $is_delayed ? ' style="display: none;"' : '' ?>>
        <div>
            <table class="delay-main-settings mb-1">
                <tbody>
                    <tr>
                        <td>
                            <?php esc_html_e( 'Slide delay', 'ml-slider-pro' ) ?>
                        </td>
                        <td>
                            <label class="mb-0">
                                <input
                                    type="number"
                                    class="w-24 m-0"
                                    name="attachment[<?php
                                    esc_attr_e( $post->ID ); ?>][delay_time]"
                                    min="500"
                                    max="10000"
                                    step="100"
                                    value="<?php
                                    echo $delay_time ? esc_attr( $delay_time ) : '3000'; ?>"
                                    placeholder="<?php
                                    esc_attr_e( 'default', 'ml-slider-pro' ) ?>"
                                    >
                                </label>
                                <span class="mx-1">
                                    <?php esc_html_e( 'ms', 'ml-slider-pro' ) ?>
                                </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>