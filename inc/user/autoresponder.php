<?php
	// add row for autoresponder without data yet
	foreach ($AR_LIST as $key => $autoresponder) {
		$check = $DB->info("api", "user_id = '$UserID' AND platform = '$key' ");

		if (!$check) {
			$data = json_encode($autoresponder);
			$insert = $DB->query("INSERT INTO $dbprefix"."api SET user_id = '$UserID', data = '$data', platform = '$key' ");
		}
	}

	$autoresponders = $DB->query("SELECT * FROM {$dbprefix}api WHERE user_id = '$UserID'");

	if (isset($_POST['submit'])) {
		$platform = strip($_POST['platform']);
		$apiKey = strip($_POST['api_key']);
		// Get Response
		if ($platform == 'getresponse') {
			$getResponse = [
				'key' => 'getresponse',
				'name' => 'Get Response',
				'api_key' => '',
				'account' => '',
				'list' => []
			];
		
			$account = validateGetResponse($apiKey);
			
			if ($account['success']) {
		
				// add save credentials here //
				$getResponse['api_key'] = $apiKey;
				$getResponse['account'] = $account['data'];
		
		
				// get campaign list
				$campaigns = getGetResponseList($apiKey);

				if ($campaigns['success']) {
					foreach ($campaigns['data'] as $list) {
						array_push($getResponse['list'], [
							'id' => $list->campaignId,
							'name' => $list->name
						]);
					}
				}

				 //$getResponse['list'] = $campaigns['success'] ? $campaigns['data'] : [];
		
				// json_encode the $getResponse and save to the table
				// save data on database
				$getResponse = addslashes(json_encode($getResponse));
			
				$DB->query("UPDATE {$dbprefix}api SET data = '$getResponse' WHERE user_id = '$UserID' AND platform = '$platform' ");
				// add success message 
				$_SESSION['msg'] = 'Get Response API key successfully saved.';
				redirect('index.php?cmd=autoresponder');
			} else {
				$error = 'Invalid Get Response API Key. Please check your credentials';
			}

		} 
		
		// Mailchimp
		elseif ($platform == 'mailchimp') {
			$mailchimp = [
				'key' => 'mailchimp',
				'name' => 'Mailchimp',
				'server' => '',
				'api_key' => '',
				'account' => '',
				'list' => []
			];
		
			$account = validateMailchimp($apiKey);
		
			if ($account['success']) {
		
				// add save credentials here //
				$mailchimp['server'] = getServer($apiKey);
				$mailchimp['api_key'] = $apiKey;
				$mailchimp['account'] = $account['data'];
		
				// get list here
				$list = getMailchimpList($apiKey);

				if ($list['success']) {
					foreach ($list['data']->lists as $list) {
						array_push($mailchimp['list'], [
							'id' => $list->id,
							'name' => $list->name
						]);
					}
				}
				// $mailchimp['list'] = $list['success'] ? $list['data']->lists : [];
		
				// save data on database
				$mailchimp = addslashes(json_encode($mailchimp));
				
				$DB->query("UPDATE {$dbprefix}api SET data = '$mailchimp' WHERE user_id = '$UserID' AND platform = '$platform' ");
				// add success message 
				$_SESSION['msg'] = 'Mailchimp API key successfully saved.';
				redirect('index.php?cmd=autoresponder');
			} else {
				// add error message here.
				$error = 'Invalid Mailchimp API Key. Please check your credentials';
			}
		} 
		
		// Convertkit
		elseif ($platform == 'convertkit') {
			$apiSecret = strip($_POST['api_secret']);
		
			$convertKit = [
				'key' => 'convertkit',
				'name' => 'Convert Kit',
				'api_key' => '',
				'api_secret' => '',
				'account' => '',
				'list' => []
			];
		
			$account = validateConvertKit($apiSecret);
		
			if ($account['success']) {
				// add save credentials here //
				$convertKit['api_key'] = $apiKey;
				$convertKit['api_secret'] = $apiSecret;
				$convertKit['account'] = $account['data'];
		
				$tags = getConvertKitTags($apiSecret);
				
				if ($tags['success']) {
					foreach ($tags['data']->tags as $tag) {
						array_push($convertKit['list'], [
							'id' => $tag->id,
							'name' => $tag->name
						]);
					}
				}

				// $convertKit['list'] = $tags['success'] ? $tags['data']->tags : [];
		
				// save data on database
				$convertKit = addslashes(json_encode($convertKit));
				
				$DB->query("UPDATE {$dbprefix}api SET data = '$convertKit' WHERE user_id = '$UserID' AND platform = '$platform' ");
				// add success message 
				$_SESSION['msg'] = 'Convert Kit API key and secret are successfully saved.';
				redirect('index.php?cmd=autoresponder');
			
			} else {
				$error = 'Invalid Convert Kit API Key or API Secret. Please check your credentials';
			}
		} 
		
		// Sendlane
		elseif ($platform == 'sendlane') {
			$api = strip($_POST['api']);
	    	$hash = strip($_POST['hash']);
		
			 $sendlane = [
			    'key' => 'sendlane',
				'name' => 'Sendlane',
                'api' => '',
                'hash' => '',
                'account' => '',
                'list' => []
            ];
		
			$account = validateSendlane($api, $hash);
		
			if ($account['success']) {
				// add save credentials here //
				$sendlane['api'] = $api;
				$sendlane['hash'] = $hash;
				
				foreach ($account['data'] as $list) {
					array_push($sendlane['list'], [
						'id' => $list->list_id,
						'name' => $list->list_name
					]);
				}

                // $sendlane['list'] = $account['data'];
		
				// save data on database
				$sendlane = addslashes(json_encode($sendlane));
			
				$DB->query("UPDATE {$dbprefix}api SET data = '$sendlane' WHERE user_id = '$UserID' AND platform = '$platform' ");
				// add success message 
				$_SESSION['msg'] = 'Sendlane API key and secret are successfully saved.';
				redirect('index.php?cmd=autoresponder');
			
			} else {
				$error = 'Invalid Sendlane API Key or hash. Please check your credentials';
			}
		} 
		
		// Active Campaign
		elseif ($platform == 'activecampaign') {
			$api_key = strip($_POST['api_key']);
	    	$acc_url = strip($_POST['acc_url']);
		
			 $activecampaign = [
			    'key' => 'activecampaign',
				'name' => 'Active Campaign',
				'api_key' => '',
				'acc_url' => '',
				'account' => '',
				'list' => []
            ];
		
			$lists = getActiveCampaignList($api_key, $acc_url);
		
			if ($lists['success']) {
				// add save credentials here //
				$activecampaign['api_key'] = $api_key;
				$activecampaign['acc_url'] = $acc_url;
				
				//$lists = getActiveCampaignList($apiKey, $acc_url);
				foreach ($lists['data']->lists as $list) {
						array_push($activecampaign['list'], [
							'id' => $list->id,
							'name' => $list->name
						]);
					}

		
				// save data on database
				$activecampaign = addslashes(json_encode($activecampaign));
				
				$DB->query("UPDATE {$dbprefix}api SET data = '$activecampaign' WHERE user_id = '$UserID' AND platform = '$platform' ");
				// add success message 
				$_SESSION['msg'] = 'ActiveCampaign API key and URL are successfully saved.';
				redirect('index.php?cmd=autoresponder');
			
			} else {
				$error = 'Invalid ActiveCampaign API Key or URL. Please check your credentials';
			}
		} 
		
		// Hubspot
		elseif ($platform == 'hubspot') {
			$api = strip($_POST['api']);
		
			 $hubspot = [
			    'key' => 'hubspot',
				'name' => 'Hub Spot',
                'api' => '',
                'account' => '',
                'list' => []
            ];
		
			$account = validateHubspot($api);
		
			if ($account['success']) {
				// add save credentials here //
				$hubspot['api'] = $api;
				$hubspot['account'] = $account['data'];
				
				$lists = getHubspotList($api);

				if ($lists['success']) {
					foreach ($lists['data']->lists as $list) {
						array_push($hubspot['list'], [
							'id' => $list->listId,
							'name' => $list->name
						]);
					}
				}

		
				// save data on database
				$hubspot = addslashes(json_encode($hubspot));
			
				$DB->query("UPDATE {$dbprefix}api SET data = '$hubspot' WHERE user_id = '$UserID' AND platform = '$platform' ");
				// add success message 
				$_SESSION['msg'] = 'HubSpot API key are successfully saved.';
				redirect('index.php?cmd=autoresponder');
			
			} else {
				$error = 'Invalid HubSpot API Key. Please check your credentials';
			}
		} 
		
		// Aweber
		elseif ($platform == 'aweber') {
			$auth_code = strip($_POST['auth_code']);
		
			$aweber = [
			    'key' => 'aweber',
				'name' => 'AWeber',
				'auth_code' => '',
				'access_token' => '',
				'refresh_token' => '',
				'account_id' => '',
				'list' => []
            ];
			
			$getToken = getAccToken ($auth_code);
			if ($getToken['success'] && !isset($getToken['data']->error)) {
				$access_token = strip($getToken['data']->access_token);
				$refresh_token = strip($getToken['data']->refresh_token);

				$getAcc = getAccount($access_token);

				if ($getAcc['success']) {
					foreach ($getAcc['data']->entries as $entry) {
						$account_id = strip($entry->id);
					}
				}
				
				$getList = getList($access_token, $account_id);
				if ($getList['success']) {
					foreach ($getList['data']->entries as $entry) {
						array_push($aweber['list'], [
							'id' => $entry->id,
							'name' => $entry->name
						]);
					}
				}
				
				$aweber['auth_code'] = $auth_code;
				$aweber['access_token'] = $access_token;
				$aweber['refresh_token'] = $refresh_token;
				$aweber['account_id'] = $account_id;
				
				// save data on database
				$aweber = addslashes(json_encode($aweber));
				$DB->query("UPDATE {$dbprefix}api SET data = '$aweber' WHERE user_id = '$UserID' AND platform = '$platform'");
				
				$_SESSION['msg'] = 'AWeber Keys are successfully saved.';
				redirect('index.php?cmd=autoresponder');

			} else {
				$error = 'Invalid authorization code. Please make sure you copy the authorization code properly.';
			}
			
		} 
		
		// Constant Contact
		elseif ($platform == 'constantcontact') {
			$client_id = strip($_POST['client_id']);
			$redirect_uri = strip($_POST['redirect_uri']);
			$secret_key = strip($_POST['secret_key']);
			$auth_code = strip($_POST['auth_code']);
			
			$constantcontact = [
			    'key' => 'constantcontact',
				'name' => 'Constant Contact',
				'client_id' => '',
				'secret_key' => '',
				'redirect_uri' => '',
				'auth_code' => '',
				'access_token' => '',
				'refresh_token' => '',
				'list' => []
            ];
			
			$getToken = getCCToken ($auth_code, $redirect_uri, $client_id, $secret_key);
			if ($getToken['success']) {
					$access_token = strip($getToken['data']->access_token);
					$refresh_token = strip($getToken['data']->refresh_token);
			}
			
			$getList = getCCList($access_token);
			if ($getList['success']){
				foreach ($getList['data']->lists as $list) {
					array_push($constantcontact['list'], [
						'id' => $list->list_id,
						'name' => $list->name
					]);
				}
			}
			
			$constantcontact['client_id'] = $client_id;
			$constantcontact['redirect_uri'] = $redirect_uri;	
			$constantcontact['secret_key'] = $secret_key;	
			$constantcontact['auth_code'] = $auth_code;	
			$constantcontact['access_token'] = $access_token;
			$constantcontact['refresh_token'] = $refresh_token;
			// save data on database
			$constantcontact = addslashes(json_encode($constantcontact));
			
			$DB->query("UPDATE {$dbprefix}api SET data = '$constantcontact' WHERE user_id = '$UserID' AND platform = '$platform' ");
			$_SESSION['msg'] = 'Constant Contact Keys are successfully saved.';
			redirect('index.php?cmd=autoresponder');
			
		}

		// Sendiio
		elseif ($platform == 'sendiio') {
			$apiToken = strip($_POST['api_token']);
			$apiSecret = strip($_POST['api_secret']);
		
			$sendiio = [
				'key' => 'sendiio',
				'name' => 'Sendiio',
				'api_token' => '',
				'api_secret' => '',
				'account' => '',
				'list' => []
			];
		
			$account = validateSendiio($apiToken, $apiSecret);
			
			if ($account['success']) {
				// add save credentials here //
				$sendiio['api_token'] = $apiToken;
				$sendiio['api_secret'] = $apiSecret;
				$sendiio['account'] = $account['data'];
		
				$lists = getSendiioList($apiToken, $apiSecret);

				if (isset($lists['data']) && $lists['data']->msg = "OK") {
					foreach ($lists['data']->data->lists as $list) {
						array_push($sendiio['list'], [
							'id' => $list->id,
							'name' => $list->name
						]);
					}
				}

				// save data on database
				$sendiio = addslashes(json_encode($sendiio));
			
				$DB->query("UPDATE {$dbprefix}api SET data = '$sendiio' WHERE user_id = '$UserID' AND platform = '$platform' ");
				// add success message 
				$_SESSION['msg'] = 'Sendiio API token and secret are successfully saved.';
				redirect('index.php?cmd=autoresponder');
			
			} else {
				$error = 'Invalid Sendiio API token or secret. Please check your credentials';
			}
		} 
	}
	
	//Aweber refresh token
	if (isset($_POST['refreshAw'])) {
		$client_id = strip($_POST['client_id']);
	    	$secret_key = strip($_POST['secret_key']);
			$redirect_uri = strip($_POST['redirect_uri']);
			$auth_code = strip($_POST['auth_code']);
			//$access_token = strip($_POST['access_token']);
		
			$aweber = [
			    'key' => 'aweber',
				'name' => 'AWeber',
				'client_id' => '',
				'secret_key' => '',
				'redirect_uri' => '',
				'auth_code' => '',
				'access_token' => '',
				'refresh_token' => '',
				'account_id' => '',
				'list' => []
            ];

		$autoresponder = $DB->info("api", "user_id = '$UserID' AND platform = 'aweber' ");
		$autoresponderData = json_decode($autoresponder['data']);
		
		$refresh = refreshAccToken ($autoresponderData->refresh_token, $client_id, $secret_key);
		if ($refresh['success']) {
				$access_token = strip($refresh['data']->access_token);
				$refresh_token = strip($refresh['data']->refresh_token);
		}
		
		$getAcc = getAccount($access_token);
			if ($getAcc['success']) {
				foreach ($getAcc['data']->entries as $entry) {
					$account_id = strip($entry->id);
				}
			}
			
			$getList = getList($access_token, $account_id);
			if ($getList['success']) {
				foreach ($getList['data']->entries as $entry) {
					array_push($aweber['list'], [
						'id' => $entry->id,
						'name' => $entry->name
					]);
				}
			}
			
			$aweber['client_id'] = $client_id;
			$aweber['secret_key'] = $secret_key;
			$aweber['redirect_uri'] = $redirect_uri;
			$aweber['auth_code'] = $auth_code;
			$aweber['access_token'] = $access_token;
			$aweber['refresh_token'] = $refresh_token;
			$aweber['account_id'] = $account_id;
			
			// save data on database
			$aweber = json_encode($aweber);
			$DB->query("UPDATE {$dbprefix}api SET data = '$aweber' WHERE user_id = '$UserID' AND platform = 'aweber' ");
			$_SESSION['msg'] = 'Access Token Refreshed!';
			redirect('index.php?cmd=autoresponder');
	}
	
	
?>
<style>
	.instructions{
		margin-bottom: 30px;
	}

	input.apiInput {
		border: 1px solid #aaa;
		padding: 10px;
		border-radius: 5px;
		width: 100%;
	}

	input.apiInputview {
		border: 1px solid #aaa;
		padding: 5px;
		border-radius: 5px;
		width: 100%;
	}
</style>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.js"></script>

<?php if($error) : ?>
	<div class="alert alert-danger"><?php echo $error;?></div>
<?php elseif($_SESSION['msg']) : ?>
	<div class="alert alert-success"><?php echo $_SESSION['msg']; $_SESSION['msg']='';?></div>
<?php endif; ?>
<div id="content">
	<div class="row">
		<?php foreach($autoresponders as $key => $autoresponder) : ?>
			<?php $data = json_decode($autoresponder['data']); ?>
			<?php ${$data->key} = $data; ?>

			<div class="col-md-4 col-sm-12">
				<div class="card">
					<div class="card-body">
						<h3><?php echo ${$data->key}->name; ?></h3>
						<div class="img-container mb-3">
							<img src="<?php echo "../assets/img/". ${$data->key}->key .".png" ?>" alt="" class="img-responsive">
						</div>

						<button class="btn btn-primary btn-block" data-toggle="modal" data-target="#<?php echo ${$data->key}->key; ?>"><i class="fa fa-plug" aria-hidden="true"></i>  &nbsp;Connect</button>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

</div>

<!--Modals-->
<!-- Get Response  -->
<!-- Modal -->
<div class="modal fade" id="getresponse" tabindex="-1" role="dialog" aria-labelledby="getresponseLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="POST">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="getresponseLabel">Get Response</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				
				<div class="modal-body">
					<!-- Instructions -->
					<div class="instructions">
						<h5>How to get Get Response API Key</h5>
						<p> 1. Login to your <a href="https://app.getresponse.com/login" target="_blank">Get Response</a> account.<br/>
							2. Select 'Integrations & API' from your Account menu at the top left. <em>(<a href="http://prntscr.com/qvy9zs">see screenshot</a>)</em><br/>
							3. Generate Key if you do not have one <em>(<a href="http://prntscr.com/qvya6e" target="_blank">see screenshot</a>)</em><br/>
							4. Click the "COPY" button to copy your API key <em>(<a href="http://prntscr.com/qvyaqs" target="_blank">see screenshot</a>)</em></p>
					</div>
					<input type="hidden" name="platform" value="getresponse">

					<label for="">API Key</label>
					<input type="text" name="api_key" class="form-control" value="<?php echo $getresponse->api_key; ?>" required>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" name="submit" class="btn btn-primary">Connect</button>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- End Get Response  -->

<!-- Mailchimp  -->
<!-- Modal -->
<div class="modal fade" id="mailchimp" tabindex="-1" role="dialog" aria-labelledby="mailchimpLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="POST">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="mailchimpLabel">Mailchimp</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				
				<div class="modal-body">
					<!-- Instructions -->
					<div class="instructions">
						<h5>How to get Mail Chimp API Key</h5>
						<p> 1. Register and login to <a href="https://login.mailchimp.com/" target="_blank">Mailchimp</a>. <br />
							2. Click your profile name to expand the Account Panel and choose Account Settings. <em>(<a href="http://prntscr.com/qvy1ao" target="_blank">see screenshot</a>)</em><br />
							3. Click the Extras menu and choose API keys. <em>(<a href="http://prntscr.com/qvy1sj" target="_blank">see screenshot</a>)</em>  <br />
							4. Click "Create A Key" button if you do not have any API key or Copy an existing API key. <em>(<a href="http://prntscr.com/qvy2h3" target="_blank">see screenshot</a>)</em></p>
					</div>
					<input type="hidden" name="platform" value="mailchimp">
					<label for="">API Key</label>
					<input type="text" name="api_key" class="form-control" value="<?php echo $mailchimp->api_key; ?>" required>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" name="submit" class="btn btn-primary">Connect</button>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- End Mailchimp  -->

<!-- Convert Kit  -->
<!-- Modal -->
<div class="modal fade" id="convertkit" tabindex="-1" role="dialog" aria-labelledby="convertkitLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="POST">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="convertkitLabel">Convert Kit</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				
				<div class="modal-body">
					<!-- Instructions -->
					<div class="instructions">
						<h5>How to get Convert Kit API Key and API Secret</h5>
						<p> 1. Register and login to your <a href="https://app.convertkit.com/users/login" target="_blank">Convert Kit</a> account. <br />
							2. Click "Account" from the Top Menu to access your account settings <em>(<a href="https://prnt.sc/qvy3gp" target="_blank">see screenshot</a>)</em><br />
							3. Copy your API Key and API Secret <em>(<a href="http://prntscr.com/qvy4c4" target="_blank">see screenshot</a>)</em> <br />
					</div>
					<input type="hidden" name="platform" value="convertkit">

					<div class="form-group">
						<label for="">API Key</label>
						<input type="text" name="api_key" class="form-control" value="<?php echo $convertkit->api_key; ?>" required>
					</div>

					<div class="form-group">
						<label for="">API Secret</label>
						<input type="text" name="api_secret" class="form-control" value="<?php echo $convertkit->api_secret; ?>" required>
					</div>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" id="submit" name="submit" value="Connect" class="btn btn-primary" />
				</div>
			</div>
		</form>
	</div>
</div>
<!-- End Convert Kit  -->

<!-- Sendlane  -->
<!-- Modal -->
<div class="modal fade" id="sendlane" tabindex="-1" role="dialog" aria-labelledby="sendlaneLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="POST">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="sendlaneLabel">Sendlane</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				
				<div class="modal-body">
					<!-- Instructions -->
					<div class="instructions">
						<h5>How to get Sendlane API Key and Hash</h5>
						<p> 1. Register and login to your <a href="https://sendlane.com/login" target="_blank">Sendlane</a> account. <br />
							2. Click on the "API" menu on the side <em>(<a href="http://prntscr.com/qvy597" target="_blank">see screenshot</a>)</em> <br />
							3. From there, you can copy and paste your API Key and API Hash Key. <em>(<a href="http://prntscr.com/qvy5o0" target="_blank">see screenshot</a>)</em></p>
					</div>
					<input type="hidden" name="platform" value="sendlane">

					<div class="form-group">
						<label for="">API</label>
						<input type="text" name="api" class="form-control" value="<?php echo $sendlane->api; ?>" required>
					</div>

					<div class="form-group">
						<label for="">Hash</label>
						<input type="text" name="hash" class="form-control" value="<?php echo $sendlane->hash; ?>" required>
					</div>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" id="submit" name="submit" value="Connect" class="btn btn-primary" />
				</div>
			</div>
		</form>
	</div>
</div>
<!-- End Sendlane -->

<!-- ActiveCampaign  -->
<!-- Modal -->
<div class="modal fade" id="activecampaign" tabindex="-1" role="dialog" aria-labelledby="activecampaignLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="POST">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="activecampaignLabel">ActiveCampaign</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				
				<div class="modal-body">
					<!-- Instructions -->
					<div class="instructions">
						<h5>How to get ActiveCampaign API and URL</h5>
						<p> 1. 1. Register and login to your <a href="https://www.activecampaign.com/login/" target="_blank">ActiveCampaign</a> account. <br />
							2. Click the "Settings" option located in the left side navigation menu. <em>(<a href="https://prnt.sc/qwozni" target="_blank">see screenshot</a>)</em> <br />
							3. The Account Settings menu will appear. Click the "Developer" option. <em>(<a href="https://prnt.sc/qwp09m" target="_blank">see screenshot</a>)</em> <br />
							4. The Developer Settings page will load and will display your ActiveCampaign API URL and Key. </p>
					</div>
					<input type="hidden" name="platform" value="activecampaign">

					<div class="form-group">
						<label for="">API Key</label>
						<input type="text" name="api_key" class="form-control" value="<?php echo $activecampaign->api_key; ?>" required>
					</div>

					<div class="form-group">
						<label for="">Account Url</label>
						<input type="text" name="acc_url" class="form-control" value="<?php echo $activecampaign->acc_url; ?>" required>
					</div>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" id="submit" name="submit" value="Connect" class="btn btn-primary" />
				</div>
			</div>
		</form>
	</div>
</div>
<!-- End ActiveCampaign  -->

<!-- Hubspot  -->
<!-- Modal -->
<div class="modal fade" id="hubspot" tabindex="-1" role="dialog" aria-labelledby="hubspotLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="POST">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="hubspotLabel">Hub Spot</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				
				<div class="modal-body">
					<!-- Instructions -->
					<div class="instructions">
						<h5>How to get Hub Spot API Key</h5>
						<p> 1. In your <a href="https://app.hubspot.com/" target="_blank">HubSpot account</a>, click the settings icon settings in the main navigation bar.  <br />
							2. In the left sidebar menu, navigate to Integrations > API key. <em>(<a href="https://prnt.sc/qwp5i4" target="_blank">see screenshot</a>)</em> <br />
							3. If a key has never been generated for your account, click Generate API key. <em>(<a href="https://prnt.sc/qwp5zs" target="_blank">see screenshot</a>)</em>  <br />
							4. If you've already generated an API key, click Show to display your key. <br />
							<i>Note: Make sure you make your lists static. </i></p>
							
					</div>
					<input type="hidden" name="platform" value="hubspot">

					<label for="">API Key</label>
					<input type="text" name="api" class="form-control" value="<?php echo $hubspot->api; ?>" required>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" name="submit" class="btn btn-primary">Connect</button>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- End Hubspot  -->

<!-- AWeber  -->
<!-- Modal -->
<div class="modal fade" id="aweber" tabindex="-1" role="dialog" aria-labelledby="aweberLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="POST">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="aweberLabel">AWeber</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				
				<div class="modal-body">
					<!-- Instructions -->
					<div class="instructions">
						<h5>How to connect your AWeber account</h5>
						<p>1. Click the "Connect" button you will be opening a new tab.</p>
						<p>2. Sign in to your AWeber account and allow our app.</p>
						<p>3. After signing in and copy the provided authorization code.</p>
						<p>4. Paste the authorization code below.</p>
						<p>5. Click saved to process the account verification and get your email list.</p>
					</div>
					<input type="hidden" name="platform" value="aweber">
				
					<div class="form-group">
						<label for="">Authorization Code</label>
						<input type="text" name="auth_code" class="form-control" value="<?php echo $aweber->auth_code; ?>">
					</div>
					
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" id="submit" name="submit" value="Save" class="btn btn-primary" />
					<a class="btn btn-primary" href="https://auth.aweber.com/oauth2/authorize?response_type=code&client_id=<?php echo $WEBSITE['aweber_client_id']; ?>&redirect_uri=urn:ietf:wg:oauth:2.0:oob&scope=list.read+account.read+subscriber.read+subscriber.write&state=magick" target="_blank">Connect</a>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- End AWeber  -->

<!-- Constant Contact  -->
<!-- Modal -->
<div class="modal fade" id="constantcontact" tabindex="-1" role="dialog" aria-labelledby="constantcontactLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="POST">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="constantcontactLabel">Constant Contact</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				
				<div class="modal-body">
					 Instructions
					<div class="instructions">
						<h5>How to get Constant Contact Client ID, Secret Key and Redirect Url</h5>
						<p> 1. Sign up for a <a href="https://v3.developer.constantcontact.com/login/index.html" style="color: blue;"> Constant Contact developer account </a> <br />
							2. Register an <a href="https://app.constantcontact.com/" style="color: blue;">application.</a> <br />
							3. Then on your apps page, scroll down to your app and copy the keys from there. </p>
							
						<h5>How to get Authorization code</h5>
						<p> 1. Insert first the three keys (Client ID, Secret and Redirect Url), after that, click get auth code.<br />
							2. You will be redirected on a login page, login to your account <br />
							3. After logging in, copy the code that is given (on the url). Then click add keys again. <em>(<a href="https://prnt.sc/r7amnt" target="_blank">see screenshot</a>)</em><br /></p>
					</div> 
					<input type="hidden" name="platform" value="constantcontact">

					<div class="form-group">
						<label for="">API Key</label>
						<input type="text" name="client_id" class="form-control" value="<?php echo $constantcontact->client_id; ?>" required>
					</div>
					
					<div class="form-group">
						<label for="">Secret Key</label>
						<input type="text" name="secret_key" class="form-control" value="<?php echo $constantcontact->secret_key; ?>" required>
					</div>
					
					<div class="form-group">
						<label for="">Redirect URI</label>
						<input type="text" name="redirect_uri" class="form-control" value="<?php echo $constantcontact->redirect_uri; ?>" required>
					</div>
					
					<div class="form-group">
						<label for="">Auth Code</label>
						<input type="text" name="auth_code" class="form-control" value="<?php echo $constantcontact->auth_code; ?>" >
					</div>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" id="submit" name="submit" value="Add Keys" class="btn btn-primary" />
					<a class="btn btn-primary" href="https://api.cc.email/v3/idfed?client_id=<?php echo $constantcontact->client_id; ?>&redirect_uri=<?php echo $constantcontact->redirect_uri; ?>&response_type=code&scope=contact_data+account_read " target="_blank">Get Auth Code</a>
					<!--<input type="submit" id="refreshCC" name="refreshCC" value="Refresh Token" class="btn btn-primary" />-->
				</div>
			</div>
		</form>
	</div>
</div>
<!-- End Constant Contact  -->

<!-- Sendiio  -->
<!-- Modal -->
<div class="modal fade" id="sendiio" tabindex="-1" role="dialog" aria-labelledby="sendiioLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="POST">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="sendiioLabel">Sendiio</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				
				<div class="modal-body">
					<!-- Instructions -->
					<div class="instructions">
						<h5>How to get Sendiio API Token & Secret</h5>
						<p> 1. Register and login to your <a href="https://sendiio.com/auth/login" target="_blank">Sendiio</a> account. <br />
							2. Click on the "Edit Profile" under your account name <em>(<a href="http://i.imgur.com/hQ5Akla.png" target="_blank">see screenshot</a>)</em> <br />
							3. From there, you can copy and paste your API Token and API Secret. <em>(<a href="http://i.imgur.com/t419eFU.png" target="_blank">see screenshot</a>)</em></p>
					</div>
					<input type="hidden" name="platform" value="sendiio">

					<div class="form-group">
						<label for="">API Token</label>
						<input type="text" name="api_token" class="form-control" value="<?php echo $sendiio->api_token; ?>" required>
					</div>
					<div class="form-group">
						<label for="">API Secret</label>
						<input type="text" name="api_secret" class="form-control" value="<?php echo $sendiio->api_secret; ?>" required>
					</div>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" id="submit" name="submit" value="Connect" class="btn btn-primary" />
				</div>
			</div>
		</form>
	</div>
</div>
<!-- End Sendiio -->
