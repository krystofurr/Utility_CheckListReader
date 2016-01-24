<?php
error_reporting(-1); // ALL ERRORS
/*

.o88b. db   db d88888b  .o88b. db   dD db      d888888b .d8888. d888888b      d8888b. d88888b  .d8b.  d8888b. d88888b d8888b.
d8P  Y8 88   88 88'     d8P  Y8 88 ,8P' 88        `88'   88'  YP `~~88~~'      88  `8D 88'     d8' `8b 88  `8D 88'     88  `8D
8P      88ooo88 88ooooo 8P      88,8P   88         88    `8bo.      88         88oobY' 88ooooo 88ooo88 88   88 88ooooo 88oobY'
8b      88~~~88 88~~~~~ 8b      88`8b   88         88      `Y8b.    88         88`8b   88~~~~~ 88~~~88 88   88 88~~~~~ 88`8b
Y8b  d8 88   88 88.     Y8b  d8 88 `88. 88booo.   .88.   db   8D    88         88 `88. 88.     88   88 88  .8D 88.     88 `88.
`Y88P' YP   YP Y88888P  `Y88P' YP   YD Y88888P Y888888P `8888Y'    YP         88   YD Y88888P YP   YP Y8888D' Y88888P 88   YD

Author: Chris Sigouin
Date: 01/21/2016

*/


// The JSON checklist file
define('JSON_FILE', 'Checklists.json');

// Create a 'CheckListManager'
require_once('CheckListManager.php');
$CM = new CheckListManager();
$CM->initializeJSONData(JSON_FILE);

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>CHECKLIST READER</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" media="screen" title="no title" charset="utf-8">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/myJs.js"></script>

  </head>
  <body>

    <form action="checkListReader.php" method="POST"/>
        <div style="text-align:center;">
            <div style="height: 50px"></div> <!-- SPACER -->
            <label for="list">Check List Type</label>

            <select name="list">
                <?php
                foreach($CM->listTypes as $key => $value) {
                ?>
                    <option value="<?php echo $value ?>"><?php echo $key ?></option>
                <?php
                }
                ?>
            </select>

            <button class="btn btn-info" type="submit" name="submit">Load List!</button></br></br>
            <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#add">Add Question</button>
            <button class="btn btn-primary" type="button" name="update">Update Question</button>
            <button class="btn btn-danger" type="button" name="delete">Delete Question</button>

        </div>
    </form>
    <form action="checkListReader.php" method="post">
        <!-- PLACE THE MODALS FOR THE ADD, UPDATE AND DELETE buttons -->

        <!-- ADD MODAL -->
        <div id="add" class="modal fade" role="dialog">
          <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ADD QUESTION</h4>
              </div>
              <div class="modal-body">
                <select id="listCombobox" name="list">
                  <?php
                  foreach($CM->listTypes as $listId => $value) {
                  ?>
                      <option value="<?php echo $value; ?>"><?php echo $listId; ?></option>
                  <?php
                  }
                  ?>
                </select>

                <div id="sectionsCB"></div>
                <div id="questionsCB"></div>

                <label for="question">What section?</label>
                <input type="text" name="questionAdd" /></br>
                <label for="question">At Start?</label>
                <input type="checkbox" name="start" /></br>
                <label for="question">At End?</label>
                <input type="checkbox" name="end" /></br>
                <label for="question">Between ? and ?</label>
                <select name="questionStart" id="questionStartCombobox">
                  <?php
                      $questions = $CM->getSectionQuestions(1);
                      foreach($questions as $questionsIndex => $question) {
                          // If the question is equal to or longer than 50 characters, shorten it
                          if(strlen($question) >= 50) { $question = substr($question, 0, 50).'.....'; }
                      ?>
                        <option value="<?php echo $questionsIndex; ?>"><?php echo ($questionsIndex + 1).". ".$question; ?></option>
                      <?php
                      }
                  ?>
                </select>
                AND
                <select name="questionEnd" id="questionEndCombobox">
                    <?php
                        $questions = $CM->getSectionQuestions(0);
                        foreach($questions as $questionsIndex => $question) {
                            // If the question is equal to or longer than 50 characters, shorten it
                            if(strlen($question) >= 50) { $question = substr($question, 0, 50).'.....'; }
                        ?>
                            <option value="<?php echo $questionsIndex; ?>"><?php echo ($questionsIndex + 1).". ".$question; ?></option>
                        <?php
                        }
                    ?>
                </select></br>
                <label for="question">Enter the question</label>
                <input type="text" name="questionAdd" />
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary" name="submit">Save</button>
                <button  id="clear" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>

          </div>
        </div>

        <!-- UPDATE MODAL -->
        <div id="add" class="modal fade" role="dialog">
          <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">UPDATE QUESTION</h4>
              </div>
              <div class="modal-body">
                <label for="question">Enter the question</label>
                <input type="text" name="questionAdd" />
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary" name="submit">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>

          </div>
        </div>

        <!-- DELETE MODAL -->
        <div id="add" class="modal fade" role="dialog">
          <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">DELETE QUESTION</h4>
              </div>
              <div class="modal-body">
                <label for="question">Enter the question</label>
                <input type="text" name="questionAdd" />
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary" name="submit">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>

          </div>
        </div>

    </form>

<?php

if(isset($_POST['submit'])) {

  $chosenList = $_POST['list'];

  if(isset($_POST['questionAdd'])) {
      $CM->addQuestion($chosenList);
  } elseif (isset($_POST['update'])) {
      $CM->updateQuestion($chosenList);
  } elseif(isset($_POST['delete'])) {
      $CM->deleteQuestion($chosenList);
  } else {
      foreach($CM->listTypes as $key => $value ) {
          if($chosenList == $value) { $CM->listType = $chosenList; }
      }


      $CM->displayQuestions($CM->listType);
  }
}
?>




  </body>
</html>
