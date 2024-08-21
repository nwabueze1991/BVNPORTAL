<?php
include "session.php";


define('API_URL', 'http://192.164.177.170/BVNPORTAL_API/api/tableDatabaseCount.php');

$hourly = true;
$table_header = "Hit count for today.";
$dateRange = isset($_POST['dateRange']) ? array('dateRange' => $_POST['dateRange']) : array();

$resutResponse = callExternalApi($dateRange);
$report = isset($resutResponse['data']) ? $resutResponse['data'] : array();

$table_header = isset($resutRespons['table_header']) ? $resutRespons['table_header'] : $table_header;
$hourly = isset($resutRespons['message']) && $resutRespons['message'] ==true ? true : false;

function callExternalApi($dateRange) {
    $ch = curl_init(API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dateRange));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

    // Execute the POST request
    $response = json_decode(curl_exec($ch), true);

    // Check for cURL errors
    if (curl_errno($ch)) {
        curl_close($ch);
        return array('status' => 500, 'message' => 'cURL Error: ' . curl_error($ch));
    }

    curl_close($ch);
    if (is_array($response) && isset($response['status'])&& $response['status'] == 200) {
        return $response;
    }
}

function getRealDate($date) {
    $explosion = explode("_", $date);
    return $explosion[count($explosion) - 1];
}
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>BVN PORTAL | BVN LOGS</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">

        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <!-- Popper JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>

        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
        <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/TableExport/5.0.0/css/tableexport.min.css">-->
        <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <script type="text/javascript" src="plugins/Export/src/jquery.table2excel.js"></script>
        <link rel="stylesheet" href="css/base.css">
        <link rel="stylesheet" href="css/palette.css">
    </head>
    <body>
        <header>
            <nav class="navbar dark-primary-color navbar-expand-lg">
                <a class="h2 logo db-text" href="signin.php">BVN PORTAL</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse justify-content-end navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav">
                        <li class="nav-item  mr-2">
                            <a class="nav-link rounded activeState" href="tableDatabaseCount.php" data-toggle="tooltip" data-placement="bottom" title="View BVN count"><span>BVN Count</span> <i class="fas fa-coins"></i></a>
                        </li>
                        <li class="nav-item  mr-2">
                            <a class="nav-link db-text" href="bvnLogs.php" data-toggle="tooltip" data-placement="bottom" title="View Bvn Logs not including Glo records"><span>BVN Logs</span> <i class="fas fa-database"></i></a>
                        </li>
                        <li class="nav-item  mr-2">
                            <a class="nav-link db-text" href="bvnLogsGlo.php" data-toggle="tooltip" data-placement="bottom" title="View Glo records"><span>BVN Glo Logs</span> <i class="fas fa-database"></i></a>
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
        <div class="container">
            <div class="row mt-3 pl-5">
                <div class="col text-white tableHeader">
                    <h1> <span class="dark-primary-color p-1 rounded">BVN COUNT</span></h1>
                </div>
            </div>
            <div class="row mt-10 px-5 secondary-text-color">
                <div class="col-md-12">
                    <div class="d-inline d-flex">
                        <form class="form-inline" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="form-group mr-6">
                                <label class="text-white p-2 dark-primary-color mr-2 rounded" for="reportrange">Search by Date: </label>
                                <input name="dateRange" id="reportrange" class="border border-dark d-inline rounded p-2 text-center">
                                <span></span>
                                </input>
                            </div>
                            <button id="searchBtn" onclick="search()" class="btn btn-black">Search</button>
                        </form><br><br><br>
                    </div>

                </div>
            </div>

            <div class="row" style="margin-left : 5%">
                <h6 class="mb-2 text-white p-2 dark-primary-color rounded" id="dateHeader"><?php echo $table_header; ?></h6><br><br>
            </div>
            <div class="row d-flex justify-content-start mx-1 mt-4 px-5">
                <div class="row" style="margin-top: -0.5%; margin-left: 2%">
                    <div class="" style="margin-bottom: 1%">
                        <button type="submit" id="export" class="btn btn-primary btn-block btn-flat form-control" style="background: #bb3932">Download</button>
                    </div>
                </div>
                <table id="bvnTable" class="table mt-2 text-center light-primary-color secondary-text-color table-hover border border-dark rounded table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo $hourly === true ? "Time" : "Day" ?></th>
                            <th>Service Name</th>
                            <th>MTN</th>
                            <th>Airtel</th>
                            <th>GLO</th>
                            <th>9Mobile</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($report as $date => $inner_report) {
                            foreach ($inner_report as $service_name => $telco_count) {
                                ?>
                                <tr>
                                    <td><?php echo $hourly === true ? getRealDate($date) : $date; ?></td>
                                    <td><?php echo $service_name; ?></td>
                                    <td><?php echo $telco_count["MTN"]; ?></td>
                                    <td><?php echo $telco_count["AIRTEL"]; ?></td>
                                    <td><?php echo $telco_count["GLO"]; ?></td>
                                    <td><?php echo $telco_count["ETISALAT"]; ?></td>
                                </tr>

                                <?php
                            }
                        }
                        ?>
                    </tbody>

                </table>
            </div>
            <div class="row mt-3 pl-5">
                <div class="col">
                    <div class='my-legend'>
                        <div class='legend-title'>Legend - BVN COUNT TABLE</div>
                        <div class='legend-scale'>
                            <ul class='legend-labels'>
                                <li><span style='background:#FFFF33;'></span>MTN</li>
                                <li><span style='background:#006E53;'></span>9MOBILE</li>
                                <li><span style='background:#FF0000;'></span>AIRTEL</li>
                                <li><span style='background:#149B0A;'></span>GLO</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </body>
</html>
<script type="text/javascript">
            function swap(startDate, endDate) {
            if (startDate && endDate) {
            let startDateVar = new Date(startDate);
                    let endDateVar = new Date(endDate);
                    if (startDateVar > endDateVar) {
            const tempVar = startDate;
                    document.getElementById('startDate').value = endDate;
                    document.getElementById('endDate').value = startDate;
            }
            }
            }
    function swapper() {
    swap(document.getElementById('startDate').value, document.getElementById('endDate').value);
    }

    $(document).ready(function () {

    //document.getElementById('dateHeader').innerHTML = document.getElementById('dateSummary').innerHTML;
    //document.getElementById('dateHeader').classList.remove('d-none');

    let buttons = document.querySelectorAll('[tableexport-id]');
            for (let button of buttons) {
    button.classList.remove('button-default');
            button.classList.add('btn');
            button.classList.add('btn-black');
            button.classList.add('mr-2');
    }
    }
    );
            $(function () {
//        const ans = document.getElementById('dateSummary');
//
            const start = '<?php echo "$start_date" ?>' !== "" ? moment('<?php echo "$start_date" ?>') : moment().subtract(1, 'days');
                    const end = '<?php echo "$end_date" ?>' !== "" ? moment('<?php echo "$end_date" ?>') : moment().subtract(1, 'days');
                    function cb(start, end) {
                    $('#reportrange').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                    }

            $('#reportrange').daterangepicker({
            maxDate: moment().subtract(1, 'days'),
                    startDate: start,
                    endDate: end,
                    locale: {
                    format: 'YYYY-MM-DD'
                    },
                    ranges: {
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().subtract(1, 'days')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
            }, cb);
                    //Damola's download was replaced with the below by Gabriel 24/May/2019
                    //the plugin stopped working
                    $("#export").click(function () {
            var d = new Date;
                    $("#bvnTable").table2excel({
            exclude: ".noExl",
                    name: "Worksheet Name",
                    filename: "565logs " + d.getTime() + ".xls"
            });
            });
            });
</script>
