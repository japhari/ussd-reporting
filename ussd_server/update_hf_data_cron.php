<?php
// WARNING : this code is in mysqli extension ... check feasibility with CRONs directory ...

require "my_functions.php";
//require "constants.php";

//$date ='2019-01-21';
//$wh_id = '31057';
//$item_id = '1';

//1.fetch list of whid , item , dates , which have been updated/added
//2.loop . calulate the hf_data against params
//3. send data to hf.

$qry_check = "SELECT
                    ussd_session_master.week_start_date,
                    ussd_session_master.wh_id,
                    ussd_session_master.reporting_year,
                    ussd_session_master.reporting_month,
                    ussd_session_master.week_number,
                    ussd_sessions.pk_id,
                    ussd_sessions.insert_date,
                    ussd_sessions.item_id,
                    ussd_sessions.stock_received,
                    ussd_sessions.stock_consumed,
                    ussd_sessions.stock_adjustment_p,
                    ussd_sessions.stock_adjustment_n,
                    ussd_sessions.ussd_master_id,
                    ussd_sessions.is_processed
                FROM
                    ussd_session_master
                INNER JOIN ussd_sessions ON ussd_session_master.pk_id = ussd_sessions.ussd_master_id
                WHERE
                    ussd_sessions.is_processed = 0
                ";
//echo $qry_check;exit;
$result_check = mysqli_query(connect(), $qry_check);
$hf_id_arr = array();
while($row = $result_check->fetch_assoc())
{
    //echo '<pre>';print_r($row);exit;
    $hf_data_for_this_month = calc_for_hf_data($row['week_start_date'],$row['wh_id'],$row['item_id']);
    //echo '<pre>';print_r($hf_data_for_this_month);exit;
    
    $new_hf_id = send_data_to_hf_data($hf_data_for_this_month);
    if(isset($new_hf_id))
    {
        mark_as_processed($row['pk_id']);
    }
    //echo $new_hf_id;
    $hf_id_arr[] = $new_hf_id;
}

//echo '<pre>';print_r($hf_id_arr);exit;
$total_records_processed = 0 ; 
$total_records_processed = count($hf_id_arr) ; 
echo 'Total Records Processed : '.$total_records_processed;


//$qry = "UPDATE ussd_";
//mysqli_query(connect(), $qry_d);