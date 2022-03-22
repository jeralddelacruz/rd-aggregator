<?php
    $user = $DB->info("user", "user_id = '{$UserID}'");
    $subdomainName = "";
    
    if( ! empty( $user ) ){
        $subdomainID    = $user["subdomain_id"];
        $userSubdomain  = $DB->info("user_subdomain", "subdomain_id = '{$subdomainID}'");
        $subdomainName  = $userSubdomain["subdomain_name"];
    }
    
    if( isset( $_POST["submit"] ) ){
        // check if subdomain name is existing
        $newSubdomainName   = $_POST["subdomain"];
        $userSubdomain      = $DB->info("user_subdomain", "subdomain_name = '{$newSubdomainName}'");
        $subdomainID        = $userSubdomain["subdomain_id"];
        
        // VALIDATE THE SUBMITTED SUB DOMAIN
        $pattern = '/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/';
        if (preg_match($pattern, $newSubdomainName))
        {
            redirect("index.php?cmd=home&error=1");
        }
        
        if( !$userSubdomain ){
            $newSubdomainID = $DB->getauto("user_subdomain");
            $insertSubdomain = $DB->query("INSERT INTO {$dbprefix}user_subdomain SET 
				subdomain_id = '{$newSubdomainID}', 
				user_id = '{$UserID}', 
				subdomain_name = '{$newSubdomainName}', 
				subdomain_status = 1
			");
            
            if( $insertSubdomain ){
                // update the active subdomain of current user
                $userQuery = "UPDATE {$dbprefix}user SET subdomain_id = '{$newSubdomainID}' WHERE user_id = '{$UserID}'";
                $updateUser = $DB->query($userQuery);
                
                if( $updateUser ){
                    // redirect the user to the new subdomain
                    redirect( "https://" . $newSubdomainName . ".".$MAINDOMAIN."/user/index.php?cmd=home" );
                }
            }
        }else{
            
            // check if the existing subdomain is attached to the logged in user then update the active subdomain
            if( $userSubdomain["user_id"] === $UserID ){
                
                // update the active subdomain of user
                $userQuery = "UPDATE {$dbprefix}user SET subdomain_id = '{$subdomainID}' WHERE user_id = '{$UserID}'";
                $updateUser = $DB->query($userQuery);
                
                if( $updateUser ){
                    // redirect the user to the new subdomain
                    redirect( "https://" . $newSubdomainName . ".".$MAINDOMAIN."/user/index.php?cmd=home" );
                }
            }else{
                // show warning that the subdomain is already existing
                redirect("index.php?cmd=home&existing=1&subdomain={$newSubdomainName}");
            }
        }
    }
?>
<style>
    .subdomain-field-error {
        border: 1px solid #fc0000;
    }
</style>
<div class="subdomain-form">
	<div class="form-group"><h4 class="text-left text-uppercase">Sub Domain</h4></div>

	<form method="post" class="form-subdomain">
    <?php if($_GET['existing']) : ?>
	    <h5 class="title text-danger" style="margin-top:15px;line-height:1.5;margin-bottom:3px;">The subdomain "<strong><?= $_GET['subdomain'] ?></strong>" is existing, Please use another subdomain.</h5>
	<?php elseif($_GET["ok"]) : ?>
		<!--<h5 class="title text-center text-success" style="margin-top:15px;line-height:1.5;">Subdomain successfully attached to your account. <a href="./">Sign In</a></h5>-->
	<?php elseif($_GET["no"]) : ?>
		<h5 class="title text-center text-success" style="margin-top:15px;line-height:1.5;">Already have a subdomain. <a href="./">Sign In</a></h5>
	<?php endif; ?>
		<div class="form-group">
			<div class="input-group mb-1">
              <input type="text" class="form-control <?= $_GET["error"] ? 'subdomain-field-error' : '' ?>" value="<?= !empty( $subdomainName ) ? $subdomainName : '' ?>" name="subdomain" id="subdomain" placeholder="Enter your sub domain here..." aria-label="Enter your sub domain here..." aria-describedby="basic-addon2" ame="subdomain" required>
              <div class="input-group-append">
                <span class="input-group-text" id="basic-addon2">.<?= $MAINDOMAIN; ?></span>
              </div>
            </div>
			<?php if( !$_GET["error"] ){ ?>
			    <!--<label class="message">You cannot change your subdomain after successfully attached to your account. Please input correct sub domain.</label>-->
			<?php }else{ ?>
			    <label class="message text-danger">The subdomain may only <strong>Contain Letters</strong>.</label>
			<?php } ?>
		</div>
		<button class="au-btn au-btn--blue m-b-20 <?= !$user['subdomain_id'] ? "" : "btn-update" ?>" name="submit" value="Continue" type="submit">Submit</button>
	</form>
	
</div>


<script>
    $( "#subdomain" ).keyup(function(e) {
        let val = $( "#subdomain" ).val();
        let removedSpace = val.replace(/\s/g, '');
        let formatString = removedSpace.toLowerCase();
        $( "#subdomain" ).val( formatString );
    });
</script>