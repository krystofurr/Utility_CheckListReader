
<?php

require_once('../CheckListManager.php');
$CM = new CheckListManager();
$CM->initializeJSONData('../Checklists.json');

if(isset($_POST['sectionType'])) {
    echo $CM->displaySectionQuestionsComboBox($_POST['sectionType'], $_POST['functionType']);
}

?>
