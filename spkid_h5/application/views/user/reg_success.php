<?php include APPPATH."views/common/header.php"; ?>
<script type="text/javascript" src="<?php print static_style_url('js/lhgdialog/lhgdialog.min.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/login.js'); ?>"></script>
<link rel="stylesheet" href="<?php print static_style_url('css/login.css'); ?>" type="text/css" />
<script type="text/javascript">
function isIE() {
	var version = navigator.userAgent.indexOf('MSIE')>-1;
	return version;
}
$(window).ready(function () {
	var length = $('.hotArea').length;
	var width = 320*length;
	$('..success_left ul').width(width);
});
$(function () {

	//banner鼠标移入效果
	$('.success_left li').hover(function () {
		$(this).find('.iXQShadowBoxGreen').stop(true).fadeTo(150,1);

		$(this).find('.newsBoxForMove').show();
	},function () {
		$(this).find('.iXQShadowBoxGreen').stop(true).fadeTo(200,0);
		$(this).find('.newsBoxForMove').hide();
	});

	//banner切换效果效果
	var length = $('.hotArea').length;
	$('.arrowLeft').click(function () {
		if (!($('.banner ul').is(':animated'))) {
			$('.banner ul').is(':animated')
			var width=320;
			//if (isIE()) {width=318} else{width=320};
			var now = parseInt($('.banner ul').css('left'))/-width;
			var next = now==0?length-1:now-1;
			var pos = next*-width;
			$('.banner ul').animate({'left':pos},'fast','linear');
		};
	});

	$('.arrowRight').click(function () {
		if (!($('.banner ul').is(':animated'))) {
			var width=320;
			//if (isIE()) {width=318} else{width=320};
			var now = parseInt($('.banner ul').css('left'))/-width;
			var next = now==length-1?0:now+1;
			//alert(now);
			//alert(next);
			var pos = next*-width;
			$('.banner ul').animate({'left':pos},'fast','linear');
		}
	});
});

</script>
<div id="content">
	
<div class="success_box">
	<div class="success_left">
		<div class="bannerTitle">今日特卖</div>
		<div class="arrowLeft"></div>
		<div class="banner">
			<ul>
                            <?php foreach($sale_rush as $rush) {?>
				<li class="hotArea">
					<div class="iXQShadowBoxBlue"></div>
					<div class="iXQShadowBoxGreen" style="opacity: 0;"></div>
                                        <?if(!empty($rush->rush_prompt)):?>
					<p class="newsBoxForMove" style="display: none;"><?=$rush->rush_prompt?></p>
                                        <?endif?>
					<div class="iXQShadowBoxText">
						<div class="iXQTitle">
							<span class="arrow"><?=$rush->rush_brand?></span>
							<p><?=$rush->rush_category?></p>
						</div>
						<p class="iXQOff" style="color:#d90000;">
							<font><?=$rush->rush_discount?></font>
							<span>折起</span>
						</p>
						<p class="iXQDateOff"><?=$rush->end_day?>天后结束</p>
					</div>
					<a href="/rush-<?=$rush->rush_id?>.html" target="_blank">
						<img width="320px" height="238px;" alt="<?=$rush->rush_brand?>" src="<?=img_url(@$rush->image_before_url_3)?>">
					</a>
				</li>
                                <?php } ?>
			</ul>
		</div>
		<div class="arrowRight"></div>
	</div>
	<div class="success_info">
		<h1 class="welcome_to">欢迎来到<?php print SITE_NAME;?>！</h1>
		<h2 class="gx_success">恭喜您注册成功！</h2>
		<ul class="success_six">
			<li class="suc_home">首个婴童品牌限时特卖</li>
			<li class="suc_am">14：00每日新品开抢</li>
			<li class="suc_work">1000余家品牌合作</li>
			<li class="suc_china">100%品质保证</li>
			<li class="suc_huo">1000个城市货到付款</li>
			<li class="suc_seven">7天无理由退换货</li>
		</ul>
		<div class="success_bot">
			<div class="suc_right width200">
				<p>您可以马上开始您的购物之旅</p>
				<a href="/" title="立即抢购" class="sub_form ml40" hidefocus="">立即抢购</a>
			</div>
			<div class="suc_left">
				<p>完善资料即获700积分!（100积分=1元）</p>
				<a href="/user/profile" herf="javascript" title="完善资料" class="login_now ml58" hidefocus="">完善资料</a>
			</div>
		</div>
	</div>
</div>


	<div class="cl"></div>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>