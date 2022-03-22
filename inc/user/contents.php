<script src="../../assets/js/rss-feed-generator.js"></script>
<?php
    include(dirname(__FILE__) . "/../inc/simple_html_dom_v2.php");
    
	// CODE_SECTION_PHP_1: PRIVILEGE
	if(!preg_match(";content;", $cur_pack["pack_ar"])){
		// redirect("index.php?cmd=deny");
		// NOTE: COMMENTED-OUT BECAUSE content IS STILL NOT ADDED AS A PACKAGE.
	}
	
	$and_query = "";
	$subdomain_id = 0;
	if( $user_subdomain ){
	    $subdomain_id = $user_subdomain["subdomain_id"];
	    $and_query = " AND subdomain_id = '{$subdomain_id}'";
	}else{
	    $and_query = " AND subdomain_id = 0";
	}
	
	// Get the content collections
	$collections = $DB->query("SELECT * FROM {$dbprefix}content_collection WHERE user_id = '{$UserID}' {$and_query}");
	
	// Get the contents
	$contents = $DB->query("SELECT * FROM {$dbprefix}content WHERE user_id = '{$UserID}' {$and_query}");
	
	if(isset( $_POST["save_collection"] )) {
	    $collection_id = $_POST["collection_id"];
	    $collection_name = strip($_POST["collection_name"]);
	    $collection_status = $_POST["collection_status"];
	    
	    $res = $DB->query("UPDATE {$dbprefix}content_collection SET
	        subdomain_id = '{$subdomain_id}',
	        collection_name = '{$collection_name}',
	        collection_post_status = '{$collection_status}'
	    WHERE user_id = '{$UserID}' AND content_collection_id = '{$collection_id}'");
	    
	    if( $res ){
	        $_SESSION["msg_success"] = "Collection successfully saved";
	        redirect("index.php?cmd=contents");
	    }
	}
	
	if(isset( $_POST["save_new_collection"] )) {
	    $collection_name = strip($_POST["collection_name"]);
	    $collection_status = $_POST["collection_status"];
	    
	    $valid = true;
	    if( $collection_status == "Needs Approval" ) {
	        if(!preg_match(";m-edition;", $cur_pack["pack_ar"])){
        		$_SESSION["msg_error"] = "This feature is for the highest membership, please consider upgrading.";
        		$valid = false;
        	}
	    }
	    
	    if( count( $collections ) >= 2 ){
	        if(!preg_match(";m-edition;", $cur_pack["pack_ar"])){
        		$_SESSION["msg_error"] = "You have reached the limits of your account, please consider upgrading.";
	            $valid = false;
        	}
	    }
	    
	    if( $valid ) {
	        $res = $DB->query("INSERT INTO {$dbprefix}content_collection SET
    	        user_id = '{$UserID}',
    	        subdomain_id = '{$subdomain_id}',
    	        collection_name = '{$collection_name}',
    	        collection_post_status = '{$collection_status}'
    	    ");
    	    
    	    if( $res ){
    	        $_SESSION["msg_success"] = "Collection successfully saved";
    	        redirect("index.php?cmd=contents");
    	    }
	    }
	}
	
	// For generating the content
	$api_endpoint = 'https://api.rss2json.com/v1/api.json?rss_url=';
	$api_key="&api_key=6edlszdimfnisnbsrbxokautyipkvg1bpdn9nqe9";
	
	$content_data = $_SESSION['content_data'];
	$is_content_save = $_SESSION['is_content_save'];
	if( !$is_content_save && $content_data ){
	    $data = json_decode( $content_data );
	    $passed_id = $data->passed_id;
	    $feed_link = $data->feed_link;
	    $content_image = $data->content_image;
	    $status = $data->status;
	    
	    getFeed( $api_endpoint, $feed_link, $UserID, $passed_id, $dbprefix, $DB, $content_image, $SCRIPTURL, $api_key, $status );
        unset( $_SESSION['content_data']);
	    $_SESSION['is_content_save'] = true;
	}
	
	// METHOD TO GET THE FEED AND SAVE
	function getFeed( $api_endpoint, $feed_link, $UserID, $passed_id, $dbprefix, $DB, $uploaded_image, $SCRIPTURL, $api_key, $status ){
    	$rss_url = $feed_link;
        $data = json_decode( file_get_contents($api_endpoint . urlencode($rss_url) . $api_key) , true );
        $noThumbnailCount = 0;
        $hasContentCount = 0;
        // exit;
        if($data['status'] == 'ok'){
            foreach ($data['items'] as $item) {
                // INITIALIZE VARIABLES NEEDED
                $user_id            = $UserID;
            	$campaign_id        = null;
            	$content_id         = $passed_id;
            	$news_link          = $item['link'];
            	$news_thumbnail     = $item['thumbnail'];
            	$news_published_date= $item['pubDate'];
            	$news_title         = $item['title'];
            	$news_author        = $item['author'];
            	$news_content       = base64_encode($item['content']);
            	$news_description   = base64_encode($item['description']);
            	
                $content = $DB->info("news", "news_title = '{$news_title}' AND user_id ='{$user_id}' AND content_id='$content_id'");
                // exit;
                if( empty($content) ){
                    // CHECK IF THE RSS FEED LINK HAS THUMBNAIL OR IMAGE AND CHECK IF THE USER HAS UPLOADED IMAGE
                    if( $news_thumbnail || $uploaded_image ){
                        $imagePath = $uploaded_image;
                        
                        $image = $imagePath;
                        $temp_users_id = array();
                        $temp_users_id[] = $user_id;
                        $temp_users_id = json_encode( $temp_users_id );
                        
                        if( !$news_thumbnail ){
                            try {
                                $handle = curl_init();
 
                                $url = "https://newscascade.com/tertl3651!/feed_thumbnail.php";
                                // Array with the fields names and values.
                                // The field names should match the field names in the form.
                                 
                                $postData = array(
                                  'news_link' => $news_link
                                );
                               
                                // Here is the data we will be sending to the service
                                $curl = curl_init();
                                
                                // You can also set the URL you want to communicate with by doing this:
                                // $curl = curl_init('http://localhost/echoservice');
                                
                                // We POST the data
                                curl_setopt($curl, CURLOPT_POST, 1);
                                
                                // Set the url path we want to call
                                curl_setopt($curl, CURLOPT_URL, $url); 
                                  
                                // Make it so the data coming back is put into a string
                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                  
                                // Insert the data
                                curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
                                
                                // You can also bunch the above commands into an array if you choose using: curl_setopt_array
                                
                                // Send the request
                                $result = curl_exec($curl);
                                
                                // Get some cURL session information back
                                $info = curl_getinfo($curl);  
                                // echo 'content type: ' . $info['content_type'] . '<br />';
                                // echo 'http code: ' . $info['http_code'] . '<br />';
                                
                                //   echo 'http no: ' . json_encode($info) . '<br />';
                                $error_msg = curl_error($curl);
                                
                                // Free up the resources $curl is using
                                curl_close($curl);
                                // echo $result;
                                 $news_thumbnail = $result;
                            } catch (Exception $e) {
                                echo "<script>console.log('".$e->getMessage()."')</script>";
                                exit;
                            }
                        }else{
                            $images = array();
                            $images[] = $news_thumbnail;
                            
                            $news_thumbnail = json_encode($images);
                            
                        }
                        
                        $images = array();
                        $images[] = $image;
                        $image = json_encode($images);
                        
                        // CHECK THE CONTENT IF VALID
                    	$insert_sql =  "INSERT INTO {$dbprefix}news SET 
            	            users_id = '{$temp_users_id}',
            				rss_url = '{$rss_url}',
            				content_id = '{$content_id}',
            				news_link = '{$news_link}',
            				news_image = '{$news_thumbnail}',
            				uploaded_image = '{$image}',
            				news_title = '{$news_title}',
            				news_author = '{$news_author}',
            				news_content = '{$news_content}',
            				news_description = '{$news_description}',
            				status = '{$status}'
                        ";
                        
                        if( strlen($news_content) > 100 ){
                            // CHECK IF THE NEWS LINK IS ALREADY EXIST
                            $news = $DB->info("news", "news_link = '{$news_link}'");
                            if( !empty( $news ) ){
                                $news_id = $news["news_id"];
                                $users_id = json_decode( $news["users_id"] );
                                
                                if( !in_array( $user_id, $users_id ) ){
                                    $users_id[] = $user_id;
                                    $users_id = json_encode( $users_id );
                                    
                                    $update_sql =  "UPDATE {$dbprefix}news SET 
                        	            users_id = '{$users_id}',
                        				rss_url = '{$rss_url}',
                        				content_id = '{$content_id}',
                        				news_link = '{$news_link}',
                        				news_image = '{$news_thumbnail}',
                        				uploaded_image = '{$image}',
                        				news_title = '{$news_title}',
                        				news_author = '{$news_author}',
                        				news_content = '{$news_content}',
                        				news_description = '{$news_description}',
            				            status = '{$status}'
                                    WHERE news_id = '{$news_id}'";
                                    
                                    $result = $DB->query( $update_sql );
                                }else{
                                    $users_id = json_encode( $users_id );
                                    $update_sql =  "UPDATE {$dbprefix}news SET 
                        	            users_id = '{$users_id}',
                        				rss_url = '{$rss_url}',
                        				content_id = '{$content_id}',
                        				news_link = '{$news_link}',
                        				news_image = '{$news_thumbnail}',
                        				uploaded_image = '{$image}',
                        				news_title = '{$news_title}',
                        				news_author = '{$news_author}',
                        				news_content = '{$news_content}',
                        				news_description = '{$news_description}',
            				            status = '{$status}'
                                    WHERE news_id = '{$news_id}'";
                                    
                                    $result = $DB->query( $update_sql );
                                }
                                // UPDATE THE USERS ID
                                
                            }else{
                                $result = $DB->query( $insert_sql );
                            }
                            
                            if ( $result ) {
                        		$noThumbnailCount++;
                        	}
                        }
                        
                    }
                }
            }
            
            $content = $DB->query("SELECT * FROM {$dbprefix}content WHERE content_image = '' AND content_id ='{$passed_id}' {$and_query}");
            
            if( !$content ){
                $hasContentCount++;
            }
            
            // CHECK NO IMAGE COUNT IF 0
            if( $noThumbnailCount == 0 && $hasContentCount == 0 ){
                // $_SESSION['has_no_thumbnail_found'] = "No thumbnail/Image found on rss link, Content image will be used for all the generated feed.";
                $_SESSION['msg_success'] = "";
                $_SESSION['has_no_thumbnail_found'] = "Oops your rss feed link is not giving us a thumbnail/image, Please use different link or try to upload image below.";
                redirect("index.php?cmd=contentedit&id=".$passed_id);
            }
        }
	}
	// End For generating the content
	
	// CODE_SECTION_PHP_3: DELETE_TO_DATABASE
	if(!empty($_GET["collection_id"])){
		$collection_id = $_GET["collection_id"];

		$delete_content = $DB->query("DELETE FROM {$dbprefix}content_collection WHERE content_collection_id = '{$collection_id}'");

		if($delete_content){
			$_SESSION["msg_success"] = "Collection deleted.";

			redirect("index.php?cmd=contents");
		}
	}
	
?>
<!-- CODE_SECTION_HTML_2: CSS_EMBEDDED_DATATABLE -->
<style type="text/css">
	.collections-container .card-header .title{
	    display: flex;
	}
	
	.collections-container .card-header {
	    display: flex;
	    justify-content: space-between;
	}
	
	.card-content {
	    box-shadow: rgb(0 0 0 / 10%) 2px 2px 13px !important;
	    height: 100px;
	}
	
	.card-content-add {
	    box-shadow: rgb(0 0 0 / 10%) 2px 2px 13px !important;
	    border: 1px dashed #c1c1c1;
	    height: 100px;
	}
	
	i.fas.fa-trash {
        color: red;
    }
    
    i.fas.fa-edit {
        color: #006466;
    }
    
    .content-icon {
        font-size: 35px;
    }
    
    .collections-container .card-body i, .collections-container .card-body h4 {
        display: flex;
        justify-content: center;
    }
    
    .card.card-content-add .card-body {
        display: flex;
        align-items: center;
    }
    
    .card.card-content-add .card-body i {
        margin: 0 auto;
    }
    
    .collection-container {
        box-shadow: rgb(0 0 0 / 10%) 2px 2px 13px !important;
    }
    
    /*CARD*/
    #edit-collection-modal .modal-header h4, #new-collection-modal .modal-header h4 {
        margin-left: 150px;
    }
    
    #new-collection-modal .modal-header h4 {
        margin-left: 115px;
    }
    
    .modal-header {
        padding: 1.5rem;
    }
    
    #edit-collection-modal .modal-body, #new-collection-modal .modal-body {
        padding-left: 5rem;
        padding-right: 5rem;
    }
    
    .modal-footer {
        justify-content: center;
    }
    
    .col-md-4.card-content-container h5 {
        text-align: center;
    }
    
    .card-content-container .card-body {
        cursor: pointer;
    }
    
    .network-list i {
        font-size: 5rem;
        cursor: pointer;
    }
    
    .network-list {
        display: flex;
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
        // window.location.href = "https://newscascade.com/user/index.php?cmd=contentedit&collection_id=" + id;
    });
    
    $(".network-selected").click(function(e){
        let id = localStorage.getItem("collection_id");
        window.location.href = "https://newscascade.com/user/index.php?cmd=contentedit&collection_id=" + id;
    });
    
    // FOR EDIT
    $(".card-content-container .card-body").click(function(e){
        
        let id = $(this).data("content");
        let collection_id = $(this).data("collection");
        let action = $(this).data("action");
        
        if( action == "edit" ){
            window.location.href = "https://newscascade.com/user/index.php?cmd=contentedit&id=" + id + "&collection_id=" + collection_id;
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