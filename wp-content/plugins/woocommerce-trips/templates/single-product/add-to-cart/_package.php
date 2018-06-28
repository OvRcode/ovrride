<?php
if ( isset( $info['optional'] ) && "yes" == $info['optional'] ) {
  $required = "";
} else {
  $required = "<span class='required'>*</span>";
}
echo <<<PACKAGE
<br />
    <div class='packages'>
        <label for="wc_trip_{$type}_package" ><strong>{$info['label']}</strong> {$required}</label>
        <select name="wc_trip_{$type}_package" id="wc_trip_{$type}_package" data-required="true">
        <option value="">Select option</option>
PACKAGE;
echo $info['html'];
echo "</select></div>";
echo "<input type='hidden' name='wc_trip_{$type}_package_label' value='{$info['label']}' />";
