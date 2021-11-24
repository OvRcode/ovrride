<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

class ACUI_NewFeatures{
	public static function message(){
		?>
<div class="postbox">
    <h3 class="hndle"><span>&nbsp;<?php _e( 'New features', 'import-users-from-csv-with-meta' ); ?></span></h3>

    <div class="inside" style="display: block;">
        <p><?php _e( 'This is a list of new features that we want to include, but we have not been able to do yet. We have a bussiness and have not so much time for this plugin as we would like to have. If you need some new feature not included in this list, please write us to:', 'import-users-from-csv-with-meta' ); ?> <a href="mailto:contacto@codection.com">contacto@codection.com</a>. <?php _e( 'You can also donate us or talk with us to have some of the next new features done soon.', 'import-users-from-csv-with-meta' ); ?></p>
        <ul style="list-style: disc; padding-left: 15px;">
            <li>Change required document format</li>
            <li>Make a wizard to help importing</li>
            <li>Include a new step, to a simulation about how it would be the import before doing it</li>
            <li>Make a website to give a better documentation and tutorials about it</li>
            <li>Create an online service to be able to provide personal support to some imports</li>
            <li>Include a button to export the current user data database in CSV format</li>
            <li>Make this plugin compatible with WP-CLI including some command</li>
            <li>Include more CSV examples</li>
        </ul>
        <div style="clear:both;"></div>
    </div>
</div>
		<?php
	}
}