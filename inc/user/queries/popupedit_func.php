<?php
    $row=$DB->info("popup","popup_id='$id' and user_id='$UserID'");

    $avatar_url = $row["avatar_url"];
    $name = $row["name"];
    $question = $row["question"];

    $description = $row["description"];
    $sub_description = $row["sub_description"];
    $button_link = $row["button_link"];
    $second_image_url = $row["second_image_url"];
    
    $subdomain_id = "";
	if( $user_subdomain ){
	    $subdomain_id = $user_subdomain["subdomain_id"];
	}else{
	    $subdomain_id = "0";
	}

    if(isset($_POST["submit"])){

        // First page
        $name = $_POST["name"];
        $question = $_POST["question"];

        // Second page
        $description = $_POST["description"];
        $sub_description = $_POST["sub_description"];
        $button_link = $_POST["button_link"];
        
        $avatar_url = !basename($_FILES["avatar_url"]["name"]) ? $avatar_url : basename($_FILES["avatar_url"]["name"]);
        
        $second_image_url = !basename($_FILES["second_image_url"]["name"]) ? $second_image_url : basename($_FILES["second_image_url"]["name"]);

        $today = date("Y-m-d H:i:s");

        if(!$id){
            
            $id = $DB->getauto("popup");
            $DB->query("INSERT INTO {$dbprefix}popup 
                SET popup_id='$id', 
                user_id='$UserID',
                subdomain_id= '{$subdomain_id}',
                name='$name',
                question='$question',
                description='$description',
                sub_description='$sub_description',
                avatar_url='$avatar_url',
                second_image_url='$second_image_url',
                button_link='$button_link',
                created_at='$today',
                updated_at='$today'
            ");
            
            // UPLOAD OWN IMAGE
            $target_directory = "../upload/{$UserID}/popup/"; // REPLACE 0 WITH USER ID - REPLACED

            if (!file_exists($target_directory)) {
                mkdir($target_directory, 0777, true);
            }
            
            $target_file_avatar_url = $target_directory . basename($_FILES["avatar_url"]["name"]);
            $target_file_second_image_url = $target_directory . basename($_FILES["second_image_url"]["name"]);

            $upload_status_avatar_url = 1;
            $upload_status_image_url = 1;

            $image_file_type_avatar_url = strtolower(pathinfo($target_file_avatar_url, PATHINFO_EXTENSION));
            $image_file_type_image_url = strtolower(pathinfo($target_file_second_image_url, PATHINFO_EXTENSION));

            if(isset($_POST["submit"])){
                $check_1 = getimagesize($_FILES["avatar_url"]["tmp_name"]);
                $check_2 = getimagesize($_FILES["second_image_url"]["tmp_name"]);

                if($check_1 !== false){
                    // echo "File is an image - " . $check_1["mime"] . ".";

                    $upload_status_avatar_url = 1;
                }
                if($check_2 !== false){
                    // echo "File is an image - " . $check_2["mime"] . ".";

                    $upload_status_image_url = 1;
                }
            }

            // FILE EXISTENCE VALIDATION
            if(file_exists($target_file_avatar_url)){
                // echo "This file already exists.";

                $upload_status_avatar_url = 0;
            }
            if(file_exists($target_file_second_image_url)){
                // echo "This file already exists.";

                $upload_status_image_url = 0;
            }
           
            // FILE SIZE VALIDATION
            if($_FILES["avatar_url"]["size"] > 500000 && basename($_FILES["avatar_url"]["name"])){
                $error[] = "First page image file size exceeds maximum limit. Maximum allowed file size is 500kb";
                // echo "The file you are uploading is too large.";

                $upload_status_avatar_url = 0;
            }
            if($_FILES["second_image_url"]["size"] > 500000 && basename($_FILES["second_image_url"]["name"])){
                $error[] = "Second page image file size exceeds maximum limit. Maximum allowed file size is 500kb";
                // echo "The file you are uploading is too large.";

                $upload_status_image_url = 0;
            }

            // FILE FORMAT VALIDATION
            if($image_file_type_avatar_url != "jpg" && $image_file_type_avatar_url != "jpeg" && $image_file_type_avatar_url != "png" && basename($_FILES["avatar_url"]["name"])){
                $error[] = "Sorry, first page image, only .jpg, jpeg, and .png are allowed.";
                // echo "Sorry, only .jpg, jpeg, and .png are allowed.";

                $upload_status_avatar_url = 0;
            }
            if($image_file_type_image_url != "jpg" && $image_file_type_image_url != "jpeg" && $image_file_type_image_url != "png" && basename($_FILES["second_image_url"]["name"])){
                $error[] = "Sorry, second page image, only .jpg, jpeg, and .png are allowed.";
                // echo "Sorry, only .jpg, jpeg, and .png are allowed.";

                $upload_status_image_url = 0;
            }

            // CHECK IF TO PROCEED UPLOADING
            if($upload_status_avatar_url == 0){
                // echo "The file is not uploaded.";
            }
            else{
                move_uploaded_file($_FILES["avatar_url"]["tmp_name"], $target_file_avatar_url);
            }

            if($upload_status_image_url == 0){
                // echo "The file is not uploaded.";
            }
            else{
                move_uploaded_file($_FILES["second_image_url"]["tmp_name"], $target_file_second_image_url);
            }

            // check if has an error
            if (sizeof($error) == 0) {
                $_SESSION['msg'] = 'Your popup has been successfully saved.';
                redirect("index.php?cmd=popup");
            }
            
        }else{
            // echo json_encode($data);
            $DB->query("UPDATE {$dbprefix}popup 
						SET name='$name',
                            question='$question',
                            description='$description',
                            sub_description='$sub_description',
                            avatar_url='$avatar_url',
                            second_image_url='$second_image_url',
                            button_link='$button_link',
                            updated_at='$today'
						WHERE popup_id='$id' AND user_id='$UserID'");
            
            // UPLOAD OWN IMAGE
            $target_directory = "../upload/{$UserID}/popup/"; // REPLACE 0 WITH USER ID - REPLACED

            if (!file_exists($target_directory)) {
                mkdir($target_directory, 0777, true);
            }
            
            $target_file_avatar_url = $target_directory . basename($_FILES["avatar_url"]["name"]);
            $target_file_second_image_url = $target_directory . basename($_FILES["second_image_url"]["name"]);

            $upload_status_avatar_url = 1;
            $upload_status_image_url = 1;

            $error = array();

            $image_file_type_avatar_url = strtolower(pathinfo($target_file_avatar_url, PATHINFO_EXTENSION));
            $image_file_type_image_url = strtolower(pathinfo($target_file_second_image_url, PATHINFO_EXTENSION));

            if(isset($_POST["submit"])){
                $check_1 = getimagesize($_FILES["avatar_url"]["tmp_name"]);
                $check_2 = getimagesize($_FILES["second_image_url"]["tmp_name"]);

                if($check_1 !== false){
                    // echo "File is an image - " . $check_1["mime"] . ".";

                    $upload_status_avatar_url = 1;
                }
                if($check_2 !== false){
                    // echo "File is an image - " . $check_2["mime"] . ".";

                    $upload_status_image_url = 1;
                }
            }

            // FILE EXISTENCE VALIDATION
            if(file_exists($target_file_avatar_url)){
                // echo "This file already exists.";

                $upload_status_avatar_url = 0;
            }
            if(file_exists($target_file_second_image_url)){
                // echo "This file already exists.";

                $upload_status_image_url = 0;
            }
           
            // FILE SIZE VALIDATION
            if($_FILES["avatar_url"]["size"] > 500000 && basename($_FILES["avatar_url"]["name"])){
                $error[] = "First page image file size exceeds maximum limit. Maximum allowed file size is 500kb";
                // echo "The file you are uploading is too large.";

                $upload_status_avatar_url = 0;
            }
            if($_FILES["second_image_url"]["size"] > 500000 && basename($_FILES["second_image_url"]["name"])){
                $error[] = "Second page image file size exceeds maximum limit. Maximum allowed file size is 500kb";
                // echo "The file you are uploading is too large.";

                $upload_status_image_url = 0;
            }

            // FILE FORMAT VALIDATION
            if($image_file_type_avatar_url != "jpg" && $image_file_type_avatar_url != "jpeg" && $image_file_type_avatar_url != "png" && basename($_FILES["avatar_url"]["name"])){
                $error[] = "Sorry, first page image, only .jpg, jpeg, and .png are allowed.";
                // echo "Sorry, only .jpg, jpeg, and .png are allowed.";

                $upload_status_avatar_url = 0;
            }

            if($image_file_type_image_url != "jpg" && $image_file_type_image_url != "jpeg" && $image_file_type_image_url != "png" && basename($_FILES["second_image_url"]["name"])){
                $error[] = "Sorry, second page image, only .jpg, jpeg, and .png are allowed.";
                // echo "Sorry, only .jpg, jpeg, and .png are allowed.";

                $upload_status_image_url = 0;
            }

            // CHECK IF TO PROCEED UPLOADING
            if($upload_status_avatar_url == 0){
                // echo "The file is not uploaded.";
            }
            else{
                move_uploaded_file($_FILES["avatar_url"]["tmp_name"], $target_file_avatar_url);
            }

            if($upload_status_image_url == 0){
                // echo "The file is not uploaded.";
            }
            else{
                move_uploaded_file($_FILES["second_image_url"]["tmp_name"], $target_file_second_image_url);
            }

            // check if has an error
            if (sizeof($error) == 0) {
                $_SESSION['msg'] = 'Your popup has been successfully updated.';
                redirect("index.php?cmd=popup");
            }
        }
        
    }

    if($_GET["del"]){

        $del=$_GET["del"];
        if(($del=="avatar_url") && $row["avatar_url"]){
            @unlink("../upload/{$UserID}/popup/".$row["avatar_url"]);

            $DB->query("UPDATE {$dbprefix}popup SET avatar_url='' where popup_id = {$id} ");
        }
        
        if(($del=="second_image_url") && $row["second_image_url"]){
            @unlink("../upload/{$UserID}/".$row["second_image_url"]);

            $DB->query("UPDATE {$dbprefix}popup SET second_image_url='' where popup_id = {$id} ");
        }

        redirect("index.php?cmd=popupedit&id={$id}");
    }
?>