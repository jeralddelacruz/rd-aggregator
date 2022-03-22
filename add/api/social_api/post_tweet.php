<?php
    
    require_once("social_media_config.php");
    require_once("TwitterAPIExchange.php");
    
    $settings = array(
        "oauth_access_token" => TWITTER_ACCESS_TOKEN,
        "oauth_access_token_secret" => TWITTER_ACCESS_TOKEN_SECRET,
        "consumer_key" => TWITTER_CONSUMER_KEY,
        "consumer_secret" => TWITTER_CONSUMER_SECRET
        );
    
    // END-POINT URL FOR POSTING A TWEET
    $url = "https://api.twitter.com/1.1/statuses/update.json";
    
    $requestMethod = "POST";
    // STATUS IS THE MESSAGE YOU WANT TO POST
    $apiData = array(
        "status" => "Test 2 tweet coming from PHP. #PHP #TwitterAPI"
        );
    
    // NEW OBJECT FOR SENDING THE DATA FROM SITE TO TWITTER
    $twitter = new TwitterAPIExchange($settings);
    $twitter->buildOauth($url, $requestMethod);
    $twitter->setPostFields($apiData);
    
    $response = $twitter->performRequest(true, array(CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0));
    
    echo "<pre>";
    print_r(json_decode($response, true));
    echo "</pre>";
    
?>