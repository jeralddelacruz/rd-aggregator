<?php
	// PRIVILEGE
	if(!preg_match(";campaigns;", $cur_pack["pack_ar"])){
		// redirect("index.php?cmd=deny");
	}

	// GET CURRENT FILE NAME
	$current_file = pathinfo(__FILE__);

	if(strpos($current_file["filename"], "edit")){
		$filename = str_replace("edit", "", $current_file["filename"]);
	}
	else{
		$filename = $current_file["filename"];
	}

	// var_dump($filename);

	// VARIABLE INITIALIZATION
	$passed_id = $_GET["id"];

	// QUERIES
	$affiliate_links_collection = $DB->info("affiliate_links_collection", "affiliate_links_collection_id = '{$passed_id}'");
	$affiliate_links = $DB->query("SELECT * FROM {$dbprefix}affiliate_links WHERE user_id = '{$UserID}'");
	// var_dump($affiliate_links);

	// IF FORM SUBMITS
	if($_POST["submit"]){
		// INITIALIZE POST VARIABLES
		$remove[] = "'";
		$remove[] = '"';
		$remove[] = "-";

		// STRIP 1
		$cs_stripped_1 = str_replace($remove, "", $_POST["affiliate_links_collection_title"]);
		$affiliate_links_collection_title = $cs_stripped_1;

		// STRIP 2
		$cs_stripped_2 = str_replace($remove, "", $_POST["affiliate_links_collection_included_affiliate_link_ids"]);
		$affiliate_links_collection_included_affiliate_link_ids = implode(", ", $cs_stripped_2);
		
		// IF $passed_id HAS A VALUE
		if(empty($passed_id)){
			$affiliate_links_collection_id = $DB->getauto("affiliate_links_collection");
			$insert_affiliate_links_collection = $DB->query("INSERT INTO {$dbprefix}affiliate_links_collection SET 
				affiliate_links_collection_id = '{$affiliate_links_collection_id}', 
				user_id = '{$UserID}', 
				affiliate_links_collection_title = '{$affiliate_links_collection_title}', 
				affiliate_links_collection_included_affiliate_link_ids = '{$affiliate_links_collection_included_affiliate_link_ids}'");

			if($insert_affiliate_links_collection){
				$_SESSION["msg_success"] = "Affiliate Link: Collection creation successful.";

				redirect("index.php?cmd=affiliatelinkscollection");
			}
			else{
				$_SESSION["msg_error"] = "Affiliate Link: Collection creation failure.";
			}
		}
		else{
			$update_affiliate_links_collection = $DB->query("UPDATE {$dbprefix}affiliate_links_collection SET 
				affiliate_links_collection_title = '{$affiliate_links_collection_title}', 
				affiliate_links_collection_included_affiliate_link_ids = '{$affiliate_links_collection_included_affiliate_link_ids}' 
				WHERE affiliate_links_collection_id = '{$passed_id}'");

			if($update_affiliate_links_collection){
				$_SESSION["msg_success"] = "Affiliate Link: Collection update successful.";

				redirect("index.php?cmd=affiliatelinkscollection");
			}
			else{
				$_SESSION["msg_error"] = "Affiliate Link: Collection update failure.";
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

	/* PAGES CONTENT OPTIONS STYLE */
	.pages-content-options{
		cursor: pointer;

		transition: background-color .2s ease-in-out;
	}

	.pages-content-options:hover{
		background-color: gainsboro;
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
						<div class="p-2 <?= empty($passed_id) ? "mr-auto" : ""; ?>">
							<a class="btn btn-outline-secondary" href="index.php?cmd=affiliatelinkscollection"><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;Back to Affiliate Links: Collection Table</a>
						</div>
						<?php if(!empty($passed_id)) : ?>
						<div class="p-2 mr-auto">
							<button class="btn btn-outline-secondary" data-toggle="modal" data-target="#delete-modal" 
							data-id-to-delete="<?= $affiliate_links_collection["affiliate_links_collection_id"]; ?>" 
							onclick="getAttributes(this)" type="button">Delete this affiliate links: collection</button>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="card-body">
					<!-- AFFILIATE LINK: COLLECTION TITLE -->
					<div class="form-group">
						<div class="row">
							<div class="col-md-12">
								<label>Collection Title</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The title of your collection."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="affiliate_links_collection_title" value="<?= $affiliate_links_collection["affiliate_links_collection_title"]; ?>" maxlength="100" />
							</div>
						</div>
					</div>

					<!-- AFFILIATE LINK: COLLECTION INCLUDED AFFILIATE LINKS -->
					<?php if($affiliate_links) : ?>
					<div class="form-group">
						<div class="row">
							<div class="col-md-12">
								<label>Affiliate Links</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Choose affiliate links to include in your collection."><i class="fa fa-question"></i></span>
								<a class="btn btn-sm btn-outline-secondary ml-2" href="index.php?cmd=affiliatelinks">Manage your Affiliate Links</a>

								<div class="col-md-12">
									<div class="sticky-top bg-dark text-white px-4">Affiliate Links</div>
									<div class="px-3 py-1 rounded" id="checkbox-container">
										<?php $previously_selected_ids = explode(", ", $affiliate_links_collection["affiliate_links_collection_included_affiliate_link_ids"]); ?>
										<?php foreach($affiliate_links as $affiliate_link) : ?>
										<div class="form-check">
											<input class="form-check-input included-id" type="checkbox" name="affiliate_links_collection_included_affiliate_link_ids[]" value="<?= $affiliate_link["affiliate_links_product_name"]; ?>" <?= (in_array($affiliate_link["affiliate_links_product_name"], $previously_selected_ids)) ? "checked" : ""; ?> />
											<label class="form-check-label"><u><?= $affiliate_link["affiliate_links_product_name"]; ?></u></label>
										</div>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php else : ?>
					<hr />
					<h5 class="text-center">Oops! You don't have any Affiliate Links! <a href="index.php?cmd=affiliatelinks">Create one now!</a></h5>
					<?php endif; ?>
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
							<a class="btn btn-outline-secondary btn-block" href="index.php?cmd=affiliatelinkscollection">
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

<!-- DISPLAY data- ATTRIBUTES IN THE MODAL -->
<script type="text/javascript">
	function getAttributes(attributes){
		// DELETE CONFIRMATION
		var id_to_delete = attributes.getAttribute("data-id-to-delete");
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=affiliatelinkscollection&affiliate_links_collection_id=${id_to_delete}`;
	}
</script>

<!-- LIMIT INCLUDED AFFILIATE LINK TO 3 -->
<script type="text/javascript">
	var limit = 99;
	var included_ids = document.querySelectorAll(".included-id");

	<?php if(empty($affiliate_links_collection["affiliate_links_collection_included_affiliate_link_ids"])) : ?>
	var checked_array = [];
	<?php else : ?>
	<?php
		$previously_selected_ids_for_js = explode(", ", $affiliate_links_collection["affiliate_links_collection_included_affiliate_link_ids"]);
		$previously_selected_ids_for_js = implode('", "', $previously_selected_ids_for_js);
	?>
	var checked_array = ["<?= $previously_selected_collection_ids_for_js; ?>"];
	<?php endif; ?>
	for(var counter = 0; included_ids.length > counter; counter++){
		included_ids[counter].onclick = function(){
			if(this.checked == true){
				if(checked_array.length < limit){
					checked_array.push(this.checked);
				}
				else{
					this.checked = false;
				}
			}
			else{
				checked_array.pop();
			}
		}
	}
</script>