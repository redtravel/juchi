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
	       <div data-page="index" class="page article-bg ">
<div class="page-content">
	             <!--search-top start-->
		        <!-- <div class="search-top">
			      <div class="search-main clearfix">
                    <div class="searchbar-input" style="position:">
                    	<input type="search" placeholder="口罩"/>
                    	<span>X</span>
                    </div>
					<a href="#" class="history-back link">取消</a>
			      </div>
				</div> -->
				
				<form data-search-list=".search-here" data-search-in=".item-title" class="searchbar searchbar-init searchbar-active">
					<a href="javascript:void(0)" class="link back search-history-back"> <i class="icon icon-back"></i></a>
				    <div class="searchbar-input" style="margin-left:15px">
				      <input type="search" placeholder="搜索" class=""><a href="#" class="searchbar-clear"></a>
				    </div>
				    <a href="#" class="button search-confirm search_confirm"></a>
				  </form>
	           <!--search-top end-->
	           <!--Hot search start-->
		       <div class="hot-search">
		            <div class="hotsr-lb clearfix">
			         <div class="hotsr-tit search_css">热搜:</div>
			         <div class="hotword">
				     
				     <?php
                                        $str = '';
					$cnt = count($hotwordlist['list']);
					foreach ($hotwordlist['list'] as $key => $value) {
	                                        if ($value['hotword_name']) {
							$str .= '<a href="#" class="button button-fill button-raised search-anniu search_css">' . $value['hotword_name']  . '</a>';
											 
						}          

					}
					echo $str;
					?>
					
				 </div>
			    </div>
		       </div>
	        <!--Hot search end-->
	        <!--historical-records start-->
	           <div class="historical">
		        <div class="historical-tit">历史搜索:</div>
		      </div>


	       <!--historical-records end-->
	       <!--clear-history start-->
	           <div class="clear-history"><a href="#" class="button clear-history-but">清空历史记录</a></div>
	       <!--clear-history end-->   
	
	       
	       
	       
	       </div>
	  
	  
	  </div>
	  </div> 
     
     
     
     </div>
   



</div>
<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/hanzi2pinyin.js')?>"></script>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/search.js?v=version')?>"></script>

<script>
	$$('.search-history-back').on('click', function(e){
		location.href = '/';
	});
</script>

<?php include APPPATH."views/mobile/footer.php"; ?>
