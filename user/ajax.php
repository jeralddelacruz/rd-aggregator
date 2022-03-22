<?php
set_time_limit(0);
error_reporting(0);
session_set_cookie_params(3600,"/");
session_start();
$dir="../sys";
$fp=opendir($dir);
while(($file=readdir($fp))!=false){
	$file=trim($file);
	if(($file==".")||($file=="..")){continue;}
	$file_parts=pathinfo($dir."/".$file);
	if($file_parts["extension"]=="php"){
		include($dir."/".$file);
	}
}
closedir($fp);


$DB=new db($dbhost,$dbuser,$dbpass,$dbname);
$DB->connect();
$user = $_SESSION['UserID'];
if($DB->connect<1){
	echo "Can't go on, DB not initialized.";
	exit;
}

if($_POST['tip']=='editExit'){
    $eid = $_POST['eid'];
    $exp = $DB->query("select * from $dbprefix"."exp where user_id=$user and exp_id=$eid");
    $exit = array();
    foreach($exp as $key => $value){
        $exit['name']=$value['exp_name'];
        $exit['heading']=$value['exp_heading'];
        $exit['body']=$value['exp_body'];
        $exit['button']=$value['exp_button'];
        $exit['btextcolor']=$value['exp_button_tcolor'];
        $exit['bcolor']=$value['exp_bcolor'];
        $exit['blink']=$value['exp_blink'];
        $exit['img']=$value['exp_img'];
        $headfonts = unserialize($value['exp_headfont']);
        $exit['headfont'] = $headfonts[0];
        $exit['headsize'] = $headfonts[1];
        $exit['headcolor'] = $headfonts[2];
        $bodyfonts = unserialize($value['exp_bodyfont']);
        $exit['bodyfont'] = $bodyfonts[0];
        $exit['bodysize'] = $bodyfonts[1];
        $exit['bodycolor'] = $bodyfonts[2];
    }
    echo json_encode($exit);
}
if($_POST['tip']=='editSpcialProof'){
    $spid = $_POST['spid'];
    $exp = $DB->query("select * from $dbprefix"."scp where user_id=$user and scp_id=$spid");
    $sproof = array();

    foreach($exp as $key => $value){
        $sproof['title']=$value['scp_title'];
        $sproof['image']=$value['scp_image'];
        $sproof['scp_content']=$value['scp_content'];
        $sproof['scp_link']=$value['scp_link'];
        $sproof['scp_time']=$value['scp_time'];
        $sproof['scp_diff']=$value['scp_diff'];
    }

    echo json_encode($sproof);
}
if($_POST['tip']=='getEms'){
    $pid = $_POST['pid'];
    $cbp = $DB->query("select tpl_email from $dbprefix"."tpl where tpl_id=$pid limit 1");
    if(sizeof($cbp)){
        echo $cbp[0]['tpl_email'];
    }else {
        return false;
    }
}

?>