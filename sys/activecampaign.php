<?php
	//get Lists
	function getActiveCampaignList($api_key, $acc_url) {
        $api_key = trim($api_key);
		$acc_url = trim($acc_url);
		$curl = curl_init();

        curl_setopt_array($curl, [
			CURLOPT_URL => $acc_url."/api/3/lists",
            //CURLOPT_URL => "{$acc_url}/api/3/lists",
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
				"Api-Token: $api_key",
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
	
	//create contact
	function createContactActiveCampaign($api_key, $acc_url, $email) {
        $api_key = trim($api_key);
		$acc_url = trim($acc_url);
		$curl = curl_init();
		
		$data = [
			"contact" => [
                "email" => $email
            ]
        ];

        curl_setopt_array($curl, [
			CURLOPT_URL => $acc_url."/api/3/contacts",
            //CURLOPT_URL => "{$acc_url}/api/3/contacts",
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
				"Api-Token: $api_key",
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
	
	//add contact to list
	function addtoListActiveCampaign($api_key, $acc_url, $contactId, $listId) {
        $api_key = trim($api_key);
		$acc_url = trim($acc_url);
		$curl = curl_init();
		
		$data = [
			"contactList" => [
				"list" => $listId,
				"contact" => $contactId,
				"status" => '1'
			]
        ];

        curl_setopt_array($curl, [
			CURLOPT_URL => $acc_url."/api/3/contactLists",
            //CURLOPT_URL => "{$acc_url}/api/3/contactLists",
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
				"Api-Token: $api_key",
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