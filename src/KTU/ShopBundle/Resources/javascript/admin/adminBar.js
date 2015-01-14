(function(){

    var mini = $('.admin-nav').hasClass('minimize-admin-bar');

    $('.admin-nav-resize').click(function(){

        $(this).parent().toggleClass('minimize-admin-bar');
        if(mini){
            mini=false;
            $(this).find('.glyphicon-resize-full').removeClass('glyphicon-resize-full').addClass('glyphicon-resize-small');
            $.ajax({
                url: navNormalURL,
                type: 'POST'
            });
        }
        else{
            mini=true;
            $(this).find('.glyphicon-resize-small').removeClass('glyphicon-resize-small').addClass('glyphicon-resize-full');
            $.ajax({
                url: navMiniURL,
                type: 'POST'
            });
        }
    });

})();
