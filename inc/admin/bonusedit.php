<?php
$id=$_GET["id"];
$bonus_arr=$DB->get_pack();

if($id&&(!$row=$DB->info("bonus","bonus_id='$id'"))){
	redirect("index.php?cmd=bonus");
}

if(!$_POST["submit"]){
	if(!$id){
		$pack_arr=array();
	}
	else{
		$pack_arr=split(";",trim($row["bonus_pack"],";"));
		$title=$row["bonus_title"];
		$desc=$row["bonus_desc"];
		$body=$row["bonus_body"];
	}
}
else{
	$pack_arr=$_POST["pack"];
	$title=strip($_POST["title"]);
	$desc=strip($_POST["desc"],0);
	$body=strip($_POST["body"],0);

	if(!sizeof($pack_arr)||!$title||!$desc||!$body){
		$error="All fields are <strong>required</strong>.";
	}
	else{
		$pack=";".implode(";",$pack_arr).";";

		if(!$id){
			$DB->query("insert into $dbprefix"."bonus set bonus_pack='$pack',bonus_title='$title',bonus_desc='$desc',bonus_body='$body'");
		}
		else{
			$DB->query("update $dbprefix"."bonus set bonus_pack='$pack',bonus_title='$title',bonus_desc='$desc',bonus_body='$body' where bonus_id='$id'");
		}

		redirect("index.php?cmd=bonus");
	}
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=bonus" class="add">Back to Bonuses</a><?php if($id){?><a href="index.php?cmd=bonusedit" class="add">Add New Bonus</a><?php }?></h2>
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
		<th>Available for</th>
		<td>
<?php
if(sizeof($bonus_arr)){
	foreach($bonus_arr as $k=>$v){
?>
			<input type="checkbox" name="pack[]" id="pack<?php echo $k;?>" value="<?php echo $k;?>"<?php echo (in_array($k,$pack_arr)?" checked":"");?> /> <label for="pack<?php echo $k;?>"><?php echo $v;?></label><br />
<?php
	}
}
?>
		</td>
	</tr>
	<tr>
		<th><label for="title">Title</label></th>
		<td><input type="text" name="title" id="title" value="<?php echo($_POST["submit"]?slash($title):$title);?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="desc">Summary</label></th>
		<td><textarea name="desc" id="desc" class="area"><?php echo($_POST["submit"]?slash($desc):$desc);?></textarea></td>
	</tr>
	<tr>
		<td colspan="2" class="tiny"><span class="desc"><strong>NOTE:</strong> Do NOT Copy/Paste from MS Word, the content isn't formatted correctly.</span><br /><textarea name="body" id="body"><?php echo $_POST["submit"]?slash($body):$body;?></textarea></td>
	</tr>
</table>
<div class="submit"><input type="submit" name="submit" class="button" value="<?php echo($id?"Save Changes":"Add Bonus");?>" /></div>
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