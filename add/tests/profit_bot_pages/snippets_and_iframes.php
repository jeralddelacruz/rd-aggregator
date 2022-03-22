<?php
	// MANUAL
	// $servername = "localhost";
	// $username = "pprofitf_db";
	// $password = "4iH+n2P(wxTW";
	// $dbname = "pprofitf_db";
	
	// // Create connection
	// $conn = new mysqli($servername, $username, $password, $dbname);
	// // Check connection
	// if ($conn->connect_error) {
	//   die("Connection failed: " . $conn->connect_error);
	// }
	
	// $s = $_GET["s"];
	
	// $sql = "SELECT * FROM pp_pageb WHERE pageb_n_random_string_squeeze_page='". $s ."'";
	// $result = $conn->query($sql);
	
	// $row = $result->fetch_assoc();
	
	set_time_limit(0);
	error_reporting(0);
	session_start();
	
	// IMPORTANT DIRECTORY
	$dir = "../../../sys";
	$fp = opendir($dir);
	while(($file = readdir($fp)) != false){
		$file = trim($file);
		if(($file == ".") || ($file == "..")){continue;}
		$file_parts = pathinfo($dir."/".$file);
		if($file_parts["extension"] == "php"){
			include($dir . "/" . $file);
		}
	}
	closedir($fp);
	
	$DB = new db($dbhost, $dbuser, $dbpass, $dbname);
	$DB->connect();
	if($DB->connect<1){
		echo "Can't go on, DB not initialized.";
		exit;
	}
	
	// WEBSITE VARIABLE
	$res = $DB->query("select setup_key,setup_val from $dbprefix"."setup order by setup_id");
	foreach($res as $row){
		$WEBSITE[$row["setup_key"]]=$row["setup_val"];
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?= $row["pageb_n_squeeze_page_headline"] ? $row["pageb_n_squeeze_page_headline"] : "Squeeze Page"; ?></title>
	
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		
		<!-- Primary Meta Tags -->
		<meta name="title" content="<?= $WEBSITE["sitename"]; ?> Squeeze Page">
		<meta name="description" content="<?= $WEBSITE["sitename"]; ?> Description">
		
		<!-- Open Graph / Facebook -->
		<meta property="og:type" content="website">
		<meta property="og:url" content="<?= $WEBSITE["sitename"]; ?> Squeeze Page">
		<meta property="og:title" content="<?= $WEBSITE["sitename"]; ?> Squeeze Page">
		<meta property="og:description" content="<?= $WEBSITE["sitename"]; ?> Description">
		<meta property="og:image" content="<?= $SCRIPTURL . "add/". $currentBG[0]['pageb_n_bg_url_1']; ?>">
		
		<!-- Twitter -->
		<meta property="twitter:card" content="summary_large_image">
		<meta property="twitter:url" content="<?= $SCRIPTURL . "add/squeeze.php?s=" . $s; ?>">
		<meta property="twitter:title" content="<?= $WEBSITE["sitename"]; ?> Squeeze Page">
		<meta property="twitter:description" content="<?= $WEBSITE["sitename"]; ?> Description">
		<meta property="twitter:image" content="<?= $SCRIPTURL . "add/". $currentBG[0]['pageb_n_bg_url_1']; ?>">
		
		<!-- Bootstrap CDNs -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	
		<!-- Other CDNs -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/v4-shims.css">
		
		<!-- Local CDNs -->
		<link rel="stylesheet" type="text/css" href="style/style.css" />
		<script type="text/javascript" src="script/script.js"></script>
		
		<!-- Google Fonts CDN -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400" />
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open Sans:400" />
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins: 400" />
		
		<!-- Embeded Style -->
		<style>
			:root{
				--theme-color: <?php echo $row["pageb_n_theme_color"]; ?>;
				<?php if($currentBG[0]['pageb_n_bg_url_1'] == null){ ?>
				--theme-bg: url("<?php echo $SCRIPTURL . "upload/" . $row["user_id"] . "/". $currentBG2[0]['pageb_n_bg_url_2']; ?>");
				<?php }else{ ?>
				--theme-bg: url("<?php echo $SCRIPTURL . "add/". $currentBG[0]['pageb_n_bg_url_1']; ?>");
				<?php } ?>
				--theme-font-roboto: "Roboto";
			}
			
			html, body{
				margin: 0px; padding: 0px;

				width: 100%; height: 100%;
				
				box-sizing: border-box;
				
				background-image: var(--theme-bg);
				background-size: cover;
				background-position: center center;
				background-repeat: no-repeat;
				background-attachment: fixed;
			}
			
			.default{
				margin: 0px; padding: 0px;
				
				position: relative;
				
				width: auto; height: auto;
				
				display: block;
				
				box-sizing: border-box;
				
				border: 1px solid #333333;
			}

			.cs-main-wrapper{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: 100%; height: 100%;
				
				display: block;
				
				box-sizing: border-box;
			}

			.float-widget{
				margin: auto;

				position: fixed;
				bottom: 0px;

				width: 80%; height: 100px;

				border: 1px solid #333333;
			}

			.pos iframe{
				border: none;
			}

			/* OVERRIDES */
			.cs-margin-bottom-10px{
				margin-bottom: 10px !important;
			}

			.cs-margin-bottom-20px{
				margin-bottom: 20px !important;
			}

			.cs-margin-bottom-30px{
				margin-bottom: 30px !important;
			}

			.cs-margin-bottom-40px{
				margin-bottom: 40px !important;
			}

			.cs-margin-bottom-50px{
				margin-bottom: 50px !important;
			}

			/* MEDIA QUERIES */
			@media only screen and (max-width: 768px){
				.cs-two-way{
					flex-direction: column;
				}

				.cs-two-way-image-wrapper{
					width: 100%;
					min-width: 345px;
				}

				.cs-two-way-content-wrapper{
					padding: 30px;
					width: 100%;
					min-width: 422px;
				}
			}
		</style>
	</head>
	<body>
		<div class="container-fluid" style="overflow: hidden;">
			<div class="row" id="qwe">
				<div class="col-lg-4 col-lg-offset-4">
					<label>Enter a URL:</label>

					<div class="row">
						<input class="form-control" id="grabbed_url" type="url" name="" />
						<br /><br />
						<button class="btn btn-primary btn-block" id="grab_button" type="button">Grab</button>
					</div>
				</div>
			</div>

			<div class="row" id="pos">
				<iframe id="iframe_preview" src="" width="100%" height="100%"></iframe>

				<div class="float-widget">
					<div class="row">
						<h4>Float</h4>
						<button class="btn btn-default">56kj4yu6</button>
					</div>
				</div>
			</div>
		</div>
	</body>
	<script type="text/javascript">
		window.onload = function(){
			var iframePreview = document.getElementById("iframe_preview");
			var grabButton = document.getElementById("grab_button");
			var grabbedURL = document.getElementById("grabbed_url");
			var qwe = document.getElementById("qwe");
			var pos = document.getElementById("pos");

			grabButton.onclick = function(){
				iframePreview.src = grabbedURL.value;
				qwe.style.display = "none";
				pos.style.position = "absolute";
				pos.style.width = "100%";
				pos.style.height = "100%";
			}
		}
	</script>
</html>