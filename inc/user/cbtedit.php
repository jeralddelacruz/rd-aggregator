<?php
	if(!preg_match(";fbp;", $cur_pack["pack_ar"])){
		redirect("index.php?cmd=deny");
	}

	// PASSED ID IF EDIT
	$passed_id = $_GET["id"];

	if($_POST["submit"]){
		// POST VARIABLES
		$cbt_title = $_POST["cbt_title"];
		$cbt_fb_pixel_code_snippet = $_POST["cbt_fb_pixel_code_snippet"];
		$cbt_fb_comments_sdk = $_POST["cbt_fb_comments_sdk"];
		$cbt_fb_comments_code_snippet = $_POST["cbt_fb_comments_code_snippet"];
		$cbt_fb_chat_sdk_and_code_snippet = strip($_POST["cbt_fb_chat_sdk_and_code_snippet"], 0);

		if(isset($passed_id)){
			$update = $DB->query("UPDATE {$dbprefix}cbt SET cbt_title = '{$cbt_title}', 
				cbt_fb_pixel_code_snippet = '{$cbt_fb_pixel_code_snippet}', 
				cbt_fb_comments_sdk = '{$cbt_fb_comments_sdk}', 
				cbt_fb_comments_code_snippet = '{$cbt_fb_comments_code_snippet}', 
				cbt_fb_chat_sdk_and_code_snippet = '{$cbt_fb_chat_sdk_and_code_snippet}' WHERE cbt_id = '{$passed_id}' AND user_id = '{$UserID}'");
		}
		else{
			$id = $DB->getauto("cbt");
			$insert = $DB->query("INSERT INTO {$dbprefix}cbt SET cbt_id = '{$id}', 
				user_id = '{$UserID}', 
				cbt_title = '{$cbt_title}', 
				cbt_fb_pixel_code_snippet = '{$cbt_fb_pixel_code_snippet}', 
				cbt_fb_comments_sdk = '{$cbt_fb_comments_sdk}', 
				cbt_fb_comments_code_snippet = '{$cbt_fb_comments_code_snippet}', 
				cbt_fb_chat_sdk_and_code_snippet = '{$cbt_fb_chat_sdk_and_code_snippet}'");

			if($insert){
				$site_message_success = "Success! Facebook Tool created.";
				$_SESSION["msg_success"] = $site_message_success;

				redirect("index.php?cmd=cbt");
			}
			else{
				$_SESSION["msg_error"] = $site_message_error;

				redirect("index.php?cmd=cbtedit");
			}
		}
	}
	else{
		if(isset($passed_id)){
			$facebook_tool = $DB->info("cbt", "user_id = '{$UserID}' AND cbt_id = '{$passed_id}'");
		}
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

		<!-- FACEBOOK TOOLS SECTION -->
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-12">
					<div class="card">
						<div class="content card-body">
							<h4 class="text-center"><i class="fa fa-facebook-square"></i> &nbsp;Facebook Tools</h4>

							<div class="form-group">
								<label>Title</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Title for the Facebook Tool."><i class="fa fa-question" aria-hidden="true"></i></span>

								<input class="form-control" type="text" name="cbt_title" value="<?= $facebook_tool["cbt_title"]; ?>" />
							</div>

							<div class="form-group">
								<label>Facebook Pixel</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Place your Facebook Pixel code snippet here."><i class="fa fa-question" aria-hidden="true"></i></span>

								<textarea class="form-control" name="cbt_fb_pixel_code_snippet" rows="5" placeholder="Place your Facebook Pixel code snippet here..."><?= $facebook_tool["cbt_fb_pixel_code_snippet"]; ?></textarea>
							</div>

							<div class="form-group">
								<div class="row">
									<div class="col-md-6">
										<label>Facebook Comments (SDK)</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Place your Facebook Comments SDK code here."><i class="fa fa-question" aria-hidden="true"></i></span>

										<textarea class="form-control" name="cbt_fb_comments_sdk" rows="5" placeholder="Place your Facebook Comments SDK code here..."><?= $facebook_tool["cbt_fb_comments_sdk"]; ?></textarea>
									</div>

									<div class="col-md-6">
										<label>Facebook Comments (Code Snippet)</label>
										<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Place your Facebook Comments code snippet here..."><i class="fa fa-question" aria-hidden="true"></i></span>

										<textarea class="form-control" name="cbt_fb_comments_code_snippet" rows="5" placeholder="Place your Facebook Comments code snippet here..."><?= $facebook_tool["cbt_fb_comments_code_snippet"]; ?></textarea>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label>Facebook Chat</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Place your Facebook Chat SDK and code snippet here."><i class="fa fa-question" aria-hidden="true"></i></span>

								<textarea class="form-control" name="cbt_fb_chat_sdk_and_code_snippet" rows="5" placeholder="Place your Facebook Chat SDK and code snippet here..."><?= $facebook_tool["cbt_fb_chat_sdk_and_code_snippet"]; ?></textarea>
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
				<i class="fa fa-pencil-square-o"></i> &nbsp;Create Facebook Tool
			</button>
		</div>
		<?php endif; ?>
	</form>
</div>