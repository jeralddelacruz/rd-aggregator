<?php
    $subdomain_id = "";
    $and_query = "";
	if( $user_subdomain ){
	    $subdomain_id = $user_subdomain["subdomain_id"];
	    $and_query = " AND subdomain_id = '{$subdomain_id}'";
	}else{
	    $subdomain_id = "0";
	    $and_query = " AND subdomain_id = 0";
	}

	// CODE_SECTION_PHP_2: VARIABLE_INITIALIZATION
	$passed_id = $_GET["id"];
	$collection_id = $_GET["collection_id"];
	$content = $DB->info("content", "content_id = '{$passed_id}' AND user_id = '{$UserID}'");
    $categories = $DB->query("SELECT * FROM {$dbprefix}category WHERE user_id = '{$UserID}' $and_query");
    $ads = $DB->query("SELECT * FROM {$dbprefix}ads2 WHERE user_id = '{$UserID}' $and_query");
    $collection_status = $DB->info("content_collection", "content_collection_id = '{$collection_id}' AND user_id = '{$UserID}'");
    $collection_post_status = $collection_status["collection_post_status"];
    
    $status = "";
    switch($collection_post_status) {
      case "Approved":
        $status = "approved";
        break;
      case "Needs Approval":
        $status = "need_approval";
        break;
      default:
        // code block
    }
	
	// CODE_SECTION_PHP_3: BACKEND PROCESS
	if($_POST["submit"]){
	    
		// POST VARIABLES
		$remove[] = "'";
		$remove[] = '"';
		$remove[] = "-";

		// STRIP 1
		$cs_stripped_1 = str_replace($remove, "", $_POST["content_title"]);
		$content_title = $cs_stripped_1;
		$feed_link = $_POST["feed_link"];
		$category_id = $_POST["category_id"];
		$banner_ads_id = $_POST["banner_ads_id"];
		$sidebar_ads_id = $_POST["sidebar_ads_id"];
		$category_status = $_POST["category_status"];
        $content_image = empty($_FILES["content_image"]["name"]) ? $content["content_image"] : $_FILES["content_image"]["name"];
        
		// IF $passed_id HAS A VALUE
		if(empty($passed_id)){
			$content_id = $DB->getauto("content");
			$insert_content = $DB->query("INSERT INTO {$dbprefix}content SET 
				content_id = '{$content_id}',
				content_collection_id = '{$collection_id}',
				user_id = '{$UserID}',
				subdomain_id= '{$subdomain_id}',
				content_title = '{$content_title}',
				feed_link = '{$feed_link}',
				category_id = '{$category_id}',
				banner_ads_id = '{$banner_ads_id}',
				sidebar_ads_id = '{$sidebar_ads_id}',
				category_status = '{$category_status}'
            ");
            
            // UPLOAD PROCESS: ADS LOGO AND ADS IMAGE
			$upload_directory = "../upload/{$UserID}/";
			$site_message_error = "";

			$target_file_1 = $upload_directory . basename($_FILES["content_image"]["name"]);
			$upload_status_1 = 1;
			$get_file_extension_1 = strtolower(pathinfo($target_file_1, PATHINFO_EXTENSION));
			
			if(!empty($_FILES["content_image"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_1 != "jpg" && $get_file_extension_1 != "jpeg" && $get_file_extension_1 != "png"){
					$upload_status_1 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["content_image"]["size"] > 1000000){
					$upload_status_1 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_1 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["content_image"]["tmp_name"], $target_file_1);
					$update_ads_logo_1 = $DB->query("UPDATE {$dbprefix}content SET content_image = '{$content_image}' WHERE content_id = '{$content_id}' AND user_id = '{$UserID}'");
				}
			}

			if($insert_content){
				$_SESSION["msg_success"] = "Content creation successful.";
				$_SESSION["msg_warning"] = $site_message_error;
				$content_data   = [
				    "UserID"        => $UserID,
				    "passed_id"     => $content_id,
				    "feed_link"     => $feed_link,
				    "content_image" => $content_image,
				    "dbprefix"      => $dbprefix,
				    "status"      => $status
				];
				$_SESSION["content_data"] = json_encode($content_data);
				unset( $_SESSION['is_content_save'] );
				redirect("index.php?cmd=contents");
			}
			else{
				$_SESSION["msg_error"] = "Content creation failure.";
			}
		}
		else{
			$update_content = $DB->query("UPDATE {$dbprefix}content SET 
				content_title = '{$content_title}',
				feed_link = '{$feed_link}',
				category_id = '{$category_id}',
				banner_ads_id = '{$banner_ads_id}',
				sidebar_ads_id = '{$sidebar_ads_id}',
				category_status = '{$category_status}'
			    WHERE content_id = '{$passed_id}' AND user_id = '{$UserID}'");
			    
			// UPLOAD PROCESS: ADS LOGO AND ADS IMAGE
			$upload_directory = "../upload/{$UserID}/";
            $site_message_error = "";
            
			$target_file_1 = $upload_directory . basename($_FILES["content_image"]["name"]);
			$upload_status_1 = 1;
			$get_file_extension_1 = strtolower(pathinfo($target_file_1, PATHINFO_EXTENSION));
			
			if(!empty($_FILES["content_image"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_1 != "jpg" && $get_file_extension_1 != "jpeg" && $get_file_extension_1 != "png"){
					$upload_status_1 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["content_image"]["size"] > 1000000){
					$upload_status_1 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_1 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["content_image"]["tmp_name"], $target_file_1);
					$update_ads_logo_1 = $DB->query("UPDATE {$dbprefix}content SET content_image = '{$content_image}' WHERE content_id = '{$passed_id}' AND user_id = '{$UserID}'");
				}
			}

			if($update_content){
				$_SESSION["msg_success"] = "Content update successful.";
				$_SESSION["msg_warning"] = $site_message_error;
				
				$content_data   = [
				    "UserID"        => $UserID,
				    "passed_id"     => $passed_id,
				    "feed_link"     => $feed_link,
				    "content_image" => $content_image,
				    "dbprefix"      => $dbprefix,
				    "status"      => $status
				];
				$_SESSION["content_data"] = json_encode($content_data);
				unset( $_SESSION['is_content_save'] );
				redirect("index.php?cmd=contents");
			}
			else{
				$_SESSION["msg_error"] = "Content update failure.";
			}
		}
	}
	
	// DELETE PAGES LOGO OR PAGES IMAGE
	if(!empty($_GET["delete"])){
		
		if($_GET["delete"] == "content_image"){
			$deletion_update_pages_image = $DB->query("UPDATE {$dbprefix}content SET content_image = '' WHERE content_id = '{$_GET["content_id"]}' AND user_id = '{$UserID}'");
		}

		if($deletion_update_pages_image){
			if(unlink("../upload/{$UserID}/{$_GET["content_image"]}")){
				redirect("index.php?cmd=contentsedit&id={$_GET["content_id"]}");
			}
		}
	}
?>