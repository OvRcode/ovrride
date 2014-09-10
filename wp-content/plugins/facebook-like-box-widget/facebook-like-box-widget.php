<?php
/*
 * Plugin Name: Facebook Like Box
 * Version: 2.7
 * Plugin URI: http://wordpress.org/extend/plugins/facebook-like-box-widget/
 * Description: Facebook Like Box Widget is a social plugin that enables Facebook Page owners to attract and gain Likes from their own website. The Like Box enables users to: see how many users already like this page, and which of their friends like it too, read recent posts from the page and Like the page with one click, without needing to visit the page.
 * Author: Sunento Agustiar Wu
 * Author URI: http://vivociti.com/component/option,com_remository/Itemid,40/
 * License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
class FacebookLikeBoxWidget extends WP_Widget
{
	/**
	* Declares the FacebookLikeBoxWidget class.
	*
	*/
	function FacebookLikeBoxWidget(){
		$widget_ops = array('classname' => 'widget_FacebookLikeBox', 'description' => __( "Facebook Like Box Widget is a social plugin that enables Facebook Page owners to attract and gain Likes from their own website. The Like Box enables users to: see how many users already like this page, and which of their friends like it too, read recent posts from the page and Like the page with one click, without needing to visit the page.") );
		$control_ops = array('width' => 300, 'height' => 300);
		$this->WP_Widget('FacebookLikeBox', __('Facebook Like Box Widget'), $widget_ops, $control_ops);
	}
	
	/**
	* Displays the Widget
	*
	*/
	function widget($args, $instance){
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);		
		$pluginDisplayType = empty($instance['pluginDisplayType']) ? 'like_box' : $instance['pluginDisplayType'];
		$layoutMode = empty($instance['layoutMode']) ? 'xfbml' : $instance['layoutMode'];
                //example of Page URL : http://www.facebook.com/pages/VivoCiticom-Joomla-Wordpress-Blogger-Drupal-DNN-Community/119691288064264
		$pageURL = empty($instance['pageURL']) ? '' : $instance['pageURL'];
		$fblike_button_style = empty($instance['fblike_button_style']) ? 'standard' : $instance['fblike_button_style'];
		$fblike_button_showFaces = empty($instance['fblike_button_showFaces']) ? 'no' : $instance['fblike_button_showFaces'];
		$fblike_button_verb_to_display = empty($instance['fblike_button_verb_to_display']) ? 'recommend' : $instance['fblike_button_verb_to_display'];
		$fblike_button_font = empty($instance['fblike_button_font']) ? 'lucida grande' : $instance['fblike_button_font'];
		$fblike_button_width = empty($instance['fblike_button_width']) ? '292' : $instance['fblike_button_width'];
		$fblike_button_colorScheme = empty($instance['fblike_button_colorScheme']) ? 'light' : $instance['fblike_button_colorScheme'];
		
		//example of Page ID : 123961057630124
		$pageID = empty($instance['pageID']) ? '' : $instance['pageID'];
		$connection = empty($instance['connection']) ? '10' : $instance['connection'];
		$width = empty($instance['width']) ? '292' : $instance['width'];
		$height = empty($instance['height']) ? '255' : $instance['height'];
		$streams = empty($instance['streams']) ? 'yes' : $instance['streams'];
		$colorScheme = empty($instance['colorScheme']) ? 'light' : $instance['colorScheme'];
		$borderColor = empty($instance['borderColor']) ? 'AAAAAA' : $instance['borderColor'];
		$enableOtherSocialButtons = empty($instance['enableOtherSocialButtons']) ? 'no' : $instance['enableOtherSocialButtons'];
		$enableTwitterButtons = empty($instance['enableTwitterButtons']) ? 'no' : $instance['enableTwitterButtons'];
		$addThisVerticalStyle = empty($instance['addThisVerticalStyle']) ? '1' : $instance['addThisVerticalStyle'];
		$twitterButtonStyle = empty($instance['twitterButtonStyle']) ? '127' : $instance['twitterButtonStyle'];
		$enableAfterOrBeforeFBLikeBox = empty($instance['enableAfterOrBeforeFBLikeBox']) ? 'before' : $instance['enableAfterOrBeforeFBLikeBox'];
		$addThisPubId = empty($instance['addThisPubId']) ? '' : $instance['addThisPubId'];
		$twitterUsername = empty($instance['twitterUsername']) ? '' : $instance['twitterUsername'];
		$showFaces = empty($instance['showFaces']) ? 'yes' : $instance['showFaces'];
		$header = empty($instance['header']) ? 'yes' : $instance['header'];
		//$creditOn = empty($instance['creditOn']) ? 'no' : $instance['creditOn'];
		$sharePlugin = "http://vivociti.com";
		
		if ($fblike_button_showFaces == "yes") {
			$fblike_button_showFaces == "true";			
		} else {
			$fblike_button_showFaces == "false";
		}		
		if ($showFaces == "yes") {
			$showFaces = "true";			
		} else {
			$showFaces = "false";
		}
		if ($streams == "yes") {
			$streams = "true";
			$height = $height + 300;
		} else {
			$streams = "false";
		}
		if ($header == "yes") {
			$header = "true";
			$height = $height + 32;
		} else {
			$header = "false";
		}

		# Before the widget
		echo $before_widget;
		
		# The title
		if ( $title )
			echo $before_title . $title . $after_title;
		
		//this is to check for backward compatibility, previous version all is using Page ID instead of Page URL
		//If Page URL is filled, we will use it
		$isUsingPageURL = false;
		if (strlen($pageURL) > 23) {	
			$isUsingPageURL = true;  //flag to be used for backward
			$like_box_iframe = "<iframe src=\"http://www.facebook.com/plugins/likebox.php?href=$pageURL&amp;width=$width&amp;colorscheme=$colorScheme&amp;border_color=$borderColor&amp;show_faces=$showFaces&amp;connections=$connection&amp;stream=$streams&amp;header=$header&amp;height=$height\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:" . $width . "px; height:" . $height . "px;\" allowTransparency=\"true\"></iframe>";
			$like_box_xfbml = "<script src=\"http://connect.facebook.net/en_US/all.js#xfbml=1\"></script><fb:like-box href=\"$pageURL\" width=\"$width\" show_faces=\"$showFaces\" border_color=\"$borderColor\" stream=\"$streams\" header=\"$header\"></fb:like-box>";
		} else {
			$like_box_iframe = "<iframe src=\"http://www.facebook.com/plugins/likebox.php?id=$pageID&amp;width=$width&amp;colorscheme=$colorScheme&amp;border_color=$borderColor&amp;show_faces=$showFaces&amp;connections=$connection&amp;stream=$streams&amp;header=$header&amp;height=$height\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:" . $width . "px; height:" . $height . "px;\" allowTransparency=\"true\"></iframe>";
			$like_box_xfbml = "<script src=\"http://connect.facebook.net/en_US/all.js#xfbml=1\"></script><fb:like-box id=\"$pageID\" width=\"$width\" show_faces=\"$showFaces\" border_color=\"$borderColor\" stream=\"$streams\" header=\"$header\"></fb:like-box>";		
		}
		$like_button_xfbml  = "<script src=\"http://connect.facebook.net/en_US/all.js#xfbml=1\"></script><fb:like layout=\"$fblike_button_style\" show_faces=\"$fblike_button_showFaces\" width=\"$fblike_button_width\" action=\"$fblike_button_verb_to_display\" font=\"$fblike_button_font\" colorscheme=\"$fblike_button_colorScheme\"></fb:like>";
		$html = ""; 
		$img_live_dir = 'http://www.cmsvoteup.com/images/power_by_2x2.gif';
		$html = "<div><a href=\"http://cmsvoteup.com/joomla-extensions/facebook-like-box-like-recommendation-for-joomla-wordpress/\" title=\"Vote for this Free Facebook Like Box for Wordpress\" target=\"_blank\">*</a></div>"; 

		if ( ($enableTwitterButtons == "yes") && ($enableAfterOrBeforeFBLikeBox == "before") ){
			echo '<a href="http://twitter.com/' . $twitterUserName . '" title="Follow ' . $twitterUserName . ' "><img src="http://twithut.com/twitsigs/' . $twitterButtonStyle . '/' . $twitterUserName . '.png' .   '" border=0></a>';
		}
		switch ($pluginDisplayType) {
			case 'like_box' :
				if (strcmp($layoutMode, "iframe") == 0) {
					$renderedHTML = $like_box_iframe;
				} else {
					$renderedHTML = $like_box_xfbml;
				}
				break;
			case 'like_button' :
				$renderedHTML = $like_button_xfbml;
				break;
			case 'both':
				if (strcmp($layoutMode, "iframe") == 0) {
					$renderedHTML = $like_box_iframe;
				} else {
					$renderedHTML = $like_box_xfbml;
				}
				$renderedHTML = $renderedHTML . "\n" . $like_button_xfbml;
				break;
		}
		echo $renderedHTML;
		
		if ( ($enableTwitterButtons == "yes") && ($enableAfterOrBeforeFBLikeBox == "after") ){
			echo '<a href="http://twitter.com/' . $twitterUserName . '" title="Follow ' . $twitterUserName . ' "><img src="http://twithut.com/twitsigs/' . $twitterButtonStyle . '/' . $twitterUserName . '.png' .   '" border=0></a>';
		}
		
		/*
		if ($creditOn == "yes") {
            echo $html;
        } */
		
		if ($enableOtherSocialButtons == "yes") {
			switch ($addThisVerticalStyle) {
			case '1' :
				echo "
				<!-- AddThis Button BEGIN -->
				<div class=\"addthis_toolbox addthis_floating_style addthis_counter_style\" style=\"left:50px;top:50px;\">
				<a class=\"addthis_button_facebook_like\" fb:like:layout=\"box_count\"></a>
				<a class=\"addthis_button_tweet\" tw:count=\"vertical\"></a>
				<a class=\"addthis_button_google_plusone\" g:plusone:size=\"tall\"></a>
				<a class=\"addthis_counter\"></a>
				</div>
				<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js#pubid=$addThisPubId\"></script>
				<!-- AddThis Button END -->";
				break;
			case '2' :
				echo "
				<!-- AddThis Button BEGIN -->
				<div class=\"addthis_toolbox addthis_floating_style addthis_32x32_style\" style=\"left:50px;top:50px;\">
				<a class=\"addthis_button_preferred_1\"></a>
				<a class=\"addthis_button_preferred_2\"></a>
				<a class=\"addthis_button_preferred_3\"></a>
				<a class=\"addthis_button_preferred_4\"></a>
				<a class=\"addthis_button_compact\"></a>
				</div>
				<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-$addThisPubId\"></script>
				<!-- AddThis Button END -->
				";
				break;
			case '3':
				echo "
				<!-- AddThis Button BEGIN -->
				<div class=\"addthis_toolbox addthis_floating_style addthis_16x16_style\" style=\"left:50px;top:50px;\">
				<a class=\"addthis_button_preferred_1\"></a>
				<a class=\"addthis_button_preferred_2\"></a>
				<a class=\"addthis_button_preferred_3\"></a>
				<a class=\"addthis_button_preferred_4\"></a>
				<a class=\"addthis_button_compact\"></a>
				</div>
				<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-$addThisPubId\"></script>
				<!-- AddThis Button END -->
				";
				break;
			case '4' :
				echo "
				<!-- AddThis Button BEGIN -->
				<div class=\"addthis_toolbox addthis_floating_style addthis_counter_style\" style=\"right:50px;top:50px;\">
				<a class=\"addthis_button_facebook_like\" fb:like:layout=\"box_count\"></a>
				<a class=\"addthis_button_tweet\" tw:count=\"vertical\"></a>
				<a class=\"addthis_button_google_plusone\" g:plusone:size=\"tall\"></a>
				<a class=\"addthis_counter\"></a>
				</div>
				<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js#pubid=$addThisPubId\"></script>
				<!-- AddThis Button END -->";
				break;
			case '5' :
				echo "
				<!-- AddThis Button BEGIN -->
				<div class=\"addthis_toolbox addthis_floating_style addthis_32x32_style\" style=\"right:50px;top:50px;\">
				<a class=\"addthis_button_preferred_1\"></a>
				<a class=\"addthis_button_preferred_2\"></a>
				<a class=\"addthis_button_preferred_3\"></a>
				<a class=\"addthis_button_preferred_4\"></a>
				<a class=\"addthis_button_compact\"></a>
				</div>
				<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-$addThisPubId\"></script>
				<!-- AddThis Button END -->
				";
				break;
			case '6':
				echo "
				<!-- AddThis Button BEGIN -->
				<div class=\"addthis_toolbox addthis_floating_style addthis_16x16_style\" style=\"right:50px;top:50px;\">
				<a class=\"addthis_button_preferred_1\"></a>
				<a class=\"addthis_button_preferred_2\"></a>
				<a class=\"addthis_button_preferred_3\"></a>
				<a class=\"addthis_button_preferred_4\"></a>
				<a class=\"addthis_button_compact\"></a>
				</div>
				<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-$addThisPubId\"></script>
				<!-- AddThis Button END -->
				";
				break;
		}
     }
	
	//end of creditOn is yes

		# After the widget
		echo $after_widget;
	}
	
	/**
	* Saves the widgets settings.
	*
	*/
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['pageID'] = strip_tags(stripslashes($new_instance['pageID']));
		$instance['connection'] = strip_tags(stripslashes($new_instance['connection']));
		$instance['width'] = strip_tags(stripslashes($new_instance['width']));
		$instance['height'] = strip_tags(stripslashes($new_instance['height']));
		$instance['creditOn'] = strip_tags(stripslashes($new_instance['creditOn']));
		$instance['header'] = strip_tags(stripslashes($new_instance['header']));
		$instance['streams'] = strip_tags(stripslashes($new_instance['streams']));   //thanks to : Krzysztof Piech <chrisx29a@gmail.com>
		$instance['colorScheme'] = strip_tags(stripslashes($new_instance['colorScheme']));
		$instance['borderColor'] = strip_tags(stripslashes($new_instance['borderColor']));
		$instance['enableOtherSocialButtons'] = strip_tags(stripslashes($new_instance['enableOtherSocialButtons']));
		$instance['addThisVerticalStyle'] = strip_tags(stripslashes($new_instance['addThisVerticalStyle']));
		$instance['enableTwitterButtons'] = strip_tags(stripslashes($new_instance['enableTwitterButtons']));
		$instance['twitterButtonStyle'] = strip_tags(stripslashes($new_instance['twitterButtonStyle']));		
		$instance['addThisPubId'] = strip_tags(stripslashes($new_instance['addThisPubId']));
		$instance['twitterUsername'] = strip_tags(stripslashes($new_instance['twitterUsername']));
		$instance['showFaces'] = strip_tags(stripslashes($new_instance['showFaces']));		
		$instance['pluginDisplayType'] = strip_tags(stripslashes($new_instance['pluginDisplayType']));
		$instance['layoutMode'] = strip_tags(stripslashes($new_instance['layoutMode']));
		$instance['pageURL'] = strip_tags(stripslashes($new_instance['pageURL']));
		$instance['fblike_button_style'] = strip_tags(stripslashes($new_instance['fblike_button_style']));
		$instance['fblike_button_showFaces'] = strip_tags(stripslashes($new_instance['fblike_button_showFaces']));
		$instance['fblike_button_verb_to_display'] = strip_tags(stripslashes($new_instance['fblike_button_verb_to_display']));
		$instance['fblike_button_font'] = strip_tags(stripslashes($new_instance['fblike_button_font']));
		$instance['fblike_button_width'] = strip_tags(stripslashes($new_instance['fblike_button_width']));
		$instance['fblike_button_colorScheme'] = strip_tags(stripslashes($new_instance['fblike_button_colorScheme']));
		
		return $instance;
	}
	
	/**
	* Creates the edit form for the widget.
	*
	*/
	function form($instance){
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('title'=>'', 'pageID'=>'119691288064264', 'height'=>'255', 'width'=>'292', 'connection'=>'10', 'streams'=>'yes', 'colorScheme'=>'light', 'showFaces'=>'yes', 'borderColor'=>'AAAAAA','enableOtherSocialButtons'=>'no', 'addThisVerticalStyle'=>'1', 'addThisPubId'=>'', 'enableTwitterButtons'=>'no', 'twitterButtonStyle'=>'127', 'twitterUsername'=>'', 'enableAfterOrBeforeFBLikeBox'=>'before', 'header'=>'yes', 'creditOn'=>'no', 'pluginDisplayType'=>'like_box', 'layoutMode'=>'xfbml', 'pageURL'=>'http://www.facebook.com/pages/VivoCiticom-Joomla-Wordpress-Blogger-Drupal-DNN-Community/119691288064264', 'fblike_button_style'=>'standard', 'fblike_button_showFaces'=>'false','fblike_button_verb_to_display'=>'recommend','fblike_button_font'=>'arial', 'fblike_button_width'=>'292','fblike_button_colorScheme'=>'light') );
		
		$title = htmlspecialchars($instance['title']);		
		$pluginDisplayType = empty($instance['pluginDisplayType']) ? 'like_box' : $instance['pluginDisplayType'];
		$layoutMode = empty($instance['layoutMode']) ? 'xfbml' : $instance['layoutMode'];
		$pageURL = empty($instance['pageURL']) ? 'http://www.facebook.com/pages/...' : $instance['pageURL'];
		$fblike_button_style = empty($instance['fblike_button_style']) ? 'standard' : $instance['fblike_button_style'];
		$fblike_button_showFaces = empty($instance['fblike_button_showFaces']) ? 'no' : $instance['fblike_button_showFaces'];
		$fblike_button_verb_to_display = empty($instance['fblike_button_verb_to_display']) ? 'recommend' : $instance['fblike_button_verb_to_display'];
		$fblike_button_font = empty($instance['fblike_button_font']) ? 'lucida grande' : $instance['fblike_button_font'];
		$fblike_button_width = empty($instance['fblike_button_width']) ? '292' : $instance['fblike_button_width'];
		$fblike_button_colorScheme = empty($instance['fblike_button_colorScheme']) ? 'light' : $instance['fblike_button_colorScheme'];		
		$pageID = empty($instance['pageID']) ? '' : $instance['pageID'];
		$connection = empty($instance['connection']) ? '10' : $instance['connection'];
		$width = empty($instance['width']) ? '292' : $instance['width'];
		$height = empty($instance['height']) ? '255' : $instance['height'];
		$streams = empty($instance['streams']) ? 'yes' : $instance['streams'];
		$colorScheme = empty($instance['colorScheme']) ? 'yes' : $instance['colorScheme'];
		$borderColor = empty($instance['borderColor']) ? 'AAAAAA' : $instance['borderColor'];
		$enableOtherSocialButtons = empty($instance['enableOtherSocialButtons']) ? 'no' : $instance['enableOtherSocialButtons'];
		$addThisVerticalStyle = empty($instance['addThisVerticalStyle']) ? '1' : $instance['addThisVerticalStyle'];
		
		$enableTwitterButtons = empty($instance['enableTwitterButtons']) ? 'no' : $instance['enableTwitterButtons'];
		$twitterButtonStyle = empty($instance['twitterButtonStyle']) ? '127' : $instance['twitterButtonStyle'];
		$twitterUsername = empty($instance['twitterUsername']) ? '' : $instance['twitterUsername'];
		$enableAfterOrBeforeFBLikeBox = empty($instance['enableAfterOrBeforeFBLikeBox']) ? 'before' : $instance['enableAfterOrBeforeFBLikeBox'];
		
		$addThisPubId = empty($instance['addThisPubId']) ? '' : $instance['addThisPubId'];
		$showFaces = empty($instance['showFaces']) ? 'yes' : $instance['showFaces'];
		$header = empty($instance['header']) ? 'yes' : $instance['header'];
		//$creditOn = empty($instance['creditOn']) ? 'no' : $instance['creditOn'];
		$sharePlugin = "http://vivociti.com";
		
		$pageID = htmlspecialchars($instance['pageID']);
		$connection = htmlspecialchars($instance['connection']);
		$streams = htmlspecialchars($instance['streams']);
		$colorScheme = htmlspecialchars($instance['colorScheme']);
		$borderColor = htmlspecialchars($instance['borderColor']);
		$enableOtherSocialButtons = htmlspecialchars($instance['enableOtherSocialButtons']);
		$addThisVerticalStyle = htmlspecialchars($instance['addThisVerticalStyle']);
		$addThisPubId = htmlspecialchars($instance['addThisPubId']);
		$showFaces = htmlspecialchars($instance['showFaces']);
		$header = htmlspecialchars($instance['header']);
		//$creditOn = htmlspecialchars($instance['creditOn']);
		
		$pluginDisplayType = htmlspecialchars($instance['pluginDisplayType']);
		$layoutMode = htmlspecialchars($instance['layoutMode']);
		$pageURL = htmlspecialchars($instance['pageURL']);
		$fblike_button_style = htmlspecialchars($instance['fblike_button_style']);
		$fblike_button_showFaces = htmlspecialchars($instance['fblike_button_showFaces']);
		$fblike_button_verb_to_display = htmlspecialchars($instance['fblike_button_verb_to_display']);
		$fblike_button_font = htmlspecialchars($instance['fblike_button_font']);
		$fblike_button_width = htmlspecialchars($instance['fblike_button_width']);
		$fblike_button_colorScheme = htmlspecialchars($instance['fblike_button_colorScheme']);
		
		
				
		# Output the options
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('title') . '">' . __('Title:') . ' <input style="width: 250px;" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></label></p>';
		# Fill Display Type Selection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('pluginDisplayType') . '">' . __('Display:') . ' <select name="' . $this->get_field_name('pluginDisplayType')  . '" id="' . $this->get_field_id('pluginDisplayType')  . '">"';
?>
		<option value="like_box" <?php if ($pluginDisplayType == 'like_box') echo 'selected="yes"'; ?> >Like Box</option>
		<option value="like_button" <?php if ($pluginDisplayType == 'like_button') echo 'selected="yes"'; ?> >Like Button</option>			 
		<option value="both" <?php if ($pluginDisplayType == 'both') echo 'selected="yes"'; ?> >Like Box &amp; Button</option>			 
<?php
		echo '</select></label>';
		# Fill Layout Mode Selection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('layoutMode') . '">' . __('Render Mode:') . ' <select name="' . $this->get_field_name('layoutMode')  . '" id="' . $this->get_field_id('layoutMode')  . '">"';
?>
		<!--- <option value="iframe" <?php if ($layoutMode == 'iframe') echo 'selected="yes"'; ?> >IFRAME</option> --->
		<option value="xfbml" <?php if ($layoutMode == 'xfbml') echo 'selected="yes"'; ?> >XFBML</option>		
<?php
		echo '</select></label>';
		echo '<hr/><p style="text-align:left;"><b>Like Box Setting</b></p>';
		echo '<p style="text-align:left;"><i><strong>Fill Page ID Or Page URL below:</strong></i></p>';
		# Fill Page ID
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('pageID') . '">' . __('Facebook Page ID:') . ' <input style="width: 150px;" id="' . $this->get_field_id('pageID') . '" name="' . $this->get_field_name('pageID') . '" type="text" value="' . $pageID . '" /></label></p>';
		# Fill Page URL
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('pageURL') . '">' . __('Facebook Page URL:') . ' <input style="width: 150px;" id="' . $this->get_field_id('pageURL') . '" name="' . $this->get_field_name('pageURL') . '" type="text" value="' . $pageURL . '" /></label></p>';
		
		# Connection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('connection') . '">' . __('Connections:') . ' <input style="width: 100px;" id="' . $this->get_field_id('connection') . '" name="' . $this->get_field_name('connection') . '" type="text" value="' . $connection . '" /></label></p>';
		# Width
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('width') . '">' . __('Width:') . ' <input style="width: 100px;" id="' . $this->get_field_id('width') . '" name="' . $this->get_field_name('width') . '" type="text" value="' . $width . '" /></label></p>';
		# Height
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('height') . '">' . __('Height:') . ' <input style="width: 100px;" id="' . $this->get_field_id('height') . '" name="' . $this->get_field_name('height') . '" type="text" value="' . $height . '" /></label></p>';		
		# Fill Streams Selection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('streams') . '">' . __('Streams:') . ' <select name="' . $this->get_field_name('streams')  . '" id="' . $this->get_field_id('streams')  . '">"';
?>
		<option value="yes" <?php if ($streams == 'yes') echo 'selected="yes"'; ?> >Yes</option>
		<option value="no" <?php if ($streams == 'no') echo 'selected="yes"'; ?> >No</option>			 
<?php
		echo '</select></label>';
# Fill Color Scheme Selection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('colorScheme') . '">' . __('Color Scheme:') . ' <select name="' . $this->get_field_name('colorScheme')  . '" id="' . $this->get_field_id('colorScheme')  . '">"';
?>
		<option value="light" <?php if ($colorScheme == 'light') echo 'selected="yes"'; ?> >Light</option>
		<option value="dark" <?php if ($colorScheme == 'dark') echo 'selected="yes"'; ?> >Dark</option>			 
<?php
		echo '</select></label>';
		# Border Color
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('borderColor') . '">' . __('Border Color:') . ' <input style="width: 100px;" id="' . $this->get_field_id('borderColor') . '" name="' . $this->get_field_name('borderColor') . '" type="text" value="' . $borderColor . '" /></label></p>';
# Fill Show Faces Selection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('showFaces') . '">' . __('Show Faces:') . ' <select name="' . $this->get_field_name('showFaces')  . '" id="' . $this->get_field_id('showFaces')  . '">"';
?>
		<option value="yes" <?php if ($showFaces == 'yes') echo 'selected="yes"'; ?> >Yes</option>
		<option value="no" <?php if ($showFaces == 'no') echo 'selected="yes"'; ?> >No</option>			 
<?php
		echo '</select></label>';
	# Fill header Selection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('header') . '">' . __('Header:') . ' <select name="' . $this->get_field_name('header')  . '" id="' . $this->get_field_id('header')  . '">"';
?>
		<option value="yes" <?php if ($header == 'yes') echo 'selected="yes"'; ?> >Yes</option>
		<option value="no" <?php if ($header == 'no') echo 'selected="yes"'; ?> >No</option>			 
<?php
		echo '</select></label>';	
		echo '<hr/><p style="text-align:left;"><b>Like Button Setting</b></p>';
		# Fill Like Button Style Selection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('fblike_button_style') . '">' . __('Button Style:') . ' <select name="' . $this->get_field_name('fblike_button_style')  . '" id="' . $this->get_field_id('fblike_button_style')  . '">"';
?>
		<option value="standard" <?php if ($fblike_button_style == 'standard') echo 'selected="yes"'; ?> >standard</option>
		<option value="button_count" <?php if ($fblike_button_style == 'button_count') echo 'selected="yes"'; ?> >button_count</option>		
		<option value="box_count" <?php if ($fblike_button_style == 'box_count') echo 'selected="yes"'; ?> >box_count</option>		
<?php
		echo '</select></label>';
		# Fill Verb To Display Selection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('fblike_button_verb_to_display') . '">' . __('Verb To Display:') . ' <select name="' . $this->get_field_name('fblike_button_verb_to_display')  . '" id="' . $this->get_field_id('fblike_button_verb_to_display')  . '">"';
?>
		<option value="like" <?php if ($fblike_button_verb_to_display == 'like') echo 'selected="yes"'; ?> >like</option>
		<option value="recommend" <?php if ($fblike_button_verb_to_display == 'recommend') echo 'selected="yes"'; ?> >recommend</option>				
<?php
		echo '</select></label>';
		# Like Button Width
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('fblike_button_width') . '">' . __('Width:') . ' <input style="width: 100px;" id="' . $this->get_field_id('fblike_button_width') . '" name="' . $this->get_field_name('fblike_button_width') . '" type="text" value="' . $fblike_button_width . '" /></label></p>';
		# Fill Like Button Color Scheme Selection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('fblike_button_colorScheme') . '">' . __('Color Scheme:') . ' <select name="' . $this->get_field_name('fblike_button_colorScheme')  . '" id="' . $this->get_field_id('fblike_button_colorScheme')  . '">"';
?>
		<option value="light" <?php if ($fblike_button_colorScheme == 'light') echo 'selected="yes"'; ?> >Light</option>
		<option value="dark" <?php if ($fblike_button_colorScheme == 'dark') echo 'selected="yes"'; ?> >Dark</option>			 
<?php
		echo '</select></label>';
# Fill Like Button Show Faces Selection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('fblike_button_showFaces') . '">' . __('Show Faces:') . ' <select name="' . $this->get_field_name('fblike_button_showFaces')  . '" id="' . $this->get_field_id('fblike_button_showFaces')  . '">"';
?>
		<option value="yes" <?php if ($fblike_button_showFaces == 'yes') echo 'selected="yes"'; ?> >Yes</option>
		<option value="no" <?php if ($fblike_button_showFaces == 'no') echo 'selected="yes"'; ?> >No</option>			 
<?php
		echo '</select></label>';
		# Fill Like Button Font Selection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('fblike_button_font') . '">' . __('Font:') . ' <select name="' . $this->get_field_name('fblike_button_font')  . '" id="' . $this->get_field_id('fblike_button_font')  . '">"';
?>
		<option value="arial" <?php if ($fblike_button_font == 'arial') echo 'selected="yes"'; ?> >arial</option>
		<option value="lucida grande" <?php if ($fblike_button_font == 'lucida grande') echo 'selected="yes"'; ?>>lucida grande</option>	
		<option value="segoe ui" <?php if ($fblike_button_font == 'segoe ui') echo 'selected="yes"'; ?> >segoe ui</option>
		<option value="tahoma" <?php if ($fblike_button_font == 'tahoma') echo 'selected="yes"'; ?> >tahoma</option>	
		<option value="trebuchet ms" <?php if ($fblike_button_font == 'trebuchet ms') echo 'selected="yes"'; ?> >trebuchet ms</option>
		<option value="verdana" <?php if ($fblike_button_font == 'verdana') echo 'selected="yes"'; ?> >verdana</option>	
<?php
		echo '</select></label>';
		
		#Enable Other Social Network Settings Section
		echo '<hr/><p style="text-align:left;"><b>Google+, Twitter, Pinterest Floating Buttons Integration</b><br/>Powered By <a href="http://addthis.com" target="_blank">AddThis.com</a></p>';
		# Fill Enable Other Social Buttons Selection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('enableOtherSocialButtons') . '">' . __('Enable Other Social Buttons:') . ' <select name="' . $this->get_field_name('enableOtherSocialButtons')  . '" id="' . $this->get_field_id('enableOtherSocialButtons')  . '">"';
?>
		<option value="yes" <?php if ($enableOtherSocialButtons == 'yes') echo 'selected="yes"'; ?> >Yes</option>
		<option value="no" <?php if ($enableOtherSocialButtons == 'no') echo 'selected="yes"'; ?> >No</option>				
<?php
		echo '</select></label>';
		
		# Fill Social Buttons Style Selection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('addThisVerticalStyle') . '">' . __('Choose Vertical Floating Style:') . ' <select name="' . $this->get_field_name('addThisVerticalStyle')  . '" id="' . $this->get_field_id('addThisVerticalStyle')  . '">"';
?>
		<option value="1" <?php if ($addThisVerticalStyle == '1') echo 'selected="yes"'; ?> >Style 1 - Left</option>
		<option value="2" <?php if ($addThisVerticalStyle == '2') echo 'selected="yes"'; ?> >Style 2 - Left</option>				
		<option value="3" <?php if ($addThisVerticalStyle == '3') echo 'selected="yes"'; ?> >Style 3 - Left</option>				
		<option value="4" <?php if ($addThisVerticalStyle == '4') echo 'selected="yes"'; ?> >Style 4 - Right</option>				
		<option value="5" <?php if ($addThisVerticalStyle == '5') echo 'selected="yes"'; ?> >Style 5 - Right</option>				
		<option value="6" <?php if ($addThisVerticalStyle == '6') echo 'selected="yes"'; ?> >Style 6 - Right</option>				
<?php
		echo '</select></label>';
		
		# AddThis Publishere Id
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('addThisPubId') . '">' . __('AddThis Pub Id:') . ' <input style="width: 100px;" id="' . $this->get_field_id('addThisPubId') . '" name="' . $this->get_field_name('addThisPubId') . '" type="text" value="' . $addThisPubId . '" /></label></p>';
		
		#Enable Twitter Signatures from Twithut.com
		echo '<hr/><p style="text-align:left;"><b>Twitter Counter &amp; Twitter Signature Integration</b><br/>Powered By <a href="http://twithut.com" target="_blank">TwitHut.com</a></p>';
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('enableTwitterButtons') . '">' . __('Enable Twitter Signature & Counter:') . ' <select name="' . $this->get_field_name('enableTwitterButtons')  . '" id="' . $this->get_field_id('enableTwitterButtons')  . '">"';
?>
		<option value="no" <?php if ($enableTwitterButtons == 'no') echo 'selected="yes"'; ?> >No</option>				
		<option value="yes" <?php if ($enableTwitterButtons == 'yes') echo 'selected="yes"'; ?> >Yes</option>	
<?php
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('enableAfterOrBeforeFBLikeBox') . '">' . __('Display Before Or After Facebook Like Box:') . ' <select name="' . $this->get_field_name('enableAfterOrBeforeFBLikeBox')  . '" id="' . $this->get_field_id('enableAfterOrBeforeFBLikeBox')  . '">"';
?>
		<option value="before" <?php if ($enableAfterOrBeforeFBLikeBox == 'before') echo 'selected="yes"'; ?> >Before</option>				
		<option value="after" <?php if ($enableAfterOrBeforeFBLikeBox == 'after') echo 'selected="yes"'; ?> >After</option>	
<?php
		echo '</select></label>';		
		# Fill Twitter Username
		echo '<p style="text-align:left;"><b>To display cool Twitter Signature, Twitter Counter &amp; Twitter QR Code you have to follow below steps:</b></p>';
		echo '<p style="text-align:left;">1. Register for a free account at TwitHut.com and link your Twitter account via OAuth. For more details <a href="http://twithut.com" target="_blank">Register Here!</a></p>';
		echo '<p style="text-align:left;">2. Fill in your Twitter username below:</p>';
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('twitterUsername') . '">' . __('Twitter Username:') . ' <input style="width: 100px;" id="' . $this->get_field_id('twitterUsername') . '" name="' . $this->get_field_name('twitterUsername') . '" type="text" value="' . $twitterUsername . '" /></label></p>';
		echo '<p style="text-align:left;">3. Select Twitter Signature or Twitter Counter style below</p>';		
		# Fill Twitter Buttons Style Selection
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('twitterButtonStyle') . '">' . __('') . ' <select name="' . $this->get_field_name('twitterButtonStyle')  . '" id="' . $this->get_field_id('twitterButtonStyle')  . '">"';
?>
		<option value="127" <?php if ($twitterButtonStyle == '127') echo 'selected="yes"'; ?> >Counter 7 (150x90) - Free</option>
		<option value="40" <?php if ($twitterButtonStyle == '40') echo 'selected="yes"'; ?> >Special 1 (160x200) - Free</option>
		<option value="222" <?php if ($twitterButtonStyle == '222') echo 'selected="yes"'; ?> >QR Code Twitter URL (Free)</option>
		<option value="121" <?php if ($twitterButtonStyle == '121') echo 'selected="yes"'; ?> >Counter 1 (150x90)</option>
		<option value="122" <?php if ($twitterButtonStyle == '122') echo 'selected="yes"'; ?> >Counter 2 (150x90)</option>
		<option value="123" <?php if ($twitterButtonStyle == '123') echo 'selected="yes"'; ?> >Counter 3 (150x90)</option>
		<option value="124" <?php if ($twitterButtonStyle == '124') echo 'selected="yes"'; ?> >Counter 4 (150x90)</option>
		<option value="125" <?php if ($twitterButtonStyle == '125') echo 'selected="yes"'; ?> >Counter 5 (150x90)</option>
		<option value="126" <?php if ($twitterButtonStyle == '126') echo 'selected="yes"'; ?> >Counter 6 (150x90)</option>
		<option value="128" <?php if ($twitterButtonStyle == '128') echo 'selected="yes"'; ?> >Counter 8 (150x90)</option>
		<option value="129" <?php if ($twitterButtonStyle == '129') echo 'selected="yes"'; ?> >Counter 9 (150x90)</option>
		<option value="130" <?php if ($twitterButtonStyle == '130') echo 'selected="yes"'; ?> >Counter 10 (150x90)</option>
		<option value="131" <?php if ($twitterButtonStyle == '131') echo 'selected="yes"'; ?> >Counter 11 (150x90)</option>
		<option value="132" <?php if ($twitterButtonStyle == '132') echo 'selected="yes"'; ?> >Counter 12 (150x90)</option>
		<option value="42" <?php if ($twitterButtonStyle == '42') echo 'selected="yes"'; ?> >Special 2 (160x200)</option>
		 <option value="43" <?php if ($twitterButtonStyle == '43') echo 'selected="yes"'; ?> >Special 3 (160x200)</option>
		 <option value="44" <?php if ($twitterButtonStyle == '44') echo 'selected="yes"'; ?> >Special 4 (160x200)</option>
		 <option value="45" <?php if ($twitterButtonStyle == '45') echo 'selected="yes"'; ?> >Special 5 (160x200)</option>
		 <option value="46" <?php if ($twitterButtonStyle == '46') echo 'selected="yes"'; ?> >Special 6 (160x200)</option>
		 <option value="47" <?php if ($twitterButtonStyle == '47') echo 'selected="yes"'; ?> >Special 7 (160x200)</option>
		 <option value="48" <?php if ($twitterButtonStyle == '48') echo 'selected="yes"'; ?> >Special 8 (160x200)</option>
		 <option value="49" <?php if ($twitterButtonStyle == '49') echo 'selected="yes"'; ?> >Special 9 (160x200)</option>
		 <option value="50" <?php if ($twitterButtonStyle == '50') echo 'selected="yes"'; ?> >Special 10 (160x200)</option>
		 <option value="51" <?php if ($twitterButtonStyle == '51') echo 'selected="yes"'; ?> >Special 11 (160x200)</option>
		 <option value="52" <?php if ($twitterButtonStyle == '52') echo 'selected="yes"'; ?> >Special 12 (160x200)</option>
		 <option value="53" <?php if ($twitterButtonStyle == '53') echo 'selected="yes"'; ?> >Special 13 (160x200)</option>
		 <option value="54" <?php if ($twitterButtonStyle == '54') echo 'selected="yes"'; ?> >Special 14 (160x200)</option>
		 <option value="55" <?php if ($twitterButtonStyle == '55') echo 'selected="yes"'; ?> >Special 15 (160x200)</option>
		 <option value="56" <?php if ($twitterButtonStyle == '56') echo 'selected="yes"'; ?> >Special 16 (160x200)</option>
		 <option value="57" <?php if ($twitterButtonStyle == '57') echo 'selected="yes"'; ?> >Special 17 (160x200)</option>
		 <option value="58" <?php if ($twitterButtonStyle == '58') echo 'selected="yes"'; ?> >Special 18 (160x200)</option>
		 <option value="59" <?php if ($twitterButtonStyle == '59') echo 'selected="yes"'; ?> >Special 19 (160x200)</option>
		 <option value="60" <?php if ($twitterButtonStyle == '60') echo 'selected="yes"'; ?> >Special 20 (160x200)</option>
		 <option value="61" <?php if ($twitterButtonStyle == '61') echo 'selected="yes"'; ?> >Special 21 (160x200)</option>
		 <option value="62" <?php if ($twitterButtonStyle == '62') echo 'selected="yes"'; ?> >Special 22 (160x200)</option>
		 <option value="63" <?php if ($twitterButtonStyle == '63') echo 'selected="yes"'; ?> >Special 23 (160x200)</option>
		 <option value="64" <?php if ($twitterButtonStyle == '64') echo 'selected="yes"'; ?> >Special 24 (160x200)</option>
		 <option value="65" <?php if ($twitterButtonStyle == '65') echo 'selected="yes"'; ?> >Special 25 (160x200)</option>
		 <option value="66" <?php if ($twitterButtonStyle == '66') echo 'selected="yes"'; ?> >Special 26 (160x200)</option>
		 <option value="67" <?php if ($twitterButtonStyle == '67') echo 'selected="yes"'; ?> >Special 27 (160x200)</option>
		 <option value="68" <?php if ($twitterButtonStyle == '68') echo 'selected="yes"'; ?> >Special 28 (160x200)</option>
		 <option value="69" <?php if ($twitterButtonStyle == '69') echo 'selected="yes"'; ?> >Special 29 (160x200)</option>
		 <option value="70" <?php if ($twitterButtonStyle == '70') echo 'selected="yes"'; ?> >Special 30 (160x200)</option>
		 <option value="71" <?php if ($twitterButtonStyle == '71') echo 'selected="yes"'; ?> >Special 31 (160x200)</option>
		 <option value="72" <?php if ($twitterButtonStyle == '72') echo 'selected="yes"'; ?> >Special 32 (160x200)</option>
		 <option value="73" <?php if ($twitterButtonStyle == '73') echo 'selected="yes"'; ?> >Special 33 (160x200)</option>
		 <option value="74" <?php if ($twitterButtonStyle == '74') echo 'selected="yes"'; ?> >Special 34 (160x200)</option>
		 <option value="75" <?php if ($twitterButtonStyle == '75') echo 'selected="yes"'; ?> >Special 35 (160x200)</option>
		 <option value="76" <?php if ($twitterButtonStyle == '76') echo 'selected="yes"'; ?> >Special 36 (160x200)</option>
		 <option value="77" <?php if ($twitterButtonStyle == '77') echo 'selected="yes"'; ?> >Special 37 (160x200)</option>
		 <option value="78" <?php if ($twitterButtonStyle == '78') echo 'selected="yes"'; ?> >Special 38 (160x200)</option>
		 <option value="79" <?php if ($twitterButtonStyle == '79') echo 'selected="yes"'; ?> >Special 39 (160x200)</option>
		 <option value="80" <?php if ($twitterButtonStyle == '80') echo 'selected="yes"'; ?> >Special 40 (160x200)</option>
		 <option value="81" <?php if ($twitterButtonStyle == '81') echo 'selected="yes"'; ?> >Special 41 (160x200)</option>
		 <option value="82" <?php if ($twitterButtonStyle == '82') echo 'selected="yes"'; ?> >Special 42 (160x200)</option>
		 <option value="83" <?php if ($twitterButtonStyle == '83') echo 'selected="yes"'; ?> >Special 43 (160x200)</option>
		 <option value="84" <?php if ($twitterButtonStyle == '84') echo 'selected="yes"'; ?> >Special 44 (160x200)</option>
		 <option value="85" <?php if ($twitterButtonStyle == '85') echo 'selected="yes"'; ?> >Special 45 (160x200)</option>
		 <option value="86" <?php if ($twitterButtonStyle == '86') echo 'selected="yes"'; ?> >Special 46 (160x200)</option>
		 <option value="87" <?php if ($twitterButtonStyle == '87') echo 'selected="yes"'; ?> >Special 47 (160x200)</option>
		 <option value="88" <?php if ($twitterButtonStyle == '88') echo 'selected="yes"'; ?> >Special 48 (160x200)</option>
		 <option value="89" <?php if ($twitterButtonStyle == '89') echo 'selected="yes"'; ?> >Special 49 (160x200)</option>
		 <option value="90" <?php if ($twitterButtonStyle == '90') echo 'selected="yes"'; ?> >Special 50 (160x200)</option>
		 <option value="93" <?php if ($twitterButtonStyle == '93') echo 'selected="yes"'; ?> >Modern 1 (400x150)</option>
		 <option value="94" <?php if ($twitterButtonStyle == '94') echo 'selected="yes"'; ?> >Modern 2 (400x150)</option>
		 <option value="95" <?php if ($twitterButtonStyle == '95') echo 'selected="yes"'; ?> >Modern 3 (400x150)</option>
		 <option value="96" <?php if ($twitterButtonStyle == '96') echo 'selected="yes"'; ?> >Modern 4 (400x150)</option>
		 <option value="97" <?php if ($twitterButtonStyle == '97') echo 'selected="yes"'; ?> >Modern 5 (400x150)</option>
		 <option value="98" <?php if ($twitterButtonStyle == '98') echo 'selected="yes"'; ?> >Modern 6 (400x150)</option>
		 <option value="99" <?php if ($twitterButtonStyle == '99') echo 'selected="yes"'; ?> >Modern 7 (400x150)</option>
		 <option value="100" <?php if ($twitterButtonStyle == '100') echo 'selected="yes"'; ?> >Modern 8 (400x150)</option>
		 <option value="101" <?php if ($twitterButtonStyle == '101') echo 'selected="yes"'; ?> >Modern 9 (400x150)</option>
		 <option value="102" <?php if ($twitterButtonStyle == '102') echo 'selected="yes"'; ?> >Modern 10 (400x150)</option>
		 <option value="103" <?php if ($twitterButtonStyle == '103') echo 'selected="yes"'; ?> >Modern 11 (400x150)</option>
		 <option value="104" <?php if ($twitterButtonStyle == '104') echo 'selected="yes"'; ?> >Modern 12 (400x150)</option>
		 <option value="105" <?php if ($twitterButtonStyle == '105') echo 'selected="yes"'; ?> >Modern 13 (400x150)</option>
		 <option value="106" <?php if ($twitterButtonStyle == '106') echo 'selected="yes"'; ?> >Modern 14 (400x150)</option>
		 <option value="107" <?php if ($twitterButtonStyle == '107') echo 'selected="yes"'; ?> >Modern 15 (400x150)</option>
		 <option value="108" <?php if ($twitterButtonStyle == '108') echo 'selected="yes"'; ?> >Modern 16 (400x150)</option>
		 <option value="109" <?php if ($twitterButtonStyle == '109') echo 'selected="yes"'; ?> >Modern 17 (400x150)</option>
		 <option value="110" <?php if ($twitterButtonStyle == '110') echo 'selected="yes"'; ?> >Modern 18 (400x150)</option>
		 <option value="111" <?php if ($twitterButtonStyle == '111') echo 'selected="yes"'; ?> >Modern 19 (400x150)</option>
		 <option value="112" <?php if ($twitterButtonStyle == '112') echo 'selected="yes"'; ?> >Modern 20 (400x150)</option>
		 <option value="113" <?php if ($twitterButtonStyle == '113') echo 'selected="yes"'; ?> >Modern 21 (400x150)</option>
		 <option value="115" <?php if ($twitterButtonStyle == '114') echo 'selected="yes"'; ?> >Modern 22 (400x150)</option>
		 <option value="116" <?php if ($twitterButtonStyle == '116') echo 'selected="yes"'; ?> >Modern 23 (400x150)</option>
		 <option value="117" <?php if ($twitterButtonStyle == '117') echo 'selected="yes"'; ?> >Modern 24 (400x150)</option>
		 <option value="118" <?php if ($twitterButtonStyle == '118') echo 'selected="yes"'; ?> >Modern 25 (400x150)</option>
		 <option value="119" <?php if ($twitterButtonStyle == '119') echo 'selected="yes"'; ?> >Modern 26 (400x150)</option>
		 <option value="120" <?php if ($twitterButtonStyle == '120') echo 'selected="yes"'; ?> >Modern 27 (400x150)</option>
		 <option value="1" <?php if ($twitterButtonStyle == '1') echo 'selected="yes"'; ?> >Medium 1 (312x92)</option>
		 <option value="2" <?php if ($twitterButtonStyle == '2') echo 'selected="yes"'; ?> >Medium 2 (312x92)</option>
		 <option value="3" <?php if ($twitterButtonStyle == '3') echo 'selected="yes"'; ?> >Medium 3 (312x92)</option>
		 <option value="4" <?php if ($twitterButtonStyle == '4') echo 'selected="yes"'; ?> >Medium 4 (312x92)</option>
		 <option value="5" <?php if ($twitterButtonStyle == '5') echo 'selected="yes"'; ?> >Medium 5 (312x92)</option>
		 <option value="6" <?php if ($twitterButtonStyle == '6') echo 'selected="yes"'; ?> >Medium 6 (312x92)</option>
		 <option value="7" <?php if ($twitterButtonStyle == '7') echo 'selected="yes"'; ?> >Medium 7 (312x92)</option>
		 <option value="8" <?php if ($twitterButtonStyle == '8') echo 'selected="yes"'; ?> >Medium 8 (312x92)</option>
		 <option value="9" <?php if ($twitterButtonStyle == '9') echo 'selected="yes"'; ?> >Medium 9 (312x92)</option>
		 <option value="10" <?php if ($twitterButtonStyle == '10') echo 'selected="yes"'; ?> >Medium 10 (312x92)</option>
		 <option value="11" <?php if ($twitterButtonStyle == '11') echo 'selected="yes"'; ?> >Medium 11 (312x92)</option>
		 <option value="12" <?php if ($twitterButtonStyle == '12') echo 'selected="yes"'; ?> >Medium 12 (312x92)</option>
		 <option value="13" <?php if ($twitterButtonStyle == '13') echo 'selected="yes"'; ?> >Medium 13 (312x92)</option>
		 <option value="14" <?php if ($twitterButtonStyle == '14') echo 'selected="yes"'; ?> >Medium 14 (312x92)</option>
		 <option value="15" <?php if ($twitterButtonStyle == '15') echo 'selected="yes"'; ?> >Medium 15 (312x92)</option>
		 <option value="16" <?php if ($twitterButtonStyle == '16') echo 'selected="yes"'; ?> >Medium 16 (312x92)</option>
		 <option value="17" <?php if ($twitterButtonStyle == '17') echo 'selected="yes"'; ?> >Medium 17 (312x92)</option>
		 <option value="18" <?php if ($twitterButtonStyle == '18') echo 'selected="yes"'; ?> >Medium 18 (312x92)</option>
		 <option value="19" <?php if ($twitterButtonStyle == '19') echo 'selected="yes"'; ?> >Medium 19 (312x92)</option>
		 <option value="20" <?php if ($twitterButtonStyle == '20') echo 'selected="yes"'; ?> >Medium 20 (312x92)</option>
		 <option value="21" <?php if ($twitterButtonStyle == '21') echo 'selected="yes"'; ?> >Medium 21 (312x92)</option>
		 <option value="22" <?php if ($twitterButtonStyle == '22') echo 'selected="yes"'; ?> >Medium 22 (312x92)</option>
		 <option value="23" <?php if ($twitterButtonStyle == '23') echo 'selected="yes"'; ?> >Medium 23 (312x92)</option>
		 <option value="24" <?php if ($twitterButtonStyle == '24') echo 'selected="yes"'; ?> >Medium 24 (312x92)</option>
		 <option value="25" <?php if ($twitterButtonStyle == '25') echo 'selected="yes"'; ?> >Medium 25 (312x92)</option>
		<option value="26" <?php if ($twitterButtonStyle == '26') echo 'selected="yes"'; ?> >Medium 26 (312x92)</option>
		 <option value="27" <?php if ($twitterButtonStyle == '27') echo 'selected="yes"'; ?> >Medium 27 (312x92)</option>
		 <option value="28" <?php if ($twitterButtonStyle == '28') echo 'selected="yes"'; ?> >Medium 28 (312x92)</option>
		 <option value="29" <?php if ($twitterButtonStyle == '29') echo 'selected="yes"'; ?> >Medium 29 (312x92)</option>
		 <option value="30" <?php if ($twitterButtonStyle == '30') echo 'selected="yes"'; ?> >Medium 30 (312x92)</option>		 
		 <option value="223" <?php if ($twitterButtonStyle == '223') echo 'selected="yes"'; ?> >Premium Badge 1(131x182)</option>
		 <option value="224" <?php if ($twitterButtonStyle == '224') echo 'selected="yes"'; ?> >Premium Badge 2(131x182)</option>
		 <option value="225" <?php if ($twitterButtonStyle == '225') echo 'selected="yes"'; ?> >Premium Badge 3(131x182)</option>
		 <option value="226" <?php if ($twitterButtonStyle == '226') echo 'selected="yes"'; ?> >Premium Badge 4(131x182)</option>
		 <option value="227" <?php if ($twitterButtonStyle == '227') echo 'selected="yes"'; ?> >Premium Badge 5(131x182)</option>
		 <option value="228" <?php if ($twitterButtonStyle == '228') echo 'selected="yes"'; ?> >Premium Badge 6(131x182)</option>
		 <option value="229" <?php if ($twitterButtonStyle == '229') echo 'selected="yes"'; ?> >Premium Badge 7(131x182)</option>
		 <option value="230" <?php if ($twitterButtonStyle == '230') echo 'selected="yes"'; ?> >Premium Badge 8(131x182)</option>
		 <option value="231" <?php if ($twitterButtonStyle == '231') echo 'selected="yes"'; ?> >Premium Badge 9(131x182)</option>
		 <option value="232" <?php if ($twitterButtonStyle == '232') echo 'selected="yes"'; ?> >Premium Badge 10(131x182)</option>
		<option value="220" <?php if ($twitterButtonStyle == '220') echo 'selected="yes"'; ?> >Premium (iPhone Model)</option>
		<option value="211" <?php if ($twitterButtonStyle == '211') echo 'selected="yes"'; ?> >Premium (iPOD Model 1)</option>
		<option value="212" <?php if ($twitterButtonStyle == '212') echo 'selected="yes"'; ?> >Premium (iPOD Model 2)</option>
		<option value="213" <?php if ($twitterButtonStyle == '213') echo 'selected="yes"'; ?> >Premium (iPOD Model 3)</option>
		<option value="214" <?php if ($twitterButtonStyle == '214') echo 'selected="yes"'; ?> >Premium (iPOD Model 4)</option>
		<option value="215" <?php if ($twitterButtonStyle == '215') echo 'selected="yes"'; ?> >Premium (iPOD Model 5)</option>
		<option value="216" <?php if ($twitterButtonStyle == '216') echo 'selected="yes"'; ?> >Premium (iPOD Model 6)</option>
		<option value="217" <?php if ($twitterButtonStyle == '217') echo 'selected="yes"'; ?> >Premium (iPOD Model 7)</option>
		<option value="218" <?php if ($twitterButtonStyle == '218') echo 'selected="yes"'; ?> >Premium (iPOD Model 8)</option>
		<option value="219" <?php if ($twitterButtonStyle == '219') echo 'selected="yes"'; ?> >Premium (iPOD Model 9)</option>		
		<option value="233" <?php if ($twitterButtonStyle == '233') echo 'selected="yes"'; ?> >Premium Bubble Up 1(248x220)</option>
		<option value="234" <?php if ($twitterButtonStyle == '234') echo 'selected="yes"'; ?> >Premium Bubble Up 2(248x220)</option>
		<option value="235" <?php if ($twitterButtonStyle == '235') echo 'selected="yes"'; ?> >Premium Bubble Up 3(248x220)</option>
		<option value="236" <?php if ($twitterButtonStyle == '236') echo 'selected="yes"'; ?> >Premium Bubble Up 4(248x220)</option>		 
		<option value="164" <?php if ($twitterButtonStyle == '164') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="165" <?php if ($twitterButtonStyle == '165') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="166" <?php if ($twitterButtonStyle == '166') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="167" <?php if ($twitterButtonStyle == '167') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="168" <?php if ($twitterButtonStyle == '168') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="169" <?php if ($twitterButtonStyle == '169') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="170" <?php if ($twitterButtonStyle == '170') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="171" <?php if ($twitterButtonStyle == '171') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="172" <?php if ($twitterButtonStyle == '172') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="173" <?php if ($twitterButtonStyle == '173') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="174" <?php if ($twitterButtonStyle == '174') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="176" <?php if ($twitterButtonStyle == '176') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="177" <?php if ($twitterButtonStyle == '177') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="178" <?php if ($twitterButtonStyle == '178') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="179" <?php if ($twitterButtonStyle == '179') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="180" <?php if ($twitterButtonStyle == '180') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="181" <?php if ($twitterButtonStyle == '181') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="182" <?php if ($twitterButtonStyle == '182') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="183" <?php if ($twitterButtonStyle == '183') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="184" <?php if ($twitterButtonStyle == '184') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="185" <?php if ($twitterButtonStyle == '185') echo 'selected="yes"'; ?> >Premium (172x326)</option>
		<option value="186" <?php if ($twitterButtonStyle == '186') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="187" <?php if ($twitterButtonStyle == '187') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="188" <?php if ($twitterButtonStyle == '188') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="189" <?php if ($twitterButtonStyle == '189') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="190" <?php if ($twitterButtonStyle == '190') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="191" <?php if ($twitterButtonStyle == '191') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="192" <?php if ($twitterButtonStyle == '192') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="193" <?php if ($twitterButtonStyle == '193') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="194" <?php if ($twitterButtonStyle == '194') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="195" <?php if ($twitterButtonStyle == '195') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="196" <?php if ($twitterButtonStyle == '196') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="197" <?php if ($twitterButtonStyle == '197') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="198" <?php if ($twitterButtonStyle == '198') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="199" <?php if ($twitterButtonStyle == '199') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="200" <?php if ($twitterButtonStyle == '200') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="201" <?php if ($twitterButtonStyle == '201') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="202" <?php if ($twitterButtonStyle == '202') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="203" <?php if ($twitterButtonStyle == '203') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="204" <?php if ($twitterButtonStyle == '204') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="205" <?php if ($twitterButtonStyle == '205') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="206" <?php if ($twitterButtonStyle == '206') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="207" <?php if ($twitterButtonStyle == '207') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="208" <?php if ($twitterButtonStyle == '208') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="209" <?php if ($twitterButtonStyle == '209') echo 'selected="yes"'; ?> >Premium (320x252)</option>
		<option value="210" <?php if ($twitterButtonStyle == '210') echo 'selected="yes"'; ?> >Premium (320x252)</option>		
<?php
		echo '</select></label>';
		
		# Twitter Username
		echo '<p style="text-align:right;"></p>';
		
		#Follow our News
		echo '<hr/><p style="text-align:left;"><b>Follow Us or Like US:</b></p>';
		echo '<p style="text-align:left;"><a title="Join Us @Facebook" href="http://www.facebook.com/pages/VivoCiticom-Joomla-Wordpress-Blogger-Drupal-DNN-Community/119691288064264" target="_blank"><img src="http://vivociti.com/images/stories/facebook_16x16.png" border="0"></a>&nbsp;<a href="https://plus.google.com/100723813888588053339?prsrc=3" style="text-decoration:none;"><img src="https://ssl.gstatic.com/images/icons/gplus-16.png" alt="" style="border:0;width:16px;height:16px;"/></a>&nbsp;<a title="Follow Us @Twitter" href="http://twitter.com/vivociti" target="_blank"><img src="http://vivociti.com/images/stories/twitter_16x16.png" border="0"></a>&nbsp;<a title="Follow Us @Digg" href="http://digg.com/vivoc" target="_blank"><img src="http://vivociti.com/images/stories/digg_16x16.png" border="0"></a>&nbsp;<a title="Follow Us @StumbleUpon" href="http://www.stumbleupon.com/stumbler/vivociti/" target="_blank"><img src="http://vivociti.com/images/stories/stumbleupon_16x16.png" border="0"></a>&nbsp;<a title="Follow Our RSS" href="http://feeds2.feedburner.com/vivociti" target="_blank"><img src="http://vivociti.com/images/stories/feed_16x16.png" border="0"></a></p>';
		echo '<p/>';
		echo '<hr/>';
		# Fill Author Credit : option to select YEs or No 
		echo '<p style="text-align:right;">You can optionally support my development work contributing via <a href="http://bit.ly/9Njzpo" target="_blank">PayPal</a>';
?>
<?php
		echo '</p>';
		echo '<p style="text-align:left;">Our other Wordpress Widget you may like is:<br/>
		<ul>
		  <li><a title="Google +1 Button" href="http://wordpress.org/extend/plugins/google-1-recommend-button-for-wordpress/" target="_blank">Google +1 Button</a></li>
		  <li><a title="Twitter QR Code for Wordpress" href="http://wordpress.org/extend/plugins/twitter-qr-code-signatures/" target="_blank">Twitter QR Code Widget</a></li>
		  <li><a title="Twitter Signature for Wordpress" href="http://wordpress.org/extend/plugins/twitter-signature/" target="_blank">Twitter Signature for Wordpress</a></li>
		</ul></p>';
		
	
	} //end of form

}// END class
	
	/**
	* Register  widget.
	*
	* Calls 'widgets_init' action after widget has been registered.
	*/
	function FacebookLikeBoxInit() {
	register_widget('FacebookLikeBoxWidget');
	}	
	add_action('widgets_init', 'FacebookLikeBoxInit');
?>