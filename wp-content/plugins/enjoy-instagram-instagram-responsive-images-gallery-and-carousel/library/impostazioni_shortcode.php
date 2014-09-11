<form method="post" action="options.php" novalidate>
<?php settings_fields('enjoyinstagram_options_carousel_group'); ?>
<?php echo realpath(home_url()); ?>
 
 

<script type="text/javascript">
jQuery(document).ready(function($){
    $("input[name$='enjoyinstagram_user_or_hashtag']").click(function() {
        var test = $(this).val();
		if(test=='user'){
		$('#enjoyinstagram_hashtag').attr('disabled',true);
		}else if(test=='hashtag'){
		$('#enjoyinstagram_hashtag').attr('disabled',false);
		}
        $("div.desc").hide();
        $("#enjoyinstagram_user_or_hashtag_" + test).show();
    });});
</script>
 
            <table class="form-table">
                <tbody>
    
                    <tr valign="top">
                    <th scope="row" style="align:left;">
                        <label for="enjoyinstagram_user_or_hashtag" class="enfasi">Inclusion mode:</label>
                    </th>
                    <td><div class="ei_block">
                    	<div class="ei_settings_float_block">
                    			Show pics: 
						</div>
						<div class="ei_settings_float_block"> 
							<input type="radio" name="enjoyinstagram_user_or_hashtag" <?php if (get_option('enjoyinstagram_user_or_hashtag')=='user') echo "checked";?> value="user">of Your Profile<br/><br/>
							<input type="radio"  name="enjoyinstagram_user_or_hashtag" <?php if (get_option('enjoyinstagram_user_or_hashtag')=='hashtag') echo "checked";?> value="hashtag">by Hashtag<br />
                        </div>
                        <div class="ei_settings_float_block"> 
 
						<div id="enjoyinstagram_user_or_hashtag_user" class="desc" <?php if (get_option('enjoyinstagram_user_or_hashtag')!='user') echo 'style="display:none;"';?> >
						 &nbsp;<input type="text" class="ei_disabled" id="enjoyinstagram_user" disabled value="<?php echo get_option('enjoyinstagram_user_username'); ?>" name="enjoyinstagram_user" />
						</div>

						<div id="enjoyinstagram_user_or_hashtag_hashtag" class="desc" <?php if (get_option('enjoyinstagram_user_or_hashtag')!='hashtag') echo 'style="display:none;"';?>>
                        #<input type="text" id="enjoyinstagram_hashtag" required value="<?php echo get_option('enjoyinstagram_hashtag'); ?>" name="enjoyinstagram_hashtag" />
 						<span class="description">insert a hashtag without '#'</span>
                        
 						</div>      
                         </div> </div>                      
					</td>
                        
                  </tr>
                </tbody>
            </table>
           
            <hr />
  
            <table class="form-table">
                <tbody>
                     <tr valign="top">
                    <th scope="row" style="align:left;">
                    	<label for="enjoyinstagram_carousel_items_numbe" class="enfasi">Carousel settings:</label>
                    </th>
                     
                        <td><div class="ei_block">
                                <div class="ei_settings_float_block ei_fixed">
                                    Images displayed at a time:
                                </div>
                                <div class="ei_settings_float_block">
                                    <select name="enjoyinstagram_carousel_items_number" class="ei_sel" id="enjoyinstagram_carousel_items_number">
									<?php for ($i = 1; $i <= 10; $i++) { ?>
                                        <option value="<?php echo $i?>" <?php if (get_option('enjoyinstagram_carousel_items_number')==$i) 
                                        echo "selected='selected'";?>>
                                        <?php echo "&nbsp;".$i;	 ?>			
                                        </option>
                                    
                                    <?php } ?>
                                    </select>
                                </div>
                            </div>
                         	<div class="ei_block">
                                <div class="ei_settings_float_block ei_fixed">
                                	Navigation buttons:
                                </div>
                                <div class="ei_settings_float_block">
                                <select name="enjoyinstagram_carousel_navigation" class="ei_sel" id="enjoyinstagram_carousel_navigation">
                                    <option value="true" <?php if (get_option('enjoyinstagram_carousel_navigation')=='true') echo "selected='selected'";?>>Yes
                                    </option>
                                    <option value="false" <?php if (get_option('enjoyinstagram_carousel_navigation')=='false') echo "selected='selected'";?>>No
                                    </option>
                                </select>
                           		</div>
                           </div>     
						</td>
                    </tr>
                </tbody>
            </table>
         
            <hr />
         
            <!-- SHORTCODE WALL GRID -->
            
       <table class="form-table">
                <tbody>
                     <tr valign="top">
                    <th scope="row" style="align:left;">
                    	<label for="enjoyinstagram_carousel_grid" class="enfasi">Grid view settings:</label>
                    </th>
                     
                        <td><div class="ei_block">
                                <div class="ei_settings_float_block ei_fixed">
                                    Number of Columns:
                                </div>
                                <div class="ei_settings_float_block">
                                <select name="enjoyinstagram_grid_cols" id="enjoyinstagram_grid_cols" class="ei_sel">

                                    
									<?php for ($i = 1; $i <= 10; $i++) { ?>
                                        <option value="<?php echo $i?>" <?php if (get_option('enjoyinstagram_grid_cols')==$i) 
                                        echo "selected='selected'";?>>
                                        <?php echo "&nbsp;".$i;	 ?>			
                                        </option>
                                    
                                    <?php } ?>
                                    </select>
                                </div>
                            </div>
                        
                        	<div class="ei_block">
                                <div class="ei_settings_float_block ei_fixed">
                                    Number of Rows:
                                </div>
                                <div class="ei_settings_float_block">
                                <select name="enjoyinstagram_grid_rows" id="enjoyinstagram_grid_rows" class="ei_sel">

                                    
									<?php for ($i = 1; $i <= 10; $i++) { ?>
                                        <option value="<?php echo $i?>" <?php if (get_option('enjoyinstagram_grid_rows')==$i) 
                                        echo "selected='selected'";?>>
                                        <?php echo "&nbsp;".$i;	 ?>			
                                        </option>
                                    
                                    <?php } ?>
                                 </select>
                                </div>
                            </div>
                        
                         
                        </td>
                    </tr>
                     
                    
        
        
          </tbody>
            </table>
            <hr/>  
                       <p><strong>Free version</strong>: Only 20 images allowed.</p>
                    <input type="submit" class="button-primary" id="button_enjoyinstagram_advanced" name="button_enjoyinstagram_advanced" value="Save Settings"/>
              </form>