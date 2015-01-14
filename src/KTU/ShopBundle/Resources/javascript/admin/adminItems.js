(function(){

    var confirmationModal = new ConfirmationModal();
    var informationModal = new InformationModal();
    var warningModal = new WarningModal();
    var Table = new TableHelper('items', totalPages, rowsPerPage, 63);

    var body = $('body');
    var selectedRow = null;
    var selectedItem = '';
    var id = '';

    informationModal.setTitle('Operacija sėkminga');
    confirmationModal.setTitle('Patvirtinti');
    warningModal.setTitle('Klaida');

    body.tooltip({ selector: '[data-toggle="tooltip"]' });

    body.on('click', '.btn-remove', function(){
        selectedRow = $(this).parent().parent();
        id = $(this).parent().find('#item-id').val();
        selectedItem = selectedRow.find('.item-name').text();

        confirmationModal.setContentAsText('Ar tikrai norite ištrinti ' +
        '<span class="emphasis">'+selectedItem+'</span> prekę?');
        confirmationModal.setIntentions('remove');
        confirmationModal.showModal();
    });

    body.on('modal-confirm-event', function(e, intention){
        confirmationModal.hideModal();

        if(intention == 'remove'){
            $("body").css("cursor", "wait");

             $.ajax({
             url: removeItemURL,
             data: {
                itemId: id
             },
             type: 'POST',
             success: function(){
                $("body").css("cursor", "default");
                 informationModal.setContentAsText('<span class="emphasis">' +
                 selectedItem+'</span> prekė ištrinta.');
                 informationModal.showModal();
                 selectedRow.hide(250,function(){
                     selectedRow.remove();
                 });
             },
             error: function(){
                $("body").css("cursor", "default");
                 warningModal.setContentAsText('<span class="emphasis">' +
                 selectedItem+'</span> prekės ištrinti nepavyko!');
                 warningModal.showModal();
             }
             });
        }
    });

})();