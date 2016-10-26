<?php include APPPATH."views/mobile/header.php";?>

    <link rel="stylesheet" href="<?php echo static_style_url('mobile/css/yyw-app.css')?>">
    <link rel="stylesheet" href="<?php echo static_style_url('mobile/css/tank.css')?>">
    <!-- 演示站商城-->
	<div class="views">
	    <div class="view view-main" data-page="index">
	        <div class="pages">

				<div data-page="index" class="page no-toolbar">
				  	<!-- navbar start -->
				    <div class="navbar">
				     	<div class="navbar-inner">
				           <div class="left"><a class="link icon-only history-back" href="#"> <i class="icon icon-back"></i></a></div>
				           <div class="center c_name">订单详情</div>
				     	</div>
				    </div>
				    <!-- navbar end -->
				    <?php if( $status == 'pending' ) { ?>
					<div class="yywtoolbar">
				        <div class="yywtoolbar-inner row no-gutter">
				          <div class="col-20 meiqia">客服</div>
						  <div class="col-80 payment-hu"><a class="link external" href="#" onclick="j_order_pay('<?php print $order->order_id; ?>');">付款</a></div>
				      	</div>
				    </div>
				    <?php } ?>
					  
					<div class="page-content article-bg2 no-top2">
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
										     	<div class="details-yunfei">商品合计: </div>
										     	<div class="details-jiage">&yen;<?php echo $order->order_price; ?></div>
									        </div>
									    </li>
									    <li>
									        <div class="order-details-rr">
										     	<div class="details-yunfei">运费: </div>
										     	<div class="details-jiage">&yen;<?php print $order->shipping_fee; ?></div>
									        </div>
									    </li>
									    <?php if(isset($order->balance)){ ?>
									    <li>
									        <div class="order-details-rr">
										     	<div class="details-yunfei">使用余额: </div>
										     	<div class="details-jiage">-&yen;<?php print $order->balance; ?></div>
									        </div>
									    </li>
										<?php } ?>   
										<?php if(isset($order->coupon)){ ?>
										<li>
									        <div class="order-details-rr">
										     	<div class="details-yunfei">使用现金券: </div>
										     	<div class="details-jiage">-&yen;<?php print $order->coupon; ?></div>
									        </div>
									    </li>
									    <?php } ?>
									    <li class="none-line">
									        <div class="order-details-rr">
										     	<div class="details-yunfei">实付款: </div>
										     	<div class="details-jiage">&yen;<?php echo number_format($order->real_pay,2,'.','')?></div>
									        </div>
									    </li>
								    </ul>
								</div>
				      			<!-- order-details-feiyong end -->	 
				      
				      			<!-- order-message start -->
				      			<?php if(!empty($order->user_notice)){?>
				          		<div class="order-message">
					       			<div class="order-message-wr"><?php print $order->user_notice; ?></div>
					  			</div>	
					  			<?php } ?> 
				      			<!-- order-message end -->	 
				      
						       <!-- Invoice start -->
						        <div class="order-message invoice">
							       <div class="order-message-wr order-fp">
							       		<?php if ($order->invoice_title): ?>要发票|<?php echo $order->invoice_title?><?php else:?>不需要发票<?php endif?>
							       </div>
							  	</div>
						     	 <!-- Invoiceend -->
								<?php if ('pending' == $status): ?>
								<div class="not-paid not-paid2">
								    <div class="order-details-rr">
									 	<a href="#" class="item-link item-content open-picker" data-picker=".picker-pay">
									      	<div class="not-paid-wr not-paid-wr2 clearfix">	           
								                <div id="default_pay" data-id="<?php echo $order->pay_id;?>">
								                	支付方式： <img src="<?php echo img_url($order->pay_logo);?>" />
								                	<?php echo $order->pay_name?>
								                </div>		  
									      	</div>
									      	<div class="not-paid-jt"></div>
									 	</a>
								    </div>
								</div>
								<div class="order-number">
									订单编号：<span><?php print $order->order_sn ?></span></br><span>
									创建时间：<?php print $order->create_date?></span>
								</div> 
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
								<!--交易终止-->
								<?php elseif('invalid' == $status):?>
								<div class="order-number">
								    <div class="juli-plick">
									    <ul>
									        <li>订单编号：<span><?php print $order->order_sn ?></span></li>
									        <li>创建时间：<span><?php print $order->create_date?></span></li>
									        <li>终止时间：<span><?php print $order->is_ok_date?></span></li>								        
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
					</div>
					<div class="picker-modal picker-pay" style="height:auto;position: fixed;">
					    <div class="navbar" >
					      	<div class="navbar-inner" style="padding:0 10px;font-size: 14px;background-color: #ea9b56;">
					        	<div class="left">选择支付方式</div>
					        	<div class="right" style="margin-left:auto;"><a href="#" class="close-picker" style="color:#fff;">完成</a></div>
					      	</div>
					    </div>
					    <div class="picker-modal-inner">
					        <div class="order-details-feiyong2">
							    <ul class="hu-express">
								    <?php foreach ($pay_list as $p): ?>
								    <li class="swipeout pay_list" data-id="<?=$p->pay_id?>">
								        <div class="order-details-rr clearfix">
							               <div class="hu-kd-bt">
							               		<span><img src="<?php print img_url($p->pay_logo); ?>" /><?=$p->pay_name?></span>
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
		       	</div>
			</div>
		</div>
	</div>	
	<!-- 支付弹层开始-->	       
	
	<?php include APPPATH . "views/mobile/common/footer-js.php"; ?>
	<script type="text/javascript">
		function j_order_pay(p_order_ids){
			ga('send', 'event', 'order-pay2', 'click', 'pay-two');
		    var pay_id = $$("#default_pay").attr('data-id');
		    myApp.showIndicator();
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

		// $$(document).on('click', '.history_back_for_pay', function(e){
		// 			location.href = '/user/order';
		// 		});
	</script>
	<!-- 支付弹层结束-->	
<?php include APPPATH . "views/mobile/common/meiqia.php"; ?>	
<?php include APPPATH . "views/mobile/footer.php"; ?>
	  	 
