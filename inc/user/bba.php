<?php
	// CHECK USER'S PACKAGE. DENY ACCESS IF USER DOES NOT HAVE THE FEATURE IN HIS/HER PACKAGE
	if(!preg_match(";bba;", $cur_pack["pack_ar"])) : redirect("index.php?cmd=deny"); endif;

	// VARIABLE INITIALIZATION
	$pageb_type = "bba";

	$bba_campaigns = $DB->query("SELECT * FROM {$dbprefix}pageb WHERE user_id = '{$UserID}' AND pageb_type = '{$pageb_type}'");

	// SOCIAL SHARES VERSION 1
	$social_shares_v1 = array(
		"Email" => "fa fa-envelope", 
		"Facebook" => "fa fa-facebook", 
		"Twitter"  => "fa fa-twitter", 
		"LinkedIn"  => "fa fa-linkedin", 
		"Tumblr"  => "fa fa-tumblr", 
		"Reddit"  => "fa fa-reddit", 
		"Pinterest"  => "fa fa-pinterest"
	);

	// DELETE A CAMPAIGN
	if(isset($_GET["delete"])){
		$pageb_id_to_delete = $_GET["delete"];

		$delete = $DB->query("DELETE FROM {$dbprefix}pageb WHERE pageb_id = '{$pageb_id_to_delete}'");

		if($delete){
			$_SESSION["msg_success"] = "Campaign deleted.";

			redirect("index.php?cmd=bba");
		}
	}
?>
<!-- DATATABLE CDN -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.js"></script>

<!-- CUSTOMIZED THE DATATABLE'S PAGINATION BUTTON COLOR SCHEME -->
<style type="text/css">
	.page-item .page-link{
		color: #666;
	}

	.page-item.active .page-link{
		background-color: #6c757d;
		border-color: #6c757d;
		color: #fff;
	}
</style>

<div class="container-fluid">
	<!-- DISPLAY SUCCESS OR ERROR -->
	<?php if($_SESSION["msg_success"]) : ?>
	<div class="col-md-12">
		<div class="alert alert-success"><?php echo $_SESSION["msg_success"]; $_SESSION["msg_success"] = ""; ?></div>
	</div>
	<?php endif; ?>
	
	<?php if($_SESSION["msg_error"]) : ?>
	<div class="col-md-12">
		<div class="alert alert-danger"><?php echo $_SESSION["msg_error"]; $_SESSION["msg_error"] = ""; ?></div>
	</div>
	<?php endif; ?>

	<div class="col-md-12">
		<div class="card">
			<div class="header card-header">
				<div class="d-flex flex-row justify-content-between align-items-center">
					<div class="p-2">
						<h4 style="padding: 10px;"><?= $index_title; ?></h4>
						<small style="padding-left: 10px;">Manage your Campaigns here</small>
					</div>
					<div class="p-2 mr-auto">
						<a class="btn btn-outline-secondary" href="index.php?cmd=bbaedit">Create New</a>
					</div>
				</div>
			</div>
			<div class="content table-responsive table-full-width" style="padding: 10px;">
				<table class="table table-hover table-striped" id="campaigns">
					<thead>
						<tr>
							<th>Campaign Title</th>
							<th class="text-center">Edit</th>
							<th class="text-center">Publish</th>
							<th class="text-center">Social Shares</th>
							<th class="text-center">Delete</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($bba_campaigns as $bba_campaign) : ?>
							<?php
								$squeeze_url = $SCRIPTURL . "add/squeeze.php?s=" . $bba_campaign["pageb_campaign_unique_identifier_squeeze"];
								$affiliate_url = $SCRIPTURL . "add/affiliate.php?s=" . $bba_campaign["pageb_campaign_unique_identifier_affiliate"];
								if($bba_campaign["pageb_campaign_lead_magnet_id"]){
									$download_url = $SCRIPTURL . "add/download.php?s=" . $bba_campaign["pageb_campaign_unique_identifier_download"];
									// var_dump($download_url);
								}
								else{
									$download_url = "You haven't chosen a lead magnet.";
									// var_dump($download_url);
								}

								$share_email = "mailto:?subject=" . "Campaign" . "&body=Check this out: " . urlencode($SCRIPTURL . "squeeze.php?s=" . $bba_campaign["pageb_campaign_unique_identifier_squeeze"]) . "\" title=\"Share by Email";
								$share_facebook = "https://www.facebook.com/sharer.php?u=" . urlencode($SCRIPTURL . "squeeze.php?s=" . $bba_campaign["pageb_campaign_unique_identifier_squeeze"]);
								$share_twitter = "https://twitter.com/share?url=" . urlencode($SCRIPTURL . "squeeze.php?s=" . $bba_campaign["pageb_campaign_unique_identifier_squeeze"]) . "&text=" . $bba_campaign["pageb_campaign_title"] . "&hashtags=Campaign";
								$share_linkedin = "https://www.linkedin.com/shareArticle?url=" . urlencode($SCRIPTURL . "squeeze.php?s=" . $bba_campaign["pageb_campaign_unique_identifier_squeeze"]) . "&title=Campaign";
								$share_tumblr = "https://www.tumblr.com/share/link?url=" . urlencode($SCRIPTURL . "squeeze.php?s=" . $bba_campaign["pageb_campaign_unique_identifier_squeeze"]) . "&name=Campaign&description=Check this out.";
								$share_reddit = "https://reddit.com/submit?url=" . urlencode($SCRIPTURL . "squeeze.php?s=" . $bba_campaign["pageb_campaign_unique_identifier_squeeze"]) . "&title=Campaign";
								$share_pinterest = "https://pinterest.com/pin/create/bookmarklet/?media=[post-img]&url=" . urlencode($SCRIPTURL . "squeeze.php?s=" . $bba_campaign["pageb_campaign_unique_identifier_squeeze"]) . "&is_video=[is_video]&description=Campaign";
							?>
						<tr>
							<td><?= $bba_campaign["pageb_campaign_title"]; ?></td>
							<td class="text-center">
								<a href="index.php?cmd=bbaedit&id=<?= $bba_campaign["pageb_id"]; ?>" class="btn btn-secondary">
									<i class="fa fa-pencil-square-o"></i> &nbsp;Edit
								</a>
							</td>
							<td class="text-center">
								<button class="btn btn-secondary btn-publish" onclick="getAttributes(this)" data-toggle="modal" data-target="#publish-modal" 
								data-campaign-title="<?= $bba_campaign["pageb_campaign_title"]; ?>" 
								data-squeeze-url="<?= $squeeze_url; ?>" 
								data-affiliate-url="<?= $affiliate_url; ?>" 
								data-download-url="<?= $download_url; ?>" 
								data-squeeze-url-view="<?= $squeeze_url; ?>&v=1" 
								data-affiliate-url-view="<?= $affiliate_url; ?>&v=1" 
								data-download-url-view="<?= $download_url; ?>&v=1" >
									<i class="fa fa-file-text-o"></i> &nbsp;Publish
								</button>
							</td>
							<td class="text-center">
								<button class="btn btn-secondary" onclick="getAttributes(this)" data-toggle="modal" data-target="#social-shares-modal" 
								data-share-email="<?= $share_email; ?>" 
								data-share-facebook="<?= $share_facebook; ?>" 
								data-share-twitter="<?= $share_twitter; ?>" 
								data-share-linkedin="<?= $share_linkedin; ?>" 
								data-share-tumblr="<?= $share_tumblr; ?>" 
								data-share-reddit="<?= $share_reddit; ?>" 
								data-share-pinterest="<?= $share_pinterest; ?>" 
								>
									<i class="fa fa-share-square-o"></i> &nbsp;Share
								</button>
							</td>
							<td class="text-center">
								<a href="index.php?cmd=bba&delete=<?= $bba_campaign["pageb_id"]; ?>" class="btn btn-danger">
									<i class="fa fa-times"></i> &nbsp;Delete
								</a>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- PUBLISH MODAL -->
<div class="modal fade" id="publish-modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Publish Modal</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
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
								
								<h4>Squeeze Page URL</h4>
								<div class="input-group mb-4">
									<input type="text" class="form-control" name="squeeze_url_view_input" id="squeeze_url_view_input" value="" readonly>
									<div class="input-group-append">
										<button type="button" id="squeeze_url_view_copy" onclick="copyData(this)" class="btn btn-outline-secondary btn-copy">Copy</button>
									</div>
									<div class="input-group-append">
										<a href="" target="_blank" class="btn btn-outline-secondary btn-viewsqueeze" id="squeeze_url_view_anchor">View</a>
									</div>
								</div>
								
								<h4>Affiliate Page URL</h4>
								<div class="input-group mb-4">
									<input type="text" class="form-control" name="affiliate_url_view_input" id="affiliate_url_view_input" value="" readonly>
									<div class="input-group-append">
										<button type="button" id="affiliate_url_view_copy" onclick="copyData(this)" class="btn btn-outline-secondary btn-copy">Copy</button>
									</div>
									<div class="input-group-append">
										<a href="" target="_blank" class="btn btn-outline-secondary btn-viewbonus" id="affiliate_url_view_anchor">View</a>
									</div>
								</div>
								
								<h4>Download Page URL</h4>
								<div class="input-group mb-4">
									<input type="text" class="form-control" name="download_url_view_input" id="download_url_view_input" value="" readonly>
									<div class="input-group-append">
										<button type="button" id="download_url_view_copy" onclick="copyData(this)" class="btn btn-outline-secondary btn-copy">Copy</button>
									</div>
									<div class="input-group-append">
										<a href="" target="_blank" class="btn btn-outline-secondary btn-viewdownload" id="download_url_view_anchor">View</a>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="v-pills-embed-pages" role="tabpanel" aria-labelledby="v-pills-embed-pages-tab">
								<h4 class="mb-4">Copy the code and Paste anywhere you want the page to show.</h4>
								
								<h4>Squeeze Page Embed</h4>
								<div class="input-group mb-4">
									<input type="text" class="form-control" name="squeeze_url_embed" id="squeeze_url_embed" value="" readonly>
									<div class="input-group-append">
										<button type="button" id="squeeze_url_embed_copy" onclick="copyData(this)" class="btn btn-outline-secondary btn-copy">Copy</button>
									</div>
								</div>
								
								<h4>Affiliate Page Embed</h4>
								<div class="input-group mb-4">
									<input type="text" class="form-control" name="affiliate_url_embed" id="affiliate_url_embed" value="" readonly>
									<div class="input-group-append">
										<button type="button" id="affiliate_url_embed_copy" onclick="copyData(this)" class="btn btn-outline-secondary btn-copy">Copy</button>
									</div>
								</div>
								
								<h4>Download Page Embed</h4>
								<div class="input-group mb-4">
									<input type="text" class="form-control" name="download_url_embed" id="download_url_embed" value="" readonly>
									<div class="input-group-append">
										<button type="button" id="download_url_embed_copy" onclick="copyData(this)" class="btn btn-outline-secondary btn-copy">Copy</button>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="v-pills-downloadpage" role="tabpanel" aria-labelledby="v-pills-downloadpage-tab">
								<h4 class="mb-4">Download pages individually.</h4>
								
								<h4>Squeeze Page Download</h4>
								<div class="input-group mb-4">
									<input type="text" class="form-control" name="squeeze_url_download" id="squeeze_url_download" value="" readonly>
									<div class="input-group-append">
										<a href="" target="_blank" download class="btn btn-outline-secondary" id="squeeze_url_download_2">Download</a>
									</div>
								</div>
								
								<h4>Affiliate Page Download</h4>
								<div class="input-group mb-4">
									<input type="text" class="form-control" name="affiliate_url_download" id="affiliate_url_download" value="" readonly>
									<div class="input-group-append">
										<a href="" target="_blank" download class="btn btn-outline-secondary" id="affiliate_url_download_2">Download</a>
									</div>
								</div>
								
								<h4>Download Page Download</h4>
								<div class="input-group mb-4">
									<input type="text" class="form-control" name="download_url_download" id="download_url_download" value="" readonly>
									<div class="input-group-append">
										<a href="" target="_blank" download class="btn btn-outline-secondary" id="download_url_download_2">Download</a>
									</div>
								</div>
							</div>

							<div class="tab-pane fade" id="v-pills-download" role="tabpanel" aria-labelledby="v-pills-download-tab">
								<h4 class="mb-4">Download all pages.</h4>
								
								<div class="input-group">
									<input type="text" class="form-control" name="download_all_pages" value="Download Squeeze, Affiliate, and Download Pages." readonly>
									<a href="" target="_blank" download id="squeeze_page_download"></a>
									<a href="" target="_blank" download id="affiliate_page_download"></a>
									<a href="" target="_blank" download id="download_page_download"></a>
									<div class="input-group-append">
										<button class="btn btn-outline-secondary" id="download_all_pages_button">Download</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" type="button" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!-- SOCIAL SHARES MODAL -->
<div class="modal fade" id="social-shares-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Social Shares</h4>
				<button class="close" type="button" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<?php foreach($social_shares_v1 as $social_shares_v1_key => $social_shares_v1_value) : ?>
				<a class="btn btn-outline-secondary mt-1 social-share-v1-button" href="" target="_blank"><i class="<?= $social_shares_v1_value ?>"></i> <?= $social_shares_v1_key;  ?></a>
				<?php endforeach; ?>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" type="button" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#campaigns').DataTable();
	});
</script>

<script type="text/javascript">
	function getAttributes(attributes){
		// ATTRIBUTES FROM PHP FOREACH
		var campaignTitleData = attributes.getAttribute("data-campaign-title");
		var squeezeURLData = attributes.getAttribute("data-squeeze-url");
		var affiliateURLData = attributes.getAttribute("data-affiliate-url");
		var downloadURLData = attributes.getAttribute("data-download-url");

		// DISPLAY IN THESE INPUTS
		// VIEW
		var squeezeURLViewInput = document.getElementById("squeeze_url_view_input");
		var affiliateURLViewInput = document.getElementById("affiliate_url_view_input");
		var downloadURLViewInput = document.getElementById("download_url_view_input");
		var squeezeURLViewAnchor = document.getElementById("squeeze_url_view_anchor");
		var affiliateURLViewAnchor = document.getElementById("affiliate_url_view_anchor");
		var downloadURLViewAnchor = document.getElementById("download_url_view_anchor");
		squeezeURLViewInput.value = squeezeURLData;
		affiliateURLViewInput.value = affiliateURLData;
		downloadURLViewInput.value = downloadURLData;
		squeezeURLViewAnchor.href = squeezeURLData;
		affiliateURLViewAnchor.href = affiliateURLData;
		downloadURLViewAnchor.href = downloadURLData;

		// EMBED
		var squeezeURLEmbedInput = document.getElementById("squeeze_url_embed");
		var affiliateURLEmbedInput = document.getElementById("affiliate_url_embed");
		var downloadURLEmbedInput = document.getElementById("download_url_embed");
		squeezeURLEmbedInput.value = "<iframe src=\"" + squeezeURLData + "\" style=\"width: 100%; height: 100%; border: none;\"></iframe>";
		affiliateURLEmbedInput.value = "<iframe src=\"" + affiliateURLData + "\" style=\"width: 100%; height: 100%; border: none;\"></iframe>";
		downloadURLEmbedInput.value = "<iframe src=\"" + downloadURLData + "\" style=\"width: 100%; height: 100%; border: none;\"></iframe>";

		// DOWNLOAD
		var squeezeURLDownloadInput = document.getElementById("squeeze_url_download");
		var affiliateURLDownloadInput = document.getElementById("affiliate_url_download");
		var downloadURLDownloadInput = document.getElementById("download_url_download");
		var squeezeURLDownloadAnchor = document.getElementById("squeeze_url_download_2");
		var affiliateURLDownloadAnchor = document.getElementById("affiliate_url_download_2");
		var downloadURLDownloadAnchor = document.getElementById("download_url_download_2");
		squeezeURLDownloadInput.value = squeezeURLData;
		affiliateURLDownloadInput.value = affiliateURLData;
		downloadURLDownloadInput.value = downloadURLData;
		squeezeURLDownloadAnchor.href = squeezeURLData;
		affiliateURLDownloadAnchor.href = affiliateURLData;
		downloadURLDownloadAnchor.href = downloadURLData;

		// DOWNLOAD ALL PAGES
		var squeezePageDownload = document.getElementById("squeeze_page_download");
		var affiliatePageDownload = document.getElementById("affiliate_page_download");
		var downloadPageDownload = document.getElementById("download_page_download");
		squeezePageDownload.href = squeezeURLData;
		affiliatePageDownload.href = affiliateURLData;
		downloadPageDownload.href = downloadURLData;

		var downloadAllPagesButton = document.getElementById("download_all_pages_button");
		downloadAllPagesButton.onclick = function(){
			squeezePageDownload.click();
			affiliatePageDownload.click();
			downloadPageDownload.click();
		}

		// SOCIAL SHARE V1
		var social_shares_v1_button = document.querySelectorAll(".social-share-v1-button");

		var data_share_email = attributes.getAttribute("data-share-email");
		var data_share_facebook = attributes.getAttribute("data-share-facebook");
		var data_share_twitter = attributes.getAttribute("data-share-twitter");
		var data_share_linkedin = attributes.getAttribute("data-share-linkedin");
		var data_share_tumblr = attributes.getAttribute("data-share-tumblr");
		var data_share_reddit = attributes.getAttribute("data-share-reddit");
		var data_share_pinterest = attributes.getAttribute("data-share-pinterest");

		var data_shares = [data_share_email, data_share_facebook, data_share_twitter, data_share_linkedin, data_share_tumblr, data_share_reddit, data_share_pinterest];

		for(var offset = 0; offset < social_shares_v1_button.length; offset++){
			if(data_shares[offset] == undefined){
				data_shares[offset] = "#";
			}

			social_shares_v1_button[offset].href = data_shares[offset];
		}

	}

	function copyData(clipboardText){
		var copyThis = clipboardText.parentNode.previousElementSibling;
		
		copyThis.select();
		document.execCommand("copy");
	}
</script>