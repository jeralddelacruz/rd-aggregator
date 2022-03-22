<?php
	session_start();

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
	
	$response = getAccounts();

	// var_dump(realpath($_SERVER["DOCUMENT_ROOT"]) . "/add/api/reddit/reddit_config.php");
	require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/add/api/reddit/reddit_config.php");
	$username = REDDIT_USERNAME;
	$password = REDDIT_PASSWORD;
	$app_id = REDDIT_APP_ID;
	$app_secret = REDDIT_APP_SECRET;
	$redirect_uri = REDDIT_REDIRECT_URI;
	$scopes = REDDIT_SCOPES;
	$state = rand();
	$duration = "permanent";

	function redditGetAuthorization(){
		global $app_id, $redirect_uri, $scopes, $state, $duration;

		$api_endpoint_authorization = "https://www.reddit.com/api/v1/authorize";

		$end_point_params_authorization = "?response_type=code" . "&client_id=" . $app_id . "&redirect_uri=" . urlencode($redirect_uri) . "&scope=" . $scopes . "&state=" . $state . "&duration=" . $duration;

		header("Location: " . $api_endpoint_authorization . $end_point_params_authorization);
	}

	if($_GET["reddit"] == 1){
		redditGetAuthorization();
	}

	function redditGetAccessToken($rCode, $rState){
		global $app_id, $app_secret, $redirect_uri;

		$api_endpoint = "https://www.reddit.com/api/v1/access_token";
	
		$params = array(
			"grant_type" => "authorization_code",
			"code" => $rCode,
			"redirect_uri" => $redirect_uri
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
		
		// echo "<pre>";
		// var_dump($response);
		// echo "</pre>";

		curl_close($ch);
		
		return $response;
	}

	$_SESSION["reddit_code"] = $_GET["code"];
	$_SESSION["reddit_state"] = $_GET["state"];

	$rCode = $_SESSION["reddit_code"];
	$rState = $_SESSION["reddit_state"];

	if(isset($rCode) && isset($rState)){
		$reddit_response = redditGetAccessToken($rCode, $rState);

		$_SESSION["reddit_access_token"] = $reddit_response->access_token;
		$_SESSION["reddit_access_token_type"] = $reddit_response->token_type;
		$_SESSION["reddit_refresh_token"] = $reddit_response->refresh_token;
	}

	function redditGetUserInfo($reddit_access_token, $reddit_access_token_type, $reddit_refresh_token){
		global $username;

		$subreddit_display_name = $username;

		$api_endpoint_get_user = "https://oauth.reddit.com/api/v1/me";

		$ch = curl_init($api_endpoint_get_user);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $subreddit_display_name . " by /u/" . $username . " (Phapper 1.0)");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: " . $reddit_access_token_type . " " . $reddit_access_token));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		// curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		
		// CURL RESPONSE
		$response_raw = curl_exec($ch);
		$response = json_decode($response_raw);

		curl_close($ch);

		return $response;
	}

	if(isset($reddit_response)){
		$reddit_access_token = $_SESSION["reddit_access_token"];
		$reddit_access_token_type = $_SESSION["reddit_access_token_type"];
		$reddit_refresh_token = $_SESSION["reddit_refresh_token"];

		$redditUser = redditGetUserInfo($reddit_access_token, $reddit_access_token_type, $reddit_refresh_token);
		$redditRefresh = redditGetRefreshToken($reddit_refresh_token);

		$data = array(
			"user_id" => $UserID,
			"account_name" => $redditUser->subreddit->display_name_prefixed,
			"rCode" => $rCode,
			"rState" => $rState,
			"reddit_access_token" => $reddit_access_token,
			"reddit_access_token_type" => $reddit_access_token_type,
			"reddit_refresh_token" => $reddit_refresh_token
		);

		saveAccount($data);

		// var_dump($dbprefix, $UserID, $redditUser->subreddit->display_name_prefixed, $rCode, $rState, $reddit_access_token, $reddit_access_token_type, $reddit_refresh_token);

		// echo "<pre>";
		// var_dump($redditRefresh, $insert);
		// echo "</pre>";
	}

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

	function saveAccount($data){
		global $DB, $dbprefix;

		$redditAccountCheck = $DB->query("SELECT * FROM {$dbprefix}social_media_accounts WHERE user_id='{$data["user_id"]}' AND account_name='{$data["account_name"]}'");

		if($redditAccountCheck == false){
			$insert = $DB->query("INSERT INTO {$dbprefix}social_media_accounts 
			SET user_id='{$data["user_id"]}', 
			account_name='{$data["account_name"]}', 
			type='reddit', 
			reddit_code='{$data["rCode"]}', 
			reddit_state='{$data["rState"]}', 
			reddit_access_token='{$data["reddit_access_token"]}', 
			reddit_access_token_type='{$data["reddit_access_token_type"]}', 
			reddit_refresh_token='{$data["reddit_refresh_token"]}'");
		}
		else{
			$update = $DB->query("UPDATE {$dbprefix}social_media_accounts 
				SET reddit_code='{$data["rCode"]}', 
				reddit_state='{$data["rState"]}', 
				reddit_access_token='{$data["reddit_access_token"]}', 
				reddit_access_token_type='{$data["reddit_access_token_type"]}', 
				reddit_refresh_token='{$data["reddit_refresh_token"]}' 
				WHERE user_id='{$data["user_id"]}' AND account_name='{$data["account_name"]}'");
		}

		// FOR DEBUGGING PURPOSES
		// if($insert){
		// 	echo "Successfully inserted.";
		// }

		// if($update){
		// 	echo "Successfully updated.";
		// }
	}

	$redditAccounts = $DB->query("SELECT * FROM {$dbprefix}social_media_accounts WHERE user_id='{$UserID}'");
?>

<div class="row">
	<div class="col-md-12">
		<div class="card">
			
			<!-- TABLE HEAD CONTAINER -->
			<div class="header card-header">
				<h4 class="title" style="float:left;margin:5px 15px 0 0;"><?php echo $index_title;?></h4>
				<!-- https://cppages.com/user/index.php?cmd=socialshare -->
				<a href="index.php?cmd=socialshare"><div class="btn btn-primary btn-fill"><i class="fa fa-share-alt-square"></i> Go to Social Shares</div></a>
			</div>
			
			<div class="content card-body">
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item" role="presentation" class="active">
						<a href="#tab1" class="nav-link active" aria-controls="profile" role="tab" data-toggle="tab">Twitter</a>
					</li>
					<li class="nav-item" role="presentation">
						<a href="#tab2" class="nav-link" aria-controls="profile" role="tab" data-toggle="tab">Reddit</a>
					</li>
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
											<a href="https://affilashop.com/?token=tertl3651!&type=twitter&mode=connect&uid=<?= $UserID; ?>&referral=<?= ($SCRIPTURL . 'user/index.php?cmd=social'); ?>" class="btn btn-primary"><i class="fa fa-sign-in"></i> &nbsp;Connect to Twitter</a>
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
											<a href="<?php echo $SCRIPTURL . "user/index.php?cmd=social&reddit=1" ?>" class="btn btn-primary">
												<i class="fa fa-sign-in"></i> Connect to Reddit
											</a>
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
							<?php foreach($redditAccounts as $redditAccount) : ?>
							<div class="col-md-4 col-sm-12">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title text-center">
											<i class="fa fa-reddit"></i> │ <?= $redditAccount["account_name"]; ?>
										</h3>
									</div>
									
									<div class="card-body text-center">
										Created at: <?= $redditAccount["created_at"]; ?>
									</div>
								</div>
							</div>
							<?php endforeach; ?>
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

<!-- REDDIT MODAL -->
<div id="reddit-modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Modal Header</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<div class="form-group">
						<input type="text" name="" value="<?php echo $response2; ?>" />
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>