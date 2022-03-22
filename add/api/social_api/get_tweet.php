<?php
    
    require_once("social_media_config.php");
    require_once("TwitterAPIExchange.php");
    
    $settings = array(
        "oauth_access_token" => TWITTER_ACCESS_TOKEN,
        "oauth_access_token_secret" => TWITTER_ACCESS_TOKEN_SECRET,
        "consumer_key" => TWITTER_CONSUMER_KEY,
        "consumer_secret" => TWITTER_CONSUMER_SECRET
        );
    
    // END-POINT URL FOR GETTING A TWEET
    $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
    
    $requestMethod = "GET";
    // STATUS IS THE MESSAGE YOU WANT TO POST
    $getField = "?screen_name=TestTes51848627&count=1";
    
    // NEW OBJECT FOR SENDING THE DATA FROM SITE TO TWITTER
    $twitter = new TwitterAPIExchange($settings);
    $twitter->setGetField($getField);
    $twitter->buildOauth($url, $requestMethod);
    
    $response = $twitter->performRequest(true, array(CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0));
    $tweet = json_decode($response, true);
    
    echo "<pre>";
    print_r($tweet);
    echo "</pre>";
    
?>