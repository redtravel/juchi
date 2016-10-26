/**
 * 此文件用于index的4个page
 * @date 20150925
 */
var mySwiper = myApp.swiper('.swiper-container', {
		pagination:'.swiper-pagination',
		paginationClickable: true,
		spaceBetween: 40,
		centeredSlides: true,
		autoplay: 5000,
		autoplayDisableOnInteraction: false,
		slidesPerView: 1,
		loop: true
});


myApp.onPageInit('article', function (page) {
    article_page_init();
})
function article_page_init(){
    $$('.page[data-page="article"] .tabs .tab').on('show', function () {
        var id=$$(this).attr('id');
        var cat=id.split('_')[1];
        //console.log('tab.show ');
        $$('.page[data-page="article"] .page-content').data('parent','#'+id+' .listb02 ul').data('params','cat='+cat);
    })
    $$('.page[data-page="article"] .tabs .tab').eq(0).trigger('show');

}
if( myApp.mainView.activePage.name == 'article' )article_page_init();
// myApp.onPageInit('*', function (page) {    
$$('.toolbar-inner>a').on('click',function (){
    $$('.toolbar-inner i').removeClass('tabbar-selecte');
    $$(this).children().addClass('tabbar-selecte');
});

/*var started=getCookie('started');
if (!started){
var startPage = '<div class="page no-toolbar" data-page="start">' +
			       '  <div class="yywtoolbar">' +
              '   <div class="yywtoolbar-inner ">' +
                '     <div class="right " ><a href="#" id="jump" class="button button-fill color-green">跳过</a></div>' +
              '   </div>' +
         '    </div>' +
                        '<div class="page-content" style="padding-top:0;cursor:pointer">' +
			       

			
                          '<div class="first-page"><img src="'+static_host+'mobile/img/fp.jpg" width="100%" style="vertical-align:middle;"></div>' +
                        '</div>' +
                      '</div>';
mainView.router.loadContent(startPage);
$$('#jump').on('click',function(){
    mainView.back();
    setCookie('started',true,24);
})
$$('.page-content').on('click',function(){
    //mainView.loadPage('/user/signin/register-step1');
    setCookie('started',true,24);
    location.href='/user/signin/register-step1';
    
})
*/
//myApp.init();





	
	
	
	
