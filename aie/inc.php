<?php
require_once("config.php");
require_once("func.php");

if(!empty($_FILES)){
	$type=$_POST["dztype"];
	$dir=$aie_dir."/upload/".$UserID."/".$type;
	if(!is_dir($aie_dir."/upload/".$UserID)){
		mkdir($aie_dir."/upload/".$UserID);
		chmod($aie_dir."/upload/".$UserID,0777);
	}
	if(!is_dir($dir)){
		mkdir($dir);
		chmod($dir,0777);
	}

	if(getimagesize($_FILES["file"]["tmp_name"])){
		$name=$_FILES["file"]["name"];
		$file=$dir."/".$name;
		move_uploaded_file($_FILES["file"]["tmp_name"],$file);
		if($type=="bg"){
			$_arr=pathinfo($name);
			$file_n=$dir."/".$_arr["filename"]."_s.".$_arr["extension"];
			$im=new imageLib($file);
			$im->resizeImage(125,200,2);
			$im->saveImage($file_n,75);
		}
	}
}

$dir_arr=json_decode(file_get_contents($aie_api_url."/?func=dir&type=bg"));
$icon_arr=json_decode(file_get_contents($aie_api_url."/?func=dir&type=icon"));

$_arr1=scan_dir($aie_dir."/font");
$_arr2=json_decode(file_get_contents($aie_api_url."/?func=font"));
if(sizeof($_arr2)){
	foreach($_arr2 as $f){
		if(in_array($f,$_arr1)){continue;}
		copy($aie_api_url."/upload/font/$f",$aie_dir."/font/$f");
	}
}
$font_arr=scan_dir($aie_dir."/font");

$style="";
$font_sel="";
if(sizeof($font_arr)){
	foreach($font_arr as $font){
		$name=substr($font,0,-4);
		$style.="@font-face{font-family:$name;src:url(".$aie_dir."/font/$font);}";
		$font_sel.="<option style=\"font-family:$name;font-size:24px;\"".(($name==$aie_def["font"])?" selected":"").">$name</option>";
	}
}
?>
<script>
jQuery(function($){
	$("head").append("<script src='<?php echo $aie_dir;?>/aie.js'><\/script>");
	$("head").append("<script src='<?php echo $aie_dir;?>/jquery.blockUI.js'><\/script>");
	$("head").append("<script src='<?php echo $aie_dir;?>/jquery.rotatable.js'><\/script>");
	$("head").append("<script src='<?php echo $aie_dir;?>/resizable-rotation.patch.js'><\/script>");
	$("head").append("<script src='<?php echo $aie_dir;?>/jquery.imgareaselect.js'><\/script>");
	$("head").append("<script src='<?php echo $aie_dir;?>/jquery.waitforimages.js'><\/script>");
	$("head").append("<link rel='stylesheet' href='<?php echo $aie_dir;?>/aie.css' />");
	$("head").append("<style type='text/css'><?php echo $style;?><\/style>");
	$("#aie-thumb").tabs();
	$("#aie-thumb-tpl").tabs();
	$("#aie-icon-tab").tabs();

	$("#bg-type").on("change",function(){
		$("#bg-w").val($("option:selected",this).attr("w"));
		$("#bg-h").val($("option:selected",this).attr("h"));
	});
	$("#bg-grad").change(function(){
		if($(this).prop("checked")){
			$(this).val(1);
			$("#aie-grad-wrap").show();
		}
		else{
			$(this).val(0);
			$("#aie-grad-wrap").hide();
		}
	});
});
</script>
<div id="aie-thumb">
	<ul>
		<li><a href="#aie-thumb-tpl">Background Template</a></li>
		<li><a href="#aie-thumb-color">Background Color</a></li>
	</ul>
	<div id="aie-thumb-tpl">
<?php
if(sizeof($dir_arr)){
?>
		<ul>
<?php
	foreach($dir_arr as $f){
?>
			<li class="aie-thumb-tpl"><a href="#aie-thumb-tpl-sub"><?php echo $f;?></a></li>
<?php
	}
?>
			<li class="aie-thumb-upload"><a href="#aie-thumb-upload">My Templates</a></li>
			<li class="aie-thumb-premium"><a href="#aie-thumb-premium-sub">Premium Templates</a></li>
		</ul>
		<div id="aie-thumb-tpl-sub"></div>
		<div id="aie-thumb-upload">
<?php
$bg_num=count_dir($aie_dir."/upload/".$UserID."/bg","_s");
$bg_max=$ECG_ARR["bg"];
?>
<form action="index.php?cmd=aie" class="dropzone" id="dzbg">
<input type="hidden" name="dztype" value="bg" />
</form>
<script>
Dropzone.options.dzbg={
	maxFiles: <?php echo $bg_max;?>,
	maxFilesize: <?php echo $ECG_ARR["bgs"];?>,
	acceptedFiles: "image/*",
	dictDefaultMessage: "<p class='desc'><span class='large'>Drop files here or click to upload.</span></p>",
	init: function(){
		dzbg=this;
		this.on("maxfilesexceeded",function(file){
			this.removeFile(file);
		});
	},
	queuecomplete: function(){
		alert("All files have been uploaded!");
		this.removeAllFiles();
		$(".aie-thumb-upload").trigger("click");
	}
};
</script>
<br />
<h3 class="ac">Folder Usage: <span id="aie-bg-num"><?php echo $bg_num;?></span> of <?php echo $bg_max;?></h3>
<div id="aie-thumb-upload-sub"></div>
		</div>
		<div id="aie-thumb-premium-sub"></div>
<?php
}
?>
	</div>
	<div id="aie-thumb-color" class="ac">
		<p class="desc">Select Background Type and/or enter Background Size below.</p>
		<select id="bg-type" class="sel">
			<option w="640" h="480">[Select Background Type]</option>
<?php
foreach($aie_dim_arr as $arr){
?>
			<option w="<?php echo $arr["w"];?>" h="<?php echo $arr["h"];?>"><?php echo $arr["title"]." (".$arr["w"]."x".$arr["h"].")";?></option>
<?php
}
?>
		</select>
		<p>Background Size: <input type="text" id="bg-w" value="640" class="text_s" style="width:50px;" /> x <input type="text" id="bg-h" value="480" class="text_s" style="width:50px;" /> px</p>
		<p><input type="checkbox" id="bg-grad" value="0" /> <label for="bg-grad">Choose Background Color</label></p>
		<div id="aie-grad-wrap" class="aie-hide">
			<p class="desc">Pick up Start and End colors for the Background Gradient. Make the colors equal for invariable Background.</p>
			<input id="aie-color-s" type="text" class="color" value="#336699" />
			<input id="aie-color-e" type="text" class="color" value="#ccddee" />
			<input id="btnGradCreate" type="button" value="Show Gradient Types" class="aie-btn" />
			<br />
			<div id="aie-grad"></div>
		</div>
		<p><input id="btnBgCreate" type="button" value="Create Background" class="button" /></p>
	</div>
</div>
<div id="aie-area">
<div id="aie-menu" class="aie-hide">
	<div id="aie-menu-main">
		<img id="btnIcon" src="<?php echo $aie_dir;?>/img/btnicon.png" title="Insert Image" class="aie-img tip" />
		<img id="btnText" src="<?php echo $aie_dir;?>/img/btntext.png" title="Insert Text" class="aie-img tip" />
		<img id="btnTextEdit" src="<?php echo $aie_dir;?>/img/btnedit.png" title="Edit Text" class="aie-img tip" />
		<img id="btnSave" src="<?php echo $aie_dir;?>/img/btnsave.png" title="Save Graphics" class="aie-img tip" />
		<img id="btnCancel" src="<?php echo $aie_dir;?>/img/btncancel.png" title="Cancel" class="aie-img tip" />
		<a href="#" class="fb"><img id="btnView" src="<?php echo $aie_dir;?>/img/eye.png" title="View Sample Template" class="aie-img aie-hide tip" /></a>
		<input id="btnCrop" type="button" value="Crop" class="aie-btn aie-hide" />
		<input id="btnCropCancel" type="button" value="Cancel" class="aie-btn aie-hide" />
	</div>
	<div id="aie-menu-sub">
		<div id="aie-size-text">W:<input id="aie-w" type="text" readonly /> H:<input id="aie-h" type="text" readonly /></div>
		<img id="imgZout" src="<?php echo $aie_dir;?>/img/zout.png" title="Zoom Out" class="aie-img aie-zoom tip" />
		<div id="aie-zoom-text"></div>
		<img id="imgZin" src="<?php echo $aie_dir;?>/img/zin.png" title="Zoom In" class="aie-img aie-zoom tip" />
		<img id="imgGrey" src="<?php echo $aie_dir;?>/img/grey.png" title="Grey Scale" class="aie-img aie-ef tip" />
		<img id="imgBlur" src="<?php echo $aie_dir;?>/img/blur.png" title="Blur" class="aie-img aie-ef tip" />
		<img id="imgSharp" src="<?php echo $aie_dir;?>/img/sharp.png" title="Sharpen" class="aie-img aie-ef tip" />
		<img id="imgBright" src="<?php echo $aie_dir;?>/img/bright.png" title="Brightness" class="aie-img tip" />
		<div id="aie-bSlide" class="aie-slide aie-hide"></div>
		<img id="imgContrast" src="<?php echo $aie_dir;?>/img/contrast.png" title="Contrast" class="aie-img tip" />
		<div id="aie-cSlide" class="aie-slide aie-hide"></div>
		<img id="imgCrop" src="<?php echo $aie_dir;?>/img/crop.png" title="Crop" class="aie-img tip" />
		<img id="imgDel" src="<?php echo $aie_dir;?>/img/del.png" title="Delete" class="aie-img tip" />
		<img id="imgUndo" src="<?php echo $aie_dir;?>/img/undo.png" title="Undo" class="aie-img tip" />
		<br/>
		<img id="imgAL" src="<?php echo $aie_dir;?>/img/imgal.png" title="Align Left" class="aie-img tip" />
		<img id="imgAC" src="<?php echo $aie_dir;?>/img/imgac.png" title="Align Center" class="aie-img tip" />
		<img id="imgAR" src="<?php echo $aie_dir;?>/img/imgar.png" title="Align Right" class="aie-img tip" />
		<img id="imgAT" src="<?php echo $aie_dir;?>/img/imgat.png" title="Align Top" class="aie-img tip" />
		<img id="imgAM" src="<?php echo $aie_dir;?>/img/imgam.png" title="Align Middle" class="aie-img tip" />
		<img id="imgAB" src="<?php echo $aie_dir;?>/img/imgab.png" title="Align Bottom" class="aie-img tip" />
		<img id="imgFront1" src="<?php echo $aie_dir;?>/img/front1.gif" title="Bring Forward" class="aie-img tip" />
		<img id="imgFront" src="<?php echo $aie_dir;?>/img/front.gif" title="Bring to Front" class="aie-img tip" />
		<img id="imgBack1" src="<?php echo $aie_dir;?>/img/back1.gif" title="Send Backward" class="aie-img tip" />
		<img id="imgBack" src="<?php echo $aie_dir;?>/img/back.gif" title="Send to Back" class="aie-img tip" />
	</div>
</div>
<div id="aie-wrap" class="aie-hide">
	<div id="aie-canvas" class="aie-hide"></div>
	<div id="aie-text" class="aie-hide">
		<input type="hidden" id="aie-isedit" value="0" />
		<textarea id="aie-text-val" class="aie-ta" placeholder="Your Text Here" style="font-family:<?php echo $aie_def["font"];?>;font-size:72px;text-align:center;"></textarea><br />
		<select id="aie-font" class="aie-sel" style="font-family:<?php echo $aie_def["font"];?>;font-size:24px;"><?php echo $font_sel;?></select>
		<input id="aie-size" type="text" class="aie-text-s" value="72" maxlength="3" />
		<input id="aie-color" type="text" class="color" value="#000000" />
		<img src="<?php echo $aie_dir;?>/img/textal.png" title="Align Left" value="left" class="aie-align aie-img tip" />
		<img src="<?php echo $aie_dir;?>/img/textac.png" title="Align Center" value="center" class="aie-align aie-img aie-img-hover tip" />
		<img src="<?php echo $aie_dir;?>/img/textar.png" title="Align Right" value="right" class="aie-align aie-img tip" />
		<input id="btnTextAdd" type="button" value="Insert Text" class="aie-btn" />
		<input id="btnTextCancel" type="button" value="Cancel" class="aie-btn" />
	</div>
<script>
jQuery(function($){
	$("#aie-font").on("change",function(){
		$("#aie-text-val").css("font-family",$(this).val());
	});
	$("#aie-size").on("change",function(){
		$("#aie-text-val").css("font-size",$(this).val()+"px");
	});
	$("#aie-color").on("change",function(){
		$("#aie-text-val").css("color",$(this).val());
	});
	$(".aie-align").on("click",function(){
		$("#aie-text-val").css("text-align",$(this).attr("value")).focus();
		$(".aie-img").removeClass("aie-img-hover");
		$(this).addClass("aie-img-hover");
	});

	$(".fb").fancybox({"title":""});
});
</script>
	<div id="aie-icon" class="aie-hide">
		<div class="aie-cancel">
			<input id="btnIconCancel" type="button" value="Cancel" class="aie-btn" />
		</div>
		<div id="aie-icon-tab">
<?php
if(sizeof($icon_arr)){
?>
			<ul>
<?php
	foreach($icon_arr as $f){
?>
				<li class="aie-icon-tab"><a href="#aie-icon-sub"><?php echo $f;?></a></li>
<?php
	}
?>
				<li class="aie-icon-pix"><a href="#aie-icon-pix">DELUXE</a></li>
				<li class="aie-icon-upload"><a href="#aie-icon-upload">My Images</a></li>
			</ul>
			<div id="aie-icon-sub"></div>
			<div id="aie-icon-pix">
<p class="desc ac">Choose below a DELUXE Resource and enter keyword(s) to load images that meet your criteria.</p><br />
<div class="ac">
<select id="lux-src" class="sel">
	<option value="pix">Pixabay</option>
<?php
$lux_arr=split(";",trim($cur_pack["pack_lux"],";"));
foreach($LUX_ARR as $k=>$v){
	if(in_array($k,$lux_arr)){
?>
	<option value="<?php echo $k;?>"><?php echo $v;?></option>
<?php
	}
}
?>
</select>
<input type="text" id="aie-pix" class="text" placeholder="Your Keyword(s) Here" />
<input type="button" id="btnPix" value="Search" class="aie-btn" />
</div>
<br />
<div id="aie-icon-pix-sub"></div>
			</div>
			<div id="aie-icon-upload">
<?php
$icon_num=count_dir($aie_dir."/upload/".$UserID."/icon");
$icon_max=$ECG_ARR["icon"];
?>
<form action="index.php?cmd=aie" class="dropzone" id="dzicon">
<input type="hidden" name="dztype" value="icon" />
</form>
<script>
Dropzone.options.dzicon={
	maxFiles: <?php echo $icon_max;?>,
	maxFilesize: <?php echo $ECG_ARR["icons"];?>,
	acceptedFiles: "image/*",
	dictDefaultMessage: "<p class='desc'><span class='large'>Drop files here or click to upload.</span></p>",
	init: function(){
		dzicon=this;
		this.on("maxfilesexceeded",function(file){
			this.removeFile(file);
		});
	},
	queuecomplete: function(){
		alert("All files have been uploaded!");
		this.removeAllFiles();
		$(".aie-icon-upload").trigger("click");
	}
};
</script>
<br />
<h3 class="ac">Folder Usage: <span id="aie-icon-num"><?php echo $icon_num;?></span> of <?php echo $icon_max;?></h3>
<div id="aie-icon-upload-sub"></div>
			</div>
<?php
}
?>
		</div>
	</div>
</div>
</div>
<div id="aie-load" class="aie-hide"><img src="<?php echo $aie_dir;?>/img/load.gif" /></div>
<?php
if($id){
?>
<input type="hidden" id="coverID" value="<?php echo $id;?>" />
<input type="button" id="btnEdit" value="Edit" class="aie-hide" />
<?php
}
?>