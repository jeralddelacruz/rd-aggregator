<?php
    // CODE_SECTION_PHP_2: VARIABLE_INITIALIZATION
	$passed_id = $_GET["id"];
	$category = $DB->info("category", "category_id = '{$passed_id}' AND user_id = '{$UserID}'");
	
	$subdomain_id = "";
	if( $user_subdomain ){
	    $subdomain_id = $user_subdomain["subdomain_id"];
	}else{
	    $subdomain_id = "0";
	}

	// CODE_SECTION_PHP_3: BACKEND PROCESS
	if($_POST["submit"]){
		// POST VARIABLES
		$remove[] = "'";
		$remove[] = '"';
		$remove[] = "-";

		// STRIP 1
		$cs_stripped_1 = str_replace($remove, "", $_POST["category_name"]);
		$category_name = $cs_stripped_1;
		
		// STRIP 1
		$category_descr = str_replace($remove, "", $_POST["category_desc"]);
		$category_desc = $category_descr;
		
		// IF $passed_id HAS A VALUE
		if(empty($passed_id)){
			$category_id = $DB->getauto("category");
			$insert_category = $DB->query("INSERT INTO {$dbprefix}category SET 
				category_id = '{$category_id}', 
				user_id = '{$UserID}', 
				subdomain_id= '{$subdomain_id}',
				category_name = '{$category_name}', 
				category_desc = '{$category_desc}'
            ");

			if($insert_category){
				$_SESSION["msg_success"] = "Category creation successful.";

				redirect("index.php?cmd=category");
			}
			else{
				$_SESSION["msg_error"] = "Category creation failure.";
			}
		}
		else{
			$update_category = $DB->query("UPDATE {$dbprefix}category SET 
				category_name = '{$category_name}', 
				category_desc = '{$category_desc}'
			    WHERE category_id = '{$passed_id}' AND user_id = '{$UserID}'");

			if($update_category){
				$_SESSION["msg_success"] = "Category update successful.";

				redirect("index.php?cmd=category");
			}
			else{
				$_SESSION["msg_error"] = "Category update failure.";
			}
		}
	}
?>