<?php include APPPATH."views/mobile/header.php"; ?>

<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/framework7.material.css')?>
">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/main.css')?>
">
<!-- Bootstrap -->
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/bootstrap.min.css')?>
">
<style>

		
               /*category_page slidee*/
		.m-category-sly{padding:10px 0 0; -webkit-animation:fadeInLeft 1s linear; }
		.m-category-sly .frame{height:160px; overflow:hidden;}
		.m-category-sly .frame ul{list-style:none;	margin:0; padding:0; height:145px;}
		.m-category-sly .frame ul li{float:left; width:140px; height:140px; margin:0 1px 0 0; padding:1px; border:1px solid #f1f1f1; border-radius:5px; background:#fff; color:#ddd; text-align:center; cursor:pointer;}
		.m-category-sly .frame ul li.active{color:#fff;}
		.m-category-sly .effects{height:147px; overflow-y:show;	-webkit-perspective:800px; perspective:800px; 	-webkit-perspective-origin:50% 50%; perspective-origin:50% 50%;}
		.m-category-sly .effects ul{-webkit-transform-style:preserve-3d; transform-style:preserve-3d;}
		.m-category-sly .effects ul li{position:relative; margin:0 -20px; -webkit-transform:rotateY(60deg) scale(0.9); transform: rotateY(60deg) scale(0.9);	-webkit-transition:-webkit-transform 300ms ease-out; transition:transform 300ms ease-out;}
		.m-category-sly .effects ul li.active{z-index:10;	-webkit-transform:scale(1);	transform:scale(1);}
		.m-category-sly .effects ul li.active ~ li{-webkit-transform:rotateY(-60deg) scale(0.9); transform:rotateY(-60deg) scale(0.9);}
		.m-category-sly .effects ul li img{width:100%; height:100%; border-radius:5px;}
		.m-category-sly .effects ul li:before{
		  pointer-events:none; position:absolute; z-index:-1; content:''; top:100%; left:5%; height:10px; width:90%;
		  background:-webkit-radial-gradient(center, ellipse, rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0) 80%);
		  background:radial-gradient(ellipse at center, rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0) 80%);
		  -webkit-transform: translateY(-5px);
		}

		.m-category-sly .effects ul li.active:before{left:10%; width:80%; height:12px}


		 .nav-tabs{ float:left; width:20%;  background:#539fbf; }
		 .nav-stacked>li+li,
		.nav-tabs>li{position:relative;  height:46px; margin:0; padding:0; border-bottom:1px solid #0D7AA5;}
		 .nav-tabs>li.active>a, .product-type .nav-tabs>li.active>a:hover, .product-type .nav-tabs>li.active>a:focus,
		
		.nav-tabs>li>a{  border:none; border-radius:0; padding:0; line-height:46px; text-align:center; color:#fff; margin-right: 0;}
		
		.product-type .nav-tabs>li.active>a, .product-type .nav-tabs>li.active>a:hover, .product-type .nav-tabs>li.active{color:#404040;}

		.product-type .tab-content{ padding-top:10px; padding-left:15px; overflow: auto;}
		.product-type .tab-pane>ul>li{float:left; width:33.333%; margin-bottom:10px;}
		.product-type .tab-pane>ul>li>a{display:block; margin-right:5px; font-size:12px; text-align:center; color:#323232;}
		.product-type .tab-pane>ul>li>a>img{display:block; width:100%; margin-bottom:5px; }
		.page {
				background-color:#0D7AA5;
				color:black;
			}

		.product-type .nav-tabs {
			border-top-left-radius:10px;  -webkit-border-top-left-radius:10px; -mos-border-top-left-radius:10px; -moz-border-top-left-radius:10px;
			border-bottom-left-radius:10px; -webkit-border-bottom-left-radius:10px; -mos-border-bottom-left-radius:10px; -moz-border-bottom-left-radius:10px;
		}
		
		.third_item {
			list-style:none;
		}

		.third_item li{ border-bottom:dashed 1px #ccc; 	padding-bottom:10px; width:100%; padding-left:0;
			
		}
		
		.third_item li a{ border-right: 1px solid #656668; padding-right:10px;  margin-right:10px; }
		
		.tab-content-additional {
			background:white; width:80%;
		}

		.tab-content>.tab-pane {
			
			padding-right:6px;
		}
		
		.nav-tabs{ border-bottom: none;}
		</style>
</head>
<body>
<div class="statusbar-overlay"></div>
<div class="panel-overlay"></div>
<div class="views">
	<!-- Your main view, should have "view-main" class-->
	<div class="view view-main">
		<!-- Pages, because we need fixed-through navbar and toolbar, it has additional appropriate classes-->
		<div class="pages">
			<!-- Index Page-->
			<div data-page="index" class="page">
				<!-- Scrollable page content-->
				<div class="navbar">
					<div class="navbar-inner">
						<div class="left">
							<a href="javascript:void(0)" class="link history-back"> <i class="icon icon-back"></i>
							</a>

						</div>
						<div class="center c_name">分类检索</div>
						<div class="right">
							<a href="/product/search" class="link icon-only open-panel external"> <i class="icon searchico"></i>
							</a>
						</div>

					</div>
				</div>

				<div class="page-content">
					<div class="content-block" style="padding-right:0; padding-left:5px;">
						<section class="product-type clearfix" id="m-category" style="height: 100%;">
							<!-- Nav tabs -->
							<ul class="nav nav-tabs nav-stacked tab-radius" role="tablist">

								<?php foreach($product_type_list as $k =>
								$v): ?>
								<li role="presentation" type_id="<?php echo $v['type_id']?>
									">
									<a href="#type<?php echo $k?>
										" aria-controls="cate
										<?php echo $k?>
										" role="tab" data-toggle="tab">
										<?php echo $v['type_name']?></a>
								</li>
								<?php endforeach;?>
							</ul>
							 <script>
					                    $$("ul li:first-child").addClass('nav-tabs')
					                </script>

							<!-- Tab panes -->
							<div class="tab-content tab-content-additional">

								<?php foreach($product_type_list as $f =>$fv):?>
								<div role="tabpanel" class="tab-pane fade" id="type<?php echo $fv['type_id']?>">
									<?php foreach($fv['list'] as $sk =>$sv):?>
										<div style="margin-top:5px;">
											<a href="/searchResult?kw=<?php echo $sv['type_name']?>&search_type=p" style="color:#97989a;font-weight:bold;margin-bottom:1em;display:block;" class="external"><?php echo $sv['type_name']?></a>
											<ul class="third_item"><li>
												<?php if(!empty($sv['list'])):?>
													<?php foreach ($sv['list'] as $tk =>$tv):?>
														<!-- buttons-row start -->
															<a href="/searchResult?kw=<?php echo $tv['type_name']?>&search_type=p" class="" style="color:#636466; "><?php echo $tv['type_name']?></a>														
														<!-- buttons-row end -->
													<?php endforeach;?>
												<?php endif;?>
											</li></ul>
										</div>
									<?php endforeach;?>
								</div>
								<?php endforeach;?>
							</div>

						</section>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?php echo static_style_url('mobile/js/framework7.js?v=version')?>"></script>
<script src="<?php echo static_style_url('mobile/js/jquery-1.11.0.min.js')?>" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo static_style_url('mobile/js/bootstrap.min.js')?>"></script>

<script>		
		Dom7('ul[role="tablist"]').find('li').eq(0).addClass('active');
		Dom7('[role="tabpanel"]').eq(0).addClass('active').addClass('in');
		$('.nav-tabs li').click(function(){
			var type_id = $(this).attr('type_id'); 
			var index = $(this).index();
			$('.tab-pane').eq(index).siblings().removeClass('active in').end().addClass('active in');
		});
        $('.history-back').on('click', function(e){ history.go(-1);  });
	</script>

<?php include APPPATH."views/mobile/footer.php"; ?>
