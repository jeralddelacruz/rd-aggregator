<?php
    // CODE_SECTION_PHP_3: DELETE_TO_DATABASE
	if(!empty($_GET["category_id"])){
		$category_id = $_GET["category_id"];

		$delete_category = $DB->query("DELETE FROM {$dbprefix}category WHERE category_id = '{$category_id}'");

		if($delete_category){
			$_SESSION["msg_success"] = "Category deleted.";

			redirect("index.php?cmd=category");
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