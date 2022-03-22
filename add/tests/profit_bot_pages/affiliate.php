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
	$currentBG = $DB->query("SELECT pageb_n_bg_url_1 FROM {$dbprefix}pageb WHERE pageb_n_random_string_squeeze_page='$s'");
	$currentBG2 = $DB->query("SELECT pageb_n_bg_url_2 FROM {$dbprefix}pageb WHERE pageb_n_random_string_squeeze_page='$s'");
	
	if(isset($_POST['submit'])) {
		$platformValue = $row["pageb_platform"];
		$redirectUrl = $SCRIPTURL . "add/lead-magnet.php?s=" . $row["pageb_n_random_string_leadmagnet_page"];
		$autoresponder = $DB->info("api", "user_id = '$user' AND platform = '$platformValue' ");
		$autoresponderData = json_decode($autoresponder['data']);
	
		if($platformValue == 'getresponse') {
	
			$campaignId = $row["pageb_list"];
			$email = strip($_POST["email"]);
			
			$send = addGREmailtoList($email, $campaignId, $autoresponderData->api_key);
			//$_SESSION['msg'] = $send['success'] ? 'Success!' : '';
			if($send['success']) {
				if($redirectUrl) {
					redirect($redirectUrl);
				}
				else {
					$_SESSION['msg'] = 'Success!' ;
				}
			}
	
		} elseif($platformValue == 'mailchimp') {
	
			$listId = $row["pageb_list"];
			$email = strip($_POST["email"]);
			
			$send = addEmailtoMailchimpList($email, $listId, $autoresponderData->api_key);
			if($send['success']) {
				if($redirectUrl) {
					redirect($redirectUrl);
				}
				else {
					$_SESSION['msg'] = 'Success!' ;
				}
			}
	
		} elseif($platformValue == 'convertkit') {
	
			$tagId = $row["pageb_list"];
			$email = strip($_POST["email"]);
			
			$send = addEmailToTags($email, $tagId, $autoresponderData->api_key) ;
			if($send['success']) {
				if($redirectUrl) {
					redirect($redirectUrl);
				}
				else {
					$_SESSION['msg'] = 'Success!' ;
				}
			}
	
		} elseif($platformValue == 'sendlane') {
	
			$listId = $row["pageb_list"];
			$email = strip($_POST["email"]);
			
			$send = addEmailToListSendlane($email, $listId, $autoresponderData->api, $autoresponderData->hash) ;
			//$_SESSION['msg'] = $send['success'] ? 'Success!' : '';
			if($send['success']) {
				if($redirectUrl) {
					redirect($redirectUrl);
				}
				else {
					$_SESSION['msg'] = 'Success!' ;
				}
			}
	
		} elseif($platformValue == 'hubspot') {
	
			$email = strip($_POST["email"]);
			$send = createContactHubspot($email, $autoresponderData->api) ;
			if($send['success']) {
				$id = $row["pageb_list"];
				//echo "<script>console.log('$id')</script>";
				$add = addEmailToHubspotList($email, $id, $autoresponderData->api);
				if($add['success']){
					if($redirectUrl) {
						redirect($redirectUrl);
					}
					else {
						$_SESSION['msg'] = 'Success!' ;
					}
				}
			}
	
		} elseif($platformValue == 'activecampaign') {
			
			$email = strip($_POST["email"]);
			$create = createContactActiveCampaign($autoresponderData->api_key, $autoresponderData->acc_url, $email) ; //create contact
			if($create['success']) {
				foreach ($create['data'] as $list){
					$contactId = $list->id;
				}
				//echo "<script>console.log('$contactId')</script>";	
				$listId = $row["pageb_list"];
				$add = addtoListActiveCampaign($autoresponderData->api_key, $autoresponderData->acc_url, $contactId, $listId); //add contact to list
				if($add['success']){
					if($redirectUrl) {
						redirect($redirectUrl);
					}
					else {
						$_SESSION['msg'] = 'Success!' ;
					}
				}
			}
	
		} elseif($platformValue == 'aweber') {
	
			$id = $row["pageb_list"];
			$email = strip($_POST["email"]);
			
			// refresh token
			$refresh = refreshAccToken($autoresponderData->refresh_token, $client_id, $secret_key);
	
			if ($refresh['success']) {
				$access_token = strip($refresh['data']->access_token);
			}
			
			// add subscriber
			$send = addAWSubcriber($autoresponderData->account_id, $id, $email, $access_token) ;
			
			if($send['success']){
				if($redirectUrl) {
					redirect($redirectUrl);
				}
				else {
					$_SESSION['msg'] = 'Success!' ;
				}
			}
			
		} elseif($platformValue == 'constantcontact') {
			
			$list_id = $row["pageb_list"];
			$email = strip($_POST["email"]);
			
			$refresh = refreshCCToken($autoresponderData->refresh_token);
	
			if ($refresh['success']) {
				$autoresponderData->access_token  = strip($refresh['data']->access_token);
				$autoresponderData->refresh_token = strip($refresh['data']->refresh_token);
	
				$constantcontact = json_encode($autoresponderData);
				$DB->query("UPDATE {$dbprefix}api SET data = '$constantcontact' WHERE user_id = '$user' AND platform = 'constantcontact' ");
	
				$add = addContactCC($autoresponderData->access_token, $email, $list_id) ;
	
				if ($add['success']) {
					if($redirectUrl) {
						redirect($redirectUrl);
					}
					else {
						$_SESSION['msg'] = 'Success!' ;
					}
				}
			}
			
		} elseif($platformValue == 'sendiio') {
			
			$listId = $row["pageb_list"];
			$email = strip($_POST["email"]);
			
			$send = subscribeEmailToList($email, $listId, $autoresponderData->api_token, $autoresponderData->api_secret);
			
			if($send['success']) {
				if($redirectUrl) {
					redirect($redirectUrl);
				}
				else {
					$_SESSION['msg'] = 'Success!' ;
				}
			}
	
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?= $row["pageb_n_squeeze_page_headline"] ? $row["pageb_n_squeeze_page_headline"] : "Affiliate Page"; ?></title>
	
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
	<body>
		<div class="cs-main-wrapper">
			<div class="cs-header-fixed-with-button" id="close-element">
				<div class="cs-row-flex">
					<div class="cs-body-text-17px cs-margin-right-5px">Order in the next 00:00:00 and get 15% off your first order!</div>
					<button class="cs-form-button cs-form-button-theme-color cs-margin-left-5px" type="button">I love saving</button>
					<button class="cs-form-button-close cs-form-button-close-theme-color cs-margin-left-0px cs-margin-right-5px" onclick="closeElement()" type="button">x</button>
				</div>
			</div>

			<div class="cs-body-absolute">
				<iframe id="affiliate-iframe" src="https://1kdailysystem.net/exclusive/"></iframe>
			</div>

			<div class="cs-footer-fixed">
				<div class="cs-row-flex">
					<div class="cs-profile-square cs-profile-square-r85px cs-margin-right-5px">
						<img src="sample_image_2.jpg" />
					</div>
					<div class="cs-bubble cs-margin-left-5px">
						<div class="cs-row-flex">
							<div class="cs-body-text-17px">Your DFY Funnel has been successfully saved.</div>
							<button class="cs-form-button-small cs-form-button-theme-color cs-margin-left-5px" type="button">6km9d</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
	<script type="text/javascript">
		function closeElement(){
			var closeElement = document.getElementById("close-element");

			closeElement.style.opacity = "0";

			function asd(){
				var closeElement = document.getElementById("close-element");

				closeElement.style.display = "none";
			}

			setTimeout(asd, 500);
		}
	</script>
</html>