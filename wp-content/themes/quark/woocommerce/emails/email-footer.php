<?php
/**
 * Email Footer
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Load colours
$base = get_option( 'woocommerce_email_base_color' );

$base_lighter_40 = woocommerce_hex_lighter( $base, 40 );

// For gmail compatibility, including CSS styles in head/body are stripped out therefore styles need to be inline. These variables contain rules which are added to the template inline.
$template_footer = "
	border-top:0;
	-webkit-border-radius:6px;
";

$credit = "
	border:0;
	color: $base_lighter_40;
	font-family: Arial;
	font-size:12px;
	line-height:125%;
	text-align:center;
";
?>
															</div>
														</td>
                                                    </tr>
                                                </table>
                                                <!-- End Content -->
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Body -->
                                </td>
                            </tr>
                        	<tr>
                            	<td align="center" valign="top">
                                    <!-- Footer -->
                                	<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="<?php echo $template_footer; ?>">
                                    	<tr>
                                        	<td valign="top">
                                                <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td colspan="2" valign="middle" id="policy" style="<?php echo $credit; ?>">
                                                            <a href="http://ovrride.com/terms-and-conditions/" style="<?php echo $credit; ?>">
                                                                Cancellation Policy/Terms and Conditions
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" valign="middle" style="text-align:center;">
                                                            <a href="https://www.facebook.com/ovrride">
                                                            <img src="http://ovrride.com/wp-content/themes/quark/images/soc-icons/facebook-square.png">
                                                            </a>
                                                            <a href="http://instagram.com/ovrride/">
                                                                <img src="http://ovrride.com/wp-content/themes/quark/images/soc-icons/instagram-square.png">
                                                            </a>
                                                            <a href="https://twitter.com/ovrride">
                                                                <img src="http://ovrride.com/wp-content/themes/quark/images/soc-icons/twitter-square.png">
                                                            </a>
                                                            <a href="https://plus.google.com/+OvRRide/">
                                                                <img src="http://ovrride.com/wp-content/themes/quark/images/soc-icons/google-plus-square.png">
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" valign="middle" id="credit" style="<?php echo $credit; ?>">
                                                        	<?php echo wpautop( wp_kses_post( wptexturize( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ) ) ); ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Footer -->
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>