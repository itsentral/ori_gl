<?php
date_default_timezone_set("Asia/Bangkok"); 
include_once 'connection.php';

$db1 		= new database_ORI();
$koneksi 	= $db1->connect();
$query      = "SELECT * FROM coa_master";
$result     = mysqli_query($koneksi, $query);

$ArrData    = array();
while($row  = mysqli_fetch_array($result))
$ArrData[]  = $row;

$ArrData_2  = array();
foreach($ArrData AS $val => $valx){
    $ArrData_2[$val]['no_perkiraan'] 	= $valx['no_perkiraan'];
    $ArrData_2[$val]['nama'] 			= strtoupper($valx['nama']);
    $ArrData_2[$val]['kdcab'] 			= strtoupper($valx['kdcab']);
    $ArrData_2[$val]['tipe'] 			= $valx['tipe'];
    $ArrData_2[$val]['level'] 			= $valx['level'];
}

header('Content-Type: application/json'); 

print_r(json_encode($ArrData_2));

