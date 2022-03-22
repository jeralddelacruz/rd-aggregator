<?php
	// POSTING TO A SUB-REDDIT

	// CAME FROM reddit_get_access_token.php
	$access_token = "493838948629-PNHCP89UIhuWERdA8FW_6BGjP34";
	$access_token_type = "bearer";
	
	$username = "markydevtest101";
	
	// CHANGE VALUE FOR SUB-BREDDIT NAME (COMMUNITY)
	$subreddit_name = "news";
	
	$subreddit_display_name = "markydevtest101";
	$post_title = "Reddit API with PHP test.";
	$post_url = "https://pprofitfunnels.com/user/index.php?cmd=home";
	$post_text = "Sample text...";
	
	$api_endpoint = "https://oauth.reddit.com/api/submit";
	
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
	$ch = curl_init($api_endpoint);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, $subreddit_display_name . " by /u/" . $username . " (Phapper 1.0)");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: " . $access_token_type . " " . $access_token));
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