<?php
function getblock($str,$start="<!--begin_block-->",$end="<!--end_block-->"){
	$arr=explode("\r\n",$str);
	$s=array_search($start,$arr);
	$e=array_search($end,$arr);

	$start_arr=array();
	for($i=0;$i<$s;$i++){
		$start_arr[$i]=$arr[$i];
	}

	$block_arr=array();
	for($i=($s+1);$i<$e;$i++){
		$block_arr[$i]=$arr[$i];
	}

	$end_arr=array();
	for($i=($e+1);$i<sizeof($arr);$i++){
		$end_arr[$i]=$arr[$i];
	}

	return array("start"=>implode("\r\n",$start_arr),"block"=>implode("\r\n",$block_arr),"end"=>implode("\r\n",$end_arr));
}

function get_legal($page_id=0,$user_id=0,$blank=0,$style=""){
	global $DB,$dbprefix,$SCRIPTURL;

	$str="";
	$res=$DB->query("select * from $dbprefix"."page where page_fe='1' order by page_order");
	if(sizeof($res)){
		foreach($res as $row){
			$id=$row["page_id"];
			$title=$row["page_title"];
			$str.=(($id==$page_id)?$title:("<a href=\"$SCRIPTURL/index.php?p=$id".($user_id?"&u=$user_id":"")."\"".($blank?" target=\"_blank\"":"").">$title</a>"))." | ";
		}
	}
	$str=$str?substr($str,0,-3):"";

	return $str;
}

?>