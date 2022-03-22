<?php
	// PRIVILEGE
	if(!preg_match(";campaigns;", $cur_pack["pack_ar"])){
		// redirect("index.php?cmd=deny");
	}

	// GET CURRENT FILE NAME
	$current_file = pathinfo(__FILE__);

	if(strpos($current_file["filename"], "edit")){
		$filename = str_replace("edit", "", $current_file["filename"]);
	}
	else{
		$filename = $current_file["filename"];
	}

	// var_dump($filename);

	// VARIABLE INITIALIZATION
	$passed_id = $_GET["id"];
	$campaigns_type = "dfy2";

	// QUERIES
	$campaign = $DB->info("campaigns", "campaigns_id = '{$passed_id}' AND campaigns_type = '{$campaigns_type}'");
	$pages = $DB->query("SELECT * FROM {$dbprefix}pages WHERE user_id = '{$UserID}'");

	// IF FORM SUBMITS
	if($_POST["submit"]){
		// INITIALIZE POST VARIABLES
		$campaigns_title = $_POST["campaigns_title"];
		$campaigns_type = $campaigns_type;
		$campaigns_theme_color = $_POST["campaigns_theme_color"];
		$campaigns_theme_font = $_POST["campaigns_theme_font"];
		$campaigns_logo = empty($_FILES["campaigns_logo"]["name"]) ? $campaign["campaigns_logo"] : $_FILES["campaigns_logo"]["name"];
		$campaigns_headline = $_POST["campaigns_headline"];
		$campaigns_headline_alignment = $_POST["campaigns_headline_alignment"];
		$campaigns_body = $_POST["campaigns_body"];
		$campaigns_body_alignment = $_POST["campaigns_body_alignment"];
		$campaigns_background_image = empty($_FILES["campaigns_background_image"]["name"]) ? $campaign["campaigns_background_image"] : $_FILES["campaigns_background_image"]["name"];
		$campaigns_button_text = $_POST["campaigns_button_text"];
		$campaigns_button_url = $_POST["campaigns_button_url"];
		$included_article_pages_ids = implode(", ", $_POST["included_article_pages_ids"]);
		$included_webinar_page_id = $_POST["included_webinar_page_id"];
		
		// IF $passed_id HAS A VALUE
		if(empty($passed_id)){
			$campaigns_id = $DB->getauto("campaigns");
			$insert_campaign = $DB->query("INSERT INTO {$dbprefix}campaigns SET 
				campaigns_id = '{$campaigns_id}', 
				user_id = '{$UserID}', 
				campaigns_title = '{$campaigns_title}', 
				campaigns_type = '{$campaigns_type}', 
				campaigns_theme_color = '{$campaigns_theme_color}', 
				campaigns_theme_font = '{$campaigns_theme_font}', 
				campaigns_logo = '{$campaigns_logo}', 
				campaigns_headline = '{$campaigns_headline}', 
				campaigns_headline_alignment = '{$campaigns_headline_alignment}', 
				campaigns_body = '{$campaigns_body}', 
				campaigns_body_alignment = '{$campaigns_body_alignment}', 
				campaigns_button_text = '{$campaigns_button_text}', 
				campaigns_button_url = '{$campaigns_button_url}', 
				campaigns_background_image = '{$campaigns_background_image}', 
				included_article_pages_ids = '{$included_article_pages_ids}', 
				included_webinar_page_id = '{$included_webinar_page_id}'");

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

			if($insert_campaign){
				$_SESSION["msg_success"] = "Campaign creation successful.";

				redirect("index.php?cmd=campaignsdfy2");
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
				campaigns_theme_font = '{$campaigns_theme_font}', 
				campaigns_logo = '{$campaigns_logo}', 
				campaigns_headline = '{$campaigns_headline}', 
				campaigns_headline_alignment = '{$campaigns_headline_alignment}', 
				campaigns_body = '{$campaigns_body}', 
				campaigns_body_alignment = '{$campaigns_body_alignment}', 
				campaigns_button_text = '{$campaigns_button_text}', 
				campaigns_button_url = '{$campaigns_button_url}', 
				campaigns_background_image = '{$campaigns_background_image}', 
				included_article_pages_ids = '{$included_article_pages_ids}', 
				included_webinar_page_id = '{$included_webinar_page_id}' WHERE campaigns_id = '{$passed_id}' AND user_id = '{$UserID}'");

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
					$update_pages_logo_2 = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_background_image = '{$campaigns_background_image}' WHERE campaigns_id = '{$campaigns_id}' AND user_id = '{$UserID}'");
				}
			}

			if($update_campaign){
				$_SESSION["msg_success"] = "Campaign update successful.";

				redirect("index.php?cmd=campaignsdfy2");
			}
			else{
				$_SESSION["msg_error"] = "Campaign update failure.";
			}
		}
	}

	// DELETE PAGES LOGO OR PAGES IMAGE
	if(!empty($_GET["delete"])){
		
		if($_GET["delete"] == "pages_image"){
			$deletion_update_pages_image = $DB->query("UPDATE {$dbprefix}pages SET pages_image = '' WHERE pages_id = '{$_GET["pages_id"]}' AND user_id = '{$UserID}'");
		}

		if($deletion_update_pages_image){
			if(unlink("../upload/{$UserID}/{$_GET["pages_image"]}")){
				redirect("index.php?cmd=campaignsdfy2edit&id={$_GET["pages_id"]}");
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
						<div class="p-2 <?= empty($passed_id) ? "mr-auto" : ""; ?>">
							<a class="btn btn-outline-secondary" href="index.php?cmd=campaignsdfy2"><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;Back to Campaigns Table</a>
						</div>
						<?php if(!empty($passed_id)) : ?>
						<div class="p-2 mr-auto">
							<button class="btn btn-outline-secondary" data-toggle="modal" data-target="#delete-modal" 
							data-pages-id="<?= $campaign["campaigns_id"]; ?>" 
							onclick="getAttributes(this)" type="button">Delete this campaign</button>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="card-body">
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

					<!-- THEME SETTINGS -->
					<div class="form-group">
						<div class="card">
							<div class="card-body">
								<h4 class="text-center mb-3">Theme Settings</h4>

								<div class="row mb-2">
									<div class="col-md-6">
										<label>Campaign Theme Color</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The theme color for your campaign."><i class="fa fa-question"></i></span>

										<select class="form-control" name="campaigns_theme_color">
											<option selected disabled>Select an option</option>
											<option value="red" <?= ($campaign["campaigns_theme_color"]) ? "selected" : ""; ?>>Red</option>
											<option value="blue" <?= ($campaign["campaigns_theme_color"]) ? "selected" : ""; ?>>Blue</option>
											<option value="green" <?= ($campaign["campaigns_theme_color"]) ? "selected" : ""; ?>>Green</option>
											<option value="yellow" <?= ($campaign["campaigns_theme_color"]) ? "selected" : ""; ?>>Yellow</option>
										</select>
									</div>

									<div class="col-md-6">
										<label>Campaign Theme Font</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The theme font for your campaign."><i class="fa fa-question"></i></span>

										<select class="form-control" name="campaigns_theme_font">
											<option selected disabled>Select an option</option>
											<option value="Verdana" <?= ($campaign["campaigns_theme_font"]) ? "selected" : ""; ?>>Verdana</option>
											<option value="Tahoma" <?= ($campaign["campaigns_theme_font"]) ? "selected" : ""; ?>>Tahoma</option>
											<option value="Times New Roman" <?= ($campaign["campaigns_theme_font"]) ? "selected" : ""; ?>>Times New Roman</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- PAGES SETTINGS -->
					<div class="form-group">
						<div class="card">
							<div class="card-body">
								<h4 class="text-center mb-3">Pages Settings</h4>

								<div class="row mb-2">
									<div class="col-md-4">
										<label>Page Logo</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The logo of your page."><i class="fa fa-question"></i></span>

										<input class="form-control-file" id="campaigns-logo" type="file" name="campaigns_logo" value="" />
									</div>

									<div class="col-md-8">
										<label>Page Headline</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The headline of your home page. You can adjust the text alignment using clicking L, C, and R buttons in the right corner."><i class="fa fa-question"></i></span>
										<span class="pull-right">
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="campaigns_headline_alignment" value="left" <?= ($campaign["campaigns_headline_alignment"] == "left") ? "checked" : ""; ?> />
												<label class="form-check-label"><i class="fa fa-align-left"></i></label>
											</div>
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="campaigns_headline_alignment" value="center" <?= ($campaign["campaigns_headline_alignment"] == "center") ? "checked" : ""; ?> />
												<label class="form-check-label"><i class="fa fa-align-center"></i></label>
											</div>
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="campaigns_headline_alignment" value="right" <?= ($campaign["campaigns_headline_alignment"] == "right") ? "checked" : ""; ?> />
												<label class="form-check-label"><i class="fa fa-align-right"></i></label>
											</div>
										</span>

										<input class="form-control" type="text" name="campaigns_headline" value="<?= $campaign["campaigns_headline"]; ?>" />
									</div>

									<!-- PAGE LOGO PREVIEW -->
									<div class="col-md-12" id="campaigns-logo-preview-container" style="<?= !empty($campaign["campaigns_logo"]) ? "display: block;" : "display: none;"; ?>">
										<label class="mt-3">Preview</label>

										<img class="img-fluid rounded bg-secondary" id="campaigns-logo-preview" src="<?= "../upload/{$UserID}/" . $campaign["campaigns_logo"]; ?>" />

										<div class="img-overlay-custom text-center">
											<a class="btn btn-danger mt-2" href="<?= "index.php?cmd=campaignsdfy2edit&delete=campaigns_logo&campaigns_id={$campaign["campaigns_id"]}&campaigns_logo={$campaign["campaigns_logo"]}"; ?>">Delete</a>
										</div>
									</div>
								</div>

								<div class="row mb-2 align-items-center">
									<div class="col-md-4">
										<label class="mt-3">Background Image</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The background image for your home page."><i class="fa fa-question"></i></span>

										<input class="form-control-file" id="campaigns-background-image" type="file" name="campaigns_background_image" value="" />
									</div>
									
									<div class="col-md-8">
										<label>Page Body</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The body text of your home page. You can adjust the text alignment using clicking L, C, and R buttons in the right corner."><i class="fa fa-question"></i></span>
										<span class="pull-right">
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="campaigns_body_alignment" value="left" <?= ($campaign["campaigns_headline_alignment"] == "left") ? "checked" : ""; ?> />
												<label class="form-check-label"><i class="fa fa-align-left"></i></label>
											</div>
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="campaigns_body_alignment" value="center" <?= ($campaign["campaigns_headline_alignment"] == "center") ? "checked" : ""; ?> />
												<label class="form-check-label"><i class="fa fa-align-center"></i></label>
											</div>
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="campaigns_body_alignment" value="right" <?= ($campaign["campaigns_headline_alignment"] == "right") ? "checked" : ""; ?> />
												<label class="form-check-label"><i class="fa fa-align-right"></i></label>
											</div>
										</span>

										<textarea class="form-control" name="campaigns_body" rows="7"><?= $campaign["campaigns_body"]; ?></textarea>
									</div>

									<!-- CAMPAIGN BACKGROUND IMAGE PREVIEW -->
									<div class="col-md-12" id="campaigns-background-image-preview-container" style="<?= !empty($campaign["campaigns_background_image"]) ? "display: block;" : "display: none;"; ?>">
										<label class="mt-3">Preview</label>

										<img class="img-fluid rounded bg-secondary" id="campaigns-background-image-preview" src="<?= "../upload/{$UserID}/" . $campaign["campaigns_background_image"]; ?>" />

										<div class="img-overlay-custom text-center">
											<a class="btn btn-danger mt-2" href="<?= "index.php?cmd=campaignsdfy2edit&delete=campaigns_background_image&campaigns_id={$campaign["campaigns_id"]}&campaigns_background_image={$campaign["campaigns_background_image"]}"; ?>">Delete</a>
										</div>
									</div>
								</div>

								<div class="row mb-2">
									<div class="col-md-6">
										<label>Page Button Text</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The button text of your home page."><i class="fa fa-question"></i></span>

										<input class="form-control" type="text" name="campaigns_button_text" value="<?= $campaign["campaigns_button_text"]; ?>" />
									</div>

									<div class="col-md-6">
										<label>Page Button URL</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The button URL of your home page."><i class="fa fa-question"></i></span>

										<input class="form-control" type="url" name="campaigns_button_url" value="<?= $campaign["campaigns_button_url"]; ?>" />
									</div>
								</div>

								<?php if($pages) : ?>
								<div class="row mb-2">
									<div class="col-md-6">
										<label>I want these Article Pages</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Include 3 Article Pages to your Campaign."><i class="fa fa-question"></i></span>

										<div class="px-3 py-1 rounded" style="height: 100px; overflow: auto; border: 1px solid gainsboro;">
											<?php $selected_articles = explode(", ", $campaign["included_article_pages_ids"]); ?>
											<?php $article_count = 0; ?>
											<?php foreach($pages as $page) : ?>
											<?php if($page["pages_type"] == "article") : ?>
											<div class="form-check">
												<input class="form-check-input included-articles" type="checkbox" name="included_article_pages_ids[]" value="<?= $page["pages_id"]; ?>" <?= ($selected_articles[0] == $page["pages_id"] || $selected_articles[1] == $page["pages_id"] || $selected_articles[2] == $page["pages_id"]) ? "checked" : ""; ?> />
												<label class="form-check-label"><u><?= $page["pages_title"]; ?></u></label>
											</div>
											<?php endif; ?>
											<?php endforeach; ?>
										</div>
									</div>

									<div class="col-md-6">
										<label>I want this Webinar Page</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Include a Webinar Page to your Campaign."><i class="fa fa-question"></i></span>

										<div class="px-3 py-1 rounded" style="height: 100px; overflow: auto; border: 1px solid gainsboro;">
											<?php foreach($pages as $page) : ?>
											<?php if($page["pages_type"] == "webinar") : ?>
											<div class="form-check">
												<input class="form-check-input" type="radio" name="included_webinar_page_id" value="<?= $page["pages_id"]; ?>" <?= ($campaign["included_webinar_page_id"] == $page["pages_id"]) ? "checked" : ""; ?> />
												<label class="form-check-label"><u><?= $page["pages_title"]; ?></u></label>
											</div>
											<?php endif; ?>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
								<?php else : ?>
								<h5 class="text-center mt-3">You have no Article or Webinar pages.</h5>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					<div class="row">
						<div class="col-md-6">
							<button class="btn btn-outline-secondary btn-block" type="submit" name="submit" value="submit">
								<?php if(empty($passed_id)) : ?>
								<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;Create Now
								<?php else : ?>
								<i class="fa fa-save"></i>&nbsp;&nbsp;Save Changes
								<?php endif; ?>
							</button>
						</div>
						<div class="col-md-6">
							<a class="btn btn-outline-secondary btn-block" href="index.php?cmd=campaignsdfy2">
								<?php if(empty($passed_id)) : ?>
								<i class="fa fa-ban"></i>&nbsp;&nbsp;Cancel Campaign Creation
								<?php else : ?>
								<i class="fa fa-ban"></i>&nbsp;&nbsp;Cancel Campaign Update
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
<script type="text/javascript">
	var article_page_limit = 3;
	var included_articles = document.querySelectorAll(".included-articles");

	var checked_array = [];
	for(var counter_included_articles = 0; included_articles.length > counter_included_articles; counter_included_articles++){
		included_articles[counter_included_articles].onclick = function(){
			if(this.checked == true){
				if(checked_array.length < article_page_limit){
					checked_array.push(this.checked);
				}
				else{
					this.checked = false;
				}
			}
			else{
				checked_array.pop();
			}
		}
	}
</script>

<!-- DISPLAY data- ATTRIBUTES IN THE MODAL -->
<script type="text/javascript">
	function getAttributes(attributes){
		// DELETE CONFIRMATION
		var id_to_delete = attributes.getAttribute("data-pages-id");
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=campaignsdfy2&campaigns_id=${id_to_delete}`;
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
	}
</script>