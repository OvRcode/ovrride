<?php
$html = get_post_meta( $this->slide->ID, 'ml-slider_html', true );
?>
<textarea id="editor<?php 
    echo esc_attr( $this->slide->ID ); ?>" name="attachment[<?php 
    echo esc_attr( $this->slide->ID ); ?>][html]" style="height:150px"><?php 
    echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></textarea>
<?php
