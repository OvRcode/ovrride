<?php
$caption = htmlentities( $this->slide->post_excerpt, ENT_QUOTES, 'UTF-8' );
?>
<textarea name="attachment[<?php 
echo esc_attr( $this->slide->ID ); ?>][post_excerpt]" placeholder="<?php 
esc_attr_e( 'Caption', 'ml-slider' ); ?>"><?php echo esc_html( $caption ); ?></textarea>
