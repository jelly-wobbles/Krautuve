(function(){

    var id = $('#item-id').val();
    var body = $('body');
    var confirmationModal = new ConfirmationModal();
    var informationModal = new InformationModal();
    var warningModal = new WarningModal();
    var removeItemsBtnPrototype = '<button class="btn btn-default btn-remove">Išmesti <span class="glyphicon glyphicon-trash"></span></button>';

    informationModal.setTitle('Operacija sėkminga');
    confirmationModal.setTitle('Patvirtinti');
    warningModal.setTitle('Klaida');

    $('.btn-add').click(function(){
        addItemsToStorage();
    });

    body.on('click', '.btn-remove', function(){
        removeItemsFromStorage();
    });

    function addItemsToStorage(){
        confirmationModal.setContentAsText('Kiek prekių norite <span class="emphasis">pridėti</span>?'+
            itemCountPrototype);
        confirmationModal.setIntentions('add');
        confirmationModal.showModal();
    }

    function removeItemsFromStorage(){
        confirmationModal.setContentAsText('Kiek prekių norite <span class="emphasis">išmesti</span>?'+
        itemCountPrototype);
        confirmationModal.setIntentions('remove');
        confirmationModal.showModal();
    }

    body.on('shown.bs.modal', function(){
        $('#shop_item_storage_itemCount').focus();
    });

    body.on('modal-confirm-event', function(e, intention){
        confirmationModal.hideModal();
        var value = $('#shop_item_storage_itemCount').val();
        if(intention == 'add'){
            $("body").css("cursor", "wait");
            $.ajax({
                url: addStorageURL,
                type: 'POST',
                data: {
                    'shop_item_storage[itemCount]': value
                },
                success: function(response){
                    $("body").css("cursor", "default");

                    if(response['success'] == true){
                        informationModal.setContentAsText('<span class="emphasis">'+value+'</span> prekės(-ių) buvo sėkmingai pridėta.');
                        informationModal.showModal();
                        changeStorageInformation(response['itemCount']);
                    }
                    else{
                        confirmationModal.setContentAsText('Kiek prekių norite <span class="emphasis">pridėti</span>?'+
                            itemCountPrototype);
                        addErrors(response['errors']);
                        confirmationModal.showModal();
                    }
                },
                error: function(){
                    $("body").css("cursor", "default");
                }
            });
        }
        else if(intention == 'remove'){
            $("body").css("cursor", "wait");
            $.ajax({
                url: removeStorageURL,
                type: 'POST',
                data: {
                    'shop_item_storage[itemCount]': value
                },
                success: function(response){
                    $("body").css("cursor", "default");

                    if(response['success'] == true){
                        informationModal.setContentAsText('<span class="emphasis">'+value+'</span> prekės(-ių) buvo sėkmingai išmesta.');
                        informationModal.showModal();
                        changeStorageInformation(response['itemCount']);
                    }
                    else{
                        confirmationModal.setContentAsText('Kiek prekių norite <span class="emphasis">išmesti</span>?'+
                            itemCountPrototype);
                        addErrors(response['errors']);
                        confirmationModal.showModal();
                    }

                },
                error: function(){
                    $("body").css("cursor", "default");
                }
            });
        }
    });

    function addErrors(error){
        var container = $('.modal-content-text');
        container.addClass('has-error');
        container.append('<ul class="help-block"><li>'+error+'</li></ul>');
    }

    function changeStorageInformation(itemCount){
        if(itemCount == 0){
            $('.btn-remove').remove();
        }
        else if($('.btn-remove').length == 0){
            $(removeItemsBtnPrototype).insertAfter('.btn-add');
        }
        $('#stored-items').text(itemCount);
    }

})();

