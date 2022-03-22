<?php include('../../cache_solution/top-cache-v2.php'); ?>
<!-- Fancy Box -->
<?php if(isset($THEME[0]) && isset($THEME[0][0]) && $THEME[0][0] == 2) : include_once('fancybox.php'); endif; ?>


<?php
if(!preg_match(";bba2;",$cur_pack["pack_ar"])){
	redirect("index.php?cmd=deny");
}

$id=$_GET["id"];

if((!$id)&&($LIMIT_ARR["bb"]&&($LIMIT_ARR["bb_left"]<=0))){
?>
<div class="row">
	<div class="alert alert-danger">You have exceeded the allowed amount of Bonus Builder Pages to add this month. You can <a href="index.php?cmd=renew" class="mark">Upgrade Membership</a> to a higher level.</div>
</div>
<?php
}
else{

if($id&&(!$row=$DB->info("pageb_copy","pageb_id='$id' and user_id='$UserID' and pageb_type='bba2'"))){
	redirect("index.php?cmd=bba2");
}
$exits = $DB->query("select * from $dbprefix"."exp where user_id=$UserID");
$socials = $DB->query("select * from $dbprefix"."scp where user_id=$UserID");
$res = $DB->query("select * from $dbprefix"."cbt where user_id=$UserID limit 1");
$fbpval = ''; $fbcval = ''; $fbcsval = ''; $fbmval = ''; 
$bbaproducts = $DB->query("SELECT p.prb_id, pr.* FROM {$dbprefix}prb p LEFT JOIN {$dbprefix}pr pr ON p.pr_id=pr.pr_id order by prb_order ASC ");

if(sizeof($res)){
    $fbpval = $res[0]['cbt_fbp'];
    $fbcval = $res[0]['cbt_fbc'];
    $fbcsval = $res[0]['cbt_fbcs'];
    $fbmval = $res[0]['cbt_fbm'];
}

if(!$_POST["submit"]){
	if($id){
		$title=$row["pageb_title"];
		$lp=$row["pageb_lp"];
//		$op=$row["pageb_op"];
		$tp=$row["pageb_tp"];
		$bcode=$row["pageb_bcode"];
		$prcode=$row["pageb_prcode"];
		$fbpcode=$row["pageb_fbp"];
		$fbccode=$row["pageb_fbc"];
		$fbcscode=$row["pageb_fbcs"];
		$fbmcode=$row["pageb_fbm"];
		$expid=$row["pageb_exp"];
		$scpids=$row["pageb_scp"];
		//$topbar=$row["pageb_topbar"];
		$scpid=explode(",",$scpids);
//		$oarcode=$row["pageb_oarcode"];
		$cdt=$row["pageb_cdt"];
		$expire=$row["pageb_expire"];
		$bbaproduct = explode(',', $row['pageb_pr']);
		// $pr_arr=unserialize($row["pageb_pr"]);
		// if(sizeof($pr_arr)){
		// 	foreach($pr_arr as $k=>$v){
		// 		$id_arr[$k]=$k;
		// 		$price_arr[$k]=$v;
		// 	}
		// }
	}
}
else{
	$author=(int)$_POST["author"];
	$title=strip($_POST["title"]);
	$lp=strip($_POST["lp"],0);
//	$op=strip($_POST["op"],0);
	$tp=strip($_POST["tp"],0);
	$bcode=strip($_POST["bcode"]);
	$prcode=strip($_POST["prcode"]);
	$expop=strip($_POST["exp"]);
	$scpop=strip($_POST["socs"]);
	//$topbar=strip($_POST["topbar"]);
//	$oarcode=strip($_POST["oarcode"],0);
	$cdt=strip($_POST["cdt"],0);
	$expire=(int)strtotime(strip($_POST["expire"]));
	// $id_arr=$_POST["id_arr"];
	// $price_arr=$_POST["price_arr"];
	$bbaproduct = $_POST['bbaproduct'];
    $fbpixel=''; $fbcomment=''; $fbcsnip=''; $fbmes='';
    
	if($_POST["fbpd"]=='1'){
	    $fbpixel = $fbpval;
	}else{
	    $fbpixel = $_POST["fbp"];
    }
    
	if($_POST["fbcd"]){
	    $fbcomment = $fbcval;
	    $fbcsnip = $fbcsval;
	}else{
	    $fbcomment = $_POST["fbc"];
	    $fbcsnip = $_POST["fbcs"];
    }
    
	if($_POST["fbmd"]){
	    $fbmes = $fbmval;
	}else{
	    $fbmes = $_POST["fbm"];
    }

	$error="";
	if(!$title||!$lp||!$tp||!$bcode){
		$error.="&bull; Required fields should be <strong>filled in</strong>.<br />";
	}
	if(!preg_match("/%link%/i",$lp)){
		$error.="&bull; <strong>%link%</strong> token is missing in <strong>Sales Page Content</strong>.<br />";
	}
	if(!preg_match("/%download%/i",$tp)||!preg_match("/%license%/i",$tp)){
		$error.="&bull; <strong>%download%</strong> and/or <strong>%license%</strong> tokens are missing in <strong>Download Page Content</strong>.<br />";
	}
	if(!sizeof($bbaproduct)){
		$error.="&bull; At least one <strong>Commission Builder Product</strong> should be <strong>chosen</strong>.<br />";
	}

	if(!$error){
		// $pr_arr=array();
		// foreach($id_arr as $k=>$v){
		// 	$pr_arr[$k]=(double)$price_arr[$k];
		// }
		// $pr=serialize($pr_arr);
		echo "<script>console.log('".json_encode($bbaproduct)."')</script>";
        // exit;
		$bbaproduct = implode(',', $bbaproduct);
        
		if(!$id){

			$lps=rand_str(10);
			$ops=rand_str(10);
			$tps=rand_str(10);
			$res=$DB->query("select pageb_id from $dbprefix"."pageb_copy where (pageb_lps='$lps' or pageb_ops='$ops' or pageb_tps='$tps')");
			if(!sizeof($res)){
				$id=$DB->getauto("pageb_copy");

				$DB->query("
					INSERT into $dbprefix"."pageb_copy SET
					pageb_id='$id',
					user_id='$UserID',
					pageb_title='$title',
					pageb_lps='$lps',
					pageb_ops='$ops',
					pageb_tps='$tps',
					pageb_lp='$lp',
					pageb_op='',
					pageb_tp='$tp',
					pageb_bcode='$bcode',
					pageb_prcode='$prcode',
					pageb_oarcode='$oarcode',
					pageb_cdt='$cdt',
					pageb_expire='$expire',
					pageb_pr='$bbaproduct',
					pageb_type='bba2',
					pageb_fbp='$fbpixel',
					pageb_fbc='$fbcomment',
					pageb_fbcs='$fbcsnip',
					pageb_fbm='$fbmes',
					pageb_exp='$expop',
					pageb_scp='$scpop'
				");
			}

		}
		else{
		    echo "<script>console.log('".json_encode($bbaproduct)."')</script>";
            // exit;
			$DB->query("
				UPDATE $dbprefix"."pageb_copy SET 
				pageb_title='$title',
				pageb_lp='$lp',
				pageb_tp='$tp',
				pageb_bcode='$bcode',
				pageb_prcode='$prcode',
				pageb_oarcode='$oarcode',
				pageb_cdt='$cdt',
				pageb_expire='$expire',
				pageb_pr='$bbaproduct',
				pageb_fbp='$fbpixel',
				pageb_fbc='$fbcomment',
				pageb_fbcs='$fbcsnip',
				pageb_fbm='$fbmes',
				pageb_exp='$expop',
				pageb_scp='$scpop'
				WHERE pageb_id='$id'");
		}
		
		

        $_SESSION['msg'] = 'Your DFY Funnel has been successfully saved.';
		redirect("index.php?cmd=bba2");
	}
}

/*
$author_str="";
$res=$DB->query("select * from $dbprefix"."author where user_id='$UserID' order by author_id");
if(sizeof($res)){
	foreach($res as $row){
		$author_str.="<option value=\"".$row["author_id"]."\"".(($row["author_id"]==$author)?" selected":"").">".$row["author_fname"]." ".$row["author_lname"]." (".$row["author_email"].")</option>";
	}
}
*/

$pr_str="";
$res=$DB->query("select * from $dbprefix"."prb p LEFT JOIN $dbprefix"."pr pr ON p.pr_id=pr.pr_id order by prb_order ASC LIMIT 900");
if(sizeof($res)){
	foreach($res as $row){
		$prb_id=$row["prb_id"];
		$price=(double)($price_arr[$prb_id]?$price_arr[$prb_id]:$row["prb_price"]);

		$tip='<h5><b>'.$row["pr_title"].'</b></h5><img src="'.($row["pr_cover"]?$row["pr_cover"]:"../upload/pr/cover.gif").'" class="img-responsive ecover-img" /><div class="text-justify" style="font-size:12px;">'.str_replace("'","&#039;",str_replace("<p","<p style=\"font-size:12px;\"",$row["pr_desc"])).'</div>';
		$pr_str.="
			<li data-toggle='tooltip' data-html='true' title='$tip'>
				<div class=form-check inline-block d-inline-block' style='margin-top:7px; margin-right: 15px;'>
					<input type='checkbox' id='pr_$prb_id' name='id_arr[$prb_id]' value='$prb_id' class='form-check-input' ".(in_array($prb_id,$id_arr)?" checked":"").(preg_match("/;$PackID;/i",$row["prb_pack"])?"":" disabled")." /> 
					<label for='pr_$prb_id'> ". (strlen($row['prb_title']) > 35 ? substr($row['prb_title'],0, 32)."..." : $row['prb_title']) . "</label>
				</div>
				<div class=\"col-xs-3 pull-right\" style=\"margin-bottom:2px;\">
					<input type=\"text\" name=\"price_arr[$prb_id]\" value=\"".($price?number_format($price,2,".",""):"")."\" class=\"form-control\" style=\"max-width: 100px\" />
				</div>
				<div class=\"clearfix\"></div>
			</li>";
	}
}
?>
<div class="row">
<form method="post" class="col-md-12">

<?php
if($error){
?>
	<div class="col-md-12">
		<div class="alert alert-danger"><?php echo $error;?></div>
	</div>
<?php
}
?>

	<div class="col-md-12">
		<div class="card">
			<div class="header card-header">
				<h4 class="title" style="float:left;margin:5px 15px 15px 0;"><?php //echo $index_title;?><?= "Create Bonus Builder"; ?></h4>
				<a href="index.php?cmd=bba2"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill">Back to Bonus Builder App</div></a>
<?php
if($id){
?>
				<a href="index.php?cmd=bbaedit2"><div class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill" style="margin-left:10px;">Add New Bonus Builder App Funnel</div></a>
<?php
}
?>
			</div>
			<div class="content card-body">
				<div class="row">
					<div class="col-md-9">
						<div class="form-group">
							<input type="text" name="title" value="<?php echo $_POST["submit"]?slash($title):$title;?>" placeholder="Enter Page Title" class="form-control" />
						</div>
					</div>
<?php
if($error&&!$title){
?>
					<div class="red pull-left" style="margin-top:10px;" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></div>
<?php
}
?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-12">
		<div class="card">
			<div class="header card-header">
				<h4 class="title" style="float:left;margin:5px 15px 15px 0;">Custom Tokens/Shortcodes</h4>
				<button class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill collapsed" type="button" data-toggle="collapse" data-target="#token-table" aria-expanded="false" aria-controls="token-table">Click to <span class="showcoll">Show</span><span class="hidecoll">Hide</span></button>
			</div>
			<div class="clearfix"></div>
			<div class="content card-body collapse fade" id="token-table">
				<div class="row">
					<div class="col-md-6">
						<strong>Sales Page Required tokens:</strong><br />
						%link% - Affiliate Link URL<br /><br />
					
    					<strong>Affiliate Signup Link token:</strong><br />
                        %signup_link% - Affiliate Signup Link<br /><br />
<!--
						<strong>Thank You Page Required tokens:</strong><br />
						%form% - Autoresponder Form Position<br /><br />
-->
						<strong>Download Page Required tokens:</strong><br />
						%download% - Download Product Link Position<br />
						%license% - Download License Link Position<br /><br />
    						
    					<?php if(/*preg_match(";fbc;",$cur_pack["pack_ar"])*/0){ ?>
        					<strong>Facebook Comments token:</strong><br />
        						%fbcomments% - Facebook Comment display position<br />
    					<?php } ?>
					</div>
					<div class="col-md-6">
						<strong>Sales/Download Pages Optional tokens:</strong><br />
						%cddate% - Commission Page Expiry Date/Time<br />
						%num% - Bonus Order Number<br />
						%title% - Product Title<br />
						%summary% - Product Summary<br />
						%description% - Product Description<br />
						%cover% - Product eCover<br />
						%price% - Product Price<br />
						%total% - Total of Product Prices<br /><br />
						<strong>All Pages Optional tokens:</strong><br />
						%fname% - Author First Name<br />
						%lname% - Author Last Name<br />
						%email% - Author E-mail Address<br />
						%avatar% - Author Avatar<br />
						%date% - Current Date<br />
						%time% - Current Time<br />
						%cdtimer% - CountDown Timer Position
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-12">
		<div class="card">
			<div class="content card-body">
				<h4 class="title">Instructions</h4>
				<strong>Step 1:</strong> Click Here To Start/Edit Your Funnel &rArr; <a href="../load.php?t=2" class="fb" data-fancybox-type="iframe"><button class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill" type="button" style="margin:10px 0;">Load Sales/Download Pages Template</button></a>
				<br />
				<strong>Step 2:</strong> Click Settings Tab To Configure Your Funnel
				<br /><br />
				<strong>Step 3:</strong> Click Save All Edits Button at the Bottom of the Page
			</div>
		</div>
	</div>

	<div class="col-md-12 salespagesettings">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="nav-item active"><a href="#salespagecontent" class="nav-link active" aria-controls="home" role="tab" data-toggle="tab">Sales Page Content</a></li>
			<li role="presentation" class="nav-item"><a href="#downloadpagecontent" class="nav-link" aria-controls="profile" role="tab" data-toggle="tab">Download Page Content</a></li>
			<li role="presentation" class="nav-item"><a href="#settings" class="nav-link" aria-controls="profile" role="tab" data-toggle="tab">Settings</a></li>
		</ul>
		<div class="card">
			<div class="content nopadding card-body">
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="salespagecontent">
						<textarea id="lp" name="lp" class="tinymce"><?php echo $_POST["submit"]?slash($lp):$lp;?></textarea>
					</div>
					<div role="tabpanel" class="tab-pane" id="downloadpagecontent">
						<textarea id="tp" name="tp" class="tinymce"><?php echo $_POST["submit"]?slash($tp):$tp;?></textarea>
					</div>
					<div role="tabpanel" class="tab-pane" id="settings">
						<div class="content">
							<div class="row">
								<div class="col-md-6">
									<div class="card">
										<div class="content card-body">
											<h5 class="">Sales Page Settings</h5>
											<div class="form-group">
												<label for="bcode" style="float:left;margin-top:7px;">Affiliate Link URL</label>
												<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="This is were you add Your Affiliate Link for the Product you are promoting."><i class="fa fa-question" aria-hidden="true"></i></span>
<?php
if($error&&!$bcode){
?>
												<span class="red" data-toggle="tooltip" title="Required"><i class="fa fa-warning" aria-hidden="true"></i></span>
<?php
}
?>
												<input type="text" id="bcode" name="bcode" value="<?php echo $_POST["submit"]?slash($bcode):$bcode;?>" class="form-control" />
											</div>
										</div>
									</div>
								
									<div class="card">
										<div class="content card-body">
											<h5 class="">All Pages Settings</h5>
											<div class="form-group">
												<label for="expire" style="float:left;margin-top:7px;">Expiry Date/Time (optional)</label>
												<span class="blue info-tooltip" data-toggle="tooltip" data-html="true" title="Used for replacing %cddate% token (if any) and in built-in CountDown Timer (if any) on Sales Page."><i class="fa fa-question" aria-hidden="true"></i></span>
												<input type="text" id="expire" name="expire" value="<?php echo $expire?date("Y-m-d H:i:s",$expire):"";?>" placeholder="YYYY-MM-DD HH:MM:SS" class="form-control" />
											</div>
										</div>
									</div>
									
									<?php if(preg_match(";exp;",$cur_pack["pack_ar"])){?>
    									<div class="card">
    										<div class="content card-body">
    											<h5 class="">Exit Pop Settings</h5>
    											<div class="form-group">
    											    <select id="exp" name="exp" class="form-control">
    											        <option value="0" default>Select your Exit Pop</option>
    											        <?php foreach($exits as $key => $value){?>
    													<option value="<?=$value['exp_id']?>"><?=$value['exp_name']?></option>
    													<?php } ?>
    												</select>
    											</div>
    										</div>
    									</div>
									<?php } ?>
									
									<?php if(preg_match(";scp;",$cur_pack["pack_ar"])){?>
    									<div class="card">
    										<div class="content card-body">
    											<h5 class="">Social Proof Settings</h5>
    											<div class="form-group">
                                                    <input type="hidden" name="socs" id="socs" value="<?php echo $_POST["submit"]?slash($scpids):$scpids;?>">
    											    <a class="btn-scp btn text-white btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill" data-toggle="modal" data-target="#SocialProof">Select Social Proof Popups</a>
    											</div>
    										</div>
    									</div>
									<?php } ?>
									
									
								</div>
								<div class="col-md-6">
						
									
												<div class="card">
										<div class="content card-body">
											<h5 class="">Sales/Download Pages Settings</h5>
											<div class="form-group">
												<select id="bbaproduct" name="bbaproduct[]" class="form-control" multiple="multiple">
													<?php foreach($bbaproducts as $key => $bbaprod): ?>
                                                        <?php 
                                                            $tip = "<h5 class='text-white'><b>{$bbaprod["pr_title"]}</b></h5>
                                                                <img src='{$bbaprod["pr_cover"]}' class='img-responsive ecover-img' />
                                                                <p>".htmlspecialchars($bbaprod['pr_desc'])."</p>";
                                                        ?>

                                                        <?php if($bbaprod['pr_title'] == "") : ?>
                                                            <?php $bbaprod['pr_title'] = "Working progress"; ?>
                                                        <?php else : ?>
                                                            <option 
                                                                title="<?= $tip; ?>" 
                                                                value="<?= $bbaprod['prb_id'] ?>" 
                                                                <?= in_array($bbaprod['prb_id'], $bbaproduct) ? "selected" : "" ?> >

                                                                <?= $key + 1 ?> | <?= $bbaprod['pr_title']; ?>
                                                            </option>
                                                        <?php endif; ?>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
									</div>


									<?php if(preg_match(";fbp;",$cur_pack["pack_ar"]) || preg_match(";fbc;",$cur_pack["pack_ar"]) || preg_match(";fbm;",$cur_pack["pack_ar"])){?>
										<div class="card">
											<div class="content card-body">
												<h5 class="">Facebook Tools</h5>
												<?php if(preg_match(";fbp;",$cur_pack["pack_ar"])){?>
												<div class="form-group">
													<label for="fbp" style="float:left;margin-top:7px;">Facebook Pixel Code</label>
													<div class="custom-control custom-checkbox pull-right">
														<span class="blue info-tooltip" data-placement="right" data-toggle="tooltip" data-html="true" title="" data-original-title="Use the configured settings under DFY Funnerls Tools > Facebook Tools > Facebook Pixel."><i class="fa fa-question" aria-hidden="true"></i></span>  
														<label class="custom-control-label" for="fbpd">Use default setting </label>
														<input type="checkbox" name="fbpd" id="fbpd" value="1">
													</div>
													<span class="blue info-tooltip" data-placement="right" data-toggle="tooltip" data-html="true" title="" data-original-title="Paste your Facebook Pixel Tracking code."><i class="fa fa-question" aria-hidden="true"></i></span>
													<textarea id="fbp" name="fbp" rows="4" placeholder="Enter your Facebook Pixel code here" class="form-control"><?php echo $_POST["submit"]?slash($fbpcode):$fbpcode;?></textarea>
												</div>
												<?php } ?>
												<?php if(/*preg_match(";fbc;",$cur_pack["pack_ar"])*/0){?>
												<div class="form-group">
													<label for="fbc" style="float:left;margin-top:7px;">Facebook Comments SDK code</label>
													<div class="custom-control custom-checkbox pull-right">
														<span class="blue info-tooltip" data-placement="right" data-toggle="tooltip" data-html="true" title="" data-original-title="Use the configured settings under DFY Funnerls Tools > Facebook Tools > Facebook Comments Plugin."><i class="fa fa-question" aria-hidden="true"></i></span>  
														<label class="custom-control-label" for="fbcd">Use default setting</label>
														<input type="checkbox" name="fbcd" id="fbcd" value="1">
													</div>
													<span class="blue info-tooltip" data-placement="right" data-toggle="tooltip" data-html="true" title="" data-original-title="Paste Javascript SDK code from Facebook to integrate Facebook Comment Plugin."><i class="fa fa-question" aria-hidden="true"></i></span>
													<textarea id="fbc" name="fbc" rows="4" placeholder="Enter Javascript SDK code from Facebook." class="form-control"><?php echo $_POST["submit"]?slash($fbccode):$fbccode;?></textarea>
												</div>
												<div class="form-group">
													<label for="fbcs" style="float:left;margin-top:7px;">Facebook Comments code snippet</label>
													<div class="custom-control custom-checkbox pull-right">
													</div>
													<span class="blue info-tooltip" data-placement="right" data-toggle="tooltip" data-html="true" title="" data-original-title="Paste code snippet from Facebook to integrate Facebook Comment Plugin."><i class="fa fa-question" aria-hidden="true"></i></span>
													<textarea id="fbcs" name="fbcs" rows="4" placeholder="Enter Javascript code snippet from Facebook." class="form-control"><?php echo $_POST["submit"]?slash($fbcscode):$fbcscode;?></textarea>
												</div>
												<?php } ?>
												<?php if(preg_match(";fbm;",$cur_pack["pack_ar"])){?>
												<div class="form-group">
													<label for="fbm" style="float:left;margin-top:7px;">Facebook Chat Widget code</label>
													<div class="custom-control custom-checkbox pull-right">
														<span class="blue info-tooltip" data-placement="right" data-toggle="tooltip" data-html="true" title="" data-original-title="Use the configured settings under DFY Funnerls Tools > Facebook Tools > Facebook Chat Widget."><i class="fa fa-question" aria-hidden="true"></i></span>  
													<label class="custom-control-label" for="fbmd">Use default setting</label>
													<input type="checkbox" name="fbmd" id="fbmd" value="1">
													</div>
													<span class="blue info-tooltip" data-placement="right" data-toggle="tooltip" data-html="true" title="" data-html="true" data-trigger="click" data-original-title="Paste code snippet from Facebook to integrate Facebook Chat Widget. Here is a guide to setup your customer chat plugin <a href='https://developers.facebook.com/docs/messenger-platform/discovery/customer-chat-plugin/#steps' target='_blank'>HERE</a>"><i class="fa fa-question" aria-hidden="true"></i></span>
													<textarea id="fbm" name="fbm" rows="4" placeholder="Enter Javascript code snippet from Facebook." class="form-control"><?php echo $_POST["submit"]?slash($fbmcode):$fbmcode;?></textarea>
												</div>
												<?php } ?>
											</div>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-12" style="margin-bottom:25px;">
		<input type="submit" name="submit" value="Save All Edits" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill pull-right" />
	</div>

</form>
</div>

<div id="SocialProof" class="modal fade" role="dialog" data-backdrop="false" style="background-color: rgba(0,0,0,.5);">
  <div class="modal-dialog" style="width: 80%;">

    <!-- Modal content-->
    <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Select your active Social Proof Popups</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-md-12">
					    <div class="form-group">
                            <label class="bmd-label-floating">Social Proof</label>
                            <select id="scps" class="form-control">
                                <option value="0">Select Social Proof</option>
                                <?php foreach($socials as $key => $value){ if(!in_array($value['scp_id'],$scpid)) { ?>
                                <option value="<?=$value['scp_id']?>"><?=$value['scp_title']?></option>
                                <?php }} ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <code>Drag your social proofs to reorder</code>
                        </div>
                        <div class="table-responsive">
                                <table class="table table-fixed text-center" id="pop-list">
                                    <thead class="">
                                        <tr>
                                            <th class="text-center">Title</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listscp">
                                        <?php foreach($socials as $key => $value){ if(in_array($value['scp_id'],$scpid)) { ?>
                                        <tr data-id="<?=$value['scp_id']?>">
                                            <td><?=$value['scp_title']?></td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <div class="btn-group btn-group-sm" role="group" aria-label="SCP actions">
                                                        <button type="button" class="btn btn-danger btn-fill" data-name="<?=$value['scp_title']?>" data-id="<?=$value['scp_id']?>" onclick="remove(this);">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php }} ?>
                                    </tbody>

                                </table>
                            </div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger btn-fill" data-dismiss="modal">Cancel</button>
				<a name="addSocialProof" class="btn save-scp btn-<?php echo $WEBSITE["theme_btn"];?> text-white btn-fill">Save</a>
			</div>
    </div>

  </div>
</div>

<script src="../tinymce/tinymce.min.js"></script>
<!--Search Function -->
<script>
	function myFunction() {
		var input, filter, ul, li, a, i, txtValue;
		input = document.getElementById("myInputProduct");
		filter = input.value.toUpperCase();
		ul = document.getElementById("myUL");
		li = ul.getElementsByTagName("li");

		for (i = 0; i < li.length; i++) {
			a = li[i].getElementsByTagName("label")[0];
			txtValue = a.textContent || a.innerText;
			if (txtValue.toUpperCase().indexOf(filter) > -1) {
				li[i].style.display = "";
			} else {
				li[i].style.display = "none";
			}
		}
	}
</script>
<script>
$('body').on('click', '#fbpd', function() {
    if($('#fbpd').is(':checked')){
        $('#fbpd').val('1');
        $('textarea#fbp').val('');
        $('textarea#fbp').attr('readonly', 'true');
    }else{
        $('#fbpd').val('0');
        $('textarea#fbp').removeAttr('readonly');
    }
});
$('body').on('click', '#fbcd', function() {
    if($('#fbcd').is(':checked')){
        $('#fbcd').val('1');
        $('textarea#fbc').val('');
        $('textarea#fbc').attr('readonly', 'true');
        $('textarea#fbcs').val('');
        $('textarea#fbcs').attr('readonly', 'true');
    }else{
        $('#fbcd').val('0');
        $('textarea#fbc').removeAttr('readonly');
        $('textarea#fbcs').removeAttr('readonly');
    }
});
$('body').on('click', '#fbmd', function() {
    if($('#fbmd').is(':checked')){
        $('#fbmd').val('1');
        $('textarea#fbm').val('');
        $('textarea#fbm').attr('readonly', 'true');
    }else{
        $('#fbmd').val('0');
        $('textarea#fbm').removeAttr('readonly');
    }
});
jQuery(document).ready(function($){
    $('#exp').val(<?=$expid?>);
    $('#topbar').val(<?=$topbar?>);

var lp_top=$("#lp").offset().top-150;
$(".fb").fancybox({
	maxWidth: 800,
	maxHeight: 600,
	autoSize: false,
	beforeClose: function(){
		var lp_val=$(".fancybox-iframe").contents().find("#lp").val();
//		var op_val=$(".fancybox-iframe").contents().find("#op").val();
		var tp_val=$(".fancybox-iframe").contents().find("#tp").val();
		if(lp_val&&tp_val){
			tinymce.get("lp").setContent(lp_val);
//			tinymce.get("op").setContent(op_val);
			tinymce.get("tp").setContent(tp_val);
		}
	},
	afterClose: function(){
		$("html,body").animate({scrollTop:lp_top},500);
	}
});

tinymce.init({
selector:".tinymce",
height:400,
theme:"modern",

plugins:["advlist autolink lists link image media emoticons charmap preview hr anchor searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking save table contextmenu directionality paste textcolor colorpicker fullpage"],

toolbar1:"fontselect | fontsizeselect | forecolor backcolor | bold italic underline strikethrough | removeformat",
toolbar2:"alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media emoticons | code fullpage preview",

fontsize_formats:"8px 9px 10px 11px 12px 13px 14px 15px 16px 18px 20px 22px 24px 26px 28px 36px 48px 72px",

relative_urls:false,
remove_script_host:false,

external_filemanager_path:"../tinymce_fm/",
external_plugins:{"filemanager":"../tinymce_fm/plugin.min.js"},
filemanager_title:"File Manager",
filemanager_sort_by:"name",
filemanager_descending:true,

valid_elements:"*[*]"
});

$("#expire").datetimepicker({dateFormat:"yy-mm-dd",timeFormat:"HH:mm:ss"});

});

function remove(element){
    var option = $(element);
    var optionHtml = '<option value="'+$(element).attr('data-id')+'">'+$(element).attr('data-name')+'</option>';
    $('select#scps').append(optionHtml);
    option.parent().parent().parent().parent().remove();
}
$(document).ready(function() {
    $('#pop-list tbody').sortable();
});

$('select#scps').change(function(){
    var option = $(this).find(':selected');
    var optionHtml = '<tr data-id="'+option.val()+'">';
    optionHtml += '<td>'+option.text()+'</td>';
    optionHtml += '<td><div class="btn-group" role="group" aria-label="Basic example">';
    optionHtml += '<div class="btn-group btn-group-sm" role="group" aria-label="SCP actions">';
    optionHtml += '<button type="button" class="btn btn-danger btn-fill" data-name="'+option.text()+'" data-id="'+option.val()+'" onclick="remove(this);"><i class="fa fa-times"></i></button>';
    optionHtml += '</div></div></td></tr>';
    $('#listscp').append(optionHtml);
    option.remove();
});

$('.save-scp').click(function(){
    var socs = [];
    $('#listscp tr').each(function(i){
        socs.push($(this).attr('data-id'));
    });
    $('#socs').val(socs.toString());
    $('#SocialProof').modal('hide');
});

$(window).on('load', function () {
	$('#bbaproduct').select2({
        tags: true,
	});

	$('#bbaproduct').on('select2:select select2:closing', function (e) {
		$(".select2-results__option").tooltip('dispose');
		$('.tooltip').remove();
	});

	$('body').on('mouseenter', '.select2-results__option.select2-results__option--highlighted', function (e) {
		$(this).tooltip({
			title: $(this).attr('title'),
			html: true,
			placement: 'auto',
			
		}).tooltip('show');
	});

	$('body').on('mouseleave', '.select2-results__option.select2-results__option--highlighted', function (e) {
		$(".select2-results__option").tooltip('dispose');
		$('.tooltip').remove();
	});

	$('body').on('click', '.select2-results__option.select2-results__option--highlighted', function (e) {
		$(".select2-results__option").tooltip('dispose');
		$('.tooltip').remove();
	});

	
})

$(document).ready(function(){
	$(window).scroll(function () {
		$(".select2-results__option").tooltip('dispose');
		$('.tooltip').remove();
	});
});

</script>

<?php
}
?>
<?php include('../../cache_solution/bottom-cache-v2.php'); ?>