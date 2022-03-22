<?php
if(!ereg(";lg;",$cur_pack["pack_ar"])){
	redirect("index.php?cmd=deny");
}

if($_GET["dld"]){
	$dld=$_GET["dld"];
	if(!$cur_lg=$DB->info("lg","lg_id='$dld' and user_id='$UserID'")){
		redirect("index.php?cmd=lg");
	}

	$tmp="../lg/u".$UserID."-l".$dld.".tmp";
	$name=alphanum(str_replace(" ","_",$cur_lg["lg_title"]))."_license.pdf";
	lgsave($tmp,$name);
}
elseif($_GET["del"]){
	lg_del($_GET["del"],$UserID);
	redirect("index.php?cmd=lg");
}

?>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="header">
				<h4 class="title" style="float:left;margin:5px 15px 0 0;"><?php echo $index_title;?></h4>
				<a href="index.php?cmd=lgedit"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Add New License Certificate</div></a>
				<a href="index.php?cmd=lgt"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill" style="margin-left:15px;">Custom License Terms</div></a>
			</div>
			<div class="content table-responsive table-full-width">
				<table class="table table-hover table-striped">
					<thead>
						<tr>
							<th>Product Name</th>
							<th class="text-center" style="width:150px;">Last Modified</th>
							<th class="text-center" style="width:125px;">Action</th>
						</tr>
					</thead>
<?php
$res=$DB->query("select * from $dbprefix"."lg where user_id='$UserID' order by lg_rd desc");
if(sizeof($res)){
?>
					<tbody>
<?php
	foreach($res as $row){
		$id=$row["lg_id"];
?>
						<tr>
							<td><?php echo $row["lg_title"];?></td>
							<td class="text-center"><?php echo date("Y-m-d H:i:s",$row["lg_rd"]);?></td>
							<td class="text-center">
								<a href="index.php?cmd=lg&dld=<?php echo $id;?>" class="green" data-toggle="tooltip" title="Download"><i class="fa fa-download" aria-hidden="true"></i></a>
								<a href="index.php?cmd=lgedit&id=<?php echo $id;?>" class="blue" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
								<a href="index.php?cmd=lg&del=<?php echo $id;?>" class="red" data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you wish to delete this License Certificate?');"><i class="fa fa-close" aria-hidden="true"></i></a>
							</td>
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