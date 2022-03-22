<?php

    function getServer ($apiKey) {
        if (strstr($apiKey, "-")){
            list($key, $dc) = explode("-", $apiKey, 2);
        }

        return $dc ? $dc : "us1";
    }

    function validateMailchimp ($apiKey) {
        $apiKey = trim($apiKey);
        $server = getServer($apiKey);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://{$server}.api.mailchimp.com/3.0/",
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
            CURLOPT_USERPWD => 'anystring' . ':' . $apiKey
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

    function getMailchimpList ($apiKey) {
        $apiKey = trim($apiKey);
        $server = getServer($apiKey);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://{$server}.api.mailchimp.com/3.0/lists",
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
            CURLOPT_USERPWD => 'anystring' . ':' . $apiKey
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

    function addEmailtoMailchimpList ($email, $listId, $apiKey) {
        $apiKey = trim($apiKey);
        $server = getServer($apiKey);
        $curl = curl_init();

        $data = [
            "email_address" => $email,
            'status' => 'subscribed'
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://{$server}.api.mailchimp.com/3.0/lists/{$listId}/members",
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
            CURLOPT_USERPWD => 'anystring' . ':' . $apiKey,
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