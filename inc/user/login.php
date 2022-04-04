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
			<form method="post" action="index.php?cmd=home" class="form-signin">
				<?php if ($_POST["LoginSubmit"]) : ?>
					<div class="alert alert-danger">Authorization <strong>failed</strong>.</div>
				<?php endif; ?>

				<div class="form-group">
					<label>Email Address</label>
					<input class="au-input au-input--full" type="email" name="LoginName" placeholder="Email">
				</div>
				<div class="form-group">
					<label>Password</label>
					<input class="au-input au-input--full" type="password" name="LoginPass" placeholder="Password">
				</div>
				<div class="login-checkbox">
					<label>
						<a href="index.php?cmd=forgot">Forgotten Password?</a>
					</label>
				</div>

				<button class="au-btn au-btn--block au-btn--blue btn-fill m-b-20" name="LoginSubmit" value="LOGIN"  type="submit">sign in</button>
			</form>
		</div>
	</div>
</div>

<?php elseif($THEME[0][0] == 2) : ?>
<div class="login-wrap p-0">
	<div class="login-content">
		<div class="particle1"></div>
		<div class="particle2"></div>
		<div class="particle3"></div>
		<div class="particle4"></div>
		<div class="particle5"></div>
		<div class="particle6"></div>
		<div class="particle7"></div>
		<div class="particle8"></div>
		<div class="row align-items-center">
			<div class="col-6">
		<div class="login-logo">
			<a href="#">
				<?php if ($WEBSITE["logo"]) : ?>
					<img src="../img/<?php echo $WEBSITE["logo"];?>" class="img-responsive" style="margin:0 auto; float:none;" />
				<?php endif; ?>
			</a>
		</div>

		</div>
		<div class="col-6">
			<h2 class="text-center mb-5">LOGIN</h2>
		<div class="login-form">
			<form method="post" action="index.php?cmd=home" class="form-signin">
				<?php if ($_POST["LoginSubmit"]) : ?>
					<div class="alert alert-danger">Authorization <strong>failed</strong>.</div>
				<?php endif; ?>
				
                <?php if ($_SESSION["domain_fail"]) : ?>
					<div class="alert alert-warning"><strong>Oops!</strong> Please use this domain "newsmaximizer.com" <a href="<?= $SCRIPTURL ?>user/">here</a>.</div>
				<?php 
				    $_SESSION["domain_fail"] = "";
				    endif; 
				?>
				<?php if( !$is_subdomain_exist && !$is_maindomain ): ?>
                    <h3>Sorry! this domain is not registered.</h3>
                <?php else: ?>
    				<div class="form-group">
    					<input class="fontAwesome au-input au-input--full" type="email" name="LoginName" placeholder="&#xf0e0; Email">
    				</div>
    				<div class="form-group">
    				    <input class="fontAwesome au-input au-input--full mb-4" type="password" name="LoginPass" placeholder="&#xf023; Password">
    				</div>
    
    				<button class="au-btn au-btn--block au-btn--blue m-b-50 au-btn-rad" name="LoginSubmit" value="LOGIN"  type="submit"  style="padding: 10px;">
    					<i class="fa fa-sign-in"></i> &nbsp;Sign-In
    				</button>
    
    				<div class="login-checkbox">
    					<label style="margin: auto 0px auto auto;">
    						<a href="index.php?cmd=forgot">Forgotten Password?</a>
    					</label>
    				</div>
    			<?php endif; ?>
				</div>
				</div>
			</form>
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
				<form method="post" action="index.php?cmd=home" class="form-signin">
					<?php if ($_POST["LoginSubmit"]) : ?>
						<div class="alert alert-danger">Authorization <strong>failed</strong>.</div>
					<?php endif; ?>

					<div class="form-group">
						<label>Email Address</label>
						<input class="au-input au-input--full" type="email" name="LoginName" placeholder="Email">
					</div>

					<div class="form-group">
						<label>Password</label>
						<input class="au-input au-input--full" type="password" name="LoginPass" placeholder="Password">
					</div>

					<div class="login-checkbox">
						<label>
							<a href="index.php?cmd=forgot">Forgotten Password?</a>
						</label>
					</div>
					<button class="au-btn au-btn--block au-btn--blue m-b-20" name="LoginSubmit" value="LOGIN"  type="submit">sign in</button>
				</form>
			</div>
		</div>
	</div>
<?php else: ?>
<div class="card col-md-8 col-sm-10 col-xs-9" style="margin:0 auto;float:none;padding-bottom:5px;">
	<div class="content">
		<form method="post" action="index.php?cmd=home" class="form-signin">
			<h4 class="title text-center"><b>Authorization</b></h4>
			<br />
		<?php
		if($_POST["LoginSubmit"]){
		?>
			<div class="alert alert-danger">Authorization <strong>failed</strong>.</div>
		<?php
		}
		?>
			<div class="form-group">
				<label for="email">E-mail:</label>
				<input type="email" id="email" name="LoginName" class="form-control" autofocus />
			</div>
			<div class="form-group">
				<label for="pass">Password:</label>
				<input type="password" id="pass" name="LoginPass" class="form-control" />
			</div>
			<input type="submit" name="LoginSubmit" value="LOGIN" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill pull-left" />
			<a href="index.php?cmd=forgot" class="pull-right">Forgot Password?</a>
			<div class="clearfix"></div>
		<?php
		if($WEBSITE["sign"]){
		?>
			<div class="text-center" style="margin-top:10px;">
				<a href="<?php echo $WEBSITE["sign"];?>">New Memeber? Sign Up Now!</a>
			</div>
		<?php
		}
		?>
		</form>
	</div>
</div>
<?php endif; ?>