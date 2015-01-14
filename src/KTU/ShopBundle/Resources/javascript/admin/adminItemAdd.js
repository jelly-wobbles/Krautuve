(function(){

    var imgArray = [];
    var ind = 0;

    var body = $('body');
    var progressModal = new ProgressModal();

    var removeBtn = '<div class="remove-btn-wrap">'+
    '<button type="button" class="btn btn-default remove-field remove-specs">' +
    '<span class="glyphicon glyphicon-remove"></span></button>'+
    '</div>';

    // Remove the title from choose file input
    if (window.webkitURL)
        $('input[type="file"]').attr('title', ' ');
    else
        $('input[type="file"]').attr('title', '');


    $('.add-specs').click(function(){

        var newSpecName = generateField(specsNamesPrototype);
        var newSpecValue = generateField(specsValuesPrototype);

        var row = '<div class="specs-value"><div class="input-wrap input-names">'+
                  newSpecName+'</div><div class="input-wrap input-values">'+newSpecValue+'' +
                  '</div>'+removeBtn+
                  '</div>';

        var rowElement = $(row);
        rowElement.appendTo('.specs-values');

        specsCount++;

        if(specsCount == 1){
            $('.specs-labels').slideDown(150,function(){
                rowElement.slideDown(250);
            });
        }
        else{
            rowElement.slideDown(250);
        }

    });

    body.on('click', '.remove-upload', function(){
        var ind = $(this).parent().attr('data-upload-index');
        imgArray[ind] = null;

        $(this).parent().hide(250, function(){
            $(this).remove();

            if($('.upload-info').size() == 0){
                $('.upload-text').show(500);
            }
        });

    });

    body.on('click', '.remove-specs', function(){

        specsCount--;

        if(specsCount == 0){
            $(this).parent().parent().slideUp(250,function(){
                $(this).remove();
                $('.specs-labels').slideUp(150);
            });
        }
        else{
            $(this).parent().parent().slideUp(250,function(){
                $(this).remove();
            });
        }

    });

    $('.add-images').change(function(e){
        handleFiles(e);
        // reset the input
        var input = $(this);
        input.replaceWith(input.val('').clone(true));
    });

    // We need native javascript here because jQuery messes up the events
    var uploadZone = document.getElementsByClassName('upload-zone')[0];

    uploadZone.addEventListener('dragover', handleDragOver, false);
    uploadZone.addEventListener('drop', handleDroppedFiles, false);

    function generateField(prototype){

        var newInput = prototype;
        newInput =  newInput.replace(/__name__/g, '');

        return newInput;
    }

    function handleFiles(e){

        // Check for the various File API support.
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            var files = e.target.files; // FileList object
            renderFiles(files);
        }

        return false;
    }

    function handleDroppedFiles(e){

        e.stopPropagation();
        e.preventDefault();

        // Check for the various File API support.
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            var files = e.dataTransfer.files; // FileList object.
            renderFiles(files);
        }

        return false;
    }

    function renderFiles(files){
        for (var i = 0, f; f = files[i]; i++) {
            // Only process image files.
            if (!f.type.match('image.*')) {
                continue;
            }

            var reader = new FileReader();

            reader.onload = (function(file) {
                return function(e) {

                    // Render thumbnail.
                    var imgContainer = $('<div class="upload-info" data-upload-index="'+ind+'">' +
                    '<div class="img-container"><img class="upload-thumb" ' +
                    'src="'+ e.target.result+'"></img></div>' +
                    '<p class="file-name">'+ file.name +'</p>' +
                    '<button type="button" class="btn btn-default remove-upload">Pašalinti</button>' +
                    '</div>');
                    $('.rendered-uploads').append(imgContainer);

                    imgArray[ind++] = file;
                    if(imgArray.length > 0){
                        $('.upload-text').hide();
                    }

                    $(imgContainer).show(250);
                };
            })(f);

            // Read in the image file as a data URL.
            reader.readAsDataURL(f);
        }
    }

    function handleDragOver(e){
        e.stopPropagation();
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';   // Show this as copy
    }

    $('form').submit(function(e){
        e.preventDefault();
        progressModal.showModal();
        ajaxPersistData();
    });

    function ajaxPersistData(){

        var formData = new FormData(document.getElementsByName('shop_item_add')[0]);

        $.each(imgArray, function(i, val){
            formData.append('shop_item_add[uploaded][]', val);
        });

        $.ajax({
            url: persistDataURL,
            data: formData,
            processData: false,
            contentType: false,
            type: 'POST',
            success: function(response) {
                if (response['success'] == true) {
                    progressModal.hideModal();
                    window.location.replace(response['redirectURL']);
                }
                else {
                    progressModal.hideModal();
                    manageErrors(response['errors']);
                }
            }
        });

    }


    function manageErrors(errors){

        var errorPrototype = '<ul class="help-block"><li>__error__</li></ul>';
        var group = null;

        if(errors['images'] != false){
            $.each(errors['images'], function(i, val){
                if(val != null){
                    var file = $('.upload-info:eq('+i+')');
                    file.find('.invalid-text').remove();
                    file.removeClass('invalid-upload');
                    file.addClass('invalid-upload');
                    file.append('<div class="invalid-text">'+val+'</div>');
                }
            });
        }

        if(errors['name'] != false){
            group = $('.form-group:eq(0)');
            group.addClass('has-error');
            group.find('.help-block').remove();
            group.append(errorPrototype.replace(/__error__/g, errors['name']));
        }
        else{
            group = $('.form-group:eq(0)');
            group.removeClass('has-error');
            group.find('.help-block').remove();
        }

        if(errors['category'] != false){
            group = $('.form-group:eq(1)');
            group.addClass('has-error');
            group.find('.help-block').remove();
            group.append(errorPrototype.replace(/__error__/g, errors['category']));
        }
        else{
            group = $('.form-group:eq(1)');
            group.removeClass('has-error');
            group.find('.help-block').remove();
        }

        if(errors['price'] != false){
            group = $('.form-group:eq(2)');
            group.addClass('has-error');
            group.find('.help-block').remove();
            group.append(errorPrototype.replace(/__error__/g, errors['price']));
        }
        else{
            group = $('.form-group:eq(2)');
            group.removeClass('has-error');
            group.find('.help-block').remove();
        }

        if(typeof(errors['specs_names']) !== 'undefined'){
            $.each(errors['specs_names'], function(i, val){
                if(val != ''){
                    group = $('.input-names:eq('+i+')');
                    groupParent = group.parent();
                    group.addClass('has-error');

                    group.find('.help-block').remove();
                    group.parent().removeClass('help-push');

                    group.append(errorPrototype.replace(/__error__/g, val));
                    group.parent().addClass('help-push');
                }
                else{
                    group = $('.input-names:eq('+i+')');
                    group.removeClass('has-error');
                    group.parent().removeClass('help-push');
                    group.find('.help-block').remove();
                }
            });
        }

        if(typeof(errors['specs_values']) !== 'undefined'){
            $.each(errors['specs_values'], function(i, val){
                if(val != ''){
                    group = $('.input-values:eq('+i+')');
                    group.addClass('has-error');
                    group.find('.help-block').remove();
                    group.append(errorPrototype.replace(/__error__/g, val));
                }
                else{
                    group = $('.input-values:eq('+i+')');
                    group.removeClass('has-error');
                    group.find('.help-block').remove();
                }
            });
        }

        if(errors['description'] != false){
            group = $('.form-group:eq(4)');
            group.addClass('has-error');
            group.find('.help-block').remove();
            group.append(errorPrototype.replace(/__error__/g, errors['description']));
        }
        else{
            group = $('.form-group:eq(4)');
            group.removeClass('has-error');
            group.find('.help-block').remove();
        }
    }


})();

