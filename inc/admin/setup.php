<?php
$ignore_arr=array("logo","icon","bg_login","bg_menu","sign","free","mod","menu","color","cb_lic","cb_mlic");
$res=$DB->query("select * from $dbprefix"."setup order by setup_order,setup_id");

$img_arr=array("logo"=>"Site Logo","icon"=>"Site Favicon","bg_login"=>"Member CP Login Background","bg_menu"=>"Member CP Menu Background");
foreach($img_arr as $k=>$v){
	${$k}=$WEBSITE[$k];
}

$color_arr=unserialize($WEBSITE["color"]);
$mod_arr=unserialize($WEBSITE["mod"]);

if($_GET["del"]){
	$del=trim($_GET["del"]);
	if(${$del}){
		@unlink("../img/".${$del});
		$DB->query("update $dbprefix"."setup set setup_val='' where setup_key='$del'");
	}
	redirect("index.php?cmd=setup");
}

if($_POST["submit"]){
	foreach($img_arr as $k=>$v){
		if($_FILES[$k]){
			if(getimagesize($_FILES[$k]["tmp_name"])){
				$name_arr=explode(".",$_FILES[$k]["name"]);
				${$k}=$k.".".$name_arr[sizeof($name_arr)-1];
				if(@move_uploaded_file($_FILES[$k]["tmp_name"],"../img/".${$k})){
					$DB->query("update $dbprefix"."setup set setup_val='".${$k}."' where setup_key='$k'");
				}
			}
		}
	}

	$color_arr=$_POST["color"];
	$css_arr=array("admin","style","tinymce","user");
	foreach($css_arr as $css){
		$str=file_get_contents("../css/".$css."_tpl.css");
		foreach($color_arr as $k=>$color){
			$str=str_replace("%color$k%",$color,$str);
		}

		$fp=fopen("../css/".$css.".css","w");
		fputs($fp,$str);
		fclose($fp);
	}
	$DB->query("update $dbprefix"."setup set setup_val='".serialize($color_arr)."' where setup_key='color'");

	$mod_arr=$_POST["mod"];
	$DB->query("update $dbprefix"."setup set setup_val='".serialize($mod_arr)."' where setup_key='mod'");

	foreach($res as $row){
		$key=$row["setup_key"];
		if(in_array($key,$ignore_arr)){continue;}
		$val=strip($_POST[$key],$row["setup_plain"]?1:0);

		// DYNAMIC FOLDER NAME
		if($key == "register_folder_name"){
			$folder_directory = "../";
			$folder_name = $row["setup_val"];

			$old_folder_name = $folder_directory . $folder_name;
			$new_folder_name = $folder_directory . $_POST[$key];

			$rename = rename($folder_directory . $folder_name, $folder_directory . $_POST[$key]);
		}

		$DB->query("update $dbprefix"."setup set setup_val='$val' where setup_key='$key'");
	}

	redirect("index.php?cmd=setup&ok=1");
}

$help_arr=array(
"1"=>"Success message border color.",
"2"=>"Search Button text color, Member and Admin menu/sub-menu text color.",
"3"=>"Link text color, Submit button background color, Poll animated results background color, Admin sub-menu link hover text color.",
"4"=>"Tool tip, Notes and Footer text color.",
"5"=>"Search Button hover border color, Admin menu active and hover background color.",
"6"=>"Member document body background color.",
"7"=>"Link hover text color, Error message border color, Inactive/Suspended text color.",
"8"=>"Search Button border color, inactive button text color, Member content area borders color.",
"9"=>"Add New link background color.",
"10"=>"Member tabs background color, List and FILTER tables border color, Search Button background color, input fields border color.",
"11"=>"Member and Admin header background color, Admin menu background color and sub-menu border color, Add New link hover background color, List and FILTER tables header background color.",
"12"=>"Success message background color.",
"13"=>"MOTD, Free Member Sign Up form and tool tip border color.",
"14"=>"Error message background color.",
"15"=>"MOTD, tool tip and List table row hover background color.",
"16"=>"Member and Admin content area background color, Member menu active and hover background color, Admin menu active and hover text color and sub-menu background color, Search Button hover background color, Submit button text color.",
);
?>
<h2><?php echo $index_title;?></h2>
<?php
if($_GET["ok"]){
?>
<div class="ok">Settings <strong>saved</strong>.</div>
<?php
}
?>
<!-- <img src="<?//= "../img/" . $WEBSITE["logo"]; ?>" /> -->
<form method="post" enctype="multipart/form-data" action="index.php?cmd=setup">
<table class="tbl_form">
	<tr>
		<th>GraphicsGenerator Admin CP</th>
		<td style="padding-top:10px;"><a href="<?php echo $WEBSITE["cdb_url"];?>/ecg/abz915/" target="_blank" class="i"><?php echo $WEBSITE["cdb_url"];?>/ecg/abz915/</a></td>
	</tr>
<?php
foreach($img_arr as $k=>$v){
?>
	<tr>
		<th><label for="<?php echo $k;?>"><?php echo $v;?></label><img src="../img/help.png" title="GIF, PNG or JPG image." class="help" /></th>
		<td><?php echo (${$k}?("<a href=\"../img/".${$k}."\" class=\"fb\" rel=\"gal\"><img src=\"../img/".${$k}."\" style=\"max-height:".(($k=="icon")?16:100)."px;\" /></a> <a href=\"index.php?cmd=setup&del=$k\" onclick=\"return confirm('Are you sure you wish to delete the $v?');\" title=\"Delete\"><img src=\"../img/del.png\" class=\"tip\" /></a>"):"<input type=\"file\" name=\"$k\" id=\"$k\" />");?></td>
	</tr>
<?php
}
?>
	<tr>
		<th>Website Colors</th>
		<td>
<?php
foreach($color_arr as $k=>$v){
?>
			<input type="text" name="color[<?php echo $k;?>]" value="<?php echo $v;?>" class="color" /><img src="../img/help.png" title="<?php echo $help_arr[$k];?>" class="help" />&nbsp;&nbsp;&nbsp;
<?php
	if($k==8){
?>
			<p></p>
<?php
	}
}
?>
		</td>
	</tr>
	<tr>
		<th>Member CP Modules</th>
		<td>
<?php
foreach($WS_MOD_ARR as $k=>$v){
?>
			<input type="checkbox" id="mod_<?php echo $k;?>" name="mod[<?php echo $k;?>]" value="1"<?php echo $mod_arr[$k]?" checked":"";?> /> <label for="mod_<?php echo $k;?>"><?php echo $v;?></label>&nbsp;&nbsp;&nbsp;
<?php
}
?>
		</td>
	</tr>
<?php
foreach($res as $row){
	$id=$row["setup_id"];
	$key=$row["setup_key"];
	if(in_array($key,$ignore_arr)){continue;}
	$val=$row["setup_val"];
	$plain=$row["setup_plain"];
?>
	<tr>
		<th><label for="<?php echo $key;?>"><?php echo $row["setup_desc"];?></label><?php echo $row["setup_help"]?("<img src=\"../img/help.png\" title=\"".$row["setup_help"]."\" class=\"help\" />"):"";?></th>
		<td><?php if($plain){?><input type="text" name="<?php echo $key;?>" id="<?php echo $key;?>" value="<?php echo $val;?>" class="text_l" /><?php }else{?><textarea name="<?php echo $key;?>" id="<?php echo $key;?>" class="area"><?php echo $val;?></textarea><?php }?></td>
	</tr>
<?php
}
?>
</table>
<div class="submit"><input type="submit" name="submit" value="Save Changes" class="button" /></div>
</form>
<script src="../tinymce/tinymce.min.js"></script>
<script>
$(".fb").fancybox();

tinymce.init({
selector:"textarea",
height:250,
theme:"modern",

plugins:["advlist autolink lists link image media emoticons charmap preview hr anchor searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking save table contextmenu directionality paste textcolor colorpicker"],

toolbar1:"fontselect | fontsizeselect | forecolor backcolor | bold italic underline strikethrough | removeformat",
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
<script>
    window.onload = function(){
        document.getElementById("siteurl").value = "<?php echo $SCRIPTURL; ?>";
        document.getElementById("siteurl").setAttribute("disabled", "true");
    }
</script>