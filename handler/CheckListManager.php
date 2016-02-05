<?php
/*

.o88b. db   db d88888b  .o88b. db   dD db      d888888b .d8888. d888888b      .88b  d88.  .d8b.  d8b   db  .d8b.   d888b  d88888b d8888b.
d8P  Y8 88   88 88'     d8P  Y8 88 ,8P' 88        `88'   88'  YP `~~88~~'      88'YbdP`88 d8' `8b 888o  88 d8' `8b 88' Y8b 88'     88  `8D
8P      88ooo88 88ooooo 8P      88,8P   88         88    `8bo.      88         88  88  88 88ooo88 88V8o 88 88ooo88 88      88ooooo 88oobY'
8b      88~~~88 88~~~~~ 8b      88`8b   88         88      `Y8b.    88         88  88  88 88~~~88 88 V8o88 88~~~88 88  ooo 88~~~~~ 88`8b
Y8b  d8 88   88 88.     Y8b  d8 88 `88. 88booo.   .88.   db   8D    88         88  88  88 88   88 88  V888 88   88 88. ~8~ 88.     88 `88.
`Y88P' YP   YP Y88888P  `Y88P' YP   YD Y88888P Y888888P `8888Y'    YP         YP  YP  YP YP   YP VP   V8P YP   YP  Y888P  Y88888P 88   YD

Author: Christopher Sigouin
Date: Jan 23rd, 2016

http://stackoverflow.com/questions/2904131/what-is-the-difference-between-json-and-object-literal-notation

*/

require '/var/www/html/checkmate/vendor/autoload.php';

class CheckListManager {

    private $listTypes;                 # An array that holds the type of lists in the JSON file
    private $listType;                  # The current list type
    private $rootTypes;                 # The current root level list from the JSON file
    private $jsonFile;                  # Holds the filename of the JSON file
    private $jsonFileOutput;            # Filename that will hold the saved output
    private $jsonArray;                 # Holds the JSON data as an associative array
    private $sectionHeader;             # Holds the current section name ' String '
    private $additionalQuestions;       # Boolean value
    private $debug;                     # Used for debugging the script
    private $document;

    public function __construct()
    {
        require_once('/var/www/html/checkmate/config.php');
        $this->jsonFileOutput = 'data/'.JSON_FILE_OUTPUT;
        $this->debug = false;
        $this->rootTypes = array('l0' => 'BE', 'l1' => 'SD', 'l2' => 'ID', 'l3' => 'TH',
                           'l4' => 'FI', 'l5' => 'SA', 'l6' => 'AU');
        $this->listTypes = array('BREAK_AND_ENTER' => 'BE', 'SUDDEN_DEATH' => 'SD', 'IMPAIRED_DRIVING' => 'ID', 'THEFT' => 'TH',
                           'FIRE' => 'FI', 'SEXUAL_ASSAULT' => 'SA', 'ASSAULT' => 'AU');

        $this->listType = $this->listTypes['BREAK_AND_ENTER']; // DEFAULT
        $this->document = new JohnStevenson\JsonWorks\Document();

    }

    // GETTERS AND SETTERS
    function __get($name){
        return $this->$name;
    }
    function __set($name,$value) {
        $this->$name = $value;
    }

    /**
     * @param mixed $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }


    public function initializeJSONData($jsonFile) {
        // Grab the JSON data
        $this->jsonData = file_get_contents($jsonFile);
        // Set to 'true' to return an associative array
        $this->jsonArray = json_decode($this->jsonData, true);

        // $this->jsonData = file_get_contents('test.json');
        $this->document->loadData($this->jsonData);

    }

    public function displayQuestions($listType) {

        foreach( $this->jsonArray as $key => $value) {
          // Check for the type of list that you want ( Defined above as constants )
          if($this->jsonArray[$key]["id"] == $listType ) {

              // Check to see if the value is an array
              if( gettype($value) == 'array'){
                  // Loop through this array

                  foreach($value as $key => $value2 ) {
                    if( $key == 'name') { echo "<h1 style='text-align: center'>$value2</h1>"; }
                    // Look for the 'sections' property
                    if( $key == 'sections' ) {
                        // Check to see if the value is an array
                        if( gettype($value2) == 'array' ) {
                            // Loop through the array [ === SECTION TITLES OUTPUT HERE === ]
                            foreach($value2 as $key => $value3 ) {
                              // Output 'Title' of the section and space
                              echo "</br></br>".$value3['title']."</br></br>";
                              if( gettype($value3) == 'array') {
                                  foreach($value3 as $key => $value4) {
                                    if( $key == 'questions' ) {
                                      if( gettype($value4) == 'array') {
                                          // Loop through the array [ === QUESTIONS OUTPUT HERE === ]
                                          foreach($value4 as $key => $question) {
                                            $quesNumber = $key + 1;
                                            echo "$quesNumber .".$question['output']."</br>";
                                            if( isset($question['additionalQuestions'])) {
                                              echo "</br>ADDITIONAL QUESTIONS</br>";
                                              if( gettype($question['additionalQuestions']) == 'array') {
                                                // Loop through the additional questions [ === ADDITIONAL QUESTIONS OUTPUT HERE === ]
                                                foreach($question['additionalQuestions'] as $key => $question2) {
                                                  $quesNumber = $key + 1;
                                                  echo "$quesNumber. ".$question2['output']."</br>";
                                                }
                                                echo "</br>";
                                              }
                                            }



                                           }
                                      }
                                    }
                                  }
                              }
                            }
                        }
                    }
                  }
              }
          }
      } // LAST FOREACH Loop

    }

    // NUMBER OF SECTIONS                   => count($this->jsonArray['l0']['sections']);
    // NUMBER OF QUESTIONS IN A SECTION     => count($this->jsonArray['l0']['sections'][0]['questions']);
    // GET QUESTION NAME $this->document->getValue("#/l0/sections/0/questions/0/output");


    private function getQuestionArray($list, $section) {
        $rootLevel = "";
        // Check what root level is required ( l0, l1, l2, ...)
        foreach($this->rootTypes as $key => $value) {
            if($value == $list) {
                $rootLevel = $key;
                break;
            }
        }
        // Load the questions from the corresponding section into a new variable
        $questionArray = $this->jsonArray[$rootLevel]['sections'][$section]['questions'];

        // Need both root level and the question array to complete the changes for JSON list
        return $result = array("rootLevel"=>$rootLevel, "questionArray"=>$questionArray);
    }

    public function addQuestion($list, $section, $questionBefore, $questionAfter, $newQuestion) {


        // Load the questions from the corresponding section into a new variable
        $questions = $this->getQuestionArray($list, $section);
        // Used with array_splice later in the function
        $origQuestionCount = count($questions['questionArray']);

        // Set a default question structure to be loaded and inserted ( Only used as a template )
        $insertQuestion = $this->jsonArray['l0']['sections'][0]['questions'][0];

        // Load the question with the passed in values
        $insertQuestion['id'] = $questionBefore + 1;
        $insertQuestion['output'] = $newQuestion;

        // Insert the question into the JSON array ( Have to define that it is an array in order to insert correctly)
        array_splice($questions['questionArray'], $questionBefore + 1, 0, array($insertQuestion));

        // Update every question Id after the inserted question and increment by 1

        /*
            -Have to get the number of elements after the inserted question

                So...get the total count.  Then subtract the value of the index for the question
                before the inserted question and add 1.  That should give the remaining elements in
                the question array.

            -Have to loop only through those elements and increment their ID by 1

                Start after the inserted question ( $questionAfter ).  Need to add 1 due to it being the array index
        */

        $totalCount = count($questions['questionArray']);
        $remainingElementCount = $totalCount - ($questionBefore + 1);

        for($i = ($questionAfter + 1); $i <= $remainingElementCount; ++$i) {
                $questions['questionArray'][$i]['id'] = $questions['questionArray'][$i]['id'] + 1;
        }

        // // Put the array of altered questions back into the original JSON array
        // array_splice($this->jsonArray[$questions['rootLevel']]['sections'][$section]['questions'],
        //              0,
        //              count($this->jsonArray[$questions['rootLevel']]['sections'][$section]['questions']),
        //              $questions['questionArray']);
        //
        // // var_dump($this->jsonArray[$questions['rootLevel']]['sections'][$section]);
        //
        // // Put the array back into the JSON file and call it 'update.json'
        // $jsonString = json_encode($this->jsonArray);
        //
        // if(!file_put_contents("update.json", $jsonString, FILE_USE_INCLUDE_PATH)) {
        //     echo '<h2 class="text-center">Could not create a JSON file</h2>';
        // } else {
        //     echo '<h2 class="text-center">Updated JSON file successfully</h2>';
        // }

        // Save to a JSON file
        $this->saveToJson($this->jsonArray, $this->jsonFileOutput, $questions, $section);

    }

    public function deleteQuestion($list, $section, $questionToDelete) {

        $questions = $this->getQuestionArray($list, $section);

        // Remove the question from the array
        unset($questions['questionArray'][$questionToDelete]);
        // Reset the index values
        $questions['questionArray'] = array_values($questions['questionArray']);

        // Reset the IDs for every question below the removed question ( Minus 1 )
        $this->resetQuestionIds($questions, $questionToDelete, "decrement");

        // Save to a JSON file
        $this->saveToJson($this->jsonArray, $this->jsonFileOutput, $questions, $section);

    }


    public function updateQuestion($list, $section, $questionToUpdate, $updateString) {

        // Get questions
        $questions = $this->getQuestionArray($list, $section);

        // Update the question output
        $questions['questionArray'][$questionToUpdate]['output'] = $updateString;

        // Save to a JSON file
        $this->saveToJson($this->jsonArray, $this->jsonFileOutput, $questions, $section);

    }

    // $resetType => "increment" or "decrement".  Step by 1
    private function resetQuestionIds(&$questions, $offset, $resetType) {

      /*
          -Have to get the number of elements after the inserted question

              - Get the total number of elements from the altered array ( After add/update/delete )
              - Subtract the total number from the offset value
              - This value should be the remaining elements after the target question

          -Have to loop only through those elements and increment their ID by 1

              Start after the inserted question ( $questionAfter ).  Need to add 1 due to it being the array index
      */
        $totalQuestionCount = count($questions['questionArray']);
        $remainingElements = $totalQuestionCount - $offset;

        for($i = $offset; $i < $remainingElements; ++$i) {
                if($resetType == "increment") {
                    $questions['questionArray'][$i]['id'] = $questions['questionArray'][$i]['id'] + 1;
                } else {
                    $questions['questionArray'][$i]['id'] = $questions['questionArray'][$i]['id'] - 1;
                }
        }
    }

    // Splices questions back into the jsonArray and saves them to a file
    private function saveToJson(&$jsonArray, $jsonFilename, $questions, $section) {

        // Put the array of altered questions back into the original JSON array
        array_splice($jsonArray[$questions['rootLevel']]['sections'][$section]['questions'],
                     0,
                     count($jsonArray[$questions['rootLevel']]['sections'][$section]['questions']),
                     $questions['questionArray']);

        // Encode the associative array into a JSON string
        $jsonString = json_encode($this->jsonArray);

        if(!file_put_contents($jsonFilename, $jsonString, FILE_USE_INCLUDE_PATH)) {
            echo '<h2 class="text-center">Could not create a JSON file</h2>';
        } else {
            echo '<h2 class="text-center">Updated JSON file successfully</h2>';
        }

        // Output to the screen to review
        var_dump($jsonArray[$questions['rootLevel']]['sections'][$section]['questions']);
    }


    /**
        $listId = 'BE', 'AU', etc

        Will convert the ID of a list into the root key index first.  Next using the
        root key, the function acquires each section name through 'json_works'

    */
    public function getSectionHeaders($listId) {

        $listKey;       # Root list key l0, l1, l2

        // Get the root list value to use
        foreach($this->jsonArray as $key => $listIndex) {
            if($listIndex['id'] == $listId) {
                $listKey = $key;
                break;
            }
        }

        // Get the number of sections for the list type
        $sectionCount = count($this->jsonArray[$listKey]['sections']);
        $sectionHeaders = [];
        $counter = 0;
        // Loop through and collect each section header
        while( $counter < $sectionCount) {

            $sectionHeaders[] = $this->document->getValue("#/$listKey/sections/$counter/title");
            ++$counter;
        }

        // Return the array of section headers
        return $sectionHeaders;
    }

    public function getSectionQuestions($sectionHeaderIndex) {


        // Get the number of sections for the list type
        $questionCount = count($this->jsonArray['l0']['sections'][$sectionHeaderIndex]['questions']);
        $questions = [];
        $counter = 0;
        // Loop through and collect each section header
        while( $counter < $questionCount) {

            $questions[] = $this->document->getValue("#/l0/sections/$sectionHeaderIndex/questions/$counter/output");
            ++$counter;
        }

        // Return the array of section headers
        return $questions;

    }

    public function displaySectionHeaderComboBox($listId) {

        ?>
        <label class="show labelSpacing" for="section" id="labelSectionComboBox">Choose a section:</label>
        <select name="section" id="sectionCombobox">
          <?php
          $sectionHeaders = $this->getSectionHeaders($listId);
          foreach($sectionHeaders as $sectionHeaderIndex => $header) {
          ?>
              <option value="<?php echo $sectionHeaderIndex; ?>"><?php echo $header; ?></option>
          <?php
          }
          ?>
        </select>
        <?php
    }

    /**
        Accepts the Id of the section to find all questions related to that section

    */
    public function displaySectionQuestionsComboBox($sectionHeaderIndex, $functionType) {

        $DOT_SPACING = ".....";
        $sectionQuestions = $this->getSectionQuestions($sectionHeaderIndex);
        $questionComboBox = null;

        // Loop and create a single comboBox filled with the required questions of the desired section
        foreach($sectionQuestions as $questionId => $question) {
            if(strlen($question) >= 50) { $question = substr($question, 0, 50).$DOT_SPACING; }


            $questionComboBox .= '<option value='.$questionId.'>'.($questionId + 1).'.'.$question.'</option>';
            // $questionCombobox_After .= '<option value='.$questionId.'>'.($questionId + 1).'.'.$question.'</option>';

        }

        // SWITCH - Change output based on the function required ( User clicked on Add, Update or Delete )
        switch($functionType) {

          // Requires two comboBoxes and a textfield
          case "comboBoxContainerAdd":
            ?>
                <label class="show labelSpacing" for="questions" id="labelQuestionComboBox">Between which questions?</label>
                <select name="questionBefore" id="questionCombobox_Before">
                    <?php echo $questionComboBox; ?>
                </select>

                <select name="questionAfter" id="questionCombobox_After">
                    <?php echo $questionComboBox; ?>
                </select>

                <label class="show" for="questionAdd" id="labelNewQuestion">New Question:</label>
                <input type="text" name="questionAdd" id="newQuestion" />

            <?php
            break;
          // Requires one comboBox and a textfield
          case "comboBoxContainerUpdate":
            ?>

            <label class="show labelSpacing" for="questions" id="labelQuestionComboBox">Update which question?</label>
            <select name="questionUpdate" id="questionCombobox_Update">
                <?php echo $questionComboBox; ?>
            </select>

            <label class="show" for="questionUpdate" id="labelNewQuestion">Enter Update:</label>
            <input type="text" name="updateString" id="newUpdate" />

            <?php
            break;
          // Requires one comboBox and a textfield
          case "comboBoxContainerDelete":
            ?>

            <label class="show labelSpacing" for="questions" id="labelQuestionComboBox">Delete which question?</label>
            <select name="questionDelete" id="questionCombobox_Delete">
                <?php echo $questionComboBox; ?>
            </select>

            <?php
            break;

        }
    }
}


?>
