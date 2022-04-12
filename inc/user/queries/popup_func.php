<?php
    // VARIABLE INITIALIZATION
	$current_user_id = $UserID;
	$dfy_author_id = $WEBSITE["dfy_author"];
	$site_domain_url = $SCRIPTURL;

	// DELETE A ROW
	if($_GET["del"]){
		// CODE_SECTION_PHP_3: DELETE_TO_DATABASE
    	
		$popup_id = $_GET["del"];
		$result = $DB->query("SELECT * FROM {$dbprefix}popup WHERE popup_id = '{$popup_id}'")[0];
		if($result){
		    $target_directory_1 = "../upload/{$UserID}/popup/".$result['avatar_url'];
		    unset($target_directory_1);
		    
		    $target_directory_2 = "../upload/{$UserID}/".$result['second_image_url'];
		    unset($target_directory_2);
		    
		    $delete_ads = $DB->query("DELETE FROM {$dbprefix}popup WHERE popup_id = '{$popup_id}'");

    		if($delete_ads){
    			$_SESSION["msg"] = "Popup deleted.";
    			redirect("index.php?cmd=popup");
    		}
		}
	}
	
	$and_query = "";
	if( $user_subdomain ){
	    $subdomain_id = $user_subdomain["subdomain_id"];
	    $and_query = " AND subdomain_id = '{$subdomain_id}'";
	}else{
	    $and_query = " AND subdomain_id = 0";
	}
?>