<?php include('../../cache_solution/top-cache-v2.php'); ?>
<?php
	// CODE_SECTION_PHP_1: PRIVILEGE
	if(!preg_match(";category;", $cur_pack["pack_ar"])){
		// redirect("index.php?cmd=deny");
		// NOTE: COMMENTED-OUT BECAUSE category IS STILL NOT ADDED AS A PACKAGE.
	}

	// CODE_SECTION_PHP_2: VARIABLE_INITIALIZATION
	$passed_id = $_GET["id"];
	$category = $DB->info("category", "category_id = '{$passed_id}' AND user_id = '{$UserID}'");
	$affiliate_links_collection = $DB->query("SELECT * FROM {$dbprefix}affiliate_links_collection WHERE user_id = '{$UserID}'");
	
	$subdomain_id = "";
	if( $user_subdomain ){
	    $subdomain_id = $user_subdomain["subdomain_id"];
	}else{
	    $subdomain_id = "0";
	}

	// CODE_SECTION_PHP_3: BACKEND PROCESS
	if($_POST["submit"]){
		// POST VARIABLES
		$remove[] = "'";
		$remove[] = '"';
		$remove[] = "-";

		// STRIP 1
		$cs_stripped_1 = str_replace($remove, "", $_POST["category_name"]);
		$category_name = $cs_stripped_1;
		
		// STRIP 1
		$category_descr = str_replace($remove, "", $_POST["category_desc"]);
		$category_desc = $category_descr;
		
		// IF $passed_id HAS A VALUE
		if(empty($passed_id)){
			$category_id = $DB->getauto("category");
			$insert_category = $DB->query("INSERT INTO {$dbprefix}category SET 
				category_id = '{$category_id}', 
				user_id = '{$UserID}', 
				subdomain_id= '{$subdomain_id}',
				category_name = '{$category_name}', 
				category_desc = '{$category_desc}'
            ");

			if($insert_category){
				$_SESSION["msg_success"] = "Category creation successful.";

				redirect("index.php?cmd=category");
			}
			else{
				$_SESSION["msg_error"] = "Category creation failure.";
			}
		}
		else{
			$update_category = $DB->query("UPDATE {$dbprefix}category SET 
				category_name = '{$category_name}', 
				category_desc = '{$category_desc}'
			    WHERE category_id = '{$passed_id}' AND user_id = '{$UserID}'");

			if($update_category){
				$_SESSION["msg_success"] = "Category update successful.";

				redirect("index.php?cmd=category");
			}
			else{
				$_SESSION["msg_error"] = "Category update failure.";
			}
		}
	}

?>
<style type="text/css">
	.img-overlay-custom{
		position: absolute;
		top: 0px; left: 0px;

		width: 100%; height: 100%;

		display: block;

		box-sizing: border-box;

		background-color: rgba(0, 0, 0, 0);

		transition: background-color .2s;
	}

	.img-overlay-custom a{
		opacity: 0;

		transition: opacity .2s;
	}

	.img-overlay-custom:hover{
		background-color: rgba(0, 0, 0, .4);
	}

	.img-overlay-custom:hover a{
		opacity: 1;
	}

	#checkbox-container{
		height: 100px;
		overflow: auto;
		border: 1px solid gainsboro;
	}
</style>

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