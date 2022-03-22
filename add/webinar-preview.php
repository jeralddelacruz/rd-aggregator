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
	
	$pages_id = $_GET['pages_id'];
    $pages_type = $_GET['pages_type'];
	
    $webinarData = $DB->query("SELECT * FROM {$dbprefix}pages WHERE pages_id = {$pages_id}")[0];
    // var_dump( $webinarData );
    $webinar = [
        "webinar_id"            => $webinarData['pages_id'],
        "webinar_url"           => $webinarData['pages_video_url'],
        "webinar_headline"      => htmlspecialchars_decode($webinarData['pages_name']),
        "webinar_subheadline"   => htmlspecialchars_decode($webinarData['pages_introduction']),
        "webinar_affiliate_link"   => $webinarData['pages_affiliate_link_webinar']
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
        <link href="css/webinar.css" rel="stylesheet" />
        <link href="css/modal.css" rel="stylesheet" />
        <style>
            iframe#pages-video-url-preview {
                width: 100%;
                min-height: 500px;
            }
        </style>
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
            <a class="navbar-brand" href="#">Previewing...</a>
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
        <!-- Articles Section -->
        <section class="webinar-container">
            <!-- CONTENT: IF PAGES TYPE -->
            <div class="container-fluid mt-5">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <!-- PAGES IMAGE OR PAGES VIDEO URL -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="progress" style="height: 50px;">
                                    <div class="progress-bar progress-bar-striped theme-color progress-bar-animated text-uppercase" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"><b style="font-size: 27px;">Live Training In Progress Do Not Close The Window</b></div>
                                </div>
                                <div class="embed-responsive embed-responsive-16by9">
                                    <?php
        								// URL OPTIMIZATION: YOUTUBE
        								if(strpos($webinar["webinar_url"], "youtu.be")){
        									$optimizedURL = str_replace("youtu.be", "youtube.com/embed" , $webinar["webinar_url"]);
        								}
        								
        								if(strpos($webinar["webinar_url"], "watch")){
        									$optimizedURL = str_replace("watch", "embed", $webinar["webinar_url"]);
        								}
        								
        								if(strpos($webinar["webinar_url"], "watch?v=")){
        									$optimizedURL = str_replace("watch?v=", "embed/", $webinar["webinar_url"]);
        								}
        
        								// URL OPTIMIZATION: VIMEO
        								if(strpos($webinar["webinar_url"], "vimeo.com")){
        									$optimizedURL = str_replace("vimeo.com", "player.vimeo.com/video", $webinar["webinar_url"]);
        								}
        							?>
                                    <iframe class="embed-responsive-item" id="pages-video-url-preview" src="<?= $optimizedURL; ?>"></iframe>
                                </div>
                            </div>
                        </div>

                        <?php if ( isset($pages_type) && $pages_type !== 'webinar' ): ?>
                        <!-- PAGE HEADLINE -->
                        <div class="row">
                            <div class="col-md-12 mt-4 px-5">
                                <h2 class="text-uppercase chunky"><?= $webinar['webinar_headline']; ?></h2>
                                <p class="chunky"><?= $webinar['webinar_subheadline']; ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-12 mt-3 px-5 text-center">
                                <h4 class="text-<?= $theme_color; ?>">Extremely Limited Time Offer!</h4>
                                <p class="text-center">100% Money Back Guarantee. Only 100 spots available. This <b>WILL</b> sell out fast.</p>

                                <a class="btn btn-lg theme-color text-uppercase theme-text-color" href="<?= $webinar['webinar_affiliate_link'] ?>">YES! I DESERVE THIS! GIVE ME ACCESS NOW!</a>
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
