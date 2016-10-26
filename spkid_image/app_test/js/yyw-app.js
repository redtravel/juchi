var myApp = new Framework7({
    //init: false,
    //material: true
})
var $$ = Dom7;

var mainView = myApp.addView('#view-main', {
    domCache: true
});
var view = myApp.addView('#view-course', {
    domCache: true
});
var mySwiper = myApp.swiper('.swiper-container', {
		pagination:'.swiper-pagination',
		paginationClickable: true,
		spaceBetween: 40,
		centeredSlides: true,
		autoplay: 2500,
		autoplayDisableOnInteraction: false,
		slidesPerView: 1,
		loop: true
});

$$('.toolbar-inner>a').on('click',function (){
    $$('.toolbar-inner>a.active').removeClass('active');
    $$(this).addClass('active');
});
/*myApp.onPageInit('*', function (page) {
    console.log(page.name);
    changeToolbarStatus(page);
});*/
//myApp.init();


// Export selectors engine


 // 加载flag
var loading = false;
 
// 上次加载的序号

var lastIndex = $$('.listb li').length;

// 最多可加载的条目
var maxItems = 15;
 
// 每次加载添加多少条目
var itemsPerLoad = 5;


var prods = [
			['#11','mark_sale', '新品', 'prod001', '新亚 .022# 网底直丝弓颊面管 第一磨牙(上下单方管)', '25','prod_pprice','348.00'],
			['#12','mark_show', '展品', 'prod004', '新亚 .022# 网底直丝弓颊面管 第一磨牙', '225','ask_price',''],
			['#13','mark_sale', '热品', 'prod002', '新亚 .022# 网底直丝弓颊面管 第一磨牙(上下单方管)', '145','prod_pprice','120.00'],
			['#14','mark_show', '展品', 'prod003', '新亚 .022# 网底直丝弓颊面管 第一磨牙(上下单方管)', '445','ask_price',''],
			['#15','mark_edu', '课程', 'prod005', '新亚 .022# 网底直丝弓颊面管 第一磨牙', '65','ask_price','3000.00'],
			['#16','mark_show', '展品', 'prod002', '新亚 .022# 网底直丝弓颊面管 第一磨牙(上下单方管)', '24','ask_price',''],
			['#17','mark_show', '展品', 'prod003', '新亚 .022# 网底直丝弓颊面管 第一磨牙(上下单方管)', '1245','ask_price',''],
			['#18','mark_sale', '热品', 'prod004', '新亚 .022# 网底直丝弓颊面管 第一磨牙', '745','prod_pprice','56.00'],
			['#19','mark_show', '展品', 'prod001', '新亚 .022# 网底直丝弓颊面管 第一磨牙(上下单方管)', '815','ask_price',''],
			['#20','mark_show', '展品', 'prod002', '新亚 .022# 网底直丝弓颊面管 第一磨牙', '100','ask_price',''],
			['#21','mark_sale', '新品', 'prod001', '新亚 .022# 网底直丝弓颊面管 第一磨牙(上下单方管)', '25','prod_pprice','348.00'],
			['#22','mark_show', '展品', 'prod004', '新亚 .022# 网底直丝弓颊面管 第一磨牙', '225','ask_price',''],
			['#23','mark_sale', '热品', 'prod002', '新亚 .022# 网底直丝弓颊面管 第一磨牙(上下单方管)', '145','prod_pprice','120.00'],
			['#24','mark_show', '展品', 'prod003', '新亚 .022# 网底直丝弓颊面管 第一磨牙(上下单方管)', '445','ask_price',''],
			['#25','mark_edu', '课程', 'prod005', '新亚 .022# 网底直丝弓颊面管 第一磨牙', '65','ask_price','3000.00'],
			['#26','mark_show', '展品', 'prod002', '新亚 .022# 网底直丝弓颊面管 第一磨牙(上下单方管)', '24','ask_price',''],
			['#27','mark_show', '展品', 'prod003', '新亚 .022# 网底直丝弓颊面管 第一磨牙(上下单方管)', '1245','ask_price',''],
			['#28','mark_sale', '热品', 'prod004', '新亚 .022# 网底直丝弓颊面管 第一磨牙', '745','prod_pprice','56.00'],
			['#29','mark_show', '展品', 'prod001', '新亚 .022# 网底直丝弓颊面管 第一磨牙(上下单方管)', '815','ask_price',''],
			['#30','mark_show', '展品', 'prod002', '新亚 .022# 网底直丝弓颊面管 第一磨牙', '100','ask_price','']
			];

var j =  -1;
	
// 注册'infinite'事件处理函数
/*$$('.infinite-scroll').on('infinite', function () {
 
  // 如果正在加载，则退出
  if (loading) return;
 
  // 设置flag
  loading = true;
 
  // 模拟1s的加载过程
  setTimeout(function () {
    // 重置加载flag
    loading = false;
 
    if (lastIndex >= maxItems) {
      // 加载完毕，则注销无限加载事件，以防不必要的加载
      myApp.detachInfiniteScroll($$('.infinite-scroll'));
      // 删除加载提示符
      $$('.infinite-scroll-preloader').remove();
      return;
    }
 
    // 生成新条目的HTML
    var html = '';
	for (var i = lastIndex + 1; i <= lastIndex + itemsPerLoad; i++) {
		j = j + 2;
     html += '<li><a href="' + prods[j-1][0] + '" class="external"><div class="prod_sbox"><div class="mark '+ prods[j-1][1] + '">' + prods[j-1][2] + '</div><div class="sbox">'
			+ '<div class="img_sbox"><img src="img/' + prods[j-1][3] +'.jpg"></div><div class="prod_name">' + prods[j-1][4] + '</div><div class="bline">'
        	+ '<div class="favoheart">' + prods[j-1][5] + '</div><div class="price_bar"><span class="' + prods[j-1][6] + '">' + prods[j-1][7] +'</span></div></div></div></div></a>'
    		+ '<a href="' + prods[j][0] + '" class="external"><div class="prod_sbox"><div class="mark '+ prods[j][1] + '">' + prods[j][2] + '</div><div class="sbox">'
			+ '<div class="img_sbox"><img src="img/' + prods[j][3] +'.jpg"></div><div class="prod_name">' + prods[j][4] + '</div><div class="bline">'
        	+ '<div class="favoheart">' + prods[j][5] + '</div><div class="price_bar"><span class="' + prods[j][6] + '">' + prods[j][7] + '</span>'
			+ '</div></div></div></div></a></li>';	
	}

    // 添加新条目
    $$('.listb ul').append(html);
 
    // 更新最后加载的序号
    lastIndex = $$('.listb li').length;
  }, 1000);
});    
*/ 



$$('.view').on('show', function () {    
    var id=$$(this).attr('id');
    id='#'+id;
    $$(id+' .toolbar>.toolbar-inner>a[href="'+id+'"]').addClass('active');
});
	
	
	
