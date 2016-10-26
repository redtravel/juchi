<?php include APPPATH."views/mobile/header.php"; ?>
<style>

input, select, textarea {
    -webkit-tap-highlight-color: rgba(0,0,0,0);
    -webkit-appearance: none;
    border: 0;
    border-radius: 0;
}
.list-block .item-content{ padding-left:10px;}
.list-block .item-media + .item-inner{ margin-left:5px;}
label.label-checkbox i.icon-form-checkbox{
width: 25px;
height: 25px;
border-radius: 22px;
border: 1px solid #fff;
}

input[type="radio" i], input[type="checkbox" i] {
    margin: 0;
    padding: initial;
    background-color: initial;
    border: initial;
}
.list-block .item-media + .item-inner{ margin-left:0;}

label.label-checkbox i.icon-form-checkbox:after {
  content: ' ';
  position: absolute;
  width: 25px;
  height: 25px;
  left: -2px;
  top: -2px;
  -webkit-transition-duration: 300ms;
  transition-duration: 300ms;
  opacity: 0;
  background: no-repeat center;
  background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20fill%3D'%23ffffff'%20width%3D'24'%20height%3D'24'%20viewBox%3D'0%200%2024%2024'%3E%3Cpath%20d%3D'M9%2016.17L4.83%2012l-1.42%201.41L9%2019%2021%207l-1.41-1.41z'%2F%3E%3C%2Fsvg%3E");
  -webkit-background-size: 100% auto;
  background-size: 100% auto;
}
.toolbar:not(.messagebar) ~ .page-content {
  padding-top: 26px;
}

</style>
<div class="views">
    <div class="view view-main">
        <div class="pages">
            <!-- 购物车默认页面开始-->
            <div class="page" data-page="cart-index">
                <div class="navbar">
                    <div class="navbar-inner">
                        <div class="left"><a href="#" class="link icon-only history-back"><i class="icon icon-back"></i></a></div>
                        <div class="center">购物车(<?php print $cart_summary['product_num']; ?>)</div>
                        <div class="right" style="margin-right:8px;"><a href="#cart-edit" style="color:#e1e1e1;">编辑</a>
                        </div>
                    </div>
                </div> 
            	<!-- 底部工具栏开始 -->    
                <div class="toolbar">
                    <div class="toolbar-inner row no-gutter hu-cart-settlement">
                        <div class="col-60"><a href="#" class="link"><em style="color:#333; font-style:normal; font-size:1em;">合计：</em><span class="heji-car-hu" id="h_total_price">￥<?php print fix_price($cart_summary['product_price']); ?></span><span style="color:#666;">不含运费</span></a></div>
                        <div class="col-40 settlemen-hu"><a href="/cart/checkout/<?=$default_provider?>" class="link external" style="color:#fff;">结算(<span id="h_total_num"><?php print $cart_summary['product_num']; ?></span>)</a></div>
                    </div>
                </div>
            	<!-- 底部工具栏结束 -->    
                <div class="page-content article-bg no-top2">
		     		<div class="page-content-inner edu-fot">
	                    <div class="list-block media-list no-top" style="margin-top:25px;">
	                    	<!-- <div class="v-activity-postage">购满200元包邮</div> -->			    
	                        <ul class="hu-cart-shops">
							<?php foreach ($cart_summary['product_list'] as $provider): ?>
	                          	<?php foreach ($provider['product_list'] as $product): ?>
	                          	<li class="c_rec<?=$product->rec_id?>">
	                              	<a href="#" class="item-link item-content">
		                                <div class="item-media col-v-img">
		                                    <img src="<?php print img_url($product->img_url); ?>.85x85.jpg" alt="<?php print $product->brand_name . ' ' . $product->product_name; ?>"  />
		                                </div>
		                              	<div class="item-inner">
			                                <div class="public-text"><?php print $product->brand_name . ' ' . $product->product_name; ?></div>
			                                <div class="item-text hu-gwc">规格：<span class="c_size<?=$product->rec_id?>" data-subid="<?=$product->sub_id;?>"><?php print $product->size_name; ?><span></div>
			                                <div class="hu-cart-nobg clearfix" style="padding-top:20px;">
                                                <div class="hu-cart-number">X<span><?php print $product->product_num;?></span></div>
			                                  	<div class="guanzhu-jiage">￥<?php print fix_price($product->product_price); ?></div>
			                                  	
			                                </div>          
		                              	</div>
	                              	</a>
	                          	</li>
	                          	<?php endforeach; ?>
				  			<?php endforeach; ?>
	                        </ul>
	                    </div>
	                </div>
				</div>
            </div>
            <!-- 购物车默认页面结束-->
            
	    <!-- 购物车编辑页面开始-->
            <div class="page cached" data-page="cart-edit">
                <div class="navbar">
                    <div class="navbar-inner">
                        <div class="left">
                            <a href="#" class="link icon-only back">
                                <i class="icon icon-back"></i>
                            </a>
                        </div>
                        <div class="center">购物车(<?php print $cart_summary['product_num']; ?>)</div>
                        <div class="right" style="margin-right:8px;"><a href="#" class="open-popover" style="color:#e1e1e1;">完成</a></div>
                    </div>
                </div> 
                
		
		
		<!--
		<div class="toolbar">
                    <div class="toolbar-inner row no-gutter hu-cart-settlement">
		        <div class="col-60 hu-select" style="margin-top:-10px;">
			<label class="label-checkbox item-content">
			<input type="checkbox" name="chk_all"/>
			<div class="item-media" id="h_chk_all"><i class="icon icon-form-checkbox" style="float:left; margin:0;"></i></div>
			 <div class="hu-selects">全选</div>
			
			</label>
			</div>
			<div class="col-40 ">批量删除</div>
                    </div>
                </div>
		-->
		
		 
                <div class="page-content article-bg no-top2">
		    <div class="page-content-inner no-top edu-fot">
                   
                    <!--
                    <div><?php print $provider['provider_name'] ?></div>
                    -->
                    <div class="list-block media-list no-top" style="margin-top:55px;">
                        <ul class="hu-cart-shops">
			 <?php foreach ($cart_summary['product_list'] as $provider): ?>
                            <?php foreach ($provider['product_list'] as $product): ?>
                            <li class="c_rec<?=$product->rec_id?>">
                                <a href="#" class="item-link item-content">
                                 <input type="hidden" name="sub_id[]" value="<?php print $product->rec_id; ?>"/>
                                 <!--
                                 <label class="label-checkbox item-content">
                                 <input type="checkbox" name="sub_id[]" value="<?php print $product->rec_id; ?>"/>
                                 -->
				 <div class="item-media col-v-img">
                                 <!--
				 <i class="icon icon-form-checkbox"></i>
                                 -->
				 <img src="<?php print img_url($product->img_url); ?>.85x85.jpg" alt="<?php print $product->brand_name . ' ' .$product->product_name; ?>" />
                                 </div>
                                <div class="item-inner" style="flex-basis:60%;  -webkit-flex-basis:60%; -moz-flex-basis:60%;">
                                  <div class="public-text"><?php print $product->product_name; ?></div>
                                  <div class="item-title-row" style="border-bottom:solid 1px #a1a1a1; padding-bottom:5px;">
                                      <div class="item-title" style="color:#000; padding-top:5px;">规格：<span class="c_size<?=$product->rec_id?>" data-subid="<?=$product->sub_id;?>" ><?php print $product->size_name; ?><span></div>
                                    <div class="item-after edit_size" style="font-weight: bold;" data-recid="<?php print $product->rec_id; ?>"></div>
                                  </div>
                                  <div class="item-title-row" style="border-bottom:solid 1px #a1a1a1; padding-bottom:5px;">
                                    <div class="down" data-recid="<?php print $product->rec_id; ?>"></div>
                                    <div><input type="number" onblur="j_change_num(this)" id="qty_<?php print $product->rec_id; ?>" min="1" max="<?=$cart_goods_buy_num?>" step="1" value="<?php print $product->product_num; ?>" style="color:#666; text-align:center; padding-top:5px;"></div>
                                    <div class="up" data-recid="<?php print $product->rec_id; ?>"></div>
                                  </div>
                                  <div class="item-subtitle guanzhu-jiage2" style="font-size:1em; padding-left:0" id="money_<?php print $product->rec_id; ?>">￥<?php print fix_price($product->product_price); ?></div>
                                </div>
                                <div style="flex-basis:15%; -webkit-flex-basis:15%; -moz-flex-basis:15%; ">
                                    <input type="button" value="删除" style="width:100%; height:12em; background-color:#f9221d; color:#ffffff;font-size:1em; text-align:center; " class="cart_del" data-recid="<?php print $product->rec_id; ?>"/>
                                </div>
                                 <!--
                                </label>
                                 -->
                                </a>
                            </li>
                          <?php endforeach; ?>
			  <?php endforeach; ?>
                        </ul>
                    </div>
                    
                </div>
		</div>               
            </div>
            <!-- 购物车编辑页面结束-->
        </div>
<!-- pages -->
    </div>
<!-- views -->
</div>
<!-- views -->
<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
<script type="text/javascript">
var v_edit_flag = false;//是否有修改商品数量
//手动修改购买数量
function j_change_num(obj){
    var v_obj = $$(obj);
    var v_obj_val = parseInt(v_obj.val());
    var v_obj_val_max = parseInt(v_obj.attr('max'));
    var v_obj_val_min = parseInt(v_obj.attr('min'));
    v_edit_flag = true;
    if (v_obj_val <= v_obj_val_max && v_obj_val >= v_obj_val_min){
        return false;
    }
    
    v_obj_val = (v_obj_val > v_obj_val_max) ? v_obj_val_max : v_obj_val_min;  
    v_obj.val(v_obj_val);
};
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
    v_edit_flag = true;
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
    v_edit_flag = true;
    v_obj.val(v_obj_val-1);    
});
//全选/返选

//$$("input[type=checkbox][name=chk_all]").on('click', function(){
$$("#h_chk_all").on('click', function(){
    //var isChecked = $$(this).prop("checked");
    var isChecked = $$("input[type=checkbox][name=chk_all]").prop('checked');
    $$("input[type=checkbox][name='sub_id[]']").each(function(){
        if(!isChecked){
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
        $$("input[name='sub_id[]']:checked").each(function(){
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
	async:true,
        type: 'POST',
        success: function(result) {
            if (result.msg)
                myApp.alert(result.msg);
            if (result.err)
                return false;
            var goods_cnt = $$(".c_rec"+rec_id).parent().children("li").length;
            
	    $$(".c_rec"+rec_id).remove();
            $$("#h_total_price").html('￥'+result.total_price);
            $$("#h_total_num").html(result.total_num);
            if (goods_cnt <= 2) 
                location.href = '/cart';
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
    $$("input[name='sub_id[]']").each(function(){
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
                    //myApp.alert(result.msg, '');
                    $$("#err_tip").html(result.msg);
                }
                if (result.err)
                    return false;
                $$(".c_size"+d_rec_id).html($$(".sel_size").html());
                $$(".c_size"+d_rec_id).attr('data-subid', a_sub_id);

                var v_html = $$(".page-content .list-block.media-list").eq(0).html().replace(eval("/"+d_rec_id+"/g"),result.rec_id);
                $$(".page-content .list-block.media-list").eq(0).html(v_html);
                
                var v_html2 = $$(".page-content .list-block.media-list").eq(1).html().replace(eval("/"+d_rec_id+"/g"),result.rec_id);
                $$(".page-content .list-block.media-list").eq(1).html(v_html2);                
                myApp.closeModal('.picker-modal.modal-in');
            }
    });
}

//点击完成
$$('.open-popover').on('click', function () {
    //没有修改商品数量的话，直接切换至购物车默认页面
    if (v_edit_flag){
        var v_id_arr = new Array();
        var v_num_arr = new Array();
        $$("input[name='sub_id[]']").each(function(){
                var rec_id = $$(this).val();
                var num = parseInt($$("#qty_"+rec_id).val())||1;
                v_id_arr.push(rec_id);
                v_num_arr.push(num);
        });
        
        $$.ajax({
        url: '/cart/update_cart_batch?rnd='+new Date().getTime(),
        data: {rec_ids:v_id_arr.join("|"), nums:v_num_arr.join("|")},
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
                myApp.popover(popoverHTML, $$("#money_"+result.links.rec_id));
            }
            if (result.err)
                return false;

            location.href = '/cart';
        }
    });
        /*$$("input[type=checkbox][name='sub_id[]']").each(function(){
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
    } else {
        mainView.router.load({pageName: 'cart-index'});
    }
    
});
	$$("ul li:last-child").addClass('hu-cart-noline')
</script>
<?php include APPPATH."views/mobile/footer.php"; ?>
