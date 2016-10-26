<?php include APPPATH . 'views/common/user_header.php'?>           
			   	<div class="personal-center-right">
			        <h1 class="page-title">我的优惠</h1> 
			        <div class="v-privilege-lb">
		                <ul class="v-privilege-ul clearfix">
		                    <li data-value="0" class="v-privilege-currt v-privilege-vouchers">未使用(<span><?php echo count($voucher_unused);?></span>)</li>
		                    <li data-value="1" class="v-privilege-vouchers">已使用(<span><?php echo count($voucher_used);?></span>)</li>
		                    <li data-value="2" class="v-privilege-vouchers">已过期(<span><?php echo count($voucher_expired);?></span>)</li>
		                </ul>
			        </div>
			        <!-- 未使用 -->
		            <div class="v-privilege-right clearfix">
		            	<div class="v-privilege-top">
		                	<span>仅显示最近六个月未使用的优惠券</span>
		                </div>
		            	<?php if($voucher_unused){?>
			            <div class="chaochu">
			            	<ul class="v-privilege-del v-privilege-unused clearfix">
			            	<?php foreach ($voucher_unused as $val_unused) : ?>
			            		<li>
			            			<div class="v-voucher-img"></div>
			            			<button style="display:none;" class="v-privilege-close voucher-del" data-recid="<?php echo $val_unused->voucher_id;?>"><span>&times;</span></button>
			            			<div class="v-privilege-condition"><p class="v-voucher-amount">&yen;<?php echo round($val_unused->voucher_amount);?></p><p class="v-min-order">【消费满<?php echo round($val_unused->min_order);?>可用】</p><p class="v-voucher-date"><?php echo date('Y-m-d',strtotime($val_unused->start_date));?>至<?php echo date('Y-m-d',strtotime($val_unused->end_date));?></p></div>
			            			<div class="v-privilege-describe"><p>券 编 号:<?php echo $val_unused->voucher_sn;?></p><p>品类限制:<?php echo $val_unused->campaign_name;?></p><p>平台限制:全平台</p></div>
			            		</li>
			            	<?php endforeach;?>
			            	</ul>
			            </div>
			            <?php }else{ ?>    	
							<p class="v-privilege-empty">您还没有未使用的优惠券!!!</p>
			            <?php } ?>
		            </div>  
					<!-- 已使用 -->
		            <div class="v-privilege-right clearfix" style="display:none;">
		            	<div class="v-privilege-top">
		                	<span>仅显示最近六个月已使用的优惠券</span>
		                </div>
		            	<?php if($voucher_used){ ?>
			            <div class="chaochu">
			                <ul class="clearfix v-privilege-used">
			            	<?php foreach ($voucher_used as $val_used) : ?>
			            		<li>
			            			<div class="v-voucher-img" style="display:none;"></div>
			            			<button style="display:none;" class="v-privilege-close"><span>&times;</span></button>
			            			<div class="v-privilege-condition"><p class="v-voucher-amount">&yen;<?php echo round($val_used->voucher_amount);?></p><p class="v-min-order">【消费满<?php echo round($val_used->min_order);?>可用】</p><p class="v-voucher-date"><?php echo date('Y-m-d',strtotime($val_used->start_date));?>至<?php echo date('Y-m-d',strtotime($val_used->end_date));?></p></div>
			            			<div class="v-privilege-describe"><p>券 编 号:<?php echo $val_used->voucher_sn;?></p><p>品类限制:<?php echo $val_used->campaign_name;?></p><p>平台限制:全平台</p></div>
			            		</li>
			            	<?php endforeach;?>
			            	</ul>
			            </div>
			            <?php }else{ ?>    	
							<p class="v-privilege-empty">您还没有已使用的优惠券!!!</p>
			            <?php } ?>      	
	            	</div>
	            	<!-- 已过期 -->
		            <div class="v-privilege-right clearfix" style="display:none;">
		            	<div class="v-privilege-top">
		                	<span>仅显示最近一周内已过期的优惠券</span>
		                </div>
		            	<?php if($voucher_expired){?>
		               	<div class="chaochu">
			                <ul class="clearfix v-privilege-expired">
			            	<?php foreach ($voucher_expired as $val_expired) : ?>
			            		<li>
			            			<div class="v-voucher-img" style="display:none;"></div>
			            			<button style="display:none;" class="v-privilege-close"><span>&times;</span></button>
			            			<div class="v-privilege-condition"><p class="v-voucher-amount">&yen;<?php echo round($val_expired->voucher_amount);?></p><p class="v-min-order">【消费满<?php echo round($val_expired->min_order);?>可用】</p><p class="v-voucher-date"><?php echo date('Y-m-d',strtotime($val_expired->start_date));?>至<?php echo date('Y-m-d',strtotime($val_expired->end_date));?></p></div>
			            			<div class="v-privilege-describe"><p>券 编 号:<?php echo $val_expired->voucher_sn;?></p><p>品类限制:<?php echo $val_expired->campaign_name;?></p><p>平台限制:全平台</p></div>
			            			<div class="v-privilege-guoqi"><p>过期时间:<?php echo date('Y-m-d',strtotime($val_expired->end_date));?></p></div>
			            		</li>
			            	<?php endforeach;?>
			            	</ul>
		            	</div>
			            <?php }else{ ?>    	
							<p class="v-privilege-empty">您还没有已过期的优惠券!!!</p>
			            <?php } ?>	   	
	            	</div>
	            	<div class="v-privilege-bottom">
	                	<h1 class="v-privilege-why">什么是演示站优惠券</h1>
						<p>优惠券是演示站通过买赠、活动参与、积分兑换等形式发放给用户，用于减免购买支付的惠民措施。从使用限额分为商品券和全场券；</p>
						<p>从销售主体进一步划分，分为限商品券和全场券；</p>
						<p>从优惠券存在形式划分，分为实体密码券和账户电子券两种。</p>
						<p>优惠券优惠部分不开具发票，以下为演示站现有各优惠券详细说明：</p>
						<p>1、全场券</p>
						<p>演示站站内通用，无使用限额、品类、地域限制。单张订单可以使用多张全场券，按面值总额减免支付，不能与商品券叠加使用。特殊商品不能使用。使用全场券提交订单时，若全场券金额大于订单需支付商品金额，差额不予退回。</p>
						<p>2、商品券</p>
						<p>商品券内通用，有使用限额限制，当订单中所购商品总额满足商品券使用限额才能使用，按商品券面值减免支付，例如：200-10的商品券，订单需支付商品金额需在200元以上才可以使用，使用后实际支付减免10元。特殊商品不能使用。单张订单只能使用一张商品券，且不能与其他任一优惠券叠加使用。</p>
						<p>3、使用范围说明</p>
						<p>活动不同，使用范围可能会有变更，请以活动页面为准。</p>
	                </div>		                 
			    </div>
	        </div>
	    </div>
	</div>
	<script type="text/javascript">
	    $(".v-privilege-ul li").bind("click", function () {
	        $(".v-privilege-ul li").removeClass("v-privilege-currt");
	        $(this).addClass("v-privilege-currt");
	        var i = $(this).attr("data-value");
	        $(".v-privilege-right").hide();
	        $(".v-privilege-right:eq(" + i + ")").show();
	    });

	    $(".v-privilege-right .v-privilege-del li").mouseover(function(){
			$(this).children().eq(1).show();
		}).mouseout(function(){$(this).children().eq(1).hide();});

	</script>

	<div id="voucher-box" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">
      	<div class="modal-dialog modify-address">
        	<div class="modal-content">
          		<div class="modal-header v-close">
		            <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
		            <h4 class="modal-title">您确定要删除所选优惠券吗？</h4>
		        </div>
          		<div class="modal-body v-button">
		            <button class="btn btn-lg btn-blue voucher-del-cfm" type="submit">确定</button>
		            <button class="btn cancel " type="submit" data-dismiss="modal">取消</button>
		        </div>
        	</div>
      	</div>
	</div>

	<script type="text/javascript">
		//删除优惠券
		var voucher_id = "";
		//显示确认弹框
		$(document).on('click', '.voucher-del', function (e) {
			voucher_id = $(this).attr('data-recid');
		    $('#voucher-box').modal('show');
		});

		//点击删除弹框层中“确认”按钮
		$(".voucher-del-cfm").click(function(){
		    if (voucher_id == "") return;
		    delete_voucher(voucher_id);
		});

		//删除选中优惠券
		function delete_voucher(voucher_id)
		{
		    $.ajax({
		        url: '/account/remove_voucher',
		        data: {voucher_id: voucher_id, rnd: new Date().getTime()},
		        dataType: 'json',
			async:true,
		        type: 'POST',
		        success: function(result) {
		            if (result.msg)
		            	alert(result.msg);
		            location.href = '/account/privilege';
		        }
		    });
		}
	</script>
<?php include APPPATH . 'views/common/footer.php'?>
