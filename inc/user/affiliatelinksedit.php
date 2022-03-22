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
	$campaigns_type = "regular";

	// QUERIES
	$affiliate_links = $DB->info("affiliate_links", "affiliate_links_id = '{$passed_id}'");
	$affiliate_links_collection = $DB->query("SELECT * FROM {$dbprefix}affiliate_links_collection WHERE user_id = '{$UserID}'");

	// IF FORM SUBMITS
	if($_POST["submit"]){
		// POST VARIABLES
		$affiliate_links_product_name = strip($_POST["affiliate_links_product_name"]);
		$affiliate_links_product_subheadline = strip($_POST["affiliate_links_product_subheadline"]);
		$affiliate_links_button_text = strip($_POST["affiliate_links_button_text"]);
		$affiliate_links_link = $_POST["affiliate_links_link"];
		$affiliate_links_link_user = ($_POST["affiliate_links_link_user"]) ? $_POST["affiliate_links_link_user"] : "#";
		$affiliate_links_content = strip($_POST["affiliate_links_content"]);
		$affiliate_links_collection_ids = implode(", ", $_POST["affiliate_links_collection_ids"]);
		$product_image = empty($_FILES["product_image"]["name"]) ? $campaign["product_image"] : $_FILES["product_image"]["name"];
		
		// IF $passed_id DOESN'T HAVE A VALUE
		if(empty($passed_id)){
			$affiliate_links_id = $DB->getauto("affiliate_links");
			$insert_affiliate_links = $DB->query("INSERT INTO {$dbprefix}affiliate_links SET 
				affiliate_links_id = '{$affiliate_links_id}', 
				user_id = '{$UserID}', 
				affiliate_links_product_name = '{$affiliate_links_product_name}', 
				affiliate_links_product_subheadline = '{$affiliate_links_product_subheadline}', 
				affiliate_links_button_text = '{$affiliate_links_button_text}', 
				affiliate_links_link = '{$affiliate_links_link}', 
				affiliate_links_link_user = '{$affiliate_links_link_user}', 
				affiliate_links_content = '{$affiliate_links_content}'
			");
			
			// UPLOAD PROCESS: PAGES LOGO AND PAGES IMAGE
			$upload_directory = "../upload/{$UserID}/";

			$target_file_1 = $upload_directory . basename($_FILES["product_image"]["name"]);
			$upload_status_1 = 1;
			$get_file_extension_1 = strtolower(pathinfo($target_file_1, PATHINFO_EXTENSION));
			
			if(!empty($_FILES["product_image"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_1 != "jpg" && $get_file_extension_1 != "jpeg" && $get_file_extension_1 != "png"){
					$upload_status_1 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["product_image"]["size"] > 1000000){
					$upload_status_1 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_1 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file_1);
					$update_pages_logo_1 = $DB->query("UPDATE {$dbprefix}affiliate_links SET product_image = '{$product_image}' WHERE affiliate_links_id = '{$affiliate_links_id}' AND user_id = '{$UserID}'");
				}
			}

			if($insert_affiliate_links){
				$_SESSION["msg_success"] = "Affiliate Link creation successful.";

				redirect("index.php?cmd=affiliatelinks");
			}
			else{
				$_SESSION["msg_error"] = "Affiliate Link creation failure.";
			}
		}
		else{
			$update_affiliate_links = $DB->query("UPDATE {$dbprefix}affiliate_links SET 
				affiliate_links_product_name = '{$affiliate_links_product_name}', 
				affiliate_links_product_subheadline = '{$affiliate_links_product_subheadline}', 
				affiliate_links_button_text = '{$affiliate_links_button_text}', 
				affiliate_links_link = '{$affiliate_links_link}', 
				affiliate_links_link_user = '{$affiliate_links_link_user}', 
				affiliate_links_content = '{$affiliate_links_content}' 
				WHERE affiliate_links_id = '{$passed_id}'
			");
			
			// UPLOAD PROCESS: PAGES LOGO AND PAGES IMAGE
			$upload_directory = "../upload/{$UserID}/";

			$target_file_1 = $upload_directory . basename($_FILES["product_image"]["name"]);
			$upload_status_1 = 1;
			$get_file_extension_1 = strtolower(pathinfo($target_file_1, PATHINFO_EXTENSION));
			
			if(!empty($_FILES["product_image"]["name"])){
				// FILE EXTENSION CHECK
				if($get_file_extension_1 != "jpg" && $get_file_extension_1 != "jpeg" && $get_file_extension_1 != "png"){
					$upload_status_1 = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				if($_FILES["product_image"]["size"] > 1000000){
					$upload_status_1 = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				if($upload_status_1 == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file_1);
					$update_pages_logo_1 = $DB->query("UPDATE {$dbprefix}affiliate_links SET product_image = '{$product_image}' WHERE affiliate_links_id = '{$passed_id}' AND user_id = '{$UserID}'");
				}
			}

			if($update_affiliate_links){
				$_SESSION["msg_success"] = "Affiliate Link update successful.";

				redirect("index.php?cmd=affiliatelinks");
			}
			else{
				$_SESSION["msg_error"] = "Affiliate Link update failure.";
			}
		}
	}

	// DELETE PROCESS: AFFILIATE LINKS
	if(!empty($_GET["affiliate_links_id"])){
		$id_to_delete = $_GET["affiliate_links_id"];

		$delete_affiliate_link = $DB->query("DELETE FROM {$dbprefix}affiliate_links WHERE affiliate_links_id = '{$id_to_delete}'");

		if($delete_affiliate_link){
			$_SESSION["msg_success"] = "Affiliate Link deleted.";

			redirect("index.php?cmd=affiliatelinks");
		}
	}
	
	// DELETE PRODUCTS IMAGE
	if(!empty($_GET["delete"])){
		$id_to_delete = $_GET["id"];
		if($_GET["delete"] == "product_image"){
			$deletion_update_pages_image = $DB->query("UPDATE {$dbprefix}affiliate_links SET product_image = '' WHERE affiliate_links_id = '{$id_to_delete}'");
		}

		if($deletion_update_pages_image){
			if(unlink("../upload/{$UserID}/{$_GET["product_image"]}")){
				redirect("index.php?cmd=affiliatelinksedit&id={$id_to_delete}");
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
							<a class="btn btn-outline-secondary" href="index.php?cmd=affiliatelinks"><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;Back to Affiliate Links Table</a>
						</div>
						<?php if(!empty($passed_id)) : ?>
						<div class="p-2 mr-auto">
							<button class="btn btn-outline-secondary" data-toggle="modal" data-target="#delete-modal" 
							data-id-to-delete="<?= $affiliate_links["affiliate_links_id"]; ?>" 
							onclick="getAttributes(this)" type="button">Delete this affiliate link</button>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="card-body">
					<!-- PRODUCT NAME AND SUBHEADLINE -->
					<div class="form-group">
						<div class="row">
							<div class="col-md-6">
								<label>Product Name</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Give your product a name."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="affiliate_links_product_name" value="<?= $affiliate_links["affiliate_links_product_name"]; ?>" maxlength="50" required />
							</div>

							<div class="col-md-6">
								<label>Headline</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The headline for your product name."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="affiliate_links_product_subheadline" value="<?= $affiliate_links["affiliate_links_product_subheadline"]; ?>" maxlength="50" required />
							</div>
							
							<div class="col-md-12">
								<label>Content</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The content of your product."><i class="fa fa-question"></i></span>

								<textarea class="form-control" rows="3" name="affiliate_links_content" required><?= $affiliate_links["affiliate_links_content"]; ?></textarea>
							</div>
							
						    <div class="col-md-12">
								<label>Affiliate Link</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The affiliate link of your product."><i class="fa fa-question"></i></span>

								<input class="form-control" type="url" name="affiliate_links_link_user" value="<?= $affiliate_links["affiliate_links_link_user"]; ?>" required />
							</div>
						</div>
					</div>

					 <!--AFFILIATE LINK -->
					<div class="form-group">
						<div class="row">
						    <div class="col-md-12">
								<label>Product Image</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The logo of your page."><i class="fa fa-question"></i></span>

								<input class="form-control-file" id="pages-image" type="file" name="product_image" value="" <?= (!empty($affiliate_links["product_image"])) ? "disabled" : "" ; ?> style="<?= (!empty($affiliate_links["product_image"])) ? "cursor: not-allowed;" : ""; ?>" required />
							</div>
							<!-- PAGE IMAGE: PREVIEW -->
							<div class="col-md-12" id="pages-image-preview-container" style="<?= !empty($affiliate_links["product_image"]) ? "display: block;" : "display: none;"; ?>">
								<h5 class="mt-3">Preview</h5>
                                
								<img class="img-fluid rounded" id="pages-image-preview" src="<?= "../upload/{$UserID}/".$affiliate_links["product_image"]; ?>" />
								
								<?php if( !empty($affiliate_links["product_image"]) ){ ?>
                                    <div class="img-overlay-custom text-center">
    									<a class="btn btn-danger mt-2" href="<?= "index.php?cmd=affiliatelinksedit&id={$passed_id}&delete=product_image&product_image={$affiliate_links["product_image"]}"; ?>">Delete</a>
    								</div>
                                <?php } ?>
							</div>
							<div class="col-md-6">
								<label>Button Text</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The display text of your affiliate button."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="affiliate_links_button_text" value="<?= $affiliate_links["affiliate_links_button_text"]; ?>" maxlength="50" required />
							</div>

							<div class="col-md-6">
								<label>Apply Link</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The apply link of your product."><i class="fa fa-question"></i></span>

								<input class="form-control" type="url" name="affiliate_links_link" value="<?= $affiliate_links["affiliate_links_link"]; ?>" />
							</div>
						</div>
					</div>

					<!-- AFFILIATE LINK -->
					<!--<div class="form-group">-->
					<!--	<div class="row">-->
					<!--		<div class="col-md-12">-->
					<!--			<label>Affiliate Link</label>-->
					<!--			<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The affiliate link of your product."><i class="fa fa-question"></i></span>-->

					<!--			<input class="form-control" type="url" name="affiliate_links_link_user" value="<?= $affiliate_links["affiliate_links_link_user"]; ?>" />-->
					<!--		</div>-->
					<!--	</div>-->
					<!--</div>-->

					<!--INTRODUCTION -->
					<!--<div class="form-group">-->
					<!--	<div class="row">-->
					<!--		<div class="col-md-12">-->
					<!--			<label>Content</label>-->
					<!--			<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The content of your product."><i class="fa fa-question"></i></span>-->

					<!--			<textarea class="form-control" rows="8" name="affiliate_links_content"><?= $affiliate_links["affiliate_links_content"]; ?></textarea>-->
					<!--		</div>-->
					<!--	</div>-->
					<!--</div>-->
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
							<a class="btn btn-outline-secondary btn-block" href="index.php?cmd=affiliatelinks">
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

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal fade" id="delete-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Delete Confirmation</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body text-center">Are you sure you want to <b>DELETE</b> this affiliate link?</div>
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
		var id_to_delete = attributes.getAttribute("data-id-to-delete");
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=affiliatelinksedit&affiliate_links_id=${id_to_delete}`;
	}
</script>
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