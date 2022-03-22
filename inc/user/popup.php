<?php include('../../cache_solution/top-cache-v2.php'); ?>
<?php
	// VARIABLE INITIALIZATION
	$current_user_id = $UserID;
	$dfy_author_id = $WEBSITE["dfy_author"];
	$site_domain_url = $SCRIPTURL;
?>
<?php
	// DELETE A ROW
	if($_GET["del"]){
		// CODE_SECTION_PHP_3: DELETE_TO_DATABASE
    	
		$popup_id = $_GET["del"];
		$result = $DB->query("SELECT * FROM {$dbprefix}popup WHERE popup_id = '{$popup_id}'")[0];
		if($result){
		    $target_directory_1 = "../upload/{$UserID}/popup/".$result['avatar_url'];
		    unset($target_directory_1);
		    
		    $target_directory_2 = "../upload/{$UserID}/".$result['second_image_url'];
		    unset($target_directory_2);
		    
		    $delete_ads = $DB->query("DELETE FROM {$dbprefix}popup WHERE popup_id = '{$popup_id}'");

    		if($delete_ads){
    			$_SESSION["msg"] = "Popup deleted.";
    			redirect("index.php?cmd=popup");
    		}
		}
	}
	
	$and_query = "";
	if( $user_subdomain ){
	    $subdomain_id = $user_subdomain["subdomain_id"];
	    $and_query = " AND subdomain_id = '{$subdomain_id}'";
	}else{
	    $and_query = " AND subdomain_id = 0";
	}
?>
<!-- CODE_SECTION_HTML_1: CDN_DATATABLE -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.js"></script>

<!-- RESPONSE SECTION (SUCCESS AND ERROR MESSAGES) -->
<?php if($error){ ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php } ?>

<?php if($_SESSION["msg"]){ ?>
<div class="alert alert-success"><?php echo $_SESSION['msg']; $_SESSION['msg'] = ''; ?></div>
<?php } ?>

<!-- FRONTEND SECTION -->
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="header card-header d-flex justify-content-between">
				<h4 class="pull-left" style="margin-top: 10px; margin-right: 10px;">Your Pop-up's</h4>
				
				<!-- SHOW CREATE BUTTON IF YOU ARE THE DFY AUTHOR -->
				<a href="index.php?cmd=popupedit"><div class="btn btn-danger btn-fill">Create a Pop-up</div></a>
			</div>
			<div class="card-body">
    			<div class="content table-responsive table-full-width">
					<table class="table table-hover table-striped" id="popup-table">
    					<thead>
    						<tr>
    							<th>Title</th>
    							<th>Question</th>
    							<th class="text-center">Edit</th>
    							
    							<!-- SHOW DELETE COLUMN IF YOU ARE THE DFY AUTHOR -->
    							<?php if($current_user_id == $dfy_author_id){ ?>
    							    <th class="text-center">Delete</th>
    							<?php } ?>
    						</tr>
    					</thead>
    					
    					<tbody>
    					<?php
    						// LOOPING PROCESS
    						$popups = $DB->query("SELECT * FROM $dbprefix" . "popup WHERE user_id = '" . $current_user_id . "' {$and_query} ORDER BY popup_id");
    						
    						foreach($popups as $popup){
    							$popup_id = $popup["popup_id"];
    							$name = $popup["name"];
    							$question = $popup["question"];
    					?>
    						<tr>
    							<td><?php echo $name; ?></td>
    							<td><?php echo $question; ?></td>
    							<td class="text-center">
    								<a href="index.php?cmd=popupedit&id=<?php echo $popup_id; ?>" class="btn btn-primary" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
    							</td>
    							
    							<!-- SHOW DELETE BUTTON IF YOU ARE THE DFY AUTHOR -->
    							<?php if($current_user_id == $dfy_author_id){ ?>
    							<td class="text-center">
    								<a href="index.php?cmd=popup&del=<?php echo $popup_id; ?>" class="btn btn-danger" data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you wish to delete this Popup?');"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>
    							</td>
    							<?php } ?>
    						</tr>
    					<?php } ?>
    					</tbody>
    				</table>
    			</div>
    		</div>
    		<div class="card-footer text-center">This is the end of the table.</div>
		</div>
	</div>
</div>
<!-- DATATABLE INITIALIZATION -->
<script type="text/javascript">
	$(document).ready(function(){
		$('#popup-table').DataTable();
	});
</script>

<!-- SCRIPT SECTION -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sharer.js/0.4.0/sharer.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
<?php include('../../cache_solution/bottom-cache-v2.php'); ?>