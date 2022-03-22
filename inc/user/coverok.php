<?php
if(!ereg(";ecg;",$cur_pack["pack_ar"])){
	redirect("index.php?cmd=deny");
}

$file="../upload/".$UserID."/".$_GET["type"]."/".$_GET["name"];

if(!is_file($file)){
	redirect("index.php?cmd=cover");
}

?>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="header">
				<h4 class="title" style="float:left;margin:5px 15px 0 0;"><?php echo $index_title;?></h4>
				<a href="index.php?cmd=cover"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Back to Graphics</div></a>
			</div>
			<div class="content">
				<div class="alert alert-success">New Graphics <strong>created</strong>.</div>
				<img src="<?php echo $file;?>" class="img-responsive center-block" />
			</div>
		</div>
	</div>
</div>