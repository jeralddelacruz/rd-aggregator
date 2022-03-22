<?php
if(!preg_match(";scp;",$cur_pack["pack_ar"])){
	redirect("index.php?cmd=deny");
}
include('cbtnav.php');
$hasCbt = false;
$exp = $DB->query("select * from $dbprefix"."scp where user_id=$UserID");
if(isset($_POST["addSocialProof"])){
	$img = '';
	$title = mysql_real_escape_string($_POST['title']);
	$content = mysql_real_escape_string($_POST['content']);
	$link = mysql_real_escape_string($_POST['link']);
	$display_time = mysql_real_escape_string($_POST['display_time']);
	$time_difference = mysql_real_escape_string($_POST['time_difference']);

	if($_FILES["image"]["tmp_name"]&&getimagesize($_FILES["image"]["tmp_name"])&&($_FILES["image"]["size"]<=2097152)){
		$sp_img = time().$_FILES["image"]["name"];
		@move_uploaded_file($_FILES["image"]["tmp_name"],"../upload/scp/".$sp_img);
		$img = $sp_img;
	}

	$DB->query("insert into $dbprefix"."scp values (null, $UserID, '$title', '$img', '$content', '$link', '$display_time', '$time_difference', now());");
	$_SESSION['msg'] = 'created';
	redirect("index.php?cmd=scp");

}elseif(isset($_POST["editSocialProof"])){
	$img = ''; $img_string = '';
	$scp_id = mysql_real_escape_string($_POST['scp_id']);
	$title = mysql_real_escape_string($_POST['title']);
	$content = mysql_real_escape_string($_POST['content']);
	$link = mysql_real_escape_string($_POST['link']);
	$display_time = mysql_real_escape_string($_POST['display_time']);
	$time_difference = mysql_real_escape_string($_POST['time_difference']);
	
	if($_FILES["image"]["tmp_name"]&&getimagesize($_FILES["image"]["tmp_name"])&&($_FILES["image"]["size"]<=2097152)){
		$sp_img = time().$_FILES["image"]["name"];
		@move_uploaded_file($_FILES["image"]["tmp_name"],"../upload/scp/".$sp_img);
		$img = $sp_img;
		$DB->query("update $dbprefix"."scp set scp_title='$title', scp_image='$img', scp_content='$content', scp_link='$link', scp_time='$display_time', scp_diff='$time_difference' where scp_id='$scp_id';");
	}else{
		$DB->query("update $dbprefix"."scp set scp_title='$title', scp_content='$content', scp_link='$link', scp_time='$display_time', scp_diff='$time_difference' where scp_id='$scp_id';");
	}

	$_SESSION['msg'] = 'updated';
	redirect("index.php?cmd=scp");

}elseif(isset($_POST["delSocialProof"])){
	$social_proof_id = mysql_real_escape_string($_POST['social_proof_id']);

	$DB->query("delete FROM $dbprefix"."scp WHERE scp_id='$social_proof_id';");
	$_SESSION['msg'] = 'deleted';
	redirect("index.php?cmd=scp");
}

if($error){
?>
<div class="alert alert-danger"><?php echo $error;?></div>
<?php
}
elseif($_SESSION['msg']){
?>
<div class="alert alert-success">Social Proof has been successfully <strong><?php echo $_SESSION['msg']; $_SESSION['msg']='';?></strong>.</div>
<?php
}
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.js"></script>

<div id="content">
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="header card-header">
					<button class="pull-right btn btn-fill btn-<?php echo $WEBSITE["theme_btn"];?>" data-toggle="modal" data-target="#addSocialProof">Create new social proof</button>
					<h4 class="title">Social Proof</h4>
				</div>
				<div class="content card-body">
					<div class="row">
						<div class="col-md-12 col-sm-12">
						    <div class="table-responsive">
                                <table class="table table-fixed " id="pop-list">
                                    <thead class="">
                                        <tr>
                                            <th>Title</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($exp as $key => $value){ ?>
                                        <tr>
                                            <td><?=$value['scp_title']?></td>
                                            <td><?=$value['scp_created']?></td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                  <div class="btn-group btn-group-sm" role="group" aria-label="Exit pop actions">
                                                    <button type="button" class="btn btn-info btn-fill btn-prev-e" 
                                                        data-title="<?=$value['scp_title']?>" 
                                                        data-image="<?=$value['scp_image']?>" 
                                                        data-link="<?=$value['sp_link']?>" 
                                                        data-content="<?=$value['scp_content']?>" 
                                                        data-time="<?=$value['scp_time']?>">
                                                        <i class="fa fa-search"></i></i></button>
                                                    <button type="button" class="btn btn-primary btn-fill btn-edit-e" data-eid="<?=$value['scp_id']?>"><i class="fa fa-pencil"></i></i></button>
                                                    <button type="button" class="btn btn-danger btn-fill btn-delete-e" data-eid="<?=$value['scp_id']?>" data-toggle="modal" data-target="#delSocialProof"><i class="fa fa-trash"></i></button>
                    		                      </div>    
                                                </div>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>

                                </table>
                            </div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="addSocialProof" class="modal fade" role="dialog" data-backdrop="false" style="background-color: rgba(0,0,0,.5);">
  <div class="modal-dialog modal-lg mw-100" style="width: 80%;">

    <!-- Modal content-->
    <div class="modal-content">
		<form method="post" action="index.php?cmd=scp" name="addSocialProofForm" enctype="multipart/form-data">
			<div class="modal-header">
				<h4 class="modal-title">Create Social Proof</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-6 col-md-12">
						<div class="table-responsive">
							<table class="table">
								<tbody>
									<tr>
										<th width="20%"><strong>Image <span class="blue" data-toggle="tooltip" data-placement="right" title="Upload social proof image. Standard image size is 100px x 100px"><i class="fa fa-question" aria-hidden="true"><i></span></strong></th>
										<td>
											<input type="file" name="image" accept="image/*" class="form-control-file" id="image" required>
											<p class="text-muted">Standard image size is 100px x 100px</p>
										</td>
									</tr>
									<tr>
										<th width="20%"><strong>Title <span class="blue" data-toggle="tooltip" data-placement="right" title="Add title of your social proof. Maximum length 80 characters"><i class="fa fa-question" aria-hidden="true"></i></span></strong></th>
										<td>
											<input type="text" name="title" maxlength="80" placeholder="Enter Social Proof Title" class="form-control" required>
										</td>
									</tr>
									<tr>
										<th width="20%"><strong>Content <span class="blue" data-toggle="tooltip" data-placement="right" title="Add content for your social proof. Maximum length 200 characters"><i class="fa fa-question" aria-hidden="true"></i></span></strong></th>
										<td>
											<textarea name="content" maxlength="200" id="" class="form-control" rows="5" required></textarea>
										</td>
									</tr>
									<tr>
										<th width="20%"><strong>Link <span class="blue" data-toggle="tooltip" data-placement="right" title="Set link of your social proof"><i class="fa fa-question" aria-hidden="true"></i></span></strong></th>
										<td>
											<input type="url" name="link" placeholder="Enter Social Proof Link" class="form-control" required>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-lg-6 col-md-12">
							<div class="card-header card-header-primary">
								<h4 class="card-title"><strong>Settings</strong></h4>
								<p class="card-category">Configure settings for your social proof</p>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table">
										<tbody>
											<tr>
												<th width="30%"><strong>Display Time <span class="blue" data-toggle="tooltip" min="1" data-placement="right" title="Set how long social proof will be displayed in seconds"><i class="fa fa-question" aria-hidden="true"></i></span></strong></th>
												<td>
													<input type="number" name="display_time" placeholder="Enter Display Time (seconds)" class="form-control" required>
												</td>
											</tr>
											<tr>
												<th width="30%"><strong>Time Difference<span class="blue" data-toggle="tooltip" min="1" data-placement="right" title="Set how long for the next social proof will be displayed in seconds"><i class="fa fa-question" aria-hidden="true"></i></span></strong></th>
												<td>
													<input type="number" name="time_difference" placeholder="Enter Time Difference (seconds)" class="form-control" required>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-disabled btn-fill" data-dismiss="modal">Cancel</button>
				<button type="submit" name="addSocialProof" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Save</button>
			</div>
		</form>
    </div>

  </div>
</div>

<div id="editSpcialProof" class="modal fade" role="dialog" data-backdrop="false" style="background-color: rgba(0,0,0,.5);">
  <div class="modal-dialog modal-lg mw-100" style="width: 80%;">

    <!-- Modal content-->
    <div class="modal-content">
		<form method="post" action="index.php?cmd=scp" name="addSocialProofForm" enctype="multipart/form-data">
			<div class="modal-header">
				<h4 class="modal-title">Edit Social Proof</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-6 col-md-12">
						<div class="table-responsive">
							<table class="table">
								<tbody>
									<tr>
										<th width="20%">
											<strong>Image <span class="blue" data-toggle="tooltip" data-placement="right" title="Upload social proof image. Standard image size is 100px x 100px"><i class="fa fa-question" aria-hidden="true"></i></span></strong>
										</th>
										<td>
											<input type="file" name="image" accept="image/*" class="form-control-file" id="image">
											<p class="text-muted">Standard image size is 100px x 100px</p>
											<img src="" class="socialProofImg" alt="" width="100px" height="100px">
										</td>
									</tr>
									<tr>
										<th width="20%"><strong>Title <span class="blue" data-toggle="tooltip" data-placement="right" title="Add title of your social proof. Maximum length 80 characters"><i class="fa fa-question" aria-hidden="true"></i></span></strong></th>
										<td>
											<input type="text" name="title" maxlength="80" placeholder="Enter Social Proof Title" class="form-control" required>
										</td>
									</tr>
									<tr>
										<th width="20%"><strong>Content <span class="blue" data-toggle="tooltip" data-placement="right" title="Add content for your social proof. Maximum length 200 characters"><i class="fa fa-question" aria-hidden="true"></i></span></strong></th>
										<td>
											<textarea name="content" maxlength="200" id="" class="form-control" rows="5" required></textarea>
										</td>
									</tr>
									<tr>
										<th width="20%"><strong>Link <span class="blue" data-toggle="tooltip" data-placement="right" title="Set link of your social proof"><i class="fa fa-question" aria-hidden="true"></i></span></strong></th>
										<td>
											<input type="url" name="link" placeholder="Enter Social Proof Link" class="form-control" required>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-lg-6 col-md-12">
							<div class="card-header card-header-primary">
								<h4 class="card-title"><strong>Settings</strong></h4>
								<p class="card-category">Configure settings for your social proof</p>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table">
										<tbody>
											<tr>
												<th width="30%"><strong>Display Time <span class="blue" data-toggle="tooltip" min="1" data-placement="right" title="Set how long social proof will be displayed in seconds"><i class="fa fa-question" aria-hidden="true"></i></span></strong></th>
												<td>
													<input type="number" name="display_time" placeholder="Enter Display Time (seconds)" class="form-control" required>
												</td>
											</tr>
											<tr>
												<th width="30%"><strong>Time Difference<span class="blue" data-toggle="tooltip" min="1" data-placement="right" title="Set how long for the next social proof will be displayed in seconds"><i class="fa fa-question" aria-hidden="true"></i></span></strong></th>
												<td>
													<input type="number" name="time_difference" placeholder="Enter Time Difference (seconds)" class="form-control" required>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
					</div>
				</div>
			</div>
			<input type="hidden" name="scp_id">
			<div class="modal-footer">
				<button type="button" class="btn btn-disabled btn-fill" data-dismiss="modal">Cancel</button>
				<button type="submit" name="editSocialProof" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Save</button>
			</div>
		</form>
    </div>

  </div>
</div>

<div id="delSocialProof" class="modal fade" role="dialog" data-backdrop="false" style="background-color: rgba(0,0,0,.5);">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Are you sure?</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<form method="post" action="index.php?cmd=scp" id="delSp">
					<div class="form-group">
						<span class="category">Once deleted, it will be impossible to recover data!</span>
						<input type="hidden" id="socialProofId" name="social_proof_id" required />
					</div>
				</form>
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-disabled btn-fill" data-dismiss="modal">Cancel</button>
	      <button type="submit" name="delSocialProof" class="btn btn-danger btn-fill"  form="delSp">Delete social proof</button>
      </div>
    </div>
  </div>
</div>
<script src="../js/bootstrap-notify.js"></script>

<script>
    $(document).ready(function() {
        $('#pop-list').DataTable();
    });

    function readURL(input, element) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $(element).attr('src', e.target.result);
                $(element).show();
            }

            reader.readAsDataURL(input.files[0]);
        }else{
            $(element).attr('src', '');
            $(element).hide();
        }
    }

    function preview(data) {
        console.log(data);
        var template = '';
        template += '<div data-notify="container" style="background: white; color: #000; padding: 15px; box-shadow:0 1px 5px #a2a2a2;" class="col-xs-11 col-sm-3 sp-container-hits alert alert-{0}" role="alert">';
        template += '   <button type="button" aria-hidden="true" class="close" data-notify="dismiss" style="top: unset;">×</button>';
        template += '   <img data-notify="icon" class="pull-left sp-img" style="max-width: 120px; margin-right: 7px; min-height: 120px; max-height: 120px;">';
        template += '   <span data-notify="title">{1}</span>';
        template += '   <span data-notify="message" class="msg">{2}</span>';
        template += '   <a href="{3}" target="{4}" data-notify="url"></a>';
        template += '</div>';

        var title = data.title.length > 63 ? data.title.substring(0, 60) + '...' : data.title;

        var notify = $.notify({
            icon: `/upload/scp/${data.image}`,
            title: '<h5><strong>' + title + '</strong></h5>',
            message: '<small>' + data.content + '</small>',
            url: data.link,
            target: '_blank'
        },{
            placement: {
                from: "bottom",
                align: "left"
            },
            type: 'minimalist',
            delay: data.time * 1000,
            icon_type: 'image',
            template: template,
        });  

        return notify;
    }


    $('.btn-prev-e').click(function(){
        var data = $(this).data();
        preview(data);
    });

    $('.btn-edit-e').click(function(){
        var spid = $(this).data('eid');
        $.ajax({
			url: 'ajax.php',
			type: 'POST',
			data: {
				spid : spid,
				tip : 'editSpcialProof',
			},
			success: function(result){
				var scpData = JSON.parse(result);
				$('#editSpcialProof input[name=scp_id]').val(spid);
				$('#editSpcialProof .socialProofImg').attr('src', `../upload/scp/${scpData.image}`);
				$('#editSpcialProof input[name=title]').val(scpData.title);
				$('#editSpcialProof textarea[name=content]').val(scpData.scp_content);
				$('#editSpcialProof input[name=link]').val(scpData.scp_link);
				$('#editSpcialProof input[name=display_time]').val(scpData.scp_time);
				$('#editSpcialProof input[name=time_difference]').val(scpData.scp_diff);
				$('#editSpcialProof').modal('show', {backdrop: 'static'});
			}
		});
    });

    $('.btn-delete-e').click(function(){
        var eid = $(this).data('eid');
        $("#delSocialProof .modal-body #socialProofId").val(eid);
    });

</script>