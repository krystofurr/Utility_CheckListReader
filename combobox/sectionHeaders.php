
<?php
require_once('../config.php');
require_once('../handler/CheckListManager.php');
$CM = new CheckListManager();
$CM->initializeJSONData('../data/'.JSON_FILE);

if(isset($_POST['listType'])) {
    echo $CM->displaySectionHeaderComboBox($_POST['listType']);
}

?>
