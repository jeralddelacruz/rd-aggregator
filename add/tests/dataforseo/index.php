<?php
//You can download this file from here https://api.dataforseo.com/_examples/php/_php_RestClient.zip
require('RestClient.php');
$api_url = 'https://api.dataforseo.com/';
try {
    //Instead of 'login' and 'password' use your credentials from https://my.dataforseo.com/#api_dashboard
    $client = new RestClient($api_url, null, 'chris@greysmokemedia.com', 'gVjbqsgzLCutQIKg');
} catch (RestClientException $e) {
    echo "\n";
    print "HTTP code: {$e->getHttpCode()}\n";
    print "Error code: {$e->getCode()}\n";
    print "Message: {$e->getMessage()}\n";
    print  $e->getTraceAsString();
    echo "\n";
    exit();
}
$post_array = array();

// example #2 - will return results faster than #1, but is simpler than example #3
// All parameters should be set in the text format.
// All data will be will be searched, compared to our internal parameters
// and used as:
// "se_id", "loc_id", "key_id" ( actual and
// fresh list can be found here: "se_id": https://api.dataforseo.com/v2/cmn_se ,
// "loc_id": https://api.dataforseo.com/v2/cmn_locations )
// If a task was set successfully, this *_id will be returned in results: 'v2/rnk_tasks_post' so you can use it.
// The setting of a task can fail, if you set not-existent search engine, for example.
// Disadvantages: The process of search and comparison of provided data to our internal parameters may take some time.
$my_unq_id = mt_rand(0, 30000000); // your unique ID. we will return it with all results. you can set your database ID, string, etc.
$post_array[$my_unq_id] = array(
    "priority" => 1,
    "site" => "google.com",
    "se_name" => "google.co.uk",
    "se_language" => "English",
    "loc_name_canonical" => "London,England,United Kingdom",
    "key" => mb_convert_encoding("seo data api", "UTF-8")
    //,"pingback_url" => "http://your-domain.com/pingback_url_example.php?task_id=$task_id" //see pingback_url_example.php script
);

// This example has a 3 elements, but in the case of large number of tasks - send up to 100 elements per POST request
if (count($post_array) > 0) {
    try {
        // POST /v2/rnk_tasks_post/$tasks_data
        // $tasks_data must by array with key 'data'
        $task_post_result = $client->post('/v2/rnk_tasks_post', array('data' => $post_array));
        echo "<pre>";
        echo "RESPONSE OF INITIAL INPUT <br />";
        print_r($task_post_result);
        echo "</pre>";

        echo "<br />";

        $task_id = $task_post_result["results"][$my_unq_id]["task_id"];

        $task_get_result = $client->get('/v2/rnk_tasks_get/' . $task_id);
        echo "<pre>";
        echo "RESPONSE OF GETTING TASK <br />";
        print_r($task_get_result);
        echo "</pre>";

        //do something with post results
        $post_array = array();
    } catch (RestClientException $e) {
        echo "\n";
        print "HTTP code: {$e->getHttpCode()}\n";
        print "Error code: {$e->getCode()}\n";
        print "Message: {$e->getMessage()}\n";
        print  $e->getTraceAsString();
        echo "\n";
    }
}
$client = null;
?>
