<?php 
require "database.php";

function getHealthFacilities($phone_number) {
    //    $phone_no_db = $str = substr_replace($phone_number, 92, 0, 1);
    //    $phone_no_db = $str = str_replace('-', '', $phone_no_db);
        $qry_hf = "";
        $qry = "SELECT
    tbl_warehouse.wh_name,
    tbl_locations.LocName,
    tbl_warehouse.stkofficeid,
    tbl_warehouse.is_active,
    tbl_warehouse.hf_type_id,
    tbl_warehouse.wh_id
    FROM
        wh_user
    INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
    INNER JOIN sysuser_tab ON sysuser_tab.UserID = wh_user.sysusrrec_id
    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
    WHERE
        sysuser_tab.sysusr_cell = '" . $phone_number . "' AND
    stakeholder.lvl = 7 limit 3 
    ";
        //echo $qry;exit;
        $res = mysqli_query(connect(), $qry);
        $display = "";
        $count = 1;
    
        if ($res->num_rows > 0) {
            $display .= nl2br("Please select health facility \n");
            while ($row = $res->fetch_assoc()) {
    
                $display .= nl2br($count . "-" . $row['wh_name'] . "\n");
                $qry_hf_pk = "Select pk_id from ussd_hf where ussd_hf.wh_id=" . $row['wh_id'] . " AND ussd_hf.phone_number='" . $phone_number . "'";
    //            print_r($qry_hf_pk);
                $res_hf_pk = mysqli_query(connect(), $qry_hf_pk);
                if ($res_hf_pk->num_rows == 0) {
                    $qry_hf = " INSERT INTO ussd_hf(wh_id,serial_number,phone_number) values(" . $row['wh_id'] . "," . $count . ",'" . $phone_number . "')";
    //                print_r($qry_hf);
                    $res_hf = action_query($qry_hf);
                }
                $count++;
    //              print_r($qry_hf);
    //            $menu = $qry_hf;
            }
            //echo $count;
            return $display;
        } else {
            delete_from_screen_level($phone_number);
            delete_from_temp($phone_number);
            //delete_from_hf($phone_number);
            return "You are not authorized to use this service <end>";
        }
    }
function count_hf($phone_number) {
        
        $qry_hf = "";
        $qry = "SELECT
    Count(tbl_warehouse.wh_id) as t_wh
    FROM
        wh_user
    INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
    INNER JOIN sysuser_tab ON sysuser_tab.UserID = wh_user.sysusrrec_id
    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
    WHERE
        sysuser_tab.sysusr_cell = '" . $phone_number . "' AND
    stakeholder.lvl = 7  
    ";
        //print_r($qry);
        $res = mysqli_query(connect(), $qry);
        $count_of_hf = 0;
    
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $count_of_hf =  $row['t_wh'];
            }
            return $count_of_hf;
        } 
    }
function get_single_hf_info($phone_number) {
        ////call this funciton only in case , when one warehouse per user.
        $qry_hf = "";
        $qry = "SELECT
    tbl_warehouse.wh_id,
    tbl_warehouse.wh_name
    FROM
        wh_user
    INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
    INNER JOIN sysuser_tab ON sysuser_tab.UserID = wh_user.sysusrrec_id
    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
    WHERE
        sysuser_tab.sysusr_cell = '" . $phone_number . "' AND
    stakeholder.lvl = 7  
    ";
        $res = mysqli_query(connect(), $qry);
        $info_of_hf = array();
    
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $info_of_hf =  $row;
            }
            return $info_of_hf;
        } 
    }
function get_week_number($day_of_week){
    //$ddate = "2012-10-18";
$date = new DateTime($day_of_week);
$week = $date->format("w");
return $week;
}
function validateDate($date, $format = 'd-m-y'){
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($format) === $date;
}


///////New standard functions
function delete_from_screen_level($phone_number){
    $qry = "Delete from ussd_screen_level where phone_number='" . $phone_number . "'";
    action_query($qry);
}
function delete_from_temp($phone_number){
    $qry = "Delete from ussd_temp where phone_number='" . $phone_number . "'";
    action_query($qry);
}
function delete_from_hf($phone_number){
    $qry = "Delete from ussd_hf where phone_number='" . $phone_number . "'";
    action_query($qry);
}
function delete_from_adjustment($phone_number){
    $qry = "Delete from ussd_adjustment where phone_number='" . $phone_number . "'";
    action_query($qry);
}
function update_screen_level($new_level,$phone_number){
    
    $qry_2 = "update ussd_screen_level set screen_level='" .$new_level. "' WHERE phone_number='" . $phone_number . "'";
    $res_2 = action_query($qry_2);
}
function fetch_already_entered_data($master_id , $item_id){
    $qry_ses = "select pk_id,item_id,stock_received from ussd_sessions where ussd_master_id=" .$master_id. " AND item_id=".$item_id ;
    $result_ses = mysqli_query(connect(), $qry_ses);
    return $result_ses;
}
function insert_into_temp_item($phone_number,$wh_id,$data_type,$ussd_master_id){
    $qry_insert = "INSERT INTO ussd_temp_item(phone_number,wh_id,data_type,ussd_master_id) values('".$phone_number."','".$wh_id."','".$data_type."','".$ussd_master_id."')";
    action_query($qry_insert);
}
function update_temp_item($phone_number,$wh_id,$ussd_master_id,$item_id){
    $qry_insert = "UPDATE ussd_temp_item SET item_id = '".$item_id."' WHERE phone_number='".$phone_number."' AND wh_id ='".$wh_id."'   AND ussd_master_id='".$ussd_master_id."' ";
    action_query($qry_insert);;
}
function delete_temp_item($phone_number){
    $qry_insert = "DELETE FROM ussd_temp_item WHERE phone_number='".$phone_number."' ";
    action_query($qry_insert);;
}
function fetch_temp_item($phone_number){
   $qry_i = "SELECT ussd_temp_item.pk_id,
                    ussd_temp_item.phone_number,
                    ussd_temp_item.data_type,
                    ussd_temp_item.wh_id,
                    ussd_temp_item.ussd_master_id,
                    ussd_temp_item.item_id,
                    itminfo_tab.itm_name
                    FROM
                    ussd_temp_item
                    INNER JOIN itminfo_tab ON ussd_temp_item.item_id = itminfo_tab.itm_id 
                    WHERE phone_number='".$phone_number."' limit 1 ";
   $result_ses = mysqli_query(connect(), $qry_i);
   $row = $result_ses->fetch_assoc();
   return $row;
}
function fetch_sess_data($requested_phone_num,$ussd_temp_info){
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
                    ussd_session_master.phone_number,
                    ussd_session_master.reporting_month
                    FROM
                    ussd_session_master
                    LEFT JOIN ussd_sessions ON ussd_session_master.pk_id = ussd_sessions.ussd_master_id
                    WHERE week_number   =" . $ussd_temp_info['week_number'] . " "
                . " AND reporting_year  =" . $ussd_temp_info['reporting_year'] . " AND wh_id=" . $ussd_temp_info['wh_id'] . " "
                . " AND reporting_month =" . $ussd_temp_info['reporting_month'] . " AND phone_number='" . $requested_phone_num . "' ";
    //echo $qry_check;
    $result_check = mysqli_query(connect(), $qry_check);
    $sess_info = array();
    while($rowrrd = $result_check->fetch_assoc())
    {
        $sess_info[$rowrrd['item_id']] = $rowrrd;
        $sess_info['master_id'] = $rowrrd['pk_id'];
    }
    return $sess_info;
}
function get_column_name($temp_itm_arr){
        $col_name = 'stock_received';
        if($temp_itm_arr['data_type'] == 'rcv')     $col_name = 'stock_received';
        if($temp_itm_arr['data_type'] == 'cons')    $col_name = 'stock_consumed';
        if($temp_itm_arr['data_type'] == 'adj_p')   $col_name = 'stock_adjustment_p';
        if($temp_itm_arr['data_type'] == 'adj_n')   $col_name = 'stock_adjustment_n';
        return $col_name;
    }
function getStartAndEndDate($week, $year) {
            $dto = new DateTime();
            $dto->setISODate($year, $week);
            $ret['week_start'] = $dto->format('Y-m-d');
            $dto->modify('+6 days');
            $ret['week_end'] = $dto->format('Y-m-d');
            return $ret;
}
function get_month_wise_week_dates($week,$month, $year){
	$month_wise_array= array();
	$week_arr = getStartAndEndDate($week,$year);
	$mon_1 = date('m',strtotime($week_arr['week_start']));
	$mon_2 = date('m',strtotime($week_arr['week_end']));
	if($mon_1 != $mon_2){
		//we need to divide the week 
		if($month == $mon_1){
				$s_date = $week_arr['week_start'];
				$e_date = date('Y-m-t',strtotime($week_arr['week_start']));
				$month_wise_array['week_start'] = $s_date;
				$month_wise_array['week_end'] 	= $e_date;
		}
		else{
				$s_date = date('Y-m-01',strtotime($week_arr['week_end']));
				$e_date = $week_arr['week_end'];
				$month_wise_array['week_start'] = $s_date;
				$month_wise_array['week_end'] 	= $e_date;
		}
	}
	else{
	$month_wise_array = $week_arr;
	}
	
	return $month_wise_array;
}
function exit_func($requested_phone_num){
          
    delete_from_screen_level($requested_phone_num);
    delete_from_adjustment($requested_phone_num);
    delete_from_hf($requested_phone_num);
    delete_from_temp($requested_phone_num);
    delete_temp_item($requested_phone_num);
            
}
function calc_cb($on_this_date,$wh_id,$item_id=null){
        $qry = "    SELECT
                        ussd_sessions.stock_received,
                        ussd_sessions.stock_consumed,
                        ussd_sessions.stock_adjustment_p,
                        ussd_sessions.stock_adjustment_n,
                        ussd_sessions.item_id
                    FROM
                        ussd_sessions
                    INNER JOIN ussd_session_master ON ussd_sessions.ussd_master_id = ussd_session_master.pk_id
                    WHERE
                        ussd_session_master.wh_id = '$wh_id'
                        AND week_start_date <= '".$on_this_date."' ";
        if(!empty($item_id)){
            $qry .= " AND item_id = $item_id ";
        }
        //echo $qry;
        $result1 = mysqli_query(connect(), $qry);
        $temp_arr = $cb_arr = array();
        while($row = $result1->fetch_assoc())
        {
            @$temp_arr[$row['item_id']]['rcv']   += $row['stock_received'];
            @$temp_arr[$row['item_id']]['cons']  += $row['stock_consumed'];
            @$temp_arr[$row['item_id']]['adj_p'] += $row['stock_adjustment_p'];
            @$temp_arr[$row['item_id']]['adj_n'] += $row['stock_adjustment_n'];
        }
        
        foreach($temp_arr as $item_id => $itm_data){
            $this_cb = 0;
            $this_cb = (!empty($itm_data['rcv'])?$itm_data['rcv']:0)
                                -(!empty($itm_data['cons'])?$itm_data['cons']:0)
                                +(!empty($itm_data['adj_p'])?$itm_data['adj_p']:0)
                                -(!empty($itm_data['adj_n'])?$itm_data['adj_n']:0);
            $cb_arr[$item_id] = $this_cb;
        }
        return $cb_arr;
}
function calc_cb_from_hf_data($on_this_date,$wh_id,$item_id){
        $qry = "   SELECT
                        tbl_hf_data.warehouse_id,
                        tbl_hf_data.item_id,
                        tbl_hf_data.closing_balance,
                        tbl_hf_data.reporting_date
                    FROM
                        tbl_hf_data
                    WHERE
                        tbl_hf_data.reporting_date <= '".$on_this_date."' AND
                        tbl_hf_data.warehouse_id = '$wh_id' AND
                        tbl_hf_data.item_id = $item_id
                    ORDER BY 
                        tbl_hf_data.reporting_date desc 
                    limit 1  ";
        
        //echo $qry;
        $result1 = mysqli_query(connect(), $qry);
        $temp_arr = $cb_arr = array();
        $row = $result1->fetch_assoc();
        $cb_arr[$item_id] = $row['closing_balance'];
        
        return $cb_arr;
}
function calc_one_month_data($date,$wh_id,$item_id){
        $lmis_month     = date('Y-m-01',strtotime($date));
        $start_of_month = date('Y-m-01',strtotime($date));
        $end_of_month   = date('Y-m-t',strtotime($date));
    
        $qry = "    SELECT
                        ussd_sessions.stock_received,
                        ussd_sessions.stock_consumed,
                        ussd_sessions.stock_adjustment_p,
                        ussd_sessions.stock_adjustment_n,
                        ussd_sessions.item_id
                    FROM
                        ussd_sessions
                    INNER JOIN ussd_session_master ON ussd_sessions.ussd_master_id = ussd_session_master.pk_id
                    WHERE
                        ussd_session_master.wh_id = '$wh_id'
                        AND week_start_date BETWEEN '".$start_of_month."' AND '".$end_of_month."' "
                . "      AND item_id = $item_id ";
      
        //echo $qry;exit;
        $result1 = mysqli_query(connect(), $qry);
        $temp_arr = $cb_arr = array();
        while($row = $result1->fetch_assoc())
        {
            if(empty($temp_arr['rcv']))    $temp_arr['rcv'] = 0;
            if(empty($temp_arr['cons']))   $temp_arr['cons'] = 0;
            if(empty($temp_arr['adj_p']))  $temp_arr['adj_p'] = 0;
            if(empty($temp_arr['adj_n']))  $temp_arr['adj_n'] = 0;
            
            @$temp_arr['rcv']   += $row['stock_received'];
            @$temp_arr['cons']  += $row['stock_consumed'];
            @$temp_arr['adj_p'] += $row['stock_adjustment_p'];
            @$temp_arr['adj_n'] += $row['stock_adjustment_n'];
        }
 
        return $temp_arr;
}
function calc_for_hf_data($date,$wh_id,$item_id){

        $last_date_of_prev_mon = date('Y-m-d',(strtotime('last day of previous month',strtotime($date))));
        $data_of_this_month = calc_one_month_data($date,$wh_id,$item_id);
        
        //$last_cb = calc_cb($last_date_of_prev_mon,$wh_id,$item_id);
        $last_cb = calc_cb_from_hf_data($last_date_of_prev_mon,$wh_id,$item_id);
        //echo '<pre>';print_r($last_cb);exit;
        
        $hf_data_for_this_month = $hf_temp = array();
        $hf_temp['wh_id']       = $wh_id;
        $hf_temp['item_id']       = $item_id;
        $hf_temp['reporting_month']       = date('Y-m-01',strtotime($date));
        $hf_temp['ob']       = (!empty($last_cb[$item_id])?$last_cb[$item_id]:0);
        $hf_temp['rcv']      = (!empty($data_of_this_month['rcv'])?$data_of_this_month['rcv']:'0');
        $hf_temp['cons']     = (!empty($data_of_this_month['cons'])?$data_of_this_month['cons']:'0');
        $hf_temp['adj_p']    = (!empty($data_of_this_month['adj_p'])?$data_of_this_month['adj_p']:'0');
        $hf_temp['adj_n']    = (!empty($data_of_this_month['adj_n'])?$data_of_this_month['adj_n']:'0');

        $this_cb = 0;
        $this_cb = (!empty($last_cb[$item_id])?$last_cb[$item_id]:0)
                            +(!empty($data_of_this_month['rcv'])?$data_of_this_month['rcv']:0)
                            -(!empty($data_of_this_month['cons'])?$data_of_this_month['cons']:0)
                            +(!empty($data_of_this_month['adj_p'])?$data_of_this_month['adj_p']:0)
                            -(!empty($data_of_this_month['adj_n'])?$data_of_this_month['adj_n']:0);
        $hf_temp['cb']       = $this_cb;

        $hf_data_for_this_month=$hf_temp;

        return $hf_data_for_this_month;
}
function check_in_hf_data($rep_month,$wh_id,$item_id){
    $qry = " SELECT
                tbl_hf_data.pk_id,
                tbl_hf_data.warehouse_id,
                tbl_hf_data.item_id,
                tbl_hf_data.reporting_date
            FROM
                tbl_hf_data
            WHERE
                tbl_hf_data.warehouse_id = '".$wh_id."' AND
                tbl_hf_data.item_id = '".$item_id."' AND
                tbl_hf_data.reporting_date = '".$rep_month."' ";
    echo $qry;
    $result1 = mysqli_query(connect(), $qry);
    $row = $result1->fetch_assoc();
    
    return $row['pk_id'];
}
function send_data_to_hf_data($ussd_data){
    global $conn;
    //echo '<pre>';print_r($ussd_data);
    //exit;
    $qry_d = " DELETE FROM `tbl_hf_data` 
            WHERE
                `reporting_date`='".$ussd_data['reporting_month']."' AND 
                `warehouse_id`='".$ussd_data['wh_id']."' AND
                `item_id`='".$ussd_data['item_id']."'  
             ";
   // echo $qry_d;exit;
   mysqli_query($conn, $qry_d);

    $qry = " INSERT INTO `tbl_hf_data` 
            (`warehouse_id`, `item_id`, `opening_balance`, `received_balance`, 
            `issue_balance`, `closing_balance`, 
            `adjustment_positive`, `adjustment_negative`, `reporting_date`,
	`created_by` ) 
            VALUES 
            ('".$ussd_data['wh_id']."','".$ussd_data['item_id']."','".$ussd_data['ob']."','".$ussd_data['rcv']."',"
        . "'".$ussd_data['cons']."','".$ussd_data['cb']."',"
        . "'".$ussd_data['adj_p']."','".$ussd_data['adj_n']."','".$ussd_data['reporting_month']."','0');";
    //echo $qry;
    mysqli_query($conn, $qry);
    
    $next_month_of_de = date('Y-m-01',strtotime("+1 month",strtotime($ussd_data['reporting_month'])));
    $current_month = date('Y-m-01');
    if($next_month_of_de <= $current_month){
        
        $qry_rep = " SELECT REPUpdateCarryForwardHF('".$next_month_of_de."','".$ussd_data['item_id']."','".$ussd_data['wh_id']."'); ";
        mysqli_query($conn, $qry_rep);
    }
    
    $hf_id = mysqli_insert_id($conn);
    //echo $hf_id;
    return $hf_id;
}
function mark_as_processed($ussd_sessions_pk_id){
    $qry_2 = "update ussd_sessions set is_processed='1' WHERE pk_id='" . $ussd_sessions_pk_id . "' ;";
    $res_2 = action_query($qry_2);
}
function get_user_id($phone_number){
   $qry_i = "
SELECT
    sysuser_tab.UserID
    FROM
        wh_user
    INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
    INNER JOIN sysuser_tab ON sysuser_tab.UserID = wh_user.sysusrrec_id
    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
    WHERE
        
            stakeholder.lvl = 7       AND 
            sysuser_tab.sysusr_ph = '".$phone_number."' limit 1 ";
   //echo $qry_i;exit;
   $result_ses = mysqli_query(connect(), $qry_i);
   $row = $result_ses->fetch_assoc();
   return $row['UserID'];
}
?>