<?php
    $API_BASE_URL = ' https://api.convertkit.com/v3/';

    function validateConvertKit ($apiSecret) {
        $apiSecret = trim($apiSecret);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.convertkit.com/v3/account?api_secret=" . $apiSecret,
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

    function getConvertKitTags ($apiSecret) {
        $apiSecret = trim($apiSecret);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.convertkit.com/v3/tags?api_secret=" . $apiSecret,
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

    function addEmailToTags($email, $tagId, $apiKey) {
        
        $apiKey = trim($apiKey);
        $curl = curl_init();

        $data = [
            "api_key" => $apiKey,
            "email" => $email
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.convertkit.com/v3/tags/{$tagId}/subscribe",
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