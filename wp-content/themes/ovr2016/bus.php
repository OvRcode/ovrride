<?php
/**
 * Template Name: Bus Stop
 *
 * @package ovr2016
 */

 get_header();
 $place = get_post_custom_values( 'bus_stop' );
 $place = urlencode($place[0]);
wp_enqueue_script("ovr_tab_js", get_template_directory_uri(). '/includes/js/ovr_tab.min.js', array('jquery'), false, true);
 ?>

 <div class="col-sm-12 col-md-10 col-md-offset-1 mainBackground">
   <div class="row">
     <div class="col-xs-5 col-xs-offset-1">
       <div class="bus_thumb">
         <?php the_post_thumbnail(); ?>
       </div>
     </div>
     <div class="col-xs-6">
       <div class="bus_title">
         <h1><?php the_title(); ?></h1>
       </div>
     </div>
   </div>
   <div class="row">
     <div class="col-sm-10 col-sm-offset-1">
       <iframe class="bus_map" src="https://www.google.com/maps/embed/v1/place?key=<?php echo get_option('google_maps_api'); ?>&q=<?php echo $place;?>&zoom=15"></iframe>
     </div>
   </div>
   <div class="row">
     <div class="col-sm-10 col-sm-offset-1">
       <ul class="nav nav-tabs" role="tablist">
       <?php foreach( get_post_custom_values('tabs') as $index => $tab) { ?>
         <li role="presentation"><a href="#<?php echo $tab;?>" aria-controls="<?php echo $tab;?>" role="tab" data-toggle="tab"><?php echo str_replace("_"," ", $tab);?></a></li>
        <?php } ?>
      </ul>
      <!-- Tab panes -->
      <div class="tab-content">
      <?php foreach( get_post_custom_values('tabs') as $index => $tab) { ?>
          <div role="tabpanel" class="tab-pane" id="<?php echo $tab;?>">
            <?php
              $tab_content = get_post_custom_values('tab_'.$tab);
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
