<?php
$menu_arr=unserialize($WEBSITE["menu"]);

if(sizeof($menu_arr)<2){
    $menu_arr=$USER_MENU;
}
else{
	foreach($USER_MENU as $k=>$arr){
		if(!in_array($k,array_keys($menu_arr))){
            $menu_arr[$k]=$arr;
		}
	}
}

$tag_arr=array();
$res=$DB->query("select * from $dbprefix"."page where page_tmenu='1' order by page_order");
if(sizeof($res)){
	foreach($res as $row){
		if(!in_array($row["page_slug"],array_keys($menu_arr))){
			$menu_arr[$row["page_slug"]]=array($row["page_title"],"pe-7s-menu", $row['in_left'], $row['in_right']);
		}
		$tag_arr[]=$row["page_slug"];
	}
}

if($_POST["submit"]){
	$slug_arr=$_POST["slug_arr"];
	$icon_arr=$_POST["icon_arr"];
	$title_arr=$_POST["title_arr"];
	$sub_menu=$_POST["sub_menu"];
	// $in_left_arr=$_POST["in_left"];
    // $in_right_arr=$_POST["in_right"];
    
    print("<pre>".print_r($in_left_arr,true)."</pre>");  
	$arr=array();
	foreach($slug_arr as $k=>$slug){
		$icon=slash(strip($icon_arr[$k]));
        $icon=$icon?$icon:$menu_arr[$slug][1];
        
		$title=slash(strip($title_arr[$k]));
        $title=$title?$title:$menu_arr[$slug][0];

        $inleft= $_POST["in_left_{$slug}"] ? 'true' : 'false';
        $inleft=$inleft?$inleft:$menu_arr[$slug][2];

        $inright= $_POST["in_right_{$slug}"] ? 'true' : 'false';
        $inright=$inright?$inright:$menu_arr[$slug][3];

        $parentMenu = $sub_menu[$k];
        $hasChild = false;

        foreach( $slug_arr as $j => $slg ){
            if ($sub_menu[$j] == $slug) {
                $hasChild = true;
                break;
            }
        }

        // foreach( $slug_arr as $j => $slg ){
        //     if ($sub_menu[$j] == $slug) {
        //         $title=slash(strip($title_arr[$j]));
        //         $title=$title?$title:$menu_arr[$slg][0];

        //         $icon=slash(strip($icon_arr[$j]));
        //         $icon=$icon?$icon:$menu_arr[$slg][1];

        //         $submenus[] = [
        //             $title,
        //             $icon,
        //             $slg,
        //         ];
        //     }
        // }


		$arr[$slug]=array($title, $icon, $inleft, $inright, $parentMenu, $hasChild); //title, icon, in left, in right
    }
    
    $DB->query("update $dbprefix"."setup set setup_val='".addslashes(serialize($arr))."' where setup_key='menu'");

	redirect("index.php?cmd=menu");
}
?>
<h2><?php echo $index_title;?></h2>
<p class="desc">Drag & Drop table rows to change the Menu items order. <a href="http://themes-pixeden.com/font-demos/7-stroke/" target="_blanks">Icons list can be found here</a>. Press button at the bottom to <b>Save Changes</b>.</p>
<form method="post">
<table class="tbl_list">
	<tr>
		<th class="w100 ac">Current #</th>
		<th>Menu SLUG</th>
		<th>Menu Icon</th>
		<th>Menu Title</th>
		<th><input type="checkbox" name="in_left_all" class="checkbox" style="vertical-align: middle;" /> Left Sidebar</th>
		<th><input type="checkbox" name="in_right_all" class="checkbox" style="vertical-align: middle;" /> Right Sidebar</th>
		<th>Under menu </th>
		<th class="w100 ac">New #</th>
	</tr>
	<tbody id="sort">
<?php
$i=1;
foreach($menu_arr as $key=>$arr){
	if(!(in_array($key,array_keys($USER_MENU))||in_array($key,$tag_arr))){continue;}
?>
	<tr>
		<td class="ac" style="cursor:move;"><?php echo $i;?></td>
		<td><input type="text" name="slug_arr[]" value="<?php echo $key;?>" class="text_s ro" readonly /></td>
		<td><input type="text" name="icon_arr[]" value="<?php echo $arr[1];?>" class="text" /></td>
		<td><input type="text" name="title_arr[]" value="<?php echo $arr[0];?>" class="text" /></td>
		<td><input type="checkbox" class="left" name="in_left_<?php echo $key;?>" <?php echo $arr[2] === 'true' ? 'checked' : '' ;?> /></td>
		<td><input type="checkbox" class="right" name="in_right_<?php echo $key;?>" <?php echo $arr[3] === 'true' ? 'checked' : '' ;?> /></td>
		<td>
            <select name="sub_menu[]" class="submenu">
                <option value="">Select Parent Slug</option>

                <?php foreach($menu_arr as $k => $menu) : 
                    if ( !(in_array($k,array_keys($USER_MENU)) || in_array($k,$tag_arr)) || $k == $key){ continue; } ?>
		            <option value="<?= $k; ?>" <?php echo $arr[4] == $k ? 'selected' : '' ;?> ><?= $k; ?></option>

                <?php endforeach; ?>
            </select>
        </td>
		<td class="ac"><?php echo $i;?></td>
	</tr>
<?php
	$i++;
}
?>
	</tbody>
</table>
<div class="submit ac"><input type="submit" name="submit" value="Save Changes" class="button" /></div>
</form>
<script>
jQuery(function($){
	$("#sort").sortable({
		update: function(event,ui){
			$(this).children().each(function(index){
				$(this).find("td").last().html(index+1);
			});
		},
		helper: fixWidthHelper
	});

	function fixWidthHelper(e,ui){
		ui.children().each(function(){
			$(this).width($(this).width());
		});
		return ui;
	}

    $('input[name=in_left_all]').on('click', function() {
        $('.left').not(this).prop('checked', this.checked);
    })

    $('input[name=in_right_all]').on('click', function() {
        $('.right').not(this).prop('checked', this.checked);
    })

    $('.submenu').on('input', function() {
        if ($(this).val()) {
            $(this).parents('tr').find('.right').prop('checked', false);
        }
    })
});
</script>