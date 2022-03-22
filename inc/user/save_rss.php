<?php
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
	
	$user_id   = $_POST['user_id'];
	$campaign_id  = null;
	$content_id  = $_POST['content_id'];
	$news_link   = $_POST['news_link'];
	$news_image   = $_POST['news_image'];
	$news_thumbnail   = $_POST['news_thumbnail'];
	$news_published_date   = $_POST['news_published_date'];
	$news_title   = $_POST['news_title'];
	$db_prefix   = $_POST['db_prefix'];
	$news_author   = $_POST['news_author'];
	$news_content   = $_POST['news_content'];
	$news_description   = $_POST['news_description'];
	
	$sql =  "INSERT INTO {$db_prefix}news SET 
	            user_id = '{$user_id}',
				campaign_id = '{$campaign_id}',
				content_id = '{$content_id}',
				news_link = '{$news_link}',
				news_image = '{$news_image}',
				news_thumbnail = '{$news_thumbnail}',
				news_title = '{$news_title}',
				news_author = '{$news_author}',
				news_content = '{$news_content}',
				news_description = '{$news_description}'
            ";
    $content = $DB->query("SELECT * FROM {$dbprefix}news WHERE news_title = '{$news_title}' AND user_id ='{$user_id}'");
    if( !$content ){
        if ($DB->query($sql)) {
    		echo json_encode(array("statusCode"=>200));
    		
    	} else {
    		echo json_encode(array("statusCode"=>201));
    		
    	}
    }
	
?>

