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

$db = new Database();
$db->connect();

//获取参数
$desktop = $_POST['desktop'];
$sites = $_POST['sites'];

$args = explode("&sep0;",$sites);
for($i=0;$i<sizeof($args);$i++){
  $result = $db->addIcon($name,$passwd,$args[$i],$desktop);
  if($result < 0){
    echo $result;
    return;
  }
}
$db->close();

echo $result;
?>
