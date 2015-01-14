function disableActions(element, check){

    if( check == true){
        $(".actionsButton").attr("disabled", "disabled").css("opacity","0.3");
        $(".quantityInput").not(element).prop('disabled', true).css({"opacity":"0.7"});
    }
    else{
        $(".actionsButton").attr("disabled", false).css("opacity","1");
        $(".quantityInput").prop('disabled', false).css({"opacity":"1"});
    }

}


function quantityChange(id, element, url){

    var url2 = url + '/recalculatePrices';
    var url3 = url + '/recalculateTotalPrice';

    disableActions(element, true);

    $("body").children(".modal").remove();
    var emptyStockModal = new ModalWindow('emptyStockModal');
    emptyStockModal.setTitle('Atsiprašome, tačiau sandėlyje šių prekių daugiau nebeturime.');
    emptyStockModal.setButtons([emptyStockModal.okButton]);

    var newQuantity = $(element).val();

    if( newQuantity > 0 ){
        $(element).fadeOut('fast');

        $("body").css("cursor","wait");

        $.ajax({
            type: "POST",
            url: url,
            cache: "false",
            data: {
                id: id,
                newQuantity: newQuantity
            },
            success: function( data ) {
                if( data > 0 ){
                    recalculatePricesQuantity(element, id, url2);
                    recalculatePricesTotal(id, url3);
                    $(".quantityInput").css({'color':'black', 'box-shadow':'none', '-webkit-box-shadow': 'none', 'text-decoration':'none'});
                    $("body").css("cursor","default");
                    $(element).fadeIn('fast').promise().done(function(){
                        setCartCount(data);
                        disableActions(element, false);
                    });
                }
                else{
                    if( data < 0 ){
                        emptyStockModal.setContentAsText('Šiuo metu sandėlyje turimas prekės kiekis: ' + (-data) + ' vnt.');
                        emptyStockModal.showModal();

                        $(element).css({'color':'red', 'box-shadow':'0 0 5px red', '-webkit-box-shadow': '0 0 5px red', 'text-decoration':'none'});
                        $("body").css("cursor","default");
                        $(element).fadeIn('fast');

                        $("body").click(function(){
                            emptyStockModal.hideModal();
                        });
                    }
                    else{
                        if( data == 0 ){
                            location.reload();
                        }
                    }

                }

            },
            error: function( data ) {
                console.log( "error: " + data );
            }
        });

    }
    else{
        $(element).css({'color':'red', 'box-shadow':'0 0 5px red', '-webkit-box-shadow': '0 0 5px red', 'text-decoration':'none'});
    }



}