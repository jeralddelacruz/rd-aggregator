<?php
if($_GET["move"]){
	$DB->move("door",$_GET["move"]);
	redirect("index.php?cmd=door");
}
elseif($_GET["del"]){
	door_del($_GET["del"]);
	redirect("index.php?cmd=door");
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=dooredit" class="add">Add New</a></h2>
<div class="sus">Inactive Doorways are highlighted in red.</div>
<table class="tbl_list">
	<tr>
		<th>Title</th>
		<th class="w100 ac">Status</th>
		<th class="w50 ac">Order</th>
		<th class="w50 ac">Action</th>
	</tr>
<?php
$res=$DB->query("select * from $dbprefix"."door order by door_order");
$num=sizeof($res);
if($num){
	$i=1;
	foreach($res as $row){
		$id=$row["door_id"];
		$sus=$row["door_act"]?0:1;
?>
	<tr<?php echo $sus?" class=\"sus\"":"";?>>
		<td><?php echo $row["door_title"];?></td>
		<td class="ac"><?php echo $ACT_ARR[$row["door_act"]];?></td>
		<td><?php if($i>1){?><a href="index.php?cmd=door&move=u<?php echo $id;?>" title="Move Up"><img src="../img/oup.gif" /></a><?php }?><?php if($i<$num){?><a href="index.php?cmd=door&move=d<?php echo $id;?>" title="Move Down"><img src="../img/odown.gif" align="right" /></a><?php }?></td>
		<td class="ac"><a href="index.php?cmd=dooredit&id=<?php echo $id;?>" title="Edit" class="tip"><img src="../img/edit.png" /></a> <a href="index.php?cmd=door&del=<?php echo $id;?>" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete this Doorway?');"><img src="../img/del.png" /></a></td>
	</tr>
<?php
		$i++;
	}
}
?>
</table>