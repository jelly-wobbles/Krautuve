
function fillBackground(color, val, timer){
    $('.starsContainer')
        .css('background-image','linear-gradient(left, '+color+', '+color+', transparent '+val+'%, transparent 100%)')
        .css('background-image','-webkit-linear-gradient(left, '+color+', '+color+' '+val+'%, transparent '+val+'%, transparent 100%)')
        .animate({
            opacity: '1'
        }, timer);
}

function mouseOverEffect(){
    $(".bg").on('mouseover', function(){
        $(this).parents('.starsContainer').css('background','transparent');
        $(this).css('background','#00EE00');
        $(this).prevAll().css('background','#00EE00');
        $(this).nextAll().css('background','transparent');
    });
}


function mouseOutEffect(color, val){
    $(".bg").on('mouseout',function(){
        $(".bg").css('background','transparent');

        fillBackground(color, val, 0);
    });
}

function showContent(check){
    if( check == true ){
        $('.starsContainer, .rating, #usersRating, .ratingP, .usersRatingP').animate({
            opacity: '1'
        }, 300);
    }
    else{
        $('.starsContainer, .rating, #usersRating, .ratingP, .usersRatingP').animate({
            opacity: '0'
        }, 300);
        $(".noRatingsP").fadeOut();
    }
}

function indicateProgress(check){
    if( check == true ){
        $("body, .bg").css("cursor","wait");
    }
    else{
        $("body").css("cursor","default");
        $(".bg").css("cursor","pointer");
    }
}


function rateEffects(val, url){

    var val = Math.floor(val * 10);
    var color = '#00BB00';

    fillBackground(color, val, 1000);
    mouseOverEffect();
    mouseOutEffect(color, val);


    $(".bg").on('click', function(){

        indicateProgress(true);
        showContent(false);

        var index = $( ".bg" ).index( this );
        index += 1;


            $.ajax({
                type: "POST",
                url: url,
                cache: "false",
                data: {
                    value: index
                },
                success: function( data ) {

                    val = Math.floor(data * 10);
                    $(".rating").html((val/10) + " / 10");
                    $("#usersRating").html(index);

                    $.when( fillBackground(color, val, 0) ).done(function () {
                        showContent(true);
                        indicateProgress(false);
                        mouseOutEffect(color, val);
                    });

                },
                error: function( data ) {
                    console.log( "error: " + data );
                }
            });

    });

}
