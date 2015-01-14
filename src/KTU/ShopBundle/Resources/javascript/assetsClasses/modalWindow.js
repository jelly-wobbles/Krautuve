//-------------------------------------------------------------------------------------------//
//     Start of ModalWindow Class
//-------------------------------------------------------------------------------------------//
/**
 *
 * @param className string - class name of the modal window
 * @constructor
 */
function ModalWindow(className, prototype) {

    var HTMLtemplate = '';

    if (typeof prototype === 'undefined') {
        HTMLtemplate = '<div class="modal fade '+className+'">' +
            '<div class="modal-dialog">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal">' +
            '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' +
            '<h4 class="modal-title">Some title</h4>'+
            '</div>' +
            '<div class="modal-body clearfix">' +
            '<div class="modal-sign"></div>' +
            '<div class="modal-inner-content"></div>' +
            '</div>' +
            '<div class="modal-footer">' +
            '</div>' +
            '</div></div></div>';

    }
    else{
        HTMLtemplate = prototype;
    }


    var body = $('body');
    var intention = '';

    // Signs
    this.informationSign = '<span class="glyphicon glyphicon-info-sign"></span>';
    this.warningSign = '<span class="glyphicon glyphicon-warning-sign"></span>';
    this.questionSign = '<span class="glyphicon glyphicon-question-sign"></span>';

    // Buttons
    this.confirmButton = '<button class="btn btn-default modal-confirm">Patvirtinti</button>';
    this.okButton = '<button class="btn btn-default modal-ok" data-dismiss="modal">Gerai</button>';
    this.cancelButton = '<button class="btn btn-default modal-cancel" data-dismiss="modal">Atšaukti</button>';

    // Add modal element to DOM
    body.append(HTMLtemplate);

    // Sets the title for the modal window
    this.setTitle = function(title){
        $('.'+className+' .modal-title').html(title);
    };

    // Change the modal window prototype
    this.setPrototype = function(prototype){
        HTMLtemplate  = prototype;
    };


    // Sets the buttons for modal window
    this.setButtons = function(buttons){
        var buttonsHTML = '';

        if(Array.isArray(buttons)){
            $.each(buttons, function(key, value){
                buttonsHTML += value;
            });
        }
        else{
            buttonsHTML = buttons;
        }

        $('.'+className+' .modal-footer').html(buttonsHTML);
    };

    // Sets the text for the modal window
    this.setContentAsText = function(text){
        var innerContent = $('.'+className+' .modal-inner-content');

        innerContent.css('display', 'table');
        innerContent.html('<span class="modal-content-text">'+text+'</span>');

    };

    // Sets modal Window content as html
    this.setContentAsHTML = function(content){
        var innerContent = $('.'+className+' .modal-inner-content');

        innerContent.css('display', 'block');
        innerContent.html(content);

    };

    // Sets a sign icon to the side of the modal window
    this.setSign = function(sign){
        var modalSign = $('.'+className+' .modal-sign');
        var innerContent = $('.'+className+' .modal-inner-content');

        if(sign == null){
            modalSign.html('');
            innerContent.css('width', '100%');
            modalSign.css('display', 'hidden');
        }
        else{
            modalSign.html(sign);
            innerContent.css('width', '85%');
            modalSign.css('display', 'block');
        }

    };

    // Fire Events

    body.on('click', '.'+className+' .modal-confirm', function(){
        body.trigger( "modal-confirm-event",  intention);
    });

    body.on('click', '.'+className+' .modal-ok', function(){
        body.trigger( "modal-ok-event", intention);
    });

    body.on('click', '.'+className+' .modal-cancel', function(){
        body.trigger( "modal-cancel-event",  intention);
    });

    // Set intention

    this.setIntentions = function(intent){
        intention = intent;
    };

    // Shows the modal window
    this.showModal = function(){
        $('.'+className+'.modal').modal();
    };

    this.hideModal = function(){
        $('.'+className+'.modal').modal('hide');
    };

}

//-------------------------------------------------------------------------------------------//
//      End of ModalWindow Class
//-------------------------------------------------------------------------------------------//

//-------------------------------------------------------------------------------------------//
//     Start of ConfirmationModal Class
//-------------------------------------------------------------------------------------------//

function ConfirmationModal()
{
    ModalWindow.call(this, 'confirmation-modal');

    this.setTitle('Confirmation');
    this.setSign(this.questionSign);
    this.setButtons([this.confirmButton, this.cancelButton]);

}

//-------------------------------------------------------------------------------------------//
//      End of ConfirmationModal Class
//-------------------------------------------------------------------------------------------//

//-------------------------------------------------------------------------------------------//
//     Start of InformationModal Class
//-------------------------------------------------------------------------------------------//

function InformationModal()
{
    ModalWindow.call(this, 'information-modal');

    this.setTitle('Information');
    this.setSign(this.informationSign);
    this.setButtons(this.okButton);

    this.noControls = function(){
        $('.information-modal .modal-footer').remove();
    }

}

//-------------------------------------------------------------------------------------------//
//      End of InformationModal Class
//-------------------------------------------------------------------------------------------//

//-------------------------------------------------------------------------------------------//
//     Start of WarningModal Class
//-------------------------------------------------------------------------------------------//

function WarningModal()
{
    ModalWindow.call(this, 'warning-modal');

    this.setTitle('Error');
    this.setSign(this.warningSign);
    this.setButtons(this.okButton);

}

//-------------------------------------------------------------------------------------------//
//      End of WarningModal Class
//-------------------------------------------------------------------------------------------//

//-------------------------------------------------------------------------------------------//
//     Start of ProgressModal Class
//-------------------------------------------------------------------------------------------//

function ProgressModal()
{
    var prototype = '<div class="modal fade progress-modal" data-backdrop="static">' +
        '<div class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-body clearfix">' +
        '<div class="modal-inner-content"></div>' +
        '</div>' +
        '</div>' +
        '</div></div>';

    ModalWindow.call(this, 'progress-modal', prototype);

    this.setContentAsHTML('<div class="preloader"></div><p class="progress-map"></p>');

    this.setStages = function(stages){
        var stagesString = '';

        $.each(stages, function(i, val){
            if (i == stages.length-1){
                stagesString = stagesString+'<span>'+val+'</span>';
            }
            else if(i == 0){
                stagesString = stagesString+'<span class="active-progress">'+val+'</span> / ';
            }
            else{
                stagesString = stagesString+'<span>'+val+'</span> / ';
            }
        });

        this.nextStage = function(){
            var active = $('.active-progress');
            var next = active.next();
            active.removeClass('active-progress');
            next.addClass('active-progress');
        };

        this.resetStages = function(){
            $('.active-progress').removeClass('active-progress');
            $('.progress-map span:first').addClass('active-progress');
        };

        this.setContentAsHTML('<div class="preloader"></div><p class="progress-map">'+stagesString+'</p>');

    };

}

//-------------------------------------------------------------------------------------------//
//      End of ProgressModal Class
//-------------------------------------------------------------------------------------------//