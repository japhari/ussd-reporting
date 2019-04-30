<?php

//require "database.php";
$screen_num = '0';
$correct_option_error = "Please type correct option\n";
$nothing_to_display = "nothing to display";
$ph_no = $_REQUEST['cell'];

function phoneFormatting($requested_phone_number) {
    if ((strpos($requested_phone_number, '3')) == 1 && (strpos($requested_phone_number, '3') != FALSE)) {
        $requested_phone_number = preg_replace('/03/', '923', $requested_phone_number, 1);
        $position = 0;
    } else if ((strpos($requested_phone_number, '2')) == 1 && (strpos($requested_phone_number, '2') != FALSE)) {

        $requested_phone_number = preg_replace('/923/', '923', $requested_phone_number, 1);
        $position = 0;
    } else if ((strpos($requested_phone_number, '9')) == 1 && (strpos($requested_phone_number, '9') != FALSE)) {
        $requested_phone_number = preg_replace('/\+92/', '', $requested_phone_number, 1);
        $requested_phone_number = preg_replace('/923/', '923', $requested_phone_number, 1);
        $position = 0;
    } else if ((strpos($requested_phone_number, '92')) == 2 && (strpos($requested_phone_number, '92') != FALSE)) {

        $requested_phone_number = preg_replace('/00923/', '923', $requested_phone_number, 1);
        $position = 0;
    }
    $requested_phone_number = trim($requested_phone_number, " ");
    return $requested_phone_number;
}
$ph_no= phoneFormatting($ph_no);
//print_r($ph_no);exit;

$menu1 = "�?یلتھ �?یسلٹی کا نام منتخب کریں <br><br>
        FWC ABC<br>
        FWC XYZ<br>
        FHC RTY";

$first_week_month = $second_week = $third_week = $fourth_week = "";

$weeks_menu = "Select Reporting Week";

$todays_date = date('Y-m-d');

$qry_trans = "SELECT
                ussd_weeks.pk_id,
                ussd_weeks.`year`,
                ussd_weeks.`month`,
                ussd_weeks.`week`,
                ussd_weeks.date_start,
                ussd_weeks.date_end
                FROM
                ussd_weeks
                WHERE
                ussd_weeks.date_start <= '".$todays_date."'
                ORDER BY
                ussd_weeks.date_start DESC
                LIMIT 4
";
$res_trans = mysqli_query(connect(), $qry_trans);
$count_inc = 1;
$weeks_raw_data = array();
while ($row_weeks = $res_trans->fetch_assoc()) {
    $weeks_raw_data[$count_inc]['disp']=date('d',strtotime($row_weeks['date_start'])).'-'.date('d M',strtotime($row_weeks['date_end']));
    $weeks_raw_data[$count_inc]['year']=$row_weeks['year'];
    $weeks_raw_data[$count_inc]['month']=$row_weeks['month'];
    $weeks_raw_data[$count_inc]['week']=$row_weeks['week'];
    $count_inc++;
}
foreach($weeks_raw_data as $serial=>$v){
    $weeks_menu .= "\n(".$serial.") ".$v['disp'];
}
$menu2  = nl2br($weeks_menu);
$menu2 .= nl2br("\n(99) -Exit Menu- \n");
//echo '<pre>';print_r($weeks_raw_data);exit;

$menu3 = nl2br("Select Option \n(1) Stock Received \n(2) Stock Consumed \n(3) Adjustment(P) \n(4) Adjustment(N) \n(5) View Stock ");
$menu3 .= nl2br("\n(99) -Exit Menu- \n");

$menuA1 = nl2br("Select Option  \n  1.Stock Increased \n 2.Stock Decreased  ");
$menuA3 = nl2br("Select Option \n ");
$trans_increase_arr = $trans_decrease_arr = array();
$qry_trans = "SELECT
tbl_trans_type.trans_id,
tbl_trans_type.trans_type,
tbl_trans_type.trans_nature,
tbl_trans_type.is_adjustment
FROM
tbl_trans_type
WHERE
tbl_trans_type.trans_nature='+'
";
$res_trans = mysqli_query(connect(), $qry_trans);
$count_inc = 1;
while ($row_trans = $res_trans->fetch_assoc()) {
    $menuA3 .= nl2br($count_inc . "." . $row_trans['trans_type'] . "\n");
    $trans_increase_arr[$count_inc] = $row_trans['trans_id'];
    $count_inc++;
}
//$menuA3.="</ol>";
$qry_trans_dec = "SELECT
tbl_trans_type.trans_id,
tbl_trans_type.trans_type,
tbl_trans_type.trans_nature,
tbl_trans_type.is_adjustment
FROM
tbl_trans_type
WHERE
tbl_trans_type.trans_nature='-'
";
$res_trans_dec = mysqli_query(connect(), $qry_trans_dec);
$count_dec = 1;
$menuA2 = nl2br("Select Option  \n   ");
while ($row_trans = $res_trans_dec->fetch_assoc()) {
    $menuA2 .= nl2br($count_dec . "." . $row_trans['trans_type'] . "\n");
    $trans_decrease_arr[$count_dec] = $row_trans['trans_id'];
    $count_dec++;
}


//$menuR1 = nl2br("\r\n Stock Receive Date (Format DD-MM-YY)");

$qry_master_id = "Select ussd_temp.wh_id from ussd_temp where phone_number='" . $ph_no . "'";
//print_r($qry_master_id);
$result_master_id = mysqli_query(connect(), $qry_master_id);
//print_r($result_master_id);
$menuA4 = '';
if (!empty($result_master_id)) {
    while ($row_master = $result_master_id->fetch_assoc()) {
        if ($row_master['wh_id'] != null) {
            $query_get_itm = "SELECT
                                itminfo_tab.itmrec_id,
                                itminfo_tab.itm_id,
                                itminfo_tab.itm_name,
                                itminfo_tab.itm_type,
                                itminfo_tab.itm_des,
                                itminfo_tab.frmindex,
                                itminfo_tab.itm_category,
                                itminfo_tab.item_unit_id
                        FROM
                        tbl_warehouse
                        INNER JOIN alerts_mapping ON tbl_warehouse.prov_id = alerts_mapping.province_id AND tbl_warehouse.stkid = alerts_mapping.stakeholder_id AND tbl_warehouse.hf_type_id = alerts_mapping.hf_type_id
                        INNER JOIN itminfo_tab ON alerts_mapping.product_id = itminfo_tab.itm_id
                        WHERE
                        tbl_warehouse.wh_id =  " . $row_master['wh_id'] . " 
                            AND
                        alerts_mapping.`value` = 'Available' 
                        ORDER BY itminfo_tab.frmindex ASC ";
            //print_r($query_get_itm);
            $count = 1;
            $cases = 1;
            $cases_to_items = array();
            $cases_to_menu = array();
            $cases_arr = array();
            $itm_arr = array();
            $menuA4 = nl2br("Select Option \n");
            $res_itm = mysqli_query(connect(), $query_get_itm);
            while ($row_itm = $res_itm->fetch_assoc()) {
                ${"menuR" . $count} = "How many " . $row_itm["itm_name"] . " Received ?";
                ${"menuC" . $count} = "How many " . $row_itm["itm_name"] . " Consumed ?";
                $cases_to_items["R-" . $cases] = $row_itm["itm_id"];
                $cases_to_items["C-" . $cases] = $row_itm["itm_id"];
                $cases_to_items[$cases]=$row_itm['itm_id'];
                $cases_to_menu["R-" . $cases] = "How many " . $row_itm["itm_name"] . " Received? ";
//                $cases_to_menu["R-" . $cases] = ${"menuR" . $count};
//                $cases_to_menu["R-" . $cases] = "is it here";
                $cases_to_menu["C-" . $cases] = ${"menuC" . $count};
                $cases_arr["R-" . $cases] = "R-" . $cases;
                $cases_arr["C-" . $cases] = "C-" . $cases;
                $itm_arr[$cases] = $row_itm['itm_id'];
//    echo ${'menuR' . $count};
//                            $menuR.$count="How many".$row_itm['itm_name']." Received";
                $menuA4 .= nl2br("(".$count . ") " . $row_itm['itm_name'] . " \n");
                $test_array["R-" . $cases] = "R-" . $cases;
                $count++;
                $cases++;
            }
        }
    }
}
$menuA4 .= nl2br("(99) -Exit Menu- \n");
//$count_1 = 1;
//$a = "menuR";
//$x = "menuR1";
//print_r($cases_to_menu);exit;
//echo '<pre>';
//print_r($itm_arr);
$menuA5 = "Quantity to be adjusted";
$menuA6 = 'Successfully entered <end>';
//$menuRR = 'Enter Quantity';
$menuAA = 'You donot have data for this product <end>';
?>