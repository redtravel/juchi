// Initialize your app
var myApp = new Framework7({
    modalTitle: 'yueya',
    // Enable Material theme
    material: true,
	scroller:"native"
});

// Export selectors engine
var $$ = Dom7;
var totalNum=10;
// Add view
var mainView = myApp.addView('.view-main', {
    dynamicNavbar: true,
    domCache: true
});
/*
$$('.infinite-scroll').on('infinite', function () {
	if (0==totalNum)
	{
		console.log('done');
		return false;
	}
	 var str='<div class="block-view two-column"> <div class="block-product"><!-- 一个块 --> <div class="view-image"><!-- 图片内容 --> <a href="" class="prd-hot create-page"><img src="static/img/product/pro_002.jpg" /></a> </div> <div class="product-caption">标准型网底直丝弓托槽(E系列) 3带钩</div><!-- 标题 --> <div class="price-line"> <a href="" class="market-price">&yen;198</a> <span class="price">&yen;150</span> <span class="rate">10</span></div> </div>';                  
                      str+='<div class="block-product"><!-- 一个块 --> <div class="view-image"><!-- 图片内容 --> <a href="" class="prd-exhibit"><img src="static/img/product/pro_003.jpg" /></a> </div> <div class="product-caption">标准型网底直丝弓托槽(E系列) 3带钩</div><!-- 标题 --> <div class="price-line"> <a href="" class="market-price">&yen;198</a> <span class="price">&yen;150</span> <div class="rate">10</div> </div> </div> <!-- 一个块 --></div>';
	//$$('block-view:last-child').insertBefore(str);
    $$(this).append(str);
	totalNum--;
})
*/

var loading = false;
$$('.infinite-scroll').on('infinite', function () {
    // Exit, if loading in progress
    if (loading) return;
    // Set loading trigger
    loading = true;
    var lastLoadedIndex = $$('.infinite-scroll .list-block li').length;
    var data='<li class="item-content"><div class="item-inner"><div class="item-title">Item '+(lastLoadedIndex + 1)+'</div></div></li>';
    $$('.infinite-scroll .list-block ul').append(data);
    if (39 == lastLoadedIndex) {
        // Nothing more to load, detach infinite scroll events to prevent unnecessary loadings
        myApp.detachInfiniteScroll($$('.infinite-scroll'));
    }
    loading = false;
});