<?php
	// CODE_SECTION_PHP_1: PRIVILEGE
	if(!preg_match(";affiliatelinks;", $cur_pack["pack_ar"])){
		// redirect("index.php?cmd=deny");
		// NOTE: COMMENTED-OUT BECAUSE affiliatelinks IS STILL NOT ADDED AS A PACKAGE.
	}

	// QUERY
	$DFYAuthorID = $WEBSITE["dfy_author"];
	// $affiliate_links_collection = $DB->query("SELECT * FROM {$dbprefix}affiliate_links_collection WHERE user_id = '{$UserID}'");

	// if(sizeof($affiliate_links_collection) == 0){
	// 	$affiliate_links_collection_dfy = $DB->query("SELECT * FROM {$dbprefix}affiliate_links_collection WHERE user_id = '{$DFYAuthorID}'");

	// 	foreach($affiliate_links_collection_dfy as $affiliate_link_collection_dfy){
	// 		$affiliate_links_collection_id = $DB->getauto("affiliate_links_collection");

	// 		$affiliate_links_collection_title = $affiliate_link_collection_dfy["affiliate_links_collection_title"];
	// 		$affiliate_links_collection_included_affiliate_link_ids = $affiliate_link_collection_dfy["affiliate_links_collection_included_affiliate_link_ids"];

	// 		$insert_affiliate_links_collection = $DB->query("INSERT INTO {$dbprefix}affiliate_links_collection SET 
	// 			affiliate_links_collection_id = '{$affiliate_links_collection_id}', 
	// 			user_id = '{$UserID}', 
	// 			affiliate_links_collection_title = '{$affiliate_links_collection_title}', 
	// 			affiliate_links_collection_included_affiliate_link_ids = '{$affiliate_links_collection_included_affiliate_link_ids}'");
	// 	}
	// }

	// DELETE PROCESS: AFFILIATE LINKS
	if(!empty($_GET["affiliate_links_collection_id"])){
		$id_to_delete = $_GET["affiliate_links_collection_id"];

		$delete_affiliate_links_collection = $DB->query("DELETE FROM {$dbprefix}affiliate_links_collection WHERE affiliate_links_collection_id = '{$id_to_delete}'");

		if($delete_affiliate_links_collection){
			$_SESSION["msg_success"] = "Affiliate Link deleted.";

			redirect("index.php?cmd=affiliatelinkscollection");
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
						<small style="padding-left: 10px;">Manage your Collections here</small>
					</div>
					<div class="p-2 mx-0">
						<a class="btn btn-outline-secondary" href="<?= "index.php?cmd=affiliatelinkscollectionedit"; ?>">Create New</a>
					</div>
					<div class="p-2 ml-0 mr-auto">
						<a class="btn btn-outline-secondary" href="index.php?cmd=affiliatelinks"><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;Back to Resource Slider Table</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="content table-responsive table-full-width">
					<table class="table table-hover table-striped" id="affiliate-links-collection-table">
						<thead>
							<tr>
								<th>Collection Title</th>
								<th class="text-center">Edit</th>
								<th class="text-center">Delete</th>
							</tr>
						</thead>
						<tbody>
						<?php $affiliate_links_collection = $DB->query("SELECT * FROM {$dbprefix}affiliate_links_collection WHERE user_id = '{$UserID}'"); ?>
						<?php foreach($affiliate_links_collection as $collection) : ?>
							<tr>
								<td><?= $collection["affiliate_links_collection_title"]; ?></td>
								<td class="text-center">
									<a class="btn btn-secondary" href="<?= "index.php?cmd=affiliatelinkscollectionedit&id={$collection["affiliate_links_collection_id"]}"; ?>">
										<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;Edit
									</a>
								</td>
								<td class="text-center">
									<button class="btn btn-secondary" data-toggle="modal" data-target="#delete-modal" 
									data-id-to-delete="<?= $collection["affiliate_links_collection_id"]; ?>" 
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
			<div class="modal-body text-center">Are you sure you want to <b>DELETE</b> this affiliate link: collection?</div>
			<div class="modal-footer">
				<a class="btn btn-danger" id="delete-button" href="" data-dismiss="modal">Yes I am sure</a>
			</div>
		</div>
	</div>
</div>

<!-- DATATABLE INITIALIZATION: AFFILIATE LINKS -->
<script type="text/javascript">
	$(document).ready(function(){
		$('#affiliate-links-collection-table').DataTable();
	});
</script>

<!-- DELETE PROCESS: AFFILIATE LINKS -->
<script type="text/javascript">
	function getAttributes(attributes){
		// DELETE CONFIRMATION
		var id_to_delete = attributes.getAttribute("data-id-to-delete");
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=affiliatelinkscollection&affiliate_links_collection_id=${id_to_delete}`;
	}
</script>