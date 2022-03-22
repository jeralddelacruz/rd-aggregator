<?php
$id=$_GET["id"];
if(!$cur_page=$DB->info("page","page_id='$id' and page_fe='0' and page_pack like '%;$PackID;%'")){
	redirect("index.php?cmd=home");
}

$body=preg_match("/%product%/i",$cur_page["page_body"])?pr_block($cur_page["page_body"],$cur_page["page_pr"]):$cur_page["page_body"];
$body=str_replace("[SITE_URL]", $SCRIPTURL, $body);
$body=str_replace("[SITE_NAME]",$WEBSITE['sitename'], $body);
$body=str_replace("[ADDRESS]", $WEBSITE['address'], $body);
$body=str_replace("[NOTIF_EMAIL_ADDRESS]", $WEBSITE['notif_email'], $body);

?>
<div class="row">
	<div class="col-md-12">
	<div class="card article-content">
		<div class="card-body">
			<h2><?php echo $cur_page["page_title"];?></h2>
				
				<?php echo $body;?>
			
			<div class="clearfix"></div>
		</div>
	</div>
	</div>
</div>