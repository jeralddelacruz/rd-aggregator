<div class="row">
	<div class="col-md-12">
<?php
$res=$DB->query("select * from $dbprefix"."art order by art_order");
if(sizeof($res)){
	foreach($res as $row){
?>
		<div class="card article-item">
			<a href="index.php?cmd=artview&id=<?php echo $row["art_id"];?>" class="article-link">
				<div class="col-md-12 col-sm-6 col-xs-12 article-desc">
					<h4><?php echo $row["art_title"];?></h4>
					<?php echo $row["art_desc"];?>
				</div>
			</a>
			<div class="clearfix"></div>
		</div>
<?php
	}
}
?>
	</div>
</div>