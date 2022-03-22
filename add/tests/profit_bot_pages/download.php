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
	
	$s = $_GET["s"];
	
	$row = $DB->info("pageb", "pageb_n_random_string_squeeze_page='$s'");
?>
<!DOCTYPE html>
<html class="cs-bg-color-gainsboro">
	<head>
		<title><?= $row["pageb_n_squeeze_page_headline"] ? $row["pageb_n_squeeze_page_headline"] : "Download Page"; ?></title>
	
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
				--theme-color: <?php echo $row["pageb_n_theme_color"] ? $row["pageb_n_theme_color"] : "hotpink"; ?>;
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
				margin: auto; padding: 0px;
				
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

			.cs-two-way{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: 100%; height: 100%;
				
				display: flex;
				flex-direction: row;
				
				box-sizing: border-box;
			}

			.cs-two-way-image-wrapper{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: 45%; height: 100%;
				
				display: block;
				
				box-sizing: border-box;
			}

			.cs-two-way-image-wrapper img{
				width: 100%; height: 100%;
				min-width: 100%;
				object-fit: cover;
			}

			.cs-two-way-content-wrapper{
				margin: auto; padding: 150px;
				
				position: relative;
				
				width: 55%; height: auto;
				
				display: block;
				
				box-sizing: border-box;
			}

			.cs-text, .cs-text-title, .cs-text-body, .cs-text-headline, .cs-text-subheadline{
				margin: auto;

				font-family: var(--theme-font-roboto);
				font-size: 15px;
				font-weight: normal;
				line-height: normal;
				letter-spacing: normal;
				text-transform: normal;
				text-align: center;
				color: #333333;
			}

			.cs-text-title{
				font-size: 24px;
				font-weight: 700;
			}

			.cs-text-body{
				font-size: 17px;
				font-weight: normal;
			}

			.cs-text-headline{
				font-size: 42px;
				font-weight: 700;
			}

			.cs-text-subheadline{
				font-size: 24px;
				font-weight: 400;
			}

			.cs-text-font-size-15px{
				font-size: 15px;
			}

			.cs-text-font-size-17px{
				font-size: 17px;
			}

			.cs-text-font-size-20px{
				font-size: 20px;
			}

			.cs-text-font-size-24px{
				font-size: 24px;
			}

			.cs-text-font-size-27px{
				font-size: 27px;
			}

			.cs-form-group{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: auto; height: auto;
				
				display: block;
				
				box-sizing: border-box;
			}

			.cs-form-control{
				margin: auto; padding: 12px;
				
				position: relative;
				
				width: auto; height: 50px;
				
				display: block;
				
				box-sizing: border-box;

				font-family: var(--theme-font-roboto);
				font-size: 17px;
				font-weight: 400;
				line-height: normal;
				letter-spacing: normal;
				text-transform: normal;
				text-align: left;
				color: #333333;

				border: 1px solid grey;
				border-radius: 5px;
			}

			.cs-form-control-50-percent{
				width: 50%;
			}

			.cs-form-control-70-percent{
				width: 70%;
			}

			.cs-form-control-100-percent{
				width: 100%;
			}

			.cs-form-button, .cs-form-button-small, .cs-form-button-close{
				margin: auto; padding: 10px;
				
				position: relative;
				
				width: auto; height: auto;
				
				display: block;
				
				box-sizing: border-box;

				font-family: var(--theme-font-roboto);
				font-size: 21px;
				font-weight: 400;
				line-height: normal;
				letter-spacing: normal;
				text-transform: normal;
				text-align: center;
				color: grey;

				border: 1px solid grey;
				border-radius: 5px;

				cursor: pointer;
			}

			.cs-form-button-small{
				padding: 0px 10px;

				font-family: var(--theme-font-roboto);
				font-size: 17px;
				font-weight: 400;
				line-height: normal;
				letter-spacing: normal;
				text-transform: normal;
				text-align: center;
				color: grey;
			}

			.cs-form-button-close{
				padding: 0px 10px;

				font-family: var(--theme-font-roboto);
				font-size: 21;
				font-weight: 400;
				line-height: normal;
				letter-spacing: normal;
				text-transform: normal;
				text-align: center;
				color: grey;

				border: none;
				background-color: white;
			}

			.cs-form-button-50-percent{
				width: 50%;
			}

			.cs-form-button-70-percent{
				width: 70%;
			}

			.cs-form-button-100-percent{
				width: 100%;
			}

			.cs-form-button-theme-color{
				background-color: var(--theme-color);
				color: white;
				border-color: var(--theme-color);
			}

			.cs-form-button-close-theme-color{
				/*background-color: white;*/
				color: var(--theme-color);
				/*border-color: var(--theme-color);*/
			}

			.cs-form-button-dodgerblue{
				background-color: dodgerblue;
				color: white;
				border-color: dodgerblue;
			}

			.cs-form-button-teal{
				background-color: teal;
				color: white;
				border-color: teal;
			}

			.cs-form-button-cornflowerblue{
				background-color: cornflowerblue;
				color: white;
				border-color: cornflowerblue;
			}

			.cs-header-fixed-with-button{
				margin: auto; padding: 10px;
				
				position: fixed;
				
				width: 100%; height: auto;
				
				display: block;
				
				box-sizing: border-box;
				
				border: none;
				border-top: 5px solid var(--theme-color);

				background-color: white;

				transition: opacity .5s;

				z-index: 99;
			}

			.cs-body-absolute{
				margin: auto; padding: 0px;
				
				position: absolute;
				
				width: 100%; height: 100%;
				
				display: block;
				
				box-sizing: border-box;
			}

			.cs-footer-fixed{
				margin: auto; padding: 20px;
				
				position: fixed;
				bottom: 0px;
				
				width: 100%; height: auto;
				
				display: block;
				
				box-sizing: border-box;

				z-index: 99;
			}

			.cs-row-flex{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: 100%; height: auto;
				
				display: flex;
				flex-direction: row;
				
				box-sizing: border-box;
			}

			.cs-column-flex{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: 100%; height: auto;
				
				display: flex;
				flex-direction: column;
				
				box-sizing: border-box;
			}

			.cs-profile-square{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: auto; height: auto;
				
				display: block;
				
				box-sizing: border-box;

				border: 1px solid transparent;
				border-radius: 5px;

				background-color: gainsboro;
			}

			.cs-profile-square img{
				width: 100%; height: 100%;
			}

			.cs-profile-square-r25px{
				width: 25px; height: 25px;
				min-width: 25px; min-height: 25px;
			}

			.cs-profile-square-r50px{
				width: 50px; height: 50px;
				min-width: 50px; min-height: 50px;
			}

			.cs-profile-square-r75px{
				width: 75px; height: 75px;
				min-width: 75px; min-height: 75px;
			}

			.cs-profile-square-r85px{
				width: 85px; height: 85px;
				min-width: 85px; min-height: 85px;
			}

			.cs-profile-square-r100px{
				width: 100px; height: 100px;
				min-width: 100px; min-height: 100px;
			}

			.cs-profile-square-r150px{
				width: 150px; height: 150px;
				min-width: 150px; min-height: 150px;
			}

			.cs-bubble{
				margin: auto; padding: 20px;
				
				position: relative;
				
				width: auto; height: auto;
				max-width: 720px;
				
				display: block;
				
				box-sizing: border-box;

				background-color: white;

				border: 1px solid white;
				border-radius: 5px;
			}

			.cs-bubble:before{
				position: absolute;
				left: -11px;

				width: 0px; height: 0px;

				border-top: 10px solid transparent;
				border-bottom: 10px solid transparent;

				border-right: 10px solid white;

				content: "";
			}

			.cs-download-container{
				margin: auto; padding: 40px;
				
				position: relative;
				
				width: 50%; height: auto;
				
				display: block;
				
				box-sizing: border-box;
				
				border: 1px solid #333333;
			}

			.cs-download-button{
				margin: auto; padding: 10px;
				
				position: relative;
				
				width: 100%; height: auto;
				
				display: block;
				
				box-sizing: border-box;
				
				border: 3px solid var(--theme-color);

				font-family: var(--theme-font-roboto);
				font-size: 24px;
				font-weight: 700;
				line-height: normal;
				letter-spacing: normal;
				text-transform: normal;
				text-align: center;
				color: var(--theme-color);

				transition: background-color .2s;
			}

			.cs-download-button:hover{
				background-color: var(--theme-color);
				color: white;
			}

			.cs-image-container{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: 100%; height: auto;
				
				display: block;
				
				box-sizing: border-box;

				text-align: center;
			}

			.cs-image-container img{
				margin: auto;
				width: 100%;
				object-fit: cover;
			}

			/* OVERRIDES */
			.cs-margin-right-5px{
				margin-right: 5px !important;
			}

			.cs-margin-right-10px{
				margin-right: 10px !important;
			}

			.cs-margin-right-20px{
				margin-right: 20px !important;
			}

			.cs-margin-right-30px{
				margin-right: 30px !important;
			}

			.cs-margin-left-0px{
				margin-left: 0px !important;
			}

			.cs-margin-left-5px{
				margin-left: 5px !important;
			}

			.cs-margin-left-10px{
				margin-left: 10px !important;
			}

			.cs-margin-left-20px{
				margin-left: 20px !important;
			}

			.cs-margin-left-30px{
				margin-left: 30px !important;
			}

			.cs-margin-bottom-5px{
				margin-bottom: 5px !important;
			}

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

			.cs-margin-top-0px{
				margin-top: 0px !important;
			}

			.cs-margin-top-10px{
				margin-top: 10px !important;
			}

			.cs-margin-top-20px{
				margin-top: 20px !important;
			}

			.cs-margin-top-30px{
				margin-top: 30px !important;
			}

			.cs-margin-top-40px{
				margin-top: 40px !important;
			}

			.cs-margin-top-50px{
				margin-top: 50px !important;
			}

			.cs-font-weight-normal{
				font-weight: normal !important;
			}

			.cs-theme-color{
				color: var(--theme-color) !important;
			}

			.cs-border-dashed{
				border-style: dashed !important;
			}

			.cs-border-color-gainsboro{
				border-color: gainsboro !important;
			}

			.cs-border-color-lightgrey{
				border-color: lightgrey !important;
			}

			.cs-border-color-darkgrey{
				border-color: darkgrey !important;
			}

			.cs-border-color-grey{
				border-color: grey !important;
			}

			.cs-border-radius-1px{
				border-radius: 1px;
			}

			.cs-border-radius-2px{
				border-radius: 2px;
			}

			.cs-border-radius-3px{
				border-radius: 3px;
			}

			.cs-border-radius-4px{
				border-radius: 4px;
			}

			.cs-border-radius-5px{
				border-radius: 5px;
			}

			.cs-border-width-1px{
				border-width: 1px !important;
			}

			.cs-border-width-2px{
				border-width: 2px !important;
			}

			.cs-border-width-3px{
				border-width: 3px !important;
			}

			.cs-border-width-4px{
				border-width: 4px !important;
			}

			.cs-border-width-5px{
				border-width: 5px !important;
			}

			.cs-width-10percent{
				width: 10% !important;
			}

			.cs-width-20percent{
				width: 20% !important;
			}

			.cs-width-30percent{
				width: 30% !important;
			}

			.cs-width-40percent{
				width: 40% !important;
			}

			.cs-width-50percent{
				width: 50% !important;
			}

			.cs-bg-color-gainsboro{
				background-color: gainsboro !important;
			}

			.cs-bg-color-white{
				background-color: white !important;
			}

			a, a:active, a:visited, a:hover{
				text-decoration: none;
				color: var(--theme-color);
			}

			/* IDs */
			#affiliate-iframe{
				margin: auto; padding: 0px;

				position: relative;

				width: 100%; height: 100%;

				display: block;

				box-sizing: border-box;

				border: none;
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

				.cs-body-text-17px{
					margin: auto !important;
				}

				.cs-row-flex{
					flex-direction: column;
				}

				.cs-form-button{
					margin: 5px auto !important;
					width: 100%;
				}

				.cs-form-button-small{
					margin: 5px auto !important;
					width: 100%;
				}

				.cs-profile-square{
					margin: 5px auto !important;
				}

				.cs-bubble{
					margin: 5px auto !important;

					width: 100%;
				}

				.cs-download-container{
					width: 95%;
				}
			}
		</style>
		<?php if($row["pageb_n_redirection_toggle"]){ ?>
		<script>
			document.addEventListener("mouseleave", function(e){
				var mouseLoc = e.pageY - document.body.scrollTop;
				
				if(mouseLoc <= 50){
					window.location.href = "<?php echo $SCRIPTURL . "add/lead-magnet.php?s=" . $row["pageb_n_random_string_leadmagnet_page"]; ?>";
				}
			});
		</script>
		<?php } ?>
	</head>
	<body class="cs-bg-color-gainsboro">
		<div class="cs-main-wrapper">
			<div class="cs-download-container cs-border-dashed cs-border-width-3px cs-border-color-darkgrey cs-border-radius-4px cs-margin-top-50px cs-bg-color-white">
				<div class="cs-text-headline cs-theme-color">THANKS</div>
				<div class="cs-text-headline cs-font-weight-normal cs-margin-bottom-30px">Your Free Download</div>

				<div class="cs-column-flex">
					<div class="cs-image-container cs-width-40percent cs-margin-bottom-30px">
						<img src="sample_image_2.jpg" />
					</div>

					<div class="cs-text-title cs-text-font-size-27px">Product Title</div>
					<div class="cs-text-body cs-text-font-size-20px cs-margin-bottom-30px">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>

					<a class="cs-download-button" href="#"><i class="fa fa-arrow-down"></i> Click Here To Download Now</a>
				</div>
			</div>
		</div>
	</body>
</html>