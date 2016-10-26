<?php include APPPATH . "views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/plist.css'); ?>" type="text/css" />
<link href="<?php print static_style_url('css/list.css'); ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript">
    function change_order (uri) {
	location.href = base_url+uri;
    }
    
$(function(){
	//选择品类尺码模块鼠标移入移出效果
	$('.bytype').hover(function () {
        $(this).find('span').css("background","url(http://static01.tbaby.cn/img/plist/plist_bg.png) 0 -482px no-repeat");
	    $(this).find('.hide_type').show();
	    $(this).find('.hide_type').append()
	},function () {
	    $(this).find('.hide_type').fadeOut(100);
        $(this).find('span').css("background","url(http://static01.tbaby.cn/img/plist/plist_bg.png) 0 -510px no-repeat");
	});

	//点击选择品类尺码模块的筛选条件后文字信息变化
	$('.hide_type a').click(function () {
	    var text = $(this).text();
	    $(this).parent().parent().parent().parent().parent().find('.select_type').text(text);
	});

	//点击价格模块切换样式
	/*
	$('#select_priceUp').click(function () {
	    if ($(this).hasClass('jiageUp')){
            return;
		    
	    } else{
            $(this).addClass('jiageUp');
            if($(".jiageDown").size()>0){
                $('#select_priceDown').removeClass('jiageDown');
            }
        }
	});
    $('#select_priceDown').click(function () {
        if ($(this).hasClass('jiageDown')){
            return;
            
        } else{
            $(this).addClass('jiageDown');
            if($(".jiageUp").size()>0){
                $('#select_priceUp').removeClass('jiageUp');
            }
        }
    });
	*/
	//选择品类尺码模块的筛选条件鼠标移入移出效果
	$('.sec_area a').hover(function () {
	    $(this).addClass('hover');
	},function () {
	    $(this).removeClass('hover');
	});

	//去除尺码中的最后一个竖线
	$('.footage ul').each(function(){
		$(this).find('li:last').css('backgroundImage','none');
	});
});
</script>
<?php
$share_content = "我在".SITE_NAME."看中了".@addslashes($arr_rush['rush_index'])."，专柜品质，价格实惠,和大家分享哟！一般人我不告诉哒~粑粑麻麻最爱的婴童品牌特卖网站，每天下午2点准时上新";
$share_pic='';
?>
<div id="content" style="overflow:visible">
    <div class="now_pos">
	<a href="index.html">首页</a>
	&gt; <a href="/rushlist">限时抢购</a>
	&gt; <a class="last_nav" href="javascript:void(0)"><?= $arr_rush['rush_index']?></a>
    </div>
    <div class="plistMain">
	<div class="productListTopAdv" id="productListTopAdv">
	    <img src="<?php print img_url( $arr_rush['image_ing_url']); ?>" width="984" height="320" />
	    <!--倒计时-->
	    <div id="countdown" class="countdown">
		<span class="font14">剩余:</span>
		<span class="red font16b" id="timeDay"></span><span class="font14">天</span>
		<span class="red font16b" id="timeHour"></span><span class="font14">时</span>
		<span class="red font16b" id="timeMinu"></span><span class="font14">分</span>
		<span class="red font16b" id="timeSecond"></span><span class="font14">秒</span>
	    </div>
	</div>
	<script type="text/javascript" src="<?php print static_style_url('js/countDown.js'); ?>"></script>
	<script type="text/javascript">
	    countDown({
		startTime:'<?= $arr_rush['start_date'] ?>',
		endTime:'<?= $arr_rush['end_date'] ?>',
		nowTime:'<?php echo date('Y-m-d H:i:s');?>',
		dayElement:'timeDay',
		hourElement:'timeHour',
		minuElement:'timeMinu',
		secElement:'timeSecond',
		callback:function () {
		    //alert('下线');
		}
	    });
	</script>
	<?php // include  APPPATH . 'views/rush/rush_ promotion.php'; ?>

	<!--分享微博空间开始-->
	<script src="http://tjs.sjs.sinajs.cn/open/api/js/wb.js?appkey=708902181" type="text/javascript" charset="utf-8"></script>
	<div id="shareFriend" style="height:25px; float:right;">	
		<p style="width:auto; float:left; font-size:12px; margin:0; padding:0; height:16px; line-height:16px;">分享给好友：</p>
		<!--分享到微博-->
		<div class="shareFriends" id="sina_weibo">
			<wb:share-button title="<?=$share_content;?>" count="n" appkey="708902181" pic=""></wb:share-button>
		</div>
		<!--分享到qq好友和群-->
		<div class="shareFriends" >
			<script type="text/javascript">
				(function(){
					var p = {
					url:location.href,
					showcount:'0',/*是否显示分享总数,显示：'1'，不显示：'0' */
					desc:'',/*默认分享理由(可选)*/
					summary:'',/*分享摘要(可选)*/
					title:'',/*分享标题(可选)*/
					site:'',/*分享来源 如：腾讯网(可选)*/
					pics:'', /*分享图片的路径(可选)*/
					style:'203',
					width:22,
					height:22
					};
					var s = [];
					for(var i in p){
					s.push(i + '=' + encodeURIComponent(p[i]||''));
					}
					document.write(['<a version="1.0" class="qzOpenerDiv" href="http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?',s.join('&'),'" target="_blank">分享</a>'].join(''));
				})();
			</script>
			<script src="http://qzonestyle.gtimg.cn/qzone/app/qzlike/qzopensl.js#jsdate=20111201" charset="utf-8"></script>
			<script type="text/javascript">
			    $(function($){
				var ldqq=document.createElement('script');
				ldqq.src="http://connect.qq.com/widget/loader/loader.js";
				ldqq.charset="utf-8";
				ldqq.widget="shareqq";
				document.body.appendChild(ldqq);
			    })
			</script>
		</div>
		<!--分享腾讯微博-->
		<div class="shareFriends" >
			<div id="qqwb_share__" data-appkey="801358475" data-icon="2" data-counter="0" data-content="<?=$share_content?>" data-richcontent="{line1}|{line2}|{line3}" data-pic="{pic}"></div>
		</div>
		
		
		
	</div>
	<!--分享微博JS-->
	<script src="http://mat1.gtimg.com/app/openjs/openjs.js#autoboot=no&debug=no"></script>


	<!--分享微博空间结束-->
	<div class="plist" style="clear:both">
	    <?php include  APPPATH . 'views/rush/rush_cat.php'; ?>
	    <?php include  APPPATH . 'views/rush/list.php'; ?>
	    <?php include  APPPATH . 'views/rush/rush_page.php'; ?>
	</div>
	<div class="cl"></div>  
    </div>
</div>
<script type="text/javascript">
	$(function(){
		$("img[fslazy]").lazyload({
			threshold: 200,
			effect: "fadeIn"
		})
	});
</script>
<?php include APPPATH . 'views/common/footer.php'; ?>