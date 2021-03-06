<?php
    
	// CODE_SECTION_PHP_1: PRIVILEGE
	if(!preg_match(";ads;", $cur_pack["pack_ar"])){
		// redirect("index.php?cmd=deny");
		// NOTE: COMMENTED-OUT BECAUSE ads IS STILL NOT ADDED AS A PACKAGE.
	}

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
						<div class="d-flex">
    						<div class="p-2 <?= empty($passed_id) ? "" : ""; ?>">
    							<a class="btn btn-outline-secondary" href="index.php?cmd=ads2"><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;Back to ads Table</a>
    						</div>
    						<?php if(!empty($passed_id)) : ?>
    						<div class="p-2">
    							<button class="btn btn-outline-secondary" data-toggle="modal" data-target="#delete-modal" 
    							data-ads-id="<?= $ads["ads_id"]; ?>" 
    							onclick="getAttributes(this)" type="button">Delete this ads</button>
    						</div>
    						<?php endif; ?>
    					</div>
					</div>
				</div>
				<div class="card-body">
					<!-- Ads TITLE AND TYPE -->
					<div class="form-group">
						<div class="row">
                            <div class="col-md-4">
								<label>Ads Name</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The name of your ads."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="ads_name" value="<?= $ads["ads_name"]; ?>" maxlength="100" required/>
							</div>
                            <div class="col-md-4">
								<label>Ads URL</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The url of your ads."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="ads_url" value="<?= $ads["ads_url"]; ?>" maxlength="100" required/>
							</div>
							
							<div class="col-md-4">
								<label>Ads Type</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The type of your ads."><i class="fa fa-question"></i></span>
                                
                                <select class="form-control" name="ads_type">
        							<option selected disabled>Select an option</option>
        							<option value="Banner" <?= $ads['ads_type'] == 'Banner' ? 'selected' : '' ?>>Banner</option>
        							<option value="Sidebar" <?= $ads['ads_type'] == 'Sidebar' ? 'selected' : '' ?>>Sidebar</option>
    							</select>
							</div>

                            <!-- ADS IMAGE -->
                            <div class="col-md-12">
								<label>Ads Image</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="An image for your ads."><i class="fa fa-question"></i></span>

								<input class="form-control" id="ads-image" type="file" name="ads_image" value="" <?= (!empty($ads["ads_image"])) ? "disabled" : "" ; ?> style="<?= (!empty($ads["ads_image"])) ? "cursor: not-allowed;" : ""; ?>" />
							</div>

							<!-- ADS IMAGE: PREVIEW -->
							<div class="col-md-12" id="ads-image-preview-container" style="<?= !empty($ads["ads_image"]) ? "display: block;" : "display: none;"; ?>">
								<h5 class="mt-3">Preview</h5>
                                
								<img class="img-fluid rounded" id="ads-image-preview" src="<?= "../upload/{$UserID}/".$ads["ads_image"]; ?>" />
                                <?php if( !empty($ads["ads_image"]) ){ ?>
                                    <div class="img-overlay-custom text-center">
    									<a class="btn btn-danger mt-2" href="<?= "index.php?cmd=ads2edit&delete=ads_image&ads_id={$ads["ads_id"]}&ads_image={$ads["ads_image"]}"; ?>">Delete</a>
    								</div>
                                <?php } ?>
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
							<a class="btn btn-outline-secondary btn-block" href="index.php?cmd=ads2">
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
			<div class="modal-body text-center">Are you sure you want to <b>DELETE</b> this ads?</div>
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
		var id_to_delete = attributes.getAttribute("data-ads-id");
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=ads2&ads_id=${id_to_delete}`;
	}
</script>

<script type="text/javascript">
	window.onload = function(){
		var ads_image = document.getElementById("ads-image");
		var ads_image_preview = document.getElementById("ads-image-preview");
		var ads_image_preview_container = document.getElementById("ads-image-preview-container");

		function readFile(input){

			if(input.files && input.files[0]){
				var file_reader = new FileReader();

				file_reader.onload = function(e){
					ads_image_preview.src = e.target.result;
				}

				file_reader.readAsDataURL(input.files[0]);
			}
		}

		ads_image.oninput = function(){
			if(ads_image.value == ""){
				ads_image_preview_container.style.display = "none";
			}
			else{
				ads_image_preview_container.style.display = "block";
			}

			readFile(this);
		}
	}
</script>