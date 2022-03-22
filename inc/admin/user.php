<?php
$in_arr=array("fname"=>"First Name","lname"=>"Last Name","email"=>"E-mail Address");
$pack_arr=array("-1"=>"[Membership]")+$DB->get_pack();
$act_arr=array("-1"=>"[Status]")+$USER_ACT_ARR+array("2"=>"Expired/Refunded");
$sort_arr=array("fname"=>"First Name","lname"=>"Last Name","email"=>"E-mail Address","rd"=>"Sign Up Date");
$order_arr=array("asc"=>"Asc","desc"=>"Desc");
$pp_arr=array("10"=>"10","25"=>"25","50"=>"50","100"=>"100");

$for=strip($_GET["for"]);

$arr=array("in"=>"Search In","pack"=>"Membership","act"=>"Status","sort"=>"Sort By","order"=>"Order","pp"=>"Show");
foreach($arr as $key=>$val){
	if(isset($_GET[$key])){
		${$key}=$_GET[$key];
	}
}
if(!isset($pack)){$pack=-1;}
if(!isset($act)){$act=-1;}

if(!in_array($in,array_keys($in_arr))){$in="email";}
if(!in_array($pack,array_keys($pack_arr))){$pack=-1;}
if(!in_array($act,array_keys($act_arr))){$act=-1;}
if(!in_array($sort,array_keys($sort_arr))){$sort="rd";}
if(!in_array($order,array_keys($order_arr))){$order="desc";}
if(!in_array($pp,array_keys($pp_arr))){$pp=25;}

$add="&for=$for&in=$in&pack=$pack&act=$act&sort=$sort&order=$order&pp=$pp";
$p_add=$_GET["p"]?("&p=".$_GET["p"]):"";

if($_GET["del"]){
	user_del($_GET["del"]);
	redirect("index.php?cmd=user$add$p_add");
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=useredit" class="add">Add New</a></h2>
<div class="export"><a href="index.php?cmd=user<?php echo $add.$p_add;?>&exp=1" class="add">Export to CSV</a><img src="../img/help.png" title="All Members matching the FILTER criteria below (except Show) are included in the CSV file." class="help" /></div>
<form method="get">
<input type="hidden" name="cmd" value="user" />
<table class="tbl_srch">
	<tr>
		<th>FILTER</th>
		<td><span class="b">Search For</span><br /><input type="text" name="for" value="<?php echo slash($for);?>" class="text_s" /></td>
<?php
foreach($arr as $key=>$val){
?>
		<td>
			<span class="b"><?php echo $val;?></span><br />
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
		<td><input type="submit" value="APPLY" class="btn_srch" /></td>
	</tr>
</table>
</form>
<?php
$query="select u.*, (select log_rd from $dbprefix"."log l where l.user_id=u.user_id order by l.log_rd desc limit 0,1) as log_rd from $dbprefix"."user u where u.user_$in like '$for%'".(($pack>=0)?" and (u.pack_id='$pack')":"").(($act>=0)?(($act==2)?" and u.user_expire<>'0' and u.user_expire<'".time()."'":" and u.user_act='$act'"):"")." order by u.user_$sort $order";

if($_GET["exp"]){
	ob_clean();
	header("Content-Disposition:attachment;filename=members_".date("Y-m-d").".csv");
	header("Content-Type:application/force-download");
	header("Expires:0");
	header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
	header("Pragma:public");
	echo "First Name,Last Name,E-mail Address\r\n";
	$res=$DB->query($query);
	if(sizeof($res)){
		foreach($res as $row){
			echo $row["user_fname"].",".$row["user_lname"].",".$row["user_email"]."\r\n";
		}
	}
	exit;
}

$PL=new PageList($query,$DB,$_GET["p"],$pp,"index.php?cmd=user$add");
?>
<div class="pl">Displaying <strong><?php echo $PL->display();?></strong> of <strong><?php echo number_format($PL->total(),0,".",",");?></strong> Members</div>
<table class="tbl_list">
	<tr>
		<th class="w75 ac">Member ID</th>
		<th>Contact Name</th>
		<th>E-mail Address</th>
		<th>Membership</th>
		<th>Registration Type</th>
		<th class="w75 ac">Status</th>
		<th class=" ac">Sign Up</th>
		<th class="w75 ac">Expiry</th>
		<th class=" ac">Last Login</th>
		<th class="w50 ac">Action</th>
	</tr>
<?php
$res=$DB->query($query);
if(sizeof($res)){
	foreach($res as $row){
		$id=$row["user_id"];
		$pack=$pack_arr[$row["pack_id"]];
		$sus=(!($row["user_act"]&&(($row["user_expire"]==0)||($row["user_expire"]>time()))))?1:0;
        $ipn_request = (strpos($pack, 'JVZ') !== false) ? 'JVZoo' : 'Added Manually';
        $ipn_request = (strpos($pack, 'W+') !== false) ? 'W+' : $ipn_request; 
        $ipn_request = (strpos($pack, 'CB') !== false) ? 'Click Bank' : $ipn_request; 
?>
	<tr<?php if($sus){echo " class=\"sus\"";}?>>
		<td class="ac"><?php echo $id; ?></td>
		<td><?php echo $row["user_fname"]." ".$row["user_lname"];?><?php if($sus){echo "<img src=\"../img/sus.png\" title=\"Expired and/or Suspended\" class=\"help\" />";}?></td>
		<td><a href="mailto:<?php echo $row["user_email"];?>"><?php echo $row["user_email"];?></a></td>
		<td><a href="index.php?cmd=ipn&kw=<?php echo $row["user_email"];?>" title="View Payments" class="tip"><?php echo $pack;?></a></td>
		<td><?php echo $ipn_request; ?></td>
		<td class="ac"><?php echo $USER_ACT_ARR[$row["user_act"]];?></td>
		<td class="ac"><?php echo date("Y-m-d H:i:s",$row["user_rd"]);?></td>
		<td class="ac"><?php echo $row["user_expire"]?date("Y-m-d",$row["user_expire"]):"Unlimited";?></td>
		<td class="ac"><?php echo $row["log_rd"]?date("Y-m-d H:i:s",$row["log_rd"]):"N/A";?></td>
		<td class="ac"><a href="index.php?cmd=useredit&id=<?php echo $id;?>" title="Edit" class="tip"><img src="../img/edit.png" /></a> <a href="index.php?cmd=user<?php echo "$add$p_add&del=$id";?>" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete this Member?');"><img src="../img/del.png" /></a></td>
	</tr>
<?php
	}
}
?>
</table>
<div class="pl"><?php echo $PL->pages();?></div>