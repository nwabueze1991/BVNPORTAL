<?php

const BASE_URL = "http://192.164.177.170/b2bapi/externalPortal/bvnPortal/nibss/";

function logStringToFile($string)
{
    $today_date = date("Y-m-d");
    $microseconds = explode(" ", microtime());
    $timestamp = date("Y-m-d H:i:s.") . str_replace("0.", "", $microseconds[0]);
    $log_file = "/home/htmladmin/logs/dsn/log-$today_date.log";
    file_put_contents($log_file, "$timestamp ==> $string\n\n", FILE_APPEND);
}


function triggerGetRequest($url, $header = array())
{
    logStringToFile("GET: $url");
    try {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $response = curl_exec($ch);
        curl_close($ch);
        logStringToFile($response);
    } catch (Exception $ex) {
        $response = $ex->getMessage();
    }
    return $response;
}

function triggerPostRequest($url = null, $payload = null, $header = array())
{
    logStringToFile("POST: $url: " . json_encode($payload) . ": " . json_encode($header));
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $response = curl_exec($ch);
        logStringToFile($response);
        curl_close($ch);
    } catch (Exception $e) {
        $response = $e->getMessage();
    }
    return $response;
}
