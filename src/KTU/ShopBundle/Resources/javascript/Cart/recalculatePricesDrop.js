

function recalculatePricesDrop(amount){

    var oldEU = $(".totalPrices").children("#price").html();
        oldEU = parseFloat( oldEU.replace(',', '.') );
        oldEU = oldEU.toFixed(2);


    var newEU = oldEU - amount;
        newEU = newEU.toFixed(2).toString().replace(".", ",");


    $(".totalPrices").children("#price").html( newEU + " €");
}