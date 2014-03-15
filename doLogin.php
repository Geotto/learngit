<?php
include "Database.php";

header("Charset: utf-8");

$name = $_POST['name'];
$passwd = $_POST['passwd'];
$keep_login = $_POST['keep_login'];

if(!$name || !$passwd || $name=="" || $passwd == ""){
    echo "FAIL_INVALID_NAME_OR_PASSWD";
}
else{
    $passwd = md5($passwd);
    
    $db = new Database();
    $result = $db->connect();
    if($result){
        echo "FAIL_UNABLE_CONNECTED_TO_DATABASE";
    }
    else{
        $output = $db->login($name,$passwd);
        if($output == "SUCCESS"){
            if($keep_login == "true"){
                setcookie("user",$name);
                setcookie("passwd",$passwd);
            }
            else{
                setcookie("user",$name,time()+3600);
                setcookie("passwd",$passwd,time()+3600);
           	}
            
            echo "SUCCESS";
        }
        else{
        	echo "FAIL_CAN_NOT_LOGIN";
        }
    }
    
    $db->close();
}
?>