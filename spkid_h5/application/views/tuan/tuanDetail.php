<?php include APPPATH . "views/common/tuanheader.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/tuan.css'); ?>" type="text/css" />
<link rel="stylesheet" href="<?php print static_style_url('css/default.css'); ?>" type="text/css" />
<link rel="stylesheet" href="<?php print static_style_url('css/login.css'); ?>" type="text/css" />
<script type="text/javascript" src="<?php print static_style_url('js/product.js'); ?>?t=0.3"></script>
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
//NTKF参数设置
NTKF_PARAM.itemid='<?php print $p->product_id; ?>';
$(function(){
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
<!--团购内容-->
<div id="tuanBtnMoveBox">
    <div id="tuanBtnMove">
        <ul>
            <li id="tLiPrice"><h3><?php print number_format($tuaninfo->tuan_price, 2, '.', '') ?></h3></li>
            <li id="libtnBuyNow1"><a href="#tuanInfoMsg" <?if ($p->is_onsale) { ?> class="btnBuyNow1" <? } else { ?> class="btnBuyNow1Gray" <? } ?> > </a></li>
            <li class="moveLiNormal"><p>市场价</p><s><?php print intval($p->market_price) ?>元</s></li>
            <li class="moveLiNormal"><p>折扣</p><s><?php print $tuaninfo->product_discount ?>折</s></li>
            <li class="moveLiNormal"><p>节省</p><s><?php print $p->market_price-$tuaninfo->tuan_price ?>元</s></li>
            <li id="moveLiTotal"><span><?php print $tuaninfo->buy_num ?>人</span><h4>已购买</h4></li>
        </ul>
    </div>
</div>
<div class="tuanCont">
	<!--左边部分-->
	<div id="tuanFloatL">
		<!--产品显示购买-->
		<div  id="tuanFloatLTop">
			<p id="tuanInfoMsg"><?php print $tuaninfo->tuan_name ?></p>
			<div id="tuanBroadBox">
				<!--价格牌box-->
				<div id="tuanBroadLeft">
					<h3><?php print number_format($tuaninfo->tuan_price, 2, '.', '') ?></h3>
					<a href="javascript: void(0)" target="_self" title="立即购买" <?if ($p->is_onsale) { ?> id="btnBuyNow0" <? } else { ?> class="btnBuyNow0Gray" <? } ?> ></a>
					<ul id="priceOff">
						<li class="moveLiNormal"><p>市场价</p><s><?php print intval($p->market_price) ?>元</s></li>
						<li class="moveLiNormal"><p>折扣</p><s><?php print $tuaninfo->product_discount ?>折</s></li>
						<li class="moveLiNormal"><p>节省</p><s><?php print $p->market_price-$tuaninfo->tuan_price ?>元</s></li>
					</ul>
					<p id="tuanComeDown">
						<b id="timeDay"></b>天<b id="timeHour"></b>时<b id="timeMinu"></b>分<b id="timeSecond"></b>秒
                        <script type="text/javascript" src="<?php print static_style_url('js/countDown.js'); ?>"></script>
                    	<script type="text/javascript">
                    	countDown({
                    		startTime:'<?php echo $tuaninfo->tuan_online_time; ?>',
                    		endTime:'<?php echo $tuaninfo->tuan_offline_time; ?>',
                    		nowTime:'<?php echo date('Y-m-d H:i:s'); ?>',
                    		dayElement:'timeDay',
                    		hourElement:'timeHour',
                    		minuElement:'timeMinu',
                    		secElement:'timeSecond',
                    		callback:function () {
                    		}
                    	});
                    	</script>
					</p>
					<div class="tuanCountNum">
						<span><?php print $tuaninfo->buy_num ?>人</span><h4>已购买</h4>
						<p>数量有限，下手要快哦！</p>
					</div>
				</div>
				<!--产品图box-->
				<div id="tuanBroadRight">
					<div id="tuanBroadRImgBox">
						<div id="tuanShadowBox" style="display:none;">
							<h4 id="tuanShadowTitle">请选择您想购买的商品尺寸和颜色<a href="javascript: void(0)" id="closeShadowBox" title="关闭弹层"></a></h4>
								<div class="y_block">
                        			<input type="hidden" name="sub_id" value="" />
                        			<dl>
                        			<dt>颜色 ：</dt>
                                                <?if (TRUE || $p->is_onsale) { ?>
                        			<dd class="color">
                        				<?php foreach ($sub_list as $c_id => $color): ?>
                        					<span onclick="click_color(<?php print $c_id; ?>)" id="color_<?php print $c_id ?>">
                        					<?php print $color['color_name']; ?>
                        					<s></s>
                        					</span>
                        				<?php endforeach; ?>
                        			</dd>
                                                <?php } else {?>
                                                <span>
                                                    所有颜色已售空
                        			</span>
                                                <?php } ?>
                        			</dl>
                        			<dl>
                        			<dt>尺码 ：</dt>
                                    <?if (TRUE || $p->is_onsale) { ?>
                        			<dd class="size">
                        			</dd>
                        			    <?if (isset($p->size_image)&& !empty($p->size_image) ) { ?>
                        				<dd style="width:115px;">
                        					<a href="javascript:void(0)" class="refer_size" id="refer_size" style="display: none;" display="1">查看尺码表</a>
                        				</dd>
                                        <?php } 
                                        } else {?>
                                        <span>所有尺码已售空</span>
                                        <?php } ?>
                        			</dl>
                        			<dl>
                        			<dt>数量 ：</dt>
                                                <?if (TRUE || $p->is_onsale) { ?>
                        			<dd class="number">
                        				<a class="down">-</a>
                        				<input type="text" name="num" id="num" value="1" disabled="disabled">
                        				<a class="up">+</a>&nbsp;
                        			</dd>
                                                <?php } ?>
                        			</dl>
                        			<p id="btnBuyNow3" <?if (!$is_preview && $p->is_onsale) { ?> onclick="add_to_cart_dapter_tuan(0)" <?php } ?>>
                                    </p> 
                        		</div>
						</div>
						<img src="<?php print img_url($tuaninfo->img_500_450 ); ?>" width="500px" height="450px" alt="" />
					</div>
					<div id="tuanBroadRIcon">
                        <a id="tuanIconFav" style="cursor:pointer;" onclick="add_to_collect(<?php print $p->product_id; ?>,0);">我收藏</a>
						
						<!--分享到**开始-->
						    <script src="http://tjs.sjs.sinajs.cn/open/api/js/wb.js?appkey=708902181" type="text/javascript" charset="utf-8"></script>
						    <div id="shareFriend" style="height:25px; float:right;">  
						        <p style="width:auto; float:left; font-size:12px; margin:0; padding:0; height:16px; line-height:16px;">|&nbsp;&nbsp;&nbsp;&nbsp;分享到：</p>
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
						        
						        
						    </div>
						    <!--分享微博JS-->
						    <script src="http://mat1.gtimg.com/app/openjs/openjs.js#autoboot=no&debug=no"></script>
						    <!--分享到**结束-->
					</div>
				</div>
			</div>
			<div id="tuanBuyNote">
				<?php print $tuaninfo->tuan_desc ?>
			</div>
			
		</div>
		<!--编辑器-->
		<div class="htmlEditorBox"><?php print $tuaninfo->userdefine1 ?></div>
		<!--商品评论信息故事-->
		<div id="tuanTabBox">
			<div id="tuanTabTop">
				<ul id="ulTuanTab">
					<li class="onTabShow">商品信息</li>
					<li>商品评论</li>
					<li>品牌故事</li>
				</ul>
			</div>
			<div id="tuanTabCon">
				<!--商品详情-->
				<div id="tuanTabCon1" class="pro_main_block" style="display: block;">
					<?php if ($gifts_list): ?>
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
            		
            		<!--产品属性-->
            		<div class="pro_main_block_b">
            			<h3 class="b">产品属性</h3>
            			<div class="pro_main_block_c">
            			<table class="desc_table">
            				<?php
            				foreach ($p->product_desc_additional as $val ) { if (empty($val['desc']) ) continue;
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
                    <!--产品描述-->
                    <div class="pro_main_block_b">
                        <h3 class="b">产品描述</h3>
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
                    <div class="htmlEditorBox"><?php print $tuaninfo->userdefine2 ?></div>
            		<!--平铺图-->
            		<div class="pro_main_block_b">
            			<div class="attr_title">
            			<div class="tile_show"></div>
            			<span><?php print $p->brand_name ?></span>
            			</div>
            			<div class="pro_main_block_c pro_main_block_c_img">
            			<?php
            			foreach ($g_list as $key => $val) {
            			    foreach ($val as $key1 => $val1) {
            				if($key1 != "part" && $key1 != "tonal" ){
            				    if (!empty($val1->img_760_760)) {
            					?>
            					<img src="<?php print img_url( $val1->img_760_760);?>" width="760" height="760">
            					<?php
            				    }
            				 }
            				foreach ($val1 as $key2 => $val2) {
            				    if (!empty($val2->img_760_760)) {
            					?>
            					<img src="<?php print img_url( $val2->img_760_760);?>" width="760" height="760">
            					<?php
            				    }
            				}
            			    }
            			}
            			?>
            			</div>
            		</div>
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
            		<div class="pointOut">
            			<p>1. 关于色差: <?php print SITE_NAME;?>的商品全部采用专业影棚拍摄，已尽量减少色差，由于每个人的显示器不同，仍然存在一部分色差，希望各位会员能够理解。</p>
            			<p>2. 关于商品: <?php print SITE_NAME;?>商品的市场价均采集自商场门店专柜价，品牌官网标价或由品牌供应商提供。由于地区差异性或时间差异性可能导致<?php print SITE_NAME;?>供您参考的市场价与您购物时的市场价不一致，温馨提醒您，<?php print SITE_NAME;?>标注的市场价仅供您参考，不作为购物依据，请您知悉。</p>
            		</div>
				</div>
				<!--商品评论-->
				<div id="tuanTabCon2" class="pro_main_block" style="display:none;">
                    <div class="comment_rule">
            			<p>
            			1. 每次点评可获得<strong class="red">20</strong>积分；<br>
            			2. 只有购买过该商品的用户才能点评；<br>
            			3. 第一个点评的用户，将获得<strong class="red">40</strong>积分，详情请见<a href="/help-4.html" target="_blank">积分规则</a>；<br>
            			4. 评论请查看<?php print SITE_NAME;?><a href="/help-4.html" target="_blank">点评规则</a>；
            			</p>
            			<a href="javascript:void(0)" class="getfen" title="点评拿积分" id="pinglun_show_box_btn"  onclick="load_dianping_panel('<?php print $p->product_id; ?>');"></a>
            		</div>
            		<div id="dianping_div" class="myAdeMsgBox" style="background-image:none">
            		</div>
				</div>
				<!--品牌故事-->
				<div class="pro_main_block" id="tuanTabCon3" style="display:none"></div>
			</div>
		</div>
		<!--编辑器-->
		<div class="htmlEditorBox"><?php print $bottom_ad[0]->ad_code ?></div>
	</div>
	<!--右边部分-->
	<div id="tuanFloatR">
		<div class="adTraceBox"><?php print $tuaninfo->userdefine4 ?></div>

		<div id="tuanListRightBox">
			<h3>您可能感兴趣的团购</h3>
			<ul id="tuanUlList">
                	<?php
            		foreach ($right_ad as $key => $val) {
            		?>
            			<li>
        					<a class="tuanLiTitle main_black" title="" target='_blank' href="tuanDetail-<?php print $val->tuan_id ?>.html"><?php print $val->tuan_name ?></a>
        					<a href="tuanDetail-<?php print $val->tuan_id ?>.html" target='_blank' title=""><img src="<?php print img_url($val->img_168_110 ); ?>" width="168px" height="110px" alt="" /></a>
        					<div class="tuanBroadBuyBox">
        						<h5><span>￥</span><?php print number_format($val->tuan_price, 2, '.', '') ?></h5>
        						<s>￥<?php print number_format($val->market_price, 2, '.', '') ?></s>
        						<a href="tuanDetail-<?php print $val->tuan_id ?>.html" class="btnBuyNow2" target="_blank" title="立即购买"></a>
        						<p><?php print $val->buy_num ?><b>人已团购</b></p>
        					</div>
        				</li>
            		<?php
            		}
            		?>
			</ul>
            <? foreach($rights_ad as $val) { ?>
            <div class="ad2GoodBox"><img src="<?= img_url($val->pic_url)?>" alt=""/></div>   
            <? } ?>
		</div>

	</div>
	<!--右边结束-->
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
				<h3 class="other_way">使用其他方式登录<?php print SITE_NAME;?></h3>
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
<?php include APPPATH . 'views/common/tuanfooter.php'; ?>
<script type="text/javascript" src="<?=static_style_url("js/tuanDetail.js")?>"></script>
<script type="text/javascript">
click_color(<?php print $color_id;?>);
load_pro_brand_story_tuan(<?=$p->brand_id;?>);
</script>
