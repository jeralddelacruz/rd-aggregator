<?php
    error_reporting(0);

    ini_set('max_execution_time', 0);
    include(dirname(__FILE__) . "/../sys/class.db.php");
    include(dirname(__FILE__) . "/../sys/config.php");
    include(dirname(__FILE__) . "/../sys/func.php");

    // connecting DB
    $DB = new db($dbhost,$dbuser,$dbpass,$dbname);
    $DB->connect();

    // if there are ploblems, just exit
    if( $DB->connect < 1 ) exit;

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
?>