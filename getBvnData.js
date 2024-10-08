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
//            url: "https://v2.v2nmobile.com/BVNPORTAL_API/api/getBVNData.php",
//            type: "GET",
//            data: function (d) {
//                // Additional data processing if necessary
//            },
//            complete: function () {
//                // Ensure the processing indicator is hidden after data load
//                closeSpinner(); // Hide the processing spinner if used
//            }
//        },
//        drawCallback: function (settings) {
//            // Ensure spinner is closed after drawing the table
//            closeSpinner(); 
//        }
//    });
//
//    // Example of using the `columns` API
//    if (table && typeof table.columns === 'function') {
//        table.columns().every(function() {
//            var column = this;
//            // Your column operations here
//        });
//    } else {
//        console.error("Table is not properly initialized or `columns` method is not available.");
//    }
//});


$(document).ready(function () {


    // Initialize the DataTable
    var table = $('#table').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        lengthMenu: [10, 50, 100, 500, 2000, 5000],
        dom: 'lBfrtip',
        buttons: ['excelHtml5', 'csvHtml5', 'pdfHtml5'],
        drawCallback: function (settings) {
            closeSpinner();
        },
        ajax: {
            url: "azqy/bvnLogsData.php",
            type: "GET",
            data: function (param) {
                param.operator = $("#operator").val().trim();
                // param.dateRange = $("#reportrange span").text;
                param.ussd = $("#ussd").val().trim();
            },
            dataSrc: function (response) {
                console.log(response);
                // let data = response["data"];
                // response["recordsFiltered"] = data["filteredRecordCount"];
                // response["recordsTotal"] = data["overallRecordCount"];
                // return data["logs"];
                return [];
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
            {data: "msisdn"},
            {data: "serviceCode"},
            {data: "ussdContent"},
            {data: "timestamp"},
            {data: "operator"}
        ]
    });
});
