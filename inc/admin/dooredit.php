<?php
$id=$_GET["id"];

if($id&&(!$row=$DB->info("door","door_id='$id'"))){
	redirect("index.php?cmd=door");
}

if(!$_POST["submit"]){
	if(!$id){
		$act=1;
		$top="Continue to Account";
		$bot="Continue";
	}
	else{
		$act=$row["door_act"];
		$title=$row["door_title"];
		$body=$row["door_body"];
		$top=$row["door_top"];
		$bot=$row["door_bot"];
	}
}
else{
	$act=$_POST["act"];
	$title=strip($_POST["title"]);
	$body=strip($_POST["body"],0);
	$top=strip($_POST["top"]);
	$bot=strip($_POST["bot"]);

	if(!$title||!$body||!$top||!$bot){
		$error="All fields should be <strong>filled in</strong>.";
	}
	else{
		if(!$id){
			$order=$DB->getmaxval("door_order","door")+1;
			$DB->query("insert into $dbprefix"."door set door_act='$act',door_title='$title',door_body='$body',door_top='$top',door_bot='$bot',door_order='$order'");
		}
		else{
			$DB->query("update $dbprefix"."door set door_act='$act',door_title='$title',door_body='$body',door_top='$top',door_bot='$bot' where door_id='$id'");
		}

		redirect("index.php?cmd=door");
	}
}

$act_str="";
foreach($ACT_ARR as $k=>$v){
	$act_str.="<option value=\"$k\"".(($k==$act)?" selected":"").">$v</option>";
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=door" class="add">Back to Doorways</a><?php if($id){?><a href="index.php?cmd=dooredit" class="add">Add New Doorway</a><?php }?></h2>
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
		<th><label for="act">Status</label></th>
		<td><select name="act" id="act" class="sel"><?php echo $act_str;?></select></td>
	</tr>
	<tr>
		<th><label for="title">Title</label></th>
		<td><input type="text" name="title" id="title" value="<?php echo($_POST["submit"]?slash($title):$title);?>" class="text" maxlength="250" /></td>
	</tr>
	<tr>
		<td colspan="2" class="tiny"><span class="desc"><strong>NOTE:</strong> Do NOT Copy/Paste from MS Word, the content isn't formatted correctly.</span><br /><textarea name="body" id="body"><?php echo $_POST["submit"]?slash($body):$body;?></textarea></td>
	</tr>
	<tr>
		<th><label for="top">Top Link Text</label></th>
		<td><input type="text" name="top" id="top" value="<?php echo($_POST["submit"]?slash($top):$top);?>" class="text" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="bot">Bottom Button Text</label></th>
		<td><input type="text" name="bot" id="bot" value="<?php echo($_POST["submit"]?slash($bot):$bot);?>" class="text" maxlength="250" /></td>
	</tr>
</table>
<div class="submit"><input type="submit" name="submit" class="button" value="<?php echo($id?"Save Changes":"Add Doorway");?>" /></div>
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