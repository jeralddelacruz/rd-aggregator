<?php
$res1=$DB->query("SELECT SUM(`ipn_ctransamount`) as sale FROM `$dbprefix"."ipn` WHERE `ipn_ctransaction`='SALE'");
$sale=number_format($res1[0]["sale"],2,".",",");

$res2=$DB->query("SELECT SUM(`ipn_ctransamount`) as rfnd FROM `$dbprefix"."ipn` WHERE `ipn_ctransaction`='RFND'");
$rfnd=number_format($res2[0]["rfnd"],2,".",",");

$nett=number_format($res1[0]["sale"]-$res2[0]["rfnd"],2,".",",");
?>
<h2><?php echo $index_title;?></h2>
<?php
echo "<strong>SALE:</strong> $sale<br /><strong>RFND:</strong> $rfnd<br /><strong>NETT:</strong> $nett";
?>