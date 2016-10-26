<?php include APPPATH."views/mobile/header.php"; ?>

<div class="views">
<div class="view view-main" data-page="cart-checkout">
<div data-page="index" class="page public-bg no-toolbar page-on-center">
    <div class="yywtoolbar">
        <div class="yywtoolbar-inner row no-gutter">
            <div class="col-100  payment-hu"><a class="link" href="#" onclick="submit_cart();">确认报名</a></div>
        </div>
    </div>
    <!--navbar start-->
    <div class="navbar">
        <div class="navbar-inner">
            <div class="left"><a href="#" class="link icon-only history-back"><i class="icon back"></i></a></div>
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
			    <li style="color:#fff;"><span class="active-hu">1</span>确认信息</li>
			    <li><span>2</span>在线付款</li>
			    <li><span>3</span>报名成功</li>
			    <li><span>4</span>参加培训</li>
			    </ul>
		       </div>
		  </div>
	     <!--registration-procedure end-->  
	     
	     <!-- registration-information-list start -->
	        <ul class="two-col " style="margin-top:10px;">
		<?php $product_desc_additional = (!empty($product->product_desc_additional)) ? json_decode($product->product_desc_additional, true) : array();?>
		<li>
		<div class="row no-gutter hu-baomis">
		<div class="col-20 title" style="height:70px; padding-top:25px;">名称:</div>
		<div class="col-80 value" style="height:70px; padding:12px 10px 10px 10px; "><?=$product->brand_name . ' ' . $product->product_name?></div>
		</li>
		<li>
		<div class="row no-gutter hu-baomis">
		     <div class="col-20 title">老师:</div>
		     <div class="col-80 value"><?=$product->subhead?>&nbsp;</div>
		</div>
		</li>
		<li>
		<div class="row no-gutter hu-baomis">
		     <div class="col-20 title">类型:</div>
		     <div class="col-80 value"><?=$product->type_name?>&nbsp;</div>
		</div>
		</li>
		<li>
		<div class="row no-gutter hu-baomis">
		     <div class="col-20 title">时间:</div>
		     <div class="col-80 value"><?php echo date("y.m.d", strtotime($product->package_name));?> <?php if (isset($product_desc_additional['desc_waterproof'])) echo '-' . date("y.m.d", strtotime($product_desc_additional['desc_waterproof']))?></div>
		</div>
		</li>
		<li>
		<div class="row no-gutter hu-baomis">
		     <div class="col-20 title" style="height:70px; padding-top:25px;">地点:</div>
		     <div class="col-80 value" style="height:70px; padding:12px 10px 10px 10px; "><?php if(isset($product_desc_additional['desc_crowd'])) echo $product_desc_additional['desc_crowd']?>&nbsp;</div>
		</div>
		</li>
		<li>
		<div class="row no-gutter hu-baomis">
	   	     <div class="col-20 title">费用:</div>
		     <div class="col-80 value"><?=$product->product_price?>元/<?=$product->unit_name?></div>
		</div>
		</li>
		<li>
		<div class="row no-gutter hu-baomis">
		     <div class="col-20 title">客服:</div>
		     <div class="col-80 value"><?php if(isset($product_desc_additional['desc_expected_shipping_date'])) echo $product_desc_additional['desc_expected_shipping_date']?><span class="kefu-dh">|</span><span class="kefu-dh">手机号：<?php if(isset($product_desc_additional['desc_composition'])) echo $product_desc_additional['desc_composition']?></span></div>
		</div>
		</li>
		</ul>
	    <!-- registration-information-list end -->
	     
	    <!-- fill-registration-information start -->
	        <div class="two-col">
		    <div class="hu-bmxx-tit">填写报名信息</div>
		      <ul class="receiving-address">
                      <li>
                        <div class="edit-list">
                            <span class="hus-ren"></span>
			    <span class="downs hu-downs" data-id="<?php print $product->sub_id; ?>"></span>
                            <input type="text" onblur="j_change_num(this)" id="qty_<?php print $product->sub_id; ?>" min="1" max="<?=$product->sale_num?>" step="1" value="1" maxlength="5" class="hu-bm-sq">
                            <span class="hu-chengtuan"><i class="up ups" data-id="<?php print $product->sub_id; ?>"></i><em>人&nbsp;&nbsp;&nbsp;10以上报团,请联系客服</em></span>
			    
                        </div>
                    </li>
		    <li>
                        <div class="edit-list">
                            <div class="edit-user"><input type="text" id="consignee" placeholder="填写真实姓名"></div>
			</div>
                    </li>
		    
		    <li>
                        <div class="edit-list">
                            <div class="edit-user Phone-number-hu">
                                <div class="item-input item-input-field-noheight item-input-field"><input type="tel" id="mobile" placeholder="手机号码" value=""></div>
                            </div>
                        </div>
                    </li>
		    
		    <li>
                        <div class="edit-list">
                            <div class="edit-user youjian-hu">
                                <div class="item-input item-input-field-noheight item-input-field">
                                    <input type="email" id="email" placeholder="电子邮箱" value="">
                                </div>
                            </div>
                        </div>
                    </li> 
		    
		     <li>
                        <div class="edit-list">
                            <div class="edit-user icon-dizhi">
                                <div class="item-input item-input-field-noheight item-input-field">
                                    <input type="text" id="address" placeholder="详细地址" value="" class="">
                                </div>
                            </div>
                        </div>
                    </li>
		    
		    <li>
                        <div class="edit-list">
                            <div class="edit-user zhensuo-hu">
                                <div class="item-input item-input-field-noheight item-input-field">
                                    <input type="text" id="company" placeholder="单位" value="">
                                </div>
                            </div>
                        </div>
                    </li>
		    
		    <li>
                        <div class="edit-list">
                            <div class="edit-user duanxin-hu">
                                <div class="item-input item-input-field-noheight item-input-field">
                                    <input type="text" id="remark" placeholder="留言" value="">
                                </div>
                            </div>
                        </div>
                    </li>
		    
                   </ul>
		</div>
	<!-- fill-registration-information end -->
	     
	     
	     
	     
	     
	     
	     
	     </div>
           
        </div>
    </div>
                 
                 
</div>
</div>
</div>
<?php include APPPATH."views/mobile/common/footer-js.php"; ?>


<script type="text/javascript">
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
    var rec_id = $$(this).attr('data-id');
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
    var rec_id = $$(this).attr('data-id');
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

var last_cart_submit_time = 0;
var sub_id = '<?php print $product->sub_id; ?>';
var genre_id = '<?=$genre_id?>';
function submit_cart() {
    if(new Date().getTime() - last_cart_submit_time < 10000){
        myApp.alert('请不要重复提交', '');
        return false;
    }

    var num = $$("#qty_"+sub_id).val();
    var v_mobile = $$("#mobile").val();
    var v_consignee = $$("#consignee").val();
    var v_email = $$("#email").val();
    var v_address = $$("#address").val();
    var v_company = $$("#company").val();
    var v_remark = $$("#remark").val();
    if (v_mobile == ''){
        myApp.alert('请填写手机号', '');
        return false;
    }
    

    if (v_mobile.length != 11){
        myApp.alert('手机号不正确', '');
        return false;
    }
    
    var mobileReg = !!v_mobile.match(/^(13[0-9]|15[0-9]|17[678]|18[0-9]|14[57])[0-9]{8}$/);
    if (mobileReg == false){
        myApp.alert('手机号不正确', '');
        return false;
    }
    
    if (v_consignee == ''){
        myApp.alert('请填写真实姓名', '');
        return false;
    }

    // 收集数据，提交
    var data = {rnd:new Date().getTime(),sub_id:sub_id};
    data['num'] = num;
    data['mobile'] = v_mobile;
    data['consignee'] = v_consignee;
    data['email'] = v_email;
    data['address'] = v_address;
    data['company'] = v_company;
    data['remark'] = v_remark;
    last_cart_submit_time = new Date().getTime();
    $$.ajax({
        url:'/cart/proc_checkout_course',
        data:data,
        dataType:'json',
        type:'POST',
        success:function(result){
            last_cart_submit_time = 0;
            if (result.msg) myApp.alert(result.msg, '');
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
</body>
</html>