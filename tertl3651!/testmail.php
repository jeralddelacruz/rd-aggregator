<?php 

// error_reporting(0);
// including the sys files being able to connect DB
include("../sys/class.db.php");
include("../sys/config.php");
include("../sys/func.php");
include("../sys/jsonRPCClient.php");

// connecting DB
$DB=new db($dbhost,$dbuser,$dbpass,$dbname);
$DB->connect();
// if there are ploblems, just exit
if($DB->connect<1){
	exit;
}

$fname = "paul";
$lname = "eridk";
$ccustemail = "paulerickcampos24@gmail.com";
$pass = "123";

// loading Site Setup, to be used in validate functions
$res=$DB->query("select setup_key,setup_val from $dbprefix"."setup order by setup_id");
foreach($res as $row){
	$WEBSITE[$row["setup_key"]]=$row["setup_val"];
}

global $DB,$dbprefix,$WEBSITE,$SCRIPTURL,$dbkey;

sendmail(1,array("fname"=>$fname,"lname"=>$lname,"email"=>$ccustemail,"pass"=>$pass,"sitename"=>$WEBSITE["sitename"],"siteurl"=>$SCRIPTURL));

?>