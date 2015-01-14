(function(){

    var body = $('body');
    var Table = new TableHelper('purchases', totalPages, rowsPerPage, 63);

    body.tooltip({ selector: '[data-toggle="tooltip"]' });

})();