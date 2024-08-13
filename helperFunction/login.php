<?php
function hash_equalsFunc($known_string, $user_string) {
    $ret = 0;

    if (strlen($known_string) !== strlen($user_string)) {
        $user_string = $known_string;
        $ret = 1;
    }

    $res = $known_string ^ $user_string;

    for ($i = strlen($res) - 1; $i >= 0; --$i) {
        $ret |= ord($res[$i]);
    }

    return !$ret;
}

function login($email, $password){
  $servername = "192.164.177.171:1527";
//   $servername = "140.86.97.24";
  $connBvn = oci_connect('bvn', 'bvn123', "//$servername/mydb2:POOLED");
  $loginSql = "SELECT * from APPLICATION_LOGIN where email='$email' and password = '$password' and application='BVN_PORTAL'";
  $loginResOracle = oci_parse($connBvn, $loginSql);
  oci_execute($loginResOracle);
  oci_fetch_all($loginResOracle, $loginOracle, null, null, OCI_FETCHSTATEMENT_BY_ROW);
  return !empty($loginOracle);
}
 ?>
