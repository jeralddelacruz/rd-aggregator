<?php
	if($_GET["move"]){
		$DB->move("tpl",$_GET["move"]);
		redirect("index.php?cmd=tpl");
	}
	elseif($_GET["del"]){
		tpl_del($_GET["del"]);
		redirect("index.php?cmd=tpl");
	}elseif (isset($_POST['type'])){
		$type = strip($_POST['type']);
		$pack_arr = implode(';', $_POST['pack']);
		$pack=";". $pack_arr .";";
		$cb_templates = strip($_POST['cb_templates']);
		
		if($type == 'apply_memberships'){
			$DB->query("update $dbprefix"."tpl set tpl_pack='$pack' where tpl_order in ($cb_templates)");
		}

		redirect("index.php?cmd=tpl");
	}
	
	$prb_arr=$DB->get_pack();
	$res=$DB->query("select * from $dbprefix"."tpl order by tpl_order");
?>
<br><br>
<form method="post" id="cbtform">
	<h2>Apply Membership/s or Prices on CB Templates</h2>
	<br>

	<div class="form-group">
		<label for="">Select CB Templates Ids</label>
		<input type="number" class="text_s" name="cb_templates_from" min="1" max="<?php echo sizeof($res); ?>" placeholder="From" >
		<input type="number" class="text_s" name="cb_templates_to" min="1" max="<?php echo sizeof($res); ?>" placeholder="To" >
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

	<div class="form-group">
		<input type="hidden" name="cb_templates">
		<input type="hidden" name="type" value="">
		<input type="submit" name="apply_memberships" value="Apply Membership/s" class="button apply" />
	</div>
</form>
<br><br>
<h2><?php echo $index_title;?><a href="index.php?cmd=tpledit" class="add">Add New</a></h2>
<table class="tbl_list">
	<tr>
		<th class="w50 ac">#</th>
		<th class="w50 ac"></th>
		<th class="w200 ac">Screenshot</th>
		<th>Title</th>
		<th class="w150 ac">Available For</th>
		<th class="w150 ac">Sales Page</th>
<!--
		<th class="w150 ac">Thank You Page</th>
-->
		<th class="w150 ac">Download Page</th>
		<th class="w50 ac"><img src="../img/help.png" title="DFY Template" class="help" /></th>
		<th class="w50 ac">Order</th>
		<th class="w50 ac">Action</th>
	</tr>
	<?php
	if(sizeof($res)){
		$i=1;
		foreach($res as $row){
			$id=$row["tpl_id"];
			$title=$row["tpl_title"];
			$src=is_file("../upload/tpl/$id/thumb.png")?"../upload/tpl/$id/thumb.png":"../upload/tpl/thumb.jpg";
			$thumb="<a href=\"$src\" title=\"$title\" class=\"view tip fb\" rel=\"gal\"><img src=\"$src\" style=\"max-width:180px;\" /></a>";
			$show="<img src=\"../img/".strtolower($YN_ARR[$row["tpl_show"]]).".png\" title=\"".$YN_ARR[$row["tpl_show"]]."\" class=\"tip\" />";

			$pack_arr=split(";",trim($row["tpl_pack"],";"));
			$pack="";
			foreach($pack_arr as $v){
				if($prb_arr[$v]){
					$pack.=$prb_arr[$v].", ";
				}
			}
			$pack=substr($pack,0,strlen($pack)-2);
			$pack="<img src=\"../img/help.png\" title=\"$pack\" class=\"help\" />";
	?>
	<tr>
		<td class="ac"><?php echo $i;?></td>
		<td class="ac">
			<input type="checkbox" name="multi-select" id="">
		</td>
		<td class="ac"><?php echo $thumb;?></td>
		<td><?php echo $title;?></td>
		<td class="ac"><?php echo $pack;?></td>
		<td class="ac"><a href="../upload/tpl/<?php echo $id;?>/" target="_blank" title="<?php echo $title;?> Sales Page" class="tip">Preview</a></td>
<!--
		<td class="ac"><?php if(file_exists("../upload/tpl/$id/thankyou.html")){?><a href="../upload/tpl/<?php echo $id;?>/thankyou.html" target="_blank" title="<?php echo $title;?> Thank You Page" class="tip">Preview</a><?php }?></td>
-->
		<td class="ac"><?php if(file_exists("../upload/tpl/$id/download.html")){?><a href="../upload/tpl/<?php echo $id;?>/download.html" target="_blank" title="<?php echo $title;?> Download Page" class="tip">Preview</a><?php }?></td>
		<td class="ac"><?php echo $show;?></td>
		<td><?php if($i>1){?><a href="index.php?cmd=tpl&move=u<?php echo $id;?>" title="Move Up" class="tip"><img src="../img/oup.gif" /></a><?php }?><?php if($i<$num){?><a href="index.php?cmd=tpl&move=d<?php echo $id;?>" title="Move Down" class="tip"><img src="../img/odown.gif" align="right" /></a><?php }?></td>
		<td class="ac"><a href="index.php?cmd=tpledit&id=<?php echo $id;?>" title="Edit" class="tip"><img src="../img/edit.png" /></a> <a href="index.php?cmd=tpl&del=<?php echo $id;?>" title="Delete" class="tip" onclick="return confirm('Are you sure you wish to delete this Template?');"><img src="../img/del.png" /></a></td>
	</tr>
<?php
		$i++;
	}
}
?>
</table>
<script>
jQuery(document).ready(function($){
	$(".fb").fancybox({});
	
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
	
	$('input[name=cb_templates_from], input[name=cb_templates_to]').on('keyup', function(){
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

	$('input[name=cb_templates_from], input[name=cb_templates_to]').on('change', function(){
		var from = $('input[name=cb_templates_from]');
		var to = $('input[name=cb_templates_to]');
		var ids = [];

		if(parseInt(from.val()) && parseInt(to.val())){
			$('input[name=multi-select]').prop('checked', false);

			if(parseInt(from.val()) <= parseInt(to.val())){
				$('.apply').removeAttr('disabled');
					
				$('tr').each(function(index, value){
					var tdVal = parseInt($($(this).find('td')[0]).text());

					if(tdVal >= from.val() && tdVal <= to.val()){
						$($(this).find('td')[1]).find('input').prop('checked', true);
						ids.push(tdVal);
					}
				})
				$('input[name=cb_templates]').val(ids.join(','))
			}else{
				$('input[name=cb_templates]').val('')
				$('.apply').attr('disabled', 'disabled');
				alert('Invalid Range');
			}
		}
	})

	$('input[name=apply_memberships]').on('click', function(e){
		e.preventDefault();
	
		if($('input[name=multi-select]:checked').length <= 0){
			alert('Please select atleast one template.');
		}else if($('input[name="pack[]"]:checked').length <= 0){
			alert('Please select atleast one membership.');
		}else{
			$('input[name=type]').val('apply_memberships');
			$('#cbtform').submit();
		}
	})
});
</script>