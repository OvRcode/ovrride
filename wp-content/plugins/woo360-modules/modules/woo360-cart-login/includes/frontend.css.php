.fl-node-<?php echo $id; ?> .fl-loginout-link a, .fl-node-<?php echo $id; ?> .fl-loginout-link {
    color: #<?php echo $settings->text_color?>;
    text-align: <?php echo $settings->text_alignment?>;
    font-size: <?php echo $settings->font_size?>px;
    <?php if( $settings->font_family['family'] != "Default" ){ ?>
   	    <?php UABB_Helper::uabb_font_css( $settings->font_family ); ?>
   	<?php } ?>
}

.fl-node-<?php echo $id; ?> .fl-loginout-link a:hover, .fl-node-<?php echo $id; ?> .fl-loginout-link:hover {
    color: #<?php echo $settings->hover_color?>;
    text-decoration: none;
}

.fl-node-<?php echo $id; ?> .fl-loginout-link i {
    font-size: <?php echo $settings->icon_size?>px;
}