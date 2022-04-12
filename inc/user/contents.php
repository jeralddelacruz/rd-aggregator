<?php
	hasPageAccess("contents", $cur_pack["pack_ar"]);
	
    include(dirname(__FILE__) . "/../inc/simple_html_dom_v2.php");
	include("queries/contents_func.php");
?>
<link rel="stylesheet" href="../inc/user/styles/contents_style.css">

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
	
	<?php if($_SESSION["msg_warning"]) : ?>
    	<div class="col-md-12">
    		<div class="alert alert-warning"><?php echo $_SESSION["msg_warning"]; $_SESSION["msg_warning"] = ""; ?></div>
    	</div>
	<?php endif; ?>

	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<div class="d-flex flex-row justify-content-between align-items-center">
					<div class="p-2">
						<h4 style="padding: 10px;"><?= $index_title; ?></h4>
						<small style="padding-left: 10px;">Manage your Contents here</small>
					</div>
					<div class="p-2">
                        <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#new-collection-modal">Create New</button>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="collections-container">
				    <div class="row">
				        <?php foreach( $collections as $collection ): ?>
    				        <div class="col-md-6 col-sm-12 col-lg-4">
    				            <div class="card collection-container">
    				                <div class="card-header">
    				                    <div class="title">
    				                        <h4 class="pr-2"><?= $collection['collection_name'] ?></h4> <small><?= $collection['collection_post_status'] ?></small>
    				                    </div>
    				                    <div class="group-btn">
    				                        <button type="button mr-2" data-toggle="modal" data-target="#edit-collection-modal" class="edit-collection" data-id="<?= $collection['content_collection_id'] ?>" data-name="<?= $collection['collection_name'] ?>" data-status="<?= $collection['collection_post_status'] ?>"><i class="fas fa-edit"></i></button>
    				                        <button type="button" data-toggle="modal" data-target="#delete-modal" 
    				                            data-collection-id="<?= $collection["content_collection_id"]; ?>" 
									            onclick="getAttributes(this)"
									        > <i class="fas fa-trash"></i> </button>
    				                    </div>
    				                </div>
    				                <div class="card-body">
    				                    <div class="row">
    				                        <?php foreach( $contents as $content ): ?>
        				                        <?php if( $collection["content_collection_id"] === $content["content_collection_id"] ): ?>
            				                        <div class="col-md-6 col-sm-12 col-lg-4 card-content-container">
            				                            <div class="card card-content">
            				                                <div class="card-body" data-content="<?= $content['content_id'] ?>" data-collection="<?= $content['content_collection_id'] ?>" data-action="edit">
            				                                    <i class="fas fa-rss-square content-icon"></i>
            				                                    <h5><?= $content["content_title"] ?></h5>
            				                                </div>
            				                            </div>
            				                        </div>
            				                    <?php endif; ?>
        				                    <?php endforeach; ?>
        				                    <div class="col-md-6 col-sm-12 col-lg-4 card-content-container">
                    				            <div class="card card-content-add" data-id="<?= $collection['content_collection_id'] ?>" data-toggle="modal" data-target="#new-feed-modal">
                    				                <div class="card-body" data-action="add">
                    				                    <i class="fas fa-plus"></i>
                    				                </div>
                    				            </div>
                    				        </div>
    				                    </div>
    				                </div>
    				            </div>
    				        </div>
    				    <?php endforeach; ?>
				    </div>
				</div>
			</div>
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
			<div class="modal-body text-center">Are you sure you want to <b>DELETE</b> this content?</div>
			<div class="modal-footer">
				<a class="btn btn-danger" id="delete-button" href="" data-dismiss="modal">Yes I am sure</a>
			</div>
		</div>
	</div>
</div>

<!-- EDIT COLLECTION MODAL -->
<div class="modal fade" id="edit-collection-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">EDIT COLLECTION</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<form method="POST">
    			<div class="modal-body">
    			    <input type="hidden" name="collection_id" id="collection_id1">
			        <div class="form-group">
			            <label>Name</label>
			            <input type="text" name="collection_name" id="collection_name1" class="form-control" required>
			        </div>
			        
			        <div class="form-group">
			            <label>Status</label>
			            <select name="collection_status" id="collection_status1" class="form-control">
			                <option value="Approved">Approved</option>
			                <option value="Needs Approval">Needs Approval</option>
			            </select>
			        </div>
    			</div>
    			<div class="modal-footer">
    				<button type="submit" class="btn btn-danger" name="save_collection" href="" >Save Collection</a>
    			</div>
    		</form>
		</div>
	</div>
</div>

<!-- NEW COLLECTION MODAL -->
<div class="modal fade" id="new-collection-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">CREATE NEW COLLECTION</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<form method="POST">
    			<div class="modal-body">
			        <div class="form-group">
			            <label>Name</label>
			            <input type="text" name="collection_name" class="form-control" required>
			        </div>
			        
			        <div class="form-group">
			            <label>Status</label>
			            <select name="collection_status" class="form-control">
			                <option value="Approved">Approved</option>
			                <option value="Needs Approval">Needs Approval</option>
			            </select>
			        </div>
    			</div>
    			<div class="modal-footer">
    				<button type="submit" class="btn btn-danger" name="save_new_collection" href="" >Save Collection</a>
    			</div>
    		</form>
		</div>
	</div>
</div>

<!-- NEW COLLECTION MODAL -->
<div class="modal fade" id="new-feed-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Select network</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
			    <p class="mb-2">Other network will implemented soon.</p>
		        <div class="network-list">
		            <i class="fas fa-rss-square content-icon network-selected"></i>
		        </div>
			</div>
		</div>
	</div>
</div>

<!-- DATATABLE INITIALIZATION -->
<script type="text/javascript">
	$(document).ready(function(){
	
	});
</script>

<!-- DISPLAY data- ATTRIBUTES IN THE MODAL -->
<script type="text/javascript">
    $(".edit-collection").click(function(e){
        $("#collection_id1").val($(this).data("id"));
        $("#collection_name1").val($(this).data("name"));
        $("#collection_status1").val($(this).data("status")).change();
    });
    
    // FOR ADDING
    $(".card-content-add").click(function(e){
        let id = $(this).data("id");
        localStorage.setItem("collection_id", id);
    });
    
    $(".network-selected").click(function(e){
        let id = localStorage.getItem("collection_id");
        window.location.href = "<?= $SCRIPTURL ?>user/index.php?cmd=contentedit&collection_id=" + id;
    });
    
    // FOR EDIT
    $(".card-content-container .card-body").click(function(e){
        
        let id = $(this).data("content");
        let collection_id = $(this).data("collection");
        let action = $(this).data("action");
        
        if( action == "edit" ){
            window.location.href = "<?= $SCRIPTURL ?>user/index.php?cmd=contentedit&id=" + id + "&collection_id=" + collection_id;
        }
    });
    
	function getAttributes(attributes){
		// DELETE CONFIRMATION
		var id_to_delete = attributes.getAttribute("data-collection-id");
        console.log(id_to_delete)
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=contents&collection_id=${id_to_delete}`;
	}
</script>