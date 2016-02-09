// A $( document ).ready() block.
$( document ).ready(function() {
    console.log( "jQuery ready!" );

    // Instantiate Clipboard.js
    new Clipboard('#copyButton');

    // Constants to use with the ajax drop boxes
    var COMBO_BOX_CONTAINER_ADD = "comboBoxContainerAdd";
    var COMBO_BOX_CONTAINER_UPDATE = "comboBoxContainerUpdate";
    var COMBO_BOX_CONTAINER_DELETE = "comboBoxContainerDelete";
    var COMBO_BOX_LIST = 'listCombobox';
    var COMBO_BOX_SECTION = 'sectionCombobox';
    var LABEL_SECTION = 'labelSectionComboBox';
    var COMBO_BOX_QUESTION_BEFORE = 'questionCombobox_Before';
    var COMBO_BOX_QUESTION_AFTER = 'questionCombobox_After';
    var TEXT_BOX_NEW_QUESTION = 'newQuestion';
    var LABEL_QUESTION = 'labelQuestionComboBox';
    var LABEL_NEW_QUESTION = 'labelNewQuestion';

    $("#dumpJSON").click(function() {
      var listId = document.getElementById('list').value;

      $.ajax({
          url: "utility/dumpJSON.php",                     // Script to call
          type: 'post',
          data: {
            listType: listId
          },                                         // Data sending method
          success: function(result){
              console.log("[ INFO ]: JSON Dump");                                // What to do if it's successful
              $('body').append(result);        // Clear previous and append
          },
          error: function(result){
              alert('DUMP failed');
          }
      });
    })

    // Event Delegation done through this DIV container for all Combo Boxes
    $("#"+COMBO_BOX_CONTAINER_ADD)
        .add("#"+COMBO_BOX_CONTAINER_UPDATE)
        .add("#"+COMBO_BOX_CONTAINER_DELETE)
        .click(function(event) {

        // Holds the data for a single question.  Additional question section is 'false' by default
        var selectedQuestion = {
            value: "",
            output: "",
            additionalQuestions: false
        };
        var isOption, targetDivId, comboBoxId, payload;
        // Check for browser type because drop downs behave differently with click events
        if(bowser.chrome == true) {

            console.log("[ INFO ]: Browser Type: Google Chrome");
            // Use jQuery to convert the target into a collection ( used for Chrome )
            var target = $( event.target );
            isOption = target.children()[0].nodeName;
            comboBoxId = target.context.id;
            targetDivId = event.currentTarget.id;
            listId = target[0].value;

        } else if(bowser.firefox == true) {
            console.log("[ INFO ]: Browser Type: Mozilla Firefox");
            isOption = event.target.nodeName;
            comboBoxId = event.target.parentNode.id;
            targetDivId = event.currentTarget.id;
            listId =  event.target.value;

        } else {
            console.log("[ INFO ]: Browser Type: Other");
            isOption = event.target.nodeName;
        }


        // Check to see what the ID is for the 'SELECT' element
        if(isOption == "OPTION") {
            console.log('Option Selected!');

            // SWITCH... on the ID of the 'SELECT' element based on the target event element
            switch(comboBoxId) {
                case COMBO_BOX_LIST:
                    console.log("CHOSEN: " + COMBO_BOX_LIST);

                    // Check for existing 'Section' & 'Question' Combo Box
                    if($('#'+COMBO_BOX_SECTION).length) {
                        document.getElementById(COMBO_BOX_SECTION).remove();
                        document.getElementById(LABEL_SECTION).remove();
                        document.getElementById(LABEL_QUESTION).remove();
                        document.getElementById(COMBO_BOX_QUESTION_BEFORE).remove();
                        document.getElementById(COMBO_BOX_QUESTION_AFTER).remove();
                        document.getElementById(LABEL_NEW_QUESTION).remove();
                        document.getElementById(TEXT_BOX_NEW_QUESTION).remove();
                    }

                    $.ajax({
                        url: "combobox/sectionHeaders.php",                     // Script to call
                        type: 'post',                                           // Data sending method
                        data: {
                          listType: listId
                        },                                           // Data to send
                        success: function(result){
                            console.log(result);                                // What to do if it's successful
                            $('#' + targetDivId).append(result);        // Clear previous and append
                        },
                        error: function(result){
                            alert('Sections AJAX request failed');
                        }
                    });

                    break;
                // User chooses a section to display the combo box(s) for questions
                case COMBO_BOX_SECTION:
                    console.log("CHOSEN: " + COMBO_BOX_SECTION);

                    // Check for existing 'Question' Combo Box
                    if($('#'+COMBO_BOX_QUESTION_BEFORE).length) {
                        document.getElementById(LABEL_QUESTION).remove();
                        document.getElementById(COMBO_BOX_QUESTION_BEFORE).remove();
                        document.getElementById(COMBO_BOX_QUESTION_AFTER).remove();
                        document.getElementById(LABEL_NEW_QUESTION).remove();
                        document.getElementById(TEXT_BOX_NEW_QUESTION).remove();



                    }

                    $.ajax({
                        url: "combobox/sectionQuestions.php",          // Script to call
                        type: 'post',                                  // Data sending method
                        data: {
                          sectionType: listId,
                          functionType: targetDivId                 // Determine what function ( ADD, UPDATE or DELETE )
                        },
                        success: function(result){                     // What to do if it's successful
                            $('#' + targetDivId).append(result);
                        },
                        error: function(result){
                            alert('Questions AJAX request failed');
                        }
                    });


                    break;


            }

        }

    });
});
