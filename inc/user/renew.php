<?php
if($cur_pack["pack_buy"]){
	header("location:".$cur_pack["pack_buy"]);
	exit;
}
else{
?>
<div class="row">
	<!-- <div class="col-md-12">
		<div class="alert alert-warning">You're a <strong><?php echo $cur_pack["pack_title"];?></strong> Member and <strong>can NOT</strong> upgrade to the higher level.</div>
	</div> -->
	<div class="col-md-12">
		<a href="index.php?cmd=info"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill pull-right">Back to Profile</div></a>
	</div>
	<div class="col-md-12">
		<div class="embed-responsive embed-responsive-16by9">
			<!--<iframe src='https://kosky.clickfunnels.com/buy-upgrades1603899833746' width='100%' height='650' frameborder='0'></iframe>-->
			<iframe src='https://kosky.clickfunnels.com/buy-upgrades1629915977317' width='100%' height='650' frameborder='0'></iframe>
		</div>
	</div>
</div>
<?php
}
?>