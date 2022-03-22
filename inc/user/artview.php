<?php
$id=$_GET["id"];

if(!$row=$DB->info("art","art_id='$id'")){
	redirect("index.php?cmd=art");
}
?>
<div class="row">
	<div class="col-md-12">
		<div class="card article-content">
			<div class="card-body">
				<a href="index.php?cmd=art"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill pull-right">Back to Articles</div></a>
				<h2><?php echo $row["art_title"];?></h2>
				<?php echo $row["art_body"];?>
			</div>
		</div>
	</div>
</div>