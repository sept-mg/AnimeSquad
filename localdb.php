<?php

try {

    // $servername = "animesquad.fr:3306";
    // $username = "animesquad_db";
    // $dbname = "animesquad_db";
    // $password = "YmY72RnNTQbhoY0iP4oK..4";


    $servername = "127.0.0.1";
    $username = "root";
    $dbname = "animesquad_db";
    $password = "";


    $localmysql = new mysqli($servername, $username, $password, $dbname);
} catch (Exception $e) {
    echo "Local database Error.";
    die();
}

?>