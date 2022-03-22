<?php
if($_GET["del"]){
	$del=$_GET["del"];
	if(($del=="avatar")&&$cur_user["user_avatar"]){
		@unlink("../upload/avatar/".$cur_user["user_avatar"]);

		$DB->query("update $dbprefix"."user set user_avatar='' where user_id='$UserID'");
	}

	redirect("index.php?cmd=info");
}

$arr=array("fname","lname","email","avatar");
$lock = $cur_user["user_lock"];

if(!$_POST["submit"]){
	foreach($arr as $val){
		${$val}=$cur_user["user_".$val];
	}
}
else{
	foreach($arr as $val){
		${$val}=strip($_POST[$val]);
	}
	$npass=strip($_POST["npass"]);
	$cpass=strip($_POST["cpass"]);
	$avatar=$cur_user["user_avatar"]?$cur_user["user_avatar"]:"";

	$error="";
	$fields = $lock ? (!$fname||!$lname) : (!$fname||!$lname||!$email);
	if($fields){
		$error.="&bull; Required fields should be <strong>filled in</strong>.<br />";
	}
	if($email&&!is_email($email)){
		$error.="&bull; Invalid <strong>E-mail Address</strong>.<br />";
	}
	if($DB->info("user","user_email='$email' and user_id<>'$UserID'")){
		$error.="&bull; <strong>E-mail Address</strong> already <strong>exists</strong>, try another one.<br />";
	}
	if($cpass!=$npass){
		$error.="&bull; Invalid <strong>Password</strong> confirmation.";
	}

	if(!$error){
		$pass=$npass?mc_encrypt($npass,$dbkey):$cur_user["user_pass"];
		$email_query = $lock ? "" : "user_pass='$pass',user_email='$email',";
		$DB->query("update $dbprefix"."user set $email_query user_fname='$fname',user_lname='$lname' where user_id='$UserID'");

		if($_FILES["avatar"]["tmp_name"]&&getimagesize($_FILES["avatar"]["tmp_name"])&&($_FILES["avatar"]["size"]<=102400)){
			$name_arr=explode(".",$_FILES["avatar"]["name"]);
			$avatar=$UserID.".".$name_arr[sizeof($name_arr)-1];
			@move_uploaded_file($_FILES["avatar"]["tmp_name"],"../upload/avatar/".$avatar);
			$DB->query("update $dbprefix"."user set user_avatar='$avatar' where user_id='$UserID'");
		}

		$_SESSION["UserPass"]=$pass;
		redirect("index.php?cmd=info&ok=1");
	}
}

if($error){
?>
<div class="alert alert-danger"><?php echo $error;?></div>
<?php
}
elseif($_GET["ok"]){
?>
<div class="alert alert-success">Profile <strong>updated</strong>.</div>
<?php
}
?>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="header card-header">
				<h4 class="card-title title">Edit Profile</h4>
			</div>
			<div class="content card-body">
			<form method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-6 col-sm-6">
						<div class="form-group">
							<label style="float:left;margin-top:7px;">Membership Type</label>
							<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="This is your current Membership level.<br />To upgrade click on the button to the right."><i class="fa fa-question" aria-hidden="true"></i></span>
							<input type="text" value="<?php echo $cur_pack["pack_title"];?>" class="form-control" disabled />
						</div>
					</div>
					<div class="col-md-6 col-sm-6">
						<div class="form-group">
							<?php if ($cur_pack["pack_id"] < 30): ?>
							<a href="index.php?cmd=renew"><input type="button" value="Upgrade Membership" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill" style="margin-top:25px;" /></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="fname">First Name</label>
							<input type="text" id="fname" name="fname" value="<?php echo $_POST["submit"]?slash($fname):$fname;?>" class="form-control" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="lname">Last Name</label>
							<input type="text" id="lname" name="lname" value="<?php echo $_POST["submit"]?slash($lname):$lname;?>" class="form-control" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="email">E-mail Address</label>
							<input type="email" id="email" name="email" value="<?php echo $_POST["submit"]?slash($email):$email;?>" class="form-control" <?php echo $lock ? "disabled" : "" ;?>/>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="avatar" style="float:left;margin-top:7px;">Avatar (optional)</label>
							<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Max File Size is 100KB."><i class="fa fa-question" aria-hidden="true"></i></span>
							<div class="clearfix"></div>
<?php
if($avatar){
?>
							<img src="../upload/avatar/<?php echo $avatar;?>" class="img-responsive inline-block" />
							<a href="index.php?cmd=info&del=avatar" onclick="return confirm('Are you sure you wish to delete Avatar?');"><img src="../img/del.png" data-toggle="tooltip" title="Delete" style="vertical-align:bottom;" /></a>
<?php
}
else{
?>
							<label class="btn btn-default btn-file"><input type="file" id="avatar" name="avatar" style="cursor:pointer;" /></label>
<?php
}
?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<h4 class="title" style="float:left;">Change Password</h4>
						<span class="blue info-tooltip" data-toggle="tooltip" title="Fill in the fields below only if you wish to change the Current Password."><i class="fa fa-question" aria-hidden="true"></i></span>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6">
						<div class="form-group">
							<label for="npass">New Password</label>
							<input type="password" id="npass" name="npass" class="form-control" <?php echo $lock ? "disabled" : "" ;?> />
						</div>
					</div>
					<div class="col-md-6 col-sm-6">
						<div class="form-group">
							<label for="cpass">Confirm Password</label>
							<input type="password" id="cpass" name="cpass" class="form-control" <?php echo $lock ? "disabled" : "" ;?> />
						</div>
					</div>
				</div>
				<input type="submit" name="submit" value="Update Profile" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill pull-right" />
				<div class="clearfix"></div>
			</form>
			</div>
		</div>
	</div>
</div>