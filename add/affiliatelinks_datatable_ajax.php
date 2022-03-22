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

	$user = $DB->info("user", null);

	$fetch = $DB->query("SELECT * FROM {$dbprefix}affiliate_links WHERE user_id = '{$user["user_id"]}'");

	$data = [];
	foreach($fetch as $data_individual){
		$data[] = "{$data_individual["affiliate_links_id"]}, {$data_individual["user_id"]}, {$data_individual["affiliate_links_link_main"]}, {$data_individual["affiliate_links_link_alt"]}, {$data_individual["affiliate_links_created_at"]}, {$data_individual["affiliate_links_updated_at"]}";
	}

	for($x = 0; count($data) > $x; $x++){
		$data[$x] = explode(", ", $data[$x]);
		$data[$x] = implode('", "', $data[$x]);
	}

	echo '{ "data": [';
	for($offset = 0; count($data) > $offset; $offset++){
		echo ' ["'.$data[$offset].'"] '; if($offset != 2){ echo ", "; }
	}
	echo "] }";
?>