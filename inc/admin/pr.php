<?php
if($_GET["add"]){
	$pid_arr=array();
	$res=$DB->query("select pr_pid from $dbprefix"."pr order by pr_id");
	if(sizeof($res)){
		foreach($res as $row){
			$pid_arr[]=$row["pr_pid"];
		}
	}

	$pr_arr=json_decode(file_get_contents("http://instantfunnellab.com/abz358/pr.php"),true);
	foreach($pr_arr as $arr){
		if(!in_array($arr["id"],$pid_arr)){
			$DB->query("insert into $dbprefix"."pr set pr_pid='".$arr["id"]."',pr_title='".addslashes($arr["title"])."',pr_desc='".addslashes($arr["desc"])."',pr_body='".addslashes($arr["body"])."',pr_cloud='".addslashes($arr["cloud"])."',pr_url='".addslashes($arr["url"])."',pr_cover='".addslashes($arr["cover"])."'");
		}
	}

	redirect("index.php?cmd=pr");
}
/*
elseif($_GET["move"]){
	$DB->move("pr",$_GET["move"]);
	redirect("index.php?cmd=pr");
}
*/
elseif($_GET["del"]){
//	pr_del($_GET["del"]);
	$DB->query("delete from $dbprefix"."pr where pr_id='".$_GET["del"]."'");
	redirect("index.php?cmd=pr");
}

?>
<h2><?php echo $index_title;?><a href="index.php?cmd=pr&add=1" class="add">Get New Products</a></h2>
<table class="tbl_list">
	<tr>
		<th class="w50 ac">#</th>
		<th class="w150 ac">eCover</th>
		<th>Title / Summary</th>
		<th class="w100">File</th>
<!--
		<th class="w50 ac">Order</th>
-->
		<th class="w50 ac">Delete</th>
	</tr>
<?php
$res=$DB->query("select * from $dbprefix"."pr order by pr_title");
$num=sizeof($res);
if($num){
	$i=1;
	foreach($res as $row){
		$id=$row["pr_id"];
		$title=$row["pr_title"];
		$url=$row["pr_cloud"]?$row["pr_cloud"]:$row["pr_url"];
		$dld=$row["pr_cloud"]?"Cloud URL":"Download File";
		$cover=$row["pr_cover"]?$row["pr_cover"]:"../upload/pr/cover.gif";
?>
	<tr>
		<td class="ac"><?php echo $i;?></td>
		<td class="ac"><a href="<?php echo $cover;?>" title="<?php echo $title;?>" class="view tip fb" rel="gal"><img src="<?php echo $cover;?>" style="max-width:100px;" /></a></td>
		<td><strong><?php echo $title;?></strong><br /><?php echo $row["pr_desc"];?></td>
		<td><a href="<?php echo $url;?>" target="_blank" title="Download" class="tip"><?php echo $dld;?></a></td>
<!--
		<td><?php if($i>1){?><a href="index.php?cmd=pr&move=u<?php echo $id;?>" title="Move Up"><img src="../img/oup.gif" /></a><?php }?><?php if($i<$num){?><a href="index.php?cmd=pr&move=d<?php echo $id;?>" title="Move Down"><img src="../img/odown.gif" align="right" /></a><?php }?></td>
-->
		<td class="ac"><a href="index.php?cmd=pr&del=<?php echo $id;?>" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete this Product?');"><img src="../img/del.png" /></a></td>
	</tr>
<?php
		$i++;
	}
}
?>
</table>
<script>
jQuery(document).ready(function($){
	$(".fb").fancybox({});
});
</script>