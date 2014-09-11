<?php
// Add Shortcode
function enjoyinstagram_mb_shortcode_grid_widget($atts) { 
if(get_option('enjoyinstagram_client_id') || get_option('enjoyinstagram_client_id') != '') {
	extract( shortcode_atts( array(
		'id' => 'rigrid_default',
		'n_c' => '6',
		'n_r' => '2',
		'u_or_h' => 'user'
	), $atts ) );
$instagram = new Enjoy_Instagram(get_option('enjoyinstagram_client_id'));
$instagram->setAccessToken(get_option('enjoyinstagram_access_token'));
if("{$u_or_h}"=='hashtag'){
$result = $instagram->getTagMedia(get_option('enjoyinstagram_hashtag'));
}else{
$result = $instagram->getUserMedia(get_option('enjoyinstagram_user_id'));
}
?>
<div id="rigrid-<?php echo "{$id}"; ?>" class="ri-grid ri-grid-size-2 ri-shadow" style="display:none">
    <ul>
<?php

if($result->data){
foreach ($result->data as $entry) {
	
	echo "<li><a title=\"{$entry->caption->text}\" class=\"swipebox_grid\" href=\"{$entry->images->standard_resolution->url}\"><img  src=\"{$entry->images->standard_resolution->url}\"></a></li>";
	
  }
}
?>
    </ul></div>

<script type="text/javascript">	
    

			jQuery(function() {
			
				jQuery('#rigrid-<?php echo "{$id}"; ?>').gridrotator({
					rows		: <?php echo "{$n_r}"; ?>,
					columns		: <?php echo "{$n_c}"; ?>,
					animType	: 'fadeInOut',
					onhover : false,
					interval		: 7000,
					preventClick    : false,
					w1024           : {
    rows    : <?php echo "{$n_r}"; ?>,
    columns : <?php echo "{$n_c}"; ?>
},
 
w768            : {
    rows    : <?php echo "{$n_r}"; ?>,
    columns : <?php echo "{$n_c}"; ?>
},
 
w480            : {
    rows    : <?php echo "{$n_r}"; ?>,
    columns : <?php echo "{$n_c}"; ?>
},
 
w320            : {
    rows    : <?php echo "{$n_r}"; ?>,
    columns : <?php echo "{$n_c}"; ?>
},
 
w240            : {
    rows    : <?php echo "{$n_r}"; ?>,
    columns : <?php echo "{$n_c}"; ?>
}
				});
				jQuery('#rigrid-<?php echo "{$id}"; ?>').fadeIn('slow');
			});

			
		</script>
<?php

}
?>

   
<?php
}

add_shortcode( 'enjoyinstagram_mb_grid_widget', 'enjoyinstagram_mb_shortcode_grid_widget' );



?>
