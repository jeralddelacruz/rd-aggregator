<?php
    $campaigns = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = {$UserID}");

    // REDDIT SETUP
    require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/add/api/reddit/reddit_config.php");
    $username = REDDIT_USERNAME;
    $password = REDDIT_PASSWORD;
    $app_id = REDDIT_APP_ID;
    $app_secret = REDDIT_APP_SECRET;
    $redirect_uri = REDDIT_REDIRECT_URI;
    $scopes = REDDIT_SCOPES;
    $state = rand();
    $duration = "permanent";

    function getAccounts() {
        global $UserID, $SPECIALTOKEN;
        
        $ch = curl_init();  
     
        curl_setopt($ch,CURLOPT_URL, "https://affilashop.com/?token={$SPECIALTOKEN}&mode=list&uid={$UserID}&type=twitter");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false); 
     
        $output=curl_exec($ch);
     
        curl_close($ch);
        return isset($output) ? json_decode($output) : [];   
    }

    function redditGetAccount(){
        global $DB, $UserID, $dbprefix;

        $fetchRedditAccounts = $DB->query("SELECT * FROM {$dbprefix}social_media_accounts WHERE user_id='{$UserID}'");

        return $fetchRedditAccounts;
    }

    $redditAccounts = redditGetAccount();

    function redditGetRefreshToken($reddit_refresh_token){
        global $app_id, $app_secret, $redirect_uri, $username, $password, $redirect_uri, $scopes, $state;

        $api_endpoint = 'https://ssl.reddit.com/api/v1/access_token';

        $params = array(
            "grant_type" => "refresh_token",
            "refresh_token" => $reddit_refresh_token
        );
        
        // CURL PROCESS
        $ch = curl_init($api_endpoint);
        curl_setopt($ch, CURLOPT_USERPWD, $app_id . ":" . $app_secret);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        
        // CURL RESPONSE
        $response_raw = curl_exec($ch);
        $response = json_decode($response_raw);

        curl_close($ch);
        
        return $response;
    }

    function redditPost($data){
        // POSTING TO A SUB-REDDIT
        global $username;

        // var_dump($data); die();
        // CAME FROM reddit_get_access_token.php
        
        // CHANGE VALUE FOR SUBREDDIT NAME (COMMUNITY)
        $subreddit_name = $data["subreddit"];
        
        $subreddit_display_name = $username;
        $post_title = $data["post_title"];
        $post_url = $data["post_url"];
        $post_text = $data["post_text"];
        
        $api_endpoint_submit = "https://oauth.reddit.com/api/submit";
        
        // FOR POSTING LINKS
        $params = array(
            "text" => $post_url . $post_text,
            "title" => $post_title,
            "sr" => $subreddit_name,
            "kind" => "self"
        );
        
        // FOR POSTING TEXT
        // $params = array(
        //     "url" => $post_url,
        //     "title" => $post_title,
        //     "text" => $post_text,
        //     "sr" => $subreddit_name,
        //     "kind" => "self"
        // );

        // echo "<pre>";
        // print_r($params);
        // echo "</pre>";
        // die();
        
        // CURL PROCESS
        $ch = curl_init($api_endpoint_submit);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $username . " by /u/" . $username . " (Phapper 1.0)");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: " . $data["reddit_access_token_type"] . " " . $data["reddit_access_token"]));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        
        // CURL RESPONSE
        $response_raw = curl_exec($ch);
        $response = json_decode($response_raw);

        // echo "<pre>";
        // print_r($response);
        // echo "</pre>";
        // die();
        
        curl_close($ch);
    }
    
     function postSocialMedia($data, $postURL) {
        $ch = curl_init();  

        curl_setopt($ch,CURLOPT_URL, "https://affilashop.com" . $postURL);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false); 
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'POST'); 
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, [
            'body' => $data['body'],
            'hashtags' => $data['hashtags'],
            'account_name' => $data['account_name'],
            'blog_name' => $data['account_name'],
            'title' => $data['title']
        ]); 
        
     
        $output=curl_exec($ch);

        curl_close($ch);

        return isset($output) ? json_decode($output) : [];  
    }
    
    $response = getAccounts();
    
    if($_GET["del"]){
        $id = $_GET['del'];
        $DB->query("DELETE FROM {$dbprefix}social_sharing WHERE id = '$id'");
        $_SESSION['msg'] = 'Share Campaign successfully deleted.';
        redirect("index.php?cmd=socialshare");
    }

    if ( isset($_POST['submit']) ) {
        $account_name = $_POST['account_name'];
        $type = strip($_POST['type']);
        $title = strip($_POST['title']);
        $page_url = strip($_POST['page_url'], 0);
        $message = strip($_POST['message']);
        $subreddit = strip($_POST['subreddit']);
        $hashtags = strip($_POST['hashtags'], 0);
        $scheduled_post = $publish_type == 'immediate' ? date('Y-m-d' . ' ' . 'h:i:s') : $_POST['scheduled_post'];
        $publish_type = $_POST['publish_type'];
        $published = $publish_type == 'immediate' ? 1 : 0;

        if ($publish_type == 'immediate') {
            // call api to post to tumblr or tweeter
        }

        $insert = $DB->query("INSERT INTO {$dbprefix}social_sharing SET 
            user_id = '$UserID', 
            account_name = '$account_name', 
            type = '$type', 
            title = '$title', 
            page_url = '$page_url', 
            message = '$message', 
            hashtags = '$hashtags', 
            scheduled_post = '$scheduled_post', 
            published = '$published'
        ");
        
        if($published == 1){
            if($type == 'twitter' || $type == 'tumblr'){
                $data =  [
                'body' => $page_url . " " . $message,
                'hashtags' => $hashtags,
                'account_name' => $account_name,
                'title' => $title
                ];
                
                $postURL = "/?token={$SPECIALTOKEN}&mode=post&uid={$UserID}&type={$type}";

                $success = postSocialMedia($data, $postURL);
            }

            if($type == 'reddit'){
                $redditAccounts = $DB->query("SELECT * FROM {$dbprefix}social_media_accounts WHERE user_id='{$UserID}' AND account_name='{$account_name}'");

                $reddit_refresh_token = $redditAccounts[0]["reddit_refresh_token"];

                $refreshedAccessToken = redditGetRefreshToken($reddit_refresh_token);

                // var_dump($refreshedAccessToken->access_token); die();

                $reUpdateAccessToken = $DB->query("UPDATE {$dbprefix}social_media_accounts SET reddit_access_token='{$refreshedAccessToken->access_token}' WHERE user_id='{$UserID}' AND account_name='{$account_name}'");

                // var_dump($reUpdateAccessToken, $reddit_refresh_token, $redditAccounts[0]["reddit_access_token"]); die();

                $redditAccounts2 = $DB->query("SELECT * FROM {$dbprefix}social_media_accounts WHERE user_id='{$UserID}' AND account_name='{$account_name}'");

                $reddit_access_token = $redditAccounts2[0]["reddit_access_token"];
                $reddit_access_token_type = $redditAccounts2[0]["reddit_access_token_type"];

                $data = array(
                    "reddit_access_token" => $reddit_access_token,
                    "reddit_access_token_type" => $reddit_access_token_type,
                    "subreddit" => $subreddit,
                    "post_title" => $title,
                    "post_url" => $page_url,
                    "post_text" => $message
                );

                $success = redditPost($data);
            }

            if (count($success)) {
                $_SESSION['msg'] = 'Share Campaign successfully saved and posted.';

                redirect("index.php?cmd=socialshare");
            } else {
                // $error = 'Oops! Something went wrong!';
            }
        }
        else{
            $_SESSION['msg'] = 'Share Campaign successfully saved.';
            redirect("index.php?cmd=socialshare");
        }
    }

    $socialShares = $DB->query("SELECT * FROM {$dbprefix}social_sharing WHERE user_id = {$UserID}");
?>

<?php if($error): ?>
<div class="alert alert-danger"><?php echo $error;?></div>
<?php elseif($_SESSION['msg']): ?>
<div class="alert alert-success"><?php echo $_SESSION['msg']; $_SESSION['msg']='';?></div>
<?php endif; ?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.js"></script>

<div id="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="padding: 30px;">
                <div class="header card-header">
                    <h4 class="title">
                        <?php echo $index_title;?>

                        <button data-toggle="modal" data-target="#shareCampaign" class="float-right btn btn-primary add-campaign">Share a campaign</button>
                    </h4>
                    
                    <!--<small>...</small>-->
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-fixed" id="pop-list">
                        <thead class="">
                            <tr>
                                <th>#</th>
                                <th>Campaign Name</th>
                                <th>URL</th>
                                <th class="text-center">Message</th>
                                <th class="text-center">Hashtags</th>
                                <th class="text-center">Is Published</th>
                                <th class="text-center">Schedule</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(sizeof($socialShares)): ?>
                                <?php foreach($socialShares as $socialShare): ?>
                                <tr>
                                    <td><?= $socialShare['id']; ?></td>
                                    <td><?= $socialShare['title']; ?></td>
                                    <td><?= $socialShare['page_url']; ?></td>
                                    <td class="text-center"><?= $socialShare['message']; ?></td>
                                    <td class="text-center"><?= $socialShare['hashtags']; ?></td>
                                    <td class="text-center <?= ($socialShare['published'] == 1) ? "text-success" : "text-danger" ?>"><?= ($socialShare['published'] == 1) ? "Published" : "Not yet" ?></td>
                                    <td class="text-center"><?= ($socialShare['scheduled_post'] == "0000-00-00 00:00:00") ? "-" : $socialShare['scheduled_post'] ?></td>
                                    <td class="text-center">
                                        <a href="index.php?cmd=socialshare&del=<?= $socialShare['id']; ?>" class="btn btn-danger">Delete</a>
                                    </td>
                                </tr>    
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="shareCampaign" class="modal fade" role="dialog" data-backdrop="false" style="background-color: rgba(0,0,0,.5);">
    <div class="modal-dialog" style="width: 50%;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
            <h4 class="modal-title">Share Campaign</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="">Title</label>
                        <input type="text" name="title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Account</label>
                        <select name="account_name" class="form-control">
                            <?php foreach($response->data as $account): ?>
                                <option data-type="<?= $account->type; ?>" value="<?= $account->name; ?>"><?= $account->name; ?></option>
                            <?php endforeach; ?>

                            <?php foreach($redditAccounts as $redditAccount) : ?>
                                <option data-type="<?= $redditAccount["type"]; ?>" value="<?= $redditAccount["account_name"]; ?>"><?= $redditAccount["account_name"]; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="type">
                    </div>
                    <div class="form-group">
                        <label for="">Select Campaign</label>
                        <select name="page_url" class="form-control">
                            <?php foreach($campaigns as $campaign): ?>
                                <?php
                                    $page_url = "{$SCRIPTURL}add/pages.php?pages_id={$campaign["included_webinar_page_id"]}&campaigns_id={$campaign["campaigns_id"]}";        
                                ?>
                                <option value="<?= $page_url; ?>"><?= $campaign["campaigns_title"]; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Message</label>
                        <textarea name="message" rows="" class="form-control"></textarea>
                    </div>
                    <div class="form-group" id="subreddit" style="display:none;">
                        <label for="">Subreddit</label>
                        <input type="text" name="subreddit" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Hashtags</label>
                        <input type="text" name="hashtags" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Publish</label>
                        <select name="publish_type" class="form-control mb-2">
                            <option value="immediate">Immediate Post</option>
                            <option value="schedule">Schedule Post</option>
                        </select>

                        <input type="datetime-local" min="<?= date('Y-m-d' . " " . 'h:i:s'); ?>" name="scheduled_post" class="form-control" style="display:none;">
                    </div>
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    jQuery(document).ready(function($) {
        $(".fb").fancybox({});
    });
    
    $(document).ready( function () {
        $('#pop-list').DataTable();
    } );
    
    $('select[name=publish_type]').on('input', function() {
        $('input[name=scheduled_post]').val();

        if ($(this).val() == 'schedule') {
            $('input[name=scheduled_post]').show()
        } else {
            $('input[name=scheduled_post]').hide()
        }
    })

    $('select[name=account_name]').on('input', function() {
        $('input[name=type]').val($(this).find(':selected').data().type)
    })

    $('select[name=account_name]').trigger('input')

    // ADD
    $('select[name=account_name]').on('input', function() {
        $('input[name=type]').val();

        // console.log($('input[name=type]').val());
        // console.log($(this).val());

        if ($('input[name=type]').val() == 'reddit') {
            $('#subreddit').show()
        } else {
            $('#subreddit').hide()
        }
    })

    $(document).ready( function () {
        if ($('input[name=type]').val() == 'reddit') {
            $('#subreddit').show()
        } else {
            $('#subreddit').hide()
        }
    } );
</script>