
function setCartCount(count){
    $(".cartInfo").children("h4").html(count);
}

function indicateProgress(element, check){

    if( check == true){
        $(".actionsButton").attr("disabled", "disabled").css("opacity","0.3");
        $(element).html("<div class='imgWrap'><img src='/krautuve/web/images/loading.gif'></div>");
        $(element).siblings("#title, #price").css({"opacity":"0.5"});
    }
    else{
        $(".actionsButton").attr("disabled", false).css("opacity","1");
    }

}


function dropItem(url, element, src){
    var element = $(element).parents("#actions");
    var txt;
    var r = confirm("Ar tikrai norite išmesti šią prekę iš krepšelio?");
    if (r == true) {

        indicateProgress(element, true);

        var price = $(element).siblings("#price").children("span").html();
        price = parseFloat( price.replace(',', '.') );

        $.ajax({
            type: "POST",
            url: url,
            cache: "false",
            data: {
            },
            success: function( data ) {

                if( data >= 1 ) {
                    $(element).parents(".itemContainer").fadeOut();
                    recalculatePricesDrop(price);
                    setCartCount(data);
                    indicateProgress(element, false);
                }
                else{
                    if( data == 0 ){
                        $(element).parents(".itemContainer").fadeOut();
                        $(".cartContainer").fadeOut();
                        location.href = '/krautuve/web/';
                    }
                    else
                    {
                        location.reload();
                    }
                }
            },
            error: function( data ) {
                console.log( "error: " + data );
            }
        });

    } else {
        // do nothing
    }


}
