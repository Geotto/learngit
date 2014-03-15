<?php
include "Database.php";

header("Charset: utf-8");

$db = new Database();
$result = $db->connect();
if($result){
    echo "无法连接到数据库";
}
else{
	echo $db->getContent();
}
?>