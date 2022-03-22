<?php
$dir="../upload/lgbg";
$action="index.php?cmd=lgbg";

if(!empty($_FILES)){
	if(getimagesize($_FILES["file"]["tmp_name"])){
		$name=$_FILES["file"]["name"];
		$file=$dir."/".$name;
		move_uploaded_file($_FILES["file"]["tmp_name"],$file);
	}
}
elseif($_POST["del"]){
	$act_arr=$_POST["act_arr"];
	if(sizeof($act_arr)){
		foreach($act_arr as $f){
			unlink($dir."/".$f);
		}
	}
	redirect($action);
}

$arr=dir_scan($dir);
unset($arr[array_search("index.html",$arr)]);
?>
<h2><?php echo $index_title;?></h2>
<form action="<?php echo $action;?>" class="dropzone" id="mydz"></form>
<script>
Dropzone.options.mydz={
//	maxFilesize: 4,
	acceptedFiles: "image/*",
	dictDefaultMessage: "<p class='desc'><span class='large'>Drop files here or click to upload.</span></p>",
	queuecomplete: function(){
		alert("All files have been uploaded!");
		window.location="<?php echo $action;?>";
	}
};
</script>
<?php
if(sizeof($arr)){
?>
<form method="post">
<div style="overflow:hidden;">
<?php
	foreach($arr as $f){
		$src=$dir."/".str_replace("%","%25",$f);
?>
<div class="dz"><a href="<?php echo $src;?>" class="fb view" rel="gal"><img src="<?php echo $src;?>" class="dz-img" /></a><br /><input type="checkbox" name="act_arr[]" value="<?php echo $f;?>" class="chk" /></div>
<?php
	}
?>
</div>
<br />
<div class="ac">
<input type="checkbox" id="chk" /> <label id="chk_l" for="chk">Check All</label><br />
<input type="submit" name="del" value="Delete Selected" class="button" onclick="return confirm('Are you sure you wish to delete selected images?');" />
</div>
</form>
<script>
jQuery(function($){
	$("#chk").click(function(){
		var chk=$(this).prop("checked");
		$(".chk").prop("checked",chk);
		$("#chk_l").text(chk==true?"Uncheck All":"Check All");
	});

	$(".fb").fancybox();
});
</script>
<?php
}
?>