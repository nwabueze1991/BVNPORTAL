<?php
//include "session.php"; todo: uncomment
include 'cleanInput.php';
include 'validator.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>BVN LOGS</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">

        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <!-- Popper JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>

        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
        <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/base.css">
        <link rel="stylesheet" href="css/palette.css">

        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    </head>
    <body>
        <header>
            <nav class="navbar navbar-dark dark-primary-color navbar-expand-lg">
                <a class="h2 logo db-text" href="signin.php">BVN PORTAL</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse justify-content-end navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav">
                        <li class="nav-item  mr-2">
                            <a class="nav-link db-text" href="tableDatabaseCount.php" data-toggle="tooltip" data-placement="bottom" title="View BVN count"><span>BVN Count</span> <i class="fas fa-coins"></i></a>
                        </li>
                        <li class="nav-item  mr-2">
                            <a class="nav-link rounded activeState" href="bvnLogs.php" data-toggle="tooltip" data-placement="bottom" title="View Bvn Logs not including Glo records"><span>BVN Logs</span> <i class="fas fa-database"></i></a>
                        </li>
                        <li class="nav-item  mr-2">
                            <a class="nav-link db-text" href="bvnLogsGlo.php" data-toggle="tooltip" data-placement="bottom" title="View Bvn GLO Logs"><span>BVN Glo Logs</span> <i class="fas fa-database"></i></a>
                        </li>
                        <li class="nav-item  mr-2">
                            <a class="nav-link db-text" href="bvnLogsNia.php" data-toggle="tooltip" data-placement="bottom" title="View Bvn Logs for NIA"><span>BVN Logs NIA</span> <i class="fas fa-database"></i></a>

                        </li>
                        <li class="nav-item  mr-2">
                            <a class="nav-link db-text" href="logout.php" data-toggle="tooltip" data-placement="bottom" title="Logout from the application"><span>Logout </span> <i class="fas fa-sign-out-alt"></i></a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <div id="loaderContainer" class="loaderContainer d-none">
            <div class="loader"></div>
        </div>
        <div class="container">
            <div class="row mt-3  pl-5">
                <div class="col-3">
                    <h1><p class="text-white p-2 dark-primary-color  tableHeader mr-2 rounded">
                            BVN LOGS
                        </p></h1>

                </div>

            </div>

            <div class="row mt-1  pl-5">
                <div class="col-12">
                    <div class="d-inline d-flex">
                        <form class="form-inline" action="">
                            <div class="form-group mr-4">
                                <label class="text-white p-2 dark-primary-color mr-2 rounded" for="operator">Search by Operator: </label>
                                <select class="border border-dark p-2 form-control rounded" id="operator" name="searchText">
                                    <option value="">ALL</option>
                                    <option value="mtn">MTN</option>
                                    <option value="airtel">AIRTEL</option>
                                    <option value="etisalat">9MOBILE</option>
                                </select>
                            </div>
                            <div class="form-group mr-4">
                                <label class="text-white p-2 dark-primary-color mr-2 rounded" for="reportrange">Date Range: </label>
                                <input name="dateRange" id="reportrange" class="border border-dark d-inline rounded p-2 text-center">
                                <span></span>
                                </input>
                            </div>
                            <div class="form-group mr-4">
                                <label class="text-white p-2 dark-primary-color mr-2 rounded" for="ussd">USSD Code</label>
                                <select class="border border-dark p-2 form-control rounded" name="ussd" id="ussd">
                                    <option value="star0">*0</option>
                                    <option value="star2">*2</option>
                                </select>
                            </div>
                            <button id="searchBtn" class="btn btn-black">Search</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row mt-3 pl-5">
                <div class="col-md-7 col-9">
                    <h6 class="mb-2 text-white p-2 dark-primary-color rounded" id="dateHeader">
                        The record count spans from start date: <span id="dateStart"></span> to end date: <span id="dateEnd"></span>
                    </h6>
                </div>

            </div>
            <div class="row pl-5">
                <div class="col-12 p-3">

                    <table id="table" class="table mt-2 text-center light-primary-color secondary-text-color table-hover border border-dark rounded table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    PHONE NUMBER
                                </th>
                                <th>
                                    SERVICE CODE
                                </th>
                                <th>
                                    USSD CONTENT
                                </th>
                                <th>
                                    TIMESTAMP
                                </th>
                                <th>
                                    OPERATOR
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                </div>
                <!-- /.col-lg-12 -->
            </div>
        </div>


        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>

        <script type="text/javascript" src="getBvnData.js"></script>

        <script type="text/javascript">
                                    function summary(start, end) {

                                        document.getElementById("dateStart").innerText = start;
                                        document.getElementById("dateEnd").innerText = end;
                                        console.log('summary', start, end);
                                    }
                                    $(function () {
                                        const tf = document.getElementById("table_filter");
                                        tf.remove();
                                    });

        </script>
        <script>
            $(function () {

                const reportRangeValue = document.getElementById("reportrange");
                const value = reportRangeValue.value.split(' - ');
                const end = value[1] || moment();
                const start = value[0] || moment().subtract(1, 'days').subtract(29, 'days');
                function cb(start, end) {
                    $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                    console.log('cb', start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                    summary(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
                    // searchInit(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));

                }

                $('#reportrange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    locale: {
                        format: 'YYYY-MM-DD'
                    },
                    maxDate: end,
                    ranges: {
                        'Today': [end, end],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), end],
                        'Last 30 Days': [moment().subtract(29, 'days'), end],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                }, cb);

                cb(start, end);

            });
        </script>
        <script src="js/makeCool.js"></script>
        <script type="text/javascript">

            $(
                    function () {
                        const length = document.getElementById("table_length");
                        const tableInfo = document.getElementById("table_info");
                        const tablePag = document.getElementById("table_paginate");
                        //text-white p-2 dark-primary-color mr-4 rounded
                        makeCool(length);
                        makeCool(tableInfo);

                        tableInfo.classList.add("mt-2");
                        tableInfo.classList.add("p-2");
                        makeCool(tablePag);
                        tablePag.classList.add("mt-2");
                        tablePag.classList.remove("mb-4");
                        tableInfo.classList.remove("mb-4");

                    }
            );


        </script>
        <script type="text/javascript" src="js/search.js">


        </script>
        <script type="text/javascript" src="js/spinner.js" >

        </script>
        <script>
            // function searchInit(s, e) {
            //     openSpinner();
            //     const start = s || document.getElementById("dateStart").innerHTML;
            //     const end = e || document.getElementById("dateEnd").innerHTML;
            //     const operator = document.getElementById("operator").value;
            //     const ussd = document.getElementById("ussd").value;
            //     console.log(ussd, operator, start, end);
            //     search([2, 3, 4], `${start} - ${end};${operator};${ussd}`);
            // }
        </script>
    </body>
</html>
