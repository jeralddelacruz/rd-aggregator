<?php
$id=$_GET["id"];

if(!$row=$DB->info("pr","pr_id='$id'")){
	redirect("index.php?cmd=home");
}

$title=$row["pr_title"];
$body=$row["pr_body"];
$cover=$row["pr_cover"];
$cover="<img src=\"../upload/pr/".($cover?"$id/$cover":"cover.gif")."\" class=\"img-responsive center-block\" />";
$dld=$row["pr_dld"];
$plr=$row["pr_plr"];

if($_GET["dld"]){
	$DB->query("update $dbprefix"."pr set pr_numd=(pr_numd+1) where pr_id='$id'");

	ob_clean();
	header("Content-Type:application/force-download");
	header("Content-Disposition:attachment;filename=$dld");
	header("Expires:0");
	header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
	header("Pragma:public");
	echo file_get_contents("../upload/pr/$id/$dld");

	exit;
}
elseif($_GET["plr"]){
	ob_clean();
	header("Content-Type:application/force-download");
	header("Content-Disposition:attachment;filename=$plr");
	header("Expires:0");
	header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
	header("Pragma:public");
	echo file_get_contents("../upload/pr/$id/$plr");

	exit;
}

$item="$cover<div class=\"al\">$body</div><a href=\"index.php?cmd=prview&id=$id&dld=1\"><input type=\"button\" value=\"Download\" class=\"button\" /></a><br /><br /><a href=\"index.php?cmd=prview&id=$id&plr=1\">Download License</a>";
?>
<div class="row">
	<div class="col-md-12">
		<div class="card article-content">
			<h2><?php echo $title;?></h2>
			<?php echo $cover;?>
			<br />
			<?php echo $body;?>
			<br />
			<div class="text-center">
				<a href="index.php?cmd=prview&id=<?php echo $id;?>&dld=1"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Download Product</div></a>
				<br /><br />
				<a href="index.php?cmd=prview&id=<?php echo $id;?>&plr=1"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Download License</div></a>
			</div>
		</div>
	</div>
</div>