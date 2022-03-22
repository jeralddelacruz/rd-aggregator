<?php
if($_GET["move"]){
	$DB->move("art",$_GET["move"]);
	redirect("index.php?cmd=art");
}
elseif($_GET["del"]){
	art_del($_GET["del"]);
	redirect("index.php?cmd=art");
}

?>
<h2><?php echo $index_title;?><a href="index.php?cmd=artedit" class="add">Add New</a></h2>
<table class="tbl_list">
	<tr>
		<th class="w400">Title</th>
		<th>Summary</th>
		<th class="w50 ac">Order</th>
		<th class="w50 ac">Action</th>
	</tr>
<?php
$res=$DB->query("select * from $dbprefix"."art order by art_order");
$num=sizeof($res);
if($num){
	$i=1;
	foreach($res as $row){
		$id=$row["art_id"];
?>
	<tr>
		<td><?php echo $row["art_title"];?></td>
		<td><?php echo $row["art_desc"];?></td>
		<td><?php if($i>1){?><a href="index.php?cmd=art&move=u<?php echo $id;?>" title="Move Up"><img src="../img/oup.gif" /></a><?php }?><?php if($i<$num){?><a href="index.php?cmd=art&move=d<?php echo $id;?>" title="Move Down"><img src="../img/odown.gif" align="right" /></a><?php }?></td>
		<td class="ac"><a href="index.php?cmd=artedit&id=<?php echo $id;?>" title="Edit" class="tip"><img src="../img/edit.png" /></a> <a href="index.php?cmd=art&del=<?php echo $id;?>" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete this Article?');"><img src="../img/del.png" /></a></td>
	</tr>
<?php
		$i++;
	}
}
?>
</table>