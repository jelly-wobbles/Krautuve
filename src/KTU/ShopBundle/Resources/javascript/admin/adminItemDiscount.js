(function(){

    var id = $('#item-id').val();
    var body = $('body');
    var confirmationModal = new ConfirmationModal();
    var informationModal = new InformationModal();
    var warningModal = new WarningModal();

    var changeDiscountBtnsPrototype = '<button class="btn btn-default btn-opt btn-discount">Keisti akciją <span class="glyphicon glyphicon-tag"></span></button>'+
                                        '<button class="btn btn-default btn-remove">Nuimti akciją <span class="glyphicon glyphicon-remove"></span></button>';

    var addDiscountBtnPrototype = '<button class="btn btn-default btn-opt btn-discount">Pridėti akciją <span class="glyphicon glyphicon-tag"></span></button>';

    informationModal.setTitle('Operacija sėkminga');
    confirmationModal.setTitle('Patvirtinti');
    warningModal.setTitle('Klaida');

    body.on('click', '.btn-discount', function(){
        manageDiscount();
    });

    body.on('click', '.btn-remove', function(){
        removeDiscount();
    });

    function manageDiscount(){
        confirmationModal.setContentAsText('Įveskite akcijos dydį procentais:'+
        itemDiscountPrototype);
        confirmationModal.setIntentions('discount');
        confirmationModal.showModal();
    }

    function removeDiscount(){
        confirmationModal.setContentAsText('Ar tikrai norite <span class="emphasis">nuimti</span> akciją?');
        confirmationModal.setIntentions('remove');
        confirmationModal.showModal();
    }

    body.on('shown.bs.modal', function(){
        $('#shop_item_discount_itemDiscount').focus();
    });

    body.on('modal-confirm-event', function(e, intention){
        confirmationModal.hideModal();

        if(intention == 'discount'){
            $("body").css("cursor", "wait");
            var value = $('#shop_item_discount_itemDiscount').val();
            $.ajax({
                url: manageDiscountURL,
                type: 'POST',
                data: {
                    'shop_item_discount[itemDiscount]': value
                },
                success: function(response){
                    $("body").css("cursor", "default");
                    if(response['errors'] != null){
                        confirmationModal.setContentAsText('Įveskite akcijos dydį procentais:'+
                        itemDiscountPrototype);
                        addErrors(response['errors']);
                        confirmationModal.showModal();
                    }
                    else if(response['success'] == true){
                        if(value != 0){
                            $('.discount-controls').html(changeDiscountBtnsPrototype);
                        }
                        else{
                            $('.discount-controls').html(addDiscountBtnPrototype);
                        }
                        manageDiscountPrices(response['prices'], value);
                        informationModal.setContentAsText('Akcija buvo sėkmingai pakeista.');
                        informationModal.showModal();
                    }
                },
                error: function(){
                    $("body").css("cursor", "default");
                    warningModal.setContentAsText('Akcijos pridėti nepavyko.');
                    warningModal.showModal();
                }
            });
        }
        else if(intention == 'remove'){
            $("body").css("cursor", "wait");
            $.ajax({
                url: manageDiscountURL,
                type: 'POST',
                data: {
                    'shop_item_discount[itemDiscount]': 0
                },
                success: function(response){
                    $("body").css("cursor", "default");
                    $('.discount-controls').html(addDiscountBtnPrototype);
                    manageDiscountPrices(response['prices'], 0);
                    informationModal.setContentAsText('Akcija buvo sėkmingai nuimta.');
                    informationModal.showModal();
                },
                error: function(){
                    $("body").css("cursor", "default");
                    warningModal.setContentAsText('Akcijos nuimti nepavyko.');
                    warningModal.showModal();
                }
            });
        }
    });

    function addErrors(error){
        var container = $('.modal-content-text');
        container.addClass('has-error');
        container.append('<ul class="help-block"><li>'+error+'</li></ul>');
    }

    function manageDiscountPrices(prices, discount){

        if(discount == 0){
            $('#price-e-no-discount').text('');
            $('#price-lt-no-discount').text('');
            $('#price-e').text(prices['e']);
            $('#price-lt').text(prices['lt']);
            $('#discount').text(discount+'%');
        }
        else{
            $('#price-e-no-discount').text(prices['pure-e']);
            $('#price-lt-no-discount').text(prices['pure-lt']);
            $('#price-e').text(prices['e']);
            $('#price-lt').text(prices['lt']);
            $('#discount').text(discount+'%');
        }

    }

})();