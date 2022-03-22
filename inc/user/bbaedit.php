<?php
	// PRIVILEGE
	if(!preg_match(";pageb;", $cur_pack["pack_ar"])){
		redirect("index.php?cmd=deny");
	}

	// VARIABLE INITIALIZATION
	$passed_id = $_GET["id"];
	$passed_pages_type = $_GET["pages_type"];
	$page = $DB->info("pages", "pages_id = '{$passed_id}' AND user_id = '{$UserID}'");
	$affiliate_links = $DB->query("SELECT * FROM {$dbprefix}affiliate_links WHERE user_id = '{$UserID}'");

	// CODE_SECTION_PHP_3: BACKEND PROCESS
	if($_POST["submit"]){
		// POST VARIABLES
		$pages_title = $_POST["pages_title"];
		$pages_type = $_POST["pages_type"];
		$pages_affiliate_link = $_POST["pages_affiliate_link"];
		$pages_image = empty($_FILES["pages_image"]["name"]) ? $page["pages_image"] : $_FILES["pages_image"]["name"];
		$pages_video_url = $_POST["pages_video_url"];
		$pages_headline_long = $_POST["pages_headline_long"];
		$pages_headline_short = $_POST["pages_headline_short"];
		$pages_content_introduction = $_POST["pages_content_introduction"];
		$pages_content_headline = $_POST["pages_content_headline"];
		$pages_content_body = $_POST["pages_content_body"];
		$pages_content_button_text = $_POST["pages_content_button_text"];
		$pages_content_button_url = $_POST["pages_content_button_url"];
		$pages_content_additional_ids = implode(", ", $_POST["pages_content_additional_ids"]);
		$pages_description_content = !empty($_POST["pages_description_content"]) ? implode(", ", $_POST["pages_description_content"]) : $page["pages_description_content"];
		$pages_created_at = $_POST["pages_created_at"];
		$pages_updated_at = $_POST["pages_created_at"];
		
		// IF $passed_id HAS A VALUE
		if(empty($passed_id)){
			$pages_id = $DB->getauto("pages");
			$insert_page = $DB->query("INSERT INTO {$dbprefix}pages SET pages_id = '{$pages_id}', user_id = '{$UserID}', 
				pages_title = '{$pages_title}', 
				pages_type = '{$pages_type}', 
				pages_affiliate_link = '{$pages_affiliate_link}', 
				pages_image = '{$pages_image}', 
				pages_video_url = '{$pages_video_url}', 
				pages_headline_long = '{$pages_headline_long}', 
				pages_headline_short = '{$pages_headline_short}', 
				pages_content_introduction = '{$pages_content_introduction}', 
				pages_content_headline = '{$pages_content_headline}', 
				pages_content_body = '{$pages_content_body}', 
				pages_content_button_text = '{$pages_content_button_text}', 
				pages_content_button_url = '{$pages_content_button_url}', 
				pages_content_additional_ids = '{$pages_content_additional_ids}'");

			// UPLOAD PROCESS: PAGES LOGO AND PAGES IMAGE
			$upload_directory = "../upload/{$UserID}/";

			$target_file_1 = $upload_directory . basename($_FILES["pages_image"]["name"]);
			$upload_status_1 = 1;
			$get_file_extension_1 = strtolower(pathinfo($target_file_1, PATHINFO_EXTENSION));
			
			if(!empty($_FILES["pages_image"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_1 != "jpg" && $get_file_extension_1 != "jpeg" && $get_file_extension_1 != "png"){
					$upload_status_1 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["pages_image"]["size"] > 1000000){
					$upload_status_1 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_1 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["pages_image"]["tmp_name"], $target_file_1);
					$update_pages_logo_2 = $DB->query("UPDATE {$dbprefix}exp SET pages_image = '{$pages_image}' WHERE pages_id = '{$passed_id}' AND user_id = '{$UserID}'");
				}
			}

			if($insert_page){
				$_SESSION["msg_success"] = "Page creation successful.";

				redirect("index.php?cmd=pages");
			}
			else{
				$_SESSION["msg_error"] = "Page creation failure.";
			}
		}
		else{
			$update_page = $DB->query("UPDATE {$dbprefix}pages SET pages_title = '{$pages_title}', 
				pages_type = '{$pages_type}', 
				pages_affiliate_link = '{$pages_affiliate_link}', 
				pages_image = '{$pages_image}', 
				pages_video_url = '{$pages_video_url}', 
				pages_headline_long = '{$pages_headline_long}', 
				pages_headline_short = '{$pages_headline_short}', 
				pages_content_introduction = '{$pages_content_introduction}', 
				pages_content_headline = '{$pages_content_headline}', 
				pages_content_body = '{$pages_content_body}', 
				pages_content_button_text = '{$pages_content_button_text}', 
				pages_content_button_url = '{$pages_content_button_url}', 
				pages_content_additional_ids = '{$pages_content_additional_ids}' WHERE pages_id = '{$passed_id}' AND user_id = '{$UserID}'");

			// UPLOAD PROCESS: PAGES LOGO
			$upload_directory = "../upload/{$UserID}/";

			$target_file_1 = $upload_directory . basename($_FILES["pages_image"]["name"]);
			$upload_status_1 = 1;
			$get_file_extension_1 = strtolower(pathinfo($target_file_1, PATHINFO_EXTENSION));

			if(!empty($_FILES["pages_image"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_1 != "jpg" && $get_file_extension_1 != "jpeg" && $get_file_extension_1 != "png"){
					$upload_status_1 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["pages_image"]["size"] > 1000000){
					$upload_status_1 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_1 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["pages_image"]["tmp_name"], $target_file_1);
					$update_pages_logo_2 = $DB->query("UPDATE {$dbprefix}exp SET pages_image = '{$pages_image}' WHERE pages_id = '{$passed_id}' AND user_id = '{$UserID}'");
				}
			}

			if($update_page){
				$_SESSION["msg_success"] = "Page update successful.";

				redirect("index.php?cmd=pages");
			}
			else{
				$_SESSION["msg_error"] = "Page update failure.";
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
				redirect("index.php?cmd=pagesedit&id={$_GET["pages_id"]}");
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
							<a class="btn btn-outline-secondary" href="index.php?cmd=pages"><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;Back to Campaigns Table</a>
						</div>
						<?php if(!empty($passed_id)) : ?>
						<div class="p-2 mr-auto">
							<button class="btn btn-outline-secondary" data-toggle="modal" data-target="#delete-modal" 
							data-pages-id="<?= $page["pages_id"]; ?>" 
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

								<input class="form-control" type="text" name="campaigns_title" value="" maxlength="50" />
							</div>
						</div>
					</div>

					<!-- PAGES SETTINGS -->
					<div class="form-group">
						<div class="card">
							<div class="card-body">
								<h4 class="text-center mb-5">Pages Settings</h4>

								<div class="row mb-2">
									<div class="col-md-6">
										<label>Page Logo</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The logo of your page."><i class="fa fa-question"></i></span>

										<input class="form-control-file" id="pages-logo" type="file" name="pages_logo" value="" />

										<div class="col-md-12" id="pages-logo-preview-container" style="<?= !empty($page["pages_logo"]) ? "display: block;" : "display: none;"; ?>">
											<label class="mt-3">Preview</label>

											<img class="img-fluid rounded bg-secondary" id="pages-logo-preview" src="<?= "../upload/{$UserID}/" . $page["pages_logo"]; ?>" />

											<div class="img-overlay-custom text-center">
												<a class="btn btn-danger mt-2" href="<?= "index.php?cmd=pagesedit&delete=pages_logo&pages_id={$page["pages_id"]}&pages_logo={$page["pages_logo"]}"; ?>">Delete</a>
											</div>
										</div>
									</div>

									<div class="col-md-6">
										<label>Page Headline</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The headline of your home page."><i class="fa fa-question"></i></span>

										<input class="form-control" type="text" name="campaigns_headline" value="" />
									</div>
								</div>

								<div class="row mb-2">
									<div class="col-md-12">
										<label>Page Body</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The body text of your home page."><i class="fa fa-question"></i></span>

										<textarea class="form-control" name="campaigns_body" rows="7"></textarea>
									</div>
								</div>

								<div class="row mb-2">
									<div class="col-md-6">
										<label>Page Button Text</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The button text of your home page."><i class="fa fa-question"></i></span>

										<input class="form-control" type="text" name="campaigns_button_text" value="" />
									</div>

									<div class="col-md-6">
										<label>Page Button URL</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The button URL of your home page."><i class="fa fa-question"></i></span>

										<input class="form-control" type="url" name="campaigns_button_url" value="" />
									</div>
								</div>

								<div class="row mb-2">
									<div class="col-md-6">
										<label>Include these Article Pages</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Include 3 Article Pages to your Campaign."><i class="fa fa-question"></i></span>

										<input class="form-control" type="text" name="campaigns_button_text" value="" />
									</div>

									<div class="col-md-6">
										<label>I want this Webinar Page</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Include a Webinar Page to your Campaign."><i class="fa fa-question"></i></span>

										<input class="form-control" type="url" name="campaigns_button_url" value="" />
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
								<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;Create Now
								<?php else : ?>
								<i class="fa fa-save"></i>&nbsp;&nbsp;Save Changes
								<?php endif; ?>
							</button>
						</div>
						<div class="col-md-6">
							<a class="btn btn-outline-secondary btn-block" href="index.php?cmd=pages">
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

<!-- CODE_SECTION_JAVASCRIPT_1: CREATE NEW CONTENT -->
<script type="text/javascript">
	var create_new_content_button = document.getElementById("create-new-content-button");
	var pages_content_modal_button = document.getElementById("pages-content-modal-button");

	var pass_user_id = "<?= $UserID; ?>";
	var pages_content = document.getElementById("pages-content");
	var pages_content_id = document.getElementById("pages-content-id");
	var pages_content_headline = document.getElementById("pages-content-headline");
	var pages_content_body = document.getElementById("pages-content-body");
	var pages_content_button_text = document.getElementById("pages-content-button-text");
	var pages_content_button_url = document.getElementById("pages-content-button-url");

	// RESET THE VALUE INPUT FORMS WHEN CREATE NEW IS CLICKED
	create_new_content_button.onclick = function(){
		pages_content_id.value = "";
		pages_content_headline.value = "";
		pages_content_body.value = "";
		pages_content_button_text.value = "";
		pages_content_button_url.value = "";
	}

	// CHECK INPUT VALUE TO TOGGLE SUBMIT BUTTON'S DISABLE PROPERTY
	function checkPagesContentInput(){
		var pattern = new RegExp('^(https?:\\/\\/)?'+ // PROTOCOL
		'((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // DOMAIN NAME
		'((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR IP (v4) ADDRESS
		'(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // PORT AND PATH
		'(\\?[;&a-z\\d%_.~+=-]*)?'+ // QUERY STRING
		'(\\#[-a-z\\d_]*)?$','i'); // FRAGMENT LOCATOR

		var isURL = pattern.test(pages_content_button_url.value);

		if(pages_content_headline.value == "" || pages_content_body.value == "" || pages_content_button_text.value == "" || pages_content_button_url.value == "" || !isURL){
			pages_content_modal_button.disabled = true;
		}
		else{
			pages_content_modal_button.disabled = false;
		}
	}

	pages_content_modal_button.onclick = function(){
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function(){
			if(this.readyState == 4 && this.status == 200){
				getAllPagesContent();
			}
		}
		xhttp.open("POST", "<?= "{$SCRIPTURL}inc/user/"; ?>pagesedit_ajax.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(`pass_user_id=${pass_user_id}&pages_content_id=${pages_content_id.value}&pages_content_headline=${pages_content_headline.value}&pages_content_body=${pages_content_body.value}&pages_content_button_text=${pages_content_button_text.value}&pages_content_button_url=${pages_content_button_url.value}`);
	}

	getAllPagesContent();
	function getAllPagesContent(){
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function(){
			if(this.readyState == 4 && this.status == 200){
				var serverResponse = this.responseText;
				serverResponse = serverResponse.split(",");
				serverResponse = JSON.parse(serverResponse);

				var pages_content_options = "";
				for(var x = 0; serverResponse.length > x; x++){
					pages_content_options += `<option class="pages-content-options" value="${serverResponse[x][0]}" ondblclick="editPagesContent(${x})">${serverResponse[x][2]}</option>`;
				}

				pages_content.innerHTML = pages_content_options;
			}
		}
		xhttp.open("POST", "<?= "{$SCRIPTURL}inc/user/"; ?>pagesedit_ajax_2.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(`pass_user_id=${pass_user_id}`);
	}

	function editPagesContent(passed_offset){
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function(){
			if(this.readyState == 4 && this.status == 200){
				var serverResponse = this.responseText;
				serverResponse = serverResponse.split(",");
				serverResponse = JSON.parse(serverResponse);
				
				pass_user_id = serverResponse[passed_offset][1];
				pages_content_id.value = serverResponse[passed_offset][0];
				pages_content_headline.value = serverResponse[passed_offset][2];
				pages_content_body.value = serverResponse[passed_offset][3];
				pages_content_button_text.value = serverResponse[passed_offset][4];
				pages_content_button_url.value = serverResponse[passed_offset][5];

				checkPagesContentInput();

				$("#pages-content-modal").modal("show");
			}
		}
		xhttp.open("POST", "<?= "{$SCRIPTURL}inc/user/"; ?>pagesedit_ajax_2.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(`pass_user_id=${pass_user_id}`);
	}
</script>

<!-- DISPLAY data- ATTRIBUTES IN THE MODAL -->
<script type="text/javascript">
	function getAttributes(attributes){
		// DELETE CONFIRMATION
		var id_to_delete = attributes.getAttribute("data-pages-id");
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=pages&pages_id=${id_to_delete}`;
	}
</script>

<!-- CODE_SECTION_JAVASCRIPT_2: PREVIEW_IMAGE_OR_VIDEO_URL_ON_INPUT -->
<?php if($passed_pages_type == "article" || $page["pages_type"] == "article") : ?>
<script type="text/javascript">
	window.onload = function(){
		var pages_image = document.getElementById("pages-image");
		var pages_image_preview = document.getElementById("pages-image-preview");
		var pages_image_preview_container = document.getElementById("pages-image-preview-container");

		function readFile(input){

			if(input.files && input.files[0]){
				var file_reader = new FileReader();

				file_reader.onload = function(e){
					pages_image_preview.src = e.target.result;
				}

				file_reader.readAsDataURL(input.files[0]);
			}
		}

		pages_image.oninput = function(){
			if(pages_image.value == ""){
				pages_image_preview_container.style.display = "none";
			}
			else{
				pages_image_preview_container.style.display = "block";
			}

			readFile(this);
		}
	}
</script>
<?php elseif($passed_pages_type == "webinar"  || $page["pages_type"] == "webinar") : ?>
<script type="text/javascript">
	window.onload = function(){
		var pages_video_url = document.getElementById("pages-video-url");
		var pages_video_url_preview = document.getElementById("pages-video-url-preview");
		var pages_video_url_preview_container = document.getElementById("pages-video-url-preview-container");

		// ON INPUT
		pages_video_url.oninput = function(){
			if(pages_video_url.value == ""){
				pages_video_url_preview_container.style.display = "none";
			}
			else{
				pages_video_url_preview_container.style.display = "block";
			}

			var youtube_optimized_url = "";

			if(pages_video_url.value.includes("youtu.be")){
				youtube_optimized_url = pages_video_url.value.replace("youtu.be", "youtube.com/embed");
			}

			if(pages_video_url.value.includes("watch")){
				youtube_optimized_url = pages_video_url.value.replace("watch", "embed");
			}

			if(pages_video_url.value.includes("watch?v=")){
				youtube_optimized_url = pages_video_url.value.replace("watch?v=", "embed/");
			}

			pages_video_url_preview.src = youtube_optimized_url;
		}

		// ON LOAD
		if(pages_video_url.value == ""){
			pages_video_url_preview_container.style.display = "none";
		}
		else{
			pages_video_url_preview_container.style.display = "block";
		}

		var youtube_optimized_url = "";

		if(pages_video_url.value.includes("youtu.be")){
			youtube_optimized_url = pages_video_url.value.replace("youtu.be", "youtube.com/embed");
		}

		if(pages_video_url.value.includes("watch")){
			youtube_optimized_url = pages_video_url.value.replace("watch", "embed");
		}

		if(pages_video_url.value.includes("watch?v=")){
			youtube_optimized_url = pages_video_url.value.replace("watch?v=", "embed/");
		}

		pages_video_url_preview.src = youtube_optimized_url;
	}
</script>
<?php endif; ?>