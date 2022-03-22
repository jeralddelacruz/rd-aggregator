
<?php
    error_reporting(0);
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    
    ini_set('max_execution_time', 0);
    // including the sys files being able to connect DB
    include(dirname(__FILE__) . "/../sys/class.db.php");
    include(dirname(__FILE__) . "/../sys/config.php");
    include(dirname(__FILE__) . "/../sys/func.php");
    include(dirname(__FILE__) . "/../inc/simple_html_dom_v2.php");
    require_once(dirname(__FILE__) . '/../inc/admin/cpanel.php');

    $api_endpoint = 'https://api.rss2json.com/v1/api.json?rss_url=';
    $api_key="&api_key=6edlszdimfnisnbsrbxokautyipkvg1bpdn9nqe9";
    
    // connecting DB
    $DB=new db($dbhost,$dbuser,$dbpass,$dbname);
    $DB->connect();
    // if there are ploblems, just exit
    if($DB->connect<1){
        exit;
    }

    // loading Site Setup, to be used in validate functions
    $res = $DB->query("select setup_key,setup_val from $dbprefix"."setup order by setup_id");
    foreach( $res as $row ){
        $WEBSITE[$row["setup_key"]]=$row["setup_val"];
    }
	
	// CHECK THE CONTENT
	$content = $DB->query("SELECT * FROM {$dbprefix}content");
    
	if( count($content) > 0 ){
	    foreach($content as $content_data){
	        $isUpdated = false;
	        
	        $rss_url = $content_data['feed_link'];
            $data = json_decode( file_get_contents($api_endpoint . urlencode($rss_url) . $api_key) , true );
        
            $content_image = $content_data['content_image'];
            
            if($data['status'] == 'ok'){
                // get all of the same content and get the user id's
                // $contents = $DB->query("SELECT * FROM {$dbprefix}content WHERE feed_link = '{$rss_url}'");
                // $user_id = array();
                // foreach( $contents as $content_item){
                //     $user_id
                // }
                
                foreach ($data['items'] as $item) {
                    // INITIALIZE VARIABLES NEEDED
                    $user_id   = $content_data['user_id'];
                	$campaign_id  = null;
                	$content_id  = $content_data['content_id'];
                	$news_link   = $item['link'];
                	$network   = $item['network'];
                	$status   = $item['Status'];
                	
                	$news_image   = $data['feed']['image'];
                	$news_thumbnail   = $item['thumbnail'];
                	$news_published_date   = $item['pubDate'];
                	$news_title   = $item['title'];
                	$news_author   = $item['author'];
                	$news_content       = base64_encode($item['content']);
            	    $news_description   = base64_encode($item['description']);
                	
                    $content = $DB->info("news", "news_title = '{$news_title}' AND user_id ='{$user_id}' AND content_id='$content_id'");
                    
                    if( empty( $content ) ){
                        
                        if( $news_thumbnail || $content_image  ){
                            $imagePath = $SCRIPTURL."upload/".$user_id.'/'.$content_image;
                            // $image = $news_thumbnail ? $news_thumbnail : $imagePath;
                            
                            $image = $imagePath;
                            $temp_users_id = array();
                            $temp_users_id[] = $user_id;
                            $temp_users_id = json_encode( $temp_users_id );
                            
                            if( $content_image === ""){
                                $image = null;
                            }
                            // Create DOM from URL or file
                            
                            if( !$news_thumbnail ){
                                try {
                                    $html = file_get_html2($news_link);
                                    if( $html ){
                                        $images = array();
                                        // Find all images
                                        foreach($html->find('img') as $element){
                                            $imgSrc = $element->src;
                                            if (strpos($imgSrc, 'https') !== false) {
                                                // CHECK THE IMAGE RESOLUTION FIRST
                                                $size = getimagesize($imgSrc);
                                                $width = str_replace('"','',$size[3]);
                                                $new = str_replace(' height','',explode("width=",$width)[1]);
                                                $new2 = explode("=", $new)[0];
                                                if( $new2 >= 150){
                                                    $images[] = $imgSrc;
                                                }
                                            }
                                        }
                                        
                                        $news_thumbnail = json_encode($images);
                                    }
                                    
                                } catch (Exception $e) {
                                    echo "<script>console.log('".$e->getMessage()."')</script>";
                                    echo $e->getMessage();
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
                            $insert_sql =  "INSERT INTO {$dbprefix}news SET 
                	            user_id = '{$temp_users_id}',
                	            rss_url = '{$rss_url}',
                				content_id = '{$content_id}',
                				news_link = '{$news_link}',
                				news_image = '{$news_thumbnail}',
            				    uploaded_image = '{$image}',
                				news_title = '{$news_title}',
                				news_author = '{$news_author}',
                				news_content = '{$news_content}',
                				news_description = '{$news_description}',
                				network = '{$network}',
                				status = '{$status}'
                            ";
                            
                            if( strlen($news_content) > 100 ){
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
                            }
                        }
                    }
                }
            }
	    }
	}
?>