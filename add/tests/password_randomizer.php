<?php
	error_reporting(0);
	// PASSWORD RANDOMIZER
	function generatePassword($length) {
		$source = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~`!@#$%^&*()_-+={[}]|\:;\"'<,>.?/";
		$source_length = strlen($source);

		$password = "";

		for ($offset = 0; $offset < $length; $offset++) {
			$password .= $source[rand(0, $source_length - 1)];
		}
		return $password;
	}

	// echo generatePassword(8);

	// RENAME A FOLDER
	function renameFolder(){
		if(rename("../../register", "../../register_yeah")){
			echo "Folder renamed.";
		}
		else{
			echo "Rename failed.";
		}
	}

	renameFolder();

	// CHECK IF FOLDER EXIST
	function folderExist(){
		$folder_directory = "../../";
		$folder_name = "register_yeah";

		$existence_check = $folder_directory . $folder_name;

		if(file_exists($existence_check)){
			echo "Folder exist.";
		}
		else{
			echo "This folder doesn't exist.";
		}
	}

	echo "<br />";
	folderExist();
?>