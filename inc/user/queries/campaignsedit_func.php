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

	// ============= GET CURRENT FILE NAME ============= //
	$current_file = pathinfo(__FILE__);

	if(strpos($current_file["filename"], "edit")){
		$filename = str_replace("edit", "", $current_file["filename"]);
	}
	else{
		$filename = $current_file["filename"];
	}

	// ============= VARIABLE INITIALIZATION ============= //
	$campaigns_type = $_GET["campaigns_type"];

	// ============= QUERIES ============= //
	// ============= INTEGRATIONS OR AUTORESPONDER ============= //
	// ============= ADD ROW FOR AUTORESPONDERS WITHOUT DATA YET ============= //
	foreach($AR_LIST as $key => $autoresponder){
		$check_1 = $DB->info("api", "user_id = '{$UserID}' AND platform = '{$key}'");

		if(!$check_1){
			$data = json_encode($autoresponder);
			$insert = $DB->query("INSERT INTO {$dbprefix}api SET user_id = '{$UserID}', data = '{$data}', platform = '{$key}'");
		}
	}

	// ============= SQL QUERIES ============= //
	$autoresponders     = $DB->query("SELECT * FROM {$dbprefix}api WHERE user_id = '{$UserID}'");
	$popups             = $DB->query("SELECT * FROM {$dbprefix}popup WHERE user_id = '{$UserID}' $and_query");
	$campaign           = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE campaigns_id = '{$passed_id}' AND campaigns_type = '{$campaigns_type}'")[0];
	$contents           = $DB->query("SELECT * FROM {$dbprefix}content WHERE user_id = '{$UserID}' $and_query");
	$content_collections  = $DB->query("SELECT * FROM {$dbprefix}content_collection WHERE user_id = '{$UserID}' $and_query");

	// ============= IF FORM SUBMITS ============= //
	if($_POST["submit"]){
		// ============= INITIALIZE POST VARIABLES ============= //
		$remove[] = "'";
		$remove[] = '"';
		$remove[] = "-";
		
		// ============= STRIP 1 ============= //
		$cs_stripped_1      = str_replace($remove, "", $_POST["campaigns_title"]);
		$campaigns_title    = $cs_stripped_1;

		$campaigns_type                 = $campaigns_type;
		$campaigns_theme_color          = $_POST["campaigns_theme_color"];
		$campaigns_theme_text_color     = $_POST["campaigns_theme_text_color"];
		$campaigns_theme_font           = $_POST["campaigns_theme_font"];
		$content_collection_id           = $_POST["content_collection_id"];
		$campaigns_header_image         = empty($_FILES["campaigns_header_image"]["name"]) ? $campaign["campaigns_header_image"] : $_FILES["campaigns_header_image"]["name"];
		$campaigns_body                 = strip($_POST["campaigns_body"]);
        $content_id                     = json_encode($_POST["content_id"]);

        // ============= INTEGRATIONS: AUTORESPONDER ============= //
		$campaigns_integrations_platform_name   = $_POST["campaigns_integrations_platform_name"];
		$campaigns_integrations_list_name       = $_POST["campaigns_integrations_list_name"];
		$campaigns_integrations_raw_html        = $_POST["campaigns_integrations_raw_html"];
	
		// ============= SOCIAL: LINKS ============= //
		$campaigns_facebook   = $_POST["campaigns_facebook"];
		$campaigns_twitter    = $_POST["campaigns_twitter"];
		$campaigns_instagram  = $_POST["campaigns_instagram"];
		$campaigns_youtube    = $_POST["campaigns_youtube"];
		
		// ============= AUTO RESPONDER FORM ============= //
		$optin_title                = $_POST["optin_title"];
		$optin_btn_title            = $_POST["optin_btn_title"];
		$campaigns_responder_image  = empty($_FILES["campaigns_responder_image"]["name"]) ? $campaign["campaigns_responder_image"] : $_FILES["campaigns_responder_image"]["name"];
		
		// ============= CALL TO ACTION FORM ============= //
		$c2a_title  = $_POST["c2a_title"];
		$c2a_btn_text  = $_POST["c2a_btn_text"];
		$c2a_btn_link  = $_POST["c2a_btn_link"];
		
		// ============= POPUP ============= //
		$popup_id  = $_POST["popup_id"];
        
		// ============= IF $passed_id HAS A VALUE ============= //
		if(empty($passed_id)){
			$campaigns_id = $DB->getauto("campaigns");
			$insert_campaign = $DB->query("INSERT INTO {$dbprefix}campaigns SET 
				campaigns_id                                        = '{$campaigns_id}', 
				user_id                                             = '{$UserID}', 
				subdomain_id                                        = '{$subdomain_id}',
				popup_id                                            = '{$popup_id}', 
				content_collection_id                               = '{$content_collection_id}', 
				campaigns_title                                     = '{$campaigns_title}', 
				campaigns_type                                      = '{$campaigns_type}', 
				campaigns_theme_color                               = '{$campaigns_theme_color}', 
				campaigns_theme_text_color                          = '{$campaigns_theme_text_color}', 
				campaigns_theme_font                                = '{$campaigns_theme_font}', 
				campaigns_header_image                              = '{$campaigns_header_image}', 
				campaigns_body                                      = '{$campaigns_body}', 
				content_id                                          = '{$content_id}', 
				campaigns_integrations_platform_name                = '{$campaigns_integrations_platform_name}', 
				campaigns_integrations_list_name                    = '{$campaigns_integrations_list_name}', 
				campaigns_integrations_raw_html                     = '{$campaigns_integrations_raw_html}', 
				campaigns_facebook                                  = '{$campaigns_facebook}', 
				campaigns_twitter                                   = '{$campaigns_twitter}', 
				campaigns_instagram                                 = '{$campaigns_instagram}', 
				campaigns_youtube                                   = '{$campaigns_youtube}', 
				optin_title                                         = '{$optin_title}', 
				optin_btn_title                                     = '{$optin_btn_title}', 
				c2a_title                                           = '{$c2a_title}', 
				c2a_btn_text                                        = '{$c2a_btn_text}', 
				c2a_btn_link                                        = '{$c2a_btn_link}'
			");

			// ============= UPLOAD PROCESS: PAGES LOGO AND PAGES IMAGE ============= //
			// ============= CAMPAIGNS HEADER IMAGE ============= //
			$upload_directory = "../upload/{$UserID}/";

			$target_file_1          = $upload_directory . basename($_FILES["campaigns_header_image"]["name"]);
			$upload_status_1        = 1;
			$get_file_extension_1   = strtolower(pathinfo($target_file_1, PATHINFO_EXTENSION));
			
			if(!empty($_FILES["campaigns_header_image"]["name"])){
				// ============= FILE EXTENSION CHECK ============= //
				if($get_file_extension_1 != "jpg" && $get_file_extension_1 != "jpeg" && $get_file_extension_1 != "png"){
					$upload_status_1 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["campaigns_header_image"]["size"] > 1000000){
					$upload_status_1 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_1 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["campaigns_header_image"]["tmp_name"], $target_file_1);
					$update_pages_logo_1 = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_header_image = '{$campaigns_header_image}' WHERE campaigns_id = '{$campaigns_id}' AND user_id = '{$UserID}'");
				}
			}
			
			// ============= CAMPAIGNS RESPONDER IMAGE ============= //
			$target_file_2          = $upload_directory . basename($_FILES["campaigns_responder_image"]["name"]);
			$upload_status_2        = 1;
			$get_file_extension_2   = strtolower(pathinfo($target_file_2, PATHINFO_EXTENSION));
			
			if(!empty($_FILES["campaigns_responder_image"]["name"])){
				// ============= FILE EXTENSION CHECK ============= //
				if($get_file_extension_2 != "jpg" && $get_file_extension_2 != "jpeg" && $get_file_extension_2 != "png"){
					$upload_status_2 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["campaigns_responder_image"]["size"] > 1000000){
					$upload_status_2 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_2 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["campaigns_responder_image"]["tmp_name"], $target_file_2);
					$update_pages_logo_2 = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_responder_image = '{$campaigns_responder_image}' WHERE campaigns_id = '{$campaigns_id}' AND user_id = '{$UserID}'");
				}
			}

			if($insert_campaign){
				$_SESSION["msg_success"] = "Campaign creation successful.";

				redirect("index.php?cmd=campaigns");
			}
			else{
				$_SESSION["msg_error"] = "Campaign creation failure.";
			}
		}
		else{
			$update_campaign = $DB->query("UPDATE {$dbprefix}campaigns SET 
			    popup_id                                            = '{$popup_id}', 
			    content_collection_id                               = '{$content_collection_id}', 
				campaigns_title                                     = '{$campaigns_title}', 
				campaigns_type                                      = '{$campaigns_type}', 
				campaigns_theme_color                               = '{$campaigns_theme_color}', 
				campaigns_theme_text_color                          = '{$campaigns_theme_text_color}', 
				campaigns_theme_font                                = '{$campaigns_theme_font}', 
				campaigns_header_image                              = '{$campaigns_header_image}', 
				campaigns_body                                      = '{$campaigns_body}', 
                content_id                                          = '{$content_id}', 
				campaigns_integrations_platform_name                = '{$campaigns_integrations_platform_name}', 
				campaigns_integrations_list_name                    = '{$campaigns_integrations_list_name}', 
				campaigns_integrations_raw_html                     = '{$campaigns_integrations_raw_html}', 
				campaigns_facebook                                  = '{$campaigns_facebook}', 
				campaigns_twitter                                   = '{$campaigns_twitter}', 
				campaigns_instagram                                 = '{$campaigns_instagram}', 
				campaigns_youtube                                   = '{$campaigns_youtube}', 
				optin_title                                         = '{$optin_title}', 
				optin_btn_title                                     = '{$optin_btn_title}', 
				c2a_title                                           = '{$c2a_title}', 
				c2a_btn_text                                        = '{$c2a_btn_text}', 
				c2a_btn_link                                        = '{$c2a_btn_link}'
				WHERE campaigns_id = '{$passed_id}' AND user_id = '{$UserID}'");

			// ============= UPLOAD PROCESS: PAGES LOGO AND PAGES IMAGE ============= //
			// ============= CAMPAIGNS HEADER IMAGE ============= //
			$upload_directory = "../upload/{$UserID}/";

			$target_file_1 = $upload_directory . basename($_FILES["campaigns_header_image"]["name"]);
			$upload_status_1 = 1;
			$get_file_extension_1 = strtolower(pathinfo($target_file_1, PATHINFO_EXTENSION));
			
			if(!empty($_FILES["campaigns_header_image"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_1 != "jpg" && $get_file_extension_1 != "jpeg" && $get_file_extension_1 != "png"){
					$upload_status_1 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["campaigns_header_image"]["size"] > 1000000){
					$upload_status_1 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_1 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["campaigns_header_image"]["tmp_name"], $target_file_1);
					$update_pages_logo_1 = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_header_image = '{$campaigns_header_image}' WHERE campaigns_id = '{$passed_id}' AND user_id = '{$UserID}'");
				}
			}
			
			// ============= CAMPAIGNS RESPONDER IMAGE ============= //
			$target_file_2          = $upload_directory . basename($_FILES["campaigns_responder_image"]["name"]);
			$upload_status_2        = 1;
			$get_file_extension_2   = strtolower(pathinfo($target_file_2, PATHINFO_EXTENSION));
			
			if(!empty($_FILES["campaigns_responder_image"]["name"])){
				// ============= FILE EXTENSION CHECK ============= //
				if($get_file_extension_2 != "jpg" && $get_file_extension_2 != "jpeg" && $get_file_extension_2 != "png"){
					$upload_status_2 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["campaigns_responder_image"]["size"] > 1000000){
					$upload_status_2 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_2 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["campaigns_responder_image"]["tmp_name"], $target_file_2);
					$update_pages_logo_2 = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_responder_image = '{$campaigns_responder_image}' WHERE campaigns_id = '{$passed_id}' AND user_id = '{$UserID}'");
				}
			}

			if($update_campaign){
				$_SESSION["msg_success"] = "Campaign update successful.";

				redirect("index.php?cmd=campaigns");
			}
			else{
				$_SESSION["msg_error"] = "Campaign update failure.";
			}
		}
	}

	// DELETE PAGES LOGO OR PAGES IMAGE
	if(!empty($_GET["delete"])){
		if($_GET["delete"] == "campaigns_header_image"){
			$delete_this = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_header_image = '' WHERE campaigns_id = '{$_GET['id']}'");
			
			if($delete_this){
    			if(unlink("../upload/{$UserID}/{$_GET['campaigns_header_image']}")){
    				redirect("index.php?cmd=campaignsedit&id={$_GET['id']}&campaigns_type={$campaigns_type}");
    			}
    		}
		}
		
		if($_GET["delete"] == "campaigns_responder_image"){
			$delete_this = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_responder_image = '' WHERE campaigns_id = '{$_GET['id']}'");
			
			if($delete_this){
    			if(unlink("../upload/{$UserID}/{$_GET['campaigns_responder_image']}")){
    				redirect("index.php?cmd=campaignsedit&id={$_GET['id']}&campaigns_type={$campaigns_type}");
    			}
    		}
		}

	}
?>