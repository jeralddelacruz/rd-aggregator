<?php
$id=$_GET["id"];

if($id&&(!$row=$DB->info("bg","bg_id='$id'"))){
	redirect("index.php?cmd=bg");
}

$bg_arr=$DB->get_pack();

if($id){
	$prev_img="<img src=\"../aie/upload/bg/".$row["bg_id"]."_prev_s.".$row["bg_prev"]."\" />";
	$empty_img="<img src=\"../aie/upload/bg/".$row["bg_id"]."_empty_s.".$row["bg_empty"]."\" />";
}

if(!$_POST["submit"]){
	if(!$id){
		$pack_arr=array();
	}
	else{
		$pack_arr=split(";",trim($row["bg_pack"],";"));
	}
}
else{
	$pack_arr=$_POST["pack"];

	$error="";
	if(!sizeof($pack_arr)){
		$error.="&bull; At least one <strong>Membership</strong> should be <strong>chosen</strong>.<br />";
	}
	if(!$id&&(!$_FILES["prev"]||!getimagesize($_FILES["prev"]["tmp_name"]))){
		$error.="&bull; Invalid <strong>Preview Template</strong> file type.<br />";
	}
	if(!$id&&(!$_FILES["empty"]||!getimagesize($_FILES["empty"]["tmp_name"]))){
		$error.="&bull; Invalid <strong>Empty Template</strong> file type.<br />";
	}

	if(!$error){
		$pack=";".implode(";",$pack_arr).";";

		if(!$id){
			$id=$DB->getauto("bg");
			$dir="../aie/upload/bg";

			$_arr=pathinfo($_FILES["prev"]["name"]);
			$prev=$_arr["extension"];
			$file=$dir."/".$id."_prev.".$prev;
			$file_n=$dir."/".$id."_prev_s.".$prev;
			move_uploaded_file($_FILES["prev"]["tmp_name"],$file);
			$im=new imageLib($file);
			$im->resizeImage(125,200,2);
			$im->saveImage($file_n,75);

			$_arr=pathinfo($_FILES["empty"]["name"]);
			$empty=$_arr["extension"];
			$file=$dir."/".$id."_empty.".$empty;
			$file_n=$dir."/".$id."_empty_s.".$empty;
			move_uploaded_file($_FILES["empty"]["tmp_name"],$file);
			$im=new imageLib($file);
			$im->resizeImage(125,200,2);
			$im->saveImage($file_n,75);

			$DB->query("insert into $dbprefix"."bg set bg_id='$id',bg_pack='$pack',bg_prev='$prev',bg_empty='$empty'");
		}
		else{
			$DB->query("update $dbprefix"."bg set bg_pack='$pack' where bg_id='$id'");
		}

		redirect("index.php?cmd=bg");
	}
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=bg" class="add">Back to Templates</a><?php if($id){?><a href="index.php?cmd=bgedit" class="add">Add New Template</a><?php }?></h2>
<?php
if($error){
?>
<div class="error"><?php echo $error;?></div>
<?php
}
?>
<form method="post" enctype="multipart/form-data">
<table class="tbl_form">
	<tr>
		<th>Available for</th>
		<td>
<?php
if(sizeof($bg_arr)){
	foreach($bg_arr as $k=>$v){
?>
			<input type="checkbox" name="pack[]" id="pack<?php echo $k;?>" value="<?php echo $k;?>"<?php echo (in_array($k,$pack_arr)?" checked":"");?> /> <label for="pack<?php echo $k;?>"><?php echo $v;?></label><br />
<?php
	}
}
?>
		</td>
	</tr>
	<tr>
		<th><label for="prev">Preview Template</label></th>
		<td><?php if(!$id){?><input type="file" name="prev" id="prev" /><?php }else{echo $prev_img;}?></td>
	</tr>
	<tr>
		<th><label for="empty">Empty Template</label></th>
		<td><?php if(!$id){?><input type="file" name="empty" id="empty" /><?php }else{echo $empty_img;}?></td>
	</tr>
</table>
<div class="submit"><input type="submit" name="submit" class="button" value="<?php echo($id?"Save Changes":"Add Template");?>" /></div>
</form>