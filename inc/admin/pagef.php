<?php
$extra_page="page_fe='1'";

if($_GET["del"]){
	page_del($_GET["del"],$extra_page);
	redirect("index.php?cmd=pagef");
}

?>
<h2><?php echo $index_title;?><a href="index.php?cmd=pagefedit" class="add">Add New</a></h2>
<table class="tbl_list">
	<tr>
		<th>Title</th>
		<th class="w50 ac">Action</th>
	</tr>
<?php
$res=array();
$res=$DB->query("select * from $dbprefix"."page where $extra_page order by page_order");
if(sizeof($res)){
	foreach($res as $row){
		$id=$row["page_id"];
		$title="<a href=\"$SCRIPTURL/index.php".($row["page_index"]?"":"?p=$id")."\" target=\"_blank\" title=\"View Page\" class=\"tip\">".$row["page_title"]."</a>";
		$index=$row["page_index"]?" <img src=\"../img/index.png\" title=\"Index (Home) Page\" class=\"tip\" />":"";
?>
	<tr>
		<td><?php echo $title.$index;?></td>
		<td class="ac"><a href="index.php?cmd=pagefedit&id=<?php echo $id;?>" title="Edit" class="tip"><img src="../img/edit.png" /></a> <a href="index.php?cmd=pagef&del=<?php echo $id;?>" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete this Page?');"><img src="../img/del.png" /></a></td>
	</tr>
<?php
	}
}
?>
</table>