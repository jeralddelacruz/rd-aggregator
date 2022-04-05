<?php
	// index.php of USERS
	set_time_limit(0);
	// error_reporting(0);
	ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
	// index.php of 
    
    $domain = "";
	$serverName = $_SERVER["SERVER_NAME"];
    $serverName1 = explode( ".test", $serverName )[0];
    $serverName2 = explode(".", $serverName1);
    if( count( $serverName2 ) > 1 ){
        $domain = $serverName2[count($serverName2) - 1];
    }else{
        $domain = $serverName2[0];
    }
	session_set_cookie_params(14400,"/", '.'.$domain.'.test');
	session_name($domain);
	session_start();
	
	// sys FOLDER DIRECTORY INCLUDE
	$dir = "../sys";
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
	    $user_subdomain = $DB->info("user_subdomain", "subdomain_name = '{$serverName2[0]}' AND subdomain_status = 1");
	    if( $user_subdomain ){
	        $is_subdomain_exist = true;
	    }else{
	        $is_subdomain_exist = false;
	    }
	}else{
	    $is_maindomain = true;
	}
	
	$and_query = "";
	if( isset( $user_subdomain ) ){
	    $subdomain_id = $user_subdomain["subdomain_id"];
	    $and_query = " AND subdomain_id = '{$subdomain_id}'";
	}else{
	    $and_query = " AND subdomain_id = 0";
	}
	

	// GLOBAL VARIABLE WEBSITE
	$res = $DB->query("SELECT setup_key,setup_val FROM {$dbprefix}setup ORDER BY setup_id");
	foreach($res as $row){
		$WEBSITE[$row["setup_key"]] = $row["setup_val"];
	}

	if(!in_array($WEBSITE["theme"], array_keys($THEME_ARR))){
		$WEBSITE["theme"] = "gray";
	}
	$WEBSITE["theme_btn"] = $THEME_ARR[$WEBSITE["theme"]];

	if(!in_array($WEBSITE["cdb"], array_keys($ECG_PDF_ARR))){
		$WEBSITE["cdb"] = "cdb";
	}
	$WEBSITE["cdb_url"] = $ECG_PDF_ARR[$WEBSITE["cdb"]];
	$_SESSION["CDB_URL"] = $WEBSITE["cdb_url"];

	$TAG_ARR = array();
	$res = $DB->query("SELECT * FROM {$dbprefix}page WHERE page_tmenu = '1'");
	if(count($res)){
		foreach($res as $row){
			$USER_CMD[$row["page_slug"]] = array($row["page_title"], $row["page_slug"]);
			$TAG_ARR[] = $row["page_slug"];
		}
	}

	$THEME = $DB->query("SELECT * FROM {$dbprefix}themes WHERE selected = 1");
	$THEME_FONT = $DB->query("SELECT * FROM {$dbprefix}fonts WHERE selected = 1");
	$THEME_COLOR = $DB->query("SELECT * FROM {$dbprefix}colors WHERE selected = 1");

	$FOOTER = $DB->query("SELECT * FROM {$dbprefix}page WHERE page_bmenu = '1' AND page_pack LIKE '%;{$_SESSION["PackID"]};%' ORDER BY page_order");
	
	$MOD_ARR = unserialize($WEBSITE["mod"]);
	$USER_MENU_ARR = unserialize($WEBSITE["menu"]);

	if(!$MOD_ARR["art"]){
		unset($USER_CMD["art"]);
		unset($USER_CMD["artview"]);
		unset($USER_MENU["art"]);
		unset($USER_MENU_ARR["art"]);
	}
	if(!$MOD_ARR["bonus"]){
		unset($USER_CMD["bonus"]);
		unset($USER_CMD["bonusview"]);
		unset($USER_MENU["bonus"]);
		unset($USER_MENU_ARR["bonus"]);
	}
	if(!$MOD_ARR["ecg"]){
		unset($USER_CMD["aie"]);
		unset($USER_CMD["cover"]);
		unset($USER_CMD["cover3d"]);
		unset($USER_CMD["coverok"]);
		unset($USER_MENU["cover"]);
		unset($USER_MENU_ARR["cover"]);
	}
	if(!$MOD_ARR["lg"]){
		unset($USER_CMD["lg"]);
		unset($USER_CMD["lgedit"]);
		unset($USER_CMD["lgt"]);
		unset($USER_MENU["lg"]);
		unset($USER_MENU_ARR["lg"]);
	}
	
	require_once('simplepie.php');

	// GET VARIABLE cmd
	$cmd = $_GET["cmd"];

	$CMD_KEY = array_keys($USER_CMD);
	if(!in_array($cmd,$CMD_KEY)){
		$cmd = "login";
	}

	if(($cmd == "login") || ($cmd == "free") || ($cmd == "freeok") || ($cmd == "pro") || ($cmd == "prook") || ($cmd == "forgot") || ($cmd == "subdomain")){
		unset($_SESSION["UserName"]);
		unset($_SESSION["UserPass"]);
		unset($_SESSION["UserID"]);
		unset($_SESSION["PackID"]);
		unset($_SESSION["ECG_ARR"]);
	}

	$UserName = $_SESSION["UserName"];
	// $UserPass=mc_decrypt($_SESSION["UserPass"],$dbkey);
	if($_POST["LoginSubmit"]){
		$UserName = strip($_POST["LoginName"]);
		$UserPass = strip($_POST["LoginPass"]);
	}
    
    $and_query = "";
    if(isset($user_subdomain)){
        $user_id_subdomain = $user_subdomain["user_id"];
        $and_query = " and user_id=$user_id_subdomain";
    }
    
	$cur_user=$DB->info("user","user_email='$UserName' and user_act='1' $and_query");
	if(!($cur_user && (mc_decrypt($UserPass, $cur_user["user_pass"]) == $UserPass))){
		if(!(($cmd == "free") || ($cmd == "freeok") || ($cmd == "forgot") || ($cmd == "pro") || ($cmd == "prook") || ($cmd == "subdomain"))){
			$cmd = "login";
		}

		$is_user = 0;
	}
	else{
		$is_user = 1;
		$_SESSION["UserName"] = $UserName;
		$_SESSION["user_name"] = $cur_user['user_fname'] . " " . $cur_user['user_lname'];
		$_SESSION["user_avatar"] = !empty($cur_user["user_avatar"]) ? '../upload/avatar/' . $cur_user['user_avatar'] : '/themes/images/icon/avatar-no-img.jpg';
		$_SESSION["UserPass"] = $cur_user["user_pass"];
		$UserID = $cur_user["user_id"];
		$PackID = $cur_user["pack_id"];
		$_SESSION["UserID"] = $UserID;
		$_SESSION["PackID"] = $PackID;

		$cur_pack = $DB->info("pack", "pack_id='$PackID'");

		list($from, $to) = get_exptime($cur_user["user_rd"]);

		$ECG_ARR = unserialize($cur_pack["pack_ecg"]);

		$res = $DB->query("SELECT COUNT(limit_id) AS num FROM {$dbprefix}limit WHERE user_id = '{$UserID}' AND limit_type = 'cover_flat' AND (limit_rd >= '{$from}' AND limit_rd < '{$to}')");
		$ECG_ARR["left"] = $ECG_ARR["mon"] - (int)$res[0]["num"];

		$ECG_ARR["to"] = date("Y/m/d g:i A",$to);

		$_SESSION["ECG_ARR"] = $ECG_ARR;

		if(!$_POST["LoginSubmit"]){
			if(isset($_SESSION["Door"])){
				$cmd = "door";
			}
			elseif($cur_user["user_expire"] && ($cur_user["user_expire"] < time())){
				$cmd = "renew";
			}
		}
	}

	if($WEBSITE["icon"]) {
	    $WEBSITE["icon"] = $WEBSITE["icon"] . "?v=" . rand(1, 1000);
	}

	$index_title = str_replace("%sitename%",$WEBSITE["sitename"],$USER_CMD[$cmd][0]);
	$noAuth = (in_array($cmd,array("login", "free", "freeok", "pro", "prook", "forgot", "door", "subdomain")) ? "1" : "");

	ob_start();
	
	// HEADER THEME
	if($THEME[0][0] == 1){
		include($noAuth ? "../inc/user/Theme_1/login-header.php" : "../inc/user/Theme_1/header.php");
	}
	elseif($THEME[0][0] == 2){
		include($noAuth ? "../inc/user/Theme_2/login-header.php" : "../inc/user/Theme_2/header.php");
	}
	elseif($THEME[0][0] == 3){
		include($noAuth ? "../inc/user/Theme_3/login-header.php" : "../inc/user/Theme_3/header.php");
	}
	else{
		include("../inc/user/header{$noAuth}.php");
	}
	
	// BODY THEME
	include("../inc/user/".(in_array($cmd,$TAG_ARR)?"tag":$cmd).".php");

	// FOOTER THEME
	if($THEME[0][0] == 1){
		include($noAuth ? "../inc/user/Theme_1/login-footer.php" : "../inc/user/Theme_1/footer.php");
	}
	elseif($THEME[0][0] == 2){
		include($noAuth ? "../inc/user/Theme_2/login-footer.php" : "../inc/user/Theme_2/footer.php");
	}
	elseif($THEME[0][0] == 3){
		include($noAuth ? "../inc/user/Theme_3/login-footer.php" : "../inc/user/Theme_3/footer.php");
	}
	else{
		include("../inc/user/footer{$noAuth}.php");
	}
	ob_end_flush();
?>