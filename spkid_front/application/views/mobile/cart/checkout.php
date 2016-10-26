<?php include APPPATH."views/mobile/header.php"; ?>
<style>
.picker-modal{height:auto;}    
</style>
<div class="views">
<div class="view view-main" data-page="cart-checkout">
    
<div class="navbar">
    <div class="navbar-inner">
        <div class="left"><a href="#" class="link icon-only  history-back"><i class="icon icon-back"></i></a></div>
        <div class="center">确认订单</div>
        
    </div>
</div> 

<!-- 底部工具栏开始 -->    
<div class="toolbar">
     <div class="toolbar-inner row no-gutter hu-cart-settlement">
            <div class="col-60"><a href="#" class="link cart_del" style="color:#fff;">应付总价：￥<span id="h_unpay_total"><?php print fix_price($cart_summary['product_price'] + $shipping_fee - $cart_summary['voucher']); ?></span></a></div>
            <div class="col-40 settlemen-hu"><a href="#" onclick="submit_cart();" class="link external" style="color:#fff;">提交订单</a></div>
      </div>
</div>
<!-- 底部工具栏结束 -->   
<div class="page-content article-bg">
    <div class="page-content-inner">
       <div class="content-block wrap">
         
        <div class="receiving-address">   
	    <a href="#" class="item-link item-content open-popup"  data-popup=".popup-address">
	    	<div class="receiving-address-list hu-dingdan">
		<?php if(!empty($default_address)): ?>
				<div class="juli-plick item-title clearfix">
		                       <div class="receiving-lb" id="default_address" data-id="<?=$default_address->address_id?>">
				            <span class="dizhi-user"><?php echo $default_address->consignee;?></span>
					    <span class="address-tel"><?php echo (!empty($default_address->mobile))? $default_address->mobile : $default_address->tel;?></span>
					    <div class="receiving-dizhi"><?php echo $default_address->province_name.'  '.$default_address->city_name.$default_address->district_name.$default_address->address;?></div>
				       </div>
		                       
				       <div class="address-returned"></div>
				</div>
				
				
				
			<?php else: ?> 
                	<div class="juli-plick item-title clearfix">
			         <div class="receiving-lb" style="padding-top:10px;">没有收货地址</div>
			         <div class="not-paid-jt"></div>
		        </div>	
		  	<?php endif; ?> 
		    
				
		</div>
	    </a>
      </div>
        
    <div class="list-block media-list" style="margin:0 0;">
        <ul class="hu-qrdds">
          <?php foreach ($cart_summary['product_list'] as $provider_id => $provider):?>
          <?php foreach ($provider['product_list'] as $product): ?>
          <li class="c_rec<?=$product->rec_id?>">
              <a href="#" class="item-link item-content">
                <div class="item-media col-v-img">
                    <img src="<?php print img_url($product->img_url); ?>.85x85.jpg" alt="<?php print $product->brand_name . ' ' . $product->product_name; ?>"  />
                </div>
              <div class="item-inner">
                <div class="public-text"><?php print $product->brand_name . ' ' . $product->product_name; ?></div>
                <div class="hu-gwc">规格：<span class="c_size<?=$product->rec_id?>" data-subid="<?=$product->sub_id;?>"><?php print $product->size_name; ?><span></div>
                <div class="item-title-row" style="background-image:url();">
                  <div class="guanzhu-jiage">￥<?php print fix_price($product->product_price); ?></div>
                  <div class="item-after">X <?php print $product->product_num; ?></div>
                </div>          
              </div>
              </a>
          </li>
          <?php endforeach; ?>
          <?php endforeach; ?>
        </ul>
	 
	 <div class="zonggong-sp-hu"><i>共 <?php print fix_price($cart_summary['product_num']); ?> 件商品</i>合计：￥<span id="h_goods_total"><?php print fix_price($cart_summary['product_price']); ?></span></div>
    </div>

    <div class="not-paid">
        <div class="order-details-rr">
	 		<a href="#" class="item-link item-content open-picker" data-picker=".picker-shipping">
	      		<div class="not-paid-wr clearfix">	        
                    <div class="">配送方式：
		      			<span id="default_shipping" data-id="<?=$shipping_list[$shipping['shipping_id']]->shipping_id?>"><?=$shipping_list[$shipping['shipping_id']]->shipping_name?></span> ￥<span id="shipping_fee"><?=$shipping_fee?></span>元
		     		</div>                
	      		</div>
	      		<div class="not-paid-jt"></div>
	      	</a>
        </div>
    </div> 
    
    
    <div class="not-paid">
         <div class="order-details-rr">
	 <a href="#" class="item-link item-content open-picker" data-picker=".picker-invoice">
	      <div class="not-paid-wr clearfix">
	           
                     <div class="item-title">发票信息：<span id="h_invoice">不需要发票</span></div>
		  
	      </div>
	      <div class="not-paid-jt"></div>
	 </a>
        </div>
    </div> 
    
    <div class="order-number">
         <div class="order-details-rr">
	       <textarea id="remark" class="resizables" placeholder="请输入单位名称"></textarea>
	 </div>
    </div>
   
   
   <div class="not-paid hu-xjqs">
       
         <div class="order-details-rr">
            <a href="#" class="item-link item-content open-popup" data-popup=".popup-voucher">
	      <div class="not-paid-wr clearfix">
	         
	         使用现金券<span id="h_use_voucher" style="margin-left:10px;">￥<?=$cart_summary['voucher']?></span> <span id="h_use_voucher_sn"><?=$cart_summary['voucher_sn']?></span>
                 
	     </div>
	    <div class="not-paid-jt"></div>
            </a>
	 </div>
            
   </div>
   
   
   <div class="not-paid hu-xjqs">
         <div class="order-details-rr">
	      <div class="default-address-hu clearfix" style="padding:0 0;">                   
	           <a href="#" class="item-link item-content" style="display:inline-block;">                    
                    使用余额 <span id="h_user_money" style="margin-left:10px;">￥<?=$user->user_money?></span> 
		   <span id="h_used_money" class="guanzhu-jiage">￥<?=$user->user_money?></span>                  
                   </a>                   
	           <div class="default-address-anniu" style="padding:0 5px;">                      
                       <label class="label-switch">                       
                           <input type="checkbox" name="is_used" checked="">
                           <div class="checkbox"></div>                           
                       </label>                          
                   </div>
                   
	      </div>
	  </div>
   </div>
   
<?php
    if (!$payment['pay_id'] || ($payment['pay_id'] && !isset($pay_list[$payment['pay_id']]))){
        $pay = end($pay_list);
        $payment['pay_id'] = $pay->pay_id;
    }
?>	    
<div class="not-paid">
         <div class="order-details-rr">
	 <a href="#" class="item-link item-content open-picker" data-picker=".picker-pay">
	      <div class="not-paid-wr clearfix">	           
                     <div id="default_pay" data-id="<?=$payment['pay_id']?>">支付方式： <img src="<?php print img_url($pay_list[$payment['pay_id']]->pay_logo); ?>" /> 
                     <?=$pay_list[$payment['pay_id']]->pay_name?></div>		  
	      </div>
	      <div class="not-paid-jt"></div>
	 </a>
        </div>
</div> 
  
</div>   
       
 </div>           
</div>
</div>
</div>

<!-- 地址弹层start -->
<div class="popup popup-address tablet-fullscreen">
  <div class="view navbar-fixed">
	<div class="pages">
	  <div class="page">
		<div class="navbar">
		  <div class="navbar-inner">
                        <div class="left">
                            <a href="#" class="link icon-only close-popup">
                            <i class="icon icon-back"></i>
                            </a>
                        </div>
			<div class="center">选择收货地址</div>
			<div class="right" style="margin-right:8px;"><a href="/#!//address/index" class="external" style="color:#76DEFF;">管理</a></div>
		  </div>
		</div>
		
		
		
		<div class="page-content article-bg " style="padding-top:50px;">
	          <div class="content-block wrap">
		  
		        <ul class="receiving-address">
		        <?php foreach($address_list as $id => $address): ?>               	
		        <li class="address-list<?php if($address->is_used) echo ' default-address';?>" >
				 <div class="receiving-address-list ">
				     <div class="juli-plick clearfix">
		                          <div class="receiving-lb" data-id="<?=$address->address_id?>">
				               <span class="dizhi-user"><?php echo $address->consignee;?></span>
					       <span class="address-tel"><?php echo (!empty($address->mobile))? $address->mobile : $address->tel;?></span>
					       <div class="receiving-dizhi"><?php echo $address->province_name.'  '.$address->city_name.$address->district_name.$address->address;?></div>
				          </div>
                                          <?php if($address->is_used): ?>
		                          <div class="address-returneds"></div>
					  <?php endif; ?>
				     </div>  
			        </div>
		        </li>
			
			
			<?php endforeach; ?>
			
		      </ul>
		  </div>
	     </div>
	     
	     
	  </div>
	</div>
  </div>
</div>
<!-- 地址弹层end -->
<!-- 现金券弹层start -->
<div class="popup popup-voucher tablet-fullscreen ">
  <div class="view navbar-fixed ">
	<div class="pages ">
	  <div class="page">
		<div class="navbar">
		  <div class="navbar-inner">
                        <div class="left">
                            <a href="#" class="link icon-only close-popup">
                            <i class="icon icon-back"></i>
                            </a>
                        </div>
			<div class="center">选择现金券</div>
			<div class="right" style="margin-right:1em">
			<a href="/#!//account/account_content?type=voucher" class="external" style="color:#76DEFF;">管理</a></div>
		  </div>
		</div>
		<div class="page-content article-bg native-scroll">
                    <div class="list-block media-list" style="margin-top:0; color:#fff;">
                        <ul>
                            <?php foreach ($voucher_list as $v): ?>
                            <li>
                                <div class="item-content voucher_list">
                                    
                                    <div class="item-inner">
                                         <div class="item-title"><?php print $v->voucher_sn ?></div>
                                         <div class="item-subtitle"><?php print $v->end_date; ?></div>
                                        <div class="item-text" style=" color:#fff;"><?php print $v->voucher_name; ?></div>
                                    </div>
				    <div class="item-after" style="margin:20px 5px 0 0;">￥<?php print $v->voucher_amount; ?></div>
                              </div>
                            </li>
                            <?php endforeach ?>
                            <li>
			     <div class="order-details-rr clearfix" style="padding-left:16px; margin-top:10px;">
                                <div style="float:left; width:70%;"><input type="text" id="h_voucher_input" value="" placeholder="请输入券号" style="border:solid 1px #ccc; padding-left:10px; color:#fff;"></div>
                                <div style="float:right;"><a href="#" id="voucher_btn" class="submit-order2 btn btn-shine external">使用</a></div>
			     </div>	
                            </li>
                        </ul>
                    </div>
                </div>
	  </div>
	</div>
  </div>
</div>
<!-- 现金券弹层end -->
<!-- 快递弹层start -->
<div class="picker-modal picker-shipping">
    <div class="toolbar">
      <div class="toolbar-inner" style="padding:0 10px; ">
        <div class="left">选择配送方式</div>
        <div class="right"><a href="#" class="close-picker" style="color:#fff;">完成</a></div>
      </div>
    </div>
    <div class="picker-modal-inner">
         <div class="order-details-feiyong2">
		<ul class="hu-express">
		<?php foreach($shipping_list as $shipping2): ?>
		<li class="swipeout transitioning shipping_list" data-id="<?=$shipping2->shipping_id?>">
		<div class="order-details-rr clearfix">
		     <div class="hu-kd-bt item-title"><?=$shipping2->shipping_name?></div>
                     <?php if($shipping['shipping_id'] == $shipping2->shipping_id): ?>
		     <div class="address-returneds2"></div>
                     <?php endif; ?>
		</div>
		</li>
		<?php endforeach; ?>
		</ul>
	 </div>
    </div>
</div>
<!-- 快递弹层end -->
<!-- 支付方式弹层start -->
<div class="picker-modal picker-pay">
    <div class="toolbar" >
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
	               <div class="hu-kd-bt"><span><img src="<?php print img_url($p->pay_logo); ?>" /> 
                       <?=$p->pay_name?></span></div>
                       <?php if($payment['pay_id'] == $p->pay_id): ?>
	               <div class="address-returneds2"></div>
                       <?php endif; ?>
	          </div>
	        </li>
	       <?php endforeach ?>		
	      
	      
	      </ul>
	 
	 
	 </div>
    </div>
</div>
<!-- 支付方式弹层end -->
<!-- 发票弹层start -->
<div class="picker-modal picker-invoice">
    <div class="toolbar">
         <div class="toolbar-inner" style="padding:0 10px; ">
              <div class="left">发票信息</div>
              <div class="right"><a href="#" class="close-picker" style="color:#fff;">完成</a></div>
         </div>
    </div>
    <div class="picker-modal-inner">
        <div class="order-details-feiyong2">
	     <ul class="hu-express">
	     <li class="invoice_list">
	     <div class="order-details-rr clearfix">
	          <div class="hu-kd-bt">不需要发票</div>
	          <div class="address-returneds2"></div>
	     </div>
	     </li>
	     <?php foreach ($invoice_list as $invoice): ?>
	     <li class="invoice_list">
		     <div class="order-details-rr clearfix">
		          <div class="hu-kd-bt"><?=$invoice->title?></div>
		          
		     </div>
	     </li>
	     <?php endforeach ?>
             <li class="c_invoice_add">
	     <div class="order-details-rr ">
	          <div class="hu-kd-bt">
		       <div class="item-input item-input-field"><input type="text" id="h_invoice_input" value="" class="resizable" placeholder="请输入单位名称"></div>
		  </div>
		  <div class="address-returneds22 invoice_add" style="cursor: pointer;"></div>
	     </div>
	     </li>  
	     	 	     
	     </ul>
	
	
	</div>
    </div>
</div>
<!-- 发票弹层end -->
<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
<script type="text/javascript">
var v_shop_id = '<?=$shop_id?>';
//选择地址
$$(document).on('click', '.address-list', function (e) {
    var _this = this;
    var id = $$(".receiving-lb", _this).attr('data-id');
    var html = $$(".receiving-lb", _this).html();
    $$("#default_address").attr('data-id', id);
    $$("#default_address").html(html);
    var shipping_id = $$("#default_shipping").attr('data-id');
    $$.ajax({
        url: '/cart/get_shipping_fee2/'+shipping_id+'/'+id+'/'+v_shop_id,
        data: {rnd: new Date().getTime()},
        dataType: 'json',
	async:true,
        type: 'POST',
        success: function(result) {
            if (result.msg)
                myApp.alert(result.msg);
            if (result.err)
                return false;
            $$("#shipping_fee").html(result.data);
            unpay_total();
	    $$(".address-list.default-address .address-returneds").remove();
	    $$(".address-list.default-address").removeClass("default-address");
	    $$(_this).addClass('default-address');
	    $$('<div class="address-returneds"></div>').insertAfter(".default-address .receiving-lb");
            myApp.closeModal('.popup.modal-in');
        }
    });   
});
//选择支付方式
$$(document).on('click', '.pay_list', function (e) {
    var _this = this;
    var id = $$(this).attr('data-id');
    //var src_html = $$(".item-media", this).html();
    var name_html = $$(".hu-kd-bt span", this).html();
    $$("#default_pay").attr('data-id', id);
    $$("#default_pay").html('支付方式： '+name_html);
    $$(".pay_list .address-returneds2").remove();
    $$(".order-details-rr", _this).append('<div class="address-returneds2"></div>');
    myApp.closeModal('.picker-modal.modal-in');
});
//选择物流公司
$$(document).on('click', '.shipping_list', function (e) {
    var _this = this;
    var id = $$(this).attr('data-id');
    var name_html = $$(".item-title", this).html();
    $$("#default_shipping").attr('data-id', id);
    $$("#default_shipping").html(name_html);
    var address_id = $$("#default_address").attr('data-id');
    $$.ajax({
        url: '/cart/get_shipping_fee2/'+id+'/'+address_id+'/'+v_shop_id,
        data: {rnd: new Date().getTime()},
        dataType: 'json',
	async:true,
        type: 'POST',
        success: function(result) {
            if (result.msg)
                myApp.alert(result.msg);
            if (result.err)
                return false;
            $$("#shipping_fee").html(result.data);
            unpay_total();
	    $$(".shipping_list .address-returneds2").remove();
	    $$(".order-details-rr", _this).append('<div class="address-returneds2"></div>');
            myApp.closeModal('.picker-modal.modal-in');
        }
    });
});
//选择发票抬头
$$(document).on('click', '.invoice_list', function (e) {
    var name_html = $$(".hu-kd-bt", this).html();
    $$("#h_invoice").html(name_html);
    $$(".invoice_list .address-returneds2").remove();
    $$(".order-details-rr", this).append('<div class="address-returneds2"></div>');
    myApp.closeModal('.picker-modal.modal-in');
});
//发票抬头添加
$$(document).on('click', '.invoice_add', function (e) {
    var v_input = $$("#h_invoice_input").val();
    if (v_input == ''){
        myApp.alert('请输入发票抬头！', '');
        return false;
    }
        
    $$.ajax({
        url: '/cart/invoice_add',
        data: {content: v_input, rnd: new Date().getTime()},
        dataType: 'json',
	async:true,
        type: 'POST',
        success: function(result) {
            if (result.msg)
                myApp.alert(result.msg, '');
            if (result.err)
                return false;
            var v_html = '<li class="invoice_list">'+
                         '<div class="order-details-rr clearfix">'+
		         '<div class="hu-kd-bt">'+v_input+'</div>'+
                         '</div>'+
                         '</li>';
            $$(v_html).insertAfter($$(".invoice_list").eq(0));
	    $$("#h_invoice_input").val('');
        }
    });
});
//选择券
$$(document).on('click', '.voucher_list', function (e) {
    var v_voucher_sn = $$(".item-title", this).html();
    j_voucher_use(v_voucher_sn);
});

//选择券
$$(document).on('click', '#voucher_btn', function (e) {
    var v_voucher_sn = $$("#h_voucher_input").val();
    j_voucher_use(v_voucher_sn);
});

function j_voucher_use(v_voucher_sn){
    $$.ajax({
        url: '/cart/pay_voucher',
        data: {provider_id: v_shop_id, voucher_sn: v_voucher_sn, rnd: new Date().getTime()},
        dataType: 'json',
	async:true,
        type: 'POST',
        success: function(result) {
            if (result.msg)
                myApp.alert(result.msg, '');
            if (result.err)
                return false;
            $$("#h_use_voucher").html(result.data.voucher_amount+'￥');
            $$("#h_use_voucher_sn").html(result.data.voucher_sn);
            $$("#h_voucher_input").val('');
            unpay_total();
	    myApp.closeModal('.popup.modal-in');
        }
    });
}
//使用余额
$$("#h_user_money_flag").on('click', function(){
    var v_checked = $$(this).prop('checked');
    var total = parseInt($$('#h_goods_total').html());
    var user_money = parseInt($$('#h_user_money').html().replace('￥', ''));
    var used_money;
    if (total <= user_money){
        used_money = total;
    } else {
        used_money = user_money;
    }
    $$("#h_used_money").html('￥'+used_money);
    unpay_total();
});
// 计算应付总金额
function unpay_total(){
    var used_money = parseFloat($$("#h_used_money").html().replace('￥', ''));//余额
    var total = parseFloat($$('#h_goods_total').html());//商品总额
    var shipping_fee = parseFloat($$("#shipping_fee").html());//运费
    var voucher = parseFloat($$("#h_use_voucher").html().replace('￥', ''));//券
    var unpay_amount = total + shipping_fee - used_money - voucher;
    $("#h_unpay_total").html(unpay_amount);
}
var last_cart_submit_time = 0;
function submit_cart() {
    if(new Date().getTime() - last_cart_submit_time < 10000){
        myApp.alert('请不要重复提交', '');
        return false;
    }
    // 检查支付方式
    var pay_id = $$("#default_pay").attr('data-id');
    /*if (pay_id == '') {
        myApp.alert('请选择支付方式', '');
        return false;
    }*/
       
    var address_id = $$("#default_address").attr('data-id');
    if(!address_id){
        myApp.alert('请选择收货地址', '');
        return false;
    }
    var shipping_id = $$("#default_shipping").attr('data-id');
    if(!shipping_id){
        myApp.alert('请选择快递公司', '');
        return false;
    }
    // 收集数据，提交
    var data = {rnd:new Date().getTime(),address_id:address_id};
    data['use_balance'] = $$("#h_user_money_flag").prop('checked')?1:0;
    data['pay_id'] = pay_id;
    data['shipping_id'] = shipping_id;
    data['invoice'] = $$("#h_invoice").html();
    data['remark'] = $$("#remark").val();
    last_cart_submit_time = new Date().getTime();
    $$.ajax({
        url:'/cart/proc_checkout/'+v_shop_id,
        data:data,
        dataType:'json',
        type:'POST',
        success:function(result){
            last_cart_submit_time = 0;
            if (result.msg) myApp.alert(result.msg, '');
            if (result.url) {location.href=result.url;};
            if (result.err) return false;
            if(result.order_id) location.href='/#!//order/info/'+result.order_id ;
        },
        error:function()
        {
            last_cart_submit_time = 0;
        }
    });
}
</script>
</body>
</html>