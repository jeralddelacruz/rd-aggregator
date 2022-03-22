<?php
	$serverName = $_SERVER["SERVER_NAME"];
    $serverName1 = explode( ".com", $serverName )[0];
    $serverName2 = explode(".", $serverName1);
   
   
    // sys FOLDER DIRECTORY INCLUDE
	$dir = "../../sys";
	$fp = opendir($dir);
	while(($file = readdir($fp)) != false){
		$file = trim($file);
		if(($file == ".") || ($file == "..")){continue;}
		$file_parts = pathinfo($dir . "/" . $file);
		if($file_parts["extension"] == "php"){
			include($dir . "/" . $file);
		}
	}
	closedir($fp);

	// DATABASE CONNECTION THROUGH class.db.php
	$DB = new db($dbhost, $dbuser, $dbpass, $dbname);
	$DB->connect();
	if($DB->connect < 1){
		exit("Can't proceed. The database is not initialized.");
	}
	
	// ============================ //
	// === SUBDOMAIN VALIDATION === //
	// ============================ //
	// check if the url is main 
	$is_subdomain_exist = false;
	$is_maindomain = false;
	if( $serverName2[0] !== $APPNAME ){
	    // if has subdomain then check if subdomain is existing
	    $user_subdomain = $DB->info("user_subdomain", "subdomain_name = '{$serverName2[0]}'");
	    if( $user_subdomain ){
	        $is_subdomain_exist = true;
	    }else{
	        $is_subdomain_exist = false;
	    }
	    
	}else{
	    $is_maindomain = true;
	}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include_once('inc/user/Theme_2/meta.php'); ?>
        
        <?php include_once('inc/user/Theme_2/css.php'); ?>
    </head>
    <body class="animsition">
        <div class="page-wrapper">
            <div class="page-content--bge5">
                <div class="container h-100 d-flex align-items-center">
                    <div class="login-wrap p-0">
                        <div class="login-content">
                            <div class="login-logo">
                                <a href="#">
                                <?php if ($WEBSITE["logo"]) : ?>
                                    <img src="../img/<?php echo $WEBSITE["logo"];?>" class="img-responsive" style="margin:0 auto;float:none;" />
                                <?php endif; ?>
                                </a>
                            </div>
                            
                            <?php if( !$is_subdomain_exist && !$is_maindomain ): ?>
                                <h3>Sorry! this domain is not registered.</h3>
                            <?php else: ?>
                                <div class="login-form">
                                    <div class="social-login-content">
                                        <div class="social-button">
                                            <a href="<?php echo $WEBSITE["sales_page_link"];?>" class="au-btn au-btn--block au-btn--blue m-b-20 text-center">Not yet a member?</a>
                                            <a href="<?php echo $USERDIR; ?>" class="au-btn au-btn--block au-btn--blue2 text-center">Already a member? Sign in here</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <?php include_once('inc/user/Theme_2/scripts.php'); ?>
    </body>
</html>