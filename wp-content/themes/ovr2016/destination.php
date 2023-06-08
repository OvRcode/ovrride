<?php
/**
 * Template Name: Destination
 *
 * @package ovr2016
 */

 get_header();


 wp_enqueue_script("ovr_tab_js", get_template_directory_uri(). '/includes/js/ovr_tab.min.js', array('jquery'), false, true);
 ?>

 <div class="col-sm-12 col-md-10 col-md-offset-1 mainBackground">
   <div class="row">
     <div class="col-sm-10 col-sm-offset-1">
       <div class="destination_img">
         <?php echo the_post_thumbnail( 'full' );  ?>
       </div>
       <ul class="nav nav-tabs" role="tablist">
       <?php foreach( get_post_custom_values('tabs') as $index => $tab) { ?>
         <?php $id = str_replace(" ", "_", $tab); ?>
         <li role="presentation"><a href="#<?php echo $id;?>" aria-controls="<?php echo $id;?>" role="tab" data-toggle="tab"><?php echo str_replace("_", " ", $tab);?></a></li>
        <?php } ?>
      </ul>
      <!-- Tab panes -->
      <div class="tab-content">
      <?php foreach( get_post_custom_values('tabs') as $index => $tab) { ?>
        <?php $id = str_replace(" ", "_", $tab); ?>
          <div role="tabpanel" class="tab-pane clearfix" id="<?php echo $id;?>">
            <?php
              $tab_content = get_post_custom_values('tab_'.$id);
              $tab_content = $tab_content[0];
              echo apply_filters('the_content', do_shortcode( shortcode_unautop($tab_content) ) );
            ?>
          </div>
      <?php } ?>
     </div>
   </div>
   <div class="row">
     <div class="col-sm-10 col-sm-offset-1">
       <?php echo apply_filters('the_content', do_shortcode( shortcode_unautop($post->post_content) ) ); ?>
     </div>
   </div>

 </div>


 <?php get_footer(); ?>
