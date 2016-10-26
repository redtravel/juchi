<?php include APPPATH."views/common/header2.php"; ?>

<!--cart-wrapper start-->
<div class="cart-wrapper">
     <div class="gwc-wrapper">
          <div class="home-wrapper">
                    <div class="cart-title">全部商品(共<span id="cart_num"><?php print $cart_summary['product_num']; ?></span>件)</div>
                    <div class="cartlist">
                         <ul class="cartlb clearfix">
                             <li><span><em class="selectall i-checkbox i<?php if(!empty($cart_summary['product_list'])): ?> checked<?php endif; ?>"></em>全选</span><span class="goods-name">商品信息</span></li>
                             <li class="goods-price">单价</li>
                             <li><span class="goods-num">数量</span><span class="goods-money">小计</span></li>
                             <li class="operate">操作</li>
                         </ul> 
                         <ul class="cart-main clearfix">
                             <!--购物车有内容的时候开始-->
                             <?php if(!empty($cart_summary['product_list'])): ?>
                             <?php foreach ($cart_summary['product_list'] as $provider): ?>
                             <?php foreach ($provider['product_list'] as $product): ?>
                             <li class="goods-first bulky c_rec<?=$product->rec_id?>">
                                 <div class="item-table">
                                       <span class="chooseone i-checkbox i checked" data-sid="<?php print $product->rec_id; ?>"></span>
                                       <a class="goods-info" target="_blank" href="/pdetail-<?php print $product->product_id; ?>.html">
                                       <div class="goods-img item-table">
                                           <img src="<?php print img_url($product->img_url); ?>.85x85.jpg" alt="<?php print $product->brand_name . ' ' .$product->product_name; ?>" />
                                       </div>
                                       <div class="goods-desc item-table">
                                            <p class="goods-name"><?php print $product->product_name; ?></p>
                                            <p class="goods-attr"><span class="c_size<?=$product->rec_id?>" data-subid="<?=$product->sub_id;?>" ><?php print $product->size_name; ?></span></p>
                                        </div>
                                      </a> 
                                </div>
                                
                                <div class="goods-price item-table"><span>￥<em class="bprice"><?php print fix_price($product->product_price); ?></em></span></div>
                                
                                <div class="counter item-table">
                                    <div class="counter-wrapper fl-right">
                                         <span name="down" class="minus cart-num fl-left down" data-recid="<?php print $product->rec_id; ?>">-</span>
                                         <input type="text" class="num fl-left" onblur="j_change_num(this)" id="qty_<?php print $product->rec_id; ?>" value="<?php print $product->product_num; ?>">
                                         <span name="up" class="plus cart-num fl-left up" data-recid="<?php print $product->rec_id; ?>">+</span> 
                                    </div>
	                               <div class="tips" style="display: block;"></div>
	                          </div>
                              
                              <div class="goods-money item-table">￥<span><em class="total" id="product_total_<?php print $product->rec_id; ?>"><?php print fix_price($product->product_price * $product->product_num); ?></em></span></div>
                              <div class="operate item-table">
                                   <a class="single-del cart_del" href="javascript:void(0);" data-recid="<?php print $product->rec_id; ?>">删除</a>
                                   <a id="collect_<?php print $product->rec_id; ?>" class="collect" href="javascript:void(0);" data-pid="<?php print $product->product_id; ?>-<?php print $product->rec_id; ?>" title="" data-container="body" 
      data-toggle="popover" data-placement="bottom" 
      data-content="√ 成功移到我的关注！">移到我的关注</a>
                              </div>
                             </li>
                             <?php endforeach; ?>
                             <?php endforeach; ?>
                             <!--购物车有内容的时候结束-->
                             <?php else: ?>                            
                            <!--购物车没有内容的时候开始-->
                            <li class="empty-cart empty-line">您的购物车还是空的，赶紧去<a href="/"><span>购物</span></a>吧！</li>
                            <!--购物车没有内容的时候结束-->
                            <?php endif; ?>
                         </ul>  
                         <?php if(!empty($cart_summary['product_list'])): ?>
                         <div class="cart-statement clearfix" style="display: block;">
                                <div class="fl-left">
                                    <span><em class="selectall i-checkbox i checked"></em>全选</span>
                                    <a href="javascript:void(0);" class="shanchun cart_del">删除选中商品</a>
                                    <a href="#" style="display: none;">清除失效商品</a>
                                </div>
                                <div class="fl-right">
                                    <span class="fl-left">已选<i class="buy-count" id="product_sel_num"><?=$cart_summary['product_num']?></i>件商品</span>
                                    <span class="fl-left"><em class="fl-left">总计金额（不含运费）：</em><i class="amounted" id="total_price">¥ <?=$cart_summary['product_price']?></i></span>
                                    <span type="submit" class="payment settle-accounts" id="cart_checkout">去结算</span>
                                </div>  
                        </div>
                        <?php endif; ?>
                    </div>              
               </div>   
     </div>
</div>         
<!--cart-wrapper end-->

<!--associated-goods start-->
<div class="gwc-wrapper">
    <div class="home-wrapper">
         <div class="associated-goods">
               
               <ul class="associated-title clearfix">
                    <li data-value="0" class="current">关联商品</li>
                    <li data-value="1">牙医们喜欢</li>
               </ul>
               
               <ul class="associated-pic clearfix">
               <?php foreach($relation_goods as $rg): ?>
               <li><a href="/pdetail-<?php print $rg->product_id; ?>.html"><img src="<?php print img_url($rg->img_url); ?>.140x140.jpg"><p class="associated-sprice">￥<?=($rg->is_promote) ? $rg->promote_price : $rg->shop_price ?></p><p class="associated-mc"><?php print $rg->product_name; ?></p></a>
               </li>
               <?php endforeach; ?>
               </ul>
             
               <ul class="associated-pic clearfix" style="display:none;">
               <?php foreach($hot_goods as $hg): ?>
               <li><a href="/pdetail-<?php print $hg->product_id; ?>.html"><img src="<?php print img_url($hg->img_url); ?>.140x140.jpg"><p class="associated-sprice">￥<?=($hg->is_promote) ? $hg->promote_price : $hg->shop_price ?></p><p class="associated-mc"><?php print $hg->product_name; ?></p></a>
               </li>
               <?php endforeach; ?>
               </ul>   
       </div>
      
   </div>
</div>
<!--associated-goods-end-->

<!-- 删除弹层1开始 -->
<div id="dcart-box1" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header v-close">
              <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">请选择您要删除的商品</h4>
          </div>
          <div class="modal-body v-button">
              <button class="btn btn-lg btn-blue" type="submit" data-dismiss="modal">确定</button>
              <button class="btn cancel " type="submit" data-dismiss="modal">取消</button>
          </div>
        </div>
      </div>
</div>
<!-- 删除弹层1结束 -->

<!-- 删除弹层2开始 -->
<div id="dcart-box2" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header v-close">
              <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">您确定要删除所选商品吗？</h4>
          </div>
          <div class="modal-body v-button">
              <button class="btn btn-lg btn-blue cart_del_cfm" type="submit">确定</button>
              <button class="btn cancel " type="submit" data-dismiss="modal">取消</button>
          </div>
        </div>
      </div>
</div>
<!-- 删除弹层2结束 -->

<!-- 关注层开始 -->
<div id="collect-box" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header v-close">
              <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">移动后选中商品将不在购物车中显示。</h4>
          </div>
          <div class="modal-body v-button">
              <button class="btn btn-lg btn-blue collect_cfm" type="submit">确定</button>
              <button class="btn cancel " type="submit" data-dismiss="modal">取消</button>
          </div>
        </div>
      </div>
</div>
<!-- 关注层结束 -->
<script type="text/javascript">
var v_cart_buy_num = '<?=$cart_goods_buy_num?>';

$(".associated-title li").bind("click", function () {
    $(".associated-title li").removeClass("current");
    $(this).addClass("current");
    var i = $(this).attr("data-value");
    $(".associated-pic").hide();
    $(".associated-pic:eq(" + i + ")").show();
});

//全选复选框
$(".selectall").click(function(){
    //已选中
    if ($(this).hasClass('checked')){
        $('.selectall').removeClass('checked');
        $(".chooseone").removeClass('checked');
    } else {
        $('.selectall').addClass('checked');
        $(".chooseone").addClass('checked');
    }
    j_goods_cnt();
});

//单选复选框
$(".chooseone").click(function(){
    if ($(this).hasClass('checked')){
        $(this).removeClass('checked');
    } else {
        $(this).addClass('checked');
    }
    j_goods_cnt();
});

function j_goods_cnt(){
    var total_num = 0;
    var total_price = 0.00;
    $(".chooseone.checked").each(function(){
        var v_sid = $(this).attr('data-sid');
        total_num += parseInt($("#qty_"+v_sid).val());
        total_price += parseFloat($("#product_total_"+v_sid).html());        
    });
    $("#product_sel_num").html(total_num);
    $("#total_price").html('¥ '+total_price.toFixed(2));
    if (total_num > 0) {
        $("#cart_checkout").addClass("settle-accounts");
    } else {
        $("#cart_checkout").removeClass("settle-accounts");
    }
    //$("#cart_checkout").toggleClass("settle-accounts");
}

//去结算
$("#cart_checkout").click(function(){
    var v_sid_str = '';
    $(".chooseone.checked").each(function(){
        v_sid_str += "-"+$(this).attr('data-sid');
    });
    if (v_sid_str.length <= 0) return;
    window.location.href = '/cart/checkout/0/'+v_sid_str.substr(1);
});
//商品数量+1
$(document).on('click', '.up', function (e) {
    var rec_id = $(this).attr('data-recid');
    var v_obj = $("#qty_"+rec_id);
    var v_obj_val = parseInt(v_obj.val());
    if (v_obj_val >= v_cart_buy_num){
        v_obj.val(v_cart_buy_num);
        j_qtyEdit(rec_id);
        return false;
    }
    v_obj.val(v_obj_val+1);
    j_qtyEdit(rec_id);
});
//商品数量-1
$(document).on('click', '.down', function (e) {
    var rec_id = $(this).attr('data-recid');
    var v_obj = $("#qty_"+rec_id);
    var v_obj_val = parseInt(v_obj.val());
    if (v_obj_val <= 1){
        v_obj.val(1);
        j_qtyEdit(rec_id);
        return false;
    }
    v_obj.val(v_obj_val-1);
    j_qtyEdit(rec_id);
});
//手动修改购买数量
function j_change_num(obj){
    var v_obj = $(obj);
    var v_rec_id = $(obj).attr("id").replace(/qty_/i, "");
    var v_obj_val = parseInt(v_obj.val());
    if (v_obj_val <= v_cart_buy_num && v_obj_val >= 1){
        j_qtyEdit(v_rec_id);
        return false;
    }
    
    v_obj_val = (v_obj_val > v_cart_buy_num) ? v_cart_buy_num : 1;  
    v_obj.val(v_obj_val);
    j_qtyEdit(v_rec_id);
    
};
//修改购物车数量
function j_qtyEdit(rec_id) {
    var num = parseInt($("#qty_" + rec_id).val())||1;
    $.ajax({
        url: '/cart/update_cart',
        data: {rec_id: rec_id, num: num, rnd: new Date().getTime()},
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            $("#product_total_"+rec_id).html((result.cart.product_num*result.cart.product_price).toFixed(2));
            $("#product_sel_num").html(result.cart_summary.product_num);
            $("#total_price").html(result.cart_summary.product_price);
            update_cart_num();
        }
    });	
}
var recObj = [];
//删除购物车中商品
$(document).on('click', '.cart_del', function (e) {
    
    var rec_id = $(this).attr('data-recid');
    recObj = [];
    if (rec_id == null){
        var ischk = false;
               
        $(".chooseone.checked").each(function(){
            ischk = true;
            recObj.push(parseInt($(this).attr('data-sid')));
        });
         
        if (ischk == false) {
            $('#dcart-box1').modal('show');
            return false;
        }     
    } else {
        recObj.push(rec_id);            
    }
    $('#dcart-box2').modal('show');
});

//点击删除弹框层2中“确认”按钮
$(".cart_del_cfm").click(function(){
    if (recObj.length < 1) return;    
    $.each(recObj, function(idx, value){
        delete_cart(value);
    });
});

//删除购物车中商品
function delete_cart(rec_id)
{
    $.ajax({
        url: '/cart/remove_from_cart',
        data: {rec_id: rec_id, rnd: new Date().getTime()},
        dataType: 'json',
	async:true,
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            var goods_cnt = $(".c_rec"+rec_id).parent().children("tr").length;
            
	    $(".c_rec"+rec_id).remove();
            if (goods_cnt <= 1) 
                location.href = '/cart';
            update_cart_num();
        }
    });
}
var v_collect_pid = '';
//关注
$(document).on('click', '.collect', function (e) {   
    v_collect_pid = $(this).attr('data-pid');
    $("#collect-box").modal('show');
    
});
//关注确认
$(".collect_cfm").click(function(){
    if (v_collect_pid == '') return;
    var pid_arr = v_collect_pid.split("-");
    set_collect(pid_arr[0]);
    $("#collect-box").modal('hide');
    $("#collect_"+pid_arr[1]).popover('show');  
    setTimeout(function(){delete_cart(pid_arr[1])}, 1000);
});

function set_collect(pid){
    $.ajax({
        url: '/product_api/add_to_collect',
        data: {product_id: pid, product_type:0, rnd: new Date().getTime()},
        dataType: 'json',
	async:true,
        type: 'POST',
        success: function(result) {
            /*if (result.msg)
                alert(result.msg);*/
            if (result.err)
                return false;
        }
    });
}

function update_cart_num () {
    var cart_num=getCookie('cart_num');
    if(cart_num) {
        $('#cart_num').html(cart_num);
    }
}
</script>
<?php include APPPATH.'views/common/footer.php'; ?>