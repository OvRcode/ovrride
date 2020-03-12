/**
 * This file should contain frontend styles that 
 * will be applied to individual module instances.
 *
 * You have access to three variables in this file: 
 * 
 * $module An instance of your module class.
 * $id The module's ID.
 * $settings The module's settings.
 *
 * Example: 
 */
 


<?php 
/* Convert hexdec color string to rgb(a) string */
/* http://mekshq.com/how-to-convert-hexadecimal-color-code-to-rgb-or-rgba-using-php/ */


if($settings->cb_caption_color_field){
  $color = $settings->cb_caption_color_field;

  if ($color[0] == '#' ) {
    $color = substr( $color, 1 );
  }
  //Check if color has 6 or 3 characters and get values
  if (strlen($color) == 6) {
          $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
  } elseif ( strlen( $color ) == 3 ) {
          $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
  } else {
          return $default;
  }

  //Convert hexadec to rgb
  $rgb =  array_map('hexdec', $hex);
  
  $color_8 = 'rgba('.implode(",",$rgb).','. $settings->hover_overlay_opacity  .')';
  $color_3 = 'rgba('.implode(",",$rgb).','. $settings->overlay_opacity  .')';
  
} else {
  $color_8 = 'rgba(0,0,0,0.8)';
  $color_3 = 'rgba(0,0,0,0)';
}
?>





<?php if($settings->cb_caption_vertical_align) { ?>
.fl-node-<?php echo $id; ?> .cb-caption-boxes .cb-caption-box-wrapper .cb-caption-box-overlay .cb-caption-box-inner {
    -webkit-box-pack: <?php echo $settings->cb_caption_vertical_align ?>;    -webkit-justify-content: <?php echo $settings->cb_caption_vertical_align ?>;    -ms-flex-pack: <?php echo $settings->cb_caption_vertical_align ?>;    justify-content: <?php echo $settings->cb_caption_vertical_align ?>;
}
<?php }  ?>




.fl-node-<?php echo $id; ?> .cb-caption-boxes .cb-caption-box-wrapper .cb-caption-box-overlay { background: <?php echo $color_3; ?>; }
.fl-node-<?php echo $id; ?> .cb-caption-boxes .cb-caption-box-wrapper:hover .cb-caption-box-overlay { background: <?php echo $color_8; ?>; }
.fl-node-<?php echo $id; ?> .cb-caption-boxes .cb-caption-box-wrapper.hover_effect .cb-caption-box-overlay { background: <?php echo $color_8; ?>; }

.cb-caption-boxes .cb-caption-box-wrapper .cb-caption-box-overlay .cb-caption-box-inner * {
    color: #<?php echo $settings->text_color_field; ?>;
}

.cb-caption-boxes .cb-caption-box-wrapper.hover_effect, .cb-caption-boxes .cb-caption-box-wrapper:hover {
    box-shadow: 0 0 25px rgba(0, 0, 0, .5);
    -webkit-transform: scale3d(1.03, 1.03, 1.03) translate(0px, -<?php echo $settings->box_hover_transform; ?>px);
    transform: scale3d(1.03, 1.03, 1.03) translate(0px, -<?php echo $settings->box_text_transform; ?>px);
    z-index: 5000;
  }

    .cb-caption-boxes .cb-caption-box-wrapper.hover_effect .cb-caption-box-inner, .cb-caption-boxes .cb-caption-box-wrapper:hover .cb-caption-box-inner {
    opacity: 1;
    -webkit-transform: scale3d(1.05, 1.05, 1.05) translate(0px, -<?php echo $settings->text_hover_transform; ?>px);
    transform: scale3d(1.03, 1.03, 1.03) translate(0px, -<?php echo $settings->text_hover_transform; ?>px);
  }


<?php   if($settings->cb_caption_photo_align !== 'center'){ ?>

    .fl-node-<?php echo $id; ?> .cb-caption-boxes .cb-caption-img-wrapper img {
       float: <?php echo $settings->cb_caption_photo_align; ?>;
    }  

<?php   } else {  ?>

    .fl-node-<?php echo $id; ?> .cb-caption-boxes .cb-caption-img-wrapper img {
       margin-left: auto;
       margin-right: auto;
       display: block;
    }    

<?php   }  ?>