<?php
if($_POST["submit"]){
	$email=strip($_POST["email"]);
	if($row=$DB->info("user","user_email='$email' and user_act='1' and (user_expire='0' or user_expire>'".time()."')")){
		$pass=rand_str(8);
		$DB->query("update $dbprefix"."user set user_pass='".mc_encrypt($pass,$dbkey)."' where user_id='".$row["user_id"]."'");
		
		sendmail(4,array("fname"=>$row["user_fname"],"lname"=>$row["user_lname"],"email"=>$email,"pass"=>$pass,"sitename"=>$WEBSITE["sitename"],"siteurl"=>$SCRIPTURL));

		redirect("index.php?cmd=forgot&ok=1");
	}
	else{
		redirect("index.php?cmd=forgot&error=1");
	}
}
?>

<?php if ($THEME[0][0] == 1) : ?>
	<div class="login-wrap p-0">
		<div class="login-content">
			<div class="login-logo">
				<a href="/">
				<?php if ($WEBSITE["logo"]) : ?>
					<img src="../img/<?php echo $WEBSITE["logo"];?>" class="img-responsive" style="margin:0 auto;float:none;" />
				<?php endif; ?>
				</a>
			</div>
			<div class="login-form">
				<div class="form-group"><h4 class="text-center text-uppercase"><?php echo $index_title;?></h4></div>

				<form method="post" class="form-signin">
				<?php if($_GET["error"]) : ?>
					<h5 class="title text-center text-danger mb-3" style="margin-top:15px;line-height:1.5;">E-mail Address whether is <strong>NOT found</strong> in our database<br />or the Member is <strong>Expired</strong> and/or <strong>Suspended</strong>.<br /><br /><a href="index.php?cmd=forgot">&laquo; Go Back</a></h5>
				<?php elseif($_GET["ok"]) : ?>
					<h5 class="title text-center text-success mb-3" style="margin-top:15px;line-height:1.5;">Newly generatad <strong>Password</strong> has been <strong>sent</strong> to your <strong>E-mail Address</strong>.<br />Read it carefully and follow the instructions provided within.<br /><br /><a href="./">Sign In</a></h5>
				<?php else : ?>
					<div class="form-group">
						<input class="fontAwesome au-input au-input--full mb-3" type="email" name="email" placeholder="&#xf0e0; Email">
					</div>
					
					<input class="au-btn au-btn--block au-btn--blue btn-fill m-b-20" name="submit" value="Submit" type="submit">
				</form>
				<?php endif; ?>
			</div>
		</div>
	</div>

<?php elseif ($THEME[0][0] == 2) : ?>
	<div class="login-wrap p-0">
		<div class="login-content">
			<div class="row align-items-center">
				<div class="col-6">
			<div class="login-logo">
				<a href="#">
				<?php if ($WEBSITE["logo"]) : ?>
					<img src="../img/<?php echo $WEBSITE["logo"];?>" class="img-responsive" style="margin:0 auto;float:none;" />
				<?php endif; ?>
				</a>
			</div>
			</div>

			<div class="col-6">
			<div class="login-form">
				<div class="form-group mb-3"><h4 class="text-center" style="font-size: 20px;">Forgot Password</h4></div>

				<form method="post" class="form-signin">
				<?php if($_GET["error"]) : ?>
					<h5 class="title text-center text-danger mb-3" style="margin-top:15px;line-height:1.5;">E-mail Address whether is <strong>NOT found</strong> in our database<br />or the Member is <strong>Expired</strong> and/or <strong>Suspended</strong>.<br /><br /><a href="index.php?cmd=forgot">&laquo; Go Back</a></h5>
				<?php elseif($_GET["ok"]) : ?>
					<h5 class="title text-center text-success mb-3" style="margin-top:15px;line-height:1.5;">Newly generatad <strong>Password</strong> has been <strong>sent</strong> to your <strong>E-mail Address</strong>.<br />Read it carefully and follow the instructions provided within.<br /><br /><a href="./">Sign In</a></h5>
				<?php else : ?>
					<div class="form-group">
						<input class="fontAwesome au-input au-input--full mb-3" type="email" name="email" placeholder="&#xf0e0; Email">
					</div>
					
					<button class="au-btn au-btn--block au-btn--blue m-b-20" name="submit" value="Continue" type="submit" style="padding: 10px;">Submit</button>
				</form>
				<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php elseif($THEME[0][0] == 3) : ?>
	<div class="login-wrap m-t-20">
		<div class="login-content">
			<div class="login-logo">
				<a href="#">
				<?php if ($WEBSITE["logo"]) : ?>
					<img src="../img/<?php echo $WEBSITE["logo"];?>" class="img-responsive" style="margin:0 auto;float:none;" />
				<?php endif; ?>
				</a>
			</div>
			<div class="login-form">
				<div class="form-group"><h4 class="text-center text-uppercase mb-3">Forgot Password</h4></div>

				<form method="post" class="form-signin">
				<?php if($_GET["error"]) : ?>
					<h5 class="title text-center text-danger mb-3" style="margin-top:15px;line-height:1.5;">E-mail Address whether is <strong>NOT found</strong> in our database<br />or the Member is <strong>Expired</strong> and/or <strong>Suspended</strong>.<br /><br /><a href="index.php?cmd=forgot">&laquo; Go Back</a></h5>
				<?php elseif($_GET["ok"]) : ?>
					<h5 class="title text-center text-success mb-3" style="margin-top:15px;line-height:1.5;">Newly generatad <strong>Password</strong> has been <strong>sent</strong> to your <strong>E-mail Address</strong>.<br />Read it carefully and follow the instructions provided within.<br /><br /><a href="./">Sign In</a></h5>
				<?php else : ?>
					<div class="form-group">
						<input class="fontAwesome au-input au-input--full mb-3" type="email" name="LoginName" placeholder="&#xf0e0; Email">
					</div>
					
					<button class="au-btn au-btn--block au-btn--blue m-b-20" name="submit" value="Continue" type="submit">Submit</button>
				</form>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php else: ?>
	<div class="card col-md-12 col-sm-10 col-xs-9" style="margin:0 auto;float:none;">
		<div class="content">
		<?php
		if($_GET["error"]){
		?>
			<h4 class="title text-center"><b>Forgot Password</b></h4>
			<h5 class="title text-center" style="margin-top:15px;line-height:1.5;">E-mail Address whether is <strong>NOT found</strong> in our database<br />or the Member is <strong>Expired</strong> and/or <strong>Suspended</strong>.<br /><br /><a href="index.php?cmd=forgot">&laquo; Go Back</a></h5>
		<?php
		}
		elseif($_GET["ok"]){
		?>
			<h4 class="title text-center"><b>Forgot Password</b></h4>
			<h5 class="title text-center" style="margin-top:15px;line-height:1.5;">Newly generatad <strong>Password</strong> has been <strong>sent</strong> to your <strong>E-mail Address</strong>.<br />Read it carefully and follow the instructions provided within.<br /><br /><a href="./">Sign In</a></h5>
		<?php
		}
		else{
		?>
		<form method="post" class="form-signin">
			<h4 class="title text-center"><b>Forgot Password</b></h4>
			<h5 class="title text-center" style="margin:15px 0;line-height:1.5;">Enter Your <strong>E-mail Address</strong> in the field below and press <strong>Continue</strong>.</h5>
			<div class="form-group">
				<label for="email">E-mail:</label>
				<div class="clearfix"></div>
				<div class="col-md-8">
					<input type="email" id="email" name="email" class="form-control" autofocus />
				</div>
				<div class="col-md-4">
					<input type="submit" name="submit" value="Continue" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill pull-left" style="width:100%;" />
				</div>
			</div>
		</form>
		<?php
		}
		?>
		</div>
</div>
<?php endif; ?>
