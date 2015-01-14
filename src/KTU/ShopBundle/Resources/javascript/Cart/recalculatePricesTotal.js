

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
            var newLT = newEU * 3.4528;
            newEU = format2(newEU);
            newLT = format2(newLT);

            $(thisPriceField).html( newLT + " Lt / " + "<span>" + newEU + " € </span>").promise().done(function(){
               showField(thisPriceField, true);
            });
        },
        error: function( data ) {
            console.log( "error: " + data );
        }
    });

}