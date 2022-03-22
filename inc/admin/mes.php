<?php
if($_GET["del"]){
	mes_del($_GET["del"]);
	redirect("index.php?cmd=mes");
}

?>
<h2><?php echo $index_title;?><a href="index.php?cmd=mesedit" class="add">Add New</a></h2>
<table class="tbl_list">
	<tr>
		<th class="w100 ac">Date</th>
		<th>Subject</th>
		<th class="w50 ac">Views</th>
		<th class="w50 ac">Action</th>
	</tr>
<?php
$res=$DB->query("select m.*,(select count(mesview_id) from $dbprefix"."mesview v where v.mes_id=m.mes_id) as view from $dbprefix"."mes m order by mes_rd desc");
$num=sizeof($res);
if($num){
	foreach($res as $row){
		$id=$row["mes_id"];
?>
	<tr>
		<td class="ac"><?php echo date("Y-m-d H:i:s",$row["mes_rd"]);?></td>
		<td><?php echo $row["mes_title"];?></td>
		<td class="ac"><?php echo number_format($row["view"],0,".",",");?></td>
		<td class="ac"><a href="index.php?cmd=mesedit&id=<?php echo $id;?>" title="Edit" class="tip"><img src="../img/edit.png" /></a> <a href="index.php?cmd=mes&del=<?php echo $id;?>" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete this Notification?');"><img src="../img/del.png" /></a></td>
	</tr>
<?php
	}
}
?>
</table>