<?php
set_time_limit(0);
error_reporting(0);
session_start();
$UserID=$_SESSION["UserID"];
$PackID=$_SESSION["PackID"];
$ECG_ARR=$_SESSION["ECG_ARR"];
$VidID=$_SESSION["VidID"];

require_once("class.gradient.php");
require_once("class.imagelib.php");
require_once("config.php");
require_once("func.php");
require_once("../sys/class.db.php");
require_once("../sys/config.php");

$func=$_POST["func"];
if($func=="load"){
	$error=0;
	$dir=$_POST["dir"]."/".$_POST["sub"];

	if(!$error){
		$dir_arr=json_decode(file_get_contents($aie_api_url."/?func=file&type=".$_POST["dir"]."&sub=".urlencode($_POST["sub"])));
		$str="";
		if(sizeof($dir_arr)){
			foreach($dir_arr as $f){
				$src=$aie_api_url."/upload/".$dir."/".str_replace("%","%25",$f);
				if($_POST["dir"]=="vbg"){
					$str.="<div class='aie-thumb'><img src='$src' class='aie-thumb-img' /></div>";
				}
				elseif($_POST["dir"]=="icon"){
					$str.="<div class='aie-icon'><img src='$src' class='aie-icon-img' /></div>";
				}
			}
		}
		$res="success|".$str;
	}
	else{
		$res="No Folder found.";
	}

	echo $res;
}
elseif($func=="loadp"){
	$error=1;
	$src=trim($_POST["src"]);
	$q=trim($_POST["q"]);
	if($q){
		$error=0;
	}

	if(!$error){
		$DB=new db($dbhost,$dbuser,$dbpass,$dbname);
		$DB->connect();
		if($DB->connect==1){
			$res0=$DB->query("select setup_val from $dbprefix"."setup where setup_key='$src'");
			$key=$res0[0]["setup_val"];
		}

		$res_arr=array();
		if($src=="pix"){
			$_arr=json_decode(file_get_contents("http://pixabay.com/api/?key=$key&per_page=200&q=".urlencode($q)),true);
			$arr=$_arr["hits"];

			if(sizeof($arr)){
				foreach($arr as $img){
					$res_arr[]=array("src"=>$img["previewURL"],"url"=>$img["webformatURL"]);
				}
			}
		}
		elseif($src=="fr"){
			$_arr=json_decode(file_get_contents("https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=$key&per_page=200&format=json&nojsoncallback=1&tags=".urlencode($q)),true);
			$arr=$_arr["photos"]["photo"];

			if(sizeof($arr)){
				foreach($arr as $img){
					$farm_id=$img["farm"];
					$server_id=$img["server"];
					$photo_id=$img["id"];
					$secret_id=$img["secret"];
					$size_m="m";
					$size_l="c";
					$src="http://farm".$farm_id.".staticflickr.com/".$server_id."/".$photo_id."_".$secret_id."_".$size_m."."."jpg";
					$url="http://farm".$farm_id.".staticflickr.com/".$server_id."/".$photo_id."_".$secret_id."_".$size_l."."."jpg";

					$res_arr[]=array("src"=>$src,"url"=>$url);
				}
			}
		}
		elseif($src=="oc"){
			$_arr=json_decode(file_get_contents("https://openclipart.org/search/json/?amount=200&query=".urlencode($q)),true);
			$arr=$_arr["payload"];

			if(sizeof($arr)){
				foreach($arr as $img){
					$res_arr[]=array("src"=>$img["svg"]["png_thumb"],"url"=>$img["svg"]["png_full_lossy"]);
				}
			}
		}
		elseif($src=="if"){
			$_arr=json_decode(file_get_contents("https://api.iconfinder.com/v2/icons/search?count=100&premium=0&vector=0&query=".urlencode($q)),true);
			$arr=$_arr["icons"];

			if(sizeof($arr)){
				foreach($arr as $img){
					$num=sizeof($img["raster_sizes"])-1;
					$src=$img["raster_sizes"][$num]["formats"][0]["preview_url"];
					$url=$src;
					$res_arr[]=array("src"=>$src,"url"=>$url);
				}
			}
		}

		$str="";
		if(sizeof($res_arr)){
			foreach($res_arr as $su){
				$src=$su["src"];
				$url=$su["url"];
				$str.="<div class='aie-pix'><img src='$src' url='$url' class='aie-pix-img' /></div>";
			}
		}
		$res="success|".$str;
	}
	else{
		$res="No Keyword(s) entered.";
	}

	echo $res;
}
elseif($func=="loadu"){
	$error=0;
	$type=$_POST["dir"];
	$dir="upload/".$UserID."/".$type;

	if(!$error){
		$dir_arr=scan_dir($aie_dir."/".$dir,(($type=="bg")?"_s":""));
		$num=sizeof($dir_arr);
		$max=$ECG_ARR[$type];

		$str="";
		if(sizeof($dir_arr)){
			foreach($dir_arr as $f){
				$src=$aie_dir."/".$dir."/".str_replace("%","%25",$f);
				if($type=="bg"){
					$str.="<div class='aie-thumb'><img src='$src' class='aie-thumb-img' /><br /><input type='checkbox' value='$f' /></div>";
				}
				elseif($type=="icon"){
					$str.="<div class='aie-icon'><img src='$src' class='aie-icon-img' /><br /><input type='checkbox' value='$f' /></div>";
				}
			}
			$str.="<div class='ac'><input type='button' id='".$type."_del_btn' value='Remove Selected' class='button' /></div>";
		}

		$res="success|".$str."|".(int)$num."|".(int)$max;
	}
	else{
		$res="No Folder found.";
	}

	echo $res;
}
elseif($func=="loadpr"){
	$error=0;

	if(!$error){
		$DB=new db($dbhost,$dbuser,$dbpass,$dbname);
		$DB->connect();
		if($DB->connect==1){
			$res=$DB->query("select * from $dbprefix"."bg where bg_pack like '%;$PackID;%' order by bg_id desc");
		}

		$str="";
		if(sizeof($res)){
			foreach($res as $row){
				$src=$aie_dir."/upload/bg/".$row["bg_id"]."_prev_s.".$row["bg_prev"];
				$str.="<div class='aie-thumb'><img src='$src' class='aie-thumb-img' type='premium' /><br /><a href='".str_replace("_s","",$src)."' class='fb tip' title='Zoom In' rel='prev'><img src='$aie_dir/img/view.png' /></a></div>";
			}
		}

		$res="success|".$str;
	}
	else{
		$res="No Folder found.";
	}

	echo $res;
}
elseif($func=="del"){
	$error=0;
	$type=$_POST["dir"];
	$dir=$aie_dir."/upload/".$UserID."/".$type;
	$arr=explode(",",$_POST["str"]);

	if(!is_dir($dir)){
		$error=1;
	}

	if(!$error){
		if(sizeof($arr)){
			foreach($arr as $f){
				$src=$dir."/".$f;
				unlink($src);
				if($type=="bg"){
					unlink(str_replace("_s","",$src));
				}
			}
		}
		$res="success";
	}
	else{
		$res="No Folder found.";
	}

	echo $res;
}
elseif($func=="grad"){
	$error=1;
	$s=$_POST["s"];
	$e=$_POST["e"];
	if($s&&$e){
		$error=0;
	}

	if(!$error){
		$arr=array("vertical","horizontal","rectangle","ellipse","ellipse2","circle","circle2","diamond");
		foreach($arr as $d){
			$src=$aie_dir."/grad.php?w=110&h=62&d=".$d."&s=".$s."&e=".$e;
			$str.="<div class='aie-grad'><img src='$src' title='Choose' class='aie-grad-img' direction='$d' /></div>";
			if($s==$e){break;}
		}
		$res="success|".$str;
	}
	else{
		$res="No Colors specified.";
	}

	echo $res;
}
/*
elseif($func=="init"){
	$error=1;
	$bg_s=$_POST["bg"];
	if($bg_s=="grad"){
		$d=$_POST["d"];
		$s=$_POST["s"];
		$e=$_POST["e"];
		if($d&&$s&&$e){
			$error=0;
		}
	}
	else{
		$_arr=pathinfo($bg_s);
		$bg=$_arr["dirname"]."/".substr($_arr["filename"],0,-2).".".$_arr["extension"];
		if($_POST["type"]=="premium"){
			$DB=new db($dbhost,$dbuser,$dbpass,$dbname);
			$DB->connect();
			if($DB->connect==1){
				$id=substr($_arr["filename"],0,-4);
				if($row=$DB->info("bg","bg_id='$id'")){
					$prev=str_replace("..",$SCRIPTURL,$bg);
					$prev_s=str_replace(".".$row["bg_prev"],"_s.".$row["bg_prev"],$prev);
					$bg=str_replace("prev","empty",$bg);
					$bg=str_replace($row["bg_prev"],$row["bg_empty"],$bg);
					$_arr["extension"]=$row["bg_empty"];
				}
			}
		}
		$error=0;
	}

	if(!$error){
		$key=$UserID."-".date("YmdHis");
		$dir="./tmp/".$key;
		@mkdir($dir,0777);
		@chmod($dir,0777);

		if($bg_s=="grad"){
			$file=date("YmdHis").".png";
			$im=new gd_gradient(640,360,$d,$s,$e,0);
			$im->save($dir."/".$file);
		}
		else{
			$file=date("YmdHis").".".$_arr["extension"];
			if(preg_match("/^http/i",$bg)){
				$bg=str_replace(" ","%20",$bg);
			}
			copy($bg,$dir."/".$file);
		}

		$arr=getimagesize($dir."/".$file);
		$res="success|".$key."|".$aie_dir."/tmp/".$key."/".$file."|".$arr[0]."|".$arr[1]."|".$prev."|".$prev_s;
	}
	else{
		$res="No Template found.";
	}

	echo $res;
}
*/
elseif($func=="cngbg"){
	$error=1;
	$dir=$atl_dir."/".$_POST["key"];
	$bg_s=$_POST["bg"];
	if($bg_s=="grad"){
		$d=$_POST["d"];
		$s=$_POST["s"];
		$e=$_POST["e"];
		if($d&&$s&&$e){
			$error=0;
		}
	}
	else{
		$_arr=pathinfo($bg_s);
		$bg=$_arr["dirname"]."/".substr($_arr["filename"],0,-2).".".$_arr["extension"];
		$error=0;
	}

	if(!$error){
		$file="bg-".date("YmdHis").".png";
		if($bg_s=="grad"){
			$im=new gd_gradient(640,360,$d,$s,$e,0);
			$im->save($dir."/".$file);
		}
		else{
			if(preg_match("/^http/i",$bg)){
				$bg=str_replace(" ","%20",$bg);
			}
			$tmp="tmp-".date("YmdHis").".".$_arr["extension"];
			copy($bg,$dir."/".$tmp);
			$im=new imageLib($dir."/".$tmp);
			$im->resizeImage(640,360);
			$im->saveImage($dir."/".$file,50);
			unlink($dir."/".$tmp);
		}

//		$arr=getimagesize($dir."/".$file);
		$res="success|".$dir."/".$file;
	}
	else{
		$res="No Template found.";
	}

	echo $res;
}
elseif($func=="text"){
	$error=1;
	$text=$_POST["text"];
	$font=$_POST["font"];
	$size=$_POST["size"];
	$color=$_POST["color"];
	$align=$_POST["align"];

	$dir=$atl_dir."/".$_POST["key"];
	$font=$aie_dir."/font/".$font.".ttf";
	if(is_dir($dir)&&is_file($font)){
		$error=0;
	}

	if(!$error){
		$file_n=$dir."/".date("YmdHis").".png";

		$bbox=imagettfbbox($size,0,$font,$text);
		$width=$bbox[2]-$bbox[6]+10;
		$height=$bbox[3]-$bbox[7]+5;

		$im=imagecreatetruecolor($width,$height);

		imagealphablending($im,false);
		imagesavealpha($im,true);
		$col=imagecolorallocatealpha($im,255,255,255,127);
		imagefill($im,0,0,$col);

		$arr=hex2dec($color);
		$col=imagecolorallocate($im,$arr[0],$arr[1],$arr[2]);

		$arr=explode("\n",$text);
		$num=sizeof($arr);
		if(($align=="left")||($num==1)){
			imagettftext($im,$size,0,-$bbox[6],-$bbox[7]+2,$col,$font,$text);
		}
		else{
			$hstep=0;
			$hsize=0;
			$hs=0;
			foreach($arr as $str){
				$lbox=imagettfbbox($size,0,$font,$str);
				if($hsize==0){
					$hsize=($lbox[3]-$lbox[7]);
					$hs=($height-5-$hsize*$num)/($num-1);
				}
				if($align=="center"){
					$left_x=round(($width-($lbox[2]-$lbox[0]))/2);
				}
				else{
					$left_x=round($width-($lbox[2]-$lbox[0]));
				}

				imagettftext($im,$size,0,-$bbox[6]+$left_x,-$bbox[7]+$hstep+2,$col,$font,$str);
				$hstep+=$hsize+$hs;
			}
		}

		imagepng($im,$file_n);
		imagedestroy($im);

		$DB=new db($dbhost,$dbuser,$dbpass,$dbname);
		$DB->connect();
		if($DB->connect==1){
			$id=$DB->getauto("vtext");
			$DB->query("insert into $dbprefix"."vtext set vtext_id='$id',user_id='$UserID',vid_id='$VidID',vtext_val='".addslashes($text)."',vtext_font='".addslashes($_POST["font"])."',vtext_size='$size',vtext_color='$color',vtext_align='$align',vtext_dir='".addslashes($_POST["key"])."',vtext_file='$file_n'");
		}

		$res="success|".$file_n."|".$width."|".$height."|".$id;
	}
	else{
		$res="No Font found.";
	}

	echo $res;
}
elseif($func=="pix"){
	$error=1;
	$dir=$atl_dir."/".$_POST["key"];
	$url=$_POST["url"];
	$_arr=pathinfo($url);
	$file_n=$dir."/".date("YmdHis").".".$_arr["extension"];

	if(is_dir($dir)&&copy($url,$file_n)){
		$error=0;
	}

	if(!$error){
		$arr=getimagesize($file_n);
		$res="success|".$file_n."|".$arr[0]."|".$arr[1];
	}
	else{
		$res="Failed to copy the Image.";
	}

	echo $res;
}
elseif($func=="icon"){
	$error=1;
	$dir=$atl_dir."/".$_POST["key"];
	$file=$_POST["file"];
	if(is_dir($dir)){
		$error=0;
	}

	if(!$error){
		$_arr=pathinfo($file);
		$file_n=$dir."/".date("YmdHis").".".$_arr["extension"];
		if(preg_match("/^http/i",$file)){
			$file=str_replace(" ","%20",$file);
		}
		copy($file,$file_n);

		$arr=getimagesize($file_n);
		$res="success|".$file_n."|".$arr[0]."|".$arr[1];
	}
	else{
		$res="No Image found.";
	}

	echo $res;
}
elseif($func=="ef"){
	$error=1;
	$dir=$atl_dir."/".$_POST["key"];
	$file=$_POST["file"];
	if(is_dir($dir)&&is_file($file)){
		$error=0;
	}

	if(!$error){
		$_arr=pathinfo($file);
		$file_n=$dir."/".date("YmdHis").".".$_arr["extension"];

		$im=new imageLib($file);
		$im->{strtolower(str_replace("img","",$_POST["ef"]))}($_POST["arg"]);
		$im->saveImage($file_n,50);

		$res="success|".$_POST["item"]."|".$file_n;
	}
	else{
		$res="No Object found.";
	}

	echo $res;
}
elseif($func=="crop"){
	$error=1;
	$dir=$atl_dir."/".$_POST["key"];
	$file=$_POST["file"];
	$item=$_POST["item"];
	if(is_dir($dir)&&is_file($file)){
		$error=0;
	}

	if(!$error){
		$_arr=pathinfo($file);
		$file_n=$dir."/".date("YmdHis").".".$_arr["extension"];

		if($item!="bg"){
			$im=new imageLib($file);
			$im->resizeImage($_POST["rw"],$_POST["rh"]);
			$im->saveImage($file_n,50);
			$file=$file_n;
		}

		$im=new imageLib($file);
		$im->cropImage($_POST["w"],$_POST["h"],$_POST["x"]."x".$_POST["y"]);
		$im->saveImage($file_n,50);

		$res="success|$item|".$file_n;
	}
	else{
		$res="No Object found.";
	}

	echo $res;
}
elseif($func=="save"){
	$error=1;
	$id=$_POST["id"];
	$dir=$atl_dir."/".$_POST["key"];
	$file=$_POST["file"];
	if(is_dir($dir)&&is_file($file)){
		$error=0;
	}

	if(!$error){
		$_arr=json_decode($_POST["str"],true);
		if(sizeof($_arr)){
			$wm_arr=array();
			foreach($_arr as $i=>$arr){
				if(is_file($arr["f"])){
					$p_arr=pathinfo($arr["f"]);
					$file_n=$dir."/img-".$i.".png";
					$wm_arr[]=array($file_n,$arr["l"],$arr["t"]);

					$im=new imageLib($arr["f"]);
					$im->resizeImage($arr["w"],$arr["h"]);
					$deg=(int)$arr["d"];
					if($deg){
						$im->rotate($deg);
					}
					$im->saveImage($file_n,50);
				}
			}
		}

		$im=new imageLib($file);
		if(sizeof($wm_arr)){
			foreach($wm_arr as $arr){
				$im->addWatermark($arr[0],$arr[1]."x".$arr[2],0,0);
			}
		}
		$file_n=$dir."/Slide-".date("YmdHis").".png";
		$im->resizeImage($im->widthOriginal,$im->heightOriginal);
		$im->saveImage($file_n,50);

		$file_c=str_replace($dir."/","",$file_n);

		$DB=new db($dbhost,$dbuser,$dbpass,$dbname);
		$DB->connect();
		if($DB->connect==1){
			if(!$id){
				$DB->query("insert into $dbprefix"."vcover set user_id='$UserID',vid_id='$VidID',vcover_file='$file_c',vcover_dir='".addslashes($_POST["key"])."',vcover_bg='".addslashes($_POST["file"])."',vcover_obj='".addslashes($_POST["str"])."',vcover_rd='".time()."'");
			}
			else{
				$DB->query("update $dbprefix"."vcover set vcover_file='$file_c',vcover_dir='".addslashes($_POST["key"])."',vcover_bg='".addslashes($_POST["file"])."',vcover_obj='".addslashes($_POST["str"])."' where vcover_id='$id'");
			}
			$DB->query("update $dbprefix"."vid set vid_mod='1' where vid_id='$VidID'");
		}

		$res="success|index.php?cmd=videdit&id=".$VidID;
	}
	else{
		$res="No Template found.";
	}

	echo $res;
}
elseif($func=="edit"){
	$error=1;
	$id=$_POST["id"];

	$DB=new db($dbhost,$dbuser,$dbpass,$dbname);
	$DB->connect();
	if($DB->connect==1){
		if($cur_cover=$DB->info("vcover","vcover_id='$id' and user_id='$UserID'")){
			$error=0;
		}
	}

	if(!$error){
		$arr=getimagesize($cur_cover["vcover_bg"]);
		$res="success|".$cur_cover["vcover_dir"]."|".$cur_cover["vcover_bg"]."|".$arr[0]."|".$arr[1]."|".$cur_cover["vcover_obj"];
	}
	else{
		$res="No Graphics found.";
	}

	echo $res;
}
elseif($func=="textedit"){
	$error=1;
	$id=$_POST["id"];
	$key=$_POST["key"];
	$dir=$atl_dir."/".$key;
	$file=$_POST["file"];
	if(is_dir($dir)&&is_file($file)){
		$error=0;
	}

	$DB=new db($dbhost,$dbuser,$dbpass,$dbname);
	$DB->connect();
	if($DB->connect==1){
		if(($cur_text=$DB->info("vtext","vtext_id='$id' and user_id='$UserID' and vtext_dir='$key' and vtext_file='$file'"))&&is_dir($dir)&&is_file($file)){
			$error=0;
		}
	}

	if(!$error){
		$res="success|".$cur_text["vtext_val"]."|".$cur_text["vtext_font"]."|".$cur_text["vtext_size"]."|".$cur_text["vtext_color"]."|".$cur_text["vtext_align"];
	}
	else{
		$res="No Text found.";
	}

	echo $res;
}
elseif($func=="cancel"){
	if($VidID){
		$res="success|index.php?cmd=videdit&id=".$VidID;
	}
	else{
		$res="No Video found.";
	}

	echo $res;
}
else{
	echo "No function found.";
}

?>