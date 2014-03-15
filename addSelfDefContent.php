<?php
include "Database.php";

//连接数据库
$db = new Database();
$db->connect();

//获取用户信息
if(isset($_COOKIE['user']) && isset($_COOKIE['passwd'])){
  $name = $_COOKIE['user'];
  $passwd = $_COOKIE['passwd'];
}
else{
  $name = "customer";
  $passwd = md5("123456");
}

//获取参数
$desktop = $_POST['desktop'];
$siteName = $_POST['siteName'];
$siteUrl = $_POST['siteUrl'];
$siteType = $_POST['siteType'];
$tags = $_POST['tags'];

//执行操作
$result = $db->addSelfDefContent($name,$passwd,$desktop,$siteName,$siteUrl,$siteType,$tags);

echo $result;

$db->close();
?>