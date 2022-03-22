<?php
    function autoResponder( $DB, $campaign, $email ){
		$platformValue = $campaign["campaigns_integrations_platform_name"];
		$redirectUrl = "";
		$autoresponder = $DB->info("api", "user_id = '{$campaign["user_id"]}' AND platform = '$platformValue' ");
		$autoresponderData = json_decode($autoresponder['data']);
		if($platformValue == 'getresponse') {
	
			$campaignId = $campaign["campaigns_integrations_list_name"];
			
			$send = addGREmailtoList($email, $campaignId, $autoresponderData->api_key);
			if($send['success']) {
				if($redirectUrl) {
					redirect($redirectUrl);
				}
				else {
					$_SESSION['msg'] = 'Success!' ;
				}
			}
	
		} elseif($platformValue == 'mailchimp') {
	
			$listId = $campaign["campaigns_integrations_list_name"];
			
			$send = addEmailtoMailchimpList($email, $listId, $autoresponderData->api_key);
			if($send['success']) {
				if($redirectUrl) {
					redirect($redirectUrl);
				}
				else {
					$_SESSION['msg'] = 'Success!' ;
				}
			}
	
		} elseif($platformValue == 'convertkit') {
	
			$tagId = $campaign["campaigns_integrations_list_name"];
			
			$send = addEmailToTags($email, $tagId, $autoresponderData->api_key) ;
			if($send['success']) {
				if($redirectUrl) {
					redirect($redirectUrl);
				}
				else {
					$_SESSION['msg'] = 'Success!' ;
				}
			}
	
		} elseif($platformValue == 'sendlane') {
	
			$listId = $campaign["campaigns_integrations_list_name"];
			
			$send = addEmailToListSendlane($email, $listId, $autoresponderData->api, $autoresponderData->hash);
			if($send['success']) {
				if($redirectUrl) {
					redirect($redirectUrl);
				}
				else {
					$_SESSION['msg'] = 'Success!' ;
				}
			}
	
		} elseif($platformValue == 'hubspot') {
	
			$send = createContactHubspot($email, $autoresponderData->api) ;
			if($send['success']) {
				$id = $campaign["campaigns_integrations_list_name"];
				$add = addEmailToHubspotList($email, $id, $autoresponderData->api);
				if($add['success']){
					if($redirectUrl) {
						redirect($redirectUrl);
					}
					else {
						$_SESSION['msg'] = 'Success!' ;
					}
				}
			}
	
		} elseif($platformValue == 'activecampaign') {
			
			$create = createContactActiveCampaign($autoresponderData->api_key, $autoresponderData->acc_url, $email) ; //create contact
			if($create['success']) {
				foreach ($create['data'] as $list){
					$contactId = $list->id;
				}
				//echo "<script>console.log('$contactId')</script>";    
				$listId = $campaign["campaigns_integrations_list_name"];
				$add = addtoListActiveCampaign($autoresponderData->api_key, $autoresponderData->acc_url, $contactId, $listId); //add contact to list
				if($add['success']){
					if($redirectUrl) {
						redirect($redirectUrl);
					}
					else {
						$_SESSION['msg'] = 'Success!' ;
					}
				}
			}
	
		} elseif($platformValue == 'aweber') {
		
			$id = $campaign["campaigns_integrations_list_name"];
			
			// refresh token
			$refresh = refreshAccToken($autoresponderData->refresh_token, $client_id, $secret_key);
			if ($refresh['success']) {
				$access_token = strip($refresh['data']->access_token);
			}
			
			// add subscriber
			$send = addAWSubcriber($autoresponderData->account_id, $id, $email, $access_token) ;
			
			if($send['success']){
				if($redirectUrl) {
					redirect($redirectUrl);
				}
				else {
					$_SESSION['msg'] = 'Success!' ;
				}
			}
			
		} elseif($platformValue == 'constantcontact') {
			
			$list_id = $campaign["campaigns_integrations_list_name"];
			
			$refresh = refreshCCToken($autoresponderData->refresh_token);
	
			if ($refresh['success']) {
				$autoresponderData->access_token  = strip($refresh['data']->access_token);
				$autoresponderData->refresh_token = strip($refresh['data']->refresh_token);
	
				$constantcontact = json_encode($autoresponderData);
				$DB->query("UPDATE {$dbprefix}api SET data = '$constantcontact' WHERE user_id = '$user' AND platform = 'constantcontact' ");
	
				$add = addContactCC($autoresponderData->access_token, $email, $list_id) ;
	
				if ($add['success']) {
					if($redirectUrl) {
						redirect($redirectUrl);
					}
					else {
						$_SESSION['msg'] = 'Success!' ;
					}
				}
			}
			
		} elseif($platformValue == 'sendiio') {
			
			$listId = $campaign["campaigns_integrations_list_name"];
			
			$send = subscribeEmailToList($email, $listId, $autoresponderData->api_token, $autoresponderData->api_secret);
			if($send['success']) {
				if($redirectUrl) {
					redirect($redirectUrl);
				}
				else {
					$_SESSION['msg'] = 'Success!' ;
				}
			}
	
		}
	}
?>