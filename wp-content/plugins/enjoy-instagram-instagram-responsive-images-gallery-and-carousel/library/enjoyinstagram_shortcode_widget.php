<?php
// Add Shortcode
function enjoyinstagram_mb_shortcode_widget($atts) { 
if(get_option('enjoyinstagram_client_id') || get_option('enjoyinstagram_client_id') != '') {
	extract( shortcode_atts( array(
		'n' => '4',
		'id' => 'owl',
		'n_y_n' =>'false',
		'u_or_h' => 'user'
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
jQuery("#owl-<?php echo "{$id}"; ?>").owlCarousel({
	  items : <?php echo "{$n}"; ?>,
	  navigation: <?php echo "{$n_y_n}"; ?>,
		 });
		 jQuery("#owl-<?php echo "{$id}"; ?>").fadeIn('slow');
		 });
</script>
<?php
$instagram = new Enjoy_Instagram(get_option('enjoyinstagram_client_id'));
$instagram->setAccessToken(get_option('enjoyinstagram_access_token'));
if("{$u_or_h}"=='hashtag'){
$result = $instagram->getTagMedia(get_option('enjoyinstagram_hashtag'));
}else{
$result = $instagram->getUserMedia(get_option('enjoyinstagram_user_id'));
}
?>
<div id="owl-<?php echo "{$id}"; ?>" class="owl-example">
<?php
if($result->data){
foreach ($result->data as $entry) {
	
	if(get_option('enjoyinstagram_carousel_items_number')!='1'){
    echo "<div class=\"box\"><a title=\"{$entry->caption->text}\" rel=\"gallery_swypebox\" class=\"swipebox\" href=\"{$entry->images->standard_resolution->url}\"><img  src=\"{$entry->images->thumbnail->url}\"></a></div>";
	}else{
	    echo "<div class=\"box\"><a title=\"{$entry->caption->text}\" rel=\"gallery_swypebox\" class=\"swipebox\" href=\"{$entry->images->standard_resolution->url}\"><img style=\"width:100%;\" src=\"{$entry->images->standard_resolution->url}\"></a></div>";
	}
  }
}
?>
</div>
<?php

}
}
add_shortcode( 'enjoyinstagram_mb_widget', 'enjoyinstagram_mb_shortcode_widget' );


?>