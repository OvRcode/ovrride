<?php
//Gets CSV data from POST and returns file download
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=".$_POST['csvName']);
header("Pragma: no-cache");
header("Expires: 0");
echo $_POST['csvData'];
?>