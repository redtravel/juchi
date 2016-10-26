<?php include APPPATH."views/mobile/header.php"; ?>
<div class="views">
<div class="view view-main" data-page="cart-checkout">
<div data-page="index" class="page article-bg no-toolbar page-on-center">
    <!--navbar start-->
    <div class="navbar">
        <div class="navbar-inner">
            <div class="left"><a href="/" class="link icon-only back external"> <i class="icon back"></i></a></div>
            <div class="center">支付成功</div>
        </div>             
    </div>
   <!--navbar end-->		
   
   <div class="page-content native-scroll no-top2">
        <div class="page-content-inner no-top">
	     <div class="content-block wrap no-top">
	     <!--registration-procedure start-->      
		  <!-- <div class="registration-procedure" style="margin-top:20px;">
		       <div class="order-details-rr">
		            <ul class="hu-bmlc registration-lb">
		  			    <li><span>1</span>确认信息</li>
		  			    <li><span>2</span>在线付款</li>
		  			    <li style="color:#fff;"><span class="active-hu">3</span>报名成功</li>
		  			    <li><span>4</span>参加培训</li>
		  			    </ul>
		       </div>
		  </div> -->
	     <!--registration-procedure end-->
	         
		 <!-- <div class="application" style="background-color:#D16038;">
		     <div class="hu-application"><span><strong>报名费用支付成功</strong><br/>请按时到制定地址参加培训</span></div>
		 		</div> -->
	        
		<?php foreach($order_list as $id => $order): ?>
		<div class="hu-lczz">
		    <div class="order-details-rr">订单号：<?=$order->order_sn?></div>
		</div>
		<div class="zonggong-sp-hu" style="text-align:left; padding:10px 0 0 10px;">实付金额：<span>￥<?=$order->paid_price?></span></div>
		
		<div class="row hu-chenggong">
                  <div class="col-50"><a href="/#!//order/<?php echo $info_type;?>/<?=$order->order_id?>" class="button button-big button-fill color-lanse">订单详情</a></div>
                  <div class="col-50"><a href="/" class="button button-big button-fill color-lanse">返回首页</a></div>
                </div>
	        <?php endforeach; ?>
	       
	       
	       <div class="hu-safety">
	           <div class="juli-plick ">
	                <h3 style="color:#fff; text-align:center; font-weight:normal;">安全提醒</h3>
	                <p style="color:#224A63; padding-top:10px;">付款成功后，演示站不会以付款异常，系统升级为由联系您。<br/>请勿泄露银行卡号、手机验证码。<br/>否则会造成钱款损失，谨防电话诈骗！</p>
	           </div>
            </div>
	     
	     
	     </div>
	
	
	
	</div>
      
   
   
   
   </div>
                 
                 








</div>
</div>
</div>
</body>
</html>