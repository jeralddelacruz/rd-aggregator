<?php
error_reporting(0);

session_start();
$id=$_SESSION["UserID"];

$dir="../../";

if($id&&$_POST["image"]){
	if(!is_dir($dir."upload/".$id)){
		mkdir($dir."upload/".$id,0777);
		chmod($dir."upload/".$id,0777);
	}
	if(!is_dir($dir."upload/".$id."/3d")){
		mkdir($dir."upload/".$id."/3d",0777);
		chmod($dir."upload/".$id."/3d",0777);
	}

	$name=alphanum($_GET["name"]);
	$name=$name?$name:("Graphics_".date("YmdHis"));

	$im=imagecreatefromstring(base64_decode($_POST["image"])); 
	imagepng($im,$dir."upload/".$id."/3d/".$name.".png");
	imagedestroy($im);

	header("location:".$dir."user/index.php?cmd=coverok&type=3d&name=".$name.".png");exit;
}
else{
	header("location:".$dir."user/index.php?cmd=cover");exit;
}

function rand_str($len){
	$str="";
	
	for($i=1;$i<=$len;$i++){
		$ord=rand(65,90);
		$lower=rand(0,1);
		$str.=$lower?strtolower(chr($ord)):chr($ord);
	}

	return $str;
}

function alphanum($str){
	return preg_replace("/[^a-zA-Z0-9_-]/","",$str);
}
?>