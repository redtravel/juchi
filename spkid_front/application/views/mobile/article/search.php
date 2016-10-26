<?php include APPPATH."views/mobile/header.php"; ?>
<style>
	.searchResult {
		background:white;
		z-index:99999;
		max-height:60vh;
		overflow-y: auto;
	}
	.margin-0 {
		margin:0 auto;
    }
    a.link{line-height: 40px;height: 40px;}
    .page-content{
        padding-top:0;
	}

	.search_css {
		font-size:0.7em;
	}

	.searchbar input[type="search"] {
		background-image:none;
		border:1px solid white;
		padding:2px 2px;
	}

</style>
<div class="views">
     <div class="view view-main">
          <div class="pages">
           <div data-page="index" data-name="index" class="page article-bg">
                               
                 <!--search-top start-->
                 <!-- <div class="search-top center">
                    <div class="search-main">
                            <div class="searchbar-input"><input type="text" name="kw" placeholder="关键字"/></div>
                      <a href="#" class="history-back link">取消</a>
                    </div>
                  </div> -->
                  <form data-search-list=".search-here" data-search-in=".item-title" class="searchbar searchbar-init searchbar-active">
                    <a href="javascript:void(0)" class="link back search-history-back"> <i class="icon icon-back"></i></a>
                      <div class="searchbar-input" style="margin-left:15px">
                        <input type="search" name="kw" placeholder="搜索" class=""><a href="#" class="searchbar-clear"></a>
                      </div>
                      <a href="#" class="button search-confirm search_confirm"></a>
                    </form>
               <!--search-top end-->
                
                <!-- ends navbar -->
                <div class="page-content infinite-scroll" style="padding-top:36px" data-template="html" data-source="/article/search" data-parent=".hotsr-lb">
               <!--Hot search start-->
          
           
            <!--Hot search end-->
            <!--historical-records start-->
               <div class="historical">
                <div class="historical-tit">历史搜索:</div>
              </div>


           <!--historical-records end-->
            <div class="hotsr-lb" style="font-size:1em">
            </div>
           <!--clear-history start-->
               <div class="clear-history"><a href="#" class="button clear-history-but">清空历史记录</a></div>
           <!--clear-history end-->   
           </div>
           </div><!-- page -->
      </div> 

     </div>

</div>
<!--<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>-->
<?php include APPPATH."views/mobile/common/footer-js.php"; ?>

<script>
/*
var myApp = new Framework7({
    material: true,
})
var mainView = myApp.addView('.view-main', {
    domCache: true,
});
var $$ = Dom7;
 */
//myApp.onPageInit('index', function (page) {
    showSearchHistory();
    $$('.history-rm').on('click', function(e){
        e.preventDefault();
        $$(this).parent().remove();
        var id = $$(this).attr('href');
        id = id.substr(1);
        var history = localStorage.getItem('article_search_history');
        history = history.split(',');
        history.splice(id,1);
        //alert(history.join(','));
        localStorage.setItem('article_search_history', history.join(','));
    })

function confirm_search() {
  var words = $$('input[name="kw"]').val();
  words = words.replace(/(^\s*)|(\s*$)/g, '');
  searchArticle(words);
}

$$('.search_confirm').on('click', function(e){
  confirm_search();
})

$$('input[name="kw"]').on('keydown', function(e){
    if (e && e.keyCode == 13) {
        //alert('/article/search/'+$$(this).val());
        //location.href = '/article/search/'+;
        confirm_search();
        
    }
})
$$('.historical-wz').on('click', function(e){
    e.preventDefault();
    var words = $$(this).html();
    searchArticle(words);
})
$$('.clear-history-but').on('click', function(e){
    $$('.historical').html('');
    localStorage.removeItem("article_search_history");
})
//})

function searchArticle(words){
        myApp.showIndicator();
        $$.get('/article/search', {kw:words}, function(data){
            myApp.hideIndicator();
            var history = localStorage.getItem('article_search_history');
                
            if (history){
                if (history.indexOf(words) == -1)
                    localStorage.setItem('article_search_history', history+','+words);
                    
            }else
                localStorage.setItem('article_search_history', words);                
            if ('empty' != data){
                $$('.hotsr-lb').html(data);
                $$('.page-content').attr('data-params', 'kw='+words);
            } else{
                $$('.hotsr-lb').html('没有结果!');
            }
        }, function(data, timeout){
            console.log('error'+timeout)
        })
}
function showSearchHistory() {
  if (localStorage) {
    var hotWords = localStorage.getItem('article_search_history');
    if (!hotWords) {
      //当前没有搜索历史
      return;
    };
    hotWords = hotWords.split(',');     
    var str = '';
    if (hotWords.length) {        
      for (var i = 0; i <= hotWords.length - 1; i++) {
	
        str += '<div class="order-details-rr historical-list"><a href="#" class="historical-wz">' + hotWords[i] + '</a><a href="#'+i+'" class="historical-search-close history-rm"></a></div>';
      };
      $$('.historical').html(str);
    };
  };
}

$$('.search-history-back').on('click', function(e){
	history.go(-1);
});
</script>
<?php include APPPATH."views/mobile/footer.php"; ?>
