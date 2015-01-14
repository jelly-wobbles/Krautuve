/**
 * Created by MV on 2014.09.28.
 */
function focusEffect(){
    $(".thumbnail").on( "mouseenter", function() {
        $(".thumbnail").not(this).css("opacity","0.5");
    }).on( "mouseleave", function() {
        $(".thumbnail").css("opacity","1");
    });
}


$(document).ready(function() {
        focusEffect();
});