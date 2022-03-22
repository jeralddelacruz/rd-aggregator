<?php
	// CODE_SECTION_PHP_1: PRIVILEGE
	if(!preg_match(";ads;", $cur_pack["pack_ar"])){
		// redirect("index.php?cmd=deny");
		// NOTE: COMMENTED-OUT BECAUSE ads IS STILL NOT ADDED AS A PACKAGE.
	}

	// CODE_SECTION_PHP_3: DELETE_TO_DATABASE
	if(!empty($_GET["ads_id"])){
		$ads_id = $_GET["ads_id"];

		$delete_ads = $DB->query("DELETE FROM {$dbprefix}ads2 WHERE ads_id = '{$ads_id}'");

		if($delete_ads){
			$_SESSION["msg_success"] = "Ads deleted.";

			redirect("index.php?cmd=ads2");
		}
	}
	
	$and_query = "";
	if( $user_subdomain ){
	    $subdomain_id = $user_subdomain["subdomain_id"];
	    $and_query = " AND subdomain_id = '{$subdomain_id}'";
	}else{
	    $and_query = " AND subdomain_id = 0";
	}
?>
<!-- CODE_SECTION_HTML_1: CDN_DATATABLE -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.js"></script>

<!-- CODE_SECTION_HTML_2: CSS_EMBEDDED_DATATABLE -->
<style type="text/css">
	.ads-item .ads-link{
		color: #666;
	}

	.ads-item.active .ads-link{
		background-color: #6c757d;
		border-color: #6c757d;
		color: #fff;
	}
	
	.sorting_1 img{
	    max-width: 100px;
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
					<div class="p-2">
                        <a class="btn btn-outline-secondary" href="index.php?cmd=ads2edit">Create New</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="content table-responsive table-full-width">
					<table class="table table-hover table-striped" id="ads-table">
						<thead>
							<tr>
								<th style="width: 250px;">Ads Image</th>
								<th>Ads Name</th>
								<th>Ads Type</th>
								<th class="text-center">Edit</th>
								<th class="text-center">Delete</th>
							</tr>
						</thead>
						<tbody>
						<?php $ads = $DB->query("SELECT * FROM {$dbprefix}ads2 WHERE user_id = '{$UserID}' {$and_query}"); ?>
						<?php foreach($ads as $ad) : ?>
							<tr>
								<td><img src="../upload/<?= $UserID; ?>/<?= $ad["ads_image"]; ?>" alt="<?= $ad["ads_name"]; ?>"></td>
								<td><?= $ad["ads_name"]; ?></td>
								<td><?= $ad["ads_type"]; ?></td>
								<td class="text-center">
									<a href="index.php?cmd=ads2edit&id=<?= $ad["ads_id"]; ?>" class="btn btn-secondary">
										<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;Edit
									</a>
								</td>
								<td class="text-center">
									<button class="btn btn-secondary" data-toggle="modal" data-target="#delete-modal" 
									data-ads-id="<?= $ad["ads_id"]; ?>" 
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

<!-- DATATABLE INITIALIZATION -->
<script type="text/javascript">
	$(document).ready(function(){
		$('#ads-table').DataTable();
	});
</script>

<!-- DISPLAY data- ATTRIBUTES IN THE MODAL -->
<script type="text/javascript">
	function getAttributes(attributes){
		// DELETE CONFIRMATION
		var id_to_delete = attributes.getAttribute("data-ads-id");
        console.log(id_to_delete)
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=ads2&ads_id=${id_to_delete}`;
	}
</script>