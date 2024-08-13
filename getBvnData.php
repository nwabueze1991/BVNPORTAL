<?php
include 'session.php';
include "hex2bin.php";
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'bvn_logs';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes

$columns = array(
    array( 'db' => 'MSISDN',   'dt' => 0, 'formatter'=>function( $d, $row ) {
          if($row['OPERATOR'] == 'ETISALAT'){
            return hex2bin($row['MSISDN']);
            }
            return $row['MSISDN'];
        } ),
    array( 'db' => 'SERVICE_CODE', 'dt' => 1, 'formatter'=>function( $d, $row ) {
          if($row['OPERATOR'] == 'ETISALAT'){
            return hex2bin($row['SERVICE_CODE']);
            }
            return $row['SERVICE_CODE'];
        }),
    array( 'db' => 'USSD_CONTENT',  'dt' => 2, 'formatter'=>function( $d, $row ) {
          if($row['OPERATOR'] == 'ETISALAT'){
            return hex2bin($row['USSD_CONTENT']);
            }
            return $row['USSD_CONTENT'];
        } ),
    array( 'db' => 'TSTAMP',  'dt' => 3, 'formatter'=>function( $d, $row ) {
            $format = "d-M-y h.i.s.u A";
            $timezone = new DateTimeZone('Africa/Lagos');
            $date = DateTime::createFromFormat($format, $d, $timezone);
            return $date->format('Y-m-d H:i:s');
        } ),
    array( 'db' => 'OPERATOR',  'dt' => 4, 'formatter'=>function( $d, $row ) {
        if($row['OPERATOR'] == 'ETISALAT'){
            return "9MOBILE";
            }
            return strtoupper($row['OPERATOR']);
        } )
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
function logStringTofile($string) {
    $today_date = date("Y-m-d");
    $timestamp = date("d-m-y H:i:s");
    file_put_contents("/var/www/html/BvnPortal/log.log", "$timestamp => $string\n\n", FILE_APPEND);
}
require( 'bvnSSP.php' );

$dataResult = SSP::simple($_GET, $table, $primaryKey, $columns);
//logTofile("DataTable Result ===>" . print_r($dataResult, true));
//if ($dataResult) {
echo json_encode(
        $dataResult
);
logStringTofile("bvn......" . json_encode($_GET));
logStringTofile(json_encode($dataResult));
?>
