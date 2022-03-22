<?php
	set_time_limit(0);
	error_reporting(0);
	session_start();
	
	// IMPORTANT DIRECTORY
	$dir = "../../sys";
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

	$passed_user_id = $_POST["pass_user_id"];

	$get_all_pages_content = $DB->query("SELECT * FROM {$dbprefix}pages_content WHERE user_id = '{$passed_user_id}'");

	echo json_encode($get_all_pages_content);
?>