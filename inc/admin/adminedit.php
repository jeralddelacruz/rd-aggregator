<?php
if($_GET["id"]){
	$id=$_GET["id"];
	if(!$row=$DB->info("admin","admin_id='$id' and admin_id<>'1'")){
		redirect("index.php?cmd=admin");
	}
}

if(!$_POST["submit"]){
	if(!$id){
		$ar_arr=array();
	}
	else{
		$nick=$row["admin_nick"];
		$ar_arr=explode(";",$row["admin_ar"]);
	}
}
else{
	$nick=strip($_POST["nick"]);
	$pass=strip($_POST["pass"]);
	$cpass=strip($_POST["cpass"]);
	$ar_arr=$_POST["ar"];

	$error="";
	if(!$nick||(!$id&&(!$pass||!$cpass))){
		$error.="&bull; All fields should be <strong>filled in</strong>.<br />";
	}
	if($nick&&!is_nick($nick)){
		$error.="&bull; Invalid <strong>Username</strong>.<br />";
	}
	if($DB->info("admin","admin_nick='$nick'".($id?" and admin_id<>'$id'":""))){
		$error.="&bull; <strong>Username</strong> already <strong>exists</strong>, try another one.<br />";
	}
	if($cpass!=$pass){
		$error.="&bull; Invalid <strong>Password</strong> confirmation.";
	}

	if(!$error){
		$pass=$pass?md5($pass):$row["admin_pass"];
		$ar=implode(";",$ar_arr);
		if(!$id){
			$id=$DB->getmaxval("admin_id","admin")+1;
			$DB->query("insert into $dbprefix"."admin set admin_id='$id',admin_nick='$nick',admin_pass='$pass',admin_ar='$ar'");
		}
		else{
			$DB->query("update $dbprefix"."admin set admin_nick='$nick',admin_pass='$pass',admin_ar='$ar' where admin_id='$id'");
		}

		redirect("index.php?cmd=admin");
	}
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=admin" class="add">Back to Admin Members</a><?php if($id){?><a href="index.php?cmd=adminedit" class="add">Add New Admin Member</a><?php }?></h2>
<?php
if($error){
?>
<div class="error"><?php echo $error;?></div>
<?php
}
?>
<form method="post">
<table class="tbl_form">
	<tr>
		<th><label for="nick">Username</label></th>
		<td><input type="text" name="nick" id="nick" value="<?php echo $_POST["submit"]?slash($nick):$nick;?>" class="text" maxlength="25" /></td>
	</tr>
	<tr>
		<th><label for="pass"><?php if($id){echo "New ";}?>Password</label><?php if($id){?><img src="../img/help.png" title="Fill in this field only if you wish to change the Current Password." class="help "/><?php }?></th>
		<td><input type="password" name="pass" id="pass" class="text" /></td>
	</tr>
	<tr>
		<th><label for="cpass">Confirm Password</label><?php if($id){?><img src="../img/help.png" title="Fill in this field only if you wish to change the Current Password." class="help "/><?php }?></th>
		<td><input type="password" name="cpass" id="cpass" class="text" /></td>
	</tr>
	<tr>
		<th>Access Rights<img src="../img/help.png" title="Choose the Features this Admin Member has access to." class="help" /></th>
		<td class="vm">
<?php
foreach($ADMIN_AR_ARR as $row){
?>
				<input id="ar_<?php echo $row["menu"];?>" type="checkbox" name="ar[]" value="<?php echo $row["menu"];?>"<?php echo in_array($row["menu"],$ar_arr)?" checked":"";?> /> <label for="ar_<?php echo $row["menu"];?>"><?php echo $row["title"];?></label><br />
<?php
}
?>
		</td>
	</tr>
</table>
<div class="submit"><input type="submit" name="submit" value="Save" class="button" /></div>
</form>