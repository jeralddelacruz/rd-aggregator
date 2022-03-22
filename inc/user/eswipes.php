<?php
if(!ereg(";eswipes;",$cur_pack["pack_ar"])){
	redirect("index.php?cmd=deny");
}

$prb_arr=$DB->get_pack();
$res=$DB->query("select * from $dbprefix"."tpl p WHERE tpl_id IN (" . implode(',', $DFY_1) . ") order by tpl_order");

if($error){
?>
<div class="alert alert-danger"><?php echo $error;?></div>
<?php
}
elseif($_SESSION['msg']){
?>
<div class="alert alert-success"><?php echo $_SESSION['msg']; $_SESSION['msg']='';?></div>
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
                <h4 class="title"><?php echo $index_title;?></h4>
					<!--<small>...</small>-->
				</div>
                <div class="table-responsive">
                    <table class="table table-fixed" id="pop-list">
                        <thead class="">
                            <tr>
                                <th>#</th>
                                <th></th>
                                <th>Title</th>
                                <th>Sales Page</th>
                                <th>Download Page</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(sizeof($res)){
                                $i=1;
                                foreach($res as $row){
                                $id=$row["tpl_id"];
                                $title=$row["tpl_title"];
                                $affiliate=$row["tpl_affiliate"];
                                $emaildocs=$row["tpl_email"];
                                $src=is_file("../upload/tpl/$id/thumb.png")?"../upload/tpl/$id/thumb.png?v=1":"../upload/tpl/thumb.jpg?v=1";
                                $thumb="<a href=\"$src\" title=\"$title\" class=\"view tip fb\" rel=\"gal\"><img src=\"$src\" style=\"max-width:180px;\" /></a>";
                                $show="<img src=\"../img/".strtolower($YN_ARR[$row["tpl_show"]]).".png\" title=\"".$YN_ARR[$row["tpl_show"]]."\" class=\"tip\" />";
                        
                            ?>
                            <tr>
                                <td><?=$i?></td>
                                <td><?=$thumb;?></td>
                                <td><?=$title;?></td>
                                <td class="text-center"><a href="../upload/tpl/<?=$id;?>/" target="_blank" title="<?=$title;?> Sales Page" class="btn btn-primary btn-sm tip">Preview</a></td>
                                <td class="text-center"><?php if(file_exists("../upload/tpl/$id/download.html")){?><a href="../upload/tpl/<?=$id;?>/download.html" target="_blank" title="<?=$title;?> Download Page" class="btn btn-primary btn-sm tip">Preview</a><?php }?></td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <div class="btn-group btn-group-sm" role="group" aria-label="Exit pop actions">
                                            <a title="Get Affiliate Link" href="<?=$affiliate?>" target="_blank" class="btn btn-success btn-fill get-affiliate"><i class="fa fa-link" aria-hidden="true"></i> Get Affiliate Link</a>
                                            <!-- <a title="Get Email Swipe" href="<?=$emaildocs?>" target="_blank" class="btn btn-info btn-fill get-eswipe"><i class="fa fa-envelope"></i></i> Get Email Swipes</a> -->
                                            <!--<a href="index.php?cmd=cbpredit&id=<?=$id;?>" title="Add Affiliate Link or Email Swipe" class="btn btn-primary btn-fill"><i class="fa fa-gear"></i></a>-->
                                        </div>
                                    </div>
                                    
                                </td>
                            </tr>
                            <?php $i++; }} ?>
                        </tbody>

                    </table>
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
