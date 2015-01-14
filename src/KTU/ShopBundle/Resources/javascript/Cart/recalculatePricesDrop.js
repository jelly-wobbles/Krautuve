

function recalculatePricesDrop(amount){

    var oldEU = $(".totalPrices").children("#price").children("span").html();
        oldEU = parseFloat( oldEU.replace(',', '.') );
        oldEU = oldEU.toFixed(2);


    var newEU = oldEU - amount;
    var newLT = newEU * 3.4528;
        newEU = newEU.toFixed(2).toString().replace(".", ",");
        newLT = newLT.toFixed(2).toString().replace(".", ",");


    $(".totalPrices").children("#price").html( newLT + " Lt / " + "<span>" + newEU + " € </span>");
}