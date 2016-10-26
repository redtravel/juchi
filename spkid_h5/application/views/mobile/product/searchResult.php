<?php include APPPATH."views/mobile/header.php"; ?>

		<style>
			.page {
				background-color:#f1f1f1;
				color:white;
			}

			.ks-card-header-pic .card-header {
			  height: 40vw;
			  background-size: cover;
			  background-position: center;
			}
			.ks-facebook-card .card-header {
			  display: block;
			  padding: 10px;
			}

			.page-content{ padding-top:7px;}

			.navbar .center, .subnavbar .center{ line-height: 0;}

			.vcenter {
				display: -webkit-box;
				 -webkit-box-orient: horizontal;
				 -webkit-box-pack: center;
				 -webkit-box-align: center;
				 
				 display: -moz-box;
				 -moz-box-orient: horizontal;
				 -moz-box-pack: center;
				 -moz-box-align: center;
				 
				 display: -o-box;
				 -o-box-orient: horizontal;
				 -o-box-pack: center;
				 -o-box-align: center;
				 
				 display: -ms-box;
				 -ms-box-orient: horizontal;
				 -ms-box-pack: center;
				 -ms-box-align: center;
				 
				 display: box;
				 box-orient: horizontal;
				 box-pack: center;
				 box-align: center;
			}

			.justify{
			    display:-webkit-box;
			    display:-webkit-flex;
			    display:-ms-flexbox;
			    display:flex;

			    -webkit-box-pack:justify;
			    -webkit-justify-content:space-between;
			    -ms-flex-pack:justify;

			    justify-content:space-between;
			}

			.coupons-tab .sort {position: absolute; top: 50%; margin-top: -8px; width: 0; right:22px;  height: 0;}
            .sort .sort-up{ border-bottom:6px solid #aaa;}
            .sort .sort-active{ border-bottom:6px solid #17a2e5;}
            .sort .sort-up,.sort .sort-down{ border-left:6px solid transparent; border-right:6px solid transparent; width:0; height:0; display:block;}
            
            .sort .sort-down{ border-top:6px solid #aaa; }
            .sort .sort-active2{ border-top:6px solid #17a2e5;  }
            .sort .sort-down,.sort .sort-active2{ margin-top: 5px;}
           
		   
		   
		   
			
		</style>

		
		<div class="views">
			<!-- Your main view, should have "view-main" class-->
			<div class="view view-main">
				<!-- Pages, because we need fixed-through navbar and toolbar, it has additional appropriate classes-->
				<div class="pages">
					<!-- Index Page-->
					<div data-page="searchResult"  class="page">

						<!-- navbar -->
						<div class="navbar">
							<div class="navbar-inner">
								<div class="left">
									<a href="javascript:void(0)" class="link back history-back">
										<i class="icon icon-back">
										</i>
									</a>
								</div>
								<div class="center c_name searchBar">
									<form data-search-list=".search-here" data-search-in=".item-title" style="width:100%">
									<div class="searchbar-input"><input type="search" placeholder="搜索" class="" value="<?php echo $kw?>"></div>
								   </form>
								</div>
								<div class="right">										
									<a href="/product/ptype_list" class="link icon-only external">
									<i class="icon csearchico"></i>
									</a>
								</div>
							</div>
						</div>
						<!-- ends navbar -->
						<!-- Scrollable page content-->
						<div class="page-content  infinite-scroll" data-template="infiniteSearchTemplate" data-parent="#list a" data-source="/product/ajax_product_list/searchResult" data-params="
						<?php 
							if(isset($searchByType) && $searchByType) {
								echo "kw=$kw&type_id=$type_id";
							} else {						
								echo "kw=$kw" . (isset($ids) ? ('&ids='.$ids) : '' );
							}
						?>">
							<!-- 广告位 -->	
							<?php echo isset($ad[0]->ad_code) ? $ad[0]->ad_code : ''?>
							<!-- ends 广告位-->
			                <div class="buttons-row tab-qh buttons-sort coupons-tab " style="background-color:#fff;">
                                <a href="#" id="renqi"   class="tab-link active button coupons-list" >人气</a>
                                <a href="#" id="xiaoliang" class="tab-link button coupons-list">销量</a>
                                <a href="#" id="price" class="tab-link button coupons-list">价格
                                   <span class="sort"><i id="v-sort-up" class="sort-up"></i><em id="v-sort-down" class="sort-down"></em></span>
                                </a>
                                <a href="#" id="shaixuan" class="tab-link button coupons-list">筛选<span class="choose-icon"></span></a>
                                
                            </div>

                            <!--搜索结果列表-->
                            <div id="list">
                                <div class="order-details-rr">
                                    <?php if (!empty($product_list)){ foreach($product_list as $product){ ?>
                                    <a class="external" href="/pdetail-<?php echo $product->product_id?>">
                                     <dl class="search-list-half clearfix">
                                         <dt>
                                         <img class="lazy" data-src="<?php echo img_url($product->img_url);?>.418x418.jpg">
                                         </dt>
                                         <dd>
                                         <div class="product-name-box">
                                              <div class="product-search-name"><?php echo $product->brand_name . ' ' . $product->product_name?></div>
                                         </div>
                                         <span class="product-price">¥
                                             <?php if(isset($product->price_show) && $product->price_show):?>
                                             <span class="big-price">询价</span>
                                             <?php else:
                                             $price = explode(".", $product->product_price);
                                             ?>
                                             <span class="big-price"><?=$price[0]?></span><span class="small-price">.<?=$price[1]?></span>
                                             <?php endif;?>
                                         </span>
                                         <div class="search-list-praise"><span class="haoping">浏览量<?php echo $product->pv_num;?><em><?php echo $product->pj_real_num;?>条评价</em></span></div>
                                         </dd>
                                    </dl>
                                    </a>
                                    <?php } } else{ ?>
                                        <div style="margin:2em auto; color:#333;">搜索结果为空</div>
                                    <?php }?>
                              </div>
                            
                            </div>
							<!-- ends 搜索结果列表-->
						</div>
					</div>
				</div>
			</div>
			<?php include APPPATH."views/mobile/common/template7.php"; ?>
		</div>
		
		<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
		<script type="text/javascript" src="<?php echo static_style_url('mobile/js/search.js?v=version')?>"></script>
	<script>
		$ = Dom7;

		//$('.searchBar').on('click', function(e){
			//location.href = '/product/search.html';
		//});

		$$(document).on('ajaxStart', function (e) {
		    myApp.showIndicator();
		});
		$$(document).on('ajaxComplete', function () {
		    myApp.hideIndicator();
		});

		var parseQuery = function(query){  
		     var ret = {},  
		         seg = query.replace(/^\?/,'').split('&'),  
		         len = seg.length, i = 0, s;  
		     for (;i<len;i++) {  
		         if (!seg[i]) { continue; }  
		         s = seg[i].split('=');  
		         ret[s[0]] = s[1];  
		     }  
		     return ret;  
		 }

		var formatQuery = function(ret) {
			if (typeof ret != 'object') {
				return '';
			}
			var query = '';
			for(var p in ret) {
				query += p + '=' + ret[p] + '&';
			}

			return query.substr(0, query.length - 1);
		}
		
		$('.buttons-sort a').on('click', function(e){
			var sort_button = $(this).attr('id');
			var sortflag = $(this).data('asc');
			var sort = '';
			if (!sortflag) 
			{
				$(this).data('asc', 1);
				sort = sort_button + '_' +'asc';

			} else {
				$(this).data('asc', 0);
				sort = sort_button + '_' +'desc';
			}
			$('[data-params]').removeData('page');

			sortAjax(sort);			
		});

		var sortAjax =  function(sort){
			var template = myApp.templates['infiniteProductTemplate'];
			var query = $('[data-params]').data('params');
			var ret = parseQuery(query);
			
			switch(sort) {
				case 'price_asc':
					ret.sort = 4;
				break;
				case 'price_desc':
					ret.sort = 5;
				break;
				case 'xiaoliang_asc':
					ret.sort = 6;
				break;
				case 'xiaoliang_desc':
					ret.sort = 7;
				break;
				case 'renqi_asc':
					ret.sort = 8;
				break;
				case 'renqi_desc':
					ret.sort = 9;
				break;
				default:
					ret.sort = 0;
				break;
			}
			query = formatQuery(ret);
			console.log(query);
			$('[data-params]').attr('data-params', query);
			var url = $('[data-params]').data('source');
			$.getJSON(url , query, function (result){
			    if (result.success == 0){
			        alert('数据错误');
			    } else {	
			    	$('.listb ul').removeData('completed');		        
			    	$('.listb ul').removeData('page');		        
			    	$('.listb ul').html('');
			        $('.listb ul').append(template(result));	
			        $$('.lazy').trigger('lazy');		        
			    }			    
			});
		};

		$("#renqi").on('click',function(){
			$(this).addClass("active");
			$("#xiaoliang").removeClass("active");
			$("#price").removeClass("active");
			$("#v-sort-up").removeClass("sort-active");
			$("#v-sort-down").removeClass("sort-active2");
			
		});
		$("#xiaoliang").on('click',function(){
			$(this).addClass("active");
			$("#renqi").removeClass("active");
			$("#price").removeClass("active");
			$("#v-sort-up").removeClass("sort-active");
			$("#v-sort-down").removeClass("sort-active2");
			
		});
		$("#price").on('click',function(){
			$(this).addClass("active");
			$("#renqi").removeClass("active");
			$("#xiaoliang").removeClass("active");

      		if(!$("#v-sort-up").hasClass('sort-active')) {
	            $("#v-sort-up").addClass('sort-active');
				$("#v-sort-down").removeClass("sort-active2");
	        }else{
	        	$("#v-sort-down").addClass('sort-active2');
				$("#v-sort-up").removeClass("sort-active");
	        }
		});
	</script>
	
	</body>
</html>
