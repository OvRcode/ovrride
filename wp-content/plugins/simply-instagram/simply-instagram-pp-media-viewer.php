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
<html>
<head>

<style>

body{
	font-family: verdana;
	font-size: 12px !important;
}
.custom-table{

}

#photo-information{
	overflow: scroll; 
	height: 75px;
	font-size: 11px !important;
	width: 315px;
}

.sinst-comment-author{
	color: blue;
}

#photo-statistics{
	height: 75px;
	font-size: 11px !important;
	width: 315px;
}

#photo-comment{
	font-size: 11px !important;
	width: 315px;
	overflow-y: scroll;
	height: 450px;
}

.comments{
	font-family: verdana;
	font-size: 11px !important;
}

.about{
	font-style: italic;
	font-size: 9px;
	opacity: 0.8;
}

</style>

</head>
<body>

<table width="100%" border="0" class="custom-table" >

 <tr>
  <td width="612px">
   <img src="<?php echo $data['data']['images']['standard_resolution']['url']; ?>" width="612px">
  </td>
  
  <td>
  	<table class="custom-table">
  	
  	 <tr>
  	  <td>
  	  	<div id="photo-information">
  	  	 <img src="<?php echo $data['data']['caption']['from']['profile_picture']; ?>" width="35px" valign="middle"/> 
  	  	  <span class="sinst-comment-author"><?php echo $data['data']['caption']['from']['username']; ?></span> 
  	  	  on <?php echo date( "M d, Y", $data['data']['caption']['created_time'] ); ?> <br/>
  	  	  <?php echo $data['data']['caption']['text']; ?>
  	  	</div>
  	  </td>
  	 </tr>
  	 
  	 <?php
  	 	if( $data['data']['likes']['count'] > 0 ):
  	 ?>
  	 <tr>
  	  <td>
  	  	<div id="photo-statistics">
  	  	 <strong>Likers</strong>: 
  	  	 <?php
			for( $i=0; $i < count( $data['data']['likes']['data'] ); $i++ ):
				echo $data['data']['likes']['data'][$i]['username'] . " ";
			endfor;
		?>
  	  	</div>
  	  </td>
  	 </tr>
  	 <?php
  	 	endif;
  	 ?>
  	 
  	 <tr>
  	  <td>
  	    	<div id="photo-comment">
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
				echo '<table class="comments">';
				echo '<tr>';
				echo '<td valign="top"><img src="' . $cdata['data'][$i]['from']['profile_picture'] . '" width="35px"/></td>';				
				echo '<td valign="top"><span class="sinst-comment-author">' . $cdata['data'][$i]['from']['username'] . '</span><span class="about"> About ' . nicetime( date( "Y-m-j g:i", $cdata['data'][$i]['created_time'] ) ) . '</span><br/>' . $cdata['data'][$i]['text'] . '<br/></td>';
				echo '</tr>';
				echo '</table>';
			endfor;
			
		?>
		</div>
  	  </td>
  	 </tr>
  	 
  	</table>
  </td>
  
 </tr>
 
</table>
</body>
</html>