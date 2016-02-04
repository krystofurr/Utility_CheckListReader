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

require __DIR__ . '/vendor/autoload.php';

class CheckListManager {

    private $listTypes;                 # An array that holds the type of lists in the JSON file
    private $listType;                  # The current list type
    private $rootTypes;                 # The current root level list from the JSON file
    private $jsonFile;                  # Holds the filename of the JSON file
    private $jsonArray;                 # Holds the JSON data as an associative array
    private $sectionHeader;             # Holds the current section name ' String '
    private $additionalQuestions;       # Boolean value
    private $debug;                     # Used for debugging the script
    private $document;

    public function __construct()
    {
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



    public function addQuestion($list, $section, $questionBefore, $questionAfter, $newQuestion) {

        $rootLevel = "";
        // Check what root level is required ( l0, l1, l2, ...)
        foreach($this->rootTypes as $key => $value) {
            if($value == $list) {
                $rootLevel = $key;
                echo $rootLevel;
                break;
            }
        }
        // Load the questions from the corresponding section into a new variable
        $questionArray = $this->jsonArray[$rootLevel]['sections'][$section]['questions'];


        // Set a default question structure to be loaded and inserted ( Only for a template )
        $insertQuestion = $this->jsonArray[l0]['sections'][0]['questions'][0];

        // Load the question with the passed in values
        $insertQuestion['id'] = $questionBefore + 1;
        $insertQuestion['output'] = $newQuestion;



        // Insert the question into the JSON array
        array_splice($questionArray, $questionBefore, 0, $insertQuestion);

        var_dump($questionArray);

        // array_push($questionArray, $insertQuestion);
        // // Hold the data in the position for the new array ( ISSUE IS HERE!!! )
        // $tempArray = $this->jsonArray[$rootLevel]['sections'][$section]['questions'][$questionBefore + 1];
        // $this->jsonArray[$rootLevel]['sections'][$section]['questions'][$questionBefore + 1] = $insertQuestion;

        // Put the array of altered questions back into the original JSON array ( Reverse of the above )
        $this->jsonArray[$rootLevel]['setions'][$section]['questions'] = $questionArray;






        //$listType = $this->document->getValue("#/l0/name");
        //$this->jsonArray[l0]
        // $this->jsonArray[Top Level]['sections'][Section Value]['questions'][Question Value]
        //var_dump($this->jsonArray[l0]['sections'][0]['questions']);
        //$this->jsonArray[l0]['sections'][0]['questions']
        //array_splice($input, 3, 0, "purple");
    }

    public function deleteQuestion() {

    }

    public function updateQuestion() {

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
    public function displaySectionQuestionsComboBox($sectionHeaderIndex) {
        $DOT_SPACING = ".....";

        ?>
        <label class="show labelSpacing" for="questions" id="labelQuestionComboBox">Between which questions?</label>

          <?php
          $sectionQuestions = $this->getSectionQuestions($sectionHeaderIndex);
          foreach($sectionQuestions as $questionId => $question) {
              if(strlen($question) >= 50) { $question = substr($question, 0, 50).$DOT_SPACING; }

              $questionCombobox_Before .= '<option value='.$questionId.'>'.($questionId + 1).'.'.$question.'</option>';
              $questionCombobox_After .= '<option value='.$questionId.'>'.($questionId + 1).'.'.$question.'</option>';

          }
          ?>

        <select name="questionBefore" id="questionCombobox_Before">
            <?php echo $questionCombobox_Before; ?>
        </select>

        <select name="questionAfter" id="questionCombobox_After">
            <?php echo $questionCombobox_After; ?>
        </select>

        <label class="show" for="questionAdd" id="labelNewQuestion">New Question:</label>
        <input type="text" name="questionAdd" id="newQuestion" />
        <?php
    }

}


?>
