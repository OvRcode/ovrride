<?php
// Add Shortcode
function enjoyinstagram_mb_shortcode($atts) { 
STATIC $i = 1;
	
	
	if(get_option('enjoyinstagram_client_id') || get_option('enjoyinstagram_client_id') != '') {
	extract( shortcode_atts( array(
		'n' => '4',
	), $atts ) );
?>
<script>
    jQuery(function(){
      jQuery(document.body)
          .on('click touchend','#swipebox-slider .current img', function(e){
              jQuery('#swipebox-next').click();
			  return false;
          })
          .on('click touchend','#swipebox-slider .current', function(e){
              jQuery('#swipebox-close').trigger('click');
          });
    });
</script>
<script type="text/javascript">
jQuery(function($) {
	$(".swipebox").swipebox({
	hideBarsDelay : 0
	});
	
});   
jQuery(document).ready(function() {
jQuery("#owl-<?php echo $i; ?>").owlCarousel({
	  lazyLoad : true,
	  items : <?php echo get_option('enjoyinstagram_carousel_items_number'); ?>,
	  itemsDesktop : [1199,<?php echo get_option('enjoyinstagram_carousel_items_number'); ?>],
   	  itemsDesktopSmall : [980,<?php echo get_option('enjoyinstagram_carousel_items_number'); ?>],
      itemsTablet: [768,<?php echo get_option('enjoyinstagram_carousel_items_number'); ?>],
      itemsMobile : [479,<?php echo get_option('enjoyinstagram_carousel_items_number'); ?>],
	  stopOnHover: true,
	  navigation: <?php echo get_option('enjoyinstagram_carousel_navigation'); ?>
	  
		 });
		 jQuery("#owl-<?php echo $i; ?>").fadeIn();
		 });
</script>
<?php
$instagram = new Enjoy_Instagram(get_option('enjoyinstagram_client_id'));
$instagram->setAccessToken(get_option('enjoyinstagram_access_token'));
if(get_option('enjoyinstagram_user_or_hashtag')=='hashtag'){
$result = $instagram->getTagMedia(get_option('enjoyinstagram_hashtag'));
}else{
$result = $instagram->getUserMedia(get_option('enjoyinstagram_user_id'));
}
$pre_shortcode_content = "<div id=\"owl-".$i."\" class=\"owl-example\" style=\"display:none;\">";

foreach ($result->data as $entry) {
	
	if(get_option('enjoyinstagram_carousel_items_number')!='1'){
    $shortcode_content .=  "<div class=\"box\"><a title=\"{$entry->caption->text}\" rel=\"gallery_swypebox\" class=\"swipebox\" href=\"{$entry->images->standard_resolution->url}\"><img  src=\"{$entry->images->standard_resolution->url}\"></a></div>";
	}else{
	    $shortcode_content .=  "<div class=\"box\"><a title=\"{$entry->caption->text}\" rel=\"gallery_swypebox\" class=\"swipebox\" href=\"{$entry->images->standard_resolution->url}\"><img style=\"width:100%;\" src=\"{$entry->images->standard_resolution->url}\"></a></div>";
	}
  }
  
$post_shortcode_content = "</div>";



}
$i++;

$shortcode_content = $pre_shortcode_content.$shortcode_content.$post_shortcode_content;

return $shortcode_content;

}
add_shortcode( 'enjoyinstagram_mb', 'enjoyinstagram_mb_shortcode' );




?>