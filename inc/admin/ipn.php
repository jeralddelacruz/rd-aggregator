<?php
$sys_arr=array("-1"=>"[sys]","1"=>"JVZ","2"=>"CB","3"=>"W+");
$ctransaction_arr=array("-1"=>"[ctransaction]","1"=>"SALE","2"=>"BILL","3"=>"RFND","4"=>"sale","5"=>"subscr_created","6"=>"subscr_completed","7"=>"refund","8"=>"subscr_refunded");
$pp_arr=array("25"=>"25","50"=>"50","100"=>"100","250"=>"250","500"=>"500");

$kw=strip($_GET["kw"]);

$f_arr=array("sys","ctransaction","pp");
foreach($f_arr as $key){
	if(isset($_GET[$key])){
		${$key}=$_GET[$key];
	}
}

if(!in_array($sys,array_keys($sys_arr))){$sys=-1;}
if(!in_array($ctransaction,array_keys($ctransaction_arr))){$ctransaction=-1;}
if(!in_array($pp,array_keys($pp_arr))){$pp=50;}

$add="&kw=$kw&sys=$sys&ctransaction=$ctransaction&pp=$pp";

$arr=array("sys","ctransreceipt","ccustemail","ccustname","ctransvendor","cproditem","cprodtype","ctransaction","ctransamount");
?>
<h2><?php echo $index_title;?></h2>
<form method="get" action="index.php">
<input type="hidden" name="cmd" value="ipn" />
<table class="tbl_srch">
	<tr>
		<th style="width:16%;">FILTER</th>
		<td style="width:17%;text-align:center;vertical-align:middle;"><input type="text" name="kw" value="<?php echo slash($kw);?>" placeholder="ccustemail" class="text_m" /></td>
<?php
foreach($f_arr as $key){
?>
		<td style="width:17%;text-align:center;vertical-align:middle;">
			<select name="<?php echo $key;?>" class="sel">
<?php
	foreach(${$key."_arr"} as $k=>$v){
?>
				<option value="<?php echo $k;?>"<?php echo ($k==${$key})?" selected":"";?>><?php echo $v;?></option>
<?php
	}
?>
			</select>
		</td>
<?php
}
?>
		<td style="width:16%;"><input type="submit" value="APPLY" class="btn_srch" /></td>
	</tr>
</table>
</form>
<?php
$query="select * from $dbprefix"."ipn where ipn_ccustemail like '$kw%'".(($sys>0)?(" and ipn_sys='".$sys_arr[$sys]."'"):"").(($ctransaction>0)?(" and ipn_ctransaction='".$ctransaction_arr[$ctransaction]."'"):"")." order by ipn_rd desc";
$PL=new PageList($query,$DB,$_GET["p"],$pp,"index.php?cmd=ipn$add");
?>
<div class="pl">Displaying <strong><?php echo $PL->display();?></strong> of <strong><?php echo number_format($PL->total(),0,".",",");?></strong> IPNs</div>
<table class="tbl_list">
	<tr>
		<th>Date</th>
<?php
foreach($arr as $val){
?>
		<th><?php echo $val;?></th>
<?php
}
?>
		<th>ctranstime</th>
	</tr>
<?php
$res=$DB->query($query);
$num=sizeof($res);
if($num){
	foreach($res as $row){
?>
	<tr>
		<td><?php echo date("Y-m-d H:i:s",$row["ipn_rd"]);?></td>
<?php
foreach($arr as $val){
?>
		<td><?php echo ($val=="ccustemail")?("<a href=\"index.php?cmd=user&for=".urlencode($row["ipn_".$val])."&in=email&pack=-1&act=-1&sort=rd&order=desc&pp=25\">".$row["ipn_".$val]."</a>"):$row["ipn_".$val];?></td>
<?php
}
?>
		<td><?php echo date("Y-m-d H:i:s",$row["ipn_ctranstime"]);?></td>
	</tr>
<?php
	}
}
?>
</table>
<div class="pl"><?php echo $PL->pages();?></div>