<?php
    function validateSendiio ($apiToken, $apiSecret) {
        $data = [
            'token' => trim($apiToken),
            'secret' => trim($apiSecret)
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://sendiio.com/api/v1/auth/check",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
              "Content-Type: application/json"
            ),
        ));

        $response = json_decode($response);
        $success = (isset($response->error) && $response->error !== 0) ? false : true;

        curl_close($curl);

        return [
            'success' => $success,
            'error' => $error,
            'data' => $response
        ];
    }

    function getSendiioList ($apiToken, $apiSecret) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://sendiio.com/api/v1/lists/email",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
              "token: " . trim($apiToken),
              "secret: " . trim($apiSecret)
            ),
        ));
          
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

    function subscribeEmailToList($email, $listId, $apiToken, $apiSecret) {
        
        $curl = curl_init();

        $data = [
            "email_list_id" => $listId,
            "email" => $email
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://sendiio.com/api/v1/lists/subscribe/json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "token: " . trim($apiToken),
                "secret: " . trim($apiSecret),
                "Content-Type: application/json"
            ),
        ));

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