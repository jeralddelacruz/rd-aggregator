<?php
    $user=$DB->info("user","user_id='$UserID'");
	$campaigns_type = 'regular';
	
	$DFYAuthorID = $WEBSITE["dfy_author"];
	// CODE_SECTION_PHP_3: DELETE_TO_DATABASE
	if(!empty($_GET["delete"])){
		$campaigns_id = $_GET["campaigns_id"];

		$delete_campaign = $DB->query("DELETE FROM {$dbprefix}campaigns WHERE campaigns_id = '{$campaigns_id}'");

		if($delete_campaign){
			$_SESSION["msg_success"] = "Campaign deleted.";

			redirect("index.php?cmd=campaigns");
		}
	}
	
	$user_subdomains = $DB->query("SELECT * FROM {$dbprefix}user_subdomain WHERE user_id = '{$UserID}'");
	
	$and_query = "";
	if( $user_subdomain ){
	    $subdomain_id = $user_subdomain["subdomain_id"];
	    $and_query = " AND subdomain_id = '{$subdomain_id}'";
	}else{
	    $and_query = " AND subdomain_id = 0";
	}
	
	$campaigns = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = '{$UserID}' AND campaigns_type = '$campaigns_type' {$and_query}");
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
    }
?>