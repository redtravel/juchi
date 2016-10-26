<?php include APPPATH . "views/mobile/header.php";?>
<link rel="stylesheet" type="text/css" href="<?php echo static_style_url('mobile/css/tabs.css?v=version') ?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/animate.css?v=version') ?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/lightGallery.css?v=version')?>">
<style>
	.comment_item_r em{ font-style:normal; font-weight:normal; font-size:0.8em;}
	.comment_item_r strong{ font-weight:normal; }
	.comment_reply h2{font-size:1.2em; }

</style>
<div class="popover popover-menu">
	<div class="popover-angle"></div>
	<div class="popover-inner">
		<div class="navbar">
			<div class="navbar-inner">
				<div class="center">演示站商城</div>
			</div>
		</div>
		<div class="content-block share_modal">
			<div class="jiathis_style_32x32" style="width:1;height:1">
				<a class="jiathis_button_weixin"></a>
				<a class="jiathis_button_qzone"></a>
				<a class="jiathis_button_cqq"></a>
				<a class="jiathis_button_tsina"></a>

				<a class="jiathis_counter_style"></a>
			</div>
		</div>

	</div>
</div>

<!-- <div class="popup popup-tabs-pannel tab_hu">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="center">
				<?php echo isset($title) ? $title : '' ?></div>
			<div class="right close-popup" style="margin-right:1em">关闭</div>
		</div>
	</div>
	<div class="content-block tabs-pannel-content hu-tck"></div>
</div> -->

<!-- 点击tab标签的时候，出现tab面板 -->
<div class="popup tab_hu popup-detail1">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="center">
				<?php echo isset($title) ? $title : '' ?></div>
			<div class="right close-popup" style="margin-right:1em">关闭</div>
		</div>
	</div>
	<div class="content-block tabs-pannel-content">
		<?php echo $p->detail1 ?>
	</div>
</div>
<!-- ends 点击tab标签的时候，出现tab面板 -->

<!-- 点击tab标签的时候，出现tab面板 -->
<div class="popup tab_hu popup-detail2">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="center">
				<?php echo isset($title) ? $title : '' ?></div>
			<div class="right close-popup" style="margin-right:1em">关闭</div>
		</div>
	</div>
	<div class="content-block tabs-pannel-content">
		<?php echo $p->detail2 ?>
	</div>
</div>
<!-- ends 点击tab标签的时候，出现tab面板 -->

<!-- 点击tab标签的时候，出现tab面板 -->
<div class="popup tab_hu popup-detail3">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="center">
				<?php echo isset($title) ? $title : '' ?></div>
			<div class="right close-popup" style="margin-right:1em">关闭</div>
		</div>
	</div>
	<div class="content-block tabs-pannel-content">
		<?php echo $p->detail3 ?>
	</div>
</div>
<!-- ends 点击tab标签的时候，出现tab面板 -->

<!-- 点击tab标签的时候，出现tab面板 -->
<div class="popup tab_hu popup-detail4">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="center">
				<?php echo isset($title) ? $title : '' ?></div>
			<div class="right close-popup" style="margin-right:1em">关闭</div>
		</div>
	</div>
	<div class="content-block tabs-pannel-content">
		<?php echo $p->detail4 ?>
	</div>
</div>
<!-- ends 点击tab标签的时候，出现tab面板 -->

<!-- 点击tab标签的时候，出现tab面板 -->
<div class="popup tab_hu popup-detail5">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="center">
				<?php echo isset($title) ? $title : '' ?></div>
			<div class="right close-popup" style="margin-right:1em">关闭</div>
		</div>
	</div>
	<div class="content-block tabs-pannel-content">
		暂无相关内容
	</div>
</div>
<!-- ends 点击tab标签的时候，出现tab面板 -->



<!-- 留言对话框 -->
<div class="popup popup-Iliuya public-bg">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="center">问题留言</div>
			<div class="right close-popup" style="margin-right:1em">关闭</div>
		</div>
	</div>
	<div>
		<h4 style="color:#fff; text-align:center; padding:20px 0 0 10px;">请确认以下联系方式，便于客服及时联系您！</h4>

		<div class="list-block liuyan-box-info" style="margin:10px 0;">
			<ul>
			  <li>
			    <div class="item-content">
			      <div class="item-media"><i class="user-tracey"></i></div>
			      <div class="item-inner">
			        <div class="item-input">
			          <input type="text" placeholder="姓名：" name="name" class="" style="background:white; padding-left:5px;" value="<?php echo $user_name ?>"/>
			        </div>
			      </div>
			    </div>
			  </li>

			  <li>
			    <div class="item-content">
			    <div class="item-media"><i class="phone-ico-hu"></i></div>
			      <div class="item-inner">
			        <div class="item-input">
			          <input type="tel" placeholder="联系方式：" name="mobile" style="background:white; padding-left:5px;" value="<?php echo $mobile ?>">
			        </div>
			      </div>
			    </div>
			  </li>

			</ul>
		</div>
	</div>

	<div class="popup-Iliuya-content">
	    <div class="order-details-rr">
		 <textarea name="popup-liuya-content" class="liuyan-hu" placeholder='请填写您关于产品的问题，提交后，我们尽快请专家整理答案回复于您，并有可能将此问题，录入产品的”说明“选项中' onfocus="this.style.color='#000'; this.value='';" style="color: #f00;"></textarea>
            </div>
	</div>

	<div class="yywtoolbar">
	        <div class="yywtoolbar-inner row no-gutter">
	            <div class="col-100  payment-hu"><a class="link button-tijiao" href="javascript:void(0)">提交</a></div>
	        </div>
    	</div>
</div>
<!-- ends 留言对话框 -->





	<?php $product_desc_additional = (!empty($p->product_desc_additional)) ? json_decode($p->product_desc_additional, true) : array();?>

<div class="views">
	<div class="view view-main">

		<div class="toolbar">
			<div class="toolbar-inner2 row no-gutter">
				<div class="col-20"></div>
				<?php if (!empty($collect_data) && deep_in_array($p->product_id, $collect_data)) {?>
				<div class="col-20 heart-hu-red">
					<a class="link" href="#"></a>
				</div>
				<?php } else {?>
				<div class="col-20 heart-hu-gray" onclick="add_to_collect (<?php echo $p->product_id; ?>,3,this,'heart-hu');" >
					<a class="link" href="#"></a>
				</div>
				<?php }?>


				<div class="col-60 registration-hu">
				<?php if (date("Y-m-d", strtotime($product_desc_additional['desc_waterproof'])) > date('Y-m-d')) {?>
					<a class="link woyaobaoming" data-href="/cart/checkout_course/<?=$sub_list[$color_id]['sub_list'][0]->sub_id?>" href="javascript:void(0)" style="color:#fff;">我要报名</a>
				<?php } else {?>
					<a disabled="disabled" class="link external" href="/cart/checkout_course/<?=$sub_list[$color_id]['sub_list'][0]->sub_id?>" style="color:#fff;">已结束报名</a>
				<?php }?>
				</div>
			</div>
		</div>

		<div class="pages">
			<div data-page="course_info" class="page ">
				<div class="navbar">
					<div class="navbar-inner">
						<div class="left">
							<a href="#" class="link back history-back"> <i class="icon icon-back"></i>
							</a>
						</div>
						<div class="center">
							<?=$title?></div>
						<div class="right">
							<a href="javascript:void(0)" class="link icon-only short-func-btn"> <i class="icon icon-bars"></i>
							</a>
						</div>
					</div>
				</div>
				<div class="page-content article-bg3 edu-fot ">
				<!-- 顶部快捷键 -->
				<div class="short-func-box animated">
					<div class="short-func-row">
						<section id="kefu">
							<div class="kefu-erji hu-pro-gg"><p>客服</p></div>
						</section>

						<section id="fenxiang" class="open-popover"  data-popover=".popover-menu">
							<div class="hu-share hu-pro-gg"><p>分享</p></div>
						</section>
						<section id="shouye">
							<div class="hu-home hu-pro-gg"><p>首页</p></div>
						</section>
					</div>
				</div>
				<!-- ends 顶部快捷键 -->

					<div class="details" >
						<img  src="<?php print img_url($g_list[$color_id]["default"]->img_url . ".850x850.jpg");?>" /></div>

					<!-- educational-training start -->
					<div class="educational-training">
						<div class="edc-dw">
							<h2>
								<?php print $p->product_name?></h2>
							<div class="browse">
								<span>
									<?php echo get_page_view('course', $p->product_id); ?></span>
							</div>
						</div>
						<div class="educational-xq ">
							<ul class="edu-lb">
								<li>
									老师：
									<?=$p->subhead?></li>
								<li>
									时间:
									<?php echo date("Y.m.d", strtotime($p->
		package_name)); ?>
									<?php if (isset($product_desc_additional['desc_waterproof'])) {
	echo '-' . date("Y.m.d", strtotime($product_desc_additional['desc_waterproof']));
}
?></li>
								<li>
									地点:
									<?if (isset($product_desc_additional['desc_crowd'])) echo $product_desc_additional['desc_crowd']?></li>
								<li>
									已有
									<?=$p->ps_num?>人报名</li>
							</ul>
							<div class="feiyong">
								<h3>
									费用：
									<span class="edu-price">
										￥
										<?=$p->product_price?></span>
									<span class="edu-he">元</span>
								</h3>
								<div class="description clearfix">
									<div class="jgsm">促销信息</div>
									<div class="fylb">
                                                                            <!--
										<?php if ($p->
	is_promote): ?>
										<div class="fy-list">
											<?=date("m/d", strtotime($p->
	promote_start_date))?> -
											<?=date("m/d", strtotime($p->
	promote_end_date))?>期间报名可以优惠￥
											<?=$p->shop_price - $p->promote_price?>元</div>
										<?php else: ?>
										<div class="fy-list">本课程暂无促销活动</div>
										<?php endif;?>
										<?php if ($p->
	is_promote): ?>
										<div class="fy-list">
											非促销活动期间价格：￥
											<?=$p->shop_price?></div>
										<?php endif;?>
                                                                            -->
                                                                            <?php if (isset($product_desc_additional['desc_dimensions']) && !empty($product_desc_additional['desc_dimensions'])): ?>
                                                                            <div class="fy-list"><?=$product_desc_additional['desc_dimensions']?></div>
                                                                            <?php else: ?>
                                                                            <div class="fy-list">本课程暂无促销活动</div>
                                                                            <?php endif;?>
                                                                        </div>
								</div>
							</div>
						</div>
						<!-- educational-training end -->

						<!-- yyrz start -->
						<div class="certification">
							<img src="<?php echo static_style_url('mobile/img/education-img.jpg') ?>"></div>
						<!-- yyrz start -->

						<!-- course start -->
						<div class="educational-training">
							<ul id="auto-loop" class="gallery" style="display:none"></ul>
							<div class="educational-xq course"><?=$p->product_desc?></div>
						</div>
						<!-- course end -->

						<!-- buttons-row start -->
						
                    <div class="tabbar buttons-row">
                    		<a class="tab-link button open-popup" data-fun="peixunxiangqing" href="#tab1" data-popup=".popup-detail1"> 培训详情 </a>
                    		<a class="tab-link button open-popup" data-fun="laoshijieshao" href="#tab2" data-popup=".popup-detail2"> 老师介绍 </a>
                    		<a class="tab-link button open-popup" data-fun="jiaotongluxian" href="#tab3" data-popup=".popup-detail3"> 交通路线 </a>
                    		<a class="tab-link button open-popup" data-fun="xuanyuanpingjia" href="#tab4" data-popup=".popup-detail4"> 学员评价 </a>
                    		<a class="tab-link button open-popup" data-fun="xiangguanpeixun" href="#tab5" data-popup=".popup-detail5"> 电子书下载</a>
                    </div>

					<!-- buttons-row end -->

					<!-- registration  start -->
					<div class="educational-training">
						<div class="educational-xq registration">
							<h3>在线报名流程</h3>
							<ul class="registration-lb clearfix">
								<li>
									<span>1</span>
									报名信息
								</li>
								<li>
									<span>2</span>
									在线付款
								</li>
								<li>
									<span>3</span>
									报名成功
								</li>
								<li>
									<span>4</span>
									参加培训
								</li>
							</ul>
						</div>
					</div>
					<!-- registration  end -->

				</div>

			</div>
		</div>

	</div>
</div>

</div>
<?php include APPPATH . "views/mobile/common/footer-js.php";?>
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/lightGallery.min.js?v=version'); ?>"></script>
<script>
    //详情内容画廊
    $(document).ready(function(){
        var v_img_loop = '';
        $(".educational-xq img").each(function(){
            v_img_loop += '<li data-src="'+ $(this)[0].src +'"><a href="#"><img src="'+ $(this)[0].src +'" /></a></li>';
        });
        $("#auto-loop").append(v_img_loop);

        $('.educational-training img').on('click', function(){
            $('#auto-loop').show();
            $('#auto-loop li').eq(0).click();
        });
        $("#auto-loop").lightGallery({
            loop:true,
            auto:true,
            pause:4000,
            counter:true,
            caption:'yeuyawang',
            onCloseAfter:function(){$('#auto-loop').hide()}
        });

    });
</script>
<script>
	$(function (){
	    var list = $(".navbar .center");
	    list.each(function(index, element) {
	        var str = $(this).text();
	        $(this).text(cutStr(str,28)+'   ');
	    });
	    $('.toolbar.tabbar').remove();
	});

</script>
<script>
	var tag_id = product_id = '<?php echo $p->product_id ? $p->product_id : 0; ?>';
	var tagType = 3; //代表课程
	var comment_type = 1;

	$$('#kefu').on('click', function(e){
	    myApp.confirm('服务时间 9:00-21:00', '现在拨打热线', function () {
            $$('<a href="tel://4009905920">拨打电话</a>').click();
        });
	});


	$$('.woyaobaoming').on('click', function(e){
		if(checkLogin(false)) {
			location.href = $$(this).attr('data-href');
		} else {
			//do nothing;
		}
	});
</script>

<script type="text/javascript" src="http://v3.jiathis.com/code/jia.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/tabs.js?v=version'); ?>"></script>

<?php include APPPATH . "views/mobile/common/meiqia.php";?>
<?php include APPPATH . "views/mobile/footer.php";?>
