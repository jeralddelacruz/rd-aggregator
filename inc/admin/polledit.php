<?php
$id=$_GET["id"];

if($id&&(!$row=$DB->info("poll","poll_id='$id'"))){
	redirect("index.php?cmd=poll");
}
$opt_arr=unserialize($row["poll_opt"]);

if(!$_POST["submit"]){
	if(!$id){
		$act=1;
	}
	else{
		$act=$row["poll_act"];
		$qst=$row["poll_qst"];

		foreach($opt_arr as $arr){
			$ans_arr[]=$arr["ans"];
		}
		$ans=implode("\r\n",$ans_arr);
	}
}
else{
	$act=$_POST["act"];
	$qst=strip($_POST["qst"]);
	$ans=slash(strip($_POST["ans"]));

	$ans_arr=split("\r\n",$ans);
	$_arr=array();
	foreach($ans_arr as $val){
		$val=trim($val);
		if($val){
			$_arr[]=$val;
		}
	}
	$ans_arr=$_arr;

	if(!$qst||!$ans){
		$error="All fields should be <strong>filled in</strong>.";
	}
	elseif(sizeof($ans_arr)<2){
		$error="All least two answers should be <strong>entered</strong>.";
	}
	else{
		$arr=array();
		$k=1;
		foreach($ans_arr as $v){
			$arr[$k]=array("ans"=>trim($v),"num"=>(int)$opt_arr[$k]["num"]);
			$k++;
		}
		$opt=addslashes(serialize($arr));

		if(!$id){
			$DB->query("insert into $dbprefix"."poll set poll_act='$act',poll_qst='$qst',poll_opt='$opt',poll_rd='".time()."',poll_user=''");
		}
		else{
			$DB->query("update $dbprefix"."poll set poll_act='$act',poll_qst='$qst',poll_opt='$opt' where poll_id='$id'");
		}

		redirect("index.php?cmd=poll");
	}
}

$act_str="";
foreach($ACT_ARR as $k=>$v){
	$act_str.="<option value=\"$k\"".(($k==$act)?" selected":"").">$v</option>";
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=poll" class="add">Back to Polls</a><?php if($id){?><a href="index.php?cmd=polledit" class="add">Add New Poll</a><?php }?></h2>
<?php
if($error){
?>
<div class="error"><?php echo $error;?></div>
<?php
}
?>
<form method="post">
<table class="tbl_form">
	<tr>
		<th><label for="act">Status</label></th>
		<td><select name="act" id="act" class="sel"><?php echo $act_str;?></select></td>
	</tr>
	<tr>
		<th><label for="qst">Question</label></th>
		<td><input type="text" name="qst" id="qst" value="<?php echo($_POST["submit"]?slash($qst):$qst);?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="ans">Answers</label><br /><span class="desc">Enter one Answer per line</span></th>
		<td><textarea name="ans" id="ans" class="area"><?php echo $ans;?></textarea></td>
	</tr>
</table>
<div class="submit"><input type="submit" name="submit" class="button" value="<?php echo($id?"Save Changes":"Add Poll");?>" /></div>
</form>