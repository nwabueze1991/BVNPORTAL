<?php
include "session.php";
include 'cleanInput.php';
include 'validator.php';
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
            <div class="row mt-3 px-5 secondary-text-color">
                <div class="col-md-12">
                    <div class="d-inline d-flex">
                        <form class="form-inline" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group mr-4">
                                <label class="text-white p-2 dark-primary-color mr-2 rounded" for="operator">Search by Operator: </label>
                                <select class="border border-dark p-2 form-control rounded" id="operator" name="searchText">
                                    <option value="">ALL</option>
                                    <option value="mtn">MTN</option>
                                    <option value="airtel">AIRTEL</option>
                                    <option value="etisalat">ETISALAT</option>
                                    <option value="glo">GLO</option>
                                </select>
                            </div>
                            <div class="form-group mr-4">
                                <label class="text-white p-2 dark-primary-color mr-2 rounded" for="reportrange">Date Range: </label>
                                <input name="dateRange" id="reportrange" class="border border-dark d-inline rounded p-2 text-center">
                                <span></span>
                                </input>
                            </div>
                            <button id="searchBtn" onclick="search()" class="btn btn-black">Search</button>
                        </form>
                    </div>

                </div>
            </div>

            <div class="row" style="margin-left : 5%">
                <h6 class="mb-2 d-none text-white p-2 dark-primary-color rounded" id="dateHeader"></h6><br><br>
            </div>
            <div class="row d-flex justify-content-start mx-1 mt-4 px-5">
                <div class="row" style="margin-top: -0.5%; margin-left: 2%">
                    <div class="" style="margin-bottom: 1%">
                        <button type="submit" id="export" class="btn btn-primary btn-block btn-flat form-control" style="background: #bb3932">Download</button>
                    </div>
                </div>
                <table id="bvnTable" class="table mt-2 text-center light-primary-color secondary-text-color table-hover border border-dark rounded table-bordered">
                    <?php
                    //file_put_contents("log.log", "i am here right now \n\n",FILE_APPEND);
                    $errors = array();

                    //$servername = "192.168.100.170";
                    $servername = "192.164.177.171:1527";
                    // $servername = "140.86.97.24";
                    $username = "bvn";
                    $password = "bvn123";
                    $dbName = "mydb2";
                    $conn = oci_connect($username, $password, "//$servername/$dbName:pooled");

                    if (isset($_GET['dateRange'])) {
                        list($startDate, $endDate) = split(' - ', $_GET['dateRange']);
                        $_GET['startDate'] = $startDate;
                        $_GET['endDate'] = $endDate;
                    }
                    if ((isset($_GET['searchText'])) || (isset($_GET['startDate']) || isset($_GET['endDate']))) {


                        $networkSum = 'SUM(MTN_STAR_0) MTN_STAR_0,
                            SUM(MTN_STAR_1) MTN_STAR_1,
                           SUM(MTN_STAR_2) MTN_STAR_2,
                           SUM(GLO_STAR_0) GLO_STAR_0,
                           SUM(GLO_STAR_1) GLO_STAR_1,
                           SUM(GLO_STAR_2) GLO_STAR_2,
                           SUM(AIRTEL_STAR_0) AIRTEL_STAR_0,
                           SUM(AIRTEL_STAR_1) AIRTEL_STAR_1,
                           SUM(AIRTEL_STAR_2) AIRTEL_STAR_2,
                           SUM(ETISALAT_STAR_0) ETISALAT_STAR_0,
                           SUM(ETISALAT_STAR_1) ETISALAT_STAR_1,
                           SUM(ETISALAT_STAR_2) ETISALAT_STAR_2';
                        $dateFilter = '';
                        $indvOp = "";
                        if (isset($_GET['searchText'])) {
                            $searchText = cleanInput($_GET['searchText']);
                            $errors = validateInputs(array('search by filter' => $searchText), array());

                            if (count($errors) === 0) {
                                if ($searchText !== 'all' and ! empty($searchText)) {
                                    $searchTextSwitch = strtolower($searchText);
                                    $indvOp = "AND UPPER(OPERATOR) = '" . strtoupper($searchTextSwitch) . "'";

                                    switch ($searchTextSwitch) {
                                        case "mtn":
                                            $networkSum = 'SUM(MTN_STAR_0) MTN_STAR_0,SUM(MTN_STAR_1) MTN_STAR_1,
                                     SUM(MTN_STAR_2) MTN_STAR_2';
                                            break;
                                        case "glo":
                                            $networkSum = 'SUM(GLO_STAR_0) GLO_STAR_0,SUM(GLO_STAR_1) GLO_STAR_1,
                                     SUM(GLO_STAR_2) GLO_STAR_2';
                                            //$indvOp = "AND UPPER(OPERATOR) = '" . strtoupper($searchTextSwitch) . "'";
                                            break;
                                        case "airtel":
                                            $networkSum = 'SUM(AIRTEL_STAR_2) AIRTEL_STAR_2,SUM(AIRTEL_STAR_1) AIRTEL_STAR_1,
                                     SUM(AIRTEL_STAR_0) AIRTEL_STAR_0';
                                            //$indvOp = "AND UPPER(OPERATOR) = '" . strtoupper($searchTextSwitch) . "'";
                                            break;
                                        case "etisalat":
                                            $networkSum = 'SUM(ETISALAT_STAR_2) ETISALAT_STAR_2,SUM(ETISALAT_STAR_1) ETISALAT_STAR_1,
                                     SUM(ETISALAT_STAR_0) ETISALAT_STAR_0';
                                            //$indvOp = "AND UPPER(OPERATOR) = '" . strtoupper($searchTextSwitch) . "'";
                                            break;
                                        default:
                                            break;
                                    }
                                }
                                $case = 1;
                            }
                        }
                        if (isset($_GET['startDate']) || isset($_GET['endDate'])) {
                            $inputArray = array('startDate', 'endDate');
                            $dateArray = array();

                            foreach ($inputArray as $value) {
                                if (isset($_GET[$value])) {
                                    if (cleanInput($_GET[$value]) !== '') {

                                        $dateVar = cleanInput($_GET[$value]);
                                        $dateArray[$value] = $dateVar;
                                    }
                                }
                            }
                            $errors = validateInputs($dateArray, array());

                            if (count($errors) === 0) {
                                if (count($dateArray) === 2) {
                                    $startDate = $dateArray['startDate'];
                                    $endDate = $dateArray['endDate'];
                                    $datePhrase = "The record count spans from $startDate to $endDate";
                                    if ($startDate === $endDate) {
                                        $dateFilter .= "(date_day = to_date('$startDate','YYYY-MM-DD'))";
                                    } else {
                                        $dateFilter .= "(date_day >= to_date('$startDate','YYYY-MM-DD') AND date_day <= to_date('$endDate','YYYY-MM-DD'))";
                                    }
                                } elseif (count($dateArray) === 1) {
                                    if (array_key_exists("startDate", $dateArray)) {
                                        $startDate = $dateArray['startDate'];
                                        $datePhrase = "The record count spans from start date: $startDate";
                                        $dateFilter .= "date_day >= to_date('$startDate','YYYY-MM-DD')";
                                    } elseif (array_key_exists("endDate", $dateArray)) {
                                        $endDate = $dateArray['endDate'];
                                        $datePhrase = "The record count stops at end date: $endDate";
                                        $dateFilter .= "date_day <= to_date('$endDate','YYYY-MM-DD'))";
                                    }
                                }
                            }
                        }

                        $logSql = "SELECT $networkSum FROM  bvn_count_stars where $dateFilter";
                        $dateFilterStar11 = str_replace("date_day", "to_date(to_char(createdon,'YYYY-MM-DD'),'YYYY-MM-DD')", $dateFilter);
                        $star11Sql = "SELECT count(*) COUNT, UPPER(operator) OPERATOR from ebillsv2_log where biller='11' and $dateFilterStar11 $indvOp group by UPPER(operator)";

                        $logSql = preg_replace('/WHERE\s*$/i', "", $logSql);
                        $logSql = preg_replace('/AND\s*$/i', "", $logSql);
                        file_put_contents("log.log", "if section ran \n\n", FILE_APPEND);
                    } else {
                        $logSql = "SELECT * FROM  bvn_count_stars where DATE_DAY=to_date(to_char(SYSDATE- INTERVAL '1' DAY,'YYYY-Mon-DD'),'YYYY-Mon-DD')";
                        $star11Sql = "SELECT count(*) COUNT, UPPER(operator) OPERATOR from ebillsv2_log where biller='11' and to_date(to_char(createdon,'YYYY-Mon-DD'),'YYYY-Mon-DD')=to_date(to_char(SYSDATE- INTERVAL '1' DAY,'YYYY-Mon-DD'),'YYYY-Mon-DD') group by UPPER(operator)";
                        $startDate = date('Y-m-d', strtotime(date('Y-m-d') . ' - 1 days'));
                        $endDate = date('Y-m-d', strtotime(date('Y-m-d') . ' - 1 days'));
                        $datePhrase = "The record count spans from start date: $startDate to end date: $endDate";
                        file_put_contents("log.log", "else section ran \n\n", FILE_APPEND);
                    }
                    file_put_contents("log.log", "$logSql \n\n", FILE_APPEND);
                    file_put_contents("log.log", "$star11Sql \n\n", FILE_APPEND);

                    $logsRes = count($errors) === 0 && isset($logSql) ? oci_parse($conn, $logSql) : 'errors';
                    $star11Res = count($errors) === 0 && isset($star11Sql) ? oci_parse($conn, $star11Sql) : 'errors';
                    $nrows = 0;
                    $rowArr = $rowArr11 = array();

                    if ($logsRes !== 'errors') {
                        oci_execute($logsRes);
                        oci_execute($star11Res);
                        $rowArr = oci_fetch_array($logsRes, OCI_BOTH);
                        oci_fetch_all($star11Res, $rowArr11, null, null, OCI_FETCHSTATEMENT_BY_ROW);
                    }

                    //this modification was done by Gabriel upon issue escalation
                    //its better to write it out rather than multiple loops
                    $needed_count = array(
                        "MTN" => array("STAR_0" => 0, "STAR_1" => 0, "STAR_2" => 0, "STAR_11" => 0, "COLOR" => '#FFFF33'),
                        "GLO" => array("STAR_0" => 0, "STAR_1" => 0, "STAR_2" => 0, "STAR_11" => 0, "COLOR" => '#149B0A'),
                        "AIRTEL" => array("STAR_0" => 0, "STAR_1" => 0, "STAR_2" => 0, "STAR_11" => 0, "COLOR" => "#FF0000"),
                        "9MOBILE" => array("STAR_0" => 0, "STAR_1" => 0, "STAR_2" => 0, "STAR_11" => 0, "COLOR" => "#006E53")
                    );
                    if ($rowArr) {
                        if ($_GET['searchText']) {
                            $telco = strtoupper(cleanInput($_GET['searchText']));
                            $needed_count[$telco]["STAR_0"] = $rowArr[$telco . "_STAR_0"];
                            $needed_count[$telco]["STAR_2"] = $rowArr[$telco . "_STAR_2"];
                            $needed_count[$telco]["STAR_1"] = $rowArr[$telco . "_STAR_1"];
                        } else {
                            $needed_count["MTN"]["STAR_0"] = $rowArr["MTN_STAR_0"];
                            $needed_count["MTN"]["STAR_1"] = $rowArr["MTN_STAR_1"];
                            $needed_count["MTN"]["STAR_2"] = $rowArr["MTN_STAR_2"];
                            $needed_count["GLO"]["STAR_0"] = $rowArr["GLO_STAR_0"];
                            $needed_count["GLO"]["STAR_1"] = $rowArr["GLO_STAR_1"];
                            $needed_count["GLO"]["STAR_2"] = $rowArr["GLO_STAR_2"];
                            $needed_count["AIRTEL"]["STAR_0"] = $rowArr["AIRTEL_STAR_0"];
                            $needed_count["AIRTEL"]["STAR_1"] = $rowArr["AIRTEL_STAR_1"];
                            $needed_count["AIRTEL"]["STAR_2"] = $rowArr["AIRTEL_STAR_2"];
                            $needed_count["9MOBILE"]["STAR_0"] = $rowArr["ETISALAT_STAR_0"];
                            $needed_count["9MOBILE"]["STAR_1"] = $rowArr["ETISALAT_STAR_1"];
                            $needed_count["9MOBILE"]["STAR_2"] = $rowArr["ETISALAT_STAR_2"];
                        }
                    }

                    if ($rowArr11) {
                        foreach ($rowArr11 as $log) {
                            $operator = $log["OPERATOR"] == "ETISALAT" ? "9MOBILE" : $log["OPERATOR"];
                            $needed_count[$operator]["STAR_11"] = $log["COUNT"];
                        }
                    }
                    ?>
                    <thead>
                        <tr>
                            <th>OPERATOR</th>
                            <th>BVN Check(*0)</th>
                            <th>BVN Validation(*1)</th>
                            <th>BVN Linking(*2)</th>
                            <th>NIA (*11)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($needed_count as $operator => $count) {
                            $color = $count["COLOR"];
                            ?>
                            <tr style='background-color:<?php echo $color; ?>'>
                                <td><?php echo $operator; ?></td>
                                <td><?php echo number_format($count["STAR_0"]); ?></td>
                                <td><?php echo number_format($count["STAR_1"]); ?></td>
                                <td><?php echo number_format($count["STAR_2"]); ?></td>
                                <td><?php echo number_format($count["STAR_11"]); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                    <?php
                    if (isset($datePhrase)) {
                        echo "<p id='dateSummary' class='d-none'>$datePhrase</p>";
                    }
                    ?>
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
                swap(document.getElementById('startDate').value, document.getElementById('endDate').value)
            }
            ;
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                // const loader = document.getElementById('loader');
                // loader.classList.remove('loader');
                document.getElementById('dateHeader').innerHTML = document.getElementById('dateSummary').innerHTML;
                document.getElementById('dateHeader').classList.remove('d-none');
            }
            );
        </script>

<!--        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.4/xlsx.core.min.js"></script>
<script src="https://fFastcdn.org/FileSaver.js/1.1.20151003/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/TableExport/5.0.0/js/tableexport.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/TableExport/5.0.0/img/xlsx.svg"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/TableExport/5.0.0/img/xls.svg"></script>-->
        <script>
            $(document).ready(function () {
//                TableExport(document.getElementById("bvnTable"), {
//                    formats: ['xlsx', 'xls'],
//                    bootstrap: false,
//                    fileName: 'bvnLogs',
//                    exportButtons: true,
//                    position: 'top'
//                });
                let buttons = document.querySelectorAll('[tableexport-id]');
                for (let button of buttons) {
                    button.classList.remove('button-default');
                    button.classList.add('btn');
                    button.classList.add('btn-black');
                    button.classList.add('mr-2');
                }
            });
        </script>
        <script type="text/javascript">
            $(function () {
                const ans = document.getElementById('dateSummary');

                const start = '<?php echo "$startDate" ?>' !== "" ? moment('<?php echo "$startDate" ?>') : moment().subtract(1, 'days');
                const end = '<?php echo "$endDate" ?>' !== "" ? moment('<?php echo "$endDate" ?>') : moment().subtract(1, 'days');

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
                        filename: "logs" + d.getTime() + ".xls"
                    });
                });

            });
        </script>
    </body>
</html>
