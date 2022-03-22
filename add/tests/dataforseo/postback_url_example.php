<?php
function _in_logit_POST($id_message, $data) {
	@file_put_contents(__DIR__."/postback_url_example.log", PHP_EOL.date("Y-m-d H:i:s").": ".$id_message.PHP_EOL."---------".PHP_EOL.print_r($data, true).PHP_EOL."---------", FILE_APPEND);
}

$post_data_in = file_get_contents('php://input');
if (!empty($post_data_in)) {
	$post_arr = json_decode($post_data_in, true);
	if ((!empty($post_arr)) AND ($post_arr["status"] == "ok")) {
		foreach($post_arr["results"]["organic"] as $tasks_row) {
			_in_logit_POST($tasks_row["result_position"], $tasks_row);
			//do something with results
		}
		echo "ok";
	} else {
		//_in_logit_POST('error decode', $post_data_in);
		echo "error";
	}
} else {
	echo "empty POST";
}
?>
