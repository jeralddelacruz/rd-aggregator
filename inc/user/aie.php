<?php
if(!preg_match(";ecg;",$cur_pack["pack_ar"])){
	redirect("index.php?cmd=deny");
}

if((dir_count("../upload/".$UserID."/flat")>=$ECG_ARR["flat"])||($ECG_ARR["mon"]&&($ECG_ARR["left"]<=0))){
?>
<div class="row">
	<div class="alert alert-danger">You have exceeded the allowed amount of Flat Graphics. You can <a href="index.php?cmd=renew" class="mark">Upgrade Membership</a> to a higher level.</div>
</div>
<?php
}
else{
	$id=trim($_GET["id"]);
	if($id&&(!(($cur_cover=$DB->info("cover","cover_id='$id' and user_id='$UserID'"))&&(is_dir("../aie/tmp/".$cur_cover["cover_dir"]))&&(is_file($cur_cover["cover_bg"]))))){
		redirect("index.php?cmd=cover");
	}
?>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="content">
				<iframe src="../aie/index.php<?php echo $id?"?id=$id":"";?>" width="1000" height="1200" frameborder="no" scrolling="auto"></iframe>
			</div>
		</div>
	</div>
</div>
<?php
}
?>