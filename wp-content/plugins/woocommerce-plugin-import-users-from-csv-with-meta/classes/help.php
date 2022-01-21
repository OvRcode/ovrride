<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

class ACUI_Help{
	public static function message(){
		?>
<div class="postbox">
    <h3 class="hndle"><span>&nbsp;<?php _e( 'Need proffessional help with WordPress or WooCommerce?', 'import-users-from-csv-with-meta' ); ?></span></h3>

    <div class="inside" style="display: block;">
        <p><?php _e( 'Hi! we are', 'import-users-from-csv-with-meta' ); ?> <a href="https://twitter.com/fjcarazo" target="_blank" title="Javier Carazo">Javier Carazo</a> <?php _e( 'and the team of', 'import-users-from-csv-with-meta' ) ?> <a href="http://codection.com">Codection</a>, <?php _e( 'developers of this plugin.', 'import-users-from-csv-with-meta' ); ?></p>
        <p><?php _e( 'We work everyday with WordPress and WooCommerce, if you need proffessional help, hire us. You can send us a message to', 'import-users-from-csv-with-meta' ); ?> <a href="mailto:contacto@codection.com">contacto@codection.com</a>.</p>
        <div style="clear:both;"></div>
    </div>
</div>
		<?php
	}
}