(function(){

$('#fos_user_registration_form_zipCode').focus(function(){
    if( $(this).val() == '' )
        $(this).val('LT-');
});

$('#fos_user_registration_form_zipCode').focusout(function(){
    if( $(this).val() == 'LT-' )
        $(this).val('');
});

$('#fos_user_registration_form_phoneNumber').focus(function(){
    if( $(this).val() == '' )
        $(this).val('86');
});

$('#fos_user_registration_form_phoneNumber').focusout(function(){
    if( $(this).val() == '86' )
        $(this).val('');
});

});
