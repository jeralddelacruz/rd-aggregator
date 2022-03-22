<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="header">
				<h4 class="title"><?php echo $index_title;?></h4>
			</div>
<?php
$res=$DB->query("select * from $dbprefix"."bonus order by bonus_order, bonus_id desc");
if(sizeof($res)){
?>
			<div class="content">
				<div class="row">
<?php
$i=0;
	foreach($res as $row){
		$show=preg_match("/;$PackID;/i",$row["bonus_pack"])?1:0;

$i++;
if($i==4){
	$i=1;
?>
					<div class="clearfix"></div>
<?php
}
?>
					<div class="col-md-4">
						<a href="index.php?cmd=<?php echo $show?("bonusview&id=".$row["bonus_id"]):"renew";?>"<?php echo $show?" class=\"article-link\"":"";?>>
							<div class="card">
								<div class="content text-center"<?php echo $show?"":" style=\"opacity:0.5;\"";?>>
										<h4><?php echo $row["bonus_title"];?></h4>
										<?php echo str_replace("<img","<img class=\"img-responsive\"",$row["bonus_desc"]);?>
								</div>
							</div>
						</a>
					</div>
<?php
	}
?>
				</div>
			</div>
<?php
}
?>
		</div>
	</div>

</div>