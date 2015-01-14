(function(){

    var Table = new TableHelper('categories', totalPages, rowsPerPage, 51);
    var confirmationModal = new ConfirmationModal();
    var informationModal = new InformationModal();
    var warningModal = new WarningModal();
    var body = $('body');
    var selectedCategory = '';
    var id = '';
    var itemCount = '';
    var row = null;

    informationModal.setTitle('Operacija sėkminga');
    confirmationModal.setTitle('Patvirtinti');
    warningModal.setTitle('Klaida');

    body.tooltip({ selector: '[data-toggle="tooltip"]' });


    body.on('click', '.btn-remove', function(){
        var controls = $(this).parent();
        row = controls.parent();
        selectedCategory = row.find('.category-name').text();
        id = controls.find('#category-id').val();
        itemCount = parseInt(row.find('.item-count').text());

        confirmationModal.setContentAsText('Ar tikrai norite ištrinti ' +
            '<span class="emphasis">'+selectedCategory+'</span> kategoriją?');
        confirmationModal.setIntentions('remove-check');
        confirmationModal.showModal();
    });


    body.on('modal-confirm-event', function(e, intention){
        confirmationModal.hideModal();

        if(intention == 'remove'){
            removeCategory();
        }

        if(intention == 'remove-check'){
            setTimeout(function(){
                if(itemCount > 0){
                    confirmationModal.setContentAsText('<span class="emphasis">' +
                    selectedCategory+'</span> kategorijoje yra: <span class="emphasis">'
                    +itemCount+' prekė(-ės)</span>. ' +
                    'Prekės bus ištrintos kartu su kategorija. Ar tikrai norite ištrinti?');
                    confirmationModal.setIntentions('remove');
                    confirmationModal.showModal();
                }
                else{
                    removeCategory();
                }
            }, 400);
        }

    });

    function removeCategory(){
        $("body").css("cursor", "wait");
        $.ajax({
            url: removeCategoryURL,
            data: {
                categoryId: id
            },
            type: 'POST',
            success: function(response){
                $("body").css("cursor", "default");

                if(response['success']){
                    informationModal.setContentAsText('<span class="emphasis">' +
                    selectedCategory+'</span> kategorija ištrinta.');
                    informationModal.showModal();
                    row.hide();
                }
            },
            error: function(){
                $("body").css("cursor", "default");
                warningModal.setContentAsText('<span class="emphasis">' +
                selectedCategory+'</span> kategorijos ištrinti nepavyko!');
                warningModal.showModal();
            }
        });
    }

})();