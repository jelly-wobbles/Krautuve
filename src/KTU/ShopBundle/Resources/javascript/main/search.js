/**
 * Created by MV on 2014.09.28.
 */
function search(src){
    var keyword = "";
    var searchButton = $(".searchButton");
    var searchInput = $(".searchInput");
    var url = '/krautuve/web/search/';

    $("body").keypress(function(event){

        if (event.keyCode == 10 || event.keyCode == 13) {

            event.preventDefault();

            keyword = $(searchInput).val();

            if( keyword.replace(/ /g,'') != "" )
            {
                $.trim( keyword );
                $(searchButton).html("<img class='glyphicon' src='"+ src+ "'></img>");
                url = url + keyword;
                location.href =  url;
            }
            else{
                var clear = false;
                $(searchInput).css("border-color","red");
                $(searchButton).css("border-color","red");
                $(searchInput).attr('placeholder','Įveskite raktažodį');
                $(searchInput).keyup(function() {
                    $(searchInput).css("border-color","rgba(126, 239, 104, 0.5)");
                    $(searchButton).css("border-color","rgba(126, 239, 104, 0.5)");
                    $(searchInput).attr('placeholder','raktažodis');
                    clear = true;
                });
                if( clear == false ){
                    setTimeout(function() {
                        $(searchInput).css("border-color","rgba(126, 239, 104, 0.5)");
                        $(searchButton).css("border-color","rgba(126, 239, 104, 0.5)");
                        $(searchInput).attr('placeholder','raktažodis');
                    }, 5000);
                }

            }

        }

    });

    $(searchButton).click(function(){
        keyword = $(searchInput).val();


        if( keyword.replace(/ /g,'') != "" )
        {
            $.trim( keyword );
            $(searchButton).html("<img class='glyphicon' src='"+ src+ "'></img>");
            url = url + keyword;
            location.href =  url;
        }
        else{
            var clear = false;
            $(searchInput).css("border-color","red");
            $(searchButton).css("border-color","red");
            $(searchInput).attr('placeholder','Įveskite raktažodį');
            $(searchInput).keyup(function() {
                $(searchInput).css("border-color","rgba(126, 239, 104, 0.5)");
                $(searchButton).css("border-color","rgba(126, 239, 104, 0.5)");
                $(searchInput).attr('placeholder','raktažodis');
                clear = true;
            });
            if( clear == false ){
                setTimeout(function() {
                    $(searchInput).css("border-color","rgba(126, 239, 104, 0.5)");
                    $(searchButton).css("border-color","rgba(126, 239, 104, 0.5)");
                    $(searchInput).attr('placeholder','raktažodis');
                }, 5000);
            }

        }

    });


}


