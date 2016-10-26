$(function(){
	$('#spkidUlList li').each(function(){
		$(this).hover(function(){
			$(this).find("div").eq(1).show();
		},function(){
			$(this).find("div").eq(1).hide();
		})
	});
	$(".select_type").hover(function(){
			$('.bytype').hover(function () {
	        $(this).find('span').css("background","url(http://static1.baobeigou.com/img/plist/plist_bg.png) 0 -482px no-repeat");
		    $(this).find('.hide_type').show();
		    $(this).find('.hide_type').append()
		},function () {
		    $(this).find('.hide_type').fadeOut(100);
	        $(this).find('span').css("background","url(http://static1.baobeigou.com/img/plist/plist_bg.png) 0 -510px no-repeat");
		});
	});
	
})

//品牌及尺码默认显示及展开交互
$(function(){
	var $numBrand=parseInt($('#brand_ul').css('height'));
	//超过两行
	if($numBrand>58){
		//有选中项
		if($("#brand_ul > li[class='active_type']").length >0){
			var tagTop=$("#brand_ul > li[class='active_type']").offset().top;
			var allTop=$("#brand_ul").offset().top;
			//选中项在两行下面
			if(tagTop-allTop >= 58){
				$("#btnForMoreBrand").addClass('btnForMoreListOff');
				$("#brand_ul").css('height','auto');
				$("#btnForMoreBrand").toggle(function(){
					$("#brand_ul").css('height','58px');
					$(this).removeClass("btnForMoreListOff");
					$(this).addClass("btnForMoreListHover");
				},function(){
					$("#brand_ul").css('height','auto');
					$(this).removeClass("btnForMoreListHover");
					$(this).addClass("btnForMoreListOff");
					
				})
			}else{
			//选中项在前两行
				$("#btnForMoreBrand").addClass('btnForMoreListHover');
				$("#brand_ul").css('height','58px');
				$("#btnForMoreBrand").toggle(function(){
					$("#brand_ul").css('height','auto');
					$(this).removeClass("btnForMoreListHover");
					$(this).addClass("btnForMoreListOff");
				},function(){
					$("#brand_ul").css('height','58px');
					$(this).removeClass("btnForMoreListOff");
					$(this).addClass("btnForMoreListHover");
				})
			}
		}else{
		//无选中项且超过2行
			$("#btnForMoreBrand").addClass('btnForMoreListHover');
			$("#brand_ul").css('height','58px');
			$("#btnForMoreBrand").toggle(function(){
				$("#brand_ul").css('height','auto');
				$(this).removeClass("btnForMoreListHover");
				$(this).addClass("btnForMoreListOff");
			},function(){
				$("#brand_ul").css('height','58px');
				$(this).removeClass("btnForMoreListOff");
				$(this).addClass("btnForMoreListHover");
			})
		}
	
	}else{
		$("#btnForMoreBrand").addClass("btnForMoreListNormal");
		$("#btnForMoreBrand").removeClass("btnForMoreListHover");
		$("#brand_ul").css("paddingBottom","2px");
		
	}
	//尺码表
	var $numSize=parseInt($("#ul_size").css("height"));
	//超过2行
	if($numSize>66){
		//有选中项
		if($("#ul_size > li[class='active_size']").length >0){
			var tagSizeTop=$("#ul_size > li[class='active_size']").offset().top;
			var allSizeTop=$("#ul_size").offset().top;
			//选中项在两行下面
			if(tagSizeTop-allSizeTop >= 66){
				$("#btnForMoreSize").addClass('btnForMoreListOff');
				$("#ul_size").css('height','auto');
				$("#btnForMoreSize").toggle(function(){
					$("#ul_size").css('height','58px');
					$(this).removeClass("btnForMoreListOff");
					$(this).addClass("btnForMoreListHover");
				},function(){
					$("#ul_size").css('height','auto');
					$(this).removeClass("btnForMoreListHover");
					$(this).addClass("btnForMoreListOff");
					
				})
			}else{
			//选中项在前两行
				$("#btnForMoreSize").addClass('btnForMoreListHover');
				$("#ul_size").css('height','66px');
				$("#btnForMoreSize").toggle(function(){
					$("#ul_size").css('height','auto');
					$(this).removeClass("btnForMoreListHover");
					$(this).addClass("btnForMoreListOff");
				},function(){
					$("#ul_size").css('height','66px');
					$(this).removeClass("btnForMoreListOff");
					$(this).addClass("btnForMoreListHover");
				})
			}
		}else{
		//无选中项且超过2行
			$("#btnForMoreSize").addClass('btnForMoreListHover');
			$("#ul_size").css('height','66px');
			$("#btnForMoreSize").toggle(function(){
				$("#ul_size").css('height','auto');
				$(this).removeClass("btnForMoreListHover");
				$(this).addClass("btnForMoreListOff");
			},function(){
				$("#ul_size").css('height','66px');
				$(this).removeClass("btnForMoreListOff");
				$(this).addClass("btnForMoreListHover");
			})
		}
	
	}else{
		$("#btnForMoreSize").addClass("btnForMoreListNormal");
		$("#btnForMoreSize").removeClass("btnForMoreListHover");
		
	}
	//分类部分
	if($(".ul_type > li[class='active_type']").length == 0){
		$(".ul_type").css({borderBottom:'none',paddingBottom:'12px'})
	}
	
})
// 判断是否多行及控制空余BUG	
function clearBug(obj){
	if(obj){
		var len=obj.length;
		if(len>0){
			var fnode=obj[0].offsetTop;
	        for(var i=1; i<len; i++){
		        if(fnode < obj[i].offsetTop){
		            obj[i].style.clear='both';
		            break;
		        }
		    }
		}
	}
}


if($('.show_type_ul>li')){
	var obj=$('.show_type_ul>li');
	var len=obj.length;
	if(len>0){
	    var fnode=obj[0].offsetTop;
	    
	    for(var i=1; i<len; i++){
	        if(fnode < obj[i].offsetTop){
	            obj[i].style.clear='both';
	            break;
	        }
	    }
	}
}
if($('.ul_type>li')){
	var object=$('.ul_type>li');
	var leng=object.length;
	if(leng>0){
	    var fnodes=object[0].offsetTop;
	    
	    for(var j=1; j<leng; j++){
	        if(fnodes < object[j].offsetTop){
	            object[j].style.clear='both';
	            break;
	        }
	    }
	}
}
//产品图层交互
$(".J_pro_items").hover(function(){
		//$(this).children(".lihover").show();
		$(this).css({backgroundColor:"#fff3fb", border:"1px solid #f00"})
	},function(){
		$(this).css({backgroundColor:"#fff", border:"1px solid #fff"})
		//$(this).children(".lihover").hide();
	})



//lazyload
	$(function(){
		
		$("img[fslazy]").lazyload({
			threshold: 200,
			effect: "fadeIn"
		})
	});
