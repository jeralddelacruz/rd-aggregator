<?php
if($_GET["del"]){
	poll_del($_GET["del"]);
	redirect("index.php?cmd=poll");
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=polledit" class="add">Add New</a></h2>
<div class="sus">Inactive Polls are highlighted in red.</div>
<table class="tbl_list">
	<tr>
		<th>Question</th>
		<th class="w100 ac">Status</th>
		<th class="w100 ac">Answers</th>
		<th class="w100 ac">Votes</th>
		<th class="w100 ac">Added</th>
		<th class="w75 ac">Action</th>
	</tr>
<?php
$res=$DB->query("select * from $dbprefix"."poll order by poll_rd desc");
$num=sizeof($res);
if($num){
	foreach($res as $row){
		$id=$row["poll_id"];
		$opt_arr=unserialize($row["poll_opt"]);
		$sus=$row["poll_act"]?0:1;
?>
	<tr<?php echo $sus?" class=\"sus\"":"";?>>
		<td><?php echo $row["poll_qst"];?></td>
		<td class="ac"><?php echo $ACT_ARR[$row["poll_act"]];?></td>
		<td class="ac"><?php echo sizeof($opt_arr);?></td>
		<td class="ac"><?php echo $row["poll_vote"];?></td>
		<td class="ac"><?php echo date("Y-m-d",$row["poll_rd"]);?></td>
		<td class="ac"><a href="index.php?cmd=pollview&id=<?php echo $id;?>" title="View Results" class="tip"><img src="../img/view.png" /></a> <a href="index.php?cmd=polledit&id=<?php echo $id;?>" title="Edit" class="tip"><img src="../img/edit.png" /></a> <a href="index.php?cmd=poll&del=<?php echo $id;?>" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete this Poll?');"><img src="../img/del.png" /></a></td>
	</tr>
<?php
	}
}
?>
</table>