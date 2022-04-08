<style>
    .font-weight-bold {
        font-weight: 700!important;
    }
    .news-column {
        padding: 0;
    }
    
    .template-container .col-md-3 {
        padding: 0;
    }
    
    .trending_news_container {
        padding: 0;
    }
    
    .news-image-container.trending_news_container {
        height: 400px;
    }

    .smallnews.news-image-container {
        height: auto !important;
    }

    .smallnews {
        display: flex;
        align-items: center;
    }
</style>
<div class="col-md-12">
    <div class="template-container mt-3">
        <div class="row container m-auto">
            <div class="col-12 col-md-8">
                <div class="row">
                    <?php foreach( $filtered_news as $key => $filtered_new ): ?>
                    <?php
                        $news_id            = $filtered_new['news_id'];
                        $image              = $filtered_new['news_image'];
                        $avatar             = getUserNews($filtered_new, $news_id, "user_image", $UserID) ?? "https://farm5.staticflickr.com/4777/buddyicons/143966226@N06.jpg";
                        $news_title         = $filtered_new['news_title'];
                        $news_author        = $filtered_new['news_author'];
                        $news_date          = $filtered_new["news_published_date"];
                        $news_description   = "";
                        $status             = $filtered_new['status'];
                        $is_pinned          = $filtered_new['is_pinned'];
            
                    ?>
                        <?php if( $key >= 1 && $key <= 1 ): ?>
                            <div class="col-md-12 news-column pb-3">
                                <div id="news-<?php echo $news_id; ?>" class="news-container <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                                    <div class="news-config-container">
                                        <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $news_id ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $news_id ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0 <?= $is_pinned ? 'pin-color' : '' ?> pinNews"><i class="fas fa-thumbtack"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $news ?>'><i class="fa fa-pencil"></i></button>
                                    </div>
                                    <div class="news-image-container">
                                        <img src="<?= $image ?>">
                                    </div>
                                    <div class="news-content-container">
                                        <div class="news-heading-container">
                                            <h5><?= $news_title ?></h5>
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
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <hr>
                <div class="row">
                    <?php foreach( $filtered_news as $key => $item ): ?>
                        <?php if( $key >= 2 && $key <= 3 ): ?>
                            <div class="col-6">
                                <div id="news-<?= $item['news_id']; ?>" class="news-container <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                                    <div class="news-config-container">
                                        <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $item['news_id']; ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $item['news_id']; ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0 <?= $is_pinned ? 'pin-color' : '' ?> pinNews"><i class="fas fa-thumbtack"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $item ?>'><i class="fa fa-pencil"></i></button>
                                    </div>
                                    <div class="news-image-container">
                                        <img src="<?= $item["news_image"] ?>">
                                    </div>
                                    <div class="news-content-container">
                                        <div class="news-heading-container mb-3">
                                            <h5><?= $item['news_title']; ?></h5>
                                        </div>
                                        <div class="news-author-container">
                                            <p class="autor-name"><img src="<?php echo $avatar; ?>"> <span><?= $item['news_author']; ?></span></p>
                                            <p class="date-posted"><?= $item['news_published_date']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="row">
                    <?php foreach( $filtered_news as $key => $item ): ?>
                        <?php if( $key >= 4 && $key <= 5 ): ?>
                            <div class="col-12 pb-4">
                                <div id="news-<?= $item['news_id']; ?>" class="news-container <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                                    <div class="news-config-container">
                                        <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $item['news_id']; ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $item['news_id']; ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0 <?= $is_pinned ? 'pin-color' : '' ?> pinNews"><i class="fas fa-thumbtack"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $item ?>'><i class="fa fa-pencil"></i></button>
                                    </div>
                                    <div class="news-image-container">
                                        <img src="<?= $item["news_image"] ?>">
                                    </div>
                                    <div class="news-content-container">
                                        <div class="news-heading-container mb-3">
                                            <h5><?= $item['news_title']; ?></h5>
                                        </div>
                                        <div class="news-author-container">
                                            <p class="autor-name"><img src="<?php echo $avatar; ?>"> <span><?= $item['news_author']; ?></span></p>
                                            <p class="date-posted"><?= $item['news_published_date']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="row container mt-5 mx-auto">
            <div class="col-12 col-md-8">
                <div class="row">
                <?php foreach( $filtered_news as $key => $filtered_new ): ?>
                    <?php
                        $news_id            = $filtered_new['news_id'];
                        $image              = $filtered_new['news_image'];
                        $avatar             = getUserNews($filtered_new, $news_id, "user_image", $UserID) ?? "https://farm5.staticflickr.com/4777/buddyicons/143966226@N06.jpg";
                        $news_title         = $filtered_new['news_title'];
                        $news_author        = $filtered_new['news_author'];
                        $news_date          = $filtered_new["news_published_date"];
                        $news_description   = "";
                        $status             = $filtered_new['status'];
                        $is_pinned          = $filtered_new['is_pinned'];
            
                    ?>
                        <?php if( $key >= 1 && $key <= 1 ): ?>
                            <div class="col-md-12 news-column pb-3">
                                <div id="news-<?php echo $news_id; ?>" class="news-container <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                                    <div class="news-config-container">
                                        <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $news_id ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $news_id ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0 <?= $is_pinned ? 'pin-color' : '' ?> pinNews"><i class="fas fa-thumbtack"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $news ?>'><i class="fa fa-pencil"></i></button>
                                    </div>
                                    <div class="news-image-container">
                                        <img src="<?= $image ?>">
                                    </div>
                                    <div class="news-content-container">
                                        <div class="news-heading-container">
                                            <h5><?= $news_title ?></h5>
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
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="row">
                    <?php foreach( $filtered_news as $key => $item ): ?>
                        <?php if( $key >= 1 && $key <= 4 ): ?>
                            <div class="col-md-6">
                                <div class="row">
                                    <div id="news-<?= $item['news_id']; ?>" class="news-container <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                                        <div class="news-config-container">
                                            <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $item['news_id']; ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                                            <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $item['news_id']; ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                                            <button class="btn c-btn-primary btn-block m-0 btn-status <?= $is_pinned ? 'pin-color' : '' ?> pinNews" data-news-id="<?= $news_id ?>" data-action="pin"><i class="fas fa-thumbtack"></i></button>
                                            <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $item ?>'><i class="fa fa-pencil"></i></button>
                                        </div>
                                        <div class="row align-items-center"> 
                                            <div class="col-md-4 pr-0">
                                                <div class="smallnews news-image-container">
                                                    <img style="width: 88px; height: 88px; object-fit: cover;" src="<?= $item["news_image"]; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-8 p-0">
                                                <div class="news-content-container">
                                                    <div class="news-heading-container">
                                                        <h5><?= $item['news_title']; ?></h5>
                                                    </div>
                                                    <div class="news-author-container">
                                                        <p class="autor-name"><img src="<?php echo $avatar; ?>"> <span><?= $item['news_author']; ?></span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="row">
                    <?php foreach( $filtered_news as $key => $item ): ?>
                        <?php if( $key >= 4 && $key <= 5 ): ?>
                            <div class="col-12 pb-4">
                                <div id="news-<?= $item['news_id']; ?>" class="news-container <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                                    <div class="news-config-container">
                                        <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $item['news_id']; ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $item['news_id']; ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0 <?= $is_pinned ? 'pin-color' : '' ?> pinNews"><i class="fas fa-thumbtack"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $item ?>'><i class="fa fa-pencil"></i></button>
                                    </div>
                                    <div class="news-image-container">
                                        <img src="<?= $item["news_image"] ?>">
                                    </div>
                                    <div class="news-content-container">
                                        <div class="news-heading-container mb-3">
                                            <h5><?= $item['news_title']; ?></h5>
                                        </div>
                                        <div class="news-author-container">
                                            <p class="autor-name"><img src="<?php echo $avatar; ?>"> <span><?= $item['news_author']; ?></span></p>
                                            <p class="date-posted"><?= $item['news_published_date']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="row container mt-5 mx-auto">
            <div class="col-12 col-md-4">
                <div class="row">
                    <?php foreach( $filtered_news as $key => $item ): ?>
                        <?php if( $key >= 4 && $key <= 5 ): ?>
                            <div class="col-12 pb-4">
                                <div id="news-<?= $item['news_id']; ?>" class="news-container <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                                    <div class="news-config-container">
                                        <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $item['news_id']; ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $item['news_id']; ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0 <?= $is_pinned ? 'pin-color' : '' ?> pinNews"><i class="fas fa-thumbtack"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $item ?>'><i class="fa fa-pencil"></i></button>
                                    </div>
                                    <div class="news-image-container">
                                        <img src="<?= $item["news_image"] ?>">
                                    </div>
                                    <div class="news-content-container">
                                        <div class="news-heading-container mb-3">
                                            <h5><?= $item['news_title']; ?></h5>
                                        </div>
                                        <div class="news-author-container">
                                            <p class="autor-name"><img src="<?php echo $avatar; ?>"> <span><?= $item['news_author']; ?></span></p>
                                            <p class="date-posted"><?= $item['news_published_date']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-12 col-md-8">
                <div class="row">
                <?php foreach( $filtered_news as $key => $filtered_new ): ?>
                    <?php
                        $news_id            = $filtered_new['news_id'];
                        $image              = $filtered_new['news_image'];
                        $avatar             = getUserNews($filtered_new, $news_id, "user_image", $UserID) ?? "https://farm5.staticflickr.com/4777/buddyicons/143966226@N06.jpg";
                        $news_title         = $filtered_new['news_title'];
                        $news_author        = $filtered_new['news_author'];
                        $news_date          = $filtered_new["news_published_date"];
                        $news_description   = "";
                        $status             = $filtered_new['status'];
                        $is_pinned          = $filtered_new['is_pinned'];
            
                    ?>
                        <?php if( $key >= 1 && $key <= 1 ): ?>
                            <div class="col-md-12 news-column pb-3">
                                <div id="news-<?php echo $news_id; ?>" class="news-container <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                                    <div class="news-config-container">
                                        <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $news_id ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $news_id ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0 <?= $is_pinned ? 'pin-color' : '' ?> pinNews"><i class="fas fa-thumbtack"></i></button>
                                        <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $news ?>'><i class="fa fa-pencil"></i></button>
                                    </div>
                                    <div class="news-image-container">
                                        <img src="<?= $image ?>">
                                    </div>
                                    <div class="news-content-container">
                                        <div class="news-heading-container">
                                            <h5><?= $news_title ?></h5>
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
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="row">
                    <?php foreach( $filtered_news as $key => $item ): ?>
                        <?php if( $key >= 1 && $key <= 4 ): ?>
                            <div class="col-md-6">
                                <div class="row">
                                    <div id="news-<?= $item['news_id']; ?>" class="news-container <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                                        <div class="news-config-container">
                                            <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $item['news_id']; ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                                            <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $item['news_id']; ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                                            <button class="btn c-btn-primary btn-block m-0 btn-status <?= $is_pinned ? 'pin-color' : '' ?> pinNews" data-news-id="<?= $news_id ?>" data-action="pin"><i class="fas fa-thumbtack"></i></button>
                                            <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $item ?>'><i class="fa fa-pencil"></i></button>
                                        </div>
                                        <div class="row align-items-center"> 
                                            <div class="col-md-4 pr-0">
                                                <div class="smallnews news-image-container">
                                                    <img style="width: 88px; height: 88px; object-fit: cover;" src="<?= $item["news_image"]; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-8 p-0">
                                                <div class="news-content-container">
                                                    <div class="news-heading-container">
                                                        <h5><?= $item['news_title']; ?></h5>
                                                    </div>
                                                    <div class="news-author-container">
                                                        <p class="autor-name"><img src="<?php echo $avatar; ?>"> <span><?= $item['news_author']; ?></span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
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