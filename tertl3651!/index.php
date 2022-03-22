<?php
set_time_limit(0);
error_reporting(0);
session_set_cookie_params(14400,"/");
session_start();

$dir="../sys";
$fp=opendir($dir);
while(($file=readdir($fp))!=false){
	$file=trim($file);
	if(($file==".")||($file=="..")){continue;}
	$file_parts=pathinfo($dir."/".$file);
	if($file_parts["extension"]=="php"){
		include($dir."/".$file);
	}
}
closedir($fp);

$DB=new db($dbhost,$dbuser,$dbpass,$dbname);
$DB->connect();
if($DB->connect<1){
	echo "Can't go on, DB not initialized.";
	exit;
}

$AdminName=$_SESSION["AdminName"];
$AdminPass=$_SESSION["AdminPass"];
if($_POST["LoginSubmit"]){
	$AdminName=strip($_POST["LoginName"]);
	$AdminPass=md5(strip($_POST["LoginPass"]));
}

$cur_admin=$DB->info("admin","admin_nick='$AdminName' and admin_pass='$AdminPass'");
if($cur_admin){
	$_SESSION["AdminName"]=$AdminName;
	$_SESSION["AdminPass"]=$AdminPass;
	$UserID=0;
	$_SESSION["UserID"]=$UserID;
	$AdminID=$cur_admin["admin_id"];

	if($AdminID<>1){
		unset($ADMIN_CMD["admin"]);
		unset($ADMIN_CMD["adminedit"]);
		unset($ADMIN_MENU["admin"]);

		$ar_arr=explode(";",$cur_admin["admin_ar"]);
		foreach($ADMIN_AR_ARR as $row){
			if(in_array($row["menu"],$ar_arr)){continue;}
			foreach($row["cmd"] as $c){
				unset($ADMIN_CMD[$c]);
			}
			unset($ADMIN_MENU[$row["menu"]]);
		}
	}
}

$cmd=$_GET["cmd"];
$CMD_KEY=array_keys($ADMIN_CMD);
if(!($cur_admin&&in_array($cmd,$CMD_KEY))){
	$cmd="login";
	$cur_admin=false;
}

if($cmd=="login"){
	unset($_SESSION["AdminName"]);
	unset($_SESSION["AdminPass"]);
	unset($_SESSION["UserID"]);
}

$index_title=$ADMIN_CMD[$cmd][0];

$res=$DB->query("select setup_key,setup_val from $dbprefix"."setup order by setup_id");
foreach($res as $row){
	$WEBSITE[$row["setup_key"]]=$row["setup_val"];
}
if(!in_array($WEBSITE["cdb"],array_keys($ECG_PDF_ARR))){
	$WEBSITE["cdb"]="cdb";
}
$WEBSITE["cdb_url"]=$ECG_PDF_ARR[$WEBSITE["cdb"]];
$_SESSION["CDB_URL"]=$WEBSITE["cdb_url"];

if($WEBSITE["icon"]) {
    $WEBSITE["icon"] = $WEBSITE["icon"] . "?v=" . rand(1, 1000);
}

$THEME = $DB->query("SELECT * from $dbprefix"."themes WHERE selected = 1");
$THEME_FONT = $DB->query("SELECT * from $dbprefix"."fonts WHERE selected = 1");
$THEME_COLOR = $DB->query("SELECT * from $dbprefix"."colors WHERE selected = 1");

ob_start();
include("../inc/admin/header.php");
include("../inc/admin/$cmd".".php");
include("../inc/admin/footer.php");
ob_end_flush();
?>