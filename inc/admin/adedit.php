<?php
$id=$_GET["id"];

if($id&&(!$row=$DB->info("ad","ad_id='$id'"))){
	redirect("index.php?cmd=ad");
}

if(!$_POST["submit"]){
	if(!$id){
		$act=1;
	}
	else{
		$act=$row["ad_act"];
		$area=$row["ad_area"];
		$title=$row["ad_title"];
		$body=$row["ad_body"];
		$url=$row["ad_url"];
		$mshow=$row["ad_mshow"];
		$mclick=$row["ad_mclick"];
		$expire=$row["ad_expire"];
	}
}
else{
	$act=$_POST["act"];
	$area=$_POST["area"];
	$title=strip($_POST["title"]);
	$body=strip($_POST["body"],0);
	$url=strip($_POST["url"]);
	$mshow=(int)$_POST["mshow"];
	$mclick=(int)$_POST["mclick"];
	$expire=(int)strtotime($_POST["expire"]);

	if(!$title||!$body){
		$error="All fields should be <strong>filled in</strong>.";
	}
	else{
		if(!$id){
			$DB->query("insert into $dbprefix"."ad set ad_act='$act',ad_area='$area',ad_title='$title',ad_body='$body',ad_url='$url',ad_mshow='$mshow',ad_mclick='$mclick',ad_expire='$expire'");
		}
		else{
			$DB->query("update $dbprefix"."ad set ad_act='$act',ad_area='$area',ad_title='$title',ad_body='$body',ad_url='$url',ad_mshow='$mshow',ad_mclick='$mclick',ad_expire='$expire' where ad_id='$id'");
		}

		redirect("index.php?cmd=ad");
	}
}

$act_str="";
foreach($ACT_ARR as $k=>$v){
	$act_str.="<option value=\"$k\"".(($k==$act)?" selected":"").">$v</option>";
}

$area_str="";
foreach($AREA_ARR as $k=>$v){
	$area_str.="<option value=\"$k\"".(($k==$area)?" selected":"").">$v</option>";
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=ad" class="add">Back to Ads</a><?php if($id){?><a href="index.php?cmd=adedit" class="add">Add New Ad</a><?php }?></h2>
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
		<th><label for="area">Area</label><img src="../img/help.png" title="The height of the TOP Ad should not exceed 70 pixels, so recommended Ad sizes are 468x60 (Full Banner) and 234x60 (Half Banner).<br /><br />The width of the LEFT Ad should not exceed 180 pixels, so recommended Ad sizes are 160x600 (Skyscraper), 120x600 (Skyscraper) and 120x240 (Small Skyscraper).<br /><br />The width of the BOTTOM Ad should not exceed 980 pixels, so recommended Ad size is 728x90 (Leaderboard), but you can actually use any banner size in this area." class="help" /></th>
		<td><select name="area" id="area" class="sel"><?php echo $area_str;?></select></td>
	</tr>
	<tr>
		<th><label for="title">Title</label></th>
		<td><input type="text" name="title" id="title" value="<?php echo($_POST["submit"]?slash($title):$title);?>" class="text" maxlength="250" /></td>
	</tr>
	<tr>
		<td colspan="2" class="tiny"><span class="desc"><strong>NOTE:</strong> Do NOT Copy/Paste from MS Word, the content isn't formatted correctly.</span><br /><textarea name="body" id="body"><?php echo $_POST["submit"]?slash($body):$body;?></textarea></td>
	</tr>
	<tr>
		<th><label for="url">Link to URL</label><img src="../img/help.png" title="Leave blank for the &quot;direct&quot; Ad, don't forget to link the Ad directly in the Content section then. For &quot;direct&quot; Ads there is no statistics for Clicks, though Shows statistics is provided." class="help" /></th>
		<td><input type="text" name="url" id="url" value="<?php echo($_POST["submit"]?slash($url):$url);?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="mshow">Max Shows</label><img src="../img/help.png" title="Leave blank for unlimited. If the field is filled in, the Ad will be taken off the rotation once reaching this number." class="help" /></th>
		<td><input type="text" name="mshow" id="mshow" value="<?php echo $mshow?$mshow:"";?>" class="text_s" maxlength="9" /></td>
	</tr>
	<tr>
		<th><label for="mclick">Max Clicks</label><img src="../img/help.png" title="Leave blank for unlimited. If the field is filled in, the Ad will be taken off the rotation once reaching this number." class="help" /></th>
		<td><input type="text" name="mclick" id="mclick" value="<?php echo $mclick?$mclick:"";?>" class="text_s" maxlength="9" /></td>
	</tr>
	<tr>
		<th><label for="expire">Expiry Date</label><img src="../img/help.png" title="Leave blank for endless. If the field is filled in, the Ad will be taken off the rotation once reaching this date." class="help "/></th>
		<td><input type="text" name="expire" id="expire" value="<?php echo $expire?date("Y-m-d",$expire):"";?>" class="text_s" /></td>
	</tr>
</table>
<div class="submit"><input type="submit" name="submit" class="button" value="<?php echo($id?"Save Changes":"Add Ad");?>" /></div>
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

jQuery(function($){
	$("#expire").datepicker({dateFormat:"yy-mm-dd",minDate:+1});
});
</script>