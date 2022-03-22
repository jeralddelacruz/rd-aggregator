<?php
    include(dirname(__FILE__) . "/../sys/class.db.php");
    include(dirname(__FILE__) . "/../sys/config.php");
    include(dirname(__FILE__) . "/../sys/func.php");
    
    $DB = new db($dbhost, $dbuser, $dbpass, $dbname);
	$DB->connect();
	if($DB->connect < 1){
		echo "Can't go on, DB not initialized.";
		exit;
	}

    function returnResponse($data, $success = true) {
        ob_clean();
        header_remove();
        header("Content-type: application/json; charset=utf-8");
        
        if ($success) {
            http_response_code(200);
        } else {
            http_response_code(500);
        }
        
        echo json_encode($data);
        exit();
    }
    
    if (isset($_POST['action'])) {
        $news_id = $_POST['news_id'];
        $action = $_POST['action'];
        
        $update_news = $DB->query("UPDATE {$dbprefix}news SET 
            status = '{$action}'
        WHERE news_id = '{$news_id}'");
        
        $is_success = false;
        if( $update_news ) {
            $is_success = true;
        }
        
        $data = [
            'success' => $is_success,
            'message' => $is_success ? 'Status has been updated' : 'Failed to update status'
        ];
        
        returnResponse($data);
    }