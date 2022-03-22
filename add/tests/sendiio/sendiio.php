<?php
	$api_key = "41e8c3c4d17ac85075ebf5549cc90b95d5abc113";
	$api_secret = "zQEpj49bDCwduyOsMYVhHTIlW6iKZS2qU3eoGgPmifN1O7Dh3GxAs25yjLXZoYS0E9rzquM4kbwdFPav";
	$list_id = "eyJpdiI6IkhGbVZUUnNXdFNhdVI2dVwvbEFQR05BPT0iLCJ2YWx1ZSI6IkRcL2ZvR2NVc0RhMXN1YVRsNVp4azhiSGRLTVBYRllYTzAwQXpMd21aTW1zPSIsIm1hYyI6Ijg4OTU0MzA3N2UxMmIzMWE2NDRlYTAyNmVkZmMwNTQwYmVhMGEwZmU3ZGM1NDAwNDg2ZTNlZjlhNzU1ZTk3NTQifQ==";
	$email = "test2@gmail.com";

	function validateSendiio($api_key, $api_secret){
		$data = [
			"token" => $api_key,
			"secret" => $api_secret
		];

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://sendiio.com/api/v1/auth/check',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => json_encode($data),
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;
	}

	function getSendiioList($api_key, $api_secret){
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://sendiio.com/api/v1/lists/email',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
			'token: ' . $api_key,
			'secret: ' . $api_secret
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;
	}

	function subscribeToSendiioList($email, $list_id, $api_key, $api_secret) {
		$data = [
			"email_list_id" => $list_id,
			"email" => $email
		];

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://sendiio.com/api/v1/lists/subscribe/json',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => json_encode($data),
		  CURLOPT_HTTPHEADER => array(
			'token: ' . $api_key,
			'secret: ' . $api_secret,
			'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;
	}

	subscribeToSendiioList($email, $list_id, $api_key, $api_secret);
?>