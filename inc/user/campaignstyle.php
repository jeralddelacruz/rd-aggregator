<?php
	hasPageAccess("campaigns", $cur_pack["pack_ar"]);
    
    include("queries/campaignstyle_func.php");
?>

<!-- CODE_SECTION_HTML_2: CSS_EMBEDDED_DATATABLE -->
<link rel="stylesheet" href="../inc/user/styles/campaignstyle.css">

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

    <input type="hidden" id="user_id" value="<?= $UserID ?>">
    <input type="hidden" id="campaign_id" value="<?= $id ?>">
	
	<div id="ajax-alert" class="alert fade in ajax-alert">
        <span class="ajax-alert-message"></span>
        <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
	<div class="col-md-12 campaignstyle-action">
        <div class="btn-group">
            <a href="<?= $SCRIPTURL ?>add/news.php?campaigns_id=<?= $id ?>&collection=<?=$content_collection_id ?>&template=<?=$selected_template ?>" class="btn btn-warning" id="btn-campaign-preview" target="_blank"><i class="fa fa-eye"></i> Preview</a>
            <form method="POST">
                <!-- <input type="hidden" id="ctheme_color" name="campaigns_theme_color" value=""> -->
                <input type="hidden" id="ctheme_text_color" name="campaigns_theme_text_color" value="">
                <input type="hidden" id="ctheme_border_color" name="campaigns_theme_border_color" value="">
                <input type="hidden" id="ctheme_bg_color" name="campaigns_theme_bg_color" value="">
                <input type="hidden" id="ctheme_feed_color" name="campaigns_theme_feed_color" value="">
                <button type="submit" name="saveChanges" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
            </form>
        </div>
    </div>
	<div class="col-md-12">
		<div class="row">
        <div class="col-md-3">
		    <div class="row row-heading"></div>
		    <div class="card">
                <div class="card-header">
                <h3>Contents</h3>
                </div>		          
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
                                                <input type="number" class="form-control input feed-post-per-page" min="1" value="<?= $template_settings['feed_post_per_page'] ?>">
                                            </div>
                                        </li>
                                        <!-- <li Class="list-group-item">
                                            <div class="form-group">
                                                <label>Post Column</label>
                                                <select name="post-column" onchange="changeCol()" class="form-control input" id="post-column">
                                                    <option value="12" selected>1</option>
                                                    <option value="6">2</option>
                                                    <option value="4">3</option>
                                                    <option value="3">4</option>
                                                    <option value="2">6</option>
                                                    <option value="1">12</option>
                                                </select>
                                            </div>
                                        </li> -->
                                        <li Class="list-group-item">
                                            <div class="form-group">
                                                <label>Show Load More</label>
                                                <select name="load_more" class="form-control select feed-load-more" id="load-more">
                                                    <option value="true" <?= $template_settings['feed_load_more'] ? 'selected' : '' ?> >True</option>
                                                    <option value="false" <?= !$template_settings['feed_load_more'] ? 'selected' : '' ?> >False</option>
                                                </select>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="card">
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
                        </div> -->
                        <div class="card">
                            <div class="card-header" id="faqhead3">
                                <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq3"
                        aria-expanded="true" aria-controls="faq3">Colors / Appearance</a>
                            </div>
    
                            <div id="faq3" class="collapse" aria-labelledby="faqhead3" data-parent="#style-settings">
                                <div class="card-body">
                                    <ul class="list-group">
                                        <!-- <li Class="list-group-item">
                                            <div class="form-group">
                                                <label>Icon color</label>
                                                <input type="color" class="form-control color">
                                            </div>
                                        </li> -->
                                        <li Class="list-group-item">
                                            <div class="form-group">
                                                <label>Text color</label>
                                                <input type="color" id="textColor" value="<?= $template_settings['appearance_text_color'] ?>" class="form-control color appearance-text-color">
                                            </div>
                                        </li>
                                        <li Class="list-group-item">
                                            <div class="form-group">
                                                <label>Border color</label>
                                                <input type="color" id="borderColor" value="<?= $template_settings['appearance_border_color'] ?>" class="form-control color appearance-border-color">
                                            </div>
                                        </li>
                                        <li Class="list-group-item">
                                            <div class="form-group">
                                                <label>Background Color</label>
                                                <input type="color" id="bgColor" value="<?= $template_settings['appearance_bg_color'] ?>" class="form-control color appearance-bg-color">
                                            </div>
                                        </li>
                                        <li Class="list-group-item">
                                            <div class="form-group">
                                                <label>Feed background color</label>
                                                <input type="color" id="feedColor" value="<?= $template_settings['appearance_feed_bg_color'] ?>" class="form-control color appearance-feed-bg-color">
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
                                                        <!-- <option value="template_3" <?= $selected_template == "template_3" ? 'selected' : '' ?>>3</option> -->
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
		        <!-- <div class="collection-filter">
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
		        </div> -->
		        <div class="card" id="news-preview">
		            <div class="card-header">
		                <form role="form" method="POST">
    		                <div class="filters">
    		                    <div class="form-group">
    		                        <input type="text" class="form-control" name="search_input" value="<?= $_GET['search_input'] ?>" placeholder="Search...">
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
                                            include("../inc/user/content_templates/default.php"); 
                                            break;
                                        case "template_1":
                                            include("../inc/user/content_templates/template_1.php"); 
                                            break;
                                        case "template_2":
                                            include("../inc/user/content_templates/template_2.php"); 
                                            break;
                                        case "template_3":
                                            include("../inc/user/content_templates/template_3.php"); 
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
<script type="text/javascript" src="../../js/custom/campaignstyle.js"> </script>