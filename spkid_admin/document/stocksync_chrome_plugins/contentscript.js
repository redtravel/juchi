var api_host = 'http://kidadmin.me/';
min_stock = 30;
var html_source;
$(function(){
    if(location.href.search('noitem')!=-1){
        alert('出错啦亲！');
        return false;
    }
	parseItem();
});

/**
 * 解析当前页
 */
function parseItem(){
	html_source = $('html').html();
	// 商品ID
	var num_iid = parseId();	
	// sku
	var skus = parseSKU();
	console.log(skus);
	// 提交到服务端
	$.ajax({
		url:api_host+'tmall/sync_stock',
		data:{
			rnd:new Date().getTime(),
			num_iid: num_iid,
			skus: skus,
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
			setTimeout('parseNext()', 10000);

		}
	});
}

/**
 * 解析下一页
 */
function parseNext()
{
	$.ajax({
		url:api_host+'tmall/next_stock_num_iid',
		data:{rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(data){
			if(data.num_iid)
			{
                            location.href = 'http://detail.tmall.com/item.htm?_=stock&id=' + data.num_iid;
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
 * 解析SKU
 */
function parseSKU()
{
        var sold_out = html_source.indexOf('此商品已下架')!=-1;        
	var skus = new Array();
	var re = /\{"skuId" : "\d*","price" : "[0-9.]*","priceCent" : "\d*","stock" :"\d*"\}/ig;
	while((r=re.exec(html_source))!=null){
		var sku = eval('('+r[0]+')');
                if(sku.stock < min_stock || sold_out){
                    sku.stock = 0;
                }
		skus.push(sku.skuId+'|'+sku.stock);
	}
	if(skus.length==0){
                var stock = parseQty();
                if(stock < min_stock || sold_out){
                    stock = 0;
                }
		skus.push('BLANK|'+stock);
	}
	return skus;
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



