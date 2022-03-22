<script src="../../assets/js/rss-feed-generator.js"></script>
<?php
    include(dirname(__FILE__) . "/../inc/simple_html_dom_v2.php");
	// CODE_SECTION_PHP_1: PRIVILEGE
	if(!preg_match(";content;", $cur_pack["pack_ar"])){
		// redirect("index.php?cmd=deny");
		// NOTE: COMMENTED-OUT BECAUSE content IS STILL NOT ADDED AS A PACKAGE.
	}
	
	$and_query = "";
	if( $user_subdomain ){
	    $subdomain_id = $user_subdomain["subdomain_id"];
	    $and_query = " AND subdomain_id = '{$subdomain_id}'";
	}else{
	    $and_query = " AND subdomain_id = 0";
	}
	
	$api_endpoint = 'https://api.rss2json.com/v1/api.json?rss_url=';
	$api_key="&api_key=6edlszdimfnisnbsrbxokautyipkvg1bpdn9nqe9";
	
	$content_data = $_SESSION['content_data'];
	$is_content_save = $_SESSION['is_content_save'];
	if( !$is_content_save && $content_data ){
	    $data = json_decode( $content_data );
	    $passed_id = $data->passed_id;
	    $feed_link = $data->feed_link;
	    $content_image = $data->content_image;
	    
	    getFeed( $api_endpoint, $feed_link, $UserID, $passed_id, $dbprefix, $DB, $content_image, $SCRIPTURL, $api_key );
        unset( $_SESSION['content_data']);
	    $_SESSION['is_content_save'] = true;
	}
	
	// METHOD TO GET THE FEED AND SAVE
	function getFeed( $api_endpoint, $feed_link, $UserID, $passed_id, $dbprefix, $DB, $uploaded_image, $SCRIPTURL, $api_key ){
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
            				news_description = '{$news_description}'
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
                        				news_description = '{$news_description}'
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
                        				news_description = '{$news_description}'
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
	
	// CODE_SECTION_PHP_3: DELETE_TO_DATABASE
	if(!empty($_GET["content_id"])){
		$content_id = $_GET["content_id"];

		$delete_content = $DB->query("DELETE FROM {$dbprefix}content WHERE content_id = '{$content_id}'");

		if($delete_content){
			$_SESSION["msg_success"] = "Content deleted.";

			redirect("index.php?cmd=content");
		}
	}
?>
<!-- CODE_SECTION_HTML_1: CDN_DATATABLE -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.js"></script>

<!-- CODE_SECTION_HTML_2: CSS_EMBEDDED_DATATABLE -->
<style type="text/css">
	.content-item .content-link{
		color: #666;
	}

	.content-item.active .content-link{
		background-color: #6c757d;
		border-color: #6c757d;
		color: #fff;
	}
	
	.sorting_1 img{
	    max-width: 100px;
	}
	
	.content-img {
        width: 100%;
        height: auto;
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
                        <a class="btn btn-outline-secondary" href="index.php?cmd=contentedit">Create New</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="content table-responsive table-full-width">
					<table class="table table-hover table-striped" id="content-table">
						<thead>
							<tr>
								<!--<th>Content Image</th>-->
								<th>Content Title</th>
								<th>Total Feeds</th>
								<th class="text-center">Edit</th>
								<th class="text-center">Delete</th>
							</tr>
						</thead>
						<tbody>
						<?php $contents = $DB->query("SELECT * FROM {$dbprefix}content WHERE user_id = '{$UserID}' {$and_query}"); ?>
						<?php foreach($contents as $content) : ?>
						<?php
						    $feed_link = $content["feed_link"];
						    $newsQuery = $DB->query("SELECT * FROM {$dbprefix}news WHERE rss_url = '{$feed_link}'");
						    $totalFeed = count($newsQuery);
						?>
							<tr>
								<!--<td><img class="content-img" src="../upload/<?= $UserID; ?>/<?= $content["content_image"]; ?>" alt="<?= $content["content_title"]; ?>"></td>-->
								<td><?= $content["content_title"]; ?></td>
								<td><?= $totalFeed; ?></td>
								<td class="text-center">
									<a href="index.php?cmd=contentedit&id=<?= $content["content_id"]; ?>" class="btn btn-secondary">
										<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;Edit
									</a>
								</td>
								<td class="text-center">
									<button class="btn btn-secondary" data-toggle="modal" data-target="#delete-modal" 
									data-content-id="<?= $content["content_id"]; ?>" 
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
			<div class="modal-body text-center">Are you sure you want to <b>DELETE</b> this content?</div>
			<div class="modal-footer">
				<a class="btn btn-danger" id="delete-button" href="" data-dismiss="modal">Yes I am sure</a>
			</div>
		</div>
	</div>
</div>

<!-- DATATABLE INITIALIZATION -->
<script type="text/javascript">
	$(document).ready(function(){
		$('#content-table').DataTable();
	});
</script>

<!-- DISPLAY data- ATTRIBUTES IN THE MODAL -->
<script type="text/javascript">
	function getAttributes(attributes){
		// DELETE CONFIRMATION
		var id_to_delete = attributes.getAttribute("data-content-id");
        console.log(id_to_delete)
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=content&content_id=${id_to_delete}`;
	}
</script>