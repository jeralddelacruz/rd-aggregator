<?php
	function validateHubspot ($api) {
        $api = trim($api);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.hubapi.com/integrations/v1/me?hapikey=" . $api,
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
                "cache-control: no-cache"
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $response = json_decode($response);
        $success = (isset($response->error) || $error) ? false : true;

        return [
            'success' => $success,
            'error' => $error,
            'data' => $response
        ];
    }
	
	function getHubspotList ($api) {
        $api = trim($api);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.hubapi.com/contacts/v1/lists?hapikey=" . $api,
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
	
	function createContactHubspot($email, $api) {
        
        $api = trim($api);
        $curl = curl_init();

        $data = [
            "properties" => [
                [
                    "property" => "email",
                    "value" => $email
                ]
            ]
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.hubapi.com/contacts/v1/contact/?hapikey=" . $api,
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

    function addEmailToHubspotList($email, $id, $api) {
        
        $api = trim($api);
        $curl = curl_init();

        $data = [
            // "api" => $api,
            "emails" => [
				$email
			]
            // "list" => [
            //     "id" => $id
            // ]
            // "list" => $id
        ];

        curl_setopt_array($curl, [
            //CURLOPT_URL => "https://api.hubapi.com/contacts/v1/lists/'".$id."'/add?hapikey=" . $api,
            CURLOPT_URL => "https://api.hubapi.com/contacts/v1/lists/{$id}/add?hapikey=" . $api,
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
?>