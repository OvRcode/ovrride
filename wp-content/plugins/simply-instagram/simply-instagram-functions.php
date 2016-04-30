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
	
		$infos .= '<div class="profile-data"><img src="' . $data['data']['profile_picture'] . '" id="sIntProfilePhoto"/><p id="sInstProfileName">' . $data['data']['full_name'] . '</p></div>';	
	
		$infos .= '<div class="profile-data">' . sIntFollowButton( user_id(), $data['data']['username'] ) . '</div>';
	if( $args['bio'] == "true" ):
		$infos .= '<div id="profile-bio"><p>' . $data['data']['bio'] . '</p></div>';
	endif;
	
	if( $args['website'] == "true" ):
		$infos .= '<div class="profile-data"><p style="text-align: center;"><a href="' . $data['data']['website'] . '" target="_blank" />' . $data['data']['website'] . '</a></p></div>';
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
	if (sIntCheckCache("selffeed.json")) {
		 return sIntReadCache("selffeed.json");	 
	}
	
	$apiurl = "https://api.instagram.com/v1/users/self/feed?access_token=" . $access_token ;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
	
          	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
                
                file_put_contents( simply_instagram_plugin_path . '/cache-api/selffeed.json', $response );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
		
		file_put_contents( simply_instagram_plugin_path . '/cache-api/selffeed.json', $response['body'] );
        endif;
	
	return $data;
}

/**
* Show data in widget form
*/
function sInstShowWidgetData( $data, $count='9', $width='75', $customRel="sIntWidget", $displayCaption="true", $open_instagram="false" )
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
	
	$output = '<ul class="si-widget">';
	
 	for( $i = 0; $i < $query; $i++ ):
 	
 	if( $open_instagram == "true" ){
 		$output .= '<li><a class="si-tooltip" title="' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ). '" href="' . $data['data'][$i]['link'] . '" target="_blank" >';
 		$output .= '<img class="front-photo si-tooltip" src="' . $data['data'][$i]['images']['thumbnail']['url'] . '" alt="' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ). '" width="' . $width .'" height="' . $width . '">';
 		$output .= '</a></li>';
 	}else{
 		//if video
 		if( isset( $data['data'][$i]['videos'] ) && !empty( $data['data'][$i]['videos'] ) ){
 			$output .= '<li><a class="si-tooltip" href="' . $data['data'][$i]['videos']['standard_resolution']['url'] . '?iframe=true&width=500&height=250" "rel="' . $customRel . '[instagram]" title="' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ). '">';		
 		}else{
			$output .= '<li><a class="si-tooltip" href="' . $data['data'][$i]['images']['standard_resolution']['url'] . '" rel="' . $customRel . '[instagram]" title="' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ). '">';		
		}
			$output .= '<div class="si-content" style=" display: none; margin: 10px; "><div class="clear"></div>';
			
			/**
			 * Option to display caption.
			 * Page often breaks when caption is too long
			 * because prettyPhot can't handle it.
			*/			
			if(  $displayCaption == "true" ):
			 $output .= htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ). '<div class="clear"></div>';
			endif;	
			
			$output .= '<div class="clear"></div></div>';						
			$output .= '<img class="front-photo si-tooltip" src="' . $data['data'][$i]['images']['thumbnail']['url'] . '" width="' . $width .'" height="' . $width . '" alt="' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ). '">';
			$output .= "</a></li>";
	}				
				
	endfor;
	
	$output .= '</ul>';
	
	echo $output;	
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
	if (sIntCheckCache("following.json")) {
		 return sIntReadCache("following.json");	 
	}
	
	$apiurl = "https://api.instagram.com/v1/users/" . $user_id . "/follows?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
	
         	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
                
                file_put_contents( simply_instagram_plugin_path . '/cache-api/following.json', $response );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
		
		file_put_contents( simply_instagram_plugin_path . '/cache-api/following.json', $response['body'] );
        endif;
        
	return $data;	
}


/**
* Get the list of users this user is followed by.
*/
function sInstGetFollowers( $user_id, $access_token )
{
	if (sIntCheckCache("followers.json")) {
		 return sIntReadCache("followers.json");	 
	}
	
	$apiurl = "https://api.instagram.com/v1/users/" . $user_id . "/followed-by?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
	
          	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
                
                file_put_contents( simply_instagram_plugin_path . '/cache-api/followers.json', $response );
        else:        	
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
		
		file_put_contents( simply_instagram_plugin_path . '/cache-api/followers.json', $response['body'] );
		
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
	if (sIntCheckCache("likes.json")) {
		 return sIntReadCache("likes.json");	 
	}
	
	$apiurl = "https://api.instagram.com/v1/users/self/media/liked?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
          	
          	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
                
                file_put_contents( simply_instagram_plugin_path . '/cache-api/likes.json', $response );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
		
		file_put_contents( simply_instagram_plugin_path . '/cache-api/likes.json', $response['body'] );
        endif;
       	
	return $data;	
}

/**
* Get the most recent media published by a user.
*/
function sInstGetRecentMedia( $user_id, $access_token )
{
	if (sIntCheckCache("recentmedia.json")) {
		 return sIntReadCache("recentmedia.json");	 
	}
	
	$apiurl = "https://api.instagram.com/v1/users/" . $user_id . "/media/recent/?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
         	
         	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
                
                file_put_contents( simply_instagram_plugin_path . '/cache-api/recentmedia.json', $response );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
		
		file_put_contents( simply_instagram_plugin_path . '/cache-api/recentmedia.json', $response['body'] );
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

	//if (sIntCheckCache("userinfo.json")) {
	//	 return sIntReadCache("userinfo.json");	 
	//}
	
	$apiurl = "https://api.instagram.com/v1/users/" . $user_id . "/?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
	
          	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
                
               // file_put_contents( simply_instagram_plugin_path . '/cache-api/userinfo.json', $response );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
		
		///file_put_contents( simply_instagram_plugin_path . '/cache-api/userinfo.json', $response['body'] );
        endif;
       	
	return $data;	
}

/**
* most-popular.
*/
function sInstGetMostPopular( $media, $access_token )
{
	if (sIntCheckCache("popular.json")) {		
		 return sIntReadCache("popular.json");
	}
	
	$apiurl = "https://api.instagram.com/v1/media/" . $media . "?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
	
          	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
                
                file_put_contents( simply_instagram_plugin_path . '/cache-api/popular.json', $response );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
		
		file_put_contents( simply_instagram_plugin_path . '/cache-api/popular.json', $response['body'] );
        endif;
        
	return $data;	
}
/**
* Check if already following
*/
function sInstGetFollowingInfo( $user_id, $access_token )
{
	if (sIntCheckCache("followinginfo.json")) {
		 return sIntReadCache("followinginfo.json");	
	}

	$apiurl = "https://api.instagram.com/v1/users/" . $user_id . "/relationship?access_token=" . $access_token;
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
	        
	        $curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
                
                file_put_contents( simply_instagram_plugin_path . '/cache-api/followinginfo.json', $response );
        else:        
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
                
                $data = json_decode( $response['body'], true );			
		file_put_contents( simply_instagram_plugin_path . '/cache-api/followinginfo.json', $response['body'] );
               
        endif;
       	
	return $data;	
}

/**
 *
 * Display data from shorcode [simply_instagram]
 * htmlspecialchars() is used to filter caption text with illegal
 * char that may cause display bug.
 *
 * Settings:
 * * data - api data feed
 * * presentation - option to use masonry or polaroid in image presentation. Default: polaroid.
 * * displayoption - choose either by using prettyphoto slideshow, single image or open directly in Instagram. Default: Instagram.
 * * size - photo size on slideshow: Choices: thumbnail, low_resolution, standard_resolution. Default: low_resolution
 * * display - number of initial photo to be display. Default: 20
 * * width - image width. Default: 150
 * * customRel - custom rel for prettyphoto 
 * * showphotographer - display username of photo owner. Default: true
 * * photocomment - comments to be display. 0 to hide, maximum of 5. Default 0
 * * stat - display comment and like stat total. Default: true
 * * photocaption - display photo caption. Might affect image height. Default: true
 * * displaycomment - option to display photo comment. Default: true.
 *
*/
function sInstDisplayData( $data, $presentation = 'polaroid', $displayoption = "instagram", $size='low_resolution', $display="20", $width="150", $customRel="sIntWid", $showphotographer="true", $photocomment=0, $stat = "true", $photocaption="true", $displaycomment = true )
{			
	/**
	 * Determine query return
	 * next query used to avoid
	 * blank return when display value is 
	 * greater than API return
	*/
	//var_dump( $stat );
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
		
		$prettyPhoto = get_option( 'si_prettyphoto_settings' );
		
		if( $presentation === "polaroid" ){
			//$output = '<div class="polaroid-holder">';
			$output = '';
			$output .= '<ul id="polaroid-ul">';
				for( $i=0; $i < $query; $i++ ):
					$output .= '<li>';															
										
					//presentation
					if( $displayoption === "instagram" ){
						$output .= '<a class="si-tooltip" title="' . $data['data'][$i]['caption']['text'] . '" href="' . $data['data'][$i]['link'] . '" target="_blank" >';
						$output .= '<img class="si-tooltip" src="' . $data['data'][$i]['images'][$size]['url'] . '" alt="' . $data['data'][$i]['caption']['text'] . '">';
						$output .= '</a>';
					}					
					
					if( $displayoption === "prettyPhoto" ){
						/**
						  * Display image, description and statistic
						 **/
						$output .= '<a href="' . $data['data'][$i]['images'][$size]['url'] . '" rel="sIntSC[instagram]" >';						
						$output .= '<div class="si-content" style=" display: none; margin: 10px; "><div class="break-line" style="height: 10px;"></div>';
						/**
						 * If user choose to display photographer
						 * profile picture in settings, display this area
						*/					 
						if( $prettyPhoto['ppDisplayPhotographer']  === "true" ):						
						$output .= '<div class="ppDisplayPhotographer">';
						  $output .= '<div class="ppDisplayPhotographer-author">';
						   $output .= '<img src="' . $data['data'][$i]['user']['profile_picture'] . '" class="ppDisplayPhotographer-photo"/> <span class="ppDisplayPhotographer-username">' . $data['data'][$i]['user']['username'] . '</span> ';
						  $output .= '</div>';
						 $output .= '</div>';
						endif; // end of ppPhotoDescription
						
						/**
						 * If user choose to display statistics
						 * in settings, display this area
						*/
						if( $prettyPhoto['ppDisplayStatistic']  == "true" ):	
							$output .= '<div class="scode-content-info"><p class="si-stat-likes">' . si_format_num( $data['data'][$i]['likes']['count'] ) . ' likes</p></div>';
							$output .= '<div class="scode-content-info"><p class="si-stat-comments">' . $data['data'][$i]['comments']['count'] . ' comments</p></div>';
						endif; // end of ppPhotoDescription
						
						/**
						 * If user choose to display photo description 
						 * in settings, display this area
						*/
						if( $prettyPhoto['ppPhotoDescription']  === "true" ):
						
						 /**
						  * check if description exist to avoid big spacing
						 */
						 if( $data['data'][$i]['caption']['text'] != "" ):				 
							$output .= htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES );
						 endif; //end of description checking
						
						endif; // end of ppPhotoDescription
						$output .= '<div class="clear"></div></div>';
						
						$output .= '<img class="front-photo si-tooltip" src="' . $data['data'][$i]['images'][$size]['url'] . '" alt="' . $data['data'][$i]['caption']['text'] . '">';
						
						$output .= "</a>";
					}
					
					if( $displayoption === "single" ){
					/**
					 * else user choose to use prettyPhoto iframe
					 * display this area.
					*/
						$output .= '<div class="item-holder" data-id="' . $data['data'][$i]['id'] . '">';			
						
						 /**
						  * Display image, description and statistic
						 **/
						 $output .= '<a href="' . plugins_url( 'simply-instagram/simply-instagram-pp-media-viewer.php?mid=' . ( $data['data'][$i]['id'] ) . '&access_token=' . access_token() . '&mdc=' . get_option( 'displayCommentMediaViewer' ) ) . '&iframe=true&width=960&height=650&scrolling=no" rel="prettyphoto" style="text-decoration:none">';
						  $output .= '<img class="front-photo si-tooltip" src="' . $data['data'][$i]['images']['thumbnail']['url'] . '" width="150" height="150" alt="' . $data['data'][$i]['caption']['text'] . '">';
						 $output .= '</a>';
						 
						 $output .=  '</div>';		
				
				}	 // end of mediaViewer
					
					$output .= '</li>';
				endfor;
			$output .= '</ul>';
			//$output .= '</div>';
			echo $output;
		}else{		
			/** Start of Masonry codes */
			for( $i=0; $i < $query; $i++ ):
			
				$output = '<div class="masonryItem" data-id="' . $data['data'][$i]['id'] . '">';
				
				if( $displayoption === "instagram" ){
				/**
				 * else user choose to use Instagram site
				 * make new link to open in new tab / window
				*/
				
				$output .= '<div class="item-holder" data-id="' . $data['data'][$i]['id'] . '">';
				$output .= '<a class="si-tooltip" title="' . $data['data'][$i]['caption']['text'] . '" href="' . $data['data'][$i]['link'] . '" target="_blank">';
				$output .= '<img class="front-photo si-tooltip" src="' . $data['data'][$i]['images'][$size]['url'] . '" width="150" height="150" alt="' . $data['data'][$i]['caption']['text'] . '">';
				$output .= '</a>';
				$output .=  '</div>';
				
				 /**
					  * highlights photographer
					  * using author section class
					 */
					 if( $showphotographer === "true" ):
					 $output .= '<div class="sinst-author-section">';
					  $output .= '<div class="sinst-author">';
					   $output .= '<img src="' . $data['data'][$i]['user']['profile_picture'] . '" alt="' . $data['data'][$i]['user']['username'] . '" class="si-photographer si-tooltip"/> <span class="sinst-comment-author">' . $data['data'][$i]['user']['username'] . '</span> ';
					  $output .= '</div>';
					 $output .= '</div>';
					 endif;	 
					 
					 /**
					  * Determine if displayStatistic is allowed
					 */
					 if( $stat === "true" ):
					 
					 $output .= '<div class="scode-content-info"><p class="si-stat-likes">' . si_format_num( $data['data'][$i]['likes']['count'] ) . ' likes</p></div>';
					 
					 endif;
					 
					 /**
					  * Determine if displayDescription is allowed
					 */
					 $photocaption === "true" ? $output .= '<p class="si-photo-caption">' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ) . '</p>' : null ;
					 
					  
					 /**
					  * Comment section
					  * Check if comment exist
					  * Display latest # comments on media
					  * based on option
					 */
					 if( $data['data'][$i]['comments']['count'] != 0 ): //if there's comment
					 
					 if( $displaycomment === "true" ): //user choose to display comment
					 	 
						 $output .= '<div class="sinst-comment-section">';
						 
						 /**
						  * Determine if displayStatistic is allowed
						 */
						 if( $stat === "true" ):
						 	$output .= '<div class="scode-content-info"><p class="si-stat-comments">' . si_format_num( $data['data'][$i]['comments']['count'] ) . ' comments</p></div>';
						 endif;
						 
						 /**
						  * Determine comment to be displayed
						 */		 
						 if( $data['data'][$i]['comments']['count'] > 5 || $photocomment > 5 ):
						 	$cc = 5;
						 else:
						 	$cc = $data['data'][$i]['comments']['count'];
						 endif;
							 
						 for( $c=0; $c < $cc ; $c++ ):
							 
						 	$output .= '<div class="sinst-comments">';						 	
						 	$output .= '<img src="' . $data['data'][$i]['comments']['data'][$c]['from']['profile_picture'] . '" alt="' . $data['data'][$i]['comments']['data'][$c]['from']['username'] . '" class="si-comment-profile"/>'; 
						 	$output .= ' <span class="sinst-comment-author">' . $data['data'][$i]['comments']['data'][$c]['from']['username'] . '</span> <p>' . htmlspecialchars( $data['data'][$i]['comments']['data'][$c]['text'], ENT_QUOTES ) . '</p>';
						 	$output .= '</div>';
							 	
						 endfor;			 		
						 $output .= '</div>';
					 endif;
					  
					 endif;
				
				}				
				
				if( $displayoption === "prettyPhoto" ){
				
				/**
				 * else user choose to use prettyPhoto slideshow
				 * display this area.
				*/
					$output = '';
					$output .= '<div class="item-holder" data-id="' . $data['data'][$i]['id'] . '">';			
					
					/**
					  * Display image, description and statistic
					 **/		
					
					$output .= '<a href="' . $data['data'][$i]['images'][$size]['url'] . '" class="si-tooltip" title="' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ) . '" rel="sIntSC[instagram]">';
					$output .= '<div class="si-content" style=" display: none; margin: 10px; "><div class="break-line" style="height: 10px;"></div>';
					
					/**
					 * If user choose to display photographer
					 * profile picture in settings, display this area
					*/					 
					if( $prettyPhoto['ppDisplayPhotographer']  === "true" ):						
					$output .= '<div class="ppDisplayPhotographer">';
					  $output .= '<div class="ppDisplayPhotographer-author">';
					   $output .= '<img src="' . $data['data'][$i]['user']['profile_picture'] . '" class="ppDisplayPhotographer-photo" alt="' . $data['data'][$i]['user']['username'] . '"/> <span class="ppDisplayPhotographer-username">' . $data['data'][$i]['user']['username'] . '</span> ';
					  $output .= '</div>';
					 $output .= '</div>';
					endif; // end of ppPhotoDescription
					
					/**
					 * If user choose to display statistics
					 * in settings, display this area
					*/
					if( $prettyPhoto['ppDisplayStatistic']  == "true" ):	
						$output .= '<div class="scode-content-info"><p class="si-stat-likes">' . si_format_num(  $data['data'][$i]['likes']['count'] ) . ' likes</p></div>';
						$output .= '<div class="scode-content-info"><p class="si-stat-comments">' . si_format_num(  $data['data'][$i]['comments']['count'] ) . ' comments</p></div>';
					endif; // end of ppPhotoDescription
					
					/**
					 * If user choose to display photo description 
					 * in settings, display this area
					*/
					if( $prettyPhoto['ppPhotoDescription']  === "true" ):
					
					 /**
					  * check if description exist to avoid big spacing
					 */
					 if( $data['data'][$i]['caption']['text'] != "" ):				 
						$output .= htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES );
					 endif; //end of description checking
					endif; // end of ppPhotoDescription
					
					$output .= '<div class="clear"></div></div>';
					
					$output .= '<img class="front-photo si-prettyphoto si-tooltip" src="' . $data['data'][$i]['images']['thumbnail']['url'] . '" alt="' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ) . '">';
					
					$output .= "</a>";
					 
					 $output .=  '</div>';
					 
					 /**
					  * highlights photographer
					  * using author section class
					 */
					 if( $showphotographer === "true" ):
					 $output .= '<div class="sinst-author-section">';
					  $output .= '<div class="sinst-author">';
					   $output .= '<img src="' . $data['data'][$i]['user']['profile_picture'] . '" alt="' . $data['data'][$i]['user']['username'] . '" class="si-photographer"/> <span class="sinst-comment-author">' . $data['data'][$i]['user']['username'] . '</span> ';
					  $output .= '</div>';
					 $output .= '</div>';
					 endif;	 
					 
					 /**
					  * Determine if displayStatistic is allowed
					 */
					 if( $stat === "true" ):
					 
					 $output .= '<div class="scode-content-info"><p class="si-stat-likes">' . si_format_num( $data['data'][$i]['likes']['count'] ) . ' likes</p></div>';
					 
					 endif;
					 
					 /**
					  * Determine if displayDescription is allowed
					 */
					 $photocaption === "true" ? $output .= '<p class="si-photo-caption">' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ) . '</p>' : null ;
					 
					 
					 /**
					  * Comment section
					  * Check if comment exist
					  * Display latest # comments on media
					  * based on option
					 */
					 if( $data['data'][$i]['comments']['count'] != 0 ): //if there's comment
					 
					 if( $displaycomment === "true" ): //user choose to display comment
					 	 
						 $output .= '<div class="sinst-comment-section">';
						 
						 /**
						  * Determine if displayStatistic is allowed
						 */
						 if( $stat === "true" ):
						 	$output .= '<div class="scode-content-info"><p class="si-stat-comments">' . si_format_num( $data['data'][$i]['comments']['count'] ) . ' comments</p></div>';
						 endif;
						 
						 /**
						  * Determine comment to be displayed
						 */		 
						 if( $data['data'][$i]['comments']['count'] > 5 || $photocomment > 5 ):
						 	$cc = 5;
						 else:
						 	$cc = $data['data'][$i]['comments']['count'];
						 endif;
							 
						 for( $c=0; $c < $cc ; $c++ ):
							 
						 	$output .= '<div class="sinst-comments">';						 	
						 	$output .= '<img alt="' . $data['data'][$i]['comments']['data'][$c]['from']['username'] . '" src="' . $data['data'][$i]['comments']['data'][$c]['from']['profile_picture'] . '" class="si-comment-profile"/>'; 
						 	$output .= ' <span class="sinst-comment-author">' . $data['data'][$i]['comments']['data'][$c]['from']['username'] . '</span> <p>' . htmlspecialchars( $data['data'][$i]['comments']['data'][$c]['text'], ENT_QUOTES ) . '</p>';
						 	$output .= '</div>';
							 	
						 endfor;			 		
						 $output .= '</div>';
					 endif;
					  
					 endif;
					
				}
				
				$output .= '</div>';
				
				echo $output;
				
			endfor;
		}
		
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
		$output = '<ul class="si-widget">';
		
		for( $i=0; $i < $query; $i++ ):
			/**
			 * Check if user wanted to
			 * include info of followers
			 * or whom they following
			*/
			if( $showFollowerData == true ):
			
				//nothing here
			
			else:
				$output .= '<li><img class="front-photo si-tooltip" src="' . $data['data'][$i]['profile_picture'] . '" width="' . $width . '" height="' . $width . '" title="' . $data['data'][$i]['full_name'] . '" ></li>';
			endif;	
				
		endfor;		
		
		$output .= '</ul>';
		
		echo $output;
		
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
		
		( !empty( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) ? $protocol = "https://" : $protocol = "http://" ; 
		
		$form = '<form method="post" action="'  . sInstLoginFollower( '?return_uri=' . base64_encode( $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) . '">';
		$form .= '<input type="hidden" name="action" value="follow">';
		$form .= '<button type="submit" id="sInstRelButton" > Follow @' . $username . '</button>';
		$form .= '</form>';
		
		return $form;
	endif;
	
}


function sInstDisplayMediaInfo( $media_id )
{
	if (sIntCheckCache("mediainfo.json")) {
		 return sIntReadCache("mediainfo.json");	 
	}
	
	$apiurl = "https://api.instagram.com/v1/media/" . $media_id . "?access_token=" . access_token();
	
	if(function_exists('curl_exec') && function_exists('curl_init')):
          	
          	$curl = curl_init();               
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);               
                $response = curl_exec($curl);               
                curl_close($curl);
               
                $data = json_decode( $response, true );
                
                file_put_contents( simply_instagram_plugin_path . '/cache-api/mediainfo.json', $response );
        else:
                $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
		$data = json_decode( $response['body'], true );
		
		file_put_contents( simply_instagram_plugin_path . '/cache-api/mediainfo.json', $response['body'] );
        endif;
       	
	
	return $data;
	
	echo $data['data']['1']['filter'];
}


/** Cahcing module */
function sIntCheckCache( $file ){	
	
	$si_gen_settings = get_option( 'si_general_settings' );
	
	if( !$si_gen_settings['si_cache_option'] )
		return;
	
	if( !$si_gen_settings['gen_cache_expire_option'] )
		return;
		
	$cache_expires = get_option( 'siCacheExpires' );
	
	$cachefile = simply_instagram_plugin_path . "cache-api/" . $file;
		
	$cachefile_created = (file_exists($cachefile)) ? @filemtime($cachefile) : 0;
	 
	//var_dump ( (time() - $cache_expires) < $cachefile_created );
	 
	return ((time() - $cache_expires) < $cachefile_created);
	
	
}

/** Cahcing module */
function sIntReadCache( $file ){	
		
	$cache_file = simply_instagram_plugin_path . "cache-api/" . $file;
	
	$data = json_decode( file_get_contents( $cache_file ) , true );
	
	return $data ;
	
}

/** API Responses */
function sIntCheckResponse( $type ){

	$si_gen_settings = get_option( 'si_general_settings' );
	
	if( !isset( $si_gen_settings['si_cache_option'] ) && $si_gen_settings['si_cache_option'] === false ){
		return sprintf( '<i>%s</i>',
				__('Caching module disabled.', 'simply-instagram')
				);
	}
	
	$file = simply_instagram_plugin_path . "cache-api/" . $type;
	
	$cachefile = file_exists( $file );
	
	if( !$cachefile ){
		return sprintf( '<i>%s</i>',
				__('Cache file not found! You are not using this endpoint.', 'simply-instagram')
				);
	}else{
		$data = json_decode( file_get_contents( $file ), true );
				
		if( !empty( $data['meta']['error_message'] ) ){
			return sprintf( '<i>%s<strong>%i</strong>%s</i>',
					__('Code : ', 'simply-instagram'),
					$data['meta']['code'],
					__('Message : ', 'simply-instagram') . $data['meta']['error_message']
					);
			
		}else{
			return sprintf( '<i>%s<strong>%i</strong>%s</i><a href="%s">%s</a>',
					__('Code : ', 'simply-instagram'),
					$data['meta']['code'],
					__('View file ', 'simply-instagram'),
					plugins_url() . '/simply-instagram/cache-api/' . $type,
					__('HERE', 'simply-instagram')
					);					
		}
	}
	
}

/**
 *
 * SI format number
 *
 * Format number with proper comma
 * return { formatted number }
 *
*/
function si_format_num( $number, $decimal = "" ){
	
	if( !is_int( $number ) && !is_int( $decimal ) ){
		return;
	}
	
	return number_format( $number, (int) $decimal );		
	
}

?>