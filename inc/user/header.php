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
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
	<link rel="stylesheet" href="../css/jquery-ui-timepicker-addon.min.css" />
	<link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../assets/css/animate.min.css" />
	<link rel="stylesheet" href="../assets/css/style.css" />
	<link rel="stylesheet" href="../css/spectrum.css" />
	<link href="/themes/vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
	<link href="/themes/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700,300" />
	<link rel="stylesheet" href="../assets/css/pe-icon-7-stroke.css" />
	<link rel="stylesheet" href="../js/fancybox/jquery.fancybox.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
	<script src="../js/jquery-ui-timepicker-addon.min.js"></script>
	<script src="../assets/js/bootstrap.min.js"></script>
	<script src="../assets/js/bootstrap-checkbox-radio-switch.js"></script>
	<script src="../assets/js/bootstrap-notify.js"></script>
	<script src="../assets/js/light-bootstrap-dashboard.js"></script>
	<script src="../js/fancybox/jquery.fancybox.js"></script>
	<script src="../js/spectrum.js"></script>
</head>
<body class="searchresults">
<div class="wrapper">
	<div class="sidebar" data-color="<?php echo $WEBSITE["theme"];?>" data-image="<?php echo $WEBSITE["bg_menu"]?("../img/".$WEBSITE["bg_menu"]):"";?>">
	<div class="sidebar-wrapper">
<?php
if($WEBSITE["logo"]){
?>
		<div class="logo">
			<a href="index.php?cmd=home" class="simple-text"><img src="../img/<?php echo $WEBSITE["logo"];?>" class="img-responsive"/></a>
		</div>
<?php
}
?>
		<ul class="nav">
<?php
	foreach($USER_MENU_ARR as $key=>$arr){
		if(!(in_array($key,array_keys($USER_MENU))||in_array($key,$TAG_ARR))){continue;}
?>
			<li<?php echo ($USER_CMD[$cmd][1]==$key)?" class=\"active\"":"";?>>
				<a href="index.php?cmd=<?php echo $key;?>"><i class="<?php echo $arr[1];?>"></i><p><?php echo $arr[0];?></p></a>
			</li>
<?php
	}
?>
		</ul>
	</div>
	</div>
	<div class="main-panel">
		<nav class="navbar navbar-default navbar-fixed">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<span class="navbar-brand"><?php echo $index_title;?></span>
			</div>
			<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="index.php?cmd=info">Welcome back, <strong><?php echo $cur_user["user_fname"];?></strong></a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-globe"></i>
						<b class="caret"></b>
						<span id="mes_num" class="notification hide"></span>
					</a>
					<ul id="mes_body" class="dropdown-menu notifications-dropdown"></ul>
				</li>
				<li><a href="./">Log out</a></li>
			</ul>
			</div>
		</div>
		</nav>
		<div class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 text-center"><?php echo get_ad(1);?></div>
			</div>
