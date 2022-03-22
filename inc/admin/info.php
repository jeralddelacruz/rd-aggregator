<?php
if(!$_POST["submit"]){
	$nick=$cur_admin["admin_nick"];
}
else{
	$nick=strip($_POST["nick"]);
	$pass=strip($_POST["pass"]);
	$npass=strip($_POST["npass"]);
	$cpass=strip($_POST["cpass"]);
	$error="";

	if(!$nick||!$pass||!$npass||!$cpass){
		$error.="&bull; All fields should be <strong>filled in</strong>.<br />";
	}
	if($nick&&!is_nick($nick)){
		$error.="&bull; <strong>Username</strong> should consist of 3 to 25 alphanumeric characters.<br />";
	}
	if($DB->info("admin","admin_nick='$nick' and admin_id<>'$AdminID'")){
		$error.="&bull; <strong>Username</strong> already <strong>exists</strong>, try another one.<br />";
	}
	if($pass&&(md5($pass)!=$cur_admin["admin_pass"])){
		$error.="&bull; Invalid <strong>Current Password</strong>.<br />";
	}
	if($npass!=$cpass){
		$error.="&bull; Invalid <strong>New Password</strong> confirmation.";
	}
	
	if(!$error){
		$pass=md5($npass);
		$DB->query("update $dbprefix"."admin set admin_nick='$nick',admin_pass='$pass' where admin_id='$AdminID'");

		$_SESSION["AdminName"]=$nick;
		$_SESSION["AdminPass"]=$pass;
		redirect("index.php?cmd=info&ok=1");
	}
}
?>
<h2><?php echo $index_title;?></h2>
<?php
if($error){
?>
<div class="error"><?php echo $error;?></div>
<?php
}
elseif($_GET["ok"]){
?>
<div class="ok">Profile <strong>updated</strong>.</div>
<?php
}
?>
<form method="post" action="index.php?cmd=info">
<table class="tbl_form">
	<tr>
		<th><label for="nick">Username</label></th>
		<td><input type="text" name="nick" id="nick" value="<?php echo $_POST["submit"]?slash($nick):$nick;?>" class="text" maxlength="25" /></td>
	</tr>
	<tr>
		<th><label for="pass">Current Password</label></th>
		<td><input type="password" name="pass" id="pass" class="text" /></td>
	</tr>
	<tr>
		<th><label for="npass">New Password</label></th>
		<td><input type="password" name="npass" id="npass" class="text" /></td>
	</tr>
	<tr>
		<th><label for="cpass">Confirm New Password</label></th>
		<td><input type="password" name="cpass" id="cpass" class="text" /></td>
	</tr>
</table>
<div class="submit"><input type="submit" name="submit" value="Update Profile" class="button" /></div>
</form>