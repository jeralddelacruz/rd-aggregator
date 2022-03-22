<?php
if($_POST["submit"]){
	$title=strip($_POST["title"]);
	if($title){
		$order=$DB->getmaxval("lgt_order","lgt","user_id='$UserID'")+1;
		$DB->query("insert into $dbprefix"."lgt set user_id='$UserID',lgt_title='$title',lgt_order='$order'");
	}

	$title_arr=$_POST["title_arr"];
	if(sizeof($title_arr)){
		foreach($title_arr as $k=>$v){
			$v=strip($v);
			if($v){
				$DB->query("update $dbprefix"."lgt set lgt_title='$v' where lgt_id='$k' and user_id='$UserID'");
			}
		}
	}

	$del_arr=$_POST["del_arr"];
	if(sizeof($del_arr)){
		$DB->query("delete from $dbprefix"."lgt where lgt_id IN (".implode(",",$del_arr).") and user_id='$UserID'");
	}

	redirect("index.php?cmd=lgt");
}
elseif($_GET["move"]){
	$DB->move("lgt",$_GET["move"],"user_id='$UserID'");
	redirect("index.php?cmd=lgt");
}

?>
<h2><?php echo $index_title;?></h2>
<form method="post">
<table class="tbl_list">
	<tr>
		<td class="ac b">Add New</td>
		<td><input type="text" name="title" class="text_l" placeholder="Enter Term" /></td>
		<td colspan="2" class="ar"><input type="submit" name="submit" value="Save Changes" class="button" style="margin:0;" /></td>
	</tr>
	<tr>
		<th class="w100 ac">#</th>
		<th>Term</th>
		<th class="w50 ac">Order</th>
		<th class="w100 ac">Delete</th>
	</tr>
<?php
$res=$DB->query("select * from $dbprefix"."lgt where user_id='$UserID' order by lgt_order");
$num=sizeof($res);
if($num){
	$i=1;
	foreach($res as $row){
		$id=$row["lgt_id"];
?>
	<tr>
		<td class="ac"><?php echo $i;?></td>
		<td><input type="text" name="title_arr[<?php echo $id;?>]" value="<?php echo $row["lgt_title"];?>" class="text_l" /></td>
		<td><?php if($i>1){?><a href="index.php?cmd=lgt&move=u<?php echo $id;?>" title="Move Up" class="tip"><img src="../img/oup.gif" /></a><?php }?><?php if($i<$num){?><a href="index.php?cmd=lgt&move=d<?php echo $id;?>" title="Move Down" class="tip"><img src="../img/odown.gif" align="right" /></a><?php }?></td>
		<td class="ac"><input type="checkbox" name="del_arr[]" value="<?php echo $id;?>" /></td>
	</tr>
<?php
		$i++;
	}
}
?>
</table>
</form>