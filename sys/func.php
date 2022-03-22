<?php
	// GENERAL FUNCTIONS
	function strip($str, $tag = 1){
		if($tag){
			$str = htmlspecialchars(strip_tags($str), ENT_QUOTES);
		}

		$str = trim($str);

		if(!get_magic_quotes_gpc()){
			$str = addslashes($str);
		}

		return $str;
	}

	function slash($str){
		$str = stripslashes($str);
		return $str;
	}

	function rand_str($len){
		$str = "";

		for($i = 1; $i <= $len; $i++){
			$ord = rand(65,90);
			$lower = rand(0,1);
			$str .= $lower ? strtolower(chr($ord)) : chr($ord);
		}

		return $str;
	}

	function alphanum($str){
		return preg_replace("/[^a-zA-Z0-9_-]/", "", $str);
	}

	function redirect($url){
		header("Location:" . $url);

		exit("Redirected");
	}

	function is_email($email){
		if(!preg_match("/^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int|info)\$/i", $email)){
			return false;
		}

		return true;
	}

	function is_nick($nick){
		if(!preg_match("/^[a-z0-9]{3,25}$/i", $nick)){
			return false;
		}

		return true;
	}

	function user_mkdir($id){
		if(!is_dir("../upload/$id")){
			mkdir("../upload/$id",0775);
			chmod("../upload/$id",0775);
		}

		if(!is_dir("../upload/$id/fm")){
			mkdir("../upload/$id/fm",0775);
			chmod("../upload/$id/fm",0775);
			mkdir("../upload/$id/fm/src",0775);
			chmod("../upload/$id/fm/src",0775);
			mkdir("../upload/$id/fm/thumb",0775);
			chmod("../upload/$id/fm/thumb",0775);
		}
		
		if(!is_dir("../upload/$id/news")){
			mkdir("../upload/$id/news", 0775);
			chmod("../upload/$id/news", 0775);
			mkdir("../upload/$id/news/avatar", 0775);
			chmod("../upload/$id/news/avatar", 0775);
			mkdir("../upload/$id/news/images", 0775);
			chmod("../upload/$id/news/images", 0775);
		}

		return;
	}

	function sendmail($id,$arr){
		global $DB,$dbprefix;

		$res = $DB->query("SELECT * FROM {$dbprefix}email WHERE email_id = '{$id}'");
		if($row = $res[0]){
			$from = html_entity_decode($row["email_from"], ENT_QUOTES);
			$reply = html_entity_decode($row["email_reply"], ENT_QUOTES);
			$to = html_entity_decode($row["email_to"], ENT_QUOTES);
			$subject = html_entity_decode($row["email_subject"], ENT_QUOTES);
			$body = str_replace("\r\n","\n",$row["email_body"]);
			
			foreach($arr as $k=>$v){
				if ($k == 'siteurl') {
					$v = str_replace('.', '(dot)', $v);
				}

				$from = str_replace("%" . strtoupper($k) . "%" , $v, $from);
				$reply = str_replace("%" . strtoupper($k) . "%", $v, $reply);
				$to = str_replace("%" . strtoupper($k) . "%", $v, $to);
				$subject=str_replace("%" . strtoupper($k) . "%", $v, $subject);
				$body=str_replace("%" . strtoupper($k) . "%", $v, $body);
			}

			$headers = "From: $from\r\nReply-To: $reply";

			$body = html_entity_decode($body, ENT_QUOTES);

			@mail($to, $subject, $body, $headers);
		}

		return;
	}

	function sendgr($fname,$lname,$email){
		global $WEBSITE;

		$api_key = $WEBSITE["gr_key"];
		$api_url = "http://api2.getresponse.com";

		$client = new jsonRPCClient($api_url);

		try{
			$campaigns=$client->get_campaigns(
				$api_key, 
				array(
					"name"=>array("EQUALS"=>$WEBSITE["gr_name"])
				)
			);

			$CAMPAIGN_ID = array_pop(array_keys($campaigns));
		}catch(Exception $e){}

		if($CAMPAIGN_ID){
			try{
				$client->add_contact(
					$api_key, 
					array(
						"campaign"=>$CAMPAIGN_ID,
						"name"=>$fname." ".$lname,
						"email"=>$email
					)
				);
			}catch (Exception $e){}
		}
	}

	function sendsendiio($email){
		global $WEBSITE;

		$api_key = $WEBSITE["sendiio_api_key"];
		$api_secret = $WEBSITE["sendiio_api_secret"];
		$list_id = $WEBSITE["sendiio_list_id"];

		$data = [
			"email_list_id" => $list_id,
			"email" => $email
		];

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://sendiio.com/api/v1/lists/subscribe/json',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => json_encode($data),
		  CURLOPT_HTTPHEADER => array(
			'token: ' . $api_key,
			'secret: ' . $api_secret,
			'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
	}

	function rem_dir($dir){
		$handle = @opendir("$dir");
		while($file = @readdir($handle)){
			if(($file != ".") && ($file != "..")){
				if(is_dir("$dir/$file")){
					rem_dir("$dir/$file");
				}
				else{
					@unlink("$dir/$file");
				}
			}
		}

		@closedir($handle);
		@rmdir("$dir");
	}

	function clear_tmp($dir){
		$handle = @opendir("$dir");
		while($file = @readdir($handle)){
			if(($file != ".") && ($file != "..")){
				if(is_dir("$dir/$file")){
					clear_tmp("$dir/$file");
				}
				else{
					if(filemtime("$dir/$file") < (time() - 86400)){
						@unlink("$dir/$file");
					}
				}
			}
		}

		@closedir($handle);
	}

	function htmltostr($html){
		$arr = explode("\n", $html);

		$str = "";
		if(sizeof($arr)){
			foreach($arr as $line){
				$str .= trim($line);
			}
		}

		return $str;
	}

	function get_wh($w, $h, $max_w, $max_h){
		$new_w = $w;
		$new_h = $h;

		if($w > $max_w){
			$new_w = $max_w;
			$new_h = round(($max_w / $w) * $h);
		}

		if($new_h > $max_h){
			$new_w = round(($max_h / $new_h) * $new_w);
			$new_h = $max_h;
		}

		return array($new_w, $new_h);
	}

	function logo_wh($src, $max_w, $max_h){
		$wh = "";

		$im = getimagesize($src);
		if(($im[0] > $max_w) || ($im[1] > $max_h)){
			$arr = get_wh($im[0], $im[1], $max_w, $max_h);
			$wh = " width=\"".$arr[0]."\" height=\"" . $arr[1] . "\"";
		}

		return $wh;
	}

	function add_dash($title, $level){
		$dash = "";
		for($i = 0; $i < $level; $i++){
			$dash .= "&mdash;&nbsp;";
		}

		return $dash . $title;
	}

	// PASSWORD HASH AND ENCRYPTIONS
	function mc_encrypt($post_variable_password, $key){
		// $encrypt = serialize($encrypt);
		// $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
		// $key = pack('H*', $key);
		// $mac = hash_hmac('sha256', $encrypt, substr(bin2hex($key), -32));
		// $passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
		// $encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
		// return $encoded;

		// REPLACED THE MCRYPT BECAUSE OF DEPRECATION
		$hashed_password = password_hash($post_variable_password, PASSWORD_DEFAULT);

		return $hashed_password;
	}

	function mc_decrypt($post_variable_password, $fetched_password_from_db){
		// $decrypt = explode('|', $decrypt.'|');
		// $decoded = base64_decode($decrypt[0]);
		// $iv = base64_decode($decrypt[1]);
		// if(strlen($iv)!==mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)){ return false; }
		// $key = pack('H*', $key);
		// $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
		// $mac = substr($decrypted, -64);
		// $decrypted = substr($decrypted, 0, -64);
		// $calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
		// if($calcmac!==$mac){ return false; }
		// $decrypted = unserialize($decrypted);
		// return $decrypted;

		// REPLACED THE MCRYPT BECAUSE OF DEPRECATION
		$password_verification = password_verify($post_variable_password, $fetched_password_from_db);

		if($password_verification){
			return $post_variable_password;
		}
	}

	// ECG FUNCTIONS
	function dir_scan($dir, $extra = "", $is_dir = 0){
		$arr = array();

		$fp = opendir($dir);
		while($file = readdir($fp)){
			if(($file != ".") && ($file != "..")){
				if(is_dir("$dir/$file")){
					if($is_dir){
						$arr[] = $file;
					}
					else{
						continue;
					}
				}
				else{
					if(!$is_dir){
						$_arr = pathinfo($file);
						if(preg_match("/" . $extra . "$/", $_arr["filename"])){
							$arr[] = $file;
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

	function dir_count($dir, $extra = ""){
		return count(dir_scan($dir, $extra));
	}

	function get_exptime($rd){
		$rd_date = date("Y-n-j-H-i-s", $rd);
		list($rd_y, $rd_m, $rd_d, $rd_h, $rd_i, $rd_s) = explode("-", $rd_date);

		$date = date("Y-n-j-H-i-s");
		list($y, $m, $d, $h, $i, $s) = explode("-", $date);

		if(($rd_y == $y) && ($rd_m == $m)){
			$from = $rd;
			$to = get_addMonth($rd, 1);
		}
		else{
			if(strtotime("$y-$m-$rd_d $rd_h:$rd_i:$rd_s") > strtotime("$y-$m-$d $h:$i:$s")){
				$from = get_addMonth(strtotime("$y-$m-$rd_d $rd_h:$rd_i:$rd_s"), -1);
				$to = strtotime("$y-$m-$rd_d $rd_h:$rd_i:$rd_s");
			}
			else{
				$from = strtotime("$y-$m-$rd_d $rd_h:$rd_i:$rd_s");
				$to = get_addMonth(strtotime("$y-$m-$rd_d $rd_h:$rd_i:$rd_s"), 1);
				
			}
		}

		return array($from, $to);
	}

	function get_addMonth($time, $num = 1){
		$date = date("Y-n-j-H-i-s",$time);
		list($y, $m, $d, $h, $i, $s) = explode("-", $date);

		$m+=$num;
		while($m > 12){
			$m-=12;
			$y++;
		}

		$last_day = date("t", strtotime("$y-$m-1"));
		if($d > $last_day){
			$d = $last_day;
		}

		return strtotime("$y-$m-$d $h:$i:$s");
	}

	function life_dld($file){
		$arr = pathinfo($file);

		ob_clean();
		header("Content-Disposition:attachment;filename=" . $arr["basename"]);
		header("Content-Type:application/force-download");
		header("Content-Length:" . filesize($file));
		header("Expires:0");
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
		header("Pragma:public");
		echo file_get_contents($file);

		exit("Exited");
	}

	// PARSE FUNCTION
	function make_pat($str){
		$arr = array("/", ".", "?", "$", "(", ")");

		foreach($arr as $val){
			$str = str_replace($val, "\\" . $val, $str);
		}

		return $str;
	}

	// LG FUNCTIONS
	function lgdld($id){
		global $DB, $dbprefix, $UserID, $WEBSITE, $LG_TYPE_ARR, $LG_RIGHT_ARR, $LG_CRIGHT_ARR, $lgbg_dir, $bg_lib;

		if($cur_lg = $DB->info("lg","lg_id='$id' and user_id='$UserID'")){
			$title = $cur_lg["lg_title"];
			$type = $cur_lg["lg_type"];
			$typeval = ($cur_lg["lg_type"] == 100) ? $cur_lg["lg_typeval"] : $LG_TYPE_ARR[$cur_lg["lg_type"]];
			$right_arr = explode(";", $cur_lg["lg_right"]);
			$extra_arr = unserialize($cur_lg["lg_extra"]);
			$cright_arr = explode(";", $cur_lg["lg_cright"]);
			$cextra_arr = unserialize($cur_lg["lg_cextra"]);

			$val_arr = array("bg", "htype", "halign", "htext", "himg", "hurl", "ftype", "falign", "ftext", "fimg", "furl");
			foreach($val_arr as $val){
				${$val} = $cur_lg["lg_" . $val];
			}

			// $content="";
			// foreach($LG_RIGHT_ARR as $k=>$v){
				// $right=in_array($k,$right_arr)?1:0;
				// $extra=$extra_arr[$k]?1:0;

				// $content.="[".($right?"YES":"NO")."] ".$v.($extra?(" (".$extra_arr[$k].")"):"")."<br />";
			// }

			$yes_str = "";
			$no_str = "";
			foreach($LG_RIGHT_ARR as $k=>$v){
				$right = in_array($k, $right_arr) ? 1 : 0;
				$extra = $extra_arr[$k] ? 1 : 0;

				if($right){
					$yes_str .= "[YES] " . $v . ($extra ? (" (" . $extra_arr[$k] . ")") : "") . "<br />";
				}
				else{
					$no_str .= "[NO] " . $v . ($extra ? (" (" . $extra_arr[$k] . ")") : "") . "<br />";
				}
			}
			foreach($LG_CRIGHT_ARR as $k=>$v){
				$right = in_array($k,$cright_arr) ? 1 : 0;
				$extra = $cextra_arr[$k] ? 1 : 0;

				if($right){
					$yes_str .= "[YES] " . $v . ($extra ? (" (" . $cextra_arr[$k] . ")") : "") . "<br />";
				}
				else{
					$no_str .= "[NO] " . $v . ($extra ? (" (" . $cextra_arr[$k] . ")") : "") . "<br />";
				}
			}
			$content = $yes_str . "<br />" . $no_str;

			$back = "";
			if($bg){
				$back = " style=\"background:url(" . ($bg_lib ? str_replace($lgbg_dir . "/","",$bg) : $bg) . ") no-repeat;background-size:794px 1122px;\"";
			}

			$header = "";
			if($htype){
				$header .= ($hurl && ($htype == "text")) ? "<a href=\"$hurl\">" : "";
				$header .= ($htype == "text") ? $htext : "<img src=\"$himg\" />";
				$header .= ($hurl && ($htype == "text")) ? "</a>" : "";
			}
			$footer = "";
			if($ftype){
				$footer .= ($furl && ($ftype == "text")) ? "<a href=\"$furl\">" : "";
				$footer .= ($ftype == "text") ? $ftext : "<img src=\"$fimg\" />";
				$footer .= ($furl && ($ftype == "text")) ? "</a>" : "";
			}

			$html = file_get_contents("../tpl/tpl_lg" . ($bg_lib?"bg" : "") . ".html");
			$html = str_replace("%back%", $back, $html);
			$html = str_replace("%halign%", $halign,$html);
			$html = str_replace("%header%", $header,$html);
			$html = str_replace("%title%", $title,$html);
			$html = str_replace("%type%", $typeval,$html);
			$html = str_replace("%content%", $content,$html);
			$html = str_replace("%falign%", $falign,$html);
			$html = str_replace("%footer%", $footer,$html);

			$file = "../lg/" . $UserID . ".zip";

			$zip = new ZipArchive();
			$zip->open($file, ZIPARCHIVE::CREATE);
			$zip->addFromString("lg.html", $html);
			if($bg){
				if($bg_lib){
					$zip->addFile($bg, str_replace($lgbg_dir . "/", "", $bg));
				}
				else{
					$zip->addFile("../lg/$bg", $bg);
				}
			}
			if(($htype == "img") && $himg){
				$zip->addFile("../lg/$himg", $himg);
			}
			if(($ftype == "img") && $fimg){
				$zip->addFile("../lg/$fimg", $fimg);
			}
			$zip->close();

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $WEBSITE["cdb_url"] . "/api/");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array(
				"type"=>"lg_cms", 
				"name"=>"lg", "zip"=>"@" . realpath($file)
			));

			$content = curl_exec($ch);
			curl_close($ch);

			$fp = fopen("../lg/u" . $UserID . "-l" . $id . ".tmp", "wb");
			fwrite($fp, $content);
			fclose($fp);

			unlink($file);
		}
	}

	function lgsave($tmp, $name){
		ob_clean();
		header("Content-Type:application/pdf");
		header("Content-Disposition:attachment;filename=$name");
		header("Expires:0");
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
		header("Pragma:public");

		echo file_get_contents($tmp);

		exit("Exited");
	}
	
	function saveZip($filename, $downloadCampaignPath){
		ob_clean();
		header("Content-Type:application/zip");
		header("Content-Disposition:attachment;filename=".date('m/d/Y h:i:s a').'.zip');
		header("Expires:0");
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
		header("Pragma:public");
		readfile($downloadCampaignPath.'archives/newscampaign.zip');
        unset($_SESSION['hasDownloaded']);
// 		header('Content-disposition: attachment; filename='.date('m/d/Y h:i:s a').'.zip');
//         header('Content-type: application/zip');
//         header("Expires: 0");
//         header('Cache-Control: must-revalidate');
//     	header('Pragma: public');
//     	header('Content-Length: ' . filesize($filename));
        
        redirect("index.php?cmd=campaigns");
		exit("Exited");
		
	}

	// VID FUNCTIONS (SEE ALSO func_atl.php)
	function clearstr($str){
		$arr = explode("\r\n", $str);
		$str_arr = array();
		if(sizeof($arr)){
			foreach($arr as $v){
				$v = trim($v);
				if($v){
					$str_arr[] = $v;
				}
			}
		}
		$str = implode("\r\n", $str_arr);

		return $str;
	}

	function add_sep($text){
		$str = wordwrap(str_replace("\r\n", " ", clearstr($text)), 33, "\r\n");
		$arr = explode("\r\n", $str);

		$str_arr = array();
		if(sizeof($arr)){
			$num = 1;
			$i = 0;
			foreach($arr as $v){
				$str_arr[] = $v;
				$i++;
				if($i == 5){
					$str_arr[] = "###" . $num;
					$num++;
					$i = 0;
				}
			}
		}
		$str = implode("\r\n", $str_arr);

		return $str;
	}

	function convertToEmbedURL($url) {
		$shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
		$longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';
		
		$youtubeId = (preg_match($shortUrlRegex, $url, $matches)) ? $matches[count($matches) - 1] : (preg_match($longUrlRegex, $url, $matches)) ? $matches[count($matches) - 1] : '';

		return empty($youtubeId) ? $url : 'https://www.youtube.com/embed/' . $youtubeId ;
	}
	
	function consoleLog( $message ){
	   echo "<script>console.log('". json_encode($message) ."')</script>";
	}
?>