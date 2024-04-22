<?php
$serverName = "192.168.88.150";
$connectionOptions = array(
    "Database" => "coko",
    "Uid" => "ape",
    "PWD" => "eUrR*Vks1Nd2A8",
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);
if($conn === false) {
    die(FormatErrors(sqlsrv_errors()));
}

// var_dump($conn);