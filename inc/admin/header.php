<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title><?php echo $index_title." &lsaquo; ".$WEBSITE["sitename"]." &ndash; ";?>Admin CP</title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
	<script src="../js/spectrum.js"></script>
	<script src="../js/fancybox/jquery.fancybox.js"></script>
	<script src="../js/dropzone.js"></script>
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
	<link rel="stylesheet" href="../css/admin.css?v=<?php echo rand(1, 100); ?>" />
	<link rel="stylesheet" href="../css/style.css?v=<?php echo rand(1, 100); ?>" />
	<link rel="stylesheet" href="../css/spectrum.css" />
	<link rel="stylesheet" href="../js/fancybox/jquery.fancybox.css" />
	<link rel="stylesheet" href="../css/dropzone.css" />

	<!-- BOOTSTRAP CDN FOR FUTURE UI UPGRADE -->
	<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" /> -->
	
	<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<?php
if($WEBSITE["icon"]){
?>
	<link rel="shortcut icon" href="../img/<?php echo $WEBSITE["icon"];?>" />
<?php
}
?>
</head>
<body>
<div class="bg_nav">
	<div class="bg_main"></div>
</div>
<div class="wrap">
	<header>
		<?php if($WEBSITE["logo"]){?><a href="index.php?cmd=home"><img src="../img/<?php echo $WEBSITE["logo"];?>"<?php echo logo_wh("../img/".$WEBSITE["logo"],980,90);?> title="<?php echo $WEBSITE["sitename"];?>" /></a><?php }else{?><h1><a href="index.php?cmd=home"><?php echo $WEBSITE["sitename"];?></a> &ndash; Admin CP</h1><?php }?>
<?php
if($cur_admin){
?>
		<div class="log">Welcome back, <a href="index.php?cmd=info"><strong><?php echo $cur_admin["admin_nick"];?></strong></a> | <a href="./">Log Out</a><br/><strong>Version Number:</strong> <?php echo $VERSION_NUM;?></div>
<?php
}
?>
	</header>
	<div class="content">
		<nav>
<?php
if($cur_admin){
?>
			<ul class="menu">
<?php
foreach($ADMIN_MENU as $key=>$arr){
	$sub_arr=$arr[1];
	$key_arr=array_keys($sub_arr);
	$menu_act=0;
	if(($key==$ADMIN_CMD[$cmd][1])||@in_array($ADMIN_CMD[$cmd][1],$key_arr)){
		$menu_act=1;
	}
?>
				<li<?php if($menu_act){echo " class=\"act\"";} ?>>
					<a href="index.php?cmd=<?php echo $key;?>"><?php echo $arr[0];?></a>
<?php
	if(sizeof($sub_arr)){
?>
					<ul class="sub">
<?php
		$i=0;
		foreach($sub_arr as $sub_key=>$sub_val){
?>
						<li><?php if(!$menu_act&&!$i){echo "<span class=\"arrow\"></span>";}?><a href="index.php?cmd=<?php echo $sub_key;?>"<?php if($menu_act&&($sub_key==$ADMIN_CMD[$cmd][1])){echo " class=\"b\"";}?>><?php echo $sub_val;?></a></li>
<?php
			$i++;
		}
?>
					</ul>
<?php
	}
?>
				</li>
<?php
}
?>
			</ul>
<?php
}
else{
?>
			<br />
<?php
}
?>
		</nav>
		<div class="wrap_main">
			<div class="main">
