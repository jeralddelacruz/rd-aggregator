<?php
	// CODE_SECTION_PHP_1: PRIVILEGE
	if(!preg_match(";pages;", $cur_pack["pack_ar"])){
		// redirect("index.php?cmd=deny");
		// NOTE: COMMENTED-OUT BECAUSE pages IS STILL NOT ADDED AS A PACKAGE.
	}

	// CODE_SECTION_PHP_2: VARIABLE_INITIALIZATION
	$passed_id = $_GET["id"];
	$passed_pages_type = $_GET["pages_type"];
	$page = $DB->info("pages", "pages_id = '{$passed_id}' AND user_id = '{$UserID}'");
	$affiliate_links_collection = $DB->query("SELECT * FROM {$dbprefix}affiliate_links_collection WHERE user_id = '{$UserID}'");

	// CODE_SECTION_PHP_3: BACKEND PROCESS
	if($_POST["submit"]){
		// POST VARIABLES
		$remove[] = "'";
		$remove[] = '"';
		$remove[] = "-";

		// STRIP 1
		$cs_stripped_1 = str_replace($remove, "", $_POST["pages_name"]);
		$pages_name = $cs_stripped_1;

		$pages_type = $_POST["pages_type"];
		$pages_image = empty($_FILES["pages_image"]["name"]) ? $page["pages_image"] : $_FILES["pages_image"]["name"];
		$pages_video_url = $_POST["pages_video_url"];
		$pages_headline = strip($_POST["pages_headline"]);
		$pages_menu_title = strip($_POST["pages_menu_title"]);
		$pages_introduction = strip($_POST["pages_introduction"]);
		$pages_excerpt = strip($_POST["pages_excerpt"]);
		$pages_affiliate_link_webinar = $_POST["pages_affiliate_link_webinar"];
		$pages_button_text = $_POST["pages_button_text"];
		$pages_button_affiliate = $_POST["pages_button_affiliate"];
		
		// STRIP 2
		$cs_stripped_2 = str_replace($remove, "", $_POST["pages_included_affiliate_links_collection_id"]);
		$included_article_pages_ids = implode(", ", $cs_stripped_2);
		$pages_included_affiliate_links_collection_id = $cs_stripped_2;
		
		// IF $passed_id HAS A VALUE
		if(empty($passed_id)){
			$pages_id = $DB->getauto("pages");
			$insert_page = $DB->query("INSERT INTO {$dbprefix}pages SET 
				pages_id = '{$pages_id}', 
				user_id = '{$UserID}', 
				pages_name = '{$pages_name}', 
				pages_type = '{$pages_type}', 
				pages_image = '{$pages_image}', 
				pages_video_url = '{$pages_video_url}', 
				pages_headline = '{$pages_headline}', 
				pages_menu_title = '{$pages_menu_title}', 
				pages_introduction = '{$pages_introduction}', 
				pages_excerpt = '{$pages_excerpt}', 
				pages_affiliate_link_webinar = '{$pages_affiliate_link_webinar}', 
				pages_button_text = '{$pages_button_text}', 
				pages_button_affiliate = '{$pages_button_affiliate}', 
				pages_included_affiliate_links_collection_id = '{$pages_included_affiliate_links_collection_id}'");

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
					$update_pages_logo_1 = $DB->query("UPDATE {$dbprefix}exp SET pages_image = '{$pages_image}' WHERE pages_id = '{$passed_id}' AND user_id = '{$UserID}'");
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
			$update_page = $DB->query("UPDATE {$dbprefix}pages SET 
				pages_name = '{$pages_name}', 
				pages_type = '{$pages_type}', 
				pages_image = '{$pages_image}', 
				pages_video_url = '{$pages_video_url}', 
				pages_headline = '{$pages_headline}', 
				pages_menu_title = '{$pages_menu_title}', 
				pages_introduction = '{$pages_introduction}', 
				pages_excerpt = '{$pages_excerpt}', 
				pages_affiliate_link_webinar = '{$pages_affiliate_link_webinar}', 
				pages_button_text = '{$pages_button_text}', 
				pages_button_affiliate = '{$pages_button_affiliate}', 
				pages_included_affiliate_links_collection_id = '{$pages_included_affiliate_links_collection_id}' WHERE pages_id = '{$passed_id}' AND user_id = '{$UserID}'");

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
					$update_pages_logo_1 = $DB->query("UPDATE {$dbprefix}exp SET pages_image = '{$pages_image}' WHERE pages_id = '{$passed_id}' AND user_id = '{$UserID}'");
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

	#checkbox-container{
		height: 100px;
		overflow: auto;
		border: 1px solid gainsboro;
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
							<a class="btn btn-outline-secondary" href="index.php?cmd=pages"><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;Back to Pages Table</a>
						</div>
						<?php if(!empty($passed_id)) : ?>
						<div class="p-2 mr-auto">
							<button class="btn btn-outline-secondary" data-toggle="modal" data-target="#delete-modal" 
							data-pages-id="<?= $page["pages_id"]; ?>" 
							onclick="getAttributes(this)" type="button">Delete this page</button>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="card-body">
					<!-- PAGE TITLE AND TYPE -->
					<div class="form-group">
						<div class="row">
							<?php if($passed_pages_type == "article" || $page["pages_type"] == "article") : ?>
							<div class="col-md-6">
								<label>Article Name</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The name of your article."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="pages_name" value="<?= $page["pages_name"]; ?>" maxlength="100" required/>
							</div>
							<?php elseif($passed_pages_type == "c2a"  || $page["pages_type"] == "c2a") : ?>
							<div class="col-md-6">
								<label>Call to Action Name</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The name of your call to action."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="pages_name" value="<?= $page["pages_name"]; ?>" maxlength="100" required/>
							</div>
							<?php elseif($passed_pages_type == "ads"  || $page["pages_type"] == "ads") : ?>
							<div class="col-md-6">
								<label>Ad Name</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The name of your ads."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="pages_name" value="<?= $page["pages_name"]; ?>" maxlength="100" required/>
							</div>
							<?php elseif($passed_pages_type == "webinar"  || $page["pages_type"] == "webinar") : ?>
							<div class="col-md-6">
								<label>Webinar Name</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The name of your webinar."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="pages_name" value="<?= $page["pages_name"]; ?>" maxlength="100" required/>
							</div>
							<?php endif; ?>

							<div class="col-md-6">
								<label>Page Type</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The type of your page."><i class="fa fa-question"></i></span>

								<input class="form-control text-capitalize" style="cursor: not-allowed;" type="text" name="pages_type" value="<?= !empty($passed_pages_type) ? $passed_pages_type : $page["pages_type"]; ?>" readonly />
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="row">
							<!-- PAGE IMAGE -->
							<?php if($passed_pages_type == "article" || $page["pages_type"] == "article" || $passed_pages_type == "c2a" || $page["pages_type"] == "c2a" || $passed_pages_type == "ads" || $page["pages_type"] == "ads") : ?>
							<div class="col-md-12">
								<label>Page Image</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="An image for your article."><i class="fa fa-question"></i></span>

								<input class="form-control" id="pages-image" type="file" name="pages_image" value="" <?= (!empty($page["pages_image"])) ? "disabled" : "" ; ?> style="<?= (!empty($page["pages_image"])) ? "cursor: not-allowed;" : ""; ?>" />
							</div>
							<!-- PAGE IMAGE: PREVIEW -->
							<div class="col-md-12" id="pages-image-preview-container" style="<?= !empty($page["pages_image"]) ? "display: block;" : "display: none;"; ?>">
								<h5 class="mt-3">Preview</h5>
                                
								<img class="img-fluid rounded" id="pages-image-preview" src="<?= "../upload/{$UserID}/".$page["pages_image"]; ?>" />
                                <?php if( !empty($page["pages_image"]) ){ ?>
                                    <div class="img-overlay-custom text-center">
    									<a class="btn btn-danger mt-2" href="<?= "index.php?cmd=pagesedit&delete=pages_image&pages_id={$page["pages_id"]}&pages_image={$page["pages_image"]}"; ?>">Delete</a>
    								</div>
                                <?php } ?>
							</div>
							<?php elseif($passed_pages_type == "webinar"  || $page["pages_type"] == "webinar") : ?>
							<!-- PAGE VIDEO URL -->
							<div class="col-md-12">
								<label>Video URL</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The URL for your webinar page."><i class="fa fa-question"></i></span>

								<input class="form-control" id="pages-video-url" type="url" name="pages_video_url" value="<?= $page["pages_video_url"]; ?>" />
							</div>

							<!-- PAGE VIDEO URL: PREVIEW -->
							<div class="col-md-12" id="pages-video-url-preview-container" style="display: none;">
								<h5 class="mt-3">Preview</h5>
    
								<div class="embed-responsive embed-responsive-16by9 rounded">
									<iframe class="embed-responsive-item" id="pages-video-url-preview" src=""></iframe>
								</div>
							</div>
							<?php endif; ?>
						</div>
					</div>

					<!-- PAGE ARTICLE NAME AND MENU TITLE -->
					<div class="form-group">
						<div class="row">
							<?php if($passed_pages_type == "article" || $page["pages_type"] == "article") : ?>
							<div class="col-md-6">
								<label>Article Headline</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The headline of your article."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="pages_headline" value="<?= $page["pages_headline"]; ?>" maxlength="100" required/>
							</div>

							<div class="col-md-6">
								<label>Menu Title</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The menu title of your article. It will displayed in the top navigation. Keep it at 25 characters max."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="pages_menu_title" value="<?= $page["pages_menu_title"]; ?>" maxlength="25" required/>
							</div>
							<?php elseif($passed_pages_type == "c2a"  || $page["pages_type"] == "c2a") : ?>
							<div class="col-md-12">
								<label>Call to Action Headline</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The headline of your call to action."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="pages_headline" value="<?= $page["pages_headline"]; ?>" maxlength="100" required/>
							</div>
							<?php elseif($passed_pages_type == "webinar"  || $page["pages_type"] == "webinar") : ?>
							<div class="col-md-12">
								<label>Webinar Affiliate Link URL</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The headline of your webinar."><i class="fa fa-question"></i></span>

								<input class="form-control" type="url" name="pages_affiliate_link_webinar" value="<?= $page["pages_affiliate_link_webinar"]; ?>" />
							</div>
							<?php endif; ?>
						</div>
					</div>
                    <?php if($passed_pages_type == "article" || $page["pages_type"] == "article") : ?>
					<!--PAGE EXCERPT -->
					<div class="form-group">
						<div class="row">
							<div class="col-md-12">
								<label>Homepage Excerpt</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The excerpt of your page."><i class="fa fa-question"></i></span>

								<textarea class="form-control" name="pages_excerpt" rows="3" required><?= $page["pages_excerpt"]; ?></textarea>
							</div>
						</div>
					</div>
					<?php endif; ?>
					<?php if( ($passed_pages_type == "article" || $page["pages_type"] == "article") && ($passed_pages_type != "c2a" || $page["pages_type"] != "c2a") ) : ?>
					<!-- PAGE INTRODUCTION -->
					<div class="form-group">
						<div class="row">
							<div class="col-md-12">
								<label>Text</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The introduction of your page."><i class="fa fa-question"></i></span>

								<textarea class="form-control" name="pages_introduction" rows="5" required><?= $page["pages_introduction"]; ?></textarea>
							</div>
						</div>
					</div>
					<?php endif; ?>
					
					<?php if($passed_pages_type == "ads" || $page["pages_type"] == "ads" || $passed_pages_type == "c2a" || $page["pages_type"] == "c2a") : ?>
					<!-- BUTTON TEXT -->
					<div class="form-group">
						<div class="row">
							<div class="col-md-6">
								<label>Button Text</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The button text of your page."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="pages_button_text" value="<?= $page["pages_button_text"]; ?>" required/>
							</div>
							<?php if ( !($passed_pages_type == "c2a" || $page["pages_type"] == "c2a") ): ?>
							<div class="col-md-6">
								<label>Button Affiliate</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The button affiliate of your page."><i class="fa fa-question"></i></span>

								<input class="form-control" type="url" name="pages_button_affiliate" value="<?= $page["pages_button_affiliate"]; ?>" />
							</div>
							<?php else: ?>
							<div class="col-md-6">
								<label>Will redirect to webinar page</label>
								<input class="form-control" type="url" name="pages_button_affiliate" value="" disabled />
							</div>
							<?php endif; ?>
						</div>
					</div>
					<?php endif; ?>

					<!-- AFFILIATE LINKS: COLLECTION -->
					<!--<?php if($affiliate_links_collection && $passed_pages_type == "article" || $page["pages_type"] == "article") : ?>-->
					<!--<div class="form-group">-->
					<!--	<div class="row">-->
					<!--		<div class="col-md-12">-->
					<!--			<label>Choose a collection to include in your page.</label>-->
					<!--			<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Choose an affiliate links to include in your page."><i class="fa fa-question"></i></span>-->
					<!--			<a class="btn btn-sm btn-outline-secondary ml-2" href="index.php?cmd=affiliatelinkscollection">Manage your Affiliate Links: Collection</a>-->

					<!--			<select class="form-control" name="pages_included_affiliate_links_collection_id">-->
					<!--				<option selected disabled>Choose</option>-->
					<!--				<?php foreach($affiliate_links_collection as $collection) : ?>-->
					<!--				<option value="<?= $collection["affiliate_links_collection_title"]; ?>" <?= ($collection["affiliate_links_collection_title"] == $page["pages_included_affiliate_links_collection_id"]) ? "selected" : "" ; ?>><?= $collection["affiliate_links_collection_title"]; ?></option>-->
					<!--				<?php endforeach; ?>-->
					<!--			</select>-->
					<!--		</div>-->
					<!--	</div>-->
					<!--</div>-->
					<!--<?php elseif($passed_pages_type == "webinar"  || $page["pages_type"] == "webinar") : ?>-->
					<!--<hr />-->
					<!--<h5 class="text-center">Collections is unavailable in a Webinar Page.</a></h5>-->
					<!--<?php else : ?>-->
					<!--<hr />-->
					<!--<h5 class="text-center">Oops! You don't have any collection! <a href="index.php?cmd=affiliatelinkscollection">Create one now!</a></h5>-->
					<!--<?php endif; ?>-->
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

<!-- CODE_SECTION_HTML_2: MODALS -->
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
<?php if($passed_pages_type == "article" || $page["pages_type"] == "article" || $passed_pages_type == "c2a" || $page["pages_type"] == "c2a" || $passed_pages_type == "ads" || $page["pages_type"] == "ads") : ?>
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

			var optimized_url = "";

			if(pages_video_url.value.includes("youtu.be")){
				optimized_url = pages_video_url.value.replace("youtu.be", "youtube.com/embed");
			}

			if(pages_video_url.value.includes("watch")){
				optimized_url = pages_video_url.value.replace("watch", "embed");
			}

			if(pages_video_url.value.includes("watch?v=")){
				optimized_url = pages_video_url.value.replace("watch?v=", "embed/");
			}

			if(pages_video_url.value.includes("vimeo.com")){
				optimized_url = pages_video_url.value.replace("vimeo.com", "player.vimeo.com/video");
			}

			pages_video_url_preview.src = optimized_url;
		}

		// ON LOAD
		if(pages_video_url.value == ""){
			pages_video_url_preview_container.style.display = "none";
		}
		else{
			pages_video_url_preview_container.style.display = "block";
		}

		var optimized_url = "";

		if(pages_video_url.value.includes("youtu.be")){
			optimized_url = pages_video_url.value.replace("youtu.be", "youtube.com/embed");
		}

		if(pages_video_url.value.includes("watch")){
			optimized_url = pages_video_url.value.replace("watch", "embed");
		}

		if(pages_video_url.value.includes("watch?v=")){
			optimized_url = pages_video_url.value.replace("watch?v=", "embed/");
		}

		if(pages_video_url.value.includes("vimeo.com")){
			optimized_url = pages_video_url.value.replace("vimeo.com", "player.vimeo.com/video");
		}

		pages_video_url_preview.src = optimized_url;
	}
</script>
<?php endif; ?>