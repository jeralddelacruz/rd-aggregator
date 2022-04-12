
<?php
	hasPageAccess("campaigns", $cur_pack["pack_ar"]);

	include("queries/categoryedit_func.php");
?>
<link rel="stylesheet" href="../inc/user/Style/categoryedit_style.css">

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
    							<a class="btn btn-outline-secondary" href="index.php?cmd=category"><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;Back to category Table</a>
    						</div>
    						<?php if(!empty($passed_id)) : ?>
        						<div class="p-2">
        							<button class="btn btn-outline-secondary" data-toggle="modal" data-target="#delete-modal" 
        							data-category-id="<?= $category["category_id"]; ?>" 
        							onclick="getAttributes(this)" type="button">Delete this category</button>
        						</div>
    						<?php endif; ?>
    					</div>
					</div>
				</div>
				<div class="card-body">
					<!-- Category TITLE AND TYPE -->
					<div class="form-group">
						<div class="row">
                            <div class="col-md-12">
								<label>Category Name</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The name of your category."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="category_name" value="<?= $category["category_name"]; ?>" maxlength="100" required/>
							</div>
						</div>
					</div>
					<!-- Category DESC -->
					<div class="form-group">
						<div class="row">
                            <div class="col-md-12">
								<label>Category Desc</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The description of your category."><i class="fa fa-question"></i></span>
                                <textarea class="form-control" name="category_desc" rows="2" placeholder="Insert Description here."><?= $category["category_desc"]; ?></textarea>
								<!--<input class="form-control" type="text" name="category_desc" value="<?= $category["category_desc"]; ?>" maxlength="100" required/>-->
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
							<a class="btn btn-outline-secondary btn-block" href="index.php?cmd=category">
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
			<div class="modal-body text-center">Are you sure you want to <b>DELETE</b> this category?</div>
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
		var id_to_delete = attributes.getAttribute("data-category-id");
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=category&category_id=${id_to_delete}`;
	}
</script>
<?php include('../../cache_solution/bottom-cache-v2.php'); ?>