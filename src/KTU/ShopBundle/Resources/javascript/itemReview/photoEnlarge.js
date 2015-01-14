/**
 * Created by MV on 2014.09.28.
 */

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

function indicateEnlargeable(is ,element){
    var glass = $(element).children(".magGlassIcon");
    var photo = $(element).children(".itemPhoto");

    if(is){
        $(glass).fadeIn(300);
        $(photo).css("cursor","pointer");
    }
    else
    {
        $(glass).fadeOut(300);
        $(photo).css("cursor","default");
    }
}

function itemPhotoEnlarge(){

    var is;
    $(".imageWrap").on( "mouseenter", function() {
        is = isEnlargeable(this);
        indicateEnlargeable( is, this);
    }).on( "mouseleave", function() {
        if(is){
            indicateEnlargeable( false, this );
        }
    });

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


$(document).ready(function() {
    itemPhotoEnlarge();
    hideEnlarge();
});
