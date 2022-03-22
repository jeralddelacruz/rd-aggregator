<?php
    set_time_limit(0);
    error_reporting(0);
    session_start();
    
    // IMPORTANT DIRECTORY
    $dir="../../sys";
    $fp=opendir($dir);
    while(($file=readdir($fp))!=false){
    	$file=trim($file);
    	if(($file==".")||($file=="..")){continue;}
    	$file_parts=pathinfo($dir."/".$file);
    	if($file_parts["extension"]=="php"){
    		include($dir."/".$file);
    	}
    }
    closedir($fp);
    
    $DB = new db($dbhost, $dbuser, $dbpass, $dbname);
    $DB->connect();
    if($DB->connect<1){
    	echo "Can't go on, DB not initialized.";
    	exit;
    }
    
    // WEBSITE VARIABLE
    $res=$DB->query("select setup_key,setup_val from $dbprefix"."setup order by setup_id");
    foreach($res as $row){
    	$WEBSITE[$row["setup_key"]]=$row["setup_val"];
    }
    
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
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Copy Page</title>
    
    	<meta charset="UTF-8" />
    	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    	
    	<!-- jQuery CDN -->
    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    	
    	<!-- Bootstrap CDNs -->
    	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    	
    	<!-- Other CDNs -->
    	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
    	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/v4-shims.css">
    </head>
    <body>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    
                    <!-- TABLE HEAD CONTAINER -->
                    <div class="header card-header">
                        <h4 class="title" style="float:left;margin:5px 15px 0 0;"><?php echo $index_title;?></h4>
                        <!--<a href="index.php?cmd=socialedit"><div class="btn btn-danger btn-fill">Add a social account</div></a>-->
                    </div>
                    
                    <div class="content card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation" class="active">
                                <a href="#tab1" class="nav-link active" aria-controls="profile" role="tab" data-toggle="tab">Twitter</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tab2" class="nav-link" aria-controls="profile" role="tab" data-toggle="tab">Reddit</a>
                            </li>
                            <!--<li class="nav-item" role="presentation">-->
                            <!--    <a href="#tab3" class="nav-link" aria-controls="profile" role="tab" data-toggle="tab">LinkedIn</a>-->
                            <!--</li>-->
                            <li class="nav-item" role="presentation">
                                <a href="#tab4" class="nav-link" aria-controls="profile" role="tab" data-toggle="tab">Tumblr</a>
                            </li>
                        </ul>
                        
                        <div class="tab-content">
                            <div class="tab-pane active" role="tabpanel" id="tab1">
                                <div class="row mt-4">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="header card-header"><h4 class="text-center"><i class="fa fa-twitter"></i> │ Connect your Twitter Account</h4></div>
                                            <div class="content card-body">
                                                <div style="margin: auto !important; text-align: center;">
                                                    <!--<a href="https://affilashop.com/?token=tertl3651!&type=twitter&mode=connect&uid=<?= $UserID; ?>&referral=<?= ($SCRIPTURL . 'add/social_api_2/twitterAPI2.php'); ?>" class="btn btn-primary"><i class="fa fa-sign-in"></i> &nbsp;Connect to Twitter</a>-->
                                                    <!--<br /><br />-->
                                                    <a href="https://affilashop.com/?token=tertl3651!&type=twitter&mode=connect&uid=<?= $UserID; ?>&referral=<?= ($SCRIPTURL . 'user/index.php?cmd=social'); ?>" class="btn btn-primary"><i class="fa fa-sign-in"></i> &nbsp;Connect to Twitter</a>
                                                    <!--<a href="https://pprofitfunnels.com/add/social_api/twitterAPI.php" class="btn btn-primary">Connect to Twitter</a>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4"></div>
                                </div>
                                
                                <hr />
                                
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <h3 class="text-center">Connected Accounts</h3>
                                    </div>
                                    <div class="col-md-4"></div>
                                </div>
                                
                                <div class="row mt-4">
                                    <?php foreach($response->data as $account): ?>
                                        <?php if ($account->type == 'twitter'): ?>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title text-center">
                                                        <i class="fa fa-twitter"></i> │ @<?= $account->name ?>
                                                    </h3>
                                                </div>
                                                
                                                <div class="card-body text-center">
                                                    Created at: <?= $account->created_at ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="tab-pane" role="tabpanel" id="tab2">
                                <div class="row mt-4">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="header card-header"><h4 class="text-center"><i class="fa fa-reddit"></i> │ Connect your Reddit Account</h4></div>
                                            <div class="content card-body">
                                                <div style="margin: auto !important; text-align: center;">
                                                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#reddit-modal"><i class="fa fa-sign-in"></i> &nbsp;Connect to Reddit</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4"></div>
                                </div>
                            </div>
                            <div class="tab-pane" role="tabpanel" id="tab3">
                                <div class="row mt-4">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="header card-header"><h4 class="text-center"><i class="fa fa-linkedin"></i> │ Connect your LinkedIn Account</h4></div>
                                            <div class="content card-body">
                                                <div style="margin: auto !important; text-align: center;">
                                                    <a href="#" class="btn btn-primary"><i class="fa fa-sign-in"></i> &nbsp;Connect to LinkedIn</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4"></div>
                                </div>
                            </div>
                            <div class="tab-pane" role="tabpanel" id="tab4">
                                <div class="row mt-4">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="header card-header"><h4 class="text-center"><i class="fa fa-tumblr"></i> │ Connect your Tumblr Account</h4></div>
                                            <div class="content card-body">
                                                <div style="margin: auto !important; text-align: center;">
                                                    <a href="https://affilashop.com/?token=tertl3651!&type=tumblr&mode=connect&uid=<?= $UserID; ?>&referral=<?= ($SCRIPTURL . 'user/index.php?cmd=social'); ?>" class="btn btn-primary"><i class="fa fa-sign-in"></i> &nbsp;Connect to Tumblr</a>
                                                    <!--<a href="https://affilashop.com/?token=tertl3651!&type=tumblr&mode=connect&uid=1" class="btn btn-primary"><i class="fa fa-sign-in"></i> &nbsp;Connect to Tumblr</a>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4"></div>
                                </div>
                                
                                <hr />
                                
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <h3 class="text-center">Connected Accounts</h3>
                                    </div>
                                    <div class="col-md-4"></div>
                                </div>
                                
                                <div class="row mt-4">
                                    <?php foreach($response->data as $account): ?>
                                        <?php if ($account->type == 'tumblr'): ?>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title text-center">
                                                        <i class="fa fa-tumblr"></i> │ <?= $account->name ?>
                                                    </h3>
                                                </div>
                                                
                                                <div class="card-body text-center">
                                                    Created at: <?= $account->created_at ?>
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
            </div>
        </div>
        
        <!-- START: REDDIT MODAL -->
        <div class="modal fade" id="reddit-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                	<div class="modal-header">
                	    <h4 class="modal-title">Save a Reddit account</h4>
                		<button type="button" class="close" data-dismiss="modal">&times;</button>
                	</div>
                	<div class="modal-body">
                	    <div class="row">
                            <div class="col-md-6">
                                <!-- USERNAME -->
                                <div class="form-group">
                                    <label for="pageb_bonus_redemption_email" style="float:left;margin-top:7px;">Username</label>
                                    <span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Input your Reddit username."><i class="fa fa-question" aria-hidden="true"></i></span>
                                    
                                    <input type="text" class="form-control" value="" />
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <!-- PASSOWRD -->
                                <div class="form-group">
                                    <label for="pageb_bonus_redemption_email" style="float:left;margin-top:7px;">Password</label>
                                    <span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Input your Reddit password."><i class="fa fa-question" aria-hidden="true"></i></span>
                                    
                                    <input type="password" class="form-control" value="" />
                                </div>
                            </div>
                        </div>
                	    
                	    <!-- SUB-REDDIT NAME -->
                	    <div class="form-group">
                	        <label for="pageb_bonus_redemption_email" style="float:left;margin-top:7px;">Sub-Reddit Name (Community)</label>
                	        <span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Input a Sub-Reddit (Community) where you want to post."><i class="fa fa-question" aria-hidden="true"></i></span>
                	        
                	        <input type="text" class="form-control" value="" />
                	    </div>

                        <!-- TITLE -->
                        <div class="form-group">
                            <label for="pageb_bonus_redemption_email" style="float:left;margin-top:7px;">Title</label>
                            <span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Input a post title."><i class="fa fa-question" aria-hidden="true"></i></span>
                            
                            <input type="text" class="form-control" value="" />
                        </div>

                        <!-- TEXT -->
                        <div class="form-group">
                            <label for="pageb_bonus_redemption_email" style="float:left;margin-top:7px;">Text</label>
                            <span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Input the body of the post."><i class="fa fa-question" aria-hidden="true"></i></span>
                            
                            <textarea class="form-control" rows="6"></textarea>
                        </div>
                	</div>
                	<div class="modal-footer">
                	    <a href="#" class="btn btn-success">Save</a>
                		<button type="button" class="btn btn-disabled btn-fill" data-dismiss="modal">Cancel</button>
                	</div>
                </div>
            </div>
        </div>
        <!-- END: REDDIT MODAL -->
    </body>
</html>