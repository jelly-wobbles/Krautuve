//-------------------------------------------------------------------------------------------//
//     Start of TableHelper Class
//-------------------------------------------------------------------------------------------//

function TableHelper(tableId, totalPages, rowsPerPage, rowHeight)
{
    // ----------------------
    // table information
    // ----------------------
    var Pagination = new TablePagination(totalPages);
    var _this = this;
    var totalObjects = $('#total-objects').text();
    var objectCountStart = $('#objects-count-start');
    var objectCountEnd = $('#objects-count-end');

    // ----------------------
    // private functions
    // ----------------------

    var setPreloaderHeight = function(height){
        $('.preloader-container').height(height);
    };

    var initPreloader = function(){
        $('<div class="preloader-container" style="display:none; width: 100%;"><div class="preloader"></div></div>').insertAfter('.table');
    }

    var showPreloader = function(){
        $('.preloader-container').show();
    }

    var hidePreloader = function(){
        $('.preloader-container').hide();
    }

    // disabled = false , enaled = true
    var paginationState = function(bool){
        if(bool){
            $('.pagination').removeClass('disabled');
        }
        else{
            $('.pagination').addClass('disabled');
        }
    };

    var setPaginationInformation = function(start, size){
        var end = start+size;
        objectCountStart.text(start+1);
        if(end > totalObjects){
            objectCountEnd.text(totalObjects);
        }
        else{
            objectCountEnd.text(end);
        }
    };

    // ----------------------
    // public functions
    // ----------------------
    this.sendRequest = function(page){

        $('#'+tableId+' tr:not(.table-header)').remove();

        setPreloaderHeight(rowHeight*(objectCountEnd.text()-objectCountStart.text()+1));
        showPreloader();
        paginationState(false);

        var start = (page-1)*rowsPerPage;
        var size = rowsPerPage;

        $.ajax({
            url: tableDataURL,
            type: 'POST',
            data: {
                start: start,
                size: size
            },
            success: function(data){
                hidePreloader();
                $(data).insertAfter('#'+tableId+' .table-header');
                setPaginationInformation(start, size);
                paginationState(true);
            },
            error: function(data){
                hidePreloader();
                paginationState(true);
            }
        });

    };


    // ----------------------
    // init actions
    // ----------------------
    initPreloader();
    this.sendRequest(1);

    // ----------------------
    // event listeners
    // ----------------------
    $('body').on('tablePageChanged', function(e, page){
        _this.sendRequest(page);
    });

}

//-------------------------------------------------------------------------------------------//
//      End of TableHelper Class
//-------------------------------------------------------------------------------------------//
//-------------------------------------------------------------------------------------------//
//     Start of TablePagination Class
//-------------------------------------------------------------------------------------------//

function TablePagination(totalPages)
{
    // ----------------------
    // pagination information
    // ----------------------
    var currentPage = 1;
    var totalPages = totalPages;

    // ----------------------
    // controls
    // ----------------------
    var first = $('.pagination #table-first');
    var back = $('.pagination #table-back');
    var next = $('.pagination #table-next');
    var last = $('.pagination #table-last');
    var page = $('.pagination .page');

    // ----------------------
    // separators
    // ----------------------
    var separator = $('.pagination .pagination-separator');
    var separator2 = $('<li class="disabled pagination-separator2">...</li>');
    var separatorsNumber = separator.length;

    // ----------------------
    // private functions
    // ----------------------
    var renumber = function(numbers){
        $('.pagination .page').each(function(i){
            $(this).text(numbers[i]);
        });
    };

    var isInMiddle = function(){
        if((2 < currentPage) && (currentPage < totalPages-1)){
            return true;
        }

        return false;
    };

    var isLast = function(offset){

        offset = typeof offset !== 'undefined' ? totalPages-offset : totalPages;

        if(currentPage == offset)
            return true;
        
        return false;
    };
    
    var isFirst = function(offset){

        offset = typeof offset !== 'undefined' ? offset+1 : 1;

        if(currentPage == offset)
            return true;

        return false;
    };

    var isDisabled = function(el){
        return (el.hasClass('disabled') || $('.pagination').hasClass('disabled'));
    };
    
    var triggerPageChangeEvent = function(){
        $('body').trigger('tablePageChanged', currentPage);
    };

    // ----------------------
    // public functions
    // ----------------------

    this.getCurrentPage = function(){
        return currentPage;
    };

    // ----------------------
    // events
    // ----------------------
    first.click(function(){
        if(!isDisabled($(this))) {
            $('.pagination .active').removeClass('active');
            $('.pagination .page:eq(0)').addClass('active');
            separatorsNumber = 1;
            separator2.remove();
            separator.detach().insertBefore('.pagination .page:eq(3)');

            currentPage = 1;
            triggerPageChangeEvent();
            renumber([1, 2, 3, totalPages - 1, totalPages]);

            back.addClass('disabled');
            first.addClass('disabled');
            last.removeClass('disabled');
            next.removeClass('disabled');
        }
    });

    last.click(function(){
        if(!isDisabled($(this))) {
            $('.pagination .active').removeClass('active');
            $('.pagination .page:eq(4)').addClass('active');
            separatorsNumber = 1;
            triggerPageChangeEvent();
            separator2.remove();
            separator.detach().insertBefore('.pagination .page:eq(2)');

            currentPage = totalPages;
            renumber([1, 2, parseInt(totalPages) - 2, totalPages - 1, totalPages]);

            last.addClass('disabled');
            next.addClass('disabled');
            back.removeClass('disabled');
            first.removeClass('disabled');
        }
    });

    back.click(function(){
        if(!isDisabled($(this))) {
            var prevPage = $('.pagination .active');
            var nextPage = prevPage.prev('.page');
            currentPage = nextPage.text();
            triggerPageChangeEvent();

            if(separatorsNumber > 0){
                if (isLast(2)) {
                    if (separatorsNumber == 1 && isInMiddle()) {
                        separatorsNumber = 2;
                        separator2.insertBefore('.pagination .page:eq(1)');
                        separator.detach().insertBefore('.pagination .page:eq(4)');
                        prevPage.removeClass('active');
                        nextPage.addClass('active');
                    }
                    renumber([1, currentPage - 1, currentPage, parseInt(currentPage) + 1, totalPages]);
                }
                else {
                    if (isInMiddle()) {
                        renumber([1, currentPage - 1, currentPage, parseInt(currentPage) + 1, totalPages]);
                    }
                    else if (isFirst(1)) {
                        separatorsNumber = 1;
                        separator2.remove();
                        separator.detach().insertBefore('.pagination .page:eq(3)');
                        prevPage.removeClass('active');
                        nextPage.addClass('active');
                        renumber([1, 2, 3, totalPages - 1, totalPages]);
                    }
                    else {
                        prevPage.removeClass('active');
                        nextPage.addClass('active');
                    }
                }
            }
            else {
                prevPage.removeClass('active');
                nextPage.addClass('active');
            }

            if (isFirst()) {
                back.addClass('disabled');
                first.addClass('disabled');
            }
            else if (!isLast()) {
                last.removeClass('disabled');
                next.removeClass('disabled');
            }
        }
    });

    next.click(function(){
        if(!isDisabled($(this))) {
            var prevPage = $('.pagination .active');
            var nextPage = prevPage.next('.page');
            currentPage = nextPage.text();
            triggerPageChangeEvent();

            if(separatorsNumber > 1){
                if (isFirst(2)) {
                    if (separatorsNumber == 1 && isInMiddle()) {
                        separatorsNumber = 2;
                        separator2.insertBefore('.pagination .page:eq(1)');
                        separator.detach().insertBefore('.pagination .page:eq(4)');
                        prevPage.removeClass('active');
                        nextPage.addClass('active');
                    }
                    renumber([1, currentPage - 1, currentPage, parseInt(currentPage) + 1, totalPages]);
                }
                else {
                    if (isInMiddle()) {
                        renumber([1, currentPage - 1, currentPage, parseInt(currentPage) + 1, totalPages]);
                    }
                    else if (isLast(1)) {
                        separatorsNumber = 1;
                        separator2.remove();
                        separator.detach().insertBefore('.pagination .page:eq(2)');
                        prevPage.removeClass('active');
                        nextPage.addClass('active');
                        renumber([1, 2, currentPage - 1, currentPage, totalPages]);
                    }
                    else {
                        prevPage.removeClass('active');
                        nextPage.addClass('active');
                    }
                }
            }
            else{
                prevPage.removeClass('active');
                nextPage.addClass('active');
            }

            if (isLast()) {
                last.addClass('disabled');
                next.addClass('disabled');
            }
            else if (!isFirst()) {
                back.removeClass('disabled');
                first.removeClass('disabled');
            }
        }
    });

    page.click(function(){
        if(!isDisabled($(this))){
            page = $(this).text();

            if(separatorsNumber > 0){
                if((parseInt(currentPage)+1 == page || currentPage-1 == page) && separatorsNumber == 2){
                    renumber([1, page-1, page, parseInt(page)+1, totalPages]);
                }
                else{
                    $('.pagination .active').removeClass('active');
                    $(this).addClass('active');
                }

                currentPage = page;
                triggerPageChangeEvent();

                if(isLast()){
                    last.addClass('disabled');
                    next.addClass('disabled');
                    separatorsNumber = 1;
                    separator2.remove();
                    $('.pagination-separator').detach().insertBefore('.pagination .page:eq(2)');
                    renumber([1,2, parseInt(totalPages)-2, totalPages-1, totalPages]);
                }
                else if(isFirst()){
                    back.addClass('disabled');
                    first.addClass('disabled');
                    separatorsNumber = 1;
                    separator2.remove();
                    $('.pagination-separator').detach().insertBefore('.pagination .page:eq(3)');
                    renumber([1,2, 3, totalPages-1, totalPages]);
                }

                if(isFirst(1)){
                    separatorsNumber = 1;
                    separator2.remove();
                    $('.pagination-separator').detach().insertBefore('.pagination .page:eq(3)');
                    renumber([1,2,3, totalPages-1, totalPages]);
                    $('.pagination .active').removeClass('active');
                    $(this).addClass('active');
                    return;
                }

                if(isLast(1)){
                    separatorsNumber = 1;
                    separator2.remove();
                    $('.pagination-separator').detach().insertBefore('.pagination .page:eq(2)');
                    renumber([1,2, totalPages-2, totalPages-1, totalPages]);
                    $('.pagination .active').removeClass('active');
                    $(this).addClass('active');
                    return;
                }

                if(isFirst(2) || isLast(2)){
                    separatorsNumber = 2;
                    separator2.insertBefore('.pagination .page:eq(1)');
                    separator.detach().insertBefore('.pagination .page:eq(4)');
                    renumber([1, currentPage-1, currentPage, parseInt(currentPage)+1, totalPages]);
                }
            }
            else{
                currentPage = page;
                triggerPageChangeEvent();

                $('.pagination .active').removeClass('active');
                $(this).addClass('active');

                if(isLast()){
                    last.addClass('disabled');
                    next.addClass('disabled');
                }
                else if(isFirst()){
                    back.addClass('disabled');
                    first.addClass('disabled');
                }

            }

            if(!isFirst()){
                back.removeClass('disabled');
                first.removeClass('disabled');
            }

            if(!isLast()){
                last.removeClass('disabled');
                next.removeClass('disabled');
            }
        }
    });


}

//-------------------------------------------------------------------------------------------//
//      End of TablePagination Class
//-------------------------------------------------------------------------------------------//