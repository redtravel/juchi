
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
		
	}
	//分类部分
	if($(".ul_type > li[class='active_type']").length == 0){
		$(".ul_type").css({borderBottom:'none',paddingBottom:'12px'})
	}
	/*
		$("#btnForMoreSize").addClass("btnForMoreListHover");
		$("#ul_size").css('height','58px');
		$("#btnForMoreSize").toggle(function(){
			$("#ul_size").css('height', 'auto');
			$(this).addClass("btnForMoreListOff");
		
		},function(){
			$("#ul_size").css('height', '58px');
			$(this).removeClass("btnForMoreListOff");
		})
		
	}else{
			$("#btnForMoreSize").addClass("btnForMoreListNormal");
	}
	
	if($("#ul_size > li[class='active_size']").length > 0){
		var tagSexTop=$("#ul_size > li[class='active_size']").offset().top;
		var allSexTop=$("#ul_size").offset().top;
		
		if(tagSexTop-allSexTop >= 58){
			$("#btnForMoreSize").addClass('btnForMoreListOff');
			$("#ul_size").css('height','auto');
		}
	}*/
})
//补货通知
/*$("#msgForAddGoods").focus(function(){ 
	if($("#msgForAddGoods").val() == "请输入邮箱或手机"){
		$(this).val("");
		$("#msgOfAddGoods").text("")
	}
})
$("#msgForAddGoods").blur(function(){ 
	if($("#msgForAddGoods").val() == ""){
		$("#msgForAddGoods").val("请输入邮箱或手机");
	}
})
$(".btnForAddGoods").click(function(){
	if($("#msgForAddGoods").val() !="" && $("#msgForAddGoods").val() !="请输入邮箱或手机"){
    	$("#msgOfAddGoods").text("开售前会短信通知您！");
		
	}else{
    	$("#msgOfAddGoods").text("请输入正确的邮箱地址或手机号码！");
	}								

})*/

//产品图层交互
$("#listMainBox li").hover(function(){
		$(this).children(".lihover").show();
	},function(){
		$(this).children(".lihover").hide();
	})


//lazyload
	$(function(){
		//alert('fuck');
		$("img[fslazy]").lazyload({
			threshold: 200,
			effect: "fadeIn"
		})
	});

