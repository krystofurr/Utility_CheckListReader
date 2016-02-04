
<?php

require_once('../CheckListManager.php');
$CM = new CheckListManager();
$CM->initializeJSONData('../Checklists.json');

if(isset($_POST['listType'])) {
    echo $CM->displaySectionHeaderComboBox($_POST['listType']);
}

?>
