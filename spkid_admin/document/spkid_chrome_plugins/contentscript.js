var api_host = 'http://kidadmin.me/';
var min_stock = 30;
var html_source;
$(function(){
	parseItem();
});

/**
 * 解析当前页
 */
function parseItem(){
	html_source = $('html').html();
	// 商品ID
	var num_iid = parseId();
	// 商品描述
	var desc = parseDesc();

	// 商品名称
	var title = $("title").html().replace('-tmall.com天猫','');
	
	// 卖家ID
	var sellerId = parseSellerId();
	// 店铺ID
	var shopId = parseShopId();
	// 卖家昵称
	var nick = parseNick();
	// sku
	var skus = parseSku();
	// 品牌ID
	var brand_id = parseBrandId();
	// 品牌名称
	var brand_name = parseBrandName();
	// 分类ID
	var category = parseCategory();
	// 图片
	var images = parseImages();
	// 商品价格
	// 商品价格的显示有延时，需要进行处理
	var price = 0;
	var price_try_time = 0;	
	while(price_try_time<10)
	{
		price = parsePrice();
		if(price>0) break;
		sleep(1000);
		price_try_time += 1;
	}
	
	// 专柜价
	var reserve_price = parseReservePrice();
	
	
	// 提交到服务端
	$.ajax({
		url:api_host+'tmall/sync_item',
		data:{
			rnd:new Date().getTime(),
			num_iid: num_iid,
			desc: desc,
			title: title,
			price: price,
			reserve_price: reserve_price,
			sellerId: sellerId,
			shopId: shopId,
			nick: nick,
			skus: skus,
			brand_id: brand_id,
			brand_name: brand_name,
			category: category,
			images: images
		},
		dataType:'json',
		type:'POST',
		success:function(data){			
			if(data.msg){
				alert(data.msg);
			}
			if(data.error){
				console.log('失败');
			}else{
				console.log('成功');
			}
			setTimeout('parseNext()', 1000);

		}
	});
}

/**
 * 解析下一页
 */
function parseNext()
{
	$.ajax({
		url:api_host+'tmall/next_num_iid',
		data:{rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(data){
			if(data.num_iid)
			{
				location.href = 'http://detail.tmall.com/item.htm?id=' + data.num_iid;
			}
		}
	});
}



/**
 * 解析商品ID
 */
function parseId()
{
	var arrRequest = window.location.search.split('&');
	for(i=0; i<arrRequest.length; i++)
	{
		var arr = arrRequest[i].split('=');
		if(arr.length==2 && (arr[0]=='id' || arr[0]=='?id')){
			return parseInt(arr[1]);
		}
	}
}

/**
 * 解析商品价格
 */
function parsePrice()
{
	var price = 0;
	$(".tm-price").each(function(i){
		var _price = $(this).html();
		if(_price.indexOf('-')>=0){
			arr_price = _price.split('-');
			_price = arr_price[arr_price.length-1];
		}
                _price = parseFloat(_price);
		if(_price < price || price==0) price = _price;
	});
	return price;
}

/**
 * 解析专柜价
 */
function parseReservePrice()
{
	var re = /'reservePrice'\s*:\s*'(.+)'/ig;
	r = re.exec(html_source);
	if(r!=null && r.length>1){
		return r[1];
	}
	return 0;
}

/**
 * 解析卖家ID
 */
function parseSellerId()
{
	var re = /sellerId:"(\d+)"/ig;
	r = re.exec(html_source);
	if(r.length>1){
		return r[1];
	}
	return '';
}

/**
 * 解析店铺ID
 */
function parseShopId()
{
	var re = /shopId:"(\d+)"/ig;
	r = re.exec(html_source);
	if(r!=null && r.length>1){
		return r[1];
	}
	return '';
}

/**
 * 解析卖家昵称
 */
function parseNick()
{
	return $('input[type=hidden][name=seller_nickname]').val();
}

/**
 * 解析品牌名称
 */
function parseBrandName()
{
	var re = /'brand'\s*:\s*'(.+)'/ig;
	r = re.exec(html_source);
	if(r!=null && r.length>1){
		return decodeURIComponent(r[1]);
	}
	return '';
}

/**
 * 解析品牌ID
 */
function parseBrandId()
{
	var re = /'brandId'\s*:\s*'(\d+)'/ig;
	r = re.exec(html_source);
	if(r!=null && r.length>1){
		return r[1];
	}
	return 0;
}

function parseCategory()
{
	var re = /'categoryId'\s*:\s*'(\d+)'/ig;
	r = re.exec(html_source);
	if(r!=null && r.length>1){
		return r[1];
	}
	return 0;
}

/**
 * 解析SKU
 */
function parseSku()
{
	var arr_color = new Array();
	var arr_size = new Array();
	var arr_sku = new Array();
	// 颜色区
	$('ul.J_TSaleProp.tb-img li').each(function(i){
		var color_code = $(this).attr('data-value');
		var color_name = $(this).attr('title');
		var color_img = '';
		var re = /background:url\((.*).jpg\)/ig;
		var r = re.exec($(this).html());
		if(r!=null && r.length>1){
			color_img = r[1];
			exp_pos = color_img.indexOf('.jpg');
			color_img = color_img.substr(0, exp_pos+4);
		}
		arr_color.push([color_code, color_name, color_img]);
	});
	
	// 尺码区
	
	$('ul.J_TSaleProp').each(function(i){
		if($(this).hasClass('tb-img')) return;
		$('li', $(this)).each(function(j){
			var size_code = $(this).attr('data-value');
			var size_name = $('a span', $(this)).html();
			arr_size.push([size_code, size_name]);
		});
	
	});
	if(arr_size.length==0){
		arr_size.push(['BLANK', '均码']);
	}
	
	// 将颜色和尺码配对成SKU
	// sku格式为[color_code, color_name, color_img, size_code, size_name, skuId, stock]
	for(i=0;i<arr_color.length;i++){
		for(j=0;j<arr_size.length;j++){
			var stock = parseStock(arr_color[i][0], arr_size[j][0]);
			if(stock==null) continue;
			if(stock.stock < min_stock) continue;
			arr_sku.push([arr_color[i][0],arr_color[i][1],arr_color[i][2],arr_size[j][0],arr_size[j][1],stock.skuId, stock.stock]);
		}
	}
	
	// 如果没有颜色没有尺码，取数量
	if(arr_color.length==0){
		sku = ['BLANK','无','','BLANK','无','BLANK',0];
		sku[6] = parseQty();
                if(sku[6] >= min_stock){
                    arr_sku.push(sku);
                }
	}
	
	return arr_sku;
}

/**
 * 根据颜色尺码的编码解析库存信息
 */
function parseStock(color_code, size_code)
{
    if(color_code=='BLANK') color_code='';
    if(size_code=='BLANK') size_code='';
	if(color_code) color_code += ';';
	if(size_code) size_code += ';';
	var reg1 = new RegExp('";'+color_code+size_code+'":{([^{}]*)}', 'ig');
	var reg2 = new RegExp('";'+size_code+color_code+'":{([^{}]*)}', 'ig');
	r = reg1.exec(html_source);
	if(r!=null && r.length>1){
		return eval('({'+r[1]+'})');
	}
	r = reg2.exec(html_source);
	if(r!=null && r.length>1){
		return eval('({'+r[1]+'})');
	}
	return null;
}

function parseImages()
{
	var images = new Array();
	$('ul#J_UlThumb li img').each(function(i){
		var url = $(this).attr('src');
		var exp_pos = url.indexOf('.jpg');
		if(!exp_pos) return;
		images.push(url.substr(0, exp_pos+4));
	});
	return images;
}

/**
 * 解析描述
 */
function parseDesc()
{

	var re = /"descUrl"\s*:\s*"(.*)"/ig;
	r = re.exec(html_source);
	if(r!=null && r.length>1){
		return r[1];
	}
	return '';
}

/**
 * 等待n毫秒
 */
function sleep(n)   
{   
    var start=new   Date().getTime();
    while(true)  if(new  Date().getTime()-start> n) break;   
} 


/**
 * 在没有定义sku的情况下（或只有一个SKU，source中不显示sku的情况下）解析库存量
 */
function parseQty()
{
	var re = /'quantity'\s*:\s*(\d+)/ig;
	r = re.exec(html_source);
	if(r!=null && r.length>1){
		return parseInt(r[1]);
	}
	return 0;
}



