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
	
    $articleId = $_GET['article'];
    $page_type = $_GET['page_type'];
    $user_id = $_GET['user_id'];
    $articleData = $DB->query("SELECT * FROM {$dbprefix}pages WHERE pages_id={$articleId}")[0];
    $article = [
        "article_id"            => $articleData['pages_id'],
        "article_name"          => htmlspecialchars_decode($articleData['pages_name']),
        "article_image"         => $articleData['pages_image'],
        "article_title"         => htmlspecialchars_decode($articleData['pages_menu_title']),
        "article_excerpt"       => htmlspecialchars_decode($articleData['pages_excerpt']),
        "article_headline"      => htmlspecialchars_decode($articleData['pages_headline']),
        "article_subheadline"   => htmlspecialchars_decode($articleData['pages_introduction']),
        "article_button_text"   => "Read More"
    ];
    
    $adsData = $DB->query("SELECT * FROM {$dbprefix}pages WHERE pages_id={$articleId}")[0];
    $ads = [
        "ads_id"            => $adsData['pages_id'],
        "ads_image"         => $adsData['pages_image'],
        "ads_headline"      => htmlspecialchars_decode($adsData['pages_name']),
        "ads_subheadline"   => htmlspecialchars_decode($adsData['pages_introduction']),
        "ads_button_text"   => $adsData['pages_button_text'],
        "ads_button_affiliate"   => $adsData['pages_button_affiliate'],
    ];
    
    $c2aData = $DB->query("SELECT * FROM {$dbprefix}pages WHERE pages_id={$articleId}")[0];
    $c2a = [
        "c2a_id"            => $c2aData['pages_id'],
        "c2a_image"         => $c2aData['pages_image'],
        "c2a_headline"      => htmlspecialchars_decode($c2aData['pages_headline']),
        "c2a_subheadline"   => htmlspecialchars_decode($c2aData['pages_introduction']),
        "c2a_button_text"   => $c2aData['pages_button_text'],
        "c2a_button_affiliate"   => $c2aData['pages_button_affiliate'],
    ];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>EZPPAGES - Preview</title>
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
                --theme-color: #F58851;
                --theme-text-color: #fff;
                --theme-font: "Verdana";
            }
        </style>
        <link href="css/styles.css" rel="stylesheet" />
        <link href="css/navigation.css" rel="stylesheet" />
        <link href="css/article.css" rel="stylesheet" />
        <link href="css/modal.css" rel="stylesheet" />
    </head>
    <body>
        <div id="mySidebar" class="sidebar">
            <a href="#">-</a>
            <a href="#">-</a>
            <a href="#">-</a>
            <a href="javascript:void(0)" class="btn btn-outline-secondary w-50 m-auto p-2 close-btn" onclick="closeNav()"><span>X </span> Close</a>
        </div>
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-dark theme-color">
            <a class="navbar-brand" href="#">Previewing..</a>
            <button class="navbar-toggler" type="button" onclick="openNav()" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
          
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">...</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">...</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">...</a>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- Masthead-->
        <section class="article-container mt-5">
            <div class="container">
                <div class="row mb-4">
                    <?php if( $page_type == 'article' ){ ?>
                        <div class="col-sm-8">
                            <div class="left-container pr-5 pl-2 pt-2">   
                                <?php if( $article['article_image'] ){ ?>
                                    <img class="article-image mb-3" src="../upload/<?= $user_id ?>/<?= $article['article_image']; ?>" alt="<?= $article['article_image']; ?>">
                                <?php }else{ ?>
                                    <img class="article-image mb-3" src="assets/img/no.jpg" alt="no image">
                                <?php } ?>
                                <h1><?= $article['article_headline']; ?></h1>
                                <p class="mb-4 white-space text-justify"> <?= $article['article_subheadline']; ?> </p>
                            </div>
                        </div>
                    <?php }else{ ?>    
                        <div class="col-sm-8">
                            <div class="left-container pr-5 pl-2 pt-2">   
                                <img class="article-image mb-3" src="assets/img/no.jpg" alt="no image">
                                <h1>......</h1>
                                <p class="mb-4 white-space text-justify"> ........... </p>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if( $page_type == 'ads' ){ ?>
                        <div class="col-sm-4">
                            <div class="right-card-container">
                                <div class="right-card-image d-flex justify-content-center mb-2">
                                    <?php if( $ads['ads_image'] ){ ?>
                                        <img src="../upload/<?= $user_id ?>/<?= $ads['ads_image']; ?>" alt="<?= $ads['ads_image']; ?>">
                                    <?php }else{ ?>
                                        <img src="assets/img/no.jpg" alt="no image">
                                    <?php } ?>
                                </div>
                                <div class="right-card-content">
                                    <div class="right-card-title text-center">
                                        <h3><?= $ads['ads_headline']; ?></h3>
                                    </div>
                                    <div class="right-card-contnet text-center mb-3">
                                    <?= $ads['ads_subheadline']; ?>
                                    </div>
                                    <div class="right-card-button d-flex justify-content-center">
                                        <a href="<?= $ads['ads_button_affiliate']; ?>" class="btn btn-primary"><?= $ads['ads_button_text']; ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }else{ ?>
                        <div class="col-sm-4">
                            <div class="right-card-container">
                                <div class="right-card-image d-flex justify-content-center mb-2">
                                    <img class="article-image mb-3" src="assets/img/no.jpg" alt="no image">
                                </div>
                                <div class="right-card-content">
                                    <div class="right-card-title text-center">
                                        <h3>....</h3>
                                    </div>
                                    <div class="right-card-contnet text-center mb-3">
                                    ....
                                    </div>
                                    <div class="right-card-button d-flex justify-content-center">
                                        <a href="#" class="btn btn-primary">....</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    
                </div>
            </div>
        </section>
        <?php if( $page_type == 'c2a' ){ ?>
            <!-- Call to Action -->
            <header class="call-to-action d-flex justify-content-center">
                <div class="c2a-container">
                    <div class="row">
                        <div class="col-4">
                            <img class="c2a-image" src="../upload/<?= $user_id ?>/<?= $c2a['c2a_image']; ?>" alt="">
                        </div>
                        <div class="col-8 text-center">
                            <div class="c2a-headline"><h2><?= $c2a['c2a_headline']; ?></h2></div>
                            <div class="c2a-subheadline"><p><?= $c2a['c2a_subheadline']; ?></p></div>
                            <a href="#" class="btn btn-primary"><?= $c2a['c2a_button_text']; ?></a>
                        </div>
                    </div>
                </div>
            </header>
        <?php }else{ ?>
            <!-- Call to Action -->
            <header class="call-to-action d-flex justify-content-center">
                <div class="c2a-container">
                    <div class="row">
                        <div class="col-4">
                            <img class="article-image mb-3" src="assets/img/no.jpg" alt="no image">
                        </div>
                        <div class="col-8 text-center">
                            <div class="c2a-headline"><h2>....</h2></div>
                            <div class="c2a-subheadline"><p>....</p></div>
                            <a href="#" class="btn btn-primary">....</a>
                        </div>
                    </div>
                </div>
            </header>
        <?php } ?>
        
        <!-- Articles Section -->
        <section class="slider-tab bg-light slider-tab-content">
            <div class="container-fluid p-0">
                <div class="main-container bg-dirty">
                    <div class="container custom-card-container">
                        <div class="resources-area flex-wrap">
                            <!-- CARD -->
                            <div class="lower-section-card-container">
                                <div class="lower-sectionheader">
                                    <div class="lower-section-count"><span>01</span></div>
                                    <div class="resource-image d-flex justify-content-center">
                                        <img src="assets/img/no.jpg" alt="no image">
                                    </div>
                                </div>
                                <div class="card-content text-center p-4">
                                    <h5>....</h5>
                                    <p class="line-clamp-7">....
                                    </p>
                                    <a href="#" class="btn btn-primary btn-block">....</a>
                                </div>
                            </div>
                            <!-- CARD -->
                            <div class="lower-section-card-container">
                                <div class="lower-sectionheader">
                                    <div class="lower-section-count"><span>02</span></div>
                                    <div class="resource-image d-flex justify-content-center">
                                        <img src="assets/img/no.jpg" alt="no image">
                                    </div>
                                </div>
                                <div class="card-content text-center p-4">
                                    <h5>....</h5>
                                    <p class="line-clamp-7">....
                                    </p>
                                    <a href="#" class="btn btn-primary btn-block">....</a>
                                </div>
                            </div>
                            <!-- CARD -->
                            <div class="lower-section-card-container">
                                <div class="lower-sectionheader">
                                    <div class="lower-section-count"><span>03</span></div>
                                    <div class="resource-image d-flex justify-content-center">
                                        <img src="assets/img/no.jpg" alt="no image">
                                    </div>
                                </div>
                                <div class="card-content text-center p-4">
                                    <h5>....</h5>
                                    <p class="line-clamp-7">....
                                    </p>
                                    <a href="#" class="btn btn-primary btn-block">....</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Footer-->
        <footer class="footer theme-color">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 h-100 text-center my-auto">
                        <ul class="list-inline mb-2">
                            <li class="list-inline-item">
                                <a href="#">....</a>
                            </li>
                        </ul>
                        <p class="text-muted small mb-4 mb-lg-0">&copy; Your Website 2021. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
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
    </body>
</html>
