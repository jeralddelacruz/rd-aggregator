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

	// NEW QUERY FOR CPPAGES
	$pageb_new = $DB->query("SELECT * FROM " . $dbprefix . "pageb WHERE pageb_n_random_string_leadmagnet_page = '" . $s . "'");
	$pageb_attribute_names = explode(",", $pageb_new[0]["pageb_attribute_name"]);
	$pageb_attribute_1 = explode(",", $pageb_new[0]["pageb_attribute_1"]);
	$pageb_attribute_2 = explode(",", $pageb_new[0]["pageb_attribute_2"]);
	$pageb_attribute_3 = explode(",", $pageb_new[0]["pageb_attribute_3"]);
	
	$row = $DB->info("pageb", "pageb_n_random_string_leadmagnet_page='$s'");
	$res = $DB->query("select * from $dbprefix"."prb p LEFT JOIN $dbprefix"."pr pr ON p.pr_id=pr.pr_id where prb_id IN (". $row["pageb_n_pr"] .") order by prb_order");
	$currentBG = $DB->query("SELECT pageb_n_bg_url_1 FROM {$dbprefix}pageb WHERE pageb_n_random_string_leadmagnet_page='$s'");
	$currentBG2 = $DB->query("SELECT pageb_n_bg_url_2 FROM {$dbprefix}pageb WHERE pageb_n_random_string_leadmagnet_page='$s'");
	
	$pr_arr = explode(", ", $row["pageb_n_pr"]);
	
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
		<title><?php echo $row["pageb_n_leadmagnet_page_headline"]; ?></title>
	
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
		<!-- Bootstrap CDNs -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	
		<!-- Other CDNs -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/v4-shims.css">
		
		<!-- Local CDNs -->
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<script type="text/javascript" src="js/script.js"></script>
		
		<!-- Google Fonts CDN -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,900" />
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open Sans:400,900" />
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:400,900" />
		
		<!-- Embeded Style -->
		<style>
			:root{
				--theme-color: <?php echo $row["pageb_n_theme_color"] ?>;
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

			.recommendation-box-wrapper{
				margin: auto; padding: 30px 50px;
				
				position: relative;
				
				width: 65%; height: auto;
				
				display: block;
				
				box-sizing: border-box;

				background-color: white;
			}

			.recommendation-box-headline{
				font-family: var(--theme-text-font);
				font-size: 50px;
				font-weight: 700;
				text-transform: normal;
				text-align: center;
				color: #333333;
			}

			.recommendation-box-subheadline{
				font-family: var(--theme-text-font);
				font-size: 23px;
				font-weight: normal;
				text-transform: normal;
				text-align: center;
				color: #333333;
			}

			.recommendation-box-image-and-attributes-wrapper{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: auto; height: auto;
				
				display: flex;
				flex-direction: row;
				
				box-sizing: border-box;
			}

			.recommendation-box-image, .recommendation-box-attributes{
				margin: auto; padding: 0px;
				
				position: relative;
				
				width: 250px; height: auto;
				
				display: block;
				
				box-sizing: border-box;
			}

			.recommendation-box-image{
				margin-right: 5px;
			}

			.recommendation-box-attributes{
				margin-left: 5px;
				margin-right: 0px;
				width: 50%;
			}

			.recommendation-box-attribute{
				margin-bottom: 10px;
				/*border-bottom: 1px solid gainsboro;*/
				font-family: var(--theme-text-font);
				font-size: 15px;
				font-weight: 700;
				text-transform: normal;
				/*text-align: center;*/
				color: #333333;
			}

			.recommendation-box-image img{
				width: 100%;
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

			.recommendation-box-affiliate-product-description{
				font-family: var(--theme-text-font);
				font-size: 23px;
				font-weight: 500;
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

			a, a:active, a:hover, a:visited{
				text-decoration: none !important;
			}
			
			/* MEDIA QUERIES: RESPONSIVENESS */
			@media only screen and (max-width: 768px){
				.claim-button-wrapper{
					margin: auto;
					width: 95%;
				}

				.recommendation-box-wrapper{
					margin: auto;
					width: 95%;
				}

				.recommendation-box-image-and-attributes-wrapper{
					flex-direction: column;
				}

				.recommendation-box-image{
					margin: auto;
				}

				.recommendation-box-attributes{
					margin: auto;
				}

				.disclaimer-wrapper{
					width: 95%;
				}
			}
		</style>
	</head>
	<body>
		<div class="main-wrapper">
			<div class="page-header page-header-text margin-bottom-50px"><?php echo $row["pageb_n_leadmagnet_page_headline"]; ?></div>

			<div class="claim-button-wrapper margin-bottom-50px">
				<a href="<?php echo $SCRIPTURL . "add/lead-magnet.php?s=" . $s . "&d=" . $res[0]["prb_id"] . "&t=pr" ?>" class="claim-button">
					<div>Click To Access your Download</div>
					<div>Download Will Begin Instantly</div>
				</a>
			</div>

			<div class="recommendation-box-wrapper margin-bottom-50px">
				<div class="recommendation-box-headline margin-bottom-20px">Our Recommended Upgrade</div>
				<div class="recommendation-box-subheadline margin-bottom-30px">
					Your free download has been created with you in mind providing you the key information that you need. However if you want the tools and training to take your success to the next level I urge you to consider "<?php echo $pageb_new[0]["pageb_affiliate_name_1"]; ?>".
				</div>

				<div class="recommendation-box-image-and-attributes-wrapper margin-bottom-30px">
					<div class="recommendation-box-image">
						<?php if($pageb_new[0]["pageb_affiliate_image_1"] != "" || $pageb_new[0]["pageb_affiliate_image_1"] != null){ ?>
						<img src="<?php echo $SCRIPTURL . "upload/" . $pageb_new[0]["user_id"] . "/" . $pageb_new[0]["pageb_affiliate_image_1"]; ?>" />
						<?php } ?>
					</div>
					<div class="recommendation-box-attributes">
						<?php $offset = -1; ?>
						<?php foreach($pageb_attribute_names as $pageb_attribute_name){ ?>
						<?php $offset++; ?>
						<div class="recommendation-box-attribute">
							<!-- ALL POSITIVE ATTRIBUTE ONLY -->
							<?php if($pageb_attribute_2[$offset] == "true"  || $pageb_attribute_1[$offset] == "checked"){ ?>
							<i class="fa fa-check-square fa-2x" style="color: seagreen;"></i> <?php echo $pageb_attribute_name; ?>
							<?php } ?>
							<!-- ORIGINAL -->
							<!-- <?php if($pageb_attribute_2[$offset] == "checked"){ ?>
							<i class="fa fa-check-circle fa-2x" style="color: seagreen;"></i> <?php echo $pageb_attribute_name; ?>
							<?php }else{ ?>
							<i class="fa fa-times-circle fa-2x" style="color: firebrick;"></i> <?php echo $pageb_attribute_name; ?>
							<?php } ?> -->
						</div>
						<?php } ?>
					</div>
				</div>

				<div class="recommendation-box-affiliate-product-description">
					<?php echo $pageb_new[0]["pageb_recommendation_description"]; ?>
				</div>
			</div>

			<div class="claim-button-wrapper margin-bottom-50px">
				<a href="<?php echo $pageb_new[0]["pageb_affiliate_url_1"]; ?>" class="claim-button">
					<div>YES I DESERVE THIS</div>
					<div>CLICK HERE TO ACTIVATE YOUR BONUSES</div>
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
			<div class="leadmagnet-page-header">* YOUR FREE BONUS CAN BE FOUND BELOW *</div>
			
			<div class="static-headline-1 width-40-percent">DID YOU FORGET TO WATCH THE TRAINING?</div>
			
			<div class="leadmagnet-page-headline"><?php echo $row["pageb_n_bonus_page_headline"]; ?></div>
			
			<a href="<?php echo $row["pageb_n_bonus_page_affiliate_url"] ?>" class="leadmagnet-page-affiliate-button">
				<div class="leadmagnet-page-affiliate-button-text-1">Click Here To Watch The Training Right Now</div>
				<div class="leadmagnet-page-affiliate-button-text-2">Thank Us Later</div>
			</a>
			
			<div class="leadmagnet-page-bonus-box margin-top-40px">
				<div class="leadmagnet-page-bonus-box-headline-1">Download Your Free Bonuses</div>
				
				<?php foreach($res as $row2){ ?>
				<a href="<?php echo $SCRIPTURL . "add/lead-magnet.php?s=" . $s . "&d=" . $row2["prb_id"] . "&t=pr" ?>" class="leadmagnet-page-affiliate-button">
					<div class="leadmagnet-page-affiliate-button-text-1">Click Here To Download Your FREE Bonuses Now!</div>
					<div class="leadmagnet-page-affiliate-button-text-2">Have A Great Day!</div>
				</a>
				<?php } ?>
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