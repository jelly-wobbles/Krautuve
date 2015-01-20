

function clearCart(){


    var url = window.location.href + '/clear';
    var r = confirm("Ar tikrai norite išmesti visas prekes iš krepšelio?");
    if (r == true) {

        $("#buyButton").hide();
        $(".itemContainer").css({"opacity":"0.5"});
        $(".actions").html("<div class='imgWrap'><img src='/krautuve/web/bundles/ktushop/images/loading.gif'></div>");

        $.ajax({
            type: "POST",
            url: url,
            cache: "false",
            success: function( data ) {
                if( data == 1 ) {
                    location.href = '/krautuve/web/';
                }
                else{
                    location.reload();
                }
            },
            error: function( data ) {
                console.log( "error: " + data );
            }
        });

    } else {
        return false;
    }

}
