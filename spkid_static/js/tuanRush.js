$(function(){
		//通栏广告部分
  		BannerCarousel();
	})
//按钮交互
	$("#tuanForView").hover(function(){
		$("#btnForView").addClass("btnForView");
	},function(){
		$("#btnForView").removeClass("btnForView");
	});

	$("#tuanForView").hover(function(){
		$("#boxForView").show();
	},function(){
		$("#boxForView").hide();
	});
	$("#ulTuanRush>li").hover(function(){
		$(this).children(".greenBorderBox").show();
	},function(){
		$(this).children(".greenBorderBox").hide();
	});
	$("#moveForEarly").click(function(){
		if($("#boxForViewUl1").css('left')=="0px"){
			$("#moveForRecent").removeClass("bWFont");
			$("#boxForViewUl1").animate({
				left:"-535px"
			},500);
			$("#moveForEarly").addClass("bWFont");
		}
		
	});
	$("#moveForRecent").click(function(){
		if($("#boxForViewUl1").css('left')=="-535px"){
			$("#moveForEarly").removeClass("bWFont");
			$("#boxForViewUl1").animate({
				left:"0px"
			},500);
			$("#moveForRecent").addClass("bWFont");
		}
		
	});