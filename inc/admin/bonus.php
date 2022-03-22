<?php
if($_GET["move"]){
	$DB->move("bonus",$_GET["move"]);
	redirect("index.php?cmd=bonus");
}
elseif($_GET["del"]){
	bonus_del($_GET["del"]);
	redirect("index.php?cmd=bonus");
}

$bonus_arr=$DB->get_pack();
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=bonusedit" class="add">Add New</a></h2>
<table class="tbl_list">
	<tr>
		<th class="w400">Title</th>
		<th>Available for</th>
		<th class="w50 ac">Order</th>
		<th class="w50 ac">Action</th>
	</tr>
<?php
$res=$DB->query("select * from $dbprefix"."bonus order by bonus_order, bonus_id desc");
$num=sizeof($res);
if($num){
	$i=1;
	foreach($res as $row){
		$id=$row["bonus_id"];
		$pack_arr=split(";",trim($row["bonus_pack"],";"));
		$pack="";
		foreach($pack_arr as $v){
			if($bonus_arr[$v]){
				$pack.=$bonus_arr[$v].", ";
			}
		}
		$pack=substr($pack,0,strlen($pack)-2);
?>
	<tr>
		<td><?php echo $row["bonus_title"];?></td>
		<td><?php echo $pack;?></td>
		<td><?php if($i>1){?><a href="index.php?cmd=bonus&move=u<?php echo $id;?>" title="Move Up"><img src="../img/oup.gif" /></a><?php }?><?php if($i<$num){?><a href="index.php?cmd=bonus&move=d<?php echo $id;?>" title="Move Down"><img src="../img/odown.gif" align="right" /></a><?php }?></td>
		<td class="ac"><a href="index.php?cmd=bonusedit&id=<?php echo $id;?>" title="Edit" class="tip"><img src="../img/edit.png" /></a> <a href="index.php?cmd=bonus&del=<?php echo $id;?>" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete this Bonus?');"><img src="../img/del.png" /></a></td>
	</tr>
<?php
		$i++;
	}
}
?>
</table>