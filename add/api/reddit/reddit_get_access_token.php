<?php
	require_once("reddit_config.php");

	// GETTING AN ACCESS TOKEN
	$username = REDDIT_USERNAME;
	$password = REDDIT_PASSWORD;
	$app_id = REDDIT_APP_ID;
	$app_secret = REDDIT_APP_SECRET;
	
	$api_endpoint = "https://www.reddit.com/api/v1/access_token";
	
	$params = array(
		"grant_type" => "password",
		"username" => $username,
		"password" => $password
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
	
	echo "<pre>";
	print_r($response);
	echo "</pre>";
?>