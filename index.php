<?php
	set_time_limit(0);
	error_reporting(0);
	session_start();

	// INCLUDE FILES IN "sys" FOLDER
	$dir = "./sys";
	$fp = opendir($dir);
	while(($file = readdir($fp)) != false){
		$file = trim($file);
		if(($file==".") || ($file == "..")){continue;}
		$file_parts = pathinfo($dir . "/" . $file);
		if($file_parts["extension"] == "php"){
			include($dir . "/" . $file);
		}
	}
	closedir($fp);

	// CONNECT TO THE DATABASE THROUGH THE CREATED CLASS (sys/class.db.php)
	$DB = new db($dbhost,$dbuser,$dbpass,$dbname);
	$DB->connect();
	if($DB->connect < 1){
		exit("Can't proceed. The database is not initialized.");
	}

	// SET WEBSITE GLOBAL VARIABLE
	$res = $DB->query("SELECT setup_key, setup_val FROM {$dbprefix}setup ORDER BY setup_id");
	foreach($res as $row){
		$WEBSITE[$row["setup_key"]] = $row["setup_val"];
	}

	if(!in_array($WEBSITE["cdb"], array_keys($ECG_PDF_ARR))){
		$WEBSITE["cdb"]="cdb";
	}
	$WEBSITE["cdb_url"] = $ECG_PDF_ARR[$WEBSITE["cdb"]];
	$_SESSION["CDB_URL"] = $WEBSITE["cdb_url"];

	$id = $_GET["p"];
	if($id){
		if(!$row = $DB->info("page", "page_id='{$id}' AND page_fe='1'")){
			redirect("./");
		}
	}
	else{
		$row = $DB->info("page", "page_fe='1' AND page_index='1'");
	}

	// OUPUT PAGE BODY
	if($row["page_body"] && (preg_match("/%product%/i", $row["page_body"]))){
		if(prfe_block($row["page_body"], $row["page_pr"])){
			$body = $row["page_body"];
			$style = "<link rel=\"stylesheet\" href=\"./css/style.css\" /></head>";
			$body = str_replace("</head>", $style, $body);

			if($WEBSITE["icon"]){
				$icon = "<link rel=\"shortcut icon\" href=\"./img/" . $WEBSITE["icon"] . "\" /></head>";
				$body = str_replace("</head>", $icon, $body);
			}

			$user_id = (int)$_GET["u"];
			if(!$cur_user = $DB->info("user", "user_id = '{$user_id}' AND user_act='1' AND (user_expire='0' OR user_expire > '" . time() . "')")){
				$user_id = 0;
			}
			else{
				$body = str_replace("%company%", $WEBSITE["sitename"], $body);
				$body = str_replace("%contact%", $cur_user["user_email"], $body);
			}

			$body = str_replace("%url%", "<a href=\"$SCRIPTURL\">$SCRIPTURL</a>", $body);
			$body = str_replace("%legal%", get_legal($id,$user_id), $body);

			echo $body;
		}
		else{
			include_once("./tpl/tpl_index.php");
		}
	}
	else{
		// SEE func_ui.php
		include_once(landingPage());
	}

	exit("");
?>