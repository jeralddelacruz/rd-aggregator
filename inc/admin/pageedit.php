<?php
$extra_page="page_fe='0'";
$id=$_GET["id"];

if($id&&(!$row=$DB->info("page","page_id='$id' and $extra_page"))){
	redirect("index.php?cmd=page");
}

$page_arr=$DB->get_pack();

$slug_arr=array_keys($USER_CMD);
sort($slug_arr);
$slug_str=implode(", ",$slug_arr);

if(!$_POST["submit"]){
	if(!$id){
		$pack_arr=array();
		$pid=0;
	}
	else{
		$pack_arr=explode(";",trim($row["page_pack"],";"));
		// $pack_arr=split(";",trim($row["page_pack"],";"));
		$pid=$row["page_pid"];
		$title=$row["page_title"];
		$slug=$row["page_slug"];
		$tmenu=$row["page_tmenu"];
		$bmenu=$row["page_bmenu"];
		$body=$row["page_body"];
		$pr_arr=explode(",",$row["page_pr"]);
		// $pr_arr=split(",",$row["page_pr"]);
	}
}
else{
	$pack_arr=$_POST["pack"];
	$pid=(int)$_POST["pid"];
	$title=strip($_POST["title"]);
	$slug=alphanum($_POST["slug"]);
	$tmenu=(int)$_POST["tmenu"];
	$bmenu=(int)$_POST["bmenu"];
	$body=strip($_POST["body"],0);
	$pr_arr=$_POST["pr"];

	if(!sizeof($pack_arr)){
		$error="At least one <strong>Membership</strong> should be <strong>chosen</strong>.";
	}
	elseif(!$title||!$body){
		$error="Required fields should be <strong>filled in</strong>.";
	}
	elseif($tmenu&&!$slug){
		$error="<strong>SLUG</strong> field should be <strong>filled in</strong>.";
	}
	elseif($slug&&in_array($slug,$slug_arr)){
		$error="<strong>$slug</strong> can NOT be used as a <strong>SLUG</strong>.";
	}
	elseif($slug&&$DB->info("page","page_slug='$slug' and $extra_page".($id?" and page_id<>'$id'":""))){
		$error="<strong>SLUG</strong> already <strong>exists</strong>, try another one.<br />";
	}
	else{
		$pack=";".implode(";",$pack_arr).";";
		$pr=implode(",",$pr_arr);

		if(!$id){
			$order=$DB->getmaxval("page_order","page","page_pid='$pid' and $extra_page")+1;
			$DB->query("insert into $dbprefix"."page set page_pid='$pid',page_pack='$pack',page_title='$title',page_slug='$slug',page_tmenu='$tmenu',page_bmenu='$bmenu',page_body='$body',page_pr='$pr',page_order='$order'");
		}
		else{
			$DB->query("update $dbprefix"."page set page_pid='$pid',page_pack='$pack',page_title='$title',page_slug='$slug',page_tmenu='$tmenu',page_bmenu='$bmenu',page_body='$body',page_pr='$pr' where page_id='$id'");
		}

		redirect("index.php?cmd=page");
	}
}

$pid_arr=array("0"=>array("title"=>"[None]"));
$DB->get_cat("page",$pid_arr,$id,0,0," and $extra_page");

$pr_str="";
$res=$DB->query("select * from $dbprefix"."pr order by pr_order");
if(sizeof($res)){
	foreach($res as $row){
		$pr_str.="<option value=\"".$row["pr_id"]."\"".(in_array($row["pr_id"],$pr_arr)?" selected":"").">".$row["pr_title"]."</option>";
	}
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=page" class="add">Back to Member Pages</a><?php if($id){?><a href="index.php?cmd=pageedit" class="add">Add New Member Page</a><?php }?></h2>
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
if(sizeof($page_arr)){
	foreach($page_arr as $k=>$v){
?>
			<input type="checkbox" name="pack[]" id="pack<?php echo $k;?>" value="<?php echo $k;?>"<?php echo (in_array($k,$pack_arr)?" checked":"");?> /> <label for="pack<?php echo $k;?>"><?php echo $v;?></label><br />
<?php
	}
}
?>
		</td>
	</tr>
	<tr>
		<th><label for="pid">Parent Page</label></th>
		<td>
			<select name="pid" id="pid" class="sel">
<?php
foreach($pid_arr as $k=>$val){
?>
				<option value="<?php echo $k;?>"<?php echo ($k==$pid)?" selected":"";?>><?php echo add_dash($val["title"],(int)$val["level"]);?></option>
<?php
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<th><label for="title">Title</label></th>
		<td><input type="text" name="title" id="title" value="<?php echo($_POST["submit"]?slash($title):$title);?>" class="text" maxlength="250" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="checkbox" name="tmenu" id="tmenu" value="1" <?php echo($tmenu?" checked":"");?> /> <label for="tmenu">Include to Top Menu</label> with <strong>SLUG</strong> <input type="text" name="slug" value="<?php echo $slug;?>" class="text_s" maxlength="25" /><img src="../img/help.png" title="The unique SLUG of the page. If included to the Top Menu, a new Tab linked to <?php echo $SCRIPTURL;?>/user/index.php?cmd=SLUG will be added to Member CP. The following words can NOT be used as a SLUG: <?php echo $slug_str;?>." class="help" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="checkbox" name="bmenu" id="bmenu" value="1" <?php echo($bmenu?" checked":"");?> /> <label for="bmenu">Include to Bottom Menu</label></td>
	</tr>
	<tr>
		<td colspan="2" class="tiny"><span class="desc"><strong>NOTE:</strong> Do NOT Copy/Paste from MS Word, the content isn't formatted correctly.</span><br /><textarea name="body" id="body"><?php echo $_POST["submit"]?slash($body):$body;?></textarea></td>
	</tr>
	<tr>
		<td class="tiny"><strong>TOKENS</strong></td>
		<td lass="tiny">
			<span class=""><strong>Make sure to include the brackets "[]" e.g [SITE_NAME]</strong> 
			<span class=""><strong>TOKENS:</strong> 
			
			<ul>
				<li>Site Name: [SITE_NAME]</li>
				<li>Site URL: [SITE_URL]</li>
				<li>Address: [ADDRESS]</li>
				<li>Notification Email: [NOTIF_EMAIL_ADDRESS]</li>
			</ul>
		</td>	
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