<?php include APPPATH."views/common/header2.php"; ?>
<?php $product_desc_additional = (!empty($product->product_desc_additional)) ? json_decode($product->product_desc_additional, true) : array();?>
<!--cart-wrapper start-->
<div class="cart-wrapper">
     <div class="gwc-wrapper">
          <div class="home-wrapper">
                    <div class="cart-title"><img src="<?php echo static_style_url('pc/images/course-order-img1.png')?>"></div>
                    <div class="courselist">
                         <ul class="course-order clearfix">
                         <li><span class="course-name">课程名称</span></li>
                         <li><span class="course-teacher">讲师</span></li>
                         <li><span class="course-time">时间</span></li>
                         <li><span class="course-address">地点</span></li>
                         <li><span class="course-set">座位数</span></li>
                         <li><span class="course-danjia">单价</span></li>
                         </ul> 
                         <ul class="course-contact clearfix">
                             <li>
                             <div class="course-title"><a target="_blank" href="/product-<?php print $product->product_id; ?>.html"><?=$product->brand_name . ' ' . $product->product_name?></a></div>
                             <div class="course-jiangshi"><?=$product->subhead?></div>
                             <div class="course-shijian"><?php echo date("y.m.d", strtotime($product->package_name));?> <?php //if (isset($product_desc_additional['desc_waterproof'])) echo '-' . date("y.m.d", strtotime($product_desc_additional['desc_waterproof']))?>
                             </div>
                             <div class="course-baoming"><?php if(isset($product_desc_additional['desc_material'])) echo $product_desc_additional['desc_material']?></div>
                             <div class="counter">
                                  <div class="counter-wrapper fl-right">
                                       <span name="down" class="minus cart-num fl-left down" data-id="<?php print $product->sub_id; ?>">-</span>      
                                       <input type="text" onblur="j_change_num(this)" id="qty_<?php print $product->sub_id; ?>" min="1" max="<?=$product->sale_num?>" value="1" maxlength="5" class="num fl-left">
                                       <span name="up" class="plus cart-num fl-left up" data-id="<?php print $product->sub_id; ?>">+</span> 
                                  </div>
	                        </div>
                             <div class="course-sprice">￥<span id="h_goods_price"><?=$product->product_price?></span>/<?=$product->unit_name?></div>
                            </li>
                        </ul>
                  </div>
                 
                  <div class="fill-info">
                       <p class="fill-info-tit">填写凭证信息</p>
                       <ul class="fill-info-lb">
                       <li>手机号码（接收上课凭证码）：<input id="mobile" type="text"><span class="fill-empty"><i></i>无效电话号码</span></li>
                       <li>电子邮箱（接收上课凭证码）：<input id="email" type="text"></li>
                       <li>真实姓名（接收上课凭证码）：<input id="consignee" type="text"></li>
                       </ul>
                 </div>
                 <p class="fill-info-yf">应付总金额：<span id="h_total_price">￥<?=$product->product_price?></span></p>
            </div>
      </div>
      
      
      <div class="home-wrapper clearfix">
          <!--
             <div class="send fill-cur">【成都】【预约报名】正畸课程实训落地实验课<p>讲师：赵廷旺<span>时间：2016/1/3</span></p><p>地点：成都</p></div>
          -->
           <a href="javascript:void(0);" class="submit-button fl-right" onclick="submit_cart();">确认提交</a>
      </div>
</div>
<!--cart-wrapper end-->
<script type="text/javascript">
function j_total_price(goods_price){
    var v_price = parseFloat($("#h_goods_price").html())*goods_price;
    $("#h_total_price").html('￥'+v_price.toFixed(2));
}
//手动修改购买数量
function j_change_num(obj){
    var v_obj = $(obj);
    var v_obj_val = parseInt(v_obj.val());
    var v_obj_val_max = parseInt(v_obj.attr('max'));
    var v_obj_val_min = parseInt(v_obj.attr('min'));
    v_edit_flag = true;
    if (v_obj_val <= v_obj_val_max && v_obj_val >= v_obj_val_min){
        return false;
    }
    
    v_obj_val = (v_obj_val > v_obj_val_max) ? v_obj_val_max : v_obj_val_min;  
    v_obj.val(v_obj_val);
    j_total_price(v_obj_val);
    //var v_price = parseFloat($("#h_goods_price").html())*v_obj_val;
    //$("#h_total_price").html('￥'+v_price.toFixed(2));
};
//商品数量+1
$(document).on('click', '.up', function (e) {
    var rec_id = $(this).attr('data-id');
    var v_obj = $("#qty_"+rec_id);
    var v_obj_val = parseInt(v_obj.val());
    var v_obj_val_max = parseInt(v_obj.attr('max'));
    if (v_obj_val >= v_obj_val_max){
        v_obj.val(v_obj_val_max);
        j_total_price(v_obj_val_max);
        return false;
    }
    v_edit_flag = true;
    v_obj.val(v_obj_val+1);
    j_total_price(v_obj_val+1);
});
//商品数量-1
$(document).on('click', '.down', function (e) {
    var rec_id = $(this).attr('data-id');
    var v_obj = $("#qty_"+rec_id);
    var v_obj_val = parseInt(v_obj.val());
    var v_obj_val_max = parseInt(v_obj.attr('max'));
    if (v_obj_val <= 1){
        v_obj.val(1);
        j_total_price(1);
        return false;
    }
    v_edit_flag = true;
    v_obj.val(v_obj_val-1);
    j_total_price(v_obj_val-1);
});

var last_cart_submit_time = 0;
var sub_id = '<?php print $product->sub_id; ?>';
var genre_id = '<?=$genre_id?>';
function submit_cart() {
    if(new Date().getTime() - last_cart_submit_time < 10000){
        alert('请不要重复提交');
        return false;
    }

    var num = $("#qty_"+sub_id).val();
    var v_mobile = $("#mobile").val();
    var v_consignee = $("#consignee").val();
    var v_email = $("#email").val();
    //var v_address = $$("#address").val();
    //var v_company = $$("#company").val();
    //var v_remark = $$("#remark").val();
    if (v_mobile == ''){
        alert('请填写手机号');
        return false;
    }
    

    if (v_mobile.length != 11){
        alert('手机号不正确');
        return false;
    }
    
    var mobileReg = !!v_mobile.match(/^(13[0-9]|15[0-9]|17[678]|18[0-9]|14[57])[0-9]{8}$/);
    if (mobileReg == false){
        alert('手机号不正确');
        return false;
    }
    
    if (v_consignee == ''){
        alert('请填写真实姓名');
        return false;
    }

    // 收集数据，提交
    var data = {rnd:new Date().getTime(),sub_id:sub_id};
    data['num'] = num;
    data['mobile'] = v_mobile;
    data['consignee'] = v_consignee;
    data['email'] = v_email;
    //data['address'] = v_address;
    //data['company'] = v_company;
    //data['remark'] = v_remark;
    last_cart_submit_time = new Date().getTime();
    $.ajax({
        url:'/cart/proc_checkout_course',
        data:data,
        dataType:'json',
        type:'POST',
        success:function(result){
            last_cart_submit_time = 0;
            if (result.msg) alert(result.msg);
            if (result.url) {location.href=result.url;};
            if (result.err) return false;
            if(result.order_id) location.href='/cart/success/'+result.order_id+'/'+genre_id; 
        },
        error:function()
        {
            last_cart_submit_time = 0;
        }
    });
}
</script>
<?php include APPPATH.'views/common/footer.php'; ?>