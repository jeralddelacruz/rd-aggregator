<?php
    // ===================
    // INITIAL VARIABLES
    // ===================
	$user           =$DB->info("user","user_id='$UserID'");
	$campaigns_type = 'regular';
	$id             = $_GET["id"];
	$collection_id  = $_GET["collection"];
	$upload_dir     = "/upload/{$UserID}/news";
	
    // ===================
	// GET COLLECTIONS
    // ===================
    $content_collections = $DB->query("SELECT * FROM {$dbprefix}content_collection WHERE user_id='{$UserID}' {$and_query}");

    // ===================
    // GET FILTERED SEARCH
    // ===================
	$filter_search = "";
    if( isset( $_POST['search_input'] )){
        $filter_search          = empty($_POST['search_input']) ? "" : $_POST['search_input'];
        $filter_template        = $_GET['t'] ? $_GET['t'] : "";
        $filter_status          = "";
        
        if( !empty($_POST['filter_status']) ){
            $filter_status = $_POST['filter_status'];
        }else{
            $filter_status = $_GET['status'];
        }

        if( $filter_template ){
            $f_template = "&t=".$filter_template;
        }

        $content_collection_id  = $content_collections[0]['content_collection_id'];

        redirect("index.php?cmd=campaignstyle&id=$id&collection=$content_collection_id&status=$filter_status&search_input=$filter_search{$f_template}");
    }
	$filter_search = $_GET['search_input'] !== "" ? $_GET['search_input'] : "";
	$filter_status = $_GET['status'] ? $_GET['status'] : $filter_status;
	
    // ===================
    // DATABASE QUERIES
    // ===================
    $news = $DB->query("SELECT * FROM {$dbprefix}news WHERE users_id LIKE '%\"{$UserID}\"%' AND is_deleted='0'");
    
    // ===================
    // GET CURRENT USER NEWS UPDATE
    // ===================
    $news_ids   = array_column($news, "news_id"); // Get array of news ids
    $where_in   = "'" . implode("','", $news_ids) . "'"; // Implode for WHERE IN condition
    $user_news  = $DB->query("SELECT * FROM {$dbprefix}news_updates WHERE news_id IN ({$where_in}) AND user_id = '{$UserID}'");
    $user_news  = array_combine(array_column($user_news, 'news_id'), $user_news); // Set news_id as index
    $filtered_news = $news;
    if( !isset($collection_id) ) {
        $content_collection_id = $content_collections[0]['content_collection_id'];

        redirect("index.php?cmd=campaignstyle&id=$id&collection=$content_collection_id");
    }
    
    // ===================
    // TEMPLATE CONDITIONS TO SET THE TEMPLATE TO THE URL
    // ===================
    if( isset($_POST["templateNumber"]) ){
        $filter_search          = $_GET['s'] ? $_GET['status'] : "";
        $filter_status          = $_GET['status'] ? $_GET['status'] : "default";
        $content_collection_id  = $content_collections[0]['content_collection_id'];
        $filter_template        = "&t=".$_POST['templateNumber'];

        $f_srch = "";
        $f_status = "";
        $f_template = "";

        if( $filter_search ){
            $f_srch = "&s=$filter_search";
        }

        if( $filter_status ){
            $f_status = "&s=$filter_search";
        }

        redirect("index.php?cmd=campaignstyle&id=$id&collection=$content_collection_id{$f_srch}{$f_status}{$filter_template}");
    }

    if(isset($_POST['saveChanges'])){
        $campaigns_theme_text_color = $_POST['campaigns_theme_text_color'];
        $campaigns_theme_border_color = $_POST['campaigns_theme_border_color'];
        $campaigns_theme_bg_color = $_POST['campaigns_theme_bg_color'];
        $campaigns_theme_feed_color = $_POST['campaigns_theme_feed_color'];

        $update_campaigns = $DB->query("UPDATE {$dbprefix}campaigns SET 
            campaigns_theme_text_color = '{$campaigns_theme_text_color}',
            campaigns_theme_border_color = '{$campaigns_theme_border_color}',
            campaigns_theme_bg_color = '{$campaigns_theme_bg_color}',
            campaigns_theme_feed_color = '{$campaigns_theme_feed_color}'
            WHERE campaigns_id = {$id} AND user_id = '{$UserID}'");

        if($update_campaigns){

            $f_srch = "";
            $f_status = "";
            $f_template = "";

            if( $filter_search ){
                $f_srch = "&s=$filter_search";
            }
    
            if( $filter_status ){
                $f_status = "&s=$filter_search";
            }

            redirect("index.php?cmd=campaignstyle&id=$id&collection=$content_collection_id{$f_srch}{$f_status}{$filter_template}");
        }
        else{
        }
    }

    // ===================
    // STORE THE TEMPLATE TO A VARIABLE
    // ===================
    $selected_template = "template_1";
    if( $_GET["t"] ){
        $selected_template = $_GET["t"];
    }

    // ===================
    // GET THE STORED TEMPLATE SETTINGS
    // ===================
    $template_settings = $DB->query("SELECT * FROM {$dbprefix}template_settings WHERE user_id = '$UserID' AND campaign_id = '{$id}'")[0];

    // ===================
    // CHECK THE LOAD MORE COUNT
    // ===================
    $load_count = isset($_POST["loadmore"]) ? $_POST["loadmore"] + 1 : 1;

    // ===================
    // CONDITIONS TO DISPLAY THE SELECTED TEMPLATE
    // ===================
    if( $selected_template === "template_1" ){
        
        // NEWS CONTENTS
        // filter the news for template_1
        $featured   = 0;
        $trending   = 0;
        $new        = 0;
        
        // $featured_limit = !($load_count % 2 == 0) ? $load_count + 1 : $load_count + 2;
        $featured_limit = $load_count + 1;
        $trending_limit = $load_count;
        // $new_limit      = !($load_count % 2 == 0) ? $load_count + 1 : $load_count + 2 ;
        $new_limit      = $load_count + 1;

        if( $trending_limit == ($featured_limit - 1)){
            $featured_limit = $featured_limit - 1;
        }

        if( $trending_limit == ($new_limit - 1)){
            $new_limit = $new_limit - 1;
        }
        
        // Get filtered collection
    	$filter_collection = '';
        if( isset( $_POST['collections_filter'] )){
            $filter_collection = $_POST['collections_filter'];

            redirect("index.php?cmd=campaignstyle&id=$id&collection=$filter_collection&status=$filter_status");
        }
    	
    	$filter_collection      = $_GET['collection'] ? "AND nws.content_collection_id = '{$_GET['collection']}'" : "";
    	$filter_status_query    = $_GET['status'] ? "AND nws.status = '{$filter_status}'" : "";
    	$filter_search_query    = $_GET['search_input'] ? "AND nws.news_title LIKE '%".$filter_search."%'" : "";
    	
    	$additional_query = "";
    	if( $filter_search_query === "" ){
    	    $additional_query = $filter_collection . " " . $filter_status_query;

    	}else{
    	    $additional_query = $filter_search_query;

    	}
        
        // separate the news depends on the category
        $featured_result = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'Featured' {$additional_query} {$and_query} ORDER BY is_pinned DESC LIMIT {$featured_limit}");
        $trending_result = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'Trending' {$additional_query} {$and_query} ORDER BY is_pinned DESC LIMIT {$trending_limit}");
        $new_result      = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'New' {$additional_query} {$and_query} ORDER BY is_pinned DESC LIMIT {$new_limit}");
        
        $filtered_news = $featured_result;

    } elseif ( $selected_template === "template_2" ){
        // NEWS CONTENTS
        // filter the news for template_2
        
        // Get the latest news contents
        $content_collection_id  = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE campaigns_id = {$id}")[0]["content_collection_id"];
        $contents            = $DB->query("SELECT * FROM {$dbprefix}content WHERE content_collection_id = '{$content_collection_id}'");

        $content_ids = array();
        foreach ($contents as $key => $content) {
            $content_ids[] = $content["content_id"];
        }

        $latest_articles        = array();
        $array                  = implode(",", $content_ids);
        $filter_collection      = $_GET['collection'] ? "AND nws.content_collection_id = '{$_GET['collection']}'" : "";
    	$filter_status_query    = $_GET['status'] ? "AND nws.status = '{$filter_status}'" : "";
    	$filter_search_query    = $_GET['search_input'] ? "AND news_title LIKE '%".$filter_search."%'" : "";
    	
    	$additional_query = "";
    	if( $filter_search_query === "" ){
    	    $additional_query =  $filter_status_query;

    	}

        
        $latest_articles_result = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' {$additional_query} AND nws.content_id IN('".$array."') ORDER BY is_pinned DESC LIMIT 9");
        
        if( $filter_search_query !== "" ){
            $latest_articles_result = $DB->query("SELECT * FROM {$dbprefix}news WHERE content_id IN('".$array."') {$filter_search_query} LIMIT 9");
        }
        
        $categories             = $DB->query("SELECT category_id FROM {$dbprefix}content WHERE content_id IN('".$array."') GROUP BY category_id");

        foreach( $latest_articles_result as $key => $item ){
            $news_title = $item["news_title"];
            $news_image = "";
            
            if( $item["news_image"] != "[null]" || $item["news_image"] != '[""]' ) {
                $news_image = json_decode($item["news_image"])[0];
            }
            
            $latest_articles[] = [
                    "news_id"               => $item["news_id"],
                    "news_title"            => $news_title,
                    "news_image"            => $news_image,
                    "news_author"           => $item["news_author"],
                    "news_description"      => "",
                    "category"              => "Test",
                    "news_link"             => $item["news_link"],
                    "status"                => $item["status"],
                    "news_published_date"   => $item["news_published_date"],
            ];
        }

        $filtered_news = $latest_articles;

    } elseif ( $selected_template === "template_3" ){
        // NEWS CONTENTS
        // filter the news for template_3
        $featured   = 0;
        $trending   = 0;
        $new        = 0;
        
        // separate the news depends on the category
        $featured_result    = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'Featured'");
        $trending_result    = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'Trending'");
        $new_result         = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'New'");

        $filtered_news = $featured_result;
    }

    // ===================
    // FUNCTION TO GET USER NEWSFIELD BY USING NEWS_ID
    // ===================
    function getUserNews($news, $newsId, $column, $userId) {
        if (!$news[$newsId] || !$news[$newsId][$column]) {
            return null;
        }
            
        // Get image if news_update exists
        if (in_array($column, ["user_image", "post_image"])) {
            if ($column == "user_image") {
                $image_dir = "/upload/{$userId}/news/avatar/" . $news[$newsId][$column];
            } else {
                $image_dir = "/upload/{$userId}/news/images/" . $news[$newsId][$column];
            }
            
            return $image_dir;
        }
        
        return $news[$newsId][$column];
    }
?>