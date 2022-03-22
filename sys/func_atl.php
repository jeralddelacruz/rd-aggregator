<?php
function fs_format($bytes){
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } elseif ($bytes == 1) {
        return '1 byte';
    } else {
        return '0 bytes';
    }
}

function get_loop($num,$rate,$audio){
	require_once("../getid3/getid3.php");
	$getID3=new getID3;
	$getid3_arr=$getID3->analyze($audio);
	$dura=$getid3_arr["playtime_seconds"];

	$loop=1;
	$durv=$num*$rate;
	if($dura&&($durv>$dura)){
		$loop=floor($durv/$dura)+1;
	}

	return $loop;
}

function vidsave($tmp,$name){
	ob_clean();
	header("Content-Type:video/mp4");
	header("Content-Disposition:attachment;filename=$name");
	header("Expires:0");
	header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
	header("Pragma:public");
	echo file_get_contents($tmp);
	exit;
}

function viddld($id){
	global $DB,$dbprefix,$UserID,$FFMPEGURL;

	if($cur_vid=$DB->info("vid","vid_id='$id' and user_id='$UserID'")){
		$res=$DB->query("select * from $dbprefix"."vcover where vid_id='$id' order by vcover_order,vcover_id desc");
		if(sizeof($res)){
			$dir="../vid/u".$UserID."-v".$id;

			$audio=$cur_vid["vid_audio"]?("@".realpath($dir."/".$cur_vid["vid_audio"])):"";

			$data=array();
			$data["data"]=serialize($cur_vid);

			$num=0;
			foreach($res as $row){
				$data["zip[$num]"]="@".realpath($dir."/".$row["vcover_file"]);
				$num++;
			}

			$data["audio"]=$audio;


			$ch=curl_init();
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_URL,$FFMPEGURL."/ffmpeg/");
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
			$content=curl_exec($ch);
			curl_close($ch);

			$fp=fopen($dir."/vid.tmp","wb");
			fwrite($fp,$content);
			fclose($fp);

			$DB->query("update $dbprefix"."vid set vid_rd='".time()."',vid_mod='0' where vid_id='$id'");
		}
	}
}

function vidslide($id){
	global $DB,$dbprefix,$UserID;

if($cur_vid=$DB->info("vid","vid_id='$id' and user_id='$UserID'")){
	$slug="u".$UserID."-v".$id;
	$dir="../vid/".$slug;

	$bg="bg.png";
	if($cur_vid["vid_bg"]){
		$im=new imageLib($dir."/".$cur_vid["vid_bg"]);
		$im->resizeImage(640,360);
		$im->saveImage($dir."/".$bg,50);
	}
	else{
		require_once("../aie/class.gradient.php");
		$im=new gd_gradient(640,360,"vertical",$cur_vid["vid_bgcolor"],$cur_vid["vid_bgcolor"],0);
		$im->save($dir."/".$bg);
	}

	$body_arr=preg_split("/\r\n###\d+\r\n/",$cur_vid["vid_body"]);
	$num=1;
	foreach($body_arr as $text){
		$text=htmlspecialchars_decode($text,ENT_QUOTES);
		img_text($dir,add_sign(3,$num),$text,"../aie/font/".$cur_vid["vid_font"].".ttf",$cur_vid["vid_size"],$cur_vid["vid_color"]);

		$wm=$dir."/".add_sign(3,$num).".png";
		$vtext_id=$DB->getauto("vtext");
		$DB->query("insert into $dbprefix"."vtext set vtext_id='$vtext_id',user_id='$UserID',vid_id='$id',vtext_val='".addslashes($text)."',vtext_font='".$cur_vid["vid_font"]."',vtext_size='".$cur_vid["vid_size"]."',vtext_color='".$cur_vid["vid_color"]."',vtext_align='center',vtext_dir='$slug',vtext_file='$wm'");

		$im=new imageLib($wm);
		$w=$im->widthOriginal;
		$h=$im->heightOriginal;
		$l=round((640-$w)/2);
		$t=round((360-$h)/2);

		$file="img".add_sign(3,$num).".png";
		$obj="{\"0\":{\"f\":\"".$wm."\",\"w\":".$w.",\"h\":".$h.",\"d\":0,\"l\":".$l.",\"t\":".$t.",\"le\":\"".$l."px\",\"te\":\"".$t."px\",\"text\":".$vtext_id."}}";


		$im=new imageLib($dir."/".$bg);
		$im->addWatermark($wm,"m",0,0);
		$im->resizeImage($im->widthOriginal,$im->heightOriginal);
		$im->saveImage($dir."/".$file,50);

		$DB->query("insert into $dbprefix"."vcover set user_id='$UserID',vid_id='$id',vcover_file='$file',vcover_dir='$slug',vcover_bg='".($dir."/".$bg)."',vcover_obj='$obj',vcover_order='$num',vcover_rd='".time()."'");

		$num++;
	}

	$file="img".add_sign(3,$num).".png";
	$obj="";

	$im=new imageLib($dir."/".$bg);
	$im->resizeImage($im->widthOriginal,$im->heightOriginal);
	$im->saveImage($dir."/".$file,50);

	$DB->query("insert into $dbprefix"."vcover set user_id='$UserID',vid_id='$id',vcover_file='$file',vcover_dir='$slug',vcover_bg='".($dir."/".$bg)."',vcover_obj='$obj',vcover_order='$num',vcover_rd='".time()."'");

	if($cur_vid["vid_audio"]){
		$loop=get_loop($num,$cur_vid["vid_rate"],$dir."/".$cur_vid["vid_audio"]);
		$DB->query("update $dbprefix"."vid set vid_loop='$loop' where vid_id='$id'");
	}

}

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

function add_sign($len,$num,$sign="0"){
	$str="";
	for($i=0;$i<$len-strlen($num);$i++){
		$str.=$sign;
	}
	$str.=$num;

	return $str;
}

function img_text($dir,$file,$text,$font,$size,$color,$align="center"){
	$file_n=$dir."/".$file.".png";

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
}

?>