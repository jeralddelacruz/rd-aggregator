<?php
	// VARIABLE INITIALIZATION
	$current_user_id = $UserID;
	$dfy_author_id = $WEBSITE["dfy_author"];
	$site_domain_url = $SCRIPTURL;
	
	$myBlogName = 'Test';
    $myBlogUrl = 'https://qqqqq.newsmaximizer.com/add/news.php?campaigns_id=83';
    $myBlogUpdateUrl = 'https://qqqqq.newsmaximizer.com/add/news.php?campaigns_id=83';
    $myBlogRSSFeedUrl = '';
    $postSelectedPost = array();
    $isMultiple = false;

    // Just and example so you need to put your own list here
    // I haven't used many of these for years
    // List of Servers to Ping
    $xmlRpcPingUrls = array();
    $selectedPingUrls = array();
    $xmlRpcPingUrls = [
        'https://pingoo.jp/ping/',
        'https://rpc.aitellu.com',
        'https://rpc.bloggerei.de/ping/',
        'https://rpc.pingomatic.com',
        'https://rpc.reader.livedoor.com/ping'
    ];

    require_once( 'IXR_Library.php' );

    function xmlRpcPing( $url ) {
        global $myBlogName, $myBlogUrl, $myBlogUpdateUrl, $myBlogRSSFeedUrl;
        $client = new IXR_Client( $url );
        $client->timeout = 3;
        $client->useragent .= ' -- PingTool/1.0.0';
        $client->debug = false;

        if( $client->query( 'weblogUpdates.extendedPing', $myBlogName, $myBlogUrl, $myBlogUpdateUrl, $myBlogRSSFeedUrl ) )
        {
            return $client->getResponse();
        }

        if( $client->query( 'weblogUpdates.ping', $myBlogName, $myBlogUrl ) )
        {
            return $client->getResponse();
        }
        return false;
    }
    
    // GET ALL THE NOT PINGED CAMPAIGN
    $pingedID = array();
    $pinged = $DB->query("SELECT * FROM {$dbprefix}ping WHERE user_id = '{$UserID}'");
    foreach( $pinged as $key => $value ){
        $pingedID[] = explode("campaigns_id=", $value['url'])[1];
        
    }
    
    // GET ALL CAMPAIGNS
    $newPosts = array();
    $campaigns = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = '{$UserID}'");
    foreach( $campaigns as $key => $campaign ){
        if( !in_array($campaign['campaigns_id'], $pingedID, true) ){
            $newPosts[] = [
                "campaigns_title"  => $campaign['campaigns_title'],
                "campaigns_id"  => $campaign['campaigns_id']
            ];
        }
    }

    if( isset($_POST["submit"]) ){
        $selectedPingUrls = $_POST["pingUrls"];
        $postBlogName = $_POST["title"];
        $postBlogUrl = $_POST["url"];
        $isMultiple = $_POST["is_multiple"];
        $postSelectedPost = $_POST["selected_new_post"];
        
        if( count( $selectedPingUrls ) <= 0 ){
            $_SESSION["error_message"] = "No Selected RPC Ping URLs.";
            $_SESSION["success"] = false;
        }else{
            // CHECK IF THE URL IS ALREADY PINGED
            if( !$isMultiple ){
                $ping = $DB->info("ping", "url = '{$postBlogUrl}'");
                if( empty( $ping ) ){
                    $insert_campaign = $DB->query("INSERT INTO {$dbprefix}ping SET 
        				user_id = '{$UserID}', 
        				url = '{$postBlogUrl}'
        			");
        			if( $insert_campaign ){
        			    $myBlogUrl = $postBlogUrl;
                     $myBlogUpdateUrl = $myBlogUrl;
        			    $_SESSION["success"] = true;
        			}else{
        			    $_SESSION["error_message"] = "There was an error pinging your URL, please try again later.";
                        $_SESSION["success"] = false;
        			}
                }else{
                    $_SESSION["error_message"] = "Post URL is already pinged.";
                    $_SESSION["success"] = false;
                }
            }else{
                foreach( $postSelectedPost as $key => $selectedPost ){
                    $ping = $DB->info("ping", "url = '{$selectedPost}'");
                    if( empty( $ping ) ){
                        $insert_campaign = $DB->query("INSERT INTO {$dbprefix}ping SET 
            				user_id = '{$UserID}', 
            				url = '{$selectedPost}'
            			");
            			if( $insert_campaign ){
            			    $_SESSION["success"] = true;
            			}
                    }
                }
            }
        }
    }
    
    // GET ALL POST AND 
?>
<style>
    .hide{
        display: none !important;
    }
</style>
<!-- CODE_SECTION_HTML_1: CDN_DATATABLE -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.js"></script>

<!-- RESPONSE SECTION (SUCCESS AND ERROR MESSAGES) -->
<?php if($error){ ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php } ?>

<?php if($_SESSION["msg"]){ ?>
<div class="alert alert-success"><?php echo $_SESSION['msg']; $_SESSION['msg'] = ''; ?></div>
<?php } ?>

<!-- FRONTEND SECTION -->
<div class="row">
	<div class="col-md-12">
	    <?php if( isset($_POST["submit"]) && !$_SESSION["success"] ):  ?>
            <div class="alert alert-danger">
                <?= $_SESSION["error_message"]; ?>
            </div>
        <?php
            unset($_POST["submit"]);
            unset($_SESSION["success"]);
            unset($_SESSION["error_message"]);
        ?>
        <?php endif; ?>
	    <form action="" method="POST">
    		<div class="card">
    			<div class="header card-header d-flex justify-content-between">
    				<h4 class="pull-left" style="margin-top: 10px; margin-right: 10px;">Ping</h4>
    			</div>
    			<div class="card-body">
    			    <div>
                        <div class="form-group">
                            <label for="title">Title of your Page</label>
                            <input type="text" class="form-control" name="title" value="<?= $myBlogName ?>" id="title" aria-describedby="emailHelp" placeholder="Enter title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="url">URL of your Page</label>
                            <div class="form-check">
                                <input class="form-check-input included-articles" id="is-multiple" type="checkbox" name="is_multiple" value="true" />
                                <label for="is-multiple" class="is-multiple"><u>Multiple</u></label>
                            </div>
                            <input type="url" class="form-control" name="url" value="<?= $myBlogUrl ?>" id="post_url" aria-describedby="emailHelp" placeholder="Enter url" required>
                        </div>
                        
                        <!-- RESOURCE: SLIDER -->
    					<div class="form-group hide" id="multiple_post">
                            <?php if($newPosts) : ?>
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <!--<label>I want these Contents</label>-->
                                    <!--<span class="info-tooltip" data-toggle="tooltip" data-html="true" title="Include items to tab 1"><i class="fa fa-question"></i></span>-->
                                    <div class="px-3 py-1 rounded" style="height: 220px; overflow: auto; border: 1px solid gainsboro;">
                                        <?php foreach($newPosts as $key => $post) : ?>
                                        <div class="form-check">
                                            <input class="form-check-input included-articles" id="form-check-label" type="checkbox" name="selected_new_post[]" value="https://newsmaximizer.com/add/news.php?campaigns_id=<?=$post['campaigns_id']?>" />
                                            <label class="form-check-label"><u><?= $post["campaigns_title"]; ?></u></label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php else : ?>
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="text-left">No new available post!</h5>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="row">
                            <?php foreach( $xmlRpcPingUrls as $key => $url ): ?>
                                <div class="col-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="pingUrls[]" value="<?= $url ?>" class="form-check-input" id="exampleCheck<?= $key ?>">
                                        <label class="form-check-label" for="exampleCheck<?= $key ?>"><?= $url ?></label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
        		</div>
        		<div class="card-footer">
                    <button type="submit" name="submit" class="btn btn-primary float-right">Submit</button>
                </div>
    		</div>
    	</form>
    	<?php if( isset($_POST["submit"]) && $_SESSION["success"] ): ?>
            <div class="card bg-dark">
                <div class="card-body">
                    <div>
                        <?php if( $isMultiple ): ?>
                            <?php foreach( $postSelectedPost as $selectedPost ): ?>
                                <?php
                                    $myBlogUrl = $selectedPost;
                                    $myBlogUpdateUrl = $myBlogUrl;
                                ?>
                                <?php foreach( $selectedPingUrls as $url ): ?>
                                    <?php
                                        if( xmlRpcPing( $url ) ){
                                            if(xmlRpcPing( $url )['flerror'] == 0 || xmlRpcPing( $url )['flerror'] == ""){
                                                echo "<p class='text-light'>Pinged: ".$url.", Result: <span class='text-success'>Successful</span></p>";
                                            }else{
                                                echo "<p class='text-light'>Pinged: ".$url.", Result: <span class='text-danger'>".xmlRpcPing( $url )['message']."</span></p>";
                                            }
                                            
                                        }else{
                                            echo "<p class='text-light'>Pinged: ".$url.", Result: <span class='text-danger'>Not Successful</span></p>";
                                        }
                                    ?>
                                <?php 
                                    ob_flush();
                                    flush();
                                ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php foreach( $selectedPingUrls as $url ): ?>
                                <?php
                                    if( xmlRpcPing( $url ) ){
                                        if(xmlRpcPing( $url )['flerror'] == 0 || xmlRpcPing( $url )['flerror'] == ""){
                                            echo "<p class='text-light'>Pinged: ".$url.", Result: <span class='text-success'>Successful</span></p>";
                                        }else{
                                            echo "<p class='text-light'>Pinged: ".$url.", Result: <span class='text-danger'>".xmlRpcPing( $url )['message']."</span></p>";
                                        }
                                        
                                    }else{
                                        echo "<p class='text-light'>Pinged: ".$url.", Result: <span class='text-danger'>Not Successful</span></p>";
                                    }
                                ?>
                            <?php 
                                ob_flush();
                                flush();
                            ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
            unset($_POST);
        ?>
        <?php endif; ?>
	</div>
</div>
<!-- DATATABLE INITIALIZATION -->
<script type="text/javascript">
	$(document).ready(function(){
		$('#popup-table').DataTable();
		
		document.getElementById("is-multiple").addEventListener("change", function(){
        if( document.getElementById("is-multiple").checked ){
            // hide the single url
            document.querySelector("#post_url").classList.add('hide');
            // show the multiple url
            document.querySelector("#multiple_post").classList.remove('hide');
        }else{
            // hide the multiple url   
            document.querySelector("#multiple_post").classList.add('hide');
            // show the single url 
            document.querySelector("#post_url").classList.remove('hide');
        }
    });
	});
</script>

<!-- SCRIPT SECTION -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sharer.js/0.4.0/sharer.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>