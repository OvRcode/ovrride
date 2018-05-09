<?php
if( !defined('ABSPATH') ){ exit();}
$app_id = get_option('xyz_fbap_application_id');
$app_secret = get_option('xyz_fbap_application_secret');
$redirecturl=admin_url('admin.php?page=facebook-auto-publish-settings&auth=1');
// 	if(is_ssl()===false)
// 		$redirecturl=preg_replace("/^http:/i", "https:", $redirecturl);
$my_url=urlencode($redirecturl);
if(isset($_POST) && isset($_POST['fb_auth'] ))
{
	ob_clean();
}

if ( xyz_fbap_is_session_started() === FALSE ) session_start();

$code="";
if(isset($_REQUEST['code']))
$code = $_REQUEST["code"];

if(isset($_POST['fb_auth']))
{
	if (! isset( $_REQUEST['_wpnonce'] )
			|| ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'xyz_smap_fb_auth_nonce' )
			) {
	
				wp_nonce_ays( 'xyz_smap_fb_auth_nonce' );
	
				exit();
	
			}

		$xyz_fbap_session_state = md5(uniqid(rand(), TRUE));
		setcookie("xyz_fbap_session_state",$xyz_fbap_session_state,"0","/");
		
		$dialog_url = "https://www.facebook.com/".XYZ_FBAP_FB_API_VERSION."/dialog/oauth?client_id="
				. $app_id . "&redirect_uri=" . $my_url . "&state="
				. $xyz_fbap_session_state . "&scope=email,public_profile,publish_pages,user_posts,manage_pages,user_photos";
		
		header("Location: " . $dialog_url);
}


if(isset($_COOKIE['xyz_fbap_session_state']) && isset($_REQUEST['state']) && ($_COOKIE['xyz_fbap_session_state'] === $_REQUEST['state'])) {
	
	$token_url = "https://graph.facebook.com/".XYZ_FBAP_FB_API_VERSION."/oauth/access_token?"
	. "client_id=" . $app_id . "&redirect_uri=" . $my_url
	. "&client_secret=" . $app_secret . "&code=" . $code;
	
	$params = null;$access_token="";
	$response = wp_remote_get($token_url,array('sslverify'=> (get_option('xyz_fbap_peer_verification')=='1') ? true : false));

	if(is_array($response))
	{
		if(isset($response['body']))
		{
			$params= json_decode($response['body']);
			if(isset($params->access_token))
			$access_token = $params->access_token;
// 			parse_str($response['body'], $params);
// 			if(isset($params['access_token']))
// 			$access_token = $params['access_token'];
		}
	}
	
	if($access_token!="")
	{
		
		update_option('xyz_fbap_fb_token',$access_token);
		update_option('xyz_fbap_af',0);

		$offset=0;$limit=100;$data=array();
		//$fbid=get_option('xyz_fbap_fb_id');
		do
		{
			$result1="";$pagearray1="";
			$pp=wp_remote_get("https://graph.facebook.com/".XYZ_FBAP_FB_API_VERSION."/me/accounts?access_token=$access_token&limit=$limit&offset=$offset",array('sslverify'=> (get_option('xyz_fbap_peer_verification')=='1') ? true : false));
			
			if(is_array($pp))
			{
				$result1=$pp['body'];
				$pagearray1 = json_decode($result1);
				if(is_array($pagearray1->data))
					$data = array_merge($data, $pagearray1->data);
			}
			else
				break;
			$offset += $limit;
// 			if(!is_array($pagearray1->paging))
// 				break;
// 		}while(array_key_exists("next", $pagearray1->paging));
		}while(isset($pagearray1->paging->next));
			
		$count=count($data);			
		$fbap_pages_ids1=get_option('xyz_fbap_pages_ids');
		$fbap_pages_ids0=array();$newpgs="";
		if($fbap_pages_ids1!="")
			$fbap_pages_ids0=explode(",",$fbap_pages_ids1);
		
		$fbap_pages_ids=array();$profile_flg=0;
		for($i=0;$i<count($fbap_pages_ids0);$i++)
		{
		if($fbap_pages_ids0[$i]!="-1")
			$fbap_pages_ids[$i]=trim(substr($fbap_pages_ids0[$i],0,strpos($fbap_pages_ids0[$i],"-")));
			else{
			$fbap_pages_ids[$i]=$fbap_pages_ids0[$i];$profile_flg=1;
			}
		}		
		for($i=0;$i<$count;$i++)
		{
		if(in_array($data[$i]->id, $fbap_pages_ids))
			$newpgs.=$data[$i]->id."-".$data[$i]->access_token.",";
		}
		$newpgs=rtrim($newpgs,",");
		if($profile_flg==1)
		{
			if($newpgs!="")
			$newpgs=$newpgs.",-1";
            else
            $newpgs=-1;
		}
		update_option('xyz_fbap_pages_ids',$newpgs);
		
		$url = 'https://graph.facebook.com/'.XYZ_FBAP_FB_API_VERSION.'/me?access_token='.$access_token;
		$contentget=wp_remote_get($url,array('sslverify'=> (get_option('xyz_fbap_peer_verification')=='1') ? true : false));$page_id='';
		if(is_array($contentget))
		{
			$result1=$contentget['body'];
			$pagearray = json_decode($result1);
			$page_id=$pagearray->id;
		}
		update_option('xyz_fbap_fb_numericid',$page_id);
           header("Location:".admin_url('admin.php?page=facebook-auto-publish-settings&auth=1'));
	}
	else
	{	
		$xyz_fbap_af=get_option('xyz_fbap_af');
		
		if($xyz_fbap_af==1){
			header("Location:".admin_url('admin.php?page=facebook-auto-publish-settings&msg=3'));
			exit();
		}
	}
}
else {
	//header("Location:".admin_url('admin.php?page=facebook-auto-publish-settings&msg=2'));
	//exit();
}


?>
