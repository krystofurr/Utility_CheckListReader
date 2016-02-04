// A $( document ).ready() block.
$( document ).ready(function() {
    console.log( "jQuery ready!" );

    var COMBO_BOX_CONTAINER = "comboBoxContainer";
    var COMBO_BOX_LIST = 'listCombobox';
    var COMBO_BOX_SECTION = 'sectionCombobox';
    var LABEL_SECTION = 'labelSectionComboBox';
    var COMBO_BOX_QUESTION_BEFORE = 'questionCombobox_Before';
    var COMBO_BOX_QUESTION_AFTER = 'questionCombobox_After';
    var TEXT_BOX_NEW_QUESTION = 'newQuestion';
    var LABEL_QUESTION = 'labelQuestionComboBox';
    var LABEL_NEW_QUESTION = 'labelNewQuestion';


    // Event Delegation done through this DIV container for all Combo Boxes
    $("#"+COMBO_BOX_CONTAINER).click(function(event) {

        var isOption, comboBoxId, payload;
        // Check for browser type because drop downs behave differently with click events
        if(bowser.chrome == true) {
            console.log("[ INFO ]: Browser Type: Google Chrome");
            // Use jQuery to convert the target into a collection ( used for Chrome )
            var target = $( event.target );
            isOption = target.children()[0].nodeName;
            comboBoxId = target.context.id;
            // CAN'T FIGURE OUT HOW TO DEFINE THE TARGET DROP DOWN ITEM IN CHROME HERE <============
            payload = target[0].value;

        } else if(bowser.firefox == true) {
            console.log("[ INFO ]: Browser Type: Mozilla Firefox");
            isOption = event.target.nodeName;
            comboBoxId = event.target.parentNode.id;
            payload = event.target.value;

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
                        url: "combobox/sectionHeaders.php",             // Script to call
                        type: 'post',                                   // Data sending method
                        data: { listType: payload },         // Payload
                        success: function(result){
                            console.log(result);                     // What to do if it's successful
                            $('#' + COMBO_BOX_CONTAINER).append(result);         // Clear previous and append
                        },
                        error: function(result){
                            alert('ajax failed');
                        }
                    });

                    break;
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
                        data: { sectionType: payload },      // Payload
                        success: function(result){                     // What to do if it's successful
                            $('#' + COMBO_BOX_CONTAINER).append(result);
                        },
                        error: function(result){
                            alert('ajax failed');
                        }
                    });


                    break;


            }

        }

    });
});
