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

	// PASSED ID FOR EDIT
	$pages_content_id = $_POST["pages_content_id"];

	// POST VARIABLES
	$passed_user_id = $_POST["pass_user_id"];
	$pages_content_headline = $_POST["pages_content_headline"];
	$pages_content_body = $_POST["pages_content_body"];
	$pages_content_button_text = $_POST["pages_content_button_text"];
	$pages_content_button_url = $_POST["pages_content_button_url"];

	if(empty($pages_content_id)){
		$pages_content_id = $DB->getauto("pages_content");
		$insert_pages_content = $DB->query("INSERT INTO {$dbprefix}pages_content SET 
			pages_content_id = '{$pages_content_id}', 
			user_id = '{$passed_user_id}', 
			pages_content_headline = '{$pages_content_headline}', 
			pages_content_body = '{$pages_content_body}', 
			pages_content_button_text = '{$pages_content_button_text}', 
			pages_content_button_url = '{$pages_content_button_url}'");

		if($insert_pages_content){
			echo "Success";
		}
		else{
			echo $insert_pages_content;
		}
	}
	else{
		$update_pages_content = $DB->query("UPDATE {$dbprefix}pages_content SET 
			pages_content_headline = '{$pages_content_headline}', 
			pages_content_body = '{$pages_content_body}', 
			pages_content_button_text = '{$pages_content_button_text}', 
			pages_content_button_url = '{$pages_content_button_url}'
			WHERE pages_content_id = '{$pages_content_id}' AND user_id = '{$passed_user_id}'");

		if($update_pages_content){
			echo "Success";
		}
		else{
			echo $update_pages_content;
		}
	}
?>