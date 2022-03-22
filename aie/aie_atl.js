var
aie_dir="../aie",
cur_dir,
imgNum=1,
img_arr=new Array(),
curItem,
firstItem,
lastItem,
zoom=100,
scale=1,
scroll=0,
undo_arr=new Array(),
ias_w=0,
ias_h=0,
crop="";

jQuery(document).ready(function($){
	$(".aie-thumb-tpl").click(function(){
		block();
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=load&dir=vbg&sub="+$(this).text(),
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					$("#aie-thumb-tpl-sub").html(res[1]).hide().waitForImages(function(){
						$(this).show();
						unblock();
					});
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});
	$(".aie-thumb-tpl :first").trigger("click");

	$(".aie-thumb-upload").click(function(){
		block();
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=loadu&dir=bg",
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					$("#aie-thumb-upload-sub").html(res[1]).hide().waitForImages(function(){
						$(this).show();
						unblock();
					});
					dzbg.options.maxFiles=parseInt(res[3]-res[2],10);
					$("#aie-bg-num").html(res[2]);
					if(parseInt(res[2],10)>=parseInt(res[3],10)){
						$("#dzbg").addClass("hid");
					}
					else{
						$("#dzbg").removeClass("hid");
					}
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

/*
	$(".aie-thumb-premium").click(function(){
		block();
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=loadpr",
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					$("#aie-thumb-premium-sub").html(res[1]).hide().waitForImages(function(){
						$(this).show();
						unblock();
					});
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

	$("div#aie-thumb-tpl-sub,div#aie-thumb-upload-sub,div#aie-thumb-premium-sub").on("click","img.aie-thumb-img",function(){
		block();
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=init&bg="+$(this).attr("src")+"&type="+$(this).attr("type"),
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					setUnload();
					$("#aie-thumb").hide();
					$("#aie-wrap").show();
					$("#aie-menu").show();
					$("#aie-canvas").show();
					cur_dir=res[1];
					$("#aie-canvas").html("<img id='bg' src='"+res[2]+"' width='"+res[3]+"' height='"+res[4]+"' bright='0' contrast='0' style='border:#999 1px dashed;' />");
					setSize();
					var w=$(".wrap").width()-40-$(".ad2").width();
					$("#aie-area").css("max-width",w);
					$("#aie-menu").css("width",w-22);
					$("#aie-canvas").css("max-width",w);
					zoom=100*(Math.floor(w*10/$("#bg").width())/10);
					scale=zoom/100;
					setScale();

					$("#bg").load(function(){
						unblock();
					});
					$("#bg").click(function(){
						curItem="bg";
						$(".aie-resize").parent().removeClass("aie-dashed");
						$("#btnCropCancel").trigger("click");
						setSubmenu();
					});
					$("#bg").trigger("click");
					$(".aie-icon-tab :first").trigger("click");
					$("body,html").animate({scrollTop:0},400);
					$("#imgUndo").css("opacity","0.1");

					if(res[5]!=""){
						$("#btnView").show();
//						$("#imgView").attr("src",res[6]);
//						$("#imgView").parent().attr("href",res[5]);
						$("#btnView").parent().attr("href",res[5]);
					}
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

	$("#btnView").click(function(){
		$("#divView").show();
		$("#btnView").css("opacity","0.1");
	});

	$("#imgViewClose").click(function(){
		$("#divView").hide();
		$("#btnView").css("opacity","1");
	});
*/

	$("#btnEdit").click(function(){
		block();
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=edit&id="+$("#coverID").val(),
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					setUnload();
					$("#aie-thumb").hide();
					$("#aie-wrap").show();
					$("#aie-menu").show();
					$("#aie-canvas").show();
					cur_dir=res[1];
					$("#aie-canvas").html("<img id='bg' src='"+res[2]+"' width='"+res[3]+"' height='"+res[4]+"' bright='0' contrast='0' style='border:#999 1px dashed;' />");
					setSize();
					var w=$(".wrap").width()-40-$(".ad2").width();
					$("#aie-area").css("max-width",w);
					$("#aie-menu").css("width",w-22);
					$("#aie-canvas").css("max-width",w);
					zoom=100*(Math.floor(w*10/$("#bg").width())/10);
					scale=zoom/100;
					setScale();

				if(res[5]){
					var obj=JSON.parse(res[5]);
					for(var i in obj){
						$("#aie-canvas").append("<img id='img-"+imgNum+"' src='"+obj[i].f+"' class='aie-item' />");
						$("#img-"+imgNum).attr({"width":obj[i].w+"px","height":obj[i].h+"px","bright":0,"contrast":0});
						if(obj[i].text!=0){
							$("#img-"+imgNum).attr("textID",obj[i].text);
						}
						var d=Math.abs(obj[i].d*Math.PI/180);
						var l=obj[i].le;
						var t=obj[i].te;
						$("#img-"+imgNum).css({"left":l,"top":t});
						$("#img-"+imgNum).click(function(){
							curItem=$(this).attr("id");
							$(".aie-resize").parent().removeClass("aie-dashed");
							var rLeft,rTop;
							$(this).addClass("aie-resize").resizable({
								handles: "all",
							}).parent().addClass("aie-dashed").rotatable({
								angle: obj[i].d*3.1416/180,
								stop: function(event,ui){
									var deg=Math.round(ui.angle.stop*180/3.1416);
									$(this).attr("rotation",deg);
								}
							}).draggable({
								start: function(event,ui){
									var left=parseInt($(this).css("left"),10);
									left=isNaN(left)?0:left;
									var top=parseInt($(this).css("top"),10);
									top=isNaN(top)?0:top;
									rLeft=left-ui.position.left;
									rTop=top-ui.position.top;
								},
								drag: function(event,ui){
									ui.position.left+=rLeft;
									ui.position.top+=rTop;
								}
							});
							$("#btnCropCancel").trigger("click");
							setSubmenu();
							$(".ui-resizable-se").removeClass("ui-icon").removeClass("ui-icon-gripsmall-diagonal-se");
						});
						$("#img-"+imgNum).trigger("click");
						$("#img-"+imgNum).parent().attr("rotation",obj[i].d);
						img_arr[imgNum-1]=curItem;
						imgNum++;
					}
				}

					$("#bg").click(function(){
						curItem="bg";
						$(".aie-resize").parent().removeClass("aie-dashed");
						$("#btnCropCancel").trigger("click");
						setSubmenu();
					});
					$("#bg").trigger("click");
//					$(".aie-icon-tab :first").trigger("click");
					$("body,html").animate({scrollTop:0},400);
					$("#imgUndo").css("opacity","0.1");
					unblock();
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

	$("div#aie-thumb-upload-sub").on("click","#bg_del_btn",function(){
		block();
		var checked=[];
		$("#aie-thumb-upload-sub input:checked").each(function(){
			checked.push($(this).val());
		});
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=del&dir=bg&str="+checked,
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					$(".aie-thumb-upload").trigger("click");
					unblock();
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

	$("#btnPix").click(function(){
		block();
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=loadp&src="+$("#lux-src").val()+"&q="+$("#aie-pix").val(),
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					$("#aie-icon-pix-sub").html(res[1]).hide().waitForImages(function(){
						$(this).show();
						unblock();
					});
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

	$(".aie-icon-upload").click(function(){
		block();
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=loadu&dir=icon",
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]==="success"){
					$("#aie-icon-upload-sub").html(res[1]).hide().waitForImages(function(){
						$(this).show();
						unblock();
					});
					dzicon.options.maxFiles=parseInt(res[3]-res[2],10);
					$("#aie-icon-num").html(res[2]);
					if(parseInt(res[2],10)>=parseInt(res[3],10)){
						$("#dzicon").addClass("hid");
					}
					else{
						$("#dzicon").removeClass("hid");
					}
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

	$("div#aie-icon-sub,div#aie-icon-upload-sub").on("click","img.aie-icon-img",function(){
		block();
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=icon&key="+cur_dir+"&file="+$(this).attr("src"),
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					$("#aie-icon").hide();
					$("#aie-menu").show();
					$("#aie-canvas").show();
					$("#aie-canvas").append("<img id='img-"+imgNum+"' src='"+res[1]+"' class='aie-item' />");
					$("#img-"+imgNum).attr({"width":res[2]+"px","height":res[3]+"px","bright":0,"contrast":0});
					$("#img-"+imgNum).css("top",parseInt((scroll/scale)+25,10)+"px");
					$("#img-"+imgNum).load(function(){
						unblock();
					});
					$("#img-"+imgNum).click(function(){
						curItem=$(this).attr("id");
						$(".aie-resize").parent().removeClass("aie-dashed");
						var rLeft,rTop;
						$(this).addClass("aie-resize").resizable({
							handles: "all",
						}).parent().addClass("aie-dashed").rotatable({
							stop: function(event,ui){
								var deg=Math.round(ui.angle.stop*180/3.1416);
								$(this).attr("rotation",deg);
							}
						}).draggable({
							start: function(event,ui){
								var left=parseInt($(this).css("left"),10);
								left=isNaN(left)?0:left;
								var top=parseInt($(this).css("top"),10);
								top=isNaN(top)?0:top;
								rLeft=left-ui.position.left;
								rTop=top-ui.position.top;
							},
							drag: function(event,ui){
								ui.position.left+=rLeft;
								ui.position.top+=rTop;
							}
						});
						$("#btnCropCancel").trigger("click");
						setSubmenu();
						$(".ui-resizable-se").removeClass("ui-icon").removeClass("ui-icon-gripsmall-diagonal-se");
					});
					$("#img-"+imgNum).trigger("click");
					$("body,html").animate({scrollTop:scroll},400);
					img_arr[imgNum-1]=curItem;
					imgNum++;
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

	$("div#aie-icon-upload-sub").on("click","#icon_del_btn",function(){
		block();
		var checked=[];
		$("#aie-icon-upload-sub input:checked").each(function(){
			checked.push($(this).val());
		});
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=del&dir=icon&str="+checked,
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					$(".aie-icon-upload").trigger("click");
					unblock();
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

	$("#btnGradCreate").click(function(){
		block();
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=grad&s="+$("#aie-color-s").val().substring(1,7)+"&e="+$("#aie-color-e").val().substring(1,7),
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					$("#aie-grad").html(res[1]).hide().waitForImages(function(){
						$(this).show();
						unblock();
					});
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

/*
	$("div#aie-grad").on("click","div.aie-grad",function(){
			block();
			$.ajax({
				type: "POST",
				url: aie_dir+"/aie_atl.php",
				data: "func=init&bg=grad&d="+$(this).children().attr("direction")+"&s="+$("#aie-color-s").val()+"&e="+$("#aie-color-e").val(),
				cache: false,
				success: function(response){
					res=response.split("|");
					if(res[0]=="success"){
						setUnload();
						$("#aie-thumb").hide();
						$("#aie-wrap").show();
						$("#aie-menu").show();
						$("#aie-canvas").show();
						cur_dir=res[1];
						$("#aie-canvas").html("<img id='bg' src='"+res[2]+"' width='"+res[3]+"' height='"+res[4]+"' bright='0' contrast='0' style='border:#999 1px dashed;' />");
						setSize();
						var w=$(".wrap").width()-40-$(".ad2").width();
						$("#aie-area").css("max-width",w);
						$("#aie-menu").css("width",w-22);
						$("#aie-canvas").css("max-width",w);
						zoom=100*(Math.floor(w*10/$("#bg").width())/10);
						scale=zoom/100;
						setScale();

						$("#bg").load(function(){
							unblock();
						});
						$("#bg").click(function(){
							curItem="bg";
							$(".aie-resize").parent().removeClass("aie-dashed");
							$("#btnCropCancel").trigger("click");
							setSubmenu();
						});
						$("#bg").trigger("click");
						$(".aie-icon-tab :first").trigger("click");
						$("body,html").animate({scrollTop:0},400);
						$("#imgUndo").css("opacity","0.1");
					}
					else{
						alert("An ERROR has occurred: "+res[0]);
						unblock();
					}
				}
			});
	});
*/
	$("div#aie-thumb-tpl-sub,div#aie-thumb-upload-sub,div#aie-thumb-premium-sub").on("click","img.aie-thumb-img",function(){
		block();
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=cngbg&bg="+$(this).attr("src")+"&key="+cur_dir,
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					$("#aie-thumb").hide();
					$("#aie-area").show();
					$("#bg").attr("src",res[1]);
					unblock();
					$("#bg").trigger("click");
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

	$("div#aie-grad").on("click","div.aie-grad",function(){
		block();
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=cngbg&bg=grad&key="+cur_dir+"&d="+$(this).children().attr("direction")+"&s="+$("#aie-color-s").val()+"&e="+$("#aie-color-e").val(),
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					$("#aie-thumb").hide();
					$("#aie-area").show();
					$("#bg").attr("src",res[1]);
					unblock();
					$("#bg").trigger("click");
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

	$("#btnTextAdd").click(function(){
		if($.trim($("#aie-text-val").val())==""){
			alert("Please enter Your Text!");
			$("#aie-text-val").select();
		}
		else if(!$.isNumeric($("#aie-size").val())){
			alert("Please enter a valid Font Size Number!");
			$("#aie-size").select();
		}
		else{
			block();
			$.ajax({
				type: "POST",
				url: aie_dir+"/aie_atl.php",
				data: "func=text&key="+cur_dir+"&text="+$.trim(encodeURIComponent($("#aie-text-val").val()))+"&font="+$("#aie-font").val()+"&size="+$("#aie-size").val()+"&color="+$("#aie-color").val()+"&align="+$("#aie-text-val").css("text-align"),
				cache: false,
				success: function(response){
					res=response.split("|");
					if(res[0]=="success"){
						$("#aie-text").hide();
						$("#aie-menu").show();
						$("#aie-canvas").show();
						var isedit=$("#aie-isedit").val();
						var iNum=(isedit>0)?isedit:imgNum;
						if(isedit==0){
							$("#aie-canvas").append("<img id='img-"+iNum+"' src='"+res[1]+"' class='aie-item' />");
						}
						else{
							var str="crop|"+curItem+"|"+$("#"+curItem).attr("src")+"|"+$("#"+curItem).width()+"|"+$("#"+curItem).height()+"|"+$("#"+curItem).attr("textID");
							undoAdd(str);
						}
//						$("#img-"+iNum).attr({"width":res[2]+"px","height":res[3]+"px","bright":0,"contrast":0,"textID":res[4]});
						$("#img-"+iNum).attr({"bright":0,"contrast":0,"textID":res[4]});
						if(isedit==0){
							$("#img-"+iNum).css("top",parseInt((scroll/scale)+25,10)+"px");
						}
						else{
							$("#img-"+iNum).attr("src",res[1]);
							$("#img-"+iNum).css({"width":res[2]+"px","height":res[3]+"px"});
						}
						$("#img-"+iNum).load(function(){
							unblock();
						});
						$("#img-"+iNum).click(function(){
							curItem=$(this).attr("id");
							$(".aie-resize").parent().removeClass("aie-dashed");
							$(this).addClass("aie-resize").resizable({
								handles: "all",
							}).parent().addClass("aie-dashed").rotatable({
								stop: function(event,ui){
									var deg=Math.round(ui.angle.stop*180/3.1416);
									$(this).attr("rotation",deg);
								}
							}).draggable({
								start: function(event,ui){
									var left=parseInt($(this).css("left"),10);
									left=isNaN(left)?0:left;
									var top=parseInt($(this).css("top"),10);
									top=isNaN(top)?0:top;
									rLeft=left-ui.position.left;
									rTop=top-ui.position.top;
								},
								drag: function(event,ui){
									ui.position.left+=rLeft;
									ui.position.top+=rTop;
								}
							});
							$("#btnCropCancel").trigger("click");
							setSubmenu();
							$(".ui-resizable-se").removeClass("ui-icon").removeClass("ui-icon-gripsmall-diagonal-se");
						});
						$("#img-"+iNum).trigger("click");
						$("#img-"+iNum).css({"width":res[2]+"px","height":res[3]+"px"});
						$("#img-"+iNum).parent().css({"width":res[2]+"px","height":res[3]+"px"});
						$("body,html").animate({scrollTop:scroll},400);
						if(isedit==0){
							img_arr[imgNum-1]=curItem;
							imgNum++;
						}
						else{
						}
					}
					else{
						alert("An ERROR has occurred: "+res[0]);
						unblock();
					}
				}
			});
		}
	});

	$(".aie-icon-tab").click(function(){
		block();
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=load&dir=icon&sub="+$(this).text(),
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					$("#aie-icon-sub").html(res[1]).hide().waitForImages(function(){
						$(this).show();
						unblock();
					});
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

	$("div#aie-icon-pix-sub").on("click","div.aie-pix",function(){
		block();
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=pix&key="+cur_dir+"&url="+$(this).children().attr("url"),
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					$("#aie-icon").hide();
					$("#aie-menu").show();
					$("#aie-canvas").show();
					$("#aie-canvas").append("<img id='img-"+imgNum+"' src='"+res[1]+"' class='aie-item' />");
					$("#img-"+imgNum).attr({"width":res[2]+"px","height":res[3]+"px","bright":0,"contrast":0});
					$("#img-"+imgNum).css("top",parseInt((scroll/scale)+25,10)+"px");
					$("#img-"+imgNum).load(function(){
						unblock();
					});
					$("#img-"+imgNum).click(function(){
						curItem=$(this).attr("id");
						$(".aie-resize").parent().removeClass("aie-dashed");
						var rLeft,rTop;
						$(this).addClass("aie-resize").resizable({
							handles: "all",
						}).parent().addClass("aie-dashed").rotatable({
							stop: function(event,ui){
								var deg=Math.round(ui.angle.stop*180/3.1416);
								$(this).attr("rotation",deg);
							}
						}).draggable({
							start: function(event,ui){
								var left=parseInt($(this).css("left"),10);
								left=isNaN(left)?0:left;
								var top=parseInt($(this).css("top"),10);
								top=isNaN(top)?0:top;
								rLeft=left-ui.position.left;
								rTop=top-ui.position.top;
							},
							drag: function(event,ui){
								ui.position.left+=rLeft;
								ui.position.top+=rTop;
							}
						});
						$("#btnCropCancel").trigger("click");
						setSubmenu();
						$(".ui-resizable-se").removeClass("ui-icon").removeClass("ui-icon-gripsmall-diagonal-se");
					});
					$("#img-"+imgNum).trigger("click");
					$("body,html").animate({scrollTop:scroll},400);
					img_arr[imgNum-1]=curItem;
					imgNum++;
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

	$("#btnSave").click(function(){
		var img=new Object(),pos,le,te,deg,text,str,i=0;

		block();

		scale=1;
		zoom=100;
		setScale();

		$("[id^='img-']").each(function(i){
			del=$(this).parent().css("display");
			if(del!="none"){
				pos=$(this).parent().position();
				le=$(this).parent().css("left");
				te=$(this).parent().css("top");
				deg=parseInt($(this).parent().attr("rotation"),10);
				deg=isNaN(deg)?0:deg;
				text=parseInt($(this).attr("textID"),10);
				text=isNaN(text)?0:text;
				img[i]={f:$(this).attr("src"),w:$(this).width(),h:$(this).height(),d:deg,l:Math.round(pos.left),t:Math.round(pos.top),le:le,te:te,text:text};
				i++;
			}
		});
		str=JSON.stringify(img);

		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=save&id="+$("#coverID").val()+"&key="+cur_dir+"&file="+$("#bg").attr("src")+"&str="+str,
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					window.onbeforeunload=null;
					window.top.location.href="../user/"+res[1];
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
				}
				unblock();
			}
		});
	});

	$("#btnCancel").click(function(){
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=cancel",
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					window.top.location.href="../user/"+res[1];
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
				}
			}
		});
	});

	$("#btnBgChange").click(function(){
		$("#aie-area").hide();
		$("#aie-thumb").show();
	});

	$("#btnBgChangeCancel").click(function(){
		$("#aie-thumb").hide();
		$("#aie-area").show();
	});

	$("#btnText").click(function(){
		$("#aie-isedit").val(0);
		$("#btnTextAdd").val("Insert Text");
		scroll=$(document).scrollTop();
		$("body,html").animate({scrollTop:0},400);
		$("#aie-menu").hide();
		$("#aie-canvas").hide();
		$("#aie-text").show();
		$("#aie-text-val").focus();
	});

	$("#btnTextEdit").click(function(){
		if(!isNaN($("#"+curItem).attr("textID"))){
			block();
			$.ajax({
				type: "POST",
				url: aie_dir+"/aie_atl.php",
				data: "func=textedit&id="+$("#"+curItem).attr("textID")+"&key="+cur_dir+"&file="+$("#"+curItem).attr("src"),
				cache: false,
				success: function(response){
					res=response.split("|");
					if(res[0]=="success"){
						$("#aie-isedit").val($("#"+curItem).attr("id").substring(4));
						$("#aie-text-val").val(res[1]);
						$("#aie-text-val").css({"font-family":res[2],"font-size":res[3]+"px","color":res[4]});
						$("#aie-font").val(res[2]);
						$("#aie-font").css("font-family",res[2]);
						$("#aie-size").val(res[3]);
						$("#aie-color").spectrum("set",res[4]);
						$(".aie-align[value="+res[5]+"]").trigger("click");
						$("#btnTextAdd").val("Edit Text");
						scroll=$(document).scrollTop();
						$("body,html").animate({scrollTop:0},400);
						$("#aie-menu").hide();
						$("#aie-canvas").hide();
						$("#aie-text").show();
						$("#aie-text-val").focus();
						unblock();
					}
					else{
						alert("An ERROR has occurred: "+res[0]);
						unblock();
					}
				}
			});
		}
	});

	$("#btnTextCancel").click(function(){
		$("#aie-text").hide();
		$("#aie-menu").show();
		$("#aie-canvas").show();
		$("body,html").animate({scrollTop:scroll},400);
	});
	$("#aie-font").change(function(){
		$(this).css({"font-family":$(this).val()});
	});

	$("#btnIcon").click(function(){
		scroll=$(document).scrollTop();
		$("body,html").animate({scrollTop:0},400);
		$("#aie-menu").hide();
		$("#aie-canvas").hide();
		$("#aie-icon").show();
	});
	$("#btnIconCancel").click(function(){
		$("#aie-icon").hide();
		$("#aie-menu").show();
		$("#aie-canvas").show();
		$("body,html").animate({scrollTop:scroll},400);
	});

	$("#aie-canvas,.aie-img").mouseover(function(){
		$(".aie-slide").hide();
//		$("#aie-menu-sub").css("margin-top","7px");
	});

	$(".aie-zoom").click(function(){
		if(this.id=="imgZout") {
			zoom-=10;
			scale-=0.1;
		}
		else{
			zoom+=10;
			scale+=0.1;
		}
		setScale();
	});

	$("#imgAL").click(function(){
		if(curItem!="bg"){
			var left=0;
			$("#"+curItem).parent().css("left",left+"px");
		}
	});
	$("#imgAC").click(function(){
		if(curItem!="bg"){
			var left=Math.round(($("#bg").width()-$("#"+curItem).width())/2);
			$("#"+curItem).parent().css("left",left+"px");
		}
	});
	$("#imgAR").click(function(){
		if(curItem!="bg"){
			var left=Math.round($("#bg").width()-$("#"+curItem).width());
			$("#"+curItem).parent().css("left",left+"px");
		}
	});
	$("#imgAT").click(function(){
		if(curItem!="bg"){
			var top=0;
			$("#"+curItem).parent().css("top",top+"px");
			$("body,html").animate({scrollTop:top},400);
		}
	});
	$("#imgAM").click(function(){
		if(curItem!="bg"){
			var top=Math.round(($("#bg").height()-$("#"+curItem).height())/2);
			$("#"+curItem).parent().css("top",top+"px");
			$("body,html").animate({scrollTop:parseInt((top*scale),10)},400);
		}
	});
	$("#imgAB").click(function(){
		if(curItem!="bg"){
			var top=Math.round($("#bg").height()-$("#"+curItem).height());
			$("#"+curItem).parent().css("top",top+"px");
			$("body,html").animate({scrollTop:parseInt((top*scale),10)},400);
		}
	});

	$("#imgFront1").click(toFront1);
	$("#imgFront").click(toFront);
	$("#imgBack1").click(toBack1);
	$("#imgBack").click(toBack);

	$(".aie-ef").click(function(){
		block();

		var ef=$(this).attr("id"),arg='';
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=ef&key="+cur_dir+"&item="+curItem+"&file="+$("#"+curItem).attr("src")+"&ef="+ef+"&arg="+arg,
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					var str="ef|"+curItem+"|"+$("#"+curItem).attr("src")+"|||"+$("#"+curItem).attr("textID");
					undoAdd(str);
					$("#"+res[1]).load(function(){
						unblock();
					}).attr({"src":res[2]}).removeAttr("textID");
					$("#"+res[1]).trigger("click");
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});

	$("#imgBright").mouseover(function(){
		$("#aie-cSlide").hide();
		$("#aie-bSlide").show();
//		$("#aie-menu-sub").css("margin-top","-25px");
		var bright=$("#"+curItem).attr("bright");
		$("#aie-bSlide").slider({
			value: bright,
			min: -100,
			max: 100,
			step: 5,
			slide: function(event,ui){
				$("#"+curItem).attr("bright",ui.value);
			},
			stop: function(event,ui){
				if(ui.value!=bright){
					block();
					$.ajax({
						type: "POST",
						url: aie_dir+"/aie_atl.php",
						data: "func=ef&key="+cur_dir+"&item="+curItem+"&file="+$("#"+curItem).attr("src")+"&ef=bright&arg="+ui.value,
						cache: false,
						success: function(response){
							res=response.split("|");
							if(res[0]=="success"){
								var str="ef|"+curItem+"|"+$("#"+curItem).attr("src")+"|bright|"+bright+"|"+$("#"+curItem).attr("textID");
								undoAdd(str);
								$("#"+res[1]).load(function(){
									unblock();
								}).attr({"src":res[2]}).removeAttr("textID");
								$("#"+res[1]).trigger("click");
							}
							else{
								alert("An ERROR has occurred: "+res[0]);
								unblock();
							}
						}
					});
				}
			}
		});
	});
	$("#imgContrast").mouseover(function(){
		$("#aie-bSlide").hide();
		$("#aie-cSlide").show();
//		$("#aie-menu-sub").css("margin-top","-25px");
		var contrast=$("#"+curItem).attr("contrast");
		$("#aie-cSlide").slider({
			value: contrast,
			min: -100,
			max: 100,
			step: 5,
			slide: function(event,ui){
				$("#"+curItem).attr("contrast",ui.value);
			},
			stop: function(event,ui){
				if(ui.value!=contrast){
					block();
					$.ajax({
						type: "POST",
						url: aie_dir+"/aie_atl.php",
						data: "func=ef&key="+cur_dir+"&item="+curItem+"&file="+$("#"+curItem).attr("src")+"&ef=contrast&arg="+ui.value,
						cache: false,
						success: function(response){
							res=response.split("|");
							if(res[0]=="success"){
								var str="ef|"+curItem+"|"+$("#"+curItem).attr("src")+"|contrast|"+contrast+"|"+$("#"+curItem).attr("textID");
								undoAdd(str);
								$("#"+res[1]).load(function(){
									unblock();
								}).attr({"src":res[2]}).removeAttr("textID");
								$("#"+res[1]).trigger("click");
							}
							else{
								alert("An ERROR has occurred: "+res[0]);
								unblock();
							}
						}
					});
				}
			}
		});
	});

	$("#imgCrop").click(function(){
		$("#btnIcon,#btnText,#btnTextEdit,#btnSave,#btnCancel,#aie-menu-sub").hide();
		$("#btnCrop,#btnCropCancel").show();
		$(".aie-resize").parent().removeClass("aie-dashed");

		ias_w=Math.floor($("#"+curItem).width()/2);
		ias_h=Math.floor($("#"+curItem).height()/2);
		$("#"+curItem).imgAreaSelect({
			enable: true,
			parent: $("#"+curItem).parent("div"),
			x1: 0,
			y1: 0,
			x2: ias_w,
			y2: ias_h,
			handles: false,
			persistent: true,
			onSelectEnd:  function(img,selection){
				crop=selection.width+"|"+selection.height+"|"+selection.x1+"|"+selection.y1;
			}
		});
	});
	$("#btnCrop").click(function(){
		if(crop==""){
			crop=ias_w+"|"+ias_h+"|0|0";
		}

		var arr=crop.split("|");
		if((arr[0]==0)||(arr[1]==0)){
			alert("Area to be cropped is not chosen!");
			$("#btnCropCancel").trigger("click");
			return false;
		}

		block();
		$.ajax({
			type: "POST",
			url: aie_dir+"/aie_atl.php",
			data: "func=crop&key="+cur_dir+"&item="+curItem+"&file="+$("#"+curItem).attr("src")+"&w="+arr[0]+"&h="+arr[1]+"&x="+arr[2]+"&y="+arr[3]+"&rw="+$("#"+curItem).width()+"&rh="+$("#"+curItem).height(),
			cache: false,
			success: function(response){
				res=response.split("|");
				if(res[0]=="success"){
					var str="crop|"+curItem+"|"+$("#"+curItem).attr("src")+"|"+$("#"+curItem).width()+"|"+$("#"+curItem).height()+"|"+$("#"+curItem).attr("textID");
					undoAdd(str);
					$("#"+res[1]).load(function(){
						unblock();
					}).attr({"src":res[2]});
					$("#"+res[1]).width(arr[0]).height(arr[1]);
					$("#"+res[1]).parent().width(arr[0]).height(arr[1]);
					$("#btnCropCancel").trigger("click");
					$("#"+res[1]).removeAttr("textID");
					$("#"+res[1]).trigger("click");
					if(curItem=="bg"){
						setSize();
					}
				}
				else{
					alert("An ERROR has occurred: "+res[0]);
					unblock();
				}
			}
		});
	});
	$("#btnCropCancel").click(function(){
		$("#bg,.aie-item").imgAreaSelect({
			disable: true,
			hide: true
		});
		ias_w=0;
		ias_h=0;
		crop="";
		$("#btnCrop,#btnCropCancel").hide();
		$("#btnIcon,#btnText,#btnTextEdit,#btnSave,#btnCancel,#aie-menu-sub").show();
	});

	$("#imgDel").click(function(){
		if(curItem!="bg"){
			var str="delete|"+curItem;
			undoAdd(str);
			imgDel();
		}
	});

	$("#imgUndo").click(undo);

	if($("#coverID").val()!="underfined"){
		$("#btnEdit").trigger("click");
	}

});

function setUnload(){
	window.onbeforeunload=function(){
		return("Are you sure you wish to exit this page?");
	}
}

function setSize(){
	$("#aie-w").val($("#bg").width());
	$("#aie-h").val($("#bg").height());
}

function setScale(){
	if(zoom<=10){
		zoom=10;
		scale=0.1;
		$("#imgZout").css("opacity","0.1");
		$("#imgZin").css("opacity","1");
	}
	else if(zoom>10&&zoom<100){
		$("#imgZout,#imgZin").css("opacity","1");
	}
	else if(zoom>=100){
		zoom=100;
		scale=1;
		$("#imgZout").css("opacity","1");
		$("#imgZin").css("opacity","0.1");
	}
	$("#aie-zoom-text").html(zoom+"%");

	$("#aie-canvas").css({
		"transform": "scale("+scale+")",
		"-ms-transform": "scale("+scale+")",
		"-moz-transform": "scale("+scale+")",
		"-webkit-transform": "scale("+scale+")",
		"transform-origin": "top left",
		"-ms-transform-origin": "top left",
		"-moz-transform-origin": "top left",
		"-webkit-transform-origin": "top left"
	});
}

function setSubmenu(){
	if(curItem=="bg"){
		$("#imgAL,#imgAC,#imgAR,#imgAT,#imgAM,#imgAB,#imgFront1,#imgFront,#imgBack1,#imgBack,#imgDel").css("opacity","0.1");
	}
	else{
		$("#imgAL,#imgAC,#imgAR,#imgAT,#imgAM,#imgAB,#imgFront1,#imgFront,#imgBack1,#imgBack,#imgDel").css("opacity","1");
		imgOrder();
	}
	if(isNaN($("#"+curItem).attr("textID"))){
		$("#btnTextEdit").css("opacity","0.1");
	}
	else{
		$("#btnTextEdit").css("opacity","1");
	}
}

function imgGet(){
	var query="";

	for(var i in img_arr){
		del=$("#"+img_arr[i]).parent().css("display");
		if(del!="none"){
			query+=img_arr[i]+"|";
		}
	}
	query=query.substring(0,query.length-1);

	return query;
}

function imgOrder(){
	var j=0,id,i;
	img_arr=[];

	$("[id^='img-']").each(function(){
		id=$(this).attr("id");
		i=id.substring(4,id.length);
		del=$("#"+id).parent().css("display");
		if(del!="none"){
			if(j<1){firstItem=id;}
			img_arr[j]=id;
			lastItem=id;
			j++;
		}
	});

	if(j<=1){
		$("#imgFront1,#imgFront,#imgBack1,#imgBack").css("opacity","0.1");
	}
	else{
		if(curItem==firstItem){
			$("#imgFront1,#imgFront").css("opacity","1");
			$("#imgBack1,#imgBack").css("opacity","0.1");
		}
		else if(curItem==lastItem){
			$("#imgFront1,#imgFront").css("opacity","0.1");
			$("#imgBack1,#imgBack").css("opacity","1");
		}
		else{
			$("#imgFront1,#imgFront,#imgBack1,#imgBack").css("opacity","1");
		}
	}
}

function toFront1(){
	if((curItem!="bg")&&(curItem!=lastItem)){
		q=imgGet();
		q=q.split("|");
		var i=q.indexOf(curItem);
		next=q[i+1];
		$($("#"+curItem).parent()).insertAfter($("#"+next).parent());
		imgOrder();
	}
}

function toFront(){
	if((curItem!="bg")&&(curItem!=lastItem)){
		q=imgGet();
		q=q.split("|");
		var i=q.indexOf(curItem);
		next=q[q.length-1];
		$($("#"+curItem).parent()).insertAfter($("#"+next).parent());
		imgOrder();
	}
}

function toBack1(){
	if((curItem!="bg")&&(curItem!=firstItem)){
		q=imgGet();
		q=q.split("|");
		var i=q.indexOf(curItem);
		next=q[i-1];
		$($("#"+curItem).parent()).insertBefore($("#"+next).parent());
		imgOrder();
	}
}

function toBack(){
	if((curItem!="bg")&&(curItem!=firstItem)){
		q=imgGet();
		q=q.split("|");
		var i=q.indexOf(curItem);
		next=q[0];
		$($("#"+curItem).parent()).insertBefore($("#"+next).parent());
		imgOrder();
	}
}

function imgDel(){
	$("#"+curItem).parent().hide();
	$("#bg").trigger("click");
}

function undoAdd(str){
	undo_arr.push(str);
	$("#imgUndo").css("opacity","1");
}

function undo(){
	if(undo_arr.length!=0){
		var str=undo_arr.pop();
		str=str.split("|");

		if(str[0]=="delete"){
			$("#"+str[1]).parent().show();
		}
		else if(str[0]=="ef"){
			$("#"+str[1]).attr("src",str[2]);
			if((str[3]!="undefined")&&(str[3]!="")){
				$("#"+str[1]).attr(str[3],str[4]);
			}
			if(str[5]!="undefined"){
				$("#"+str[1]).attr("textID",str[5]);
			}
		}
		else if(str[0]=="crop"){
			$("#"+str[1]).attr("src",str[2]);
			$("#"+str[1]).width(str[3]).height(str[4]);
			$("#"+str[1]).parent().width(str[3]).height(str[4]);
			if(str[5]!="undefined"){
				$("#"+str[1]).attr("textID",str[5]);
			}
		}
		$("#"+str[1]).trigger("click");

		if(undo_arr.length==0){
			$("#imgUndo").css("opacity","0.1");
		};
	}
}

function block(){
	$.blockUI({message:$("#aie-load")});
}

function unblock(){
	$.unblockUI();
}
