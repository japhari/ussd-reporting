<?php
include "ussd.php";
?>
<br/><br/><br/>
_________________________________________<br/>
Parameters:
<br/>_________________________________________
<br/><br/>
 <form action="" method="GET">
    Cell: <input name="cell" value="<?=(!empty($_REQUEST['cell'])?$_REQUEST['cell']:'')?>">
    <br/>
    
  <br/>
    Data:  
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=1">1</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=2">2</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=3">3</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=4">4</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=5">5</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=6">6</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=7">7</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=8">8</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=9">9</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=10">10</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=11">11</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=12">12</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=99">99</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=">[EMPTY]</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=null">null</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=alpha">alphabets</a>&nbsp;
<a href="testing_gui.php?cell=<?=$_REQUEST['cell']?>&token=<?=$_REQUEST['token']?>&data=#">#</a>&nbsp;
<br/>
    Data:<input name="data" value="">
    <input name="token" type="hidden" value="<?=(!empty($_REQUEST['token'])?$_REQUEST['token']:'')?>"><br/>
    <input type="submit">
</form>


<br/>_________________________________________
<br/><br/>
<a href="test_display.php" target="_blank">Weekly Raw Data Display</a>