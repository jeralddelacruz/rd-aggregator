<?php
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