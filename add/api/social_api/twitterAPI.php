<?php
    
    session_start();
    
    require_once("social_media_config.php");
    require_once("TwitterAPIExchange.php");
    
    // INITIALIZE VARIABLES - GET FROM DEV ACCOUNT
    $consumer_key = TWITTER_CONSUMER_KEY;
    $consumer_secret = TWITTER_CONSUMER_SECRET;
    $access_token = TWITTER_ACCESS_TOKEN;
    $access_token_secret = TWITTER_ACCESS_TOKEN_SECRET;
    $callback_url = TWITTER_CALLBACK_URL;
    
    // ABRAHAM GIT REPOSITORY
    require_once("autoload.php");
    use Abraham\TwitterOAuth\TwitterOAuth;
    
    // CONNECT TO TWITTER API
    $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
    $content = $connection->get("account/verify_credentials");
    
    
    if (!empty($_GET['oauth_verifier'])) {
            // exchange the verifier for the keys
            $verifier = trim($_GET['oauth_verifier']);
            $oauthToken = trim($_GET['oauth_token']);
            $resp = $connection->oauth('oauth/access_token', ['oauth_token' => $oauthToken, 'oauth_verifier' => $verifier]);
    
            unset($_SESSION['tmp_oauth_token']);
            unset($_SESSION['tmp_oauth_token_secret']);
    
            $_SESSION['twitter_oauth_token'] = $resp['oauth_token'];
            $_SESSION['twitter_oauth_token_secret'] = $resp['oauth_token_secret'];

            // $save = saveTwitter($resp);

            echo json_encode([
                'success' => true,
                'message' => 'Successfully connected to Twitter.'
            ]);

            session_destroy();

            exit;
        }
    
        if (empty($_SESSION['twitter_oauth_token']) || empty($_SESSION['twitter_oauth_token_secret'])) {
            // start the old gal up
            $resp = $connection->oauth('oauth/request_token', array('oauth_callback' => $callback_url));
        
            // // Get the result
            $_SESSION['tmp_oauth_token'] = $resp['oauth_token'];
            $_SESSION['tmp_oauth_token_secret'] = $resp['oauth_token_secret'];
    
            $url = $connection->url('oauth/authorize', array('oauth_token' => $resp['oauth_token']));
    
            // echo '<a href="'.$url.'">Connect Twitter</a>';
            header("Location: {$url}");
            exit;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Oops! Something went wrong!'
        ]);

        exit;
    
?>