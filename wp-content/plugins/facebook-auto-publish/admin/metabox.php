<?php 
if( !defined('ABSPATH') ){ exit();}
add_action( 'add_meta_boxes', 'xyz_fbap_add_custom_box' );
$GLOBALS['edit_flag']=0;
function xyz_fbap_add_custom_box()
{
	$posttype="";
	if(isset($_GET['post_type']))
		$posttype=$_GET['post_type'];
	
	if($posttype=="")
		$posttype="post";
	
if(isset($_GET['action']) && $_GET['action']=="edit" && !empty($_GET['post']))   /// empty check added for fixing client scenario
	{
		$postid=intval($_GET['post']);
		$get_post_meta=get_post_meta($postid,"xyz_fbap",true);
		if($get_post_meta==1){
			$GLOBALS['edit_flag']=1;
		}
		global $wpdb;
		$table='posts';
		$accountCount = $wpdb->query($wpdb->prepare( 'SELECT * FROM '.$wpdb->prefix.$table.' WHERE id=%d and post_status!=%s LIMIT %d,%d',array($postid,'draft',0,1) )) ;
		if($accountCount>0){
			$GLOBALS['edit_flag']=1;
			}
		$posttype=get_post_type($postid);
	}
	if ($posttype=="page")
	{

		$xyz_fbap_include_pages=get_option('xyz_fbap_include_pages');
		if($xyz_fbap_include_pages==0)
			return;
	}
	else if($posttype=="post")
	{
		$xyz_fbap_include_posts=get_option('xyz_fbap_include_posts');
		if($xyz_fbap_include_posts==0)
			return;
	}
	else if($posttype!="post")
	{
		$xyz_fbap_include_customposttypes=get_option('xyz_fbap_include_customposttypes');
     	$carr=explode(',', $xyz_fbap_include_customposttypes);
		if(!in_array($posttype,$carr))
			return;

	}
	
	
	
	if(get_option('xyz_fbap_af')==0 && get_option('xyz_fbap_fb_token')!="" && get_option('xyz_fbap_post_permission')==1)
	add_meta_box( "xyz_fbap", '<strong>WP Facebook Auto Publish </strong>', 'xyz_fbap_addpostmetatags') ;
}
function xyz_fbap_addpostmetatags()
{
	$imgpath= plugins_url()."/facebook-auto-publish/images/";
	$heimg=$imgpath."support.png";
	$xyz_fbap_catlist=get_option('xyz_fbap_include_categories');
// 	if (is_array($xyz_fbap_catlist))
// 	$xyz_fbap_catlist=implode(',', $xyz_fbap_catlist);
	?>
<script>

function displaycheck_fbap()
{
	var fcheckid=jQuery("input[name='xyz_fbap_post_permission']:checked").val();
	
// var fcheckid=document.getElementsByName("xyz_fbap_post_permission")[1].value;alert(fcheckid);
if(fcheckid==1)
{
	document.getElementById("fpabpmd").style.display='';	
	document.getElementById("fpabpmf").style.display='';
	document.getElementById("fpabpmftarea").style.display='';

}
else
{
	document.getElementById("fpabpmd").style.display='none';	
	document.getElementById("fpabpmf").style.display='none';	
	document.getElementById("fpabpmftarea").style.display='none';	
}


}


</script>
<script type="text/javascript">
function detdisplay_fbap(id)
{
	document.getElementById(id).style.display='';
}
function dethide_fbap(id)
{
	document.getElementById(id).style.display='none';
}


jQuery(document).ready(function() {
	displaycheck_fbap();
   var xyz_fbap_post_permission=jQuery("input[name='xyz_fbap_post_permission']:checked").val();
   XyzFbapToggleRadio(xyz_fbap_post_permission,'xyz_fbap_post_permission'); 
	jQuery('#category-all').bind("DOMSubtreeModified",function(){
		fbap_get_categorylist(1);
		});
	
	fbap_get_categorylist(1);
	jQuery('#category-all').on("click",'input[name="post_category[]"]',function() {
		fbap_get_categorylist(1);
				});

	jQuery('#category-pop').on("click",'input[type="checkbox"]',function() {
		fbap_get_categorylist(2);
				});
});

function fbap_get_categorylist(val)
{
	var cat_list="";var chkdArray=new Array();var cat_list_array=new Array();
	var posttype="<?php echo get_post_type() ;?>";
	if(val==1){
	 jQuery('input[name="post_category[]"]:checked').each(function() {
		 cat_list+=this.value+",";
		});
	}else if(val==2)
	{
		jQuery('#category-pop input[type="checkbox"]:checked').each(function() {
			
			cat_list+=this.value+",";
		});
	}
	 if (cat_list.charAt(cat_list.length - 1) == ',') {
		 cat_list = cat_list.substr(0, cat_list.length - 1);
		}
		jQuery('#cat_list').val(cat_list);
		
		var xyz_fbap_catlist="<?php echo $xyz_fbap_catlist;?>";
		if(xyz_fbap_catlist!="All")
		{
			cat_list_array=xyz_fbap_catlist.split(',');
			var show_flag=1;
			var chkdcatvals=jQuery('#cat_list').val();
			chkdArray=chkdcatvals.split(',');
			for(var x=0;x<chkdArray.length;x++) { 
				
				if(inArray(chkdArray[x], cat_list_array))
				{
					show_flag=1;
					break;
				}
				else
				{
					show_flag=0;
					continue;
				}
				
			}
			if(show_flag==0 && posttype=="post")
				jQuery('#xyz_fbMetabox').hide();
			else
				jQuery('#xyz_fbMetabox').show();
		}
}
function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}


</script>
<table class="xyz_fbap_metalist_table">
<input type="hidden" name="cat_list" id="cat_list" value="">
<input type="hidden" name="xyz_fbap_post" id="xyz_fbap_post" value="0" >
<tr id="xyz_fbMetabox"><td colspan="2" >
<?php  if(get_option('xyz_fbap_post_permission')==1) {?>
<table class="xyz_fbap_meta_acclist_table"><!-- FB META -->


<tr>
		<td colspan="2" class="xyz_fbap_pleft15 xyz_fbap_meta_acclist_table_td"><strong>Facebook</strong>
		</td>
</tr>

<tr><td colspan="2" valign="top">&nbsp;</td></tr>
	
	
	<tr valign="top">
		<td class="xyz_fbap_pleft15" width="60%">Enable auto publish post to my facebook account
		</td>
		<td  class="switch-field">
		<label id="xyz_fbap_post_permission_yes"><input type="radio" name="xyz_fbap_post_permission" id="xyz_fbap_post_permission_1" value="1" checked/>Yes</label>
		<label id="xyz_fbap_post_permission_no"><input type="radio" name="xyz_fbap_post_permission" id="xyz_fbap_post_permission_0" value="0" />No</label>

		</td>
	</tr>
	<tr valign="top" id="fpabpmd">
		<td class="xyz_fbap_pleft15">Posting method
		</td>
		<td><select id="xyz_fbap_po_method" name="xyz_fbap_po_method">
				<option value="3"
				<?php  if(get_option('xyz_fbap_po_method')==3) echo 'selected';?>>Simple text message</option>
				
				<optgroup label="Text message with image">
					<option value="4"
					<?php  if(get_option('xyz_fbap_po_method')==4) echo 'selected';?>>Upload image to app album</option>
					<option value="5"
					<?php  if(get_option('xyz_fbap_po_method')==5) echo 'selected';?>>Upload image to timeline album</option>
				</optgroup>
				
				<optgroup label="Text message with attached link">
					<option value="1"
					<?php  if(get_option('xyz_fbap_po_method')==1) echo 'selected';?>>Attach
						your blog post</option>
					<option value="2"
					<?php  if(get_option('xyz_fbap_po_method')==2) echo 'selected';?>>
						Share a link to your blog post</option>
					</optgroup>
		</select>
		</td>
	</tr>
	<tr valign="top" id="fpabpmf">
		<td class="xyz_fbap_pleft15">Message format for posting <img src="<?php echo $heimg?>"
						onmouseover="detdisplay_fbap('xyz_fbap_informationdiv')" onmouseout="dethide_fbap('xyz_fbap_informationdiv')" style="width:13px;height:auto;">
						<div id="xyz_fbap_informationdiv" class="fbap_informationdiv" style="display: none;">
							{POST_TITLE} - Insert the title of your post.<br />{PERMALINK} -
							Insert the URL where your post is displayed.<br />{POST_EXCERPT}
							- Insert the excerpt of your post.<br />{POST_CONTENT} - Insert
							the description of your post.<br />{BLOG_TITLE} - Insert the name
							of your blog.<br />{USER_NICENAME} - Insert the nicename
							of the author.<br />{POST_ID} - Insert the ID of your post.
							<br />{POST_PUBLISH_DATE} - Insert the publish date of your post.
							<br />{USER_DISPLAY_NAME} - Insert the display name of the author.
						</div>
		</td>
	<td>
	<select name="xyz_fbap_info" id="xyz_fbap_info" onchange="xyz_fbap_info_insert(this)">
		<option value ="0" selected="selected">--Select--</option>
		<option value ="1">{POST_TITLE}  </option>
		<option value ="2">{PERMALINK} </option>
		<option value ="3">{POST_EXCERPT}  </option>
		<option value ="4">{POST_CONTENT}   </option>
		<option value ="5">{BLOG_TITLE}   </option>
		<option value ="6">{USER_NICENAME}   </option>
		<option value ="7">{POST_ID}   </option>
		<option value ="8">{POST_PUBLISH_DATE}   </option>
		<option value= "9">{USER_DISPLAY_NAME}</option>
		</select> </td></tr>
		
		<tr id="fpabpmftarea"><td>&nbsp;</td><td>
		<textarea id="xyz_fbap_message"  name="xyz_fbap_message" style="height:80px !important;" ><?php echo esc_textarea(get_option('xyz_fbap_message'));?></textarea>
	</td></tr>
	
	</table>
	<?php }?>
	</td></tr>
	
		
</table>
<script type="text/javascript">
	

	var edit_flag="<?php echo $GLOBALS['edit_flag'];?>";
	if(edit_flag==1)
		load_edit_action();
	
	function load_edit_action()
	{
		document.getElementById("xyz_fbap_post").value=1;
		var xyz_fbap_default_selection_edit="<?php echo esc_html(get_option('xyz_fbap_default_selection_edit'));?>";
		if(xyz_fbap_default_selection_edit=="")
			xyz_fbap_default_selection_edit=0;
		if(xyz_fbap_default_selection_edit==1)
			return;
		jQuery('#xyz_fbap_post_permission_0').attr('checked',true);
		displaycheck_fbap();
		
	}
	function xyz_fbap_info_insert(inf){
		
	    var e = document.getElementById("xyz_fbap_info");
	    var ins_opt = e.options[e.selectedIndex].text;
	    if(ins_opt=="0")
	    	ins_opt="";
	    var str=jQuery("textarea#xyz_fbap_message").val()+ins_opt;
	    jQuery("textarea#xyz_fbap_message").val(str);
	    jQuery('#xyz_fbap_info :eq(0)').prop('selected', true);
	    jQuery("textarea#xyz_fbap_message").focus();

	}
	jQuery("#xyz_fbap_post_permission_no").click(function(){
		displaycheck_fbap();
		XyzFbapToggleRadio(0,'xyz_fbap_post_permission');
	});
	jQuery("#xyz_fbap_post_permission_yes").click(function(){
		displaycheck_fbap();
		XyzFbapToggleRadio(1,'xyz_fbap_post_permission');
	});
	</script>
<?php 
}
?>