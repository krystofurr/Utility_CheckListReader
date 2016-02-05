
<?php
require_once('../config.php');
require_once('../handler/CheckListManager.php');
$CM = new CheckListManager();
$CM->initializeJSONData('../data/'.JSON_FILE);

echo "<pre>", print_r($CM->jsonArray, true), "</pre>";

 ?>
