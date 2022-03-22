<?php
$id=$_GET["id"];

$res=$DB->query("select m.*,(select count(mesview_id) from $dbprefix"."mesview v where v.mes_id=m.mes_id and v.user_id='$UserID') as view from $dbprefix"."mes m where m.mes_id='$id'");
if(!$row=$res[0]){
	redirect("index.php?cmd=mes");
}

if(!$row["view"]){
	$DB->query("insert into $dbprefix"."mesview set mes_id='$id',user_id='$UserID'");
}
?>
<div class="row">
	<div class="col-md-12">
		<div class="card article-content">
			<div class="card-body">
				<a href="index.php?cmd=mes"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill pull-right">Back to Notifications</div></a>
				<h2><?php echo $row["mes_title"];?></h2>
				<?php echo $row["mes_body"];?>
			</div>
		</div>
	</div>
</div>