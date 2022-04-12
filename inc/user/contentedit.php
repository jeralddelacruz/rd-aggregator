<?php
	// CODE_SECTION_PHP_1: PRIVILEGE
	hasPageAccess("contents", $cur_pack["pack_ar"]);

	include("queries/contentedit_func.php");
?>
<link rel="stylesheet" href="../inc/user/styles/contentedit_style.css">

<!-- CODE_SECTION_HTML_1: CONTENT_MAIN -->
<div class="container-fluid px-0">
	<form method="POST" enctype="multipart/form-data">
		<!-- CODE_SECTION_PHP_HTML_1: SUCCESS_AND_ERROR_ALERT -->
		<?php if($_SESSION["msg_error"]) : ?>
    		<div class="col-md-12">
    			<div class="alert alert-danger"><?php echo $_SESSION["msg_error"]; $_SESSION["msg_error"] = ""; ?></div>
    		</div>
		<?php endif; ?>
		<?php if($_SESSION["has_no_thumbnail_found"]) : ?>
        	<div class="col-md-12">
        		<div class="alert alert-danger"><?php echo $_SESSION["has_no_thumbnail_found"]; $_SESSION["has_no_thumbnail_found"] = ""; ?></div>
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
    							<a class="btn btn-outline-secondary" href="index.php?cmd=contents"><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;Back to content Table</a>
    						</div>
    						<?php if(!empty($passed_id)) : ?>
        						<div class="p-2">
        							<button class="btn btn-outline-secondary" data-toggle="modal" data-target="#delete-modal"
        							data-content-id="<?= $content["content_id"]; ?>" 
        							onclick="getAttributes(this)" type="button">Delete this content</button>
        						</div>
    						<?php endif; ?>
    					</div>
					</div>
				</div>
				<div class="card-body">
				    <input class="form-control" type="hidden" name="content_title" id="user_id" value="<?= $UserID; ?>" maxlength="100" required/>
					<!-- Content TITLE AND TYPE -->
					<div class="form-group">
						<div class="row">
                            <div class="col-md-6">
								<label>Content Name</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The name of your content."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="content_title" value="<?= $content["content_title"]; ?>" maxlength="100" required/>
							</div>
                            <div class="col-md-6">
								<label>Feed Link</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="The url of your content."><i class="fa fa-question"></i></span>

								<input class="form-control" type="text" name="feed_link" id="feedURL" value="<?= $content["feed_link"]; ?>" maxlength="100" required/>
							</div>
							
							<!-- STATUS -->
                            <div class="col-md-12 mt-3">
								<label>Status</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Status of your content."><i class="fa fa-question"></i></span>
                
								<select class="form-control" name="category_status">
        							<option selected disabled>Select an option</option>
        							<option value="Featured" <?= $content['category_status'] == 'Featured' ? 'selected' : '' ?>>Featured</option>
        							<option value="Trending" <?= $content['category_status'] == 'Trending' ? 'selected' : '' ?>>Trending</option>
        							<option value="New" <?= $content['category_status'] == 'New' ? 'selected' : '' ?>>New</option>
    							</select>
							</div>
							
							<!-- ADS IMAGE -->
                            <div class="col-md-12 mt-3">
								<label>Content Image (Optional)</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="This Content Image can be used when the system detects that the rss feed link does not have an Image or Thumbnail."><i class="fa fa-question"></i></span>

								<input class="form-control" id="content-image" type="file" name="content_image" value="" <?= (!empty($content["content_image"])) ? "disabled" : "" ; ?> style="<?= (!empty($content["content_image"])) ? "cursor: not-allowed;" : ""; ?>" />
							</div>
							
							<!-- ADS IMAGE: PREVIEW -->
							<div class="col-md-12" id="content-image-preview-container" style="<?= !empty($content["content_image"]) ? "display: block;" : "display: none;"; ?>">
								<h5 class="mt-3">Preview</h5>
                                
								<img class="img-fluid rounded" id="content-image-preview" src="<?= "../upload/{$UserID}/".$content["content_image"]; ?>" />
                                <?php if( !empty($content["content_image"]) ){ ?>
                                    <div class="img-overlay-custom text-center">
    									<a class="btn btn-danger mt-2" href="<?= "index.php?cmd=contentsedit&delete=content_image&content_id={$content["content_id"]}&content_image={$content["content_image"]}"; ?>">Delete</a>
    								</div>
                                <?php } ?>
							</div>

                            <!-- Content IMAGE -->
       <!--                     <div class="col-md-12 mt-3">-->
							<!--	<label>Content Image</label>-->
							<!--	<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="An image for your content."><i class="fa fa-question"></i></span>-->

							<!--	<input class="form-control" id="content-image" type="file" name="content_image" value="" <?= (!empty($content["content_image"])) ? "disabled" : "" ; ?> style="<?= (!empty($content["content_image"])) ? "cursor: not-allowed;" : ""; ?>" />-->
							<!--</div>-->

							<!-- Content IMAGE: PREVIEW -->
							<!--<div class="col-md-12 mt-3" id="content-image-preview-container" style="<?= !empty($content["content_image"]) ? "display: block;" : "display: none;"; ?>">-->
							<!--	<h5 class="mt-3">Preview</h5>-->
                                
							<!--	<img class="img-fluid rounded" id="content-image-preview" src="<?= "../upload/{$UserID}/".$content["content_image"]; ?>" />-->
       <!--                         <?php if( !empty($content["content_image"]) ){ ?>-->
       <!--                             <div class="img-overlay-custom text-center">-->
    			<!--						<a class="btn btn-danger mt-2" href="<?= "index.php?cmd=contentsedit&delete=content_image&content_id={$content["content_id"]}&content_image={$content["content_image"]}"; ?>">Delete</a>-->
    			<!--					</div>-->
       <!--                         <?php } ?>-->
							<!--</div>-->
							
							<!-- Category -->
                            <div class="col-md-6 mt-3">
								<label>Category</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Category of your content."><i class="fa fa-question"></i></span>
								<?php if( count($categories) > 0 ){ ?>
    								<select class="form-control" name="category_id">
            							<option>Select an option</option>
            							
            							<?php foreach($categories as $category){ ?>
            								<option <?= $category['category_id'] == $content['category_id'] ? 'selected' : '' ?> value="<?= $category['category_id'] ?>"><?= $category['category_name']; ?></option>
            							<?php } ?>
        							</select>
    							<?php } else { ?>
        							<hr />
                					<h5 class="text-left">Oops! You don't have any category! <a href="index.php?cmd=category">Create one now!</a></h5>
                				<?php } ?>
							</div>
							
							<!-- Banner Ads -->
                            <div class="col-md-3 mt-3">
								<label>Banner Ads</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Banner Ads of your content."><i class="fa fa-question"></i></span>
                                
                                <?php if( count($ads) > 0 ){ ?>
    								<select class="form-control" name="banner_ads_id">
            							<option>Select an option</option>
            							
            							<?php foreach($ads as $ad){ 
            							    if($ad['ads_type'] === "Banner"):
            							?>
            								<option <?= $ad['ads_id'] == $content['banner_ads_id'] ? 'selected' : '' ?> value="<?= $ad['ads_id']; ?>"><?= $ad['ads_name']; ?></option>
            							<?php 
            							    endif;
            							    } 
            							?>
        							</select>
        						<?php } else { ?>
        							<hr />
                					<h5 class="text-left">Oops! You don't have any ads! <a href="index.php?cmd=ads2">Create one now!</a></h5>
                				<?php } ?>
							</div>
							<!-- Sidebar Ads -->
                            <div class="col-md-3 mt-3">
								<label>Sidebar Ads</label>
								<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Sidebar Ads of your content."><i class="fa fa-question"></i></span>
                                
                                <?php if( count($ads) > 0 ){ ?>
    								<select class="form-control" name="sidebar_ads_id">
            							<option>Select an option</option>
            							
            							<?php foreach($ads as $ad){ 
            							    if($ad['ads_type'] === "Sidebar"):
            							?>
            								<option <?= $ad['ads_id'] == $content['sidebar_ads_id'] ? 'selected' : '' ?> value="<?= $ad['ads_id']; ?>"><?= $ad['ads_name']; ?></option>
            							<?php 
            							    endif;
            							    } 
            							?>
        							</select>
        						<?php } else { ?>
        							<hr />
                					<h5 class="text-left">Oops! You don't have any ads! <a href="index.php?cmd=ads2">Create one now!</a></h5>
                				<?php } ?>
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
							<a class="btn btn-outline-secondary btn-block" href="index.php?cmd=contents">
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
			<div class="modal-body text-center">Are you sure you want to <b>DELETE</b> this content?</div>
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
		var id_to_delete = attributes.getAttribute("data-content-id");
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=contents&content_id=${id_to_delete}`;
	}
</script>

<script type="text/javascript">
	window.onload = function(){
		var content_image = document.getElementById("content-image");
		var content_image_preview = document.getElementById("content-image-preview");
		var content_image_preview_container = document.getElementById("content-image-preview-container");

		function readFile(input){

			if(input.files && input.files[0]){
				var file_reader = new FileReader();

				file_reader.onload = function(e){
					content_image_preview.src = e.target.result;
				}

				file_reader.readAsDataURL(input.files[0]);
			}
		}

		content_image.oninput = function(){
			if(content_image.value == ""){
				content_image_preview_container.style.display = "none";
			}
			else{
				content_image_preview_container.style.display = "block";
			}

			readFile(this);
		}
	}
</script>