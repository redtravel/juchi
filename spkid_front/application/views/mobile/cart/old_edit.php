<?php include APPPATH."views/mobile/header.php"; ?>
<style type="text/css">
    .sel_size_yes{
        padding:5px 20px;
        margin:10px 5px;
    }
    .sel_size_no{
        padding:5px 20px;
        margin:10px 5px;
        background-color: #f0f0f0;
    }
</style>
<div class="views">
<div class="view view-main" data-page="cart">
    
<div class="navbar">
    <div class="navbar-inner">
        <div class="left">
            <a href="#" class="link icon-only">
                <i class="icon icon-back"></i>
            </a>
        </div>
        <div class="center">购物车(<?php print $cart_summary['product_num']; ?>)</div>
        <div class="right">
            <a href="#" class="open-popover">
                完成
            </a>
        </div>
    </div>
</div> 
<!-- 底部工具栏开始 -->    
<div class="toolbar">
    <div class="toolbar-inner">
           <div class="list-block">
               <ul>
                    <li>
                        <label class="label-checkbox item-content">
                            <input type="checkbox" name="chk_all" style="margin-right: 10px;"/>
                            <div class="item-media">
                                <i class="icon icon-form-checkbox"></i>
                                <span style="margin-left: 10px;">全选</span>
                            </div>
                        </label>
                    </li>
               </ul>
           </div>
        <a href="#" class="link cart_del">批量删除</a>
        <a href="#" class="link"></a>
    </div>
</div>
<!-- 底部工具栏结束 -->   
<div class="page-content">
    <?php foreach ($cart_summary['product_list'] as $provider): ?>
    <div><?php print $provider['provider_name'] ?></div>
    <div class="list-block media-list">
        <ul>
            <?php foreach ($provider['product_list'] as $product): ?>
            <li>
                <label class="label-checkbox item-content">

                 <input type="checkbox" name="sub_id[]" value="<?php print $product->rec_id; ?>" style="margin-right: 20px;"/>
                 <div class="item-media">
                     <i class="icon icon-form-checkbox"></i>
                     <img src="<?php print img_url($product->img_url); ?>.85x85.jpg" alt="<?php print $product->product_name; ?>"  width="85" height="85" />

                  </div>
                <div class="item-inner">
                  <div class="item-subtitle"><?php print $product->product_name; ?></div>
                  <div class="item-title-row" style="background-image:url();">
                      <div class="item-title">规格：<span class="c_size<?=$product->rec_id?>" data-subid="<?=$product->sub_id;?>"><?php print $product->size_name; ?><span></div>
                    <div class="item-after edit_size" style="font-weight: bold;" data-recid="<?php print $product->rec_id; ?>">V</div>
                  </div>

                  <div class="item-title-row" style="background-image:url();">
                    <div class="down" data-recid="<?php print $product->rec_id; ?>">-</div>
                    <div><input type="number"name="num[]" id="qty_<?php print $product->rec_id; ?>" min="1" max="<?=$cart_goods_buy_num?>" step="1" value="<?php print $product->product_num; ?>"></div>
                    <div class="up" data-recid="<?php print $product->rec_id; ?>">+</div>
                  </div>

                  <div class="item-subtitle" id="money_<?php print $product->rec_id; ?>">￥<?php print fix_price($product->shop_price); ?></div>
                </div>

                <div>


                    <input type="button" value="删除" style="width:50px;height:70px; background-color:#f9221d;border:0px;color:#ffffff;font-size:14px;" class="cart_del" data-recid="<?php print $product->rec_id; ?>"/>

                </div>


                </label>    
            </li>
          <?php endforeach; ?>
        </ul>
    </div>
    <?php endforeach; ?>
</div>   
            
            
            

</div>

</div>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/framework7.min.js')?>"></script>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/yyw-app.js')?>"></script>
<script type="text/javascript">
//手动修改购买数量
$$(document).on('blur', "input[type=number][name='num[]']", function (e) {
    var v_obj = $$(this);
    var v_obj_val = parseInt(v_obj.val());
    var v_obj_val_max = parseInt(v_obj.attr('max'));
    var v_obj_val_min = parseInt(v_obj.attr('min'));
    if (v_obj_val <= v_obj_val_max && v_obj_val >= v_obj_val_min){
        return false;
    }
    
    v_obj_val = (v_obj_val > v_obj_val_max) ? v_obj_val_max : v_obj_val_min;  
    v_obj.val(v_obj_val);
});
//商品数量+1
$$(document).on('click', '.up', function (e) {
    var rec_id = $$(this).attr('data-recid');
    var v_obj = $$("#qty_"+rec_id);
    var v_obj_val = parseInt(v_obj.val());
    var v_obj_val_max = parseInt(v_obj.attr('max'));
    if (v_obj_val >= v_obj_val_max){
        v_obj.val(v_obj_val_max);
        return false;
    }
    v_obj.val(v_obj_val+1);
});
//商品数量-1
$$(document).on('click', '.down', function (e) {
    var rec_id = $$(this).attr('data-recid');
    var v_obj = $$("#qty_"+rec_id);
    var v_obj_val = parseInt(v_obj.val());
    var v_obj_val_max = parseInt(v_obj.attr('max'));
    if (v_obj_val <= 1){
        v_obj.val(1);
        return false;
    }
    v_obj.val(v_obj_val-1);    
});
//全选/返选
$$("input[type=checkbox][name=chk_all]").on('click', function(){
    var isChecked = $$(this).prop("checked");
    $$("input[type=checkbox][name='sub_id[]']").each(function(){
        if(isChecked){
            this.checked = true;
        } else {
            this.checked = false;
        }
    });
});
//删除购物车中商品
$$(document).on('click', '.cart_del', function (e) {
    var rec_id = $$(this).attr('data-recid');
    if (rec_id == null){
        var ischk = false;
        var recObj = new Array();
        $$("input[type=checkbox][name='sub_id[]']:checked").each(function(){
            ischk = true;
            recObj.push(this.value);
        });
        
        if (ischk == false) {
            myApp.alert('请选择商品！', '');
            return false;
        }    
        myApp.confirm('确定从购物车中删除此商品吗？', '', function () {
            $$.each(recObj, function(idx, value){
                delete_cart(value);
            });           
        });       
    } else {
        myApp.confirm('确定从购物车中删除此商品吗？', '', function () {
            delete_cart(rec_id);
        });        
    }
});
//删除购物车中商品
function delete_cart(rec_id)
{
    $$.ajax({
        url: '/cart/remove_from_cart',
        data: {rec_id: rec_id, rnd: new Date().getTime()},
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.msg)
                alert(result.msg);
            if (result.err)
                return false;
            location.href = location.href;
        }
    });
}
// 修改商品规格

$$(document).on('click', '.edit_size', function (e) {
    var rec_id = $$(this).attr('data-recid');
    if ($$('.picker-modal.modal-in').length > 0) {
      myApp.closeModal('.picker-modal.modal-in');
    }
    $$.ajax({
        url: '/cart/size_edit',
        data: {rec_id: rec_id, rnd: new Date().getTime()},
        dataType: 'json',
        type: 'POST',
        success: function(result) {
            if (result.err == 0) {
                myApp.pickerModal(result.html);
            }           
        }
    });  
});

//选择规格
var sel_size_yes = function(id,e){console.log(id)

 var el = $$('#rec_id'+id);
    var gid = id;
    var size_name = el.html();
    var sub_id = $$("#c_sub_id").val();

    // 检查所选择的规格是否已存在于购物车，如果存在，关闭层，提示用户已存在
    var is_exists = false;
    $$("input[type=checkbox][name='sub_id[]']").each(function(){
        var v_sub_id = $$(".c_size"+this.value).attr('data-subid');
        if (sub_id != gid && gid == v_sub_id)
            is_exists = true;
    });
    if (is_exists){
        if ($$('.picker-modal.modal-in').length > 0) {
            myApp.closeModal('.picker-modal.modal-in');
            myApp.alert('此规格已存在！', '');
            return false;
        }
    }
    $$(".sel_size_yes").css('background-color', '#ffffff');
    el.css('background-color', '#f9221d');
    $$("#sel_sub_id").val(gid);
    $$(".sel_size").html(size_name);
}


//修改规格确认
//$$(".size_edit_cfm").on('click', function(){
function size_edit_cfm(){
    var d_rec_id = $$("#c_rec_id").val();
    var a_sub_id = $$("#sel_sub_id").val();
    $$.ajax({
            url: '/cart/size_edit_proc',
            data: {rec_id: d_rec_id, sub_id: a_sub_id, num: 1, rnd: new Date().getTime()},
            dataType: 'json',
            type: 'POST',
            success: function(result) {
                if (result.msg) {
                    myApp.alert(result.msg, '');
                }
                if (result.err)
                    return false;
                $$(".c_size"+d_rec_id).html($$(".sel_size").html());
                $$(".c_size"+d_rec_id).attr('data-subid', a_sub_id);

                var v_html = $$(".page-content .list-block.media-list").html().replace(eval("/"+d_rec_id+"/g"),result.rec_id);
                $$(".page-content .list-block.media-list").html(v_html);
                myApp.closeModal('.picker-modal.modal-in');
            }
    });
}
//});
//点击完成
$$('.open-popover').on('click', function () {
    mainView.router.back();
    /*var num;
    var rec_id;
    $$("input[type=checkbox][name='sub_id[]']").each(function(){
           var rec_obj = $$(this),
            rec_id = rec_obj.val(),
            num = parseInt($$("#qty_"+rec_id).val())||1;
            $$.ajax({
            url: '/cart/update_cart',
            data: {rec_id: rec_id, num: num, rnd: new Date().getTime()},
            dataType: 'json',
            type: 'POST',
            success: function(result) {
                if (result.msg) {
                    var popoverHTML = '<div class="popover popover-about">'+
                        '<div class="popover-angle"></div>'+
                        '<div class="popover-inner">'+
                          '<div class="content-block">'+
                            '<p>'+result.msg+'</p>'+
                          '</div>'+
                        '</div>'+
                    '</div>';
                    myApp.popover(popoverHTML, $$("#money_"+rec_id));
                }
                if (result.err)
                    return false;
            }
        });
    });*/
});
</script>
</body>
</html>