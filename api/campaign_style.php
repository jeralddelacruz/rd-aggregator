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
        $action = $_POST['action'];
        $campaign_id = $_POST['campaign_id'];
        $template_id = $_POST['template_id'];
        
        if ($action === 'edit_template') {
            $result = $DB->query("UPDATE {$dbprefix}campaigns SET `template_id` = '{$template_id}' WHERE `campaign_id` = '{$campaign_id}'");
            
            var_dump($result);
            exit();
            
            if ($result) {
                returnResponse([
                    'success' => true,
                    'message' => "Campaign #{$news_id} template has been updated successfully."
                ]);
            }
            
            returnResponse([
                'success' => false,
                'message' => "Something went wrong, please try again!"
            ], false);
        }
    }
    
    returnResponse([
        'success' => false,
        'message' => "The action provided doesn't exist, please try again!"
    ], false);