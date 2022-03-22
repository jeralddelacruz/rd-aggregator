<?php
	// CODE_SECTION_PHP_1: PRIVILEGE
	if(!preg_match(";affiliatelinks;", $cur_pack["pack_ar"])){
		// redirect("index.php?cmd=deny");
		// NOTE: COMMENTED-OUT BECAUSE affiliatelinks IS STILL NOT ADDED AS A PACKAGE.
	}

	if($_POST["submit"]){
		$passed_id = $_POST["passed_id"];
		$affiliate_links_link_user = ($_POST["affiliate_links_link_user"]) ? $_POST["affiliate_links_link_user"] : "#";

		$update_affiliate_links = $DB->query("UPDATE {$dbprefix}affiliate_links SET 
			affiliate_links_link_user = '{$affiliate_links_link_user}'
			WHERE affiliate_links_id = '{$passed_id}'");
	}

	// DELETE PROCESS: AFFILIATE LINKS
	if(!empty($_GET["affiliate_links_id"])){
		$affiliate_links_id = $_GET["affiliate_links_id"];

		$delete_affiliate_link = $DB->query("DELETE FROM {$dbprefix}affiliate_links WHERE affiliate_links_id = '{$affiliate_links_id}'");

		if($delete_affiliate_link){
			$_SESSION["msg_success"] = "Affiliate Link deleted.";

			redirect("index.php?cmd=affiliatelinks");
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
						<small style="padding-left: 10px;">Manage your Resource Slider here</small>
					</div>
					<div class="p-2 mr-0">
						<a class="btn btn-outline-secondary" href="<?= "index.php?cmd=affiliatelinksedit"; ?>">Create New</a>
					</div>
					<!--<div class="p-2 mr-auto">-->
					<!--	<a class="btn btn-outline-secondary" href="<?= "index.php?cmd=affiliatelinkscollection"; ?>">Manage your Collection</a>-->
					<!--</div>-->
				</div>
			</div>
			<div class="card-body">
				<div class="content table-responsive table-full-width">
					<table class="table table-hover table-striped" id="affiliate-links-table">
						<thead>
							<tr>
								<th>Product Name</th>
								<th>Apply Link</th>
								<th>Affiliate Link</th>
								<th class="text-center">Edit</th>
								<th class="text-center">Delete</th>
							</tr>
						</thead>
						<tbody id="affiliate-links-table-body">
						<?php $affiliate_links = $DB->query("SELECT * FROM {$dbprefix}affiliate_links WHERE user_id = '{$UserID}'"); ?>
						<?php foreach($affiliate_links as $affiliate_link) : ?>
							<tr>
								<td><?= $affiliate_link["affiliate_links_product_name"]; ?></td>
								<td>
									<div class="input-group">
										<input class="form-control" type="text" value="<?= $affiliate_link["affiliate_links_link"]; ?>" />
										<div class="input-group-append">
											<button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
											<div class="dropdown-menu">
												<a class="dropdown-item" href="#" onclick="checkEvent(this)" data-action-type="copy" data-copy="<?= $affiliate_link["affiliate_links_link"]; ?>"><i class="fa fa-copy"></i>&nbsp;&nbsp;Copy Link</a>
												<div role="separator" class="dropdown-divider"></div>
												<a class="dropdown-item" href="<?= $affiliate_link["affiliate_links_link"]; ?>" target="_blank"><i class="fa fa-external-link"></i>&nbsp;&nbsp;Go to Link</a>
											</div>
										</div>
									</div>
								</td>
								<td>
									<form method="POST" enctype="multipart/form-data">
									<div class="input-group">
										<input class="form-control" type="url" name="affiliate_links_link_user" value="<?= $affiliate_link["affiliate_links_link_user"]; ?>" />
										<input type="hidden" name="passed_id" value="<?= $affiliate_link["affiliate_links_id"]; ?>" />
										<div class="input-group-append">
											<button class="btn btn-secondary btn-block" type="submit" name="submit" value="submit">
												<i class="fa fa-save"></i>
											</button>
										</div>
									</div>
									</form>
								</td>
								<td class="text-center">
									<a class="btn btn-secondary" href="<?= "index.php?cmd=affiliatelinksedit&id={$affiliate_link["affiliate_links_id"]}"; ?>">
										<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;Edit
									</a>
								</td>
								<td class="text-center">
									<button class="btn btn-secondary" data-toggle="modal" data-target="#delete-modal" 
									data-id-to-delete="<?= $affiliate_link["affiliate_links_id"]; ?>" 
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

<!-- MODALS SECTION: AFFILIATE LINKS -->
<!-- MODAL ACTION: DELETE -->
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

<!-- DATATABLE INITIALIZATION: AFFILIATE LINKS -->
<script type="text/javascript">
	$(document).ready(function(){
		$('#affiliate-links-table').DataTable();
	});
</script>

<!-- DELETE PROCESS: AFFILIATE LINKS -->
<script type="text/javascript">
	function getAttributes(attributes){
		// DELETE CONFIRMATION
		var id_to_delete = attributes.getAttribute("data-id-to-delete");
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=affiliatelinks&affiliate_links_id=${id_to_delete}`;
	}
</script>

<!-- CHECK EVENT FUNCTION -->
<script type="text/javascript">
	function checkEvent(element){
		event.preventDefault();

		var action_type = element.getAttribute("data-action-type");

		if(action_type == "copy"){
			var dataToCopy = element.getAttribute("data-copy");
			var createElem = document.createElement("textarea");
			createElem.style.position = "absolute";
			createElem.style.top = "100px";
			createElem.value = dataToCopy;
			document.body.appendChild(createElem);


			createElem.select();
			document.execCommand("copy");
		}
	}
</script>