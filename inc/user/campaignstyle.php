<?php
	// CODE_SECTION_PHP_1: PRIVILEGE
	if(!preg_match(";campaigns;", $cur_pack["pack_ar"])){
		redirect("index.php?cmd=deny");
	}
    // $UserID
    
	$user=$DB->info("user","user_id='$UserID'");
	$campaigns_type = 'regular';
	$id = $_GET["id"];
	$collection_id = $_GET["collection"];
	$upload_dir = "/upload/{$UserID}/news";
	
	// Get collections
    $content_collections = $DB->query("SELECT * FROM {$dbprefix}content_collection WHERE user_id='{$UserID}' {$and_query}");
    
    // Get filtered status
	$filter_search = "";
    if( isset( $_POST['search_input'] ) && $_POST['search_input'] !== "" ){
        $filter_search = $_POST['search_input'];
        $filter_status = $_GET['status'] ? $_GET['status'] : "default";
        $content_collection_id = $content_collections[0]['content_collection_id'];
        redirect("index.php?cmd=campaignstyle&id=$id&collection=$content_collection_id&status=$filter_status&s=$filter_search");
    }
	
	$filter_search = $_GET['s'] ? $_GET['s'] : "";
	
	// Get filtered status
	$filter_status = "approved";
    if( isset( $_POST['filter_status'] )){
        $filter_status = $_POST['filter_status'];
        $content_collection_id = $content_collections[0]['content_collection_id'];
        redirect("index.php?cmd=campaignstyle&id=$id&collection=$content_collection_id&status=$filter_status&s=$filter_search");
    }
	
	$filter_status = $_GET['status'] ? $_GET['status'] : "default";
	
	
    // Get all news
    $news = $DB->query("SELECT * FROM {$dbprefix}news WHERE users_id LIKE '%\"{$UserID}\"%' AND is_deleted='0' {$additional_query}  {$and_query}");
    
    // Get current user news_updates
    $news_ids = array_column($news, "news_id"); // Get array of news ids
    $where_in = "'" . implode("','", $news_ids) . "'"; // Implode for WHERE IN condition
    $user_news = $DB->query("SELECT * FROM {$dbprefix}news_updates WHERE news_id IN ({$where_in}) AND user_id = '{$UserID}' {$and_query}");
    $user_news = array_combine(array_column($user_news, 'news_id'), $user_news); // Set news_id as index
    
    // Function to get user news field by using news_id
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
    
    $filtered_news = $news;
    
    if( !isset($collection_id) ) {
        $content_collection_id = $content_collections[0]['content_collection_id'];
        redirect("index.php?cmd=campaignstyle&id=$id&collection=$content_collection_id");
    }
    
    // Added else if for template 2 and 3
    // March 17 2022
    // TEMPLATE

    $selected_template = isset($_POST["templateNumber"]) ? filter_input(INPUT_POST, 'templateNumber', FILTER_SANITIZE_STRING) : "template_1";

    // Check the Load Count
    $load_count = isset($_POST["loadmore"]) ? $_POST["loadmore"] + 1 : 2;

    if( $selected_template === "template_1" ){
        
        // NEWS CONTENTS
        // filter the news for template_1
        $featured = 0;
        $trending = 0;
        $new = 0;
        
        $featured_limit = !($load_count % 2 == 0) ? $load_count + 1 : $load_count + 2;
        $trending_limit = $load_count;
        $new_limit = !($load_count % 2 == 0) ? $load_count + 1 : $load_count + 2 ;
        
        // Get filtered collection
    	$filter_collection = '';
        if( isset( $_POST['collections_filter'] )){
            $filter_collection = $_POST['collections_filter'];
            redirect("index.php?cmd=campaignstyle&id=$id&collection=$filter_collection&status=$filter_status");
        }
    	
    	$filter_collection = $_GET['collection'] ? "AND nws.content_collection_id = '{$_GET['collection']}'" : "";
    	
    	$filter_status_query = $_GET['status'] ? "AND nws.status = '{$filter_status}'" : "";
    	
    	$filter_search_query = $_GET['s'] ? "AND nws.news_title LIKE '%".$filter_search."%'" : "";
    	
    	$additional_query = "";
    	if( $filter_search_query === "" ){
    	    $additional_query = $filter_collection . " " . $filter_status_query;
    	}else{
    	    $additional_query = $filter_search_query;
    	}
        
        // separate the news depends on the category
        $featured_result = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'Featured' {$additional_query} {$and_query} LIMIT {$featured_limit}");
        $trending_result = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'Trending' {$additional_query} {$and_query} LIMIT {$trending_limit}");
        $new_result = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'New' {$additional_query} {$and_query} LIMIT {$new_limit}");
        // echo json_encode( $featured_result, true );
        
        $filtered_news = $featured_result;

    } elseif ( $selected_template === "template_2" ){
        // NEWS CONTENTS
        // filter the news for template_2
        
        // Get the latest news contents
        $content_ids = json_decode( $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE campaigns_id = '{$id}'")[0]["content_id"] );
        $content_category_ids = json_decode( $DB->query("SELECT * FROM {$dbprefix}content WHERE campaigns_id = '{$id}'")[0]["content_id"] );
        
        $latest_articles = array();
        $array = implode("','",$content_ids);
        $latest_articles_result = $DB->query("SELECT * FROM {$dbprefix}news WHERE content_id IN('".$array."') LIMIT 9");
        
        $categories = $DB->query("SELECT * FROM {$dbprefix}category WHERE user_id = '{$UserID}'");
        // echo count($latest_articles_result);
        // foreach( $latest_articles_result as $key => $item ){
        //     $news_title = $item["news_title"];
        //     $news_image = "";
            
        //     if( $item["news_image"] != "[null]" || $item["news_image"] != '[""]' ) {
        //         $news_image = json_decode($item["news_image"])[0];
        //     }
            
        //     $latest_articles[] = [
        //             "news_id"       => $item["news_id"],
        //             "news_title"    => $news_title,
        //             "news_image"    => $news_image,
        //             "news_author"   => $item["news_author"],
        //             "news_description"  => "",
        //             "category"      => "Test",
        //             "news_link"     => $item["news_link"],
        //     ];
        // }
        
        
        // echo json_encode( $latest_articles );
        $featured = 0;
        $trending = 0;
        $new = 0;
        
        
        
        // separate the news depends on the category
        $featured_result = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'Featured'");
        $trending_result = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'Trending'");
        $new_result = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'New'");
        // echo json_encode( $featured_result, true );

        $filtered_news = $featured_result;

    } elseif ( $selected_template === "template_3" ){
                // NEWS CONTENTS
        // filter the news for template_3
        $featured = 0;
        $trending = 0;
        $new = 0;
        
        // separate the news depends on the category
        $featured_result = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'Featured'");
        $trending_result = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'Trending'");
        $new_result = $DB->query("SELECT nws.* FROM {$dbprefix}content cnt INNER JOIN {$dbprefix}news nws ON cnt.content_id = nws.content_id AND cnt.user_id = '{$UserID}' AND cnt.category_status = 'New'");
        // echo json_encode( $featured_result, true );

        $filtered_news = $featured_result;
    }
    
?>

<!-- CODE_SECTION_HTML_2: CSS_EMBEDDED_DATATABLE -->
<link rel="stylesheet" href="../inc/user/Styles/campaignstyle.css">

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
	
	<div id="ajax-alert" class="alert fade in ajax-alert">
        <span class="ajax-alert-message"></span>
        <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
	
	<div class="col-md-12">
		<div class="row">

        <!-- Uncomment march 17 2022 -->
       <div class="col-md-3">
		      <div class="row row-heading">
		      </div>
		      <h3 class="mb-2">Contents</h3>
		      <div class="card">
		          <div class="card-body">
		                
		              <div class="accordion" id="style-settings">
                          <div class="card">
                              <div class="card-header" id="faqhead1">
                                  <a href="#" class="btn btn-header-link" data-toggle="collapse" data-target="#faq1"
                               aria-expanded="true" aria-controls="faq1">Feed</a>
                              </div>
        
                              <div id="faq1" class="collapse show" aria-labelledby="faqhead1" data-parent="#style-settings">
                                  <div class="card-body">
                                      <ul class="list-group">
                                          <li Class="list-group-item">
                                              <div class="form-group">
                                                  <label>Post Per page</label>
                                                  <input type="number" class="form-control input" min="1">
                                              </div>
                                          </li>
                                          <li Class="list-group-item">
                                              <div class="form-group">
                                                  <label>Post Column</label>
                                                  <select name="post-column" class="form-control input" id="post-column">
                                                      <option value="12" selected>1</option>
                                                      <option value="6">2</option>
                                                      <option value="4">3</option>
                                                      <option value="3">4</option>
                                                      <option value="2">6</option>
                                                      <option value="1">12</option>
                                                  </select>
                                              </div>
                                          </li>
                                          <li Class="list-group-item">
                                              <div class="form-group">
                                                  <label>Show Load More</label>
                                                  <select name="load_more" class="form-control select" id="load-more">
                                                      <option value="true">True</option>
                                                      <option value="false">False</option>
                                                  </select>
                                              </div>
                                          </li>
                                      </ul>
                                  </div>
                              </div>
                          </div>
                          <div class="card">
                              <div class="card-header" id="faqhead2">
                                  <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq2"
                            aria-expanded="true" aria-controls="faq2">Filter</a>
                              </div>
        
                              <div id="faq2" class="collapse" aria-labelledby="faqhead2" data-parent="#style-settings">
                                  <div class="card-body">
                                      <ul class="list-group">
                                          <li Class="list-group-item">
                                              <div class="form-group">
                                                  <label>Show 'custom' filter</label>
                                                  <select name="load_more" class="form-control select" id="show-custom-filter">
                                                      <option value="false">False</option>
                                                      <option value="true">True</option>
                                                  </select>
                                              </div>
                                          </li>
                                          <li Class="list-group-item">
                                              <div class="form-group">
                                                  <label>Show 'networks' filter</label>
                                                  <select name="load_more" class="form-control select" id="show-network-filter">
                                                      <option value="false">False</option>
                                                      <option value="true">True</option>
                                                  </select>
                                              </div>
                                          </li>
                                      </ul>
                                  </div>
                              </div>
                          </div>
                          <div class="card">
                              <div class="card-header" id="faqhead3">
                                  <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq3"
                           aria-expanded="true" aria-controls="faq3">Colors / Appearance</a>
                              </div>
        
                              <div id="faq3" class="collapse" aria-labelledby="faqhead3" data-parent="#style-settings">
                                  <div class="card-body">
                                      <ul class="list-group">
                                          <li Class="list-group-item">
                                              <div class="form-group">
                                                  <label>Icon color</label>
                                                  <input type="color" class="form-control color">
                                              </div>
                                          </li>
                                          <li Class="list-group-item">
                                              <div class="form-group">
                                                  <label>Text color</label>
                                                  <input type="color" id="textColor" value="#000000" class="form-control color">
                                              </div>
                                          </li>
                                          <li Class="list-group-item">
                                              <div class="form-group">
                                                  <label>Border color</label>
                                                  <input type="color" id="borderColor" value="#000000" class="form-control color">
                                              </div>
                                          </li>
                                          <li Class="list-group-item">
                                              <div class="form-group">
                                                  <label>Background Color</label>
                                                  <input type="color" id="bgColor" value="#000000" class="form-control color">
                                              </div>
                                          </li>
                                          <li Class="list-group-item">
                                              <div class="form-group">
                                                  <label>Feed background color</label>
                                                  <input type="color" id="feedColor" value="#000000" class="form-control color">
                                              </div>
                                          </li>
                                          <li Class="list-group-item">
                                              <div class="form-group">
                                                   <button onClick="resetColors();">Reset</button>
                                              </div>
                                          </li>
                                      </ul>
                                  </div>
                              </div>
                          </div>
                          <div class="card">
                              <div class="card-header" id="faqhead4">
                                  <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq4"
                               aria-expanded="true" aria-controls="faq3">Template Settings</a>
                              </div>
        
                              <div id="faq4" class="collapse" aria-labelledby="faqhead4" data-parent="#style-settings">
                                  <div class="card-body">
                                      <ul class="list-group">
                                          <form method="POST">
                                          <li Class="list-group-item">
                                                  <div class="form-group">
                                                      <label>Template</label>
                                                      <select name="templateNumber" class="form-control input" id="template-column">
                                                          <option value="template_1" <?= $selected_template == "template_1" ? 'selected' : '' ?>>1</option>
                                                          <option value="template_2" <?= $selected_template == "template_2" ? 'selected' : '' ?>>2</option>
                                                           <option value="template_3" <?= $selected_template == "template_3" ? 'selected' : '' ?>>3</option>
                                                      </select>
                                                  </div>
                                              </li>
                                              <li Class="list-group-item text-right">
                                                  <button type="submit" class="btn btn-primary btn-apply">Apply</button>
                                              </li>
                                          </form>
                                      </ul>
                                  </div>
                              </div>
                          </div>
                      </div>
		          </div>
		      </div>
		    </div>

            <!--If your going to uncomment the sidepanel just change the col from 'col-md-12' to 'col-md-9'-->
		    <div class="col-md-9">
		        <div class="collection-filter">
		            <div class="form-group">
		                <form role="form" method="POST">
    		                <select name="collections_filter" class="form-control" onchange='this.form.submit()'>
    		                    <?php foreach( $content_collections as $content_collection ): ?>
                                    <option value="<?= $content_collection['content_collection_id'] ?>" <?= $_GET['collection'] == $content_collection['content_collection_id'] ? 'selected' : '' ?>><?= $content_collection["collection_name"] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <noscript><input type="submit" name="filter_collection_submit" value="Submit"></noscript>
                        </form>
		            </div>
		            <a href="index.php?cmd=campaigns" class="btn btn-secondary">
		                <i class="fas fa-angle-left"></i> Back to Campaigns
		            </a>
		        </div>
		        <div class="card" id="news-preview">
		            <div class="card-header">
		                <form role="form" method="POST">
    		                <div class="filters">
    		                    <div class="form-group">
    		                        <input type="text" class="form-control" name="search_input" placeholder="Search...">
    		                        <button type="submit" class="btn btn-primary c-btn-search">Search</button>
    		                        <select name="filter_status" class="form-control" onchange='this.form.submit()'>
    		                            <option value="default" <?= $filter_status == "default" ? 'selected' : '' ?> disabled>Status: </option>
    		                            <option value="need_approval" <?= $filter_status == "need_approval" ? 'selected' : '' ?>>Need Approval</option>
    		                            <option value="approved" <?= $filter_status == "approved" ? 'selected' : '' ?>>Approved</option>
    		                            <option value="rejected" <?= $filter_status == "rejected" ? 'selected' : '' ?>>Rejected</option>
    		                        </select>
    		                        
    		                        <!--<select name="filter_network" class="form-control">-->
    		                        <!--    <option value="all">All</option>-->
    		                        <!--    <option value="Facebook">Facebook</option>-->
    		                        <!--    <option value="Instagram">Instagram</option>-->
    		                        <!--</select>-->
    		                        
    		                        <!--<select name="filter_source" class="form-control">-->
    		                        <!--    <option value="all">All</option>-->
    		                        <!--    <option value="niche">Niche</option>-->
    		                        <!--    <option value="qq">https://test.com/rss</option>-->
    		                        <!--</select>-->
    		                        <noscript><input type="submit" name="filter_status_submit" value="Submit"></noscript>
    		                    </div>
    		                </div>
    		            </form>
		            </div>
		            <div id="cardBody" class="card-body">
		                <div class="row">
		                    <div class="col-md-12 filter d-none">
		                        <div class="custom-filter">
		                            <p class="pr-2">#tags</p> |&nbsp;
		                            <p class="pr-2">https://text.com/rss</p>
		                        </div>      
		                    </div>
		                    <div class="col-md-12 networks d-none">
		                        <div class="network-filter">
		                            <p class="pr-2">Facebook</p> | &nbsp;
		                            <p class="pr-2">Twitter</p>| &nbsp;
		                            <p class="pr-2">Youtube</p>
		                        </div>    
		                    </div>
		                    <!--<div class="col-md-12">-->
		                    <!--    <div class="row">-->
		                    <!--        <div class="col-3">-->
		                    <!--            test-->
        		            <!--        </div>-->
        		            <!--        <div class="col-6">-->
        		            <!--            test-->
        		            <!--        </div>-->
        		            <!--        <div class="col-3">-->
        		            <!--            test-->
        		            <!--        </div>-->
		                    <!--    </div>    -->
		                    <!--</div>-->
		                    <?php if( count($filtered_news) > 0 ): ?>
    		                <?php 
    		                    switch($selected_template) {
                                    case "default":
                                        include("../inc/user/Content_Templates/default.php"); 
                                        break;
                                    case "template_1":
                                        include("../inc/user/Content_Templates/template_1.php"); 
                                        break;
                                    case "template_2":
                                         include("../inc/user/Content_Templates/template_2.php"); 
                                         break;
                                    case "template_3":
                                        include("../inc/user/Content_Templates/template_3.php"); 
                                         break;
                                  default:
                                    // code block
                                }
    		                    
    		                ?>
		                    <?php else: ?>
		                        <div class="col-12">
    		                        <div class="no-data-container">
    		                            <p>No News content found!</p>
    		                        </div>
    		                    </div>
		                    <?php endif; ?>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
	</div>
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
					<a class="btn btn-outline-secondary" href="index.php?cmd=campaignsedit&campaigns_type=dfy">A Done For You</a>
					<a class="btn btn-outline-secondary" href="index.php?cmd=campaignsedit&campaigns_type=regular">Regular Campaign</a>
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

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal fade" id="limit-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Campaigns Exceeded</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body text-center">You already have <b>2</b> campaigns, If you wan't to create more campaigns.</div>
			<div class="modal-footer">
				<a class="btn btn-danger" id="upgrade-button" href="index.php?cmd=buy-upgrades" data-dismiss="modal">Check Upgrades</a>
			</div>
		</div>
	</div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="edit-modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			    <h4>Edit News</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			
		    <form id="edit-form">
    			<div class="modal-body">
    			    <div class="row">
    			        <div class="col-md-5">
    			            <div class="news-container" id="modal-edit-container">
	                            <div class="news-config-container">
	                                <!--<button type="button" class="btn c-btn-primary m-0"><i class="fa fa-times"></i></button>-->
	                            </div>
	                            <div class="news-image-container">
	                                <img src="" id="news-image-preview">
	                            </div>
	                            <div class="news-content-container">
	                                <div class="news-heading-container">
	                                    <h5></h5>
	                                </div>
	                                <div class="news-detail-container">
	                                    <p></p>
	                                </div>
	                                <div class="news-author-container">
	                                    <p class="autor-name"><img src="" id="user-image-preview"/><span></span></p>
	                                    <p class="date-posted"></p>
	                                </div>
	                            </div>
	                        </div>
    			        </div>
    			        <div class="col-md-7">
    			            <input type="hidden" class="form-control" id="news_id" value="">
                            <div class="form-group">
                                <label for="name" class="col-form-label">Name:</label>
                                <input type="text" class="form-control" id="name">
                            </div>
                            <div class="form-group">
                                <label for="user_image" class="col-form-label">User Image:</label>
                                <input type="file" class="form-control" id="user_image">
                            </div>
                            <div class="form-group">
                                <label for="image" class="col-form-label">News Image:</label>
                                <input type="file"class="form-control" id="image">
                            </div>
                            <div class="form-group">
                                <label for="video_url" class="col-form-label">Video URL:</label>
                                <input type="url" class="form-control" id="video_url">
                            </div>
                            <div class="form-group">
                                <label for="title" class="col-form-label">Title:</label>
                                <input type="text" class="form-control" id="title">
                            </div>
                            <div class="form-group">
                                <label for="description" class="col-form-label">Description:</label>
                                <textarea class="form-control" id="description"></textarea>
                            </div>
    			        </div>
    			    </div>
			    </div>
    			<div class="modal-footer">
    			    <button class="btn btn-primary" type="submit">Save</button>
    			</div>
    		</form>
		</div>
	</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<!-- Custom Script -->
<script type="text/javascript">
	// $("#post-column").change(function(){
    //     let selected = $(this).val()
    //     let news_classname = $(".news-column").attr("class");
    //     let classname = news_classname.replace("news-column pb-4", "");
    //     $(".news-column").removeClass(classname);
    //     $(".news-column").addClass("col-md-"+selected);
    // })
    
    $("#load-more").change(function(){
        let selected = $(this).val()
        if( selected === "true" ) {
            $(".load-more-container button").removeClass("d-none");
        } else {
            $(".load-more-container button").addClass("d-none");
        }
    })
    
    $("#show-custom-filter").change(function(){
        let selected = $(this).val()
        if( selected === "true" ) {
            $(".filter").removeClass("d-none");
        } else {
            $(".filter").addClass("d-none");
        }
    })
    
    $("#show-network-filter").change(function(){
        let selected = $(this).val()
        if( selected === "true" ) {
            $(".networks").removeClass("d-none");
        } else {
            $(".networks").addClass("d-none");
        }
    })
    
    $("#faq4 .btn-apply").on('click', function(){
        // alert("Template applied");
    })
    
    $(".news-author-container .date-posted").map(function(index, item) {
        item.innerText = moment(item.innerText).fromNow();
    });
    
    let editModal = $("#edit-modal");
    let editForm = $("#edit-form");
    let alertModal = $("#ajax-alert");
    let alertTimeout = null;
    
    // Fields
    const authorField = editModal.find("#name");
    const titleField = editModal.find("#title");
    const descriptionField = editModal.find("#description");
    
    // Preview Card
    const newsImage = editModal.find(".news-image-container img");
    const newsTitle = editModal.find(".news-heading-container h5");
    const newsDescription = editModal.find(".news-detail-container p");
    const authorImage = editModal.find(".news-author-container .autor-name img");
    const authorName = editModal.find(".news-author-container .autor-name span");
    const newsDate = editModal.find(".news-author-container .date-posted");
    
    const fieldArray = [
        authorField,
        titleField,
        descriptionField
    ];
    
    const previewArray= [
        authorName,
        newsTitle,
        newsDescription
    ];
    
    fieldArray.map((field, index) => {
       field.on('keyup', function () {
           previewArray[index].text(field.val());
       }) 
    });
    
    function editNews(news) {
        // alert("Under Development");
        // return false;
        
        let data = JSON.parse(news.getAttribute('data-json'));
        
        if (!data || !data.news_id) {
            return false;
        }
        
        editModal.find("#news_id").val(data.news_id);
        authorField.val(data.news_author);
        titleField.val(data.news_title);
        descriptionField.val(data.news_description);
        
        newsImage.attr("src", data.post_image);
        newsTitle.text(data.news_title);
        newsDescription.text(data.news_description);
        authorImage.attr("src", data.user_image);
        authorName.text(data.news_author);
        newsDate.text(moment(data.created_at).fromNow());
        editModal.modal().show();
    }
    
    function pinNews(news) {
        alert("Under Development");
        return false;
    }
    
    function loadMore(news) {
        // alert("Under Development");
        // return false;
    }
    
    function showAlert(alert, message, success = true) {
        alertModal.removeClass('alert-danger alert-success');
        alertModal.find('.ajax-alert-message').text(message);
        
        if (success) {
            alertModal.addClass('alert-success'); 
        } else {
            alertModal.addClass('alert-danger'); 
        }
        
        alert.addClass('show');
        
        alertTimeout = setTimeout(function() {
            alert.removeClass('show');
        }, 8000);
    }
    
    function updateData(news) {
        let newsContainer = $('#news-' + news.news_id);
        
        if (newsContainer) {
            let upload_dir = "<?php echo $upload_dir; ?>";
            
            if (news.post_image) {
                newsContainer.find('.news-image-container img').attr("src", upload_dir + '/images/' + news.post_image);
            }
            
            if (news.user_image) {
                newsContainer.find('.news-author-container .autor-name img').attr("src", upload_dir + '/avatar/' + news.user_image);
            }
            
            newsContainer.find('.news-heading-container h5').text(news.title);
            newsContainer.find('.news-detail-container p').text(news.description);
            newsContainer.find('.news-author-container .autor-name span').text(news.name);
        }
    }
    
    alertModal.find('.close').on('click', function () {
        if (alertTimeout) {
            clearTimeout(alertTimeout);
        }
        alertModal.removeClass('show'); 
    });
    
    editModal.on('hidden.bs.modal', function () {
        editModal.find("#news_id").val("");
        editModal.find("#name").val("");
        editModal.find("#description").val("");
        editModal.find("#title").val("");
    });
    
    editModal.on('submit', function (evt) {
        evt.preventDefault();
        
        let formData = new FormData();
        formData.append('action', "edit");
        formData.append('user_id', <?php echo $UserID; ?>);
        formData.append('news_id', editModal.find("#news_id").val());
        formData.append('news_author', editModal.find("#name").val());
        formData.append('news_title', editModal.find("#title").val());
        formData.append('news_description', editModal.find("#description").val());
        formData.append('user_image', editModal.find("#user_image").get(0).files[0]);
        formData.append('image', editModal.find("#image").get(0).files[0]);
        formData.append('video_url', editModal.find("#video_url").val());
        
        $.ajax({
            url: "/api/news.php",
            method: "POST",
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                editModal.modal('hide');
                showAlert(alertModal, response.message, response.success);
                updateData(response.data);
            },
            error: function (response) {
                showAlert(alertModal, response.responseJSON.message, response.responseJSON.success);
            }
        });
    });
    
    $(".btn-status").on('click', function (evt) {
        evt.preventDefault();
        
        const news_id = $(this).data("news-id");
        const action = $(this).data("action");
        
        var container = $($(this).parent().parent()[0]);
        var rejectBtn = $(this).next();
        var rejectBtn1 = $(this).prev();
        var currentBtn = $(this);
        
        $.ajax({
            url: "/api/news_status.php",
            method: "POST",
            dataType: "json",
            data: {
                action: action,
                news_id
            },
            success: function (response) {
                if( response.success ) {
                    if( action == "approved" ) {
                        container.removeClass("blur rejected-border")
                        rejectBtn.removeClass("reject-color")
                        currentBtn.addClass("approve-color")
                    } else {
                        container.addClass("blur rejected-border")
                        currentBtn.addClass("reject-color")
                        currentBtn.prev().removeClass("approve-color")
                    }
                    
                }
                // TO DO - Update Success
                console.log(response);
            },
            error: function (err) {
                console.log(err)
            }
        });
    });
    
    window.onload = function(){
        // FOR USER AVATAR
		var user_image = document.getElementById("user_image");
		var user_image_preview = document.getElementById("user-image-preview");

		function readFile1(input){

			if(input.files && input.files[0]){
				var file_reader = new FileReader();

				file_reader.onload = function(e){
					user_image_preview.src = e.target.result;
				}

				file_reader.readAsDataURL(input.files[0]);
			}
		}

		user_image.oninput = function(){
			readFile1(this);
		}
		
        // FOR NEWS CONTENT IMAGE
        var news_image = document.getElementById("image");
		var news_image_preview = document.getElementById("news-image-preview");

		function readFile2(input){

			if(input.files && input.files[0]){
				var file_reader = new FileReader();

				file_reader.onload = function(e){
					news_image_preview.src = e.target.result;
				}

				file_reader.readAsDataURL(input.files[0]);
			}
		}

		news_image.oninput = function(){
			readFile2(this);
		}
	}

    var textColor;
    var defaultTextColor = "#000000";

    window.addEventListener("load", startTextChange, false);

    function startTextChange() {
        textColor = document.querySelector("#textColor");
        textColor.value = defaultTextColor;
        textColor.addEventListener("change", updateTextColor, false);
        textColor.select();
    }

    function updateTextColor(event) {
        document.querySelectorAll("p").forEach(function(p) {
            p.style.color = event.target.value;
        });
        document.querySelectorAll("h5").forEach(function(h5) {
            h5.style.color = event.target.value;
        });
    }

    var borderColor;
    var defaultBorderColor = "#000000";

    window.addEventListener("load", startBorderChange, false);

    function startBorderChange() {
        borderColor = document.querySelector("#borderColor");
        borderColor.value = defaultBorderColor;
        borderColor.addEventListener("change", updateBorderColor, false);
        borderColor.select();
    }

    function updateBorderColor(event) {
        document.querySelectorAll(".news-container").forEach(function(newscontainer) {
            newscontainer.style.borderColor = event.target.value;
        });
    }

    var bgColor;
    var defaultBgColor = "#000000";

    window.addEventListener("load", startBgChange, false);

    function startBgChange() {
        bgColor = document.querySelector("#bgColor");
        bgColor.value = defaultBgColor;
        bgColor.addEventListener("change", updateBgColor, false);
        bgColor.select();
    }

    function updateBgColor(event) {
        document.querySelectorAll(".news-container").forEach(function(newscontainer) {
            newscontainer.style.backgroundColor = event.target.value;
        });
    }

    var feedColor;
    var defaultFeedColor = "#000000";

    window.addEventListener("load", startFeedChange, false);

    function startFeedChange() {
        feedColor = document.querySelector("#feedColor");
        feedColor.value = defaultFeedColor;
        feedColor.addEventListener("change", updateFeedColor, false);
        feedColor.select();
    }

    function updateFeedColor(event) {
        var cardBody = document.querySelector("#cardBody");

        if (cardBody) {
            cardBody.style.backgroundColor = event.target.value;
        }
    }

    function resetColors() {
        var defaultBgColor = "#000000";

        document.querySelectorAll("p").forEach(function(p) {
            p.style.color = '';
        });
        document.querySelectorAll("h5").forEach(function(h5) {
            h5.style.color = '';
        });
        document.querySelectorAll(".news-container").forEach(function(newscontainer) {
            newscontainer.style.backgroundColor = '';
            newscontainer.style.borderColor = '';
        });
        var cardBody = document.querySelector("#cardBody");

        if (cardBody) {
            cardBody.style.backgroundColor = '';
        }

        document.querySelector("#textColor").value = defaultBgColor;
        document.querySelector("#borderColor").value = defaultBgColor;
        document.querySelector("#bgColor").value = defaultBgColor;
        document.querySelector("#feedColor").value = defaultBgColor;
    }

    // <option value="12">1</option>
    // <option value="6">2</option>
    // <option value="4">3</option>
    // <option value="3">4</option>
    // <option value="2">6</option>
    // <option value="1">12</option>

    document.addEventListener("change", () => {
        var c = document.getElementById("post-column");
        var pCol = c.value;

        console.log(typeof pCol);

        switch (pCol) {
            case '12': {
                document.querySelectorAll(".selectPostCol").forEach(function(selectPostCol) {
                    selectPostCol.classList.remove('col-md-1');
                    selectPostCol.classList.remove('col-md-2');
                    selectPostCol.classList.remove('col-md-3');
                    selectPostCol.classList.remove('col-md-4');
                    selectPostCol.classList.remove('col-md-6');
                    selectPostCol.classList.remove('col-md-12');
                    selectPostCol.classList.add('col-md-12');
                    console.log(selectPostCol.getAttribute("class"));
                });
            }
                break;
            case '6': {
                document.querySelectorAll(".selectPostCol").forEach(function(selectPostCol) {
                    selectPostCol.classList.remove('col-md-1');
                    selectPostCol.classList.remove('col-md-2');
                    selectPostCol.classList.remove('col-md-3');
                    selectPostCol.classList.remove('col-md-4');
                    selectPostCol.classList.remove('col-md-6');
                    selectPostCol.classList.remove('col-md-12');
                    selectPostCol.classList.add('col-md-6');
                    console.log(selectPostCol.getAttribute("class"));
                });
            }   
                break;         
            case '4': {
                document.querySelectorAll(".selectPostCol").forEach(function(selectPostCol) {
                    selectPostCol.classList.remove('col-md-1');
                    selectPostCol.classList.remove('col-md-2');
                    selectPostCol.classList.remove('col-md-3');
                    selectPostCol.classList.remove('col-md-4');
                    selectPostCol.classList.remove('col-md-6');
                    selectPostCol.classList.remove('col-md-12');
                    selectPostCol.classList.add('col-md-4');
                    console.log(selectPostCol.getAttribute("class"));
                });
            }     
                break;       
            case '3': {
                document.querySelectorAll(".selectPostCol").forEach(function(selectPostCol) {
                    selectPostCol.classList.remove('col-md-1');
                    selectPostCol.classList.remove('col-md-2');
                    selectPostCol.classList.remove('col-md-3');
                    selectPostCol.classList.remove('col-md-4');
                    selectPostCol.classList.remove('col-md-6');
                    selectPostCol.classList.remove('col-md-12');
                    selectPostCol.classList.add('col-md-3');
                    console.log(selectPostCol.getAttribute("class"));
                });
            }
                break;
            case '2': {
                document.querySelectorAll(".selectPostCol").forEach(function(selectPostCol) {
                    selectPostCol.classList.remove('col-md-1');
                    selectPostCol.classList.remove('col-md-2');
                    selectPostCol.classList.remove('col-md-3');
                    selectPostCol.classList.remove('col-md-4');
                    selectPostCol.classList.remove('col-md-6');
                    selectPostCol.classList.remove('col-md-12');
                    selectPostCol.classList.add('col-md-2');
                    console.log(selectPostCol.getAttribute("class"));
                });
            }
                break;
            case '1': {
                document.querySelectorAll(".selectPostCol").forEach(function(selectPostCol) {
                    selectPostCol.classList.remove('col-md-1');
                    selectPostCol.classList.remove('col-md-2');
                    selectPostCol.classList.remove('col-md-3');
                    selectPostCol.classList.remove('col-md-4');
                    selectPostCol.classList.remove('col-md-6');
                    selectPostCol.classList.remove('col-md-12');
                    selectPostCol.classList.add('col-md-1');
                    console.log(selectPostCol.getAttribute("class"));
                });
            }
                break;
        
            default: 
                break;
        }
    });

    
</script>