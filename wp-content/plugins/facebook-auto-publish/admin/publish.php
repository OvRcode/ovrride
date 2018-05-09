<?php 
if( !defined('ABSPATH') ){ exit();}
/*add_action('publish_post', 'xyz_fbap_link_publish');
add_action('publish_page', 'xyz_fbap_link_publish');
$xyz_fbap_future_to_publish=get_option('xyz_fbap_future_to_publish');

if($xyz_fbap_future_to_publish==1)
	add_action('future_to_publish', 'xyz_link_fbap_future_to_publish');

function xyz_link_fbap_future_to_publish($post){
	$postid =$post->ID;
	xyz_fbap_link_publish($postid);
}*/
//////////////
add_action(  'transition_post_status',  'xyz_link_fbap_future_to_publish', 10, 3 );

function xyz_link_fbap_future_to_publish($new_status, $old_status, $post){
	
	if(!isset($GLOBALS['fbap_dup_publish']))
		$GLOBALS['fbap_dup_publish']=array();
	$postid =$post->ID;
	$get_post_meta=get_post_meta($postid,"xyz_fbap",true);
	
	$post_permissin=get_option('xyz_fbap_post_permission');
	if(isset($_POST['xyz_fbap_post_permission']))
		$post_permissin=intval($_POST['xyz_fbap_post_permission']);
	else 
	{
		if ($post_permissin == 1) {
			if($new_status == 'publish')
			{
			if ($get_post_meta == 1 ) {
					return;
			}
			}
			else return;
		}	
	}
	if($post_permissin == 1)
	{
		if($new_status == 'publish')
		{
		if(!in_array($postid,$GLOBALS['fbap_dup_publish'])) {
			  $GLOBALS['fbap_dup_publish'][]=$postid;
	       xyz_fbap_link_publish($postid);
	      
		   }
	      
		}
	}
	
}

function xyz_fbap_link_publish($post_ID) {
	$_POST_CPY=$_POST;
	$_POST=stripslashes_deep($_POST);
	
	$post_permissin=get_option('xyz_fbap_post_permission');
	if(isset($_POST['xyz_fbap_post_permission']))
		$post_permissin=intval($_POST['xyz_fbap_post_permission']);
		
	if ($post_permissin != 1) {
		$_POST=$_POST_CPY;
		return ;
	
	}elseif(( (isset($_POST['_inline_edit'])) || (isset($_REQUEST['bulk_edit'])) ) && (get_option('xyz_fbap_default_selection_edit') == 0) ) {
		$_POST=$_POST_CPY;
		return;
	}

	$get_post_meta=get_post_meta($post_ID,"xyz_fbap",true);
	if($get_post_meta!=1)
		add_post_meta($post_ID, "xyz_fbap", "1");

	global $current_user;
	wp_get_current_user();
	
	////////////fb///////////
	$appid=get_option('xyz_fbap_application_id');
	$appsecret=get_option('xyz_fbap_application_secret');
	$useracces_token=get_option('xyz_fbap_fb_token');
	$app_name=get_option('xyz_fbap_application_name');

	$message=get_option('xyz_fbap_message');
	if(isset($_POST['xyz_fbap_message']))
		$message=$_POST['xyz_fbap_message'];
	
	//$fbid=get_option('xyz_fbap_fb_id');
	
	$posting_method=get_option('xyz_fbap_po_method');
	if(isset($_POST['xyz_fbap_po_method']))
		$posting_method=intval($_POST['xyz_fbap_po_method']);
	
	$af=get_option('xyz_fbap_af');
	
	$postpp= get_post($post_ID);global $wpdb;
	$entries0 = $wpdb->get_results($wpdb->prepare( 'SELECT user_nicename,display_name FROM '.$wpdb->prefix.'users WHERE ID=%d',$postpp->post_author));
	
	foreach( $entries0 as $entry ) {			
	$user_nicename=$entry->user_nicename;
	$user_displayname=$entry->display_name;
	}
	if ($postpp->post_status == 'publish')
	{
		$posttype=$postpp->post_type;
		$fb_publish_status=array();
			
		if ($posttype=="page")
		{

			$xyz_fbap_include_pages=get_option('xyz_fbap_include_pages');
			if($xyz_fbap_include_pages==0)
			{
				$_POST=$_POST_CPY;
				return;
			}
		}
			
		else if($posttype=="post")
		{
			$xyz_fbap_include_posts=get_option('xyz_fbap_include_posts');
			if($xyz_fbap_include_posts==0)
			{
				$_POST=$_POST_CPY;return;
			}
			
			$xyz_fbap_include_categories=get_option('xyz_fbap_include_categories');
			if($xyz_fbap_include_categories!="All")
			{
				$carr1=explode(',', $xyz_fbap_include_categories);
					
				$defaults = array('fields' => 'ids');
				$carr2=wp_get_post_categories( $post_ID, $defaults );
				$retflag=1;
				foreach ($carr2 as $key=>$catg_ids)
				{
					if(in_array($catg_ids, $carr1))
						$retflag=0;
				}
					
					
				if($retflag==1)
				{
					$_POST=$_POST_CPY;
					return;
				}
			}
		}
		else
		{
					
			$xyz_fbap_include_customposttypes=get_option('xyz_fbap_include_customposttypes');
			if($xyz_fbap_include_customposttypes!='')
			{		
				$carr=explode(',', $xyz_fbap_include_customposttypes);

				if(!in_array($posttype, $carr))
				{
					$_POST=$_POST_CPY;return;
				}
	
			}
			else
			{
				$_POST=$_POST_CPY;return;
			}
		
		}

		include_once ABSPATH.'wp-admin/includes/plugin.php';
		
		$pluginName = 'bitly/bitly.php';
		
		if (is_plugin_active($pluginName)) {
			remove_all_filters('post_link');
		}
		$link = get_permalink($postpp->ID);

		
		$xyz_fbap_apply_filters=get_option('xyz_fbap_apply_filters');
		$ar2=explode(",",$xyz_fbap_apply_filters);
		$con_flag=$exc_flag=$tit_flag=0;
		if(isset($ar2))
		{
			if(in_array(1, $ar2)) $con_flag=1;
			if(in_array(2, $ar2)) $exc_flag=1;
			if(in_array(3, $ar2)) $tit_flag=1;
		}
		
		$content = $postpp->post_content;
		if($con_flag==1)
			$content = apply_filters('the_content', $content);
		$content = html_entity_decode($content, ENT_QUOTES, get_bloginfo('charset'));
		$excerpt = $postpp->post_excerpt;
		if($exc_flag==1)
			$excerpt = apply_filters('the_excerpt', $excerpt);
		$excerpt = html_entity_decode($excerpt, ENT_QUOTES, get_bloginfo('charset'));
		$content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $content);
		$content=  preg_replace("/\\[caption.*?\\].*?\\[.caption\\]/is", '', $content);
		$content = preg_replace('/\[.+?\]/', '', $content);
		$excerpt = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $excerpt);

		if($excerpt=="")
		{
			if($content!="")
			{
				$content1=$content;
				$content1=strip_tags($content1);
				$content1=strip_shortcodes($content1);
				
				$excerpt=implode(' ', array_slice(explode(' ', $content1), 0, 50));
			}
		}
		else
		{
			$excerpt=strip_tags($excerpt);
			$excerpt=strip_shortcodes($excerpt);
		}
		$description = $content;
		$description_org=$description;
		
		$attachmenturl=xyz_fbap_getimage($post_ID, $postpp->post_content);
		if($attachmenturl!="")
			$image_found=1;
		else
			$image_found=0;
		
		$name = $postpp->post_title;
			
		$xyz_fbap_caption_for_fb_attachment=get_option('xyz_fbap_caption_for_fb_attachment');
		
		$caption=get_bloginfo('title');
		$caption = html_entity_decode($caption, ENT_QUOTES, get_bloginfo('charset'));
		
		if($tit_flag==1)
		$name = apply_filters('the_title', $name);
		$name = html_entity_decode($name, ENT_QUOTES, get_bloginfo('charset'));

		$name=strip_tags($name);
		$name=strip_shortcodes($name);
		$description=strip_tags($description);
		$description=strip_shortcodes($description);
	   	$description=str_replace("&nbsp;","",$description);
	
		$excerpt=str_replace("&nbsp;","",$excerpt);
		
		if($useracces_token!="" && $appsecret!="" && $appid!="" && $post_permissin==1)
		{
			$description_li=xyz_fbap_string_limit($description, 10000);

			$user_page_id=get_option('xyz_fbap_fb_numericid');

			$xyz_fbap_pages_ids=get_option('xyz_fbap_pages_ids');
			if($xyz_fbap_pages_ids=="")
				$xyz_fbap_pages_ids=-1;

			$xyz_fbap_pages_ids1=explode(",",$xyz_fbap_pages_ids);


			foreach ($xyz_fbap_pages_ids1 as $key=>$value)
			{
				if($value!=-1)
				{
					$value1=explode("-",$value);
					$acces_token=$value1[1];$page_id=$value1[0];
				}
				else
				{
					$acces_token=$useracces_token;$page_id=$user_page_id;
				}

				$fb=new Facebook\Facebook(array(
						'app_id'  => $appid,
						'app_secret' => $appsecret,
						'cookie' => true
				));
				$message1=str_replace('{POST_TITLE}', $name, $message);
				$message2=str_replace('{BLOG_TITLE}', $caption,$message1);
				$message3=str_replace('{PERMALINK}', $link, $message2);
				$message4=str_replace('{POST_EXCERPT}', $excerpt, $message3);
				$message5=str_replace('{POST_CONTENT}', $description, $message4);
				$message5=str_replace('{USER_NICENAME}', $user_nicename, $message5);
				$message5=str_replace('{USER_DISPLAY_NAME}', $user_displayname, $message5);
				$publish_time=get_the_time(get_option('date_format'),$post_ID );
				$message5=str_replace('{POST_PUBLISH_DATE}', $publish_time, $message5);
				$message5=str_replace('{POST_ID}', $post_ID, $message5);
				$message5=str_replace("&nbsp;","",$message5);

               $disp_type="feed";
				if($posting_method==1) //attach
				{
					$attachment = array('message' => $message5,
							'access_token' => $acces_token,
							'link' => $link,
							'actions' => json_encode(array('name' => $name,
							'link' => $link))

					);
				}
				else if($posting_method==2)  //share link
				{
					$attachment = array('message' => $message5,
							'access_token' => $acces_token,
							'link' => $link

					);
				}
				else if($posting_method==3) //simple text message
				{
					$attachment = array('message' => $message5,
							'access_token' => $acces_token				
					
					);
					
				}
				else if($posting_method==4 || $posting_method==5) //text message with image 4 - app album, 5-timeline
				{
					if($attachmenturl!="")
					{
						

						if($posting_method==5)
						{
							try{
								$album_fount=0;
								
								$albums = $fb->get("/$page_id/albums", $acces_token);
								$arrayResults = $albums->getGraphEdge()->asArray();
								
														
							}
							catch (Exception $e)
							{
								$fb_publish_status[$page_id."/albums"]=$e->getMessage();
									}
							if(isset($arrayResults))
							{
								foreach ($arrayResults as $album) {
									if (isset($album["name"]) && $album["name"] == "Timeline Photos") {
										$album_fount=1;$timeline_album = $album; break;
									}
								}
							}
							if (isset($timeline_album) && isset($timeline_album["id"])) $page_id = $timeline_album["id"];
							if($album_fount==0)
							{
								$attachment = array('name' => "Timeline Photos",
										'access_token' => $acces_token,
								);
								try{
									$album_create=$fb->post('/'.$page_id.'/albums', $attachment);
									$album_node=$album_create->getGraphNode();
									if (isset($album_node) && isset($album_node["id"]))
										$page_id = $album_node["id"];
								}
								catch (Exception $e)
								{
									$fb_publish_status[$page_id."/albums"]=$e->getMessage();
										
								}
									
							}
						}
						else
						{
							try{
								$album_fount=0;
								
								$albums = $fb->get("/$page_id/albums", $acces_token);
								$arrayResults = $albums->getGraphEdge()->asArray();
								
							}
							catch (Exception $e)
							{
								$fb_publish_status[$page_id."/albums"]=$e->getMessage();					
							}
							if(isset($arrayResults))
							{
								foreach ($arrayResults as $album)
								{
									if (isset($album["name"]) && $album["name"] == $app_name) {
										$album_fount=1;
										$app_album = $album; break;
									}
								}
						
							}
							if (isset($app_album) && isset($app_album["id"])) $page_id = $app_album["id"];
							if($album_fount==0)
							{
								$attachment = array('name' => $app_name,
										'access_token' => $acces_token,
								);
								try{
									$album_create=$fb->post('/'.$page_id.'/albums', $attachment);
									$album_node=$album_create->getGraphNode();
									if (isset($album_node) && isset($album_node["id"]))
										$page_id = $album_node["id"];
								}
								catch (Exception $e)
								{
									$fb_publish_status[$page_id."/albums"]=$e->getMessage();
								}
									
							}
						}
						
						
						$disp_type="photos";
						$attachment = array('message' => $message5,
								'access_token' => $acces_token,
								'url' => $attachmenturl	
						
						);
					}
					else
					{
						$attachment = array('message' => $message5,
								'access_token' => $acces_token
						
						);
					}
					
				}
				if($posting_method==1 || $posting_method==2)
				{
					
						//$attachment=xyz_wp_fbap_attachment_metas($attachment,$link);
						update_post_meta($post_ID, "xyz_fbap_insert_og", "1");
				}
				try{
					
				$result = $fb->post('/'.$page_id.'/'.$disp_type.'/', $attachment);
				}
							catch(Exception $e)
							{
								$fb_publish_status[$page_id."/".$disp_type]=$e->getMessage();
							}

			}

			
			if(count($fb_publish_status)>0)
				
			    $fb_publish_status_insert=serialize($fb_publish_status);
			else
				$fb_publish_status_insert=1;
			
			$time=time();
			$post_fb_options=array(
					'postid'	=>	$post_ID,
					'acc_type'	=>	"Facebook",
					'publishtime'	=>	$time,
					'status'	=>	$fb_publish_status_insert
			);
			
			$update_opt_array=array();
			
			$arr_retrive=(get_option('xyz_fbap_post_logs'));
			
			$update_opt_array[0]=isset($arr_retrive[0]) ? $arr_retrive[0] : '';
			$update_opt_array[1]=isset($arr_retrive[1]) ? $arr_retrive[1] : '';
			$update_opt_array[2]=isset($arr_retrive[2]) ? $arr_retrive[2] : '';
			$update_opt_array[3]=isset($arr_retrive[3]) ? $arr_retrive[3] : '';
			$update_opt_array[4]=isset($arr_retrive[4]) ? $arr_retrive[4] : '';
			
			array_shift($update_opt_array);
			array_push($update_opt_array,$post_fb_options);
			update_option('xyz_fbap_post_logs', $update_opt_array);
			
			
		}
		
	}
	
	$_POST=$_POST_CPY;
}

?>