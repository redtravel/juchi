<?php include APPPATH . "views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/login.css'); ?>" type="text/css" />
<link rel="stylesheet" href="<?php print static_style_url('css/plist.css'); ?>" type="text/css" />
<script type="text/javascript" src="<?php print static_style_url('js/product.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/common.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/jcarousel.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/jqzoom.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/lhgdialog/lhgdialog.min.js'); ?>"></script>
<script type="text/javascript">
var product_id = '<?php print $p->product_id; ?>';
var sub_list = <?php print json_encode($sub_list); ?>;
var g_list = <?php print json_encode($g_list); ?>;
var current_color_id = 0;
var current_size_id = 0;

$(function(){
	// 放大镜
	$(".jqzoom").jqueryzoom({
		xzoom:420,
		yzoom:420,
		offset:0,
		position:"right",
		preload:1,
		lens:1
	});
});

</script>
<?php
$share_content = "我在".SITE_NAME."看中了".@addslashes($p->product_name)."，只要".@round($p->product_price, 2)."元,和大家分享哟！一般人我不告诉哒~粑粑麻麻最爱的婴童品牌特卖网站，每天下午2点准时上新";
$share_pic='';
?>
<div id="content">
<!-- 供应商头开始 -->
<div class="mshop_bar">
		<div class="info">
			<p id="ctl00_ctl00_MainContentHolder_MainContentHolderNoForm_minishopBar_minishoplogo" class="thumb b_pink"><a href="/provider-<?=$p->provider_id?>.html"><img src="<?=img_url($p->logo)?>" alt="<?=$p->display_name?>" width="108" height="86"></a></p>
			<a href="/provider-<?=$p->provider_id?>.html"><h2 style="width:260px"><?=$p->display_name?></h2></a>
			<p class="num"><em><?=$p->product_num?></em> 个商品销售中</p>
		</div>
		
		<div class="menu" style="width: 100px;bottom: 40px;">
		    <a href="/provider-<?=$p->provider_id?>.html" class="bt bt20_23 gray"><span style="width: 80px;">所有商品
</span></a>				
		</div>
		
		<div class="shop_cp" id="sell_coupon_layer">
                    <?php foreach ($provider_brand as $k => $brand) : ?>
                    <?php if (!$k) : ?><span style="font-size:14px;">经销品牌：</span><?php endif; ?>
                    <a href="<?php print '/brand-' .$brand['brand_id'] .'.html'; ?>" target="_blank" title="<?php print $brand['brand_name']; ?>">
                        <img src="<?php print img_url($brand['brand_logo']); ?>" alt="<?php print $brand['brand_name']; ?>" width="75px" height="36px" />
                    </a>
                    <?php endforeach; ?>
		</div>		
	</div>

<!-- 供应商头结束 -->

	<div class="now_pos">
		<a href="/">首 页</a>
		<?//=$url_map?>
               &gt; <a href="/brand-<?=$p->brand_id?>.html"><?=$p->brand_name?></a> &gt;
		<a class="now"><?php print $p->product_name ?></a>
		<!-- <a class="notice" href="/">全场满200减20!</a> -->
<!--
                <div class="nav_count" <?php if (empty($p->stop_date) || $p->stop_date == "0000-00-00 00:00:00") { echo "style='display:none;'"; }?>>
			<span>仅剩：</span><b id="timeDay"></b><span>天</span><b id="timeHour"></b><span>小时</span><b id="timeMinu"></b><span>分</span><b id="timeSecond" class="red"></b>秒
		</div>
-->
	</div>
	<script type="text/javascript" src="<?php print static_style_url('js/countDown.js'); ?>"></script>
	<script type="text/javascript">
	countDown({
		startTime:'<?=$p->promote_start_date ?>',
		endTime:'<?=$p->promote_end_date ?>',
		nowTime:'<?php echo date('Y-m-d H:i:s');?>',
		dayElement:'timeDay',
		hourElement:'timeHour',
		minuElement:'timeMinu',
		secElement:'timeSecond',
		callback:function () {
		}
	});
	</script>
	<div class="dtl">
	<!--右上方的商品介绍 begin-->
	<div class="dtl_pro_c">
		<div class="pro_ppt">
		<table>
			<tr>
			<td valign="top">
				<div style="width:420px;height:420px;position:relative;">
				<div id="BigImage" class="jqzoom">
					<img id="_middleImage" width="418" height="418" src="<?php print img_url($g_list[$color_id]["default"]->img_url.".418x418.jpg"); ?>" />
				</div>
				<!--div class="zoomIcon"></div-->
				</div>
			</td>
			</tr>
			<tr>
			<td>
				<ul id="mycarousel" class="jcarousel-skin-tango">
				</ul>
			</td>
			</tr>
			
		</table>

		    <!--分享部分开始-->
		    <script src="http://tjs.sjs.sinajs.cn/open/api/js/wb.js?appkey=708902181" type="text/javascript" charset="utf-8"></script>
			<div id="shareFriend" style="display:none;" >
				<p style="width:auto; float:left; font-size:12px; margin:0; padding:0; height:16px; line-height:16px;">分享给好友：</p>
				<!--分享到微博-->
				<div class="shareFriends" >
					<wb:share-button title="<?=$share_content?>" count="n" appkey="708902181" ></wb:share-button>
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
				
				<!--分享微博JS-->
				<script src="http://mat1.gtimg.com/app/openjs/openjs.js#autoboot=no&debug=no"></script>
			</div>
			<!--分享部分结束-->
		</div>
		<div class="pro_intro">
		<h2 class="p_t"><span><?php print $p->brand_name ?></span></h2>
		<h2 class="p_t" style="overflow:hidden;"><span style="height:50px;"><?php print $p->product_name ?></span></h2>
		<div class="p_list">
			<p class="name_a">品牌名 ：<?php print $p->brand_name ?><?php if ($p->brand_story): ?><?php endif; ?></p>
			<p class="name_b">款号 ：<?php print $p->product_sn; ?></p>
		</div>
		<div class="p_list noBorder">
			<p class="name_b y_p">市场价 ：￥<?php print $p->market_price; ?></p>
		</div>
		<div class="p_pri">
			<span>
			<font>惊喜价 ：</font><font class="pri c_o">￥<?php print $p->product_price; ?></font>
			<font class="black">&nbsp;(<?php print $p->discount_percent; ?>折)</font>
			</span>
			<span>可获积分 ：<?php print $p->product_price; ?> (100 积分=￥1元)</span>
            <span class="p_logistics" style="display:none;">
                <font>物流运费 ：</font>
                <font>送至</font>
                <font class="peisong_address"></font>
                <font class="peisong_type1" id="online_fee"></font>
                <font id="free_shipping_price"></font>
            </span>
            <div id="address_float" style="display: none;">
                <div class="add_floatT">
                    <div class="add_float_name">选择你的收货城市</div>
                    <div class="add_float_closeBtn"></div>
                </div>
                <div class="add_floatDiv">
                    <dl id="dlCoutry">
                        <dt>你当前的所在地：<font class="c66" id="loc_region"></font></dt>
                        <dd><a region_id='2' href='javascript:void(0);'>北京</a></dd>
                        <dd><a region_id='23' href='javascript:void(0);'>天津</a></dd>
                        <dd><a region_id='45' href='javascript:void(0);'>河北</a></dd>
                        <dd><a region_id='244' href='javascript:void(0);'>山西</a></dd>
                        <dd><a region_id='387' href='javascript:void(0);'>内蒙古</a></dd>
                        <dd><a region_id='513' href='javascript:void(0);'>辽宁</a></dd>
                        <dd><a region_id='648' href='javascript:void(0);'>吉林</a></dd>
                        <dd><a region_id='731' href='javascript:void(0);'>黑龙江</a></dd>
                        <dd><a region_id='890' href='javascript:void(0);'>上海</a></dd>
                        <dd><a region_id='913' href='javascript:void(0);'>江苏</a></dd>
                        <dd><a region_id='1052' href='javascript:void(0);'>浙江</a></dd>
                        <dd><a region_id='1165' href='javascript:void(0);'>安徽</a></dd>
                        <dd><a region_id='1306' href='javascript:void(0);'>福建</a></dd>
                        <dd><a region_id='1410' href='javascript:void(0);'>江西</a></dd>
                        <dd><a region_id='1536' href='javascript:void(0);'>山东</a></dd>
                        <dd><a region_id='1716' href='javascript:void(0);'>河南</a></dd>
                        <dd><a region_id='1911' href='javascript:void(0);'>湖北</a></dd>
                        <dd><a region_id='2045' href='javascript:void(0);'>湖南</a></dd>
                        <dd><a region_id='2196' href='javascript:void(0);'>广东</a></dd>
                        <dd><a region_id='2363' href='javascript:void(0);'>广西</a></dd>
                        <dd><a region_id='2501' href='javascript:void(0);'>海南</a></dd>
                        <dd><a region_id='2530' href='javascript:void(0);'>重庆</a></dd>
                        <dd><a region_id='2573' href='javascript:void(0);'>四川</a></dd>
                        <dd><a region_id='2799' href='javascript:void(0);'>贵州</a></dd>
                        <dd><a region_id='2907' href='javascript:void(0);'>云南</a></dd>
                        <dd><a region_id='3069' href='javascript:void(0);'>西藏</a></dd>
                        <dd><a region_id='3157' href='javascript:void(0);'>陕西</a></dd>
                        <dd><a region_id='3285' href='javascript:void(0);'>甘肃</a></dd>
                        <dd><a region_id='3399' href='javascript:void(0);'>青海</a></dd>
                        <dd><a region_id='3459' href='javascript:void(0);'>宁夏</a></dd>
                        <dd><a region_id='3492' href='javascript:void(0);'>新疆</a></dd>
                    </dl>
                </div>
            </div>
            <script type="text/javascript">
                var provider_shipping_fee_config = <?php print json_encode($provider_shipping_fee_config);?>;
            </script>
            <script type="text/javascript" src="<?php print static_style_url('js/addressFloat.js?v=20140113'); ?>"></script>
		</div>
		
		<div class="y_block">
			<input type="hidden" name="sub_id" value="" />
			<dl>
			<dt>颜色 ：</dt>
            <?php if(TRUE || $p->is_onsale):?> 
			<dd class="color">
				<?php foreach ($sub_list as $c_id => $color): ?>
					<span onclick="click_color(<?php print $c_id; ?>)" id="color_<?php print $c_id ?>">
					<?php print $color['color_name']; ?>
					<s></s>
					</span>
				<?php endforeach; ?>
			</dd>
            <?php else:?>
            <span>所有颜色已售空</span>
            <?php endif;?>
			</dl>
			<dl>
			<dt>尺码 ：</dt>
            <?php if(TRUE || $p->is_onsale):?>
			<dd class="size">
			</dd>
			    <?php if (isset($p->size_image)&& !empty($p->size_image) ): ?>
				<dd style="width:115px;">
					<a href="javascript:void(0)" class="refer_size" id="refer_size" style="" display="1">查看尺码表</a>
				</dd>
                <?php endif;?>
             <?php else:?>
                <span>所有尺码已售空</span>
             <?php endif;?>
			</dl>
			<dl>
			<dt>数量 ：</dt>
             <?php if (TRUE || $p->is_onsale):?>
			<dd class="number">
				<a class="down">-</a>
				<input type="text" name="num" id="num" value="1" disabled="disabled">
				<a class="up">+</a>&nbsp;
			</dd>
            <?php endif;?>
            <?php if($p->limit_day && $p->limit_num):?>
            <dd style='color:red;'>该商品<?php print $p->limit_day == 1 ? '1天' : '1月' ?>内限购<?php print $p->limit_num; ?>件</dd>
            <?php endif;?>
			</dl>
			<p>
			<span class="add_car" <?php if (TRUE || $p->is_onsale):?> onclick="add_to_cart_dapter(0)" <?php endif;?>>放入购物袋</span>
			<a class="add_fav l" style="cursor:pointer;" onclick="add_to_collect(<?php print $p->product_id; ?>,0);">加入收藏夹</a>
			</p>
<!--			<p class="topNotice l">
			<span>发货时间：<?= $p->expected_shipping_date ?>前下单并完成支付于当日发货。</span>
			</p>-->	
		</div>
        <!--<p class="mianyun"></p>-->
		</div>
	</div>
	<!--右上方的商品介绍 begin-->
	
	<!--右下方table：描述，点评，咨询，品牌故事 begin -->
	<div class="pro_main_infor">
		<div class="pro_main_t">
		<ul>
			<li rel="1" class="sel">商品描述</li>
			<li rel="2">商品点评</li>
			<li rel="3">商品咨询</li>
			<!--<li rel="4">品牌故事</li>-->
		</ul>
		</div>
		<!--商品描述-->
		<div class="pro_main_block" id="product_tab_1">
		<?php if (isset($gifts_list)): ?>
			<!--优惠信息-->
			<div id="youhui" class="youhui">
				<div class="c99" style="text-align:right">(优惠方式在购物袋中选择)</div>
				<div class="youhui_top">
				<p class="youhui_table">优惠信息：</p>
				</div>
				<ul class="youhui_m">
				<?php 
				$index = 1;
				foreach ($gifts_list as $gifts) { ?>
				<li><?php print $index++."."; print $gifts['campaign_name']; ?></li><?php }
				if ($index > 3 ){
				?>
					<li style="height:0"><a class="more"></a></li>
				<?php
				}
				?>
				</ul>
			</div>
		<?php endif; ?>
                <?php if ($p->provider_cooperation != 3) : ?>
		<!--产品属性-->
		<div class="pro_main_block_b">
			<h3 class="b">产品属性</h3>
			<div class="pro_main_block_c">
			<table class="desc_table">
				<?php
				foreach ($p->product_desc_additional as $val ) { if (empty($val['desc']) || $val['desc']== '暂无' ) continue;
				?>
					<tr>
					<td class="tdleft"><?=$val['name'] ?></td>
					<td class="tdright"><?=$val['desc'] ?></td>
					</tr>
				<?php
				}
				?>
			</table>
			</div>
		</div>
                <?php endif; ?>
		<!--产品描述-->
		<div class="pro_main_block_b">
                <?php if ($p->provider_cooperation != 3) : ?>
			<h3 class="b">产品描述</h3>
                <?php endif; ?>
			<div class="pro_main_block_c" style="border-bottom:1px dotted #d9d9d9">
			<?php print $p->product_desc; ?>
			</div>
		</div>
		<!--尺码图-->
		<? if(isset($p->size_image) && !empty($p->size_image) ) { ?>
		<div class="pro_main_block_b">
			<div class="attr_title">
			<div class="goods_size"></div>
			<span><?php print $p->brand_name ?></span>
			</div>
			<div class="pro_main_block_c">
				<img src="<?php print img_url($p->size_image); ?>">
			</div>
		</div>
		<? }?>
                <?php if ($p->provider_cooperation != 3) : ?>
		<!--平铺图-->
		<div class="pro_main_block_b">
			<div class="attr_title">
			<div class="tile_show"></div>
			<span><?php print $p->brand_name ?></span>
			</div>
			<div class="pro_main_block_c pro_main_block_c_img" id="product_img_pingpu">
			<?php   
                        
			foreach ($g_list as $key => $val) {
			    foreach ($val as $key1 => $val1) {
				if($key1 != "part" && $key1 != "tonal" ){
				    if (!empty($val1->img_url)) {
					?>
					<img src="<?php print img_url( $val1->img_url.".850x850.jpg");?>" width="850" height="850">
					<?php
				    }
				 }
				foreach ($val1 as $key2 => $val2) {
				    if (!empty($val2->img_url)) {
					?>
					<img src="<?php print img_url( $val2->img_url.".850x850.jpg");?>" width="850" height="850">
					<?php
				    }
				}
			    }
			}
			?>
			</div>
		</div>
                <?php endif; ?>
		<!--细节图-->
		<? if(isset($p->product_desc_detail) && !empty($p->product_desc_detail) ){?>
		<div class="pro_main_block_b">
			<div class="attr_title">
			<div class="show_details"></div>
			<span><?php print $p->brand_name ?></span>
			</div>
			<div class="pro_main_block_c pro_main_block_c_img">
			<?php print $p->product_desc_detail;?>
			</div>
		</div>
		<?}?>
		<div class="pointOut" style="display:none;">
			<p>1. 关于色差: 妈咪树的商品全部采用专业影棚拍摄，已尽量减少色差，由于每个人的显示器不同，仍然存在一部分色差，希望各位会员能够理解。</p>
			<p>2. 关于商品: 妈咪树商品的市场价均采集自商场门店专柜价，品牌官网标价或由品牌供应商提供。由于地区差异性或时间差异性可能导致妈咪树供您参考的市场价与您购物时的市场价不一致，温馨提醒您，妈咪树标注的市场价仅供您参考，不作为购物依据，请您知悉。</p>
		</div>
		</div>
		<!--商品点评-->
		<div class="pro_main_block" id="product_tab_2" style="display:none">
		<div class="comment_rule">
			<p>
			1. 每次点评可获得<strong class="red">20</strong>积分，会员等级越高，获得的积分越多，详情请见<a href="/help-4.html" target="_blank">积分规则</a>；<br>
			2. 只有购买过该商品的用户才能点评；<br>
			3. 评论请查看妈咪树<a href="/help-4.html" target="_blank">点评规则</a>；
			</p>
			<a href="javascript:void(0)" class="getfen" title="点评拿积分" id="pinglun_show_box_btn"  onclick="load_dianping_panel('<?php print $p->product_id; ?>');">点评拿积分</a>
		</div>
		<div id="dianping_div" class="myAdeMsgBox" style="background-image:none">
		</div>
		</div>
		<!--商品咨询-->
		<div class="pro_main_block" id="product_tab_3" style="display:none">
		<div class="qa_form_area">
			<form action="" name="zixun_form" id="zixun_form" method="post">
			<?php if(empty($user_name) || empty($rank_name ) ){
				?>
			<div class="logininfo" id="zixun_user_nologin_block" display="block">
				<dl>
				<dt>用户名</dt>
				<dd><input type="text" name="zixun_login_user" id="zixun_login_user"></dd>
				</dl>
				<dl>
				<dt>密码</dt>
				<dd><input type="password" name="zixun_login_pwd" id="zixun_login_pwd"></dd>
				</dl>
				<dl id="zixun_login_code_block" style=" display:none"></dl>
				<input type="button" name="login_btn" class="login_btn" id="zixun_login_btn" title="登录" value="登录" onclick="return pro_login();">
				<a href="javascript:show_zixun_form()" class="nologin_tip" title="不登录也可咨询" id="zixun_user_nologin_link">不登录也可咨询</a>
			</div>
				<?php
			}else{
				?>
			<div class="logininfo" style="display:block" id="zixun_user_logined_block">
					<strong id="zixun_user"><?= $user_name ?></strong>
					<span id="zixun_user_rank"><?= $rank_name ?></span>
				</div>
				<?php
			} ?>
			<div class="logininfo" style="display:none;" id="zixun_user_guest_block">
				<strong id="zixun_user_yk">游客</strong>
				<a href="javascript:show_login_form()" class="nologin_tip" title="登录咨询" id="zixun_user_login_link">登录咨询</a>
			</div>
			
			<textarea name="zixun_content" id="zixun_content" cols="30" rows="10" class="qa_textarea"></textarea>
			<input type="button" class="submit_search" value="我要咨询" title="我要咨询" id="zixun_btn" onclick="return submit_zixun();"  >
			<span id="zixun_tip" name="zixun_tip"></span>
			</form>
		</div>
		<div id="zixun_div">
		</div>
		</div>
		<!--品牌故事-->
		<div class="pro_main_block" id="product_tab_4" style="display:none"></div>
	</div>
	<!--	    右下方table：描述，点评，咨询，品牌故事 end -->
	<!--看过还看过，买过还买过 begin-->
	<div class="dtl_left">
            <div class="w200_block ablack" id="last_hotsales" style="display:block;"></div>
		<!--
                <div class="w200_block ablack" id="buy_buy" style="display:block;"></div>
		<div class="w200_block ablack" id="link_product" style="display:block;"></div>
		<div class="w200_block ablack" id="product_history" style="display:none;"></div>
                 -->
		<!-- 广告位 begin -->
		<?php
		foreach ($left_ad as $key => $val) {
		?>
			<div class="adv_block">
		<a href="<?=$val->ad_link?>" target="_blank" >
			<img src="<?php print img_url($val->pic_url ); ?>" width="200" height="280" />
		</a>
			</div>
		<?php
		}
		?>
		<!-- 广告位 end -->
	</div>
	<!--看过还看过，买过还买过 end-->
        
	<!--放大镜浮层 begin -->
	<div id="zoom_float_block" style="display:none;">
		<div class="zoom_float_block_t">
		<div class="closeBtn"></div>
		<?=$p->brand_name;?>&nbsp;&nbsp;&nbsp;&nbsp;
		<?=$p->product_name;?>
		</div>
		<div class="zoom_float_block_l">
		    <a href="javascript:void(0)"><img src="<?php print img_url( $g_list[$color_id]["default"]->img_url).".85x85.jpg"; ?>" width="85" height="85"></a>
		<?php
		foreach ($g_list[$color_id]["part"] as $key => $val) {
		if (!empty($val->img_url )){
			?>
			<a href="javascript:void(0)"><img src="<?php print img_url( $val->img_url.".85x85.jpg"); ?>" width="85" height="85"></a>
		<?php
		}
		}
		?>
		</div>
		<div class="zoom_float_block_r">
		    <img src="<?php print img_url($g_list[$color_id]["default"]->img_url.".850x850.jpg" ) ;?>" width="850" height="850" style="display:none">
		<?php
		foreach ($g_list[$color_id]["part"] as $key => $val) {
			if (!empty($val->img_url)) {
			?>
			<img src="<?php print img_url($val->img_url.".850x850.jpg" ) ;?>" width="850" height="850" style="display:none">
			<?php
			}
		}
		?>
		</div>
	</div>
	<!--放大镜浮层 end -->
	<div id="float_panel" style="display:none;"></div>
	
	</div>
</div>
<!--提示登录 begin -->
<script type="text/javascript" src="<?php print static_style_url('js/login.js'); ?>"></script>
<div id="login_alert" style="display:none;">
	<div class="registerLogin">
		<div class="loginMain">
			<div class="login l" style="height:280px;">
				<h2>
					<s class="s_l"></s>
					用户登录
					<s class="sr"></s>
				</h2>
				<div class="login_c">
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td width="30%" height="45" align="center" valign="top" class="f14">
								<div class="tdTitle">用户账号：</div>
							</td>
							<td width="70%">
								<input type="text" name="user_name" class="t_w217_c99 gray" onkeydown="javascript:KeyDown();" onFocus="javascript:login_focus(this)" onBlur="javascript:login_blur(this);" value="Email地址或手机号码" />
								<div class="ts_block_box" style="height:55px;">
									<div id="l_email_error" class="ts_block" style="display:none;">帐号只能由字母数字及下划线组成</div>
								</div>
							</td>
						</tr>
						<tr>
							<td height="30" align="center" valign="top" class="f14">
								<div class="tdTitle">用户密码：</div>
							</td>
							<td valign="top">
                <div style="position:relative;">
                  <input type="text" id="passwordText2" class="t_w217_c99 gray" maxlength="16" value="6-16位字母或者数字" />
  								<input type="password" name="password" onkeydown="javascript:KeyDown();" class="t_w217_c99" />
                </div>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<div class="no_wr">
									<label for="rememberMe">
										<input name="remember" type="checkbox" value="1" id="rememberMe" />
										记住我的登录状态
									</label>
									<a href="#" onclick="get_password();return false;" class="fgreen"><strong>忘记密码?</strong></a>
								</div>
								<div class="wr_tip" id="message"><span id="message_inner"></span></div>
							</td>
						</tr>
						<tr>
							<td height="40">&nbsp;</td>
							<td>
								<input type="button" name="submitlogin" onclick="check_login_form();" class="btn_login" value="登录" />
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="otherLogin">
				<h3 class="other_way">使用其他方式登录妈咪树</h3>
				<ul class="union_login">
					<li><a href="/user/xinlang_login" title="新浪微博" class="sina_login"></a></li>
					<li><a href="/user/alipay_login" title="支付宝" class="alipay_login"></a></li>
					<li><a href="#" onclick="toQzoneLogin();return false;" title="QQ登录" class="qq_login"></a></li>
				</ul>
			</div>
		</div>
			
		<div class="registerMain">
			<div class="login l" style="height:390px;">
				<h2>
					<s class="s_l"></s>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;10秒快速注册
					<s class="sr"></s>
				</h2>
				<div class="reg_c">
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td width="30%" height="45" align="center" valign="top" class="f14">
								<div class="tdTitle">注册账号：</div>
							</td>
							<td width="70%" valign="top">
								<input type="text" class="t_w217_c99 gray" name="r_email" id="r_email" onFocus="javascript:register_focus(this);_onFocus(this);" onBlur="javascript:register_blur(this);_onBlur(this);" onkeyup="javascript:referToUsername(this);" value="Email地址或手机号码" maxlength="50" />
								<span id="r_email_success" class="exactness" style="display:none;"></span>
								<div class="ts_block_box">
									<div id="r_email_error" class="ts_block" style="display:none;">帐号只能由字母数字及下划线组成</div>
								</div>
							</td>
						</tr>
						<tr>
							<td height="45" align="center" valign="top" class="f14">
								<div class="tdTitle">设置密码：</div>
							</td>
							<td valign="top">
								<div style="position:relative;">
									<input type="text" id="passwordText" class="t_w217_c99 gray" maxlength="16" value="6-16位字母或者数字" />
									<input type="password" name="r_password" id="r_password" onFocus="javascript:register_focus(this);_onFocus(this);" onBlur="javascript:register_blur(this);_onBlur(this);" class="t_w217_c99 black" maxlength="16" value="" />
								</div>
								<span id="r_password_success" class="exactness" style="display:none;"></span>
							</td>
						</tr>
						<tr>
							<td height="45" valign="top" class="f14">
								<div class="tdTitle">重复密码：</div></td>
							<td valign="top">
								<input type="password" name="r_cpassword" id="r_cpassword" onFocus="javascript:register_focus(this);_onFocus(this);" onBlur="javascript:register_blur(this);_onBlur(this);" class="t_w217_c99" maxlength="16" />
								<span id="r_cpassword_success" class="exactness" style="display:none;"></span>
							</td>
						</tr>
						<tr>
							<td height="30" valign="top" class="f14">
								<div class="tdTitle">验证码：</div>
							</td>
							<td valign="top">
								<input type="text" class="t_w90_c99" name="r_captcha" id="r_captcha" onFocus="javascript:register_focus(this);_onFocus(this);" onBlur="javascript:register_blur(this);_onBlur(this);" onkeyup="javascript:referToUsername(this);" value="" maxlength="50" />
								<img id="verify_code" src="/user/show_verify/" style="cursor:hand;" onclick="this.src=img_src + Math.random();" alt="点击更换图片" />
								<span id="r_captcha_success" class="exactness" style="display:none;"></span>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<div class="no_wr">
									<label for="tongyi">
										<input name="agreement" type="checkbox" value = "1" checked="checked" id="tongyi" />
										同意服务条款
									</label>
									(<a href="article-42.html" target="_blank">查看详细服务条款</a>)</div>
								<div id="r_message" class="wr_tip"><span id="r_message_inner"></span></div>
							</td>
						</tr>
						<tr>
							<td height="48">&nbsp;</td>
							<td valign="middle">
								<div type="button" name="submitregister" onclick="check_register_form();" class="btn_reg" value="注册">注册并登录</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
			
	</div>
	<!-- <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tan_c">
		<tr>
			<td height="15" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td align="center" id="login_msg"><img src="<?php print static_style_url('img/common/t_png.png') ?>" width="18" height="16" align="absmiddle"/>&nbsp;<b>进行操作前您需要先登录！</b></td>
		</tr>
		<tr>
			<td align="center" height="50" valign="bottom"><a href="javascript:void(0)" onclick="lhgDG.cancel();" class="t_kg">过会再说</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="user/login" class="t_ks">立即登录</a></td>
		</tr>
	</table> -->
</div>
<!--提示登录 end -->
<div class="cl"></div>
<!--添加购物车-->
<div id="add_to_cart_msg" style="display:none;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tan_c">
		<tr>
			<td height="20" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td height="25" colspan="2" align="center" class="font14b"><img src="<?php print static_style_url('img/common/t_png_red.gif') ?>" width="36" height="36" align="absmiddle"/>&nbsp;&nbsp;&nbsp;&nbsp;商品已成功添加到购物袋！</td>
		</tr>
		<tr>
			<td colspan="2" height="70" align="center">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="cart" class="btn_r_82">去结算</a>&nbsp;&nbsp;
				<a href="javascript:void(0)" onclick="lhgDG.cancel();" class="btn_g_78">再逛逛</a>
			</td>
		</tr>
		<!--
		<tr>
			<td height="20" colspan="2" align="center" valign="bottom" class="c66">温馨提示：购物袋内商品只能保留20分钟，请尽快支付订单。</td>
		</tr>
		-->
	</table>
</div>

<!--添加收藏-->
<div id="add_to_collect_msg" style="display:none;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tan_c">
		<tr><td height="40" colspan="2">&nbsp;</td></tr>
		<tr>
			<td align="center" class="font14b"><img src="<?php print static_style_url('img/common/t_png.gif') ?>" width="36" height="36" align="absmiddle"/>&nbsp;&nbsp;&nbsp;&nbsp;商品已成功添加到收藏夹！</td>
		</tr>
		<tr><td height="56" colspan="2">&nbsp;</td></tr>
		<tr>
			<td align="right" height="20" valign="bottom">
				<a href="user/collection" class="und black">查看收藏夹>></a>
			</td>
		</tr>
	</table>
</div>
<?php include APPPATH . 'views/common/footer.php'; ?>
<script type="text/javascript">
<?php
//if ($g = $g_list[$color_id])
//    print "load_history('{$p->product_id}','{$p->product_name}','{$g['default']->img_170_170 }','{$p->market_price}','{$p->product_price}','{$p->brand_name}');";
?>
	click_color(<?php print $color_id;?>);
	//buy_buy(<?php print $p->product_id; ?>);
	//link_product(<?php print $p->product_id; ?>);
        last_hotsales();
	load_product_liuyan(1,0);
	load_product_liuyan(2,0);
	load_pro_brand_story(<?=$p->brand_id;?>);
	
</script>
