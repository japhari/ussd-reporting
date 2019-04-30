<?php
//echo '<pre>';
//print_r($_REQUEST);
//exit;
 // WARNING : this code is in mysqli extension ...
if(!empty($_REQUEST['master_id']) && !empty($_REQUEST['session_child_id']) && !empty($_REQUEST['item_id']))
{
    $master_id  = $_REQUEST['master_id'];
    $child_id   = $_REQUEST['session_child_id'];
    $item_id    = $_REQUEST['item_id'];

    require "my_functions.php";
    //1.Update the values in ussd_sessions table
    //2.fetch data of this id
    //3.calulate the hf_data
    //4. send data to hf.


    //first update the values in the table
    $qry_update  = "UPDATE ussd_sessions SET " ; 
    $qry_update  .= " is_processed = 0 ";
    if(isset($_REQUEST['rec']))   $qry_update  .= " ,stock_received=      '".$_REQUEST['rec']."' ";
    if(isset($_REQUEST['cons']))  $qry_update  .= " ,stock_consumed=      '".$_REQUEST['cons']."' ";
    if(isset($_REQUEST['adj_p'])) $qry_update  .= " ,stock_adjustment_p=  '".$_REQUEST['adj_p']."' ";
    if(isset($_REQUEST['adj_n'])) $qry_update  .= " ,stock_adjustment_n=  '".$_REQUEST['adj_n']."' ";

    $qry_update  .= " WHERE pk_id = '".$child_id."' ";
    //echo $qry_update;exit;
    mysqli_query(connect(), $qry_update);

    //Insert into history
    if(isset($_REQUEST['rec'])) {
        $qry_history = "  INSERT into  ussd_sessions_history  SET item_id='".$item_id."',  ussd_master_id = '".$master_id."',   column_name='stock_received',   value_entered='".$_REQUEST['rec']."',  user_id='".$_SESSION['user_id']."' ;";
        action_query($qry_history);
    }
    if(isset($_REQUEST['cons'])) {
        $qry_history = "  INSERT into  ussd_sessions_history  SET item_id='".$item_id."',  ussd_master_id = '".$master_id."',   column_name='stock_consumed',   value_entered='".$_REQUEST['cons']."',  user_id='".$_SESSION['user_id']."' ;";
        action_query($qry_history);
    }
    if(isset($_REQUEST['adj_p'])) {
        $qry_history = "  INSERT into  ussd_sessions_history  SET item_id='".$item_id."',  ussd_master_id = '".$master_id."',   column_name='stock_adjustment_p',   value_entered='".$_REQUEST['adj_p']."',  user_id='".$_SESSION['user_id']."' ;";
        action_query($qry_history);
    }
    if(isset($_REQUEST['adj_n'])) {
        $qry_history = "  INSERT into  ussd_sessions_history  SET item_id='".$item_id."',  ussd_master_id = '".$master_id."',   column_name='stock_adjustment_n',   value_entered='".$_REQUEST['adj_n']."',  user_id='".$_SESSION['user_id']."' ;";
        action_query($qry_history);
    }



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
                        ussd_sessions.pk_id = '".$child_id."'
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
    echo 'Approved';
}
else
{
    echo 'Could Not Save.';
}
?>