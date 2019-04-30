<?php
require "my_functions.php";
require "constants.php";
$query_get_hf = "SELECT
ussd_session_master.phone_number,
ussd_weeks.date_start,
ussd_weeks.date_end,
ussd_session_master.wh_name,
ussd_session_master.wh_id,
itminfo_tab.itm_name,
ussd_sessions.stock_received,
ussd_sessions.stock_consumed,
ussd_sessions.stock_adjustment_p as positive_adj,
ussd_sessions.stock_adjustment_n as negative_adj
FROM
ussd_session_master
INNER JOIN ussd_sessions ON ussd_session_master.pk_id = ussd_sessions.ussd_master_id
INNER JOIN ussd_weeks ON ussd_session_master.reporting_year = ussd_weeks.`year` AND ussd_session_master.reporting_month = ussd_weeks.`month` AND ussd_session_master.week_number = ussd_weeks.`week`
INNER JOIN itminfo_tab ON ussd_sessions.item_id = itminfo_tab.itm_id
ORDER BY 
ussd_session_master.wh_id,
ussd_session_master.week_start_date
";
$res_hf = mysqli_query(connect(), $query_get_hf);
$menu = $menu2;
while ($row = $res_hf->fetch_assoc()) {
   $display_data[] = $row;
   $row2=$row;
   //echo '<pre>';print_r($row);
}

foreach($row2 as $k=>$v)
{
   $columns_data[] = $k;
}
//echo '<pre>';print_r($columns_data);print_r($display_data);
?>
<head><style>
* {
  box-sizing: border-box;
}

#myInput {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  width: 80%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}

#myTable {
  border-collapse: collapse;
  width: 80%;
  border: 1px solid #ddd;
  font-size: 14px;
}

#myTable th, #myTable td {
  text-align: left;
  padding: 5px;
}

#myTable tr {
  border-bottom: 1px solid #ddd;
}

#myTable tr.header, #myTable tr:hover {
  background-color: #f1f1f1;
}
</style></head>
<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for phonenumber , warehouse name , product name.." title="Type in a name">
<table border="1" id="myTable"  class="table table-condensed table-striped left" >
    <tr bgcolor="#afb5ea">
        <?php
        echo '<th>#</th>';
        foreach($columns_data as $k=>$v)
        {
           echo '<th>'.$v.'</th>';
        }
        ?>
    </tr>
    
    <?php
    $count_of_row = 0;
        foreach($display_data as $k => $disp)
        {
           echo '<tr>';
           echo '<td>'.++$count_of_row.'</td>';
           foreach($columns_data as $k2=>$col)
           {
            echo ' <td>'.$disp[$col].'</td>';
           }   
           echo '<tr>';
        }
        ?>
</table>
    </body>
    
<script src="<?php echo PUBLIC_URL;?>js/jquery-1.4.4.js" type="text/javascript"></script>
<script src="<?php echo PUBLIC_URL;?>js/custom_table_sort.js" type="text/javascript"></script>
<script>
function myFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[4];
    //td += tr[i].getElementsByTagName("td")[5];
    
    
    if (td) {
      txtValue = td.textContent || td.innerText;
    
        td = tr[i].getElementsByTagName("td")[6];
        txtValue += td.textContent || td.innerText;
        td = tr[i].getElementsByTagName("td")[1];
        txtValue += td.textContent || td.innerText;
        td = tr[i].getElementsByTagName("td")[2];
        txtValue += td.textContent || td.innerText;
      //console.log(txtValue);
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>
</html>
