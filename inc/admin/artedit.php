<?php
$id=$_GET["id"];

if($id&&(!$row=$DB->info("art","art_id='$id'"))){
	redirect("index.php?cmd=art");
}

if(!$_POST["submit"]){
	if($id){
		$title=$row["art_title"];
		$desc=$row["art_desc"];
		$body=$row["art_body"];
	}
}
else{
	$title=strip($_POST["title"]);
	$desc=strip($_POST["desc"],0);
	$body=strip($_POST["body"],0);

	if(!$title||!$desc||!$body){
		$error="All fields are <strong>required</strong>.";
	}
	else{
		if(!$id){
			$order=$DB->getmaxval("art_order","art")+1;
			$DB->query("insert into $dbprefix"."art set art_title='$title',art_desc='$desc',art_body='$body',art_order='$order'");
		}
		else{
			$DB->query("update $dbprefix"."art set art_title='$title',art_desc='$desc',art_body='$body' where art_id='$id'");
		}

		redirect("index.php?cmd=art");
	}
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=art" class="add">Back to Articles</a><?php if($id){?><a href="index.php?cmd=artedit" class="add">Add New Article</a><?php }?></h2>
<?php
if($error){
?>
<div class="error"><?php echo $error;?></div>
<?php
}
?>
<form method="post">
<table class="tbl_form">
	<tr>
		<th><label for="title">Title</label></th>
		<td><input type="text" name="title" id="title" value="<?php echo($_POST["submit"]?slash($title):$title);?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="desc">Summary</label></th>
		<td><textarea name="desc" id="desc" class="area"><?php echo $_POST["submit"]?slash($desc):$desc;?></textarea></td>
	</tr>
	<tr>
		<td colspan="2" class="tiny"><span class="desc"><strong>NOTE:</strong> Do NOT Copy/Paste from MS Word, the content isn't formatted correctly.</span><br /><textarea name="body" id="body"><?php echo $_POST["submit"]?slash($body):$body;?></textarea></td>
	</tr>
</table>
<div class="submit"><input type="submit" name="submit" class="button" value="<?php echo($id?"Save Changes":"Add Article");?>" /></div>
</form>
<script src="../tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
selector:"textarea",
height:400,
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