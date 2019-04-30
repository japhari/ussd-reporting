<?php

session_start();

function connect() {
			$hostname = 'localhost';
			$username = "root";
			$password = "";
			$db = '';

    $conn = mysqli_connect($hostname, $username, $password, $db);
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    else
    return $conn;
}
$conn = connect();
function query($sql) {
    $conn=connect();
    $result = $conn->query($sql);
    if (!$result) {
        //error msg
        $output = "Database query failed: " . $conn->error . "<br /><br />";
        die($output);
    }
    if (mysql_num_rows($result) > 0) {
        $conn->close();
        return $result;
    } else {
        $conn->close();
        return null;
    }
}

function action_query($sql) {
    $conn=connect();
    if ($conn->query($sql) === TRUE) {
//        echo 'truee ji';
        $last_id = $conn->insert_id;
        return $last_id;
    } else {
        return null;
    }
    $conn->close();
}

function get_max_id($table,$field,$groupby,$phone_num) {
    $conn=connect();
    $x=0;
//    $phone_num = substr_replace($phone_num, 92, 0, 1);
    $qry="select  (" . $field . ") as max_val from " . $table . " where phone_number='".$phone_num."' group by ".$groupby." LIMIT 1";
//    print_r($qry);
    $result = $conn->query($qry);
    
    if (!$result) {
        //error msg
        $output = "Database query failed: " . $conn->error . "<br /><br />";
        die($output);
    }
    
    //if (mysql_num_rows($result) > 0) {
        // output data of each row
        $row = $result->fetch_array(MYSQLI_NUM);
        $conn->close();
        if (is_array($row))
            return $row[0];
        else
            return 0;
        }
     //else {
       // $conn->close();
       // return null;
    //}


