<?php
if($_GET["del"]){
	ad_del($_GET["del"]);
	redirect("index.php?cmd=ad");
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=adedit" class="add">Add New</a></h2>
<div class="sus">Inactive Ads are highlighted in red.</div>
<table class="tbl_list">
	<tr>
		<th>Title</th>
		<th class="w100 ac">Status</th>
		<th class="w100 ac">Area</th>
		<th class="w100 ac">Shows</th>
		<th class="w100 ac">Clicks</th>
		<th class="w100 ac">Expiry</th>
		<th class="w50 ac">Action</th>
	</tr>
<?php
$res=$DB->query("select * from $dbprefix"."ad order by ad_id desc");
$num=sizeof($res);
if($num){
	foreach($res as $row){
		$id=$row["ad_id"];
		$sus=(!$row["ad_act"])||($row["ad_mshow"]&&($row["ad_show"]>=$row["ad_mshow"]))||($row["ad_mclick"]&&($row["ad_click"]>=$row["ad_mclick"]))||($row["ad_expire"]&&($row["ad_expire"]<time()));
?>
	<tr<?php echo $sus?" class=\"sus\"":"";?>>
		<td><?php echo $row["ad_title"];?></td>
		<td class="ac"><?php echo $ACT_ARR[$row["ad_act"]];?></td>
		<td class="ac"><?php echo $AREA_ARR[$row["ad_area"]];?></td>
		<td class="ac"><?php echo $row["ad_show"];?></td>
		<td class="ac"><?php echo $row["ad_click"];?></td>
		<td class="ac"><?php echo $row["ad_expire"]?date("Y-m-d",$row["ad_expire"]):"N/A";?></td>
		<td class="ac"><a href="index.php?cmd=adedit&id=<?php echo $id;?>" title="Edit" class="tip"><img src="../img/edit.png" /></a> <a href="index.php?cmd=ad&del=<?php echo $id;?>" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete this Ad?');"><img src="../img/del.png" /></a></td>
	</tr>
<?php
	}
}
?>
</table>