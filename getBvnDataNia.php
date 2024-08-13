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
$table = 'ebillsv2_log';

// Table's primary key
$primaryKey = 'LOGSID';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes

$columns = array(
    array( 'db' => 'MSISDN',   'dt' => 0 ),
    array( 'db' => 'USSDCODE', 'dt' => 1),
    array( 'db' => 'CREATEDON',  'dt' => 2, 'formatter'=>function( $d, $row ) {
            $format = "d-M-y h.i.s.u A";
            $timezone = new DateTimeZone('Africa/Lagos');
            $date = DateTime::createFromFormat($format, $d, $timezone);
            return $date->format('Y-m-d H:i:s');
        } ),
    array( 'db' => 'OPERATOR',  'dt' => 3, 'formatter'=>function( $d, $row ) {
            return strtoupper($row['OPERATOR']);
        } )
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
require( 'bvnSSPNia.php' );
echo json_encode(
    SSP::simple( $_GET, $table, $primaryKey, $columns )
);
?>
