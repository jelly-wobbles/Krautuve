(function(){

    var imgArray = [];
    var imgRemove = [];
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

            if($('.upload-info').size() == 0 && $('.uploaded-info').size() == 0){
                $('.upload-text').show(500);
            }
        });

    });

    body.on('click', '.remove-uploaded', function(){

        var path = $(this).siblings('.img-container').attr('data-path');
        imgRemove.push(path);

        $(this).parent().hide(250, function(){
            $(this).remove();

            if($('.upload-info').size() == 0 && $('.uploaded-info').size() == 0){
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
        ajaxUpdateData();
    });

    function ajaxUpdateData(){

        var formData = new FormData(document.getElementsByName('shop_item_edit')[0]);

        $.each(imgArray, function(i, val){
            formData.append('shop_item_edit[uploaded][]', val);
        });

        $.each(imgRemove, function(i, val){
            formData.append('shop_item_edit[remove][]', val);
        });

        $.ajax({
            url: updateDataURL,
            data: formData,
            processData: false,
            contentType: false,
            type: 'POST',
            success: function(response){
                if(response['success'] == true){
                    progressModal.hideModal();
                    window.location.replace(redirectURL);
                }
                else{
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


    if($('.discount-info').length > 0){
        $('#shop_item_edit_price').keyup(function(){
            var newPrice = $(this).val() * (1 - ($('#discount').text() / 100 )) ;
            $('#discount-price').text(number_format(newPrice, 2,',',' ')+'\u20AC');
        });
    }

    function number_format (number, decimals, dec_point, thousands_sep) {
        // Strip all characters but numerical ones.
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

})();
