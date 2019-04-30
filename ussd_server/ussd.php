<?php
require "my_functions.php";
require "constants.php";

if (isset($_REQUEST['data'])) {
    $data = htmlspecialchars($_REQUEST['data']);
} else {
    $data = null;
}
$requested_phone_num = htmlspecialchars($_REQUEST['cell']);

if (isset($_REQUEST['token']) && $_REQUEST['token'] == '') {

    $requested_phone_num = phoneFormatting($requested_phone_num);
//print_r($requested_phone_num);exit;
    $datalength = strlen($requested_phone_num);
    $screen_num = get_max_id("ussd_screen_level", "screen_level", "phone_number", $requested_phone_num);
//echo ' S:'.$screen_num;
//exit;

    if ($datalength == 12) {

//    $requested_phone_num = substr_replace($requested_phone_num, 92, 0, 1);
        $datalength = strlen($requested_phone_num);
        $qry_log = "INSERT INTO ussd_log(insertion_date,data_inserted,phone_number) values('" . date("Y-m-d H:i:s") . "','" . $data . "','" . $requested_phone_num . "')";
//print_r($qry_log);
        action_query($qry_log);
    }
    
    
    
    //MAIN data set required for all cases
    $all_info = $ussd_temp_info = $dates_info =  array();
    $count_of_session_master = 0;
    $qry = "SELECT
            ussd_temp.week_number,
            ussd_temp.reporting_year,
            ussd_temp.wh_id,
            ussd_temp.wh_name,
            ussd_temp.phone_number,
            ussd_temp.reporting_month
            FROM
            ussd_temp
             WHERE phone_number='" . $requested_phone_num . "' LIMIT 1";

    $res__ = mysqli_query(connect(), $qry);
    $row = $res__->fetch_assoc();
    $ussd_temp_info = $row;
    
    if(!empty($ussd_temp_info['reporting_year'])){
            $qry = "SELECT
                        ussd_weeks.date_start,
                        ussd_weeks.date_end
                    FROM
                        ussd_weeks
                    WHERE
                        ussd_weeks.`year`   = ".$ussd_temp_info['reporting_year']."     AND
                        ussd_weeks.`month`  = ".$ussd_temp_info['reporting_month']."    AND
                        ussd_weeks.`week`   = ".$ussd_temp_info['week_number']." ";

        $res__ = mysqli_query(connect(), $qry);
        $row = $res__->fetch_assoc();
        $dates_info = $row;
    }
    
    $temp_itm_arr = array ();
    $temp_itm_arr = fetch_temp_item($requested_phone_num);
    //echo '<pre>';print_r($temp_itm_arr);print_r($dates_info);
    
    //print_r($row);
    if (!empty($ussd_temp_info['week_number']) && !empty($ussd_temp_info['reporting_year']) && !empty($ussd_temp_info['wh_id'])   ) {
            $qry_check = "SELECT
                        ussd_session_master.pk_id, 
                        ussd_sessions.item_id,
                        ussd_sessions.stock_received,
                        ussd_sessions.stock_consumed,
                        ussd_sessions.stock_adjustment_p,
                        ussd_sessions.stock_adjustment_n,
                        ussd_session_master.reporting_year,
                        ussd_session_master.week_number,
                        ussd_session_master.wh_id,
                        ussd_session_master.wh_name,
                        ussd_session_master.phone_number,
                        ussd_session_master.reporting_month,
                        ussd_session_master.week_start_date
                        FROM
                        ussd_session_master
                        LEFT JOIN ussd_sessions ON ussd_session_master.pk_id = ussd_sessions.ussd_master_id
                        WHERE week_number   =" . $ussd_temp_info['week_number'] . " "
                    . " AND reporting_year  =" . $ussd_temp_info['reporting_year'] . " AND wh_id=" . $ussd_temp_info['wh_id'] . " "
                    . " AND reporting_month =" . $ussd_temp_info['reporting_month'] . " AND phone_number='" . $requested_phone_num . "' ";
            //echo $qry_check;exit;
            $result_check = mysqli_query(connect(), $qry_check);
            
            while($rowrrd = $result_check->fetch_assoc())
            {
                $row_chk['pk_id'] = $rowrrd['pk_id'];
                $row_chk['reporting_year'] = $rowrrd['reporting_year'];
                $row_chk['reporting_month'] = $rowrrd['reporting_month'];
                $row_chk['week_number'] = $rowrrd['week_number'];
                $row_chk['week_start_date'] = $rowrrd['week_start_date'];
                $all_info[$rowrrd['item_id']] = $rowrrd;
                $count_of_session_master++;
            }
    }
    //echo '<pre>'.$count_of_session_master;print_r($row_chk);print_r($all_info);print_r($ussd_temp_info);exit;
    //END of MAIN data set
    
    
    
    //////////if only one HF is assigned to user
    $total_hfs_of_this_user =  count_hf($requested_phone_num) ;
    $single_hf_info         =  get_single_hf_info($requested_phone_num) ;
    //echo ' '.',HF:'.$total_hfs_of_this_user.', Screen NUM:'.$screen_num.'';
    if($total_hfs_of_this_user=='1' && $screen_num == '1' )
    {
       // echo 'A';
        $screen_num = 2;
    }
    elseif($total_hfs_of_this_user=='1' && $screen_num == '0')
    {
        if ($datalength == 12) {
        //echo 'B';
            $res = action_query("insert into ussd_temp(phone_number,wh_id,wh_name) values ('" . $requested_phone_num . "','".$single_hf_info['wh_id']."','".$single_hf_info['wh_name']."') ");
            $res = action_query("insert into ussd_screen_level(phone_number,screen_level) values ('" . $requested_phone_num . "','" . ($screen_num + 1) . "') ");
            $menu = getHealthFacilities($requested_phone_num);
            //in this case forcefully sending data equal to ONE
            $screen_num=1;
            $data = 1;
        }
        else
        {
        //echo 'C';
            $menu = "Please send us phone number";
            $screen_num = 999;
        }
    }
    //exit;



    
    //START of The Great Switch ...
    //echo '['.$screen_num.','.$data.'] ';
    //exit;
    switch ($screen_num) {
        case '0':

            if ($datalength == 12) {
                $res = action_query("insert into ussd_temp(phone_number) values ('" . $requested_phone_num . "')");
                $res = action_query("insert into ussd_screen_level(phone_number,screen_level) values ('" . $requested_phone_num . "','" . ($screen_num + 1) . "')");
                $menu = getHealthFacilities($requested_phone_num);
            } else {
                $menu = "Please send us phone number";
            }
            break;
        case '1':
            if (is_numeric($data) && $data != 0) {
                if ($data < 5) {
                    $query_get_hf = "SELECT DISTINCT
                                        ussd_hf.wh_id,
                                        tbl_warehouse.wh_name
                                        FROM
                                        ussd_hf
                                        INNER JOIN tbl_warehouse ON ussd_hf.wh_id = tbl_warehouse.wh_id
                                        WHERE
                                        ussd_hf.phone_number = '" . $requested_phone_num . "' AND
                                        ussd_hf.serial_number = $data
                                        ";
                    //echo $query_get_hf;
                    $res_hf = mysqli_query(connect(), $query_get_hf);
                    $menu = $menu2;
                    while ($row_hf = $res_hf->fetch_assoc()) {
                         $qry_temp = "update ussd_temp set wh_id=" . $row_hf['wh_id'] . ", wh_name='" . $row_hf['wh_name'] . "' WHERE phone_number='" . $requested_phone_num . "'";
                         $qry_2 = "update ussd_screen_level set screen_level='" . ($screen_num + 1) . "' WHERE phone_number='" . $requested_phone_num . "'";
                    }
                    $res_2 = action_query($qry_2);
                    $res_2 = action_query($qry_temp);
                } else {
                    $menu = $correct_option_error . getHealthFacilities($requested_phone_num);
                }
            } else if (($data != null)) {
                $menu = $correct_option_error . getHealthFacilities($requested_phone_num);
            } else if ($data == null) {
                $menu = getHealthFacilities($requested_phone_num);
            }
            break;
        case '2':
            if (is_numeric($data) && $data < 5 && $data >= 1) {
                $menu = $menu3;
                $index = 1 - $data;
                $qry_temp = "update ussd_temp set week_number=" . $weeks_raw_data[$data]['week'] . ",reporting_month=" . $weeks_raw_data[$data]['month'] . ",reporting_year=" . $weeks_raw_data[$data]['year'] . " WHERE phone_number='" . $requested_phone_num . "'";

                $n_screen_num = ($screen_num + 1);
                update_screen_level($n_screen_num,$requested_phone_num);
                action_query($qry_temp);
            } else if ($data == '99'){
                exit_func($requested_phone_num);
                $menu = " Thankyou. <end>";
            } else if (($data != null)) {
                $menu = $correct_option_error . $menu2;
            } else if ($data == null) {
                $menu = $menu2;
            }
            
            break;
        case '3':
            if (is_numeric($data) && $data != 0 && $data < 99) {
                switch ($data) {
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                            if ($count_of_session_master == 0) {
                               $q_i="insert into ussd_session_master(week_number,reporting_year,wh_id,wh_name,phone_number,reporting_month,week_start_date) values (" . $ussd_temp_info['week_number'] . "," . $ussd_temp_info['reporting_year'] . "," . $ussd_temp_info['wh_id'] . ",'" . $ussd_temp_info['wh_name'] . "','" . $ussd_temp_info['phone_number'] . "'," . $ussd_temp_info['reporting_month'] . ",'".(!empty($dates_info['date_start'])?$dates_info['date_start']:'')."')";
                                action_query($q_i);
                            }
                         $new_sess_data = fetch_sess_data($requested_phone_num,$ussd_temp_info);
                            
                        $data_type = 'empty';    
                        if($data == '1')$data_type = 'rcv'; 
                        if($data == '2')$data_type = 'cons'; 
                        if($data == '3')$data_type = 'adj_p'; 
                        if($data == '4')$data_type = 'adj_n'; 
                        insert_into_temp_item($requested_phone_num,$ussd_temp_info['wh_id'],$data_type,$new_sess_data['master_id']);
                        $menu = $menuA4;
                        $n_screen_num = '4';
                        update_screen_level($n_screen_num,$requested_phone_num);
                        break;
                    
                    case 5:

                        $n_screen_num = 'V';
                        update_screen_level($n_screen_num,$requested_phone_num);
                        $menu = $menuA4;
                        break;
                    default:
                        $menu = $correct_option_error . '' . $menu3;
                }
            } else if ($data == '99'){
                exit_func($requested_phone_num);
                $menu = " Thankyou. <end>";
            } else if (($data != null)) {
                $menu = $correct_option_error . $menu3;
            } else if ($data == null) {
                $menu = $menu3;
            }
            break;
            
        case '4':
            
            if (is_numeric($data) && $data != 0) {
                if($data=='99'){
                    exit_func($requested_phone_num);
                    $menu = " Thankyou. <end>";
                }
                elseif ($data <= sizeof($test_array)) {
                    //correct flow
                    update_temp_item($requested_phone_num,$ussd_temp_info['wh_id'],$row_chk['pk_id'],$itm_arr[$data]);
                    $temp_itm_arr = fetch_temp_item($requested_phone_num);
                    $col_name = get_column_name($temp_itm_arr);
                    
                    $concat_menu ="";
                    if(!empty($all_info[$itm_arr[$data]][$col_name])){
                        $concat_menu = " ".$all_info[$itm_arr[$data]][$col_name];
                    }
                    $prod_name = $temp_itm_arr['itm_name'];
                    $menu = 'Enter Quantity of '.$prod_name.'? '.$concat_menu;
                    $n_screen_num = '5';
                    update_screen_level($n_screen_num,$requested_phone_num);
                }
                else
                {
                    $menu = $correct_option_error.$menuA4;
                }
            } else if (($data != null)) {
                $menu = $correct_option_error.$menuA4;
            } else if ($data == null) {
                $menu = $menuA4;
            }
            break;
        case '5':
            
            if (is_numeric($data) && $data != null) {
                ////save the qty here
                
                $col_name = get_column_name($temp_itm_arr);
                
                $result_ses = fetch_already_entered_data($row_chk['pk_id'] , $temp_itm_arr['item_id']);
                if ($result_ses->num_rows == 0) {
                       $qry = "insert into ussd_sessions(item_id,".$col_name.",ussd_master_id) values (" . $temp_itm_arr['item_id'] . ",'" . $data . "','" . $row_chk['pk_id'] . "')";
                        action_query($qry);
                        
                }
                else {
                        $qry = "update ussd_sessions set ".$col_name."='" . $data . "' , is_processed = '0' where item_id=" . $temp_itm_arr['item_id'] . " AND ussd_master_id='" . $row_chk['pk_id'] . "'";
                        action_query($qry);
                        
                }
                $ussd_master_id = $row_chk['pk_id'];
                $user_id = get_user_id($requested_phone_num);
                //Insert into history
                $qry_history = "  INSERT into  ussd_sessions_history "
                        . " SET item_id='".$temp_itm_arr['item_id']."',"
                        . " ussd_master_id = '".$ussd_master_id."', "
                        . " column_name='".$col_name."', "
                        . " value_entered='".$data."',"
                        . " user_id='".$user_id."' ;";
                //echo $qry_history;exit;
                action_query($qry_history);
                
                ////////////////////////////////////////////
                ////// entering data into tbl_hf_data 
                ////// disabling direct update. instead cron will be used.
                //$hf_data_for_this_month = calc_for_hf_data($row_chk['week_start_date'],$ussd_temp_info['wh_id'],$temp_itm_arr['item_id']);
                //echo '<pre>';print_r($hf_data_for_this_month);exit;
                //$new_hf_id = send_data_to_hf_data($hf_data_for_this_month);
                ////////////////////////////////////////////
                
                $menu = $menuA4;
                $n_screen_num = '4';
                
                update_screen_level($n_screen_num,$requested_phone_num);
            } 
            else if (($data != null)) {
                    $concat_menu ="";
                    if(!empty($temp_itm_arr)){
                            $col_name = get_column_name($temp_itm_arr);
                            //echo $temp_itm_arr['item_id'].' AND '.$col_name;
                            if(!empty($all_info[$temp_itm_arr['item_id']][$col_name])){
                                    $concat_menu = " ".$all_info[$temp_itm_arr['item_id']][$col_name];
                            }
                    }
                    $prod_name = $temp_itm_arr['itm_name'];
                    $dates_arr_this = get_month_wise_week_dates($row_chk['week_number'],$row_chk['reporting_month'],$row_chk['reporting_year']);
                    $disp_date = date('d',strtotime($dates_arr_this['week_start'])).'-'.date('d M',strtotime($dates_arr_this['week_end']));
                    $menu_custom = 'Enter Quantity of '.$prod_name.' ('.$disp_date.')? '.$concat_menu;
                    $menu = $correct_option_error.$menu_custom;
            } 
            else if ($data == null) {
                    $concat_menu ="";
                    if(!empty($temp_itm_arr)){
                            $col_name = get_column_name($temp_itm_arr);
                            //echo $temp_itm_arr['item_id'].' AND '.$col_name;
                            if(!empty($all_info[$temp_itm_arr['item_id']][$col_name])){
                                    $concat_menu = " ".$all_info[$temp_itm_arr['item_id']][$col_name];
                            }
                    }
                    $prod_name = $temp_itm_arr['itm_name'];
                    $dates_arr_this = get_month_wise_week_dates($row_chk['week_number'],$row_chk['reporting_month'],$row_chk['reporting_year']);
                    $disp_date = date('d',strtotime($dates_arr_this['week_start'])).'-'.date('d M',strtotime($dates_arr_this['week_end']));
                    $menu = 'Enter Quantity of '.$prod_name.' ('.$disp_date.')? '.$concat_menu;
            }
            break;
       
        case 'V':
            //echo 'WHAT'.$data.' AND '.sizeof($test_array);
            if ($data <= sizeof($test_array)) {
                $data = ltrim($data, '0');

                $report = '';
                        $itm_where = "  ";
                        if(!empty($cases_to_items[$data]))
                            $itm_where = " AND item_id=" . $cases_to_items[$data] . " ";
                        else
                            $itm_where = "  ";
                        
                        $qry_report = "select stock_received,stock_consumed,stock_adjustment_p,stock_adjustment_n,
                                                tbl_warehouse.wh_name,
                                                itminfo_tab.itm_name,
                                                ussd_weeks.date_start,
                                                ussd_weeks.date_end
                                            FROM
                                                ussd_sessions
                                            INNER JOIN ussd_session_master ON ussd_sessions.ussd_master_id = ussd_session_master.pk_id
                                            INNER JOIN tbl_warehouse ON ussd_session_master.wh_id = tbl_warehouse.wh_id
                                            INNER JOIN itminfo_tab ON ussd_sessions.item_id = itminfo_tab.itm_id
                                            INNER JOIN ussd_weeks ON ussd_session_master.reporting_year = ussd_weeks.`year` AND ussd_session_master.reporting_month = ussd_weeks.`month` AND ussd_session_master.week_number = ussd_weeks.`week`
                                            WHERE
                                                ussd_session_master.reporting_year = '".$ussd_temp_info['reporting_year']."' AND
                                                ussd_session_master.reporting_month = '".$ussd_temp_info['reporting_month']."' AND
                                                ussd_session_master.week_number = '".$ussd_temp_info['week_number']."' AND
                                                ussd_session_master.wh_id = '".$ussd_temp_info['wh_id']."' ".$itm_where."
                                            LIMIT 1" ;
                        //print_r($qry_report);exit;
                        $res_report = mysqli_query(connect(), $qry_report);
                        //echo '>>'.$res_report->num_rows;
                        if ($res_report->num_rows > 0) {
                                while ($row_report = $res_report->fetch_assoc()) {

                                    $view_rcv = $view_cons = $view_adj_p = $view_adj_n = 0;

                                    if (!empty($row_report['stock_received'])) {
                                        $view_rcv = $row_report['stock_received'];
                                    }
                                    if (!empty($row_report['stock_consumed'])) {
                                        $view_cons = $row_report['stock_consumed'];
                                    }
                                    if (!empty($row_report['stock_adjustment_p'])) {
                                        $view_adj_p = $row_report['stock_adjustment_p'];
                                    }
                                    if (!empty($row_report['stock_adjustment_n'])) {
                                        $view_adj_n = $row_report['stock_adjustment_n'];
                                    }


                                    $report = "";
                                    $report .= $row_report['wh_name'].' - '.$row_report['itm_name'].'';
                                    $report .= nl2br("\n (").date('d',strtotime($row_report['date_start'])).''.date('-d M',strtotime($row_report['date_end'])).')';
                                    $report .= nl2br("\n Received = ") .    $view_rcv;
                                    $report .= nl2br("\n Consumed = ") .    $view_cons;
                                    $report .= nl2br("\n Adjusted (P) = ") .$view_adj_p;
                                    $report .= nl2br("\n Adjusted (N) = ") .$view_adj_n;

                                    $report .= nl2br("\n Thankyou.<end>");
                                }
                        
                            }
                            else{
                                $report = nl2br("No Data Found.\nThankyou.<end>");
                            }
                        
                                
                $menu = $report;
                
                delete_from_screen_level($requested_phone_num);
                delete_from_adjustment($requested_phone_num);
                delete_from_hf($requested_phone_num);
                delete_from_temp($requested_phone_num);
            } else {
                $menu = $correct_option_error . $menuA4;
            }
            break;
        
        case '99':
                    
                exit_func($requested_phone_num);
                $menu = " Thankyou. <end>";
        break;
        default:
            echo $nothing_to_display . "XXX";
    }
    $phonescreen = $menu;
    echo $phonescreen;
} else {
    echo "Not authorized <end>";
}
?>