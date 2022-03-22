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
	
	error_reporting(0);
	$dir="../sys";
	$fp=opendir($dir);
	while(($file=readdir($fp))!=false){
		$file=trim($file);
		if(($file==".")||($file=="..")){continue;}
		$file_parts=pathinfo($dir."/".$file);
		if($file_parts["extension"]=="php"){
			include($dir."/".$file);
		}
	}
	closedir($fp);
	
	$DB = new db($dbhost, $dbuser, $dbpass, $dbname);
	$DB->connect();
	if($DB->connect<1){
		echo "Can't go on, DB not initialized.";
		exit;
	}
	
	$s = $_GET["s"];
	
	$row = $DB->info("pageb", "pageb_n_random_string_bonus_page='$s'");
	// NEW QUERY FOR CPPAGES
	$pageb_new = $DB->query("SELECT * FROM " . $dbprefix . "pageb WHERE pageb_n_random_string_bonus_page = '" . $s . "'");
	$pageb_attribute_names = explode(",", $pageb_new[0]["pageb_attribute_name"]);
	$pageb_attribute_1 = explode(",", $pageb_new[0]["pageb_attribute_1"]);
	$pageb_attribute_2 = explode(",", $pageb_new[0]["pageb_attribute_2"]);
	$pageb_attribute_3 = explode(",", $pageb_new[0]["pageb_attribute_3"]);
	// var_dump($pageb_attribute_names[0]); die();
	// var_dump($pageb_attribute_1[0]); die();

	$res = $DB->query("select * from $dbprefix"."prb p LEFT JOIN $dbprefix"."pr pr ON p.pr_id=pr.pr_id where prb_id IN (". $row["pageb_pr"] .") order by prb_order");
	$res2 = $DB->query("select * from $dbprefix"."prb p LEFT JOIN $dbprefix"."pr pr ON p.pr_id=pr.pr_id where prb_id IN (". $row["pageb_n_pr"] .") order by prb_order");
	$currentBG = $DB->query("SELECT pageb_n_bg_url_1 FROM {$dbprefix}pageb WHERE pageb_n_random_string_bonus_page='$s'");
	$currentBG2 = $DB->query("SELECT pageb_n_bg_url_2 FROM {$dbprefix}pageb WHERE pageb_n_random_string_bonus_page='$s'");
	
	$pr_arr = explode(", ", $row["pageb_pr"]);
	$pageb_countdown_timer = explode(", ", $row["pageb_n_bonus_page_countdown_timer"]);
	
	// FOR HITTING THE YOUTUBE ENDPOINT URLS
	if(strpos($row["pageb_n_leadmagnet_page_video_url"], "youtu.be")){
		$forYouTubeEmbed = str_replace("youtu.be", "youtube.com/embed" , $row["pageb_n_leadmagnet_page_video_url"]);
	}
	elseif(strpos($row["pageb_n_leadmagnet_page_video_url"], "watch")){
		$forYouTubeEmbed = str_replace("watch", "embed", $row["pageb_n_leadmagnet_page_video_url"]);
	}
	
	if($_GET["d"]){
		$d=$_GET["d"];
		$t=$_GET["t"];
		$t_arr=array("pr","cl");
		// var_dump($pr_arr);
		// var_dump(in_array($d,array_keys($pr_arr))&&in_array($t,$t_arr)); die;
	
		if(in_array($d,$pr_arr)&&in_array($t,$t_arr)){
			$cur_prb=$DB->info("prb","prb_id='$d'");
	
			if($t=="pr"){
				$cur_pr=$DB->info("pr","pr_id='".$cur_prb["pr_id"]."'");
				if($cur_pr["pr_cloud"]){
					redirect($cur_pr["pr_cloud"]);
				}
				$url=$cur_pr["pr_url"];
			}
			else{
				$res1=$DB->query("select setup_val as cb_lic from $dbprefix"."setup where setup_key='cb_lic'");
				$url=$res1[0]["cb_lic"];
			}
			$_arr=pathinfo($url);
			$filename=$_arr["basename"];
	
			ob_clean();
			header("Content-Type:application/octet-stream");
			header("Content-Disposition:attachment;filename=\"$filename\"");
			header("Expires:0");
			header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
			header("Pragma:public");
			echo file_get_contents(str_replace(" ","%20",$url));
			exit;
		}
		else{
			redirect("$SCRIPTURL/bdp.php?s=$s");
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $row["pageb_n_bonus_page_headline"]; ?></title>
	
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
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
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,900" />
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open Sans:400,900" />
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins: 400,900" />
		
		<!-- Embeded Style -->
		<style>
			:root{
				--theme-color: <?php echo $row["pageb_n_theme_color"]; ?>;
				<?php if($currentBG[0]['pageb_n_bg_url_1'] == null){ ?>
				--theme-bg: url("<?php echo $SCRIPTURL . "upload/" . $row["user_id"] . "/". $currentBG2[0]['pageb_n_bg_url_2']; ?>");
				<?php }else{ ?>
				--theme-bg: url("<?php echo $SCRIPTURL . "add/". $currentBG[0]['pageb_n_bg_url_1']; ?>");
				<?php } ?>
				--theme-headline-font: "Open Sans";
				--theme-subheadline-font: "Poppins";
				--theme-text-font: "Poppins";
			}
			
			body{
				margin: 0px; padding: 0px;
				
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

			.main-wrapper{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: auto; height: auto;
				
				display: block;
				
				box-sizing: border-box;
			}

			/* STATICS */
			.statics{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: auto; height: auto;
				
				display: block;
				
				box-sizing: border-box;
			}

			.static-text-1{
				font-family: var(--theme-text-font);
				font-size: 18px;
				/*font-weight: 700;*/
				text-transform: normal;
				text-align: center;
				color: rgba(255, 255, 255, 0.639216);
			}

			.static-text-2{
				font-family: var(--theme-text-font);
				font-size: 14px;
				/*font-weight: 700;*/
				text-transform: normal;
				text-align: center;
				color: rgba(255, 255, 255, 0.639216);
			}

			.page-header{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: auto; height: auto;
				
				display: block;
				
				box-sizing: border-box;
			}

			.page-header-text{
				padding: 20px;

				font-family: var(--theme-text-font);
				font-size: 32px;
				/*font-weight: 700;*/
				text-transform: normal;
				text-align: center;
				color: white;

				background-color: var(--theme-color);
			}

			.bonuses-wrapper{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: auto; height: auto;
				
				display: flex;
				flex-direction: row;
				
				box-sizing: border-box;
			}

			.bonus{
				margin: auto; padding: 20px;

				position: relative;

				width: 250px; height: auto;

				display: block;

				box-sizing: border-box;

				background-color: white;
			}

			.bonus:nth-child(1){
				margin-right: 5px;
			}

			.bonus:nth-child(2){
				margin: auto 5px;

				border: 3px solid white;
				background-color: var(--theme-color);
			}

			.bonus:nth-child(3){
				margin-left: 5px;

				background-color: gray;
			}

			.bonus:nth-child(1), .bonus:nth-child(3){
				width: 200px;
			}

			.bonus:nth-child(2) .bonus-headline{
				text-decoration: underline;
			}

			.bonus:nth-child(2) .bonus-headline{
				color: white;
				font-size: 24px;
			}

			.bonus:nth-child(2) .bonus-title{
				color: white;
			}

			.bonus:nth-child(3) .bonus-headline, .bonus:nth-child(3) .bonus-title{
				color: white;
			}

			.popular-choice{
				border: 3px solid white;
				background-color: var(--theme-color);
			}

			.bonus-headline, .bonus-image, .bonus-title{
				margin: auto; padding: 0px;

				position: relative;

				width: 100%; height: auto;

				display: block;

				box-sizing: border-box;
			}

			.bonus-headline, .bonus-title{
				font-family: var(--theme-text-font);
				font-size: 18px;
				font-weight: 700;
				text-transform: normal;
				text-align: center;
				color: grey;
			}

			.bonus-image img{
				width: 100%;
			}

			.attribute-wrapper{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: auto; height: auto;
				
				display: block;
				
				box-sizing: border-box;
			}

			.attribute{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: 65%; height: auto;
				
				display: flex;
				flex-direction: row;
				
				box-sizing: border-box;

				background-color: white;

				font-family: var(--theme-text-font);
				font-size: 15px;
				font-weight: 700;
				text-transform: normal;
				text-align: center;
				color: #333333;
			}

			.attribute div{
				margin: auto; padding: 10px;
				
				position: relative;
				
				width: auto; height: auto;
				
				display: block;
				flex: 1;
				
				box-sizing: border-box;

				text-align: center;
			}

			.attribute div:nth-child(1){
				text-align: left;
				text-transform: uppercase;
			}

			.attribute-wrapper .attribute:nth-child(odd){
				background-color: gainsboro;
			}

			.attribute-wrapper .attribute:last-child{
				margin-top: 20px;

				background-color: transparent;
			}

			.get-free-button button, .get-premium-button button, .get-fast-track-button button{
				padding: 10px 20px;

				font-family: var(--theme-text-font);
				font-size: 15px;
				font-weight: 700;
				text-decoration: none;
				text-transform: uppercase;
				text-align: center;
				color: white;

				border: 1px solid transparent;
				border-radius: 3px;

				cursor: pointer;
			}

			.get-free-button button{
				background-color: white;
				color: gray;
			}

			.get-free-button button:hover{
				color: gray;
			}

			.get-premium-button button{
				padding: 20px;
				background-color: var(--theme-color);
			}

			.get-fast-track-button button{
				background-color: gray;
			}

			.bonuses-2{
				margin: auto; padding: 30px 50px;
				
				position: relative;
				
				width: 65%; height: auto;
				
				display: block;
				
				box-sizing: border-box;

				background-color: white;
			}

			.bonuses-2-headline{
				font-family: var(--theme-text-font);
				font-size: 50px;
				font-weight: 700;
				text-transform: normal;
				text-align: center;
				color: #333333;
			}

			.bonuses-2-subheadline{
				font-family: var(--theme-text-font);
				font-size: 23px;
				font-weight: normal;
				text-transform: normal;
				text-align: center;
				color: #333333;
			}

			.bonuses-2-product-wrapper{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: auto; height: auto;
				
				display: flex;
				flex-direction: row;
				
				box-sizing: border-box;
			}

			.bonuses-2-product-image, .bonuses-2-product-details{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: 150px; height: auto;
				
				display: block;
				
				box-sizing: border-box;
			}

			.bonuses-2-product-details{
				width: 400px;
			}

			.bonuses-2-product-image img{
				width: 100%;
			}

			.bonuses-2-product-details div:nth-child(1){
				font-family: var(--theme-text-font);
				font-size: 32px;
				font-weight: 700;
				text-transform: normal;
				text-align: center;
				color: #333333;
			}

			.bonuses-2-product-details div:nth-child(2){
				font-family: var(--theme-text-font);
				font-size: 16px;
				font-weight: normal;
				text-transform: normal;
				text-align: center;
				color: #333333;
			}

			.claim-button-wrapper{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: 40%; height: auto;
				
				display: block;
				
				box-sizing: border-box;
			}

			.claim-button, .claim-button:hover, .claim-button:active{
				margin: auto; padding: 10px 20px;
				
				position: relative;
				
				width: auto; height: auto;
				
				display: block;
				
				box-sizing: border-box;

				text-decoration: none;

				background-color: var(--theme-color);
			}

			.claim-button div{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: auto; height: auto;
				
				display: block;
				
				box-sizing: border-box;
			}

			.claim-button div:first-child{
				font-family: var(--theme-text-font);
				font-size: 36px;
				font-weight: 600;
				text-transform: normal;
				text-align: center;
				color: white;
			}

			.claim-button div:last-child{
				font-family: var(--theme-text-font);
				font-size: 14px;
				font-weight: normal;
				text-transform: normal;
				text-align: center;
				color: gray;
			}

			.disclaimer-wrapper{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: 65%; height: auto;
				
				display: block;
				
				box-sizing: border-box;

				font-family: var(--theme-text-font);
				font-size: 16px;
				font-weight: normal;
				text-transform: normal;
				text-align: center;
				color: black;
			}

			/* OVERRIDES */
			.margin-bottom-10px{
				margin-bottom: 10px !important;
			}

			.margin-bottom-20px{
				margin-bottom: 20px !important;
			}

			.margin-bottom-30px{
				margin-bottom: 30px !important;
			}

			.margin-bottom-40px{
				margin-bottom: 40px !important;
			}

			.margin-bottom-50px{
				margin-bottom: 50px !important;
			}

			/* MEDIA QUERIES: RESPONSIVENESS */
			@media only screen and (max-width: 768px){
				.attribute{
					width: 95%;
				}

				.bonuses-wrapper{
					flex-direction: column;
				}

				.bonus{
					margin: auto !important;
					width: 95% !important;
				}

				.bonuses-2{
					padding: 20px;
					width: 95%;
				}

				.bonuses-2-product-wrapper{
					flex-direction: column;
				}

				.bonuses-2-product-image{
					margin: auto !important;
					width: 100%;
				}

				.bonuses-2-product-details{
					margin: auto !important;
					width: 100%
				}

				.claim-button-wrapper{
					width: 95%;
				}

				.claim-button div:first-child{
					font-size: 25px;
				}

				.disclaimer-wrapper{
					width: 95%;
				}
			}
		</style>
		
		<script>

		</script>
	</head>
	<body>
		<div class="main-wrapper">
			<div class="page-header page-header-text margin-bottom-30px"><?php echo $row["pageb_n_bonus_page_headline"]; ?></div>

			<div class="bonuses-wrapper margin-bottom-30px">
				<?php foreach($res2 as $leadmagnet_product){ ?>
				<div class="bonus">
					<div class="bonus-headline">Your Download</div>
					<div class="bonus-image">
						<img src="<?php echo $leadmagnet_product["pr_cover"]; ?>" />
					</div>
					<div class="bonus-title"><?php echo $leadmagnet_product["pr_title"]; ?></div>
				</div>
				<?php } ?>

				<div class="bonus">
					<div class="bonus-headline">Popular Choice</div>
					<div class="bonus-image">
						<img src="<?php echo $SCRIPTURL . "upload/" . $pageb_new[0]["user_id"] . "/" . $pageb_new[0]["pageb_affiliate_image_1"]; ?>" />
					</div>
					<div class="bonus-title"><?php echo $pageb_new[0]["pageb_affiliate_name_1"]; ?></div>
				</div>

				<div class="bonus">
					<div class="bonus-headline">Fast Track Option</div>
					<div class="bonus-image">
						<img src="<?php echo $SCRIPTURL . "upload/" . $pageb_new[0]["user_id"] . "/" . $pageb_new[0]["pageb_affiliate_image_2"]; ?>" />
					</div>
					<div class="bonus-title"><?php echo $pageb_new[0]["pageb_affiliate_name_2"]; ?></div>
				</div>
			</div>

			<div class="attribute-wrapper margin-bottom-50px">
				<div class="attribute">
					<div></div>
					<div>Your Download</div>
					<div>Popular Choice</div>
					<div>Fast Track Option</div>
				</div>
				<?php $offset = -1; ?>
				<?php foreach($pageb_attribute_names as $pageb_attribute_name){ ?>
				<?php $offset++; ?>
				<div class="attribute" style="padding: 15px;">
					<div><?php echo $pageb_attribute_name; ?></div>
					<div>
						<?php if($pageb_attribute_1[$offset] == "true"){ ?>
						<i class="fa fa-check-circle" style="color: seagreen;"></i>
						<?php }else{ ?>
						<i class="fa fa-times-circle" style="color: firebrick;"></i>
						<?php } ?>
					</div>
					<div>
						<?php if($pageb_attribute_2[$offset] == "true"){ ?>
						<i class="fa fa-check-circle" style="color: seagreen;"></i>
						<?php }else{ ?>
						<i class="fa fa-times-circle" style="color: firebrick;"></i>
						<?php } ?>
					</div>
					<div>
						<?php if($pageb_attribute_3[$offset] == "true"){ ?>
						<i class="fa fa-check-circle" style="color: seagreen;"></i>
						<?php }else{ ?>
						<i class="fa fa-times-circle" style="color: firebrick;"></i>
						<?php } ?>
					</div>
				</div>
				<?php } ?>

				<div class="attribute">
					<div></div>
					<div>
						<a href="<?php echo $SCRIPTURL . "add/lead-magnet.php?s=" . $row["pageb_n_random_string_leadmagnet_page"] ?>" class="get-free-button">
							<button type="button">Get Free</button>
						</a>
					</div>
					<div>
						<a href="<?php echo $pageb_new[0]["pageb_affiliate_url_1"]; ?>" class="get-premium-button">
							<button type="button">Get Premium</button>
						</a>
					</div>
					<div>
						<a href="<?php echo $pageb_new[0]["pageb_affiliate_url_2"]; ?>" class="get-fast-track-button">
							<button type="button">Get Fast Track</button>
						</a>
					</div>
				</div>
			</div>

			<div class="bonuses-2 margin-bottom-30px">
				<div class="bonuses-2-headline margin-bottom-20px">*BUT WAIT... There's More</div>
				<div class="bonuses-2-subheadline margin-bottom-30px">
					If you proceed with our highly recommended product <?php echo $leadmagnet_product["pr_title"] ?> you will be eligible for the following exclusive bonuses
				</div>
				<?php foreach($res as $row2){ ?>
				<div class="bonuses-2-product-wrapper margin-bottom-20px">
					<div class="bonuses-2-product-image">
						<img src="<?php echo $row2["pr_cover"]; ?>" />
					</div>
					<div class="bonuses-2-product-details">
						<div><?php echo $row2["pr_title"]; ?></div>
						<div><?php echo $row2["pr_desc"]; ?></div>
					</div>
				</div>
				<?php } ?>
			</div>

			<div class="claim-button-wrapper margin-bottom-50px">
				<a href="<?php echo $pageb_new[0]["pageb_affiliate_url_1"]; ?>" class="claim-button">
					<div>Click Here To Claim Your Copy</div>
					<div>ACTIVATE YOUR BONUSES</div>
				</a>
			</div>

			<div class="disclaimer-wrapper margin-bottom-30px">
				Affiliate Disclaimer: While we receive affiliate compensation for reviews / promotions on this page, we always offer honest opinion, relevant experiences and genuine views related to the product or service itself. Our goal is to help you make the best purchasing decisions, however, the views and opinions expressed are ours only. As always you should do your own due diligence to verify any claims, results and statistics before making any kind of purchase. Clicking links or purchasing products recommended on this page may generate income for this website from affiliate commissions and you should assume we are compensated for any purchases you make
				<br /><br />
				This site is not a part of the FaceBook website or FaceBook INC. Additionally, this site is NOT endorsed by FaceBook in ANY WAY. FACEBOOK is a trademark of FaceBook INC.
				<br /><br />
				Income Disclaimer<br />
				This website and the items it distributes contain business strategies, marketing methods and other business advice that, regardless of my own results and experience, may not produce the same results (or any results) for you. easyinstaprofits.com makes absolutely no guarantee, expressed or implied, that by following the advice or content available from this web site you will make any money or improve current profits, as there are several factors and variables that come into play regarding any given business
				Primarily, results will depend on the nature of the product or business model, the conditions of the marketplace, the experience of the individual, and situations and elements that are beyond your control.
				As with any business endeavour, you assume all risk related to investment and money based on your own discretion and at your own potential expense.
				<br /><br />
				Liability Disclaimer<br />
				By reading this website or the documents it offers, you assume all risks associated with using the advice given, with a full understanding that you, solely, are responsible for anything that may occur as a result of putting this information into action in any way, and regardless of your interpretation of the advice.
				You further agree that our company cannot be held responsible in any way for the success or failure of your business as a result of the information provided by our company. It is your responsibility to conduct your own due diligence regarding the safe and successful operation of your business if you intend to apply any of our information in any way to your business operations.

				In summary, you understand that we make absolutely no guarantees regarding income as a result of applying this information, as well as the fact that you are solely responsible for the results of any action taken on your part as a result of any given information.

				In addition, for all intents and purposes you agree that our content is to be considered "for entertainment purposes only". Always seek the advice of a professional when making financial, tax or business decisions
			</div>
		</div>
		<!-- <div class="content-wrapper">
			<div class="bonus-header">
				<div class="static-headline-1 margin-right-50px">YOUR DOWNLOAD IS READY</div>
				<a href="<?php echo $SCRIPTURL . "add/lead-magnet.php?s=" . $row["pageb_n_random_string_leadmagnet_page"] ?>" class="redirection-leadmagnet-page-button margin-left-40px">CLICK HERE TO DOWNLOAD</a>
			</div>
			
			<div class="static-headline-2 margin-top-30px">IN THE MEANTIME...</div>
			
			<div class="bonus-headline"><?php echo $row["pageb_n_bonus_page_headline"]; ?></div>
			
			<div class="bonus-video-container">
				<iframe src="<?php echo $forYouTubeEmbed; ?>" allowfullscreen="" width="100%" height="100%" frameborder="0"></iframe>
			</div>
			
			<div class="static-text-1 margin-top-20px margin-bottom-30px">TURN THE SOUND ON AND WATCH RIGHT NOW!</div>
			
			<a href="<?php echo $row["pageb_n_bonus_page_affiliate_url"] ?>" class="bonus-affiliate-button">
				<div class="bonus-affiliate-button-text-1">Click Here To Secure Your Access & Exclusive Bonuses</div>
				<div class="bonus-affiliate-button-text-2">All Your Bonuses Are Listed Below</div>
			</a>
			
			<div class="bonus-countdown-timer-container margin-top-20px">
				<div class="static-text-1">IMPORTANT: The Exclusive Bonuses Will Expire In...</div>
				
				<div class="bonus-countdown-timer margin-top-20px">
					<input type="text" value="<?php echo $pageb_countdown_timer[0]; ?>" class="bonus-countdown-timer-hours" readonly />
					<input type="text" value="<?php echo $pageb_countdown_timer[1]; ?>" class="bonus-countdown-timer-minutes" readonly />
					<input type="text" value="<?php echo $pageb_countdown_timer[2]; ?>" class="bonus-countdown-timer-seconds" readonly />
				</div>
				<div class="bonus-countdown-timer-label-container">
					<div>HOURS</div>
					<div>MINUTES</div>
					<div>SECONDS</div>
				</div>
			</div>
			
			<div class="static-text-1 margin-top-30px">Only 20 Bonus Packages Remain... Act Now</div>
			
			<div class="static-progress-bar-container">
				<div class="progress" style="border: 3px solid white; border-radius: 100px; height: 30px;">
					<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:70%; background-color: var(--theme-color);"></div>
				</div>
			</div>
			
			<div class="bonus-bonus-box">
				<div class="bonus-bonus-box-headline-1">Your Exclusive Bonuses</div>
				<div class="bonus-bonus-box-headline-2">If You Proceed With This Amazing Product Today!</div>
				
				<?php foreach($res as $row2){ ?>
				<div class="bonus-product-container">
					<div class="bonus-product-image-container">
						<img src="<?php echo $row2["pr_cover"]; ?>" />
					</div>
					
					<div class="bonus-product-details">
						<div><?php echo $row2["pr_title"] ;?></div>
						<div><?php echo $row2["pr_desc"] ;?></div>
					</div>
				</div>
				<?php } ?>
			</div>
			
			<div class="bonus-redemption-box">
				In order to receive your qualifying bonus send an email titled "<?php echo $row["pageb_title"] ?>" to <?php echo $row["pageb_n_bonus_redemption_email"] ?> and include a copy of your email receipt that you received following purchase of <?php echo $row["pageb_title"] ?> and you will receive your bonus by return.
			</div>
			
			<div class="bonus-limited-offer-box">
				<div class="static-text-1 margin-top-30px">Extremely Limited Offer!</div>
				
				<a href="<?php echo $row["pageb_n_bonus_page_affiliate_url"] ?>" class="bonus-affiliate-button">
					<div class="bonus-affiliate-button-text-1">Click Here To Secure Your Access & Exclusive Bonuses</div>
					<div class="bonus-affiliate-button-text-2">All Your Bonuses Are Listed Below</div>
				</a>
				
				<div class="bonus-countdown-timer-container margin-top-20px">
					<div class="static-text-1">IMPORTANT: The Exclusive Bonuses Will Expire In...</div>
					
					<div class="bonus-countdown-timer margin-top-20px">
						<input type="text" value="<?php echo $pageb_countdown_timer[0]; ?>" class="bonus-countdown-timer-hours-2" readonly />
					<input type="text" value="<?php echo $pageb_countdown_timer[1]; ?>" class="bonus-countdown-timer-minutes-2" readonly />
					<input type="text" value="<?php echo $pageb_countdown_timer[2]; ?>" class="bonus-countdown-timer-seconds-2" readonly />
					</div>
					<div class="bonus-countdown-timer-label-container">
						<div>HOURS</div>
						<div>MINUTES</div>
						<div>SECONDS</div>
					</div>
				</div>
			</div>
			
			<div class="static-footer">
				<div class="static-footer-content">
					Affiliate Disclaimer: While we receive affiliate compensation for reviews / promotions on this page, we always offer honest opinion, relevant experiences and genuine views related to the product or service itself. Our goal is to help you make the best purchasing decisions, however, the views and opinions expressed are ours only. As always you should do your own due diligence to verify any claims, results and statistics before making any kind of purchase. Clicking links or purchasing products recommended on this page may generate income for this website from affiliate commissions and you should assume we are compensated for any purchases you make
					<br /><br />
					This site is not a part of the FaceBook website or FaceBook INC. Additionally, this site is NOT endorsed by
					FaceBook in ANY WAY. FACEBOOK is a trademark of FaceBook INC.
					<br /><br />
					Income Disclaimer
					<br />
					This website and the items it distributes contain business strategies, marketing methods and other business advice that, regardless of my own results and experience, may not produce the same results (or any results) for you. easyinstaprofits.com makes absolutely no guarantee, expressed or implied, that by following the advice or content available from this web site you will make any money or improve current profits, as there are several factors and variables that come into play regarding any given business
					<br />
					Primarily, results will depend on the nature of the product or business model, the conditions of the marketplace, the experience of the individual, and situations and elements that are beyond your control.
					<br />
					As with any business endeavour, you assume all risk related to investment and money based on your own discretion and at your own potential expense.
					<br /><br />
					Liability Disclaimer
					<br />
					By reading this website or the documents it offers, you assume all risks associated with using the advice given, with a full understanding that you, solely, are responsible for anything that may occur as a result of putting this information into action in any way, and regardless of your interpretation of the advice.
					<br />
					You further agree that our company cannot be held responsible in any way for the success or failure of your business as a result of the information provided by our company. It is your responsibility to conduct your own due diligence regarding the safe and successful operation of your business if you intend to apply any of our information in any way to your business operations.
					<br /><br />
					In summary, you understand that we make absolutely no guarantees regarding income as a result of applying this information, as well as the fact that you are solely responsible for the results of any action taken on your part as a result of any given information.
					<br /><br />
					In addition, for all intents and purposes you agree that our content is to be considered "for entertainment purposes only". Always seek the advice of a professional when making financial, tax or business decisions
				</div>
			</div>
		</div> -->
	</body>
</html>