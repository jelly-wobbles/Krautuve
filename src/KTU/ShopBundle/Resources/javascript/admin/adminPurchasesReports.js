(function(){

    var informationModal = new InformationModal();
    var warningModal = new WarningModal();

    informationModal.setTitle('Operacija sėkminga');
    warningModal.setTitle('Klaida');

    $('#shop_purchases_report_storageReport').change(function(){

        if($(this).get(0).files.length > 0){
            var fd = new FormData();
            var files = $(this).get(0).files;

            $.each(files, function(index, val){
                fd.append('attachment[]', val);
            });

            sendFiles(fd, storageReportURL);
        }

        // reset the input
        var input = $(this);
        input.replaceWith(input.val('').clone(true));
    });

    $('#shop_purchases_report_deliveryReport').change(function(){

        if($(this).get(0).files.length > 0){
            var fd = new FormData();
            var files = $(this).get(0).files;

            $.each(files, function(index, val){
                fd.append('attachment[]', val);
            });

            sendFiles(fd, deliveryReportURL);
        }

        // reset the input
        var input = $(this);
        input.replaceWith(input.val('').clone(true));
    });

    function sendFiles(data, url){
        $('body').css('cursor', 'wait');
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            processData: false,
            contentType: false,
            success: function(data){
                $('.table-body').html(data);
                $('body').css('cursor', 'default');
                informationModal.setContentAsText('Ataskaita sėkmingai įkelta.');
                informationModal.showModal();
            },
            error: function(){
                $('body').css('cursor', 'default');
                warningModal.setContentAsText('Ataskaitos įkelti nepavyko.');
                warningModal.showModal();
            }
        });
    }

})();