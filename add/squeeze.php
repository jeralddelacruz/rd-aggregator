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
	if($DB->connect < 1){
		echo "Can't go on, DB not initialized.";
		exit;
	}
	
	// WEBSITE VARIABLE
	$res = $DB->query("SELECT setup_key, setup_val FROM {$dbprefix}setup ORDER BY setup_id");
	foreach($res as $row){
		$WEBSITE[$row["setup_key"]] = $row["setup_val"];
	}
	
	$passed_random_string = $_GET["s"];
	
	$campaign = $DB->info("pageb", "pageb_campaign_unique_identifier_squeeze = '{$passed_random_string}'");
	$exploded_exit_action = explode(", ", $campaign["pageb_campaign_exit_actions"]);
	
	if(isset($_POST['submit'])) {
		$platformValue = $campaign["pageb_campaign_integrations_platform_name"];
		$redirectUrl = $SCRIPTURL . "add/affiliate.php?s=" . $campaign["pageb_campaign_unique_identifier_affiliate"];
		$autoresponder = $DB->info("api", "user_id = '{$campaign["user_id"]}' AND platform = '$platformValue' ");
		$autoresponderData = json_decode($autoresponder['data']);
		// var_dump($platformValue, $autoresponder); die();
		if($platformValue == 'getresponse') {
	
			$campaignId = $campaign["pageb_campaign_integrations_list_name"];
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
	
			$listId = $campaign["pageb_campaign_integrations_list_name"];
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
	
			$tagId = $campaign["pageb_campaign_integrations_list_name"];
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
	
			$listId = $campaign["pageb_campaign_integrations_list_name"];
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
				$id = $campaign["pageb_campaign_integrations_list_name"];
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
				$listId = $campaign["pageb_campaign_integrations_list_name"];
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
		
			$id = $campaign["pageb_campaign_integrations_list_name"];
			$email = strip($_POST["email"]);
			
			// refresh token
			$refresh = refreshAccToken($autoresponderData->refresh_token, $client_id, $secret_key);
			// var_dump($refresh); die();
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
			
			$list_id = $campaign["pageb_campaign_integrations_list_name"];
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
			
			$listId = $campaign["pageb_campaign_integrations_list_name"];
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
		<title><?= $campaign["pageb_campaign_squeeze_headline"]; ?></title>
	
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
		<meta property="og:image" content="<?= $SCRIPTURL . "upload/{$campaign["user_id"]}/". $campaign["pageb_campaign_squeeze_image"]; ?>">
		
		<!-- Twitter -->
		<meta property="twitter:card" content="summary_large_image">
		<meta property="twitter:url" content="<?= $SCRIPTURL . "add/squeeze.php?s=" . $s; ?>">
		<meta property="twitter:title" content="<?= $WEBSITE["sitename"]; ?> Squeeze Page">
		<meta property="twitter:description" content="<?= $WEBSITE["sitename"]; ?> Description">
		<meta property="twitter:image" content="<?= $SCRIPTURL . "upload/{$campaign["user_id"]}/". $campaign["pageb_campaign_squeeze_image"]; ?>">
		
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
				--theme-color: <?= $campaign["pageb_campaign_theme_color"]; ?>;
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

			/* width */
			::-webkit-scrollbar {
				width: 0px;
			}

			/* Track */
			::-webkit-scrollbar-track {
				background: #f1f1f1;
			}

			/* Handle */
			::-webkit-scrollbar-thumb {
				background-color: var(--theme-color);
			}

			/* Handle on hover */
			::-webkit-scrollbar-thumb:hover {
				background: #555;
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

			.cs-headline-20px{
				font-family: var(--theme-font-roboto);
				font-size: 20px;
				font-weight: 400;
				line-height: normal;
				letter-spacing: normal;
				text-transform: normal;
				text-align: center;
				color: #333333;
				word-break: break-word;
			}

			.cs-headline-42px{
				font-family: var(--theme-font-roboto);
				font-size: 42px;
				font-weight: 700;
				line-height: normal;
				letter-spacing: normal;
				text-transform: normal;
				text-align: center;
				color: #333333;
				word-break: break-word;
			}

			.cs-subheadline-17px{
				font-family: var(--theme-font-roboto);
				font-size: 17px;
				font-weight: 400;
				line-height: normal;
				letter-spacing: normal;
				text-transform: normal;
				text-align: center;
				color: #333333;
				word-break: break-word;
			}

			.cs-subheadline-42px{
				font-family: var(--theme-font-roboto);
				font-size: 42px;
				font-weight: 700;
				line-height: normal;
				letter-spacing: normal;
				text-transform: normal;
				text-align: center;
				color: #333333;
				word-break: break-word;
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

			.cs-form-button{
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

				transition: background-color .2s, border-color .2s;
			}

			.cs-form-button-theme-color:hover{
				background-color: white;
				color: var(--theme-color) !important;
				border-color: white;
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

			.cs-pop-up-wrapper{
				margin: auto; padding: 0px;

				position: absolute;
				top: 0px; left: 0px;

				width: 100%; height: 100%;

				display: none;

				box-sizing: border-box;

				transition: opacity .3s;

				z-index: 99;
			}

			.cs-pop-up-full-page{
				margin: auto; padding: 0px;

				position: relative;

				width: 100%; height: 100%;

				display: flex;
				flex-direction: row;

				box-sizing: border-box;
			}

			.cs-image-container{
				margin: auto; padding: 0px;

				position: relative;

				width: 65%; height: 100%;

				display: block;

				box-sizing: border-box;
			}

			.cs-image{
				margin: auto; padding: 0px;

				position: relative;

				width: 100%; height: 100%;

				display: block;

				box-sizing: border-box;

				object-fit: cover;
			}

			.cs-content-container{
				margin: auto; padding: 0px;

				position: relative;

				width: 35%; height: 100%;

				display: flex;

				box-sizing: border-box;

				background-color: #333333;
			}

			.cs-content-super-center{
				margin: auto; padding: 0px;

				position: relative;

				width: auto; height: auto;

				display: block;

				box-sizing: border-box;
			}

			.cs-pop-up-close-button{
				margin: auto; padding: 0px;

				position: absolute;
				top: 20px; right: 5px;
				<?php if($campaign["pageb_campaign_exit_actions"] == $exploded_exit_action[0] . ", Pop-Up Video") : ?>
				top: 20px; right: 30.5%;
				<?php endif; ?>
				<?php if($campaign["pageb_campaign_exit_actions"] == $exploded_exit_action[0] . ", Pop-Up Two Buttons") : ?>
				top: 20px; right: 25.5%;
				<?php endif; ?>

				width: auto; height: auto;

				display: block;

				box-sizing: border-box;

				font-size: 35px;
				color: white;
				line-height: 0px;

				cursor: pointer;

				transition: color .2s;

				user-select: none;

				z-index: 99;
			}

			.cs-pop-up-close-button:hover{
				color: var(--theme-color);
			}

			.cs-pop-up-video{
				margin: auto; padding: 0px;

				position: relative;

				width: auto; height: auto;

				display: block;

				box-sizing: border-box;

				background-color: white;
			}

			.cs-pop-up-video-top-section iframe{
				width: 100%; height: 400px;
			}

			.cs-pop-up-video-bottom-section{
				padding: 30px;
			}

			.cs-pop-up-video-commissions{
				margin: auto; padding: 20px;

				position: relative;

				width: auto; height: auto;

				display: block;

				box-sizing: border-box;

				background-color: rgba(30, 30, 30, 1);
			}

			.cs-row-flex{
				margin: auto; padding: 0px;

				position: relative;

				width: auto; height: auto;

				display: flex;
				flex-direction: row;

				box-sizing: border-box;
			}

			#facebook-tools-fb-comments-button-toggle{
				margin: auto; padding: 5px;

				position: fixed;
				top: 10px; right: 10px;

				width: 50px; height: 50px;

				display: flex;

				box-sizing: border-box;

				border: 2px solid var(--theme-color);
				border-radius: 100%;

				background-color: rgba(240, 240, 240, 1);

				text-align: center;
				color: var(--theme-color);

				transition: border-color .2s, color .2s;

				cursor: pointer;

				z-index: 98;

				animation: slideLeft .5s;
			}

			#facebook-tools-fb-comments-button-toggle i{
				margin: auto;

				font-size: 25px;
			}

			#facebook-tools-fb-comments-button-toggle:hover{
				border-color: white;
				color: white;
			}

			@keyframes slideLeft{
				0%{transform: translate(100px, 0px) skewY(-30deg);}
				50%{transform: translate(-30px, 0px) skewY(30deg);}
				100%{transform: translate(0px, 0px) skewY(0deg);}
			}

			#facebook-tools-fb-comments-wrapper{
				margin: auto; padding: 0px;

				position: relative;

				width: 100%; height: 0px;

				display: block;

				box-sizing: border-box;

				background-color: #dfe3ee;

				transition: height .5s;
			}

			#facebook-tools-fb-comments-container{
				margin: auto; padding: 0px;

				position: relative;

				width: 35%; height: 0px;

				display: block;

				box-sizing: border-box;

				overflow: hidden;

				transition: height .5s;
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

			.cs-text-white{
				color: white !important;
			}

			.cs-width-40-percent{
				width: 40% !important;
			}

			.cs-width-50-percent{
				width: 50% !important;
			}

			.cs-width-80-percent{
				width: 80% !important;
			}

			a, a:active, a:visited, a:hover{
				text-decoration: none !important;
				color: white !important;
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

				<?php if($campaign["pageb_campaign_exit_actions"] == $exploded_exit_action[0] . ", Pop-Up Full Page") : ?>
				.cs-pop-up-wrapper{
					height: 200%;
				}
				<?php endif; ?>

				.cs-pop-up-full-page{
					flex-direction: column;
				}

				.cs-image-container{
					width: 100%; height: 65%;
				}

				.cs-content-container{
					width: 100%; height: 35%;
				}

				.cs-pop-up-video{
					width: 90% !important;
				}

				.cs-pop-up-video-bottom-section{
					padding: 10px;
				}

				<?php if($campaign["pageb_campaign_exit_actions"] == $exploded_exit_action[0] . ", Pop-Up Video") : ?>
				.cs-pop-up-close-button{
					top: 20px; right: 7%;
				}
				<?php endif; ?>

				.cs-pop-up-video-commissions{
					width: 90% !important;
				}

				<?php if($campaign["pageb_campaign_exit_actions"] == $exploded_exit_action[0] . ", Pop-Up Two Buttons") : ?>
				.cs-pop-up-close-button{
					top: 20px; right: 5.5%;
				}
				<?php endif; ?>

				.cs-form-button{
					margin-bottom: 10px;
					width: 100% !important;
				}

				.cs-row-flex{
					flex-direction: column;
				}
			}
		</style>
		<!-- FOR POP-UPS -->
		<?php if($campaign["pageb_campaign_exit_actions"] == "redirect") : ?>
		<script>
			document.addEventListener("mouseleave", function(e){
				var mouseLoc = e.pageY - document.body.scrollTop;
				
				if(mouseLoc <= 50){
					window.location.href = "<?php echo $SCRIPTURL . "add/affiliate.php?s=" . $campaign["pageb_campaign_unique_identifier_affiliate"]; ?>";
				}
			});
		</script>
		<?php elseif($campaign["pageb_campaign_exit_actions"] == $exploded_exit_action[0] . ", Pop-Up Full Page") : ?>
		<script>
			document.addEventListener("mouseleave", function(e){
				var mouseLoc = e.pageY - document.body.scrollTop;
				var cs_pop_up_wrapper = document.querySelectorAll(".cs-pop-up-wrapper");

				if(mouseLoc <= 50){
					cs_pop_up_wrapper[0].style.display = "block";

					function opacityDelay(){
						cs_pop_up_wrapper[0].style.opacity = 1;
					}

					setTimeout(opacityDelay, 100);
				}
			});
		</script>
		<?php elseif($campaign["pageb_campaign_exit_actions"] == $exploded_exit_action[0] . ", Pop-Up Video") : ?>
		<script>
			document.addEventListener("mouseleave", function(e){
				var mouseLoc = e.pageY - document.body.scrollTop;
				var cs_pop_up_wrapper = document.querySelectorAll(".cs-pop-up-wrapper");
				
				if(mouseLoc <= 50){
					cs_pop_up_wrapper[1].style.display = "block";
					cs_pop_up_wrapper[1].style.backgroundColor = "rgba(0, 0, 0, .8)";

					function opacityDelay(){
						cs_pop_up_wrapper[1].style.opacity = 1;
					}

					setTimeout(opacityDelay, 100);
				}
			});
		</script>
		<?php elseif($campaign["pageb_campaign_exit_actions"] == $exploded_exit_action[0] . ", Pop-Up Two Buttons") : ?>
		<script>
			document.addEventListener("mouseleave", function(e){
				var mouseLoc = e.pageY - document.body.scrollTop;
				var cs_pop_up_wrapper = document.querySelectorAll(".cs-pop-up-wrapper");
				
				if(mouseLoc <= 50){
					cs_pop_up_wrapper[2].style.display = "block";
					cs_pop_up_wrapper[2].style.backgroundColor = "rgba(100, 100, 100, .8)";

					function opacityDelay(){
						cs_pop_up_wrapper[2].style.opacity = 1;
					}

					setTimeout(opacityDelay, 100);
				}
			});
		</script>
		<?php endif; ?>

		<!-- FOR FACEBOOK TOOLS: FACEBOOK PIXEL CODE SNIPPET -->
		<?php if($campaign["pageb_campaign_facebook_tools_fb_pixel_code_snippet"]) : ?>
			<?= $campaign["pageb_campaign_facebook_tools_fb_pixel_code_snippet"]; ?>
		<?php endif; ?>
	</head>
	<body>
		<!-- FOR FACEBOOK TOOLS: FACEBOOK COMMENTS SDK -->
		<?php if($campaign["pageb_campaign_facebook_tools_fb_comments_sdk"]) : ?>
			<?= $campaign["pageb_campaign_facebook_tools_fb_comments_sdk"]; ?>
			
			<div id="facebook-tools-fb-comments-wrapper">
				<div id="facebook-tools-fb-comments-button-toggle"><i class="fa fa-comments"></i></div>
				<div id="facebook-tools-fb-comments-container">
					<!-- FOR FACEBOOK TOOLS: FACEBOOK COMMENTS CODE SNIPPET -->
					<?php if($campaign["pageb_campaign_facebook_tools_fb_comments_code_snippet"]) : ?>
						<?= $campaign["pageb_campaign_facebook_tools_fb_comments_code_snippet"]; ?>
					<?php endif; ?>
				</div>
				<br />
			</div>
		<?php endif; ?>

		<!-- FOR FACEBOOK TOOLS: FACEBOOK CHAT SDK AND CODE SNIPPET -->
		<?php if($campaign["pageb_campaign_facebook_tools_fb_chat_sdk_and_code_snippet"]) : ?>
			<?= $campaign["pageb_campaign_facebook_tools_fb_chat_sdk_and_code_snippet"]; ?>
		<?php endif; ?>

		<div class="cs-main-wrapper">
			<div class="cs-two-way">
				<div class="cs-two-way-image-wrapper">
					<img draggable="false" src="<?= $SCRIPTURL . "upload/{$campaign["user_id"]}/" . $campaign["pageb_campaign_squeeze_image"]; ?>" />
				</div>
				<div class="cs-two-way-content-wrapper">
					<div class="cs-headline-42px cs-margin-bottom-30px"><?= $campaign["pageb_campaign_squeeze_headline"]; ?></div>
					<div class="cs-subheadline-17px cs-margin-bottom-30px"><?= $campaign["pageb_campaign_squeeze_sub_headline"]; ?></div>
					<div class="cs-form-group cs-margin-bottom-30px">
						<?php if($campaign["pageb_campaign_integrations_platform_name"] == "html") : ?>
							<?= $campaign["pageb_campaign_integrations_raw_html"]; ?>
						<?php else : ?>
						<form method="POST" enctype="multipart/form-data">
							<input class="cs-form-control cs-form-control-70-percent cs-margin-bottom-30px" placeholder="Email" type="email" name="email" required />
							<button class="cs-form-button cs-form-button-70-percent cs-form-button-theme-color" type="submit" name="submit">
								<i class="fa fa-envelope-o"></i> <?= $campaign["pageb_campaign_squeeze_button_text"]; ?>
							</button>
						</form>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<?php $exit_action = $DB->info("exp", "exp_id = '{$exploded_exit_action[0]}'"); ?>

		<!-- POP-UP FULL PAGE -->
		<div class="cs-pop-up-wrapper">
			<div class="cs-pop-up-close-button">&times;</div>
			<div class="cs-pop-up-full-page">
				<div class="cs-image-container">
					<img class="cs-image" src="<?= "../upload/{$exit_action["user_id"]}/" . $exit_action["exp_image"]; ?>" />
				</div>
				<div class="cs-content-container">
					<div class="cs-content-super-center cs-width-80-percent">
						<div class="cs-headline-20px cs-margin-bottom-10px cs-text-white"><?= $exit_action["exp_headline"]; ?></div>
						<div class="cs-subheadline-42px cs-margin-bottom-30px cs-text-white"><?= $exit_action["exp_sub_headline"]; ?></div>
						<a class="cs-form-button cs-form-button-theme-color" href="<?= $exit_action["exp_button_url"]; ?>"><?= $exit_action["exp_button_text"]; ?></a>
					</div>
				</div>
			</div>
		</div>

		<!-- POP-UP VIDEO -->
		<div class="cs-pop-up-wrapper">
			<div class="cs-pop-up-close-button">&times;</div>
			<div class="cs-pop-up-video cs-width-40-percent">
				<div class="cs-pop-up-video-top-section">
					<?php
						if(strpos($exit_action["exp_video_url"], "youtu.be")){
							$forYouTubeEmbed = str_replace("youtu.be", "youtube.com/embed", $exit_action["exp_video_url"]);
						}
						
						if(strpos($exit_action["exp_video_url"], "watch")){
							$forYouTubeEmbed = str_replace("watch", "embed", $exit_action["exp_video_url"]);
						}

						if(strpos($exit_action["exp_video_url"], "watch?v=")){
							$forYouTubeEmbed = str_replace("watch?v=", "embed/", $exit_action["exp_video_url"]);
						}
					?>
					<iframe src="<?= $forYouTubeEmbed; ?>"></iframe>
				</div>
				<div class="cs-pop-up-video-bottom-section">
					<div class="cs-headline-42px cs-margin-bottom-20px"><?= $exit_action["exp_headline"]; ?></div>
					<div class="cs-subheadline-17px cs-margin-bottom-30px"><?= $exit_action["exp_sub_headline"]; ?></div>

					<a class="cs-form-button cs-width-50-percent cs-form-button-theme-color" href="<?= $exit_action["exp_button_url"]; ?>"><?= $exit_action["exp_button_text"]; ?></a>
				</div>
			</div>
		</div>

		<!-- Pop-Up Two Buttons -->
		<div class="cs-pop-up-wrapper">
			<div class="cs-pop-up-close-button">&times;</div>
			<div class="cs-pop-up-video-commissions cs-width-50-percent">
				<div class="cs-pop-up-video-commissions-top-section">
					<div class="cs-headline-42px cs-text-white cs-margin-bottom-20px"><?= $exit_action["exp_headline"]; ?></div>
					<div class="cs-subheadline-17px cs-text-white cs-margin-bottom-30px"><?= $exit_action["exp_sub_headline"]; ?></div>
				</div>
				<div class="cs-pop-up-video-commissions-bottom-section">
					<div style="margin: auto; text-align: center; max-width: 200px;">
						<img style="width: 100%;" class="cs-margin-bottom-30px" src="<?= "../upload/{$exit_action["user_id"]}/" . $exit_action["exp_image"]; ?>" />
					</div>
					<div class="cs-row-flex">
						<a class="cs-form-button cs-form-button-theme-color" href="<?= $exit_action["exp_button_url"]; ?>"><?= $exit_action["exp_button_text"]; ?></a>
						<a class="cs-form-button cs-form-button-theme-color" id="for_pop_up_two_button" href="#">No Thanks</a>
					</div>
				</div>
			</div>
		</div>
	</body>

	<script type="text/javascript">
		var cs_pop_up_close_button = document.querySelectorAll(".cs-pop-up-close-button");

		cs_pop_up_close_button[0].onclick = function(){
			var parent_of_this_button = cs_pop_up_close_button[0].parentNode;

			parent_of_this_button.style.opacity = 0;

			function displayNoneDelay(){
				parent_of_this_button.style.display = "none";
			}

			setTimeout(displayNoneDelay, 500);
		}

		cs_pop_up_close_button[1].onclick = function(){
			var parent_of_this_button = cs_pop_up_close_button[1].parentNode;

			parent_of_this_button.style.opacity = 0;

			function displayNoneDelay(){
				parent_of_this_button.style.display = "none";
			}

			setTimeout(displayNoneDelay, 500);
		}

		cs_pop_up_close_button[2].onclick = function(){
			var parent_of_this_button = cs_pop_up_close_button[2].parentNode;

			parent_of_this_button.style.opacity = 0;

			function displayNoneDelay(){
				parent_of_this_button.style.display = "none";
			}

			setTimeout(displayNoneDelay, 500);
		}

		var for_pop_up_two_button = document.getElementById("for_pop_up_two_button");

		for_pop_up_two_button.onclick = function(){
			var pop_up_wrapper = document.querySelectorAll(".cs-pop-up-wrapper");
			
			pop_up_wrapper[2].style.opacity = 0;

			function displayNoneDelay(){
				pop_up_wrapper[2].style.display = "none";
			}

			setTimeout(displayNoneDelay, 500);
		}
	</script>

	<!-- FOR FACEBOOK COMMENTS LOCAL SCRIPT -->
	<?php if($campaign["pageb_campaign_facebook_tools_fb_comments_code_snippet"]) : ?>
	<script>
		var fb_comments_button = document.getElementById("facebook-tools-fb-comments-button-toggle");
		fb_comments_button.onclick = function(){
			var fb_comments_wrapper = document.getElementById("facebook-tools-fb-comments-wrapper");
			var fb_comments_container = document.getElementById("facebook-tools-fb-comments-container");
			
			if(fb_comments_container.clientHeight == 0){
				fb_comments_wrapper.style.height = "220px";
				fb_comments_container.style.height = "210px";

				fb_comments_button.style.borderColor = "white";
				fb_comments_button.style.color = "white";
			}
			else{
				fb_comments_wrapper.style.height = "0px";
				fb_comments_container.style.height = "0px";

				fb_comments_button.style.borderColor = "<?= $campaign["pageb_campaign_theme_color"]; ?>";
				fb_comments_button.style.color = "<?= $campaign["pageb_campaign_theme_color"]; ?>";
			}
		}
	</script>
	<?php endif; ?>
</html>