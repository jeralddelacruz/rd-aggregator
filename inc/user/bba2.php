<?php include('../../cache_solution/top-cache-v2.php'); ?>
<!-- Fancy Box -->
<?php if(isset($THEME[0]) && isset($THEME[0][0]) && $THEME[0][0] == 2) : include_once('fancybox.php'); endif; ?>
<?php

if(!preg_match(";bba2;",$cur_pack["pack_ar"])){
	redirect("index.php?cmd=deny");
}

if($_GET["dld"]){
    $newTypes = array("lp", "sp", "dp");
    $type_arr=array("sp"=>"lp","tp"=>"op","dp"=>"tp", "lp" => "op");
    $dlCount = 1;
    
    foreach($newTypes as $newType){
        $dld=$_GET["dld"];
    	$type=$_GET["type"];
    
    	if(($row=$DB->info("pageb_copy","pageb_id='$dld' and user_id='$UserID' and pageb_type='bba2'"))&&in_array($newType,array_keys($type_arr))){
    	    echo "<a href='$SCRIPTURL/b".$newType.".php?s=".$row["pageb_".$type_arr[$newType]."s"]."&v=1' target='_blank' download='". $row['pageb_title'] . " - b" . $newType .".html' id='dl". $dlCount++ ."'></a>";
    	}
    	else{
    		redirect("index.php?cmd=pageb_copy");
    	}
    }
    ?>
	<script>
	    document.getElementById("dl1").click();
	    document.getElementById("dl2").click();
	    document.getElementById("dl3").click();
	    
	    location.href = "index.php?cmd=bba2";
	</script>
	<?php
    
// 	$dld=$_GET["dld"];
// 	$type=$_GET["type"];

// 	$type_arr=array("sp"=>"lp","tp"=>"op","dp"=>"tp", "lp" => "op");
    
// 	if(($row=$DB->info("pageb_copy","pageb_id='$dld' and user_id='$UserID' and pageb_type='bba2'"))&&in_array($type,array_keys($type_arr))){
// 		ob_clean();
// 		header("Content-Type:text/html");
// 		header("Content-Disposition:attachment;filename=b".$type.".html");
// 		header("Expires:0");
// 		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
// 		header("Pragma:public");
// 		echo file_get_contents("$SCRIPTURL/b".$type.".php?s=".$row["pageb_".$type_arr[$type]."s"]."&v=1");
// 		exit;
// 	}
// 	else{
// 		redirect("index.php?cmd=bba2");
// 	}
}
elseif($_GET["lic"]){
	$url=$WEBSITE["cb_mlic"];

	$_arr=pathinfo($url);
	$filename=$_arr["basename"];

	ob_clean();
	header("Content-Type:application/octet-stream");
	header("Content-Disposition:attachment;filename=\"$filename\"");
	header("Expires:0");
	header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
	header("Pragma:public");
	echo file_get_contents(str_replace(" ","%20",$url));
	exit;
}
elseif($_GET["del"]){
	pageb_del($_GET["del"],"user_id='$UserID' and pageb_type='bba2'");
	redirect("index.php?cmd=bba2");
}

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
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="header card-header">
				<h4 class="title" style="float:left;margin:5px 15px 0 0;"><?php //echo $index_title; ?><?= "Bonus Builder"; ?></h4>
				<a href="index.php?cmd=bbaedit2"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Create</div></a>
<?php
if($LIMIT_ARR["bb"]){
?>
				<div class="pull-right mobile-text">
					<div class="stats">
						<i class="fa fa-clock-o"></i> You can add <strong><?php echo ($LIMIT_ARR["bb_left"]>0)?number_format($LIMIT_ARR["bb_left"],0,".",","):0;?></strong> more Bonus Builder Pages until <strong><?php echo $LIMIT_ARR["to"];?></strong></strong>
					</div>
				</div>
<?php
}
?>
			</div>
			<div class="content table-responsive table-full-width">
				<table class="table table-hover table-striped">
					<thead>
						<tr>
							<th class="">Title</th>
							<th class="text-center">Edit</th>
							<th class="text-center">Publish</th>
							<th class="text-center">Social Shares</th>
							<th class="text-center">Delete</th>
						</tr>
					</thead>
<?php
$res=array();
$res=$DB->query("select * from $dbprefix"."pageb_copy where user_id='$UserID' and pageb_type='bba2' order by pageb_title");
if(sizeof($res)){
?>
					<tbody>
<?php
	foreach($res as $row){
		$id=$row["pageb_id"];
		$bsp_url=$SCRIPTURL."/bbsp.php?s=".$row["pageb_lps"];
//		$btp_url=$SCRIPTURL."/btp.php?s=".$row["pageb_ops"];
		$bdp_url=$SCRIPTURL."/bbdp.php?s=".$row["pageb_tps"];

		$lpv=(int)$row["pageb_lpv"];
//		$opv=(int)$row["pageb_opv"];
		$tpv=(int)$row["pageb_tpv"];
		$cr=$lpv?(number_format(100*$tpv/$lpv,2,".","")."%"):"N/A";
?>
						<tr>
							<td><?php echo $row["pageb_title"];?></td>
							
							<td class="text-center">
								<div class="btn-group">
									<a href="index.php?cmd=bbaedit2&id=<?php echo $id;?>" class="btn btn-primary" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
								</div>
							</td>
							
							<!-- Publish Button -->
							<td class="text-center">
							    <button class="btn btn-primary btn-publish"
                                    data-toggle="modal" 
                                    data-target="#publishModal"
                                    data-title="<?=$row["pageb_title"];?>"
                                    data-webinarurl="<?=$bsp_url;?>"
                                    data-downloadpageurl="<?=$bdp_url;?>"
                                    data-viewwebinar="<?=$bsp_url;?>&v=1"
                                    data-viewdownloadpage="<?=$bdp_url;?>&v=1"
                                    data-downloadsitepages="index.php?cmd=bba2&dld=<?=$id;?>"
                                    ><i class="fa fa-pencil" aria-hidden="true"></i> Publish</button>
							</td>
							
							<td style="text-align: center;">
								<button class="btn btn-primary shareBtn"
									data-title="<?= $row["pageb_title"]; ?>"
									data-url="<?= $btp_url; ?>"
									data-description="<?= htmlentities($row["pageb_social_texts"]); ?>"
									data-subject="<?= htmlentities($row["pageb_title"]); ?>"
									data-image="<?= $row["pageb_image"]; ?>"
									data-pinterest="<?= $row["pageb_pinterest"]; ?>"
									data-picture="<?= $row["pageb_image"]; ?>"
									>
									<i class="fa fa-line-chart"></i>	Traffic
								</button>
							</td>
							
							<td class="text-center">
                                <a href="index.php?cmd=bba2&del=<?php echo $id;?>" class="btn btn-danger" data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you wish to delete this Page?');"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>
							</td>
							<!--<td>-->
							<!--	<input type="text" value="<?php //echo $bsp_url;?>" class="form-control" onclick="this.select();" readonly />-->
							<!--	<a href="<?php //echo $bsp_url."&v=1";?>" target="_blank" class="blue" data-toggle="tooltip" title="View Sales Page"><i class="fa fa-search" aria-hidden="true"></i></a>-->
							<!--	<a href="../embed.php?url=<?php //echo $bsp_url;?>" class="red fb" data-fancybox-type="iframe" data-toggle="tooltip" title="Embed Sales Page"><i class="fa fa-code" aria-hidden="true"></i></a>-->
							<!--	<a href="index.php?cmd=bba2&dld=<?php //echo $id;?>&type=sp" class="green" data-toggle="tooltip" title="Download Sales Page"><i class="fa fa-download" aria-hidden="true"></i></a>-->
							<!--</td>-->
							
							<!--<td>-->
							<!--	<input type="text" value="<?php //echo $btp_url;?>" class="form-control" onclick="this.select();" readonly />-->
							<!--	<a href="<?php //echo $btp_url."&v=1";?>" target="_blank" class="blue" data-toggle="tooltip" title="View Thank You Page"><i class="fa fa-search" aria-hidden="true"></i></a>-->
							<!--	<a href="../embed.php?url=<?php //echo $btp_url;?>" class="red fb" data-fancybox-type="iframe" data-toggle="tooltip" title="Embed Thank You Page"><i class="fa fa-code" aria-hidden="true"></i></a>-->
							<!--	<a href="index.php?cmd=bba&dld=<?php //echo $id;?>&type=tp" class="green" data-toggle="tooltip" title="Download Thank You Page"><i class="fa fa-download" aria-hidden="true"></i></a>-->
							<!--</td>-->
							
							<!--<td>-->
							<!--	<input type="text" value="<?php //echo $bdp_url;?>" class="form-control" onclick="this.select();" readonly />-->
							<!--	<a href="<?php //echo $bdp_url."&v=1";?>" target="_blank" class="blue" data-toggle="tooltip" title="View Download Page"><i class="fa fa-search" aria-hidden="true"></i></a>-->
							<!--	<a href="../embed.php?url=<?php //echo $bdp_url;?>" class="red fb" data-fancybox-type="iframe" data-toggle="tooltip" title="Embed Download Page"><i class="fa fa-code" aria-hidden="true"></i></a>-->
							<!--	<a href="index.php?cmd=bba&dld=<?php //echo $id;?>&type=dp" class="green" data-toggle="tooltip" title="Download Download Page"><i class="fa fa-download" aria-hidden="true"></i></a>-->
							<!--</td>-->
							
							<!--<td style="white-space:nowrap;padding:12px 17px;">-->
							<!--	<abbr data-toggle="tooltip" title="Sales Page Views">SPV:</abbr> <?php //echo $lpv;?><br/>-->
							<!--	<abbr data-toggle="tooltip" title="Thank You Page Views">TPV:</abbr> <?php //echo $opv;?><br/>-->
							<!--	<abbr data-toggle="tooltip" title="Download Page Views">DPV:</abbr> <?php //echo $tpv;?><br/>-->
							<!--	<abbr data-toggle="tooltip" title="Conversion Ratio">CR:</abbr> <?php //echo $cr;?>-->
							<!--</td>-->
							
							<!--<td class="text-center">-->
							<!--	<div class="btn-group">-->
							<!--		 <a href="index.php?cmd=bba2&lic=1" class="yellow" data-toggle="tooltip" title="Download License"><i class="fa fa-star" aria-hidden="true"></i></a> -->
							<!--		<a href="index.php?cmd=bbaedit2&id=<?php //echo $id;?>" class="btn btn-primary" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>-->
							<!--		<a href="index.php?cmd=bba2&del=<?php //echo $id;?>" class="btn btn-danger" data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you wish to delete this Page?');"><i class="fa fa-close" aria-hidden="true"></i></a>-->
							<!--	</div>-->
							<!--</td>-->
						</tr>
<?php
	}
?>
					</tbody>
<?php
}
?>
				</table>
			</div>
			<!-- Social Share  -->
			<div class="modal fade" id="socialShare" tabindex="-1" role="dialog" aria-labelledby="socialShareLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg" role="document">
					<form action="" method="POST">
						<div class="modal-content">
							<div class="modal-header" style="background-color: #ffb900;">
								<h3 class="modal-title" id="socialShareLabel">Share your campaign to all the major social sites!</h3>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							
							<div class="modal-body">
								<div class="share-btns d-flex flex-wrap">
									<?php foreach($SHARING_PLATFORMS as $sPlatform): ?>
										<a 
											href="#"
											class="button btn m-1 text-white" 
											style="background-color: <?= $sPlatform['color']; ?>"
											data-sharer="<?= $sPlatform['key']; ?>"
											<?php foreach($sPlatform['data'] as $key => $data): ?>
												<?= "data-{$key}=''" ?>
											<?php endforeach; ?>
											
											>Share on <?= $sPlatform['name']; ?>
										</a>
									<?php endforeach; ?>
								</div>
							</div>
							
							<div class="modal-footer">
								<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<!-- End Social Share  -->
			
			<!-- Start Publish Button Modal -->
            <div id="publishModal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #ffb900;">
                            <h3 class="modal-title">Publish - <span></span></h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-3">
                                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-view-page" role="tab" aria-controls="v-pills-view-page" aria-selected="true">View Page</a>
                                    <a class="nav-link" id="v-pills-embed-pages-tab" data-toggle="pill" href="#v-pills-embed-pages" role="tab" aria-controls="v-pills-embed-pages" aria-selected="false">Embed Page</a>
                                    <a class="nav-link" id="v-pills-downloadpage-tab" data-toggle="pill" href="#v-pills-downloadpage" role="tab" aria-controls="v-pills-downloadpage" aria-selected="false">Download Page</a>
                                    <a class="nav-link" id="v-pills-download-tab" data-toggle="pill" href="#v-pills-download" role="tab" aria-controls="v-pills-download" aria-selected="false">Download All</a>
                                    </div>
                                </div>
                                <div class="col-9">
                                    <div class="tab-content" id="v-pills-tabContent">
                                        <div class="tab-pane fade show active" id="v-pills-view-page" role="tabpanel" aria-labelledby="v-pills-view-page-tab">
                                            <h4 class="mb-4">View or Copy pages.</h4>
                                            
                                            <!--<h4>Opt-In Page URL</h4>-->
                                            <!--<div class="input-group mb-4">-->
                                            <!--    <input type="text" class="form-control" name="optinurl" id="optinurl" value="" readonly>-->
                                            <!--    <div class="input-group-append">-->
                                            <!--        <button type="button"  data-clipboard-text="" class="btn btn-outline-secondary btn-copy">Copy</button>-->
                                            <!--    </div>-->
                                            <!--    <div class="input-group-append">-->
                                            <!--        <a href="" target="_blank" class="btn btn-outline-secondary btn-viewOptin">View</a>-->
                                            <!--    </div>-->
                                            <!--</div>-->
                                            
                                            <h4>Webinar Page URL</h4>
                                            <div class="input-group mb-4">
                                                <input type="text" class="form-control" name="webinarurl" value="" readonly>
                                                <div class="input-group-append">
                                                    <button type="button" data-clipboard-text="" class="btn btn-outline-secondary btn-copy">Copy</button>
                                                </div>
                                                <div class="input-group-append">
                                                    <a href="" target="_blank" class="btn btn-outline-secondary btn-viewWebinar">View</a>
                                                </div>
                                            </div>
                                            
                                            <h4>Download Page URL</h4>
                                            <div class="input-group mb-4">
                                                <input type="text" class="form-control" name="downloadpageurl" value="" readonly>
                                                <div class="input-group-append">
                                                    <button type="button" data-clipboard-text="" class="btn btn-outline-secondary btn-copy">Copy</button>
                                                </div>
                                                <div class="input-group-append">
                                                    <a href="" target="_blank" class="btn btn-outline-secondary btn-viewDownloadPage">View</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="v-pills-embed-pages" role="tabpanel" aria-labelledby="v-pills-embed-pages-tab">
                                            <h4 class="mb-4">Copy the code and Paste anywhere you want the page to show.</h4>
                                            
                                            <!--<h4>Opt-In Page Embed</h4>-->
                                            <!--<div class="input-group mb-4">-->
                                            <!--    <input type="text" class="form-control" name="optinurlembed" value="" readonly>-->
                                            <!--    <div class="input-group-append">-->
                                            <!--        <button type="button"  data-clipboard-text="" class="btn btn-outline-secondary btn-copy">Copy</button>-->
                                            <!--    </div>-->
                                            <!--</div>-->
                                            
                                            <h4>Webinar Page Embed</h4>
                                            <div class="input-group mb-4">
                                                <input type="text" class="form-control" name="webinarurlembed" value="" readonly>
                                                <div class="input-group-append">
                                                    <button type="button" data-clipboard-text="" class="btn btn-outline-secondary btn-copy">Copy</button>
                                                </div>
                                            </div>
                                            
                                            <h4>Download Page Embed</h4>
                                            <div class="input-group mb-4">
                                                <input type="text" class="form-control" name="downloadpageurlembed" value="" readonly>
                                                <div class="input-group-append">
                                                    <button type="button" data-clipboard-text="" class="btn btn-outline-secondary btn-copy">Copy</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="v-pills-downloadpage" role="tabpanel" aria-labelledby="v-pills-downloadpage-tab">
                                            <h4 class="mb-4">Download pages individually.</h4>
                                            
                                            <!--<h4>Opt-In Page Download</h4>-->
                                            <!--<div class="input-group mb-4">-->
                                            <!--    <input type="text" class="form-control" name="optinurldownload" value="" readonly>-->
                                            <!--    <div class="input-group-append">-->
                                            <!--        <a href="" target="_blank" download="" class="btn btn-outline-secondary btn-downloadOptin">Download</a>-->
                                            <!--    </div>-->
                                            <!--</div>-->
                                            
                                            <h4>Webinar Page Download</h4>
                                            <div class="input-group mb-4">
                                                <input type="text" class="form-control" name="webinarurldownload" value="" readonly>
                                                <div class="input-group-append">
                                                    <a href="" target="_blank" download="" class="btn btn-outline-secondary btn-downloadWebinar">Download</a>
                                                </div>
                                            </div>
                                            
                                            <h4>Download Page Download</h4>
                                            <div class="input-group mb-4">
                                                <input type="text" class="form-control" name="downloadpageurldownload" value="" readonly>
                                                <div class="input-group-append">
                                                    <a href="" target="_blank" download="" class="btn btn-outline-secondary btn-downloadDownloadPage">Download</a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="v-pills-download" role="tabpanel" aria-labelledby="v-pills-download-tab">
                                            <h4 class="mb-4">Download all pages.</h4>
                                            
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="downloadsitepages" value="" readonly>
                                                <div class="input-group-append">
                                                    <a href="" target="_self" class="btn btn-outline-secondary">Download</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Publish Button Modal -->
		</div>
	</div>
</div>
<script>
jQuery(document).ready(function($){

        $('.shareBtn').on('click', function() {
			$.each($(this).data(), function(key, value) {
				$('.share-btns a').attr(`data-${key}`, value)
			});
			
			$('#socialShare').modal('show');
		})

        $(".fb").fancybox({
        	maxWidth: 550,
        	maxHeight: 200,
        	autoSize: false
        });
        
        $('.btn-publish').on('click', function() {
            const { title, optinurl, webinarurl, downloadpageurl, downloadsitepages, viewwebinar, viewdownloadpage } = $(this).data()

            $('#publishModal .modal-title span').text(title)
            
            // Initial Input Values
            $('#publishModal input[name=optinurl]').val(optinurl)
            $('#publishModal input[name=webinarurl]').val(webinarurl)
            $('#publishModal input[name=downloadpageurl]').val(downloadpageurl)
            $('#publishModal input[name=downloadsitepages]').val("Download Webinar, and Download Pages")
            
            
            // Clipboard Text Values
            // $('#publishModal input[name=optinurl]').next().find('button').attr('data-clipboard-text', optinurl)
            $('#publishModal input[name=webinarurl]').next().find('button').attr('data-clipboard-text', webinarurl)
            $('#publishModal input[name=downloadpageurl]').next().find('button').attr('data-clipboard-text', downloadpageurl)
            
            // View Site Pages
            // $('.btn-viewOptin').attr('href', viewoptin)
            $('.btn-viewWebinar').attr('href', viewwebinar)
            $('.btn-viewDownloadPage').attr('href', viewdownloadpage)
            
            // Embed Site Pages
            // $('#publishModal input[name=optinurlembed]').val("<iframe src='" + optinurl + "' width='100%' height='100%' frameborder='no' scrolling='auto'></iframe>")
            // $('#publishModal input[name=optinurlembed]').next().find('button').attr('data-clipboard-text', "<iframe src='" + optinurl + "' width='100%' height='100%' frameborder='no' scrolling='auto'></iframe>")
            $('#publishModal input[name=webinarurlembed]').val("<iframe src='" + webinarurl + "' width='100%' height='100%' frameborder='no' scrolling='auto'></iframe>")
            $('#publishModal input[name=webinarurlembed]').next().find('button').attr('data-clipboard-text', "<iframe src='" + webinarurl + "' width='100%' height='100%' frameborder='no' scrolling='auto'></iframe>")
            $('#publishModal input[name=downloadpageurlembed]').val("<iframe src='" + downloadpageurl + "' width='100%' height='100%' frameborder='no' scrolling='auto'></iframe>")
            $('#publishModal input[name=downloadpageurlembed]').next().find('button').attr('data-clipboard-text', "<iframe src='" + downloadpageurl + "' width='100%' height='100%' frameborder='no' scrolling='auto'></iframe>")
            
            // Download Individual Site Pages
            // $('#publishModal input[name=optinurldownload]').val(optinurl)
            // $('#publishModal input[name=optinurldownload]').next().find('a').attr('href', optinurl)
            // $('#publishModal input[name=optinurldownload]').next().find('a').attr('download', title + " - " + "Opt-In Page.html")
            $('#publishModal input[name=webinarurldownload]').val(webinarurl)
            $('#publishModal input[name=webinarurldownload]').next().find('a').attr('href', webinarurl)
            $('#publishModal input[name=webinarurldownload]').next().find('a').attr('download', title + " - " + "Webinar Page.html")
            $('#publishModal input[name=downloadpageurldownload]').val(downloadpageurl)
            $('#publishModal input[name=downloadpageurldownload]').next().find('a').attr('href', downloadpageurl)
            $('#publishModal input[name=downloadpageurldownload]').next().find('a').attr('download', title + " - " + "Download Page.html")
            
            // Download All Site Pages
            $('#publishModal input[name=downloadsitepages]').next().find('a').attr('href', downloadsitepages)
        })

        const clipboard = new ClipboardJS('.btn-copy');

        clipboard.on('success', function(e) {
            $(e.trigger).text('Copied')

            setTimeout(() => {
                $(e.trigger).text('Copy')
            }, 2500);
        });

});
</script>
<?php include('../../cache_solution/bottom-cache-v2.php'); ?>