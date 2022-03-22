<?php
	// VARIABLE INITIALIZATION
	$current_user_id = $UserID;
	$dfy_author_id = $WEBSITE["dfy_author"];
	$site_domain_url = $SCRIPTURL;
	
	// ========== CHECK USER SUBDOMAIN ========== //
	include('subdomain/subdomain_checker.php');
	
	// get all the subdomains stored to the databse by logged in user
	$user_subdomains = $DB->query("SELECT * FROM {$dbprefix}user_subdomain WHERE user_id = '{$UserID}'");
	
	// get main domain
	$domain = "";
	$serverName = $_SERVER["SERVER_NAME"];
    $serverName1 = explode( ".com", $serverName )[0];
    $serverName2 = explode(".", $serverName1);
    if( count( $serverName2 ) > 1 ){
        $domain = $serverName2[count($serverName2) - 1];
    }else{
        $domain = $serverName2[0];
    }
    // end of getting main domain
?>

<!-- FRONTEND SECTION -->
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="header card-header d-flex justify-content-between">
				<h4 class="pull-left" style="margin-top: 10px; margin-right: 10px;">Your Subdomain's</h4>
				
				<a href="#" class="btn btn-primary mt-2" data-toggle="modal" data-target="#subdomainModal">Add subdomain</a>
			</div>
			<div class="card-body">
    			<?php if($_SESSION["success_update"]): ?>
            	    <div class="col-md-12 pl-0 pr-0">
                		<div class="alert alert-success">
            		        <p><i class="fas fa-check"></i> <?= $_SESSION["success_update"]; ?></p>
            		        <?php $_SESSION["success_update"] = ""; ?>
                		</div>
                	</div>
                <?php endif; ?>
                <?php if($_SESSION["existing_subdomain"]) : ?>
                    <div class="col-md-12 pl-0 pr-0">
                		<div class="alert alert-danger">
                		    
            		        <p><i class="fa fa-exclamation"></i> <?= $_SESSION["existing_subdomain"]; ?></p>
            		        <?php $_SESSION["existing_subdomain"] = ""; ?>
                		</div>
                	</div>
            	<?php elseif($_SESSION["delete_success"]) : ?>
            		<!--<h5 class="title text-center text-success" style="margin-top:15px;line-height:1.5;">Subdomain successfully attached to your account. <a href="./">Sign In</a></h5>-->
                    <div class="col-md-12 pl-0 pr-0">
                		<div class="alert alert-success">
            		        <p><i class="fa fa-check"></i> <?= $_SESSION["delete_success"]; ?></p>
            		        <?php $_SESSION["delete_success"] = ""; ?>
                		</div>
                	</div>
            	<?php endif; ?>
	            <?php if( count( $user_subdomains ) < 1 ) : ?>
                    <div class="alert alert-warning">
        		        <h4 class="alert-heading"><i class="fas fa-warning"></i> Uh oh! Subdomain Setup</h4>
            		    <p>You don't have subdomain setup yet, add your subdomain now.</p>
            		</div>
        		<?php endif; ?>
		        <?php
		            include("subdomain/dashboard-subdomain.php");
		        ?>
    		</div>
		</div>
	</div>
</div>