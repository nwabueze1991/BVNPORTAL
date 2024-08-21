<?php
include "functions.php";
$response = triggerGetRequest(BASE_URL. "bvnLogs?".http_build_query($_GET));
echo $response;