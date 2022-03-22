<?php
// admin del functions
function pack_del($id){
	global $DB,$dbprefix;

	$res=$DB->query("select p.pack_id,(select count(user_id) from $dbprefix"."user u where u.pack_id=p.pack_id) as num from $dbprefix"."pack p where p.pack_id='$id'");
	if($res[0]&&($id!=1)&&(!$res[0]["num"])){
		$DB->query("delete from $dbprefix"."pack where pack_id='$id'");
	}

	return;
}

function admin_del($id){
	global $DB,$dbprefix;

	$DB->query("delete from $dbprefix"."admin where admin_id='$id' and admin_id<>'1'");

	return;
}

function user_del($id){
	global $DB,$dbprefix;

	if($row=$DB->info("user","user_id='$id'")){
		$res=$DB->query("select lg_id from $dbprefix"."lg where user_id='$id'");
		if(sizeof($res)){
			foreach($res as $row){
				lg_del($row["lg_id"],$id);
			}
		}

		$res=$DB->query("select distinct cover_dir from $dbprefix"."cover where user_id='$id'");
		if(sizeof($res)){
			foreach($res as $row){
				if(is_dir("../aie/tmp/".$row["cover_dir"])){
					rem_dir("../aie/tmp/".$row["cover_dir"]);
				}
			
			}
		}

		if(is_dir("../upload/".$id)){
			rem_dir("../upload/".$id);
		}
		if(is_dir("../aie/upload/".$id)){
			rem_dir("../aie/upload/".$id);
		}
		if($row["user_avatar"]&&is_file("../upload/avatar/".$row["user_avatar"])){
			unlink("../upload/avatar/".$row["user_avatar"]);
		}

		$DB->query("delete from $dbprefix"."cover where user_id='$id'");
		$DB->query("delete from $dbprefix"."limit where user_id='$id'");
		$DB->query("delete from $dbprefix"."log where user_id='$id'");
		$DB->query("delete from $dbprefix"."text where user_id='$id'");
		$DB->query("delete from $dbprefix"."user where user_id='$id'");
	}

	return;
}

function pr_del($id,$extra){
	global $DB,$dbprefix;

	if($DB->info("pr","pr_id='$id'".($extra?" and $extra":""))){
		$DB->query("delete from $dbprefix"."pr where pr_id='$id'");
		rem_dir("../upload/pr/".$id);
	}

	return;
}

function tpl_del($id,$extra=""){
	global $DB,$dbprefix;

	if($DB->info("tpl","tpl_id='$id'".($extra?" and $extra":""))){
		$DB->query("delete from $dbprefix"."tpl where tpl_id='$id'");
		rem_dir("../upload/tpl/".$id);
	}

	return;
}

function prb_del($id,$extra=""){
	global $DB,$dbprefix;

	if($DB->info("prb","prb_id='$id'".($extra?" and $extra":""))){
		$DB->query("delete from $dbprefix"."prb where prb_id='$id'");
	}

	return;
}

function pageb_del($id,$extra=""){
	global $DB,$dbprefix;

	if($DB->info("pageb_copy","pageb_id='$id'".($extra?" and $extra":""))){
		$DB->query("delete from $dbprefix"."pageb_copy where pageb_id='$id'");
	}

	return;
}

function page_del($id,$extra){
	global $DB,$dbprefix;

	if($DB->info("page","page_id='$id'".($extra?" and $extra":""))){
		$DB->query("delete from $dbprefix"."page where page_id='$id'");
		$DB->query("update $dbprefix"."page set page_pid='0' where page_pid='$id'");
	}

	return;
}

function art_del($id){
	global $DB,$dbprefix;

	$DB->query("delete from $dbprefix"."art where art_id='$id'");

	return;
}

function bonus_del($id){
	global $DB,$dbprefix;

	$DB->query("delete from $dbprefix"."bonus where bonus_id='$id'");

	return;
}

function door_del($id){
	global $DB,$dbprefix;

	$DB->query("delete from $dbprefix"."door where door_id='$id'");

	return;
}

function ad_del($id){
	global $DB,$dbprefix;

	$DB->query("delete from $dbprefix"."ad where ad_id='$id'");

	return;
}

function poll_del($id){
	global $DB,$dbprefix;

	$DB->query("delete from $dbprefix"."poll where poll_id='$id'");

	return;
}

// user del functions
function lg_del($id,$user_id){
	global $DB,$dbprefix;

	if($row=$DB->info("lg","lg_id='$id' and user_id='$user_id'")){
		@unlink("../lg/u".$user_id."-l".$id.".tmp");
		if($row["lg_bg"]&&!preg_match("/^".make_pat("../upload")."/i",$row["lg_bg"])){
			@unlink("../lg/".$row["lg_bg"]);
		}
		if($row["lg_himg"]){
			@unlink("../lg/".$row["lg_himg"]);
		}
		if($row["lg_fimg"]){
			@unlink("../lg/".$row["lg_fimg"]);
		}
		$DB->query("delete from $dbprefix"."lg where lg_id='$id'");
	}

	return;
}

function vid_del($id,$user_id){
	global $DB,$dbprefix;

	if($row=$DB->info("vid","vid_id='$id' and user_id='$user_id'")){
		rem_dir("../vid/u".$user_id."-v".$id);
		$DB->query("delete from $dbprefix"."vcover where vid_id='$id'");
		$DB->query("delete from $dbprefix"."vid where vid_id='$id'");
		$DB->query("delete from $dbprefix"."vtext where vid_id='$id'");
	}

	return;
}

function mes_del($id){
	global $DB,$dbprefix;

	$DB->query("delete from $dbprefix"."mes where mes_id='$id'");

	return;
}


?>