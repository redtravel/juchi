
	//右边绿红按钮的交互
	/*$("#tuanUlList>li").hover(function(){

		$(this).children("div").children('a').addClass("btnBuyNow2Red");
	},function(){
		$(this).children("div").children('a').removeClass("btnBuyNow2Red");
	});*/
//绿按钮的点击 购买闪框弹出btnBuyNow1
	$("#btnBuyNow0").click(function(){
		$("#tuanShadowBox").show();
	});
	$(".btnBuyNow1").click(function(){
		$("#tuanShadowBox").show();
	});
	$("#closeShadowBox").click(function(){
		$("#tuanShadowBox").hide();
	});

	//产品信息tab切换
	$("#ulTuanTab>li").click(function(){
		var index=$("#ulTuanTab>li").index($(this));
		$(".onTabShow").removeClass("onTabShow");
		$("#tuanTabCon>div:visible").hide();
		$(this).addClass("onTabShow");
		$("#tuanTabCon"+(index+1)).show();
	});

	/*倒计时
     *广告倒计时
     *
     */
    
	function timeOff(varOff){
		var i=setInterval(function countDown(){
		//计算差值
			var now=new Date();
			//varOff = 1366950385858;
			//endTime=Date(varOff);
			//varOff=now.getTime(endTime);
			if(varOff <= now.getTime()){
				return false;
			}else{
				varSub=Math.floor((varOff - now.getTime())/1000);
				varHour=Math.floor(varSub/3600);
				varMinute=Math.floor((varSub%3600)/60);
				varSecond=varSub%3600%60;
				$('#tuanCH').text(varHour);
				$('#tuanCM').text(varMinute);
				$('#tuanCS').text(varSecond);
				if(varHour <0 || varMinute <0 || varSecond <0){
					clearInterval(i);
					$("#tuanComeDown").html("时间已到期！");
				}
  			}
		},1000);
	}
	var t=new Date("Tue Jun 19 2013 10:09:18 GMT+0800");
	timeOff(t);


	//导航条位置控制
	//var $posTopMenu=$('#tuanBtnMove').offset().top;
	var posTopMenu=window.innerHeight;
	if(typeof posTopMenu != 'number'){
		if(document.compatMode == 'CSS1Compat'){
			posTopMenu=document.documentElement.clientHeight;
		}else{
			posTopMenu= document.body.clientHeight;
		}
	}
	$(window).scroll(function(){
		var $posLeftMenu=($(window).width()-1000)/2;
		if($(document).scrollTop() >= posTopMenu){
			if($(window).width()<1000){
				if(window.XMLHttpRequest){
					$('#tuanBtnMove').css({position:"fixed", top:0, left:0, zIndex:999});
				}else{
					$('#tuanBtnMove').css({position:"absolute", left:0, zIndex:999});
					$('#tuanBtnMove').addClass('forIe6Menu');
				}
			}else{
				if(window.XMLHttpRequest){
					$('#tuanBtnMove').css({position:"fixed", top:0, left:$posLeftMenu,  zIndex:999});
				}else{
					$('#tuanBtnMove').css({position:"absolute", left:$posLeftMenu, zIndex:999});
					$('#tuanBtnMove').addClass('forIe6Menu');
				}
			}
		}else{
			if(!window.XMLHttpRequest){
				$('#tuanBtnMove').removeClass('forIe6Menu');
			}
			$('#tuanBtnMove').removeAttr("style");
		}
	})
	
	$(window).resize(function(){
		var $posLeftMenu=($(window).width()-1000)/2;
		var $winWidth=$(window).width();
		if($winWidth<1000){
			if(window.XMLHttpRequest){
				
				$('#tuanBtnMove').css({position:"fixed", left:0, zIndex:999});
			}else{
				$('#tuanBtnMove').css({position:"absolute", left:0, zIndex:999});
				$('#tuanBtnMove').addClass('forIe6Menu');
			}
		}else{
			if(window.XMLHttpRequest){
				$('#tuanBtnMove').css({position:"fixed", left:$posLeftMenu, zIndex:999});
			}else{
				$('#tuanBtnMove').css({position:"absolute", left:$posLeftMenu, zIndex:999});
				$('#tuanBtnMove').addClass('forIe6Menu');
			}
		}
	})

	//8个保证等icon的载入  
	/*$("#iconQA").click(function(){
		$("#loadContentBox").html($(".pointOut").html());
	});

	$("#icon7Day").click(function(){
		$("#loadContentBox").html($("#tuanTabCon1").html());
	})*/


	//文字说明初始化以";"为标志
	/*var t=$("#tuanBuyNote>p").html(),
	text=t.split(';');
	$("#tuanBuyNote").html("");
	for(var i=0; i<text.length; i++){
	    var p=document.createElement('p');
	    p.innerHTML=text[i];
	    $("#tuanBuyNote").append(p);
	}*/	
	
	
	//缓加载QQ分享js
	$(function(){
		var js=document.createElement('script');
		js.src="http://connect.qq.com/widget/loader/loader.js";
		js.widget="shareqq";
		js.charset="utf-8";
		document.body.appendChild(js);
	}) 