<?php include APPPATH."views/mobile/header.php"; ?>
<div class="views">
<div class="view view-main" data-page="cart-checkout">
<div data-page="index" class="page public-bg no-toolbar page-on-center">
    <div class="yywtoolbar">
        <div class="yywtoolbar-inner row no-gutter">
            <div class="col-100 payment-hu"><a class="link" href="#" onclick="j_order_pay('<?php print implode('-', array_keys($order_list)); ?>');">付款</a></div>
        </div>
    </div>
    <!--navbar start-->
    <div class="navbar">
        <div class="navbar-inner">
            <div class="left"><a href="/" class="link icon-only back external"> <i class="icon back"></i></a></div>
            <div class="center">课程培训报名</div>
         </div>             
    </div>
   <!--navbar end-->		
    <div class="page-content native-scroll no-top2">
         <div class="page-content-inner no-top">
	      <div class="content-block wrap no-top">
	           
	      <!--registration-procedure start-->      
		  <div class="registration-procedure">
		       <div class="order-details-rr">
		            <ul class="hu-bmlc registration-lb">
			    <li><span>1</span>确认信息</li>
			    <li style="color:#fff;"><span class="active-hu">2</span>在线付款</li>
			    <li><span>3</span>报名成功</li>
			    <li><span>4</span>参加培训</li>
			    </ul>
		       </div>
		  </div>
	     <!--registration-procedure end-->  
	        <div class="application">
		     <div class="hu-application"><span><strong>报名信息确认成功</strong><br/>请支付培训费用</span></div>
		</div>
	        <?php 
                $pay_id = 0;
                foreach($order_list as $id => $order): 
                    if(!$pay_id) $pay_id = $order->pay_id;
                ?>
		<div class="hu-lczz"><div class="order-details-rr"><?=$order->product_list[0]->brand_name . ' ' .$order->product_list[0]->product_name?></div></div>
		<div class="order-details-feiyong">
	            <ul>
		     <li>
		        <div class="order-details-rr">
			     <div class="details-yunfei">收费标准：</div>
			     <div class="details-jiage">￥<?=$order->order_price/$order->product_num?> / 人</div>
		        </div>
		     </li>
	             <li>
		        <div class="order-details-rr">
			     <div class="details-yunfei">早鸟优惠：</div>
			     <div class="details-jiage">￥<?=($order->shop_price-$order->order_price)/$order->product_num?> / 人</div>
		        </div>
		     </li>
	     
		     <li>
		        <div class="order-details-rr">
			     <div class="details-yunfei">报名人数：</div>
			     <div class="details-jiage"><?=$order->product_num?> 人</div>
		        </div>
		     </li>
		    </ul>
		    <div class="zonggong-sp-hu" style="margin-top:10px;">待付金额：<span>￥<?=$order->order_price-$order->paid_price?></span></div>
	     </div>	      
	      
	      <div class="not-paid">
	         <div class="order-details-rr">
		 <a href="#" class="item-link item-content open-picker" data-picker=".picker-pay">
		      <div class="not-paid-wr clearfix">	           
	                     <div id="default_pay" data-id="<?=$pay_list[$order->pay_id]->pay_id?>">支付方式： <img src="<?php print img_url($pay_list[$order->pay_id]->pay_logo); ?>" /> 
                             <?=$pay_list[$order->pay_id]->pay_name?></div>	  
		      </div>
		      <div class="not-paid-jt"></div>
		 </a>
	        </div>
              </div>
	   <?php endforeach; ?> 
	   <div class="juli-plick">提示：因培训课程有名额限制，请尽快支付费用，确保报名成功。</div>
	      
	      
	      
	      
	      
	      
	      </div>
	 
	 
	 </div>
    </div>
                 
                 
        </div>
    </div>
</div>

<div class="picker-modal picker-pay modal-in">
    <div class="toolbar">
      <div class="toolbar-inner" style="padding:0 10px;">
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
                        <?php if($pay_id == $p->pay_id): ?>
	               <div class="address-returneds2"></div>
                       <?php endif; ?>
                    </div>
                </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>

<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
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
</body>
</html>