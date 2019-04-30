<?php 

?>
<head>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>
<div class="row" >
    <div  class="col-md-12 " >
        <div class="col-md-5">ِ</div>
            <div class="col-md-3 ">
               <div    style="background-image: url(mobile.jpg);height: 541px;width: 272px; font-size:.8em;padding-top:70;padding-left:40;position:absolute;z-index: 1;">&nbsp</div>
               <div  id="receive_data" style="height: 200px;width: 250px; font-size:1em;padding-top:70;padding-left:40;position:absolute;z-index: 1;"></div>
              
<!--        </div>
        
    </div>
        
    <div class="col-md-12 ">
        <div class="col-md-5">ِ</div>
            <div class="col-md-3 ">
         -->
         <div style="padding-top: 300px;position:absolute;z-index: 10;">
             Phone No:<input  id="cell_no" name="cell_no" value="" ><br/>
             User Input:<input  id="send_value" name="send_value" value="" ><br/>
               <button type="button" id="send_btn" name="send_btn" class="btn btn-success left" >Send</button>
         </div>
         <div style="padding-left: 20px;padding-top: 420px;position:absolute;z-index: 2;">
         <table  >
            
            <tr>
               <td width="80px"><input type="button" class="cell_btns" name="one" value="1" ></td>
               <td width="80px"><input type="button"  class="cell_btns" name="two" value="2" ></td>
               <td><input type="button"  class="cell_btns" name="three" value="3" ></td>
            </tr>
            <tr>
               <td><input type="button"  class="cell_btns" name="four" value="4"  ></td>
               <td><input type="button"  class="cell_btns" name="five" value="5" ></td>
               <td><input type="button"  class="cell_btns" name="six" value="6" ></td>
            </tr>
            <tr>
               <td><input type="button"  class="cell_btns" name="seven" value="7"  ></td>
               <td><input type="button"  class="cell_btns" name="eight" value="8"  ></td>
               <td><input type="button"  class="cell_btns" name="nine" value="9"  ></td>
            </tr>
            <tr>
               <td> </td>
               <td><input type="button" class="cell_btns" name="zero" value="0" ></td>
               <td> </td>
            </tr>
         </table>
             
         </div>
            </div>
    </div>
</div>

 
<script>
    function addNewlines(str) {
            var result = '';
            while (str.length > 0) {
              result += str.substring(0, 10) + '\n';
              str = str.substring(10);
            }
            return result;
          }
// Ajax call to get 
    $('#send_btn').click(function () {
        var send = '';
        send = document.getElementById("send_value").value;
        var cell_no = '';
        cell_no = document.getElementById("cell_no").value;
        if (!$.trim(send).length) {
            $('.error').css("display", "block");
            send = 'null';
        } 
        
            $.ajax({
                type: "POST",
                url: 'ussd.php',
                data: {action:'submit',token:'mGdJgx8L54Wd',data: send,cell:cell_no},
                dataType: 'html',
                success: function (data) {
                    //data = addNewlines(data);
                    $("#receive_data").html(data);
                    $("#send_value").val('');
                }
            });
        
    });
// Ajax call to get 
    $('.cell_btns').click(function () {
        var new_clicked = $(this).val();
        var send = '';
        send = $('#send_value').val();
        console.log('Clicking numberS:'+send);
        if (!$.trim(send).length) {
            //$('.error').css("display", "block");
        } else {
            
        }
        send += new_clicked;
        $('#send_value').val(send);
    });
    
    
</script>