var jq = jQuery.noConflict();

var TableTasks = function () {

    var handleTable = function () {

        var oTable = table.DataTable({
            "lengthMenu": [
                [5, 10, 20, -1],
                [5, 10, 20, "All"] // change per page values here
            ],
            "pageLength": 10,

            "language": {
                "lengthMenu": " _MENU_ records"
            },
            "columnDefs": [{ // set default column settings
                'orderable': true,
                'targets': [0]
            }, {
                "searchable": true,
                "targets": [0]
            }],
            "order": [
                [0, "asc"]
            ] 
        });
    }
    
    return {
        init: function (table) {
            handleTable();
        }

    };

}();