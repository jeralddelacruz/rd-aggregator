<?php
	if(!preg_match(";exp;", $cur_pack["pack_ar"])){
		redirect("index.php?cmd=deny");
	}

	$exit_pop_ups = $DB->query("SELECT * FROM {$dbprefix}exp WHERE user_id = '{$UserID}'");

	// DELETE A FACEBOOK TOOL
	if(isset($_GET["delete"])){
		$id_to_delete = $_GET["delete"];

		$delete = $DB->query("DELETE FROM {$dbprefix}exp WHERE exp_id = '{$id_to_delete}'");

		if($delete){
			$_SESSION["msg_success"] = "Facebook Tool deleted.";

			redirect("index.php?cmd=exp");
		}
	}
?>
<!-- DATATABLE CDN -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.js"></script>

<!-- CUSTOMIZED THE DATATABLE'S PAGINATION BUTTON COLOR SCHEME -->
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

<div class="container-fluid">
	<!-- DISPLAY SUCCESS OR ERROR -->
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
			<div class="header card-header">
				<div class="d-flex flex-row justify-content-between align-items-center">
					<div class="p-2">
						<h4 style="padding: 10px;"><?= $index_title; ?></h4>
						<small style="padding-left: 10px;">Manage your Exit Pop-Ups here</small>
					</div>
					<div class="p-2">
						<a class="btn btn-outline-secondary" href="index.php?cmd=expedit">Create New</a>
					</div>
					<div class="p-2 ml-auto">
						<div class="btn-group flex-wrap">
							<a class="btn btn-outline-secondary <?= strpos($_SERVER["REQUEST_URI"], "cbt") ? "active" : ""; ?>" href="index.php?cmd=cbt">Facebook Tools</a>
							<a class="btn btn-outline-secondary <?= strpos($_SERVER["REQUEST_URI"], "exp") ? "active" : ""; ?>" href="index.php?cmd=exp">Exit Pop-Ups</a>
							<!-- SOCIAL PROOF UN-COMMENTED-OUT -->
							<a class="btn btn-outline-secondary <?= strpos($_SERVER["REQUEST_URI"], "scp") ? "active" : ""; ?>" href="index.php?cmd=scp">Social Proof Pop-Ups</a>
						</div>
					</div>
				</div>
			</div>
			<div class="content card-body table-responsive table-full-width" style="padding: 10px;">
				<table class="table table-hover table-striped" id="exit-pop-ups">
					<thead>
						<tr>
							<th>ID</th>
							<th>Title</th>
							<th class="text-center">Type</th>
							<th class="text-center">Headline</th>
							<th class="text-center">Sub Headline</th>
							<th class="text-center">Edit</th>
							<th class="text-center">Delete</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($exit_pop_ups as $exit_pop_up) : ?>
						<tr>
							<td><?= $exit_pop_up["exp_id"]; ?></td>
							<td><?= $exit_pop_up["exp_title"]; ?></td>
							<td class="text-center"><?= $exit_pop_up["exp_type"]; ?></td>
							<td class="text-center"><?= $exit_pop_up["exp_headline"]; ?></td>
							<td class="text-center"><?= $exit_pop_up["exp_sub_headline"]; ?></td>
							<td class="text-center">
								<a class="btn btn-secondary" href="<?= "index.php?cmd=expedit&id=" . $exit_pop_up["exp_id"]; ?>">
									<i class="fa fa-pencil-square-o"></i> &nbsp;Edit
								</a>
							</td>
							<td class="text-center">
								<a class="btn btn-danger" href="index.php?cmd=exp&delete=<?= $exit_pop_up["exp_id"]; ?>">
									<i class="fa fa-times"></i> &nbsp;Delete
								</a>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- DATATABLE SCRIPT -->
<script type="text/javascript">
	$(document).ready(function(){
		$('#exit-pop-ups').DataTable();
	});
</script>