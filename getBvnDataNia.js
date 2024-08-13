$(document).ready(function () {
   

    // Initialize the DataTable
    var table = $('#table').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        lengthMenu: [10, 50, 100, 500, 2000, 5000],
        dom: 'lBfrtip',
        buttons: [
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "drawCallback": function (settings) {
            closeSpinner();
        },
        "ajax": {
            url: "https://v2.v2nmobile.com/BVNPORTAL_API/api/getBvnDataNia.php",
            type: "GET",
            "dataSrc": function (json) {
                return json.data; 
            },
//            beforeSend: function () {
//                showSpinner(); // Show the spinner before the request is sent
//            },
            complete: function () {
                closeSpinner(); 
            },
            error: function () {
                closeSpinner();
            }
        },
        columns: [
            {data: 0, title: 'PHONE NUMBER'},
            {data: 1, title: 'USSD CONTENT'},
            {data: 2, title: 'TIMESTAMP'},
            {data: 3, title: 'OPERATOR'}
        ]
    });
});



//
//"drawCallback": function (settings) {
//            closeSpinner();
//        },
//
//
//
//
//$(document).ready(function () {
//    var table = $('#table').DataTable({
//        processing: true,
//        serverSide: true,
//        searching: true,
//        dom: 'Bfrtip',
//        buttons: [
//            'excelHtml5',
//            'csvHtml5',
//            'pdfHtml5'
//        ],
//        ajax: {
//            url: "https://v2.v2nmobile.com/BVNPORTAL_API/api/getBvnDataNia.php",
//            type: "GET",
//            data: function (d) {
//                // Include custom parameters in the search object
//                d.search = {
//                    value: $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD') + ' - ' + $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD') + '; ' + $('#operator').val(),
//                    regex: false
//                };
//            },
//            complete: function () {
//                // Ensure the processing indicator is hidden after data load
//                closeSpinner(); // Hide the processing spinner if used
//            }
//        },
//        drawCallback: function (settings) {
//            // Ensure spinner is closed after drawing the table
//            closeSpinner();
//        },
//        columns: [
//            { data: 0 }, // Adjust based on your data structure
//            { data: 1 },
//            { data: 2 },
//            { data: 3}
//        ]
//    });
//
//    // Initialize Date Range Picker
//    $('#reportrange').daterangepicker({
//        startDate: moment().subtract(29, 'days'),
//        endDate: moment(),
//        locale: {
//            format: 'YYYY-MM-DD'
//        }
//    }, function(start, end) {
//        $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
//        searchInit(); // Refresh the DataTable when date range changes
//    });
//
//    // Initialize the DataTable with the default search parameters
//    searchInit();
//});
//
//function searchInit() {
//    $('#table').DataTable().ajax.reload();
//}
