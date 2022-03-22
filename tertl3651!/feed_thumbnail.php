<?php
    $news_link = $_POST["news_link"];
    
    // echo($news_link);
    include("../inc/simple_html_dom_v2.php");
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $news_link);
    curl_setopt($curl, CURLOPT_REFERER, $news_link);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $str = curl_exec($curl);
    curl_close($curl);
    
    // Create a DOM object
    $html_base = new simple_html_dom();
    // Load HTML from a string
    $html_base->load($str);
    
    //get all category links
    $images = array();
    // echo $news_link;
    foreach($html_base->find('img') as $element) {
        $imgSrc = $element->src;
        if (strpos($imgSrc, 'https') !== false) {
            // CHECK THE IMAGE RESOLUTION FIRST
            if ( strpos($imgSrc, '.svg') !== false ) {
                if( !in_array( $imgSrc, $images ) ){
                    $images[] = $imgSrc;
                }
            }else{
                $size = getimagesize($imgSrc);
                
                $width = str_replace('"','',$size[3]);
                $new = str_replace(' height','',explode("width=",$width)[1]);
                $new2 = explode("=", $new)[0];
                
                if( $new2 >= 150){
                    if( !in_array( $imgSrc, $images ) ){
                        $images[] = $imgSrc;
                    }
                }
            }
            
        }
    }
    
    echo json_encode( $images );
    $html_base->clear(); 
    unset($html_base);
?>