$(function () {
	$('.nav_count').show();
});
/*
// 点击颜色图片时触发的动作
// 布置好尺码选项
// 如果尺码数量为1，则触发 click_size
// 重置图片
*/
function click_color(color_id){
	if (color_id == current_color_id) return;
	//清场
	current_color_id = color_id;
	current_size_id = 0;
	$('#current_sub').html('');
	$('dd.size').html('');
	$(':input[name=num]').value = 1;
	$(':hidden[name=sub_id]').val('');
	$('dd.color span').removeClass('sel');
	
	//赋值	
	$('dd.color').find('span#color_'+color_id).addClass('sel');
	$('#current_sub').html('"'+sub_list[color_id]['color_name']+'"');
	
    // todo: 重置图片
	reset_gallery(color_id);
        
	// 重置尺码	
	var html = '';
	var num_html = '请先选择尺码';
	var enabled_size = new Array();
	for(i in sub_list[color_id]['sub_list']){		
		sub = sub_list[color_id]['sub_list'][i];
		if(sub.is_on_sale>0 ){
			if (sub.sale_num>0 ) {
				enabled_size.push(sub.size_id);
				html += '<a href="javascript:void(0)" id="size_'+sub.size_id+'" onclick="click_size('+sub.size_id+')">'+sub.size_name+'<s></s></a>';
			} else {
				html += '<a href="javascript:void(0)" class="outofsale" title="已售完">'+sub.size_name+'<s></s></a>';
			}
		}else{
			num_html = '所有尺码已售空';
		}
	}
	$('dd.size').html(html);
	if(html=='') $('dd.size').html('所有颜色已售空');
	if(enabled_size.length==1){
		click_size(enabled_size[0]);
	}else {//该尺码下无库存
		$('dd.number').html(num_html);
	}

}

function click_size (size_id) {
	if (current_size_id == size_id) {return;};
	//清场
	current_size_id = size_id;
	var num_sel = $(':input[name=num]');//num select: $(':input[name=num]')[0]
	num_sel.value=1;
	$('dd.size a').removeClass('sel');
	$(':hidden[name=sub_id]').val('');
	
	var sub = null;
	for(i in sub_list[current_color_id]['sub_list']){
		sub = sub_list[current_color_id]['sub_list'][i];
		if(sub.size_id==size_id) break;
	}
	
	$(':hidden[name=sub_id]').val(sub.sub_id);	
	$('dd.size').find('a#size_'+size_id).addClass('sel');
	$('span#current_sub').html('"'+sub.color_name+'"、"'+sub.size_name+'"');
	
	//尺码对应库存 判断
	var html = '<dd class="number"><a class="down">-</a><input type="text" name="num" id="num" value="1" disabled="disabled"/><a class="up">+</a>&nbsp;';
	if(sub.sale_num <= 2 && sub.sale_num > 0 ) {//库存紧张
		html += '<font class="c66">库存紧张,请抓紧抢购</font>';
	}
	html += '</dd>';
	
	$('dd.number').html(html);
	if(sub.sale_num <= 0 ) $('dd.number').html('所有尺码已售空');
	
	//数量+ - 限制
	num_up_down(sub.sale_num );
}

function reset_gallery (color_id) {
	var container = $('#product_img_pingpu');	
	var gs = g_list[color_id];
        //初始化幻灯区
	$('#_middleImage').attr('src',img_host+'/'+gs['default'].img_url+'.418x418.jpg');//img_270_360
	share_pic=img_host+'/'+gs['default'].img_url+'.418x418.jpg';
	$('#_middleImage').attr('longdesc',img_host+'/'+gs['default'].img_url+'.850x850.jpg');
	var html='';
	html+='<li><img class="curr_base" width="85" height="85" src="'+img_host+'/'+gs['default'].img_url+'.85x85.jpg'+'" middesc="'+img_host+'/'+gs['default'].img_url+'.418x418.jpg'+'" longdesc="'+img_host+'/'+gs['default'].img_url+'.850x850.jpg'+'"/></li>';
	for(i in gs['part']){
		html+='<li><img class="curr_base" width="85" height="85" src="'+img_host+'/'+gs['part'][i].img_url+'.85x85.jpg'+'" middesc="'+img_host+'/'+gs['part'][i].img_url+'.418x418.jpg'+'" longdesc="'+img_host+'/'+gs['part'][i].img_url+'.850x850.jpg'+'"/></li>';
	}
	$('#mycarousel').html(html);
	$("#mycarousel").jcarousel({initCallback:mycarousel_initCallback});
	//初始化商品图片区
	html='';
	container.html('');
	html+='<img  src="'+img_host+'/'+gs['default'].img_url+'.850x850.jpg'+'"  width="850" height="850" /><br/>';
	html+=gs['default'].img_desc?('<span class="pro_des">'+gs['default'].img_desc+'</span>'):'';
	for(i in gs['part']){
		html+='<img  src="'+img_host+'/'+gs['part'][i].img_url+'.850x850.jpg'+'"  width="850" height="850" /><br/>';
		html+=gs['part'][i].img_desc?('<span class="pro_des">'+gs['part'][i].img_desc+'</span>'):'';
	}
	container.html(html);
}

function add_to_cart_dapter(direct){
	var sub_id = $(':hidden[name=sub_id]').val();
	var num = $(':input[name=num]').val();
	
	var sub = null;
	for(i in sub_list[current_color_id]['sub_list']){
		if(sub_list[current_color_id]['sub_list'][i]['sub_id'] == sub_id){
			sub = sub_list[current_color_id]['sub_list'][i];
			break;
		}
	}

	if(!sub_id)
	{
		alert('请选择颜色尺码');
		return false;
	}
	if(!num)
	{
		alert('请选择购买数量');
		return false;
	}   
	if(num < 1 || num > 99 || (sub != null && num > sub.sale_num) ) {
		alert('请选择购买数量');
		return false;
	}
    add_to_cart(sub_id,num,direct);
}

function add_to_cart (sub_id,num,direct) {
	$.ajax({
		url:'cart/add_to_cart',
		data:{sub_id:sub_id,num:num,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return;
			if(direct){
				location.href=base_url+'cart'
			}else{
				lhgDG = new $.dialog({ id:'thepanel',bgcolor:'#333',titleBar:true,title:'成功放入购物袋',iconTitle:false,btnBar:false,maxBtn:false,resize:false,width:400,height:200,cover:true,html:$('#add_to_cart_msg')[0] });
				lhgDG.ShowDialog();
				update_cart_num();	
			}
		}
	});
}

function add_to_cart_dapter_tuan(direct){
	var sub_id = $(':hidden[name=sub_id]').val();
	var num = $(':input[name=num]').val();
	
	var sub = null;
	for(i in sub_list[current_color_id]['sub_list']){
		if(sub_list[current_color_id]['sub_list'][i]['sub_id'] == sub_id){
			sub = sub_list[current_color_id]['sub_list'][i];
			break;
		}
	}

	if(!sub_id)
	{
		alert('请选择颜色尺码');
		return false;
	}
	if(!num)
	{
		alert('请选择购买数量');
		return false;
	}

        if(num < 1 || num > 99 || num > sub.sale_num ) {
		alert('请选择购买数量');
		return false;
	}

	if (direct == 2) location.href=base_url+'virtual/checkout/'+sub_id+'/'+num;
	
    add_to_cart_tuan(sub_id,num,direct);
}

function add_to_cart_tuan (sub_id,num,direct) {
	$.ajax({
		url:'cart/add_to_cart',
		data:{sub_id:sub_id,num:num,type:5,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return;
			if(direct){
				location.href=base_url+'cart'
			}else{
				lhgDG = new $.dialog({ id:'thepanel',bgcolor:'#333',titleBar:true,title:'成功放入购物袋',iconTitle:false,btnBar:false,maxBtn:false,resize:false,width:400,height:200,cover:true,html:$('#add_to_cart_msg')[0] });
				lhgDG.ShowDialog();
				update_cart_num();	
			}
		}
	});
}

function load_history (product_id,product_name,img,market_price,product_price,brand_name) {
	$.ajax({
		url:'product_api/history',
		data:{product_id:product_id,product_name:product_name,img:img,market_price:market_price,product_price:product_price,brand_name:brand_name,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result) {
			if(result.err) return false;
			if(result.html) $('#product_history').html(result.html).css('display','');
		}
	});
}

function buy_buy(product_id){
	$.ajax({
		url:'product_api/buy_buy',
		data:{product_id:product_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result) {
			if(result.err) return false;
			if(result.html) $('#buy_buy').html(result.html).css('display','');
		}
	});
}

function link_product(product_id){
	$.ajax({
		url:'product_api/link_product',
		data:{product_id:product_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result) {
			if(result.err) return false;
			if(result.html) $('#link_product').html(result.html).css('display','');
		}
	});
}

//商品咨询相关
function show_zixun_form () {
	$('#zixun_user_nologin_block').css('display','none');
	$('#zixun_user_guest_block').css('display','');
	$('#zixun_content').focus();
}
//
function show_login_form(){
	$('#zixun_user_nologin_block').css('display','');
	$('#zixun_user_guest_block').css('display','none');
	$('input[name=zixun_login_user]').focus();
}

//提交咨询
function submit_zixun () {
	var user_name = $('#zixun_login_user').val();
	var pwd = $('#zixun_login_pwd').val();
	var zixun_content= $.trim($('#zixun_content').val());
	
	var check_login = true;
	if ($('#zixun_user_nologin_block').length > 0 && $('#zixun_user_nologin_block:hidden').length==0 ){
		check_login = check_zixun_word_length(zixun_content );
		if(check_login){
			var rs = pro_login();
			if( rs == "false" || rs == false){
				return false;
			}
		}else{
			return false;
		}
	}
	
	if(check_zixun_word_length(zixun_content )){
		$.ajax({
			url:'liuyan/proc_zixun',
			data:{comment_type:1,tag_type:1,tag_id:product_id,comment_content:zixun_content,user_name:user_name,pwd:pwd,rnd:new Date().getTime()},
			dataType:'json',
			type:'POST',
			success:function(result){
				if(result.msg) alert(result.msg);
				if(result.err) {
					return false
				};
				$('#zixun_content').val('');
			}
		});
	}
}

function load_pro_brand_story(brand_id){
	$.ajax({
		url:'product/get_brand_story/' + brand_id,
		data:{brand_id:brand_id},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.content ) $('#product_tab_4').html(result.content );
		}
	});
}

function load_pro_brand_story_tuan(brand_id){
	$.ajax({
		url:'product/get_brand_story/' + brand_id,
		data:{brand_id:brand_id},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.content ) $('#tuanTabCon3').html(result.content );
		}
	});
}

function load_product_liuyan (comment_type,page) {
	$.ajax({
		url:'liuyan/liuyan_list',
		data:{comment_type:comment_type,tag_type:1,tag_id:product_id,page:page,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err) return false;
			if(result.html){
				var target=comment_type==1?$('#zixun_div'):$('#dianping_div');
				target.html(result.html);
			}
		}
	});
}

function load_dianping_panel(product_id)
{
	$.ajax({
		url:'/user/load_dianping_panel',
		data:{is_ajax:true,product_id:product_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.error!=0){
				if(result.need_login){
					//lhgDG = new $.dialog({ id:'thepanel',maxBtn:false,title:'请登录/注册演示站',iconTitle:false,btnBar:false,width:800,height:444,cover:false,resize:false,bgcolor:'#333',drag:true,html:$('#login_alert')[0] });
					//lhgDG.ShowDialog();
					window.location='http://www.mammytree.com/user/login';
					return false;
				}
				alert(result.msg);
				return false;
			}
			var parent_dom = $("div#float_panel");
			//parent_dom.show();
			parent_dom.html(result.content);
			lhgDG = new $.dialog({ id:'thepanel',height:190,maxBtn:false, title:'商品点评',iconTitle:false,cover:true,btnBar:false,resize:false,bgcolor:'#333',drag:true,html:$('#float_panel')[0] });
			lhgDG.ShowDialog();
			$('#lhgdgCover').click(function () {$('#lhgdg_xbtn_thepanel').click()});
			$('.btn_g_78').click(function(){post_dianping()});
		}
	});
}

function post_dianping()
{
	var parent=$('#float_panel');
	var fl_text=$.trim($('textarea[name=fl_text]',parent).val());
	var product_id = $.trim($('input[type=hidden][name=product_id]',parent).val());
 	if(!check_dianping_word_length()){
		return false;
	}

	$.ajax({
				url:'user/post_dianping',
				data:{is_ajax:true,product_id:product_id,comment_content:fl_text,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error!=0){
						alert(result.msg);
						return false;
					}
					alert(result.msg);
					location.reload();
				}
	});
}

//检查咨询内容是否超字
function check_zixun_word_length(content )
{
	var content_length=cnlength(content);
	if(content_length == 0 || content == '' || $('#zixun_content').val() == '请输入您要咨询的内容，可输入5到200个汉字！'){
		alert("咨询内容不能为空");
		return false;
	}else if(content_length< 5){
		alert("咨询内容至少为5个汉字");
		return false;
	}else if(content_length>200){
		alert("咨询内容至多为200个汉字");
		return false;
	}
	return true;
}

//点评检查
function check_dianping_word_length()
{
	var content=$.trim($(':input[name=fl_text]').val());
        content = content.replace(/字数限制5-200个汉字/g, '');
	var content_length=cnlength(content);
	if(content_length == 0 || content == '' || $('#zixun_content').val() == '请输入您要评论的内容，可输入5到200个汉字！'){
		alert("评论内容不能为空");
		return false;
	}else if(content_length< 5){
		alert("评论内容至少为5个汉字");
		return false;
	}else if(content_length>200){
		alert("评论内容至多为200个汉字");
		return false;
	}
	return true;
}

function share(stype) {
	var share_url=base_url+'product-'+product_id;
	if(current_color_id) share_url+='-'+current_color_id;
	share_url+='.html';
	var share_title=$('title').html();
	var url='';
	if (stype == "qzone") {
	  url = 'http://v.t.qq.com/share/share.php?source=1000002&amp;site=http://www.52kid.cn';
	  url = url + "&title=" + share_content + "&pic=" + share_pic + "&url=" + share_url;
	}
	if (stype == "sina") {
	  url = 'http://v.t.sina.com.cn/share/share.php?appkey=473945049';
	  url = url + "&title=" + encodeURIComponent(share_content) + "&pic=" + encodeURIComponent(share_pic) + "&url=" + encodeURIComponent(share_url);
	  window.open(url, "", "height=500, width=600");
	}
	if (stype == "renren") {
	  url = 'http://share.renren.com/share/buttonshare/post/1004?';
	  url = url + "title=" + share_title +"&content="+ share_content + "&pic=" + share_pic + "&url=" + share_url;
	}
	if (stype == "kaixin") {
	  url = 'http://www.kaixin001.com/repaste/share.php?';
	  url = url + "rtitle=" + share_title + "&rcontent=" + share_content + "&rurl=" + share_url;
	}
	if (stype == "douban") {
	  url = 'http://www.douban.com/recommend/?';
	  url = url + "title=" + share_title + "&comment=" + share_content + "&url=" + share_url;
	}
	if (stype == "MSN") {
	  url = 'http://profile.live.com/badge/?';
	  url = url + "url=" + share_url + "&title=" + share_title + "&description=" + share_content + "&screenshot=" + share_pic;
	}
	if (stype != "sina") {
	  window.open(encodeURI(url), "", "height=500, width=600");
	}
} // End of share

function view_dianping() {
	$('.pro_main_t li[rel=4]').click();
	$('html,body').animate({scrollTop:700},120);
	return true;
} // End of view_dianping

function num_up_down(sale_num ){
//添加商品数量
	$('.number .up').click(function () {
		var num = parseInt($('#num').attr('value'))+1;
		var maxSaleNum = 5;
		if (sale_num > 0 ){
			maxSaleNum = sale_num;
		}
		num = num>=maxSaleNum?maxSaleNum:num;
		$('#num').attr('value',num);
	});
	$('.number .down').click(function () {
		var num = parseInt($('#num').attr('value'))-1;
		num = num<1?1:num;
		$('#num').attr('value',num);
	});
}

$(function () {
	//弹出尺寸图
	var hei=$(document).height();
	$('.refer_size').click(function () {
		var img = $('.goods_size').parent().next().children();
		$('.pro_intro').append(img.clone());
		var w = img.width()+20,
			h = img.height()+46;
		lhgDG = new $.dialog({ id:'size',bgcolor:'#333',titleBar:true,title:'尺寸图',iconTitle:false,btnBar:false,maxBtn:false,resize:false,width:w,height:h,cover:true,html:$('.pro_intro img:last')[0] });
		lhgDG.ShowDialog();
		$('#lhgdgCover').css({'opacity':0.01,'height':hei});
		$('#lhgdgCover').click(function () {
			$('#lhgdlg_size').remove();
			$('#lhgdlg_reLoadId').remove();
			$(this).remove();
		});
	});
	//优惠活动的展开闭合
	$('.youhui_m a:last').click(function () {
		var height = $('.youhui_m').height();
		var length = $('.youhui_m li').length;
		var length_hide = $('.youhui_m li:hidden').length;
		if ($('.youhui_m li').length>2) {
			if (length_hide>0) {
				var heightNow = height+length_hide*32;
				$('.youhui_m li:hidden').fadeIn(200).addClass('onShow');
				$('.youhui_m').animate({height:heightNow},200);
				$(this).removeClass('more').addClass('close');
			} else {
				var heightNow = height-(length-3)*32;
				$('.youhui_m').animate({height:heightNow},200);
				$(this).removeClass('close').addClass('more');
				$('.youhui_m .onShow').fadeOut(200).removeAttr('class');
			}
		};
	});

	//商品详情页标签切换
	$('.pro_main_t li').click(function () {
		var num = $(this).index();
		$('.pro_main_t li').removeClass('sel');
		$(this).addClass('sel');
		$('.pro_main_block').hide();
		$('.pro_main_block').eq(num).show();
	});

	//textarea的鼠标触发事件
	$('#zixun_content').attr('value','请输入您要咨询的内容，可输入5到200个汉字！');
	$('#zixun_content').focus(function () {
		if ($(this).attr('value')=='请输入您要咨询的内容，可输入5到200个汉字！') {
			$(this).attr('value','');
			$(this).css({'color':'#000'});
		};
	});
	$('#zixun_content').blur(function () {
		if ($(this).attr('value')=='') {
			$(this).attr('value','请输入您要咨询的内容，可输入5到200个汉字！');
			$(this).removeAttr('style');
		};
	});

	//放大镜模块点击浮层
	$('.zoomIcon').click(function () {
		var w = $(window).width(),
			h = $(document).outerHeight(true),
			t = $(document).scrollTop(),
			l = (w-$('#zoom_float_block').width())/2;
		l = w<1000?0:l;
		$('.zoom_float_block_l a').removeAttr('style');
		$('.zoom_float_block_l a').eq(0).css({'border-color':'red'}).show();
		$('#zoom_float_block').css({'top':t,'left':l});
		$('#zoom_float_block').before('<div class="coverMask"></div>');
		$('.coverMask').fadeTo(0,0.5).css({'width':w,'height':h});
		$('#zoom_float_block').show();
		$('.zoom_float_block_r img').hide();
		$('.zoom_float_block_r img').eq(0).show();
		$('.coverMask').click(function () {
			$('#zoom_float_block').hide();
			$('.coverMask').remove();
		});
		winScroll();
	});
	//窗口大小变化执行遮罩层大小发生变化
	function winScroll() {
		$(window).resize(function () {
			var w = $(window).width();
			w = w<1000?1000:w;
			var l = (w-$('#zoom_float_block').width())/2;
			$('#zoom_float_block').css({'left':l});
			$('.coverMask').fadeTo(0,0.5).css({'width':w});
		});
	}
	//放大镜浮层关闭
	$('#zoom_float_block .closeBtn').click(function () {
		$('#zoom_float_block').hide();
		$('.coverMask').remove();
	});
	//放大镜模块交互
	$('.zoom_float_block_l a').hover(function () {
		$('.zoom_float_block_l a').stop(true);
		var rightImgV = $('.zoom_float_block_r img:visible');
		var numV = rightImgV.index();
		var num = $(this).index();
		if (num!=numV) {
			$('.zoom_float_block_l a').removeAttr('style');
			$(this).css({'border-color':'red'});
			rightImgV.show();
			rightImgV.removeAttr('style').hide();
			$('.zoom_float_block_r img').eq(num).fadeIn(200);
		}
	},function () {
		return false;
	});
});
//页面加载时判断活动数量
jQuery(document).ready(function($) {
	if($('.youhui_m li').length>2){
		$('.youhui_m a:last').removeClass('close').addClass('more');
		$('.youhui_m li').each(function () {
			var num = $(this).index();
			if (num>1&&num<$('.youhui_m li').length-1) {
				$(this).hide();
			};
		});
	}
});

//详情页咨询登录
function pro_login(){
	if ($('#zixun_login_user').val() == '' || $('#zixun_login_pwd').val() == '' ) {
		alert('用户名或密码不能为空');
		return false;
	}
	$.ajax({
		url:'user/proc_login',
		data:{user_name:$('#zixun_login_user').val(),password:$('#zixun_login_pwd').val(),remember:0,is_ajax:1,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.message) alert(result.message);
			if(result.error) {
				return false;
			}
			var content = '<div class="logininfo" style="display:block" id="zixun_user_logined_block"><strong id="zixun_user">'+ result.user_name +'</strong><span id="zixun_user_rank">'+ result.rank_name +'</span></div>';
			$('#zixun_user_nologin_block').replaceWith(content );
			change_loing_satus( result.user_name );
			return true;
		}
	});
	return false;
}

function change_login_satus( user_name ){
	$('#li_login').css("display","none");
	$('#li_register').css("display","none");
	$('#userInfobox').css("display","block");
	$('#mainIconUser').html( user_name);
}

function last_hotsales() {
	$.ajax({
		url:'product_api/last_hotsales',
		data:{rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result) {
			if(result.err) return false;
			if(result.html) $('#last_hotsales').html(result.html).css('display','');
		}
	});    
}
