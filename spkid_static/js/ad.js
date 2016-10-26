// JavaScript Document
function BannerCarousel(){
	var iBannerObj=document.getElementById("allAdBox");
	if(!iBannerObj){
		return;
	}
	if($('#allBannerUl li').length<1){
		$("#allAdBox").remove();
		return;
	}

	var bToDownBtn=document.getElementById("btnSwitchAllAd");//切换按钮
	var iBannerUl=iBannerObj.getElementsByTagName("ul");
	var index=0;
	var lis=iBannerUl[1].getElementsByTagName("li");
	var lisLen=lis.length;
	if(lisLen>1){
		var pageLis=iBannerUl[0].getElementsByTagName("li");
		if(pageLis.length == lisLen){
			for(var i=0; i<lisLen; i++){
				$("#boxBtnAllAd li").eq(i).text(function(){
					return i+1;
				})
			}
		}
		var s=null;
		var i=0;
		function showAtIndex(p,n){
			$(lis[p]).fadeOut(300).removeClass("Active");
			$(pageLis[p]).removeClass("activeBtnLiAllAd");
			if(n===lisLen){
				n=0;
			}
			index=n;
			if($(lis[n]).find('img').attr("src")=='' || $(lis[n]).find('img').attr("src")==undefined){
				$(lis[n]).find('img').attr("src", $(lis[n]).find('img').attr("psrc"));
				//console.log(n);
			}
			$(lis[n]).fadeIn(300).addClass("Active");
			$(pageLis[n]).addClass("activeBtnLiAllAd");
		}
		//循环显示图片
		s=setInterval(function(){
			showAtIndex(index,(index+1));
		},3000);
		//添加mouseover事件
		for(i=0;i<lisLen;i++){
			pageLis[i].onmouseover=function(i){
				return function(){
					if(index!==i){
						showAtIndex(index,i);
					}
				clearInterval(s);
				}
			}(i);
			pageLis[i].onmouseout=function(){
				s=setInterval(function(){
					showAtIndex(index,(index+1));
					},3000);
				};
			}
		}
		var isPutAway=false;
		//初始化
		var st=setTimeout(function(){
		$(iBannerObj).animate({"height":"40px"},200);
		bToDownBtn.setAttribute("title","展开");
		$(bToDownBtn).addClass("btnSwitchAllAdDown");
		isPutAway=true;
		$("#boxBtnAllAd").hide("slow");
	},6000);
	//展开收起功能
	bToDownBtn.onclick=function(){
	if(st){
	clearTimeout(st);
	}
	$(iBannerObj).stop(true,true);
	if(isPutAway){
		$(iBannerObj).animate({"height":"250px"},200);
		bToDownBtn.setAttribute("title","收起");
		$(bToDownBtn).removeClass("btnSwitchAllAdDown");
		$("#boxBtnAllAd").show();
	}else{
		$(iBannerObj).animate({"height":"40px"},200);
		bToDownBtn.setAttribute("title","展开");
		$(bToDownBtn).addClass("btnSwitchAllAdDown");
		$("#boxBtnAllAd").hide();
	}
	isPutAway=!isPutAway;
	}
}
