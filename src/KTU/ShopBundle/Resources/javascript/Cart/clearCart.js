
function indicateProgress(){
    $("#buyButton").hide();
    $(".itemContainer").css({"opacity":"0.5"});
    $(".actions").html("<div class='imgWrap'><img src='/krautuve/web/images/loading.gif'></div>");
}


function clearCart(url){

    var r = confirm("Ar tikrai norite išmesti visas prekes iš krepšelio?");
    if (r == true) {

        indicateProgress();

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

    }

}
