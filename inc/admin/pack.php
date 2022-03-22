<?php
if($_POST["submit"]){
	$free=(int)$_POST["free"];
	$DB->query("update $dbprefix"."setup set setup_val='$free' where setup_key='free'");

	$sign=strip($_POST["sign"]);
	$DB->query("update $dbprefix"."setup set setup_val='$sign' where setup_key='sign'");
	redirect("index.php?cmd=pack");
}
elseif($_GET["move"]){
	$DB->move("pack",$_GET["move"]);
	redirect("index.php?cmd=pack");
}
elseif($_GET["copy"]){
	if($row=$DB->info("pack","pack_id='".$_GET["copy"]."'")){
		$jvz=$row["pack_jvz"]?($row["pack_jvz"]."_copy"):"";
		$cb=$row["pack_cb"]?($row["pack_cb"]."_copy"):"";
		$pp=$row["pack_pp"]?($row["pack_pp"]."_copy"):"";
		$title=$row["pack_title"]." Copy";

		$arr=array("buy","price","freq","trial","tprice","ar","ecg","lux","order");
		foreach($arr as $val){
			${$val}=$row["pack_".$val];
		}

		$DB->query("insert into $dbprefix"."pack set pack_jvz='$jvz',pack_cb='$cb',pack_pp='$pp',pack_title='$title',pack_buy='$buy',pack_price='$price',pack_freq='$freq',pack_trial='$trial',pack_tprice='$tprice',pack_ar='$ar',pack_ecg='$ecg',pack_lux='$lux',pack_order='$order'");
	}

	redirect("index.php?cmd=pack");
}
elseif($_GET["del"]){
	pack_del($_GET["del"]);
	redirect("index.php?cmd=pack");
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=packedit" class="add">Add New</a></h2>
<p class="desc"><label for="jvzipn">JVZoo IPN URL:</label> <input type="text" id="jvzipn" value="<?php echo $SCRIPTURL."/".$ADMINDIR."/jvzipn.php"?>" class="text_l desc" onclick="this.select();" readonly /></p>
<p class="desc"><label for="cbipn">ClickBank IPN URL:</label> <input type="text" id="cbipn" value="<?php echo $SCRIPTURL."/".$ADMINDIR."/cbipn.php"?>" class="text_l desc" onclick="this.select();" readonly /></p>
<p class="desc"><label for="wpipn">W+ IPN URL:</label> <input type="text" id="wpipn" value="<?php echo $SCRIPTURL."/".$ADMINDIR."/wpipn.php"?>" class="text_l desc" onclick="this.select();" readonly /></p>
<p class="desc"><label for="wpkg">W+ Keygen URL:</label> <input type="text" id="wpkg" value="<?php echo $SCRIPTURL."/".$ADMINDIR."/wpkg.php"?>" class="text_l desc" onclick="this.select();" readonly /></p>
<p class="desc"><label for="prourl">Pro Member Sign Up Page:</label> <input type="text" id="prourl" value="<?php echo $SCRIPTURL."/user/index.php?cmd=pro"?>" class="text_l desc" onclick="this.select();" readonly /> <a href="<?php echo $SCRIPTURL."/user/index.php?cmd=pro"?>" target="_blank" title="Visit Page" class="tip"><img src="../img/visit.png" /></a></p>
<p class="desc"><label for="freeurl">Free Member Sign Up Page:</label> <input type="text" id="freeurl" value="<?php echo $SCRIPTURL."/user/index.php?cmd=free"?>" class="text_l desc" onclick="this.select();" readonly /> <a href="<?php echo $SCRIPTURL."/user/index.php?cmd=free"?>" target="_blank" title="Visit Page" class="tip"><img src="../img/visit.png" /></a></p>
<br />
<form method="post">
<div>
<span class="large i"><label for="free">Free Membership Duration, Days</label></span><img src="../img/help.png" title="Enter 0 for permanent." class="help" style="vertical-align:middle;" /> <input type="text" id="free" name="free" value="<?php echo $WEBSITE["free"];?>" class="text_s" />
<br />
<span class="large i"><label for="sign">New Member Sign Up URL</label></span><img src="../img/help.png" title="Leave blank NOT to display Sign Up Link on the Member Login Page." class="help" style="vertical-align:middle;" /> <input type="text" id="sign" name="sign" value="<?php echo $WEBSITE["sign"];?>" class="text_l" />
<input type="submit" name="submit" value="Save Changes" class="button" />
</div>
</form>
<br />
<table class="tbl_list">
	<tr>
		<th>Title</th>
		<th class="w100">Regular Price</th>
		<th class="w100">Frequency</th>
		<th class="w100">Trial Period</th>
		<th class="w100">Trial Price</th>
		<th class="w50 ac">Members</th>
		<th class="w50 ac">Order</th>
		<th class="w75 ac">Action</th>
	</tr>
<?php
$res=$DB->query("select p.*,(select count(user_id) from $dbprefix"."user u where u.pack_id=p.pack_id) as num from $dbprefix"."pack p order by p.pack_order");
$num=sizeof($res);
if($num){
	$i=1;
	foreach($res as $row){
		$id=$row["pack_id"];
?>
	<tr>
		<td><?php echo $row["pack_title"];?></td>
		<td><?php echo "$".number_format($row["pack_price"],2,".","");?></td>
		<td><?php echo $FREQ_ARR[$row["pack_freq"]];?></td>
		<td><?php echo $row["pack_trial"]?($row["pack_trial"]." Days"):"N/A";?></td>
		<td><?php echo $row["pack_trial"]?("$".number_format($row["pack_tprice"],2,".","")):"N/A";?></td>
		<td class="ac"><a href="index.php?cmd=user&pack=<?php echo $id;?>" title="View Members" class="tip"><?php echo $row["num"];?></a></td>
		<td><?php if($i>1){?><a href="index.php?cmd=pack&move=u<?php echo $id;?>" title="Move Up"><img src="../img/oup.gif" /></a><?php }?><?php if($i<$num){?><a href="index.php?cmd=pack&move=d<?php echo $id;?>" title="Move Down"><img src="../img/odown.gif" align="right" /></a><?php }?></td>
		<td><a href="index.php?cmd=pack&copy=<?php echo $id;?>" title="Copy" class="tip" onclick="return confirm('Are you sure you wish to copy this Membership?');"><img src="../img/copy.png" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php?cmd=packedit&id=<?php echo $id;?>" title="Edit" class="tip"><img src="../img/edit.png" /></a>&nbsp;&nbsp;<?php if(($id!=1)&&(!$row["num"])){?><a href="index.php?cmd=pack&del=<?php echo $id;?>" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete this Membership?');"><img src="../img/del.png" align="right" /></a><?php }?></td>
	</tr>
<?php
		$i++;
	}
}
?>
</table>