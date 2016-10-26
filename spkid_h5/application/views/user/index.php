<?php include APPPATH."views/common/header.php"; ?>
<script type="text/javascript" src="<?php print static_style_url('js/jcarousellite.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/lhgdialog/lhgdialog.min.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/user.js'); ?>"></script>
<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />

<script type="text/javascript" >function load_ad() {}</script>

<div id="removeOrder" style="display:none;" >
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tan_c">
		<tr>
			<td height="30" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td height="25" colspan="2" align="center" valign="middle">
				<img src="<?php print static_style_url('img/common/t_png.gif') ?>" width="38" height="38" align="absmiddle"/>
				<span class="info">成功取消订阅！</span>
			</td>
		</tr>
		<tr>
			<td height="30" colspan="2" align="center" valign="bottom" class="c66">温馨提示 : 如果需要重新订阅请在订阅处留下手机或邮箱。</td>
		</tr>
	</table>
</div>


<div id="content">
	<div class="ucenter_top"></div>
	<div class="ucenter_left">
		<?php include APPPATH."views/user/left.php"; ?>
	</div>
	<div class="ucenter_main">
		<div class="ucenter_mid">
			<?php if ((isset($user->no_profile) && $user->no_profile == 1) || ($user->email_validated == 0 && $user->mobile_checked == 0)): ?>
			<div class="userWelcome">
				您好！
				[<font><?php echo $user->short_user_name ?></font>]&nbsp;
				<a href="/user/profile">[修改]</a>
			</div>
			<div class="tip">
				您目前的会员级别为：
                                <a class="f<?=$user->rank_id?>"><?php echo $user->rank_name ?></a>
        <?php if ($user->rank_id <= 4): ?>
				,再消费<font class="red"><?=$user->next_min_points?>元</font>即可成为<?=$user->next_min_rank_name?>会员&nbsp;&nbsp;
       <?php endif; ?> 
<!--                                <?php if ($user->paid_money == 0) { ?>
				,再消费<font class="red">1元</font>即可成为银卡会员&nbsp;&nbsp;
                                <?php } else if($user->paid_money > 1 && $user->paid_money < 1500) {?>
				,再消费<font class="red"><?= 1500 - $user->paid_money ?>元</font>即可成为金卡会员&nbsp;&nbsp;
                                <?php } else if($user->paid_money > 1500 && $user->paid_money < 5000) {?>
				,再消费<font class="red"><?= 5000 - $user->paid_money ?>元</font>即可成为钻石会员&nbsp;&nbsp;
                                <?php } ?>-->
				<div class="mid_block_info und"><a href="/help-5.html">会员等级说明</a></div>
			</div>
			<?php endif; ?>
			<div class="mid_block">
				<dl>
					<dt>
						<div class="mid_block_title bold">订单提醒：</div>
						<div class="mid_block_info w120"><a class="blackred" href="/user/order">待支付订单(<?= isset($order["wait_pay_num"]) ? $order["wait_pay_num"] : "0"?>)</a></div>
						<div class="mid_block_info w120"><a class="blackred" href="/user/order">进行中的订单(<?= isset($order["ing_num"]) ? $order["ing_num"] : "0"?>)</a></div>
					</dt>
					<dd>
						<div class="mid_block_title font12">帐户余额：</div>
						<div class="mid_block_info"><font class="red und"><?php echo $user->user_money ?></font>&nbsp;元</div>
						<div class="mid_block_info und"><a href="/user/account">要充值</a></div>
						<div class="mid_block_title font12 mL75">总消费额：</div>
						<div class="mid_block_info w200"><font class="red und"><?php print $user->paid_money?$user->paid_money:'0.00';?></font>&nbsp;元</div>
					</dd>
					<dd>
						<div class="mid_block_title font12">可用现金券：</div>
						<div class="mid_block_info"><font class="red und"><?php echo $user->voucher_num ?></font>&nbsp;张</div>
						<div class="mid_block_info und"><a href="/help-11.html">如何使用现金券</a></div>
						<div class="mid_block_title font12 mL75">账户积分：</div>
						<div class="mid_block_info"><font class="red und"><?php echo $user->pay_points_single ?></font>&nbsp;分</div>
						<div class="mid_block_info und"><a href="/user/exchange_voucher.html">积分兑换现金券</a></div>
					</dd>
				</dl>
			</div>
			<div class="mid_block">
				<dl>
					<dt>
						<div class="mid_block_title bold">账户保护：</div>
						<?php if (@$user->mobile_checked == 1) { ?>
                                                <a href="javascript:;" class="mid_block_info phone_icon_on">手机已验证</a>
                                                <?php } else { ?>
                                                <a href="/user/validate_mobile" class="mid_block_info phone_icon_off">手机未验证</a>
                                                <?php } ?>
                                                <?php if (@$user->email_validated == 1) { ?>
                                                <a href="javascript:;" class="mid_block_info mail_icon_on">邮箱已验证</a>
                                                <?php } else { ?>
                                                <a href="/user/profile" class="mid_block_info mail_icon_off">邮箱未验证</a>
                                                <?php } ?>
						
					</dt>
					<dd>
						<div class="mid_block_title font12">资料完善程度：</div>
                                                <?php if (@$user->no_profile == 1) { ?>
						<div class="mid_block_info red">未完善</div>
						<div  class="mid_block_info und"><a href="/user/profile">立即完善</a></div>
                                                <?php } else { ?>
						<div class="mid_block_info red">已完善</div>
                                                <div class="mid_block_info noWidth"><a class="und selInfo" href="/user/profile">设置</a></div>
                                                <?php } ?>
                                                <!-- come soon
						<div class="mid_block_title font12 mL75">订阅特卖信息：</div>
						<div class="mid_block_info red">已订阅</div>
						<div class="mid_block_info und w80"><a class="removeOrder cur" onclick="removeOrderDiv();">取消订阅</a></div>
                                                -->
					</dd>
                                        <!-- come soon
					<dd>
						<div class="mid_block_title font12">邀请好友：</div>
						<div class="mid_block_info"><font class="red">12</font>&nbsp;人</div>
						<div class="mid_block_info und w120"><a href="/">邀请好友,即送15积分</a></div>
					</dd>
                                        -->
				</dl>
			</div>

			
		</div>
	</div>
<script type="text/javascript">
function removeOrderDiv(){
	lhgDG = new $.dialog({ id:'thepanel',bgcolor:'#333',titleBar:true,title:'取消订阅',iconTitle:false,btnBar:false,maxBtn:false,width:417,height:214,cover:true,html:$('#removeOrder')[0] });
	lhgDG.ShowDialog();
}
</script>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>
