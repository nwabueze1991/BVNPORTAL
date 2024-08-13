$(document).ready(function () {
    table = $('#table').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        drawCallback: function (settings) {
            closeSpinner();
        },
        ajax: {
            url: "getBvnDataGlo.php",
            type: "GET"

        }
    });

});
