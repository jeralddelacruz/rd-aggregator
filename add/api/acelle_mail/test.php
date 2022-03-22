<?php
	// API TEST FOR ACELLE MAIL (INBOXEY)
	$api_endpoint_list = "http://inboxey.net/api/v1/lists?api_token=k8EP0F9D0wQVf0RPOqxuQFcSJJ7fJzkkn8cp13TOGfkGji2nCa1NJq3OA6qC";

	// $params = array(
	// 	"api_token" => "k8EP0F9D0wQVf0RPOqxuQFcSJJ7fJzkkn8cp13TOGfkGji2nCa1NJq3OA6qC"
	// );

	// CURL PROCESS
	$ch = curl_init($api_endpoint_list);
	// curl_setopt($ch, CURLOPT_USERPWD, $app_id . ":" . $app_secret);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	// curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

	// CURL RESPONSE
	$response_raw = curl_exec($ch);
	$response = json_decode($response_raw);

	curl_close($ch);

	// var_dump($response->updated_at); die();

	echo "<pre>";
	print_r($response);
	echo "</pre>";
?>