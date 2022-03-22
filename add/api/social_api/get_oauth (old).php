<?php
    
    session_start();
    
    require_once("social_media_config.php");
    require_once("TwitterAPIExchange.php");
    
    class ppTwitterAPI{
        const TWITTER_API_DOMAIN = "https://api.twitter.com/";
        
        private $_consumerKey;
        private $_consumerSecret;
        
        public function __construct($consumerKey, $consumerSecret){
            $this->_consumerKey = $consumerKey;
            $this->_consumerSecret = $consumerSecret;
        }
        
        public function getDataForLogin($callbackURL){
            $requestToken = $this->getRequestToken($callbackURL);
            $requestToken["twitter_login_url"] = $this->getLoginURL($requestToken);
            
            // echo "<pre>";
            // print_r($requestToken);
            // die();
            
            if("Ok" == $requestToken["status"]){
                $_SESSION["request_oauth_token"] = $requestToken["api_data"]["oauth_token"];
                $_SESSION["request_oauth_token_secret"] = $requestToken["api_data"]["oauth_token_secret"];
            }
            
            return $requestToken;
        }
        
        public function getLoginURL($requestToken){
            $loginURL = "";
            
            if("Ok" == $requestToken["status"]){
                $endpoint = self::TWITTER_API_DOMAIN . "oauth/authorize";
                $params = array("oauth_token" => $requestToken["api_data"]["oauth_token"]);
                
                $loginURL = $endpoint . "?" . http_build_query($params);
            }
            
            return $loginURL;
        }
        
        public function getRequestToken($callbackURL){
            $method = "POST";
            $endpoint = self::TWITTER_API_DOMAIN . "oauth/request_token";
            $authorizationParams = array(
                "oauth_callback" => rawurlencode($callbackURL),
                "oauth_consumer_key" => $this->_consumerKey,
                "oauth_nonce" => md5(microtime() . mt_rand()),
                "oauth_signature_method" => "HMAC-SHA1",
                "oauth_timestamp" => time(),
                "oauth_version" => "1.0"
            );
            
            // echo "<pre>";
            // print_r($authorizationParams);
            // die();
            
            $authorizationParams["oauth_signature"] = $this->getSignature($method, $endpoint, $authorizationParams);
            
            // echo "<pre>";
            // print_r($authorizationParams["oauth_signature"]);
            // die();
            
            $apiParams = array(
                "method" => $method,
                "endpoint" => $endpoint,
                "authorization" => $this->getAuthorizationString($authorizationParams),
                "url_params" => array()
            );
            
            // echo "<pre>";
            // print_r($apiParams);
            // die();
            
            return $this->makeAPICall($apiParams);
        }
        
        public function makeAPICall($apiParams){
            // FOR VERIFYING CERTIFICATE
            $curlOptions = array(
                CURLOPT_URL => $apiParams["endpoint"],
                CURLOPT_CAINFO => PATH_TO_CERT,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_SSL_VERIFYPEER => TRUE,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_HEADER => TRUE,
                CURLOPT_HTTPHEADER => array(
                    "Accept: application/json",
                    $apiParams["authorization"],
                    "Expect:"
                )
            );
            
            // echo "<pre>";
            // print_r($curlOptions);
            // die();
            
            // FOR NOT VERIFYING CERTIFICATE
            // $curlOptions = array(
            //     CURLOPT_URL => $apiParams["endpoint"],
            //     CURLOPT_RETURNTRANSFER => TRUE,
            //     CURLOPT_SSL_VERIFYPEER => 0,
            //     CURLOPT_SSL_VERIFYHOST => 0,
            //     CURLOPT_HEADER => TRUE,
            //     CURLOPT_HTTPHEADER => array(
            //         "Accept: application/json",
            //         $apiParams["authorization"],
            //         "Expect:"
            //     )
            // );
            
            if("POST" == $apiParams["method"]){
                $curlOptions[CURLOPT_POST] = TRUE;
                $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($apiParams["url_params"]);
            }
            elseif("GET" == $apiParams["method"]){
                $curlOptions[CURLOPT_URL] .= "?" . http_build_query($apiParams["url_params"]);
            }
            
            // echo "<pre>";
            // print_r($curlOptions[CURLOPT_POST]);
            // die();
            
            $ch = curl_init();
            curl_setopt_array($ch, $curlOptions);
            
            // echo "<pre>";
            // print_r($ch);
            // die();
            
            $apiResponse = curl_exec($ch);
            $responseParts = explode("\r\n\r\n", $apiResponse);
            $responseBody = array_pop($responseParts);
            $responseBodyJSON = json_decode($responseBody);
            
            if(curl_errno($ch)){
                echo curl_error($ch);
            }
            
            // echo "<pre>";
            // print_r($apiResponse);
            // die();
            
            if(json_last_error() == JSON_ERROR_NONE){
                $response = json_decode($responseBody, true);
            }
            else{
                parse_str($responseBody, $response);
            }
            
            if(200 == curl_getinfo($ch, CURLINFO_HTTP_CODE)){
                $status = "Ok";
                $message = "";
            }
            else{
                $status = "Fail";
                $message = isset($response["errors"][0]["message"]) ? $response["errors"][0]["message"] : "Unauthorized";
            }
            
            // echo "<pre>";
            // print_r($message);
            // die();
            
            curl_close($ch);
            
            return array(
                "status" => $status,
                "message" => $message,
                "api_data" => $response,
                "endpoint" => $curlOptions[CURLOPT_URL],
                "authorization" => $apiParams["authorization"]
            );
        }
        
        public function getSignature($method, $endpoint, $authorizationParams){
            // SORT AUTHORIZATION PARAMETERS: REQUIRED BY TWITTER
            uksort($authorizationParams, "strcmp");
            
            // LOOP EACH PARAMETER AND URL ENCODE THEM
            foreach($authorizationParams as $key => $value){
                $authorizationParams[$key] = rawurlencode($key) . "=" . rawurlencode($value);
            }
            
            $signatureBase = array(
                rawurlencode($method),
                rawurlencode($endpoint),
                rawurlencode(implode("&", $authorizationParams)),
            );
                
            $signatureBaseString = implode("&", $signatureBase);
            
            $signatureKey = array(
                rawurlencode($this->_consumerSecret),
                ""
            );
            
            $signatureKeyString = implode("&", $signatureKey);
            
            return base64_encode(hash_hmac("sha1", $signatureBaseString, $signatureKeyString, true));
        }
        
        public function getAuthorizationString($authorizationParams){
            $authorizationString = "Authorization: OAuth";
            $count = 0;
            
            foreach($authorizationParams as $key => $value){
                $authorizationString .= !$count ? " " : ", ";
                $authorizationString .= rawurlencode($key) . "='" . rawurlencode($value) . "'";
                
                $count++;
            }
            
            return $authorizationString;
        }
    }
    
    // INSTANTIATE TWITTER CLASS
    $ppTwitterAPI = new ppTwitterAPI(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET);
    
    
    $twitterData =  $ppTwitterAPI->getDataForLogin(TWITTER_CALLBACK_URL);
    
    // echo "<pre>";
    // print_r($twitterData);
    // die();
    
?>