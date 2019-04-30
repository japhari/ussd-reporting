<?php
require "database.php"; 
$query_get_hf = "SELECT
ussd_log.pk_id,
ussd_log.insertion_date,
ussd_log.phone_number,
ussd_log.data_inserted
FROM
ussd_log
ORDER BY
ussd_log.insertion_date DESC

";
$res_hf = mysqli_query(connect(), $query_get_hf);

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
<?php
echo 'Showing data as of :'.date('Y-M-d H:i:s A').'<br/>';
?>
<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for phonenumber , date time ..." title="Type in to search">
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
    td = tr[i].getElementsByTagName("td")[2];
    //td += tr[i].getElementsByTagName("td")[5];
    
    
    if (td) {
      txtValue = td.textContent || td.innerText;
    
        td = tr[i].getElementsByTagName("td")[3];
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
