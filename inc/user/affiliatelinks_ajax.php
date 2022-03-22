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

	$ajax_type = $_POST["ajax_type"];
	$user_id = $_POST["user_id"];
	$id = $_POST["id"];
	$data = $_POST["data"];
	$data = explode(",", $data);

	if($ajax_type == "fetch"){
		$fetch = $DB->query("SELECT * FROM {$dbprefix}affiliate_links WHERE user_id = '{$user_id}'");

		if($fetch){
			echo json_encode($fetch);
		}
		else{
			echo "Data fetch failed.";
		}
	}
	elseif($ajax_type == "delete"){
		$delete = $DB->query("DELETE FROM {$dbprefix}affiliate_links WHERE affiliate_links_id = '{$id}'");

		if($delete){
			$fetch = $DB->query("SELECT * FROM {$dbprefix}affiliate_links WHERE user_id = '{$user_id}'");
			echo json_encode($fetch);
		}
		else{
			echo "Data delete failed.";
		}
	}
	elseif($ajax_type == "save"){
		if(empty($id) || $id == "null"){
			$id = $DB->getauto("affiliate_links");
			$insert = $DB->query("INSERT INTO {$dbprefix}affiliate_links SET 
				affiliate_links_id = '{$id}', 
				user_id = '{$user_id}', 
				affiliate_links_title = '{$data[0]}', 
				affiliate_links_link_main = '{$data[1]}', 
				affiliate_links_link_alt = '{$data[2]}'");
		}
		else{
			$update = $DB->query("UPDATE {$dbprefix}affiliate_links SET 
				affiliate_links_title = '{$data[0]}', 
				affiliate_links_link_main = '{$data[1]}', 
				affiliate_links_link_alt = '{$data[2]}' 
				WHERE affiliate_links_id = '{$id}'");
		}

		if($insert || $update){
			$fetch = $DB->query("SELECT * FROM {$dbprefix}affiliate_links WHERE user_id = '{$user_id}'");
			echo json_encode($fetch);
		}
		else{
			echo "Data save failed.";
		}
	}
?>