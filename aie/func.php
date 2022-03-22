<?php
function scan_dir($dir,$extra="",$is_dir=0){
	$arr=array();

	$fp=opendir($dir);
	while($file=readdir($fp)){
		if(($file!=".")&&($file!="..")){
			if(is_dir("$dir/$file")){
				if($is_dir){
					$arr[]=$file;
				}
				else{
					continue;
				}
			}
			else{
				if(!$is_dir){
					$_arr=pathinfo($file);
					if(preg_match("/".$extra."$/",$_arr["filename"])){
						$arr[]=$file;
					}
				}
				else{
					continue;
				}
			}
		}
	}
	closedir($fp);

	sort($arr);

	return $arr;
}

function count_dir($dir,$extra=""){
	return sizeof(scan_dir($dir,$extra));
}

if(!function_exists("hex2dec")){
function hex2dec($color){
	$color=str_replace("#","",$color);

	$red=hexdec(substr($color,0,2));
	$green=hexdec(substr($color,2,2));
	$blue=hexdec(substr($color,4,2));

	return array($red,$green,$blue);
}
}

?>