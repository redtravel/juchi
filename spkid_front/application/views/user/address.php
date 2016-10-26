<?php include APPPATH . 'views/common/user_header.php'?>
<style type="text/css">
  .v-pov > .modal-dialog {
    margin: 130px auto;
  }
  .v-add-pov > .modal-dialog {
    margin: 300px auto;
  }
  .v-add-top {
    padding-top:30px;
  }
</style>                     
            <div class="personal-center-right">
                <h1 class="order-details-bt">收货地址</h1>
                <div class="shipping-address">
                    <div class="addr-title">
        		            <a <?php if(count($address_list) >= 20) { echo 'href="#limit-box"';}else{ echo 'href="#add-box"';}?> class="add-addr btn-small btn-primary btn" data-toggle="modal" data-container="body">添加新地址</a>
                        <p class="tips">您已添加<span class="own"><?php echo count($address_list)?></span>个地址，还可以添加<span class="surplus"><?php echo 20 - count($address_list)?></span>个</p>
        	          </div>                         
                    <?php foreach ($address_list as $a) :?>
                    <?php if($a->is_used):?>                        
                    <div class="addr-list default">
        			          <ul class="clearfix">
                            <li><span class="addr-title2"><em class="space-name">收货</em>人：</span><span class="name"><?php echo $a->consignee?></span></li>
        				            <li><span class="addr-title2">手机号码：</span><span class="tel"><?php echo $a->mobile?></span></li>
        				            <li class="clearfix"><span class="addr-title2">收货地址：</span><span class="area"><?php echo $a->province_name.'  '.$a->city_name.$a->district_name.$a->address;?></span></li>
        			          </ul>
			                  <div class="operate operate2">
                            <a class="modify-addr modify" onclick="v_address_form(<?php print $a->address_id ?>)">修改</a><span class="separate"></span>
                            <a href="#del-box" class="del" address-id="<?php echo $a->address_id?>">删除</a>
                        </div>
                        <span class="current">默认地址</span>
                    </div>
                    <?php else:?>
                    <div class="addr-list">
        			          <ul class="clearfix">
                            <li><span class="addr-title2"><em class="space-name">收货</em>人：</span><span class="name"><?php echo $a->consignee?></span></li>
        				            <li><span class="addr-title2">手机号码：</span><span class="tel"><?php echo $a->mobile?></span></li>
        				            <li class="clearfix"><span class="addr-title2">收货地址：</span><span class="area"><?php echo $a->province_name.'  '.$a->city_name.$a->district_name.$a->address;?></span></li>
        			          </ul>
        			          <div class="operate">
                            <a class="modify-addr modify" onclick="v_address_form(<?php print $a->address_id ?>)">修改</a><span class="separate"></span>
                            <a href="#del-box" class="del" address-id="<?php echo $a->address_id?>">删除</a><span class="separate"></span>
                            <a href="javascript:void(0);" class="setdefault base-addr">设为默认</a>
                        </div>
                    </div>
                    <?php endif;?>                                   
                    <?php endforeach;?>
                </div>         
            </div>
        </div>
    </div>
</div>
<!--删除收货地址-->
<div id="del-box" class="modal v-pov v-add-pov" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header v-close">
                <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title v-add-top">你确定要删除所选地址吗?</h4>
            </div>
            <div class="modal-body v-button">
                <button class="btn btn-lg btn-blue confirm" type="submit">确定</button>             
                <button class="btn cancel" type="submit" data-dismiss="modal">取消</button>              
            </div>
        </div>
    </div>
</div>
<!--限制增加收货地址-->
<div id="limit-box" class="modal v-pov v-add-pov" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header v-close">
                <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title v-add-top">收货地址已满，请修改或删除收货地址</h4>
            </div>
            <div class="modal-body v-button">
                <button class="btn btn-lg btn-blue" type="submit" data-dismiss="modal">确定</button>             
                <button class="btn cancel" type="submit" data-dismiss="modal">取消</button>              
            </div>
        </div>
    </div>
</div>
<!--新增收货地址-->
<div id="add-box" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modify-address">
        <div class="modal-content">
            <div class="modal-header v-close">
                <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title xiugai">新增地址</h4>
            </div>
            <div class="modal-body">
                <div class="modify-dizhi form clearfix">
                    <ul>
                        <form class="address_info">
                            <input type="hidden" value="" name="address_id" id="address_id">
                            <li class="clearfix">
                                <label class="text-label"><i>*</i><span class="addr-title">收货地区：</span></label>
                                <div class="fl-left">
                                    <select name="province" id="province" onchange="return change_region(1,this.value,'city')" class="combobox">
                                        <option value="" selected="selected">-省/直辖市-</option>
                                        <?php foreach ($province as $pro_val) { ?>
                                        <option value="<?php echo $pro_val->region_id;?>"><?php echo $pro_val->region_name;?></option>
                                        <?php } ?>
                                    </select>
                                    <select name="city" id="city"  onchange="return change_region(2,this.value,'district')"  class="combobox" >
                                        <option value="" selected="selected">-市-</option>
                                    </select>
                                    <select name="district" id="district" class="combobox last">
                                        <option value="" selected="selected">-区/县-</option>
                                    </select>                              
                                </div>
                                <div class="v-add">
                                    <span id="address_err" class="v-add-province">不能为空</span>
                                </div>
                            </li>
                            <li>
                                <label class="text-label"><i>*</i><span class="addr-title">详细地址：</span></label>
                                <span class="err_tip" id="address_err" style="bottom:0px;">不能为空</span>
                                <textarea rows="10" cols="30" class="addrDetail text-filed" id="address" name="address"></textarea>
                            </li>
                            <li>
                                <label class="text-label"><i>*</i><span class="addr-title"><em class="space-name">收货</em>人：</span></label>
                                <span class="err_tip" id="address_err">不能为空</span>
                                <input type="text" class="text-filed" id="consignee" name="consignee">
                            </li>
                          
                            <li>
                                <label class="text-label"><i>*</i><span class="addr-title">手机号码：</span></label>
                                <span class="err_tip v-add-mobile" id="address_err">格式不对</span>
                                <input type="text" class="text-filed" id="mobile" name="mobile">
                            </li>           
                            <li class="addr-default">
                                <!-- <span class=" i i-checkbox" id="iscurrent"></span> -->
                                <input type="checkbox" name="is_used">
                                <label for="setDefault">设为默认地址</label>
                            </li>
                            <div class="operate">
                                <button class="save-addr btn-primary btn-small btn" type="button">保存</button>
                            </div>
                        </form>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!--修改收货地址-->
<div id="edit-box" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">

</div>
<script>
$(function(){
    //删除
    var address_id;
    $('a.del').click(function(e){
        e.preventDefault();
        var popbox = $($(this).attr('href'));
        popbox.modal('show');
        address_id = $(this).attr('address-id');
    })
    $('.btn.confirm').click(function(){
        $.getJSON('/address/address_delete', {address_id:address_id}, function(data){
            if (0 == data.error){
                location.href = '/user/address.html?v='+Math.random();
            }else{
                location.href = '/user/address.html?v='+Math.random();
            }
        });
    })

    //设置默认
    $('.setdefault').click(function(e){
        e.preventDefault();
        address_id = $(this).siblings('.del').attr('address-id');
        $.getJSON('/address/address_default', {address_id:address_id}, function(data){
            if (0 == data.error){
                location.href = '/user/address.html?v='+Math.random();
            }
        });
    })
    
    //判断
    var is_ok = true;
    var mobile = /^(1[0-9]{10})$/;
    $('#district').click(function(){
        $('.v-add-province').css("display","none");
        is_ok = true;
    });
    $('.address_info input').click(function(){
        $(this).parent().find('.err_tip').css("display","none");
        is_ok = true;
    });
    $('#address').click(function(){
        $(this).parent().find('.err_tip').css("display","none");
        is_ok = true;
    });
    
    //新增
    $('.save-addr').click(function(){
        $('#consignee').each(function(){
            if ($(this).val() == '') {
                is_ok = false;
                $(this).siblings('.err_tip').css("display","block");
            }
        });
        $('.address_info textarea').each(function(){
            if ($(this).val() == '') {
                is_ok = false;
                $(this).siblings('.err_tip').css("display","block");
            };
        });
        $('.address_info select').each(function(){
            if ($(this).val() == '') {
                is_ok = false;
                $('.v-add-province').css("display","block");
            }
        });
        if ( $('#mobile').val() == '' || !mobile.test($('#mobile').val())) {
            is_ok = false;
            $('.v-add-mobile').css("display","block");
        }
        if (is_ok) {
            $.ajax({url:'/address/address_check', data:$('.address_info').serialize(), method:'POST', dataType:'json', success:function(data){
                if (data.error == 0 ){    
                    location.href = '/user/address.html?v='+Math.random();
                }else if(data.error == 1 ){
                    alert(msg);
                }
            }});
        } else {
            return false;
        }
    });

    
});

//选择省市
function change_region(type,value,are){
    if(type == 1){
        $('select[name=city]')[0].options.length = 1;
        $('select[name=district]')[0].options.length = 1;
    }
    if(type == 2){
        $('select[name=district]')[0].options.length = 1;
    }
    $.ajax({url: '/address/ajax_region',async:false,dataType: "json",data: {type:type,parent_id:value},success:function(msg){
        for(i in msg.list){
            $('select[name='+are+']')[0].options.add(new Option(msg.list[i].region_name , msg.list[i].region_id));
        }

    }});
}

//修改省市
function edit_change_region(type,value,are){
    if(type == 1){
        $('select[name=edit-city]')[0].options.length = 1;
        $('select[name=edit-district]')[0].options.length = 1;
    }
    if(type == 2){
        $('select[name=edit-district]')[0].options.length = 1;
    }
    $.ajax({url: '/address/ajax_region',async:false,dataType: "json",data: {type:type,parent_id:value},success:function(msg){
        for(i in msg.list){
            $('select[name='+are+']')[0].options.add(new Option(msg.list[i].region_name , msg.list[i].region_id));
        }
    }});
}

function v_address_form (address_id) {
    $.ajax({
        url:'/address/v_address_form',
        data:{address_id:address_id,rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        success:function(result){
            if (result.msg) alert(result.msg);
            if (result.err) return false;
            if(result.html) {
                $('#edit-box').html(result.html).modal('show');
            }
        },
        error:function(xhr, status) {
          console.log(xhr);
          console.log(status);
        },   
        complete:function() {

        }
    });
}
function v_edit_address(){
    $.ajax({url:'/address/address_check', data:$('.v_edit_info').serialize(), method:'POST', dataType:'json', success:function(data){
        if (data.error == 0 ){    
            location.href = '/user/address.html?v='+Math.random();
        }else if(data.error == 1 ){
            alert(msg);
        }
    }});
}


</script>
<?php include APPPATH . 'views/common/footer.php'?>
