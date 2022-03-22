<?php

    //exchange auth to token
    function getConstantContactAuthCode() {
        global $WEBSITE, $SCRIPTURL;
        
        $baseURL = "https://api.cc.email/v3/idfed";
        $redirectURI = urlencode("{$SCRIPTURL}/user/index.php?cmd=autoresponder");

        $data = [
            'client_id' => $WEBSITE['cc_client_id'],
            'redirect_uri' => $redirectURI,
            'response_type' => 'code',
            'scope' => 'account_read+contact_data+campaign_data'
        ];

        $parameters = http_build_query($data);

        return "{$baseURL}?{$parameters}";
    }

	function getCCToken ($auth_code) {
        global $WEBSITE, $SCRIPTURL;

		$auth_code = trim($auth_code);
        $redirectURI = urlencode("{$SCRIPTURL}/user/index.php?cmd=autoresponder");
        $CC_CLIENT_ID = trim($WEBSITE['cc_client_id']);
        $CC_CLIENT_SECRET = trim($WEBSITE['cc_client_secret']);
		
		$auth = "{$CC_CLIENT_ID}:{$CC_CLIENT_SECRET}";
		$credentials = base64_encode($auth);
		
		$curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://idfed.constantcontact.com/as/token.oauth2?code=".$auth_code."&redirect_uri=".$redirectURI."&grant_type=authorization_code",
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
                "Authorization: Basic $credentials",
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
	
	
	//get list
	function getCCList($access_token){
		$access_token = trim($access_token);
		$curl = curl_init();

        curl_setopt_array($curl, [
			CURLOPT_URL => "https://api.cc.email/v3/contact_lists",
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
				"Authorization: Bearer $access_token",
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
	
	//add contact
	function addContactCC($access_token, $email, $list_id){
		$access_token = trim($access_token);
		
		$curl = curl_init();
        $data = [
            "email_address" => $email,
			"list_memberships" => [
				$list_id
			]
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.cc.email/v3/contacts/sign_up_form",
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
                "Authorization: Bearer $access_token",
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
	
	//refresh token
	function refreshCCToken($refresh_token){
        global $WEBSITE, $SCRIPTURL;
        
        $refresh_token = trim($refresh_token);
        $CC_CLIENT_ID = trim($WEBSITE['cc_client_id']);
        $CC_CLIENT_SECRET = trim($WEBSITE['cc_client_secret']);
		
		$auth = "{$CC_CLIENT_ID}:{$CC_CLIENT_SECRET}";
        $credentials = base64_encode($auth);
        
		$curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://idfed.constantcontact.com/as/token.oauth2?refresh_token=".$refresh_token."&grant_type=refresh_token",
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
                "Authorization: Basic $credentials",
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
?>