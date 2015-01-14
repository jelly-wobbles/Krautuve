

function validateContactsForm(url){
    var timer, timeout = 2000;
    var confirmButton = $(".confirmButton");
    var oldOnclick =  $(confirmButton).attr('onclick');

    $(".contactsForm").children("input").keyup(function()
    {
        $(".confirmButton").attr('onclick','').css("opacity","0.3");
        var thisInput = $(this);
        var value = $(this).val();
        var inputID = $(this).attr('id');

        if (typeof timer != undefined)
            clearTimeout(timer);

        timer = setTimeout(function()
        {

            $.ajax({
                type: "POST",
                url: url,
                cache: "false",
                data: {
                    inputID: inputID,
                    value: value
                },
                success: function( data ) {
                    if( data == -1 ){
                        $(thisInput).addClass('hasError');
                    }
                    else{
                        $(confirmButton).attr('onclick', oldOnclick ).css("opacity","1");;
                        $(thisInput).removeClass('hasError');
                    }
                },
                error: function( data ) {
                    console.log( "error: " + data );
                }
            });

        }, timeout);
    });
}

