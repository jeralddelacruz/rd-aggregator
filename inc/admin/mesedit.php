<?php
$id=$_GET["id"];

if($id&&(!$row=$DB->info("mes","mes_id='$id'"))){
	redirect("index.php?cmd=mes");
}

if(!$_POST["submit"]){
	if($id){
		$title=$row["mes_title"];
		$body=$row["mes_body"];
	}
}
else{
	$title=strip($_POST["title"]);
	$body=strip($_POST["body"],0);

	if(!$title||!$body){
		$error="All fields are <strong>required</strong>.";
	}
	else{
		if(!$id){
			$rd=time();
			$DB->query("insert into $dbprefix"."mes set mes_title='$title',mes_body='$body',mes_rd='$rd'");
		}
		else{
			$DB->query("update $dbprefix"."mes set mes_title='$title',mes_body='$body' where mes_id='$id'");
		}

		redirect("index.php?cmd=mes");
	}
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=mes" class="add">Back to Notifications</a><?php if($id){?><a href="index.php?cmd=mesedit" class="add">Add New Notification</a><?php }?></h2>
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
		<th><label for="title">Subject</label></th>
		<td><input type="text" name="title" id="title" value="<?php echo($_POST["submit"]?slash($title):$title);?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="body">Body</label></th>
		<td><textarea name="body" id="body" class="area"><?php echo $_POST["submit"]?slash($body):$body;?></textarea></td>
	</tr>
</table>
<div class="submit"><input type="submit" name="submit" class="button" value="Save Changes" /></div>
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