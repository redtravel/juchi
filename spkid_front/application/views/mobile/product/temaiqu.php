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

			.list_temai {
				text-align:center; width:100%;
			}

			.list_temai ul {
				list-style:none;
			}

			.list_temai ul li{
				position:relative;
			}

			ul li a img {
				width:100%;
			}

			.list_temai ul li .temai-jiage {
				position:absolute;
				top:2px;
				right:2px;
				text-align:right;
				margin-right:1em;
			}

			.list_temai ul li .temai-jiage .market_price s {
				font-size:1.2em;
				color:grey;
			}
			.list_temai ul li .temai-jiage .shop_price {
				font-size:2em;
				color:orange;
			}
			
			
		</style>

		
		<div class="views">
			<!-- Your main view, should have "view-main" class-->
			<div class="view view-main">
				<!-- Pages, because we need fixed-through navbar and toolbar, it has additional appropriate classes-->
				<div class="pages">
					<!-- Index Page-->
					<div data-page="temaiqu" class="page no-toolbar">

						<!-- navbar -->
						<div class="navbar">
							<div class="navbar-inner">
								<div class="left">
									<a href="#" class="link back">
										<i class="icon icon-back">
										</i>
									</a>
								</div>
								<div class="center c_name searchBar">
									特卖区
								</div>								
							</div>
						</div>
						<!-- ends navbar -->
						<!-- Scrollable page content-->
						<div class="page-content">

							<!--特卖区结果列表-->
								<div class="list_temai" style="margin-top:20px;">
								 	 	<ul>

								 	 	<?php if(!empty($list)):?>
								 	 			<?php foreach($list as $k => $v):?>
								 	 				<li><a href="<?php echo $v->ad_link ? $v->ad_link : ''?>" class="external"><img style="width:100%" src="<?php echo $v->pic_url ? img_url($v->pic_url) : ''?>" alt=""></a>
								 	 				
								 	 				</li>
								 				<?php endforeach;?>
								 	 		<?php endif;?>
								 	 	</ul>
								</div>
							<!-- ends 特卖区结果列表-->
						</div>
					</div>
				</div>
			</div>
			<!-- product -->
			<script type="text/template7" id="infiniteTemaiTemplate">
			{{#each data}}
			<li>
				<a href="/pdetail-{{product_id}}.html" class="external"><img style="width:100%" src="{{../img_domain}}/{{img_url}}" alt=""></a>
				<div class="temai-jiage">
					<div class="market_price"><s>{{market_price}}</s></div>
					<div class="shop_price">{{shop_price}}</div>
				</div>	    
			</li>
			{{/each}}
			</script>
		</div>
		
		<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
		
	<script>
		$ = Dom7;

		$('.searchBar').on('click', function(e){
			location.href = '/product/search.html';
		});

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
		
		$$('.buttons-sort a').on('click', function(e){
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
			sortAjax(sort);
		});

		var sortAjax =  function(sort){
			var template = myApp.templates['infiniteTemaiTemplate'];
			var query = $('[data-params]').data('params');
			var ret = parseQuery(query);
			ret.sort = sort;
			query = formatQuery(ret);
			console.log(query);
			$('[data-params]').attr('data-params', query);
			var url = $('[data-params]').data('source');
			$.getJSON(url , query, function (result){
			    if (result.success == 0){
			        alert('数据错误');
			    } else {			        
			    	$('.listb ul').html('');
			        $('.listb ul').append(template(result));			        
			    }			    
			});
		};

	</script>
	
	</body>
</html>
