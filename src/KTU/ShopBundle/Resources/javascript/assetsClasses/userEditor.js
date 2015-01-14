//-------------------------------------------------------------------------------------------//
//     Start of UserEditor Class
//-------------------------------------------------------------------------------------------//

function UserEditor(urls){

    var actionURL = {}; // array of action URLS

    /**
     * Set url post ajax requests
     * Each method take the url with his name e.g.
     * Remove method will take actionURl['remove'] url for ajax request
     * @param object urls
     */
    this.setActionURL = function(urls){
        $.each( urls, function( key, value ) {
            actionURL[key] = value;
        });
    };

    // Sets urls given as argument
    this.setActionURL(urls);

    /**
     * Send a request to remove the user
     *
     * @param string email
     * @param function callback
     * @param function fallback
     */
    this.remove = function(email, callback, fallback){

        $("body").css("cursor", "wait");
        $.ajax({
            url: actionURL['remove'],
            type: 'POST',
            data: {
                userEmail: email
            },
            success: function(data){
                callback(data);
                $("body").css("cursor", "default");
            },
            error: function(data){
                fallback(data);
                $("body").css("cursor", "default");
            }
        });
    };

    /**
     * Send a request to ban the user
     *
     * @param string email
     * @param function callback
     * @param function fallback
     */
    this.ban = function(email, callback, fallback){

        $("body").css("cursor", "wait");
        $.ajax({
            url: actionURL['ban'],
            type: 'POST',
            data: {
                userEmail: email
            },
            success: function(data){
                callback(data);
                $("body").css("cursor", "default");
            },
            error: function(data){
                fallback(data);
                $("body").css("cursor", "default");
            }
        });

    };

    /**
     * Sends a request to unban the user
     *
     * @param string email
     * @param function callback
     * @param function fallback
     */
    this.unban = function(email, callback, fallback){

        $("body").css("cursor", "wait");
        $.ajax({
            url: actionURL['unban'],
            type: 'POST',
            data: {
                userEmail: email
            },
            success: function(data){
                callback(data);
                $("body").css("cursor", "default");
            },
            error: function(data){
                fallback(data);
                $("body").css("cursor", "default");
            }
        });

    };

    /**
     * Sends a request to activate the user
     *
     * @param string email
     * @param function callback
     * @param function fallback
     */
    this.activate = function(email, callback, fallback){

        $("body").css("cursor", "wait");
        $.ajax({
            url: actionURL['activate'],
            type: 'POST',
            data: {
                userEmail: email
            },
            success: function(data){
                callback(data);
                $("body").css("cursor", "default");
            },
            error: function(data){
                fallback(data);
                $("body").css("cursor", "default");
            }
        });

    }

}

//-------------------------------------------------------------------------------------------//
//     End of UserEditor Class
//-------------------------------------------------------------------------------------------//