<?php
	if($_POST["LoginSubmit"]){
		$insert_log = $DB->query("INSERT INTO {$dbprefix}log SET user_id = '{$UserID}', 
			log_rd = '" . time() . "', 
			log_ip = '{$_SERVER["REMOTE_ADDR"]}'");

		$_SESSION["Door"] = 0;
	}
	
	// ========== CHECK USER SUBDOMAIN ========== //
	include('subdomain/subdomain_checker.php');
	
	// CODE_SECTION_PHP_2: VARIABLE_INITIALIZATION
	$DFYAuthorID = $WEBSITE["dfy_author"];
	$campaigns_type = "dfy";
	$temp_ads_id = array();
	$temp_categories_id = array();
	$temp_content_id = array();
	$temp_news_id = array();
	$temp_popup_id = array();
	$campaigns = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = '{$UserID}' AND subdomain_id = 0");
	$hasDfyCampaigns = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = '{$UserID}' AND subdomain_id = 0");
	
	if( sizeof($hasDfyCampaigns) == 0 ){
	    // CODE_SECTION_PHP_2: ADS2
	    // CHECK IF USER ID HAS ALREADY COPIED DATA
	    $user_ads = $DB->query("SELECT * FROM {$dbprefix}ads2 WHERE user_id = '{$UserID}' AND subdomain_id = 0");
	    $dfy_ads = $DB->query("SELECT * FROM {$dbprefix}ads2 WHERE user_id = '{$DFYAuthorID}' AND subdomain_id = 0");
	    if( count( $user_ads ) <= 0 ){
	        foreach( $dfy_ads as $dfy_ad ){
	            $ads_id = $DB->getauto("ads2");
    			$ads_name = $dfy_ad["ads_name"];
    			$ads_url = $dfy_ad["ads_url"];
    			$ads_image = $dfy_ad["ads_image"];
    			$ads_type = $dfy_ad["ads_type"];
    			
    			$insert_campaign = $DB->query("INSERT INTO {$dbprefix}ads2 SET 
    				ads_id = '{$ads_id}', 
    				user_id = '{$UserID}',
    				ads_name = '{$ads_name}', 
    				ads_type = '{$ads_type}', 
    				ads_url = '{$ads_url}'
    			");
    			
    			// COPY DONE FOR YOU CAMPAIGN IMAGES TO NEW USER
    			$dfy_upload_directory = "../upload/{$DFYAuthorID}/";
    			$user_upload_directory = "../upload/{$UserID}/";
    			
    			$copy_from = $dfy_upload_directory . $ads_image;
    			$copy_to = $user_upload_directory . $ads_image;
    			if(copy($copy_from, $copy_to)){
    				$update_ads_image = $DB->query("UPDATE {$dbprefix}ads2 SET ads_image = '{$ads_image}' WHERE ads_id = '{$ads_id}'");
    			}
    			
    			$ads_data = [
    			    "dfy_ads_id"    => $dfy_ad['ads_id'],
    			    "user_ads_id"    => $ads_id
    			];
    			
    			$temp_ads_id[] = $ads_data;
	        }
	    }
	    
	    // CHECK IF USER ID HAS ALREADY COPIED DATA
	    $user_category = $DB->query("SELECT * FROM {$dbprefix}category WHERE user_id = '{$UserID}' AND subdomain_id = 0");
	    $dfy_categories = $DB->query("SELECT * FROM {$dbprefix}category WHERE user_id = '{$DFYAuthorID}' AND subdomain_id = 0");
	    if( count( $user_category ) <= 0 ){
	        foreach( $dfy_categories as $dfy_category ){
	            $category_id = $DB->getauto("category");
    			$category_name = $dfy_category["category_name"];
    			$category_desc = $dfy_category["category_desc"];
    			
    			$insert_campaign = $DB->query("INSERT INTO {$dbprefix}category SET 
    				category_id = '{$category_id}', 
    				user_id = '{$UserID}', 
    				category_name = '{$category_name}', 
    				category_desc = '{$category_desc}'
    			");
    			
    			$category_data = [
    			    "dfy_category_id"     => $dfy_category['category_id'],
    			    "user_category_id"    => $category_id
    			];
    			
    			$temp_categories_id[] = $category_data;
	        }
	    }
	    
	    // CHECK IF USER ID HAS ALREADY COPIED DATA
	    $user_popup = $DB->query("SELECT * FROM {$dbprefix}popup WHERE user_id = '{$UserID}' AND subdomain_id = 0");
	    $dfy_popups = $DB->query("SELECT * FROM {$dbprefix}popup WHERE user_id = '{$DFYAuthorID}' AND subdomain_id = 0");
	    if( count( $user_popup ) <= 0 ){
	        foreach( $dfy_popups as $dfy_popup ){
	            $popup_id = $DB->getauto("popup");
    			$user_id = $dfy_popup["user_id"];
    			$avatar_url = $dfy_popup["avatar_url"];
    			$name = $dfy_popup["name"];
    			$question = $dfy_popup["question"];
    			$description = $dfy_popup["description"];
    			$sub_description = $dfy_popup["sub_description"];
    			$button_link = $dfy_popup["button_link"];
    			$second_image_url = $dfy_popup["second_image_url"];
    			$is_active = $dfy_popup["is_active"];
    			
    			$insert_popup = $DB->query("INSERT INTO {$dbprefix}popup SET 
    				popup_id = '{$popup_id}', 
    				user_id = '{$UserID}', 
    				name = '{$name}', 
    				question = '{$question}', 
    				description = '{$description}', 
    				sub_description = '{$sub_description}', 
    				button_link = '{$button_link}', 
    				is_active = '{$is_active}'
    			");
    			
    			// COPY DONE FOR YOU CAMPAIGN IMAGES TO NEW USER
    			$dfy_upload_directory_1 = "../upload/{$DFYAuthorID}/popup/";
    			$user_upload_directory_1 = "../upload/{$UserID}/popup/";
    
                if (!file_exists($user_upload_directory_1)) {
                    mkdir($user_upload_directory_1, 0777, true);
                }
    			
    			$copy_from = $dfy_upload_directory_1 . $avatar_url;
    			$copy_to = $user_upload_directory_1 . $avatar_url;
    			if(copy($copy_from, $copy_to)){
    				$update_popup_image = $DB->query("UPDATE {$dbprefix}popup SET avatar_url = '{$avatar_url}' WHERE popup_id = '{$popup_id}'");
    			}
    		
    			// COPY DONE FOR YOU CAMPAIGN IMAGES TO NEW USER
    			$copy_from_2 = $dfy_upload_directory_1 . $second_image_url;
    			$copy_to_2 = $user_upload_directory_1 . $second_image_url;
    			if(copy($copy_from_2, $copy_to_2)){
    				$update_popup_second_image = $DB->query("UPDATE {$dbprefix}popup SET second_image_url = '{$second_image_url}' WHERE popup_id = '{$popup_id}'");
    			}
    			
    			$popup_data = [
    			    "dfy_popup_id"     => $dfy_popup['popup_id'],
    			    "user_popup_id"    => $popup_id
    			];
    			
    			$temp_popup_id[] = $popup_data;
	        }
	    }
	    
	    // CHECK IF USER ID HAS ALREADY COPIED DATA
	    $user_contents = $DB->query("SELECT * FROM {$dbprefix}content WHERE user_id = '{$UserID}' AND subdomain_id = 0");
	    $dfy_contents = $DB->query("SELECT * FROM {$dbprefix}content WHERE user_id = '{$DFYAuthorID}' AND subdomain_id = 0");
	    if( count( $user_contents ) <= 0 ){
	        foreach( $dfy_contents as $dfy_content ){
	            $content_id = $DB->getauto("content");
	            $banner_ads_id = $dfy_content["banner_ads_id"];
	            $sidebar_ads_id = $dfy_content["sidebar_ads_id"];
    			$content_title = $dfy_content["content_title"];
    			$content_image = $dfy_content["content_image"];
    			$feed_link = $dfy_content["feed_link"];
    			$category_id = $dfy_content["category_id"];
    			$category_status = $dfy_content["category_status"];
    			
    			$user_banner_ads_id = null;
    			$user_sidebar_ads_id = null;
    			$user_category_id = null;
    			
    			// GET THE USER ADS
    		    foreach( $temp_ads_id as $temp_ad_id ){
    		        if( $temp_ad_id['dfy_ads_id'] == $banner_ads_id ){
    		            $user_banner_ads_id = $temp_ad_id['user_ads_id'];
    		        }
    		    }
    		    foreach( $temp_ads_id as $temp_ad_id ){
    		        if( $temp_ad_id['dfy_ads_id'] == $sidebar_ads_id ){
    		            $user_sidebar_ads_id = $temp_ad_id['user_ads_id'];
    		        }
    		    }	
    		    
    			// GET THE USER CATEGORY
    		    foreach( $temp_categories_id as $temp_category_id ){
    		        if( $temp_category_id['dfy_category_id'] == $category_id ){
    		            $user_category_id = $temp_category_id['user_category_id'];
    		        }
    		    }
    			
    			$insert_content = $DB->query("INSERT INTO {$dbprefix}content SET 
    				content_id = '{$content_id}', 
    				user_id = '{$UserID}', 
    				banner_ads_id = '{$user_banner_ads_id}', 
    				sidebar_ads_id = '{$user_sidebar_ads_id}', 
    				content_title = '{$content_title}', 
    				feed_link = '{$feed_link}', 
    				category_id = '{$user_category_id}', 
    				category_status = '{$category_status}'
    			");
    			
    			// COPY DONE FOR YOU CAMPAIGN IMAGES TO NEW USER
    			$dfy_upload_directory = "../upload/{$DFYAuthorID}/";
    			$user_upload_directory = "../upload/{$UserID}/";
    			
    			$copy_from = $dfy_upload_directory . $content_image;
    			$copy_to = $user_upload_directory . $content_image;
    			if(copy($copy_from, $copy_to)){
    				$update_content_image = $DB->query("UPDATE {$dbprefix}content SET content_image = '{$content_image}' WHERE content_id = '{$content_id}'");
    			}
    			
    			$content_data = [
    			    "dfy_content_id"     => $dfy_content['content_id'],
    			    "user_content_id"    => $content_id
    			];
    			
    			$temp_content_id[] = $content_data;
	        }
	    }
	    
	    // CHECK IF USER ID HAS ALREADY COPIED DATA
	    $user_news = $DB->query("SELECT * FROM {$dbprefix}news WHERE user_id = '{$UserID}'");
	    $dfy_news = $DB->query("SELECT * FROM {$dbprefix}news WHERE user_id = '{$DFYAuthorID}'");
	    if( count( $user_news ) <= 0 ){
	        foreach( $dfy_news as $dfy_new ){
	            $news_id = $DB->getauto("news");
    			$users_id = 0;
    			$rss_url = 0;
    			$campaign_id = 0;
    			$content_id = $dfy_new["content_id"];
    			$news_link = $dfy_new["news_link"];
    			$news_image = $dfy_new["news_image"];
    			$uploaded_image = $dfy_new["uploaded_image"];
    			$news_published_date = $dfy_new["news_published_date"];
    			$news_title = $dfy_new["news_title"];
    			$news_author = $dfy_new["news_author"];
    			$news_content = $dfy_new["news_content"];
    			$news_description = $dfy_new["news_description"];
    			
    			$user_content_id = null;
    			
    			// GET THE USER CATEGORY
    		    foreach( $temp_content_id as $temp_id ){
    		        if( $temp_id['dfy_content_id'] == $content_id ){
    		            $user_content_id = $temp_id['user_content_id'];
    		        }
    		    }
    			
    			$insert_news = $DB->query("INSERT INTO {$dbprefix}news SET 
    				news_id = '{$news_id}', 
    				user_id = '{$UserID}', 
    				users_id = '{$users_id}', 
    				rss_url = '{$rss_url}', 
    				content_id = '{$user_content_id}', 
    				news_link = '{$news_link}',
    				news_image = '{$news_image}', 
    				uploaded_image = '{$uploaded_image}', 
    				news_published_date = '{$news_published_date}',
    				news_title = '{$news_title}',
    				news_author = '{$news_author}', 
    				news_content = '{$news_content}', 
    				news_description = '{$news_description}'
    			");
    			
    			$news_data = [
    			    "dfy_news_id"     => $dfy_new['news_id'],
    			    "user_news_id"    => $news_id
    			];
    			
    			$temp_news_id[] = $news_data;
	        }
	    }
	    
		$campaigns_dfy = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE user_id = '{$DFYAuthorID}' AND subdomain_id = 0");
        $temp_campaigns = array();
        
		foreach($campaigns_dfy as $campaign_dfy){
			$campaigns_id = $DB->getauto("campaigns");

			$campaigns_title = $campaign_dfy["campaigns_title"];
			$campaigns_type = $campaign_dfy["campaigns_type"];
			$content_id = $campaign_dfy['content_id'];
			$popup_id = $campaign_dfy['popup_id'];
			$campaigns_theme_color = $campaign_dfy["campaigns_theme_color"];
			$campaigns_theme_text_color = $campaign_dfy["campaigns_theme_text_color"];
			$campaigns_theme_font = $campaign_dfy["campaigns_theme_font"];
			$campaigns_modal_headline = $campaign_dfy["campaigns_modal_headline"];
			$campaigns_modal_sub_headline = $campaign_dfy["campaigns_modal_sub_headline"];
			$campaigns_modal_image = $campaign_dfy["campaigns_modal_image"];
			$campaigns_modal_button_link = $campaign_dfy["campaigns_modal_button_link"];
			$campaigns_modal_button_text = $campaign_dfy["campaigns_modal_button_text"];
			$campaigns_header_image = $campaign_dfy["campaigns_header_image"];
			$campaigns_logo = $campaign_dfy["campaigns_logo"];
			$campaigns_headline = $campaign_dfy["campaigns_headline"];
			$campaigns_headline_alignment = $campaign_dfy["campaigns_headline_alignment"];
			$campaigns_body = $campaign_dfy["campaigns_body"];
			$campaigns_body_alignment = $campaign_dfy["campaigns_body_alignment"];
			$campaigns_button_text = $campaign_dfy["campaigns_button_text"];
			$campaigns_background_image = $campaign_dfy["campaigns_background_image"];
			$included_article_pages_ids = $campaign_dfy["included_article_pages_ids"];
			$included_webinar_page_id = $campaign_dfy["included_webinar_page_id"];
			$included_ads_id = $campaign_dfy["included_ads_id"];
			$included_c2a_id = $campaign_dfy["included_c2a_id"];
			$campaigns_integrations_platform_name = $campaign_dfy["campaigns_integrations_platform_name"];
			$campaigns_integrations_list_name = $campaign_dfy["campaigns_integrations_list_name"];
			$campaigns_integrations_raw_html = $campaign_dfy["campaigns_integrations_raw_html"];
			$campaigns_tab1 = $campaign_dfy["campaigns_tab1"];
			$campaigns_tab2 = $campaign_dfy["campaigns_tab2"];
			$campaigns_tab3 = $campaign_dfy["campaigns_tab3"];
			$included_tab1_resource_ids = $campaign_dfy["included_tab1_resource_ids"];
			$included_tab2_resource_ids = $campaign_dfy["included_tab2_resource_ids"];
			$included_tab3_resource_ids = $campaign_dfy["included_tab3_resource_ids"];
			$optin_title = $campaign_dfy["optin_title"];
			$optin_btn_title = $campaign_dfy["optin_btn_title"];
			$campaigns_responder_image = $campaign_dfy["campaigns_responder_image"];
			$c2a_title = $campaign_dfy["c2a_title"];
			$c2a_btn_text = $campaign_dfy["c2a_btn_text"];
			$c2a_btn_link = $campaign_dfy["c2a_btn_link"];
			
			// GET THE NEW CONTENT ID
			$user_content_data = array();
			$decodedDFYContents = json_decode( $content_id );
			foreach( $temp_content_id as $temp_content ){
			    if( in_array($temp_content['dfy_content_id'], $decodedDFYContents)  ){
			        $user_content_data[] = $temp_content['user_content_id'];
			    }
			}
			
			// GET THE NEW POPUP ID
			$user_popup_id = null;
			foreach( $temp_popup_id as $item ){
			    if( $item['dfy_popup_id'] == $popup_id ){
			        $user_popup_id = $item['user_popup_id'];
			    }
			}
            
            $encodedContentData = json_encode( $user_content_data );
			$insert_campaign = $DB->query("INSERT INTO {$dbprefix}campaigns SET 
				campaigns_id = '{$campaigns_id}', 
				user_id = '{$UserID}', 
				popup_id = '{$user_popup_id}', 
				campaigns_title = '{$campaigns_title}', 
				campaigns_type = '{$campaigns_type}', 
				content_id = '{$encodedContentData}', 
				campaigns_theme_color = '{$campaigns_theme_color}', 
				campaigns_theme_text_color = '{$campaigns_theme_text_color}',
				campaigns_theme_font = '{$campaigns_theme_font}', 
				campaigns_modal_headline = '{$campaigns_modal_headline}', 
				campaigns_modal_sub_headline = '{$campaigns_modal_sub_headline}',
				campaigns_modal_button_link = '{$campaigns_modal_button_link}', 
				campaigns_modal_button_text = '{$campaigns_modal_button_text}',
				campaigns_headline = '{$campaigns_headline}', 
				campaigns_headline_alignment = '{$campaigns_headline_alignment}', 
				campaigns_body = '{$campaigns_body}', 
				campaigns_body_alignment = '{$campaigns_body_alignment}', 
				campaigns_button_text = '{$campaigns_button_text}', 
				included_article_pages_ids = '{$included_article_pages_ids}', 
				included_webinar_page_id = '{$included_webinar_page_id}', 
				included_ads_id = '{$included_ads_id}', 
				included_c2a_id = '{$included_c2a_id}', 
				campaigns_integrations_platform_name = '{$campaigns_integrations_platform_name}', 
				campaigns_integrations_list_name = '{$campaigns_integrations_list_name}', 
				campaigns_integrations_raw_html = '{$campaigns_integrations_raw_html}', 
				campaigns_tab1 = '{$campaigns_tab1}', 
				campaigns_tab2 = '{$campaigns_tab2}', 
				campaigns_tab3 = '{$campaigns_tab3}', 
				included_tab1_resource_ids = '{$included_tab1_resource_ids}', 
				included_tab2_resource_ids = '{$included_tab2_resource_ids}', 
				optin_title = '{$optin_title}', 
				optin_btn_title = '{$optin_btn_title}', 
				c2a_title = '{$c2a_title}', 
				c2a_btn_text = '{$c2a_btn_text}', 
				c2a_btn_link = '{$c2a_btn_link}'
			");

			// COPY DONE FOR YOU CAMPAIGN IMAGES TO NEW USER
			$dfy_upload_directory = "../upload/{$DFYAuthorID}/";
			$user_upload_directory = "../upload/{$UserID}/";
			
			$copy_from = $dfy_upload_directory . $campaigns_modal_image;
			$copy_to = $user_upload_directory . $campaigns_modal_image;
			if(copy($copy_from, $copy_to)){
				$update_campaign_modal_image = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_modal_image = '{$campaigns_modal_image}' WHERE campaigns_id = '{$campaigns_id}'");
			}

			$copy_from1 = $dfy_upload_directory . $campaigns_logo;
			$copy_to1 = $user_upload_directory . $campaigns_logo;
			if(copy($copy_from1, $copy_to1)){
				$update_campaign_logo = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_logo = '{$campaigns_logo}' WHERE campaigns_id = '{$campaigns_id}'");
			}

			$copy_from_2 = $dfy_upload_directory . $campaigns_background_image;
			$copy_to_2 = $user_upload_directory . $campaigns_background_image;
			if(copy($copy_from_2, $copy_to_2)){
				$update_campaigns_background_image = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_background_image = '{$campaigns_background_image}' WHERE campaigns_id = '{$campaigns_id}'");
			}
			
			$copy_from_3 = $dfy_upload_directory . $campaigns_header_image;
			$copy_to_3 = $user_upload_directory . $campaigns_header_image;
			if(copy($copy_from_3, $copy_to_3)){
				$update_campaigns_header_image = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_header_image = '{$campaigns_header_image}' WHERE campaigns_id = '{$campaigns_id}'");
			}
			
			$copy_from_4 = $dfy_upload_directory . $campaigns_responder_image;
			$copy_to_4 = $user_upload_directory . $campaigns_responder_image;
			if(copy($copy_from_4, $copy_to_4)){
				$update_campaigns_responder_image = $DB->query("UPDATE {$dbprefix}campaigns SET campaigns_responder_image = '{$campaigns_responder_image}' WHERE campaigns_id = '{$campaigns_id}'");
			}
			
			$insert_campaign = $DB->query("SELECT * FROM {$dbprefix}campaigns WHERE campaigns_id = '{$campaigns_id}'")[0];
			$temp_campaigns[] = $insert_campaign;
		}
	}
	
	
	// get all the subdomains stored to the databse by logged in user
	$user_subdomains = $DB->query("SELECT * FROM {$dbprefix}user_subdomain WHERE user_id = '{$UserID}'");
	
	// get main domain
	$domain = "";
	$serverName = $_SERVER["SERVER_NAME"];
    $serverName1 = explode( ".com", $serverName )[0];
    $serverName2 = explode(".", $serverName1);
    if( count( $serverName2 ) > 1 ){
        $domain = $serverName2[count($serverName2) - 1];
    }else{
        $domain = $serverName2[0];
    }
    // end of getting main domain
?>
<script src="../js/poll.js"></script>
<style>
    .table td, .table th {
        border-top: none;
    }
    
    .mt-2 {
        margin-top: 2rem !important;
    }
</style>
<div class="row">
	<div class="col-md-9">
		<div class="card article-t">
			<div class="card-body website-welcome"><?= $WEBSITE["welcome"]; ?></div>
		</div>

		<div class="row">
			<div class="col-md-12 text-center mb-5"><?= get_ad(3); ?></div>

			<footer class="m-auto">
				<div class="row">
					<div class="col-lg-12">
						<?php if(count($FOOTER)) : ?>
						<ul class="list-unstyled">
							<?php foreach($FOOTER as $link) : ?>
							<li><a href="<?= "index.php?cmd=page&id=" . $link["page_id"]; ?>"><?= $link["page_title"]; ?></a></li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>
					</div>
				</div>
			</footer>

			<div class="col-md-12">
				<p class="copyright">&copy; <?= date("Y") . " " . $WEBSITE["sitename"]; ?>. All rights reserved.</p>
			</div>
		</div>
	</div>
	<?= get_poll(); ?>
</div>

<script src="../assets/js/chartist.min.js"></script>

<?php if($MOD_ARR["ecg"] && preg_match(";ecg;", $cur_pack["pack_ar"])) : ?>
	<?php
		$flat_num = dir_count("../upload/{$UserID}/flat");
		$flat_max = $ECG_ARR["flat"];
		$flat_left = $flat_max - $flat_num;
		$flat_num_per = round(100 * $flat_num / $flat_max);
		$flat_left_per = round(100 * $flat_left / $flat_max);

		$td_num = dir_count("../upload/{$UserID}/3d");
		$td_max = $ECG_ARR["3d"];
		$td_left = $td_max - $td_num;
		$td_num_per = round(100 * $td_num / $td_max);
		$td_left_per = round(100 * $td_left / $td_max);

		$bg_num = dir_count("../aie/upload/$UserID/bg","_s");
		$bg_max = $ECG_ARR["bg"];
		$bg_left = $bg_max - $bg_num;
		$bg_num_per = round(100 * $bg_num / $bg_max);
		$bg_left_per = round(100 * $bg_left / $bg_max);

		$icon_num = dir_count("../aie/upload/$UserID/icon");
		$icon_max = $ECG_ARR["icon"];
		$icon_left = $icon_max - $icon_num;
		$icon_num_per = round(100 * $icon_num / $icon_max);
		$icon_left_per = round(100 * $icon_left / $icon_max);
	?>

<div class="row">
	<div class="col-md-3">
		<div class="card">
			<div class="header">
				<h4 class="title">Flat Graphics</h4>
				<p class="category">Folders Usage Statistics</p>
			</div>
			<div class="content">
				<div id="pie_3d" class="ct-chart ct-perfect-fourth"></div>
				<div class="footer">
					<div class="legend">
						<i class="fa fa-circle text-green"></i> Used
						<i class="fa fa-circle text-blue"></i> Left
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
			<div class="header">
				<h4 class="title">My Templates</h4>
				<p class="category">Folders Usage Statistics</p>
			</div>
			<div class="content">
				<div id="pie_bg" class="ct-chart ct-perfect-fourth"></div>
				<div class="footer">
					<div class="legend">
						<i class="fa fa-circle text-green"></i> Used
						<i class="fa fa-circle text-blue"></i> Left
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card">
			<div class="header">
				<h4 class="title">My Images</h4>
				<p class="category">Folders Usage Statistics</p>
			</div>
			<div class="content">
				<div id="pie_icon" class="ct-chart ct-perfect-fourth"></div>
				<div class="footer">
					<div class="legend">
						<i class="fa fa-circle text-green"></i> Used
						<i class="fa fa-circle text-blue"></i> Left
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	Chartist.Pie("#pie_flat", {labels:["<?= $flat_left; ?> left","<?= $flat_num; ?> used"], series:[<?= $flat_left_per; ?>,<?= $flat_num_per; ?>]});
	Chartist.Pie("#pie_3d", {labels:["<?= $td_left; ?> left","<?= $td_num; ?> used"], series:[<?= $td_left_per; ?>, <?= $td_num_per; ?>]});
	Chartist.Pie("#pie_bg", {labels:["<?= $bg_left; ?> left","<?= $bg_num; ?> used"], series:[<?= $bg_left_per; ?>, <?= $bg_num_per; ?>]});
	Chartist.Pie("#pie_icon", {labels:["<?= $icon_left; ?> left","<?= $icon_num; ?> used"], series:[<?= $icon_left_per; ?>, <?= $icon_num_per;?>]});
	
</script>
<?php endif; ?>