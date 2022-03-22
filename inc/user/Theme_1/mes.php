<?php
include("../../../sys/class.db.php");
include("../../../sys/config.php");

$DB=new db($dbhost,$dbuser,$dbpass,$dbname);
$DB->connect();
if($DB->connect<1){
	exit;
}

$UserID=$_POST["id"];

$res=$DB->query("select count(mes_id) as mes from $dbprefix"."mes");
$mes=(int)$res[0]["mes"];

$res=$DB->query("select count(mesview_id) as view from $dbprefix"."mesview where user_id='$UserID'");
$view=(int)$res[0]["view"];

$num=$mes-$view;
$num=($num>0)?$num:0;

$body="";
$res=$DB->query("select m.*,(select count(mesview_id) from $dbprefix"."mesview v where v.mes_id=m.mes_id and v.user_id='$UserID') as view from $dbprefix"."mes m order by mes_rd desc limit 0,10");
if(sizeof($res)){
	foreach($res as $row){
		$id=$row["mes_id"];
		$title=$row["mes_title"];
		$read=$row["view"] ? "font-weight-normal" : "font-weight-bold";
		$date=date("M d, Y H:i a",$row["mes_rd"]);
		$body.="<a href='index.php?cmd=mesview&id={$id}' class='notifi__item'>
		<div class='bg-c1 img-cir img-40'>
			<i class='zmdi zmdi-email-open'></i>
		</div>
		<div class='content'>
			<p class='{$read}'>{$title}</p>
			<span class='date'>{$date}</span>
		</div>
	</a>";
	}
}
$body=$body?$body:"<div class='notifi__title'><p>System Notifications are displayed here.</p></div>";

echo $num."|".$body;
?>