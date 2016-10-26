<?php if($is_return_from_pay) {
	include APPPATH."views/mobile/header.php";
}
?>

<?php if($is_return_from_pay): ?>
    <link rel="stylesheet" href="<?php echo static_style_url('mobile/css/yyw-app.css')?>">
    <link rel="stylesheet" href="<?php echo static_style_url('mobile/css/tank.css')?>">
<div class="views">
<!-- 演示站商城-->
    <div class="view view-main" data-page="index">
        <div class="pages">
<?endif;?>
<div data-page="index" class="page no-toolbar">
	  <!-- navbar start -->
	       <div class="navbar">
                     <div class="navbar-inner">
                           <div class="left"><a class="link icon-only back <?php echo $is_return_from_pay ? 'history_back_for_pay' : ''?>" href="#"> <i class="icon back"></i></a></div>
                           <div class="center c_name">订单详情</div>
                     </div>
                </div>
         <!-- navbar end -->
	 
	 <div class="yywtoolbar">
                <div class="yywtoolbar-inner row no-gutter">
                     <div class="col-50 order-contact-customer"><a class="link" href="#">联系客服</a></div>
                     <!--<div class="col-25 delete-order-hu cancel-order-hu view-Logistics-hu"><a class="link" href="#">删除订单</a></div>-->
                     <div class="col-50 payment-hu"><a class="link external" href="#" onclick="j_order_pay('<?php print $order->order_id; ?>');">付款</a></div>
              </div>
        </div>
	  
	 <div class="page-content public-bg no-top2">
	     <div class="page-content-inner no-top">
	        <div class="content-block article-video no-top">
	 <!-- order-details-pic start --> 
	      <div class="order-details-pic">
	           <div class="order-details-pb clearfix">
                        <div class="order-details-write"><?php echo $msg[0]?><br/><?php echo $msg[1]?></div>
                        <div class="<?php echo $status?>"></div>
		   </div>
	      </div>
        <!-- order-details-pic end --> 
	
	<!-- address start --> 
	   <div class="receiving-address-list ">
	       <div class="juli-plick clearfix">
	               <div class="receiving-lb">
		            <span class="dizhi-user"><?php print $order->consignee ?></span>
			    <span class="address-tel"><?php echo $order->mobile?></span>
			    <div class="receiving-dizhi"><?php echo "{$order->province_name} {$order->city_name} {$order->district_name} {$order->address}"?></div>
		       </div>
	               
	      </div>
          </div>
	<!-- address end -->  
	
        <!-- order-details-list start -->
	<?php foreach($product_list as $product):?>
	    <div class="item-inner order-details-list">
	        <div class="juli-plick clearfix">
		        <a href="<?php echo "/product-{$product->product_id}-{$product->color_id}.html" ?>" class="external">
		              <div class="col-v-img"><img src="<?php echo img_url($product->img_url)?>" /></div>
		              <div class="item-after order-details-yb clearfix">
		                   <span class="public-text"><?php echo $product->brand_name . ' ' . $product->product_name?></span>
				   <span class="hu-guiges"><?php echo $product->size_name?></span>
				   <div>
				        <div class="guanzhu-jiage xiangqi-hu"><?php echo $product->product_price?></div>
					<div class="number-hu">&times;<?php echo $product->product_num?></div>
					<!--<div class="refund-hu"><a href="#">退款</a></div>-->
		                   </div>
			    </div>
		       </a>
	       </div>
	  </div>
	 <?php endforeach?>
      <!-- order-details-list end --> 
      	 
      <!-- order-details-feiyong start -->
        <div class="order-details-feiyong">
	     <ul>
	     
	     <li>
	        <div class="order-details-rr">
		     <div class="details-yunfei">总金额: </div>
		     <div class="details-jiage">&yen;<?php echo $order->total_fee; ?></div>
	        </div>
	     </li>
	     <li>
	        <div class="order-details-rr">
		     <div class="details-yunfei">运费: </div>
		     <div class="details-jiage">&yen;<?php print $order->shipping_fee; ?></div>
	        </div>
	     </li>
	     
	     <li class="none-line">
	        <div class="order-details-rr">
		<?php if(0 == $order->pay_status):?> 
		     <div class="details-yunfei">应付款: </div>
		     <div class="details-jiage">&yen;<?php echo number_format($order->unpay_price,2,'.','')?></div>
		<?php else:?>
			<div class="details-yunfei">已付款: </div>
		     <div class="details-jiage">&yen;<?php echo number_format($order->paid_price,2,'.','')?></div>
		<?php endif?>
	        </div>
	     </li>
	    </ul>
	     
	     
	
	</div>
      <!-- order-details-feiyong end -->	 
      
      <!-- order-message start -->
          <div class="order-message">
	       <div class="order-message-wr"><?php print $order->user_notice; ?></div>
	  </div>	 
      <!-- order-message end -->	 
      
       <!-- Invoice start -->
           <div class="order-message invoice">
	       <div class="order-message-wr order-fp"><?php if ($order->invoice_title): ?>要发票|<?php echo $order->invoice_title?><?php else:?>不需要发票<?php endif?>
	       </div>
	  </div>
      <!-- Invoiceend -->
	<?php if ('pending' == $status): ?>
      <!-- not-paid <img src="http://s.test.com/mobile/img/alipay.png 
      <div class="not-paid">
          <div class="order-details-rr"> 
            <div class="not-paid-wr clearfix">
        <a href="#" class="open-picker" data-id=""  data-picker=".picker-pay">支付方式：<img src="" />&nbsp;</a>
	    </div>
	    <div class="not-paid-jt"></div>
	  </div>  
      </div>-->
<div class="not-paid">
         <div class="order-details-rr">
	 <a href="#" class="item-link item-content open-picker" data-picker=".picker-pay">
	      <div class="not-paid-wr clearfix">	           
                     <div id="default_pay" data-id="<?php echo $order->pay_id;?>">支付方式： <img src="<?php echo img_url($order->pay_logo);?>" />
                     <?php echo $order->pay_name?></div>		  
	      </div>
	      <div class="not-paid-jt"></div>
	 </a>
        </div>
</div>
             <div class="order-number">订单编号：<span><?php print $order->order_sn ?></span> <span>创建时间：<?php print $order->create_date?></span></div> 

	<?php elseif('paid' == $status):?>
	
	<div class="order-number">
	     <div class="juli-plick">
	      <ul>
	        <li>订单编号：<span><?php print $order->order_sn ?></span></li>
	        <li>创建时间：<span><?php print $order->create_date?></span></li>
	        <li>付款时间：<span><?php print $order->finance_date?></span></li>
	     <ul>
	    </div>
	</div>
	<?php elseif('shipped' == $status):?>
	<div class="order-number">
	    <div class="juli-plick">
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
	   <div class="juli-plick">
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
<div class="picker-modal picker-pay">
    <div class="navbar" >
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
	               <div class="hu-kd-bt"><span><img src="<?php print img_url($p->pay_logo); ?>" /> 
                       <?=$p->pay_name?></span></div>
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
	<?php if($is_return_from_pay)
		include APPPATH . "views/mobile/common/footer-js.php";
	 ?>
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

<?php if($is_return_from_pay):?>
$$(document).on('click', '.history_back_for_pay', function(e){
			location.href = '/user/order';
		});
<?endif;?>


</script>
<!-- 支付弹层结束-->
	   </div>    
	       </div>
		</div>
	</div>
</div>		
<?php
include APPPATH . "views/mobile/footer.php";
 ?>
	  	 
