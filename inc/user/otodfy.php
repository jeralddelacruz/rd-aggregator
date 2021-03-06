<?php include('../../cache_solution/top-cache-v2.php'); ?>
<?php
	// CODE_SECTION_PHP_1: PRIVILEGE
	if(!preg_match(";otodfy;", $cur_pack["pack_ar"])){
		redirect("index.php?cmd=deny");
	}
	
	$user=$DB->info("user","user_id='$UserID'");
	$campaigns_type = 'dfy';
	
	$hasAlreadySetup = false;
    if(!empty($user['subdomain_id'])){
        $hasAlreadySetup = true;
    }
    
    $DFYAuthorID = $WEBSITE["dfy_author"];
	// CODE_SECTION_PHP_3: DELETE_TO_DATABASE
	if(!empty($_GET["delete"])){
		$campaigns_id = $_GET["campaigns_id"];

		$delete_campaign = $DB->query("DELETE FROM {$dbprefix}campaigns WHERE campaigns_id = '{$campaigns_id}'");

		if($delete_campaign){
			$_SESSION["msg_success"] = "Campaign deleted.";

			redirect("index.php?cmd=otodfy");
		}
	}
	
    $campaigns = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = '{$UserID}' AND campaigns_type = '$campaigns_type'");
	// GET ALL THE CONTENTS BY CAMPAIGNS OF CURRENT USER
    foreach($campaigns as $campaign){
        // ============== GET THE NEWS ============== //
        $newsData = array();
        foreach( json_decode($campaign['content_id']) as $content_id ){
            $content = $DB->info("content", "content_id = {$content_id}");
            $category = $DB->info("category", "category_id = {$content['category_id']}");
            $news = $DB->query( "SELECT * FROM {$dbprefix}news WHERE content_id = '{$content_id}' ORDER BY created_at DESC;" );
            foreach( $news as $new ){
                $newsData[] = [
                    "news_id"   => $new['news_id'],
                    "category"  => $category['category_name'],
                    "category_desc"  => $category['category_desc'],
                    "status"    => $content['category_status'],
                    "image"     => $new['news_image'],
                    "uploaded_image" => $new['uploaded_image'],
                    "title"     => $new['news_title'],
                    "link"      => $new['news_link'],
                    "date"      => $new['news_published_date'],
                    "author"    => $new['news_author']
                ];
            }
        }
        $result = json_encode($newsData);
        file_put_contents("../add/cache/".md5($campaign['campaigns_id']).".cache", $result);
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
	
	.no-subdomain-container {
        height: 88vh;
        background-color: #d0cfcf;
        border-radius: 5px;
    }
    
    .subdomain-row{
        justify-content: space-evenly;
        align-items: center;
        height: 79%;
        text-align: center;
    }
    
    .container-item p {
        font-size: 1.4rem;
    }
    .container-item h1 {
        font-size: 4rem;
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
	
	
	<?php if( !$hasAlreadySetup ) : ?>
    	<div class="col-md-12 no-subdomain-container">
    	    <div class="row subdomain-row">
    	        <div class="col-md-6">
    	            <div class="container-item">
            	        <h1>Oops!</h1>
            	        <p>Your subdomain is not yet setup. <a href="index.php?cmd=home">setup now</a></p>
            	    </div>
    	        </div>
    	    </div>
    	    
    		<!--<div class="alert alert-warning"><strong>Oops!</strong> Your subdomain is not yet setup. <a href="index.php?cmd=home">setup now</a></div>-->
    		<!--<img src="../assets/img/nosubdomain-icon.png">-->
    	</div>
	<?php else: ?>
	
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<div class="d-flex flex-row justify-content-between align-items-center">
					<div class="p-2">
						<h4 style="padding: 10px;"><?= $index_title; ?></h4>
						<small style="padding-left: 10px;">Manage your Campaigns here</small>
					</div>
					<div class="p-2">
					    <!--<?php if($UserID == $DFYAuthorID){ ?>-->
					    <!--    <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#campaigns-type-modal">Create New</button>-->
					    <!--<?php }else{ ?>-->
					    <!--    <a href="index.php?cmd=otodfyedit&campaigns_type=regular" class="btn btn-outline-secondary">Create New</a>-->
					    <!--<?php } ?>-->
						<!--<a class="btn btn-outline-secondary" data-toggle="modal" data-target="#campaigns-type-modal">Create New</a>-->
						
						<?php if($UserID == $DFYAuthorID){ ?>
					        <a href="index.php?cmd=otodfyedit&campaigns_type=dfy" class="btn btn-outline-secondary">Create New</a>
					    <?php } ?>
						
						
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="content table-responsive table-full-width">
					<table class="table table-hover table-striped" id="pages-table">
						<thead>
							<tr>
								<th>Campaign Title</th>
								<th>Campaign Type</th>
								<th class="text-center">Edit</th>
								<th class="text-center">Publish</th>
								<!--<th class="text-center">Download</th>-->
								<th class="text-center">Delete</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						  //  if( $UserID == 2){
						  //      $campaigns = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = '{$UserID}'");
						  //  }else{
						  //      if(preg_match(";campaignsdfy;", $cur_pack["pack_ar"])){
    				// 	            $campaigns = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = '{$UserID}'");
    				// 	        }else{
    				// 	            $campaigns = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = '{$UserID}' AND campaigns_type = 'regular'");
    				// 	        }
						  //  }
						  
						    $campaigns = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = '{$UserID}' AND campaigns_type = '$campaigns_type'");
						    
						    foreach($campaigns as $campaign) : 
						?>
    							<tr>
    								<td><?= $campaign["campaigns_title"]; ?></td>
    								<td><?= $campaign["campaigns_type"]; ?></td>
    								<td class="text-center">
    									<a href="index.php?cmd=otodfyedit&id=<?= $campaign["campaigns_id"]; ?>&campaigns_type=<?= $campaign["campaigns_type"]; ?>" class="btn btn-secondary">
    										<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;Edit
    									</a>
    								</td>
    								<td class="text-center">
    									<button class="btn btn-secondary" data-toggle="modal" data-target="#publish-modal" 
    									data-campaigns-title="<?= $campaign["campaigns_title"]; ?>" 
    									data-campaigns-url="<?= "/add/news.php?campaigns_id={$campaign["campaigns_id"]}"; ?>" 
    									onclick="getAttributes(this)">
    										<i class="fa fa-file-text-o"></i>&nbsp;&nbsp;Publish
    									</button>
    								</td>
    								<!--<td class="text-center">-->
    								<!--	<button class="btn btn-secondary" data-toggle="modal" data-target="#download-modal" -->
    								<!--	data-campaigns-id="<?= $campaign["campaigns_id"]; ?>" -->
    								<!--	onclick="getAttributes(this)" type="button">-->
    								<!--		<i class="fa fa-download"></i>&nbsp;&nbsp;Download-->
    								<!--	</button>-->
    								<!--</td>-->
    								<td class="text-center">
    									<button class="btn btn-secondary" data-toggle="modal" data-target="#delete-modal" 
    									data-campaigns-id="<?= $campaign["campaigns_id"]; ?>" 
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
	<?php endif; ?>
</div>

<!-- PAGE TYPE SELECTION MODAL -->
<div class="modal fade" id="campaigns-type-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">What kind of campaign you want to create?</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body text-center">
				<div class="btn-group">
					<a class="btn btn-outline-secondary" href="index.php?cmd=otodfyedit&campaigns_type=dfy">A Done For You</a>
					<a class="btn btn-outline-secondary" href="index.php?cmd=otodfyedit&campaigns_type=regular">Regular Campaign</a>
				</div>
			</div>
			<div class="modal-footer"></div>
		</div>
	</div>
</div>

<!-- CODE_SECTION_HTML_4: MODALS -->
<!-- PUBLISH MODAL -->
<div class="modal fade" id="publish-modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modal-title">Publish</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-3">
						<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
							<a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-view-page" role="tab" aria-controls="v-pills-view-page" aria-selected="true">View Page</a>
							<a class="nav-link" id="v-pills-embed-page-tab" data-toggle="pill" href="#v-pills-embed-page" role="tab" aria-controls="v-pills-embed-page" aria-selected="false">Embed Page</a>
							<!--<a class="nav-link" id="v-pills-download-page-tab" data-toggle="pill" href="#v-pills-download-page" role="tab" aria-controls="v-pills-download-page" aria-selected="false">Download Page</a>-->
						</div>
					</div>
					<div class="col-9">
						<div class="tab-content" id="v-pills-tabContent">
							<div class="tab-pane fade show active" id="v-pills-view-page" role="tabpanel" aria-labelledby="v-pills-view-page-tab">
								<h4>View</h4>
								<div class="input-group mb-4">
									<input class="form-control" id="view-url-input" type="text" name="pages_input_view" readonly />
									<div class="input-group-append">
										<button class="btn btn-outline-secondary" type="button" onclick="copyData(this)">Copy</button>
									</div>
									<div class="input-group-append">
										<a class="btn btn-outline-secondary" id="view-url-button" href="" target="_blank">View</a>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="v-pills-embed-page" role="tabpanel" aria-labelledby="v-pills-embed-page-tab">
								<h4>Embed</h4>
								<div class="input-group mb-4">
									<input class="form-control" id="embed-url-input" type="text" readonly />
									<div class="input-group-append">
										<button class="btn btn-outline-secondary" type="button" onclick="copyData(this)">Copy</button>
									</div>
								</div>
							</div>
							<!--<div class="tab-pane fade" id="v-pills-download-page" role="tabpanel" aria-labelledby="v-pills-download-page-tab">-->
							<!--	<h4>Download</h4>-->
							<!--	<div class="input-group mb-4">-->
							<!--		<input class="form-control" type="text" id="download-url-input" readonly />-->
							<!--		<div class="input-group-append">-->
							<!--			<a class="btn btn-outline-secondary" id="download-url-button" href="" target="_blank" download>Download</a>-->
							<!--		</div>-->
							<!--	</div>-->
							<!--</div>-->
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer"></div>
		</div>
	</div>
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

<!-- DOWNLOAD CONFIRMATION MODAL -->
<div class="modal fade" id="download-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Download Confirmation</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body text-center">Are you sure you want to <b>DOWNLOAD</b> this page?</div>
			<div class="modal-footer">
				<a class="btn btn-danger" id="download-button" href="" data-dismiss="modal">Yes I am sure</a>
			</div>
		</div>
	</div>
</div>

<!-- DATATABLE INITIALIZATION -->
<script type="text/javascript">
	$(document).ready(function(){
		$('#pages-table').DataTable();
	});
</script>

<!-- DISPLAY data- ATTRIBUTES IN THE MODAL -->
<script type="text/javascript">
	function getAttributes(attributes){
	    var url = window.location.href;
        var domain = url.replace('http://','').replace('https://','').split(/[/?#]/)[0];
        
		// ATTRIBUTES FROM PHP FOREACH
		var campaigns_title = attributes.getAttribute("data-campaigns-title");
		var campaigns_url = attributes.getAttribute("data-campaigns-url");
        campaigns_url = "https://"+domain+campaigns_url;
		// MODAL TITLE
		var campaigns_modal_title = document.getElementById("modal-title");
		campaigns_modal_title.innerHTML = campaigns_title;

		// VIEW
		var view_url_input = document.getElementById("view-url-input");
		var view_url_button = document.getElementById("view-url-button");
		view_url_input.value = campaigns_url;
		view_url_button.href = campaigns_url;

		// EMBED
		var embed_url_input = document.getElementById("embed-url-input");
		embed_url_input.value = "<iframe src=\"" + campaigns_url + "\" style=\"width: 100%; height: 100%; border: none;\"></iframe>";

// 		// DOWNLOAD
// 		var download_url_input = document.getElementById("download-url-input");
// 		var download_url_button = document.getElementById("download-url-button");
// 		download_url_input.value = campaigns_url;
// 		download_url_button.href = campaigns_url;
		
		// DOWNLOAD CONFIRMATION
		var id_to_download = attributes.getAttribute("data-campaigns-id");
		var download_button = document.getElementById("download-button");
		download_button.href = `index.php?cmd=otodfy&campaigns_id=${id_to_download}&download=1`;

		// DELETE CONFIRMATION
		var id_to_delete = attributes.getAttribute("data-campaigns-id");
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=otodfy&campaigns_id=${id_to_delete}&delete=1`;
	}
</script>

<!-- COPY FUNCTION IN THE MODAL -->
<script type="text/javascript">
	function copyData(clipboardText){
		var copyThis = clipboardText.parentNode.previousElementSibling;
		
		copyThis.select();
		document.execCommand("copy");
	}
</script>

<?php
    if(empty($_GET["download"]) && $_SESSION['hasDownloaded']){
        echo "<script>";
        echo "window.open('https://newsmaximizer.com/inc/user/DownloadCampaign/archive/newscampaign.zip','_blank')";
        echo "</script>";
        unset($_SESSION['hasDownloaded']);
    }
?>
<?php include('../../cache_solution/bottom-cache-v2.php'); ?>