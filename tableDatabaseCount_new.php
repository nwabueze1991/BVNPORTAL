<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//include "session.php";
include 'cleanInput.php';
include 'validator.php';

//file_put_contents("log.log", "i am here right now \n\n",FILE_APPEND);

$servername = "192.164.177.171:1527";
$username = "bvn";
$password = "bvn123";
$dbName = "mydb2";
$conn = oci_connect($username, $password, "//$servername/$dbName:pooled");

function getQueryResult($sql) {
    global $conn;
    $query = oci_parse($conn, $sql);
    oci_execute($query);
    oci_fetch_all($query, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    return $result;
}

function combineDailyResult($others = array(), $nia = array()) {
    $result = array();
    foreach ($others as $other) {
        $date = $other["DAY"];
        $result[$date] = array(
            "BVN Check" => array("GLO" => $other["GLO_STAR_0"], "MTN" => $other["MTN_STAR_0"], "AIRTEL" => $other["AIRTEL_STAR_0"], "ETISALAT" => $other["ETISALAT_STAR_0"]),
            "BVN Validation" => array("GLO" => $other["GLO_STAR_1"], "MTN" => $other["MTN_STAR_1"], "AIRTEL" => $other["AIRTEL_STAR_1"], "ETISALAT" => $other["ETISALAT_STAR_1"]),
            "BVN Linking" => array("GLO" => $other["GLO_STAR_2"], "MTN" => $other["MTN_STAR_2"], "AIRTEL" => $other["AIRTEL_STAR_2"], "ETISALAT" => $other["ETISALAT_STAR_2"]),
            "NIA" => array("GLO" => 0, "MTN" => 0, "AIRTEL" => 0, "ETISALAT" => 0)
        );
    }

    foreach ($nia as $nia_count) {
        $date = $nia_count["DAY"];
        if (!isset($result[$date]["NIA"])) {
            $result[$date]["NIA"] = array("GLO" => 0, "MTN" => 0, "AIRTEL" => 0, "ETISALAT" => 0);
        }

        $operator = $nia_count["OPERATOR"] == "9MOBILE" ? "ETISALAT" : $nia_count["OPERATOR"];
        $result[$date]["NIA"][$operator] = $nia_count["COUNT"];
    }
    return $result;
}

function addToHourlyResult($current_result, $new_result) {
    foreach ($new_result as $other) {
        $temp = $other["TEMP"]; //this is used for ordering
        $hour = $other["HOUR"];
        $date = $temp . "_" . $hour;
        if (!isset($current_result[$date])) {
            $current_result[$date] = array(
                "BVN Check" => array("GLO" => 0, "MTN" => 0, "AIRTEL" => 0, "ETISALAT" => 0),
                "BVN Validation" => array("GLO" => 0, "MTN" => 0, "AIRTEL" => 0, "ETISALAT" => 0),
                "BVN Linking" => array("GLO" => 0, "MTN" => 0, "AIRTEL" => 0, "ETISALAT" => 0),
                "NIA" => array("GLO" => 0, "MTN" => 0, "AIRTEL" => 0, "ETISALAT" => 0)
            );
        }
        $current_result[$date][$other["CODE"]][$other["OPERATOR"]] = $other["COUNT"];
    }
    return $current_result;
}

function getDailyReport($start_date, $end_date) {
    $log_sql = <<<SQL
        select 
            to_char(date_day, 'dd-mm-yyyy') day,mtn_star_0,glo_star_0,etisalat_star_0,airtel_star_0,
            mtn_star_1,glo_star_1,etisalat_star_1,airtel_star_1,
            mtn_star_2,glo_star_2,etisalat_star_2,airtel_star_2
        from bvn_count_stars
        where 
            to_date(to_char(date_day,'yyyy-mm-dd'),'yyyy-mm-dd') <= to_date('$end_date','yyyy-mm-dd') and
            to_date(to_char(date_day,'yyyy-mm-dd'),'yyyy-mm-dd') >= to_date('$start_date','yyyy-mm-dd')
        order by date_day desc  
SQL;
    $nia_sql = <<<SQL
        select count(*) count,to_char(createdon,'dd-mm-yyyy') day,upper(operator) operator from ebillsv2_log 
        where 
            biller='11' and 
            to_date(to_char(createdon,'yyyy-mm-dd'),'yyyy-mm-dd') <= to_date('$end_date','yyyy-mm-dd') and
            to_date(to_char(createdon,'yyyy-mm-dd'),'yyyy-mm-dd') >= to_date('$start_date','yyyy-mm-dd')
        group by to_char(createdon,'dd-mm-yyyy'),upper(operator)
        order by to_date(to_char(createdon,'dd-mm-yyyy'),'dd-mm-yyyy') desc   
SQL;
    return combineDailyResult(getQueryResult($log_sql), getQueryResult($nia_sql));
}

function getHourlyReport($date) {
    $log_sql = <<<SQL
        select count(*) count,operator,code,hour,temp
        from
            (
                select upper(operator) operator,to_char(tstamp, 'HH12 AM') || ' - ' || to_char(tstamp + interval '1' hour,'HH12 AM') hour,to_char(tstamp, 'HH24') temp,
                    case 
                        when (upper(operator) = 'MTN' and USSD_CONTENT in ('*565#','*565*0#','*565*0#;'))--mtn
                            or (upper(operator) = 'AIRTEL' and SERVICE_CODE in ('565*0','565','*565','565','565*'))--airtel
                            or (upper(operator) = 'ETISALAT' and UPPER(USSD_CONTENT) in ('2A35363523','2A3536352323','2A3536352330','2A3536352A3023','2A3536352A30'))--etisalat
                            then 'BVN Check'
                        when (upper(operator) = 'MTN' and (USSD_CONTENT like '%565*2*%' or  USSD_CONTENT like '%565*2#'))--mtn
                            or SERVICE_CODE in ('565*2','*565*2','*565*2#') or service_code like '%565*2*%'--airtel
                            or (upper(operator) = 'ETISALAT' and UPPER(USSD_CONTENT) in ('2A3536352A3223','2A3536352A322A')) --etisalat
                            then 'BVN Linking'
                        when (upper(operator) = 'MTN' and (USSD_CONTENT like '%565*1*%' or  USSD_CONTENT like '%565*1#')) --mtn
                            or (upper(operator) = 'AIRTEL' and (SERVICE_CODE in ('565*1','*565*1','*565*1#') or service_code like '%565*1*%'))--airtel
                            or (upper(operator) = 'ETISALAT' and UPPER(USSD_CONTENT) in ('2A3536352A3123','2A3536352A312A'))--etisalat
                            then 'BVN Validation'
                    end code
                from bvn_logs
                where to_date(to_char(tstamp, 'yyyy-mm-dd'), 'yyyy-mm-dd') = to_date('$date','yyyy-mm-dd')
            )
        where code is not null
        group by code,operator,hour,temp
SQL;
    //$others = getQueryResult($log_sql);
    //print_r($others);

    $glo_sql = <<<SQL
        select count(*) count,code,hour,temp
        from
            (
                select
                case
                    when ussd_msg in ('*565#','*565*0','*565*0#') then 'BVN Check'
                    when ussd_msg like '*565*2*%' or ussd_msg = '*565*2#' then 'BVN Linking'
                    when ussd_msg like '*565*1*%' or ussd_msg = '*565*1#' then 'BVN Validation'
                end code,to_char(tstamp, 'HH12 AM') || ' - ' || to_char(tstamp + interval '1' hour,'HH12 AM') hour,to_char(tstamp, 'HH24') temp
                from glo_smpp_logs
                where 
                (
                    ussd_msg in ('*565#','*565*0','*565*0#') or 
                    ussd_msg like '*565*2*%' or ussd_msg = '*565*2#' or
                    ussd_msg like '*565*1*%' or ussd_msg = '*565*1#'
                )
                and to_date(to_char(tstamp, 'yyyy-mm-dd'), 'yyyy-mm-dd') = to_date('$date','yyyy-mm-dd')
            )
        group by code,hour,temp   
SQL;
    $connection = oci_connect("mmarket", "Thu_541mm", "//192.164.177.171:1527/mydb2:pooled");
    $query = oci_parse($connection, $glo_sql);
    oci_execute($query);
    oci_fetch_all($query, $glo, null, null, OCI_FETCHSTATEMENT_BY_ROW);

    $nia_sql = <<<SQL
        select count(*) count,operator,code,hour,temp
        from
        (    
            select 
                upper(operator) operator, 'NIA' code,to_char(createdon, 'HH24') temp,
                to_char(createdon, 'HH12 AM') || ' - ' || to_char(createdon + interval '1' hour,'HH12 AM') hour
            from ebillsv2_log 
            where 
                biller = '11' and
                to_date(to_char(createdon, 'yyyy-mm-dd'), 'yyyy-mm-dd') = to_date('$date','yyyy-mm-dd')
        )
        group by code,operator,hour,temp   
SQL;

    $hourly_report = addToHourlyResult(addToHourlyResult(array(), getQueryResult($log_sql)), getQueryResult($log_sql));
    $combined_report = addToHourlyResult($hourly_report, getQueryResult($nia_sql));
    krsort($combined_report);
    return $combined_report;
}

function getRealDate($date) {
    $explosion = explode("_", $date);
    return $explosion[count($explosion) - 1];
}

$hourly = true;
$table_header = "Hit count for today.";

if (isset($_POST['dateRange'])) {
    $date_range = explode(" - ", $_POST['dateRange']);
    $start_date = $date_range[0];
    $end_date = $date_range[1];
    if ($start_date != $end_date) {
        $table_header = "Hit count from $start_date till $end_date";
        $report = getDailyReport($start_date, $end_date);
        $hourly = false;
    } else {
        $table_header = "Hit count for $start_date";
        $report = getHourlyReport($start_date);
    }
} else {
    $start_date = $end_date = $today = date("Y-m-d");
    $report = getHourlyReport($today);
}
//krsort($report);
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
