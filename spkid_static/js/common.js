//定义弹出窗口的handler,以便关闭弹窗
var lhgDG=null;
$(function(){
//搜索框
	$(".search_c .text").focus(function(){$(this).val("")});
//首页广告边框
	$(".pro_block").hover(
		function(){$('.pro_block_pic',$(this)).children('div').show();$(this).addClass('pro_block_sel');},
		function(){$('.pro_block_pic',$(this)).children('div').hide();$(this).removeClass('pro_block_sel');}
	);
//头部购物车
	$("#car").hover(
		function(){
			$(this).addClass("car_hover").removeClass("car");$(".car_pro").show();
			$('.car_pro_c').html('加载中...');
			$.ajax({
				url:'product_api/float_cart',
				data:{rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if (result.msg) {$('.car_pro_c').html(result.msg);};
					if (result.err) {return false;};
					if (result.html) {$('.car_pro_c').html(result.html);};
				}
			});
		},
		function(){$(this).addClass("car").removeClass("car_hover");$(".car_pro").hide();}				  
	);
	$(".car_pro").hover(
		function(){$(this).show();$("#car").addClass("car_hover").removeClass("car")},
		function(){$('.car_pro_c').html('');$(this).hide();$("#car").addClass("car").removeClass("car_hover");}
	);
//导航二级菜单
	$(".list_sec_left div:first-child").addClass("cl_b");
	$(".nav ul li").hover(
		function(){
			$(this).addClass("sel");
			rel=$(this).attr('rel');			 
			if($('.nav > div[rel='+rel+']').length) $('.nav > div[rel='+rel+']').show();
		},
		function(){
			$(this).removeClass("sel");
			rel=$(this).attr('rel');
			if($('.nav > div[rel='+rel+']').length) $('.nav > div[rel='+rel+']').hide();
		}
	)
	$(".nav .list_sec").hover(
		function(){
			$(this).show();
			rel=$(this).attr('rel');
			$('.nav ul li[rel='+rel+']').addClass("sel");
		},			
		function(){
			$(this).hide();
			rel=$(this).attr('rel');
			$('.nav ul li[rel='+rel+']').removeClass("sel");
		}
	);

	//搜索
	$(':input[name=kw]').focus(function(){
		if($(this).val()=='请输入你要找的商品'){
			$(this).val('');
		}else{
			$(this).select();
		}
	});
	$(':input[name=kw]').blur(function(){
		if(!$(this).val()){
			$(this).val('请输入你要找的商品');
		}
	});
	
	//购物车数量
	update_cart_num();

})

function check_kw_search(){
	var input_keyword=$(':input[name=kw]');
	var keyword=$.trim(input_keyword.val());
	if(keyword==''){
		input_keyword.val('请输入你要找的商品');
		return false;
	}
	if(keyword=='请输入你要找的商品') return false;
	return true;
}

function mycarousel_initCallback(carousel){
	$("#mycarousel li").mouseover(function(){
		var JQ_img = $("img", this);
		var mid_src = JQ_img.attr("middesc");
		var long_src = JQ_img.attr("longdesc");
		$("#_middleImage").attr("src", mid_src).attr("longdesc", long_src);
		$(this).siblings().each(function(){
			$("img", this).removeClass().addClass("curr_base");
		})
		JQ_img.addClass("cur_on");
	})
};

function number_format (number,dec) {
	number = parseFloat(number);
	orig = number;
	if(orig<1) number += 1;
	dec = parseInt(dec);
	number = Math.round(number*Math.pow(10,dec));
	number += '';
	l = number.length;
	int_part = number.substr(0,l-dec);
	if(orig<1) int_part = parseInt(int_part)-1;
	return int_part+'.'+number.substr(l-dec,dec);

}

function load_ad (target,page_name,category_id,brand_id,position_tag) {
	$.ajax({
		url:'ad/info',
		data:{page_name:page_name,category_id:category_id,brand_id:brand_id,position_tag:position_tag},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.err) return false;
			$(target).html(result.html).css('display','');
		}
	});
}

function load_liuyan (target,category_id,brand_id,kw) {
	$.ajax({
		url:'liuyan/newest',
		data:{category_id:category_id,brand_id:brand_id,kw:kw},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.err) return false;
			if(result.html)$(target).html(result.html).css('display','');
		}
	});
}

function setCookie(c_name,value,expiredays) {
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie=c_name+ "=" +escape(value)+ ((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
}

function getCookie(c_name) {
	if (document.cookie.length>0) {
		c_start=document.cookie.indexOf(c_name + "=");
		if (c_start!=-1) {
			c_start=c_start + c_name.length+1;
			c_end=document.cookie.indexOf(";",c_start);
			if (c_end==-1) c_end=document.cookie.length;
			return unescape(document.cookie.substring(c_start,c_end))
		}
	}
	return "";
}

function add_to_collect (product_id) {
	$.ajax({
		url:'product_api/add_to_collect',
		data:{product_id:product_id,product_type:0,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {alert(result.msg)};
			if (result.err) {return false};
			if (result.need_login) {
                                location.href='/user/login';
				return false;
			};
			lhgDG = new $.dialog({ id:'thepanel',bgcolor:'#333',titleBar:true,title:'收藏成功',iconTitle:false,btnBar:false,maxBtn:false,width:410,height:210,cover:true,html:$('#add_to_collect_msg')[0] });
			lhgDG.ShowDialog();
		}
	});
}

function goto_login (back_url) {
	$.ajax({
		url:'user_api/goto_login',
		data:{back_url:back_url,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			location.href=base_url+'user/login';
		}
	});
}

/**
 * 判断用户是否已登录,如未登录弹出浮窗登录
 * options.back_url 站外登录成功后跳转的地址
 * options.func 登录成功后执行的操作（如果已登录，则直接执行该操作）
 * options.reload boolean 登录成功后是否刷新本页
 * options.user_id 如果user_id>0则直接执行func
 */
function check_login(func){
	if(window.user_id!=undefined&&user_id>0) {(typeof func =='function')? func():eval(func);return}//如果已登录，则执行func
	//载入ajax登录接口
	//没完成
	lhgDG=new $.dialog({id:'loginWin',page:'user/login?mod=win'});
	lhgDG.ShowDialog();
	parent_dom.html("<img src='http://img.fclub.cn/images/loading_invite.gif'");
	$("#GoodsNumber").hide();
	parent_dom.dialog({
		title:'页面载入中，请稍候...',
		minHeight:80,
		width:520,
		position:'center',
		modal: true,
		open:function(){
			float_status = false;
			$.ajax({
				url:'/user.html?act=register&is_float=1',
				data:{back_url:options.back_url||'',rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.has_logined==1){
						float_status=true
						parent_dom.dialog('close');
					}else if(result.error==0){
						parent_dom.html(result.content);
						parent_dom.dialog('option','position','center');
						parent_dom.dialog('option','title','用户登录');
					}else{
						alert(result.message);
					}
				}
			});

		},
		close:function(){
			if (float_status==true) {
				float_login_reload = true;
				(typeof func =='function')?func():eval(func);
				if(reload) window.location.href=window.location.href;
				float_status=false;
			}
		$("#GoodsNumber").show();
			parent_dom.dialog('destroy');
			parent_dom.html("<img src='http://img.fclub.cn/images/loading_invite.gif'");
		}
	});
}

function addBookmark(title,url){if(window.sidebar){window.sidebar.addPanel(title,url,"");}else if(document.all){window.external.AddFavorite(url,title);}else if(window.opera&&window.print){return true;}}
//以中文字算长度
function cnlength(str){return Math.ceil(str.replace(/[^\x00-\xff]/g, "**").length/2)}

/*function show_pay_msg(order_id) {
	$('a#btn_pay_ok').attr('href','order/info/'+order_id);
	lhgDG = new $.dialog({ id:'thepanel',bgcolor:'#333',titleBar:true,title:'提示',iconTitle:false,btnBar:false,width:350,height:200,cover:true,html:$('#pay_msg')[0] });	
	lhgDG.ShowDialog();
	return true;
}*/
// End of onlinepay_alert
window.onbeforeunload = function(e){
    if(!getCookie('bookmark')){
        setCookie('bookmark',1,30);
        confirm("是否要将演示站加入收藏夹？");
        addBookmark('演示站','http://www.mammytree.com');
    }
};