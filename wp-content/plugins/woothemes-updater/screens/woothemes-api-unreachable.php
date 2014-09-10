<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="woothemes-updater" class="wrap">
	<?php screen_icon( 'index' ); ?><div class="wrap"><h2><?php echo $this->name; ?></h2>
<div id="col-container">
	<?php
	echo '<div class="error fade">' . wpautop( __( 'There seems to be an error reaching the WooThemes API at this time. Please try again later. Should this error persist <a href="https://support.woothemes.com" target="_blank">log a ticket</a> in our help desk.', 'woothemes-updater' ) ) . '</div>' . "\n";
	?>

</div><!--/#col-container-->
</div><!--/#woothemes-updater-->