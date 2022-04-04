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
    
    if (isset($_POST['status']) && $_POST['status'] == "status_changed") {
        $news_id = $_POST['news_id'];
        $action = $_POST['action'];
        $is_success = false;

        if( $_POST['action'] == "pin" ){
            $news_content = $DB->info("news", "news_id = '{$news_id}'")["is_pinned"];
            
            $is_pinned = (int)$news_content == 0 ? 1 : 0;

            $update_news = $DB->query("UPDATE {$dbprefix}news SET 
                is_pinned = '{$is_pinned}'
            WHERE news_id = '{$news_id}'");
            
            if( $update_news ) {
                $is_success = true;
            }
        }else{
            $update_news = $DB->query("UPDATE {$dbprefix}news SET 
                status = '{$action}'
            WHERE news_id = '{$news_id}'");
            
            if( $update_news ) {
                $is_success = true;
            }
        }
        
        $data = [
            'success' => $is_success,
            'message' => $is_success ? 'Status has been updated' : 'Failed to update status'
        ];
        
        returnResponse($data);
    }

    if (isset($_POST['status']) && $_POST['status'] == "save_template") {
        // GET ALL THE DATA
        $feed_post_per_page         = $_POST['feed_post_per_page'] ? $_POST['feed_post_per_page'] : 0;
        $feed_load_more             = $_POST['feed_load_more'];
        $appearance_text_color      = $_POST['appearance_text_color'];
        $appearance_border_color    = $_POST['appearance_border_color'];
        $appearance_bg_color        = $_POST['appearance_bg_color'];
        $appearance_feed_bg_color   = $_POST['appearance_feed_bg_color'];
        $selected_template          = $_POST['selected_template'];
        $user_id                    = $_POST['user_id'];
        $campaign_id                = $_POST['campaign_id'];

        $is_success = true;

        // CHECK IF THE SETTINGS IS ALREADY EXIST ON THE DATABASE
        $campaign_settings = $DB->info("template_settings", "user_id = '{$user_id}' AND campaign_id = '{$campaign_id}'");
        
        if( $campaign_settings ){ // do update
            $template_settings_id = $campaign_settings["template_settings_id"];

            $result = $DB->query("UPDATE {$dbprefix}template_settings SET 
                selected_template         = '{$selected_template}',
                feed_post_per_page        = {$feed_post_per_page},
                feed_load_more            = {$feed_load_more},
                appearance_text_color     = '{$appearance_text_color}',
                appearance_border_color   = '{$appearance_border_color}',
                appearance_bg_color       = '{$appearance_bg_color}',
                appearance_feed_bg_color  = '{$appearance_feed_bg_color}'
                WHERE template_settings_id = '{$template_settings_id}'
            ");

            if ($result) {
                returnResponse([
                    'success' => true,
                    'message' => "Template #{$template_settings_id} has been updated successfully."
                ]);
            }
        }else{ // save new record

            $result = $DB->query("INSERT INTO {$dbprefix}template_settings SET 
                user_id                   = {$user_id},
                campaign_id               = {$campaign_id},
                selected_template         = '{$selected_template}',
                feed_post_per_page        = {$feed_post_per_page},
                feed_load_more            = {$feed_load_more},
                appearance_text_color     = '{$appearance_text_color}',
                appearance_border_color   = '{$appearance_border_color}',
                appearance_bg_color       = '{$appearance_bg_color}',
                appearance_feed_bg_color  = '{$appearance_feed_bg_color}'
            ");

            if ($result) {
                returnResponse([
                    'success' => true,
                    'message' => "Template has been saved successfully."
                ]);
            }else{
                returnResponse([
                    'success' => false,
                    'message' => "Not Saved!"
                ]);
            }
        }
    }