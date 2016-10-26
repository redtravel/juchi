$(function(){

	//通栏广告部分
  	BannerCarousel(); 
	//对联广告及通栏广告
	var sideAd={};
		if(document.getElementById("side_ad_url")!=null){
			sideAd.Width=$("#side_ad_url").attr("width");
			sideAd.Height=$("#side_ad_url").attr("height");
			sideAd.Start=$("#side_ad_url").attr("start");
			sideAd.End=$("#side_ad_url").attr("end");
			sideAd.ImgPath=$("#side_ad_url").attr("bigimg");
			sideAd.Create=function(){
				var sideDiv=document.createElement("div");
				sideDiv.id="sideBox";
				sideDiv.className="sideBox";
				document.body.insertBefore(sideDiv,document.getElementById("side_ad_url"));
				//document.body.appendChild(sideDiv);
				sideDiv.style.height=sideAd.Height+"px";
				//sideDiv.style.width=sideAd.width+"px";
				sideDiv.style.background="url("+sideAd.ImgPath+") top center no-repeat";
				
			}
			sideAd.Create();
		}
	var allAd={
			init:function(){
				allAd.closeBar();
			},
			closeBar:function(){
				$("#boxBtnAllAd").hide("slow");
				$("#allAdBox").animate({
					height:"40px"
				},500)
				
			}
		
	};
	
	//推广广告倒计时
    //varOff=1376950385858; //时间戳格式;
	//varOff=2013-04-08 00:00:00 日期时间格式
	
	varOff=$("#banerTimeOffImg").attr("end_time");
	timeOff(dateUTC(varOff));
	
	lastDayOff();
	
	//$("img").attr("src",function(){return $(this).attr("fslazy")});
})
	
	//轮播图
	/*var t = n = count = 0;
	$(function(){
		count = $("#FocusImgLi a").size();
		$("#FocusImgLi a:not(:first-child)").hide();
		$("#dotGroup span:first-child").addClass("dotActive");
		$("#dotGroup span").mouseenter(function() {
			var now=$(".dotActive").index();
			if($(this).text()-1 != now && ($("#FocusImgLi li").eq(now).css('opacity')>='0.8')){
				console.log(now);
				var i = $(this).text() - 1;
				n = i;
				if (i >= count) return;
				$("#FocusImgLi li").filter(":visible").fadeOut(500).parent().children().eq(i).fadeIn(1000);
				$(this).addClass("dotActive").siblings().removeClass("dotActive"); 
			}
		});
		t = setInterval("showAuto()", 4000);
		$(".iContainFocusImg").hover(function(){clearInterval(t)}, function(){t = setInterval("showAuto()", 4000);});
	})
	
	function showAuto()
	{
		n = n >= (count - 1) ? 0 : n + 1;
		$("#dotGroup span").eq(n).trigger('mouseenter');
	}*/
	var now = 0,t;
	$(function () {
	var bannerBtn = $('#dotGroup span');
	var length = bannerBtn.length;
	var bannerImg = $('#FocusImgLi li');
		$('#FocusImgLi li:not(:first-child)').hide();
		bannerBtn.mouseover(function fade() {
			now=$(this).index();
			//alert(now);
			if ($('.dotActive').index()!=now && bannerImg.eq(now).css('opacity')=='1') {
				bannerBtn.removeClass('dotActive');
				$(this).addClass('dotActive');
				bannerImg.css('zIndex',1000).fadeOut(500);
				if($("#FocusImgLi li").eq(now).find('img').attr("src")=='' || $("#FocusImgLi li").eq(now).find('img').attr("src")==undefined){
					var path=$("#FocusImgLi li").eq(now).find('img').attr("psrc");
					
					$("#FocusImgLi li").eq(now).find('img').attr("src", path);
					
				}
				bannerImg.eq(now).css('zIndex',2000).fadeIn(500);
			}
		});
		t=setInterval('autoplay()',3000);
		$('.iContainFocusImg').hover(function () {
			clearInterval(t);
		},function () {
			t=setInterval('autoplay()',3000);
		});
	
	});
	function autoplay() {
		now=now>$('#dotGroup span').length-1?0:++now;
		
		if($("#FocusImgLi li").eq(now).find('img').attr("src")=='' || $("#FocusImgLi li").eq(now).find('img').attr("src")==undefined){
			var path=$("#FocusImgLi li").eq(now).find('img').attr("psrc");
			
			$("#FocusImgLi li").eq(now).find('img').attr("src", path);
			
		}
		$('#dotGroup span').eq(now).trigger('mouseover');
	}
	
	// 爱分享
	$(".hot_share").hover(function(){
		$(this).find('.first_wei').stop().animate({height:76},200);
	},function(){
		$(this).find('.first_wei').stop().animate({height:30},200);
	});

	//即将出售的品牌
  //初始化第一个滚动条
  if($('#div_rush_brand_0')[0]){
		InitScrollBar("div_rush_brand_0","tomrrowLogo0","scrollbarBox","scrollbar",280,"scrollBarTop","scrollBarBottom");
  }
	$('#tabForNew li').click(function(){
		var index=$(this).attr('index');
    var li_css=['liHoverTabForNewLeft','liHoverTabForNewCenter','liHoverTabForNewCenter','liHoverTabForNewRight'];
    for(var i=0;i<li_css.length;i++){
		    $('.'+li_css[i]).removeClass(li_css[i]);
    }
		$(this).addClass(li_css[index]);
		$('.dateContentForNews .blockBrand').removeClass('blockBrand').css('display','none');
		$('#div_rush_brand_'+index).addClass('blockBrand').css('display','block');
		InitScrollBar("div_rush_brand_"+index,"tomrrowLogo"+index,"scrollbarBox","scrollbar",280,"scrollBarTop","scrollBarBottom");
		});
	var $liWillShow=$("#tabUl li");
	
	$liWillShow.hover(function(){
		$(this).addClass("iWillShowOn")
			   .siblings().removeClass("iWillShowOn");
		var index=$liWillShow.index(this);
		$("div.iContainXQWill")
			.eq(index).addClass("blocks")
			.siblings().removeClass("blocks");
		var i=$("div.iContainXQWill").eq(index).find("img[src='']");
		if(i.length>0){
			i.each(function(){
				$(this).attr("src", $(this).attr("psrc"));
			})
		}
	},function(){
		var index=$liWillShow.index(this);
		$("div.iContainXQWill")
			.eq(index).hide();
	})

	//开售通知
	$(".iWillShowCallMe").click(function(){
		window.rid=$(this).attr('rid');
			var bodyWidth=document.body.clientWidth;
			$("#greenBox").show();		
			if(window.XMLHttpRequest){
				$("#greenBox").css({top:'150px'});
			}
			newIframe('willSaleMsg');
      return false;
		})
	$("#closeGreenWindow").click(function(){
			$("#greenBox").hide();
			delIframe('willSaleMsg');
	})

	//限抢部分交互效果
	
	$(".hotArea").hover(function () {
		if ($(this).find('.iXQShadowBoxRed').css('opacity')=='0') {
			$(this).find('p.newsBoxForMove').stop().fadeTo(100,0.85);
			$(this).find('.iXQShadowBoxRed').stop().fadeTo(100,1);
		}
	},function () {
		$(this).find('.newsBoxForMove').fadeTo(100,0);
		$(this).find('.iXQShadowBoxRed').fadeTo(100,0);
	});
	
	//弹窗订阅
	$("#orderForIndexBox").focus(function(){
		if($("#orderForIndexBox").val() == "请输入手机号码或邮箱"){
			$(this).val("");
			$("#orderForIndexBox").text("")
		}
	})
	$("#orderForIndexBox").blur(function(){
		if($("#orderForIndexBox").val() == ""){
			$("#orderForIndexBox").val("请输入手机号码或邮箱");
		}
	})
	window.rid;
	$(".dateContentForNew ul li").hover(function(){
		var rush_id=$(this).attr('pop_rush_id');
		var rush_brand=$(this).attr('pop_rush_brand');
		var rush_category=$(this).attr('pop_rush_category');
		var rush_img=$(this).attr('pop_rush_img');
		//$(".willShowLogoInfoWin").attr("id", "brand_"+$rid);
		$("#pop_rush_img").attr("src",rush_img);
		$("#pop_rush_brand").text(rush_brand);
		$("#pop_rush_category").text(rush_category);
		$(".willShowLogoInfoWin").show(); 
		$(this).addClass("onHoverWillShow");
		window.rid=rush_id;
		
	},function(){
		$(".willShowLogoInfoWin").hide();
		$(this).removeClass("onHoverWillShow");
		
	})
	$(".willShowLogoInfoWin").mouseenter(function(){
			$("#"+window.rid).addClass("onHoverWillShow");
			$(this).show();
		})
	$(".willShowLogoInfoWin").mouseleave(function(){
			$("#"+window.rid).removeClass("onHoverWillShow");
			$(this).hide();
		})




    //$('#btnForOrderThisBrand').click(function(){ 
	   function btnForOrderThisBrand(){
		   
          var inputText = $("#orderForIndexBox").val();
          var vali=new Validate();
          if (inputText == '') {
              $("#msgForRushNotice").css("display","block");
              return false;
          }
          var sub = inputText.replace(/^ *| *$/, '');
          if (sub != '' && sub.indexOf('@') == -1) {
              if(!vali.isPhone(sub)){
                  $("#msgForRushNotice").css("display","block");
                  return false;
              }  
          } 
          else if (sub != '' && sub.indexOf('@') != -1) {
              if(!vali.isMail(sub)){
                  $("#msgForRushNotice").css("display","block");
                  return false;
              } 
          } 
		      var posWinTop=$(window).scrollTop()+150+"px";
		      var posWinLeft=Math.abs(($("body").width()-415)/2)+"px";
          rush_notice(window.rid,sub);
          return false;
	   }
     //});
  $(function(){
       /**
       * 注册添加订阅按钮事件
       */
      $('#add_notice').click(function(){
          var input_val=$('#notice_input').val();
          if(checkRushNoticeInput(input_val)){
              rush_notice(window.rid,input_val); 
          }
          else{
              $('.warnMsg').css('display','block');
          }
      });
      
  });


$(function () {
	$('.hot_share a:odd p').each(function(){
		if($.trim($(this).html())=='') $(this).remove()
	});
});
//lazyload
	$(document).ready(function(){
		$("img[fslazy]").lazyload({
			threshold: 200,
			effect: "fadeIn"
		})
	});
