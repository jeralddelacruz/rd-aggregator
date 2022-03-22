<?php
	// ADMIN PAGE
	// page.php
	
	$extra_page = "page_fe = '0'";

	if($_GET["move"]){
		$move = $_GET["move"];
		if($row = $DB->info("page", "page_id = '" . substr($move, 1, strlen($move)) . "'")){
			$DB->move("page", $move, "page_pid='{$row["page_pid"]}' AND {$extra_page}");
		}
		redirect("index.php?cmd=page");
	}
	elseif($_GET["del"]){
		page_del($_GET["del"], $extra_page);
		redirect("index.php?cmd=page");
	}

	$page_arr = $DB->get_pack();

	$res = array();
	$DB->get_cat("page", $res, 0, 0, 0, " AND {$extra_page}");
?>
<h2><?= $index_title; ?><a href="index.php?cmd=pageedit" class="add">Add New</a></h2>
<table class="tbl_list">
	<tr>
		<th>Title</th>
		<th>Available For</th>
		<th class="w50 ac">Top</th>
		<th class="w50 ac">Bottom</th>
		<th class="w50 ac">Order</th>
		<th class="w50 ac">Action</th>
	</tr>
	<?php if(count($res)) : ?>
		<?php foreach($res as $id=>$row) : ?>
			<?php
				$pack_arr = explode(";", trim($row["pack"]));
				$pack = "";
				foreach($pack_arr as $v){
					if($page_arr[$v]){$pack .= $page_arr[$v] . ", ";}
				}
				$pack = substr($pack, 0, strlen($pack) - 2);
				$timg = "<img src=\"../img/" . strtolower($YN_ARR[$row["tmenu"]]) . ".png\" title=\"" . $YN_ARR[$row["tmenu"]] . "\" class=\"tip\" />";
				$bimg = "<img src=\"../img/" . strtolower($YN_ARR[$row["bmenu"]]) . ".png\" title=\"" . $YN_ARR[$row["bmenu"]] . "\" class=\"tip\" />";
			?>
		<tr>
			<td><?= add_dash($row["title"], $row["level"]); ?></td>
			<td><?= $pack; ?></td>
			<td class="ac"><?= $timg; ?></td>
			<td class="ac"><?= $bimg; ?></td>
			<td>
				<?php if(!$row["first"]) : ?>
				<a href="<?= "index.php?cmd=page&move=u" . $id; ?>" title="Move Up" class="tip"><img src="../img/oup.gif" /></a>
				<?php endif; ?>

				<?php if(!$row["last"]) : ?>
				<a href="<?= "index.php?cmd=page&move=d" . $id; ?>" title="Move Down" class="tip"><img src="../img/odown.gif" align="right" /></a>
				<?php endif; ?>
			</td>
			<td class="ac">
				<a href="<?= "index.php?cmd=pageedit&id=" . $id; ?>" title="Edit" class="tip"><img src="../img/edit.png" /></a> <a href="<?= "index.php?cmd=page&del=" . $id; ?>" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete this page?');"><img src="../img/del.png" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	<?php endif; ?>
</table>