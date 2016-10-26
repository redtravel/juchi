<?php include APPPATH."views/common/header2.php"; ?>
<style>
.cartlist .cartlb li {
    display: inline-block;
    margin-right: 0px;
    width: 180px;
    text-align: center;
}
.cartlist .cart-main .goods-price { text-align: center; color:#f75555; font-size:14px;}
.cartlist .cart-main .goods-price2{ width:160px;}
.cartlist .cart-main .counter { width:207px;text-align: center;}
.cartlist .cart-main .goods-money { text-align: center; width:160px; color:#f75555;}
.cartlist .cart-main .goods-info .goods-desc {
    margin-left: 8px;
    width: 112px;
}
</style>
<!--choose-shipping start-->
<div class="cart-wrapper">
    <div class="gwc-wrapper">
         <div class="home-wrapper none-bottom">
             <h2 class="choose-title">选择收货地址</h2>
              <ul class="choose-shipping address_list clearfix">
             <?php include(APPPATH . 'views/cart/address_list2.php'); ?>
              </ul>
         </div>
    </div>
</div>
<!--choose-shipping end-->

<!--invoice-information start-->
<div class="cart-wrapper">
      <div class="gwc-wrapper">
           <div class="home-wrapper">
                <h2 class="choose-title">发票信息</h2>
                <div class="invoice-information">
                     <div class="invoice-nr">
                           <span id="invoice_msg">
                               <?php if(count($invoice_cookie)): 
                                        if (count($invoice_cookie) == 3):
                                            echo '<span>增值发票</span><span>姓名：'.$invoice_cookie[0].'</span><span>联系方式：'.$invoice_cookie[1].'</span><span>开票留言：'.$invoice_cookie[2].'</span>';
                                        else:
                                            echo '<span>普通发票</span><span>发票抬头：'.$invoice_cookie[0].'</span><span>发票内容：'.$invoice_cookie[1].'</span>';
                                        endif;
                                 else: ?>
                               <span>不需要发票</span>
                               <?php endif; ?>   
                           </span>
                           <a href="#invoice" data-toggle="modal" data-container="body" class="modify-bt">修改</a>
                     </div>
                </div>
           
           </div>
      
      </div>


</div>
<!--invoice-information end-->


<!--cart-wrapper start-->
<div class="cart-wrapper">
     <div class="gwc-wrapper">
          <div class="home-wrapper none-bottom">
                <div class="cartlist">
                         <ul class="cartlb clearfix">
                             <li>商品信息</li>
                             <li>单价</li>
                             <li>数量</li>
			     <li>小计</li>
                         </ul> 
                         <ul class="cart-main clearfix">
                             <!--购物车有内容的时候开始-->
                             <?php foreach ($cart_summary['product_list'] as $provider_id => $provider):?>
                             <?php foreach ($provider['product_list'] as $product): ?>
                             
                             <li class="goods-first bulky">
                                 <div class="item-table" style="width:180px;">
                                       <a class="goods-info goods-info2" target="_blank" href="/pdetail-<?=$product->product_id?>.html">
                                       <div class="goods-img item-table"><img src="<?php print img_url($product->img_url); ?>.85x85.jpg" style="margin-left: 5px;"/></div>
                                       <div class="goods-desc item-table">
                                            <p class="goods-name"><?php print $product->product_name; ?></p>
                                            <p class="goods-attr"><span><?php print $product->size_name; ?></span></p>
                                        </div>
                                      </a> 
                                </div>
                                
                                <div class="goods-price item-table goods-price2"><span>￥<em class="bprice"><?php print fix_price($product->product_price); ?></em></span></div>
                                
                                <div class="counter item-table">
                                    <div class="counter-wrapper">
                                         <?php print $product->product_num; ?>
                                    </div>
	                          </div>
                              
                              <div class="goods-money item-table">￥<span><em class="total"><?php print fix_price($product->product_price * $product->product_num); ?></em></span></div>
                             </li>
                            
                            <?php endforeach; ?>
                            <?php endforeach; ?>
                             <!--购物车有内容的时候结束-->
                          </ul>  
                      </div>
               </div>
          </div>
</div>         
<!--cart-wrapper end-->

<!--add-order start-->
<div class="home-wrapper">
      <div class="add-order"><span>添加订单备注</span><input id="remark" type="text" class="benzhu" placeholder="选填，限50个字，请将购买需求在备注中做详细说明"></div>
      <div class="express-way">
          普通配送
          <div class="express-xz">
              <?php foreach($shipping_list as $shipping2): ?>
              <span class="i-radio<?php if($shipping['shipping_id'] == $shipping2->shipping_id): ?> checked<?php endif; ?> express-kd" data-id="<?=$shipping2->shipping_id?>"></span><em><?=$shipping2->shipping_name?></em>
              <?php endforeach; ?>
              <span id="shipping_fee" class="express-text">￥<?=$shipping_fee?></span></div>
      </div>
</div>
<!--add-order end-->

<!--promo-code start-->
<div class="home-wrapper no-bottom" >     
     <div class="coupon-box">
          <div class="use-coupons"><div class="promo-code" onclick="openShutManager(this,'coupon-box-code',false,'-','+')">+</div>演示站优惠<span class="coupon-box-title-tip"></span></div>        
          <div id="coupon-box-code" class="coupon-box-code" style="display: none;">
              <?php if(empty($voucher_list)): ?> 
              <div class="coupon-list-no-coupon">对不起，您没有可用优惠券</div>
              <?php endif; ?>
               <div class="coupon-box-codes">
                  <?php 
                  $coupon_input_flag = false;
                  if(!empty($voucher_list)): ?>
                  <ul class="coupon-radio">
                  
                  <?php foreach ($voucher_list as $v): ?>
                  <li class="coupon-click">
                  <input type="radio" value="<?php print $v->voucher_sn; ?>" class="t-radio sel_coupon"<?php if(!empty($payment['voucher']) && $payment['voucher'][$v->provider]->voucher_sn == $v->voucher_sn): ?> checked<?php endif; ?>><label><?php print $v->voucher_name; ?></label>
                  <?php if(!empty($payment['voucher']) && isset($payment['voucher'][$v->provider]) && $payment['voucher'][$v->provider]->voucher_sn == $v->voucher_sn): 
                        $coupon_input_flag = true;    
                  ?>
                  <span><span class="qxsy coupon_del" sid="<?=$v->voucher_sn?>">取消使用</span><span class="shiyong">使用该券后将不能参加其他促销活动</span></span>
                  <?php endif; ?>
                  </li>
                  <?php endforeach ?>
                  </ul>
                  <?php endif; ?>
                  <div class="for-ful">
                      <div class="coupon-rr clearfix">
                          <?php if(!empty($payment['voucher']) && !$coupon_input_flag): 
                              $key = array_keys($payment['voucher']);
                              ?>
                          <label>优惠码：</label><div class="coupon-code-input-group"><input placeholder="请输入券号" id="coupon_code_input" value="<?=$payment['voucher'][$key[0]]->voucher_sn?>" disabled/></div>
                           <button class="coupon-code-submit-button coupon-button coupon-button2" id="use_coupon">取消</button>                          
                           <?php else: ?>
                           <label>优惠码：</label><div class="coupon-code-input-group"><input placeholder="请输入券号" id="coupon_code_input"/></div>
                           <button class="coupon-code-submit-button coupon-button" id="use_coupon">使用</button>
                           <?php endif; ?>
                           <!--
                           <button class="coupon-code-submit-button coupon-button coupon-button2">取消</button>
                           -->
                       </div>
                      <!--
                       <div class="chenggouduihuan">兑换成功！已优惠￥40</div>
                       
                       <span class="err_tip2" id="address_err">不能同时使用两种优惠方式</span>
                       -->
                 </div>                  
              </div>          
          </div>  
    </div>    
</div>
<!--promo-code end-->


<!--promo-code start-->
<div class="home-wrapper no-bottom">     
     <div class="summary">
          <div class="clearfix">
              <p class="fl-right">
                  <span class="txt">商品总额：</span>
                  <strong><em id="totalmoney">￥<?php print fix_price($cart_summary['product_price']); ?></em></strong>
                  <br>
                  <span class="txt">(普通快递) 运费：</span>
                  <strong><em id="shipping_fee2">￥<?=$shipping_fee?></em></strong>
                  <br>
                  <span class="txt">优惠券：</span>
                  <strong><em id="freight">￥<?=$cart_summary['voucher']?></em></strong>
                  <br>
                  <span id="showMoney" class="total-info txt">应付总额：</span>
                  <span class="total-info amounted">￥<em id="paymoney"><?php print fix_price($cart_summary['product_price'] + $shipping_fee - $cart_summary['voucher']); ?></em></span></p>
                  <span class="total-num fl-right">共<?=$cart_summary['product_num']?>件商品</span>
          </div>
   </div>
     
</div>
<!--promo-code end-->

<!--send start-->
<div class="home-wrapper clearfix">
     <div class="send">寄送至：<?=$default_address->province_name?>  <?=$default_address->city_name?>  <?=$default_address->district_name?><?=$default_address->address?><p>收货人：<?=$default_address->consignee?>  <?php echo (!empty($default_address->mobile))? $default_address->mobile : $default_address->tel;?></p></div>
     <a href="#" class="submit-button fl-right" onclick="submit_cart();">提交</a>
</div>
<!--send end-->

<!-- 地址添加弹层开始 -->
<div id="address_block" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">

</div>
<!-- 地址添加弹层结束 -->

<!--限制增加收货地址-->
<div id="limit-box" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header v-close">
                <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">收货地址已满，请修改或删除收货地址</h4>
            </div>
            <div class="modal-body v-button">
                <button class="btn btn-lg btn-blue" type="submit" data-dismiss="modal">确定</button>             
                <button class="btn cancel" type="submit" data-dismiss="modal">取消</button>              
            </div>
        </div>
    </div>
</div>

<!--发票弹出框-->
<div id="invoice" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog invoice-box">
        <div class="modal-content">
          <div class="modal-header v-close">
              <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title2">发票信息</h4>
          </div>
          <?php 
          $invoice_type = 0;
          if(count($invoice_cookie) > 2){
              $invoice_type = 1;
          } ?>
          <div class="modal-body invoice-thickbox">
                   <div class="tab-nav">
                        <ul class="tab-nav-item clearfix">	
                        <li data-value="0"<?php if(!$invoice_type): ?> class="invoice-currt"<?php endif;?>>普通发票</li>
                        <li data-value="1" <?php if($invoice_type): ?> class="invoice-currt"<?php endif;?>>增值税发票</li>		
                        </ul>
                        
                        <div class="invoice-lb clearfix">
                             <div class="invoice-item invoice-item-selected clearfix">
                                  <span class="invoice-taitou">发票抬头：</span>
                                  <div id="invoice_list" class="invoice-fl" style="max-height: 132px;overflow-y: auto;position: relative;">
                                      
                                    <span class="fore2<?php if(!$invoice_type && isset($invoice_cookie[0]) && $invoice_cookie[0] == '不需要发票'): ?> fore2-selet<?php endif; ?>"><input type="text" value="不需要发票" class="itxt" readonly="readonly">
                                    </span>
                                    <?php foreach ($invoice_list as $invoice): ?>  
                                    <span class="fore2<?php if(!$invoice_type && isset($invoice_cookie[0]) && $invoice_cookie[0] == $invoice->title): ?> fore2-selet<?php endif; ?>"><input type="text" value="<?=$invoice->title?>" class="itxt" data-r="<?=$invoice->id?>" readonly="readonly">
                                           <div class="btns">
                                            <!--
                                            <a class="ftx-05" href="#">编辑</a>
                                            -->
                                            <a class="ftx-05 invoice_del" href="#" data-r="<?=$invoice->id?>">删除</a>
                                            </div>
                                    </span>
                                    
                                    <?php endforeach ?>
                                      <span id="invoice_save" class="fore2" style="display: none;"><input type="text" value="" class="itxt" placeholder="新增单位发票抬头">
                                           <div class="btns">
                                            <!--
                                            <a class="ftx-05" href="#">编辑</a>
                                            -->
                                            <a class="ftx-05 save-tit" href="#">保存</a>
                                            </div>
                                    </span>
                                  </div>
                                  <span class="invoice-bc invoice_add">新增</span>
                                  
                          </div>
                            <div class="invoice-content clearfix">
                                 <span class="fpnr-tit">发票内容:</span>
                                 <ul class="fpnr-lb">
                                 <li<?php if(!$invoice_type && isset($invoice_cookie[1]) && $invoice_cookie[1] == '明细'): ?> class="fpnr-lb-selt"<?php endif; ?>>明细</li>
                                 <li<?php if(!$invoice_type && isset($invoice_cookie[1]) && $invoice_cookie[1] == '牙科耗材一批'): ?> class="fpnr-lb-selt"<?php endif; ?>>牙科耗材一批</li>
                                 </ul>
                            </div>
                            
                           <div class="v-button hu-button">
                                  <button class="btn btn-lg btn-blue save_invoice" type="submit">保存</button>
                                  <button class="btn cancel" type="submit" data-dismiss="modal">取消</button>
                                  <p class="wxts">温馨提示：发票金额不含演示站积分，优惠券，现金券部分</p>
                           </div>
                           
                           
                                 
                          
                        </div>
                        
                        
                        <div class="invoice-lb invoice-lb2" style="display:none;">
                             <p class="fp-lx">如需增值税发票请联系客服：400-9905-920</p>
                             <p class="fp-lx">给客服留言：</p>
                             <ul class="vat-invoice">
                             <li><label><i>*</i>姓名：</label><input id="inc_name" type="text"<?php if($invoice_type && isset($invoice_cookie[0])): ?> value="<?=$invoice_cookie[0]?>"<?php endif; ?>><span class="err_empty" id="inc_name_error">不能为空</span></li>
                             
                             <li><label><i>*</i>联系方式：</label><input id="inc_mobile" type="text"<?php if($invoice_type && isset($invoice_cookie[1])): ?> value="<?=$invoice_cookie[1]?>"<?php endif; ?>><span class="err_empty" id="inc_mobile_error">不能为空</span></li>
                             <li><label><i>*</i>开票留言：</label><textarea id="inc_content" cols="" rows="" placeholder="请将您的需求留言（限30字）"><?php if($invoice_type && isset($invoice_cookie[2])): ?><?=$invoice_cookie[2]?><?php endif; ?></textarea><span class="err_empty" id="inc_content_error">不能为空</span></li>
                             </ul>
                             
                             <div class="v-button hu-button2">
                                  <button class="btn btn-lg btn-blue save_invoice" type="submit">保存</button>
                                  <button class="btn cancel" type="submit" data-dismiss="modal">取消</button>
                                  <p class="wxts">温馨提示：发票金额不含演示站积分，优惠券，现金券部分</p>
                            </div>
                          
                        
                        </div>
                        
                        
                  </div>
                  
                  
          </div>
        </div>
      </div>
</div>
<!--发票弹出框-->

<script type="text/javascript">
var v_cur_coupon = '<?php if(!empty($payment['voucher'])){$key = array_keys($payment['voucher']); echo $payment['voucher'][$key[0]]->voucher_sn;}?>';
var v_rec_ids = '<?=$rec_ids?>';
var v_shipping_id = '<?=$shipping['shipping_id']?>';
var v_address_id = '0';
//click是鼠标点击事件  removeClass是移除样式 addClass是添加样式
$(document).on('click', '.invoice-fl .hover', function (e) {
    if (!$(this).hasClass('btn-hover')){        
        $('.invoice-fl .fore2-selet').removeClass("fore2-selet");
        $(this).addClass("fore2-selet");        
    }
});

$(".invoice_add").click(function(){
    $("#invoice_save").show();
    if($("#invoice_list span").length > 3) {
        $("#invoice_list").scrollTop($("#invoice_list").height());
    }    
});

$(document).on('click', '.btn-hover .invoice_del', function (e) {
    var v_obj = $(this);
    var v_id = parseInt(v_obj.attr('data-r'));
    if (v_id == ''){
        return;
    }
    
    $.ajax({
        url: '/cart/invoice_del',
        data: {id: v_id, rnd: new Date().getTime()},
        dataType: 'json',
	async:true,
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            v_obj.parent().parent().remove();
        }
    });
});

//发票抬头添加
$(document).on('click', '.save-tit', function (e) {
    var v_obj = $(this).parent().siblings('.itxt');
    var v_input = v_obj.val();
    if (v_input == ''){
        alert('请输入发票抬头！');
        return false;
    }
        
    $.ajax({
        url: '/cart/invoice_add',
        data: {content: v_input, rnd: new Date().getTime()},
        dataType: 'json',
	async:true,
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            //var v_html = '<span class="fore2"><input type="text" value="'+v_input+'" class="itxt" data-r="'+result.id+'" readonly="readonly">'+
            //             '<div class="btns"><a class="ftx-05 invoice_del" href="#" data-r="'+result.id+'">删除</a></div>'+
            //             '</span>';
            var v_html = '<span class="fore2"><input type="text" value="'+v_input+'" class="itxt" data-r="'+result.id+'" readonly="readonly">'+
                         '<div class="btns"><a class="ftx-05 invoice_del" href="#" data-r="'+result.id+'">删除</a></div>'+
                         '</span>';
            $(v_html).insertAfter($("#invoice_list span").eq(0));
	    v_obj.val('');
             $("#invoice_save").hide();
        }
    });
});

//发票明细切换显示
$(".fpnr-lb li").click(function(){
    $(this).siblings('.fpnr-lb-selt').removeClass('fpnr-lb-selt');
    $(this).addClass('fpnr-lb-selt');
});

$(".save_invoice").click(function(){
    var v_type = $(".invoice-currt").attr('data-value');
    $(".err_empty").hide();
    var v_html = '';
    if (v_type == 1){
        var v_inc_name = $("#inc_name").val();
        var v_inc_mobile = $("#inc_mobile").val();
        var v_inc_content = $("#inc_content").val();
        if (v_inc_name.length <= 0) {
            $("#inc_name_error").show();
            return false;
        }
        if (v_inc_mobile.length <= 0) {
            $("#inc_mobile_error").show();
            return false;
        }
        if (v_inc_content.length <= 0) {
            $("#inc_content_error").show();
            return false;
        }
        v_html = '<span>增值发票</span><span>姓名：'+v_inc_name+'</span><span>联系方式：'+v_inc_mobile+'</span><span>开票留言：'+v_inc_content+'</span>';
        setCookie('invoice_msg',v_inc_name+'#'+v_inc_mobile+'#'+v_inc_content,0);
    } else {
        var v_name = $(".fore2-selet input").val();
        var v_content = $(".fpnr-lb-selt").html();
        v_html = '<span>普通发票</span><span>发票抬头：'+v_name+'</span><span>发票内容：'+v_content+'</span>';
        setCookie('invoice_msg',v_name+'#'+v_content,0);
    }
    $("#invoice_msg").html(v_html);
    $("#invoice").modal('hide');
});

$("#use_coupon").click(function(){
    var v_coupon_code = $("#coupon_code_input").val();
    if ($(this).hasClass('coupon-button2')){
        remove_voucher(v_coupon_code, 1);
    } else {
        use_voucher(v_coupon_code, 1);
    }
});

$(document).on('click', '.coupon_del', function (e) {
    var _this = $(this);
    var v_coupon_code = _this.attr('sid');
    remove_voucher(v_coupon_code, 2, _this);
});

$(".sel_coupon").click(function(){
    var _this = $(this);
    var v_coupon_code = $(this).val();
    //if (v_coupon_code == '0'){
    //    remove_voucher(v_coupon_code, 1);
    //} else {
        use_voucher(v_coupon_code, 2, _this);
    //}   
});
function remove_voucher(voucher_sn, use_type, p_obj) {
    if (!voucher_sn) {
        alert('请选择要取消的现金券号');
        return false;
    }
    $.ajax({
        url: '/cart/unpay_voucher',
        data: {voucher_sn: voucher_sn, rnd: new Date().getTime()},
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            v_cur_coupon = '';
            $("#freight").html('￥0');
            //location.href = location.href;
            if (use_type == 1){
                $("#use_coupon").removeClass('coupon-button2');
                $("#use_coupon").html('使用');
                $("#coupon_code_input").removeAttr('disabled');
                $("#coupon_code_input").val('');
            } else {
                p_obj.parent().siblings(".sel_coupon").removeAttr('checked');
                p_obj.parent().empty();
            }
            unpay_total();
        }
    });
}
function use_voucher(voucher_sn, use_type, p_obj) {
    if (!voucher_sn) {
        alert('请选择或输入现金券号');
        return false;
    }
    $.ajax({
        url: '/cart/pay_voucher',
        data: {voucher_sn: voucher_sn, rec_ids:v_rec_ids, rnd: new Date().getTime()},
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.msg) {
                if (use_type == 2) p_obj.removeAttr('checked');
                alert(result.msg);
            }
            if (result.err)
                return false;
            v_cur_coupon = voucher_sn;
            $("#freight").html('￥'+result.data.voucher_amount);
            if (use_type == 1){
                $("#use_coupon").addClass('coupon-button2');
                $("#use_coupon").html('取消');
                $("#coupon_code_input").attr('disabled', true);
                $(".t-radio:checked").removeAttr('checked');
            } else {
                p_obj.parent().append('<span><span class="qxsy coupon_del" sid="'+result.data.voucher_sn+'">取消使用</span><span class="shiyong">使用该券后将不能参加其他促销活动</span></span>');
            }
            unpay_total();
            //location.href = location.href;
        }
    });
}   
//单选复选框
$(document).on('click', '#iscurrent', function (e) {
    if ($(this).hasClass('checked')){
        $(this).removeClass('checked');
    } else {
        $(this).addClass('checked');
    }
});
//切换地址
$(document).on('click', '.address_list li', function (e) {
    var v_id = $(this).attr('id');
    if (v_id == undefined) return;
    v_id = parseInt(v_id.replace(/address/, ''));
    v_address_id = v_id;
    $(".address_list li").removeClass('default');
    $("#address"+v_id).addClass('default');
    var v_address = $("#address"+v_id+" .prov").html()+' '+$("#address"+v_id+" .city").html()+' '+$("#address"+v_id+" .addr-bd").html();
    v_address += '<p>收货人：'+$("#address"+v_id+" .name").html()+'  '+$("#address"+v_id+" .tell").html()+'</p>';
    $(".send").html(v_address);
    get_shipping_fee();
});    
// 删除地址
$(document).on('click', '.address_del', function (e) {
    var address_id = $(this).attr('data-recid');
    $.ajax({
        url: '/address/address_delete?address_id='+address_id,
        data: {},
        dataType: 'json',
        type: 'GET',
        success: function(result) {
            if (result.mobile_check_err)
                $("#address"+address_id).remove();
        }
    });
});
//设置默认地址
$(document).on('click', '.address_default', function (e) {
    var address_id = $(this).attr('data-recid');
    $.ajax({
        url: '/address/address_default?address_id='+address_id,
        data: {},
        dataType: 'json',
        type: 'GET',
        success: function(result) {
            if (!result.error) {
                $(".address_list .deftip").remove();
                $("#address"+address_id).append('<ins class="deftip">默认</ins>');
                $(".address_list .address_default:hidden").show();
                $("#address"+address_id+" .address_default").hide();
                $(".address_list li").removeClass('default');
                $("#address"+address_id).addClass('default');
                var v_address = $("#address"+address_id+" .prov").html()+' '+$("#address"+address_id+" .city").html()+' '+$("#address"+address_id+" .addr-bd").html();
                v_address += '<p>收货人：'+$("#address"+address_id+" .name").html()+'  '+$("#address"+address_id+" .tell").html()+'</p>';
                $(".send").html(v_address);
            }
        }
    });
});

function load_address_form(address_id) {
    $.ajax({
        url: '/cart/load_address_form',
        data: {address_id: address_id, rnd: new Date().getTime()},
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            if (result.html) {
                $('#address_block').html(result.html).modal('show');
            }
        }
    });
}

function check_address() {
    var movePhone = /^1[3,5,8][0-9]{9}/,
            postCode = /^\d{6}$/;
    if ($('.address_block').css('display') == 'none')
        return true;
    
    if (!$(':input[name=province]').val() || !$(':input[name=city]').val() || !$(':input[name=district]').val())
        $("#region_err").show();
    if (!$.trim($(':input[name=address]').val()))
        $("#address_err").show();
    if (!$.trim($(':input[name=consignee]').val()))
        $("#consignee_err").show();
    if (!movePhone.exec($.trim($(':input[name=mobile]').val()))) {
        $("#mobile_err").show();
    }    
    return true;
}
function submit_address_form() {
    $('.err_tip').hide();
    check_address();
    if ($('.err_tip:visible').length > 0) {
        $('.err_tip:first').focus();
        return false;
    }
    var address_id = $.trim($(':input[name=address_id]').val());
    var data = {rnd: new Date().getTime(), address_id: address_id}
    data['address_id'] = address_id;
    data['consignee'] = $.trim($(':input[name=consignee]').val());
    data['address'] = $.trim($(':input[name=address]').val());
    data['zipcode'] = $.trim($(':input[name=zipcode]').val());
    data['mobile'] = $.trim($(':input[name=mobile]').val());
    data['province'] = $.trim($(':input[name=province]').val());
    data['city'] = $.trim($(':input[name=city]').val());
    data['district'] = $.trim($(':input[name=district]').val());
    data['is_used'] = ($("#iscurrent").hasClass('checked')) ? 1 : 0;
    $.ajax({
        url: '/cart/submit_address_form',
        data: data,
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            if (result.html)
                $('.address_list').html(result.html);
            $('#address_block').modal('hide');
        }
    });
}
function load_city() {
    $(':input[name=city]')[0].options.length = 1;
    $(':input[name=district]')[0].options.length = 1;
    var parent_id = $(':input[name=province]').val();
    if (!parent_id)
        return;
    $.ajax({
        url: '/region/load_region',
        data: {parent_id: parent_id, rnd: new Date().getTime()},
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            sel = $(':input[name=city]')[0];
            for (i in result.data) {
                sel.options.add(new Option(result.data[i].region_name, result.data[i].region_id));
            }
        }
    });
    return true;
}

function load_district() {
    $(':input[name=district]')[0].options.length = 1;
    var parent_id = $(':input[name=city]').val();
    if (!parent_id)
        return;
    $.ajax({
        url: '/region/load_region',
        data: {parent_id: parent_id, rnd: new Date().getTime()},
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            if (result.data.length == 0) {
                $(':input[name=district]').css('display', 'none');
            } else {
                $(':input[name=district]').css('display', 'inline-block');
            }
            sel = $(':input[name=district]')[0];
            for (i in result.data) {
                sel.options.add(new Option(result.data[i].region_name, result.data[i].region_id));
            }
        }
    });
    return true;
}

//选择物流公司
$(document).on('click', '.express-xz span.i-radio', function (e) {
    $('.express-xz span').removeClass('checked');
    $(this).addClass('checked');
    var id = $(this).attr('data-id');
    v_shipping_id = id;
    var address_id = $(".address_list .default").attr('id');
    if (address_id == undefined) {
        alert('请选择地址');
	return false;
    }
    v_address_id = parseInt(address_id.replace(/address/, ''));
    get_shipping_fee();
});

function get_shipping_fee(){
    $.ajax({
        url: '/cart/get_shipping_fee2/'+v_shipping_id+'/'+v_address_id+'/0/0/'+v_rec_ids,
        data: {rnd: new Date().getTime()},
        dataType: 'json',
	async:true,
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            $("#shipping_fee").html('￥'+result.data);
            $("#shipping_fee2").html('￥'+result.data);
            unpay_total();
	    
        }
    });    
}
// 计算应付总金额
function unpay_total(){
    var total = parseFloat($('#totalmoney').html().replace('￥', ''));//商品总额
    var shipping_fee = parseFloat($("#shipping_fee2").html().replace('￥', ''));//运费
    var voucher = parseFloat($("#freight").html().replace('￥', ''));//券
    var unpay_amount = total + shipping_fee  - voucher;
    $("#paymoney").html(unpay_amount);
}

var last_cart_submit_time = 0;
var default_pay_id = '<?=$default_pay_id?>';
function submit_cart() {
    if(new Date().getTime() - last_cart_submit_time < 10000){
        alert('请不要重复提交');
        return false;
    }
    // 检查支付方式
    var pay_id = default_pay_id;
    var address_id = $(".address_list .default").attr('id');
    if (address_id == undefined){
        alert('请选择地址！');
	return false;
    }

    address_id = parseInt(address_id.replace(/address/, ''));    
    if(!address_id){
        alert('请选择收货地址');
        return false;
    }
    var shipping_id = $('.express-xz .checked').attr('data-id');
    if(!shipping_id){
        alert('请选择快递公司');
        return false;
    }
    // 收集数据，提交
    var data = {rnd:new Date().getTime(),address_id:address_id};
    //data['use_balance'] = $("#h_user_money_flag").prop('checked')?1:0;
    data['pay_id'] = pay_id;
    data['shipping_id'] = shipping_id;
    //data['invoice'] = $("#h_invoice").html();
    data['remark'] = $("#remark").val();
    last_cart_submit_time = new Date().getTime();
    $.ajax({
        url:'/cart/proc_checkout/0/'+v_rec_ids,
        data:data,
        dataType:'json',
        type:'POST',
        success:function(result){
            last_cart_submit_time = 0;
            if (result.msg) alert(result.msg);
            if (result.url) {location.href=result.url;};
            if (result.err) return false;
            if(result.order_id) location.href='/cart/success/'+result.order_id;
        },
        error:function()
        {
            last_cart_submit_time = 0;
        }
    });
}

function openShutManager(oSourceObj,oTargetObj,shutAble,oOpenTip,oShutTip){
        var sourceObj = typeof oSourceObj == "string" ? document.getElementById(oSourceObj) : oSourceObj;
        var targetObj = typeof oTargetObj == "string" ? document.getElementById(oTargetObj) : oTargetObj;
        var openTip = oOpenTip || "";
        var shutTip = oShutTip || "";
        if(targetObj.style.display!="none"){
           if(shutAble) return;
           targetObj.style.display="none";
           if(openTip  &&  shutTip){
                sourceObj.innerHTML = shutTip; 
           }
        } else {
           targetObj.style.display="block";
           if(openTip  &&  shutTip){
                sourceObj.innerHTML = openTip; 
           }
        }
}

$(".tab-nav-item li").bind("click", function () {

       $(".tab-nav-item li").removeClass("invoice-currt");
       $(this).addClass("invoice-currt");
       var i = $(this).attr("data-value");
       $(".invoice-lb").hide();
       $(".invoice-lb:eq(" + i + ")").show();
});

$(document).on({
    mouseenter: function() { 
        $(this).parents('.fore2').addClass('btn-hover');
    }, 
    mouseleave: function() { 
        $(this).parents('.fore2').removeClass('btn-hover');
    }
}, '.invoice_del');

$(document).on({ 
    mouseenter: function() { 
        $(this).addClass('hover'); 
    }, 
    mouseleave: function() { 
        $(this).removeClass('hover'); 
    }, 
    mouseover: function() { 
        $(".btns").hide();
        $(this).find(".btns").show();
    }, 
    mouseout: function(){
        $(this).find(".btns").hide();
    }
}, '.fore2');
</script>
<?php include APPPATH.'views/common/footer.php'; ?>