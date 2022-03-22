<?php
	// CODE_SECTION_PHP_1: PRIVILEGE
	if(!preg_match(";campaignsdfy2;", $cur_pack["pack_ar"])){
		// redirect("index.php?cmd=deny");
		// NOTE: COMMENTED-OUT BECAUSE pages IS STILL NOT ADDED AS A PACKAGE.
	}

	// CODE_SECTION_PHP_2: VARIABLE_INITIALIZATION
	$DFYAuthorID = $WEBSITE["dfy_author"];
	$campaigns_type = "dfy2";
	$campaigns = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = '{$UserID}' AND campaigns_type = '{$campaigns_type}'");

	if(sizeof($campaigns) == 0){
		$campaigns_dfy = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = '{$DFYAuthorID}' AND campaigns_type = '{$campaigns_type}'");

		foreach($campaigns_dfy as $campaign_dfy){
			$campaigns_id = $DB->getauto("campaigns");

			$campaigns_title = $campaign_dfy["campaigns_title"];
			$campaigns_type = $campaigns_type;
			$campaigns_theme_color = $campaign_dfy["campaigns_theme_color"];
			$campaigns_theme_font = $campaign_dfy["campaigns_theme_font"];
			$campaigns_logo = $campaign_dfy["campaigns_logo"];
			$campaigns_headline = $campaign_dfy["campaigns_headline"];
			$campaigns_headline_alignment = $campaign_dfy["campaigns_headline_alignment"];
			$campaigns_body = $campaign_dfy["campaigns_body"];
			$campaigns_body_alignment = $campaign_dfy["campaigns_body_alignment"];
			$campaigns_background_image = $campaign_dfy["campaigns_background_image"];
			$campaigns_button_text = $campaign_dfy["campaigns_button_text"];
			$campaigns_button_url = $campaign_dfy["campaigns_button_url"];
			$included_article_pages_ids = implode(", ", $campaign_dfy["included_article_pages_ids"]);
			$included_webinar_page_id = $campaign_dfy["included_webinar_page_id"];

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

			// COPY DONE FOR YOU CAMPAIGN IMAGES TO NEW USER
			$dfy_upload_directory = "../upload/{$DFYAuthorID}/";
			$user_upload_directory = "../upload/{$UserID}/";

			$copy_from = $dfy_upload_directory . $campaigns_logo;
			$copy_to = $user_upload_directory . $campaigns_logo;
			if(copy($copy_from, $copy_to)){
				$update_campaign_logo = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_logo = '{$campaigns_logo}' WHERE campaigns_id = '{$campaigns_id}'");
			}

			$copy_from_2 = $dfy_upload_directory . $campaigns_background_image;
			$copy_to_2 = $user_upload_directory . $campaigns_background_image;
			if(copy($copy_from_2, $copy_to_2)){
				$update_campaigns_background_image = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_background_image = '{$campaigns_background_image}' WHERE campaigns_id = '{$campaigns_id}'");
			}

			// $copy_from_speech_bubble_avatar = $dfy_upload_directory . $campaign_speech_bubble_avatar;
			// $copy_to_speech_bubble_avatar = $user_upload_directory . $campaign_speech_bubble_avatar;
			// if(copy($copy_from_speech_bubble_avatar, $copy_to_speech_bubble_avatar)){
			// 	$update_speech_bubble_avatar = $DB->query("UPDATE {$dbprefix}pageb SET pageb_campaign_speech_bubble_avatar = '{$campaign_speech_bubble_avatar}' WHERE pageb_id = '{$campaign_id}' AND user_id = '{$UserID}'");
			// }
		}
	}

	// CODE_SECTION_PHP_3: DELETE_TO_DATABASE
	if(!empty($_GET["campaigns_id"])){
		$campaigns_id = $_GET["campaigns_id"];

		$delete_campaign = $DB->query("DELETE FROM {$dbprefix}campaigns WHERE campaigns_id = '{$campaigns_id}'");

		if($delete_campaign){
			$_SESSION["msg_success"] = "Campaign deleted.";

			redirect("index.php?cmd=campaignsdfy2");
		}
	}
?>
<!-- CODE_SECTION_HTML_1: CDN_DATATABLE -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.js"></script>

<!-- CODE_SECTION_HTML_2: CSS_EMBEDDED_DATATABLE -->
<style type="text/css">
	.page-item .page-link{
		color: #666;
	}

	.page-item.active .page-link{
		background-color: #6c757d;
		border-color: #6c757d;
		color: #fff;
	}
</style>

<!-- CODE_SECTION_HTML_3: CONTENT_MAIN -->
<div class="container-fluid">
	<!-- CODE_SECTION_PHP_HTML_1: SUCCESS_AND_ERROR_ALERT -->
	<?php if($_SESSION["msg_success"]) : ?>
	<div class="col-md-12">
		<div class="alert alert-success"><?php echo $_SESSION["msg_success"]; $_SESSION["msg_success"] = ""; ?></div>
	</div>
	<?php endif; ?>

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
						<small style="padding-left: 10px;">Manage your Campaigns here</small>
					</div>
					<?php if($UserID == $DFYAuthorID) : ?>
					<div class="p-2 mr-auto">
						<a href="index.php?cmd=campaignsdfy2edit" class="btn btn-outline-secondary">Create New</a>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="card-body">
				<div class="content table-responsive table-full-width">
					<table class="table table-hover table-striped" id="pages-table">
						<thead>
							<tr>
								<th>Campaign Title</th>
								<th class="text-center">Edit</th>
								<th class="text-center">Publish</th>
								<?php if($UserID == $DFYAuthorID) : ?>
								<th class="text-center">Delete</th>
								<?php endif; ?>
							</tr>
						</thead>
						<tbody>
						<?php foreach($campaigns as $campaign) : ?>
							<tr>
								<td><?= $campaign["campaigns_title"]; ?></td>
								<td class="text-center">
									<a href="index.php?cmd=campaignsdfy2edit&id=<?= $campaign["campaigns_id"]; ?>" class="btn btn-secondary">
										<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;Edit
									</a>
								</td>
								<td class="text-center">
									<button class="btn btn-secondary" data-toggle="modal" data-target="#publish-modal" 
									data-campaigns-title="<?= $campaign["campaigns_title"]; ?>" 
									data-campaigns-url="<?= $SCRIPTURL . "add/pages.php?campaigns_id={$campaign["campaigns_id"]}"; ?>" 
									onclick="getAttributes(this)">
										<i class="fa fa-file-text-o"></i>&nbsp;&nbsp;Publish
									</button>
								</td>
								<?php if($UserID == $DFYAuthorID) : ?>
								<td class="text-center">
									<button class="btn btn-secondary" data-toggle="modal" data-target="#delete-modal" 
									data-campaigns-id="<?= $campaign["campaigns_id"]; ?>" 
									onclick="getAttributes(this)" type="button">
										<i class="fa fa-times"></i>&nbsp;&nbsp;Delete
									</button>
								</td>
								<?php endif; ?>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="card-footer text-center">This is the end of the table.</div>
		</div>
	</div>
</div>

<!-- CODE_SECTION_HTML_4: MODALS -->
<!-- PUBLISH MODAL -->
<div class="modal fade" id="publish-modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modal-title">Publish</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-3">
						<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
							<a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-view-page" role="tab" aria-controls="v-pills-view-page" aria-selected="true">View Page</a>
							<a class="nav-link" id="v-pills-embed-page-tab" data-toggle="pill" href="#v-pills-embed-page" role="tab" aria-controls="v-pills-embed-page" aria-selected="false">Embed Page</a>
							<a class="nav-link" id="v-pills-download-page-tab" data-toggle="pill" href="#v-pills-download-page" role="tab" aria-controls="v-pills-download-page" aria-selected="false">Download Page</a>
						</div>
					</div>
					<div class="col-9">
						<div class="tab-content" id="v-pills-tabContent">
							<div class="tab-pane fade show active" id="v-pills-view-page" role="tabpanel" aria-labelledby="v-pills-view-page-tab">
								<h4>View</h4>
								<div class="input-group mb-4">
									<input class="form-control" id="view-url-input" type="text" name="pages_input_view" readonly />
									<div class="input-group-append">
										<button class="btn btn-outline-secondary" type="button" onclick="copyData(this)">Copy</button>
									</div>
									<div class="input-group-append">
										<a class="btn btn-outline-secondary" id="view-url-button" href="" target="_blank">View</a>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="v-pills-embed-page" role="tabpanel" aria-labelledby="v-pills-embed-page-tab">
								<h4>Embed</h4>
								<div class="input-group mb-4">
									<input class="form-control" id="embed-url-input" type="text" readonly />
									<div class="input-group-append">
										<button class="btn btn-outline-secondary" type="button" onclick="copyData(this)">Copy</button>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="v-pills-download-page" role="tabpanel" aria-labelledby="v-pills-download-page-tab">
								<h4>Download</h4>
								<div class="input-group mb-4">
									<input class="form-control" type="text" id="download-url-input" readonly />
									<div class="input-group-append">
										<a class="btn btn-outline-secondary" id="download-url-button" href="" target="_blank" download>Download</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer"></div>
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

<!-- DATATABLE INITIALIZATION -->
<script type="text/javascript">
	$(document).ready(function(){
		$('#pages-table').DataTable();
	});
</script>

<!-- DISPLAY data- ATTRIBUTES IN THE MODAL -->
<script type="text/javascript">
	function getAttributes(attributes){
		// ATTRIBUTES FROM PHP FOREACH
		var campaigns_title = attributes.getAttribute("data-campaigns-title");
		var campaigns_url = attributes.getAttribute("data-campaigns-url");

		// MODAL TITLE
		var campaigns_modal_title = document.getElementById("modal-title");
		campaigns_modal_title.innerHTML = campaigns_title;

		// VIEW
		var view_url_input = document.getElementById("view-url-input");
		var view_url_button = document.getElementById("view-url-button");
		view_url_input.value = campaigns_url;
		view_url_button.href = campaigns_url;

		// EMBED
		var embed_url_input = document.getElementById("embed-url-input");
		embed_url_input.value = "<iframe src=\"" + campaigns_url + "\" style=\"width: 100%; height: 100%; border: none;\"></iframe>";

		// DOWNLOAD
		var download_url_input = document.getElementById("download-url-input");
		var download_url_button = document.getElementById("download-url-button");
		download_url_input.value = campaigns_url;
		download_url_button.href = campaigns_url;

		// DELETE CONFIRMATION
		var id_to_delete = attributes.getAttribute("data-campaigns-id");
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=campaignsdfy2&campaigns_id=${id_to_delete}`;
	}
</script>

<!-- COPY FUNCTION IN THE MODAL -->
<script type="text/javascript">
	function copyData(clipboardText){
		var copyThis = clipboardText.parentNode.previousElementSibling;
		
		copyThis.select();
		document.execCommand("copy");
	}
</script>