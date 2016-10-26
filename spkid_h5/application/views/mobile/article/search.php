<?php include APPPATH."views/mobile/header.php"; ?>
<style>
	.searchResult { background:white; z-index:99999; max-height:60vh; overflow-y: auto; }
	.margin-0 { margin:0 auto; }
  a.link{ line-height: 40px; height: 40px; }
  .page-content{ padding-top:0;	}
	.search_css { font-size:0.7em; }
	.hotsr-v { margin-top:10px; }
	.t-name1 { padding-top: 20px;}
</style>
<div class="views">
    <div class="view view-main">
        <div class="pages">
            <div data-page="index" data-name="index" class="page article-bg">
                <form data-search-list=".search-here" data-search-in=".item-title" class="searchbar searchbar-init searchbar-active">
                    <a href="javascript:void(0)" class="link back search-history-back"> <i class="icon icon-back2"></i></a>
                        <div class="searchbar-input" style="margin-left:15px">
                            <input type="search" name="kw" placeholder="搜索" class="">
                        </div>
                    <a href="javascript:void(0);" class="button search-confirm search_confirm"></a>
                </form>
                <div class="hot-search">
                    <div class="hotsr-lb clearfix">                        
                    </div>
                </div>
                <div class="page-content infinite-scroll" style="padding-top:0px" data-template="html" data-source="/article/search" data-parent=".hotsr-v">
                    <div class="historical-tit"><span>搜索历史</span></div>
                    <div class="historical"></div>
                    <div class="hotsr-v" style="font-size:1em"></div>
                    <div class="clear-history"><a href="javascript:void(0);" class="button clear-history-but">清空历史记录</a></div>
                </div>
            </div>
        </div> 
    </div>
    <?php include APPPATH."views/mobile/common/template7.php"; ?>
</div>
<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
<script>
    showSearchHistory();
    $$('.history-rm').on('click', function(e){
        e.preventDefault();
        $$(this).parent().remove();
        var id = $$(this).attr('href');
        id = id.substr(1);
        var history = localStorage.getItem('article_search_history');
        history = history.split(',');
        history.splice(id,1);
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

    function searchArticle(words){
        myApp.showIndicator();
        var v_isview = GetRequest();
        $$.get('/article/search', {kw:words,is_preview:v_isview.is_preview}, function(data){
            myApp.hideIndicator();
            var history = localStorage.getItem('article_search_history');                
            if (history){
                if (history.indexOf(words) == -1)
                    localStorage.setItem('article_search_history', history+','+words);                   
            }else{
                localStorage.setItem('article_search_history', words);    
            }            
            if ('empty' != data){
                $$('.hotsr-v').html(data);
                $$('.page-content').attr('data-params', 'kw='+words);
            } else{
                $$('.hotsr-v').html('没有结果!');
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
                if(hotWords.length > 3) {var hotWords_v = hotWords.length - 3 ;}else{ var hotWords_v = 0 ;}
                for (var i = hotWords.length -1; i >= hotWords_v; i--) {          	
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
