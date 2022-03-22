<?php
$extra_page="page_fe='1'";
$id=$_GET["id"];

if($id&&(!$row=$DB->info("page","page_id='$id' and $extra_page"))){
	redirect("index.php?cmd=pagef");
}

if(!$_POST["submit"]){
	if($id){
		$title=$row["page_title"];
		$index=$row["page_index"];
		$body=$row["page_body"];
		$pr_arr=split(",",$row["page_pr"]);
	}
}
else{
	$title=strip($_POST["title"]);
	$index=(int)$_POST["index"];
	$body=strip($_POST["body"],0);
	$pr_arr=$_POST["pr"];

	if(!$title||!$body){
		$error="All fields should be <strong>filled in</strong>.";
	}
	else{
		$pr=implode(",",$pr_arr);
		if(!$id){
			$id=$DB->getauto("page");
			$DB->query("insert into $dbprefix"."page set page_id='$id',page_fe='1',page_pack='',page_title='$title',page_body='$body',page_pr='$pr'");
		}
		else{
			$DB->query("update $dbprefix"."page set page_index='0',page_title='$title',page_body='$body',page_pr='$pr' where page_id='$id'");
		}

		if($index){
			$DB->query("update $dbprefix"."page set page_index='0' where $extra_page");
			$DB->query("update $dbprefix"."page set page_index='1' where page_id='$id'");
		}

		redirect("index.php?cmd=pagef");
	}
}

$pr_str="";
$res=$DB->query("select * from $dbprefix"."pr order by pr_order");
if(sizeof($res)){
	foreach($res as $row){
		$pr_str.="<option value=\"".$row["pr_id"]."\"".(in_array($row["pr_id"],$pr_arr)?" selected":"").">".$row["pr_title"]."</option>";
	}
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=pagef" class="add">Back to Front End Pages</a><?php if($id){?><a href="index.php?cmd=pagefedit" class="add">Add New Front End Page</a><?php }?></h2>
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
		<td><input type="text" name="title" id="title" value="<?php echo($_POST["submit"]?slash($title):$title);?>" class="text" maxlength="250" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="checkbox" name="index" id="index" value="1" <?php echo($index?" checked":"");?> /> <label for="index">Index (Home) Page</label></td>
	</tr>
	<tr>
		<td colspan="2" class="tiny"><span class="desc"><strong>NOTE:</strong> Do NOT Copy/Paste from MS Word, the content isn't formatted correctly.</span><br /><textarea name="body" id="body"><?php echo $_POST["submit"]?slash($body):$body;?></textarea></td>
	</tr>
	<tr>
		<th><label for="pr">Include to Products Block</label><img src="../img/help.png" title="If you wish <strong>Products</strong> to be displayed on the Page, choose the one(s) to include to the <strong>Products Block</strong>.<br />Point out the <strong>Products Block</strong> position with <strong>%product%</strong> token in the <strong>Page Content</strong> field above." class="help" /><br /><span class="desc">(press <strong>Ctrl</strong> for multiple selection)</span></th>
		<td><select id="pr" name="pr[]" class="sel_l" multiple><?php echo $pr_str;?></select></td>
	</tr>
</table>
<div class="submit"><input type="submit" name="submit" class="button" value="<?php echo($id?"Save Changes":"Add Page");?>" /></div>
</form>
<script src="../tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
selector:"textarea",
height:600,
theme:"modern",

plugins:["advlist autolink lists link image media emoticons charmap preview hr anchor searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking save table contextmenu directionality paste textcolor colorpicker fullpage"],

toolbar1:"undo redo | fontselect | fontsizeselect | forecolor backcolor | bold italic underline strikethrough | removeformat",
toolbar2:"alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media emoticons | code fullpage preview",

fontsize_formats:"8px 9px 10px 11px 12px 13px 14px 15px 16px 18px 20px 22px 24px 26px 28px 36px 48px 72px",

relative_urls:false,
remove_script_host:false,

external_filemanager_path:"../tinymce_fm/",
external_plugins:{"filemanager":"../tinymce_fm/plugin.min.js"},
filemanager_title:"File Manager",
filemanager_sort_by:"name",
filemanager_descending:true,

valid_elements:"*[*]"
});
</script>