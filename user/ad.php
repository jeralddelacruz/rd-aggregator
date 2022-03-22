<?php
include("../sys/class.db.php");
include("../sys/config.php");

$DB=new db($dbhost,$dbuser,$dbpass,$dbname);
$DB->connect();
if($DB->connect<1){
	echo "Can't go on, DB not initialized.";
	exit;
}

session_start();

$area=(int)$_GET["area"];
$key="AdArea$area";

if(isset($_SESSION[$key])){
	$ad_id=$_SESSION[$key]["id"];
	$ad_url=$_SESSION[$key]["url"];

	$DB->query("update $dbprefix"."ad set ad_click=(ad_click+1) where ad_id='$ad_id'");

	header("location:$ad_url");exit;
}
else{
	echo "An error has occurred.";exit;
}
?>