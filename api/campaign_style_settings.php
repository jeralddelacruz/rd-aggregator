<?php
    include(dirname(__FILE__) . "/../sys/class.db.php");
    include(dirname(__FILE__) . "/../sys/config.php");
    include(dirname(__FILE__) . "/../sys/func.php");
    
    $DB = new db("localhost", "root", "", "newscasc_db");
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
    
    // GET ALL THE DATA
    $feed_post_per_page         = $_POST['feed_post_per_page'];
    $feed_load_more             = $_POST['feed_load_more'];
    $appearance_text_color      = $_POST['appearance_text_color'];
    $appearance_border_color    = $_POST['appearance_border_color'];
    $appearance_bg_color        = $_POST['appearance_bg_color'];
    $appearance_feed_bg_color   = $_POST['appearance_feed_bg_color'];
    $selected_template          = $_POST['selected_template'];
    $user_id                    = $_POST['user_id'];
    $campaign_id                = $_POST['campaign_id'];
    
    // CHECK IF THE SETTINGS IS ALREADY EXIST ON THE DATABASE
    $campaign_settings = $DB->query("SELECT * FROM {$dbprefix}template_settings WHERE user_id = '{$user_id}' AND campaign_id = '{$campaign_id}'")[0];

    if( $campaign_settings ){ // do update
        $template_settings_id = $campaign_settings["template_settings_id"];

        $result = $DB->query("UPDATE {$dbprefix}template_settings SET 
            `selected_template`         = '{$selected_template}',
            `feed_post_per_page`        = '{$feed_post_per_page}',
            `feed_load_more`            = '{$feed_load_more}',
            `appearance_text_color`     = '{$appearance_text_color}',
            `appearance_border_color`   = '{$appearance_border_color}',
            `appearance_bg_color`       = '{$appearance_bg_color}',
            `appearance_feed_bg_color`  = '{$appearance_feed_bg_color}'
            WHERE `template_settings_id` = '{$template_settings_id}'
        ");

        if ($result) {
            returnResponse([
                'success' => true,
                'message' => "Template #{$template_settings_id} has been updated successfully."
            ]);
        }
    }else{ // save new record
        $result = $DB->query("INSERT INTO {$dbprefix}template_settings SET 
            `user_id`                   = '{$user_id}',
            `campaign_id`               = '{$campaign_id}',
            `selected_template`         = '{$selected_template}',
            `feed_post_per_page`        = '{$feed_post_per_page}',
            `feed_load_more`            = '{$feed_load_more}',
            `appearance_text_color`     = '{$appearance_text_color}',
            `appearance_border_color`   = '{$appearance_border_color}',
            `appearance_bg_color`       = '{$appearance_bg_color}',
            `appearance_feed_bg_color`  = '{$appearance_feed_bg_color}'
        ");

        if ($result) {
            returnResponse([
                'success' => true,
                'message' => "Template has been saved successfully."
            ]);
        }
    }
    
    // returnResponse([
    //     'success' => false,
    //     'message' => "The action provided doesn't exist, please try again!"
    // ], false);