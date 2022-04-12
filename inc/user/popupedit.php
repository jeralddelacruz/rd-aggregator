<?php
	// VARIABLE INITIALIZATION
	$id = $_GET["id"];

    if($id&&(!$row=$DB->info("popup","popup_id='$id' and user_id='$UserID'"))){
        redirect("index.php?cmd=popup");
    }

    include("queries/popupedit_func.php");
		
?>
<div class="row">
<div class="col-md-12">
<form method="post" enctype="multipart/form-data">

<?php
if($error){
?>
	<div class="col-md-12">
        <?php
            foreach ($error as $key => $value) {
        ?>
            <div class="alert alert-danger"><?php echo $value;?></div>
        <?php
            }
        ?>
	</div>
<?php
}
?>
    <style>
        #attribute_container .row:not(.remove) .col-md-12.p-3 {
            display: none;
        }

        #attribute_container .row:not(.remove):last-of-type .col-md-12.p-3 {
            display: block;
        }
    </style>
	<div class="col-md-12">
		<div class="card">
			<div class="header card-header d-flex justify-content-between">
				<h4 class="title" style="float:left;margin:5px 15px 15px 0;"><?php echo $index_title;?></h4>
				<div>
    				<a href="index.php?cmd=popup"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Back to Your Pop-up's</div></a>
    				
    				<?php if($id){ ?>
    				<a href="index.php?cmd=popupedit"><div class="btn btn-danger" style="margin-left:10px;">Create New Pop-up</div></a>
    				<?php } ?>
    			</div>

			</div>
			<div class="content card-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="title" style="float:left;margin-top:7px;">Popup Name</label>
							<input type="text" name="name" value="<?php echo $_POST["submit"]?slash($name):$name;?>" placeholder="Your pop-up name" class="form-control" required />
						</div>
					</div>
					
					<?php if($error&&!$title){ ?>
					<div class="red pull-left" style="margin-top:10px;" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></div>
					<?php } ?>
				</div>
                <div class="row">
					<div class="col-md-6">
						
						<!-- BEGIN TRY -->
						<div class="card">
							<div class="content card-body">
								<center><h4 class="mb-3">First Page</h4></center>
								
								<!-- FIRST PAGE -->
								<div class="form-group">
                                    <label for="title" style="float:left;margin-top:7px;">Popup Question</label>
                                    <input type="text" name="question" value="<?php echo $_POST["submit"]?slash($question):$question;?>" placeholder="Your pop-up question" class="form-control" required />
									
									<?php if($error && !$question){ ?>
									<span class="red" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></span>
									<?php } ?>
									
								</div>
								
								<!-- IF UPLOAD MY OWN IMAGE -->
								<!-- NOTE: ADD VALIDATION -->
								<div class="form-group" id="avatar_urlContainer">
									<label for="avatar_url" style="float:left;margin-top:7px;">Upload Image</label>
									<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Upload your own image. Max size is 3mb."><i class="fa fa-question" aria-hidden="true"></i></span>
									
									<?php if($error && !$avatar_url){ ?>
									<span class="red" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></span>
									<?php } ?>
									
									<input type="file" class="form-control" style="overflow: hidden;" name="avatar_url" id="avatar_url" value="<?php echo $_POST["submit"] ? slash($avatar_url) : $avatar_url; ?>" />
									
									<?php if($avatar_url){ ?>
									<img src="../upload/<?php echo $UserID; ?>/popup/<?php echo $avatar_url; ?>" class="img-responsive" />
									<a href="#" onclick="return confirm('Are you sure you wish to delete?') ? window.location.href = 'index.php?cmd=popupedit&id=<?php echo $id; ?>&del=avatar_url' : '';"><img src="../img/del.png" data-toggle="tooltip" title="Delete" style="vertical-align:bottom;" /></a>
									<?php } ?>
								</div>
								
							</div>
						</div>
					</div>
					<div class="col-md-6">
						
						<div class="card">
							<div class="content card-body">
								<center><h4 class="mb-3">Second Page</h4></center>
								
								<!-- BONUS PAGE DESCRIPTION -->
								<div class="form-group">
									<label for="description" style="float:left;margin-top:7px;">Description</label>
									<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Adds a description for your Popup Page."><i class="fa fa-question" aria-hidden="true"></i></span>
									
									<?php if($error && !$description){ ?>
									<span class="red" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></span>
									<?php } ?>
									
									<input type="text" name="description" class="form-control" value="<?php echo $_POST["submit"]?slash($description):$description;?>" required/>
								</div>

                                <div class="form-group">
									<label for="sub_description" style="float:left;margin-top:7px;">Sub Description</label>
									<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Adds a Sub Description for your Popup Page."><i class="fa fa-question" aria-hidden="true"></i></span>
									
									<?php if($error && !$sub_description){ ?>
									<span class="red" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></span>
									<?php } ?>
									
									<input type="text" name="sub_description" class="form-control" value="<?php echo $_POST["submit"]?slash($sub_description):$sub_description;?>" required/>
								</div>
								
								<div class="form-group">
									<label for="sub_description" style="float:left;margin-top:7px;">Button Link</label>
									<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Add a link for you button."><i class="fa fa-question" aria-hidden="true"></i></span>
									
									<?php if($error && !$button_link){ ?>
									<span class="red" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></span>
									<?php } ?>
									
									<input type="text" name="button_link" class="form-control" value="<?php echo $_POST["submit"]?slash($button_link):$button_link;?>" required/>
								</div>

                                <!-- IF UPLOAD MY OWN IMAGE -->
								<!-- NOTE: ADD VALIDATION -->
								<div class="form-group" id="second_image_urlContainer">
									<label for="second_image_url" style="float:left;margin-top:7px;">Upload Image</label>
									<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Upload your own image. Max size is 3mb."><i class="fa fa-question" aria-hidden="true"></i></span>
									
									<?php if($error && !$second_image_url){ ?>
									<span class="red" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></span>
									<?php } ?>
									
									<input type="file" class="form-control" style="overflow: hidden;" name="second_image_url" id="second_image_url" value="<?php echo $_POST["submit"] ? slash($second_image_url) : $second_image_url; ?>" />
									
									<?php if($second_image_url){ ?>
									<img src="../upload/<?php echo $UserID; ?>/popup/<?php echo $second_image_url; ?>" class="img-responsive" />
									<a href="#" onclick="return confirm('Are you sure you wish to delete?') ? window.location.href = 'index.php?cmd=popupedit&id=<?php echo $id; ?>&del=second_image_url' : '';"><img src="../img/del.png" data-toggle="tooltip" title="Delete" style="vertical-align:bottom;" /></a>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12" style="margin-bottom:25px;">
		<input type="submit" name="submit" value="Save All Edits" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill pull-right" />
	</div>
</form>
</div>
</div>
<?php include('../../cache_solution/bottom-cache-v2.php'); ?>