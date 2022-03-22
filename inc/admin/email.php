<?php
if($_POST["submit"]){
	foreach($_POST["from"] as $k=>$v){
		$from0=strip($v);
		$reply0=strip($_POST["reply"][$k]);
		$to0=strip($_POST["to"][$k]);
		$subject0=strip($_POST["subject"][$k]);
		$body0=strip($_POST["body"][$k]);
		$DB->query("update $dbprefix"."email set email_from='$from0',email_reply='$reply0',email_to='$to0',email_subject='$subject0',email_body='$body0' where email_id='$k'");
	}
	redirect("index.php?cmd=email&ok=1");
}
?>
<h2><?php echo $index_title;?></h2>
<?php
if($_GET["ok"]){
?>
<div class="ok">Changes <strong>saved</strong>.</div>
<?php
}
?>
<form method="post">
<table class="tbl_form">
<?php
$res=$DB->query("select * from $dbprefix"."email order by email_id");
foreach($res as $row){
	$key_arr=explode(";",$row["email_key"]);
	$val_arr=explode(";",$row["email_val"]);
	$str="";
	foreach($key_arr as $k=>$v){
		$str.="%".$v."% &ndash; ".$val_arr[$k]."<br />";
	}
?>
	<tr>
		<td colspan="3" class="desc"><?php echo (($row["email_id"]==1)?"":"<br />").$row["email_desc"];?></td>
	</tr>
	<tr>
		<th><label for="from<?php echo $row["email_id"];?>">From</label></th>
		<td colspan="2"><input type="text" name="from[<?php echo $row["email_id"];?>]" id="from<?php echo $row["email_id"];?>" value="<?php echo $row["email_from"];?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="reply<?php echo $row["email_id"];?>">Reply</label></th>
		<td colspan="2"><input type="text" name="reply[<?php echo $row["email_id"];?>]" id="reply<?php echo $row["email_id"];?>" value="<?php echo $row["email_reply"];?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="to<?php echo $row["email_id"];?>">To</label></th>
		<td><input type="text" name="to[<?php echo $row["email_id"];?>]" id="to<?php echo $row["email_id"];?>" value="<?php echo $row["email_to"];?>" class="text_l" maxlength="250" /></td>
		<td rowspan="3"><div class="holder"><strong>Custom Tokens:</strong><br /><?php echo $str;?></div></td>
	</tr>
	<tr>
		<th><label for="subject<?php echo $row["email_id"];?>">Subject</label></th>
		<td><input type="text" name="subject[<?php echo $row["email_id"];?>]" id="subject<?php echo $row["email_id"];?>" value="<?php echo $row["email_subject"];?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="body<?php echo $row["email_id"];?>">Body</label></th>
		<td><textarea name="body[<?php echo $row["email_id"];?>]" id="body<?php echo $row["email_id"];?>" class="area"><?php echo $row["email_body"];?></textarea></td>
	</tr>
<?php
}
?>
</table>
<div class="submit"><input type="submit" name="submit" value="Save Changes" class="button" /></div>
</form>