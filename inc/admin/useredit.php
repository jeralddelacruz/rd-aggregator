<?php
if($_GET["id"]){
	$id=$_GET["id"];
	if(!$row=$DB->info("user","user_id='$id'")){
		redirect("index.php?cmd=user");
	}
}

if($_GET["del"]){
	$del=$_GET["del"];
	if(($del=="avatar")&&$row["user_avatar"]){
		@unlink("../upload/avatar/".$row["user_avatar"]);

		$DB->query("update $dbprefix"."user set user_avatar='' where user_id='$id'");
	}

	redirect("index.php?cmd=useredit&id=".$id);
}

$pack_arr=$DB->get_pack();

$arr=array("fname","lname","email","act","expire","avatar","lock");
if(!$_POST["submit"]){
	if(!$id){
		$act=1;
	}
	else{
		$pack=$row["pack_id"];
		foreach($arr as $val){
			${$val}=$row["user_".$val];
		}
	}
}
else{
	foreach($arr as $val){
		${$val}=strip($_POST[$val]);
	}
	$expire=(int)strtotime($expire);
	$pass=strip($_POST["pass"]);
	$cpass=strip($_POST["cpass"]);
	$pack=(int)$_POST["pack"];
	$lock=(int)$_POST["lock"];
	$avatar=$row["user_avatar"]?$row["user_avatar"]:"";

	$error="";
	if(!$fname||!$lname||!$email||(!$id&&(!$pass||!$cpass))||!$pack){
		$error.="&bull; All fields should be <strong>filled in</strong>.<br />";
	}
	if($email&&!is_email($email)){
		$error.="&bull; Invalid <strong>E-mail Address</strong>.<br />";
	}
	if($DB->info("user","user_email='$email'".($id?" and user_id<>'$id'":""))){
		$error.="&bull; <strong>E-mail Address</strong> already <strong>exists</strong>, try another one.<br />";
	}
	if($cpass!=$pass){
		$error.="&bull; Invalid <strong>Password</strong> confirmation.";
	}

	if(!$error){
		if(!$id){
			$DB->query("insert into $dbprefix"."user set pack_id='$pack',user_pass='".mc_encrypt($pass,$dbkey)."',user_email='$email',user_fname='$fname',user_lname='$lname',user_rd='".time()."',user_act='$act',user_lock='$lock',user_expire='$expire'");

			$row=$DB->info("user","user_email='$email'");
			$id=$row["user_id"];

			user_mkdir($id);

			sendmail(1,array("fname"=>$fname,"lname"=>$lname,"email"=>$email,"pass"=>$pass,"sitename"=>$WEBSITE["sitename"],"siteurl"=>$SCRIPTURL));
			sendmail(2,array("fname"=>$fname,"lname"=>$lname,"email"=>$email,"pass"=>$pass,"sitename"=>$WEBSITE["sitename"],"siteurl"=>$SCRIPTURL));
			sendmail(3,array("fname"=>$fname,"lname"=>$lname,"email"=>$email));

			sendgr($fname,$lname,$email);
			sendsendiio($email);
		}
		else{
			$pass=$pass?mc_encrypt($pass,$dbkey):$row["user_pass"];
			$DB->query("update $dbprefix"."user set pack_id='$pack',user_pass='$pass',user_email='$email',user_fname='$fname',user_lname='$lname',user_act='$act',user_lock='$lock',user_expire='$expire' where user_id='$id'");
		}

		if($_FILES["avatar"]&&getimagesize($_FILES["avatar"]["tmp_name"])&&($_FILES["avatar"]["size"]<=102400)){
			$name_arr=explode(".",$_FILES["avatar"]["name"]);
			$avatar=$id.".".$name_arr[sizeof($name_arr)-1];
			@move_uploaded_file($_FILES["avatar"]["tmp_name"],"../upload/avatar/".$avatar);
			$DB->query("update $dbprefix"."user set user_avatar='$avatar' where user_id='$id'");
		}

		redirect("index.php?cmd=user");
	}
}
?>
<h2><?php echo $index_title;?><a href="index.php?cmd=user" class="add">Back to Members</a><?php if($id){?><a href="index.php?cmd=useredit" class="add">Add New Member</a><?php }?></h2>
<?php
if($error){
?>
<div class="error"><?php echo $error;?></div>
<?php
}
?>
<form method="post" enctype="multipart/form-data">
<table class="tbl_form">
	<tr>
		<th><label for="fname">First Name</label></th>
		<td><input type="text" name="fname" id="fname" value="<?php echo $_POST["submit"]?slash($fname):$fname;?>" class="text" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="lname">Last Name</label></th>
		<td><input type="text" name="lname" id="lname" value="<?php echo $_POST["submit"]?slash($lname):$lname;?>" class="text" maxlength="250" /></td>
	</tr>
	<tr>
		<th><label for="email">E-mail Address</label></th>
		<td><input type="text" name="email" id="email" value="<?php echo $_POST["submit"]?slash($email):$email;?>" class="text" maxlength="250" /></td>
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
		<th><label for="pack">Membership</label></th>
		<td>
			<select name="pack" id="pack" class="sel">
<?php
foreach($pack_arr as $k=>$v){
?>
				<option value="<?php echo $k;?>"<?php echo ($k==$pack)?" selected":"";?>><?php echo $v;?></option>
<?php
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<th><label for="expire">Expiry Date</label><img src="../img/help.png" title="Membership Expiry Date. Leave blank for Unlimited." class="help "/></th>
		<td><input type="text" name="expire" id="expire" value="<?php echo $expire?date("Y-m-d",$expire):"";?>" class="text_s" /></td>
	</tr>
	<tr>
		<th><label for="act">Status</label></th>
		<td>
			<select name="act" id="act" class="sel">
<?php
foreach($USER_ACT_ARR as $k=>$v){
?>
				<option value="<?php echo $k;?>"<?php echo ($k==$act)?" selected":"";?>><?php echo $v;?></option>
<?php
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<th><label for="lock">Lock account</label></th>
		<td>
			<input type="checkbox" name="lock" id="lock" value="1" <?php echo ($lock) ? 'checked' : '' ;?>>
		</td>
	</tr>
	<tr>
		<th><label for="avatar">Avatar</label> <span class="desc">(optional)</span></th>
		<td><?php if($avatar){?><img src="../upload/avatar/<?php echo $avatar;?>" style="max-width:100px;" /> <a href="index.php?cmd=useredit&id=<?php echo $id;?>&del=avatar" onclick="return confirm('Are you sure you wish to delete Avatar?');"><img src="../img/del.png" title="Delete" class="tip" /></a><?php }else{?><input type="file" name="avatar" id="avatar" /><br /><span class="desc">max filesize 100 KB</span><?php }?></td>
	</tr>
</table>
<div class="submit"><input type="submit" name="submit" value="Save" class="button" /></div>
</form>
<script>
jQuery(function($){
	$("#expire").datepicker({dateFormat:"yy-mm-dd",minDate:+1});
});
</script>