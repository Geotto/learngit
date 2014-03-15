<?php
include "Database.php";

$user = $_POST['user'];
$passwd = $_POST['passwd'];
$desktop = $_POST['desktop'];
$indexs = $_POST['indexs'];

if(!$user || !$passwd || $passwd == "" || $user == ""){
	return "FAILT_INVALID_NAME_OR_PASSWD";
}

if($indexs == "" || $desktop == ""){
	return "FAIL_WRONG_ID_OR_DESKTOP";
}

$db = new Database();
$db->connect();

$output = $db->delIcon($user,$passwd,$desktop,$indexs);

$db->close();

echo $output;
?>