<?php
/**
 * @author https://smashr.org
 * Plugin: Facebook Page Plugin
 */
?>
<div class="postbox">
	<div class="crunchlr">
		<h3>Option panel for Widget/Sidebar Area</h3>

		<div>
			<table class="form-table">

				<tr valign="top">
					<th scope="row" style="width: 29%;"><label>Widget Title</label></th>
					<td><textarea id="styled" name="smashify_facebook_page_plugin_widget_title"
							cols="38" rows="1"><?php echo get_option('smashify_facebook_page_plugin_widget_title'); ?></textarea></td>
				</tr>
				<tr valign="top" class="alternate">
					<th scope="row" style="width: 29%;"><label>Facebook Page Name</label></th>
					<td><textarea id="styled" name="smashify_facebook_page_plugin_widget_data_href"
							cols="38" rows="1"><?php echo get_option('smashify_facebook_page_plugin_widget_data_href'); ?></textarea>
                    &nbsp;<?=$fb_pagename1?>
                    <br> <a
						href="http://www.facebook.com/pages/create.php" target="_blank">Create
							Facebook Page</a></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label>Hide Cover?</label></th>
					<td><input name="smashify_facebook_page_plugin_widget_data_hide_cover" type="radio"
						value="true"
						<?php checked('true', $smashify_facebook_page_plugin_widget_data_hide_cover); ?> />
						&nbsp;YES <input name="smashify_facebook_page_plugin_widget_data_hide_cover" type="radio"
						value="false"
						<?php checked('false', $smashify_facebook_page_plugin_widget_data_hide_cover); ?> />
                    &nbsp;NO (default)
                    &nbsp;<?=$fb_showstream1?>
                </td>
				</tr>
				<tr valign="top" class="alternate">
					<th scope="row"><label>Show Facespile?</label></th>
					<td><input name="smashify_facebook_page_plugin_widget_data_show_facepile" type="radio"
						value="true"
						<?php checked('true', $smashify_facebook_page_plugin_widget_data_show_facepile); ?> />
						&nbsp;YES (default) <input name="smashify_facebook_page_plugin_widget_data_show_facepile"
						type="radio" value="false"
						<?php checked('false', $smashify_facebook_page_plugin_widget_data_show_facepile); ?> />
						&nbsp;NO</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label>Show Posts?</label></th>

					<td><input name="smashify_facebook_page_plugin_widget_data_show_posts" type="radio"
						value="true"
						<?php checked('true', $smashify_facebook_page_plugin_widget_data_show_posts); ?> />
						&nbsp;YES <input name="smashify_facebook_page_plugin_widget_data_show_posts"
						type="radio" value="false"
						<?php checked('false', $smashify_facebook_page_plugin_widget_data_show_posts); ?> />
						&nbsp;NO (default)</td>
				</tr>
				<tr valign="top" class="alternate">
					<th scope="row" style="width: 29%;"><label>If you like, help
							promote a plugin</label></th>
					<td><input name="smashify_fbmembers_show_sponser_link" type="checkbox"
						<?php if (get_option('smashify_fbmembers_show_sponser_link') != '-1') echo 'checked="checked"'; ?>
						value="1" /> <code>Check</code> to hide promotion link after
						widget</td>
				</tr>

			</table>
		</div>
	</div>
</div>

<div class="submit">

	<input name="my_fmz_update_setting" type="hidden"
		value="<?php echo wp_create_nonce('fmz-update-setting'); ?>" /> <input
		type="submit" name="info_update" class="button-primary"
		value="<?php _e('Update options'); ?> &raquo;" />

</div>
</form>

