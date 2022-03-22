<?php
set_time_limit(0);
error_reporting(0);
require_once("class.gradient.php");

$im=new gd_gradient($_GET["w"],$_GET["h"],$_GET["d"],$_GET["s"],$_GET["e"],0);
$im->display();
?>