<style>
.font-weight-bold {
    font-weight: 700!important;
}
.news-column {
    /* padding-bottom: 1rem; */
    padding: 0;
}

.trending_news_container {
    padding: 0;
}

.news-image-container.trending_news_container {
    height: 400px;
}
</style>


<div class="col-md-12">
    <div class="template-container">
        <div class="row m-auto ">
            <div class="selectPostCol col-md-6 p-0">
                 <h2 class=" font-weight-bold p-3">Featured</h2>
            


            <div class="col-md-12 pl-0">
                <div class="news-column pb-3">
                <?php foreach( $featured_result as $filtered_new ): ?>
                <?php
                    $news_id = $filtered_new['news_id'];
                    $image = getUserNews($user_news, $news_id, "post_image", $UserID) ?? json_decode($filtered_new["news_image"])[0];
                    $avatar = getUserNews($user_news, $news_id, "user_image", $UserID) ?? "https://farm5.staticflickr.com/4777/buddyicons/143966226@N06.jpg";
                    $news_title = getUserNews($user_news, $news_id, "title", $UserID) ?? $filtered_new['news_title'];
                    $news_author = getUserNews($user_news, $news_id, "name", $UserID) ?? $filtered_new['news_author'];
                    $news_date = getUserNews($user_news, $news_id, "created_at", $UserID) ?? $filtered_new["news_published_date"];
                    $news_description = getUserNews($user_news, $news_id, "description", $UserID) ?? "";
                    $status = $filtered_new['status'];
                    $is_pinned = $filtered_new['is_pinned'];
                    
                    $filtered_new['news_title'] = $news_title;
                    $filtered_new['news_description'] = $news_description;
                    $filtered_new['news_author'] = $news_author;
                    $filtered_new['post_image'] = $image;
                    $filtered_new['user_image'] = $avatar;
                    $filtered_new['created_at'] = $news_date;
                    $news = json_encode($filtered_new);
                ?>
                <div id="news-<?php echo $news_id; ?>" class="news-container mb-3 <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                            <div class="news-config-container">
                                <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $news_id ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                                <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $news_id ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                                <button class="btn c-btn-primary btn-block m-0 <?= $is_pinned ? 'pin-color' : '' ?>" onClick="pinNews(this)"><i class="fas fa-thumbtack"></i></button>
                                <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $news ?>'><i class="fa fa-pencil"></i></button>
                            </div>
                <div class="news-image-container">
                    <img src="<?= $image ?>">
                </div>
                <div class="news-content-container">
                    <div class="news-heading-container">
                        <h5 class="font-weight-bold pt-3"><?= $news_title ?></h5>
                        </div>
                        
                        <div class="news-detail-container">
                            <p><?= $news_description ?></p>
                        </div>
                        
                        <div class="news-author-container">
                        <p class="autor-name"><img src="<?php echo $avatar; ?>"> <span><?= $news_author ?></span></p>
                         <p class="date-posted"><?= $news_date ?></p>
                         </div>
                </div>
            </div>
            <?php endforeach; ?>
               </div>
          </div>
          </div>

          <div class="selectPostCol col-md-3 p-0">
                 <h2 class="font-weight-bold p-3">Trending</h2>
            
            <div class="col-md-12 p-0">
                <div class="news-column pb-3">
                <?php foreach( $trending_result as $filtered_new ): ?>
                <?php
                    $news_id = $filtered_new['news_id'];
                    $image = getUserNews($user_news, $news_id, "post_image", $UserID) ?? json_decode($filtered_new["news_image"])[0];
                    $avatar = getUserNews($user_news, $news_id, "user_image", $UserID) ?? "https://farm5.staticflickr.com/4777/buddyicons/143966226@N06.jpg";
                    $news_title = getUserNews($user_news, $news_id, "title", $UserID) ?? $filtered_new['news_title'];
                    $news_author = getUserNews($user_news, $news_id, "name", $UserID) ?? $filtered_new['news_author'];
                    $news_date = getUserNews($user_news, $news_id, "created_at", $UserID) ?? $filtered_new["news_published_date"];
                    $news_description = getUserNews($user_news, $news_id, "description", $UserID) ?? "";
                    $status = $filtered_new['status'];
                    $is_pinned = $filtered_new['is_pinned'];
                    
                    $filtered_new['news_title'] = $news_title;
                    $filtered_new['news_description'] = $news_description;
                    $filtered_new['news_author'] = $news_author;
                    $filtered_new['post_image'] = $image;
                    $filtered_new['user_image'] = $avatar;
                    $filtered_new['created_at'] = $news_date;
                    $news = json_encode($filtered_new);
                ?>
                <div id="news-<?php echo $news_id; ?>" class="news-container mb-3 <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                            <div class="news-config-container">
                                <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $news_id ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                                <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $news_id ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                                <button class="btn c-btn-primary btn-block m-0 <?= $is_pinned ? 'pin-color' : '' ?>" onClick="pinNews(this)"><i class="fas fa-thumbtack"></i></button>
                                <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $news ?>'><i class="fa fa-pencil"></i></button>
                            </div>
                            <div class="news-image-container">
                            <img src="<?= $image ?>">
                </div>
                <div class="news-content-container">
                    <div class="news-heading-container">
                     <h5 class="font-weight-bold pt-3"><?= $news_title ?></h5>
                        </div>
                        
                        <div class="news-detail-container">
                        <p><?= $news_description ?></p>
                        </div>
                        
                        <div class="news-author-container">
                        <p class="autor-name"><img src="<?php echo $avatar; ?>"> <span><?= $news_author ?></span></p>
                         <p class="date-posted"><?= $news_date ?></p>
                         </div>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            </div>
            </div>

            <div class="selectPostCol col-md-3 p-0">
                 <h2 class="font-weight-bold p-3">New</h2>
            
            <div class="col-md-12 pr-0">
                <div class="news-column pb-3">
                <?php foreach( $new_result as $filtered_new ): ?>
                <?php
                    $news_id = $filtered_new['news_id'];
                    $image = getUserNews($user_news, $news_id, "post_image", $UserID) ?? json_decode($filtered_new["news_image"])[0];
                    $avatar = getUserNews($user_news, $news_id, "user_image", $UserID) ?? "https://farm5.staticflickr.com/4777/buddyicons/143966226@N06.jpg";
                    $news_title = getUserNews($user_news, $news_id, "title", $UserID) ?? $filtered_new['news_title'];
                    $news_author = getUserNews($user_news, $news_id, "name", $UserID) ?? $filtered_new['news_author'];
                    $news_date = getUserNews($user_news, $news_id, "created_at", $UserID) ?? $filtered_new["news_published_date"];
                    $news_description = getUserNews($user_news, $news_id, "description", $UserID) ?? "";
                    $status = $filtered_new['status'];
                    $is_pinned = $filtered_new['is_pinned'];
                    
                    $filtered_new['news_title'] = $news_title;
                    $filtered_new['news_description'] = $news_description;
                    $filtered_new['news_author'] = $news_author;
                    $filtered_new['post_image'] = $image;
                    $filtered_new['user_image'] = $avatar;
                    $filtered_new['created_at'] = $news_date;
                    $news = json_encode($filtered_new);
                ?>
                <div id="news-<?php echo $news_id; ?>" class="news-container mb-3 <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                            <div class="news-config-container">
                                <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $news_id ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                                <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $news_id ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                                <button class="btn c-btn-primary btn-block m-0 <?= $is_pinned ? 'pin-color' : '' ?>" onClick="pinNews(this)"><i class="fas fa-thumbtack"></i></button>
                                <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $news ?>'><i class="fa fa-pencil"></i></button>
                            </div>
                            <div class="news-image-container">
                            <img src="<?= $image ?>">
                </div>
                <div class="news-content-container">
                    <div class="news-heading-container">
                     <h5 class="font-weight-bold pt-3"><?= $news_title ?></h5>
                        </div>
                        
                        <div class="news-detail-container">
                        <p><?= $news_description ?></p>
                        </div>
                        
                        <div class="news-author-container">
                        <p class="autor-name"><img src="<?php echo $avatar; ?>"> <span><?= $news_author ?></span></p>
                         <p class="date-posted"><?= $news_date ?></p>
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
<div class="col-12">
    <div class="load-more-container">
        <form method="POST">
            <input type="hidden" name="loadmore" value="<?= $load_count ?>">
            <button type="submit" class="btn btn-secondary">LOAD MORE</button>
        </form>
    </div>
</div>
 
