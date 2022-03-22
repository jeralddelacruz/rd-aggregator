<?php
$id=$_GET["id"];

if(!$row=$DB->info("poll","poll_id='$id'")){
	redirect("index.php?cmd=poll");
}

?>
<h2><?php echo $index_title;?><a href="index.php?cmd=poll" class="add">Back to Polls</a><a href="index.php?cmd=polledit" class="add">Add New Poll</a></h2>
<div class="poll">
<div class="poll_qst"><?php echo $row["poll_qst"];?></div>
<?php echo poll_res($row["poll_opt"],$row["poll_vote"]);?>
</div>