<?php
    set_time_limit(0);
    error_reporting(0);
    session_start();
    
    // IMPORTANT DIRECTORY
    $dir="../../sys";
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
    
    // WEBSITE VARIABLE
    $res=$DB->query("select setup_key,setup_val from $dbprefix"."setup order by setup_id");
    foreach($res as $row){
    	$WEBSITE[$row["setup_key"]]=$row["setup_val"];
    }
    
    // DATA FROM TWITTER
    $oauth_token = $_GET["oauth"];
    $oauth_token_secret = $_GET["oauthS"];
    $twitter_user_id = $_GET["uID"];
    $twitter_screen_name = $_GET["screenN"];
    $twitter_ppid = $_GET["ppuid"];
    
    $current_user_id = $twitter_ppid;
    
    // TEST VARIABLES FOR POSTING
    $twitter_message = "Post test";
    $twitter_hashtag = "TwitterAPI";
    
    $current_user_campaigns = $DB->query("SELECT * FROM $dbprefix"."pageb WHERE user_id='" . $current_user_id . "'");
    
    // echo "<pre>";
    // print_r($current_user_campaigns);
    // die();
?>
<!DOCTYPE html>
<html>
    <head>
    	<title>Twitter Page</title>
    
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
    	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400" />
    	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open Sans:400" />
    	
    	<!-- Embeded Style -->
    	<style>
    	
    	</style>
    	<script>
    	    window.onload = function(){
    	        var getTimeline = document.getElementById("getTimeline");
    	        var postTimeline = document.getElementById("postTimeline");
    	        
    	        var campaigns = document.getElementById("campaigns");
    	        
    	        campaigns.onchange = function(){
    	            var x = "<?php $s = $DB->info("pageb"."pageb_id='" . campaigns.value . "'"); ?>";
    	        }
    	        
    	       // getTimeline.onclick = function(){
    	       //     window.location.href="https://affilashop.com/?token=tertl3651!&type=twitter&mode=timeline&uid=1&account_name=" + "<?php echo $twitter_screen_name; ?>";
    	       // }
    	        
    	       // postTimeline.onclick = function(){
    	       //     window.location.href="https://affilashop.com/?token=tertl3651!&type=twitter&mode=post&uid=1&account_name=" + "<?php echo $twitter_screen_name; ?>&m=" + "<?php echo $twitter_message; ?>&h=" + "<?php echo $twitter_hashtag; ?>";
    	       // }
    	    }
    	</script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row text-center" style="background-color: #1DA1F2; color: white; padding: 10px;">
                <div class="col-md-4">
                    <i class="fa fa-twitter fa-4x"></i>
                </div>
                <div class="col-md-4">
                    <h4>Welcome to your Twitter Portal, @<?php echo $twitter_screen_name; ?></h4>
                </div>
                <div class="col-md-4"></div>
            </div>
            
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="campaigns" style="float:left;margin-top:7px;">Select a Campaign:</label>
                        
                        <select class="form-control" id="campaigns">
                            <?php foreach($current_user_campaigns as $current_user_campaign){ ?>
                            <option value="<?php echo $current_user_campaign["pageb_id"] ?>"><?php echo $current_user_campaign["pageb_title"] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
            
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="campaign" style="float:left;margin-top:7px;">Campaign URL:</label>
                        
                        <input type="text" class="form-control" value="<?php echo $SCRIPTURL . "add/squeeze.php?s=" . $s["pageb_n_random_string_squeeze_page"] ?>" readonly />
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
            
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        <!--<label for="campaign" style="float:left;margin-top:7px;">Campaign URL:</label>-->
                        
                        <button type="submit" class="btn btn-primary btn-block" name="submit">Post</button>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </body>
</html>