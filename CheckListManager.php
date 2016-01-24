
<!--

.o88b. db   db d88888b  .o88b. db   dD db      d888888b .d8888. d888888b      .88b  d88.  .d8b.  d8b   db  .d8b.   d888b  d88888b d8888b.
d8P  Y8 88   88 88'     d8P  Y8 88 ,8P' 88        `88'   88'  YP `~~88~~'      88'YbdP`88 d8' `8b 888o  88 d8' `8b 88' Y8b 88'     88  `8D
8P      88ooo88 88ooooo 8P      88,8P   88         88    `8bo.      88         88  88  88 88ooo88 88V8o 88 88ooo88 88      88ooooo 88oobY'
8b      88~~~88 88~~~~~ 8b      88`8b   88         88      `Y8b.    88         88  88  88 88~~~88 88 V8o88 88~~~88 88  ooo 88~~~~~ 88`8b
Y8b  d8 88   88 88.     Y8b  d8 88 `88. 88booo.   .88.   db   8D    88         88  88  88 88   88 88  V888 88   88 88. ~8~ 88.     88 `88.
`Y88P' YP   YP Y88888P  `Y88P' YP   YD Y88888P Y888888P `8888Y'    YP         YP  YP  YP YP   YP VP   V8P YP   YP  Y888P  Y88888P 88   YD

Author: Christopher Sigouin
Date: Jan 23rd, 2016

http://stackoverflow.com/questions/2904131/what-is-the-difference-between-json-and-object-literal-notation

-->

<?php

require __DIR__ . '/vendor/autoload.php';

class CheckListManager {

    private $listTypes;                 # An array that holds the type of lists in the JSON file
    private $listType;                  # The current list type
    private $jsonFile;                  # Holds the filename of the JSON file
    private $jsonArray;                 # Holds the JSON data as an associative array
    private $sectionHeader;             # Holds the current section name ' String '
    private $additionalQuestions;       # Boolean value
    private $debug;                     # Used for debugging the script
    private $document;

    public function __construct()
    {
        $this->debug = false;
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



    public function addQuestion($list) {

        $listType = $this->document->getValue("#/l0/name");
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

        ?>
        <select name="section" id="questionCombobox">
          <?php
          $sectionQuestions = $this->getSectionQuestions($sectionHeaderIndex);
          foreach($sectionQuestions as $questionId => $question) {
          ?>
              <option value="<?php echo $questionId; ?>"><?php echo $question; ?></option>
          <?php
          }
          ?>
        </select>
        <?php
    }

}


?>
