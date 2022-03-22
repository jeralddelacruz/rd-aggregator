<?php
	require_once("reddit_config.php");

	// GETTING AN ACCESS TOKEN
	$username = REDDIT_USERNAME;
	$password = REDDIT_PASSWORD;
	$app_id = REDDIT_APP_ID;
	$app_secret = REDDIT_APP_SECRET;
	$redirect_uri = REDDIT_REDIRECT_URI;
	$scopes = REDDIT_SCOPES;
	$state = rand();
	
	function getAccessToken(){
		global $app_id, $app_secret, $redirect_uri;

		$api_endpoint = "https://www.reddit.com/api/v1/access_token";
	
		$params = array(
			"grant_type" => "authorization_code",
			"code" => $username,
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
		
		curl_close($ch);
		
		echo "<pre>";
		print_r($response);
		echo "</pre>";

		$access_token = $response->access_token;
		$access_token_type = $response->token_type;
	}

	function postToASubreddit($access_token, $access_token_type){
		// POSTING TO A SUB-REDDIT
		global $username;
		// CAME FROM reddit_get_access_token.php
		
		// CHANGE VALUE FOR SUB-BREDDIT NAME (COMMUNITY)
		$subreddit_name = "news";
		
		$subreddit_display_name = $username;
		$post_title = "Reddit API with PHP test.";
		$post_url = "https://pprofitfunnels.com/user/index.php?cmd=home";
		$post_text = "Sample text...";
		
		$api_endpoint_submit = "https://oauth.reddit.com/api/submit";
		
		// FOR POSTING LINKS
		$params = array(
			"url" => $post_url,
			"title" => $post_title,
			"sr" => $subreddit_name,
			"kind" => "link"
		);
		
		// FOR POSTING TEXT
		// $params = array(
		//     "url" => $post_url,
		//     "title" => $post_title,
		//     "text" => $post_text,
		//     "sr" => $subreddit_name,
		//     "kind" => "self"
		// );
		
		// CURL PROCESS
		$ch = curl_init($api_endpoint_submit);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $subreddit_display_name . " by /u/" . $username . " (Phapper 1.0)");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: " . $access_token_type . " " . $access_token));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		
		// CURL RESPONSE
		$response_raw = curl_exec($ch);
		$response = json_decode($response_raw);

		echo "<pre>";
		print_r($response);
		echo "</pre>";
		die();
		
		curl_close($ch);
	}

	function redditAuthorization(){
		global $app_id, $redirect_uri, $scopes, $state;

		$api_endpoint_authorization = "https://www.reddit.com/api/v1/authorize";

		$endpoint_get_params = "?response_type=code" . "&client_id=" . $app_id . "&redirect_uri=" . urlencode($redirect_uri) . "&scope=" . $scopes . "&state=" . $state;

		header("Location: " . $api_endpoint_authorization . $endpoint_get_params);
	}
?>