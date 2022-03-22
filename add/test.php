<?php
    set_time_limit(0);
	error_reporting(0);
	session_start();
	
	// IMPORTANT DIRECTORY
	$dir = "../sys";
	$fp = opendir($dir);
	while(($file = readdir($fp)) != false){
		$file = trim($file);
		if(($file == ".") || ($file == "..")){continue;}
		$file_parts = pathinfo($dir."/".$file);
		if($file_parts["extension"] == "php"){
			include($dir . "/" . $file);
		}
	}
	closedir($fp);
	
	$DB = new db($dbhost, $dbuser, $dbpass, $dbname);
	$DB->connect();
	if($DB->connect < 1){
		echo "Can't go on, DB not initialized.";
		exit;
	}
	echo $DB;
	// WEBSITE VARIABLE
	$res = $DB->query("SELECT setup_key, setup_val FROM {$dbprefix}setup ORDER BY setup_id");
	foreach($res as $row){
		$WEBSITE[$row["setup_key"]] = $row["setup_val"];
	}
	
	$campaignID = $_GET['campaigns_id'];
	$_SESSION['campaigns_id'] = $campaignID;
	
	// GET THE CAMPAIGN
	$campaign = $DB->info( "campaigns", "campaigns_id = '{$campaignID}'" );
	
	// GET ALL THE CATEGORIES
	// FROM CONTENT
	$categoryGroupSql = "SELECT * FROM {$dbprefix}content GROUP BY category_id";
	$categoryGroup = $DB->query( $categoryGroupSql );
	
    $siteName       = "News Maximizer";
    $newTitle       = $campaign['campaigns_title'];
    $bannerHeroTitle= $campaign['campaigns_body'];
    $isSearch       = false;
    $numberOfMenu   = 3;
    $facebookLink   = $campaign['campaigns_facebook'];
    $twitterLink    = $campaign['campaigns_twitter'];
    $instagramLink  = $campaign['campaigns_instagram'];
    $youtubeLink    = $campaign['campaigns_youtube'];
    $bannerImage    = "../upload/{$campaign['user_id']}/".$campaign['campaigns_header_image'];
    
    $categories = array();
    foreach( $categoryGroup as $categoryData ){
        $category = $DB->query( "SELECT * FROM {$dbprefix}category WHERE category_id = '{$categoryData['category_id']}'" );
        if( $category ){
            $categories[] = [
                "name"  => $category[0]['category_name'],
                "key"   => strtolower( $category[0]['category_name'] ),
            ];
        }
        
    }
    
    // GET THE NEWS
    $newsData = array();
    foreach( json_decode($campaign['content_id']) as $content_id ){
        $content = $DB->info("content", "content_id = {$content_id}");
        $category = $DB->info("category", "category_id = {$content['category_id']}");
        $news = $DB->query( "SELECT * FROM {$dbprefix}news WHERE content_id = '{$content_id}'" );
        foreach( $news as $new ){
            $newsData[] = [
                "news_id"   => $new['news_id'],
                "category"  => $category['category_name'],
                "category_desc"  => $category['category_desc'],
                "status"    => $content['category_status'],
                "image"     => $new['news_image'],
                "thumbnail" => $new['news_thumbnail'],
                "title"     => $new['news_title'],
                "link"      => $new['news_link'],
                "date"      => $new['news_published_date'],
                "author"    => "Cameron Frew"
            ];
        }
        
    }

    // SEARCH VALUE
    $searchData = array();
    $searchValue = $_GET['search'];
    if(isset($searchValue))
    {
        
        $isSearch = true;
        foreach ($newsData as $key => $newsDataItem) {
            $isCategoryFound    = strpos($newsDataItem['category'], $searchValue);
            $isTitleFound       = strpos($newsDataItem['title'], $searchValue);
            
            if ($isCategoryFound !== false  || $isTitleFound !== false) {
                $searchData[] = $newsDataItem;
            }
        }
        if(empty($campaignID)){
            redirect("news.php?campaigns_id={$_POST['campaigns_id']}&search={$searchValue}");
        }
        
    }
    
    // FUNCTION TO SORT THE DATA BY DATE DESCENDING
    function compareByTimeStamp($time1, $time2)
    {
        if (strtotime($time1['date']) < strtotime($time2['date']))
            return 1;
        else if (strtotime($time1['date']) > strtotime($time2['date'])) 
            return -1;
        else
            return 0;
    }
    
    // LIMIT THE DATA FOR RECENT
    $mostRecent = $newsData;
    usort($mostRecent, "compareByTimeStamp");
    $recentLimit = 5;
    $limitedMostRecent = array();
    foreach($mostRecent as $key => $date){
        if ( $key < $recentLimit ) {
            $limitedMostRecent[] = $date;
        }
    }
    
    // GET RECOMMENDED NEWS ( NEW )
    $recommended = array();
    $recommendLimit = 5;
    foreach( $newsData as  $key => $data){
        if( $data['status'] === "New" ){
            $recommended[] = $data;
        }
    }
    
    $limitedRecommed = array();
    foreach($recommended as $key => $recommend){
        if ( $key < $recommendLimit ) {
            $limitedRecommed[] = $recommend;
        }
    }
    
    // GET NEWS DATA WITH CATEGORY
    $cagetory = $_GET['category'];
    $cagetoryLabel = "";
    $cagetoryDesc = "";
    $cagetoryLength =  strlen($cagetory);
    
    $categoriesResult = array();
    $isViewingCategory = false;
    if($cagetoryLength > 0){
        $isViewingCategory = true;
        foreach( $newsData as  $key => $data){
            if( strtolower( $data['category'] ) === strtolower( $cagetory ) ){
                $cagetoryLabel = $data['category'];
                $cagetoryDesc = $data['category_desc'];
                $categoriesResult[] = $data;
            }
        }
        
        $searchData = $categoriesResult;
    }
    
    // SINGLE POST
    $adsText = "Sample Text Ads";
    $newsID = $_GET['news'];
    $hasNewsPost = false;
    if(isset($newsID))
    {
        $hasNewsPost = true;
        $newsSql = "SELECT * FROM {$dbprefix}news WHERE news_id = '{$newsID}'";
        $newsData = $DB->query($newsSql)[0];
        
        $contentSql = "SELECT * FROM {$dbprefix}content WHERE content_id = '{$newsData['content_id']}'";
        $contentData = $DB->query($contentSql)[0];
        
        $adsSql = "SELECT * FROM {$dbprefix}ads2 WHERE ads_id = '{$contentData['ads_id']}'";
        $adsData = $DB->query($adsSql)[0];
    }
?>