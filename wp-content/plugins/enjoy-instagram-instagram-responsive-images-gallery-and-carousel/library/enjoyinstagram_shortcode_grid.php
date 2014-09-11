<?php
// Add Shortcode
function enjoyinstagram_mb_shortcode_grid() { 
STATIC $i = 1;
if(get_option('enjoyinstagram_client_id') || get_option('enjoyinstagram_client_id') != '') {

$instagram = new Enjoy_Instagram(get_option('enjoyinstagram_client_id'));
$instagram->setAccessToken(get_option('enjoyinstagram_access_token'));
if(get_option('enjoyinstagram_user_or_hashtag')=='hashtag'){
$result = $instagram->getTagMedia(get_option('enjoyinstagram_hashtag'));
}else{
$result = $instagram->getUserMedia(get_option('enjoyinstagram_user_id'));
}

$pre_shortcode_content = "<div id=\"grid-".$i."\" class=\"ri-grid ri-grid-size-2 ri-shadow\" style=\"display:none;\"><ul>";

    


foreach ($result->data as $entry) {
	
	$shortcode_content .=  "<li><a title=\"{$entry->caption->text}\" class=\"swipebox_grid\" href=\"{$entry->images->standard_resolution->url}\"><img  src=\"{$entry->images->standard_resolution->url}\"></a></li>";
	
  }
  
$post_shortcode_content = "</ul></div>";
  
?>

    

<script type="text/javascript">	
    
			jQuery(function() {
				jQuery('#grid-<?php echo $i; ?>').gridrotator({
					rows		: <?php echo get_option('enjoyinstagram_grid_rows'); ?>,
					columns		: <?php echo get_option('enjoyinstagram_grid_cols'); ?>,
					animType	: 'fadeInOut',
					onhover : false,
					interval		: 7000,
					preventClick    : false,
					w1400           : {
    rows    : <?php echo get_option('enjoyinstagram_grid_rows'); ?>,
    columns : <?php echo get_option('enjoyinstagram_grid_cols'); ?>
},
					w1024           : {
    rows    : <?php echo get_option('enjoyinstagram_grid_rows'); ?>,
    columns : <?php echo get_option('enjoyinstagram_grid_cols'); ?>
},
 
w768            : {
    rows    : <?php echo get_option('enjoyinstagram_grid_rows'); ?>,
    columns : <?php echo get_option('enjoyinstagram_grid_cols'); ?>
},
 
w480            : {
    rows    : <?php echo get_option('enjoyinstagram_grid_rows'); ?>,
    columns : <?php echo get_option('enjoyinstagram_grid_cols'); ?>
},
 
w320            : {
    rows    : <?php echo get_option('enjoyinstagram_grid_rows'); ?>,
    columns : <?php echo get_option('enjoyinstagram_grid_cols'); ?>
},
 
w240            : {
    rows    : <?php echo get_option('enjoyinstagram_grid_rows'); ?>,
    columns : <?php echo get_option('enjoyinstagram_grid_cols'); ?>
}
				});
				
			jQuery('#grid-<?php echo $i; ?>').fadeIn('1000');
			
			
			});
			
		</script>
<?php

}
$i++;

$shortcode_content = $pre_shortcode_content.$shortcode_content.$post_shortcode_content;

return $shortcode_content;
}

add_shortcode( 'enjoyinstagram_mb_grid', 'enjoyinstagram_mb_shortcode_grid' );



?>
