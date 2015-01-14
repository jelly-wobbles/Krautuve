(function(){

    $('.drop').click(function(){
        $(this).siblings('ul').slideToggle(250, function(){
            var parent = $(this).parent();
            parent.find('.caret').toggleClass('up');
            parent.toggleClass('dropped');
        });
    });

})();