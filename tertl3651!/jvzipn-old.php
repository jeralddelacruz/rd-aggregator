<?php
error_reporting(0);
// including the sys files being able to connect DB
include("../sys/class.db.php");
include("../sys/config.php");
include("../sys/func.php");
include("../sys/jsonRPCClient.php");

// connecting DB
$DB=new db($dbhost,$dbuser,$dbpass,$dbname);
$DB->connect();
// if there are ploblems, just exit
if($DB->connect<1){
	exit;
}

// loading Site Setup, to be used in validate functions
$res=$DB->query("select setup_key,setup_val from $dbprefix"."setup order by setup_id");
foreach($res as $row){
	$WEBSITE[$row["setup_key"]]=$row["setup_val"];
}

// the core of the script... JVZ sends post request to the script to be handled by it... see below
if(isset($_POST["ctransaction"])){
	// see processIPN func below...
	error_log("IPN Data:".print_r($_POST,true));
	processIPN();
}
else{
	// if not a POST request from JVZ, just writing to error_log
	error_log("Unknown post request. POST Data:".print_r($_POST,true));
}

// the function to validate $WEBSITE["jvzipn"], etc... i.e. we're a valid Member of JVZ...
function validateIPN(){
	global $WEBSITE;

	$secretKey=$WEBSITE["jvzipn"];
	$pop = "";
	$ipnFields = array();

	if ( get_magic_quotes_gpc() ) {
		$_POST = array_map('stripslashes', $_POST);
	}

	foreach ($_POST AS $key => $value) {
		if ($key == "cverify") {
			continue;
		}
		$ipnFields[] = $key;
	}
	sort($ipnFields);

	foreach ($ipnFields as $field) {
		$pop = $pop . $_POST[$field] . "|";
	}
	$pop = $pop . $secretKey;

	$calcedVerify = sha1(mb_convert_encoding($pop, "UTF-8"));
	$calcedVerify = strtoupper(substr($calcedVerify,0,8));

	return $calcedVerify == $_POST["cverify"];
}

// the script core goes here... see above
function processIPN(){
	global $DB,$dbprefix,$WEBSITE,$SCRIPTURL,$dbkey;

	// i.e. if that is a true POST from JVZ
	if(validateIPN()){
		$arr=array("ctransreceipt","ccustemail","ccustname","ctransvendor","cproditem","cprodtype","ctransaction","ctransamount","ctranstime");
		$str="";
		foreach($arr as $val){
			${$val}=$_POST[$val];
			$str.=",ipn_".$val."='".${$val}."'";
		}

		// checking we have a Membership with that cproditem
		if(($cur_pack=$DB->info("pack","pack_jvz='$cproditem'"))){
			// just a check we din't handle this transaction before... JVZ likes to send multiple IPNs...
			if(!$cur_ipn=$DB->info("ipn","ipn_ctransreceipt='$ctransreceipt' and ipn_ctransaction='$ctransaction'")){
				// and here I insert to that ipn DB table...
				$DB->query("insert into $dbprefix"."ipn set ipn_rd='".time()."',ipn_sys='JVZ'".$str);

				// so, once the above checks are done... SALE means first payment
				if($ctransaction=="SALE"){
					$login_url=$SCRIPTURL."/user/";
					// new Member
					if(!$cur_user=$DB->info("user","user_email='$ccustemail'")){
						$expire=0;
						// if TRIAL period, the amount should be equal to the Trial Period Price...
						if($cur_pack["pack_trial"]&&($ctransamount>=$cur_pack["pack_tprice"])){
							$expire=strtotime("+".$cur_pack["pack_trial"]." days");
						}
						// if no TRIAL period, checking the amount>=pack_price
						elseif($ctransamount>=$cur_pack["pack_price"]){
							$expire=strtotime("+".$cur_pack["pack_freq"]." months");
						}

						// the amount corresponds to the Product Price
						if($expire){
							$pack=$cur_pack["pack_id"];
							$pass=rand_str(8);
							$name_arr=explode(" ",$ccustname);
							$fname=$name_arr[0];
							$lname=$name_arr[1];

							// lifetime Membership
							if($cur_pack["pack_freq"]==120){
								$expire=0;
							}

							// ... add Member to the user DB table
							$DB->query("insert into $dbprefix"."user set pack_id='$pack',user_pass='".mc_encrypt($pass,$dbkey)."',user_email='$ccustemail',user_fname='$fname',user_lname='$lname',user_rd='".time()."',user_act='1',user_expire='$expire'");

							$row=$DB->info("user","user_email='$ccustemail'");
							user_mkdir($row["user_id"]);

							// ... sending e-mails...
							sendmail(1,array("fname"=>$fname,"lname"=>$lname,"email"=>$ccustemail,"pass"=>$pass,"sitename"=>$WEBSITE["sitename"],"siteurl"=>$SCRIPTURL));
							sendmail(2,array("fname"=>$fname,"lname"=>$lname,"email"=>$ccustemail,"pass"=>$pass,"sitename"=>$WEBSITE["sitename"],"siteurl"=>$SCRIPTURL));
							sendmail(3,array("fname"=>$fname,"lname"=>$lname,"email"=>$ccustemail));
							// sendgr($fname,$lname,$ccustemail);

							// ... and printing on JVZ...
							print("Thanks for your Purchase, <b>$ccustname</b>. Below is your login details to Sign In at <b>$login_url</b><br /><br />E-mail Address: <b>$ccustemail</b><br />Password: <b>$pass</b><br /><br />You can change your Password at any time under My Profile tab in Member Control Panel.");
						}
					}
					// upgrade
					else{
						// if amount corresponds to the Product Price
						if($ctransamount>=$cur_pack["pack_price"]){
							$pack=$cur_pack["pack_id"];
							$pass=mc_decrypt($cur_user["user_pass"],$dbkey);
							$expire=($cur_pack["pack_freq"]==120)?0:strtotime("+".$cur_pack["pack_freq"]." months");
							$DB->query("update $dbprefix"."user set pack_id='$pack',user_expire='$expire' where user_id='".$cur_user["user_id"]."'");

							print("Thanks for your Purchase, <b>$ccustname</b>. Below is your login details to Sign In at <b>$login_url</b><br /><br />E-mail Address: <b>$ccustemail</b><br />Password: <b>$pass</b><br /><br />You can change your Password at any time under My Profile tab in Member Control Panel.");
						}
					}
				}
				// that is a re-bill action
				elseif($ctransaction=="BILL"){
					// checking amount>=pack_price
					// checking we have a Member with this $ccustemail
					if(($ctransamount>=$cur_pack["pack_price"])&&($cur_user=$DB->info("user","user_email='$ccustemail'"))){
						// and setting expire==+freq months
						$expire=strtotime("+".$cur_pack["pack_freq"]." months");
						$DB->query("update $dbprefix"."user set user_expire='$expire' where user_id='".$cur_user["user_id"]."'");
					}
				}
				// should be self-explanatory... :D
				elseif($ctransaction=="RFND"){
					if($cur_user=$DB->info("user","user_email='$ccustemail'")){
						$expire=time();
						$DB->query("update $dbprefix"."user set user_expire='$expire' where user_id='".$cur_user["user_id"]."'");
					}
				}
			}
		}
	}
	else{
		error_log("Calculated hash does not match POST data.");
	}
}

?>