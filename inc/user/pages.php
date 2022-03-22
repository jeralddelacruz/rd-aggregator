<?php
	// CODE_SECTION_PHP_1: PRIVILEGE
	if(!preg_match(";pages;", $cur_pack["pack_ar"])){
		// redirect("index.php?cmd=deny");
		// NOTE: COMMENTED-OUT BECAUSE pages IS STILL NOT ADDED AS A PACKAGE.
	}

	// CODE_SECTION_PHP_3: DELETE_TO_DATABASE
	if(!empty($_GET["pages_id"])){
		$pages_id = $_GET["pages_id"];

		$delete_page = $DB->query("DELETE FROM {$dbprefix}pages WHERE pages_id = '{$pages_id}'");

		if($delete_page){
			$_SESSION["msg_success"] = "Page deleted.";

			redirect("index.php?cmd=pages");
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
						<small style="padding-left: 10px;">Manage your Ads here</small>
					</div>
					<div class="p-2 mr-auto">
						<button class="btn btn-outline-secondary" data-toggle="modal" data-target="#pages-type-modal">Create New</button>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="content table-responsive table-full-width">
					<table class="table table-hover table-striped" id="pages-table">
						<thead>
							<tr>
								<th>Page Name</th>
								<th class="text-center">Page Type</th>
								<th class="text-center">Edit</th>
								<th class="text-center">Publish</th>
								<th class="text-center">Delete</th>
							</tr>
						</thead>
						<tbody>
						<?php $pages = $DB->query("SELECT * FROM {$dbprefix}pages WHERE user_id = '{$UserID}'"); ?>
						<?php foreach($pages as $page) : 
						    // CHECK IF WHAT CAMPAIGN INCLUDED THE PAGES
						    $page_type = $page["pages_type"];
						    $campaigns = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = '{$UserID}' ORDER BY campaigns_created_at ASC");
						    $campaign_id = 0;
						    foreach($campaigns as $campaign){
						        
						        switch( $page_type ){
						            case 'article':
						                $decoded_articles_id = json_decode( $campaign['included_article_pages_ids'] );
						                if(in_array($page["pages_id"], $decoded_articles_id)){
						                    $campaign_id = $campaign['campaigns_id'];
						                }
						                break;
						            case 'c2a':
						                if( $page["pages_id"] == $campaign['included_c2a_id'] ){
						                    $campaign_id = $campaign['campaigns_id'];
						                }
						                break;
						            case 'ads':
						                if( $page["pages_id"] == $campaign['included_ads_id'] ){
						                    $campaign_id = $campaign['campaigns_id'];
						                }
						                break;
						            case 'webinar':
						                if( $page["pages_id"] == $campaign['included_webinar_page_id'] ){
						                    $campaign_id = $campaign['campaigns_id'];
						                }
						                break;
						            default:
						        }
						    }
						?>
							<tr>
								<td><?= $page["pages_name"]; ?></td>
								<td class="text-center text-capitalize"><?= $page["pages_type"]; ?></td>
								<td class="text-center">
									<a href="index.php?cmd=pagesedit&id=<?= $page["pages_id"]; ?>" class="btn btn-secondary">
										<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;Edit
									</a>
								</td>
								<td class="text-center">
									<button class="btn btn-secondary" data-toggle="modal" data-target="#publish-modal" 
									data-pages-title="<?= $page["pages_title"]; ?>" 
									data-pages-url="<?= $page["pages_type"] != 'webinar' ? $SCRIPTURL . "add/article-preview.php?article={$page['pages_id']}&user_id={$UserID}&page_type={$page['pages_type']}" : $SCRIPTURL . "add/webinar-preview.php?pages_id={$page['pages_id']}&pages_type={$page['pages_type']}"; ?>" 
									onclick="getAttributes(this)">
										<i class="fa fa-file-text-o"></i>&nbsp;&nbsp;Publish
									</button>
								</td>
								<td class="text-center">
									<button class="btn btn-secondary" data-toggle="modal" data-target="#delete-modal" 
									data-pages-id="<?= $page["pages_id"]; ?>" 
									onclick="getAttributes(this)" type="button">
										<i class="fa fa-times"></i>&nbsp;&nbsp;Delete
									</button>
								</td>
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
<!-- PAGE TYPE SELECTION MODAL -->
<div class="modal fade" id="pages-type-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">What kind of page you want to create?</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body text-center">
				<div class="btn-group">
					<a class="btn btn-outline-secondary" href="index.php?cmd=pagesedit&pages_type=article">An Article Page</a>
					<a class="btn btn-outline-secondary" href="index.php?cmd=pagesedit&pages_type=c2a">Call to Action</a>
					<a class="btn btn-outline-secondary" href="index.php?cmd=pagesedit&pages_type=ads">Ads</a>
					<a class="btn btn-outline-secondary" href="index.php?cmd=pagesedit&pages_type=webinar">A Webinar Page</a>
				</div>
			</div>
			<div class="modal-footer"></div>
		</div>
	</div>
</div>

<!-- PUBLISH MODAL -->
<div class="modal fade" id="publish-modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="pages-modal-title">Publish Page</h4>
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
							    <!--<p class="text-muted">Note: If you view this page, this will be displayed to the included latest campaign</p>-->
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
		var pages_title = attributes.getAttribute("data-pages-title");
		var pages_url = attributes.getAttribute("data-pages-url");

		// MODAL TITLE
		var pages_modal_title = document.getElementById("pages-modal-title");
		pages_modal_title.innerHTML = pages_title;

		// VIEW
		var view_url_input = document.getElementById("view-url-input");
		var view_url_button = document.getElementById("view-url-button");
		view_url_input.value = pages_url;
		view_url_button.href = pages_url;

		// EMBED
		var embed_url_input = document.getElementById("embed-url-input");
		embed_url_input.value = "<iframe src=\"" + pages_url + "\" style=\"width: 100%; height: 100%; border: none;\"></iframe>";

		// DOWNLOAD
		var download_url_input = document.getElementById("download-url-input");
		var download_url_button = document.getElementById("download-url-button");
		download_url_input.value = pages_url;
		download_url_button.href = pages_url;

		// DELETE CONFIRMATION
		var id_to_delete = attributes.getAttribute("data-pages-id");
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=pages&pages_id=${id_to_delete}`;
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