<?php
$id=$_GET["id"];

if($id&&(!$row=$DB->info("tpl","tpl_id='$id'"))){
	redirect("index.php?cmd=tpl");
}

if($id){
	$dir0="upload/tpl/".$id;
	$dir="../".$dir0;
}

if(!$_POST["submit"]){
	if(!$id){
		$show=1;
	}
	else{
		$show=$row["tpl_show"];
		$title=$row["tpl_title"];
		$affiliate_link=$row["tpl_affiliate"];
		$email_swipe=$row["tpl_email"];

		$lp=file_get_contents($dir."/index.html");
		$lp=str_replace("\"assets/","\"$SCRIPTURL/$dir0/assets/",$lp);
		$lp=str_replace("'assets/","'$SCRIPTURL/$dir0/assets/",$lp);
		$lp=str_replace("\"js/","\"$SCRIPTURL/$dir0/js/",$lp);
		$lp=str_replace("'js/","'$SCRIPTURL/$dir0/js/",$lp);
		$lp=str_replace("\"images/","\"$SCRIPTURL/$dir0/images/",$lp);
		$lp=str_replace("'images/","'$SCRIPTURL/$dir0/images/",$lp);
		$lp=str_replace("\"img/","\"$SCRIPTURL/$dir0/img/",$lp);
		$lp=str_replace("'img/","'$SCRIPTURL/$dir0/img/",$lp);
		$lp=str_replace("url(im","url($SCRIPTURL/$dir0/im",$lp);
		$lp=str_replace("\"css/","\"$SCRIPTURL/$dir0/css/",$lp);
		$lp=str_replace("'css/","'$SCRIPTURL/$dir0/css/",$lp);
		$lp=str_replace("href=\"style","href=\"$SCRIPTURL/$dir0/style",$lp);
		$lp=str_replace("href='style","href='$SCRIPTURL/$dir0/style",$lp);

/*
		$op=file_get_contents($dir."/thankyou.html");
		$op=str_replace("\"assets/","\"$SCRIPTURL/$dir0/assets/",$op);
		$op=str_replace("'assets/","'$SCRIPTURL/$dir0/assets/",$op);
		$op=str_replace("\"js/","\"$SCRIPTURL/$dir0/js/",$op);
		$op=str_replace("'js/","'$SCRIPTURL/$dir0/js/",$op);
		$op=str_replace("\"images/","\"$SCRIPTURL/$dir0/images/",$op);
		$op=str_replace("'images/","'$SCRIPTURL/$dir0/images/",$op);
		$op=str_replace("\"img/","\"$SCRIPTURL/$dir0/img/",$op);
		$op=str_replace("'img/","'$SCRIPTURL/$dir0/img/",$op);
		$op=str_replace("url(im","url($SCRIPTURL/$dir0/im",$op);
		$op=str_replace("\"css/","\"$SCRIPTURL/$dir0/css/",$op);
		$op=str_replace("'css/","'$SCRIPTURL/$dir0/css/",$op);
		$op=str_replace("href=\"style","href=\"$SCRIPTURL/$dir0/style",$op);
		$op=str_replace("href='style","href='$SCRIPTURL/$dir0/style",$op);
*/

		$tp=file_get_contents($dir."/download.html");
		$tp=str_replace("\"assets/","\"$SCRIPTURL/$dir0/assets/",$tp);
		$tp=str_replace("'assets/","'$SCRIPTURL/$dir0/assets/",$tp);
		$tp=str_replace("\"js/","\"$SCRIPTURL/$dir0/js/",$tp);
		$tp=str_replace("'js/","'$SCRIPTURL/$dir0/js/",$tp);
		$tp=str_replace("\"images/","\"$SCRIPTURL/$dir0/images/",$tp);
		$tp=str_replace("'images/","'$SCRIPTURL/$dir0/images/",$tp);
		$tp=str_replace("\"img/","\"$SCRIPTURL/$dir0/img/",$tp);
		$tp=str_replace("'img/","'$SCRIPTURL/$dir0/img/",$tp);
		$tp=str_replace("url(im","url($SCRIPTURL/$dir0/im",$tp);
		$tp=str_replace("\"css/","\"$SCRIPTURL/$dir0/css/",$tp);
		$tp=str_replace("'css/","'$SCRIPTURL/$dir0/css/",$tp);
		$tp=str_replace("href=\"style","href=\"$SCRIPTURL/$dir0/style",$tp);
		$tp=str_replace("href='style","href='$SCRIPTURL/$dir0/style",$tp);
	}
}
else{
	$show=(int)$_POST["show"];
	$title=strip($_POST["title"]);
	$affiliate_link=strip($_POST["affiliate_link"]);
	$email_swipe=strip($_POST["email_swipe"], 0);
	
	$tpl=$_FILES["tpl"]["tmp_name"];

	$is_tpl=0;
	if($tpl){
		$zip=new ZipArchive();
		$is_tpl=(int)$zip->open($tpl,ZipArchive::CHECKCONS);
		$zip->close();
	}

	if($id){
		$lp=slash(strip($_POST["lp"],0));
//		$op=slash(strip($_POST["op"],0));
		$tp=slash(strip($_POST["tp"],0));
	}

	$error="";
	if(!$title||(!$id&&!$tpl)){
		$error.="&bull; All fields should be <strong>filled in</strong>.<br />";
	}
	if($tpl&&($is_tpl!=1)){
		$error.="&bull; <strong>Invalid</strong> <strong>Template ZIP Archive</strong>.<br />";
	}

	if(!$error){
		if(!$id){
			$id=$DB->getauto("tpl");
			$order=$DB->getmaxval("tpl_order","tpl")+1;

			$DB->query("insert into $dbprefix"."tpl set tpl_id='$id',tpl_show='$show',tpl_title='$title', tpl_affiliate='$affiliate_link', tpl_email='$email_swipe', tpl_order='$order'");

			$tpl_dir="../upload/tpl/$id";
			$tpl_file="$tpl_dir/tpl.zip";

			@mkdir($tpl_dir,0777);
			@chmod($tpl_dir,0777);

			@move_uploaded_file($tpl,$tpl_file);
			$zip=new ZipArchive();
			$zip->open($tpl_file);
			$zip->extractTo($tpl_dir);
			$zip->close();
			unlink($tpl_file);
		}
		else{
			$DB->query("update $dbprefix"."tpl set tpl_show='$show', tpl_title='$title', tpl_affiliate='$affiliate_link', tpl_email='$email_swipe' where tpl_id='$id'");

			$lp=str_replace("\"$SCRIPTURL/$dir0/assets/","\"assets/",$lp);
			$lp=str_replace("'$SCRIPTURL/$dir0/assets/","'assets/",$lp);
			$lp=str_replace("\"$SCRIPTURL/$dir0/js/","\"js/",$lp);
			$lp=str_replace("'$SCRIPTURL/$dir0/js/","'js/",$lp);
			$lp=str_replace("\"$SCRIPTURL/$dir0/images/","\"images/",$lp);
			$lp=str_replace("'$SCRIPTURL/$dir0/images/","'images/",$lp);
			$lp=str_replace("\"$SCRIPTURL/$dir0/img/","\"img/",$lp);
			$lp=str_replace("'$SCRIPTURL/$dir0/img/","'img/",$lp);
			$lp=str_replace("url($SCRIPTURL/$dir0/im","url(im",$lp);
			$lp=str_replace("\"$SCRIPTURL/$dir0/css/","\"css/",$lp);
			$lp=str_replace("'$SCRIPTURL/$dir0/css/","'css/",$lp);
			$lp=str_replace("href=\"$SCRIPTURL/$dir0/style","href=\"style",$lp);
			$lp=str_replace("href='$SCRIPTURL/$dir0/style","href='style",$lp);
			file_put_contents($dir."/index.html",$lp);

/*
			$op=str_replace("\"$SCRIPTURL/$dir0/assets/","\"assets/",$op);
			$op=str_replace("'$SCRIPTURL/$dir0/assets/","'assets/",$op);
			$op=str_replace("\"$SCRIPTURL/$dir0/js/","\"js/",$op);
			$op=str_replace("'$SCRIPTURL/$dir0/js/","'js/",$op);
			$op=str_replace("\"$SCRIPTURL/$dir0/images/","\"images/",$op);
			$op=str_replace("'$SCRIPTURL/$dir0/images/","'images/",$op);
			$op=str_replace("\"$SCRIPTURL/$dir0/img/","\"img/",$op);
			$op=str_replace("'$SCRIPTURL/$dir0/img/","'img/",$op);
			$op=str_replace("url($SCRIPTURL/$dir0/im","url(im",$op);
			$op=str_replace("\"$SCRIPTURL/$dir0/css/","\"css/",$op);
			$op=str_replace("'$SCRIPTURL/$dir0/css/","'css/",$op);
			$op=str_replace("href=\"$SCRIPTURL/$dir0/style","href=\"style",$op);
			$op=str_replace("href='$SCRIPTURL/$dir0/style","href='style",$op);
			file_put_contents($dir."/thankyou.html",$op);
*/

			$tp=str_replace("\"$SCRIPTURL/$dir0/assets/","\"assets/",$tp);
			$tp=str_replace("'$SCRIPTURL/$dir0/assets/","'assets/",$tp);
			$tp=str_replace("\"$SCRIPTURL/$dir0/js/","\"js/",$tp);
			$tp=str_replace("'$SCRIPTURL/$dir0/js/","'js/",$tp);
			$tp=str_replace("\"$SCRIPTURL/$dir0/images/","\"images/",$tp);
			$tp=str_replace("'$SCRIPTURL/$dir0/images/","'images/",$tp);
			$tp=str_replace("\"$SCRIPTURL/$dir0/img/","\"img/",$tp);
			$tp=str_replace("'$SCRIPTURL/$dir0/img/","'img/",$tp);
			$tp=str_replace("url($SCRIPTURL/$dir0/im","url(im",$tp);
			$tp=str_replace("\"$SCRIPTURL/$dir0/css/","\"css/",$tp);
			$tp=str_replace("'$SCRIPTURL/$dir0/css/","'css/",$tp);
			$tp=str_replace("href=\"$SCRIPTURL/$dir0/style","href=\"style",$tp);
			$tp=str_replace("href='$SCRIPTURL/$dir0/style","href='style",$tp);
			file_put_contents($dir."/download.html",$tp);
		}

		redirect("index.php?cmd=tpl");
	}
}

?>
<h2><?php echo $index_title;?><a href="index.php?cmd=tpl" class="add">Back to CB Templates</a><?php if($id){?><a href="index.php?cmd=tpledit" class="add">Add New CB Template</a><?php }?></h2>
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
		<th><label for="show">DFY Template</label></th>
		<td><input type="checkbox" name="show" id="show" value="1"<?php echo $show?" checked":"";?> /></td>
	</tr>
	<tr>
		<th><label for="title">Title</label></th>
		<td><input type="text" name="title" id="title" value="<?php echo($_POST["submit"]?slash($title):$title);?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="affiliate_link">Affiliate Link</label></th>
		<td><input type="text" name="affiliate_link" id="affiliate_link" value="<?php echo($_POST["submit"]?slash($affiliate_link):$affiliate_link);?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="email_swipe">Email Swipe</label></th>
		<td><input type="text" name="email_swipe" id="email_swipe" value="<?php echo($_POST["submit"]?slash($email_swipe):$email_swipe);?>" class="text_l" maxlength="255" /></td>

	</tr>
<?php
if(!$id){
?>
	<tr>
<!--
		<th><label for="tpl">Template ZIP Archive</label><img src="../img/help.png" title="Should contain <strong>index.html</strong> file for <strong>Sales Page Content</strong>,<br /><strong>thankyou.html</strong> file for <strong>Thank You Page Content</strong><br />and <strong>download.html</strong> file for <strong>Download Page Content</strong>.<br />May contain <strong>thumb.png</strong> file for screenshot,<br /><strong>images/</strong> and/or <strong>img/</strong> folder for graphics<br />and <strong>css/</strong> folder for stylesheets." class="help" /></th>
-->
		<th><label for="tpl">Template ZIP Archive</label><img src="../img/help.png" title="Should contain <strong>index.html</strong> file for <strong>Sales Page Content</strong><br />and <strong>download.html</strong> file for <strong>Download Page Content</strong>.<br />May contain <strong>thumb.png</strong> file for screenshot,<br /><strong>images/</strong> and/or <strong>img/</strong> folder for graphics<br />and <strong>css/</strong> folder for stylesheets." class="help" /></th>
		<td><input type="file" name="tpl" id="tpl" /></td>
	</tr>
<?php
}
else{
?>
	<tr>
		<td colspan="2" class="tiny"><span class="large">Sales Page Content</span><br /><textarea name="lp" class="tinymce"><?php echo $lp;?></textarea></td>
	</tr>
<!--
	<tr>
		<td colspan="2" class="tiny"><span class="large">Thank You Page Content</span><br /><textarea name="op" class="tinymce"><?php echo $op;?></textarea></td>
	</tr>
-->
	<tr>
		<td colspan="2" class="tiny"><span class="large">Download Page Content</span><br /><textarea name="tp" class="tinymce"><?php echo $tp;?></textarea></td>
	</tr>
<?php
}
?>
</table>
<div class="submit"><input type="submit" name="submit" value="Save Changes" class="button" /></div>
</form>
<script src="../tinymce/tinymce.min.js"></script>
<script>
jQuery(document).ready(function($){

tinymce.init({
selector:".tinymce",
height:400,
theme:"modern",

plugins:["advlist autolink lists link image media emoticons charmap preview hr anchor searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking save table contextmenu directionality paste textcolor colorpicker fullpage"],

toolbar1:"fontselect | fontsizeselect | forecolor backcolor | bold italic underline strikethrough | removeformat",
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

});
</script>