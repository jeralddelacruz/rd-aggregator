<?php
	set_time_limit(0);
	error_reporting(0);
	session_start();
	
	// IMPORTANT DIRECTORY
	$dir = "../sys";
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
	
	$passed_random_string = $_GET["s"];
	$campaign = $DB->info("pageb", "pageb_campaign_unique_identifier_affiliate = '{$passed_random_string}'");

	$timer_value = explode(", ", $campaign["pageb_campaign_speech_bubble_timer"]);
	$timer_second_value_hours = $timer_value[0] * 3600;
	$timer_second_value_minute = $timer_value[1] * 60;
	$timer_second_value_second = $timer_value[2];
	$timer_total_value_in_seconds = ($timer_second_value_hours + $timer_second_value_minute + $timer_second_value_second);

	include "tests/cURL_scrape_full_page.php";

	// $curl = new cURL_full_page();
	// $html = $curl->get("{$campaign["pageb_campaign_affiliate_link_final"]}");
	
	// echo "$html";

	// header('X-Frame-Options: ALLOW-FROM https://warriorplus.com/o2/a/kb535/0');
	// SET PAGE TO ALLOW HEADERS OR NOT
	// if (isset($_SERVER['HTTP_ORIGIN'])) {
	// 	// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
	// 	// you want to allow, and if so:
	// 	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	// 	header('Access-Control-Allow-Credentials: true');
	// 	header('Access-Control-Max-Age: 86400');    // cache for 1 day
	// }
	
	// // Access-Control headers are received during OPTIONS requests
	// if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		
	// 	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
	// 		// may also be using PUT, PATCH, HEAD etc
	// 		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
		
	// 	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
	// 		header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
	
	// 	exit(0);
	// }
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?= $campaign["pageb_campaign_top_bar_text"]; ?></title>
	
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
		
	
		<!-- Other CDNs -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/v4-shims.css">
		
		<!-- Local CDNs -->
		<link rel="stylesheet" type="text/css" href="style/style.css" />
		<script type="text/javascript" src="script/script.js"></script>
		
		<!-- Google Fonts CDN -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,900" />
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open Sans:400,900" />
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:400,900" />
		
		<!-- Embeded Style -->
		<style>
			:root{
				--theme-color: <?= $campaign["pageb_campaign_theme_color"]; ?>;
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

			.cs-headline-42px{
				margin: auto;

				font-family: var(--theme-font-roboto);
				font-size: 42px;
				font-weight: 700;
				line-height: normal;
				letter-spacing: normal;
				text-transform: normal;
				text-align: center;
				color: #333333;

			}

			.cs-subheadline-17px, .cs-body-text-17px{
				margin: auto;

				font-family: var(--theme-font-roboto);
				font-size: 17px;
				font-weight: 400;
				line-height: normal;
				letter-spacing: normal;
				text-transform: normal;
				text-align: center;
				color: #333333;

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
				<?php if($campaign["pageb_campaign_speech_bubble_extras"] == "video") : ?>
				top: 85px;
				<?php endif; ?>

				width: 0px; height: 0px;

				border-top: 10px solid transparent;
				border-bottom: 10px solid transparent;

				border-right: 10px solid white;

				content: "";
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

			a, a:active, a:visited, a:hover{
				text-decoration: none;
				color: white;
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

			#redirection-overlay{
				margin: 0px; padding: 0px;

				position: absolute;
				top: 0px; left: 0px;

				width: 100%; height: 100%;

				display: block;

				box-sizing: border-box;

				background-color: rgba(0, 0, 0, .1);
				z-index: 999;
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

				.cs-bubble:before{
					position: absolute;
					left: 47.5%; top: -21px;

					width: 0px; height: 0px;

					border-left: 10px solid transparent;
					border-bottom: 10px solid white;

					border-right: 10px solid transparent;

					content: "";
				}
			}
		</style>
	</head>
	<body>
		<div class="cs-main-wrapper">
			<!-- FOR REDIRECTING THIS DISPLAY PAGE TO AFFILATE LINK -->
			<div id="redirection-overlay"></div>

			<!-- FOR WORKAROUND REDIRECTION -->
			<a id="workaround-redirection" style="display: none;" href=""></a>

			<div class="cs-header-fixed-with-button" id="close-element">
				<div class="cs-row-flex">
					<div class="cs-body-text-17px <?= ($campaign["pageb_campaign_lead_magnet_id"]) ? "cs-margin-right-5px" : "" ; ?>"><?= $campaign["pageb_campaign_top_bar_text"]; ?></div>
					<?php if($campaign["pageb_campaign_lead_magnet_id"]) : ?>
					<a class="cs-form-button cs-form-button-theme-color cs-margin-left-5px" href="<?= $SCRIPTURL . "add/download.php?s={$campaign["pageb_campaign_unique_identifier_download"]}"; ?>">Download</a>
					<?php endif; ?>
					<button class="cs-form-button-close cs-form-button-close-theme-color cs-margin-left-0px cs-margin-right-5px" onclick="closeElement()" type="button">x</button>
				</div>
			</div>

			<div id="curl-html">
				<?php
					$curl = new cURL_full_page();
					$html = $curl->get("{$campaign["pageb_campaign_affiliate_link_final"]}");
					
					echo "$html";
				?>
			</div>

			<div class="cs-footer-fixed">
				<div class="cs-row-flex">
					<div class="cs-profile-square cs-profile-square-r85px cs-margin-right-5px">
						<img src="<?= $SCRIPTURL . "upload/{$campaign["user_id"]}/" . $campaign["pageb_campaign_speech_bubble_avatar"]; ?>" />
					</div>
					<div class="cs-bubble cs-margin-left-5px">
						<div class="cs-row-flex">
							<div class="cs-body-text-17px"><?= $campaign["pageb_campaign_speech_bubble_text"]; ?></div>
							<?php if($campaign["pageb_campaign_speech_bubble_extras"] == "coupon_code") : ?>
							<button class="cs-form-button-small cs-form-button-theme-color cs-margin-left-5px" type="button"><?= $campaign["pageb_campaign_speech_bubble_coupon_code"]; ?></button>
							<?php elseif($campaign["pageb_campaign_speech_bubble_extras"] == "timer") : ?>
							<button class="cs-form-button-small cs-form-button-theme-color cs-margin-left-5px" id="timer-container" type="button">
								<?= "&nbsp;&nbsp;" . $timer_value[0] . "h " . $timer_value[1] . "m " . $timer_value[2] . "s"; ?>
							</button>
							<?php elseif($campaign["pageb_campaign_speech_bubble_extras"] == "video") : ?>
								<?php
									if(strpos($campaign["pageb_campaign_speech_bubble_video_url"], "youtu.be")){
										$forYouTubeEmbed = str_replace("youtu.be", "youtube.com/embed" , $campaign["pageb_campaign_speech_bubble_video_url"]);
									}
									
									if(strpos($campaign["pageb_campaign_speech_bubble_video_url"], "watch")){
										$forYouTubeEmbed = str_replace("watch", "embed", $campaign["pageb_campaign_speech_bubble_video_url"]);
									}
									
									if(strpos($campaign["pageb_campaign_speech_bubble_video_url"], "watch?v=")){
										$forYouTubeEmbed = str_replace("watch?v=", "embed/", $campaign["pageb_campaign_speech_bubble_video_url"]);
									}
								?>
							&nbsp;&nbsp;<iframe src="<?= $forYouTubeEmbed; ?>"></iframe>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
	<script type="text/javascript">
		window.onload = function(){
			var x = document.getElementById("curl-html");
			var y = document.getElementById("redirection-overlay");

			y.style.height = x.clientHeight + "px";
		}

		// CLICK ANYWHERE REDIRECTS TO AFFILIATE LINK
		var redirection_overlay = document.getElementById("redirection-overlay");
		redirection_overlay.onclick = function(){
			var workaround_redirection = document.getElementById("workaround-redirection");
			workaround_redirection.href = "<?= $campaign["pageb_campaign_affiliate_link"]; ?>";
			workaround_redirection.click();
		}

		function closeElement(){
			var closeElement = document.getElementById("close-element");

			closeElement.style.opacity = "0";

			function delayDisplayNone(){
				var closeElement = document.getElementById("close-element");

				closeElement.style.display = "none";
			}

			setTimeout(delayDisplayNone, 500);
		}

		var time_in_seconds = <?= $timer_total_value_in_seconds; ?>;
		function timeConversion(time_in_seconds){
			// time_in_seconds--;
			// console.log(time_in_seconds);
			var timer_container = document.getElementById("timer-container");
			var hours = Math.floor(time_in_seconds / 3600);
			var minutes = Math.floor(time_in_seconds % 3600 / 60);
			var seconds = Math.floor(time_in_seconds % 3600 % 60);

			var hours_display = hours > 0 ? hours + (hours == 1 ? "h " : "h ") : "";
			var minutes_display = minutes > 0 ? minutes + (minutes == 1 ? "m " : "m ") : "";
			var seconds_display = seconds > 0 ? seconds + (seconds == 1 ? "s" : "s") : "";

			timer_container.innerHTML = "&nbsp;&nbsp;" + (hours_display + minutes_display + seconds_display);
			// console.log(hours_display + minutes_display + seconds_display);
			// return hours_display + minutes_display + seconds_display;
		}

		setInterval(function(){timeConversion(time_in_seconds--)}, 1000);
	</script>

	<!-- BYPASS X-FRAME SCRIPT -->
	<!-- <script type="module" src="https://unpkg.com/x-frame-bypass"></script> -->
</html>