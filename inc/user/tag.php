<?php
	if(!$cur_page = $DB->info("page", "page_slug = '{$cmd}'")){
		redirect("index.php?cmd=home");
	}

	if(!preg_match(";$PackID;", $cur_page["page_pack"])){
		redirect("index.php?cmd=deny");
	}

	$id = $cur_page["page_id"];
	$body = $cur_page["page_body"];
	$child = "";

	$res = $DB->query("SELECT * FROM {$dbprefix}page WHERE page_pid = '{$id}' AND page_fe = '0' AND page_pack LIKE '%;$PackID;%'");
	if(count($res)){
		foreach($res as $row){
			$child .= "<p><a href=\"index.php?cmd=page&id={$row["page_id"]}\">{$row["page_title"]}</a></p>";
		}
	}

	$body = str_replace("%child%", $child,$body);
	$body = str_replace("[SITE_URL]", $SCRIPTURL, $body);
	$body = str_replace("[SITE_NAME]", $WEBSITE['sitename'], $body);
?>
<div class="row">
	<div class="col-md-12">
		<div class="card article-content">
			<div class="card-header">
				<h2><?= $cur_page["page_title"]; ?></h2>
			</div>
			<div class="card-body">
				<?= $body; ?>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>