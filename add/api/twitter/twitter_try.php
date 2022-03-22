<?php
	// LOCAL INITIALIZATION
	$twitter_consumer_key = "R347TtU4CmRFo15lMvd2kq67i";
	$twitter_consumer_secret = "03W49BOd0vtqw5lcm1w3gvgADozQDEI0wuvQ6lSG5JL6MV5mJN";
	$access_token = "1161902823950303232-0Pe40Llv5ZIHUmTFWgsxQpB8E3ybe6";
	$access_token_secret = "pOdTVi8svi7S6QakcRPpmSe4Rc7YJx0d7Kv6jidbK0Imf";
	$callback_url = "https://prbsites.com/add/api/twitter/twitter_try.php";

	// INITIALIZE SDK
	require "vendor/autoload.php";
	use Abraham\TwitterOAuth\TwitterOAuth;

	// ESTABLISH CONNECTION
	$connection = new TwitterOAuth($twitter_consumer_key, $twitter_consumer_secret, $access_token, $access_token_secret);
	$content = $connection->get("account/verify_credentials");
	$statuses = $connection->get("statuses/home_timeline", ["count" => 5, "exclude_replies" => true]);

	echo "<pre>";
		print_r($statuses);
	echo "</pre>";
?>