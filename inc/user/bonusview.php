<?php
$id=$_GET["id"];

if(!$row=$DB->info("bonus","bonus_id='$id' and bonus_pack like '%;$PackID;%'")){
	redirect("index.php?cmd=bonus");
}
?>
<div class="row">
	<div class="col-md-12">
		<div class="card article-content">
			<div class="card-body">
				<a href="index.php?cmd=bonus"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill pull-right">Back to Bonuses</div></a>
				<h2><?php echo $row["bonus_title"];?></h2>
				<?php echo $row["bonus_body"];?>
			</div>
		</div>
	</div>
</div>