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
            redirect("index.php?cmd=subdomains&error=1");
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
                    $_SESSION["success_update"] = "Successfuly added ".$newSubdomainName." as new subdomain.";
                    redirect( "https://" . $newSubdomainName . ".".$MAINDOMAIN."/user/index.php?cmd=subdomains" );
                }
            }
        }else{
            $_SESSION["existing_subdomain"] = "Subdomain ".$newSubdomainName." is already in use.";
            redirect( "https://" . $subdomainName . ".".$MAINDOMAIN."/user/index.php?cmd=subdomains" );
            
            // // check if the existing subdomain is attached to the logged in user then update the active subdomain
            // if( $userSubdomain["user_id"] === $UserID ){
                
            //     // update the active subdomain of user
            //     $userQuery = "UPDATE {$dbprefix}user SET subdomain_id = '{$subdomainID}' WHERE user_id = '{$UserID}'";
            //     $updateUser = $DB->query($userQuery);
                
            //     if( $updateUser ){
            //         // redirect the user to the new subdomain
            //         redirect( "https://" . $newSubdomainName . ".".$MAINDOMAIN."/user/index.php?cmd=subdomains" );
            //     }
            // }else{
            //     // show warning that the subdomain is already existing
            //     redirect("index.php?cmd=subdomains&existing=1&subdomain={$newSubdomainName}");
            // }
        }
    }
    
    // DELETE_SUBDOMAIN_TO_DATABASE
	if(!empty($_GET["delete"])){
		$subdomain_id = $_GET["subdomain_id"];
		$subdomain_name = $_GET["subdomain_name"];

		$delete_subdomain = $DB->query("DELETE FROM {$dbprefix}user_subdomain WHERE subdomain_id = '{$subdomain_id}'");

		if($delete_subdomain){
			$_SESSION["delete_success"] = "Subdomain deleted.";
            $serverName = $_SERVER["SERVER_NAME"];
            $serverName1 = explode( ".".$MAINDOMAIN, $serverName )[0];
    
            // check if the deleted subdomain is equal to the current subdomain
            // if equal redirect to the main domain
            if( $serverName1 == $subdomain_name){
                redirect( "https://".$MAINDOMAIN."/user/index.php?cmd=subdomains" );
            }else{
                // if not equal remain to the main url
			    redirect("index.php?cmd=subdomains");
            }
		}
	}
?>
<!-- CODE_SECTION_HTML_1: CDN_DATATABLE -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.js"></script>

<style>
    .subdomain-field-error {
        border: 1px solid #fc0000;
    }
</style>
<div class="subdomain-form">
	<!--<div class="form-group mb-4"><h4 class="text-left text-uppercase">Subdomains</h4></div>-->
	
	<table class="table" id="subdomain-table">
      <thead>
        <tr>
          <th scope="col">Subdomains</th>
          <th scope="col">Status</th>
          <th scope="col" width="10">Switch</th>
          <th scope="col" width="10">Delete</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach( $user_subdomains as $user_subdomain ): ?>
            <tr>
              <th scope="row">www.<?= $user_subdomain["subdomain_name"] ?>.<?= $domain ?>.com</th>
              <td><span class="<?= $user_subdomain["subdomain_status"] ? "text-success" : "text-danger" ?>"><?= $user_subdomain["subdomain_status"] ? "Connected" : "Disconnected" ?></span></td>
              <td><a href="#" class="btn btn-warning" data-toggle="modal" data-target="#switch-modal" data-subdomain-name="<?= $user_subdomain["subdomain_name"]; ?>" onclick="getSwitchAttributes(this)"><i class="fa fa-random"></i></a></td>
              <td><a href="#" class="btn btn-danger" data-toggle="modal" data-target="#delete-modal" data-subdomain-id="<?= $user_subdomain["subdomain_id"]; ?>" onclick="getAttributes(this)"><i class="fa fa-trash"></i></a></td>
            </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
</div>
<!-- ADD SUBDOMAIN MODAL -->
<div class="modal fade" id="subdomainModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <form method="post" class="form-subdomain">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add subdomain</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
    		<div class="form-group">
    			<div class="input-group mb-1">
                  <input type="text" class="form-control <?= $_GET["error"] ? 'subdomain-field-error' : '' ?>" value="" name="subdomain" id="subdomain" placeholder="Enter your sub domain here..." aria-label="Enter your sub domain here..." aria-describedby="basic-addon2" ame="subdomain" required>
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
          </div>
          <div class="modal-footer">
            <button type="submit" name="submit" class="btn btn-primary">Add</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </form>
    </div>
  </div>
</div>
<!-- END ADD SUBDOMAIN MODAL -->

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal fade" id="delete-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Delete Confirmation</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body text-center">Are you sure you want to <b>DELETE</b> this subdomain?</div>
			<div class="modal-footer">
				<a class="btn btn-primary" id="delete-button" href="" data-dismiss="modal">Yes I am sure</a>
			</div>
		</div>
	</div>
</div>

<!-- SWITCH CONFIRMATION MODAL -->
<div class="modal fade" id="switch-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Subdomain switching notice</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body text-center">Are you sure you want to <b>Switch</b> into this subdomain?</div>
			<div class="modal-footer">
				<a class="btn btn-primary" id="switch-button" href="" data-dismiss="modal">Yes I am sure</a>
			</div>
		</div>
	</div>
</div>

<script>
    $(document).ready(function(){
		$('#subdomain-table').DataTable();
	});
	
    $( "#subdomain" ).keyup(function(e) {
        let val = $( "#subdomain" ).val();
        let removedSpace = val.replace(/\s/g, '');
        let formatString = removedSpace.toLowerCase();
        $( "#subdomain" ).val( formatString );
    });
    
    function getAttributes(attributes){
		// DELETE CONFIRMATION
		var id_to_delete = attributes.getAttribute("data-subdomain-id");
		var delete_button = document.getElementById("delete-button");
		delete_button.href = `index.php?cmd=subdomains&subdomain_id=${id_to_delete}&delete=1`;
	}
	
	function getSwitchAttributes(attributes){
		// SWITCH CONFIRMATION
		var name_to_switch = attributes.getAttribute("data-subdomain-name");
		var switch_button = document.getElementById("switch-button");
		switch_button.href = `https://${name_to_switch}.<?= $MAINDOMAIN ?>/user/index.php?cmd=subdomains`;
	}
</script>