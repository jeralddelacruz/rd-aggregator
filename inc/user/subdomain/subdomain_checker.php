<?php
    // ========== GET THE USER SUBDOMAIN ========== //
    $user = $DB->info("user", "user_id = '{$UserID}'");
    $serverName = $_SERVER["SERVER_NAME"];
    $serverName1 = explode( ".".$MAINDOMAIN, $serverName )[0];
    
    $userSubdomains = $DB->query("SELECT * FROM {$dbprefix}user_subdomain WHERE user_id = '{$UserID}' AND subdomain_status = 1");
    
    // check if the current subdomain is existing to the current user
    $singleSubdomain = $DB->info("user_subdomain", "user_id = '{$UserID}' AND subdomain_name = '{$serverName1}' AND subdomain_status = 1");
        
    if( $serverName1 != $MAINDOMAIN ){
        if( !$singleSubdomain ){
            redirect( "https://".$MAINDOMAIN."/user/index.php?cmd=home" );
        } 
    } // else Good Redirection
    
    if( $_SESSION["connected_subdomain"] != "success" ){
        if( count($userSubdomains) > 0 ){
            // check if the current subdomain is match to the 
            // check for all the subdomain the current subdomain
            foreach($userSubdomains as $userSubdomain){
                if( $userSubdomain["subdomain_name"] == $serverName1 ){
                    $_SESSION["connected_subdomain"] = "success";
                    redirect( "https://" . $userSubdomain["subdomain_name"] . ".".$MAINDOMAIN."/user/index.php?cmd=home" );
                }
            }
        }else{
            // check the url if have subdomain
            // if have then redirect to the main domain
            if( $serverName1 != $MAINDOMAIN ){
                redirect( "https://".$MAINDOMAIN."/user/index.php?cmd=home" );
            }// else Good Redirection
            
        }
    }

?>