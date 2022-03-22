<?php
    $campaignID = $_GET['campaigns_id'];
	$_SESSION['campaigns_id'] = $campaignID;
	// ============== GET THE CAMPAIGN ============== //
	$campaign = $DB->query( "SELECT * FROM {$dbprefix}campaigns WHERE campaigns_id = '{$campaignID}' $and_query")[0];
	
	// ============== GET ALL THE CATEGORIES FROM CONTENT ============== //
	$categoryGroupSql = "SELECT * FROM {$dbprefix}content GROUP BY category_id $and_query";
	$categoryGroup = $DB->query( $categoryGroupSql );
	
	// ============== GLOBAL VARIABLE INITIALIZATION ============== //
    $siteName       = "News Maximizer";
    $newTitle       = $campaign['campaigns_title'];
    $bannerHeroTitle= $campaign['campaigns_body'];
    $isSearch       = false;
    $numberOfMenu   = 4;
    $facebookLink   = $campaign['campaigns_facebook'];
    $twitterLink    = $campaign['campaigns_twitter'];
    $instagramLink  = $campaign['campaigns_instagram'];
    $youtubeLink    = $campaign['campaigns_youtube'];
    $bannerImage    = $campaign['campaigns_header_image'];
    $callToActionTitle = $campaign['c2a_title'];
    $callToActionButtonText = $campaign['c2a_btn_text'];
    $callToActionButtonLink = $campaign['c2a_btn_link'];
    $optinTitle = $campaign['optin_title'];
    $optinBtnTitle = $campaign['optin_btn_title'];
    $responderImg = $campaign['campaigns_responder_image'];
    
    // ============== CHECK THE CATEGORIES ============== //
    $catSql = "SELECT * FROM {$dbprefix}category WHERE user_id = '{$campaign['user_id']}' $and_query";
    $categoryGroupData = $DB->query( $catSql );
    $categories = array();
    foreach( $categoryGroupData as $categoryData ){
        $categories[] = [
            "name"  => $categoryData['category_name'],
            "key"   => strtolower( $categoryData['category_name'] ),
        ];
    }
    
    // ============== GET THE NEWS ============== //
    // $content_ids = array();
    // $content_ids = json_decode( $campaign["content_id"] );
    $campaign_collection_id = $campaign['content_collection_id'];
    $content_ids = $DB->query( "SELECT content_id FROM {$dbprefix}content WHERE content_collection_id = '{$campaign_collection_id}'" );
    
    foreach( $content_ids as $content_id ){
        // get content
        $contents_id = (int)$content_id["content_id"];
        $content = $DB->info("content", "content_id = $contents_id");
        $rss_url = $content["feed_link"];
        
        $category = $DB->info("category", "category_id = {$content['category_id']}");
        $news = $DB->query( "SELECT * FROM {$dbprefix}news WHERE rss_url = '{$rss_url}' ORDER BY created_at DESC;" );
        
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
        
        // get news by feedlink
        $newsData2 = array();
        foreach($newsData as $newData){
            $newsItemImage = $newData['image'];
                                        
            if( $newsItemImage == "manual" ){
                $cacheFile = "cache/".md5($newData['news_id']).".cache";
        	    if(file_exists($cacheFile) && (filemtime($cacheFile) > (time() - ($cacheTimeSeconds)))){
        	        $newsItemImage = file_get_contents($cacheFile);
        	        echo "<script>console.log('if')</script>";
        	    }else{
        	        echo "<script>console.log('else')</script>";
        	        $html = file_get_html( $newData['link'] );
                    $newsItemImage = fetchImage( $html, $newData['news_id'] );
        	    }
                
            }
            
            $newsData2[] = [
                "news_id"   => $newData['news_id'],
                "category"  => $newData['category'],
                "category_desc"  => $newData['category_desc'],
                "status"    => $newData['status'],
                "image"     => $newsItemImage,
                "uploaded_image" => $newData['uploaded_image'],
                "title"     => $newData['title'],
                "link"      => $newData['link'],
                "date"      => $newData['date'],
                "author"    => $newData['author']
            ];
        }
        $newsData = $newsData2;
    }
    
    // echo json_encode( $newsData );
    
    // $sqlCacheName = md5($campaign['campaigns_id']) . ".cache";
    // $cache = 'cache';
    // $cacheFile = $cache . "/" . $sqlCacheName;
    // $cacheTimeSeconds = (60 * 60);
    // $fileContents = file_get_contents($cacheFile);
    // // echo $fileContents;
    // // echo "<script>alert('".$fileContents."')</script>";
    
    // $newsData = json_decode($fileContents, true);
    // $newsData2 = array();
    // //If the file exists and the filemtime time is larger than
    // //our cache expiry time.
    // if(file_exists($cacheFile) && (filemtime($cacheFile) > (time() - ($cacheTimeSeconds)))){
    //     $fileContents = file_get_contents($cacheFile);
    //     $newsData = json_decode($fileContents, true);
    //     foreach($newsData as $newData){
    //         $newsItemImage = $newData['image'];
                                        
    //         if( $newsItemImage == "manual" ){
    //             $cacheFile = "cache/".md5($newData['news_id']).".cache";
    //     	    if(file_exists($cacheFile) && (filemtime($cacheFile) > (time() - ($cacheTimeSeconds)))){
    //     	        $newsItemImage = file_get_contents($cacheFile);
    //     	        echo "<script>console.log('if')</script>";
    //     	    }else{
    //     	        echo "<script>console.log('else')</script>";
    //     	        $html = file_get_html( $newData['link'] );
    //                 $newsItemImage = fetchImage( $html, $newData['news_id'] );
    //     	    }
                
    //         }
    //         echo json_encode( $newData );
    //         $newsData2[] = [
    //             "news_id"   => $newData['news_id'],
    //             "category"  => $newData['category'],
    //             "category_desc"  => $newData['category_desc'],
    //             "status"    => $newData['status'],
    //             "image"     => $newsItemImage,
    //             "uploaded_image" => $newData['uploaded_image'],
    //             "title"     => $newData['title'],
    //             "link"      => $newData['link'],
    //             "date"      => $newData['date'],
    //             "author"    => $newData['author']
    //         ];
    //     }
    //     $newsData = $newsData2;
    // }

    // ============== SEARCH VALUE ============== //
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
    
    // ============== FUNCTION TO SORT THE DATA BY DATE DESCENDING ============== //
    function compareByTimeStamp($time1, $time2)
    {
        if (strtotime($time1['date']) < strtotime($time2['date']))
            return 1;
        else if (strtotime($time1['date']) > strtotime($time2['date'])) 
            return -1;
        else
            return 0;
    }
    
    // ============== LIMIT THE DATA FOR RECENT ============== //
    $mostRecent = $newsData;
    usort($mostRecent, "compareByTimeStamp");
    $recentLimit = 5;
    $limitedMostRecent = array();
    foreach($mostRecent as $key => $date){
        if ( $key < $recentLimit ) {
            $limitedMostRecent[] = $date;
        }
    }
    
    // ============== GET RECOMMENDED NEWS ( NEW ) ============== //
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
    
    // ============== GET NEWS DATA WITH CATEGORY ============== //
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
    
    // ============== LIMIT THE DATA FOR RECENT ============== //
    usort($newsData, "compareByTimeStamp");
    
    // ============== SINGLE POST ============== //
    $adsText = "Sample Text Ads";
    $newsID = $_GET['news'];
    $hasNewsPost = false;
    if(isset($newsID))
    {
        $hasNewsPost = true;
        $newsSql = "SELECT * FROM {$dbprefix}news WHERE news_id = '{$newsID}'";
        $newsData = $DB->query($newsSql)[0];
        
        $contentSql = "SELECT * FROM {$dbprefix}content WHERE content_id = '{$newsData['content_id']}' $and_query";
        $contentData = $DB->query($contentSql)[0];
        
        $bannerAdsSql = "SELECT * FROM {$dbprefix}ads2 WHERE ads_id = '{$contentData['banner_ads_id']}' AND ads_type='Banner' $and_query";
        $bannerAdsData = $DB->query($bannerAdsSql)[0];
        
        $sidebarAdsSql = "SELECT * FROM {$dbprefix}ads2 WHERE ads_id = '{$contentData['sidebar_ads_id']}' AND ads_type='Sidebar' $and_query";
        $sidebarAdsData = $DB->query($sidebarAdsSql)[0];
        
    }
    
    // ============== COUNT THE DATA FOR FEATURED ============== //
    $featuredCount = 0;
    foreach ($newsData as $key => $newsDataItem) { 
        if ($newsDataItem['status'] == 'Featured') {
            $featuredCount++;
        }
    }
    
    // ============== COUNT THE DATA FOR TRENDING ============== //
    $trendingCount = 0;
    foreach ($newsData as $key => $newsDataItem) { 
        if ($newsDataItem['status'] == 'Trending') {
            $trendingCount++;
        }
    }
    
    // ============== COUNT THE DATA FOR FEATURED ============== //
    $newCount = 0;
    foreach ($newsData as $key => $newsDataItem) { 
        if ($newsDataItem['status'] == 'New') {
            $newCount++;
        }
    }
    
    // ============== STORE THE COUNTS INTO ARRAY ============== //
    $countOfArrays = [$featuredCount, $newCount];
    
    // ============== GET THE MIN NUMBER OF AN ARRAY VALUE ============== //
    $minNumber = min( $countOfArrays );
    
    $newMinNumber = 0;
    $totalTrendingCount = 0;
    $newTotalTrendingCount = 0;
    for($i = 1; $i <= $trendingCount; $i++){
         
        if ($i % 2) {
            //   echo '$i is odd';
            if( $newMinNumber <= $minNumber ){
                $newMinNumber++;
                $newTotalTrendingCount += 2;
            }
        } else {
            //   echo '$i is even';
            if( $newMinNumber <= $minNumber ){
                $newMinNumber += 2;
            }
        }
        
    }
    
    $minNumber = $newMinNumber;
    if( $newTotalTrendingCount % 2){
        $newTotalTrendingCount -= 1;
    }
    $totalTrendingCount = $newTotalTrendingCount;
    
    if( $totalTrendingCount < 4 ){
        $totalTrendingCount = 4;
        $minNumber = 6;
    }
    
    // FOR POPUP DETAILS
    $popup_data = $DB->query("SELECT * FROM " . $dbprefix . "popup WHERE popup_id = '" . $campaign['popup_id'] . "' $and_query");
    
    if( isset($_POST['btn_save']) ){
        $email = strip($_POST['email_responder']);
        autoResponder( $DB, $campaign, $email );
    }
	
	function fetchImage( $html, $news_id ){
	    $cacheFile = "cache/".md5($news_id).".cache";
        if(file_exists($cacheFile) && (filemtime($cacheFile) > (time() - ($cacheTimeSeconds)))){
            $news_thumbnail = file_get_contents($cacheFile);
        }else{
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
                        if( $new2 > 600){
                            $images[] = $imgSrc;
                        }
                    }
                }
                
                $news_thumbnail = json_encode($images);
                file_put_contents($cacheFile, $news_thumbnail);
            }
            return $news_thumbnail;
        }
	}
?>