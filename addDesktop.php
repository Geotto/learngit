<?php
include "Database.php";

//验证用户
if(isset($_COOKIE['user']) && isset($_COOKIE['passwd'])){
	$name = $_COOKIE['user'];
    $passwd = $_COOKIE['passwd'];
}
else{
    $name = "customer";
    $passwd = md5("123456");
}

$request = $_POST['request'];
if(!$request || $request != "add_button"){
	echo "无效的请求";
    return;
}

$db = new Database();
$db->connect();

$result = $db->addDesktop($name,$passwd);
echo $result."，5秒后自动刷新";

$db->close();
?>
