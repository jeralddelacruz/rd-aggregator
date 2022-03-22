<?php
$error=true;

if(isset($_SESSION["Door"])){
	$error=false;

	$arr=array();
	$res=$DB->query("select * from $dbprefix"."door where door_act='1' order by door_order");
	foreach($res as $row){
		$arr[]=$row;
	}

	if(sizeof($arr)){
		if($_POST["submit"]){
			$_SESSION["Door"]++;
			redirect("index.php?cmd=door");
		}
		if($_SESSION["Door"]>=sizeof($arr)){
			$error=true;
		}
	}
	else{
		$error=true;
	}
}

if($error){
	unset($_SESSION["Door"]);
	redirect("index.php?cmd=home");
}
else{
	$index_title=$arr[$_SESSION["Door"]]["door_title"];
?>
<div class="card col-md-12 col-sm-10 col-xs-9" style="margin:0 auto;float:none;">
<div class="content">
<form method="post">
	<h4 class="title">
		<b><?php echo $index_title;?></b>
		<small><a id="link" href="#" class="pull-right"><?php echo $arr[$_SESSION["Door"]]["door_top"];?></a></small>
	</h4>
	<br />
	<div class="title"><?php echo $arr[$_SESSION["Door"]]["door_body"];?></div>
	<br />
	<input type="submit" name="submit" id="submit" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill" value="<?php echo $arr[$_SESSION["Door"]]["door_bot"];?>" />
</form>
</div>
</div>
<script>
jQuery(function($){
	$("#link").click(function(){
		$("#submit").click();
	});
});
</script>
<?php
}
?>