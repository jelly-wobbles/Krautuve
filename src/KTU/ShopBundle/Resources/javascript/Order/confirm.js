
function notEnoughStock(value){
    var notEnoughStock = new ModalWindow('notEnoughStock');
    notEnoughStock.setTitle('Klaida');
    notEnoughStock.setContentAsText("Atsiprašome, tačiau jūsų išsirinktas prekes kažkas jau nupirko.");
    notEnoughStock.setButtons([ notEnoughStock.okButton]);
    notEnoughStock.showModal();

    $("body").css("cursor","pointer").click(function(){
        location.href = '/krautuve/web/cart/' + value;
    });
}


function confirm(url, route, src){
    var number = $(".contactsForm").children("#number").val();
    var address = $(".contactsForm").children("#address").val();
    var name = $(".contactsForm").children("#name").val();
    var surname = $(".contactsForm").children("#surname").val();

    $(".confirmButton").html("<img src='" + src + "'></img>");

            $.ajax({
                type: "POST",
                url: url,
                cache: "false",
                data: {
                    number: number,
                    address: address,
                    name: name,
                    surname: surname
                },
                success: function( data ) {
                    if( data == 1){
                        location.href = route;
                    }
                    else{
                        if( data == -1 ){
                            alert("Klaida patvirtinant informaciją");
                        }
                        else{
                            notEnoughStock(data);
                        }
                    }


                },
                error: function( data ) {
                    console.log( "error: " + data );
                }
            });


}

