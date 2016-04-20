<?php

// Security
if (!$squelch_taas_admin) exit;
if (!current_user_can( 'manage_options' )) exit;

$theme = get_option( 'squelch_taas_jquery_ui_theme' );


/* Save changes
 */
if (!empty($_POST['submit']) && $_POST['submit'] == "Save Changes") {
    $valid = true;

    $new_theme      = $_POST['jquery_ui_theme'];
    ////$custom_css     = $_POST['custom_css_url'];
    ////if (('custom' == $new_theme) && (empty($custom_css))) {
    ////    $GLOBALS['squelch_taas_admin_msg'] .= '<div class="error"><p>Custom CSS URL cannot be empty, please enter a URL or upload a stylesheet.</p></div>';
    ////    $valid = false;
    ////}

    if ($valid) {
        update_option( 'squelch_taas_jquery_ui_theme',  $new_theme      );
        ////update_option( 'squelch_taas_custom_css_url',   $custom_css );

        $msg  = isset($GLOBALS['squelch_taas_admin_msg']) ? $GLOBALS['squelch_taas_admin_msg'] : '';
        $msg .= '<div class="updated"><p>Changes saved.</p></div>';
        $GLOBALS['squelch_taas_admin_msg'] = $msg;

        $theme = $new_theme;
    }
}


// Detect whether a custom theme has been uploaded or not:

$upload_dir = wp_upload_dir();
$upload_dir = $upload_dir['basedir'];
$custom_theme_dir = trailingslashit( $upload_dir ) . 'jquery-ui-1.9.2.custom';

$custom_theme_detected = false;
if (file_exists( $custom_theme_dir )) $custom_theme_detected = true;



global $squelch_taas_admin_msg;
$custom_css = get_option('squelch_taas_custom_css_url');

?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>

    <?php echo $squelch_taas_admin_msg; ?>

    <h2>Squelch Tabs And Accordions Shortcodes</h2>
    <p>
        Squelch Tabs and Accordions Shortcodes provides shortcodes for adding stylish Web 2.0 style accordions and tabs to your WordPress website: Horizontal accordions, vertical accordions and tabs.
    </p>
    <a href="http://squelchdesign.com/web-development/free-wordpress-plugins/squelch-tabs-and-accordions-shortcodes/" target="_blank" class="button">Theme Documentation</a>
    <a href="https://wordpress.org/support/topic/please-read-this-before-you-post-5?replies=2" target="_blank" class="button">Support Forum</a>
    <a href="https://wordpress.org/plugins/squelch-tabs-and-accordions-shortcodes/" target="_blank" class="button">Rate on WordPress.org</a>
    <a href="http://squelchdesign.com/uncategorized/roll-theme-squelch-tabs-accordions-shortcodes-plugin/" target="_blank" class="button">How to create a custom theme</a>
    <form method="post" action="">
        <div>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <label for="jquery_ui_theme">
                                jQuery UI theme
                            </label>
                        </th>
                        <td valign="top">
                            <select id="jquery_ui_theme" name="jquery_ui_theme">
                                <option<?php selected($theme, 'none'); ?> value="none">No jQuery UI theme</option>
                                <!-- option <?php /*selected($theme, 'custom');*/ ?> value="custom">Use your own custom CSS</option -->
                                <option<?php selected($theme, 'base'); ?> value="base">jQuery Base Styles Only</option>
                                <?php if ($custom_theme_detected) : ?>
                                    <option<?php selected($theme, 'custom'); ?> value="custom">Custom jQuery theme</option>
                                <?php endif; ?>
                                <option<?php selected($theme, 'ui-lightness'); ?> value="ui-lightness">Lightness</option>
                                <option<?php selected($theme, 'ui-darkness'); ?> value="ui-darkness">Darkness</option>
                                <option<?php selected($theme, 'smoothness'); ?> value="smoothness">Smoothness</option>
                                <option<?php selected($theme, 'start'); ?> value="start">Start</option>
                                <option<?php selected($theme, 'redmond'); ?> value="redmond">Redmond</option>
                                <option<?php selected($theme, 'sunny'); ?> value="sunny">Sunny</option>
                                <option<?php selected($theme, 'overcast'); ?> value="overcast">Overcast</option>
                                <option<?php selected($theme, 'le-frog'); ?> value="le-frog">Le Frog</option>
                                <option<?php selected($theme, 'flick'); ?> value="flick">Flick</option>
                                <option<?php selected($theme, 'pepper-grinder'); ?> value="pepper-grinder">Pepper Grinder</option>
                                <option<?php selected($theme, 'eggplant'); ?> value="eggplant">Eggplant</option>
                                <option<?php selected($theme, 'dark-hive'); ?> value="dark-hive">Dark Hive</option>
                                <option<?php selected($theme, 'cupertino'); ?> value="cupertino">Cupertino</option>
                                <option<?php selected($theme, 'south-street'); ?> value="south-street">South Street</option>
                                <option<?php selected($theme, 'blitzer'); ?> value="blitzer">Blitzer</option>
                                <option<?php selected($theme, 'humanity'); ?> value="humanity">Humanity</option>
                                <option<?php selected($theme, 'hot-sneaks'); ?> value="hot-sneaks">Hot Sneaks</option>
                                <option<?php selected($theme, 'excite-bike'); ?> value="excite-bike">Excite Bike</option>
                                <option<?php selected($theme, 'vader'); ?> value="vader">Vader</option>
                                <option<?php selected($theme, 'dot-luv'); ?> value="dot-luv">Dot Luv</option>
                                <option<?php selected($theme, 'mint-choc'); ?> value="mint-choc">Mint Choc</option>
                                <option<?php selected($theme, 'black-tie'); ?> value="black-tie">Black Tie</option>
                                <option<?php selected($theme, 'trontastic'); ?> value="trontastic">Trontastic</option>
                                <option<?php selected($theme, 'swanky-purse'); ?> value="swanky-purse">Swanky Purse</option>
                            </select>
                        </td>
                    </tr>
                    <!--tr valign="top" style="display: none;" id="custom-css-row">
                        <th scope="row">
                            <label for="upload_css">
                                Use your own custom CSS
                            </label>
                        </th>
                        <td>
                            <label for="upload_css">
                                <input id="upload_css" type="text" size="36" name="custom_css_url" value="<?php /*echo $custom_css;*/ ?>" />
                                <input id="upload_css_button" type="button" value="Upload CSS" />
                                <br />Upload your own custom CSS as built by the <a href="http://jqueryui.com/themeroller/" target="_blank">jQuery UI Theme Roller</a>, or enter the URL of your custom CSS in the box above.
                        </label></td>
                    </tr -->
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" />
            </p>
        </div>
    </form>
</div>
