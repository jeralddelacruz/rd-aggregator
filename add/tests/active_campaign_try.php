<?php
	// NEEDS
	$api_key = "c436fb7ce2345e4d92a36a4c49ec87a16a8fa1d95413c55f8a0c7c484ab29a3d17415abe";
	$account_url = "https://myseosucks.api-us1.com";
	$list_id = "27";

	// FIELDS
	$email = "tempsdorarssgrrr.email@gmail.com";

	$create = createContactActiveCampaign($api_key, $account_url, $email) ; //create contact

	if($create['success']) {
		foreach ($create['data'] as $list){
			$contactId = $list->id;
		}

		$add = addtoListActiveCampaign($api_key, $account_url, $contactId, $list_id); //add contact to list
		if($add['success']){
			$response = "Added successfully.";
		}
	}

	// FUNCTIONS
	//get Lists
	function getActiveCampaignList($api_key, $account_url) {
		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => $account_url . "/api/3/lists",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_HTTPHEADER => [
				"Content-Type: application/json",
				"Api-Token: " . $api_key,
				"cache-control: no-cache"
			],
		]);

		$response = curl_exec($curl);
		$error = curl_error($curl);

		curl_close($curl);
		
		$success = $error ? false : true;

		return [
			'success' => $success,
			'error' => $error,
			'data' => json_decode($response)
		];
	}
	
	//create contact
	function createContactActiveCampaign($api_key, $account_url, $email) {
		$curl = curl_init();
		
		$data = [
			"contact" => [
				"email" => $email
			]
		];

		curl_setopt_array($curl, [
			CURLOPT_URL => $account_url."/api/3/contacts",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_HTTPHEADER => [
				"Content-Type: application/json",
				"Api-Token: " . $api_key,
				"cache-control: no-cache"
			],
			CURLOPT_POSTFIELDS => json_encode($data),
		]);

		$response = curl_exec($curl);
		$error = curl_error($curl);

		curl_close($curl);
		
		$success = $error ? false : true;

		return [
			'success' => $success,
			'error' => $error,
			'data' => json_decode($response)
		];
	}
	
	//add contact to list
	function addtoListActiveCampaign($api_key, $account_url, $contactId, $list_id) {
		$curl = curl_init();
		
		$data = [
			"contactList" => [
				"list" => $list_id,
				"contact" => $contactId,
				"status" => '1'
			]
		];

		curl_setopt_array($curl, [
			CURLOPT_URL => $account_url . "/api/3/contactLists",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_HTTPHEADER => [
				"Content-Type: application/json",
				"Api-Token: " . $api_key,
				"cache-control: no-cache"
			],
			CURLOPT_POSTFIELDS => json_encode($data),
		]);

		$response = curl_exec($curl);
		$error = curl_error($curl);

		curl_close($curl);
		
		$success = $error ? false : true;

		return [
			'success' => $success,
			'error' => $error,
			'data' => json_decode($response)
		];
	}
?>