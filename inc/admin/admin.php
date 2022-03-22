<?php
if($_GET["del"]){
	admin_del($_GET["del"]);
	redirect("index.php?cmd=admin");
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=adminedit" class="add">Add New</a></h2>
<table class="tbl_list">
	<tr>
		<th class="w100 ac">Admin ID</th>
		<th class="w200">Username</th>
		<th>Access Rights</th>
		<th class="w50 ac">Action</th>
	</tr>
<?php
$res=$DB->query("select * from $dbprefix"."admin where admin_id<>'1' order by admin_id");
if(sizeof($res)){
	foreach($res as $row){
		$id=$row["admin_id"];

		$ar_arr=explode(";",$row["admin_ar"]);
		foreach($ADMIN_AR_ARR as $val){
			if(in_array($val["menu"],$ar_arr)){
				$ar.=$val["title"].", ";
			}
		}
		$ar=$ar?substr($ar,0,strlen($ar)-2):"";
?>
	<tr>
		<td class="ac"><?php echo $id;?></td>
		<td><?php echo $row["admin_nick"];?></td>
		<td><?php echo $ar;?></td>
		<td class="ac"><a href="index.php?cmd=adminedit&id=<?php echo $id;?>" title="Edit" class="tip"><img src="../img/edit.png" /></a> <a href="index.php?cmd=admin&del=<?php echo $id;?>" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete this Admin Member?');"><img src="../img/del.png" /></a></td>
	</tr>
<?php
	}
}
?>
</table>