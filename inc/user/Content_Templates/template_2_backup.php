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
</style>
<div class="container mt-3">
    <div class="row">
        <div class="col-12 col-md-8">
            <div class="row">
                <div class="col-12">
                    <div id="news-<?= $latest_articles_result[0]["news_id"]; ?>" class="news-container <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                        <div class="news-config-container">
                            <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $latest_articles_result[0]["news_id"]; ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                            <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $latest_articles_result[0]["news_id"]; ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                            <button class="btn c-btn-primary btn-block m-0 <?= $is_pinned ? 'pin-color' : '' ?>" onClick="pinNews(this)"><i class="fas fa-thumbtack"></i></button>
                            <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $latest_articles_result[0] ?>'><i class="fa fa-pencil"></i></button>
                        </div>
                        <div class="news-image-container">
                            <img class="col-12" src="<?= $latest_articles_result[0]["news_image"] != "[null]" || $latest_articles_result[0]["news_image"] != '[""]' ? json_decode($latest_articles_result[0]["news_image"])[0] : '' ?>">
                        </div>
                        <div class="news-content-container">
                            <div class="news-heading-container mb-3">
                                <h5><?= $latest_articles_result[0]["news_title"] ?></h5>
                            </div>
                            <div class="news-author-container">
                                <p class="autor-name"><img src="<?php echo $avatar; ?>"> <span><?= $latest_articles_result[0]["news_author"] ?></span></p>
                                <p class="date-posted"><?= $latest_articles_result[0]["news_published_date"] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 pt-4">
                    <div class="row">
                        <?php foreach( $latest_articles_result as $key => $item ): ?>
                            <?php if( $key >= 1 && $key <= 2 ): ?>
                                <div class="col-6">
                                    <div id="news-<?= $item['news_id']; ?>" class="news-container <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                                        <div class="news-config-container">
                                            <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $item['news_id']; ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                                            <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $item['news_id']; ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                                            <button class="btn c-btn-primary btn-block m-0 <?= $is_pinned ? 'pin-color' : '' ?>" onClick="pinNews(this)"><i class="fas fa-thumbtack"></i></button>
                                            <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $item ?>'><i class="fa fa-pencil"></i></button>
                                        </div>
                                        <div class="news-image-container">
                                            <img class="col-12" src="<?= $item["news_image"] != "[null]" || $item["news_image"] != '[""]' ? json_decode($item["news_image"])[0] : '' ?>">
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
        </div>
        <div class="col-12 col-md-4">
            <div class="row">
                <?php foreach( $latest_articles_result as $key => $item ): ?>
                    <?php if( $key >= 4 && $key <= 5 ): ?>
                        <div class="col-12 pb-4">
                            <div id="news-<?= $item['news_id']; ?>" class="news-container <?= $status == "rejected" ? 'blur rejected-border' : $status == "need_approval" ? 'blur' : '' ?>">
                                <div class="news-config-container">
                                    <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'approved' ? 'approve-color' : '' ?>" id="btn-approve" data-news-id="<?= $item['news_id']; ?>" data-action="approved"><i class="fas fa-check-circle"></i></button>
                                    <button class="btn c-btn-primary btn-block m-0 btn-status <?= $status == 'rejected' ? 'reject-color' : '' ?>" data-news-id="<?= $item['news_id']; ?>" data-action="rejected"><i class="fas fa-minus-circle"></i></button>
                                    <button class="btn c-btn-primary btn-block m-0 <?= $is_pinned ? 'pin-color' : '' ?>" onClick="pinNews(this)"><i class="fas fa-thumbtack"></i></button>
                                    <button class="btn c-btn-primary btn-block m-0" onClick="editNews(this)" data-json='<?= $item ?>'><i class="fa fa-pencil"></i></button>
                                </div>
                                <div class="news-image-container">
                                    <img class="col-12" src="<?= $item["news_image"] != "[null]" || $item["news_image"] != '[""]' ? json_decode($item["news_image"])[0] : '' ?>">
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
    <!--CATEGORIZED NEWS-->
    <div class="categorized-news-section container">
        <?php foreach ($categories as $key => $category): ?>
            <div class="row">
                <div class="col-12">
                    <div class="latest-new-title mt-4 mb-4">
                        <a href="?c=<?= $category['category_id'] ?>">
                            <h3><?= $category["category_title"]; ?></h3> <i class="fas fa-arrow-from-left"></i>
                        </a>
                    </div>
                </div>
                <?php if($key % 2 == 0): ?>
                    <!-- LEFT SIDE -->
                    <div class="col-md-8">
                        <?php 
                            $featured_news = false;
                            include("./includes/pages/cat-news.php"); 
                        ?>
                    </div>
    
                    <!-- RIGHT SIDE -->
                    <div class="col-md-4">
                        <div class="row b-left">
                            <?php 
                            $categorized_item_attached = 0;
                            foreach ($categorized_news as $key => $item): 
                                if( $item["category"] == $category["category_id"] && $featured_news):
                                    $categorized_item_attached += 1;
                                    if( $categorized_item_attached >= 4  && $categorized_item_attached <= 6 ):
    
                            ?>
                                <div class="col-12">
                                    <div class="latest-news-content not-featured">
                                        <div class="news-image pb-2">
                                            <a href="<?= $item['news_link'] ?>">
                                                <img src="<?= $item['news_image'] ?>" alt="">
                                            </a>
                                        </div>
                                        <div class="news-details">
                                            <a href="<?= $item['news_link'] ?>">
                                                <h4><?= $item['news_title'] ?></h4>
                                            </a>
                                            <p class="news-author mt-2"><b><?= $site_title ?></b></p>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                    endif;
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- LEFT SIDE -->
                    <div class="col-md-4">
                        <div class="row b-right">
                            <?php 
                            $categorized_item_attached = 0;
                            foreach ($categorized_news as $key => $item): 
                                if( $item["category"] == $category["category_id"] && $featured_news):
                                    $categorized_item_attached += 1;
                                    if( $categorized_item_attached >= 4  && $categorized_item_attached <= 6 ):
    
                            ?>
                                <div class="col-12">
                                    <div class="latest-news-content not-featured">
                                        <div class="news-image pb-2">
                                            <a href="<?= $item['news_link'] ?>">
                                                <img src="<?= $item['news_image'] ?>" alt="">
                                            </a>
                                        </div>
                                        <div class="news-details">
                                            <a href="<?= $item['news_link'] ?>">
                                                <h4><?= $item['news_title'] ?></h4>
                                            </a>
                                            <p class="news-author mt-2"><b><?= $site_title ?></b></p>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                    endif;
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                    <!-- RIGHT SIDE -->
                    <div class="col-md-8">
                        <?php 
                            $featured_news = false;
                            include("./includes/pages/cat-news.php"); 
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>