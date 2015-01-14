//-------------------------------------------------------------------------------------------//
//     Start of TableSelector Class
//-------------------------------------------------------------------------------------------//

function TableSelector(tableSelector, mainBoxSelector, rowBoxesSelector){
    var table = $(tableSelector);
    var mainBox = table.find(mainBoxSelector);
    var rowBoxes = table.find(rowBoxesSelector);
    var all = rowBoxes.size();
    var selected = 0;

    /**
     * Gets selected table rows
     *
     * @returns {jQuery}
     */
    this.getSelectedRows = function(){
        return $(rowBoxesSelector+':checked').parents('tr');
    };

    /**
     *
     * Selects all table rows
     */
    var selectAll = function(){
        rowBoxes.prop('checked', true);
        selected = all;
    };

    /**
     *
     * Deselects all table rows
     */
    var deselectAll = function(){
        rowBoxes.prop('checked', false);
        selected = 0;
    };

    /**
     *
     * Checks if all table rows are selected
     *
     * @returns {boolean}
     */
    var isAllSelected = function(){
        if(all == selected)
            return true;

        return false;
    };

    // Events

    // If mainBox is checked all table rows are selected
    // else all table rows are deselected
    mainBox.change(function(){
        if(mainBox.prop('checked') == true) {
            selectAll();
            $(rowBoxesSelector+':checked').parents('tr').addClass('selected-row');
        }
        else {
            deselectAll();
            $(rowBoxesSelector+':not(:checked)').parents('tr').removeClass('selected-row');
        }
    });

    // Select/deselects one row
    rowBoxes.change(function(){

        if($(this).prop('checked') == true){
            $(this).parents('tr').addClass('selected-row');
            selected++;
        }
        else{
            $(this).parents('tr').removeClass('selected-row');
            selected--;
        }

        // if all rows are selected checks mainBox else deselects mainBox
        if(isAllSelected())
            mainBox.prop('checked', true);
        else
            mainBox.prop('checked', false);

    });

}

//-------------------------------------------------------------------------------------------//
//      End of TableSelector Class
//-------------------------------------------------------------------------------------------//
