<?php
	//include '../css/simplyInstagram.css';
	$apiurl = "https://api.instagram.com/v1/media/" . $_GET['mid'] . "?access_token=" . $_GET['access_token'];
	
	$context = stream_context_create(array('http' => array('header'=>'Connection: close')));
	$response = file_get_contents( $apiurl, false, $context );
	$data = json_decode( $response, true );
	
	function nicetime($date)
	{
	    if(empty($date)) {
	        return "No date provided";
	    }
	    
	    $periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	    $lengths         = array("60","60","24","7","4.35","12","10");
	    
	    $now             = time();
	    $unix_date         = strtotime($date);
	    
	       // check validity of date
	    if(empty($unix_date)) {    
	        return "Bad date";
	    }
	
	    // is it future date or past date
	    if($now > $unix_date) {    
	        $difference     = $now - $unix_date;
	        $tense         = "ago";
	        
	    } else {
	        $difference     = $unix_date - $now;
	        $tense         = "from now";
	    }
	    
	    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
	        $difference /= $lengths[$j];
	    }
	    
	    $difference = round($difference);
	    
	    if($difference != 1) {
	        $periods[$j].= "s";
	    }
	    
	    return "$difference $periods[$j] {$tense}";
	}
		
?>
<div id="image-holder"><img src="<?php echo $data['data']['images']['standard_resolution']['url']; ?>"></div>
<div id="image-description">
 <img src="<?php echo $data['data']['caption']['from']['profile_picture']; ?>" /> <span class="sinst-comment-author"><?php echo $data['data']['caption']['from']['username']; ?></span> on <?php echo date( "M d, Y", $data['data']['caption']['created_time'] ); ?>
 <div class="clear"></div>
 <?php echo $data['data']['caption']['text']; ?>
</div>
<div id="image-statistics"><span class="sinst-comment-author">Likers: </span>
<?php
	for( $i=0; $i < count( $data['data']['likes']['data'] ); $i++ ):
		echo $data['data']['likes']['data'][$i]['username'] . " ";
	endfor;
?>
</div>
<div id="image-comments">
<?php
	$apicommenturl = "https://api.instagram.com/v1/media/" . $data['data']['id'] . "/comments?access_token=" . $_GET['access_token'];
	
	$context = stream_context_create(array('http' => array('header'=>'Connection: close')));
	$cresponse = file_get_contents( $apicommenturl, false, $context );
	$cdata = json_decode( $cresponse, true );
	
	if( $_GET['mdc'] > count( $cdata['data'] ) ):
		$ctd = count( $cdata['data'] );
	elseif( count( $cdata['data'] ) < 1 ):
		$ctd = count( $cdata['data'] );
	else:
		$ctd = $_GET['mdc'];
	endif;
	//echo $ctd;
	for( $i=0; $i < $ctd; $i++ ):
		echo '<div class="comments-holder">';
		echo '<div class="comment-profile"><img src="' . $cdata['data'][$i]['from']['profile_picture'] . '" /></div>';
		echo '<div class="comment-holder"><span class="sinst-comment-author">' . $cdata['data'][$i]['from']['username'] . '</span><br/>' . $cdata['data'][$i]['text'] . '<br/>About ' . nicetime( date( "Y-m-j g:i", $cdata['data'][$i]['created_time'] ) ) . '</div>';
		echo '</div>';
	endfor;
	
?>
</div>