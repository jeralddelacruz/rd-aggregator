<?php
error_reporting(0);

include("../sys/class.db.php");
include("../sys/config.php");
include("../sys/func.php");
include("../sys/func_ui.php");

$DB=new db($dbhost,$dbuser,$dbpass,$dbname);
$DB->connect();
if($DB->connect<1){
	echo "Can't go on, DB not initialized.";
	exit;
}

session_start();

$id=$_SESSION["PollID"];
$UserID=$_SESSION["UserID"];
$ans=$_GET["ans"];

$row=$DB->info("poll","poll_id='$id' and poll_act='1'");
$opt_arr=unserialize($row["poll_opt"]);

if(!$row||!in_array($ans,array_keys($opt_arr))||ereg(";$UserID;",$row["poll_user"])){
	echo "No Poll found.";
}
else{
	$opt_arr[$ans]["num"]++;
	$opt=addslashes(serialize($opt_arr));
	$vote=$row["poll_vote"]+1;
	$user=$row["poll_user"].";$UserID;";
	$DB->query("update $dbprefix"."poll set poll_opt='$opt',poll_vote='$vote',poll_user='$user' where poll_id='$id'");
	echo poll_res(stripslashes($opt),$vote);
}
?>