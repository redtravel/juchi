<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/tank.css')?>">
<div data-page="index" class="page no-toolbar">
     <!-- navbar start -->
	       <div class="navbar">
                     <div class="navbar-inner">
                           <div class="left"><a class="link icon-only back" href="#"> <i class="icon back"></i></a></div>
                           <div class="center c_name">报名培训详情</div>
                     </div>
                </div>
   <!-- navbar end -->
   
   <div class="yywtoolbar">
                <div class="yywtoolbar-inner row no-gutter">
                     <div class="col-50 order-contact-customer"><a class="link" href="#">联系客服</a></div>
                     <div class="col-50 payment-hu"><a class="link" href="#" onclick="j_order_pay('<?php print $order->order_id; ?>');">付款</a></div>
              </div>
        </div>
   
   
   
    <div class="page-content public-bg no-top2">
          <div class="page-content-inner no-top">
	       <div class="content-block article-video no-top">
	            <!-- order-details-pic start --> 
		       <div class="order-details-pic registration-training-details">
		            <div class="order-details-pb">
		                 <div class="order-details-write2"><span class="registration-information-hu"><?php echo $msg[0]?></span><?php echo $msg[1]?></div>
		            </div>
		       </div>
                   <!-- order-details-pic end --> 
	           
		   <!-- registration-information-list start -->
		   <?php $product_desc_additional = (!empty($p->product_desc_additiona)) ? json_decode($p->product_desc_additiona, true) : array(); ?>
		        <ul class="two-col clearfix">
			<li><div class="title">名称:</div><div class="value"><?php echo $course->brand_name . ' ' . $course->product_name?></div></li>
			<li><div class="title">老师:</div><div class="value"><?php echo $course->subhead?></div></li>
			<li><div class="title">类型:</div><div class="value"><?php echo $course->category?></div></li>
			<li><div class="title">时间:</div><div class="value"><?php echo date("Y-m-d", strtotime($course->package_name));?> - <?php echo $desc['desc_waterproof']?></div></li>
			<li><div class="title">地点:</div><div class="value"><?php echo $desc['desc_crowd']?></div></li>
			<li><div class="title">费用:</div><div class="value">&yen;<?php echo $course->product_price?>/人</div></li>
			<li><div class="title">客服:</div><div class="value"><?php echo $desc['desc_expected_shipping_date']?><span class="kefu-dh">|</span><span class="kefu-dh"><?php echo $desc['desc_composition']?></span></div></li>
			</ul>
		   <!-- registration-information-list end --> 
	        
		  <!-- order-details-feiyong start -->
		        <div class="order-details-feiyong order-details-feiyong2 ">
			     <ul>
			     <li>
			        <div class="order-details-rr">
				     <div class="details-yunfei">早鸟优惠: </div>
				     <div class="details-jiage">&yen;<?php echo $course->save_price?><span class="baoming-renshu">/人</span></div>
			        </div>
			     </li>
	     
			     <li>
			        <div class="order-details-rr">
				     <div class="details-yunfei">报名人数: </div>
				     <div class="details-jiage"><?php echo $course->ps_num?><span class="baoming-renshu">/人</span></div>
			        </div>
			     </li>
	     
			     <li class="none-line">
			        <div class="order-details-rr">
				     <div class="shifukuan-hu">应付款: <span>&yen;<?php $order->order_price?></span></div>
				</div>
			     </li>
			    </ul>
			</div>
              <!-- order-details-feiyong end -->
	      <!-- trains-details-list start -->
	            <div class="trains-details-list">
		         <div class="trains-details-dc clearfix">
			      <ul>
			      <li>
			      <span class="peixun-ren shuliangs-hu"><?php echo $course->ps_num?>人</span><span class="peixun-ren renming"><?php echo $client->name?></span><span class="peixun-ren Phone-number-hu no-right"><?php echo $client->mobile_phone?></span>
			      </li>
			      <li>
			      <span class="peixun-ren youjian-hu"><?php echo $client->field_1?></span><span class="peixun-ren icon-dizhi"><?php echo $client->field_2?></span>
			      </li>
			      <li>
			      <span class="peixun-ren zhensuo-hu"><?php echo $client->field_3?></span>
			      </li>
			      <li>
			      <span class="peixun-ren duanxin-hu"><?php echo $order->user_notice;?></span>
			      </li>
			      </ul>
			 </div>
		    </div>
	  <!-- trains-details-list start -->
	      
	      	<?php if ('pending' == $status): ?>

      <!-- not-paid --> 
      <div class="not-paid">
         <div class="order-details-rr">
	      <a href="#" class="item-link item-content open-picker" data-picker=".picker-pay">
                 <div class="not-paid-wr clearfix" id="default_pay" data-id="<?=$order->pay_id?>">支付方式：<img src="<?php echo $order->pay_logo?>" />&nbsp;<?php echo $order->pay_name?></div>
	         <div class="not-paid-jt"></div>
	     </a>
	  </div>  
      </div>
             <div class="order-number">
	          <div class="order-details-rr">订单编号：<span><?php print $order->order_sn ?></span> <span>创建时间：<?php print $order->create_date?></span></div>
            </div> 

	<?php elseif('paid' == $status):?>
	
	<div class="order-number">
	    <div class="order-details-rr">
	    <ul>
	        <li>订单编号：<span><?php print $order->order_sn ?></span></li>
	        <li>创建时间：<span><?php print $order->create_date?></span></li>
	        <li>付款时间：<span><?php print $order->finance_date?></span></li>
	    <ul>
	    </div>
	</div>
	<?php elseif('shipped' == $status):?>
	<div class="order-number">
	   <div class="order-details-rr">
	    <ul>
	        <li>订单编号：<span><?php print $order->order_sn ?></span></li>
	        <li>创建时间：<span><?php print $order->create_date?></span></li>
	        <li>付款时间：<span><?php print $order->finance_date?></span></li>
	        <li>发货时间：<span><?php print $order->shipping_date?></span></li>
	    <ul>
	    </div>
	</div>
	<!--交易成功-->
	<?php else:?>
	<div class="order-number">
	   <div class="order-details-rr">
	    <ul>
	        <li>订单编号：<span><?php print $order->order_sn ?></span></li>
	        <li>创建时间：<span><?php print $order->create_date?></span></li>
	        <li>付款时间：<span><?php print $order->finance_date?></span></li>
	        <li>发货时间：<span><?php print $order->shipping_date?></span></li>
	    <ul>
	    </div>
	</div>

	<?php endif?>	 
 		
		
		
		
		
		
	       
	       
	       </div>
	  
	  
	  
	  
	  </div>
<!-- 支付弹层开始-->
<div class="picker-modal picker-pay modal-in">
    <div class="navbar">
      <div class="navbar-inner" style="padding:0 10px;">
        <div class="left">选择支付方式</div>
        <div class="right"><a href="#" class="close-picker" style="color:#fff;">完成</a></div>
      </div>
    </div>
    <div class="picker-modal-inner">
        <div class="order-details-feiyong2">
            <ul class="hu-express">
                <?php foreach ($pay_list as $p): ?>
                <li class="swipeout pay_list" data-id="<?=$p->pay_id?>">
                    <div class="order-details-rr clearfix">
                        <div class="hu-kd-bt">
                            <span><img src="<?php print img_url($p->pay_logo); ?>" /> 
                        <?=$p->pay_name?></span>
                        </div>
                        <?php if($order->pay_id == $p->pay_id): ?>
	               <div class="address-returneds2"></div>
                       <?php endif; ?>
                    </div>
                </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>   
<script type="text/javascript">
function j_order_pay(p_order_ids){
    var pay_id = $$("#default_pay").attr('data-id');
    window.location.href="/order/pay/"+p_order_ids+"/"+pay_id;
}
//选择支付方式
$$(document).on('click', '.pay_list', function (e) {
    var _this = this;
    var id = $$(this).attr('data-id');
    var name_html = $$(".hu-kd-bt span", this).html();
    $$("#default_pay").attr('data-id', id);
    $$("#default_pay").html('支付方式： '+name_html);
    $$(".pay_list .address-returneds2").remove();
    $$(".order-details-rr", _this).append('<div class="address-returneds2"></div>');
    myApp.closeModal('.picker-modal.modal-in');
});
</script>
<!-- 支付弹层结束-->
    </div>
</div>

