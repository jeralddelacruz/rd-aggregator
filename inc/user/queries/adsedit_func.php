<?php
    // CODE_SECTION_PHP_2: VARIABLE_INITIALIZATION
	$passed_id = $_GET["id"];
	$ads = $DB->info("ads2", "ads_id = '{$passed_id}' AND user_id = '{$UserID}'");
	
	$subdomain_id = "";
	if( $user_subdomain ){
	    $subdomain_id = $user_subdomain["subdomain_id"];
	}else{
	    $subdomain_id = "0";
	}

	// CODE_SECTION_PHP_3: BACKEND PROCESS
	if($_POST["submit"]){
		// POST VARIABLES
		$remove[] = "'";
		$remove[] = '"';
		$remove[] = "-";

		// STRIP 1
		$cs_stripped_1 = str_replace($remove, "", $_POST["ads_name"]);
		$ads_name = $cs_stripped_1;
		$ads_url = $_POST["ads_url"];
		$ads_type = $_POST["ads_type"];

        $ads_image = empty($_FILES["ads_image"]["name"]) ? $ads["ads_image"] : $_FILES["ads_image"]["name"];
		
		// IF $passed_id HAS A VALUE
		if(empty($passed_id)){
			$ads_id = $DB->getauto("ads2");
			$insert_ads = $DB->query("INSERT INTO {$dbprefix}ads2 SET 
				ads_id = '{$ads_id}', 
				user_id = '{$UserID}', 
				subdomain_id= '{$subdomain_id}',
				ads_name = '{$ads_name}',
				ads_url = '{$ads_url}',
				ads_type = '{$ads_type}'
            ");

            // UPLOAD PROCESS: ADS LOGO AND ADS IMAGE
			$upload_directory = "../upload/{$UserID}/";

			$target_file_1 = $upload_directory . basename($_FILES["ads_image"]["name"]);
			$upload_status_1 = 1;
			$get_file_extension_1 = strtolower(pathinfo($target_file_1, PATHINFO_EXTENSION));
			
			if(!empty($_FILES["ads_image"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_1 != "jpg" && $get_file_extension_1 != "jpeg" && $get_file_extension_1 != "png"){
					$upload_status_1 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["ads_image"]["size"] > 1000000){
					$upload_status_1 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_1 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["ads_image"]["tmp_name"], $target_file_1);
					$update_ads_logo_1 = $DB->query("UPDATE {$dbprefix}ads2 SET ads_image = '{$ads_image}' WHERE ads_id = '{$ads_id}' AND user_id = '{$UserID}'");
				}
			}

			if($insert_ads){
				$_SESSION["msg_success"] = "Ads creation successful.";

				redirect("index.php?cmd=ads2");
			}
			else{
				$_SESSION["msg_error"] = "Ads creation failure.";
			}
		}
		else{
			$update_ads = $DB->query("UPDATE {$dbprefix}ads2 SET 
				ads_name = '{$ads_name}',
				ads_url = '{$ads_url}',
				ads_type = '{$ads_type}'
			    WHERE ads_id = '{$passed_id}' AND user_id = '{$UserID}'");

            // UPLOAD PROCESS: ADS LOGO AND ADS IMAGE
			$upload_directory = "../upload/{$UserID}/";

			$target_file_1 = $upload_directory . basename($_FILES["ads_image"]["name"]);
			$upload_status_1 = 1;
			$get_file_extension_1 = strtolower(pathinfo($target_file_1, PATHINFO_EXTENSION));
			
			if(!empty($_FILES["ads_image"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_1 != "jpg" && $get_file_extension_1 != "jpeg" && $get_file_extension_1 != "png"){
					$upload_status_1 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["ads_image"]["size"] > 1000000){
					$upload_status_1 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_1 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["ads_image"]["tmp_name"], $target_file_1);
					$update_ads_logo_1 = $DB->query("UPDATE {$dbprefix}ads2 SET ads_image = '{$ads_image}' WHERE ads_id = '{$passed_id}' AND user_id = '{$UserID}'");
				}
			}

			if($update_ads){
				$_SESSION["msg_success"] = "Ads update successful.";

				redirect("index.php?cmd=ads2");
			}
			else{
				$_SESSION["msg_error"] = "Ads update failure.";
			}
		}
	}
?>