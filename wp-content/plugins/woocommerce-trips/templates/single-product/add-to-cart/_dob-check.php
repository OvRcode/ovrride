
<div class="dobCheck">
  <input type="hidden" value="<?php echo $age_limit; ?>" id="wc_trip_age_limit"></input>
  <label for="wc_trip_age_check"><strong>Is this guest at least <?php echo $age_limit; ?> years of age?</strong><span class="required"> *</span></label>
  <br />
  <input type="radio" id="wc_trip_age_check" name="wc_trip_age_check" value="yes" data-required="true"> Yes
  <br />
  <input type="radio" id="wc_trip_age_check" name="wc_trip_age_check" value="no" data-required="true"> No
  <br />
<?php if ( "18" == $age_limit) : ?>
  <p>Guests under 18 years of age are welcome to join us as long as they abide by the terms of the <a href="http://ovrride.com/ovrride-age-policy/" target="_blank"><strong>OvRride Age Policy</strong></a>, and we are aware of the underage guest.</p>
<?php elseif ( "21" == $age_limit ): ?>
    <p>Guests under 21 years of age are welcome to join us as long as they abide by the terms of the <a href="http://ovrride.com/ovrride-age-policy/" target="_blank">OvRride Age Policy</a>, and we are aware of the underage guest.</p>
  <?php elseif ( "21+" == $age_limit ): ?>
    <p>For this trip guests need to be at least 21 years of age, we are unable to accomodate guests under 21 years of age.</p>
  <?php endif;?>
  <label class="dobLabel"><strong>Date of Birth</strong><span class="required">*</span></label>
  <div class="dob clearfix">
    <div class="monthGroup">
      <input type="text" maxlength="2" name="wc_trip_dob_month" id="wc_trip_dob_month" data-required="true" />
      <label for="wc_trip_dob_month">MM</label>
    </div>
    <div class="dayGroup">
      <input type="text" maxlength="2" name="wc_trip_dob_day" id="wc_trip_dob_day" data-required="true"/>
      <label for="wc_trip_dob_day">DD</label>
    </div>
    <div class="yearGroup">
      <input type="text" maxlength="4" name="wc_trip_dob_year" id="wc_trip_dob_year" data-required="true"/>
      <label for="wc_trip_dob_year">YYYY</label>
      <input type="hidden" id="wc_trip_dob_field" name="wc_trip_dob_field" value="" />
    </div>
    <br /><br />
    <div class="dobError" style="float:left; clear: left;">
      <p class="month"></p>
      <p class="day"></p>
      <p class="year"></p>
    </div>
    <div class="dobComment">
      <br />
      <p>
        Please fill out accurately, as this information will be used to secure flight and/or other international reservations.
      </p>
    </div>
  </div>

</div>
