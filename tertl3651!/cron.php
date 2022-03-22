<?php
    error_reporting(0);

    include "../sys/cpanel_credentials.php";

    ini_set('max_execution_time', 0);
    // including the sys files being able to connect DB
    include(dirname(__FILE__) . "/../sys/class.db.php");
    include(dirname(__FILE__) . "/../sys/config.php");
    include(dirname(__FILE__) . "/../sys/func.php");
    require_once(dirname(__FILE__) . '/../inc/admin/cpanel.php');

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

    /////////////////////////////
    // send email notification //
    /////////////////////////////

    $cpanel = new cPanel($cPanel_username, $cPanel_password, $cPanel_host_subdomain);
    $usages = json_decode($cpanel->getUsage());
    $stats = "";
    $subject = '';

    foreach ($usages->data as $usage) {
		$used = isset($usage->formatter) ? number_format(floatval($usage->usage / 1073741824), 2, '.', ',') . 'GB': $usage->usage;
		$percent = number_format(floatval(isset($usage->formatter) ? ($usage->usage / $usage->maximum) * 100 : 0), '2', '.', '');

        if($percent >= 80){
            $stats .= $usage->description .": ". $used ." / ". $maximum ." - ". $percent ."% \r\n";
            $subject .= $usage->description . ', ';
        }
    }

    if(!empty($subject)){
        sendmail(5,[
            "EMAIL" => $WEBSITE['notif_email'],
            "SUBJECT" =>  $subject,
            "STATISTICS" => htmlentities($stats),
            "SITENAME" => $WEBSITE["sitename"],
            "SITEURL" => $SCRIPTURL
        ]);
    }
    
    /////////////////////////////////////
    // backup database and application //
    /////////////////////////////////////
    $tmpDir = dirname(__FILE__) . '/../tmp/';

    // remove all content  //
    $files = glob($tmpDir . '*'); 
    foreach ($files as $file) { 
        if (is_file($file) && !(strpos($file, '.html') !== false)) {
            @unlink($file); 
        }
    }

    // mysql dump //
    $backup_file = $tmpDir . $dbname . '.sql';
    $mysqldump=exec('which mysqldump');
    $command = "$mysqldump --opt -h $dbhost -u$dbuser -p'$dbpass' ". "$dbname 2>&1> $backup_file";
    exec($command, $output);
    
    
    // CRON FOR SOCIAL MEDIA SCHEDULED POSTS
    $scheduledCampaigns = $DB->query("SELECT * FROM {$dbprefix}social_sharing WHERE published = '0'");

    // var_dump($scheduledCampaigns);
    function postSocialMedia($data, $postURL) {
        $ch = curl_init();  

        curl_setopt($ch,CURLOPT_URL, "https://affilashop.com" . $postURL);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false); 
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'POST'); 
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, [
            'body' => $data['page_url'] . " " . $data['message'],
            'hashtags' => $data['hashtags'],
            'account_name' => $data['account_name']
        ]); 
     
        $output=curl_exec($ch);
     
        curl_close($ch);

        return isset($output) ? json_decode($output) : [];  
    }

    foreach ($scheduledCampaigns as $scheduledCampaign) {
        $userId = $scheduledCampaign['user_id'];
        $type = $scheduledCampaign['type'];
        $id = $scheduledCampaign['id'];

        $postURL = "/?token={$SPECIALTOKEN}&mode=post&uid={$userId}&type={$type}";

        try {
            $post = postSocialMedia($scheduledCampaign, $postURL);
            $DB->query("UPDATE {$dbprefix}social_sharing SET published = '1' WHERE id = '{$id}'");
        } catch (\Exception $e) {
            //throw $th;
            var_dump($e); die;
        }
        
        
    }

    // zip project //
    // $rootPath = realpath(dirname(__FILE__) . '/../');
    // $zipName = dirname(__FILE__) . '/../tmp/wflbk.zip';
    // $zip = new ZipArchive();
    // $zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
   
    exec('zip -r '. $zipName . ' ' . $rootPath .' -x *.zip');
?>