<?php
	if($_POST["submit"]){
		// MAKE A DIRECTORY FOLDER
		// mkdir("../../upload/0/test");

		$target_directory = "../../upload/0/"; // REPLACE 0 WITH USER ID
		// $file = $_POST["file_to_upload"];
		$target_file = $target_directory . basename($_FILES["file_to_upload"]["name"]);

		$upload_status = 1;

		$image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

		if(isset($_POST["submit"])){
			$check = getimagesize($_FILES["file_to_upload"]["tmp_name"]);

			if($check !== false){
				echo "File is an image - " . $check["mime"] . ".";

				$upload_status = 1;
			}
			else{
				echo "This file is not an image.";

				$upload_status = 0;
			}
		}

		// FILE EXISTENCE VALIDATION
		if(file_exists($target_file)){
			echo "This file already exists.";

			$upload_status = 0;
		}

		// FILE SIZE VALIDATION
		if($_FILES["file_to_upload"]["size"] > 500000){
			echo "The file you are uploading is too large.";

			$upload_status = 0;
		}

		// FILE FORMAT VALIDATION
		if($image_file_type != "jpg" && $image_file_type != "jpeg" && $image_file_type != "png"){
			echo "Sorry, only .jpg, jpeg, and .png are allowed.";

			$upload_status = 0;
		}

		// CHECK IF TO PROCEED UPLOADING
		if($upload_status == 0){
			echo "The file is not uploaded.";
		}
		else{
			if(move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $target_file)){
				echo "The file" . htmlspecialchars(basename($_FILES["file_to_upload"]["tmp_name"])) . " has been uploaded.";
			}
			else{
				echo "Sorry, there was an error uploading your file.";
			}
		}
	}

	if($_POST["delete"]){
		unlink("../../upload/0/Chrome Image.png");
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>File Upload</title>
</head>
<body>
	<form method="POST" enctype="multipart/form-data">
		<input type="file" name="file_to_upload" id="file_to_upload" />
		<input type="submit" name="submit" value="Upload Image" />

		<input type="submit" name="delete" value="Delete" />
	</form>
</body>
</html>