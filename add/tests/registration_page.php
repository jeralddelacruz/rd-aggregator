<?php
	set_time_limit(0);
	error_reporting(0);
	session_start();
	
	// IMPORTANT DIRECTORY
	$dir="../../sys";
	$fp=opendir($dir);
	while(($file=readdir($fp))!=false){
		$file=trim($file);
		if(($file==".")||($file=="..")){continue;}
		$file_parts=pathinfo($dir."/".$file);
		if($file_parts["extension"]=="php"){
			include($dir."/".$file);
		}
	}
	closedir($fp);
	
	$DB = new db($dbhost, $dbuser, $dbpass, $dbname);
	$DB->connect();
	if($DB->connect<1){
		echo "Can't go on, DB not initialized.";
		exit;
	}
	
	// WEBSITE VARIABLE
	$res=$DB->query("select setup_key,setup_val from $dbprefix"."setup order by setup_id");
	foreach($res as $row){
		$WEBSITE[$row["setup_key"]]=$row["setup_val"];
	}

	$pack_arr = $DB->get_pack();

	$pack_re_arr = array();

	foreach($pack_arr as $key => $value){
		if($key == "34"){
			array_push($pack_re_arr, $pack_re_arr[$key] = $value);
		}
	}

	$pack_re_arr = array_unique($pack_re_arr);

	if($_POST["submit"]){
		$user_added_through = "new_registration";
		$user_first_name = strip($_POST["user_first_name"]);
		$user_last_name = strip($_POST["user_last_name"]);
		$user_email = $_POST["user_email"];
		$user_confirm_email = $_POST["user_confirm_email"];
		$user_password = strip($_POST["user_password"]);
		$user_confirm_password = strip($_POST["user_confirm_password"]);
		$user_membership = $_POST["user_membership"];
		$user_rd = time();

		// ERROR HANDLING
		$error = "";
		if($user_email && !is_email($user_email)){
			$error .= "Invalid Email Address.";
		}

		if($DB->info("user", "user_email = '" . $user_email . "'")){
			$error .= "Email Address already exists";
		}

		if($user_email != $user_confirm_email){
			$error .= "Email Adress does not match.";
		}

		if($user_password != $user_confirm_password){
			$error .= "Password does not match.";
		}

		if(!$error){
			$insert = $DB->query("INSERT INTO {$dbprefix}user SET pack_id = '" . $user_membership . "', 
				user_added_through = '" .  $user_added_through . "', 
				user_fname = '" .  $user_first_name . "', 
				user_lname = '" . $user_last_name . "', 
				user_email = '" . $user_email . "', 
				user_pass = '" . mc_encrypt($user_password, $dbkey) . "', 
				user_rd = '" . $user_rd . "'");

			sendmail(1,array("fname"=>$user_first_name,"lname"=>$user_last_name,"email"=>$user_email,"pass"=>$user_password,"sitename"=>$WEBSITE["sitename"],"siteurl"=>$SCRIPTURL));
			sendmail(2,array("fname"=>$user_first_name,"lname"=>$user_last_name,"email"=>$user_email,"pass"=>$user_password,"sitename"=>$WEBSITE["sitename"],"siteurl"=>$SCRIPTURL));
			sendmail(3,array("fname"=>$user_first_name,"lname"=>$user_last_name,"email"=>$user_email));

			sendgr($fname,$lname,$email);

			if($insert){
				$_SESSION['msg'] = "Registration complete. Login your account at <a href='{$SCRIPTURL}user'>{$SCRIPTURL}user</a>";
				redirect("{$SCRIPTURL}" . "add/tests/registration_page.php");
			}
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Registration Page</title>

	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<!-- BOOTSTRAP CDN -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />

	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

	<!-- FONT AWESOME CDNs -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/v4-shims.css">

	<style type="text/css">
		body{
			margin: 0px; padding: 0px;

			box-sizing: border-box;

			background-color: gold;
		}

		.site-logo{
			margin: auto;
			width: 70%;
		}
	</style>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12 col-md-12 col-lg-6 mx-auto mt-5 mb-5">
				<div class="card">
					<form method="POST" enctype="multipart/form-data" id="registration_form" autocomplete="off">
						<div class="card-header text-center">
							<img src="../../img/<?= $WEBSITE['logo'] ?>" class="site-logo" />

							<?php if($error) : ?>
							<div class="alert alert-danger"><?= $error; ?></div>
							<?php elseif($_SESSION['msg']) : ?>
							<div class="alert alert-success"><?= $_SESSION['msg']; $_SESSION['msg'] = ""; ?></div>
							<?php endif; ?>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-6">
									<div class="form-group">
										<label>First Name</label>

										<input class="form-control" type="text" name="user_first_name" maxlength="100" required />
									</div>
								</div>
								<div class="col-sm-12 col-md-12 col-lg-6">
									<div class="form-group">
										<label>Last Name</label>

										<input class="form-control" type="text" name="user_last_name" maxlength="100" required />
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-6">
									<div class="form-group">
										<label>Email</label>

										<input class="form-control" type="email" name="user_email" maxlength="100" required />
									</div>
								</div>
								<div class="col-sm-12 col-md-12 col-lg-6">
									<div class="form-group">
										<label>Confirm Email</label>

										<input class="form-control" type="email" name="user_confirm_email" maxlength="100" required />
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-5">
									<div class="form-group">
										<label>Password</label>

										<input class="form-control" id="user_password" type="password" name="user_password" required />
									</div>
								</div>
								<div class="col-sm-12 col-md-12 col-lg-2">
									<div class="form-group">
										<label>&nbsp;</label>

										<button class="btn btn-primary btn-block" type="button" onclick="generatePassword(8)" data-toggle="modal" data-target="#passwordGeneratorModal">Generate</button>
									</div>
								</div>
								<div class="col-sm-12 col-md-12 col-lg-5">
									<div class="form-group">
										<label>Confirm Password</label>

										<input class="form-control" id="user_confirm_password" type="password" name="user_confirm_password" required />
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-12">
									<div class="form-group">
										<label>Membership</label>

										<select class="form-control" name="user_membership">
											<?php foreach($pack_re_arr as $k => $v) : ?>
											<option value="<?= $k; ?>"><?= $v; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<input class="btn btn-primary btn-block" type="submit" name="submit" value="Register" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- MODAL: PASSWORD GENERATOR -->
	<div id="passwordGeneratorModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Password Generator</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<div class="alert alert-warning">
						<div class="col-md-12 text-center">
							<strong>From <?= $WEBSITE["sitename"]; ?></strong>
						</div>
						&bull; Change your password after logging-in.
						<br />
						&bull; Regularly change your password. For security purposes.
					</div>

					<label>Generated Password</label>

					<div class="input-group">
						<input class="form-control" id="generated_password" type="text" name="" value="" />
						<div class="input-group-append">
							<button class="btn btn-primary btn-block" type="button" onclick="generatePassword(8)">Generate</button>
						</div>
						<div class="input-group-append">
							<button class="btn btn-primary" type="button" onclick="copyGeneratedPassword()">Copy</button>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal" onclick="useGeneratedPassword()">Use password</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		function generatePassword(password_length){
			var source = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~`!@#$%^&*()_-+={[}]|\:;\"'<,>.?/";
			var source_length = source.length;
			var password = "";

			for(var offset = 0; offset < password_length; ++offset){
				password += source.charAt(Math.floor(Math.random() * source_length));
			}

			var generated_password = document.getElementById("generated_password");
			generated_password.value = password;
		}

		function copyGeneratedPassword(){
			var generated_password = document.getElementById("generated_password");

			generated_password.select();
			document.execCommand("copy");
		}

		function useGeneratedPassword(){
			var generated_password = document.getElementById("generated_password");
			var user_password = document.getElementById("user_password");
			var user_confirm_password = document.getElementById("user_confirm_password");

			user_password.value = generated_password.value;
			user_confirm_password.value = generated_password.value;
		}
	</script>
</body>
</html>