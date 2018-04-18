<?php
function generatePostFix($index) {
  //take index integer, add 1 and firgure out correct post fix
  $number = $index + 1;
  switch( intVal( substr($number,-1,1) ) ) {
    case 1:
      $postFix = "st";
      break;
    case 2:
      $postFix = "nd";
      break;
    case 3:
      $postFix = "rd";
      break;
    default:
      $postFix = "th";
  }
  return $number .= $postFix;
}
 ?>
<label>Type:</label><?php echo $type_drop_down; ?><hr />
<h4>Contact Information</h4>
<label>Contact: </label><input type="text" size="36" name="_contact" value="<?php echo $contact; ?>" />
<br />
<label>Contact Phone: </label><input type="text" size="20" name="_contact_phone" value="<?php echo $contactPhone; ?>" />
<br />
<label>Rep: </label><input type="text" size="36" name="_rep" value="<?php echo $rep; ?>" />
<br />
<label>Rep Phone: </label><input type="text" size="20" name="_rep_phone" value="<?php echo $repPhone; ?>" />
<br />
<hr />
<h4>Trail Map(s)</h4>
<label>Trail Map</label><input id="upload_trail_map" type="text" size="36" name="upload_trail_map" value="<?php echo $map; ?>" />
<input id="upload_trail_map_button" type="button" value="Select Trail Map" />
<br />
<label>Trail Map Two</label><input id="upload_trail_map_2" type="text" size="36" name="upload_trail_map_2" value="<?php echo $map_two; ?>" />
<input id="upload_trail_map_2_button" type="button" value="Select Trail Map Two" />
<br />
<label>Trail Map Three</label><input id="upload_trail_map_3" type="text" size="36" name="upload_trail_map_3" value="<?php echo $map_three; ?>" />
<input id="upload_trail_map_3_button" type="button" value="Select Trail Map Three" />
<br />
<label>Trail Map Four</label><input id="upload_trail_map_4" type="text" size="36" name="upload_trail_map_4" value="<?php echo $map_four; ?>" />
<input id="upload_trail_map_4_button" type="button" value="Select Trail Map Four" />
<br />
<hr />
<h4>Lesson Package Age Restriction</h4>
<label>Age ( 0 for no restriction ) : </label><input type="number" name="_lesson_age" min="0" max="100" value="<?php echo $lessonAge; ?>"/>
<br />
<hr />
<h4>Automated Report Settings</h4>
<label>Report Enabled: </label>
<input type="radio" name="_report_active" value="active" <?php echo $reportActive; ?> > Yes</input>
<input type="radio" name="_report_active" value="inactive" <?php echo $reportInActive; ?> > No</input><br />
<?php foreach($reportSettings['email'] as $emailIndex => $reportEmail): ?>
  <?php $emailNumber = generatePostFix($emailIndex); ?>
  <div class="emailContainer">
    <label>Report <?php echo $emailNumber; ?> Email: </label><input type="text" size="36" name="_report_email" value="<?php echo $reportEmail; ?>" /><i class="fa fa-2x fa-times emailDelete" ></i>
    <br />
  </div>
<?php endforeach; ?>
<div class="reportSettings">
<?php foreach( $reportSettings['reports'] as $index => $array ): ?>
  <?php $number = generatePostFix($index); ?>
<div class="reportSetting">
  <i class="fa fa-2x fa-times reportDelete" ></i><br />
  <label><?php echo $number; ?> Report Days before trip (0-7): </label><input type="number" name="_report_day[]" min="0" max="7" value="<?php echo $array['day']; ?>">
  <br/>
  <label><?php echo $number; ?> Report Time to send report (24hr EST): </label><input type="number" name="_report_hour[]" min="0" max="24" value="<?php echo $array['hour']; ?>">:<input type="number" name="_report_minute[]" min="0" max="59" value="<?php echo $array['minute']; ?>" >
  <br/>
</div>
<?php endforeach; ?>
</div>
<button id="addReport">Add new report</button>
