<?php
require "my_functions.php";
require "constants.php";

$date ='2019-01-21';
$wh_id = '31057';
$item_id = '1';


$hf_data_for_this_month = calc_for_hf_data($date,$wh_id,$item_id);
//echo '<pre>';print_r($hf_data_for_this_month);exit;


$new_hf_id = send_data_to_hf_data($hf_data_for_this_month);

//$qry = "UPDATE ussd_";
//mysqli_query(connect(), $qry_d);