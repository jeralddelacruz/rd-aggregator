<?php
set_time_limit(0);
error_reporting(0);
session_start();

$UserID=$_SESSION["UserID"];
$PackID=$_SESSION["PackID"];
$ECG_ARR=$_SESSION["ECG_ARR"];

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

if(!$cur_user=$DB->info("user","user_id='$UserID' and user_act='1' and (user_expire='0' or user_expire>'".time()."')")){
	echo "Oops, nothing here...";
	exit;
}

if((dir_count("../upload/".$UserID."/flat")>=$ECG_ARR["flat"])||($ECG_ARR["mon"]&&($ECG_ARR["left"]<=0))){
	echo "Oops, nothing here...";
	exit;
}

$id=trim($_GET["id"]);
if($id&&(!(($cur_cover=$DB->info("cover","cover_id='$id' and user_id='$UserID'"))&&(is_dir("../aie/tmp/".$cur_cover["cover_dir"]))&&(is_file($cur_cover["cover_bg"]))))){
	echo "Oops, nothing here...";
	exit;
}

$cur_pack=$DB->info("pack","pack_id='$PackID'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Graphics Generator</title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
	<script src="../js/spectrum.js"></script>
	<script src="../js/poll.js"></script>
	<script src="../js/fancybox/jquery.fancybox.js"></script>
	<script src="../js/dropzone.js"></script>
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
	<link rel="stylesheet" href="../css/user.css" />
	<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="../css/spectrum.css" />
	<link rel="stylesheet" href="../js/fancybox/jquery.fancybox.css" />
	<link rel="stylesheet" href="../css/dropzone.css" />
	<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>
<body>
<div class="wrap" style="width:950px;border:none;">
	<div class="content" style="padding:0;">
		<div class="main" style="padding:10px 10px 10px 0px;">
			<table class="tbl_main">
				<tr>
					<td class="ad2"></td>
					<td>
<?php
include("inc.php");
?>

					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<script>
jQuery(function($){
	$(".help").tooltip({tooltipClass:"tooltip_l",position:{my:"left top+25",at:"left bottom"},track:true,show:{effect:"slideDown"},content:function(){return this.getAttribute("title");}});
	$(".tip").tooltip({tooltipClass:"tooltip_l",position:{my:"left+15 bottom",at:"right bottom"},track:true,show:{effect:"slideDown"},content:function(){return this.getAttribute("title");}});
	$(".color").spectrum({showInitial:true,showInput:true,preferredFormat:"hex"});
});
</script>
</body>
</html>