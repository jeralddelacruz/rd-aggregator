<?php
$id=$_GET["id"];

if($id&&(!$row=$DB->info("prb","prb_id='$id'"))){
	redirect("index.php?cmd=prb");
}

if(!$_POST["submit"]){
	if(!$id){
		$pr=0;
		$pack_arr=array("11","13","10","12","15","17","14","16","19","21","18","20","5","4");
		$ptext="Download Now";
		$ltext="Download License";
	}
	else{
		$pr=$row["pr_id"];
		$pack_arr=explode(";",trim($row["prb_pack"],";"));
		$title=$row["prb_title"];
		$price=$row["prb_price"];
		$ptext=$row["prb_ptext"];
		$ltext=$row["prb_ltext"];
	}
}
else{
	$pr=(int)$_POST["pr"];
	$pack_arr=$_POST["pack"];
	$title=strip($_POST["title"]);
	$price=(double)$_POST["price"];
	$ptext=strip($_POST["ptext"]);
	$ltext=strip($_POST["ltext"]);

	$error="";
	if(!$pr||!$title||!$ptext||!$ltext){
		$error.="&bull; Required fields should be <strong>filled in</strong>.<br />";
	}
	if(!sizeof($pack_arr)){
		$error.="&bull; At least one <strong>Available For</strong> Membership should be <strong>chosen</strong>.<br />";
	}

	if(!$error){
		$pack=";".implode(";",$pack_arr).";";
		$price=number_format($price,2,".","");

		if(!$id){
			$id=$DB->getauto("prb");
			$order=$DB->getmaxval("prb_order","prb")+1;

			$DB->query("insert into $dbprefix"."prb set prb_id='$id',pr_id='$pr',prb_pack='$pack',prb_title='$title',prb_price='$price',prb_ptext='$ptext',prb_ltext='$ltext',prb_order='$order'");
		}
		else{
			$DB->query("update $dbprefix"."prb set pr_id='$pr',prb_pack='$pack',prb_title='$title',prb_price='$price',prb_ptext='$ptext',prb_ltext='$ltext' where prb_id='$id'");
		}

		redirect("index.php?cmd=prb");
	}
}

$pr_str="";
$res1=$DB->query("select * from $dbprefix"."pr order by pr_title");
if(sizeof($res1)){
	foreach($res1 as $row1){
		$k=$row1["pr_id"];
		$pr_str.="<option value=\"$k\"".(($k==$pr)?" selected":"").">".$row1["pr_title"]."</option>";
	}
}

$prb_arr=$DB->get_pack();
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=prb" class="add">Back to CB Products</a><?php if($id){?><a href="index.php?cmd=prbedit" class="add">Add New CB Product</a><?php }?></h2>
<?php
if($error){
?>
<div class="error"><?php echo $error;?></div>
<?php
}
?>
<form method="post">
<table class="tbl_form">
	<tr>
		<th><label for="pr">Product</label></th>
		<td><select name="pr" id="pr" class="sel"><option value="0">[Select Product]</option><?php echo $pr_str;?></select></td>
	</tr>
	<tr>
		<th>Available For<img src="../img/help.png" title="Choose Membership(s) the Product is available for." class="help" /></th>
		<td><input type="checkbox" id="chk" /> <label id="chk_l" for="chk">Check All</label><br /><br />
<?php
if(sizeof($prb_arr)){
	foreach($prb_arr as $k=>$v){
?>
			<input type="checkbox" name="pack[]" id="pack<?php echo $k;?>" value="<?php echo $k;?>"<?php echo (in_array($k,$pack_arr)?" checked":"");?> class="chk" /> <label for="pack<?php echo $k;?>"><?php echo $v;?></label><br />
<?php
	}
}
?>
		</td>
	</tr>
	<tr>
		<th><label for="title">Title</label></th>
		<td><input type="text" name="title" id="title" value="<?php echo($_POST["submit"]?slash($title):$title);?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="price">Price, USD <span class="desc">(optional)</span></label><img src="../img/help.png" title="Used for VALUE or WORTH info." class="help" /></th>
		<td><input type="text" name="price" id="price" value="<?php echo $price?number_format($price,2,".",""):"";?>" class="text_s" maxlength="25" /></td>
	</tr>
	<tr>
		<th><label for="ptext">Download Product Link Text</label></th>
		<td><input type="text" name="ptext" id="ptext" value="<?php echo($_POST["submit"]?slash($ptext):$ptext);?>" class="text" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="ltext">Download License Link Text</label></th>
		<td><input type="text" name="ltext" id="ltext" value="<?php echo($_POST["submit"]?slash($ltext):$ltext);?>" class="text" maxlength="250" /></td>
	</tr>
</table>
<div class="submit"><input type="submit" name="submit" class="button" value="Save Changes" /></div>
</form>
<script>
jQuery(document).ready(function($){

$("#pr").on("change",function(){
	var selected=$(this).val();

	if(selected!=0){
		$("#title").val($("#pr option:selected").text());
	}
});

$("#chk").click(function(){
	var chk=$(this).prop("checked");
	$(".chk").prop("checked",chk);
	$("#chk_l").text(chk==true?"Uncheck All":"Check All");
});

});
</script>