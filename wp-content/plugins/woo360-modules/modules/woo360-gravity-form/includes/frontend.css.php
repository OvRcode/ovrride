.fl-node-<?php echo $id; ?> input.gform_button {
    <?php if( $settings->button_block == "yes" ){ ?>
           width: 100% !important;
    <?php } ?>

    background-color: #<?php echo $settings->button_background; ?>;
    color: #<?php echo $settings->button_text; ?>;

    <?php if( $settings->button_radius >= 0 ){ ?>
    border-radius: <?php echo $settings->button_radius; ?>px <?php echo $settings->button_radius; ?>px <?php echo $settings->button_radius; ?>px <?php echo $settings->button_radius; ?>px !important;
    <?php } ?>

    transition: <?php echo $settings->transition; ?>s;
    border-width: 0px;
    padding-top: <?php echo $settings->vert_button_padding; ?>px;
    padding-bottom: <?php echo $settings->vert_button_padding; ?>px;
    padding-left: <?php echo $settings->horiz_button_padding; ?>px;
    padding-right: <?php echo $settings->horiz_button_padding; ?>px;
    }

    .fl-node-<?php echo $id; ?> input.gform_button:hover {
        background-color: #<?php echo $settings->hover_button_background; ?>;
        border-width: 0px;
        color: #<?php echo $settings->hover_button_text; ?>;   
    }


