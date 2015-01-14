

function hideEnlarge(){
    $(".enlarged").on("click", function(){
        $(this).hide().html("");
    });
}

function Enlarge(element){
    var clone = element.clone();
    $(".enlarged").html(clone).promise().done(function(){
        $(this).fadeIn(200);
    });

}


function isEnlargeable(element){
    var is = true;
    var frameWidth = $(".imageFrame").width();
    var frameHeight = $(".imageFrame").height();
    var imgWidth = $(element).children(".itemPhoto").width();
    var imgHeight = $(element).children(".itemPhoto").height();

    if( imgHeight < frameHeight && imgWidth < frameWidth ){
        is = false;
    }

    return is;
}


function itemPhotoEnlarge(){


    $('.thumbnail').on('click', '.magGlassIcon', function() {
        var img = $(this).siblings(".itemPhoto");
        Enlarge( img );
    });

    $('.thumbnail').on('click', '.imageWrap', function() {
        var img = $(this).children('.itemPhoto');
        if( isEnlargeable( $(this) ) ){
            Enlarge( img );
        }
    });

}

function addTags(){

    var thisWrap = null;

    $( ".imageWrap" ).each(function() {
        thisWrap = $(this);

        if( isEnlargeable(thisWrap) ){
            thisWrap.addClass('enlargeable');
        }
    });

}


$(document).ready(function() {
    $(window).load(function() {
        addTags();
        itemPhotoEnlarge();
        hideEnlarge();
    });
});



