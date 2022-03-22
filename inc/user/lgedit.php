<?php
if(!ereg(";lg;",$cur_pack["pack_ar"])){
	redirect("index.php?cmd=deny");
}

$id=$_GET["id"];

if($id&&!$cur_lg=$DB->info("lg","lg_id='$id' and user_id='$UserID'")){
	redirect("index.php?cmd=lg");
}

$lgbg_dir="../upload/lgbg";
$lgbg_arr=dir_scan($lgbg_dir);
unset($lgbg_arr[array_search("index.html",$lgbg_arr)]);

$bg_lib=0;
if($cur_lg&&preg_match("/^".make_pat($lgbg_dir)."/i",$cur_lg["lg_bg"])){
	$bg_lib=1;
}

if($_GET["del"]){
	$del=trim($_GET["del"]);
	if($cur_lg["lg_".$del]){
		if(!(($del=="bg")&&$bg_lib)){
			@unlink("../lg/".$cur_lg["lg_".$del]);
		}
		$DB->query("update $dbprefix"."lg set lg_".$del."='' where lg_id='$id'");

		if($del!="bg"){
			$type=str_replace("img","type",$del);
			$DB->query("update $dbprefix"."lg set lg_".$type."='img' where lg_id='$id'");
		}
	}
	redirect("index.php?cmd=lgedit&id=$id");
}

$LG_RIGHT_ARR=array();
$res=$DB->query("select * from $dbprefix"."lgt where user_id='0' order by lgt_order");
if(sizeof($res)){
	foreach($res as $row){
		$LG_RIGHT_ARR[$row["lgt_id"]]=$row["lgt_title"];
	}
}

$LG_CRIGHT_ARR=array();
$res=$DB->query("select * from $dbprefix"."lgt where user_id='$UserID' order by lgt_order");
if(sizeof($res)){
	foreach($res as $row){
		$LG_CRIGHT_ARR[$row["lgt_id"]]=$row["lgt_title"];
	}
}

$dim_arr=$LG_HF_DIM_ARR;

$val_arr=array("bg","htype","halign","htext","himg","hurl","ftype","falign","ftext","fimg","furl");
if(!$_POST["submit"]){
	if(!$id){
		$right_arr=array();
		$extra_arr=array();
		$cright_arr=array();
		$cextra_arr=array();
	}
	else{
		$title=$cur_lg["lg_title"];
		$type=$cur_lg["lg_type"];
		$typeval=$cur_lg["lg_typeval"];
		$right_arr=explode(";",$cur_lg["lg_right"]);
		$extra_arr=unserialize($cur_lg["lg_extra"]);
		$cright_arr=explode(";",$cur_lg["lg_cright"]);
		$cextra_arr=unserialize($cur_lg["lg_cextra"]);

		foreach($val_arr as $val){
			${$val}=$cur_lg["lg_".$val];
		}
	}
}
else{
	$title=strip($_POST["title"]);
	$type=$_POST["type"];
	$typeval=strip($_POST["typeval"]);

	$right_arr=array_keys($_POST["right"]);
	$cright_arr=array_keys($_POST["cright"]);

	$extra_arr=array();
	foreach($_POST["extra"] as $k=>$v){
		$extra_arr[$k]=slash(strip($v));
	}
	$cextra_arr=array();
	foreach($_POST["cextra"] as $k=>$v){
		$cextra_arr[$k]=slash(strip($v));
	}

	foreach($val_arr as $val){
		if(($val=="bg")||($val=="himg")||($val=="fimg")){continue;}
		${$val}=strip($_POST[$val]);
	}
	if($hurl&&!preg_match("/^(http|https|ftp)/i",$hurl)){$hurl="http://".$hurl;}
	if($furl&&!preg_match("/^(http|https|ftp)/i",$furl)){$furl="http://".$furl;}

	$error="";
	if(!$title||!$type){
		$error.="&bull; Required fields should be <strong>filled in</strong>.<br />";
	}
	if(($type==100)&&!$typeval){
		$error.="&bull; License Type should be <strong>entered</strong>.<br />";
	}
	if(($htype=="text")&&!$htext){
		$error.="&bull; Header Text should be <strong>filled in</strong>.<br />";
	}
	if((!$cur_lg["lg_bg"])&&$_FILES["bg"]["tmp_name"]&&!getimagesize($_FILES["bg"]["tmp_name"])){
		$error.="&bull; <strong>Invalid</strong> Background Image file.<br />";
	}
	if((!$cur_lg["lg_himg"])&&($htype=="img")&&!($himg_arr=getimagesize($_FILES["himg"]["tmp_name"]))){
		$error.="&bull; <strong>Invalid</strong> Header Image file.<br />";
	}
	if((!$cur_lg["lg_himg"])&&($htype=="img")&&(($himg_arr[0]>$dim_arr[0])||($himg_arr[1]>$dim_arr[1]))){
		$error.="&bull; <strong>Invalid</strong> Header Image dimensions.<br />";
	}
	if(($ftype=="text")&&!$ftext){
		$error.="&bull; Footer Text should be <strong>filled in</strong>.<br />";
	}
	if((!$cur_lg["lg_fimg"])&&($ftype=="img")&&!($fimg_arr=getimagesize($_FILES["fimg"]["tmp_name"]))){
		$error.="&bull; <strong>Invalid</strong> Footer Image file.<br />";
	}
	if((!$cur_lg["lg_fimg"])&&($ftype=="img")&&(($fimg_arr[0]>$dim_arr[0])||($fimg_arr[1]>$dim_arr[1]))){
		$error.="&bull; <strong>Invalid</strong> Footer Image dimensions.<br />";
	}

	if(!$error){
		$right=implode(";",$right_arr);
		$extra=addslashes(serialize($extra_arr));
		$cright=implode(";",$cright_arr);
		$cextra=addslashes(serialize($cextra_arr));

		$q="";
		foreach($val_arr as $val){
			if(($val=="bg")||($val=="himg")||($val=="fimg")){continue;}
			$q.=",lg_".$val."='".${$val}."'";
		}

		if(!$id){
			$id=$DB->getauto("lg");
			$DB->query("insert into $dbprefix"."lg set lg_id='$id',user_id='$UserID',lg_title='$title',lg_type='$type',lg_typeval='$typeval',lg_right='$right',lg_extra='$extra',lg_cright='$cright',lg_cextra='$cextra'".$q.",lg_rd='".time()."'");
		}
		else{
			$DB->query("update $dbprefix"."lg set lg_title='$title',lg_type='$type',lg_typeval='$typeval',lg_right='$right',lg_extra='$extra',lg_cright='$cright',lg_cextra='$cextra'".$q.",lg_rd='".time()."' where lg_id='$id'");
		}

		if(!$cur_lg["lg_bg"]){
			if($_FILES["bg"]["tmp_name"]){
				$name_arr=explode(".",$_FILES["bg"]["name"]);
				$bg="u".$UserID."-l".$id."-bg.".$name_arr[sizeof($name_arr)-1];
				if(@move_uploaded_file($_FILES["bg"]["tmp_name"],"../lg/".$bg)){
					$DB->query("update $dbprefix"."lg set lg_bg='$bg' where lg_id='$id'");
				}
			}
			elseif($_POST["bg_lib"]){
				$bg=$_POST["bg_lib"];
				$bg_lib=1;
				$DB->query("update $dbprefix"."lg set lg_bg='$bg' where lg_id='$id'");
			}
		}
		if((!$cur_lg["lg_himg"])&&($htype=="img")){
			$name_arr=explode(".",$_FILES["himg"]["name"]);
			$himg="u".$UserID."-l".$id."-h.".$name_arr[sizeof($name_arr)-1];
			if(@move_uploaded_file($_FILES["himg"]["tmp_name"],"../lg/".$himg)){
				$DB->query("update $dbprefix"."lg set lg_himg='$himg' where lg_id='$id'");
			}
		}
		if((!$cur_lg["lg_fimg"])&&($ftype=="img")){
			$name_arr=explode(".",$_FILES["fimg"]["name"]);
			$fimg="u".$UserID."-l".$id."-f.".$name_arr[sizeof($name_arr)-1];
			if(@move_uploaded_file($_FILES["fimg"]["tmp_name"],"../lg/".$fimg)){
				$DB->query("update $dbprefix"."lg set lg_fimg='$fimg' where lg_id='$id'");
			}
		}

		lgdld($id);

		redirect("index.php?cmd=lg");
	}
}

$type_str="";
foreach($LG_TYPE_ARR as $k=>$v){
	$type_str.="<option value=\"$k\"".(($k==$type)?" selected":"").">$v</option>";
}

$hf_type_arr=array("text"=>"Text","img"=>"Image");
$htype_str="";
$ftype_str="";
foreach($hf_type_arr as $k=>$v){
	$htype_str.="<option value=\"$k\"".(($k==$htype)?" selected":"").">$v</option>";
	$ftype_str.="<option value=\"$k\"".(($k==$ftype)?" selected":"").">$v</option>";
}

$hf_align_arr=array("al"=>"Left","ac"=>"Center","ar"=>"Right");
$halign_str="";
$falign_str="";
foreach($hf_align_arr as $k=>$v){
	$halign_str.="<option value=\"$k\"".(($k==$halign)?" selected":"").">$v</option>";
	$falign_str.="<option value=\"$k\"".(($k==$falign)?" selected":"").">$v</option>";
}

if($error){
?>
<div class="alert alert-danger"><?php echo $error;?></div>
<?php
}
?>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="header">
				<h4 class="title" style="float:left;margin:5px 15px 0 0;"><?php echo $index_title;?></h4>
				<a href="index.php?cmd=lg"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Back to License Generator</div></a>
<?php
if($id){
?>
				<a href="index.php?cmd=lgedit"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill" style="margin-left:15px;">Add New License Certificate</div></a>
<?php
}
?>
			</div>
			<div class="content">
			<form method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="title" style="margin-top:7px;">Product Name</label>
							<input type="text" id="title" name="title" value="<?php echo $_POST["submit"]?slash($title):$title;?>" class="form-control" maxlength="250" />
						</div>
						<div class="form-group">
							<label for="type">License Type</label>
							<select id="type" name="type" class="form-control"><option value="0">[Select Type]</option><?php echo $type_str;?></select>
						</div>
						<div id="typeval_div" class="form-group">
							<input type="text" id="typeval" name="typeval" value="<?php echo $_POST["submit"]?slash($typeval):$typeval;?>" class="form-control" maxlength="250" placeholder="Enter License Type" />
						</div>
						<div class="form-group">
							<label for="bg" style="float:left;margin-top:7px;">Background Image (optional)</label>
							<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Background image is spread to 794 x 1122 pixels to fill A4 paper size."><i class="fa fa-question" aria-hidden="true"></i></span>
							<div class="clearfix"></div>
							<div id="bg_div">
<?php
if(!$cur_lg["lg_bg"]){
?>
								<label class="btn btn-default btn-file"><input type="file" id="bg" name="bg" style="cursor:pointer;" /></label>
<?php
	if(sizeof($lgbg_arr)){
?>
								<a href="#lgbg" class="fb pull-right"><label class="btn btn-default btn-file btn_srch">Browse Library...</label></a>
<?php
	}
}
else{
?>
								<img src="<?php echo ($bg_lib?"":"../lg/").$cur_lg["lg_bg"];?>" class="img-responsive inline-block" />
								<a href="index.php?cmd=lgedit&id=<?php echo $id;?>&del=bg" onclick="return confirm('Are you sure you wish to delete Background Image?');"><img src="../img/del.png" data-toggle="tooltip" title="Delete" style="vertical-align:bottom;" /></a>
<?php
}
?>
							</div>
						</div>
						<div class="htype-container">
							<div class="form-group">
								<label for="htype">Header Type</label>
								<select id="htype" name="htype" class="form-control"><option value="">[None]</option><?php echo $htype_str;?></select>
							</div>
							<div id="halign_div" class="form-group">
								<label for="halign">Header Align</label>
								<select id="halign" name="halign" class="form-control"><?php echo $halign_str;?></select>
							</div>
							<div id="htext_div" class="form-group">
								<label for="htext">Header Text</label>
								<input type="text" id="htext" name="htext" value="<?php echo $_POST["submit"]?slash($htext):$htext;?>" class="form-control" maxlength="250" />
							</div>
							<div id="hurl_div" class="form-group">
								<label for="hurl">Header URL (optional)</label>
								<input type="text" id="hurl" name="hurl" value="<?php echo $_POST["submit"]?slash($hurl):$hurl;?>" class="form-control" maxlength="250" />
							</div>
							<div id="himg_div" class="form-group">
								<label for="himg" style="float:left;margin-top:7px;">Header Image</label>
								<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="The image dimensions should NOT exceed <?php echo $dim_arr[0]."x".$dim_arr[1];?> px."><i class="fa fa-question" aria-hidden="true"></i></span>
								<div class="clearfix"></div>
<?php
if(!$cur_lg["lg_himg"]){
?>
								<label class="btn btn-default btn-file"><input type="file" id="himg" name="himg" style="cursor:pointer;" /></label>
<?php
}
else{
?>
								<img src="../lg/<?php echo $cur_lg["lg_himg"];?>" class="img-responsive inline-block" />
								<a href="index.php?cmd=lgedit&id=<?php echo $id;?>&del=himg" onclick="return confirm('Are you sure you wish to delete Header Image?');"><img src="../img/del.png" data-toggle="tooltip" title="Delete" style="vertical-align:bottom;" /></a>
<?php
}
?>
							</div>
						</div>
						<div class="ftype-container">
							<div class="form-group">
								<label for="ftype">Footer Type</label>
								<select id="ftype" name="ftype" class="form-control"><option value="">[None]</option><?php echo $ftype_str;?></select>
							</div>
							<div id="falign_div" class="form-group">
								<label for="falign">Footer Align</label>
								<select id="falign" name="falign" class="form-control"><?php echo $falign_str;?></select>
							</div>
							<div id="ftext_div" class="form-group">
								<label for="ftext">Footer Text</label>
								<input type="text" id="ftext" name="ftext" value="<?php echo $_POST["submit"]?slash($ftext):$ftext;?>" class="form-control" maxlength="250" />
							</div>
							<div id="furl_div" class="form-group">
								<label for="furl">Footer URL (optional)</label>
								<input type="text" id="furl" name="furl" value="<?php echo $_POST["submit"]?slash($furl):$furl;?>" class="form-control" maxlength="250" />
							</div>
							<div id="fimg_div" class="form-group">
								<label for="fimg" style="float:left;margin-top:7px;">Footer Image</label>
								<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="The image dimensions should NOT exceed <?php echo $dim_arr[0]."x".$dim_arr[1];?> px."><i class="fa fa-question" aria-hidden="true"></i></span>
								<div class="clearfix"></div>
<?php
if(!$cur_lg["lg_fimg"]){
?>
								<label class="btn btn-default btn-file"><input type="file" id="fimg" name="fimg" style="cursor:pointer;" /></label>
<?php
}
else{
?>
								<img src="../lg/<?php echo $cur_lg["lg_fimg"];?>" class="img-responsive inline-block" />
								<a href="index.php?cmd=lgedit&id=<?php echo $id;?>&del=fimg" onclick="return confirm('Are you sure you wish to delete Footer Image?');"><img src="../img/del.png" data-toggle="tooltip" title="Delete" style="vertical-align:bottom;" /></a>
<?php
}
?>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="cur-def" style="float:left;margin-top:7px;">License Terms</label>
							<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Enter additional stipulations (if any) in the field following a certain <strong>License Term</strong>."><i class="fa fa-question" aria-hidden="true"></i></span>
							<div class="clearfix"></div>
							<div class="form-box">
<?php
if(sizeof($LG_RIGHT_ARR)){
	foreach($LG_RIGHT_ARR as $k=>$v){
?>
								<div class="form-check" style="margin-bottom:15px;">
									<input type="checkbox" id="right<?php echo $k;?>" name="right[<?php echo $k;?>]" value="1" class="form-check-input"<?php echo in_array($k,$right_arr)?" checked":"";?> />
									<label for="right<?php echo $k;?>"><?php echo $v;?></label>
									<input type="text" name="extra[<?php echo $k;?>]" value="<?php echo $extra_arr[$k];?>" class="form-control" maxlength="250" />
								</div>
<?php
	}
}
?>
							</div>
						</div>
						<div class="form-group">
							<label class="cur-def" style="float:left;margin-top:7px;">Custom License Terms</label>
							<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Enter additional stipulations (if any) in the field following a certain <strong>License Term</strong>."><i class="fa fa-question" aria-hidden="true"></i></span>
							<div class="clearfix"></div>
							<div class="form-box">
<?php
if(sizeof($LG_CRIGHT_ARR)){
	foreach($LG_CRIGHT_ARR as $k=>$v){
?>
								<div class="form-check" style="margin-bottom:15px;">
									<input type="checkbox" id="cright<?php echo $k;?>" name="cright[<?php echo $k;?>]" value="1" class="form-check-input"<?php echo in_array($k,$cright_arr)?" checked":"";?> />
									<label for="cright<?php echo $k;?>"><?php echo $v;?></label>
									<input type="text" name="cextra[<?php echo $k;?>]" value="<?php echo $cextra_arr[$k];?>" class="form-control" maxlength="250" />
								</div>
<?php
	}
}
?>
								<a href="index.php?cmd=lgt"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Custom License Terms</div></a>
							</div>
						</div>
					</div>
				</div>
				<input type="submit" id="submit" name="submit" value="Save Changes" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill pull-right" />
				<div class="clearfix"></div>
			</form>
			</div>
		</div>
	</div>
</div>
<div id="lgbg" style="display:none;">
<p class="text-center">Click on any image below to choose it as a <strong>Background Image</strong>.</p>
<?php
if(sizeof($lgbg_arr)){
	foreach($lgbg_arr as $f){
		$src=$lgbg_dir."/".str_replace("%","%25",$f);
?>
<img src="<?php echo $src;?>" class="bg-img inline-block" style="max-width:150px;vertical-align:bottom;padding:1px;cursor:pointer;" />
<?php
	}
}
?>
</div>
<div id="wait" style="display:none;padding:10px;font-size:15px;font-weight:bold;">
<img src="../img/loader.gif" style="vertical-align:middle;" />&nbsp;&nbsp;Please wait...
<div id="time" style="font-weight:normal;">00:00:00</div>
</div>
<script src="../js/jquery.blockUI.js"></script>
<script src="../js/jquery.stopwatch.js"></script>
<script>
jQuery(document).ready(function($){
	$(".fb").fancybox({maxWidth:800});

	$(".bg-img").click(function(){
		$("#bg_div").html("<label class=\"btn btn-default btn-file cur-def\"><input type=\"hidden\" name=\"bg_lib\" value=\""+$(this).attr("src")+"\" />"+$(this).attr("src").replace("<?php echo $lgbg_dir;?>/","")+"</label>");
		$.fancybox.close();
	});

	$("#type").change(function(){
		if($(this).val()=="100"){
			$("#typeval_div").removeClass("hide");
		}
		else{
			$("#typeval_div").addClass("hide");
		}
	});
	$("#type").trigger("change");

	$("#htype").change(function(){
		if($(this).val()=="text"){
			$("#halign_div").removeClass("hide");
			$("#htext_div").removeClass("hide");
			$("#hurl_div").removeClass("hide");
			$("#himg_div").addClass("hide");
		}
		else if($(this).val()=="img"){
			$("#halign_div").removeClass("hide");
			$("#htext_div").addClass("hide");
			$("#hurl_div").addClass("hide");
			$("#himg_div").removeClass("hide");
		}
		else{
			$("#halign_div").addClass("hide");
			$("#htext_div").addClass("hide");
			$("#hurl_div").addClass("hide");
			$("#himg_div").addClass("hide");
		}
	});
	$("#htype").trigger("change");

	$("#ftype").change(function(){
		if($(this).val()=="text"){
			$("#falign_div").removeClass("hide");
			$("#ftext_div").removeClass("hide");
			$("#furl_div").removeClass("hide");
			$("#fimg_div").addClass("hide");
		}
		else if($(this).val()=="img"){
			$("#falign_div").removeClass("hide");
			$("#ftext_div").addClass("hide");
			$("#furl_div").addClass("hide");
			$("#fimg_div").removeClass("hide");
		}
		else{
			$("#falign_div").addClass("hide");
			$("#ftext_div").addClass("hide");
			$("#furl_div").addClass("hide");
			$("#fimg_div").addClass("hide");
		}
	});
	$("#ftype").trigger("change");

	$("#submit").click(function(){
		$.blockUI({message:$("#wait")});
		$("#time").stopwatch().stopwatch("start");
	});
});
</script>