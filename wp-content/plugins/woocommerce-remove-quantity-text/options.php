<div class="wrap">
	
    <?php screen_icon(); ?>
    
	<form action="options.php" method="post" id="<?php echo $plugin_id; ?>_options_form" name="<?php echo $plugin_id; ?>_options_form">
    
	<?php settings_fields($plugin_id.'_options'); ?>
    	  
    <h2>Woocommerce Remove Quantity Text &raquo; Settings</h2>
    <table class="widefat">
		<thead>
		   <tr>
			 <th><input type="submit" name="submit" value="Save Settings" class="button-primary"  /></th>
		   </tr>
		</thead>
		<tfoot>
		   <tr>
			 <th><input type="submit" name="submit" value="Save Settings" class="button-primary"  /></th>
		   </tr>
		</tfoot>
		<tbody>
			<tr>
				<td style="padding:25px;font-family:Verdana, Geneva, sans-serif;color:#666;">
					Set Which Product type should not have Quantity.
				</td>
			</tr>
		   <tr>
			 <td style="padding:25px;font-family:Verdana, Geneva, sans-serif;color:#666;">
                 <label for="kkpo_quote">
                     	<p>  Variable Product :
			<select name="wooremoveqtytext_variable">
			 <option value="no" <?php if(get_option('wooremoveqtytext_variable') == 'no'){ echo "selected"; } ?> >No</option>	
			  <option value="yes" <?php if(get_option('wooremoveqtytext_variable') == 'yes'){ echo "selected"; } ?> >Yes</option>
			  
			</select></p>
			<p>  Grouped Product :
			<select name="wooremoveqtytext_grouped">
			 <option value="no" <?php if(get_option('wooremoveqtytext_grouped') == 'no'){ echo "selected"; } ?> >No</option>	
			 <option value="yes" <?php if(get_option('wooremoveqtytext_grouped') == 'yes'){ echo "selected"; } ?> >Yes</option>
			  
			</select></p>
			<p>  External Product :
			<select name="wooremoveqtytext_external">
			  <option value="no" <?php if(get_option('wooremoveqtytext_external') == 'no'){ echo "selected"; } ?> >No</option>
			  <option value="yes" <?php if(get_option('wooremoveqtytext_external') == 'yes'){ echo "selected"; } ?> >Yes</option>
	
			</select></p>
			<p>  Simple Product :
			<select name="wooremoveqtytext_default">
			  <option value="no" <?php if(get_option('wooremoveqtytext_default') == 'no'){ echo "selected"; } ?> >No</option>	
			  <option value="yes" <?php if(get_option('wooremoveqtytext_default') == 'yes'){ echo "selected"; } ?> >Yes</option>

			</select></p>

                 </label>
             </td>
		   </tr>
		</tbody>
	</table>
    
	</form>
    

</div>