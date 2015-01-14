
function showNewPrices(field, eu, lt){
    $(field).html( lt + " Lt / " + "<span>" + eu + " € </span>").promise().done(function(){
        showField(field, true);
    });
}

function showField(field, check){
    if(check == true){
        field.animate({
            opacity: 1
        }, 'fast');
    }
    else{
        field.animate({
            opacity: 0
        }, 'fast');
    }
}

function format2(value){
    return value.toFixed(2).toString().replace(".", ",");
}

function recalculatePricesQuantity(element, id, url){


    var thisPriceField = $(element).parents("#quantity").siblings("#price");

    showField(thisPriceField, false);

    $.ajax({
        type: "POST",
        url: url,
        cache: "false",
        data: {
            id: id
        },
        success: function( data ) {

            data = parseFloat(data);

            if( data > -1 ){
                var newEU = data;
                var newLT = newEU * 3.4528;
                newEU = format2(newEU);
                newLT = format2(newLT);

                showNewPrices(thisPriceField, newEU, newLT);
            }
            else{
                location.reload();
            }

        },
        error: function( data ) {
            console.log( "error: " + data );
        }
    });

}