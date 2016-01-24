// A $( document ).ready() block.
$( document ).ready(function() {
    console.log( "jQuery ready!" );

    // On the list box change call the PHP script to create the comboBox based on list type
    $("#listCombobox").change(function() {
        $.ajax({
            url: "combobox/sectionHeaders.php",             // Script to call
            type: 'post',                                   // Data sending method
            data: { listType: this.value },                 // Payload
            success: function(result){                      // What to do if it's successful
                $("#sectionsCB").empty().append(result);    // Clear previous and append
            },
            error: function(result){
                alert('ajax failed');
            }
        });
    });

    $("#sectionCombobox").change(function() {
        $.ajax({
            url: "combobox/sectionQuestions.php",   // Script to call
            type: 'post',                           // Data sending method
            data: { sectionType: this.value },      // Payload
            success: function(result){              // What to do if it's successful
                $("#questionsCB").empty().append(result);
            },
            error: function(result){
                alert('ajax failed');
            }
        });
    });

    $("#clear").click(function() {
        alert('cleared');
        $("#sectionsCB").empty()
        $("#questionsCB").empty()
    });


});
