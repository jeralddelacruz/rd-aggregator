<?php
if($_POST["submit"]){
	$del_arr=$_POST["del_arr"];
	if(sizeof($del_arr)){
		foreach($del_arr as $del){
			if($row=$DB->info("bg","bg_id='$del'")){
				$DB->query("delete from $dbprefix"."bg where bg_id='$del'");
				unlink("../aie/upload/bg/".$del."_prev.".$row["bg_prev"]);
				unlink("../aie/upload/bg/".$del."_prev_s.".$row["bg_prev"]);
				unlink("../aie/upload/bg/".$del."_empty.".$row["bg_empty"]);
				unlink("../aie/upload/bg/".$del."_empty_s.".$row["bg_empty"]);
			}
		}
	}

	redirect("index.php?cmd=bg");
}

$bg_arr=$DB->get_pack();
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=bgedit" class="add">Add New</a></h2>
<form method="post">
<table class="tbl_list">
	<tr>
		<th class="w200 ac">Preview Template</th>
		<th class="w200 ac">Empty Template</th>
		<th>Available for</th>
		<th class="w100 ac">Delete</th>
	</tr>
<?php
$res=$DB->query("select * from $dbprefix"."bg order by bg_id desc");
$num=sizeof($res);
if($num){
	foreach($res as $row){
		$id=$row["bg_id"];
		$pack_arr=split(";",trim($row["bg_pack"],";"));
		$pack="";
		foreach($pack_arr as $v){
			if($bg_arr[$v]){
				$pack.=$bg_arr[$v].", ";
			}
		}
		$pack=substr($pack,0,strlen($pack)-2);
?>
	<tr>
		<td class="ac"><a href="../aie/upload/bg/<?php echo $id."_prev.".$row["bg_prev"];?>" class="fb" rel="prev"><img src="../aie/upload/bg/<?php echo $id."_prev_s.".$row["bg_prev"];?>" /></a></td>
		<td class="ac"><a href="../aie/upload/bg/<?php echo $id."_empty.".$row["bg_empty"];?>" class="fb" rel="empty"><img src="../aie/upload/bg/<?php echo $id."_empty_s.".$row["bg_empty"];?>" /></a></td>
		<td><a href="index.php?cmd=bgedit&id=<?php echo $id;?>" title="Edit" class="tip"><?php echo $pack;?></a></td>
		<td class="ac"><input type="checkbox" name="del_arr[]" value="<?php echo $id;?>" /></td>
	</tr>
<?php
	}
}
?>
	<tr>
		<td colspan="3"></td>
		<td class="ac"><input type="submit" name="submit" value="Delete" class="button"<?php echo $num?"":" disabled";?> /></td>
	</tr>
</table>
</form>
<script>
jQuery(function($){
	$(".fb").fancybox({"title":""});
});
</script>