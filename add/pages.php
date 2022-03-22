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
	
	$campaign_id = $_GET['campaigns_id'];
	// CAMPAIGN DETAILS
	if( $campaign_id ){
	    // CAMPAIGNS
	    $campaign = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE campaigns_id = '{$campaign_id}'")[0];
	    
	    // AFFILIATE LINKS
	    $decodedArticles = json_decode($campaign['included_article_pages_ids']);
	    
	    // ARTICLES
	    $decodedArticles = json_decode($campaign['included_article_pages_ids']);
	}

    // AUTO RESPONDER SUBMIT
	if(isset($_POST['submit'])) {
		$platformValue = $campaign["campaigns_integrations_platform_name"];
		$redirectUrl = "{$SCRIPTURL}add/webinar.php?pages_id={$campaign["included_webinar_page_id"]}&pages_type=webinar&campaigns_id={$campaign_id}";
		$autoresponder = $DB->info("api", "user_id = '{$campaign["user_id"]}' AND platform = '$platformValue' ");
		$autoresponderData = json_decode($autoresponder['data']);
		
		// var_dump($platformValue, $autoresponder); die();
		if($platformValue == 'getresponse') {
	
			$campaignId = $campaign["campaigns_integrations_list_name"];
			$email = strip($_POST["email"]);
			
			$send = addGREmailtoList($email, $campaignId, $autoresponderData->api_key);
			//$_SESSION['msg'] = $send['success'] ? 'Success!' : '';
			if($send['data']->error) {
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
			if($send['data']->error) {
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
			if($send['data']->error) {
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
			if($send['data']->error) {
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
			if($send['data']->error) {
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
				if($add['data']->error){
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
			
			if($send['data']->error){
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
	
				if (!$add['data']->error) {
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
			if(!$send['data']->error) {
				if($redirectUrl) {
					redirect($redirectUrl);
				}
				else {
					$_SESSION['msg'] = 'Success!' ;
				}
			}
	
		}
	}
	
    // Dummy Data ezprofitlogo.png, ezprofiticon.png
	$page_logo = $campaign['campaigns_logo'];
	$page_logo_text = $campaign['campaigns_title'];
	$articles = array();
	foreach($decodedArticles as $decodedArticle){
	    $articlesData = $DB->info("pages", "pages_id ='{$decodedArticle}'");
	    $temp_data = [
            "article_id"            => $articlesData['pages_id'],
            "article_name"          => $articlesData['pages_name'],
            "article_image"         => $articlesData['pages_image'],
            "article_title"         => htmlspecialchars_decode($articlesData['pages_menu_title']),
            "article_excerpt"       => htmlspecialchars_decode($articlesData['pages_excerpt']),
            "article_headline"      => htmlspecialchars_decode($articlesData['pages_headline']),
            "article_subheadline"   => htmlspecialchars_decode($articlesData['pages_introduction']),
            "article_button_text"   => "Read More"
        ];
        array_push( $articles, $temp_data );
	}

    $page = [
        "header_background_image"   => $campaign['campaigns_background_image'],
        "header_headline"           => htmlspecialchars_decode($campaign['campaigns_headline']),
        "header_subheadline"        => htmlspecialchars_decode($campaign['campaigns_body']),
        "header_button_text"        => $campaign['campaigns_button_text'],
        "header_extra_text"         => "I teach business owners, educators and entrepreneurs the profitable action steps for building a highly engaged email list, creating online training courses, and using online marketing strategies to sell with ease.",
        "theme_color"               => $campaign['campaigns_theme_color'],
        "theme_text_color"          => $campaign['campaigns_theme_text_color'],
        "theme_font"                => $campaign['campaigns_theme_font'],
        "campaigns_integrations_platform_name"                => $campaign['campaigns_integrations_platform_name'],
        "tab1"      => [
            "name"      => $campaign['campaigns_tab1'],
            "items"     => $campaign['included_tab1_resource_ids']
        ],
        "tab2"      => [
            "name"      => $campaign['campaigns_tab2'],
            "items"     => $campaign['included_tab2_resource_ids']
        ],
        "tab3"      => [
            "name"      => $campaign['campaigns_tab3'],
            "items"     => $campaign['included_tab3_resource_ids']
        ],
    ];
    
    $productsData = $DB->query("SELECT * FROM {$dbprefix}affiliate_links");
    $products = array();
    foreach( $productsData as $productData ){
        $tempData = [
            "product_id"                    => $productData['affiliate_links_id'],
            "product_name"                  => $productData['affiliate_links_product_name'],
            "product_image"                 => $productData['product_image'],
            "product_headline"              => htmlspecialchars_decode($productData['affiliate_links_product_subheadline']),
            "product_subheadline"           => htmlspecialchars_decode($productData['affiliate_links_content']),
            "product_button_text"           => $productData['affiliate_links_button_text'],
            "product_button_affiliate"      => $productData['affiliate_links_link_user'],
        ];
        array_push( $products, $tempData );
    }

    $popup = [
        "popup_image"       => $campaign['campaigns_modal_image'],
        "popup_headline"    => htmlspecialchars_decode($campaign['campaigns_modal_headline']),
        "popup_subheadline" => htmlspecialchars_decode($campaign['campaigns_modal_sub_headline']),
        "popup_button_text" => $campaign['campaigns_modal_button_text']
    ];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>EZPPAGES - <?= $campaign["campaigns_title"]; ?></title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" type="text/css" />
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <style>
            :root {
                --theme-color: <?= $page['theme_color']; ?>;
                --theme-text-color: <?= $page['theme_text_color']; ?>;
                --theme-font: <?= $page['theme_font']; ?>;
            }
        </style>
        <link href="css/styles.css" rel="stylesheet" />
        <link href="css/navigation.css" rel="stylesheet" />
        <link href="css/homepage.css" rel="stylesheet" />
        <link href="css/modal.css" rel="stylesheet" />
        <script src="js/resource-slider.js"></script>
        <style>
            header.masthead {
                background: url("../upload/<?= $campaign["user_id"]; ?>/<?= $page['header_background_image']; ?>") no-repeat center center;
                background-size: cover;
            }
        </style>
        
    </head>
    <body>
        <div id="mySidebar" class="sidebar">
            <?php foreach ($articles as $key => $value) { ?>
                <a href="article.php?article=<?= $value['article_id']; ?>&campaigns_id=<?= $campaign_id; ?>"><?= $value['article_title']; ?></a>
            <?php } ?>
            <a href="javascript:void(0)" class="btn btn-outline-secondary w-50 m-auto p-2 close-btn" onclick="closeNav()"><span>X </span> Close</a>
        </div>
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-dark theme-color">
            <a class="navbar-brand" href="pages.php?campaigns_id=<?= $campaign_id ?>"><?php if ( $page_logo ) { ?><img src="../upload/<?= $campaign["user_id"]; ?>/<?= $page_logo; ?>"><?php }else{ ?> <?= $page_logo_text; ?> <?php } ?></a>
            <button class="navbar-toggler" type="button" onclick="openNav()" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
          
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav ml-auto">
                <?php foreach ($articles as $key => $value) { ?>
                    <li class="nav-item">
                        <!-- <a class="nav-link" href="article.php?<?= $value['article_id']; ?>"><?= $value['article_name']; ?> <span class="sr-only">(current)</span></a> -->
                        <a class="nav-link" href="article.php?article=<?= $value['article_id']; ?>&campaigns_id=<?= $campaign_id; ?>"><?= $value['article_title']; ?></a>
                    </li>
                <?php } ?>
              </ul>
            </div>
          </nav>
        <!-- Masthead-->
        <header class="masthead">
            <div class="container position-relative">
                <div class="row justify-content-start">
                    <div class="col-xl-6 header-content">
                        <div class="text-left text-white">
                            <!-- Page heading-->
                            <h1 class="mb-4"><?= $page['header_headline']; ?></h1>
                            <h5 class="mb-4 lh-mid"><i><?= $page['header_subheadline']; ?></i></h5>
                            <form class="form-subscribe mb-4" id="contactForm" data-sb-form-api-token="API_TOKEN">
                                <button type="button" class="btn btn-primary btn-text-mid" id="headline-button" data-toggle="modal" data-target=".bd-example-modal-lg"><?= $page['header_button_text']; ?>
                              </button>                         
                            </form>
                            <!--<h6 class="mb-4"><?= $page['header_extra_text']; ?></h6>-->
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Sliders Tab -->
        <section class="slider-tab bg-light slider-tab-content">
            <div class="container-fluid p-0">
                <div class="row text-center">
                    <div class="col-sm-4 tab-menu active" id="tab-menu1" onclick="changeTab(1)">
                        <h4><?= $page['tab1']['name']; ?></h4>
                    </div>
                    <div class="col-sm-4 tab-menu" id="tab-menu2" onclick="changeTab(2)">
                     <h4><?= $page['tab2']['name']; ?></h4>   
                    </div>
                    <div class="col-sm-4 tab-menu" id="tab-menu3" onclick="changeTab(3)">
                     <h4><?= $page['tab3']['name']; ?></h4>   
                    </div>
                </div>
                <div class="main-container">
                    <div class="container custom-card-container">
                        <div class="tabs show" id="tab1">
                            <div class="resources-area flex-wrap">
                                <?php foreach (json_decode($page['tab1']['items']) as $key => $item) { 
                                    $product_data = [];
                                    foreach ($products as $key => $product) {
                                        if ( $product['product_id'] == $item) {
                                            $product_data = $product;
                                        }
                                    }
                                ?>
                                    <div class="card-container">
                                        <div class="custom-card-header">
                                            <div class="resource-image d-flex justify-content-center">
                                                <?php if( $product_data['product_image'] ){ ?>
                                                    <img src="../upload/<?= $campaign['user_id'] ?>/<?= $product_data['product_image']; ?>" alt="<?= $product_data['product_name']; ?>">
                                                <?php }else{ ?>
                                                    <img src="assets/img/no.jpg" alt="no image">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="card-content text-center p-4">
                                            <h5><?= $product_data['product_headline']; ?></h5>
                                            <p class="line-clamp-7"><?= $product_data['product_subheadline']; ?>
                                            </p>
                                            <a href="<?= $product_data['product_button_affiliate']; ?>" class="btn btn-primary w-50"><?= $product_data['product_button_text']; ?></a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="tabs" id="tab2">
                            <div class="resources-area flex-wrap">
                                <?php foreach (json_decode($page['tab2']['items']) as $key => $item) { 
                                    $product_data = [];
                                    foreach ($products as $key => $product) {
                                        if ( $product['product_id'] == $item) {
                                            $product_data = $product;
                                        }
                                    }
                                ?>
                                    <div class="card-container">
                                        <div class="custom-card-header">
                                            <div class="resource-image d-flex justify-content-center">
                                                <?php if( $product_data['product_image'] ){ ?>
                                                    <img src="../upload/<?= $campaign['user_id'] ?>/<?= $product_data['product_image']; ?>" alt="<?= $product_data['product_name']; ?>">
                                                <?php }else{ ?>
                                                    <img src="assets/img/no.jpg" alt="no image">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="card-content text-center p-4">
                                            <h5><?= $product_data['product_headline']; ?></h5>
                                            <p class="line-clamp-7"><?= $product_data['product_subheadline']; ?>
                                            </p>
                                            <a href="<?= $product_data['product_button_affiliate']; ?>" class="btn btn-primary w-50"><?= $product_data['product_button_text']; ?></a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="tabs" id="tab3">
                            <div class="resources-area flex-wrap">
                                <?php foreach (json_decode($page['tab3']['items']) as $key => $item) { 
                                    $product_data = [];
                                    foreach ($products as $key => $product) {
                                        if ( $product['product_id'] == $item) {
                                            $product_data = $product;
                                        }
                                    }
                                ?>
                                    <div class="card-container">
                                        <div class="custom-card-header">
                                            <div class="resource-image d-flex justify-content-center">
                                                <?php if( $product_data['product_image'] ){ ?>
                                                    <img src="../upload/<?= $campaign['user_id'] ?>/<?= $product_data['product_image']; ?>" alt="<?= $product_data['product_name']; ?>">
                                                <?php }else{ ?>
                                                    <img src="assets/img/no.jpg" alt="no image">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="card-content text-center p-4">
                                            <h5><?= $product_data['product_headline']; ?></h5>
                                            <p class="line-clamp-7"><?= $product_data['product_subheadline']; ?>
                                            </p>
                                            <a href="<?= $product_data['product_button_affiliate']; ?>" class="btn btn-primary w-50"><?= $product_data['product_button_text']; ?></a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>

        </section>
        <!-- Articles Section -->
        <section class="slider-tab bg-light slider-tab-content">
            <div class="container-fluid p-0">
                <div class="main-container bg-dirty">
                    <div class="container custom-card-container">
                        <div class="resources-area flex-wrap">
                            
                            <?php foreach ($articles as $key => $article) { 
                                $count = $key + 1;
                                $count = $count <= 9 ? "0".$count : $count;
                            ?>
                                <!-- CARD -->
                                <div class="lower-section-card-container">
                                    <div class="lower-sectionheader">
                                        <div class="lower-section-count"><span><?= $count ?></span></div>
                                        <div class="resource-image d-flex justify-content-center">
                                            <?php if( $article['article_image'] ){ ?>
                                                <img src="../upload/<?= $campaign['user_id']; ?>/<?= $article['article_image'] ?>" alt="">
                                            <?php }else{ ?>
                                                <img src="assets/img/no.jpg" alt="no image">
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="card-content text-center p-4">
                                        <h5><?= $article['article_headline'] ?></h5>
                                        <p class="line-clamp-7"><?= $article['article_excerpt'] ?>
                                        </p>
                                        <a href="article.php?article=<?= $article['article_id']; ?>&campaigns_id=<?= $campaign_id; ?>" class="btn btn-primary btn-block"><?= $article['article_button_text'] ?></a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal-->
        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content">
                <div class="modal-body">
                    <span class="close-icon" data-dismiss="modal">x</span>
                    <div class="row">
                        <div class="col-6">
                            <div class="img-wrapper">
                                <?php if( $popup['popup_image'] ){ ?>
                                    <img src="../upload/<?= $campaign['user_id']; ?>/<?= $popup['popup_image'] ?>" alt="">
                                <?php }else{ ?>
                                    <img src="assets/img/no.jpg" alt="no image">
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="content-wrapper">
                                <div class="modal-headline text-center mb-4"><h2><?= $popup['popup_headline']; ?></h2></div>
                                <div class="modal-subheadline text-center mb-3"><p><?= $popup['popup_subheadline']; ?></p></div>
                                <?php if($page['campaigns_integrations_platform_name'] != "html") { ?>
                                <div class="form-wrapper">
                                    <form method="POST" enctype="multipart/form-data">
                                        <!--<input type="text" name="firstname" placeholder="First Name" class="form-control mb-3">-->
                                        
                                        <input type="email" name="email" placeholder="Email" class="form-control mb-3">
                                        <button type="submit" name="submit" value="submit" class="btn btn-primary btn-block"><?= $popup['popup_button_text']; ?></button>
                                    </form>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    
                  </div>
              </div>
            </div>
        </div>
        <!-- Footer-->
        <footer class="footer theme-color">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 h-100 text-center my-auto">
                        <ul class="list-inline mb-2">
                            <?php foreach ($articles as $key => $value) { ?>
                                <li class="list-inline-item"><a href="article.php?article=<?= $value['article_id']; ?>&campaigns_id=<?= $campaign_id; ?>"><?= $value['article_title']; ?></a></li>
                            <?php } ?>
                        </ul>
                        <p class="text-muted small mb-4 mb-lg-0">&copy; Your Website 2021. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
        <!-- SHOW THE SHARE BUTTONS ONLY IF THE USER IS LOGGED IN -->
		<!-- SHARER -->
		<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-6098c6b417fcb02b"></script>
		<script type="text/javascript">
			var addthis_share = {
				url: "<?= "{$SCRIPTURL}add/pages.php?campaigns_id={$campaign['campaigns_id']}"; ?>",
				title: "<?= $campaign["campaigns_title"]; ?>",
				description: "<?= $campaign["campaigns_body"]; ?>",
				media: "<?= $SCRIPTURL ?>/upload/<?= $campaign["user_id"]; ?>/<?= $page['header_background_image']; ?>"
			}
		</script>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <!-- * *                               SB Forms JS                               * *-->
        <!-- * * Activate your form at https://startbootstrap.com/solution/contact-forms * *-->
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script>
            function openNav() {
              document.getElementById("mySidebar").style.width = "250px";
              document.getElementById("main").style.marginLeft = "250px";
            }
            
            function closeNav() {
              document.getElementById("mySidebar").style.width = "0";
              document.getElementById("main").style.marginLeft= "0";
            }
        </script>
        <script>
			document.addEventListener("mouseleave", function(e){
				var mouseLoc = e.pageY - document.body.scrollTop;
				var popup = document.querySelector("#headline-button");
				
				if(mouseLoc <= 50){
				    popup.click();

					function opacityDelay(){
						popup.style.opacity = 1;
					}

					setTimeout(opacityDelay, 100);
				}
			});
		</script>
    </body>
</html>
