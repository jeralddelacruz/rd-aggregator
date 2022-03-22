<?php
	// PRIVILEGE
	if(!preg_match(";campaigns;", $cur_pack["pack_ar"])){
		redirect("index.php?cmd=deny");
	}

	// GET CURRENT FILE NAME
	$current_file = pathinfo(__FILE__);

	if(strpos($current_file["filename"], "edit")){
		$filename = str_replace("edit", "", $current_file["filename"]);
	}
	else{
		$filename = $current_file["filename"];
	}

	// VARIABLE INITIALIZATION
	$passed_id = $_GET["id"];
	$campaigns_type = "dfy";

	// QUERIES
	// INTEGRATIONS OR AUTORESPONDER
	// ADD ROW FOR AUTORESPONDERS WITHOUT DATA YET
	foreach($AR_LIST as $key => $autoresponder){
		$check_1 = $DB->info("api", "user_id = '{$UserID}' AND platform = '{$key}'");

		if(!$check_1){
			$data = json_encode($autoresponder);
			$insert = $DB->query("INSERT INTO {$dbprefix}api SET user_id = '{$UserID}', data = '{$data}', platform = '{$key}'");
		}
	}

	// SQL QUERIES
	$autoresponders     = $DB->query("SELECT * FROM {$dbprefix}api WHERE user_id = '{$UserID}'");
	$campaign           = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE campaigns_id = '{$passed_id}' AND campaigns_type = '{$campaigns_type}'")[0];
	$pages              = $DB->query("SELECT * FROM {$dbprefix}pages WHERE user_id = '{$UserID}'");
	$resourcesliders    = $DB->query("SELECT * FROM {$dbprefix}affiliate_links WHERE user_id = '{$UserID}'");

	// IF FORM SUBMITS
	if($_POST["submit"]){
		// INITIALIZE POST VARIABLES
		$remove[] = "'";
		$remove[] = '"';
		$remove[] = "-";
		
		// STRIP 1
		$cs_stripped_1      = str_replace($remove, "", $_POST["campaigns_title"]);
		$campaigns_title    = $cs_stripped_1;

		$campaigns_type         = $campaigns_type;
		$campaigns_theme_color  = $_POST["campaigns_theme_color"];
		$campaigns_theme_text_color  = $_POST["campaigns_theme_text_color"];
		$campaigns_theme_font   = $_POST["campaigns_theme_font"];
		
		$campaigns_modal_headline       = strip($_POST["campaigns_modal_headline"]);
		$campaigns_modal_sub_headline   = strip($_POST["campaigns_modal_sub_headline"]);
		$campaigns_modal_image          = empty($_FILES["campaigns_modal_image"]["name"]) ? $campaign["campaigns_modal_image"] : $_FILES["campaigns_modal_image"]["name"];
		$campaigns_modal_button_text    = strip($_POST["campaigns_modal_button_text"]);
		$campaigns_modal_button_link    = strip($_POST["campaigns_modal_button_link"]);
		$campaigns_logo                 = empty($_FILES["campaigns_logo"]["name"]) ? $campaign["campaigns_logo"] : $_FILES["campaigns_logo"]["name"];
		$campaigns_headline             = strip($_POST["campaigns_headline"]);
		$campaigns_headline_alignment   = $_POST["campaigns_headline_alignment"];
		$campaigns_body                 = strip($_POST["campaigns_body"]);
		$campaigns_body_alignment       = $_POST["campaigns_body_alignment"];
		$campaigns_background_image     = empty($_FILES["campaigns_background_image"]["name"]) ? $campaign["campaigns_background_image"] : $_FILES["campaigns_background_image"]["name"];
		$campaigns_button_text          = strip($_POST["campaigns_button_text"]);
		$campaigns_tab1                 = $_POST["campaigns_tab1"];
		$campaigns_tab2                 = $_POST["campaigns_tab2"];
		$campaigns_tab3                 = $_POST["campaigns_tab3"];
		$included_tab1_resource_ids     = json_encode($_POST["included_tab1_resource_ids"]);
		$included_tab2_resource_ids     = json_encode($_POST["included_tab2_resource_ids"]);
		$included_tab3_resource_ids     = json_encode($_POST["included_tab3_resource_ids"]);

		// STRIP 2
		$included_article_pages_ids = json_encode($_POST["included_article_pages_ids"]);

		// STRIP 3
		$included_webinar_pages_ids = $_POST["included_webinar_page_id"];
		
		// STRIP 3
		$included_ads_id = $_POST["included_ads_id"];
		
		// STRIP 3
		$included_c2a_id = $_POST["included_c2a_id"];
		
		// INTEGRATIONS: AUTORESPONDER
		$campaigns_integrations_platform_name = $_POST["campaigns_integrations_platform_name"];
		$campaigns_integrations_list_name = $_POST["campaigns_integrations_list_name"];
		$campaigns_integrations_raw_html = $_POST["campaigns_integrations_raw_html"];

		// FACEBOOK TOOLS
		$campaigns_facebook_tools_preset = $_POST["campaigns_facebook_tools_preset"];
		$campaigns_facebook_tools_pixel_code_snippet = $_POST["campaigns_facebook_tools_pixel_code_snippet"];
		$campaigns_facebook_tools_comments_sdk = $_POST["campaigns_facebook_tools_comments_sdk"];
		$campaigns_facebook_tools_comments_code_snippet = $_POST["campaigns_facebook_tools_comments_code_snippet"];
		$campaigns_facebook_tools_chat_sdk_and_code_snippet = strip($_POST["campaigns_facebook_tools_chat_sdk_and_code_snippet"], 0);
		
		// IF $passed_id HAS A VALUE
		if(empty($passed_id)){
			$campaigns_id = $DB->getauto("campaigns");
			$insert_campaign = $DB->query("INSERT INTO {$dbprefix}campaigns SET 
				campaigns_id = '{$campaigns_id}', 
				user_id = '{$UserID}', 
				campaigns_title = '{$campaigns_title}', 
				campaigns_type = '{$campaigns_type}', 
				campaigns_theme_color = '{$campaigns_theme_color}', 
				campaigns_theme_text_color = '{$campaigns_theme_text_color}', 
				campaigns_theme_font = '{$campaigns_theme_font}', 
				campaigns_modal_headline = '{$campaigns_modal_headline}',
				campaigns_modal_sub_headline = '{$campaigns_modal_sub_headline}',
				campaigns_modal_button_text = '{$campaigns_modal_button_text}',
				campaigns_modal_button_link = '{$campaigns_modal_button_link}',
				campaigns_logo = '{$campaigns_logo}', 
				campaigns_headline = '{$campaigns_headline}', 
				campaigns_headline_alignment = '{$campaigns_headline_alignment}', 
				campaigns_body = '{$campaigns_body}', 
				campaigns_body_alignment = '{$campaigns_body_alignment}', 
				campaigns_button_text = '{$campaigns_button_text}', 
				campaigns_background_image = '{$campaigns_background_image}', 
				included_article_pages_ids = '{$included_article_pages_ids}', 
				included_webinar_page_id = '{$included_webinar_pages_ids}', 
				included_ads_id = '{$included_ads_id}',
				included_c2a_id = '{$included_c2a_id}', 
				campaigns_integrations_platform_name = '{$campaigns_integrations_platform_name}', 
				campaigns_integrations_list_name = '{$campaigns_integrations_list_name}', 
				campaigns_integrations_raw_html = '{$campaigns_integrations_raw_html}', 
				campaigns_facebook_tools_preset = '{$campaigns_facebook_tools_preset}', 
				campaigns_facebook_tools_pixel_code_snippet = '{$campaigns_facebook_tools_pixel_code_snippet}', 
				campaigns_facebook_tools_comments_sdk = '{$campaigns_facebook_tools_comments_sdk}', 
				campaigns_facebook_tools_comments_code_snippet = '{$campaigns_facebook_tools_comments_code_snippet}', 
				campaigns_facebook_tools_chat_sdk_and_code_snippet = '{$campaigns_facebook_tools_chat_sdk_and_code_snippet}',
				campaigns_tab1 = '{$campaigns_tab1}',
                campaigns_tab2 = '{$campaigns_tab2}',
                campaigns_tab3 = '{$campaigns_tab3}',
                included_tab1_resource_ids = '{$included_tab1_resource_ids}',
                included_tab2_resource_ids = '{$included_tab2_resource_ids}',
                included_tab3_resource_ids = '{$included_tab3_resource_ids}'
			");

			// UPLOAD PROCESS: PAGES LOGO AND PAGES IMAGE
			$upload_directory = "../upload/{$UserID}/";

			$target_file_1 = $upload_directory . basename($_FILES["campaigns_logo"]["name"]);
			$upload_status_1 = 1;
			$get_file_extension_1 = strtolower(pathinfo($target_file_1, PATHINFO_EXTENSION));
			
			if(!empty($_FILES["campaigns_logo"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_1 != "jpg" && $get_file_extension_1 != "jpeg" && $get_file_extension_1 != "png"){
					$upload_status_1 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["campaigns_logo"]["size"] > 1000000){
					$upload_status_1 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_1 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["campaigns_logo"]["tmp_name"], $target_file_1);
					$update_pages_logo_1 = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_logo = '{$campaigns_logo}' WHERE campaigns_id = '{$campaigns_id}' AND user_id = '{$UserID}'");
				}
			}

			$target_file_2 = $upload_directory . basename($_FILES["campaigns_background_image"]["name"]);
			$upload_status_2 = 1;
			$get_file_extension_2 = strtolower(pathinfo($target_file_2, PATHINFO_EXTENSION));

			if(!empty($_FILES["campaigns_background_image"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_2 != "jpg" && $get_file_extension_2 != "jpeg" && $get_file_extension_2 != "png"){
					$upload_status_2 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["campaigns_background_image"]["size"] > 1000000){
					$upload_status_2 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_2 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["campaigns_background_image"]["tmp_name"], $target_file_2);
					$update_pages_logo_2 = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_background_image = '{$campaigns_background_image}' WHERE campaigns_id = '{$campaigns_id}' AND user_id = '{$UserID}'");
				}
			}
			
			$target_file_3 = $upload_directory . basename($_FILES["campaigns_modal_image"]["name"]);
			$upload_status_3 = 1;
			$get_file_extension_3 = strtolower(pathinfo($target_file_3, PATHINFO_EXTENSION));

			if(!empty($_FILES["campaigns_modal_image"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_3 != "jpg" && $get_file_extension_3 != "jpeg" && $get_file_extension_3 != "png"){
					$upload_status_3 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["campaigns_modal_image"]["size"] > 1000000){
					$upload_status_3 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_3 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["campaigns_modal_image"]["tmp_name"], $target_file_3);
					$update_pages_logo_3 = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_modal_image = '{$campaigns_modal_image}' WHERE campaigns_id = '{$campaigns_id}' AND user_id = '{$UserID}'");
				}
			}

			if($insert_campaign){
				$_SESSION["msg_success"] = "Campaign creation successful.";

				redirect("index.php?cmd=campaignsdfy");
			}
			else{
				$_SESSION["msg_error"] = "Campaign creation failure.";
			}
		}
		else{
			$update_campaign = $DB->query("UPDATE {$dbprefix}campaigns SET 
				campaigns_title = '{$campaigns_title}', 
				campaigns_type = '{$campaigns_type}', 
				campaigns_theme_color = '{$campaigns_theme_color}', 
				campaigns_theme_text_color = '{$campaigns_theme_text_color}', 
				campaigns_theme_font = '{$campaigns_theme_font}', 
				campaigns_modal_headline = '{$campaigns_modal_headline}', 
				campaigns_modal_sub_headline = '{$campaigns_modal_sub_headline}',
				campaigns_modal_button_text = '{$campaigns_modal_button_text}',
				campaigns_modal_button_link = '{$campaigns_modal_button_link}', 
				campaigns_logo = '{$campaigns_logo}', 
				campaigns_headline = '{$campaigns_headline}', 
				campaigns_headline_alignment = '{$campaigns_headline_alignment}', 
				campaigns_body = '{$campaigns_body}', 
				campaigns_body_alignment = '{$campaigns_body_alignment}', 
				campaigns_button_text = '{$campaigns_button_text}', 
				campaigns_background_image = '{$campaigns_background_image}', 
				included_article_pages_ids = '{$included_article_pages_ids}', 
				included_webinar_page_id = '{$included_webinar_pages_ids}', 
				included_ads_id = '{$included_ads_id}', 
				included_c2a_id = '{$included_c2a_id}', 
				campaigns_integrations_platform_name = '{$campaigns_integrations_platform_name}', 
				campaigns_integrations_list_name = '{$campaigns_integrations_list_name}', 
				campaigns_integrations_raw_html = '{$campaigns_integrations_raw_html}', 
				campaigns_facebook_tools_preset = '{$campaigns_facebook_tools_preset}', 
				campaigns_facebook_tools_pixel_code_snippet = '{$campaigns_facebook_tools_pixel_code_snippet}', 
				campaigns_facebook_tools_comments_sdk = '{$campaigns_facebook_tools_comments_sdk}', 
				campaigns_facebook_tools_comments_code_snippet = '{$campaigns_facebook_tools_comments_code_snippet}', 
				campaigns_facebook_tools_chat_sdk_and_code_snippet = '{$campaigns_facebook_tools_chat_sdk_and_code_snippet}',
				campaigns_tab1 = '{$campaigns_tab1}',
                campaigns_tab2 = '{$campaigns_tab2}',
                campaigns_tab3 = '{$campaigns_tab3}',
                included_tab1_resource_ids = '{$included_tab1_resource_ids}',
                included_tab2_resource_ids = '{$included_tab2_resource_ids}',
                included_tab3_resource_ids = '{$included_tab3_resource_ids}'
				WHERE campaigns_id = '{$passed_id}' AND user_id = '{$UserID}'");

			// UPLOAD PROCESS: PAGES LOGO AND PAGES IMAGE
			$upload_directory = "../upload/{$UserID}/";

			$target_file_1 = $upload_directory . basename($_FILES["campaigns_logo"]["name"]);
			$upload_status_1 = 1;
			$get_file_extension_1 = strtolower(pathinfo($target_file_1, PATHINFO_EXTENSION));
			
			if(!empty($_FILES["campaigns_logo"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_1 != "jpg" && $get_file_extension_1 != "jpeg" && $get_file_extension_1 != "png"){
					$upload_status_1 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["campaigns_logo"]["size"] > 1000000){
					$upload_status_1 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_1 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["campaigns_logo"]["tmp_name"], $target_file_1);
					$update_pages_logo_1 = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_logo = '{$campaigns_logo}' WHERE campaigns_id = '{$passed_id}' AND user_id = '{$UserID}'");
				}
			}

			$target_file_2 = $upload_directory . basename($_FILES["campaigns_background_image"]["name"]);
			$upload_status_2 = 1;
			$get_file_extension_2 = strtolower(pathinfo($target_file_2, PATHINFO_EXTENSION));

			if(!empty($_FILES["campaigns_background_image"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_2 != "jpg" && $get_file_extension_2 != "jpeg" && $get_file_extension_2 != "png"){
					$upload_status_2 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["campaigns_background_image"]["size"] > 1000000){
					$upload_status_2 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_2 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["campaigns_background_image"]["tmp_name"], $target_file_2);
					$update_pages_logo_2 = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_background_image = '{$campaigns_background_image}' WHERE campaigns_id = '{$passed_id}' AND user_id = '{$UserID}'");
				}
			}
			
			$target_file_3 = $upload_directory . basename($_FILES["campaigns_modal_image"]["name"]);
			$upload_status_3 = 1;
			$get_file_extension_3 = strtolower(pathinfo($target_file_3, PATHINFO_EXTENSION));

			if(!empty($_FILES["campaigns_modal_image"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_3 != "jpg" && $get_file_extension_3 != "jpeg" && $get_file_extension_3 != "png"){
					$upload_status_3 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["campaigns_modal_image"]["size"] > 1000000){
					$upload_status_3 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_3 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["campaigns_modal_image"]["tmp_name"], $target_file_3);
					$update_pages_logo_3 = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_modal_image = '{$campaigns_modal_image}' WHERE campaigns_id = '{$passed_id}' AND user_id = '{$UserID}'");
				}
			}

			if($update_campaign){
				$_SESSION["msg_success"] = "Campaign update successful.";

				redirect("index.php?cmd=campaignsdfy");
			}
			else{
				$_SESSION["msg_error"] = "Campaign update failure.";
			}
		}
	}

	// DELETE PAGES LOGO OR PAGES IMAGE
	if(!empty($_GET["delete"])){
		if($_GET["delete"] == "campaigns_logo"){
			$delete_this = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_logo = '' WHERE campaigns_id = '{$_GET["campaigns_id"]}'");
		}

		if($delete_this){
			if(unlink("../upload/{$UserID}/{$_GET["campaigns_logo"]}")){
				redirect("index.php?cmd=campaignsdfyedit&id={$_GET["campaigns_id"]}");
			}
		}

		if($_GET["delete"] == "campaigns_background_image"){
			$delete_this = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_background_image = '' WHERE campaigns_id = '{$_GET["campaigns_id"]}'");
		}

		if($delete_this){
			if(unlink("../upload/{$UserID}/{$_GET["campaigns_background_image"]}")){
				redirect("index.php?cmd=campaignsdfyedit&id={$_GET["campaigns_id"]}");
			}
		}
	}
?>
<style type="text/css">
	.img-overlay-custom{
		position: absolute;
		top: 0px; left: 0px;

		width: 100%; height: 100%;

		display: block;

		box-sizing: border-box;

		background-color: rgba(0, 0, 0, 0);

		transition: background-color .2s;
	}

	.img-overlay-custom a{
		opacity: 0;

		transition: opacity .2s;
	}

	.img-overlay-custom:hover{
		background-color: rgba(0, 0, 0, .4);
	}

	.img-overlay-custom:hover a{
		opacity: 1;
	}

	/* PAGES CONTENT OPTIONS STYLE */
	.pages-content-options{
		cursor: pointer;

		transition: background-color .2s ease-in-out;
	}

	.pages-content-options:hover{
		background-color: gainsboro;
	}
	
	input.form-control.campaign-color {
        height: 38px;
        padding: 0px;
        width: 100%;
    }
    
    hr {
        border-top: 1px solid #14213D !important;
    }
</style>
<!-- CODE_SECTION_HTML_1: CONTENT_MAIN -->
<div class="container-fluid px-0">
	<form method="POST" enctype="multipart/form-data">
		<!-- CODE_SECTION_PHP_HTML_1: SUCCESS_AND_ERROR_ALERT -->
		<?php if($_SESSION["msg_error"]) : ?>
		<div class="col-md-12">
			<div class="alert alert-danger"><?php echo $_SESSION["msg_error"]; $_SESSION["msg_error"] = ""; ?></div>
		</div>
		<?php endif; ?>

		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div class="d-flex flex-row justify-content-between align-items-center">
						<div class="p-2">
							<h4 style="padding: 10px;"><?= $index_title; ?></h4>
						</div>
						<div class="d-flex">
    						<div class="p-2 <?= empty($passed_id) ? "mr-auto" : ""; ?>">
    							<a class="btn btn-outline-secondary" href="index.php?cmd=campaignsdfy"><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;Back to Campaigns Table</a>
    						</div>
    						<?php if(!empty($passed_id)) : ?>
    						<div class="p-2">
    							<button class="btn btn-outline-secondary" data-toggle="modal" data-target="#delete-modal" 
    							data-pages-id="<?= $campaign["campaigns_id"]; ?>" 
    							onclick="getAttributes(this)" type="button">Delete this campaign</button>
    						</div>
    						<?php endif; ?>
    					</div>
					</div>
				</div>
				<div class="card-body">
				    <div class="row">
				        <div class="col-md-12">
				            <!-- PAGE TITLE AND TYPE -->
        					<div class="form-group">
        						<div class="row">
        							<div class="col-md-12">
        								<label>Campaign Title</label>
        								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Give your campaign a title. Please keep the title around 50 characters only."><i class="fa fa-question"></i></span>
        
        								<input class="form-control" type="text" name="campaigns_title" value="<?= $campaign["campaigns_title"]; ?>" maxlength="50" />
        							</div>
        						</div>
        					</div>
				        </div>
				        <div class="col-md-6">
				            <!-- THEME SETTINGS -->
        					<div class="form-group">
        					    
        						<div class="card">
        							<div class="card-body">
        								<h4 class="text-center mb-3">Theme Settings</h4>
        
        								<div class="row mb-2">
        									<div class="col-md-6">
        										<label>Campaign Theme Color</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The theme color for your campaign."><i class="fa fa-question"></i></span>
        										<input type="color" class="form-control campaign-color" name="campaigns_theme_color" value="<?= $campaign["campaigns_theme_color"]; ?>"/>
        									</div>
                                            <div class="col-md-6">
        										<label>Campaign Theme Text Color</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The theme color for your campaign text."><i class="fa fa-question"></i></span>
        										<input type="color" class="form-control campaign-color" name="campaigns_theme_text_color" value="<?= $campaign["campaigns_theme_text_color"]; ?>"/>
        									</div>
        									<div class="col-md-12">
        										<label>Campaign Theme Font</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The theme font for your campaign."><i class="fa fa-question"></i></span>
        
        										<select class="form-control" name="campaigns_theme_font">
        											<option selected disabled>Select an option</option>
        											<option value="" <?= ($campaign["campaigns_theme_font"] == "") ? "selected" : ""; ?>>Regular</option>
        											<option value="Verdana" <?= ($campaign["campaigns_theme_font"]) ? "selected" : ""; ?>>Verdana</option>
        											<option value="Tahoma" <?= ($campaign["campaigns_theme_font"]) ? "selected" : ""; ?>>Tahoma</option>
        											<option value="Times New Roman" <?= ($campaign["campaigns_theme_font"]) ? "selected" : ""; ?>>Times New Roman</option>
        										</select>
        									</div>
        								</div>
        							</div>
        						</div>
        					</div>
        					<!-- MODAL SETTINGS -->
        					<div class="form-group">
        						<div class="card">
        							<div class="card-body">
        								<h4 class="text-center mb-3">Pop-Up Settings</h4>
        
        								<div class="row mb-2">
        									<div class="col-md-12">
        										<label>Headline</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The headline of your pop-up."><i class="fa fa-question"></i></span>
        
        										<input class="form-control" type="text" name="campaigns_modal_headline" value="<?= $campaign["campaigns_modal_headline"]; ?>" maxlength="100" />
        									</div>
        									<div class="col-md-12">
        										<label>Subheadline</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The sub headline of your pop-up."><i class="fa fa-question"></i></span>
        
        										<input class="form-control" type="text" name="campaigns_modal_sub_headline" value="<?= $campaign["campaigns_modal_sub_headline"]; ?>" maxlength="100" />
        									</div>
        									<div class="col-md-12">
        										<label>Popup Image</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The image your pop-up."><i class="fa fa-question"></i></span>
                                                
        										<input class="form-control-file" id="campaigns-modal-image" type="file" name="campaigns_modal_image" value="" <?= (!empty($campaign["campaigns_modal_image"])) ? "disabled" : "" ; ?> style="<?= (!empty($campaign["campaigns_modal_image"])) ? "cursor: not-allowed;" : ""; ?>" maxlength="100" />
        									</div>
        									<!-- MODAL IMAGE PREVIEW -->
        									<div class="col-md-12" id="campaigns-modal-preview-container" style="<?= !empty($campaign["campaigns_modal_image"]) ? "display: block;" : "display: none;"; ?>">
        										<h5 class="mt-3">Preview</h5>
        
        										<img class="img-fluid rounded bg-secondary" id="campaigns-modal-preview-image" src="<?= "../upload/{$UserID}/" . $campaign["campaigns_modal_image"]; ?>" />
        
        										<div class="img-overlay-custom text-center">
        											<a class="btn btn-danger mt-2" href="<?= "index.php?cmd=campaignsdfyedit&delete=campaigns_modal_image&campaigns_id={$campaign["campaigns_id"]}&campaigns_modal_image={$campaign["campaigns_modal_image"]}"; ?>">Delete</a>
        										</div>
        									</div>
        
        									<div class="col-md-6">
        										<label>Button Text</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The button text of your pop-up."><i class="fa fa-question"></i></span>
        
        										<input class="form-control" type="text" name="campaigns_modal_button_text" value="<?= $campaign["campaigns_modal_button_text"]; ?>" maxlength="25" />
        									</div>
        									<div class="col-md-6">
        										<label>Affiliate Link</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="You'r affiliate link."><i class="fa fa-question"></i></span>
        
        										<input class="form-control" type="text" name="campaigns_modal_button_link" value="<?= $campaign["campaigns_modal_button_link"]; ?>" maxlength="25" />
        									</div>
        								</div>
        							</div>
        						</div>
        					</div>
        					<!-- INTEGRATIONS: AUTORESPONDERS -->
        					<div class="form-group">
        						<div class="card">
        							<div class="content card-body">
        								<center><h4 class="mb-3"><i class="fa fa-cogs"></i> &nbsp;Integration Settings</h4></center>
        
        								<div class="form-group">
        									<label>Choose Autoresponder</label>
        									<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Connect your Autoresponder in the Integrations Menu."><i class="fa fa-question" aria-hidden="true"></i></span>
        									
        									<select name="campaigns_integrations_platform_name" class="form-control">
        										<option disabled selected> Choose one </option>
        										<option value="html"  <?= ($campaign["campaigns_integrations_platform_name"] == "html") ? "selected" : ""; ?>> Raw HTML Form</option>
        										<?php foreach($autoresponders as $key => $autoresponder) : ?>
        											<?php $data = json_decode($autoresponder['data']); ?>
        											<?php ${$data->key} = $data; ?>
        											
        											<option value="<?= ${$data->key}->key; ?>" <?= ($campaign["campaigns_integrations_platform_name"] == ${$data->key}->key) ? "selected" : ""; ?> <?= empty($data->list) ? "disabled" : ""; ?>><?php echo ${$data->key}->name; ?></option>
        										<?php endforeach; ?>
        									</select>
        								</div>
        
        								<div class="form-group autoresponderList" style="display:none;">
        									<label>Choose List / Tags</label>
        									<select name="campaigns_integrations_list_name" class="form-control">
        										<?php foreach($autoresponders as $key => $autoresponder) : ?>
        											<?php $data = json_decode($autoresponder['data']); ?>
        										
        											<?php foreach($data->list as $list) : ?>
        												<option data-type="<?php echo $data->key; ?>" value="<?php echo $list->id; ?>" <?php echo ($campaign["campaigns_integrations_list_name"] ==  $list->id) ? 'selected' : ''; ?>> <?php echo $list->name; ?> </option>
        											<?php endforeach; ?>
        										<?php endforeach; ?>
        									</select>
        								</div>
        					
        								<div class="form-group autoresponderForm">
        									<label>Autoresponder Form Code</label>
        									<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Raw HTML code here."><i class="fa fa-question" aria-hidden="true"></i></span>
        									
        									<textarea name="campaigns_integrations_raw_html" rows="3" placeholder="Insert Autoresponder Form Code Here" class="form-control"></textarea>
        								</div>
        							</div>
        						</div>
        					</div>
        					<!-- RESOURCE: SLIDER -->
        					<div class="form-group">
        						<div class="card">
        							<div class="content card-body">
        								<center><h4 class="mb-3"><i class="fa fa-cogs"></i> &nbsp;Resource Slider</h4></center>
        
        								<?php if($pages) : ?>
            							<div class="row mb-2">
            							    <div class="col-md-12">
        										<label>Tab 1</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The Tab 1 name."><i class="fa fa-question"></i></span>
        										<input class="form-control" type="text" name="campaigns_tab1" value="<?= $campaign["campaigns_tab1"]; ?>" maxlength="100" />
        									</div>
            								<div class="col-md-12">
            									<label>I want these Items</label>
            									<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Include items to tab 1"><i class="fa fa-question"></i></span>
            									<div class="px-3 py-1 rounded" style="height: 220px; overflow: auto; border: 1px solid gainsboro;">
        											<?php $selected_tab1_resource = json_decode($campaign["included_tab1_resource_ids"]); ?>
        											<?php foreach($resourcesliders as $key => $resourceslider) : ?>
        											<div class="form-check">
        												<input class="form-check-input included-articles" type="checkbox" name="included_tab1_resource_ids[]" value="<?= $resourceslider["affiliate_links_id"]; ?>" <?= (in_array($resourceslider["affiliate_links_id"], $selected_tab1_resource)) ? "checked" : ""; ?> />
        												<label class="form-check-label"><u><?= $resourceslider["affiliate_links_product_name"]; ?></u></label>
        											</div>
        											<?php endforeach; ?>
        										</div>
            								</div>
            							</div>
            							<hr />
            							<div class="row mb-2">
                                            <div class="col-md-12">
        										<label>Tab 2</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The Tab 2 name"><i class="fa fa-question"></i></span>
        										<input class="form-control" type="text" name="campaigns_tab2" value="<?= $campaign["campaigns_tab2"]; ?>" maxlength="100" />
        									</div>
            								<div class="col-md-12">
            									<label>I want these Items</label>
            									<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Include items to tab 2"><i class="fa fa-question"></i></span>
            									<div class="px-3 py-1 rounded" style="height: 220px; overflow: auto; border: 1px solid gainsboro;">
        											<?php $selected_tab2_resource = json_decode($campaign["included_tab2_resource_ids"]); ?>
        											<?php foreach($resourcesliders as $resourceslider) : ?>
        											<div class="form-check">
        												<input class="form-check-input included-articles" type="checkbox" name="included_tab2_resource_ids[]" value="<?= $resourceslider["affiliate_links_id"]; ?>" <?= (in_array($resourceslider["affiliate_links_id"], $selected_tab2_resource)) ? "checked" : ""; ?> />
        												<label class="form-check-label"><u><?= $resourceslider["affiliate_links_product_name"]; ?></u></label>
        											</div>
        											<?php endforeach; ?>
        										</div>
            								</div>
            							</div>
            							<hr />
            							<div class="row mb-2">
                                            <div class="col-md-12">
        										<label>Tab 3</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The Tab 3 name"><i class="fa fa-question"></i></span>
        										<input class="form-control" type="text" name="campaigns_tab3" value="<?= $campaign["campaigns_tab3"]; ?>" maxlength="100" />
        									</div>
            								<div class="col-md-12">
            									<label>I want these Items</label>
            									<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Include items to tab 3."><i class="fa fa-question"></i></span>
            									<div class="px-3 py-1 rounded" style="height: 220px; overflow: auto; border: 1px solid gainsboro;">
        											<?php $selected_tab3_resource = json_decode($campaign["included_tab3_resource_ids"]); ?>
        											<?php foreach($resourcesliders as $resourceslider) : ?>
        											<div class="form-check">
        												<input class="form-check-input included-articles" type="checkbox" name="included_tab3_resource_ids[]" value="<?= $resourceslider["affiliate_links_id"]; ?>" <?= (in_array($resourceslider["affiliate_links_id"], $selected_tab3_resource)) ? "checked" : ""; ?> />
        												<label class="form-check-label"><u><?= $resourceslider["affiliate_links_product_name"]; ?></u></label>
        											</div>
        											<?php endforeach; ?>
        										</div>
            								</div>
            							</div>
            							<?php else : ?>
            							<hr />
            							<h5 class="text-center">Oops! You don't have any resource! <a href="index.php?cmd=pages">Create one now!</a></h5>
            							<?php endif; ?>
        							</div>
        						</div>
        					</div>
				        </div>
				        <div class="col-md-6">
				            <!-- PAGES SETTINGS -->
        					<div class="form-group">
        						<div class="card">
        							<div class="card-body">
        								<h4 class="text-center mb-3">Pages Settings</h4>
        
        								<div class="row mb-2">
        									<div class="col-md-12">
        										<label>Page Logo</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The logo of your page."><i class="fa fa-question"></i></span>
        
        										<input class="form-control-file" id="campaigns-logo" type="file" name="campaigns_logo" value="" <?= (!empty($campaign["campaigns_logo"])) ? "disabled" : "" ; ?> style="<?= (!empty($campaign["campaigns_logo"])) ? "cursor: not-allowed;" : ""; ?>" />
        									</div>
        									<!-- PAGE LOGO PREVIEW -->
        									<div class="col-md-12" id="campaigns-logo-preview-container" style="<?= !empty($campaign["campaigns_logo"]) ? "display: block;" : "display: none;"; ?>">
        										<h5 class="mt-3">Preview</h5>
        
        										<img class="img-fluid rounded bg-secondary" id="campaigns-logo-preview" src="<?= "../upload/{$UserID}/" . $campaign["campaigns_logo"]; ?>" />
        
        										<div class="img-overlay-custom text-center">
        											<a class="btn btn-danger mt-2" href="<?= "index.php?cmd=campaignsdfyedit&delete=campaigns_logo&campaigns_id={$campaign["campaigns_id"]}&campaigns_logo={$campaign["campaigns_logo"]}"; ?>">Delete</a>
        										</div>
        									</div>
        
        									<div class="col-md-12">
        										<label>Page Headline</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The headline of your home page."><i class="fa fa-question"></i></span>
        									
        										<input class="form-control" type="text" name="campaigns_headline" value="<?= $campaign["campaigns_headline"]; ?>" />
        									</div>
        									<div class="col-md-12">
        										<label>Page Body</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The body text of your home page. You can adjust the text alignment using clicking L, C, and R buttons in the right corner."><i class="fa fa-question"></i></span>
        										<textarea class="form-control" name="campaigns_body" rows="7"><?= $campaign["campaigns_body"]; ?></textarea>
        									</div>
        								</div>
        
        								<div class="row mb-2 align-items-center">
        									<div class="col-md-12">
        										<label class="mt-3">Background Image</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The background image for your home page."><i class="fa fa-question"></i></span>
        
        										<input class="form-control-file" id="campaigns-background-image" type="file" name="campaigns_background_image" value="" <?= (!empty($campaign["campaigns_background_image"])) ? "disabled" : "" ; ?> style="<?= (!empty($campaign["campaigns_background_image"])) ? "cursor: not-allowed;" : ""; ?>" />
        									</div>
        
        									<!-- CAMPAIGN BACKGROUND IMAGE PREVIEW -->
        									<div class="col-md-12" id="campaigns-background-image-preview-container" style="<?= !empty($campaign["campaigns_background_image"]) ? "display: block;" : "display: none;"; ?>">
        										<h5 class="mt-3">Preview</h5>
        
        										<img class="img-fluid rounded bg-secondary" id="campaigns-background-image-preview" src="<?= "../upload/{$UserID}/" . $campaign["campaigns_background_image"]; ?>" />
        
        										<div class="img-overlay-custom text-center">
        											<a class="btn btn-danger mt-2" href="<?= "index.php?cmd=campaignsdfyedit&delete=campaigns_background_image&campaigns_id={$campaign["campaigns_id"]}&campaigns_background_image={$campaign["campaigns_background_image"]}"; ?>">Delete</a>
        										</div>
        									</div>
        								</div>
        
        								<div class="row mb-2">
        									<div class="col-md-12">
        										<label>Page Button Text</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The button text of your home page."><i class="fa fa-question"></i></span>
        
        										<input class="form-control" type="text" name="campaigns_button_text" value="<?= $campaign["campaigns_button_text"]; ?>" />
        									</div>
        								</div>
        
        								<?php if($pages) : ?>
        								<div class="row mb-2">
        									<div class="col-md-12">
        										<label>I want these Article Pages</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Include 3 Article Pages to your Campaign."><i class="fa fa-question"></i></span>
        
        										<div class="px-3 py-1 rounded" style="height: 220px; overflow: auto; border: 1px solid gainsboro;">
        											<?php $selected_articles = json_decode( $campaign["included_article_pages_ids"] ); ?>
        											<?php foreach($pages as $page) : ?>
        											<?php if($page["pages_type"] == "article") : ?>
        											<div class="form-check">
        												<input class="form-check-input included-articles" type="checkbox" name="included_article_pages_ids[]" value="<?= $page["pages_id"]; ?>" <?= in_array($page["pages_id"], $selected_articles) ? "checked" : ""; ?> />
        												<label class="form-check-label"><u><?= $page["pages_name"]; ?></u></label>
        											</div>
        											<?php endif; ?>
        											<?php endforeach; ?>
        										</div>
        									</div>
        
        									<div class="col-md-12">
        										<label>I want this Webinar Page</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Include a Webinar Page to your Campaign."><i class="fa fa-question"></i></span>
        
        										<div class="px-3 py-1 rounded" style="height: 220px; overflow: auto; border: 1px solid gainsboro;">
        											<?php foreach($pages as $page) : ?>
        											<?php if($page["pages_type"] == "webinar") :?>
        											<div class="form-check">
        												<input class="form-check-input" type="radio" name="included_webinar_page_id" value="<?= $page["pages_id"]; ?>" <?= $page["pages_id"] == intval($campaign["included_webinar_page_id"]) ? "checked" : ""; ?> />
        												<label class="form-check-label"><u><?= $page["pages_name"]; ?></u></label>
        											</div>
        											<?php endif; ?>
        											<?php endforeach; ?>
        										</div>
        									</div>
        									
        									<div class="col-md-12">
        										<label>I want this Ads</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Include a Webinar Page to your Campaign."><i class="fa fa-question"></i></span>
        
        										<div class="px-3 py-1 rounded" style="height: 220px; overflow: auto; border: 1px solid gainsboro;">
        											<?php foreach($pages as $page) : ?>
        											<?php if($page["pages_type"] == "ads") :?>
        											<div class="form-check">
        												<input class="form-check-input" type="radio" name="included_ads_id" value="<?= $page["pages_id"]; ?>" <?= $page["pages_id"] == intval($campaign["included_ads_id"]) ? "checked" : ""; ?> />
        												<label class="form-check-label"><u><?= $page["pages_name"]; ?></u></label>
        											</div>
        											<?php endif; ?>
        											<?php endforeach; ?>
        										</div>
        									</div>
        									
        									<div class="col-md-12">
        										<label>I want this Call to Action</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Include a Webinar Page to your Campaign."><i class="fa fa-question"></i></span>
        
        										<div class="px-3 py-1 rounded" style="height: 220px; overflow: auto; border: 1px solid gainsboro;">
        											<?php foreach($pages as $page) : ?>
        											<?php if($page["pages_type"] == "c2a") :?>
        											<div class="form-check">
        												<input class="form-check-input" type="radio" name="included_c2a_id" value="<?= $page["pages_id"]; ?>" <?= $page["pages_id"] == intval($campaign["included_c2a_id"]) ? "checked" : ""; ?> />
        												<label class="form-check-label"><u><?= $page["pages_name"]; ?></u></label>
        											</div>
        											<?php endif; ?>
        											<?php endforeach; ?>
        										</div>
        									</div>
        								</div>
        								<?php else : ?>
        								<hr />
        								<h5 class="text-center">Oops! You don't have any pages! <a href="index.php?cmd=pages">Create one now!</a></h5>
        								<?php endif; ?>
        							</div>
        						</div>
        					</div>
				        </div>
					</div>
					
				</div>
				<div class="card-footer">
					<div class="row">
						<div class="col-md-6">
							<button class="btn btn-outline-secondary btn-block" type="submit" name="submit" value="submit">
								<?php if(empty($passed_id)) : ?>
								<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;Save Now
								<?php else : ?>
								<i class="fa fa-save"></i>&nbsp;&nbsp;Save Changes
								<?php endif; ?>
							</button>
						</div>
						<div class="col-md-6">
							<a class="btn btn-outline-danger btn-block" href="index.php?cmd=campaignsdfy">
								<?php if(empty($passed_id)) : ?>
								<i class="fa fa-ban"></i>&nbsp;&nbsp;Cancel Creation
								<?php else : ?>
								<i class="fa fa-ban"></i>&nbsp;&nbsp;Cancel Update
								<?php endif; ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<button class="btn btn-outline-secondary" id="action-response-button" data-toggle="modal" data-target="#action-response-modal" type="button" style="display: none;"></button>

<!-- CODE_SECTION_HTML_2: MODALS -->
<!-- CREATE PAGES CONTENT MODAL -->
<div class="modal fade" id="pages-content-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Create New Content</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label>Headline</label>
					<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The headline for the content."><i class="fa fa-question"></i></span>

					<input class="form-control" id="pages-content-headline" oninput="checkPagesContentInput()" type="text" maxlength="50" />
				</div>

				<div class="form-group">
					<label>Body</label>
					<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The body for the content."><i class="fa fa-question"></i></span>

					<textarea class="form-control" id="pages-content-body" oninput="checkPagesContentInput()" rows="5"></textarea>
				</div>

				<div class="form-group">
					<label>Button Text</label>
					<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The button text of the content."><i class="fa fa-question"></i></span>

					<input class="form-control" id="pages-content-button-text" oninput="checkPagesContentInput()" type="text" maxlength="50" />
				</div>

				<div class="form-group">
					<label>Button URL</label>
					<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The button text of the content."><i class="fa fa-question"></i></span>

					<input class="form-control" id="pages-content-button-url" oninput="checkPagesContentInput()" type="url" />
				</div>

				<!-- HIDDEN INPUTS -->
				<input id="pages-content-id" type="hidden" />
			</div>
			<div class="modal-footer">
				<button class="btn btn-outline-secondary" id="pages-content-modal-button" data-dismiss="modal" disabled>Submit</button>
			</div>
		</div>
	</div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal fade" id="delete-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Delete Confirmation</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body text-center">Are you sure you want to <b>DELETE</b> this page?</div>
			<div class="modal-footer">
				<a class="btn btn-danger" id="delete-button" href="" data-dismiss="modal">Yes I am sure</a>
			</div>
		</div>
	</div>
</div>

<!-- ACTION RESPONSE MODAL -->
<div class="modal fade" id="action-response-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">System Response</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body text-center" id="action-response">
				<!-- JAVASCRIPT GENERATED -->
			</div>
			<div class="modal-footer">
				<button class="btn btn-outline-secondary" data-dismiss="modal" type="button">Got it!</button>
			</div>
		</div>
	</div>
</div>

<!-- LIMIT INCLUDED ARTICLES TO 3 -->
<!--<script type="text/javascript">-->
<!--	var article_page_limit = 3;-->
<!--	var included_articles = document.querySelectorAll(".included-articles");-->

<!--	var checked_array = [];-->
<!--	for(var counter_included_articles = 0; included_articles.length > counter_included_articles; counter_included_articles++){-->
<!--		included_articles[counter_included_articles].onclick = function(){-->
<!--			if(this.checked == true){-->
<!--				if(checked_array.length < article_page_limit){-->
<!--					checked_array.push(this.checked);-->
<!--				}-->
<!--				else{-->
<!--					this.checked = false;-->
<!--				}-->
<!--			}-->
<!--			else{-->
<!--				checked_array.pop();-->
<!--			}-->
<!--		}-->
<!--	}-->
<!--</script>-->

<script type="text/javascript">
	// FACEBOOK TOOLS PRESET
	var facebook_tools = document.getElementById("facebook_tools");
	var fb_pixel_code_snippet = document.getElementById("campaigns_facebook_tools_pixel_code_snippet");
	var fb_comments_sdk = document.getElementById("campaigns_facebook_tools_comments_sdk");
	var fb_comments_code_snippet = document.getElementById("campaigns_facebook_tools_comments_code_snippet");
	var fb_chat_sdk_and_code_snippet = document.getElementById("campaigns_facebook_tools_chat_sdk_and_code_snippet");

	fb_pixel_code_snippet.value = facebook_tools.options[facebook_tools.selectedIndex].getAttribute("data-fb-pixel-code-snippet");
	fb_comments_sdk.value = facebook_tools.options[facebook_tools.selectedIndex].getAttribute("data-fb-comments-sdk");
	fb_comments_code_snippet.value = facebook_tools.options[facebook_tools.selectedIndex].getAttribute("data-fb-comments-code-snippet");
	fb_chat_sdk_and_code_snippet.value = facebook_tools.options[facebook_tools.selectedIndex].getAttribute("data-fb-chat-sdk-and-code-snippet");

	facebook_tools.oninput = function(){
		fb_pixel_code_snippet.value = facebook_tools.options[facebook_tools.selectedIndex].getAttribute("data-fb-pixel-code-snippet");
		fb_comments_sdk.value = facebook_tools.options[facebook_tools.selectedIndex].getAttribute("data-fb-comments-sdk");
		fb_comments_code_snippet.value = facebook_tools.options[facebook_tools.selectedIndex].getAttribute("data-fb-comments-code-snippet");
		fb_chat_sdk_and_code_snippet.value = facebook_tools.options[facebook_tools.selectedIndex].getAttribute("data-fb-chat-sdk-and-code-snippet");
	}

	// RESET THE FACEBOOK TOOL PRESET WHEN SDK AND CODE SNIPPETS ARE MANUALLY INPUTTED
	fb_pixel_code_snippet.oninput = function(){
		facebook_tools.selectedIndex = 0;
	}

	fb_comments_sdk.oninput = function(){
		facebook_tools.selectedIndex = 0;
	}

	fb_comments_code_snippet.oninput = function(){
		facebook_tools.selectedIndex = 0;
	}

	fb_chat_sdk_and_code_snippet.oninput = function(){
		facebook_tools.selectedIndex = 0;
	}
</script>

<!-- INTEGRATION SCRIPTS -->
<script type="text/javascript">
	let platform = $('select[name=campaigns_integrations_platform_name]')
	let autoresponderList = $('.autoresponderList');
	let autoresponderForm = $('.autoresponderForm');
	let selectList = $('select[name=campaigns_integrations_list_name]');
	let autoresponderData = <?php echo $autoresponder['data']; ?>

	platform.on('input', function () {
		if (platform.val() == 'html') {
			autoresponderForm.show()
			autoresponderList.hide()
		} else {
			autoresponderForm.hide()
			autoresponderList.show()

			selectList.children('option').attr('disabled', 'disabled')
			selectList.children('option').hide()
			selectList.children(`option[data-type=${platform.val()}]`).removeAttr('disabled')
			selectList.children(`option[data-type=${platform.val()}]`).show()
		}
	})

	platform.trigger('input')
		
	function getValuePlat(obj) {
		var platform = obj[obj.selectedIndex].value;
		document.getElementById('platformField').setAttribute('value', platform); //pass to hidden field
		//var campaign_integrations_platform_name = $("#valueField").val();
		$.ajax({
			type: "POST",
			url: "../inc/user/ajaxplatform.php",
			data: { platform },
			success: function(data) {
				$('#emailDropdown').html(data);
			}
		});
	}
</script>

<!-- DISPLAY data- ATTRIBUTES IN THE MODAL -->
<script type="text/javascript">
	function getAttributes(attributes){
		// DELETE CONFIRMATION
		var id_to_delete = attributes.getAttribute("data-pages-id");
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=campaigns&campaigns_id=${id_to_delete}`;
	}
</script>

<!-- SELECT -->
<script type="text/javascript">
	window.onload = function(){
		var campaigns_logo = document.getElementById("campaigns-logo");
		var campaigns_logo_preview = document.getElementById("campaigns-logo-preview");
		var campaigns_logo_preview_container = document.getElementById("campaigns-logo-preview-container");

		function readFile(input){

			if(input.files && input.files[0]){
				var file_reader = new FileReader();

				file_reader.onload = function(e){
					campaigns_logo_preview.src = e.target.result;
				}

				file_reader.readAsDataURL(input.files[0]);
			}
		}

		campaigns_logo.oninput = function(){
			if(campaigns_logo.value == ""){
				campaigns_logo_preview_container.style.display = "none";
			}
			else{
				campaigns_logo_preview_container.style.display = "block";
			}

			readFile(this);
		}

		var campaigns_background_image = document.getElementById("campaigns-background-image");
		var campaigns_background_image_preview = document.getElementById("campaigns-background-image-preview");
		var campaigns_background_image_preview_container = document.getElementById("campaigns-background-image-preview-container");

		function readFile2(input){

			if(input.files && input.files[0]){
				var file_reader = new FileReader();

				file_reader.onload = function(e){
					campaigns_background_image_preview.src = e.target.result;
				}

				file_reader.readAsDataURL(input.files[0]);
			}
		}

		campaigns_background_image.oninput = function(){
			if(campaigns_background_image.value == ""){
				campaigns_background_image_preview_container.style.display = "none";
			}
			else{
				campaigns_background_image_preview_container.style.display = "block";
			}

			readFile2(this);
		}
		
		// FOR POPUP
		var campaigns_modal_background_image = document.getElementById("campaigns-modal-image");
		var campaigns_modal_background_image_preview = document.getElementById("campaigns-modal-preview-container");
		var campaigns_modal_background_image_preview_container = document.getElementById("campaigns-modal-preview-image");
        
		function readFile3(input){

			if(input.files && input.files[0]){
				var file_reader = new FileReader();

				file_reader.onload = function(e){
					campaigns_modal_background_image_preview.src = e.target.result;
				}

				file_reader.readAsDataURL(input.files[0]);
			}
		}

		campaigns_modal_background_image.oninput = function(){
			if(campaigns_modal_background_image.value == ""){
				campaigns_modal_background_image_preview_container.style.display = "none";
			}
			else{
				campaigns_modal_background_image_preview_container.style.display = "block";
			}

			readFile3(this);
		}
	}
</script>