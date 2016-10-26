<?php include APPPATH."views/mobile/header.php"; ?>

		<style>
			.page {
				background-color:#0D7AA5;
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
			
		</style>

		
		<div class="views">
			<!-- Your main view, should have "view-main" class-->
			<div class="view view-main">
				<!-- Pages, because we need fixed-through navbar and toolbar, it has additional appropriate classes-->
				<div class="pages">
					<!-- Index Page-->
					<div data-page="searchResult" class="page">

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
						<div class="page-content  infinite-scroll" data-template="infiniteProductTemplate" data-parent=".listb ul" data-source="/product/ajax_product_list/searchResult" data-params="kw=<?php echo $kw;if (isset($ids)) echo '&ids='.$ids?>">
							
							<!-- 广告位 -->	
							<?php echo isset($ad[0]->ad_code) ? $ad[0]->ad_code : ''?>
							<!-- ends 广告位-->
							<div class="buttons-row tab-qh buttons-sort">
					                        <a href="javascript:void(0)" id="renqi"      class="tab-link active button button-secondary">人气</a>
					                        <a href="javascript:void(0)" id="xiaoliang"  class="tab-link button button-secondary">销量</a>
					                        <a href="javascript:void(0)" id="price"       class="tab-link button button-secondary">价格</a>
					                </div>

							<!--搜索结果列表-->
							<div class="content-block" style="padding-top:10px;">
								<div class="listb">
								 <ul class="sbox clearfix">
								<?php 
								if (!empty($product_list)){
								foreach($product_list as $product){?>
			
								     <li data-shopprice="<?php echo $product->shop_price;?>" data-xiaoliang="<?php echo $product->ps_num;?>" data-renqi="<?php echo $product->pv_num;?>">
									<div class="products-list clearfix">
									<a class="external" href="/pdetail-<?php echo $product->product_id?>">
								        <div class="img_sbox"><img class="lazy" data-src="<?php echo img_url($product->img_url);?>.418x418.jpg"></div>
								        <div class="prod_name"><?php echo $product->brand_name . ' ' . $product->product_name?></div>
								        <div class="bline clearfix">
								            <div class="favoheart"><span><?php echo get_page_view('product',$product->product_id)?></span></div>
								            <!-- <div class="price_bar"><span class="prod_pprice"><?php echo isset($product->is_zhanpin) && $product->is_zhanpin ? '询价' : $product->shop_price?></span></div> -->
								            <?php if(isset($product->price_show) && $product->price_show):?>
								                <div class="price_bar xunjia_product"><span class="prod_pprice" >询价</span></div>
								            <?php else:?>
								                <div class="price_bar" style=""><span class="prod_pprice"><?php echo $product->product_price?></span></div>
								            <?endif;?>
								        </div>
									</a>
										<?php if ( $product->is_hot ){ ?> 
										    <div class="mark mark_sale">热品</div>
										<?php }elseif ( $product->is_new ){ ?> 
										    <div class="mark mark_new">新品</div>
										<?php }elseif ( isset($product->is_zhanpin) &&  $product->is_zhanpin){ ?> 
										    <div class="mark mark_new">展品</div>
										<?php }elseif ( $product->is_offcode ){ ?> 
										    <div class="mark mark_offcode">促销</div>
										<?php } ?>
									</div>
								    </li>

								<?php }} else{?>
									<div style="margin:2em auto">搜索结果为空</div>
								<?php }?>
								 </ul>
								</div>
							<!-- ends 搜索结果列表-->
						</div>
					</div>
				</div>
				</div>
			</div>
			<?php include APPPATH."views/mobile/common/template7.php"; ?>
		</div>
		
		<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
		
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

	</script>
	
	</body>
</html>
