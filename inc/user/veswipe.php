<?php
if(!ereg(";eswipes;",$cur_pack["pack_ar"]))
	redirect("index.php?cmd=deny");

if(!isset($_GET['id']))
	redirect("index.php?cmd=eswipes");

$tplId = strip($_GET['id']);
$res=$DB->query("select * from $dbprefix"."tpl where tpl_id='$tplId'");

if($error){ ?>
    <div class="alert alert-danger"><?php echo $error;?></div>
<?php } elseif($_SESSION['msg']){ ?>
    <div class="alert alert-success"><?php echo $_SESSION['msg']; $_SESSION['msg']='';?></div>
<?php } ?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.js"></script>

<div id="content">
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="header" style="padding: 15px;">
                    <h4 class="title" style="position: relative;"><em><?php echo $res[0]['tpl_title']; ?></em> - Email Swipes
                        <a href="index.php?cmd=eswipes" style="position: absolute;right:0;top:50%;transform: translateY(-50%);" class="btn btn-<?php echo $WEBSITE["theme_btn"]; ?> btn-fill pull-right">Back</a>
                    </h4>
				</div>
            </div>
			<div class="card">
				<div class="content">
					<div class="row">
                        <div class="col-md-12">
                            <?php echo $res[0]['tpl_email']; ?>
                        </div>
                    </div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="eswipe" class="modal fade" role="dialog" data-backdrop="false" style="background-color: rgba(0,0,0,.5);">
  <div class="modal-dialog" style="width: 50%;">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Email Swipe</h4>
      </div>
      <div class="modal-body">
		<div class="row">
			<div class="col-md-12 col-sm-12">
			    <div class="row">
                    <div class="col-lg-12">
                        <div id="eswipehtml" class="card">
                            <div class="content">
                                <p class="body"></p>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
      </div>
    </div>

  </div>
</div>


<script>

jQuery(document).ready(function($){
	$(".fb").fancybox({});
});

    $('.get-affiliate').click(function(){
        var pid = $(this).data('pid');
        $.ajax({
			url: 'ajax.php',
			type: 'POST',
			data: {
				pid : pid,
				tip : 'getAff',
			},
			success: function(result){
			    $('#affihtml .body').html(result);
			}
		});
    });
    
    $('.get-eswipe').click(function(){
        var pid = $(this).data('pid');
        $.ajax({
			url: 'ajax.php',
			type: 'POST',
			data: {
				pid : pid,
				tip : 'getEms',
			},
			success: function(result){
			    $('#eswipehtml .body').html(result);
			}
		});
    });


</script>
