<?php
error_reporting(0);

session_start();
$id=$_SESSION["UserID"];

$dir="../../../";

if($id&&$_POST["image"]){
	$im=imagecreatefromstring(base64_decode($_POST["image"]));
	$file=$dir."tmp/cover".$id."_".date("YmdHis").".png";
	imagepng($im,$file);
	imagedestroy($im);

	header("location:".$file);exit;
}
else{
	header("location:".$dir."user/index.php?cmd=bonus");exit;
}
?>