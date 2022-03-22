<?php

    // index.php of USERS
	set_time_limit(0);
	error_reporting(0);
	session_set_cookie_params(14400,"/");
// 	ini_set('display_errors', 1);
//     ini_set('display_startup_errors', 1);
//     error_reporting(E_ALL);
	session_start();

	// sys FOLDER DIRECTORY INCLUDE
	$dir = "../sys";
	$fp = opendir($dir);
	while(($file = readdir($fp)) != false){
		$file = trim($file);
		if(($file == ".") || ($file == "..")){continue;}
		$file_parts = pathinfo($dir . "/" . $file);
		if($file_parts["extension"] == "php"){
			include($dir . "/" . $file);
		}
	}
	closedir($fp);

	// DATABASE CONNECTION THROUGH class.db.php
	$DB = new db($dbhost, $dbuser, $dbpass, $dbname);
	$DB->connect();
	if($DB->connect < 1){
		exit("Can't proceed. The database is not initialized.");
	}
	
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://plrcatalog.com/api/products.php',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    
    $server_products = curl_exec($curl);
    
    curl_close($curl);
    
    // ================================= //
    // ===== Destruct New Products ===== //
    // ================================= //
    
    // get current products
    $current_products = $DB->query("SELECT * FROM {$dbprefix}prb p LEFT JOIN {$dbprefix}pr pr ON p.pr_id=pr.pr_id order by pr.pr_order ASC");
    
    $options = array();
    
    foreach ($current_products as $arr) {
        $options[] = $arr["prb_id"];  // COnverted to 1-d array
        /* Result:  Array ( [0] => B00CEEZ57S [1] => B002QJZADK [2] => B001EHL2UK [3] => B003FSTNB6 )*/
    }
    
    /* Filter $array2 and obtain those results for which ['ASIN'] value matches with one of the values contained in $options */
    
    $server_products = json_decode( $server_products );
    $result = array_filter($server_products, function($v) use ($options) {
        echo json_encode( $options );
        return !in_array($v->prb_id, $options);
    });
    
    if( count( $result ) > 0 ){
        $limit = 10;
        $i = 0;
        foreach( $result as $item ){
            $i++;
            
            if( $i <= $limit ){
                // save the prb
                $prb_id = $item->prb_id;
                $cat_id = $item->cat_id;
                $prb_lic = $item->prb_lic;
                $prb_pack = $item->prb_pack;
                $prb_title = $item->prb_title;
                $prb_price = $item->prb_price;
                $prb_ptext = $item->prb_ptext;
                $prb_ltext = $item->prb_ltext;
                $prb_order = $item->prb_order;
                
                $prn_id = $item->pr_title;
                $pr_title = $item->pr_title;
                $pr_desc = $item->pr_desc;
                $pr_body = $item->pr_body;
                $pr_cloud = $item->pr_cloud;
                $pr_url = $item->pr_url;
                $pr_cover = $item->pr_cover;
                $pr_order = $item->pr_order;
                
                $new_pr = $DB->query("INSERT INTO {$dbprefix}pr SET 
                    pr_id = '{$prn_id}',
                    pr_pid = '',
                    pr_title = '{$pr_title}',
                    pr_desc = '{$pr_desc}',
                    pr_body = '{$pr_body}',
                    pr_cloud = '{$pr_cloud}',
                    pr_url = '{$pr_url}',
                    pr_cover = '{$pr_cover}',
                    pr_order = '{$pr_order}'
                ");
                
                $last_pr_id = $DB->query("SELECT pr_id FROM {$dbprefix}pr ORDER BY pr_id DESC LIMIT 1")[0];
                $pr_id = $last_pr_id["pr_id"];
                
                $new_prb = $DB->query("INSERT INTO {$dbprefix}prb SET 
                    prb_id = '{$prb_id}',
                    cat_id = '{$cat_id}',
                    pr_id = '{$pr_id}',
                    prb_lic = '{$prb_lic}',
                    prb_pack = '{$prb_pack}',
                    prb_title = '{$prb_title}',
                    prb_price = '{$prb_price}',
                    prb_ptext = '{$prb_ptext}',
                    prb_ltext = '{$prb_ltext}',
                    prb_order = '{$prb_order}'
                ");
            }
            
        }
        echo "New products has been saved";
    }else{
        echo "No new product from the server";
    }
?>