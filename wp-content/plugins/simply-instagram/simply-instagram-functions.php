<?php

/** 
 * SHow info
 * name
 * bio
 * website
 * followers
 * following
 * profile_pic
 * instagram page
*/
function sInstShowInfo( $data, $args=array(), $width="150" )
{
	$infos = '<div class="simplyInstagram-profile">';
	
	if( $args['profile_pic'] == "true" ):
		$infos .= '<div class="profile-data"><img src="' . $data['data']['profile_picture'] . '" style="width: ' . $width . 'pixels;"/></div>';	
	endif;
	
	if( $args['name'] == "true" ):
		$infos .= '<div class="profile-data"><h1>' . $data['data']['full_name'] . '</h1></div>';
	endif;
		$infos .= '<div class="profile-data">' . sIntFollowButton( user_id(), $data['data']['username'] ) . '</div>';
	if( $args['bio'] == "true" ):
		$infos .= '<div id="profile-bio">' . $data['data']['bio'] . '</div>';
	endif;
	
	if( $args['website'] == "true" ):
		$infos .= '<div class="profile-data"><a href="' . $data['data']['website'] . '" target="_blank" />' . $data['data']['website'] . '</a></div>';
	endif;
		$infos .= '<div class="data-holder">';
	if( $args['media'] == "true" ):
		$infos .= '<span class="profile-media">' . $data['data']['counts']['media'] . '<div class="profile-media-content">Photo</div></span>';
	endif;
	
	if( $args['followers'] == "true" ):
		$infos .= '<span class="profile-media">' . $data['data']['counts']['followed_by'] . '<div class="profile-media-content">Followers</div></span>';
	endif;
	
	if( $args['following'] == "true" ):
		$infos .= '<span class="profile-media">' . $data['data']['counts']['follows'] . '<div class="profile-media-content">Followings</div></span>';
	endif;
		$infos .= '</div>';
	$infos .= '</div>';
	
	echo $infos;
}

/**
* Get self feed
*/
function sInstGetSelfFeed( $access_token )
{
	$apiurl = "https://api.instagram.com/v1/users/self/feed?access_token=" . $access_token ;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
	
          	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
        endif;
       	
       	file_put_contents( simply_instagram_plugin_path . '/cache-api/selffeed.txt', $data );
	
	return $data;
}

/**
* Show data in widget form
*/
function sInstShowWidgetData( $data, $count='9', $width='75', $customRel="sIntWidget", $displayCaption="true" )
{
	/**
	 * Determine query return
	 * next query used to avoid
	 * blank return when display value is 
	 * greater than API return
	*/
	
	if( count( $data['data'] ) > $count ):
		$query = $count;
	else:
		$query = count( $data['data'] );
	endif;
	
 	for( $i = 0; $i < $query; $i++ ):
		$output = '<a href="' . $data['data'][$i]['images']['standard_resolution']['url'] . '" rel="' . $customRel . '[instagram]" title="' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ). '">';
			$output .= '<div class="si-content" style=" display: none; margin: 10px; "><div class="clear"></div>';
			
			/**
			 * Option to display caption.
			 * Page often breaks when caption is to long
			 * because prettyPhot can't handle it.
			*/			
			if(  $displayCaption == "true" ):
			 $output .= htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ). '<div class="clear"></div>';
			endif;
			
			$output .= '<div class="content-info"><img class="front-photo" src="' . $data['data'][$i]['caption']['from']['profile_picture'] . '" width="15" height="15"/>' . $data['data'][$i]['caption']['from']['username'] . '</div>'; 
			$output .= '<div class="content-info"><img src="' . plugins_url('/simply-instagram/images/instagram-like.png') . '" width="19" height="19" style="vertical-align: middle;" /> ' . $data['data'][$i]['likes']['count'] . '</div>';
			$output .= '<div class="content-info"><img src="' . plugins_url('/simply-instagram/images/instagram-comment.png') . '" width="19" height="19" style="vertical-align: middle;" /> ' . $data['data'][$i]['comments']['count'] . '</div>';
			$output .= '<div class="clear"></div></div>';			
			$output .= '<img class="front-photo" src="' . sInstCache( $data['data'][$i]['images']['thumbnail']['url'], $width ) . '" width="' . $width .'" height="' . $width . '" title="' . $data['data'][$i]['caption']['text'] . '">';
			$output .= "</a>";
						
			echo $output;		
	endfor;
}

/**
 * Check if access token exist
 * return null otherwise
*/
function access_token()
{
	global $wpdb;
	
	$getAccessToken = get_option('si_access_token');
	if( $getAccessToken ):
		return $getAccessToken;
	else:
		return null;
	endif;
}

/**
 * Check if user_is exist
 * return null otherwise
*/
function user_id()
{
	global $wpdb;
	
	$getUserID = get_option('si_user_id');
	if( $getUserID ):	
		return $getUserID;
	else:
		return null;
	endif;
}

/**
* Get the list of users this user follows.
*/
function sInstGetFollowing( $user_id, $access_token )
{
	$apiurl = "https://api.instagram.com/v1/users/" . $user_id . "/follows?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
	
         	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
        endif;
       	
	
	return $data;	
}


/**
* Get the list of users this user is followed by.
*/
function sInstGetFollowers( $user_id, $access_token )
{
	$apiurl = "https://api.instagram.com/v1/users/" . $user_id . "/followed-by?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
	
          	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
        endif;
       	
	
	return $data;
}

/**
 * See the authenticated user's list of media they've liked. 
 * Note that this list is ordered by the order in which the user liked the media. 
 * Private media is returned as long as the authenticated user has permission to view that media. 
 * Liked media lists are only available for the currently authenticated user.
*/
function sInstGetLikes( $access_token )
{
	$apiurl = "https://api.instagram.com/v1/users/self/media/liked?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
          	
          	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
        endif;
       	
	
	return $data;	
}

/**
* Get the most recent media published by a user.
*/
function sInstGetRecentMedia( $user_id, $access_token )
{
	$apiurl = "https://api.instagram.com/v1/users/" . $user_id . "/media/recent/?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
         	
         	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
        endif;
       	
	
	return $data;
}

/**
* See the authenticated user's feed.
*/
function simply_instagram_get_feed( $access_token )
{
	$apiurl = "https://api.instagram.com/v1/users/self/feed?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
	
          	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
        endif;
       	
	
	return $data;
}

/**
* Get basic information about a user.
*/
function sInstGetInfo( $user_id, $access_token )
{
	$apiurl = "https://api.instagram.com/v1/users/" . $user_id . "/?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
	
          	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
        endif;
       	
	
	return $data;	
}

/**
* most-popular.
*/
function sInstGetMostPopular( $media, $access_token )
{
	$apiurl = "https://api.instagram.com/v1/media/" . $media . "?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
	
          	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
        endif;
       	
	file_put_contents( simply_instagram_plugin_path . '/cache-api/selffeed.txt', serialize( $response )  );
	return $data;	
}

/**
* Check if already following
*/
function sInstGetFollowingInfo( $user_id, $access_token )
{
	$apiurl = "https://api.instagram.com/v1/users/" . $user_id . "/relationship?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
	        
	        $curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
        endif;
       	
	
	return $data;	
}

/**
* Show most popular randomly. Im using lightbox for viewing each photo.
* htmlspecialchars() is used to filter caption text with illegal
* char that may cause display bug.
*/
function sInstDisplayData( $data, $size='low_resolution', $display="20", $width="150", $customRel="sIntWid" )
{	
	
	/**
	 * Determine query return
	 * next query used to avoid
	 * blank return when display value is 
	 * greater than API return
	*/
	
	if( count( $data['data'] ) > $display ):
		$query = $display;
		$pagination = 0;
	else:
		/**
		 * Pagination starts here
		 * check if next url exist
		*/
		//( $data['pagination'] ? $query = $display : $query = count( $data['data'] ) );
		$query = count( $data['data'] );
		$pagination = 1;
	endif;
		for( $i=0; $i < $query; $i++ ):
			
			$output = '<div class="masonryItem" data-id="' . $data['data'][$i]['id'] . '">';
			
			/**
			 * If user choose to use builtInMediaViewer,
			 * Display this area
			*/
			if( get_option( 'mediaViewer' ) == "builtInMediaViewer" ):			
			
				$output .= '<div class="item-holder" data-id="' . $data['data'][$i]['id'] . '">';			
				
				 /**
				  * Display image, description and statistic
				 **/
				 $output .= '<a href="#" rel="' . plugins_url( 'simply-instagram/simply-instagram-media.php?mid=' . ( $data['data'][$i]['id'] ) . '&access_token=' . access_token() . '&mdc=' . get_option( 'displayCommentMediaViewer' ) ) . '" id="' . $data['data'][$i]['id'] . '" class="overlay" style="text-decoration:none">';
				  $output .= '<img class="front-photo" src="' . sInstCache( $data['data'][$i]['images']['thumbnail']['url'], "150" )  . '" width="150" height="150" title="' . $data['data'][$i]['caption']['text'] . '">';
				 $output .= '</a>';
				 
				 $output .=  '</div>';
				 
				 /**
				  * Determine if displayDescription is allowed
				 */
				 get_option( 'displayDescription' ) == "true" ? $output .= '<p>' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ) . '</p>' : null ;
				 
				  /**
				  * Determine if displayStatistic is allowed
				 */
				 if( get_option( 'displayStatistic' ) == "true" ):
				 
				 $output .= '<div class="scode-content-info"><img src="' . plugins_url('/simply-instagram/images/instagram-like.png') . '" width="12" height="12" style="vertical-align: middle;" /> ' . $data['data'][$i]['likes']['count'] . '</div>';
				 $output .= '<div class="scode-content-info"><img src="' . plugins_url('/simply-instagram/images/instagram-comment.png') . '" width="12" height="12" style="vertical-align: middle;"  /> ' . $data['data'][$i]['comments']['count'] . '</div>';
				 
				 endif;
				 
				 /**
				  * highlights photographer
				  * using author section class
				 */
				 if( get_option( 'displayPhotographer' ) == "true" ):
				 $output .= '<div class="sinst-author-section">';
				  $output .= '<div class="sinst-author">';
				   $output .= '<img src="' . sInstCache( $data['data'][$i]['user']['profile_picture'], "20" ) . '" width="20" height="20" style="vertical-align: middle;" /> <span class="sinst-comment-author">' . $data['data'][$i]['user']['username'] . '</span> ';
				  $output .= '</div>';
				 $output .= '</div>';
				 endif;	 
				 /**
				  * Comment section
				  * Check if comment exist
				  * Display latest # comments on media
				  * based on option
				 */
				 if( $data['data'][$i]['comments']['count'] != 0 ): //if there's comment
				 
				 if( get_option( 'displayComment' ) != 0 ): //user choose to display comment
				 	 
					 $output .= '<div class="sinst-comment-section">';
					 
					 /**
					  * Determine comment to be displayed
					 */		 
					 if( $data['data'][$i]['comments']['count'] > 5 || get_option( 'displayComment' ) > 5 ):
					 	$cc = 5;
					 else:
					 	$cc = $data['data'][$i]['comments']['count'];
					 endif;
						 
					 for( $c=0; $c < $cc ; $c++ ):
						 
					 	$output .= '<div class="sinst-comments">';
					 	$output .= '<img src="' . sInstCache( $data['data'][$i]['comments']['data'][$c]['from']['profile_picture'], "20" ) . '" width="20" height="20" style="vertical-align: middle;	"/>';
					 	$output .= ' <span class="sinst-comment-author">' . $data['data'][$i]['comments']['data'][$c]['from']['username'] . '</span> ' . htmlspecialchars( $data['data'][$i]['comments']['data'][$c]['text'], ENT_QUOTES ) . '<br /> About ' . nicetime( date( "Y-m-j g:i", $data['data'][$i]['comments']['data'][$c]['created_time'] ) );
					 	$output .= '</div>';
						 	
					 endfor;			 		
					 $output .= '</div>';
				 endif;
				  
				 endif;
			 
			elseif( get_option( 'mediaViewer' ) == "prettyPhoto" ):
			/**
			 * else user choose to use prettyPhoto slideshow
			 * display this area.
			*/
				$output .= '<div class="item-holder" data-id="' . $data['data'][$i]['id'] . '">';			
				
				 /**
				  * Display image, description and statistic
				 **/
				$output .= '<a href="' . $data['data'][$i]['images'][$size]['url'] . '" rel="sIntSC[instagram]" title="' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ). '">';
				$output .= '<div class="si-content" style=" display: none; margin: 10px; "><div class="break-line" style="height: 10px;"></div>';
				/**
				 * If user choose to display photo description 
				 * in settings, display this area
				*/
				if( get_option( 'ppPhotoDescription' )  == "true" ):
				
				 /**
				  * check if description exist to avoid big spacing
				 */
				 if( $data['data'][$i]['caption']['text'] != "" ):				 
					$output .= htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ). '<div class="break-line" style="height: 10px;"></div>';
				 endif; //end of description checking
				endif; // end of ppPhotoDescription
				
				/**
				 * If user choose to display photographer
				 * profile picture in settings, display this area
				*/
				 
				if( get_option( 'ppDisplayPhotographer' )  == "true" ):	
					$output .= '<div class="content-info"><img class="front-photo" src="' . sInstCache( $data['data'][$i]['caption']['from']['profile_picture'], "15" ) . '" width="15" height="15"/>' . $data['data'][$i]['caption']['from']['username'] . '</div>'; 
				endif; // end of ppPhotoDescription
				
				/**
				 * If user choose to display statistics
				 * in settings, display this area
				*/
				if( get_option( 'ppDisplayStatistic' )  == "true" ):	
					$output .= '<div class="content-info"><img src="' . plugins_url('/simply-instagram/images/instagram-like.png') . '" width="19" height="19" style="vertical-align: middle;" /> ' .  $data['data'][$i]['likes']['count'] . '</div>';
					$output .= '<div class="content-info"><img src="' . plugins_url('/simply-instagram/images/instagram-comment.png') . '" width="19" height="19" style="vertical-align: middle;" /> ' .  $data['data'][$i]['comments']['count'] . '</div>';
				endif; // end of ppPhotoDescription
					$output .= '<div class="clear"></div></div>';
								
				
				
				$output .= '<img class="front-photo" src="' . sInstCache( $data['data'][$i]['images']['thumbnail']['url'], $width ) . '" width="' . $width .'" height="' . $width .'" title="' . $data['data'][$i]['caption']['text'] . '">';
				
				$output .= "</a>";
				 
				 $output .=  '</div>';
				 
				 /**
				  * Determine if displayDescription is allowed
				 */
				 get_option( 'displayDescription' ) == "true" ? $output .= '<p>' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ) . '</p>' : null ;
				 
				  /**
				  * Determine if displayStatistic is allowed
				 */
				 if( get_option( 'displayStatistic' ) == "true" ):
				 
				 $output .= '<div class="scode-content-info"><img src="' . plugins_url('/simply-instagram/images/instagram-like.png') . '" width="12" height="12" style="vertical-align: middle;" /> ' . $data['data'][$i]['likes']['count'] . '</div>';
				 $output .= '<div class="scode-content-info"><img src="' . plugins_url('/simply-instagram/images/instagram-comment.png') . '" width="12" height="12" style="vertical-align: middle;" /> ' . $data['data'][$i]['comments']['count'] . '</div>';
				 
				 endif;
				 
				 /**
				  * highlights photographer
				  * using author section class
				 */
				 if( get_option( 'displayPhotographer' ) == "true" ):
				 $output .= '<div class="sinst-author-section">';
				  $output .= '<div class="sinst-author">';
				   $output .= '<img src="' . sInstCache( $data['data'][$i]['user']['profile_picture'], "20" ) . '" width="20" height="20" style="vertical-align: middle;	" /> <span class="sinst-comment-author">' . $data['data'][$i]['user']['username'] . '</span> ';
				  $output .= '</div>';
				 $output .= '</div>';
				 endif;	 
				 /**
				  * Comment section
				  * Check if comment exist
				  * Display latest # comments on media
				  * based on option
				 */
				 if( $data['data'][$i]['comments']['count'] != 0 ): //if there's comment
				 
				 if( get_option( 'displayComment' ) != 0 ): //user choose to display comment
				 	 
					 $output .= '<div class="sinst-comment-section">';
					 
					 /**
					  * Determine comment to be displayed
					 */		 
					 if( $data['data'][$i]['comments']['count'] > 5 || get_option( 'displayComment' ) > 5 ):
					 	$cc = 5;
					 else:
					 	$cc = $data['data'][$i]['comments']['count'];
					 endif;
						 
					 for( $c=0; $c < $cc ; $c++ ):
						 
					 	$output .= '<div class="sinst-comments">';
					 	$output .= '<img src="' . sInstCache( $data['data'][$i]['comments']['data'][$c]['from']['profile_picture'], "20" ) . '" width="20" height="20" style="vertical-align: middle;	"/>'; 
					 	$output .= ' <span class="sinst-comment-author">' . $data['data'][$i]['comments']['data'][$c]['from']['username'] . '</span> ' . htmlspecialchars( $data['data'][$i]['comments']['data'][$c]['text'], ENT_QUOTES ) . '<br /> About ' . nicetime( date( "Y-m-j g:i", $data['data'][$i]['comments']['data'][$c]['created_time'] ) );
					 	$output .= '</div>';
						 	
					 endfor;			 		
					 $output .= '</div>';
				 endif;
				  
				 endif;
			elseif( get_option( 'mediaViewer' ) == "instagramLink" ):
			/**
			 * else user choose to use Instagram site
			 * make new link to open in new tab / window
			*/
			
			$output .= '<div class="item-holder" data-id="' . $data['data'][$i]['id'] . '">';
			$output .= '<a href="' . $data['data'][$i]['link'] . '" target="_blank">';
			$output .= '<img class="front-photo" src="' . sInstCache( $data['data'][$i]['images']['thumbnail']['url'], "150" ) . '" width="150" height="150" title="' . $data['data'][$i]['caption']['text'] . '">';
			$output .= '</a>';
			$output .=  '</div>';
			
			 /**
				  * Determine if displayDescription is allowed
				 */
				 get_option( 'displayDescription' ) == "true" ? $output .= '<p>' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ) . '</p>' : null ;
				 
				  /**
				  * Determine if displayStatistic is allowed
				 */
				 if( get_option( 'displayStatistic' ) == "true" ):
				 
				 $output .= '<div class="scode-content-info"><img src="' . plugins_url('/simply-instagram/images/instagram-like.png') . '" width="12" height="12" style="vertical-align: middle;"  /> ' . $data['data'][$i]['likes']['count'] . '</div>';
				 $output .= '<div class="scode-content-info"><img src="' . plugins_url('/simply-instagram/images/instagram-comment.png') . '" width="12" height="12" style="vertical-align: middle;" /> ' . $data['data'][$i]['comments']['count'] . '</div>';
				 
				 endif;

/**
				  * highlights photographer
				  * using author section class
				 */
				 if( get_option( 'displayPhotographer' ) == "true" ):
				 $output .= '<div class="sinst-author-section">';
				  $output .= '<div class="sinst-author">';
				   $output .= '<img src="' . sInstCache( $data['data'][$i]['user']['profile_picture'], "20" ) . '" width="20" height="20" style="vertical-align: middle;	" /> <span class="sinst-comment-author">' . $data['data'][$i]['user']['username'] . '</span> ';
				  $output .= '</div>';
				 $output .= '</div>';
				 endif;	 
/**
				  * Comment section
				  * Check if comment exist
				  * Display latest # comments on media
				  * based on option
				 */
				 if( $data['data'][$i]['comments']['count'] != 0 ): //if there's comment
				 
				 if( get_option( 'displayComment' ) != 0 ): //user choose to display comment
				 	 
					 $output .= '<div class="sinst-comment-section">';
					 
					 /**
					  * Determine comment to be displayed
					 */		 
					 if( $data['data'][$i]['comments']['count'] > 5 || get_option( 'displayComment' ) > 5 ):
					 	$cc = 5;
					 else:
					 	$cc = $data['data'][$i]['comments']['count'];
					 endif;
						 
					 for( $c=0; $c < $cc ; $c++ ):
						 
					 	$output .= '<div class="sinst-comments">';
					 	$output .= '<img src="' . sInstCache( $data['data'][$i]['comments']['data'][$c]['from']['profile_picture'], "20" ) . '" width="20" height="20" style="vertical-align: middle;	"/>';
					 	$output .= ' <span class="sinst-comment-author">' . $data['data'][$i]['comments']['data'][$c]['from']['username'] . '</span> ' . htmlspecialchars( $data['data'][$i]['comments']['data'][$c]['text'], ENT_QUOTES ) . '<br /> About ' . nicetime( date( "Y-m-j g:i", $data['data'][$i]['comments']['data'][$c]['created_time'] ) );
					 	$output .= '</div>';
						 	
					 endfor;			 		
					 $output .= '</div>';
				 endif;
				  
				 endif;
			
			else:
			/**
			 * else user choose to use prettyPhoto iframe
			 * display this area.
			*/
				$output .= '<div class="item-holder" data-id="' . $data['data'][$i]['id'] . '">';			
				
				 /**
				  * Display image, description and statistic
				 **/
				 $output .= '<a href="' . plugins_url( 'simply-instagram/simply-instagram-pp-media-viewer.php?mid=' . ( $data['data'][$i]['id'] ) . '&access_token=' . access_token() . '&mdc=' . get_option( 'displayCommentMediaViewer' ) ) . '&iframe=true&width=960&height=650&scrolling=no" rel="prettyphoto" style="text-decoration:none">';
				  $output .= '<img class="front-photo" src="' . sInstCache( $data['data'][$i]['images']['thumbnail']['url'], "150" ) . '" width="150" height="150" title="' . $data['data'][$i]['caption']['text'] . '">';
				 $output .= '</a>';
				 
				 $output .=  '</div>';				 
				 
				 /**
				  * Determine if displayDescription is allowed
				 */
				 get_option( 'displayDescription' ) == "true" ? $output .= '<p>' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ) . '</p>' : null ;
				 
				  /**
				  * Determine if displayStatistic is allowed
				 */
				 if( get_option( 'displayStatistic' ) == "true" ):
				 
				 $output .= '<div class="scode-content-info"><img src="' . plugins_url('/simply-instagram/images/instagram-like.png') . '" width="12" height="12" style="vertical-align: middle;"  /> ' . $data['data'][$i]['likes']['count'] . '</div>';
				 $output .= '<div class="scode-content-info"><img src="' . plugins_url('/simply-instagram/images/instagram-comment.png') . '" width="12" height="12" style="vertical-align: middle;" /> ' . $data['data'][$i]['comments']['count'] . '</div>';
				 
				 endif;
				 
				 /**
				  * highlights photographer
				  * using author section class
				 */
				 if( get_option( 'displayPhotographer' ) == "true" ):
				 $output .= '<div class="sinst-author-section">';
				  $output .= '<div class="sinst-author">';
				   $output .= '<img src="' . sInstCache( $data['data'][$i]['user']['profile_picture'], "20" ) . '" width="20" height="20" style="vertical-align: middle;	" /> <span class="sinst-comment-author">' . $data['data'][$i]['user']['username'] . '</span> ';
				  $output .= '</div>';
				 $output .= '</div>';
				 endif;	 
				 /**
				  * Comment section
				  * Check if comment exist
				  * Display latest # comments on media
				  * based on option
				 */
				 if( $data['data'][$i]['comments']['count'] != 0 ): //if there's comment
				 
				 if( get_option( 'displayComment' ) != 0 ): //user choose to display comment
				 	 
					 $output .= '<div class="sinst-comment-section">';
					 
					 /**
					  * Determine comment to be displayed
					 */		 
					 if( $data['data'][$i]['comments']['count'] > 5 || get_option( 'displayComment' ) > 5 ):
					 	$cc = 5;
					 else:
					 	$cc = $data['data'][$i]['comments']['count'];
					 endif;
						 
					 for( $c=0; $c < $cc ; $c++ ):
						 
					 	$output .= '<div class="sinst-comments">';
					 	$output .= '<img src="' . sInstCache( $data['data'][$i]['comments']['data'][$c]['from']['profile_picture'], "20" ) . '" width="20" height="20" style="vertical-align: middle;	"/>';
					 	$output .= ' <span class="sinst-comment-author">' . $data['data'][$i]['comments']['data'][$c]['from']['username'] . '</span> ' . htmlspecialchars( $data['data'][$i]['comments']['data'][$c]['text'], ENT_QUOTES ) . '<br /> About ' . nicetime( date( "Y-m-j g:i", $data['data'][$i]['comments']['data'][$c]['created_time'] ) );
					 	$output .= '</div>';
						 	
					 endfor;			 		
					 $output .= '</div>';
				 endif;
				  
				 endif;
			
			endif;	 // end of mediaViewer
			
			$output .= '</div>';
			
			echo $output;
				
		endfor;
		//return $arr;
			//do{
			//	return $output;
			//	
			//	$i++;
			//}while( $i < $query );
		//if( $pagination > 0 ):
		//	echo '<input type="hidden" name="sPaginate" value="' . $data['pagination']['next_url'] . '" />';
		//endif;
		/* else:
			/**
			 * create pagination
			 * store everything in array
			*
			$content = array();
			$comment = array();
			
			for( $i=0; $i < count( $data['data'] ); $i++ ):
			
			 if( $data['data'][$i]['comments']['count'] != 0 ):
				( $data['data'][$i]['comments']['count'] > 5 ? $cc = 5 : $cc = $data['data'][$i]['comments']['count'] );
				for( $c=0; $c < $cc ; $c++ ):
					$comment[$c] = array( 'profile_picture' => $data['data'][$i]['comments']['data'][$c]['from']['profile_picture'],
								'user_name' => $data['data'][$i]['comments']['data'][$c]['from']['username'],
								'text' => $data['data'][$i]['comments']['data'][$c]['text'],
								'commented_on' => $data['data'][$i]['comments']['data'][$c]['created_time'] );
				endfor;
			 endif;
				$content[$i] = array( 'image' => $data['data'][$i]['images']['low_resolution']['url'], 
							'image_caption' => $data['data'][$i]['caption']['text'],
							'like_count' => $data['data'][$i]['likes']['count'],
							'comment_count' => $data['data'][$i]['comments']['count'],
							'author_profile' => $data['data'][$i]['user']['profile_picture'],
							'author_username' => $data['data'][$i]['user']['username'],
							'comments' => $comment );
				
			endfor;
			
			//print_r( $content );
		endif; */
		
}

function sInstDiplayFollowData( $data, $display="20", $width="150", $showFollowerData = false )
{
	/**
	 * Determine query return
	 * next query used to avoid
	 * blank return when display value is 
	 * greater than API return
	*/
	
	if( count( $data['data'] ) > $display ):
		$query = $display;
	else:
		$query = count( $data['data'] );
	endif;
		for( $i=0; $i < $query; $i++ ):
			/**
			 * Check if user wanted to
			 * include info of followers
			 * or whom they following
			*/
			if( $showFollowerData == true ):
			
				//$output = sInstShowInfo( sInstGetInfo( $data['data'][$i]['id'], access_token() ), array( 'name' => 'true', 'bio' => 'true', 'website' => 'true', 'media' => 'true', 'followers' => 'true', 'following' => 'true', 'profile_pic' => 'true' ), $width );
			
			else:
						
				//$output = '<div id="tooltip-division" style=" width: ' . $width . 'px; float: left; margin: 5px; overflow: auto;">';
				$output = '<img class="front-photo" src="' . $data['data'][$i]['profile_picture'] . '" width="' . $width . '" height="' . $width . '" title="' . $data['data'][$i]['full_name'] . '">';				
				//$output .= '</div>';
			
			endif;
						
			echo $output;	
		endfor;		
}

/**
 * Function login to instagram for wp-administrator
*/
function sInstLogin( $return_uri )
{
	$baseURL = "https://api.instagram.com/oauth/authorize/";
	$client_id = "39170cdd8ebf4a159f01fdfd31b989b8";
	$redirect_uri = "http://www.rollybueno.info/plugins/simply-instagram.php";
	$response = "code";
	$scope = "likes+comments+relationships+likes";
	
	return $baseURL . '?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . $return_uri . '&response_type=' . $response . '&scope=' . $scope ;
}

/**
 * Function login to instagram for following
*/
function sInstLoginFollower( $return_uri )
{
	$baseURL = "https://api.instagram.com/oauth/authorize/";
	$client_id = "a52423ad78dc46e6b3f418251b5c4004";
	$redirect_uri = "http://www.rollybueno.info/plugins/simply-instagram-v2.php";
	$response = "code";
	$scope = "relationships";
	
	return $baseURL . '?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . $return_uri . '&response_type=' . $response . '&scope=' . $scope ;
}

/**
 * Set follow / unfollow button on profile info widget
*/
function sIntFollowButton( $user_id, $username )
{
	/**
	 * Check if access token is present in param
	 * if present, set it in option
	*/
	if( isset( $_COOKIE['visitor_access_token'] ) ):
		/**
		 * Get following info
		*/	
		if( $_GET['access_token'] ):
			if( isset( $_COOKIE['visitor_access_token'] ) != isset( $_GET['access_token'] ) ):
			//refresh
				( $_SERVER["HTTPS"] == "on" ) ? $protocol = "https://" : $protocol = "http://" ; 
			?>
				<!-- <script language=" JavaScript" > window.location.reload();  </script> -->
				<meta http-equiv="refresh" content="0, <?php echo $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>asd">
			<?php 
			endif;
		endif;
		
		$followinginfo = sInstGetFollowingInfo( user_id(), $_COOKIE['visitor_access_token'] );		
		//print_r( $followinginfo );
		if( $followinginfo['data']['outgoing_status'] == "none" ):
			/**
			 * Request follow, show follow me button
			*/
			( $_SERVER["HTTPS"] == "on" ) ? $protocol = "https://" : $protocol = "http://" ; 
		
			$form = '<form method="post" action="http://www.rollybueno.info/plugins/simply-instagram-v2.php?return_uri=' . base64_encode( $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) . '&type=following">';
			$form .= '<input type="hidden" name="action" value="follow">';
			$form .= '<input type="hidden" name="user_id" value="' . user_id() . '">';
			$form .= '<input type="hidden" name="access_token" value="' . $_COOKIE['visitor_access_token'] . '">';
			$form .= '<button type="submit" id="sInstRelButton"> Follow @' . $username . '</button>';
			$form .= '</form>';
			
			return $form;
		else:
			( $_SERVER["HTTPS"] == "on" ) ? $protocol = "https://" : $protocol = "http://" ; 
		
			$form = '<form method="post" action="http://www.rollybueno.info/plugins/simply-instagram-v2.php?return_uri=' . base64_encode( $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) . '&type=following">';
			$form .= '<input type="hidden" name="action" value="unfollow">';
			$form .= '<input type="hidden" name="user_id" value="' . user_id() . '">';
			$form .= '<input type="hidden" name="access_token" value="' . $_COOKIE['visitor_access_token'] . '">';
			$form .= '<button type="submit" id="sInstRelButton"> Unfollow @' . $username . '</button>';
			$form .= '</form>';
			
			return $form;
		endif;		
	else:
		/**
		 * Show follow me button
		 * determine if the current url is using SSL or not
		*/
		if(   isset( $_COOKIE['visitor_access_token'] ) != isset( $_GET['access_token'] ) ):
		//refresh
			( $_SERVER["HTTPS"] == "on" ) ? $protocol = "https://" : $protocol = "http://" ; 
		?>
			<!-- <script language=" JavaScript" > window.location.reload();  </script> -->
			<meta http-equiv="refresh" content="0, <?php echo $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>">
		<?php 
		endif;
		
		( $_SERVER["HTTPS"] == "on" ) ? $protocol = "https://" : $protocol = "http://" ; 
		
		$form = '<form method="post" action="'  . sInstLoginFollower( '?return_uri=' . base64_encode( $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) . '">';
		$form .= '<input type="hidden" name="action" value="follow">';
		$form .= '<button type="submit" id="sInstRelButton" > Follow @' . $username . '</button>';
		$form .= '</form>';
		
		return $form;
	endif;
	
}

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

function sInstDisplayMediaInfo( $media_id )
{
	$apiurl = "https://api.instagram.com/v1/media/" . $media_id . "?access_token=" . access_token();
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
          	
          	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
        endif;
       	
	
	return $data;
	
	echo $data['data']['1']['filter'];
}

/** Caching module */
function sInstCache( $image, $width ){	
	return $image;
	/*
	if( get_option( 'enableCandCom' ) == "no" ):
		return $image;
	else:
		include_once( simply_instagram_plugin_path . '/resize_class.php' );
		/**
		 Proper naming and file caching
		*/
		/*
		$basename = basename( $image, ".jpg" );
		$final_name = $basename . "_" . $width . ".jpg";
		
		//var_dump ($image );
		if( !file_exists( simply_instagram_plugin_path . "/cache/" . $final_name ) ):
			/**
			 Copy and resize
			*/
			/*
			$copy = @copy( $image, simply_instagram_plugin_path . "cache/" . $final_name );
			
			if( $copy ):
				$resize = new resize( simply_instagram_plugin_path . "cache/" . $final_name );						 						 
	 			$resize -> resizeImage( $width, $width, 'crop' );						 						 
	 			$resize -> saveImage( simply_instagram_plugin_path . "cache/" . $final_name , get_option('JPEGCompression') );
			
				return simply_instagram_plugin_url . "cache/" . $final_name;
			endif;			
		else:
			/**
			 If exist, check if has latest dimension
			 If not, resize
			*/
			/*
			list($d_width, $d_height, $d_type, $d_attr) = getimagesize( simply_instagram_plugin_path . "/cache/" . $final_name );
			
			if( $d_width != $width ):
			
				$copy = @copy( $image, simply_instagram_plugin_path . "cache/" . $final_name );
				
				if( $copy ):
					$resize = new resize( simply_instagram_plugin_path . "cache/" . $final_name );						 						 
	 				$resize -> resizeImage( $width, $width, 'crop' );				 						 
	 				$resize -> saveImage( simply_instagram_plugin_path . "cache/" . $final_name , get_option('JPEGCompression') );
	 			endif;
			else:
				return simply_instagram_plugin_url . "cache/" . $final_name;
			endif;
		endif;	
	endif;
	*/
	
}

/** Clearing cache folder */
function sIntClearCache(){	
	$path = simply_instagram_plugin_path . "cache/";
	
	foreach(glob($path ."*.*") as $file) {
	   unlink($file); // Delete each file through the loop
	}
}

?>