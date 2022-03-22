<?php
	if(!preg_match(";exp;", $cur_pack["pack_ar"])){
		redirect("index.php?cmd=deny");
	}

	// PASSED ID IF EDIT
	$passed_id = $_GET["id"];

	// DATABASE FETCH
	$exps_initial = $DB->info("exp", "user_id = '{$UserID}' AND exp_id = '{$passed_id}'");

	if($_POST["submit"]){
		// POST VARIABLES
		$exp_title = $_POST["exp_title"];
		$exp_type = $_POST["exp_type"];
		$exp_image = ($_FILES["exp_image"]["name"] == "") ? $exps_initial["exp_image"] : $_FILES["exp_image"]["name"];
		$exp_video_url = $_POST["exp_video_url"];
		$exp_headline = $_POST["exp_headline"];
		$exp_sub_headline = $_POST["exp_sub_headline"];
		$exp_button_text = $_POST["exp_button_text"];
		$exp_button_url = $_POST["exp_button_url"];

		if(isset($passed_id)){
			$update = $DB->query("UPDATE {$dbprefix}exp SET exp_title = '{$exp_title}', 
				exp_type = '{$exp_type}', 
				exp_image = '{$exp_image}', 
				exp_video_url = '{$exp_video_url}', 
				exp_headline = '{$exp_headline}', 
				exp_sub_headline = '{$exp_sub_headline}', 
				exp_button_text = '{$exp_button_text}', 
				exp_button_url = '{$exp_button_url}' WHERE exp_id = '{$passed_id}' AND user_id = '{$UserID}'");

			// FILE UPLOAD PROCESS
			$upload_directory = "../upload/{$UserID}/";

			// TARGET FILES
			$target_file = $upload_directory . basename($_FILES["exp_image"]["name"]);

			// UPLOAD STATUS. SET TO 1 AS DEFAULT
			$upload_status = 1;

			// GET FILE EXTENSION
			$get_file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

			// SQUEEZE IMAGE VALIDATIONS
			if($_FILES["exp_image"]["name"] != ""){
				// FILE EXTENSION CHECK
				if($get_file_extension != "jpg" && $get_file_extension != "jpeg" && $get_file_extension != "png"){
					$upload_status = 0;

					$site_message_error .= "• The file you placed in \"Squeeze Image\" should only be jpg, jpeg, png." . "<br />";
				}

				// FILE EXISTENCE CHECK
				// NOTE: REMOVED 2020.01.05 BECAUSE OF FEEDBACK (IT SHOULDN'T BE REMOVED BUT...)
				// if(file_exists($target_file)){
				// 	$upload_status = 0;

				// 	$site_message_error .= "• The file you placed in \"Squeeze Image\" already exists. Rename your file or choose a different image with a different name." . "<br />";
				// }

				// FILE SIZE CHECK. MEASURED IN BYTES
				if($_FILES["exp_image"]["size"] > 1000000){
					$upload_status = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				// UPLOAD CHECK
				if($upload_status == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					// UPLOAD IN DIRECTORY
					move_uploaded_file($_FILES["exp_image"]["tmp_name"], $target_file);
					// THEN UPDATE THE DATABASE
					$update_exp_image = $DB->query("UPDATE {$dbprefix}exp SET exp_image = '{$exp_image}' WHERE exp_id = '{$passed_id}' AND user_id = '{$UserID}'");
				}
			}

			if($update && !$site_message_error){
				$site_message_success = "Success! Exit Pop-Up updated.";
				$_SESSION["msg_success"] = $site_message_success;

				redirect("index.php?cmd=exp");
			}
			else{
				$_SESSION["msg_error"] = $site_message_error;

				redirect("index.php?cmd=expedit");
			}
		}
		else{
			$id = $DB->getauto("exp");
			$insert = $DB->query("INSERT INTO {$dbprefix}exp SET exp_id = '{$id}', 
				user_id = '{$UserID}', 
				exp_title = '{$exp_title}', 
				exp_type = '{$exp_type}', 
				exp_image = '{$exp_image}', 
				exp_video_url = '{$exp_video_url}', 
				exp_headline = '{$exp_headline}', 
				exp_sub_headline = '{$exp_sub_headline}', 
				exp_button_text = '{$exp_button_text}', 
				exp_button_url = '{$exp_button_url}'");

			// FILE UPLOAD PROCESS
			$upload_directory = "../upload/{$UserID}/";

			// TARGET FILES
			$target_file = $upload_directory . basename($_FILES["exp_image"]["name"]);

			// UPLOAD STATUS. SET TO 1 AS DEFAULT
			$upload_status = 1;

			// GET FILE EXTENSION
			$get_file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

			// SQUEEZE IMAGE VALIDATIONS
			if($_FILES["exp_image"]["name"] != ""){
				// FILE EXTENSION CHECK
				if($get_file_extension != "jpg" && $get_file_extension != "jpeg" && $get_file_extension != "png"){
					$upload_status = 0;

					$site_message_error .= "• The file you placed in image should only be jpg, jpeg, png." . "<br />";
				}

				// FILE EXISTENCE CHECK
				// NOTE: REMOVED 2020.01.05 BECAUSE OF FEEDBACK (IT SHOULDN'T BE REMOVED BUT...)
				// if(file_exists($target_file)){
				// 	$upload_status = 0;

				// 	$site_message_error .= "• The file you placed in \"Squeeze Image\" already exists. Rename your file or choose a different image with a different name." . "<br />";
				// }

				// FILE SIZE CHECK. MEASURED IN BYTES
				if($_FILES["exp_image"]["size"] > 1000000){
					$upload_status = 0;

					$site_message_error .= "• Keep the image less than or equal to 1MB only." . "<br />";
				}

				// UPLOAD CHECK
				if($upload_status == 0){
					$site_message_error .= "• There was an error uploading your image." . "<br />";
				}
				else{
					// UPLOAD IN DIRECTORY
					move_uploaded_file($_FILES["exp_image"]["tmp_name"], $target_file);
					// THEN UPDATE THE DATABASE
					$update_exp_image = $DB->query("UPDATE {$dbprefix}exp SET exp_image = '{$exp_image}' WHERE exp_id = '{$passed_id}' AND user_id = '{$UserID}'");
				}
			}

			if($insert && !$site_message_error){
				$site_message_success = "Success! Exit Pop-Up created.";
				$_SESSION["msg_success"] = $site_message_success;

				redirect("index.php?cmd=exp");
			}
			else{
				$_SESSION["msg_error"] = $site_message_error;

				redirect("index.php?cmd=expedit");
			}
		}
	}
	else{
		if(isset($passed_id)){
			$exit_pop_up_data = $DB->info("exp", "user_id = '{$UserID}' AND exp_id = '{$passed_id}'");
		}

		$exit_pop_ups = array(
			"Pop-Up Video", 
			"Pop-Up Full Page", 
			"Pop-Up Two Buttons"
		);
	}
?>
<div class="container-fluid">
	<form method="POST" enctype="multipart/form-data" id="facebook-tools-form">
		<!-- DISPLAY ERROR -->
		<?php if($_SESSION["msg_error"]) : ?>
		<div class="col-md-12">
			<div class="alert alert-danger"><?php echo $_SESSION["msg_error"]; $_SESSION["msg_error"] = ""; ?></div>
		</div>
		<?php endif; ?>

		<!-- EXIT POP-UPS SECTION -->
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-12">
					<div class="card">
						<div class="content card-body">
							<h4 class="text-center"><i class="fa fa-sign-out"></i> &nbsp;Exit Pop-Ups</h4>

							<div class="row mt-2">
								<div class="col-md-6">
									<div class="form-group">
										<label>Title</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The title for your Exit Pop-Up."><i class="fa fa-question" aria-hidden="true"></i></span>

										<input class="form-control" type="text" name="exp_title" value="<?= $exit_pop_up_data["exp_title"]; ?>" maxlength="100" required />
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label>Type</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="What type of Exit Pop-Up do you want?"><i class="fa fa-question" aria-hidden="true"></i></span>

										<select class="form-control" id="exp_type" name="exp_type">
											<option disabled selected>Choose an option</option>

											<?php foreach($exit_pop_ups as $exit_pop_up) : ?>
											<option value="<?= $exit_pop_up; ?>" <?= $exit_pop_up_data["exp_type"] == $exit_pop_up ? "selected" : ""; ?>><?= $exit_pop_up; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>

							<div class="row" id="exp_image_preview_container">
								<div class="col-md-12">
									<div class="form-group">
										<label>Background Image</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The background image for Pop-Up Full Page."><i class="fa fa-question" aria-hidden="true"></i></span>

										<input class="form-control" id="exp_image" type="file" name="exp_image" />
									</div>

									<div class="mt-2">
										<label>Preview</label>

										<div class="col-md-4 mx-auto">
											<img id="exp_image_preview" src="<?= "../upload/{$UserID}/" . $exit_pop_up_data["exp_image"]; ?>" style="width: 100%;" />
										</div>
									</div>
								</div>
							</div>

							<div class="row" id="exp_video_url_preview_container">
								<div class="col-md-12">
									<div class="form-group">
										<label>Video URL</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The video URL for Pop-Up Video. Note that you should not get URL from YouTube Playlists."><i class="fa fa-question" aria-hidden="true"></i></span>

										<input class="form-control" id="exp_video_url" type="text" value="<?= $exit_pop_up_data["exp_video_url"]; ?>" name="exp_video_url" />
									</div>

									<div class="mt-2">
										<label>Preview</label>

										<iframe id="exp_video_url_preview" src="" style="width: 100%; height: 500px; border: none; background-color: gainsboro;"></iframe>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Headline</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The headline for your Exit Pop-Up."><i class="fa fa-question" aria-hidden="true"></i></span>

										<input class="form-control" type="text" name="exp_headline" value="<?= $exit_pop_up_data["exp_headline"]; ?>" maxlength="100" required />
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label>Sub Headline</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The sub headline for your Exit Pop-Up."><i class="fa fa-question" aria-hidden="true"></i></span>

										<input class="form-control" type="text" name="exp_sub_headline" value="<?= $exit_pop_up_data["exp_sub_headline"]; ?>" maxlength="100" required />
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Button Text</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The button text for your Exit Pop-Up."><i class="fa fa-question" aria-hidden="true"></i></span>

										<input class="form-control" type="text" name="exp_button_text" value="<?= $exit_pop_up_data["exp_button_text"]; ?>" maxlength="100" required />
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label>Button URL</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The button URL for your Exit Pop-Up."><i class="fa fa-question" aria-hidden="true"></i></span>

										<input class="form-control" type="url" name="exp_button_url" value="<?= $exit_pop_up_data["exp_button_url"]; ?>" required />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php if($passed_id) : ?>
		<div class="col-md-12 mb-3">
			<button class="btn btn-primary btn-block" type="submit" name="submit" value="submit">
				<i class="fa fa-save"></i> &nbsp;Save All Edits
			</button>
		</div>
		<?php else : ?>
		<div class="col-md-12 mb-3">
			<button class="btn btn-primary btn-block" type="submit" name="submit" value="submit">
				<i class="fa fa-pencil-square-o"></i> &nbsp;Create Exit Pop-Up
			</button>
		</div>
		<?php endif; ?>
	</form>
</div>

<script type="text/javascript">
	// PREVIEW IMAGE ON-INPUT
	function readFile(input){
		var exp_image = document.getElementById("exp_image");
		var exp_image_preview = document.getElementById("exp_image_preview");

		if(input.files && input.files[0]){
			var file_reader = new FileReader();

			file_reader.onload = function(e){
				exp_image_preview.src = e.target.result;
			}

			file_reader.readAsDataURL(input.files[0]);
		}
	}

	var exp_image = document.getElementById("exp_image");
	exp_image.oninput = function(){
		readFile(this);
	}

	var exp_video_url = document.getElementById("exp_video_url");
	var exp_video_url_preview = document.getElementById("exp_video_url_preview");
	exp_video_url.oninput = function(){
		var replaced_url = "";
		if(exp_video_url.value.includes("youtu.be")){
			replaced_url = exp_video_url.value.replace("youtu.be", "youtube.com/embed");
		}

		if(exp_video_url.value.includes("watch")){
			replaced_url = exp_video_url.value.replace("watch", "embed");
		}

		if(exp_video_url.value.includes("watch?v=")){
			replaced_url = exp_video_url.value.replace("watch?v=", "embed/");
		}
		exp_video_url_preview.src = replaced_url;
	}

	window.onload = function(){
		var replaced_url = "";
		if(exp_video_url.value.includes("youtu.be")){
			replaced_url = exp_video_url.value.replace("youtu.be", "youtube.com/embed");
		}

		if(exp_video_url.value.includes("watch")){
			replaced_url = exp_video_url.value.replace("watch", "embed");
		}

		if(exp_video_url.value.includes("watch?v=")){
			replaced_url = exp_video_url.value.replace("watch?v=", "embed/");
		}
		exp_video_url_preview.src = replaced_url;

		// DISPLAY NONE AND DISABLE THE INPUT DEPENDING ON THE exp_type
		var exp_type = document.getElementById("exp_type");
		var exp_image_preview_container = document.getElementById("exp_image_preview_container");
		var exp_video_url_preview_container = document.getElementById("exp_video_url_preview_container");

		if(exp_type.options[exp_type.selectedIndex].value == "Pop-Up Video"){
			exp_image.disabled = true;
			exp_image_preview_container.style.display = "none";
			exp_video_url.disabled = false;
			exp_video_url_preview_container.style.display = "block";
		}
		else{
			exp_image.disabled = false;
			exp_image_preview_container.style.display = "block";
			exp_video_url.disabled = true;
			exp_video_url_preview_container.style.display = "none";
		}

		exp_type.onchange = function(){
			if(exp_type.options[exp_type.selectedIndex].value == "Pop-Up Video"){
				exp_image.disabled = true;
				exp_image_preview_container.style.display = "none";
				exp_video_url.disabled = false;
				exp_video_url_preview_container.style.display = "block";
			}
			else{
				exp_image.disabled = false;
				exp_image_preview_container.style.display = "block";
				exp_video_url.disabled = true;
				exp_video_url_preview_container.style.display = "none";
			}
		}
	}
</script>