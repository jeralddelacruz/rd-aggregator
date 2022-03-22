<?php
	if(!preg_match(";fbp;", $cur_pack["pack_ar"])){
		redirect("index.php?cmd=deny");
	}

	$facebook_tools = $DB->query("SELECT * FROM {$dbprefix}cbt WHERE user_id = '{$UserID}'");

	// DELETE A FACEBOOK TOOL
	if(isset($_GET["delete"])){
		$id_to_delete = $_GET["delete"];

		$delete = $DB->query("DELETE FROM {$dbprefix}cbt WHERE cbt_id = '{$id_to_delete}'");

		if($delete){
			$_SESSION["msg_success"] = "Facebook Tool deleted.";

			redirect("index.php?cmd=cbt");
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
						<small style="padding-left: 10px;">Manage your Facebook Tools here</small>
					</div>
					<div class="p-2">
						<a class="btn btn-outline-secondary" href="index.php?cmd=cbtedit">Create New</a>
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
				<table class="table table-hover table-striped" id="facebook-tools">
					<thead>
						<tr>
							<th>ID</th>
							<th>Title</th>
							<th class="text-center">FB Pixel</th>
							<th class="text-center">FB Comment</th>
							<th class="text-center">FB Chat</th>
							<th class="text-center">Edit</th>
							<th class="text-center">Delete</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($facebook_tools as $facebook_tool) : ?>
						<tr>
							<td><?= $facebook_tool["cbt_id"]; ?></td>
							<td><?= $facebook_tool["cbt_title"]; ?></td>
							<td class="text-center">
								<?= $facebook_tool["cbt_fb_pixel_code_snippet"] ? "<i class=\"fa fa-check\"></i>" : "<i class=\"fa fa-times\"></i>"; ?>
							</td>
							<td class="text-center">
								<?php if($facebook_tool["cbt_fb_comments_sdk"] && $facebook_tool["cbt_fb_comments_code_snippet"]) : ?>
								<i class="fa fa-check"></i>
								<?php elseif($facebook_tool["cbt_fb_comments_sdk"] && !$facebook_tool["cbt_fb_comments_code_snippet"]) : ?>
								No code snippet
								<?php elseif(!$facebook_tool["cbt_fb_comments_sdk"] && $facebook_tool["cbt_fb_comments_code_snippet"]) : ?>
								No SDK
								<?php else : ?>
								<i class="fa fa-times"></i>
								<?php endif; ?>
							</td>
							<td class="text-center">
								<?= $facebook_tool["cbt_fb_chat_sdk_and_code_snippet"] ? "<i class=\"fa fa-check\"></i>" : "<i class=\"fa fa-times\"></i>"; ?>
							</td>
							<td class="text-center">
								<a class="btn btn-secondary" href="<?= "index.php?cmd=cbtedit&id=" . $facebook_tool["cbt_id"]; ?>">
									<i class="fa fa-pencil-square-o"></i> &nbsp;Edit
								</a>
							</td>
							<td class="text-center">
								<a class="btn btn-danger" href="index.php?cmd=cbt&delete=<?= $facebook_tool["cbt_id"]; ?>">
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
		$('#facebook-tools').DataTable();
	});
</script>