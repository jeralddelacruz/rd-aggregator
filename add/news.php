<?php
    // include('../cache_solution/top-cache-v2.php');
    
    set_time_limit(0);
	error_reporting(0);
	session_start();
	
	// ============== IMPORTANT DIRECTORY ============== //
	// including the sys files being able to connect DB
    include(dirname(__FILE__) . "/../sys/class.db.php");
    include(dirname(__FILE__) . "/../sys/config.php");
    include(dirname(__FILE__) . "/../sys/func.php");
    include(dirname(__FILE__) . "/../inc/simple_html_dom_v2.php");
    
	
	$DB = new db($dbhost, $dbuser, $dbpass, $dbname);
	$DB->connect();
	if($DB->connect < 1){
		echo "Can't go on, DB not initialized.";
		exit;
	}
	
	// ============== WEBSITE VARIABLE ============== //
	$res = $DB->query("SELECT setup_key, setup_val FROM {$dbprefix}setup ORDER BY setup_id");
	foreach($res as $row){
		$WEBSITE[$row["setup_key"]] = $row["setup_val"];
	}
	
	// ============================ //
	// === SUBDOMAIN VALIDATION === //
	// ============================ //
	$serverName = $_SERVER["SERVER_NAME"];
    $serverName1 = explode( ".com", $serverName )[0];
    $serverName2 = explode(".", $serverName1);
    
	// check if the url is main 
	$is_subdomain_exist = false;
	$is_maindomain = false;
	if( $serverName2[0] !== $APPNAME ){
	    // if has subdomain then check if subdomain is existing
	    $user_subdomain = $DB->info("user_subdomain", "subdomain_name = '{$serverName2[0]}' AND subdomain_status = 1");
	    if( $user_subdomain ){
	        $is_subdomain_exist = true;
	    }else{
	        $is_subdomain_exist = false;
	    }
	}else{
	    $is_maindomain = true;
	}
	
	$and_query = "";
	if( $user_subdomain ){
	    $subdomain_id = $user_subdomain["subdomain_id"];
	    $and_query = " AND subdomain_id = '{$subdomain_id}'";
	}else{
	    $and_query = " AND subdomain_id = 0";
	}
	
	include("./includes/query_data.php");
	include("./includes/responder.php");
	
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/85911a66a7.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
    <link href="./css/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="./includes/css-popup-widget.css" />
	<script type="text/javascript" src="./includes/js-popup-widget.js"></script>
    <title><?= $newTitle; ?></title>
    
    <style>
        .jumbotron {
            background-image: url('../upload/<?= $campaign['user_id'] ?>/<?= $bannerImage; ?>');
            background-size: cover;
            background-position: 50% 50%;
            border-radius: 0;
        }
        .side-ads-container{
            background-image: url('../upload/<?= $campaign['user_id'] ?>/<?= $sidebarAdsData['ads_image']; ?>');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            border-radius: 0;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-banner-ads .link .container{
            background-image: url('../upload/<?= $campaign['user_id'] ?>/<?= $bannerAdsData['ads_image']; ?>');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            border-radius: 0;
            min-height: 120px;
            height: auto;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .navbar-brand {
            display: inline-block;
            margin-right: 1rem;
            font-size: 1.7rem;
            font-weight: 700;
            line-height: inherit;
            white-space: nowrap;
            padding: .8rem 1rem;
            background-color: <?= $campaign['campaigns_theme_text_color'] ?>;
        }
        
        .navbar-expand-md .navbar-nav .nav-link {
            padding-right: 1rem;
            padding-left: 1rem;
            color: <?= $campaign['campaigns_theme_text_color'] ?> !important;
        }

        .icons-container div a{
            font-size: 20px;
            padding: 10px;
            color: <?= $campaign['campaigns_theme_text_color'] ?> !important;
        }

        .search-container i{
            font-size: 20px;
            padding: 10px;
            color: <?= $campaign['campaigns_theme_text_color'] ?> !important;
        }

        .shadow {
            text-shadow: <?= $campaign['campaigns_theme_color'] ?> 0.5px 0 2px;
        }
        
        .navbar-light .navbar-brand {
            color: <?= $campaign['campaigns_theme_color']; ?>;
        }
        
        .navbar-light .navbar-brand:focus, .navbar-light .navbar-brand:hover {
            color: <?= $campaign['campaigns_theme_color']; ?>;
        }
        
        .bg-theme-color {
            background-color: <?= $campaign['campaigns_theme_color']; ?> !important;
        }
        
        .theme-text-color{
            color: <?= $campaign['campaigns_theme_text_color'] ?> !important;
        }
        
        .btn-primary, .btn-primary:hover{
            border-color: <?= $campaign['campaigns_theme_color']; ?> !important;
        }
        
        .news-item-container{
            background-color: <?= $campaign['campaigns_theme_bg_color']; ?>;
            margin-bottom: 8px;
            border: 1px solid <?= $campaign['campaigns_theme_border_color']; ?>;
        }
        h5, p {
            color: <?= $campaign['campaigns_theme_text_color']; ?> !important;
        }
        main {
            background-color: <?= $campaign['campaigns_theme_feed_color']; ?>;
        }
    </style>
</head>

<body>
    <?php if(count($campaign) > 1){ ?>
        <nav class="navbar navbar-expand-md navbar-light fixed-top bg-theme-color">
            <div class="container">
                <a class="navbar-brand" href="news.php?campaigns_id=<?= $campaignID ?>"><?= strtoupper($newTitle); ?></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
                    aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
    
                <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                    <ul class="navbar-nav mr-auto">
                        <?php for ($i=0; $i < $numberOfMenu ; $i++) { ?>
                            <li class="nav-item active">
                                <a class="nav-link" href="news.php?campaigns_id=<?= $campaignID ?>&category=<?= $categories[$i]['key']; ?>"><?= strtoupper($categories[$i]['name']); ?> <span class="sr-only">(current)</span></a>
                            </li>
                        <?php } ?>
                        <?php if( count( $categories ) > $numberOfMenu ){ ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">MORE</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                                <?php for ($i=$numberOfMenu; $i < count( $categories ) ; $i++) { ?>
                                    <a class="dropdown-item" href="news.php?campaigns_id=<?= $campaignID ?>&category=<?= $categories[$i]['key']; ?>"><?= strtoupper($categories[$i]['name']); ?></a>
                                <?php } ?>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>
                    <form class="form-inline my-2 my-lg-0 icons-container">
                        <div>
                            <a href="<?= $facebookLink; ?>" class="<?= $facebookLink == '' ? 'hide' : '' ?>"><i class="fab fa-facebook-f"></i></a>
                            <a href="<?= $twitterLink; ?>" class="<?= $twitterLink == '' ? 'hide' : '' ?>"><i class="fab fa-twitter"></i></a>
                            <a href="<?= $instagramLink; ?>" class="<?= $instagramLink == '' ? 'hide' : '' ?>"><i class="fab fa-instagram"></i></a>
                            <a href="<?= $youtubeLink; ?>" class="<?= $youtubeLink == '' ? 'hide' : '' ?>"><i class="fab fa-youtube"></i></a>
                        </div>
                        <div class="search-container">
                            <form method="POST">
                                <input class="form-control mr-sm-2 hide" id="campaign_id" type="hidden" name="campaigns_id" value="<?= $campaignID; ?>" placeholder="Search..." aria-label="Search">
                                <input class="form-control mr-sm-2 hide" id="search-input" type="text" name="search" placeholder="Search..." aria-label="Search">
                            </form>
                            <i class="fa fa-search" onclick="searchInputAction()"></i>
                        </div>
                    </form>
                </div>
            </div>
        </nav>
        <?php if (!$isSearch && !$hasNewsPost && !$isViewingCategory){ ?>
            <main role="main">
                
                <!-- Main jumbotron for a primary marketing message or call to action -->
                <div class="jumbotron hero-banner">
                    <div class="container">
                        <h1 class="display-3 text-center text-light font-weight-bold shadow hero-title"><?= strtoupper($bannerHeroTitle); ?></h1>
                    </div>
                </div>
                <div class="container">
                    <!-- Example row of columns -->
                    <div class="row">
                        <div class="col-md-3 pr-1 pl-1">
                            <h5 class="text-center font-weight-bold">Featured</h5>
                            <?php 
                                $p = 0;
                                foreach ($newsData as $key => $newsDataItem) {
                                if ($newsDataItem['status'] == 'Featured') { 
                                    $p++;
                                    if( $p < $minNumber ){
                                        $newsItemImage = $newsDataItem['image'];
                                        
                                        $decodedImage = json_decode($newsItemImage);
                                        $getRandIndex = rand(0, count($decodedImage)-1);
                                        $news_images = $decodedImage[$getRandIndex];
                                        
                                        if( $news_images == "" && $newsDataItem['uploaded_image'] == "" ){
                                            $news_image = "assets/img/no.jpg";
                                        }else{
                                            // echo $news_images;
                                            $news_image = $news_images != "" ? $news_images : json_decode($newsDataItem['uploaded_image'])[0];
                                        }
                            ?>
                                <a href="news.php?campaigns_id=<?= $campaignID ?>&news=<?= $newsDataItem['news_id']; ?>">
                                    <div class="col-md-12 p-0 news-item-container">
                                        <img src="<?= $news_image ?>" alt="<?= $news_image ?>">
                                        <div class="news-detail pl-2 pr-2 pb-2">
                                            <!--<a href="<?= $newsDataItem['link']; ?>" class="news-item-category"><?= $newsDataItem['status']; ?></a>-->
                                            <!--<a href="<?= $newsDataItem['link']; ?>" class="news-item-details">-->
                                            <!--    <p><?= $newsDataItem['title']; ?></p>-->
                                            <!--</a>-->
                                            <h2>
                                                <a href="news.php?campaigns_id=<?= $campaignID ?>&news=<?= $newsDataItem['news_id']; ?>" class="news-item-category"><?= strtoupper($newsDataItem['status']); ?></a>
                                                <a href="news.php?campaigns_id=<?= $campaignID ?>&news=<?= $newsDataItem['news_id']; ?>" class="news-item-details">
                                                    <p><?= $newsDataItem['title']; ?></p>
                                                </a>
                                            </h2>
                                        </div>
                                    </div>
                                </a>
                            <?php } } } ?>
                        </div>
                        <div class="col-md-6 pr-1 pl-1">
                            <h5 class="text-center font-weight-bold">Trending</h5>
                            <?php 
                                $o = 0;
                                foreach ($newsData as $key => $newsDataItem) { 
                                
                                if ($newsDataItem['status'] == 'Trending') { 
                                    $o++;
                                    if( $o < $totalTrendingCount ){
                                        $newsItemImage = $newsDataItem['image'];
                                        
                                        $decodedImage = json_decode($newsItemImage);
                                        
                                        $uploadedImage = $newsDataItem['uploaded_image'];
                                        $isManual = false;
                                        if( count($decodedImage) <= 0 ){
                                            $decodedImage = json_decode($uploadedImage);
                                            $isManual = true;
                                        }
                                        
                                        $getRandIndex = rand(0, count($decodedImage)-1);
                                        $news_images = $decodedImage[$getRandIndex];
                                        
                                        if( $news_images == "" && $newsDataItem['uploaded_image'] == "" ){
                                            $news_image = "assets/img/no.jpg";
                                        }else{
                                            $news_image = $news_images != "" ? $news_images : json_decode($newsDataItem['uploaded_image'])[0];
                                            // if( $isManual ){
                                            //     $news_image = "assets/img/".$news_images;
                                            // }else{
                                            //     $news_image = $news_images != "" ? $news_images : json_decode($newsDataItem['uploaded_image'])[0];
                                            // }
                                            
                                        }
                            ?>
                            <a href="news.php?campaigns_id=<?= $campaignID ?>&news=<?= $newsDataItem['news_id']; ?>">
                                <div class="col-md-12 p-0 news-item-container">
                                    <img class="trending-img" src="<?= $news_image ?>" alt="<?= $news_image ?>">
                                    <div class="news-detail pl-2 pr-2 pb-2">
                                        <h2>
                                            <a href="news.php?campaigns_id=<?= $campaignID ?>&news=<?= $newsDataItem['news_id']; ?>" class="news-item-category"><?= strtoupper($newsDataItem['status']); ?></a>
                                            <a href="news.php?campaigns_id=<?= $campaignID ?>&news=<?= $newsDataItem['news_id']; ?>" class="news-item-details">
                                                <p><?= $newsDataItem['title']; ?></p>
                                            </a>
                                        </h2>
                                    </div>
                                </div>
                            </a>
                            <?php } } } ?>
                        </div>
                        <div class="col-md-3 pr-1 pl-1">
                            <h5 class="text-center font-weight-bold">New</h5>
                            <?php 
                                $i = 0;
                                foreach ($newsData as $key => $newsDataItem) {
                                
                                if ($newsDataItem['status'] == 'New') { 
                                    $i++;
                                    if( $i < $minNumber ){
                                        $newsItemImage = $newsDataItem['image'];
                                        
                                        $decodedImage = json_decode($newsItemImage);
                                        $getRandIndex = rand(0, count($decodedImage)-1);
                                        $news_images = $decodedImage[$getRandIndex];
                                        
                                        if( $news_images == "" && $newsDataItem['uploaded_image'] == "" ){
                                            $news_image = "assets/img/no.jpg";
                                        }else{
                                            // echo $news_images;
                                            $news_image = $news_images != "" ? $news_images : json_decode($newsDataItem['uploaded_image'])[0];
                                            
                                        }
                            ?>
                            <a href="news.php?campaigns_id=<?= $campaignID ?>&news=<?= $newsDataItem['news_id']; ?>">
                                <div class="col-md-12 p-0 news-item-container">
                                    <img src="<?= $news_image ?>" alt="<?= $news_image ?>">
                                    <div class="news-detail pl-2 pr-2 pb-2">
                                        <h2>
                                            <a href="news.php?campaigns_id=<?= $campaignID ?>&news=<?= $newsDataItem['news_id']; ?>" class="news-item-category"><?= strtoupper($newsDataItem['status']); ?></a>
                                            <a href="news.php?campaigns_id=<?= $campaignID ?>&news=<?= $newsDataItem['news_id']; ?>" class="news-item-details">
                                                <p><?= $newsDataItem['title']; ?></p>
                                            </a>
                                        </h2>
                                    </div>
                                </div>
                            </a>
                            <?php } } } ?>
                        </div>
                    </div>
        
                    <hr>
        
                </div> <!-- /container -->
            
            </main>
            <!-- SHOW THE SHARE BUTTONS ONLY IF THE USER IS LOGGED IN -->
        	<!-- SHARER -->
        	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-6098c6b417fcb02b"></script>
        	<script type="text/javascript">
        		var addthis_share = {
        			url: "<?= "{$SCRIPTURL}add/news.php"; ?>",
        			title: "<?= $campaign["campaigns_title"]; ?>",
        			description: "<?= $campaign["campaigns_body"]; ?>",
        			media: "<?= $SCRIPTURL ?>/upload/<?= $campaign["user_id"]; ?>/<?= $page['header_background_image']; ?>"
        		}
        	</script>
        <!-- Search Content -->
        <?php }else{ ?>
            <main>
                <!-- Main jumbotron for a primary marketing message or call to action -->
                <?php if( $hasNewsPost ){ ?>
                    <div class="jumbotron-ads hero-banner-ads">
                        <a href="<?= $bannerAdsData['ads_url'] ?>" class="link">
                            <div class="container">
                                <!--<h1 class="display-3 text-center text-light font-weight-bold"><?= $bannerAdsData['ads_name']; ?></h1>-->
                            </div>
                        </a>
                    </div>
                <?php } ?>
                <div class="container <?= $hasNewsPost ? 'main-container-ads' : 'main-container' ?>">
                    <div class="row">
                        <div class="col-md-8 mt-3 pr-5">
                            <?php if( !$hasNewsPost ){ ?>
                                <?php if( !$isViewingCategory ){ ?>
                                    <h4 class="font-weight-bold border-bottom pb-2 mb-4">Search Results for "<?= $searchValue; ?>"</h4>
                                <?php }else{ ?>
                                    <h4 class="font-weight-bold"><?= $cagetoryLabel; ?></h4>
                                    <p class="border-bottom pb-2"><?= $cagetoryDesc ?></p>
                                <?php } ?>
                                
                                <?php if( count( $searchData ) > 0 ): ?>
                                    <div class="search-found">
                                        <div class="row">
                                            <?php foreach ($searchData as $key => $newsDataItem) { 
                                                $newsItemImage = $newsDataItem['image'];
                                                
                                                $decodedImage = json_decode($newsItemImage);
                                                $getRandIndex = rand(0, count($decodedImage)-1);
                                                $news_images = $decodedImage[$getRandIndex];
                                                
                                                if( $news_images == "" && $newsDataItem['uploaded_image'] == "" ){
                                                    $news_image = "assets/img/no.jpg";
                                                }else{
                                                    $news_image = $news_images != "" ? $news_images : json_decode($newsDataItem['uploaded_image'])[0];
                                                }
                                            ?>
                                                <div class="col-md-12 mb-3">
                                                    <div class="row pb-3 mb-2">
                                                        <div class="col search-result-item pr-0">
                                                            <img src="<?= $news_image ?>" alt="">
                                                        </div>
                                                        <div class="col-8">
                                                            <a href="news.php?campaigns_id=<?= $campaignID ?>&news=<?= $newsDataItem['news_id']; ?>"><h4 class="font-weight-bold"><?= $newsDataItem['title']; ?></h4></a>
                                                            <p><?= $newsDataItem['date']; ?> | <?= $newsDataItem['author']; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="no-result-found text-center">
                                        <h3 class="font-weight-bold">
                                            Sorry, this content isn't available right now.
                                        </h3>
                                    </div>
                                <?php endif; ?>
                            <?php }else{ ?>
                                <div class="post-content-header-container">
                                    <h1><?= $newsData['news_title']; ?></h1>
                                    <p class="author text-muted">BY : <?= $newsData['news_author']; ?> : <?= $newsData['news_published_date']; ?></p>
                                    <?php
                                        $newsItemImage = $newsData['news_image'];
                                        
                                        if( $newsItemImage == "manual" ){
                                            $html = file_get_html( $newsData['news_link'] );
                                            $newsItemImage = fetchImage( $html, $newsDataItem['news_id'] );
                                        }
                                        
                                        $decodedImage = json_decode($newsItemImage);
                                        $getRandIndex = rand(0, count($decodedImage)-1);
                                        $news_images = $decodedImage[$getRandIndex];
                                        if( $news_images == "" && $newsData['uploaded_image'] == "" ){
                                            $news_image = "assets/img/no.jpg";
                                        }else{
                                            $news_image = $news_images != "" ? $news_images : json_decode($newsData['uploaded_image'])[0];
                                        }
                                    ?>
                                    <img class="news-img mb-3" src="<?= $news_image ?>">
                                    
                                    <div class="call-to-action-container mt-3 mb-3">
                                        <h3 class="text-center pl-3 pr-3 mb-4 font-weight-bold"><?= $callToActionTitle ?></h3>
                                        <a href="<?= $callToActionButtonLink ?>" class="btn btn-primary btn-block bg-theme-color theme-text-color"><?= $callToActionButtonText ?></a>
                                    </div>
                                    <p class="news-content"><?= base64_decode($newsData['news_content']); ?></p>
                                    <p class="original-post">Want to see the original post? <a href="<?= $newsData['news_link']; ?>">click here</a></p>
                                    <div class="optin-container  mb-5">
                                        <!--<div class="btn-close" onclick="closeOption()">X</div>-->
                                        <div class="row p-3">
                                            <div class="col-3 optin-image-container pl-4 pr-0">
                                                <img src="../upload/<?= $campaign['user_id']; ?>/<?= $responderImg; ?>" alt="optin image" class="optin-image"/>
                                            </div>

                                            <div class="col pl-3 pr-4">
                                                <div class="row">
                                                    <form action="" method="POST">
                                                        <div class="col-12">
                                                            <h5 class="text-center"><?= $optinTitle ?></h5>
                                                            <input type="email" class="form-control" name="email_responder" placeholder="Enter your email here" value="" required/>
                                                        </div>
                                                        <div class="col-12 pl-3 pt-3 pr -3 pb-2">
                                                            <button type="submit" name="btn_save" class="btn btn-danger btn-block"><?= $optinBtnTitle ?></button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="col-md-4 mt-4">
                            <div class="row">
                                <?php if( $hasNewsPost ){ ?>
                                    <div class="col-md-12 p-0">
                                        <a href="<?= $sidebarAdsData['ads_url'] ?>" class="link">
                                            <div class="side-ads-container mb-5">
                                                <!--<h1 class="display-3 text-center text-light font-weight-bold"><?= $sidebarAdsData['ads_name']; ?></h1>-->
                                            </div>
                                        </a>
                                    </div>
                                <?php } ?>
                                <div class="col-md-12 pl-0">
                                    <h3 class="font-weight-bold label-border-bottom text-muted">MOST RECENT</h3>
                                </div>
                                <div class="col-md-12">
                                    <?php if( count( $limitedMostRecent > 0 ) ): ?>
                                        <?php foreach ($limitedMostRecent as $key => $newsDataItem) { 
                                            $newsItemImage = $newsDataItem['image'];
                                            
                                            $decodedImage = json_decode($newsItemImage);
                                            $getRandIndex = rand(0, count($decodedImage)-1);
                                            $news_images = $decodedImage[$getRandIndex];
                                            
                                            if( $news_images == "" && $newsDataItem['uploaded_image'] == "" ){
                                                $news_image = "assets/img/no.jpg";
                                            }else{
                                                $news_image = $news_images != "" ? $news_images : json_decode($newsDataItem['uploaded_image'])[0];
                                            }
                                        ?>
                                            <div class="row border-bottom pb-3 mb-2">
                                                <div class="col most-recent pl-0 pr-0">
                                                    <img src="<?= $news_image ?>" alt="">
                                                </div>
                                                <div class="col-9 font-weight-bold most-recent-details"><a href="news.php?campaigns_id=<?= $campaignID ?>&news=<?= $newsDataItem['news_id']; ?>"><?= $newsDataItem['title']; ?></a></div>
                                            </div>
                                        <?php } ?>
                                    <?php else: ?>
                                        <div class="no-result-found text-center">
                                            <h3 class="font-weight-bold">
                                                Sorry, this content isn't available right now.
                                            </h3>
                                        </div>
                                    <?php endif; ?>
                                </div>
        
                                <div class="col-md-12 pl-0 mt-3">
                                    <h3 class="font-weight-bold text-muted">RECOMMENDED</h3>
                                </div>
                                <div class="col-md-12">
                                    <?php if( count( $limitedRecommed > 0 ) ): ?>
                                        <?php foreach ($limitedRecommed as $key => $newsDataItem) { 
                                            $newsItemImage = $newsDataItem['image'];
                                        
                                            $decodedImage = json_decode($newsItemImage);;
                                            $getRandIndex = rand(0, count($decodedImage)-1);
                                            $news_images = $decodedImage[$getRandIndex];
                                            
                                            if( $news_images == "" && $newsDataItem['uploaded_image'] == "" ){
                                                $news_image = "assets/img/no.jpg";
                                            }else{
                                                $news_image = $news_images != "" ? $news_images : json_decode($newsDataItem['uploaded_image'])[0];
                                            }
                                        ?>
                                            <div class="row border-bottom pb-3 mb-2">
                                                <div class="col most-recent pl-0 pr-0">
                                                    <img src="<?= $news_image ?>" alt="">
                                                </div>
                                                <div class="col-9 font-weight-bold most-recent-details"><a href="news.php?campaigns_id=<?= $campaignID ?>&news=<?= $newsDataItem['news_id']; ?>"><?= $newsDataItem['title']; ?></a></div>
                                            </div>
                                        <?php } ?>
                                    <?php else: ?>
                                        <div class="no-result-found text-center">
                                            <h3 class="font-weight-bold">
                                                Sorry, this content isn't available right now.
                                            </h3>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        <?php } ?>
        <?php
		    $popup_data ? include('./includes/popup.php') : '';
		?>
        <!-- <footer class="container">
            <p>© Company 2017-2018</p>
        </footer> -->
    <?php }else{ ?>
        <div class="container-404">
            <div class="notfound">
                <div class="notfound-404">
                    <h1>404</h1>
                </div>
                <h2>Oops, The Page you are looking for can't be found!</h2>
            </div>
        </div>
    <?php } ?>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
    <script>
        function searchInputAction() {
            let hasHide = document.querySelector("#search-input").classList.contains('hide')
            if ( hasHide ) {
                document.querySelector("#search-input").classList.remove('hide')
                document.querySelector(".icons-container div").classList.add('hide')
            }else{
                document.querySelector("#search-input").classList.add('hide')
                document.querySelector(".icons-container div").classList.remove('hide')
            }
        }
        
        function closeOption() {
            document.querySelector(".optin-container").classList.add('hide')
        }
        
        
    </script>
</body>

</html>
<!--<?php include('../cache_solution/bottom-cache-v2.php'); ?>-->