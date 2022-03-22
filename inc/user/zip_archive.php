<?php
    $zip = new ZipArchive;
    $res = $zip->open('./newscampaign.zip', ZipArchive::CREATE);
    $zip->close();
	
	// CODE_SECTION_PHP_3: DELETE_TO_DATABASE
	$currentPath = dirname(__FILE__);
    $downloadCampaignPath = $currentPath.'/DownloadCampaign/';
    // echo "<script>alert('". $currentPath ."');</script>";
    //     exit;
	if(!empty($_GET["download"])){
		$campaigns_id = $_GET["campaigns_id"];
        
        // Update the campaign id on file
        $current = "<?php";
        $current .= " $";
        $current .= "campaignID = ";
        $current .= $campaigns_id;
        $current .= "; ?>";
        
        file_put_contents($downloadCampaignPath."config/campaign.php", $current);
        
        // Copy the banner file to specific user and folder
	    $campaign = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE campaigns_id = {$campaigns_id}")[0];
	    $bannerFile = $campaign['campaigns_header_image'];
	    // COPY DONE FOR YOU CAMPAIGN IMAGES TO NEW USER
		$original_path = "../upload/{$UserID}/";
		$download_campaign_assets = "../inc/user/DownloadCampaign/assets/banner/";
		$copy_from = $original_path . $bannerFile;
		$copy_to = $download_campaign_assets . $bannerFile;
		copy($copy_from, $copy_to);
	    
        // Copy the ads file to specific user and folder
        // GET ALL Contents from campaign
        $adsFiles = array();
        foreach( json_decode( $campaign['content_id'] ) as $content_id ){
            $contents = $DB->query("SELECT * FROM {$dbprefix}content WHERE content_id = {$content_id}")[0];
            $adsFile = $contents['content_image'];
            // COPY DONE FOR YOU CAMPAIGN IMAGES TO NEW USER
        	$original_path = "../upload/{$UserID}/";
        	$download_campaign_assets = "../inc/user/DownloadCampaign/assets/ads/";
        	$copy_from = $original_path . $adsFile;
        	$copy_to = $download_campaign_assets . $adsFile;
        	copy($copy_from, $copy_to);
        	$adsFiles[] = $adsFile;
        }
        
	    $zip = new ZipArchive;
        $res = $zip->open('../newscampaign.zip', ZipArchive::CREATE);
        
        if ($res === TRUE) {
            $zip->addFile($downloadCampaignPath.'index.php', 'index.php');
            $zip->addFile($downloadCampaignPath.'config/campaign.php', 'config/campaign.php');
            $zip->addFile($downloadCampaignPath.'config/dbconnection.php', 'config/dbconnection.php');
            $zip->addFile($downloadCampaignPath.'config/queries.php', 'config/queries.php');
            
            foreach($adsFiles as $adFile){
                $zip->addFile($downloadCampaignPath.'assets/ads/'.$adFile, 'assets/ads/'.$adFile);
            }
            
            $zip->addFile($downloadCampaignPath.'assets/banner/'.$bannerFile, 'assets/banner/'.$bannerFile);
            $zip->addFile($downloadCampaignPath.'assets/font/SonnyVol2-Black.ttf', 'assets/font/SonnyVol2-Black.ttf');
            $zip->close();
            
            $_SESSION['hasDownloaded'] = true;
            redirect("index.php?cmd=campaigns");
        } else {
            echo 'failed';
        }
	}
?>