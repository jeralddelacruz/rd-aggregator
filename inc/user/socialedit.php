<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header card-header">
                <h4 class="title" style="float:left;margin:5px 15px 0 0;">Social</h4>
                
                <?php if($id){ ?>
                <a href="index.php?cmd=socialedit"><div class="btn btn-danger" style="margin-left:10px;"><?php echo $index_title;?></div></a>
                <?php } ?>
            </div>
            
            <div class="content card-body">
                <div class="col-md-6">
                    <div class="form-group">
    				    <label for="title" style="float:left;margin-top:7px;">Account Title</label>
    					<input type="text" name="title" value="<?php echo $_POST["submit"] ? slash($account_title) : $account_title;?>" placeholder="Account Title" class="form-control" />
    				</div>
                </div>
            </div>
        </div>
    </div>
</div>