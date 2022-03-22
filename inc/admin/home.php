<?php
include "../../sys/cpanel_credentials.php";

if($_POST["LoginSubmit"]){
	redirect("index.php?cmd=home");
}

?>
<h2><?php echo $index_title;?></h2>
<?php

require_once('cpanel.php');

$data=array();
for($i=21;$i>0;$i--){
	$from=time()-$i*86400;
	$to=time()-($i-1)*86400;

	$res=$DB->query("select count(log_id) as log from $dbprefix"."log where log_rd>'$from' and log_rd<='$to'");
	$log=(int)$res[0]["log"];

	$res=$DB->query("select count(user_id) as user from $dbprefix"."user where user_rd>'$from' and user_rd<='$to'");
	$user=(int)$res[0]["user"];

	$data[]=array(date("M j",$to),$log,$user);
}
$_SESSION["PHPlot"]["loguser"]["data"]=$data;
$_SESSION["PHPlot"]["loguser"]["title"]="Logins / Signups during last 21 days";
$_SESSION["PHPlot"]["loguser"]["legend"]=array("Logins","Signups");

//bandwidth
$cpanel = new cPanel($cPanel_username, $cPanel_password, $cPanel_host_subdomain);
$usages = json_decode($cpanel->getUsage());
?>
<style>
	.usage{
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		padding: 10px;
	}

	.items{
		padding: 10px;
		border: 1px solid #eee;
    	flex-basis: 30%;
		margin-bottom: 10px;
	}

	.green{
		background-color: #bfefc1;
		border-color: #bfefc1;
	}

	.orange{
		background-color: #ffb850;
		border-color: #ffb850;
	}

	.red{
		background-color: #ffaba5;
		border-color: #ffaba5;
	}
</style>

<div class="usage">
	<?php foreach ($usages->data as $usage) { 
		$used = isset($usage->formatter) ? number_format(floatval($usage->usage / 1073741824), 2, '.', ',') . 'GB': $usage->usage;
		$maximum = isset($usage->formatter) ? number_format(floatval($usage->maximum / 1073741824), 2, '.', ',') . 'GB': $usage->maximum;
		$percent = number_format(floatval(isset($usage->formatter) ? ($usage->usage / $usage->maximum) * 100 : 0), '2', '.', '');
		$status = isset($percent) && $percent >= 40 ? 'orange' : 'green';
		$status = isset($percent) && $percent >= 80 ? 'red' : $status;
	?>
		<div class="items <?php echo $status; ?>">
			<div class="label"><span><b><?php echo $percent >= 50 ? '&#10071;' : '&#9989;' ?></b></span><b><?php echo $usage->description; ?></b> </div>
			<div class="stats">
				<?php echo $used . ' / ' . (!empty($maximum) ? $maximum: '&#8734;'); ?> - <?php echo $percent . '%'; ?>
			</div>
		</div>
	<?php } ?>
</div>
<div class="phplot"><img src="phplot.php?type=loguser" /></div>