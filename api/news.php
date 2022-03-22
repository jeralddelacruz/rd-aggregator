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
        $user_id = $_POST['user_id'];
        $news_id = $_POST['news_id'];
        $author = $_POST['news_author'];
        $title = $_POST['news_title'];
        $description = $_POST['news_description'];
        $video_url = $_POST['video_url'];
        
        user_mkdir($user_id);
        
        // News Image
        $image = $_FILES['image'];
        $image_name = $image["name"] ?? null;
		if(!empty($image_name)){
		    $image_dir = "../upload/{$user_id}/news/images/";
            $image_file    = $image_dir . basename($image["name"]);
    		$image_status  = 1;
    		$image_ext     = strtolower(pathinfo($image_file, PATHINFO_EXTENSION));
    		
            $image_error = [];
		    
			if(!in_array($image_ext, ["jpg", "jpeg", "png"])){
				$image_status = 0;
				
				$image_error[] = "File must be a jpg, jpeg, png.";
			}

			if($image["size"] > 1000000){
				$user_image_status = 0;

				$image_error[] = "File size must not exceed 1MB.";
			}

			if($image_status == 0){
				returnResponse([
				    'success' => false,
				    'message' => $image_name . ": " . implode(", ", $image_error)
				], false);
			} else {
				move_uploaded_file($image["tmp_name"], $image_file);
			}
		}
        
        // User Image
        $user_image = $_FILES['user_image'];
        $user_image_name = $user_image["name"] ?? null;
		if(!empty($user_image_name)){
            $user_image_dir = "../upload/{$user_id}/news/avatar/";
            $user_image_file    = $user_image_dir . basename($user_image["name"]);
    		$user_image_status  = 1;
    		$user_image_ext     = strtolower(pathinfo($user_image_file, PATHINFO_EXTENSION));
    		
            $user_image_error = [];
		    
			if(!in_array($user_image_ext, ["jpg", "jpeg", "png"])){
				$user_image_status = 0;
				
				$user_image_error[] = "File must be a jpg, jpeg, png.";
			}

			if($user_image["size"] > 1000000){
				$user_image_status = 0;

				$user_image_error[] = "File size must not exceed 1MB.";
			}

			if($user_image_status == 0){
				returnResponse([
				    'success' => false,
				    'message' => $user_image_name . ": " . implode(", ", $user_image_error)
				], false);
			} else {
				move_uploaded_file($user_image["tmp_name"], $user_image_file);
			}
		}
        
        if ($action === 'edit') {
            $news = $DB->query("SELECT `news_id` FROM {$dbprefix}news_updates WHERE `news_id` = '{$news_id}' AND `user_id` = '{$user_id}'");
            
            if (count($news) == 0) {
                $result = $DB->query("INSERT INTO {$dbprefix}news_updates
                    (`user_id`, `news_id`, `name`, `title`, `description`, `video_url`, `user_image`, `post_image`)
                    VALUES ('{$user_id}', '{$news_id}', '{$author}', '{$title}', '{$description}', '{$video_url}', '{$user_image_name}', '{$image_name}')
                ");
            } else {
                $additional_columns = "";
                if ($user_image_name) {
                    $additional_columns .= ", `user_image` = '{$user_image_name}'";
                }
                
                if ($image_name) {
                    $additional_columns .= ", `post_image` = '{$image_name}'";
                }
                
                $result = $DB->query("UPDATE {$dbprefix}news_updates SET
                    `user_id` = '{$user_id}', `name` = '{$author}', `title` = '{$title}', `description` = '{$description}', 
                    `video_url` = '${video_url}'" . $additional_columns .
                    " WHERE `news_id` = '{$news_id}'
                ");
            }
            
            if ($result) {
                $data = $DB->query("SELECT * FROM {$dbprefix}news_updates WHERE `news_id` = '{$news_id}' AND `user_id` = '{$user_id}'");
                
                returnResponse([
                    'success' => true,
                    'message' => "News #{$news_id} has been updated successfully.",
                    'data' => $data[0]
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