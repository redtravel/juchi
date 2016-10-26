<?php include APPPATH . 'views/common/user_header.php'?>           
			   	<div class="personal-center-right">
			        <h1 class="page-title">我的积分</h1> 
			        <div class="v-integral-lb">
		                <ul class="v-integral-ul">
		                    <li data-value="0" class="v-integral-currt v-integral-balance">我的积分</li>
		                    <li data-value="1" class="v-integral-balance">积分兑换</li>
		                </ul>
			        </div>
		            <div class="v-integral-right clearfix">
		                <div class="v-integral-top">
		                	<span>可用积分 <em><?php echo $user_info->pay_points;?></em> 个</span>
		                </div>
		               	<div class="v-integral-topto clearfix">
		               		<ul class="v-integral-jifen">
			                    <li data-value="0" class="v-integral-currt v-integral-detail">积分明细</li>
			                    <li data-value="1" class="v-integral-detail">积分收入</li>
			                    <li data-value="2" class="v-integral-detail" style="margin-right:300px;">积分支出</li>
			                    <select name="record" id="v-integral-sel" class="v-integral-sel v-integral-currt">
									<option value="1" <?php if($before == 1) echo 'selected';?> >最近三个月积分记录</option>
									<option value="2" <?php if($before == 2) echo 'selected';?> >三个月前积分记录</option>
								</select>
			                </ul>
		                </div>
		                <div class="v-integral-center">
							<table class="v-integral-tb" >
								<tr class="v-integral-title">
									<th width="200">日期</th>
									<th width="150">收入/支出</th>
									<th width="300">详细说明</th>
								</tr>
								<?php  if(isset($integral)) { foreach ($integral as $ing) { ?>
								<tr class="v-integral-content">
									<td><?php echo date('Y/m/d H:i:s',strtotime($ing->create_date));?></td>
									<td><?php echo $ing->pay_points;?></td>
									<td style="text-align:left;padding-left:20px;"><?php echo $ing->change_desc;?></td>
								</tr>
								<?php } }else { ?>
								<tr><td colspan=3 style="padding:20px;">您还没有积分明细!!!</td></tr>
								<?php } ?>								
							</table>
							<table class="v-integral-tb" style="display:none;">
								<tr class="v-integral-title">
									<th width="200">日期</th>
									<th width="150">收入/支出</th>
									<th width="300">详细说明</th>
								</tr>
								<?php  if(isset($integral)) { foreach ($integral as $ing) { if($ing->pay_points >= 0) { ?>
								<tr class="v-integral-content">
									<td><?php echo date('Y/m/d H:i:s',strtotime($ing->create_date));?></td>
									<td><?php echo $ing->pay_points;?></td>
									<td style="text-align:left;padding-left:20px;"><?php echo $ing->change_desc;?></td>
								</tr>
								<?php } } } ?>			
							</table>
							<table class="v-integral-tb" style="display:none;" >
								<tr class="v-integral-title">
									<th width="200">日期</th>
									<th width="150">收入/支出</th>
									<th width="300">详细说明</th>
								</tr>
								<?php  if(isset($integral)) { foreach ($integral as $ing) { if($ing->pay_points < 0) { ?>
								<tr class="v-integral-content">
									<td><?php echo date('Y/m/d H:i:s',strtotime($ing->create_date));?></td>
									<td><?php echo $ing->pay_points;?></td>
									<td style="text-align:left;padding-left:20px;"><?php echo $ing->change_desc;?></td>
								</tr>
								<?php } } }?>			
							</table>
		                </div>
		                <div class="v-integral-bottom">
		                	<h1 class="v-integral-why">积分兑换规则</h1> 
		                	<p>累积积分的获得：</p>
							<p>所有订单（包括商品和课程）的付款金额（不含运费）按1:1的方式折算累积积分（1元=1分）</p>
							<p>累积积分的使用：</p>
							<p>累积积分仅用于会员等级的升降级，不作使用</p>
							<p>消费积分的获得：</p>
							<p>订单付款金额（不含运费），在交易完成后的第15天，按1:1的方式折算消费积分（1元=1分）；——同原积分规则</p>   
							<p>消费积分的使用：</p>
							<p>任何订单都可以将消费积分以100:1的形式折算成现金抵扣订单金额（100分=1元，运费不抵扣）；——同原积分规则 </p>    
							<p>行为积分的获得：</p>
							<p>新注册用户</p>
							<p>新注册用户：500分（改为首次购买直接使用20元现金券，可根据现金券编号判断新用户来源）</p>
							<p>手机绑定：200分</p>
							<p>微信绑定：200分</p>
							<p>邮箱激活及订阅：200分</p>
							<p>用户认证资料填写完整：500分</p>
							<p>每日签到：连续登录每天递增5分，30分为上限。即第一天5分，第二天10分，第三天15分，第四天20分，第五天25分，第六天30分，第七天30分。断档后从5分起重算</p>
							<p>交易完成后发表商品或课程的评论：每款评论后被管理员审核通过可获得100-1000积分（每条评论需大于20个字符）。</p>
							<p>分享文章、视频：20分/篇，每天上限300分</p>
							<p>参与网站活动（调查、投票）：100~1000分（视活动内容而定）后台处理</p>
							<p>推荐的好友注册并成功购物：完成交易后第三天获得好友首单订单金额的双倍积分</p>
							<p>其他官方赠与活动（节庆或者促销季）：视活动内容而定后台处理</p>
							<p>行为积分的使用：</p>
							<p>同消费积分（前台只显示一种积分）</p>
							<p>积分管理设置：</p>
							<p>积分抵扣订单金额部分，不开具发票。</p>
							<p>积分获取和消费明细</p>
							<p>冻结和修改积分（用于系统出现错误的情况，但需备注原因和更高级别权限审核通过。）</p>
							<p>批量修改行为积分（用于网站活动和赠与活动情况，但需备注原因和更高级别权限审核通过。）</p>
							<p>积分数据备份</p>
							<p>使用了积分但未付款的订单，</p>
							<p>该积分为冻结状态，不可在新订单中使用直到订单付款或取消</p>
		                </div>
		            </div>  
		            <div class="v-integral-right" style="display:none;">     
		                <div class="v-integral-top">
		                	<span>您有 <em><?php echo $user_info->pay_points;?></em> 积分可以兑换</span>
		                </div>
		                <div class="v-integral-center">
		                	<table class="v-integral-tab" >
								<tr class="v-integral-title">
									<th width="200">兑换金额</th>
									<th width="200">需付积分</th>
									<th width="400">是否积分兑换现金券</th>
								</tr>
								<?php if (!empty($voucher_campaign)) { foreach ($voucher_campaign as $vc_val) { ?>
								<tr class="v-integral-content">
									<td><?php echo $vc_val->voucher_amount; ?></td>
									<td><?php echo $vc_val->worth; ?></td>
									<td><a onclick="return exchange_voucher(<?php echo $vc_val->release_id;?>);" href="javascript:void(0);">立即兑换</a></td>
								</tr>
								<?php } }else{ ?>
								<tr><td colspan=3 style="padding:20px;">暂无该活动!!!</td></tr>
								<?php } ?>                
							</table>
						</div>
		                <div class="v-integral-bottom">
		                	<h1 class="v-integral-why">积分兑换</h1> 
		                	<p>获得积分：</p>
							<p>您在线完善个人信息可获得100分</p>
							<p>您购买普通商品的金额会以1:1的方式计入演示站积分（1元=1分），购买n倍积分商品则以1:n的方式获得积分（1元=n分）</p>
							<p>您参与问卷调查、意见征集（有问必答）等相关活动也将获赠相应的积分</p>
							<p>使用积分：</p>
							<p>您购买普通商品时可以100:1的方式使用演示站积分（100分=1元）</p>   
							<p>您参与积分换购、付费培训等相关活动时也可以使用相应的积分</p>
							<p>新积分规则： </p>    
							<p>累积积分的获得： </p>
							<p>所有订单（包括商品和课程）的付款金额（不含运费）按1:1的方式折算累积积分（1元=1分）</p>
							<p>累积积分的使用： </p>
							<p>累积积分仅用于会员等级的升降级，不作使用 </p>
							<p>消费积分的获得：</p>
							<p>订单付款金额（不含运费），在交易完成后的第15天，按1:1的方式折算消费积分（1元=1分）；——同原积分规则</p>
							<p>消费积分的使用： </p>
							<p>任何订单都可以将消费积分以100:1的形式折算成现金抵扣订单金额（100分=1元，运费不抵扣）；——同原积分规则 </p>
							<p>行为积分的获得：</p>
							<p>新注册用户</p>							
		                </div>
	            	</div>	            	
			    </div>
	        </div>
	    </div>
	</div>
	<script>
	    $(".v-integral-ul li").bind("click", function () {
	        $(".v-integral-ul li").removeClass("v-integral-currt");
	        $(this).addClass("v-integral-currt");
	        var i = $(this).attr("data-value");
	        $(".v-integral-right").hide();
	        $(".v-integral-right:eq(" + i + ")").show();
	    });

	    $(".v-integral-jifen li").bind("click", function () {
	        $(".v-integral-jifen li").removeClass("v-integral-currt");
	        $(this).addClass("v-integral-currt");
	        var i = $(this).attr("data-value");
	        $(".v-integral-tb").hide();
	        $(".v-integral-tb:eq(" + i + ")").show();
	    });

	    //积分兑换现金券
		function exchange_voucher(release_id){
	        $.ajax({
	            url: '/account/exchange_voucher',
	            data: {release_id: release_id, rnd: new Date().getTime()},
	            async:false,
	            dataType: "json",
	            success:function(result){
	                if (result.msg_hd) {
	                    alert(result.msg_hd);
	                    return false;
	                };
	                if (result.msg_jf) {
	                    alert(result.msg_jf);
	                    return false;
	                };
	                if (result.msg_yes) {
	                    location.href = "/account/privilege.html";
	                };
	            }
	        });
		}


		//最近积分记录
		$("#v-integral-sel").bind("change", function () {
			var sel = $(this).val();
			if(sel == 1){
				location.href = '/account/integral.html';
			}else if(sel == 2){
				location.href = '/account/integral.html?before=2';
			}
		})

	</script>
<?php include APPPATH . 'views/common/footer.php'?>
