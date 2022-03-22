<?php
if($_GET["id"]){
	$id=$_GET["id"];
	if(!$row=$DB->info("pack","pack_id='$id'")){
		redirect("index.php?cmd=pack");
	}
}

$arr=array("jvz","cb","pp","title","buy","price","freq","trial","tprice", 'display_title');
$ecg_arr=array("mon","flat","3d","bg","bgs","icon","icons");
if(!$_POST["submit"]){
	if(!$id){
		$freq=1;
		$trial=0;
		$ar_arr=array();
		$lux_arr=array();
	}
	else{
		foreach($arr as $val){
			${$val}=$row["pack_".$val];
		}

		$ar_arr=explode(";",trim($row["pack_ar"],";"));

		$ecg=unserialize($row["pack_ecg"]);
		foreach($ecg_arr as $val){
			${"ecg_".$val}=$ecg[$val];
		}

		$lux_arr=explode(";",trim($row["pack_lux"],";"));
	}
}
else{
	$jvz=strip($_POST["jvz"]);
	$cb=strip($_POST["cb"]);
	$pp=strip($_POST["pp"]);
	$title=strip($_POST["title"]);
	$display_title=strip($_POST["display_title"]);
	$buy=strip($_POST["buy"]);
	$price=(double)$_POST["price"];
	$freq=(int)$_POST["freq"];
	$trial=(int)$_POST["trial"];
	$tprice=(double)$_POST["tprice"];
	$ar_arr=$_POST["ar"];
	$lux_arr=$_POST["lux"];

	$_ecg=array();
	foreach($ecg_arr as $val){
		${"ecg_".$val}=(int)$_POST["ecg_".$val];
		$_ecg[$val]=${"ecg_".$val};
	}

	$error="";
	if(!($jvz||$cb||$pp)){
		$error.="&bull; Whether JVZoo Product ID or ClickBank Item Number or W+ Product Code should be <strong>filled in</strong>.<br />";
	}
	if(!$title){
		$error.="&bull; Title field should be <strong>filled in</strong>.<br />";
	}
	// if(!$price){
		// $error.="&bull; Product Price field should be <strong>filled in</strong>.<br />";
	// }
	if($trial&&!$tprice){
		$error.="&bull; Trial Price should be <strong>filled in</strong>.<br />";
	}
	if(!($ecg_flat&&$ecg_3d&&$ecg_bg&&$ecg_bgs&&$ecg_icon&&$ecg_icons)){
		$error.="&bull; Graphics Generator Settings should be <strong>filled in</strong>.<br />";
	}

	if(!$error){
		$ar=";".implode(";",$ar_arr).";";
		$ecg=serialize($_ecg);
		$lux=";".implode(";",$lux_arr).";";

		if(!$id){
			$order=$DB->getmaxval("pack_order","pack")+1;

			$DB->query("insert into $dbprefix"."pack set pack_jvz='$jvz',pack_cb='$cb',pack_pp='$pp',pack_title='$title',pack_display_title='$display_title',pack_buy='$buy',pack_price='$price',pack_freq='$freq',pack_trial='$trial',pack_tprice='$tprice',pack_ar='$ar',pack_ecg='$ecg',pack_lux='$lux',pack_order='$order'");
		}
		else{
			$DB->query("update $dbprefix"."pack set pack_jvz='$jvz',pack_cb='$cb',pack_pp='$pp',pack_title='$title',pack_display_title='$display_title',pack_buy='$buy',pack_price='$price',pack_freq='$freq',pack_trial='$trial',pack_tprice='$tprice',pack_ar='$ar',pack_ecg='$ecg',pack_lux='$lux' where pack_id='$id'");
		}

		redirect("index.php?cmd=pack");
	}
}
?>
<h2><?php echo $index_title.($id?"<a href=\"index.php?cmd=packedit\" class=\"add\">Add New</a>":"");?></h2>
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
		<th><label for="jvz">JVZoo Product ID</label><img src="../img/help.png" title="Should correspond to the JVZoo Product ID.<br />Leave blank if the Membership is NOT sold through JVZoo." class="help" /></th>
		<td><input type="text" name="jvz" id="jvz" value="<?php echo $_POST["submit"]?slash($jvz):$jvz;?>" class="text_s" maxlength="25" /></td>
	</tr>
	<tr>
		<th><label for="cb">ClickBank Item Number</label><img src="../img/help.png" title="Should correspond to the Item Number of the ClickBank Recurring Billing Product.<br />Leave blank if the Membership is NOT sold through ClickBank." class="help" /></th>
		<td><input type="text" name="cb" id="cb" value="<?php echo $_POST["submit"]?slash($cb):$cb;?>" class="text_s" maxlength="25" /></td>
	</tr>
	<tr>
		<th><label for="pp">W+ Product Code</label><img src="../img/help.png" title="Should correspond to the W+ Product Code.<br />Leave blank if the Membership is NOT sold through W+." class="help" /></th>
		<td><input type="text" name="pp" id="pp" value="<?php echo $_POST["submit"]?slash($pp):$pp;?>" class="text_s" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="title">Title</label></th>
		<td><input type="text" name="title" id="title" value="<?php echo $_POST["submit"]?slash($title):$title;?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="title">Display Title</label></th>
		<td><input type="text" name="display_title" id="display_title" value="<?php echo $_POST["submit"]?slash($display_title):$display_title;?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="buy">Upgrade Membership URL</label><img src="../img/help.png" title="Leave blank if the Membership can NOT be upgraded." class="help" /></th>
		<td><input type="text" name="buy" id="buy" value="<?php echo $_POST["submit"]?slash($buy):$buy;?>" class="text_l" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="price">Product Price, USD</label><img src="../img/help.png" title="Product Regular Price or Min Price for Dime Sale." class="help" /></th>
		<td><input type="text" name="price" id="price" value="<?php echo $price?number_format($price,2,".",""):"";?>" class="text_s" /></td>
	</tr>
	<tr>
		<th><label for="freq">Frequency</label><img src="../img/help.png" title="Please NOTE that ClickBank doesn't offer Yearly Frequency." class="help" /></th>
		<td>
			<select name="freq" id="freq" class="sel">
<?php
foreach($FREQ_ARR as $k=>$v){
?>
				<option value="<?php echo $k;?>"<?php echo ($k==$freq)?" selected":"";?>><?php echo $v;?></option>
<?php
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<th><label for="trial">Trial Period</label></th>
		<td>
			<select name="trial" id="trial" class="sel">
				<option value="0">None</option>
<?php
for($i=3;$i<=31;$i++){
?>
				<option value="<?php echo $i;?>"<?php echo ($i==$trial)?" selected":"";?>><?php echo $i." Days";?></option>
<?php
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<th><label for="tprice">Trial Price, USD</label><img src="../img/help.png" title="Leave blank if Trial Period is None." class="help" /></th>
		<td><input type="text" name="tprice" id="tprice" value="<?php echo $tprice?number_format($tprice,2,".",""):"";?>" class="text_s" /></td>
	</tr>
	<tr>
		<th>Access Rights<img src="../img/help.png" title="Choose the Features this Membership has access to." class="help" /></th>
		<td class="vm">
<?php
foreach($AR_ARR as $k=>$v){
?>
				<input id="<?php echo $k;?>" type="checkbox" name="ar[]" value="<?php echo $k;?>"<?php echo in_array($k,$ar_arr)?" checked":"";?> /> <label for="<?php echo $k;?>"><?php echo $v;?></label><br />
<?php
}
?>
		</td>
	</tr>
	<tr>
		<th>Graphics Generator Settings</th>
		<td><input type="text" name="ecg_mon" value="<?php echo $ecg_mon;?>" class="text_s1" /><img src="../img/help.png" title="Allowed amount of Designs per Month. Enter 0 for unlimited." class="help" /> &nbsp; <input type="text" name="ecg_flat" value="<?php echo $ecg_flat?$ecg_flat:"";?>" class="text_s1" /><img src="../img/help.png" title="Allowed amount of Flat images in My Graphicss folder." class="help" /> &nbsp; <input type="text" name="ecg_3d" value="<?php echo $ecg_3d?$ecg_3d:"";?>" class="text_s1" /><img src="../img/help.png" title="Allowed amount of 3D images in My Graphicss folder." class="help" /> &nbsp; <input type="text" name="ecg_bg" value="<?php echo $ecg_bg?$ecg_bg:"";?>" class="text_s1" /><img src="../img/help.png" title="Allowed amount of images in My Templates folder." class="help" /> &nbsp; <input type="text" name="ecg_bgs" value="<?php echo $ecg_bgs?$ecg_bgs:"";?>" class="text_s1" /><img src="../img/help.png" title="Max file size (in MB) of every image in My Templates folder." class="help" /> &nbsp; <input type="text" name="ecg_icon" value="<?php echo $ecg_icon?$ecg_icon:"";?>" class="text_s1" /><img src="../img/help.png" title="Allowed amount of images in My Images folder." class="help" /> &nbsp; <input type="text" name="ecg_icons" value="<?php echo $ecg_icons?$ecg_icons:"";?>" class="text_s1" /><img src="../img/help.png" title="Max file size (in MB) of every image in My Images folder." class="help" /></td>
	</tr>
	<tr>
		<th>Deluxe Resources<img src="../img/help.png" title="Choose Resources this Membership has access to." class="help" /></th>
		<td class="vm">
<?php
foreach($LUX_ARR as $k=>$v){
?>
				<input id="<?php echo $k;?>" type="checkbox" name="lux[]" value="<?php echo $k;?>"<?php echo in_array($k,$lux_arr)?" checked":"";?> /> <label for="<?php echo $k;?>"><?php echo $v;?></label><br />
<?php
}
?>
		</td>
	</tr>
</table>
<div class="submit"><input type="submit" name="submit" value="Save" class="button" /></div>
</form>