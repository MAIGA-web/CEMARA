(function ($) {
    "use strict";

    /* Data Table
    -------------*/
    
    if ($('#bootstrap-data-table').length) {
        $('#bootstrap-data-table').DataTable({
            lengthMenu: [[10, 20, 50, -1], [10, 20, 50, "Tout"]],
            pagingType: "full_numbers", // <-- Résout le problème 'paging action: next'
            destroy: true // Évite les conflits si le tableau est réinitialisé
        });
    }

    // Export Table
    if ($('#bootstrap-data-table-export').length) {
        $('#bootstrap-data-table-export').DataTable({
            dom: 'lBfrtip',
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tout"]],
            pagingType: "full_numbers",
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    }
    
    // Row Select Table
    if ($('#row-select').length) {
        $('#row-select').DataTable({
            pagingType: "full_numbers",
            initComplete: function () {
                this.api().columns().every( function () {
                    var column = this;
                    var select = $('<select class="form-control"><option value=""></option></select>')
                        .appendTo( $(column.footer()).empty() )
                        .on( 'change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
     
                            column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                        });
     
                    column.data().unique().sort().each( function ( d, j ) {
                        select.append( '<option value="'+d+'">'+d+'</option>' );
                    });
                });
            }
        });
    }

})(jQuery);