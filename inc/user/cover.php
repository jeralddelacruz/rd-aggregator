<?php
if(!ereg(";ecg;",$cur_pack["pack_ar"])){
	redirect("index.php?cmd=deny");
}

if($_GET["dld"]||$_GET["del"]){
	$type_arr=array("flat"=>"Flat","3d"=>"3D");
	$type=$_GET["type"];
	if(!in_array($type,array_keys($type_arr))){$type="flat";}

	$dir="../upload/".$UserID."/".$type;
	$arr=@scandir($dir);
}

if($_GET["dld"]&&@in_array($_GET["dld"],$arr)){
	life_dld($dir."/".$_GET["dld"]);
}
elseif($_GET["del"]&&@in_array($_GET["del"],$arr)){
	unlink($dir."/".$_GET["del"]);
	if($type=="flat"){
		if($cur_cover=$DB->info("cover","cover_file='".$_GET["del"]."' and user_id='$UserID'")){
			$DB->query("delete from $dbprefix"."cover where cover_id='".$cur_cover["cover_id"]."'");
			$res=$DB->query("select cover_id from $dbprefix"."cover where cover_dir='".$cur_cover["cover_dir"]."' and cover_id<>'".$cur_cover["cover_id"]."'");
			if(!sizeof($res)){
				$DB->query("delete from $dbprefix"."text where text_dir='".$cur_cover["cover_dir"]."'");
				rem_dir("../aie/tmp/".$cur_cover["cover_dir"]);
			}
		}
	}
	redirect("index.php?cmd=cover");
}

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
	<div class="col-md-12">
		<div class="card">
			<div class="header">
				<h4 class="title" style="float:left;margin:5px 15px 0 0;">Your <strong>Flat Graphics</strong></h4>
				<a href="index.php?cmd=aie"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Create New Flat Graphics</div></a>
				<div class="pull-right mobile-text">
					<div class="stats">
						<i class="fa fa-pie-chart"></i> Folder Usage: <strong><?php echo $num." of ".$ECG_ARR["flat"];?></strong>
<?php
if($ECG_ARR["mon"]){
?>
						<br />
						<i class="fa fa-clock-o"></i> You can create <strong><?php echo ($ECG_ARR["left"]>0)?number_format($ECG_ARR["left"],0,".",","):0;?></strong> more Flat Graphics until <strong><?php echo $ECG_ARR["to"];?></strong>
<?php
}
?>
					</div>
				</div>
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
				<div class="clearfix"></div><br />
<?php
		}
?>
				<div class="col-md-3 col-sm-4 col-xs-6 Graphics-item text-center">
					<a href="<?php echo $dir."/".$file;?>" class="fb" rel="gal_flat"><img src="<?php echo $dir."/".$file;?>" class="img-responsive" style="border:#ccc 1px solid;margin:0 auto;" /></a>
					<a href="<?php echo $dir."/".$file;?>" class="gray fb" rel="gal_flat1" data-toggle="tooltip" title="View"><i class="fa fa-search" aria-hidden="true"></i></a>
					<a href="index.php?cmd=cover&type=flat&dld=<?php echo $file;?>" class="green" data-toggle="tooltip" title="Download"><i class="fa fa-download" aria-hidden="true"></i></a>
<?php
		if(($cur_cover=$DB->info("cover","cover_file='$file' and user_id='$UserID'"))&&(is_dir("../aie/tmp/".$cur_cover["cover_dir"]))&&(is_file($cur_cover["cover_bg"]))){

?>
					<a href="index.php?cmd=aie&id=<?php echo $cur_cover["cover_id"];?>" class="blue" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
<?php
		}
?>
					<a href="index.php?cmd=cover&type=flat&del=<?php echo $file;?>" class="red" data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you wish to delete this Graphics?');"><i class="fa fa-times" aria-hidden="true"></i></a>
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
</div>

<?php
$dir="../upload/".$UserID."/3d";
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
	<div class="col-md-12">
		<div class="card">
			<div class="header">
				<h4 class="title" style="float:left;margin:5px 15px 0 0;">Your <strong>3D Graphics</strong></h4>
				<a href="index.php?cmd=cover3d"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Create New 3D Graphics</div></a>
				<div class="pull-right mobile-text">
					<div class="stats">
						<i class="fa fa-pie-chart"></i> Folder Usage: <strong><?php echo $num." of ".$ECG_ARR["3d"];?></strong>
					</div>
				</div>
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
				<div class="clearfix"></div><br />
<?php
		}
?>
				<div class="col-md-3 col-sm-4 col-xs-6 Graphics-item text-center">
					<a href="<?php echo $dir."/".$file;?>" class="fb" rel="gal_3d"><img src="<?php echo $dir."/".$file;?>" class="img-responsive" style="border:#ccc 1px solid;margin:0 auto;" /></a>
					<a href="<?php echo $dir."/".$file;?>" class="gray fb" rel="gal_3d1" data-toggle="tooltip" title="View"><i class="fa fa-search" aria-hidden="true"></i></a>
					<a href="index.php?cmd=cover&type=3d&dld=<?php echo $file;?>" class="green" data-toggle="tooltip" title="Download"><i class="fa fa-download" aria-hidden="true"></i></a>
					<a href="index.php?cmd=cover&type=3d&del=<?php echo $file;?>" class="red" data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you wish to delete this Graphics?');"><i class="fa fa-times" aria-hidden="true"></i></a>
				</div>
<?php
	}
}
else{
?>
				<div class="alert alert-warning">You currently don't have any 3D Graphics. <a href="index.php?cmd=cover3d" class="mark">Click here</a> to create one.</div>
<?php
}
?>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
<script>
jQuery(function($){
	$(".fb").fancybox({"title":""});
});
</script>