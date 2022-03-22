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

// the core of the script... W+ sends post request to the script to be handled by it... see below
if(isset($_POST["WP_ACTION"])){
	// see processIPN func below...
	error_log("IPN Data:".print_r($_POST,true));
	processIPN();
}
else{
	// if not a POST request from W+, just writing to error_log
	error_log("Unknown post request. POST Data:".print_r($_POST,true));
}

// the function to validate $WEBSITE["wpipn"], etc... i.e. we're a valid Member of W+...
function validateIPN(){
	global $WEBSITE;

	// if(($_POST["RECEIVEREMAIL"]==$WEBSITE["wppp"])&&($_POST["WP_SECURITYKEY"]==$WEBSITE["wpipn"])){
	if($_POST["WP_SECURITYKEY"]==$WEBSITE["wpipn"]){
			return true;
	}
	else{
		return false;
	}
}

// the script core goes here... see above
function processIPN(){
	global $DB,$dbprefix,$WEBSITE,$SCRIPTURL;

	// i.e. if that is a true POST from W+
	if(validateIPN()){
		$ctransreceipt=$_POST["WP_TXNID"];
		$ccustemail=$_POST["WP_BUYER_EMAIL"];
		$ccustname=$_POST["WP_BUYER_NAME"];
		$ctransvendor=$_POST["RECEIVERID"];
		$cproditem=$_POST["WP_ITEM_NUMBER"];
		$recur=preg_match("/^subscr_/i",$_POST["WP_ACTION"])?1:0;
		$cprodtype=$recur?"RECURRING":"STANDARD";
		$ctransaction=$_POST["WP_ACTION"];
		$ctransamount=$_POST["WP_SALE_AMOUNT"];
		$ctranstime=strtotime( (!isset($_POST["TIMESTAMP"]) ? $_POST["TIMESTAMP"] : date('Y-m-d H:i:s')) );

		// checking if we have a Membership with that cproditem
		if($cur_pack=$DB->info("pack","pack_pp='$cproditem'")){
			// just a check we din't handle this transaction before... W+ likes to send multiple IPNs...
			if(!$cur_ipn=$DB->info("ipn","ipn_ctransreceipt='$ctransreceipt' and ipn_ctransaction='$ctransaction'")){
				$arr=array("ctransreceipt","ccustemail","ccustname","ctransvendor","cproditem","cprodtype","ctransaction","ctransamount","ctranstime");
				$str="";
				foreach($arr as $val){
					$str.=",ipn_".$val."='".${$val}."'";
				}
				// and here I insert to that ipn DB table...
				$DB->query("insert into $dbprefix"."ipn set ipn_rd='".time()."',ipn_sys='W+'".$str);

				// so, once the above checks are done... 'sale' and 'subscr_created' means first payment... we handle them in keygen
				// that is a re-bill action
				if ($ctransaction=="sale" || $ctransaction=="subscr_completed") {
					// checking amount>=pack_price
					// checking we have a Member with this $ccustemail
					// if (($ctransamount >= $cur_pack["pack_price"]) && ($cur_user=$DB->info("user","user_email='$ccustemail'"))) {
                    if ($cur_user=$DB->info("user","user_email='$ccustemail'")) {
						// and setting expire==+freq months
						$expire=strtotime("+".$cur_pack["pack_freq"]." months");
						$DB->query("update $dbprefix"."user set user_expire='$expire' where user_id='".$cur_user["user_id"]."'");
					}
				}
				// should be self-explanatory... :D
				elseif(($ctransaction=="refund")||($ctransaction=="subscr_refunded")){
					if($cur_user=$DB->info("user","user_email='$ccustemail'")){
						$expire=time();
						$DB->query("update $dbprefix"."user set user_expire='$expire' where user_id='".$cur_user["user_id"]."'");
					}
				}
			}
		}
	}
	else{
		error_log("Invalid W+ PayPal E-mail Address or W+ Security Key.");
	}
}

?>