<?php
$dir="../upload/".$UserID."/flat";
$arr=@scandir($dir);
$num=0;
if(sizeof($arr)>2){
	array_shift($arr);
	array_shift($arr);
	rsort($arr);
	$num=sizeof($arr);
}

?>
<div class="row">
<?php
if(dir_count("../upload/".$UserID."/3d")>=$ECG_ARR["3d"]){
?>
<div class="alert alert-danger">You have exceeded the allowed amount of 3D Graphics. <a href="index.php?cmd=cover" class="mark">Click here</a> to delete unnecessary images or <a href="index.php?cmd=renew" class="mark">Upgrade Membership</a>.</div>
<?php
}
else{
if($_GET["flat"]&&@in_array($_GET["flat"],$arr)){
	$mod_arr=range(1,8);
?>
			<div class="col-md-6">
				<div class="card">
					<div class="header">
						<h4 class="title" style="float:left;margin:5px 15px 0 0;">Choose Graphics Style</h4>
						<a href="index.php?cmd=cover"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Back to Graphics</div></a>
					</div>
					<div class="content">
<?php
foreach($mod_arr as $k){
?>
						<img id="mod<?php echo $k;?>" src="../ecover/images/mod<?php echo $k;?>.png" data-value="<?php echo $k;?>" class="img-responsive style-3d" /><br />
<?php
}
?>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card">
					<div class="header">
						<h4 class="title" style="margin:5px 15px 0 0;">Graphics Preview</h4>
					</div>
					<div class="content">
						<embed id="preview" type="application/x-shockwave-flash" src="../ecover/mod1_1.swf?fname=&fnamePath=<?php echo $dir."/".$_GET["flat"];?>&s=nosave&cc=ffffff" width="387" height="350"></embed>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card">
					<div class="header">
						<h4 class="title" style="margin:5px 15px 0 0;">Graphics Settings</h4>
					</div>
					<div class="content">
						<div class="row">
							<div class="col-md-8">
								<input type="text" id="title" class="form-control" placeholder="Graphics Title" />
							</div>
							<div class="col-md-4">
								<input type="button" id="save" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill pull-right" value="Save Graphics" />
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
<script>
jQuery(function($){
	var style=1;
	var size=1;
	var bg="ffffff";
	var title="";
	var save="nosave";

	reload();

	$(".style-3d").click(function(){
		style=$(this).attr("data-value");
		reload();
	});

	$("#title").change(function(){
		title=$(this).val();
	});

	$("#save").click(function(){
		save="save";
		reload();
	});

	function reload(){
		$(".style-3d").removeClass("style-3d-act");
		$("#mod"+style).addClass("style-3d-act");

		$("#preview").attr("src","../ecover/mod"+style+"_"+size+".swf?fname="+title+"&fnamePath=<?php echo $dir."/".$_GET["flat"];?>&s="+save+"&cc="+bg);
	}
});
</script>
<?php
}
else{
?>
	<div class="col-md-12">
		<div class="card">
			<div class="header">
				<h4 class="title" style="float:left;margin:5px 15px 0 0;">Choose a Flat Graphics to Use</h4>
				<a href="index.php?cmd=cover"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Back to Graphics</div></a>
			</div>
			<div class="content">
<?php
if($num){
	$i=0;
	foreach($arr as $file){
		$i++;
		if($i==5){
			$i=1;
?>
				<div class="clearfix"></div>
<?php
		}
?>
				<div class="col-md-3 col-sm-4 col-xs-6 ecover-item text-center">
					<a href="index.php?cmd=cover3d&flat=<?php echo $file;?>" data-toggle="tooltip" title="Use this Graphics"><img src="<?php echo $dir."/".$file;?>" class="img-responsive ecover-img" /></a>
				</div>
<?php
	}
}
else{
?>
				<div class="alert alert-warning">You currently don't have any Flat Graphics. <a href="index.php?cmd=aie" class="mark">Click here</a> to create one.</div>
<?php
}
?>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
<?php
}
}
?>
</div>