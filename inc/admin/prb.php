<?php
	if($_POST["submit"]){
		$lic=strip($_POST["lic"]);
		$mlic=strip($_POST["mlic"]);
		$DB->query("update $dbprefix"."setup set setup_val='$lic' where setup_key='cb_lic'");
		$DB->query("update $dbprefix"."setup set setup_val='$mlic' where setup_key='cb_mlic'");
		redirect("index.php?cmd=prb");
	}elseif(isset($_POST['type'])){
		$type = strip($_POST['type']);
		$pack_arr = implode(';', $_POST['pack']);
		$pack=";". $pack_arr .";";
		$cb_products = strip($_POST['cb_products']);

		if($type == 'apply_memberships'){
			$DB->query("update $dbprefix"."prb set prb_pack='$pack' where prb_id in ($cb_products)");
		}else{
			$price_from = number_format((double)$_POST["price_from"], 2, '.', '');
			$price_to = number_format((double)$_POST["price_to"], 2, '.', '');
			$res=$DB->query("select * from $dbprefix"."prb where prb_id in ($cb_products)");

			foreach ($res as $row) {
				$id=$row["prb_id"];
				$price = number_format((double)rand($price_from, $price_to), 2, '.', '');
				$DB->query("update $dbprefix"."prb set prb_price='$price' where prb_id='$id'");
			}
		}

		redirect("index.php?cmd=prb");
	}elseif($_GET["move"]){
		$DB->move("prb",$_GET["move"]);
		redirect("index.php?cmd=prb");
	}elseif($_GET["del"]){
		prb_del($_GET["del"]);
		redirect("index.php?cmd=prb");
	}

	$prb_arr=$DB->get_pack();
	$res=$DB->query("select * from $dbprefix"."prb p LEFT JOIN $dbprefix"."pr pr ON p.pr_id=pr.pr_id order by prb_title");
	
    // Added Code 09.21.2020
    // 	ENABLE FOR UPLOAD
// 	$getUploadedPLRs=$DB->query("select * from $dbprefix"."pr");
// 	$getUsedPLRs=$DB->query("select * from $dbprefix"."prb");
	
// 	$singleArrUploadedPLRs= array();
// 	$singleArrUsedPLRs= array();
	
// 	ENABLE FOR UPLOAD
// 	foreach($getUploadedPLRs as $UploadedPLRKey => $UploadedPLRValue){
// 	    //print_r($PLR['pr_id'] . "<br />");
// 	   array_push($singleArrUploadedPLRs, $UploadedPLRValue['pr_id']);
// 	}

// 	ENABLE FOR UPLOAD	
// 	foreach($getUsedPLRs as $UsedPLRKey => $UsedPLRValue){
// 	   array_push($singleArrUsedPLRs, $UsedPLRValue['pr_id']);
// 	}
	
// 	Check if has duplicates
// 	print_r(sizeof(array_unique($singleArrUsedPLRs)));
// 	echo "<br />";
	
	// 	ENABLE FOR UPLOAD
// 	$arrayDiffOfUploadedAndUsed = array_diff($singleArrUploadedPLRs, $singleArrUsedPLRs);
	
// 	print_r("Pulled PLRs: " . sizeof($singleArrUploadedPLRs));
// 	echo "<br />";
// 	print_r("Current PLRs: " . sizeof($singleArrUsedPLRs));
// 	echo "<br />";
//  print_r("Total IDs not used: " . sizeof($arrayDiffOfUploadedAndUsed));
//  echo "<br />";
//  $implodedID = implode(", ", $arrayDiffOfUploadedAndUsed);
    
    // echo $implodedID;
    // echo "<br />";
    // Check ID to insert
    // if(empty($implodedID)){
    //     echo "Empty";
    //     echo "<br />";
    // }
    // else{
    //     echo "Not empty";
    //     echo "<br />";
    // }
	
// 	ENABLE FOR UPLOAD	
// 	$getPLRToInsert = $DB->query("select * from $dbprefix"."pr where pr_id in ($implodedID)");
	
// 	$prodCount = 1;
// 	For Reports
// 	foreach($getPLRToInsert as $forTracking){
// 	    echo "#" . $prodCount++ . ": " . $forTracking['pr_id'] . " | " . $forTracking['pr_pid'] . " | " . $forTracking['pr_title'] . " | " . $forTracking['pr_desc'] . " | " . $forTracking['pr_body'] . " | " . $forTracking['pr_cloud'] . " | " . $forTracking['pr_url'] . " | " . $forTracking['pr_cover'];
// 	    echo "<br />";
// 	    echo "<br />";
// 	}
	
// 	ENABLE FOR UPLOAD
// 	foreach($getPLRToInsert as $getPLRToInsertValue){
// 	    $prbID = $DB->getauto("prb");
//     	$PLROrder = $DB->getmaxval("prb_order","prb")+1;
//     	$packageArray = array("11","13","10","12","15","17","14","16","19","21","18","20","5","4");
//     	$package = ";".implode(";",$packageArray).";";
//     	$PLRPrice = rand(7, 57);
//     	$PLRPrice = number_format($PLRPrice,2,".","");
//     	$pText = "Download Now";
//     	$lText = "Download License";
// 	    $DB->query("insert into $dbprefix"."prb set prb_id='$prbID', pr_id='". $getPLRToInsertValue['pr_id'] ."',prb_pack='$package',prb_title='". $getPLRToInsertValue['pr_title'] ."',prb_price='$PLRPrice',prb_ptext='$pText',prb_ltext='$lText',prb_order='$PLROrder'");
// 	}
?>
<script>
    // function riprish(){
    //     location.reload();
    // }
</script>
<button type="button" onclick="riprish();" style="padding: 2px">Upload pulled products</button>
<br /><br />
<h2><?php echo $index_title;?><a href="index.php?cmd=prbedit" class="add">Add New</a></h2>
<form method="post">
	<div>
		<span class="large i"><label for="lic">ATL Personal Use License URL</label></span> <input type="text" id="lic" name="lic" value="<?php echo $WEBSITE["cb_lic"];?>" class="text_l" />
		<br />
		<span class="large i"><label for="mlic">ATL Product License URL</label></span> <input type="text" id="mlic" name="mlic" value="<?php echo $WEBSITE["cb_mlic"];?>" class="text_l" />
		<input type="submit" name="submit" value="Save Changes" class="button" />
	</div>
</form>
<br><br>
<form method="post" id="cbpform">
	<h2>Apply Membership/s or Prices on CB Products</h2>
	<br>

	<div class="form-group">
		<label for="">Select CB Product Ids</label>
		<input type="number" class="text_s" name="cb_products_from" min="1" max="<?php echo $res[sizeof($res)-1]['prb_id']; ?>" placeholder="From" >
		<input type="number" class="text_s" name="cb_products_to" min="1" max="<?php echo $res[sizeof($res)-1]['prb_id']; ?>" placeholder="To" >
	</div>
	<br>
	
	<div class="form-group">
		<label for="">Select CP Product Ids Type</label>
		<select name="ids_type" id="" class="text_s">
			<option value="all" selected>All</option>
			<option value="even">Even</option>
			<option value="odd">Odd</option>
		</select>
	</div>
	<br>
	
	<div class="form-group">
		<label for="">Available For <img src="../img/help.png" title="Choose Membership(s) the Product is available for." class="help" /></label>
	</div>
	<br>

	<div class="form-group">
		<input type="checkbox" id="chk" /> <label id="chk_l" for="chk">Check All</label>
	</div>
	<br>
	<div class="form-group">
		<?php if(sizeof($prb_arr)){
			foreach($prb_arr as $k=>$v){ ?>
				<input type="checkbox" name="pack[]" id="pack<?php echo $k;?>" value="<?php echo $k;?>"<?php echo (in_array($k,$pack_arr)?" checked":"");?> class="chk" /> <label for="pack<?php echo $k;?>"><?php echo $v;?></label><br />
			<?php }
		} ?>
	</div>
	<br>

	<div class="form-group">
		<label for="">Price Range</label>
		<input type="number" class="text_s" name="price_from" placeholder="From" >
		<input type="number" class="text_s" name="price_to" placeholder="To" >
	</div>
	<br>

	<div class="form-group">
		<input type="hidden" name="cb_products">
		<input type="hidden" name="type" value="">
		<input type="submit" name="apply_memberships" value="Apply Membership/s" class="button apply" />
		<input type="submit" name="apply_prices" value="Apply Prices" class="button apply" />
	</div>
</form>
<br />
<table class="tbl_list">
	<tr>
		<th class="w50 ac">#</th>
		<th class="w50 ac"></th>
		<th>Title <span class="desc">(mouse over the Title to <strong>View</strong> the Product details)</span></th>
		<th class="w150 ac">Available for</th>
		<th class="w75">Price</th>
		<th class="w50 ac">Order</th>
		<th class="w75 ac">Action</th>
	</tr>
<?php
if(sizeof($res)){
	$i=1;
	foreach($res as $row){
		$id=$row["prb_id"];
		$pr_id=$row["pr_id"];
		$title=$row["prb_title"];
		$price=$row["prb_price"]?("$".number_format($row["prb_price"],2,".","")):"N/A";

		$pack_arr=explode(";",trim($row["prb_pack"],";"));
		$pack="";
		foreach($pack_arr as $v){
			if($prb_arr[$v]){
				$pack.=$prb_arr[$v].", ";
			}
		}
		$pack=substr($pack,0,strlen($pack)-2);
		$pack="<img src=\"../img/help.png\" title=\"$pack\" class=\"help\" />";

		$cover="<img src=\"".($row["pr_cover"]?$row["pr_cover"]:"../upload/pr/cover.gif")."\" class=\"wso-item-thumb\" />";
		$desc=str_replace("'","&#039;",$row["pr_desc"]);
		$tip="<div class=\"wso-item\"><p class=\"b\">".$row["pr_title"]."</p>$cover<div class=\"al\">$desc</div></div>";

		$dld=$row["pr_cloud"]?$row["pr_cloud"]:$row["pr_url"];
?>
	<tr>
		<td class="ac"><?php echo $i;?></td>
		<td class="ac">
			<input type="checkbox" name="multi-select" id="">
		</td>
		<td><a href="#" title='<?php echo $tip;?>' class="tip"><?php echo $title;?></a></td>
		<td class="ac"><?php echo $pack;?></td>
		<td><?php echo $price;?></td>
		<td><?php if($i>1){?><a href="index.php?cmd=prb&move=u<?php echo $id;?>" title="Move Up" class="tip"><img src="../img/oup.gif" /></a><?php }?><?php if($i<$num){?><a href="index.php?cmd=prb&move=d<?php echo $id;?>" title="Move Down" class="tip"><img src="../img/odown.gif" align="right" /></a><?php }?></td>
		<td class="ac"><a href="<?php echo $dld;?>" target="_blank" title="Download Product" class="tip"><img src="../img/dld.png" /></a> <a href="index.php?cmd=prbedit&id=<?php echo $id;?>" title="Edit" class="tip"><img src="../img/edit.png" /></a> <a href="index.php?cmd=prb&del=<?php echo $id;?>" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete this Product?');"><img src="../img/del.png" /></a></td>
	</tr>
<?php
		$i++;
	}
}
?>
</table>
<script>
	jQuery(document).ready(function($){
		$("#pr").on("change",function(){
			var selected=$(this).val();

			if(selected!=0){
				$("#title").val($("#pr option:selected").text());
			}
		});

		$("#chk").click(function(){
			var chk=$(this).prop("checked");
			$(".chk").prop("checked",chk);
			$("#chk_l").text(chk==true?"Uncheck All":"Check All");
		});

	});

	$('input[name=cb_products_from], input[name=cb_products_to]').on('keyup', function(){
		if($(this).val()){
			var min = parseInt($(this).attr('min'))
			var max = parseInt($(this).attr('max'))
			var currentVal = parseInt($(this).val());

			if(currentVal < min)
				$(this).val(Math.abs(min))
			else if(currentVal > max)
				$(this).val(Math.abs(max))
		}
	});

	$('input[name=cb_products_from], input[name=cb_products_to], select[name=ids_type]').on('change', function(){
		var from = $('input[name=cb_products_from]');
		var to = $('input[name=cb_products_to]');
		var ids = [];
		var type = $('select[name=ids_type]').find(':selected');

		if(from.val() && to.val()){
			$('input[name=multi-select]').prop('checked', false);

			if(parseInt(from.val()) <= parseInt(to.val())){
				$('.apply').removeAttr('disabled');
					
				$('tr').each(function(index, value){
					var tdVal = parseInt($($(this).find('td')[0]).text());

					if(tdVal >= from.val() && tdVal <= to.val()){
						if(type.val() == 'all'){
							$($(this).find('td')[1]).find('input').prop('checked', true);
							ids.push(tdVal);
						}else if(type.val() == 'even' && (tdVal % 2 == 0)){
							$($(this).find('td')[1]).find('input').prop('checked', true);
							ids.push(tdVal);
						}else if(type.val() == 'odd' && (tdVal % 2 !== 0)){
							$($(this).find('td')[1]).find('input').prop('checked', true);
							ids.push(tdVal);
						}
					}
				})
				$('input[name=cb_products]').val(ids.join(','))
			}else{
				$('input[name=cb_products]').val('')
				$('.apply').attr('disabled', 'disabled');
				alert('Invalid Range');
			}
		}
	})

	$('input[name=apply_memberships]').on('click', function(e){
		e.preventDefault();
	
		if($('input[name=multi-select]:checked').length <= 0){
			alert('Please select atleast one product.');
		}else if($('input[name="pack[]"]:checked').length <= 0){
			alert('Please select atleast one membership.');
		}else{
			$('input[name=type]').val('apply_memberships');
			$('#cbpform').submit();
		}
	})
	
	$('input[name=apply_prices]').on('click', function(e){
		e.preventDefault();

		var fromPrice = $('input[name=price_from]');
		var toPrice = $('input[name=price_to]');
		
		if(fromPrice.val() && toPrice.val()){
			if(parseInt(fromPrice.val()) >= parseInt(toPrice.val())){
				alert('Invalid price range');
			}else{
				$('input[name=type]').val('apply_prices');
				$('#cbpform').submit();
			}

			
		}else{
			alert('Invalid price range');
		}
	})
</script>