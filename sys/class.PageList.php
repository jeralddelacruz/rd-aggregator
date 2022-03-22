<?php
class PageList{

var $rows;
var $pp;
var $pages;
var $page;
var $url;
var $items=15;

function PageList(&$query,$DB,&$page,$pp,$url){
	$res=$DB->query($query);
	$rows=sizeof($res);
	$this->rows=$rows;

	$pp=(int)$pp;
	if($pp<1){$pp=1;}
	$this->pp=$pp;

	$pages=floor($rows/$pp);
	if($rows%$pp){$pages++;}
	$this->pages=$pages;

	$page=(int)$page;
	if($page<1){$page=1;}
	elseif($page>$pages){$page=$pages;}
	$this->page=$page;

	$query.=$pages?(" limit ".($pp*($page-1)).",".$pp):"";

	$this->url=$url;
}

function pages(){
	$pages=$this->pages;
	$page=$this->page;
	$items=$this->items;
	$url=$this->url;

	$list="";
	if($pages>1){
		$list.=($page-1)?"<a href=\"$url&p=".($page-1)."\" title=\"Previous Page\" class=\"tip\">&laquo;</a>&nbsp;&nbsp;&nbsp;":"";

		if($items>=$pages){
			$from=1;
			$to=$pages;
		}
		else{
			$mid=round($items/2);
			$from=$page-$mid+1;
			$to=$items-1+$from;
			if($from<1){
				$from=1;
				$to=$items;
			}
			elseif($to>$pages){
				$to=$pages;
				$from=$pages-$items+1;
			}
		}
		for($i=$from;$i<=$to;$i++){
			$list.=($i==$page)?"<strong>$i</strong>&nbsp;&nbsp;&nbsp;":"<a href=\"$url&p=$i\" title=\"Page $i\" class=\"tip\">$i</a>&nbsp;&nbsp;&nbsp;";
		}

		$list.=(($page+1)>$pages)?"":"<a href=\"$url&p=".($page+1)."\" title=\"Next Page\" class=\"tip\">&raquo;</a>";
	}

	return $list;
}

function total(){
	return $this->rows;
}

function display(){
	$rows=$this->rows;
	$pp=$this->pp;
	$page=$this->page;
	
	return $rows?($pp*($page-1)+1)." &ndash; ".($pp*$page<$rows?$pp*$page:$rows):$rows;
}

}
?>