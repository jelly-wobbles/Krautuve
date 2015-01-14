(function(){

    var banButton = '<button class="btn btn-default btn-ban"'+
                    'data-toggle="tooltip" data-placement="bottom" title="Užblokuoti naudotoją.">'+
                    '<span class="glyphicon glyphicon-ban-circle"></span>'+
                    '</button>';
    var unbanButton = '<button class="btn btn-default btn-unban"'+
                      'data-toggle="tooltip" data-placement="bottom" title="Atblokuoti naudotoją.">'+
                      '<span class="glyphicon glyphicon-ok-circle"></span>'+
                      '</button>';

    var confirmationModal = new ConfirmationModal();
    var informationModal = new InformationModal();
    var warningModal = new WarningModal();
    var userEditor = new UserEditor({'remove': removeUserURL, 'ban': banUserURL, 'unban': unbanUserURL});
    var Table = new TableHelper('users', totalPages, rowsPerPage, 51);

    var selectedRow = null;
    var email = "";
    var fullname = "";
    var body = $('body');

    informationModal.setTitle('Operacija sėkminga');
    confirmationModal.setTitle('Patvirtinti');
    warningModal.setTitle('Klaida');

    body.tooltip({ selector: '[data-toggle="tooltip"]' });

    body.on('click', '.btn-remove', function(){
        selectedRow = $(this).parent().parent();
        var name = selectedRow.find('.user-name').text();
        var surname = selectedRow.find('.user-surname').text();
        email = selectedRow.find('.user-email').text();
        fullname = name+" "+surname;

        confirmationModal.setContentAsText('Ar tikrai norite ištrinti ' +
        '<span class="emphasis">'+fullname+'</span> paskyrą?');
        confirmationModal.setIntentions('delete');
        confirmationModal.showModal();
    });


    body.on('click', '.btn-ban', function(){
        selectedRow = $(this).parent().parent();
        var name = selectedRow.find('.user-name').text();
        var surname = selectedRow.find('.user-surname').text();
        email = selectedRow.find('.user-email').text();
        fullname = name+" "+surname;

        confirmationModal.setContentAsText('Ar tikrai norite užblokuoti ' +
        '<span class="emphasis">'+fullname+'</span> paskyrą?');
        confirmationModal.setIntentions('ban');
        confirmationModal.showModal();
    });

    body.on('click', '.btn-unban',function(){
        selectedRow = $(this).parent().parent();
        var name = selectedRow.find('.user-name').text();
        var surname = selectedRow.find('.user-surname').text();
        email = selectedRow.find('.user-email').text();
        fullname = name+" "+surname;

        confirmationModal.setContentAsText('Ar tikrai norite atblokuoti ' +
        '<span class="emphasis">'+fullname+'</span> paskyrą?');
        confirmationModal.setIntentions('unban');
        confirmationModal.showModal();
    });

    body.on('modal-confirm-event', function(e, intention){
        confirmationModal.hideModal();

        if(intention == 'delete'){
            userEditor.remove(email, function(data){
                informationModal.setContentAsText('<span class="emphasis">' +
                    fullname+'</span> paskyra ištrinta.');
                informationModal.showModal();
                selectedRow.hide(250);
            }, function(data){
                warningModal.setContentAsText('<span class="emphasis">' +
                    fullname+'</span> paskyros ištrinti nepavyko!');
                warningModal.showModal();
            });
        }
        else if(intention == 'ban'){
            userEditor.ban(email, function(){
                informationModal.setContentAsText('<span class="emphasis">' +
                    fullname+'</span> paskyra užblokuota.');
                informationModal.showModal();
                selectedRow.find('.btn-ban').replaceWith(unbanButton);
            }, function(){
                warningModal.setContentAsText('<span class="emphasis">' +
                fullname+'</span> paskyros užblokuoti nepavyko!');
                warningModal.showModal();
            })
        }
        else if(intention == 'unban'){
            userEditor.unban(email, function(){
                informationModal.setContentAsText('<span class="emphasis">' +
                fullname+'</span> paskyra atblokuota.');
                informationModal.showModal();
                selectedRow.find('.btn-unban').replaceWith(banButton);
            }, function(){
                warningModal.setContentAsText('<span class="emphasis">' +
                fullname+'</span> paskyros atblokuoti nepavyko!');
                warningModal.showModal();
            })
        }
    });

})();

