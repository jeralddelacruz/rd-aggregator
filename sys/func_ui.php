<?php
function get_ad($area){
	global $DB,$dbprefix;

	unset($_SESSION["AdArea$area"]);

	$str="";

	$res=$DB->query("select * from $dbprefix"."ad where ad_area='$area' and (ad_mshow='0' or ad_mshow>ad_show) and (ad_mclick='0' or ad_mclick>ad_click) and (ad_expire='0' or ad_expire>'".time()."') and ad_act='1' order by rand() limit 0,1");
	if($row=$res[0]){
		$ad_id=$row["ad_id"];
		$ad_body=$row["ad_body"];
		$ad_url=$row["ad_url"];

		if($ad_url){
			$str="<div style=\"display:inline-block;cursor:pointer;\" onclick=\"window.open('ad.php?area=$area');\">$ad_body</div>";

			$_SESSION["AdArea$area"]=array("id"=>$ad_id,"url"=>$ad_url);
		}
		else{
			$str="<div style=\"display:inline-block;cursor:pointer;\">$ad_body</div>";
		}

		$DB->query("update $dbprefix"."ad set ad_show=(ad_show+1) where ad_id='$ad_id'");
	}

	return $str;
}

function get_poll(){
	global $DB,$dbprefix,$UserID, $WEBSITE, $cur_pack; 

	$str="";

	$res=$DB->query("select * from $dbprefix"."poll where poll_act='1' order by rand() limit 0,1");
	
    // SELECT pack_id OF CURRENT USER IN user TABLE
    $currentUserPackId = $DB->query("select pack_id from $dbprefix"."user WHERE user_id='$UserID'");
    
    // SELECT pack_ar FROM pack table
    $memberships = $DB->query("select * from $dbprefix"."pack");
	
	if($row=$res[0]){
		$_SESSION["PollID"]=$row["poll_id"];
		
        // ORIGINAL FOR BACKUP - COMMENTED-OUT FOR DISPLAYING CURRENT MEMBERSHIP PACKAGE
        // $str.="<div class=\"col-md-3\"><div class=\"card\"><div class=\"content card-body\" id=\"poll\">";
        
        // START TAG OF col-md-3
        $str .= "<div class=\"col-md-3\">";
        
            $str .= "<div class=\"card\">";
                $str .= "<div class=\"content card-body\">";
                $str .= "<center><b>" . "Purchased Membership Levels: " . "</b></center><br />";

                foreach($memberships as $membership){
                    if ($currentUserPackId[0]["pack_id"] == $membership['pack_id']) break;
                    if (in_array($membership['pack_title'], ['Free']) || strpos($membership['pack_display_title'], 'DS') !== false) continue;

                    $str .= "• " . $membership['pack_display_title'] . "<br />";
                }
                
                // ADDED
                // $str .= "• " . $currentM["pack_display_title"];
                
                $str .= "</div>";
            $str .= "</div>";
            
            // START TAG OF POLL CARD
            $str .= "<div class=\"card\"><div class=\"content card-body\" id=\"poll\">";
    		$str .= "<div class=\"text-center\" style=\"padding-bottom:10px;\"><strong>".$row["poll_qst"]."</strong></div>";
    
    		if(!preg_match(";$UserID;",$row["poll_user"])){
    			$str.=poll_vote($row["poll_opt"]);
    		}
    		else{
    			$str.=poll_res($row["poll_opt"],$row["poll_vote"]);
    		}
            
            // END TAG OF POLL CARD
    		$str .= "</div></div>";
		
        // END TAG OF col-md-3		
		$str .= "</div>";
	}

	return $str;
}

function poll_vote($opt){
	global $WEBSITE;

	$str="<div id=\"poll_vote\" class=\"form-group\">";

	$opt_arr=unserialize($opt);
	foreach($opt_arr as $k=>$arr){
		$str.="<div class=\"form-check\"><input type=\"radio\" name=\"ans\" id=\"ans_$k\" value=\"$k\" class=\"ans form-check-input\" /><label for=\"ans_$k\">".$arr["ans"]."</label></div>";
	}

	$str.="<div class=\"text-center\" style=\"padding-top:10px;\"><input type=\"button\" id=\"vote_btn\" value=\"Vote\" class=\"btn btn-".$WEBSITE["theme_btn"]." btn-fill\" /></div></div>";

	return $str;
}

function poll_res($opt,$vote){
	$str="<div id=\"poll_res\">";

	$js="";
	$opt_arr=unserialize($opt);
	foreach($opt_arr as $k=>$arr){
		$per=number_format(100*$arr["num"]/($vote?$vote:1),2,".","");
		$str.=$arr["ans"]." <strong>$per%</strong> (".$arr["num"].")<div id=\"res_$k\" style=\"width:0px;height:10px;background-color:#336699;margin-bottom:5px;\"></div>";
		$js.="$(\"#res_$k\").animate({width:\"$per%\"},600);";
	}
	$str.="<strong>Total Votes:</strong> $vote";
	$str.="</div>";

	$str.="<script>jQuery(function($){".$js."});</script>";

	return $str;
}

function landingPage () {
	global $DB,$dbprefix;
	
	$THEME = $DB->query("SELECT * FROM $dbprefix"."themes WHERE selected = 1");
	$landingPage = "./tpl/tpl_index.php";

	if ($THEME[0][0] == 1) {
		$landingPage = "./tpl/Theme_1/tpl_index.php";
	} elseif ($THEME[0][0] == 2) {
		$landingPage = "./tpl/Theme_2/tpl_index.php";
	} elseif ($THEME[0][0] == 3) {
		$landingPage = "./tpl/Theme_3/tpl_index.php";
	}

	return $landingPage;
}

function colorTheme () {
	global $DB,$dbprefix;

	$THEME = $DB->query("SELECT * FROM $dbprefix"."themes WHERE selected = 1");
	$THEME_COLOR = $DB->query("SELECT * FROM $dbprefix"."colors WHERE selected = 1");
	$name = strtolower($THEME_COLOR[0]['name']);
	$v = rand(1, 1000);

	return "<link href='/themes/css/Theme_{$THEME[0][0]}/{$name}.css?v={$v}' rel='stylesheet' media='all'>";
}

function fontTheme () {
	global $DB,$dbprefix;

	$THEME_FONT = $DB->query("SELECT * FROM $dbprefix"."fonts WHERE selected = 1");
	$name = strtolower($THEME_FONT[0]['name']);

	return "{$name}";
}

?>