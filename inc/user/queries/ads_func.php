<?php
    // CODE_SECTION_PHP_3: DELETE_TO_DATABASE
	if(!empty($_GET["ads_id"])){
		$ads_id = $_GET["ads_id"];

		$delete_ads = $DB->query("DELETE FROM {$dbprefix}ads2 WHERE ads_id = '{$ads_id}'");

		if($delete_ads){
			$_SESSION["msg_success"] = "Ads deleted.";

			redirect("index.php?cmd=ads2");
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