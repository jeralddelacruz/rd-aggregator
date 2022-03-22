<?php
function _in_logit_POST($data) {
	@file_put_contents(__DIR__."/api2_pstbu_test.log", PHP_EOL.date("Y-m-d H:i:s").": ".PHP_EOL."---------".PHP_EOL.print_r($data, true).PHP_EOL."---------", FILE_APPEND);
}

$post_data_gz = file_get_contents('php://input');
if (!empty($post_data_gz)) {
	$post_data_arr = json_decode(gzdecode($post_data_gz), true);
	_in_logit_POST($post_data_arr);
	if (($post_data_arr !== false) and (isset($post_data_arr['html'])) and (!empty($post_data_arr['html']))) {
		/*
		array(
		[post_id] => "your_keyword_id#your_se_id#your_some_id", //your unique ID (like you DB "keyword_id" field. type 'string'). will be returned with all results
		[task_id] => 98825073,
		[keyword] => "Bornesenge",
		[se_domain] => "google.dk",
		[country] => "DK",
		[device] => "desktop",
		[html] => array(...
		*/
		_in_logit_POST($post_data_arr['task_id']);

		foreach($post_data_arr['html'] as $html_page) {
			//do something with post results
		}
		echo '{"status":"ok", "sleep":"'.$sleep_rand_str.'"}';
	} else {
		http_response_code(405);
		echo '{"status":"error", "message":"invalid POST data!", "sleep":"'.$sleep_rand_str.'"}';
	}
} else {
	http_response_code(405);
	echo '{"status":"error", "message":"empty POST data!", "sleep":"'.$sleep_rand_str.'"}';
}
?>