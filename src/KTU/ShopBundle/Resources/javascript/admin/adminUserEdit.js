(function(){

    var confirmationModal = new ConfirmationModal();
    var informationModal = new InformationModal();
    var warningModal = new WarningModal();
    var userEditor = new UserEditor({'remove': removeUserURL, 'ban': banUserURL, 'unban': unbanUserURL, 'activate': activateURL });

    var body = $('body');
    var form = $('.shop_user_edit_form');
    var fullname = form.find('#shop_user_edit_name').val()+" "
        +form.find('#shop_user_edit_name').val();
    var email = form.find('#shop_user_edit_email').val();

    var banBtn = '<button id="opt-ban" class="btn btn-default btn-opt" type="button">' +
            'Blokuoti <span class="glyphicon glyphicon-ban-circle"></span>' +
            '</button>';

    var unbanBtn = '<button id="opt-unban" class="btn btn-default btn-opt" type="button">' +
            'Atblokuoti <span class="glyphicon glyphicon-ok-circle"></span>' +
            '</button>';

    informationModal.setTitle('Operacija sėkminga');
    confirmationModal.setTitle('Patvirtinti');
    warningModal.setTitle('Klaida');

    $('#opt-remove').click(function(){
        confirmationModal.setContentAsText('Ar tikrai norite ištrinti ' +
        '<span class="emphasis">'+fullname+'</span> paskyrą?');
        confirmationModal.setIntentions('delete');
        confirmationModal.showModal();
    });

    $('#opt-activate').click(function(){
        confirmationModal.setContentAsText('Ar tikrai norite aktyvuoti ' +
        '<span class="emphasis">'+fullname+'</span> paskyrą?');
        confirmationModal.setIntentions('activate');
        confirmationModal.showModal();
    });

    body.on('click', '#opt-ban', function(){
        confirmationModal.setContentAsText('Ar tikrai norite užblokuoti ' +
        '<span class="emphasis">'+fullname+'</span> paskyrą?');
        confirmationModal.setIntentions('ban');
        confirmationModal.showModal();
    });

    body.on('click', '#opt-unban', function(){
        confirmationModal.setContentAsText('Ar tikrai norite atblokuoti ' +
        '<span class="emphasis">'+fullname+'</span> paskyrą?');
        confirmationModal.setIntentions('unban');
        confirmationModal.showModal();
    });

    body.on('modal-confirm-event', function(e, intention){
        confirmationModal.hideModal();

        if(intention == 'delete'){
             userEditor.remove(email, function(){
             informationModal.setContentAsText('<span class="emphasis">' +
             fullname+'</span> paskyra ištrinta.');
             informationModal.setIntentions('afterDelete');
             informationModal.showModal();
             }, function(){
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
                $('#opt-ban').replaceWith(unbanBtn);
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
                $('#opt-unban').replaceWith(banBtn);
            }, function(){
                warningModal.setContentAsText('<span class="emphasis">' +
                fullname+'</span> paskyros atblokuoti nepavyko!');
                warningModal.showModal();
            })
        }
        else if(intention == 'activate'){
            userEditor.activate(email, function(){
                informationModal.setContentAsText('<span class="emphasis">' +
                fullname+'</span> paskyra aktyvuota.');
                informationModal.showModal();
                $('#opt-activate').addClass('disabled');
            }, function(){
                warningModal.setContentAsText('<span class="emphasis">' +
                fullname+'</span> paskyros aktyvuoti nepavyko!');
                warningModal.showModal();
            })
        }
    });

    body.on('modal-ok-event', function(e, intention){
        if(intention == 'afterDelete'){
            window.location.replace(usersURL);
        }
    });

})();


