<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
	<title><?php echo $index_title." &lsaquo; ".$WEBSITE["sitename"]." &ndash; ";?>Member CP</title>
<?php
if($WEBSITE["icon"]){
?>
	<link rel="shortcut icon" href="../img/<?php echo $WEBSITE["icon"];?>" />
<?php
}
?>
	<link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../assets/css/animate.min.css" />
	<link rel="stylesheet" href="../assets/css/style.css" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700,300" />
	<link rel="stylesheet" href="../assets/css/pe-icon-7-stroke.css" />
	<style>
	.sidebar-wrapper {
		margin: 7% auto 0;
		min-height: auto !important;
		float: none;
		width:40vw !important;
	}
	.card {
		box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05), 0 0 20px 0px rgba(63, 63, 68, 0.5);
		padding-bottom: 50px;
	}
	.logo {
		margin-bottom: 10%;
	}
	.logo img {
		max-width: 400px;
	}
	</style>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="../assets/js/bootstrap.min.js"></script>
	<script src="../assets/js/light-bootstrap-dashboard.js"></script>
</head>
<body id="login">
<div class="wrapper">
	<div class="sidebar" style="width:100%" data-color="<?php echo $WEBSITE["theme"];?>" data-image="<?php echo $WEBSITE["bg_login"]?("../img/".$WEBSITE["bg_login"]):"";?>">
		<div class="sidebar-wrapper">
<?php
if($WEBSITE["logo"]){
?>
			<div class="logo">
				<img src="../img/<?php echo $WEBSITE["logo"];?>" class="img-responsive" style="margin:0 auto;float:none;" />
			</div>
<?php
}
?>
