<?php
if($_POST["add"]){
	$title=strip($_POST["title"]);
	if($title){
		$order=$DB->getmaxval("lgt_order","lgt","user_id='$UserID'")+1;
		$DB->query("insert into $dbprefix"."lgt set user_id='$UserID',lgt_title='$title',lgt_order='$order'");
	}

	redirect("index.php?cmd=lgt");
}
elseif($_POST["submit"]){
	$title_arr=$_POST["title_arr"];
	if(sizeof($title_arr)){
		foreach($title_arr as $k=>$v){
			$v=strip($v);
			if($v){
				$DB->query("update $dbprefix"."lgt set lgt_title='$v' where lgt_id='$k' and user_id='$UserID'");
			}
		}
	}

	redirect("index.php?cmd=lgt");
}
elseif($_GET["del"]){
	$del_arr[]=$_GET["del"];
	if(sizeof($del_arr)){
		$DB->query("delete from $dbprefix"."lgt where lgt_id IN (".implode(",",$del_arr).") and user_id='$UserID'");
	}

	redirect("index.php?cmd=lgt");
}
elseif($_GET["move"]){
	$DB->move("lgt",$_GET["move"],"user_id='$UserID'");

	redirect("index.php?cmd=lgt");
}

?>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="header">
				<h4 class="title" style="float:left;margin:5px 15px 0 0;"><?php echo $index_title;?></h4>
				<a href="index.php?cmd=lg"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Back to License Generator</div></a>
			</div>
			<div class="content table-responsive table-full-width">
			<form method="post">
				<table class="table table-hover table-striped">
					<thead>
						<tr>
							<th class="text-center" style="width:75px;">#</th>
							<th>License Term</th>
							<th class="text-center" style="width:75px;">Order</th>
							<th class="text-center" style="width:75px;">Action</th>
						</tr>
					</thead>
<?php
$res=$DB->query("select * from $dbprefix"."lgt where user_id='$UserID' order by lgt_order");
$num=sizeof($res);
if($num){
?>
					<tbody>
<?php
	$i=1;
	foreach($res as $row){
		$id=$row["lgt_id"];
?>
						<tr>
							<td class="text-center"><?php echo $i;?></td>
							<td><input type="text" name="title_arr[<?php echo $id;?>]" value="<?php echo $row["lgt_title"];?>" class="form-control" /></td>
							<td>
<?php
if($i>1){
?>
								<a href="index.php?cmd=lgt&move=u<?php echo $id;?>" class="green" data-toggle="tooltip" title="Move Up"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>
<?php
}
if($i<$num){
?>
								<a href="index.php?cmd=lgt&move=d<?php echo $id;?>" class="green pull-right" data-toggle="tooltip" title="Move Down"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>
<?php
}
?>
							</td>
							<td class="text-center">
								<a href="index.php?cmd=lgt&del=<?php echo $id;?>" class="red" data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you wish to delete this License Term?');"><i class="fa fa-close" aria-hidden="true"></i></a>
							</td>
	</tr>
<?php
		$i++;
	}
?>
					</tbody>
<?php
}
?>
				</table>
				<div class="col-md12">
					<input type="submit" name="submit" value="Save Changes" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill pull-right"<?php if(!$num){echo " disabled";}?> />
					<div class="clearfix"></div>
				</div>
			</form>
			</div>
		</div>
	</div>

	<div class="col-md-12">
		<div class="card">
			<div class="header">
				<h4 class="title">Add New License Term</h4>
			</div>
			<div class="content">
			<form method="post">
				<div class="row">
					<div class="col-md-9">
						<div class="form-group">
							<input type="text" id="title" name="title" placeholder="Enter License Term" class="form-control" />
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="submit" name="add" value="Add License Term" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill pull-right" />
						</div>
					</div>
				</div>
			</form>
			</div>
		</div>
	</div>
</div>