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

	if($_GET["pages_id"]){
		$page = $DB->info("pages", "pages_id = '{$_GET["pages_id"]}'");
		$included_affiliate_link_collection = $DB->info("affiliate_links_collection", "affiliate_links_collection_title = '{$page["pages_included_affiliate_links_collection_id"]}' AND user_id = '{$page["user_id"]}'");
		$included_affiliate_link_ids = explode(", ", $included_affiliate_link_collection["affiliate_links_collection_included_affiliate_link_ids"]);
		$included_affiliate_link_ids = implode("', '", $included_affiliate_link_ids);
		$included_affiliate_link = $DB->query("SELECT * FROM {$dbprefix}affiliate_links WHERE affiliate_links_product_name IN ('{$included_affiliate_link_ids}') AND user_id = '{$page["user_id"]}'");
	}

	if($_GET["campaigns_id"]){
		$campaign = $DB->info("campaigns", "campaigns_id = '{$_GET["campaigns_id"]}'");
		$included_article_pages_ids = explode(", ", $campaign["included_article_pages_ids"]);
		$included_article_pages_ids = implode("', '", $included_article_pages_ids);
		$included_article_pages = $DB->query("SELECT * FROM {$dbprefix}pages WHERE pages_name IN ('{$included_article_pages_ids}') AND user_id = '{$campaign["user_id"]}'");
		$included_webinar_page = $DB->info("pages", "pages_name = '{$campaign["included_webinar_page_id"]}' AND user_id = '{$campaign["user_id"]}'");

		if($campaign["campaigns_theme_color"] == "red"){
			$theme_color = "danger";
		}
		elseif($campaign["campaigns_theme_color"] == "blue"){
			$theme_color = "primary";
		}
		elseif($campaign["campaigns_theme_color"] == "green"){
			$theme_color = "success";
		}
		elseif($campaign["campaigns_theme_color"] == "yellow"){
			$theme_color = "warning";
		}
	}

	if($theme_color == null || $theme_color == "null" || $theme_color == ""){
		$theme_color = "primary";
	}

	if(isset($_POST['submit'])) {
		$platformValue = $campaign["campaigns_integrations_platform_name"];
		$redirectUrl = "{$SCRIPTURL}add/pages.php?pages_id={$campaign["included_webinar_page_id"]}&pages_type=webinar&campaigns_id={$campaign["campaigns_id"]}";
		$autoresponder = $DB->info("api", "user_id = '{$campaign["user_id"]}' AND platform = '$platformValue' ");
		$autoresponderData = json_decode($autoresponder['data']);
		// var_dump($platformValue, $autoresponder); die();
		if($platformValue == 'getresponse') {
	
			$campaignId = $campaign["campaigns_integrations_list_name"];
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
	
			$listId = $campaign["campaigns_integrations_list_name"];
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
	
			$tagId = $campaign["campaigns_integrations_list_name"];
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
	
			$listId = $campaign["campaigns_integrations_list_name"];
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
				$id = $campaign["campaigns_integrations_list_name"];
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
				$listId = $campaign["campaigns_integrations_list_name"];
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
		
			$id = $campaign["campaigns_integrations_list_name"];
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
			
			$list_id = $campaign["campaigns_integrations_list_name"];
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
			
			$listId = $campaign["campaigns_integrations_list_name"];
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
		<title><?= ($_GET["campaigns_id"]) ? $campaign["campaigns_headline"] : "This is a preview page only! Go to campaigns if you want the full page setup."; ?></title>

		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<!-- BOOTSTRAP CDNS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	
		<!-- FONTAWESOME CDNs -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/v4-shims.css">
		
		<!-- GOOGLE FONTS CDNs -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,900" />
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open Sans:400,900" />
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:400,900" />

		<!-- STYLES -->
		<style type="text/css">
			body{
				background-color: gainsboro;

				/*background-image: url("<?= "{$SCRIPTURL}add/assets/img/bg_1.jpg"; ?>");
				background-blend-mode: overlay;
				background-size: cover;
				background-attachment: fixed;
				background-repeat: no-repeat;*/

				<?php if($campaign["campaigns_theme_font"]) :; ?>
				font-family: "<?= $campaign["campaigns_theme_font"]; ?>";
				<?php endif; ?>
			}

			/* Width */
			::-webkit-scrollbar{
				width: 5px;
			}

			/* Track */
			::-webkit-scrollbar-track{
				background: #f1f1f1;
			}

			/* Handle */
			::-webkit-scrollbar-thumb{
				background: var(<?= ($campaign["campaigns_theme_color"] == "red") ? "--danger" 
					: ($campaign["campaigns_theme_color"] == "blue") ? "--primary" 
					: ($campaign["campaigns_theme_color"] == "green") ? "--success" 
					: ($campaign["campaigns_theme_color"] == "yellow") ? "--warning" : ""; ?>);
			}

			/* Handle on hover */
			::-webkit-scrollbar-thumb:hover{
				background: #555;
			}

			#opt-in-image-container img{
				max-width: 120%;

				transform: translate(-70px, 0px);
			}

			#opt-in-close-button-container{
				margin-top: -30px;
			}

			#opt-in-close-button{
				margin: auto -15px auto auto;
				padding-bottom: 4px;

				border-radius: 100%;
				text-align: center;
				line-height: 0px;

				height: 30px; width: 30px;

				background-color: #333;
				color: white;
				opacity: 1;
				outline: none;
			}

			.navigation-top-opt-in-button{
				padding-left: 15px;
				padding-right: 15px;
			}

			.section-1{
				background-image: url("<?= "{$SCRIPTURL}upload/{$campaign["user_id"]}/{$campaign["campaigns_background_image"]}"; ?>");
				background-size: cover;
				background-attachment: fixed;
				background-repeat: no-repeat;
			}

			.cs-cards-buttons{
				position: absolute;
				bottom: 20px;
			}

			.modal-open{
				overflow-y: auto;
				padding-right: 0px !important;
			}

			.chunky{
				font-weight: 900;
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

			@media (max-width: 768px){
				#opt-in-image-container img{
					max-width: 100%;

					transform: translate(0px, 0px);
				}
			}
		</style>

		<!-- FOR FACEBOOK TOOLS: FACEBOOK PIXEL CODE SNIPPET -->
		<?php if($campaign["campaigns_facebook_tools_pixel_code_snippet"]) : ?>
			<?= $campaign["campaigns_facebook_tools_pixel_code_snippet"]; ?>
		<?php endif; ?>
	</head>
	<body>
		<!-- FOR FACEBOOK TOOLS: FACEBOOK COMMENTS SDK -->
		<?php if($campaign["campaigns_facebook_tools_comments_sdk"]) : ?>
			<?= $campaign["campaigns_facebook_tools_comments_sdk"]; ?>
			
			<div id="facebook-tools-fb-comments-wrapper">
				<div id="facebook-tools-fb-comments-button-toggle"><i class="fa fa-comments"></i></div>
				<div id="facebook-tools-fb-comments-container">
					<!-- FOR FACEBOOK TOOLS: FACEBOOK COMMENTS CODE SNIPPET -->
					<?php if($campaign["campaigns_facebook_tools_comments_code_snippet"]) : ?>
						<?= $campaign["campaigns_facebook_tools_comments_code_snippet"]; ?>
					<?php endif; ?>
				</div>
				<br />
			</div>
		<?php endif; ?>

		<!-- FOR FACEBOOK TOOLS: FACEBOOK CHAT SDK AND CODE SNIPPET -->
		<?php if($campaign["campaigns_facebook_tools_chat_sdk_and_code_snippet"]) : ?>
			<?= $campaign["campaigns_facebook_tools_chat_sdk_and_code_snippet"]; ?>
		<?php endif; ?>

		<!-- HEADER -->
		<header class="cs-header sticky-top">
			<nav class="navbar navbar-expand-md bg-dark navbar-dark">
				<!-- LOGO -->
				<a class="navbar-brand" href='<?= "/add/pages.php?campaigns_id={$campaign['campaigns_id']}" ?>'>
					<?php if($_GET["campaigns_id"]) : ?>
					<img style="width: 200px;" src="<?= $SCRIPTURL . "upload/{$campaign["user_id"]}/{$campaign["campaigns_logo"]}"; ?>" alt="Campaign Logo" />
					<?php else : ?>
					<img class="rounded" style="width: 200px;" src="<?= $SCRIPTURL . "add/assets/img/banner_default.png"; ?>" alt="Pages Logo" />
					<?php endif; ?>
				</a>

				<!-- RESPONSIVE TOGGLE -->
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
					<span class="navbar-toggler-icon"></span>
				</button>

				<!-- COLLAPSIBLE MENU -->
				<div class="collapse navbar-collapse" id="collapsibleNavbar">
					<ul class="navbar-nav ml-auto mr-0">
						<!-- PAGES MENU TITLE -->
						<?php if(!empty($_GET["pages_id"]) && empty($_GET["campaigns_id"])) : ?>
						<li class="nav-item">
							<a class="nav-link text-light" href="#"><?= $page["pages_menu_title"]; ?></a>
						</li>
						<?php elseif(!empty($_GET["campaigns_id"])) : ?>
						<?php foreach($included_article_pages as $included_article_page) : ?>
						<li class="nav-item">
							<a class="nav-link text-light" href="<?= "{$SCRIPTURL}add/pages.php?pages_id={$included_article_page["pages_id"]}&pages_type={$included_article_page["pages_type"]}&campaigns_id={$campaign["campaigns_id"]}"; ?>"><?= $included_article_page["pages_menu_title"]; ?></a>
						</li>
						<?php endforeach; ?>
						<?php endif; ?>
						<li class="nav-item d-flex align-items-center">
							<button class="btn btn-<?= $theme_color; ?> btn-sm btn-block rounded-pill text-uppercase navigation-top-opt-in-button" 
								data-toggle="modal" 
								data-target="#opt-in-modal" 
								type="button">Free Training</button>
						</li>
					</ul>
				</div>
			</nav>
		</header>

		<?php if(!empty($_GET["pages_type"])) : ?>
		<!-- CONTENT: IF PAGES TYPE -->
		<div class="container-fluid mt-5">
			<div class="col-md-8 mx-auto">
				<div class="card">
					<!-- PAGES IMAGE OR PAGES VIDEO URL -->
					<div class="row">
						<div class="col-md-12">
							<?php if($page["pages_type"] == "article") : ?>
							<img class="img-fluid" src="<?= $SCRIPTURL . "upload/{$page["user_id"]}/{$page["pages_image"]}"; ?>" alt="Pages Image" />
							<?php elseif($page["pages_type"] == "webinar") : ?>
							<?php
								// URL OPTIMIZATION: YOUTUBE
								if(strpos($page["pages_video_url"], "youtu.be")){
									$optimizedURL = str_replace("youtu.be", "youtube.com/embed" , $page["pages_video_url"]);
								}
								
								if(strpos($page["pages_video_url"], "watch")){
									$optimizedURL = str_replace("watch", "embed", $page["pages_video_url"]);
								}
								
								if(strpos($page["pages_video_url"], "watch?v=")){
									$optimizedURL = str_replace("watch?v=", "embed/", $page["pages_video_url"]);
								}

								// URL OPTIMIZATION: VIMEO
								if(strpos($page["pages_video_url"], "vimeo.com")){
									$optimizedURL = str_replace("vimeo.com", "player.vimeo.com/video", $page["pages_video_url"]);
								}
							?>
							<div class="progress" style="height: 50px;">
								<div class="progress-bar progress-bar-striped bg-<?= $theme_color; ?> progress-bar-animated text-uppercase" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"><b style="font-size: 27px;">Live Training In Progress Do Not Close The Window</b></div>
							</div>
							<div class="embed-responsive embed-responsive-16by9">
								<iframe class="embed-responsive-item" id="pages-video-url-preview" src="<?= $optimizedURL; ?>"></iframe>
							</div>
							<?php endif; ?>
						</div>
					</div>

					<!-- PAGE HEADLINE -->
					<div class="row">
						<div class="col-md-12 mt-4 px-5">
							<h2 class="text-uppercase chunky"><?= $page["pages_headline"]; ?></h2>
						</div>
					</div>

					<?php if($page["pages_type"] == "article") : ?>
					<!-- PAGE INTRODUCTION -->
					<div class="row">
						<div class="col-md-12 mt-3 px-5">
							<p><?= $page["pages_introduction"]; ?></p>
						</div>
					</div>
					<?php endif; ?>

					<?php if($page["pages_type"] == "article") : ?>
					<!-- AFFILIATE LINK: COLLECTION -->
					<div class="row">
						<div class="col-md-12 mt-3 px-5">
							<?php foreach($included_affiliate_link as $affiliate_link) : ?>
							<div class="container-fluid mb-4 px-0">
								<h4 class="chunky"><?= $affiliate_link["affiliate_links_product_subheadline"]; ?></h4>
								<p><?= $affiliate_link["affiliate_links_content"]; ?></p>
								<a class="btn btn-sm btn-link" href="<?= $affiliate_link["affiliate_links_link_user"]; ?>">Related: <?= $affiliate_link["affiliate_links_button_text"]; ?></a>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
					<?php elseif($page["pages_type"] == "webinar") : ?>
					<div class="row mb-5">
						<div class="col-md-12 mt-3 px-5 text-center">
							<h4 class="text-<?= $theme_color; ?>">Extremely Limited Time Offer!</h4>
							<p class="text-center">100% Money Back Guarantee. Only 100 spots available. This <b>WILL</b> sell out fast.</p>

							<a class="btn btn-lg bg-<?= $theme_color; ?> text-uppercase text-white" href="<?= $page["pages_affiliate_link_webinar"]; ?>">YES! I DESERVE THIS! GIVE ME ACCESS NOW!</a>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php else : ?>
		<!-- CONTENT: IF HOME -->
		<div class="container-fluid px-0" style="overflow: hidden;">
			<section class="section-1 p-5">
				<div class="col-md-10 ml-5 mb-5">
					<h1 class="text-uppercase text-<?= $campaign["campaigns_headline_alignment"]; ?> chunky"><?= $campaign["campaigns_headline"]; ?></h1>
				</div>

				<div class="col-md-5 ml-5">
					<p class="text-<?= $campaign["campaigns_body_alignment"]; ?>" style="font-size: 30px;"><?= $campaign["campaigns_body"]; ?></p>
				</div>

				<div class="col-md-5 ml-5 text-<?= $campaign["campaigns_body_alignment"]; ?>">
					<button class="btn btn-<?= $theme_color; ?> btn-lg" type="button" data-toggle="modal" data-target="#opt-in-modal" ><?= $campaign["campaigns_button_text"]; ?></button>
				</div>
			</section>

			<?php if(!empty($included_article_pages)) : ?>
			<section class="section-2">
				<div class="row">
					<?php foreach($included_article_pages as $included_article_page) : ?>
					<div class="col-md-3 p-5 bg-white" style="box-shadow: 0px 10px 20px #333;">
						<h3 class="text-uppercase chunky"><?= $included_article_page["pages_name"]; ?></h3>

						<p class="text-muted" style="font-size: 20px;"><?= $included_article_page["pages_excerpt"]; ?></p>

						<a class="btn btn-<?= $theme_color; ?> cs-cards-buttons" href="<?= "{$SCRIPTURL}add/pages.php?pages_id={$included_article_page["pages_id"]}&pages_type={$included_article_page["pages_type"]}&campaigns_id={$campaign["campaigns_id"]}"; ?>">Read More</a>
					</div>
					<?php endforeach; ?>
					<div class="col-md-3 p-5 bg-white" style="box-shadow: 0px 10px 20px #333;">
						<h3 class="text-uppercase chunky"><?= $included_webinar_page["pages_name"]; ?></h3>

						<p class="text-muted" style="font-size: 20px;"><?= $included_webinar_page["pages_excerpt"]; ?></p>

						<button class="btn btn-<?= $theme_color; ?> cs-cards-buttons" type="button" data-toggle="modal" data-target="#opt-in-modal" >Free Training</button>
					</div>
				</div>
			</section>
			<?php endif; ?>
		</div>

		<section class="section-3" style="margin-top: 150px;">
			<h1 class="text-center text-uppercase chunky">ARE YOU READY TO GET A HEADSTART ON YOUR AFFILIATE MARKETING?</h1>
			<p class="text-center text-muted" style="font-size: 30px;">Follow these 4 Steps Below:</p>

			<div class="col-md-8 mx-auto bg-white mt-5">
				<?php $counter = 0; ?>
				<?php foreach($included_article_pages as $included_article_page) : ?>
				<div class="col-md-10 mx-auto p-2 bg-white text-center">
					<h3 class="text-uppercase chunky">Step <?= ++$counter; ?></h3>

					<p class="text-muted" style="font-size: 20px;"><?= $included_article_page["pages_excerpt"]; ?></p>

					<a class="btn btn-<?= $theme_color; ?>" href="<?= "{$SCRIPTURL}add/pages.php?pages_id={$included_article_page["pages_id"]}&pages_type={$included_article_page["pages_type"]}&campaigns_id={$campaign["campaigns_id"]}"; ?>">Read More</a>
				</div>
				<?php endforeach; ?>
				<div class="col-md-10 mx-auto p-2 bg-white text-center">
					<h3 class="text-uppercase chunky">Step <?= $counter + 1; ?></h3>

					<p class="text-muted" style="font-size: 20px;"><?= $included_webinar_page["pages_excerpt"]; ?></p>

					<button class="btn btn-<?= $theme_color; ?>" type="button" data-toggle="modal" data-target="#opt-in-modal" >Free Training</button>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<!-- FOOTER -->
		<footer class="cs-footer mt-5 bg-dark">
			<nav class="navbar navbar-expand-md bg-dark navbar-dark">
				<!-- LOGO -->
				<a href="navbar-brand" href="#">
					<?php if($_GET["campaigns_id"]) : ?>
					<img style="width: 200px;" src="<?= $SCRIPTURL . "upload/{$campaign["user_id"]}/{$campaign["campaigns_logo"]}"; ?>" alt="Campaign Logo" />
					<?php else : ?>
					<img class="rounded" style="width: 200px;" src="<?= $SCRIPTURL . "add/assets/img/banner_default.png"; ?>" alt="Pages Logo" />
					<?php endif; ?>
				</a>

				<ul class="navbar-nav ml-auto mr-0">
					<li class="nav-item">
						<a class="nav-link text-light text-uppercase small" href="#">Privacy Policy</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-light text-uppercase small" href="#">Terms & Conditions</a>
					</li>
				</ul>
			</nav>
		</footer>

		<!-- OPT-IN CONFIRMATION MODAL -->
		<div class="modal fade" id="opt-in-modal">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<!-- <div class="modal-header">
						<h4 class="modal-title">Opt-In Modal</h4>
						<button class="close" type="button" data-dismiss="modal">&times;</button>
					</div> -->
					<div class="modal-body text-center">
						<div class="row" id="opt-in-close-button-container">
							<button class="close" id="opt-in-close-button" type="button" data-dismiss="modal">&times;</button>
						</div>

						<div class="progress mt-2" style="height: 30px;">
							<div class="progress-bar text-uppercase text-dark font-weight-bold bg-<?= $theme_color; ?>" role="progressbar" style="width: 50%; font-size: 20px;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">50% Complete</div>
						</div>

						<h1 class="text-uppercase mt-3" style="font-size: 25px;"><?= $campaign["campaigns_modal_headline"]; ?></h1>

						<div class="row mt-3">
							<div class="col-md-5" id="opt-in-image-container">
								<img src="<?= "{$SCRIPTURL}add/assets/img/pages_opt_in_image.png" ?>" />
							</div>
							<div class="col-md-7">
								<form method="POST" enctype="multipart/form-data">
									<div class="form-group mt-5">
										<input class="form-control form-control-lg" placeholder="Email Address" type="text" name="email" />
									</div>

									<button class="btn btn-<?= $theme_color; ?> btn-lg btn-block text-uppercase font-weight-bold" type="submit" name="submit" value="submit"><?= $campaign["campaigns_modal_button_text"]; ?></button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- FOR FACEBOOK COMMENTS LOCAL SCRIPT -->
		<?php if($campaign["campaigns_facebook_tools_comments_code_snippet"]) : ?>
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
	</body>
</html>