<?php
set_time_limit(0);
error_reporting(0);
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

$arr=array("kw","cta","aff","sm","yt","inc");
foreach($arr as $val){
	${$val}=$_POST[$val];
}

$desc="$kw
$cta $aff";

$vid=(int)$_POST["vid"];
$UserID=$_SESSION["UserID"];
if($vid&&($inc=="true")&&$cur_vid=$DB->info("vid","vid_id='$vid' and user_id='$UserID'")){
$desc.="

".preg_replace("/\r\n###\d+/","",htmlspecialchars_decode($cur_vid["vid_body"],ENT_QUOTES))."
";
}

$desc.="
FOLLOW ME ON SOCIAL MEDIA
$sm
SUBSCRIBE TO THIS CHANNEL
$yt
$kw
$cta $aff";

$tag="";
if($_POST["tag"]!="null"){
	$tag_arr=explode(",",$_POST["tag"]);
	foreach($tag_arr as $val){
		$tag.=str_replace("%kw%",$kw,$GTAG_ARR[$val]).",";
	}
	$tag=substr($tag,0,-1);
}

echo json_encode(array("title"=>$kw,"desc"=>$desc,"tag"=>$tag));
?>