<?php
    
	$AUTHORIZE_URL = 'https://auth.aweber.com/oauth2/authorize';
    $STATE = 'magick';
    
	function getAccToken ($auth_code) {
        global $WEBSITE;

        $AW_CLIENT_ID = $WEBSITE['aweber_client_id'];
        $AW_CLIENT_SECRET = $WEBSITE['aweber_client_secret'];
        $REDIRECT_URI = 'urn:ietf:wg:oauth:2.0:oob';

		$auth_code = trim($auth_code);
		
		$curl = curl_init();
        $data = [
            "grant_type" => "authorization_code",
            "code" => $auth_code,
            "redirect_uri" => $REDIRECT_URI
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://auth.aweber.com/oauth2/token?client_id=".$AW_CLIENT_ID."&client_secret=" .$AW_CLIENT_SECRET,
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
	
	
	function refreshAccToken ($refresh_token) {
        global $WEBSITE;
        $AW_CLIENT_ID = $WEBSITE['aweber_client_id'];
        $AW_CLIENT_SECRET = $WEBSITE['aweber_client_secret'];
        // $AW_CLIENT_ID = "1lzuJyYYTJm28DrBzdVZlxY7OcZNKDGG";
        // $AW_CLIENT_SECRET = "Hhm38JLBRjmb1bSqr0Fno93Coq18f75t";
        $REDIRECT_URI = 'urn:ietf:wg:oauth:2.0:oob';

		$refresh_token = trim($refresh_token);
		
		$curl = curl_init();
        $data = [
            "grant_type" => "refresh_token",
            "refresh_token" => $refresh_token
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://auth.aweber.com/oauth2/token?client_id=".$AW_CLIENT_ID."&client_secret=" .$AW_CLIENT_SECRET,
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
	
	
	function getAccount($access_token){
		$access_token = trim($access_token);
		$curl = curl_init();

        curl_setopt_array($curl, [
			CURLOPT_URL => "https://api.aweber.com/1.0/accounts",
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
	
	function getList($access_token, $account_id){
		$access_token = trim($access_token);
		$account_id = trim($account_id);
		$curl = curl_init();

        curl_setopt_array($curl, [
			CURLOPT_URL => "https://api.aweber.com/1.0/accounts/{$account_id}/lists",
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
	
	
	function addAWSubcriber($account_id, $id, $email, $access_token){
		$access_token = trim($access_token);
		$account_id = trim($account_id);
		$curl = curl_init();
        $data = [
            "email" => $email
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.aweber.com/1.0/accounts/{$account_id}/lists/{$id}/subscribers",
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

?>