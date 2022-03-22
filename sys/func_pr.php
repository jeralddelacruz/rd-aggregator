<?php
function pr_block($body,$pr){
	global $DB,$dbprefix,$WEBSITE;

	$pr_str="";
	$res=$DB->query("select * from $dbprefix"."pr where pr_id IN ($pr) order by pr_order");
	if(sizeof($res)){
		$pr_str.="<div class=\"row\">";
		$i=0;
		foreach($res as $row){
			$id=$row["pr_id"];
			$title=$row["pr_title"];
			$pop=$row["pr_pop"]?(" <img src=\"../img/pop.png\" data-toggle=\"tooltip\" title=\"Featured Product\" />"):"";
			$desc=$row["pr_desc"];
			$desc=str_replace("<p>","<p class=\"text-justify\" style=\"font-size:85%;\">",$desc);
			$cover=$row["pr_cover"];
			$cover="<img src=\"../upload/pr/".($cover?"$id/$cover":"cover.gif")."\" class=\"img-responsive\" />";

			$i++;
			if($i==4){
				$i=1;
				$pr_str.="<div class=\"clearfix\"></div>";
			}
			$pr_str.="<div class=\"col-md-4\"><div class=\"card article-item\"><a href=\"index.php?cmd=prview&id=$id\" class=\"article-link\"><div class=\"content article-desc text-center\"><h4 style=\"font-size:125%;font-weight:bold;\">$title$pop</h4><p>$cover</p>$desc<div class=\"btn btn-".$WEBSITE["theme_btn"]." btn-fill\">Download</div></div></a></div></div>";
		}
		$pr_str.="</div>";
	}

	return str_replace("%product%",$pr_str,$body);
}

function prfe_block($body,$pr){
	global $DB,$dbprefix;

	$pr_str="";
	$res=$DB->query("select * from $dbprefix"."pr where pr_id IN ($pr) order by pr_order");
	if(sizeof($res)){
		$pr_str.="<div class=\"ac\">";
		foreach($res as $row){
			$id=$row["pr_id"];
			$title=$row["pr_title"];
			$pop=$row["pr_pop"]?("<img src=\"./img/pop.png\" title=\"Featured Product\" class=\"help\" />"):"";
			$desc=$row["pr_desc"];
			$cover=$row["pr_cover"];
			$cover="<img src=\"./upload/pr/".($cover?"$id/$cover":"cover.gif")."\" class=\"wso-item-thumb\" />";

			$item="<p class=\"b\">$title$pop</p>$cover<div class=\"al\">$desc</div>";
			$pr_str.="<div class=\"wso-item\" style=\"margin:1px;\">$item</div>";
		}
		$pr_str.="</div>";
	}

	return str_replace("%product%",$pr_str,$body);
}

?>