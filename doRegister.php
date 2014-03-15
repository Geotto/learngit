<?php
include "Database.php";

$name = $_POST['name'];
$passwd = $_POST['passwd'];

if($name == "" || $passwd == "" || $name == null || $passwd == null){
    echo "用户名或密码错误";
}
else{
    $db = new Database();
    $result = $db->connect();
    if($result){
        echo "无法连接到数据库";
    }
    else{
        echo $db->addUser($name,$passwd);
    }
    
    $db->close(); 
}
?>