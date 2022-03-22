<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="header p-3">
				<h4 class="title" style="float:left;margin:5px 15px 0 0;"><?php echo $index_title;?></h4>
			</div>
			<div class="content table-responsive table-full-width">
				<table class="table table-hover table-striped">
					<thead>
						<tr>
							<th class="text-left" style="width:250px;">Date</th>
							<th>Subject</th>
						</tr>
					</thead>
<?php
$res=$DB->query("select m.*,(select count(mesview_id) from $dbprefix"."mesview v where v.mes_id=m.mes_id and v.user_id='$UserID') as view from $dbprefix"."mes m order by mes_rd desc");
if(sizeof($res)){
?>
					<tbody>
<?php
	foreach($res as $row){
?>
						<tr>
							<td class="text-left"<?php if(!$row["view"]){echo " style=\"font-weight:bold;\"";}?>><?php echo date("Y-m-d H:i:s",$row["mes_rd"]);?></td>
							<td><a href="index.php?cmd=mesview&id=<?php echo $row["mes_id"];?>" data-toggle="tooltip" title="View Notification"<?php if(!$row["view"]){echo " style=\"font-weight:bold;\"";}?>><?php echo $row["mes_title"];?></a></td>
						</tr>
<?php
	}
?>
					</tbody>
<?php
}
?>
				</table>
			</div>
		</div>
	</div>
</div>