<?php
$id=$_GET["id"];

if($id&&(!$row=$DB->info("pr","pr_id='$id'"))){
	redirect("index.php?cmd=pr");
}

if($_GET["del"]){
	$del=$_GET["del"];
	$dir="../upload/pr/".$id;

	if(is_dir($dir)){
		if(($del=="dld")&&$row["pr_dld"]){
			@unlink($dir."/".$row["pr_dld"]);

			$DB->query("update $dbprefix"."pr set pr_dld='' where pr_id='$id'");
		}
		elseif(($del=="plr")&&$row["pr_plr"]){
			@unlink($dir."/".$row["pr_plr"]);

			$DB->query("update $dbprefix"."pr set pr_plr='' where pr_id='$id'");
		}
		elseif(($del=="cover")&&$row["pr_cover"]){
			@unlink($dir."/".$row["pr_cover"]);

			$DB->query("update $dbprefix"."pr set pr_cover='' where pr_id='$id'");
		}
	}

	redirect("index.php?cmd=predit&id=".$id);
}

if(!$_POST["submit"]){
	if($id){
		$title=$row["pr_title"];
		$pop=$row["pr_pop"];
		$desc=$row["pr_desc"];
		$body=$row["pr_body"];
		$dld=$row["pr_dld"];
		$plr=$row["pr_plr"];
		$cover=$row["pr_cover"];
	}
}
else{
	$title=strip($_POST["title"]);
	$pop=(int)$_POST["pop"];
	$desc=strip($_POST["desc"],0);
	$body=strip($_POST["body"],0);
	$dld=$row["pr_dld"]?$row["pr_dld"]:$_FILES["dld"]["name"];
	$plr=$row["pr_plr"]?$row["pr_plr"]:$_FILES["plr"]["name"];
	$cover=$row["pr_cover"]?$row["pr_cover"]:$_FILES["cover"]["name"];

	$error="";
	if(!$title||!$dld||!$plr){
		$error.="&bull; Required fields should be <strong>filled in</strong>.<br />";
	}

	if(!$error){
		if(!$id){
			$id=$DB->getauto("pr");
			$order=$DB->getmaxval("pr_order","pr")+1;

			$DB->query("insert into $dbprefix"."pr set pr_id='$id',pr_title='$title',pr_pop='$pop',pr_desc='$desc',pr_body='$body',pr_order='$order'");

			@mkdir("../upload/pr/".$id,0777);
			@chmod("../upload/pr/".$id,0777);
		}
		else{
			$DB->query("update $dbprefix"."pr set pr_title='$title',pr_pop='$pop',pr_desc='$desc',pr_body='$body' where pr_id='$id'");
		}

		if($_FILES["dld"]){
			$dld=$_FILES["dld"]["name"];

			@move_uploaded_file($_FILES["dld"]["tmp_name"],"../upload/pr/".$id."/".$dld);

			$DB->query("update $dbprefix"."pr set pr_dld='$dld' where pr_id='$id'");
		}
		if($_FILES["plr"]){
			$plr=$_FILES["plr"]["name"];

			@move_uploaded_file($_FILES["plr"]["tmp_name"],"../upload/pr/".$id."/".$plr);

			$DB->query("update $dbprefix"."pr set pr_plr='$plr' where pr_id='$id'");
		}
		if($_FILES["cover"]&&getimagesize($_FILES["cover"]["tmp_name"])){
			$cover=$_FILES["cover"]["name"];

			@move_uploaded_file($_FILES["cover"]["tmp_name"],"../upload/pr/".$id."/".$cover);

			$DB->query("update $dbprefix"."pr set pr_cover='$cover' where pr_id='$id'");
		}

		redirect("index.php?cmd=pr");
	}
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=pr" class="add">Back to Products</a><?php if($id){?><a href="index.php?cmd=predit" class="add">Add New Product</a><?php }?></h2>
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
		<th><label for="title">Product Title</label></th>
		<td><input type="text" name="title" id="title" value="<?php echo($_POST["submit"]?slash($title):$title);?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="pop">Featured Product</label></th>
		<td><input type="checkbox" name="pop" id="pop" value="1"<?php if($pop){echo " checked";}?> /></td>
	</tr>
	<tr>
		<th><label for="desc">Summary</label> <span class="desc">(optional)</span></th>
		<td><textarea name="desc" id="desc" class="area"><?php echo($_POST["submit"]?slash($desc):$desc);?></textarea></td>
	</tr>
	<tr>
		<th><label for="body">Description</label> <span class="desc">(optional)</span></th>
		<td><textarea name="body" id="body"><?php echo $_POST["submit"]?slash($body):$body;?></textarea></td>
	</tr>
	<tr>
		<th><label for="dld">Product File</label><img src="../img/help.png" title="May be of any file type." class="help" /></th>
		<td><?php if($dld){?><a href="../upload/pr/<?php echo $id."/".$dld;?>" target="_blank" title="Download" class="tip"><?php echo $dld;?></a> <a href="index.php?cmd=predit&id=<?php echo $id;?>&del=dld" onclick="return confirm('Are you sure you wish to delete the Product File?');"><img src="../img/del.png" title="Delete" class="tip" /></a><?php }else{?><input type="file" name="dld" id="dld" /><?php }?></td>
	</tr>
	<tr>
		<th><label for="plr">Product License</label><img src="../img/help.png" title="May be of any file type." class="help" /></th>
		<td><?php if($plr){?><a href="../upload/pr/<?php echo $id."/".$plr;?>" target="_blank" title="Download" class="tip"><?php echo $plr;?></a> <a href="index.php?cmd=predit&id=<?php echo $id;?>&del=plr" onclick="return confirm('Are you sure you wish to delete the Product License?');"><img src="../img/del.png" title="Delete" class="tip" /></a><?php }else{?><input type="file" name="plr" id="plr" /><?php }?></td>
	</tr>
	<tr>
		<th><label for="cover">Product eCover</label><img src="../img/help.png" title="Should be of the image file type (jpg/gif/png)." class="help" /> <span class="desc">(optional)</span></th>
		<td><?php if($cover){?><a href="../upload/pr/<?php echo $id."/".$cover;?>" class="fb view"><img src="../upload/pr/<?php echo $id."/".$cover;?>" style="max-width:100px;" /></a> <a href="index.php?cmd=predit&id=<?php echo $id;?>&del=cover" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete the Product eCover?');"><img src="../img/del.png" /></a><?php }else{?><input type="file" name="cover" id="cover" /><?php }?></td>
	</tr>
</table>
<div class="submit"><input type="submit" name="submit" class="button" value="<?php echo($id?"Save Changes":"Add Product");?>" /></div>
</form>
<script src="../tinymce/tinymce.min.js"></script>
<script>
$(".fb").fancybox();

tinymce.init({
selector:"textarea",
height:250,
theme:"modern",

plugins:["advlist autolink lists link image media emoticons charmap preview hr anchor searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking save table contextmenu directionality paste textcolor colorpicker"],

toolbar1:"undo redo | fontselect | fontsizeselect | forecolor backcolor | bold italic underline strikethrough | removeformat",
toolbar2:"alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media emoticons | code preview",

fontsize_formats:"8px 9px 10px 11px 12px 13px 14px 15px 16px 18px 20px 22px 24px 26px 28px 36px 48px 72px",

relative_urls:false,
remove_script_host:false,
content_css: "../css/tinymce.css",

external_filemanager_path:"../tinymce_fm/",
external_plugins:{"filemanager":"../tinymce_fm/plugin.min.js"},
filemanager_title:"File Manager",
filemanager_sort_by:"name",
filemanager_descending:true
});
</script>