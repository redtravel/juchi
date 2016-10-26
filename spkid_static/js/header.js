	//导航条内容
	//如果是点导航进来的 则高亮导航
	if(window.nav_id>0){
    	$('.mainMenuOn').removeClass('mainMenuOn');
	}
    $('li[xname='+window.nav_id+']').addClass('mainMenuOn');
	var currentIndex=$('#mainMenuUl>li').index($('.mainMenuOn'));
	$('#mainMenuUl>li').not($('.mainMenuOn')).hover(function(){
		var index = $(this).index();
		$(this).addClass("mainMenuOn");
		
		$(this).children("div").show();
		$(this).children(".menuSubBox").attr("id","mainMenuBox"+index);
		
		$('#mainMenuUl>li').eq(currentIndex).removeClass("mainMenuOn");
		
	}, function(){
		$(this).removeClass("mainMenuOn");
		$(this).children("div").hide();
		$('#mainMenuUl>li').eq(currentIndex).addClass("mainMenuOn");
	})
	//当前项的子内容显示
	$('.mainMenuOn').hover(function(){
		
		$(this).children("div").show();
		$(this).children(".menuSubBox").attr("id","mainMenuBox"+currentIndex);
		
		
	},function(){
		$(this).children("div").hide();
	}) 
	var menuLis=$("#mainMenuUl").children().length;
	for(var i=0; i<menuLis; i++){
		
		if(i+1==menuLis){
			$("#mainMenuUl").children().eq(i).attr("id","menuLast");
		}else{
			$("#mainMenuUl").children().eq(i).attr("id","menu"+i);
		}
		

		//$("#mainMenuUl").children(".menuSubBox").attr("id", "mainMenuBox"+i);
	}
	$("#forMen >dd").last().css("border-right","none");
	$("#forWomen >dd").last().css("border-right","none");

	//用户中心内容块
	$('#userInfobox').hover(function(){
		$(this).children("div").show();
	},function(){
		$(this).children("div").hide();
	});

	
//底部订阅信息
$(function($){
        update_cart_num(); // 更新购物车数量
       /**
       	* 检查用户是否登录 并更新头部信息
       	*/
      	$.post('/user/check_is_login',function(data){
            if(data.is_login==true){//已登录
                $('#menu_visitor').css('display','none');
                $('#menu_user').css('display','block');
                $('#welcome_msg').html("[您好 "+data.user_nick+"]");
            }
        },'json');

	//加入收藏
	$("#addFavirate").click(function(){
		addFavorite();	
	})
	//底部短信订阅
	$("#foot_notice_input").focus(function(){ 
		if($("#foot_notice_input").val() == "请输入邮箱或手机"){
			$(this).val("");
		}
		$("#footInputInfo")[0].style.display= 'none';
	})
	$("#foot_notice_input").blur(function(){ 
		if($("#foot_notice_input").val() == ""){
			$("#foot_notice_input").val("请输入邮箱或手机");
		}
	})
	$("#cancelInputCall").focus(function(){ 
		if($("#cancelInputCall").val() == "请输入手机号码"){
			$(this).val("");
			$("#cancelInputCall").text("")
		}
	})
	$("#cancelInputCall").blur(function(){ 
		if($("#cancelInputCall").val() == ""){
			$("#cancelInputCall").val("请输入手机号码");
		}
	})
	$("#cancelInputMail").focus(function(){ 
		if($("#cancelInputMail").val() == "请输入您的邮箱"){
			$(this).val("");
			$("#cancelInputMail").text("")
		}
	})
	$("#cancelInputMail").blur(function(){ 
		if($("#cancelInputMail").val() == ""){
			$("#cancelInputMail").val("请输入您的邮箱");
		}
	})
	
	
	
	//订阅
	$("#mainFOrder").click(function(){
    var notice_val=$("#foot_notice_input").val();
		if(notice_val=='' ||notice_val=='请输入邮箱或手机'){
			  $("#footInputInfo").show();
		}
    else{
        var vali=new Validate();
        if(vali.isPhone(notice_val)||vali.isMail(notice_val)){
            rush_notice(-1,notice_val);
        }
        else{
			    $("#footInputInfo").show();
        }
		}						
	})
	$("#closeSucOrderMsg").click(function(){
		$("#add_notice_msg").hide();
		delIframe('add_notice_msg');
	})
	//取消订阅
	$("#mainBtnCancel").click(function(){
		
		newIframe('cancelOrderMsg');
		$("#cancelOrderMsg").show();
		if(window.XMLHttpRequest){
			$("#cancelOrderMsg").css({top:'150px'});
		}
	})
	$("#closeCancelOrderMsg").click(function(){
		$("#cancelOrderMsg").hide();
		delIframe('cancelOrderMsg');
	})
    //取消订阅请求
    $("#cancel_notice").click(function(){
        var phone=$("#cancelInputCall").val();
        var mail=$("#cancelInputMail").val();
        phone=phone.replace(/^ *| *$/, '');
        mail=mail.replace(/^ *| *$/, '');
        if(phone=="请输入手机号码"&&mail=="请输入您的邮箱"){
            alert("请填写手机号码或邮件");
            return;
        }
        var validate=new Validate();
        if(phone!="请输入手机号码"){
            if(validate.isPhone(phone)==false){
                alert("请输入正确的手机号码");
                return;
            }
        }
        if(mail!="请输入您的邮箱"){
            if(validate.isMail(mail)==false){
                alert("请输入正确的邮箱");
                return false;
            }
        }
        $.post("/rush_notice/cancel_rush_notice",
            {'rush_id':-1,'phone':phone,'mail':mail},
            function(data){
                 $("#mainIconMsg").html(data.msg);
                 $("#notice_tip").html('温馨提示:'+data.tip);
                 $("#add_notice_msg").show();
                 if(window.XMLHttpRequest){
                     $("#add_notice_msg").css({top:'150px'});
                 }
                 newIframe('add_notice_msg');
                 $("#closeCancelOrderMsg").click();
            },'json');
   });

    // 购物车弹窗显示
    //完善hover事件响应滞留
    var mainCartLinkState=false;
	$("#mainCartLink").hover(
       function(){
       		$("#mainCartBox").show();
            $.ajax({
                url: '/cart/info',
                type: 'POST',
                dataType: 'json',
                success: function(result) {
					if(result.msg) alert(result.msg);
					if(result.err) return false;
					$("#mainCartBox").html(result.html); 
					update_cart_num ();
					if (result.nil == 1) {
						$("#mainCartBox").addClass("mainCartBoxNull");
					}
					$('#mainCartNum').html(result.cart_number + "件");
				}
		});
    },function(){
        $("#mainCartBox").hide();
		
    })
})

/**
 * 检测订阅输入信息是否是手机或邮箱
 */
function checkRushNoticeInput(val){
    var validate=new Validate();
    if(validate.isMail(val)) return true;
    if(validate.isPhone(val)) return true;
    return false;
}

/*
 * 发送订阅请求
 */
function rush_notice(rushId,noticeVal){
  $.post("/rush_notice/add_rush_notice",
         {rush_id:rushId,param:noticeVal},
         function(data){
             $("#mainIconMsg").html(data.msg);
             $("#notice_tip").html('温馨提示:'+data.tip);
			       $("#add_notice_msg").show();
						 if(window.XMLHttpRequest){
							   $("#add_notice_msg").css({top:'150px'});
						 }
						 newIframe('add_notice_msg');
             $('#greenBox').css('display','none');
             $('#closeGreenWindow').click();
          },'json');
}

function update_cart_num () {
    cart_num=getCookie('cart_num');
    if(cart_num) {
        $('#head_cart_num').html('购物袋 ' + cart_num + " 件"); // 非团购页面购购车数量
        $('#numMyCart').html(cart_num);
    }
}
function addFavorite() {
    if (document.all) {
        window.external.addFavorite('http://www.mammytree.com', '演示站');
    } else if (window.sidebar) {
        window.sidebar.addPanel('演示站', 'http://www.mammytree.com', '');
    } else {
        alert('您的浏览器不支持加入收藏，请用 ctrl+d 收藏演示站');
    }
} 
