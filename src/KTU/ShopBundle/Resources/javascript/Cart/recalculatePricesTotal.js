

function recalculatePricesTotal(id, url){

    var thisPriceField = $(".totalPrices").children("#price");

    showField( thisPriceField , false);

    $.ajax({
        type: "POST",
        url: url,
        cache: "false",
        data: {
            id: id
        },
        success: function( data ) {
            data = parseFloat(data);

            var newEU = data;
            newEU = format2(newEU);

            $(thisPriceField).html( newEU + " €").promise().done(function(){
               showField(thisPriceField, true);
            });
        },
        error: function( data ) {
            console.log( "error: " + data );
        }
    });

}