<?php
require "my_functions.php";
require "constants.php";

$on_this_date ='2019-02-21';
$wh_id = '31057';
$item_id = '1';
$r = calc_cb($on_this_date,$wh_id,$item_id);
echo '<pre>';print_r($r);