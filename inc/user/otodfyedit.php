<?php include('../../cache_solution/top-cache-v2.php'); ?>
<?php
	// ============= PRIVILEGE ============= //
	$passed_id      = $_GET["id"];
	$currentTotalCampaign = count($DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = '{$UserID}' AND campaigns_type = 'regular'"));
	if(!preg_match(";campaigns;", $cur_pack["pack_ar"]) || ($currentTotalCampaign >= 2 && !$passed_id) ){
		redirect("index.php?cmd=deny");
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
	$popups     = $DB->query("SELECT * FROM {$dbprefix}popup WHERE user_id = '{$UserID}'");
	$campaign           = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE campaigns_id = '{$passed_id}' AND campaigns_type = '{$campaigns_type}'")[0];
	$contents              = $DB->query("SELECT * FROM {$dbprefix}content WHERE user_id = '{$UserID}'");

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
				popup_id                                             = '{$popup_id}', 
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

				redirect("index.php?cmd=otodfy");
			}
			else{
				$_SESSION["msg_error"] = "Campaign creation failure.";
			}
		}
		else{
			$update_campaign = $DB->query("UPDATE {$dbprefix}campaigns SET 
			    popup_id                                            = '{$popup_id}', 
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

				redirect("index.php?cmd=otodfy");
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
    				redirect("index.php?cmd=otodfyedit&id={$_GET['id']}&campaigns_type={$campaigns_type}");
    			}
    		}
		}
		
		if($_GET["delete"] == "campaigns_responder_image"){
			$delete_this = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_responder_image = '' WHERE campaigns_id = '{$_GET['id']}'");
			
			if($delete_this){
    			if(unlink("../upload/{$UserID}/{$_GET['campaigns_responder_image']}")){
    				redirect("index.php?cmd=otodfyedit&id={$_GET['id']}&campaigns_type={$campaigns_type}");
    			}
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
    							<a class="btn btn-outline-secondary" href="index.php?cmd=otodfy"><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;Back to Campaigns Table</a>
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
        
        								<input class="form-control" type="text" name="campaigns_title" value="<?= $campaign["campaigns_title"]; ?>" maxlength="50" required/>
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
        									
        									<div class="col-md-12">
        										<label>Popup</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The popup for your campaign."><i class="fa fa-question"></i></span>
        
        										<select name="popup_id" class="form-control">
            										<option disabled selected> Choose one </option>
            										<?php foreach($popups as $key => $popup) : ?>
            											<option value="<?= $popup['popup_id']; ?>" <?= ($popup["popup_id"] == $campaign['popup_id']) ? "selected" : ""; ?>><?= $popup["name"] ?></option>
            										<?php endforeach; ?>
            									</select>
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
        					<!-- AUTO RESPONDER FORM SETTINGS -->
        					<div class="form-group">
        						<div class="card">
        							<div class="card-body">
        								<h4 class="text-center mb-3">AUTO RESPONDER FORM SETTINGS</h4>
        								<div class="row mb-2">
        									<div class="col-md-12">
        										<label>Title</label>
                								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Your call to action title here."><i class="fa fa-question"></i></span>
                								<input type="text" class="form-control" name="optin_title" value="<?= $campaign["optin_title"]; ?>"/>
        									</div>
        									<div class="col-md-12">
        										<label>Button Text</label>
                								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Your button text here."><i class="fa fa-question"></i></span>
                								<input type="text" class="form-control" name="optin_btn_title" value="<?= $campaign["optin_btn_title"]; ?>"/>
        									</div>
        									<div class="col-md-12">
        										<label>Image</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Your responder form image"><i class="fa fa-question"></i></span>
        
        										<input class="form-control-file" id="campaigns-responder-image" type="file" name="campaigns_responder_image" value="" <?= (!empty($campaign["campaigns_responder_image"])) ? "disabled" : "" ; ?> style="<?= (!empty($campaign["campaigns_responder_image"])) ? "cursor: not-allowed;" : ""; ?>" />
        									</div>
        									<!-- CAMPAIGN RESPONDER IMAGE PREVIEW -->
        									<div class="col-md-12" id="campaigns-responder-image-preview-container" style="<?= !empty($campaign["campaigns_responder_image"]) ? "display: block;" : "display: none;"; ?>">
        										<h5 class="mt-3">Preview</h5>
        
        										<img class="img-fluid rounded bg-secondary" id="campaigns-responder-image-preview" src="<?= "../upload/{$UserID}/" . $campaign["campaigns_responder_image"]; ?>" />
        
                                                <?php if( !empty($campaign["campaigns_responder_image"]) ){ ?>
                                                    <div class="img-overlay-custom text-center">
            											<a class="btn btn-danger mt-2" href="<?= "index.php?cmd=otodfyedit&delete=campaigns_responder_image&id={$campaign["campaigns_id"]}&campaigns_responder_image={$campaign["campaigns_responder_image"]}&campaigns_type={$campaigns_type}"; ?>">Delete</a>
            										</div>
                                                <?php } ?>
        										
        									</div>
        								</div>
        							</div>
        						</div>
        					</div>
        					<!-- RESOURCE: SLIDER -->
        					<div class="form-group">
        						<div class="card">
        							<div class="content card-body">
        								<center><h4 class="mb-3"><i class="fa fa-cogs"></i> &nbsp;Contents</h4></center>
        
        								<?php if($contents) : ?>
            							<div class="row mb-2">
            								<div class="col-md-12">
            									<label>I want these Contents</label>
            									<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Include items to tab 1"><i class="fa fa-question"></i></span>
            									<div class="px-3 py-1 rounded" style="height: 220px; overflow: auto; border: 1px solid gainsboro;">
        											<?php $selected_content_ids = json_decode($campaign["content_id"]); ?>
        											<?php foreach($contents as $key => $content) : ?>
        											<div class="form-check">
        												<input class="form-check-input included-articles" type="checkbox" name="content_id[]" value="<?= $content["content_id"]; ?>" <?= (in_array($content["content_id"], $selected_content_ids)) ? "checked" : ""; ?> />
        												<label class="form-check-label"><u><?= $content["content_title"]; ?></u></label>
        											</div>
        											<?php endforeach; ?>
        										</div>
            								</div>
            							</div>
            							<?php else : ?>
            							<hr />
            							<h5 class="text-center">Oops! You don't have any content! <a href="index.php?cmd=content">Create one now!</a></h5>
            							<?php endif; ?>
        							</div>
        						</div>
        					</div>
        					<!-- SOCIAL: LINK -->
        					<div class="form-group">
        						<div class="card">
        							<div class="content card-body">
        							    <center><h4 class="mb-3"><i class="fa fa-social"></i> &nbsp;Social Media</h4></center>
        							    <div class="row mb-2">
            								<div class="col-md-12">
                								<label>Facebook</label>
                								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Your facebook page."><i class="fa fa-question"></i></span>
                								<input type="text" class="form-control social-media" name="campaigns_facebook" value="<?= $campaign["campaigns_facebook"]; ?>"/>
                							</div>
                							<div class="col-md-12">
                								<label>Twitter</label>
                								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Your twitter page."><i class="fa fa-question"></i></span>
                								<input type="text" class="form-control social-media" name="campaigns_twitter" value="<?= $campaign["campaigns_twitter"]; ?>"/>
                							</div>
                							<div class="col-md-12">
                								<label>Instagram</label>
                								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Your instagram page."><i class="fa fa-question"></i></span>
                								<input type="text" class="form-control social-media" name="campaigns_instagram" value="<?= $campaign["campaigns_instagram"]; ?>"/>
                							</div>
                							<div class="col-md-12">
                								<label>Youtube</label>
                								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Your youtube page."><i class="fa fa-question"></i></span>
                								<input type="text" class="form-control social-media" name="campaigns_youtube" value="<?= $campaign["campaigns_youtube"]; ?>"/>
                							</div>
                						</div>
        							</div>
        						</div>
        					</div>
				        </div>
				        <div class="col-md-6">
				            <!-- PAGES SETTINGS -->
        					<div class="form-group">
        						<div class="card">
        							<div class="card-body">
        								<h4 class="text-center mb-3">Page Settings</h4>
        
        								<div class="row mb-2">
        									<div class="col-md-12">
        										<label>Page Header Image</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The Header of your page."><i class="fa fa-question"></i></span>
        
        										<input class="form-control-file" id="campaigns-header-image" type="file" name="campaigns_header_image" value="" <?= (!empty($campaign["campaigns_header_image"])) ? "disabled" : "" ; ?> style="<?= (!empty($campaign["campaigns_header_image"])) ? "cursor: not-allowed;" : ""; ?>" />
        									</div>
        									<!-- PAGE HEADER PREVIEW -->
        									<div class="col-md-12" id="campaigns-header-image-preview-container" style="<?= !empty($campaign["campaigns_header_image"]) ? "display: block;" : "display: none;"; ?>">
        										<h5 class="mt-3">Preview</h5>
        
        										<img class="img-fluid rounded bg-secondary" id="campaigns-header-image-preview" src="<?= "../upload/{$UserID}/" . $campaign["campaigns_header_image"]; ?>" />
        
                                                <?php if( !empty($campaign["campaigns_header_image"]) ){ ?>
                                                    <div class="img-overlay-custom text-center">
            											<a class="btn btn-danger mt-2" href="<?= "index.php?cmd=otodfyedit&delete=campaigns_header_image&id={$campaign["campaigns_id"]}&campaigns_header_image={$campaign["campaigns_header_image"]}&campaigns_type={$campaigns_type}"; ?>">Delete</a>
            										</div>
                                                <?php } ?>
        										
        									</div>
        									<div class="col-md-12">
        										<label>Page Header Body</label>
        										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The header text of your home page. You can adjust the text alignment using clicking L, C, and R buttons in the right corner."><i class="fa fa-question"></i></span>
        										<textarea class="form-control" name="campaigns_body" rows="7" required><?= $campaign["campaigns_body"]; ?></textarea>
        									</div>
        								</div>
        							</div>
        						</div>
        					</div>
        					
        					<!-- CALL TO ACTION SETTINGS -->
        					<div class="form-group">
        						<div class="card">
        							<div class="card-body">
        								<h4 class="text-center mb-3">Call to Action</h4>
        								<div class="row mb-2">
        									<div class="col-md-12">
        										<label>Title</label>
                								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Your call to action title here."><i class="fa fa-question"></i></span>
                								<input type="text" class="form-control" name="c2a_title" value="<?= $campaign["c2a_title"]; ?>"/>
        									</div>
        									<div class="col-md-6">
        										<label>Button Text</label>
                								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Your button text here."><i class="fa fa-question"></i></span>
                								<input type="text" class="form-control" name="c2a_btn_text" value="<?= $campaign["c2a_btn_text"]; ?>"/>
        									</div>
        									<div class="col-md-6">
        										<label>Button Link</label>
                								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Your button link."><i class="fa fa-question"></i></span>
                								<input type="text" class="form-control" name="c2a_btn_link" value="<?= $campaign["c2a_btn_link"]; ?>"/>
        									</div>
        								</div>
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
							<a class="btn btn-outline-danger btn-block" href="index.php?cmd=otodfy">
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
		delete_button.href = `index.php?cmd=otodfy&campaigns_id=${id_to_delete}&delete=1`;
	}
</script>

<!-- SELECT -->
<script type="text/javascript">
	window.onload = function(){
		var campaigns_header_image = document.getElementById("campaigns-header-image");
		var campaigns_header_image_preview = document.getElementById("campaigns-header-image-preview");
		var campaigns_header_image_preview_container = document.getElementById("campaigns-header-image-preview-container");

		function readFile(input){

			if(input.files && input.files[0]){
				var file_reader = new FileReader();

				file_reader.onload = function(e){
					campaigns_header_image_preview.src = e.target.result;
				}

				file_reader.readAsDataURL(input.files[0]);
			}
		}

		campaigns_header_image.oninput = function(){
			if(campaigns_header_image.value == ""){
				campaigns_header_image_preview_container.style.display = "none";
			}
			else{
				campaigns_header_image_preview_container.style.display = "block";
			}

			readFile(this);
		}
		
		var campaigns_responder_image = document.getElementById("campaigns-responder-image");
		var campaigns_responder_image_preview = document.getElementById("campaigns-responder-image-preview");
		var campaigns_responder_image_preview_container = document.getElementById("campaigns-responder-image-preview-container");

		function readFile(input){

			if(input.files && input.files[0]){
				var file_reader = new FileReader();

				file_reader.onload = function(e){
					campaigns_responder_image_preview.src = e.target.result;
				}

				file_reader.readAsDataURL(input.files[0]);
			}
		}

		campaigns_responder_image.oninput = function(){
			if(campaigns_responder_image.value == ""){
				campaigns_responder_image_preview_container.style.display = "none";
			}
			else{
				campaigns_responder_image_preview_container.style.display = "block";
			}

			readFile(this);
		}
	}
</script>
<?php include('../../cache_solution/bottom-cache-v2.php'); ?>