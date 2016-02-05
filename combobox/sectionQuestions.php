
<?php
require_once('../config.php');
require_once('../CheckListManager.php');
$CM = new CheckListManager();
$CM->initializeJSONData('../'.JSON_FILE);

if(isset($_POST['sectionType'])) {
    echo $CM->displaySectionQuestionsComboBox($_POST['sectionType'], $_POST['functionType']);
}

?>
