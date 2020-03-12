<?php

// Only load for multisite installs.
if ( ! is_multisite() ) {
	return;
}

// Defines
define( 'FL_BUILDER_MULTISITE_SETTINGS_DIR', FL_BUILDER_DIR . 'extensions/fl-builder-multisite-settings/' );
define( 'FL_BUILDER_MULTISITE_SETTINGS_URL', FL_BUILDER_URL . 'extensions/fl-builder-multisite-settings/' );

// Classes
require_once FL_BUILDER_MULTISITE_SETTINGS_DIR . 'classes/class-fl-builder-multisite-settings.php';
