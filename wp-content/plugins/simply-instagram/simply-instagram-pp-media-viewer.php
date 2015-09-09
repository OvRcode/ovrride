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
<link href='http://fonts.googleapis.com/css?family=Tillana:500,600,700' rel='stylesheet' type='text/css'>
<style>

::-webkit-scrollbar {
    width: 12px;
}
 
::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
    border-radius: 10px;
}
 
::-webkit-scrollbar-thumb {
    border-radius: 10px;
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5); 
}

body{
	font-family: 'Tillana', cursive;
	font-size: 20px !important;
}
.custom-table{

}

#photo-information{	
	min-height: 75px;
	font-size: 15px !important;
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
	font-size: 14px !important;
	width: 315px;
	height: 450px;
}

.comments{
	font-family: 'Tillana', cursive;
	font-size: 14px !important;
}

.about{
	font-style: italic;
	font-size: 9px;
	opacity: 0.8;
}
.si-photographer {
  width: 15%;
  vertical-align: middle;
  box-shadow: none !important;
  border-radius: 50%;
  border: 1px solid #fff !important;
  /* box-sizing: border-box; */
  -webkit-flex-shrink: 0;
  -ms-flex-negative: 0;
  flex-shrink: 0;
}
.si-comment-profile{
  width: 65%;
  box-shadow: none !important;
  border-radius: 50%;
  border: 1px solid #fff !important;
  /* box-sizing: border-box; */
  -webkit-flex-shrink: 0;
  -ms-flex-negative: 0;
  flex-shrink: 0;
}
</style>
</head>
<body>

<table width="90%" border="0" class="custom-table" >

 <tr>
  <td width="612px" valign="top">
   <img src="<?php echo $data['data']['images']['standard_resolution']['url']; ?>" width="612px" style="position:fixed;">
  </td>  
  
  <td style="overflow: none; position: absolute;">
  	<div id="photo-information">
  	 <img src="<?php echo $data['data']['caption']['from']['profile_picture']; ?>"  class="si-photographer" valign="middle"/> 
  	 <span class="sinst-comment-author"><strong><?php echo $data['data']['caption']['from']['username']; ?></strong></span> 
  	 <p style="padding: 5px;"><?php echo $data['data']['caption']['text']; ?></p>
  	</div>
  	 <?php
  	 	if( $data['data']['likes']['count'] > 0 ):
  	 ?>
  	 
  	  	<div id="photo-statistics">
  	  	 <br/>
  	  	 <p><strong>
  	  	 <?php
			echo $data['data']['likes']['count'];
		?> likes</strong> </p>
  	  	</div>
  	 <?php
  	 	endif;
  	 ?>
  	 
  	    	<!-- <div id="photo-comment"> -->
  	  	<?php
			$apicommenturl = "https://api.instagram.com/v1/media/" . $data['data']['id'] . "/comments?access_token=" . $_GET['access_token'];
			
			$context = stream_context_create(array('http' => array('header'=>'Connection: close')));
			$cresponse = file_get_contents( $apicommenturl, false, $context );
			$cdata = json_decode( $cresponse, true );
			
			if( count( $cdata['data'] ) > 0 ){
				echo '<p><strong>' . count( $cdata['data'] ) . ' comments</strong></p>';
				for( $i=0; $i < count( $cdata['data'] ); $i++ ):
					echo '<table class="comments" border="0">';
					echo '<tr>';
					echo '<td valign="top" width="20%"><img src="' . $cdata['data'][$i]['from']['profile_picture'] . '" class="si-comment-profile"/></td>';				
					echo '<td valign="top"><span class="sinst-comment-author">' . $cdata['data'][$i]['from']['username'] . '</span><br/>' . $cdata['data'][$i]['text'] . '<br/></td>';
					echo '</tr>';
					echo '</table>';
				endfor;
			}else{
				echo '<p><strong>There&#39;s no comment on this photo</strong></p>';
			}
			
		?>
		<!-- </div> -->
  	  
  	
  </td>
  
 </tr>
 
</table>
</body>
</html>