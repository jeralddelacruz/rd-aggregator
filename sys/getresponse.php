<?php 
    
    function validateGetResponse($apiKey) {
        $apiKey = trim($apiKey);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.getresponse.com/v3/accounts",
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
                "X-Auth-Token: api-key $apiKey",
                "cache-control: no-cache"
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $response = json_decode($response);
        $success = (isset($response->httpStatus) || $error) ? false : true;

        return [
            'success' => $success,
            'error' => $error,
            'data' => json_decode($response)
        ];
    }

    function getGetResponseList($apiKey) {
        $apiKey = trim($apiKey);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.getresponse.com/v3/campaigns",
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
                "X-Auth-Token: api-key $apiKey",
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

    function addGREmailtoList($email, $campaignId, $apiKey) {
        
        $apiKey = trim($apiKey);
        $curl = curl_init();
        $data = [
            "email" => $email,
            "campaign" => [
                "campaignId" => $campaignId
            ]
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.getresponse.com/v3/contacts",
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
                "X-Auth-Token: api-key $apiKey",
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