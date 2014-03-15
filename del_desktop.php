<?php
include "Database.php";

$user = $_POST['user'];
$passwd = $_POST['passwd'];
$id = $_POST['id'];

if(!$user || !$passwd || $passwd == "" || $user == ""){
	return "FAILT_INVALID_NAME_OR_PASSWD";
}

if(!$id){
	return "FAIL_WRONG_ID";
}

$db = new Database();
$db->connect();

$output = $db->delDesktop($user,$passwd,$id);

$db->close();

echo $output;
?>