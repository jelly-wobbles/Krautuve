
function toCart(url, id, element, src, url2) {
    $("body").children(".modal").remove();
    var emptyStockModal = new ModalWindow('emptyStockModal');
    emptyStockModal.setTitle('Baigėsi pasirinktos prekės');
    emptyStockModal.setContentAsText('Atsiprašome, tačiau sandėlyje šių prekių daugiau nebeturime.');
    emptyStockModal.setButtons([emptyStockModal.okButton]);

    var successModal = new ModalWindow('successModal');
    successModal.setTitle('Prekė pridėta');
    successModal.setContentAsText('Prekė sėkmingai pridėta į krepšelį.');
    successModal.setButtons([successModal.okButton]);



    var oldOnclick = $(element).attr('onclick');
    $(element).attr('onclick', '');

    var oldSrc =  $(element).children("img").attr('src');
    $(element).children("img").attr('src', src);

    $.ajax({
        type: "POST",
        url: url,
        cache: "false",
        data: {
            id: id
        },
        success: function( data ) {

            if( data == -1 ){
                $(element).fadeOut();
                $(element).attr('onclick', oldOnclick);
                $(".cartInfo").attr('onclick', 'location.href ="' + url2 + '"');

                emptyStockModal.showModal();

                $("body").click(function(){
                    emptyStockModal.hideModal();
                });
            }
            else{
                $(element).children("img").attr('src', oldSrc);
                $(element).attr('onclick', oldOnclick);
                $(".cartInfo").attr('onclick', 'location.href ="' + url2 + '"').addClass("available");
                $(".cartInfo").children("h4").html(data);

                successModal.showModal();
                $("body").click(function(){
                    successModal.hideModal();
                });
            }
        },
        error: function( data ) {
            console.log( "error: " + data );
        }
    });
}


