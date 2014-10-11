<?php
function wp_smtp_admin(){
	add_options_page('WP SMTP Options', 'WP SMTP','manage_options', __FILE__, 'wp_smtp_page');
}

function wp_smtp_page(){
	$ws_nonce = wp_create_nonce('my_ws_nonce');
	global $wsOptions;
	if(isset($_POST['wp_smtp_update']) && isset($_POST['wp_smtp_nonce_update'])){
		if(!wp_verify_nonce(trim($_POST['wp_smtp_nonce_update']),'my_ws_nonce')){
			wp_die('Security check not passed!');
		}
		$wsOptions = array();
		$wsOptions["from"] = trim($_POST['wp_smtp_from']);
		$wsOptions["fromname"] = trim($_POST['wp_smtp_fromname']);
		$wsOptions["host"] = trim($_POST['wp_smtp_host']);
		$wsOptions["smtpsecure"] = trim($_POST['wp_smtp_smtpsecure']);
		$wsOptions["port"] = trim($_POST['wp_smtp_port']);
		$wsOptions["smtpauth"] = trim($_POST['wp_smtp_smtpauth']);
		$wsOptions["username"] = trim($_POST['wp_smtp_username']);
		$wsOptions["password"] = trim($_POST['wp_smtp_password']);
		$wsOptions["deactivate"] = (isset($_POST['wp_smtp_deactivate'])) ? trim($_POST['wp_smtp_deactivate']) : "";
		update_option("wp_smtp_options",$wsOptions);
		if(!is_email($wsOptions["from"])){
			echo '<div id="message" class="updated fade"><p><strong>' . __("The field \"From\" must be a valid email address!","WP-SMTP") . '</strong></p></div>';
		}
		elseif(empty($wsOptions["host"])){
			echo '<div id="message" class="updated fade"><p><strong>' . __("The field \"SMTP Host\" can not be left blank!","WP-SMTP") . '</strong></p></div>';
		}
		else{
			echo '<div id="message" class="updated fade"><p><strong>' . __("Options saved.","WP-SMTP") . '</strong></p></div>';
		}
	}
	if(isset($_POST['wp_smtp_test']) && isset($_POST['wp_smtp_nonce_test'])){
		if(!wp_verify_nonce(trim($_POST['wp_smtp_nonce_test']),'my_ws_nonce')){
			wp_die('Security check not passed!');
		}
		$to = trim($_POST['wp_smtp_to']);
		$subject = trim($_POST['wp_smtp_subject']);
		$message = trim($_POST['wp_smtp_message']);
		$failed = 0;
		if(!empty($to) && !empty($subject) && !empty($message)){
			try{
				$result = wp_mail($to,$subject,$message);
			}catch(phpmailerException $e){
				$failed = 1;
			}
		}
		else{
			$failed = 2;
		}
		if(!$failed){
			if($result==TRUE){
				echo '<div id="message" class="updated fade"><p><strong>' . __("Message sent!","WP-SMTP") . '</strong></p></div>';
			}
			else{
				$failed = 1;
			}
		}
		if($failed == 1){
			echo '<div id="message" class="updated fade"><p><strong>' . __("Some errors occurred!","WP-SMTP") . '</strong></p></div>';
		}
		elseif($failed == 2){
			echo '<div id="message" class="updated fade"><p><strong>' . __("The fields \"To\" \"Subject\" \"Message\" can not be left blank when testing!","WP-SMTP") . '</strong></p></div>';
		}
	}
?>
<div class="wrap">
	
<?php screen_icon(); ?>
<h2>
WP SMTP
<span style="margin-left:10px; vertical-align:middle;">
<a href="<?php echo plugins_url('screenshot-1.png',__FILE__); ?>" target="_blank"><img src="<?php echo plugins_url('/img/gmail.png',__FILE__); ?>" alt="Gmail" title="Gmail" /></a>
<a href="<?php echo plugins_url('screenshot-2.png',__FILE__); ?>" target="_blank"><img src="<?php echo plugins_url('/img/yahoo.png',__FILE__); ?>" alt="Yahoo!" title="Yahoo!" /></a>
<a href="<?php echo plugins_url('screenshot-3.png',__FILE__); ?>" target="_blank"><img src="<?php echo plugins_url('/img/microsoft.png',__FILE__); ?>" alt="Microsoft" title="Microsoft" /></a>
<a href="<?php echo plugins_url('screenshot-4.png',__FILE__); ?>" target="_blank"><img src="<?php echo plugins_url('/img/163.png',__FILE__); ?>" alt="163" title="163" /></a>
<a href="<?php echo plugins_url('screenshot-5.png',__FILE__); ?>" target="_blank"><img src="<?php echo plugins_url('/img/qq.png',__FILE__); ?>" alt="QQ" title="QQ" /></a>
</span>
</h2>

<form action="" method="post" enctype="multipart/form-data" name="wp_smtp_form">

<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php _e('From','WP-SMTP'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="wp_smtp_from" value="<?php echo $wsOptions["from"]; ?>" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('From Name','WP-SMTP'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="wp_smtp_fromname" value="<?php echo $wsOptions["fromname"]; ?>" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('SMTP Host','WP-SMTP'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="wp_smtp_host" value="<?php echo $wsOptions["host"]; ?>" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('SMTP Secure','WP-SMTP'); ?>
		</th>
		<td>
			<label>
				<input name="wp_smtp_smtpsecure" type="radio" value=""<?php if ($wsOptions["smtpsecure"] == '') { ?> checked="checked"<?php } ?> />
				None
			</label>
			&nbsp;
			<label>
				<input name="wp_smtp_smtpsecure" type="radio" value="ssl"<?php if ($wsOptions["smtpsecure"] == 'ssl') { ?> checked="checked"<?php } ?> />
				SSL
			</label>
			&nbsp;
			<label>
				<input name="wp_smtp_smtpsecure" type="radio" value="tls"<?php if ($wsOptions["smtpsecure"] == 'tls') { ?> checked="checked"<?php } ?> />
				TLS
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('SMTP Port','WP-SMTP'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="wp_smtp_port" value="<?php echo $wsOptions["port"]; ?>" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('SMTP Authentication','WP-SMTP'); ?>
		</th>
		<td>
			<label>
				<input name="wp_smtp_smtpauth" type="radio" value="no"<?php if ($wsOptions["smtpauth"] == 'no') { ?> checked="checked"<?php } ?> />
				No
			</label>
			&nbsp;
			<label>
				<input name="wp_smtp_smtpauth" type="radio" value="yes"<?php if ($wsOptions["smtpauth"] == 'yes') { ?> checked="checked"<?php } ?> />
				Yes
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('Username','WP-SMTP'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="wp_smtp_username" value="<?php echo $wsOptions["username"]; ?>" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('Password','WP-SMTP'); ?>
		</th>
		<td>
			<label>
				<input type="password" name="wp_smtp_password" value="<?php echo $wsOptions["password"]; ?>" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('Delete Options','WP-SMTP'); ?>
		</th>
		<td>
			<label>
				<input type="checkbox" name="wp_smtp_deactivate" value="yes" <?php if($wsOptions["deactivate"]=='yes') echo 'checked="checked"'; ?> />
				<?php _e('Delete options while deactivate this plugin.','WP-SMTP'); ?>
			</label>
		</td>
	</tr>
</table>

<p class="submit">
<input type="hidden" name="wp_smtp_update" value="update" />
<input type="hidden" name="wp_smtp_nonce_update" value="<?php echo $ws_nonce; ?>" />
<input type="submit" class="button-primary" name="Submit" value="<?php _e('Save Changes'); ?>" />
</p>

</form>

<form action="" method="post" enctype="multipart/form-data" name="wp_smtp_testform">
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php _e('To:','WP-SMTP'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="wp_smtp_to" value="" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('Subject:','WP-SMTP'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="wp_smtp_subject" value="" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('Message:','WP-SMTP'); ?>
		</th>
		<td>
			<label>
				<textarea type="text" name="wp_smtp_message" value="" cols="45" rows="3" style="width:284px;height:62px;"></textarea>
			</label>
		</td>
	</tr>
</table>
<p class="submit">
<input type="hidden" name="wp_smtp_test" value="test" />
<input type="hidden" name="wp_smtp_nonce_test" value="<?php echo $ws_nonce; ?>" />
<input type="submit" class="button-primary" value="<?php _e('Send Test','WP-SMTP'); ?>" />
</p>
</form>

<br />
<?php $donate_url = plugins_url('/img/paypal_32_32.jpg', __FILE__);?>
<?php $paypal_donate_url = plugins_url('/img/paypal_donate_email.jpg', __FILE__);?>
<?php $ali_donate_url = plugins_url('/img/alipay_donate_email.jpg', __FILE__);?>
<div class="icon32"><img src="<?php echo $donate_url; ?>" alt="Donate" /></div>
<h2>Donate</h2>
<p>
If you find my work useful and you want to encourage the development of more free resources, you can do it by donating.
</p>
<p>
<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SCWY6NPFRR8EY" target="_blank"><img src="<?php echo $paypal_donate_url; ?>" alt="Paypal Donate" title="Paypal" /></a>
&nbsp;
<a href="https://www.alipay.com/" target="_blank"><img src="<?php echo $ali_donate_url; ?>" alt="Alipay Donate" title="Alipay" /></a>
</p>
<br />

<?php $blq_logo_url = plugins_url('/img/blq_32_32.jpg', __FILE__);?>
<div class="icon32"><img src="<?php echo $blq_logo_url; ?>" alt="BoLiQuan" /></div>
<h2>Related Links</h2>
<ul style="margin:0 18px;">
<li><a href="http://boliquan.com/wp-smtp/" target="_blank">WP SMTP (FAQ)</a> | <a href="http://wordpress.org/plugins/wp-smtp/" target="_blank">Usage</a> | <a href="http://wordpress.org/plugins/wp-smtp/" target="_blank">Download</a></li>
<li><a href="http://boliquan.com/wp-clean-up/" target="_blank">WP Clean Up</a> | <a href="http://wordpress.org/plugins/wp-clean-up/" target="_blank">Download</a></li>
<li><a href="http://boliquan.com/wp-anti-spam/" target="_blank">WP Anti Spam</a> | <a href="http://wordpress.org/plugins/wp-anti-spam/" target="_blank">Download</a></li>
<li><a href="http://boliquan.com/wp-code-highlight/" target="_blank">WP Code Highlight</a> | <a href="http://wordpress.org/plugins/wp-code-highlight/" target="_blank">Download</a></li>
<li><a href="http://boliquan.com/wp-slug-translate/" target="_blank">WP Slug Translate</a> | <a href="http://wordpress.org/plugins/wp-slug-translate/" target="_blank">Download</a></li>
<li><a href="http://boliquan.com/yg-share/" target="_blank">YG Share</a> | <a href="http://wordpress.org/plugins/yg-share/" target="_blank">Download</a></li>
<li><a href="http://boliquan.com/ylife/" target="_blank">YLife</a> | <a href="http://code.google.com/p/ylife/downloads/list" target="_blank">Download</a></li>
<li><a href="http://boliquan.com/" target="_blank">BoLiQuan</a></li>
</ul>

<div style="text-align:center; margin:60px 0 10px 0;">&copy; <?php echo date("Y"); ?> BoLiQuan.COM</div>

</div>
<?php 
}
add_action('admin_menu', 'wp_smtp_admin');
?>