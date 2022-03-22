<?php include('../../cache_solution/top-cache-v2.php'); ?>
<?php
	// VARIABLE INITIALIZATION
	$id = $_GET["id"];

    if($id&&(!$row=$DB->info("popup","popup_id='$id' and user_id='$UserID'"))){
        redirect("index.php?cmd=popup");
    }

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
<div class="row">
<div class="col-md-12">
<form method="post" enctype="multipart/form-data">

<?php
if($error){
?>
	<div class="col-md-12">
        <?php
            foreach ($error as $key => $value) {
        ?>
            <div class="alert alert-danger"><?php echo $value;?></div>
        <?php
            }
        ?>
	</div>
<?php
}
?>
    <style>
        #attribute_container .row:not(.remove) .col-md-12.p-3 {
            display: none;
        }

        #attribute_container .row:not(.remove):last-of-type .col-md-12.p-3 {
            display: block;
        }
    </style>
	<div class="col-md-12">
		<div class="card">
			<div class="header card-header d-flex justify-content-between">
				<h4 class="title" style="float:left;margin:5px 15px 15px 0;"><?php echo $index_title;?></h4>
				<div>
    				<a href="index.php?cmd=popup"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Back to Your Pop-up's</div></a>
    				
    				<?php if($id){ ?>
    				<a href="index.php?cmd=popupedit"><div class="btn btn-danger" style="margin-left:10px;">Create New Pop-up</div></a>
    				<?php } ?>
    			</div>

			</div>
			<div class="content card-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="title" style="float:left;margin-top:7px;">Popup Name</label>
							<input type="text" name="name" value="<?php echo $_POST["submit"]?slash($name):$name;?>" placeholder="Your pop-up name" class="form-control" required />
						</div>
					</div>
					
					<?php if($error&&!$title){ ?>
					<div class="red pull-left" style="margin-top:10px;" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></div>
					<?php } ?>
				</div>
                <div class="row">
					<div class="col-md-6">
						
						<!-- BEGIN TRY -->
						<div class="card">
							<div class="content card-body">
								<center><h4 class="mb-3">First Page</h4></center>
								
								<!-- FIRST PAGE -->
								<div class="form-group">
                                    <label for="title" style="float:left;margin-top:7px;">Popup Question</label>
                                    <input type="text" name="question" value="<?php echo $_POST["submit"]?slash($question):$question;?>" placeholder="Your pop-up question" class="form-control" required />
									
									<?php if($error && !$question){ ?>
									<span class="red" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></span>
									<?php } ?>
									
								</div>
								
								<!-- IF UPLOAD MY OWN IMAGE -->
								<!-- NOTE: ADD VALIDATION -->
								<div class="form-group" id="avatar_urlContainer">
									<label for="avatar_url" style="float:left;margin-top:7px;">Upload Image</label>
									<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Upload your own image. Max size is 3mb."><i class="fa fa-question" aria-hidden="true"></i></span>
									
									<?php if($error && !$avatar_url){ ?>
									<span class="red" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></span>
									<?php } ?>
									
									<input type="file" class="form-control" style="overflow: hidden;" name="avatar_url" id="avatar_url" value="<?php echo $_POST["submit"] ? slash($avatar_url) : $avatar_url; ?>" />
									
									<?php if($avatar_url){ ?>
									<img src="../upload/<?php echo $UserID; ?>/popup/<?php echo $avatar_url; ?>" class="img-responsive" />
									<a href="#" onclick="return confirm('Are you sure you wish to delete?') ? window.location.href = 'index.php?cmd=popupedit&id=<?php echo $id; ?>&del=avatar_url' : '';"><img src="../img/del.png" data-toggle="tooltip" title="Delete" style="vertical-align:bottom;" /></a>
									<?php } ?>
								</div>
								
							</div>
						</div>
					</div>
					<div class="col-md-6">
						
						<div class="card">
							<div class="content card-body">
								<center><h4 class="mb-3">Second Page</h4></center>
								
								<!-- BONUS PAGE DESCRIPTION -->
								<div class="form-group">
									<label for="description" style="float:left;margin-top:7px;">Description</label>
									<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Adds a description for your Popup Page."><i class="fa fa-question" aria-hidden="true"></i></span>
									
									<?php if($error && !$description){ ?>
									<span class="red" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></span>
									<?php } ?>
									
									<input type="text" name="description" class="form-control" value="<?php echo $_POST["submit"]?slash($description):$description;?>" required/>
								</div>

                                <div class="form-group">
									<label for="sub_description" style="float:left;margin-top:7px;">Sub Description</label>
									<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Adds a Sub Description for your Popup Page."><i class="fa fa-question" aria-hidden="true"></i></span>
									
									<?php if($error && !$sub_description){ ?>
									<span class="red" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></span>
									<?php } ?>
									
									<input type="text" name="sub_description" class="form-control" value="<?php echo $_POST["submit"]?slash($sub_description):$sub_description;?>" required/>
								</div>
								
								<div class="form-group">
									<label for="sub_description" style="float:left;margin-top:7px;">Button Link</label>
									<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Add a link for you button."><i class="fa fa-question" aria-hidden="true"></i></span>
									
									<?php if($error && !$button_link){ ?>
									<span class="red" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></span>
									<?php } ?>
									
									<input type="text" name="button_link" class="form-control" value="<?php echo $_POST["submit"]?slash($button_link):$button_link;?>" required/>
								</div>

                                <!-- IF UPLOAD MY OWN IMAGE -->
								<!-- NOTE: ADD VALIDATION -->
								<div class="form-group" id="second_image_urlContainer">
									<label for="second_image_url" style="float:left;margin-top:7px;">Upload Image</label>
									<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Upload your own image. Max size is 3mb."><i class="fa fa-question" aria-hidden="true"></i></span>
									
									<?php if($error && !$second_image_url){ ?>
									<span class="red" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></span>
									<?php } ?>
									
									<input type="file" class="form-control" style="overflow: hidden;" name="second_image_url" id="second_image_url" value="<?php echo $_POST["submit"] ? slash($second_image_url) : $second_image_url; ?>" />
									
									<?php if($second_image_url){ ?>
									<img src="../upload/<?php echo $UserID; ?>/popup/<?php echo $second_image_url; ?>" class="img-responsive" />
									<a href="#" onclick="return confirm('Are you sure you wish to delete?') ? window.location.href = 'index.php?cmd=popupedit&id=<?php echo $id; ?>&del=second_image_url' : '';"><img src="../img/del.png" data-toggle="tooltip" title="Delete" style="vertical-align:bottom;" /></a>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12" style="margin-bottom:25px;">
		<input type="submit" name="submit" value="Save All Edits" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill pull-right" />
	</div>
</form>
</div>
</div>
<?php include('../../cache_solution/bottom-cache-v2.php'); ?>