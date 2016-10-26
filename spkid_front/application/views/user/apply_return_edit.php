<?php
/**
 * 申请退货修改页
 */
?>
<?php include APPPATH."views/common/header.php"; ?>
<script type="text/javascript" src="<?php print static_style_url('js/jcarousellite.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/lhgdialog/lhgdialog.min.js') ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/user.js'); ?>"></script>
<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />

<link rel="stylesheet" type="text/css" href="<?php print static_style_url('css/common_new.css'); ?>" media="all" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="<?php print static_style_url('css/layoutFlow.css'); ?>" media="all" charset="utf-8" />
<script type="text/javascript" src="<?php print static_style_url('js/jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/util.js?20131121083606'); ?>" ></script>
<link rel="stylesheet" type="text/css" href="<?php print static_style_url('css/order.css'); ?>" media="all" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="<?php print static_style_url('css/orderCN.css'); ?>" media="all" charset="utf-8" />

<div id="content">
    <div class="now_pos">
            <a href="/">首 页</a>
            >
            <a href="/user">会员中心</a>
            >
            <a class="now">自助退货</a>
    </div>
    <div class="ucenter_left">
    <?php include APPPATH."views/user/left.php"; ?>
    </div>
    <div class="ucenter_main">
<form action="/user/do_apply_return/<?=$order_info->order_id?>/<?=$apply_id?>" method="post" id="form_apply_return" enctype="multipart/form-data">
<div class="goodstab">
        <input type="hidden" name="provider_id" value="<?=$provider_id?>">
        <div class="goodsReturnBoxTop">
          <span class="f14">您的当前状态</span>&nbsp;&nbsp;&nbsp;
          <span class="red">您正在修改退货申请</span>
          <div>
              *温馨提示：1.如您选择的退货商品中有赠品，则必须一并加入到退货申请中；2.若您已经拒收订单，则无需填写退货申请;3.<font color="red">食品、内衣类商品一律不得退货，谢谢合作</font>。
            <a href="/help-13.html" target="_blank">更多规则查看</a>
          </div>
        </div>
        <div class="goodsReturnBox goodsReturnDetail" style="margin-top:10px;">
          <div class="goodsReturnInfo">
            订单号：<?=$order_info->order_sn?>(<?=$order_info->create_date?>)
          </div>
          <div class="goodsReturnTable">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <th width="8%">&nbsp;</th>
                <th width="15%">商品图片</th>
                <th width="37%">商品信息</th>
                <th width="10%">退货数量</th>
                <th width="30%">操作</th>
              </tr>
              <?php foreach($order_product as $product):?>
              <tr>
                <td colspan="5">
                  <div class="goodsReturnTd" style="width:8%">
                    <input type="checkbox" class="checkBox" name='chk_goods[]' 
                        <?=isset($product->return_info)?'checked':''?>
                            value='<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>'>
                  </div>
                  <div class="goodsReturnTd" style="width:15%">
                    <img src="<?=img_url($product->img_url .'.85x85.jpg')?>" width="63">
                  </div>
                  <div class="goodsReturnTd" style="width:37%">
                    <div class="goodsInfo">
                      <div><?=$product->brand_name?> <?=$product->product_name?></div>
                      <div>￥<?=$product->shop_price?></div>
                      <div>款号：<?=$product->product_sn?></div>
                      <div>
                        <span>颜色：<?=$product->color_name?></span>
                        <span>尺码：<?=$product->size_name?></span>
                      </div>
                    </div>
                  </div>
                  <div class="goodsReturnTd" style="width:10%">
                    <div class="selectNum" max_num="<?=$product->product_num?>">
                      <span class="minBtn">-</span>
                      <input readonly="true" value="<?=isset($product->return_info)?$product->return_info['product_number']:1?>" 
                            name="num_<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>"
                            class="divSelectNum"/>
                      <span class="addBtn">+</span>
                    </div>
                  </div>
                  <div class="goodsReturnTd" style="width:30%">
                    <div class="selectQul">
                        <select name='reason_<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>'
                                id='reason_<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>'
                                class="divSelectQul">
                          <?php foreach($apply_return_reason as $key=>$reason){?>
                              <option value="<?=$key?>"><?=$reason?></option>
                          <?php }?>
                        </select>
                        <script>
                    $("#reason_"+"<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>").val("<?=isset($product->return_info)?$product->return_info['return_reason']:''?>");
                  </script>
                    </div>
                  </div>
                  <div class="goodsReturnTd problemHide" style="width:100%;<?=isset($product->return_info)?'display:block':''?>">
                    <div class="goodsReturnProblem">
                      <span class="problemText">问题描述：</span>
                      <input type="text" class="problemInt" value="<?=(isset($product->return_info)&&!empty($product->return_info['description']))?$product->return_info['description']:'请您详细的说明您有问题的商品，这样方面我们能够更加直接迅速的处理您的退货'?>"
                            name="desc_<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>">
                      <a href="javascript:void(0)" class="imgUploadBtn">选择要上传的图片</a>
                      <input type="file" class="imgUpload" onchange="previewImage(this)" 
                            name='img_<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>_1'
                            id='img_<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>_1'>
                      <input type="file" class="imgUpload" onchange="previewImage(this)" 
                            name='img_<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>_2'
                            id='img_<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>_2'>
                      <input type="file" class="imgUpload" onchange="previewImage(this)"
                            name='img_<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>_3'
                            id='img_<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>_3'>
                      <input type="file" class="imgUpload" onchange="previewImage(this)"
                            name='img_<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>_4'
                            id='img_<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>_4'>
                      <input type="file" class="imgUpload" onchange="previewImage(this)"
                            name='img_<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>_5'
                            id='img_<?=$product->product_id?>_<?=$product->color_id?>_<?=$product->size_id?>_5'>
                      <div class="imgUploadText">每张图片大小不超过2M，支持JPG，png 格式，最多上传5张</div>
                      <div class="imgUploadDiv" value="<?=isset($product->return_info)?count($product->return_info['imgs']):0?>">
                      <?php
                        if(isset($product->return_info)){
                            $index=0;
                            foreach($product->return_info['imgs'] as $img){
                                $index++;
                                $del_id="del_img_{$product->product_id}_{$product->color_id}_{$product->size_id}_$index";
                                echo '<div class="imgBlock"><img width="23" height="26" onmouseover="orgImgShow(this,this.src)"
                                    onmouseout="orgImgHide(this)" 
                                    src="'.img_url($img).'">
                                    <div onclick="del_goods_img(this,\''.$del_id.'\',\''.$img.'\')">删除</div>
                                    </div>';
                            }
                        }
                      ?>
                      </div>
                      <div class="imgUploadOrg"></div>
                    </div>
                  </div>
                </td>
              </tr>
              <?php endforeach?>
              <tr>
                <td colspan="5" style="border-bottom:0;">
                  <div class="infoAddress">
                    退包寄回地址:<?=$return_address?>
                  </div>
                  <input type="hidden" name="return_address" value="<?=$return_address?>">
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="addressInfo">
        <dl>
          <dd style="height:40px">
            <div class="addressT">退包运单号：</div>
            <div id="addressSel">
                <select name="sel_shipping" onchange="shipping_change(this)" id="sel_shipping">
                    <option value="0">选择快递公司</option>
                    <option value="申通">申通</option>
                    <option value="圆通">圆通</option>
                    <option value="韵达">韵达</option>
                    <option value="全峰">全峰</option>
                    <option value="EMS">EMS</option>
                    <option value="顺丰">顺丰</option>
                    <option value="-1">其他</option>
                </select>
            </div>
            <input class="yanzheng" type="text" id="expressName"  name="shipping_name"
                val="输入快递名称" value="<?=$apply_return_info['shipping_name']?>" style="width:120px;display:none;">

            <input class="yanzheng" type="text" id="expressNumber" name="shipping_num"
                val="输入快递运单号" value="<?=$apply_return_info['invoice_no']?>">

            <input class="yanzheng" type="text" id="expressMoney" name="shipping_fee"
                val="输入运费" value="<?=(int)$apply_return_info['shipping_fee']?>" 
                onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" style="width:60px">

            <span id="msg1"></span>
            <span class="msgErr">准确地输入快递单号我们将更好地查询到你寄包裹的状态,将增加处理退货的时效!</span>
          </dd>
          <dd>
            <div class="addressT">寄件人姓名：</div>
            <input class="yanzheng" type="text" id="userNameInp" name="user_name"
                val="输入联系人姓名" value="<?=$apply_return_info['sent_user_name']?>">
            <span id="msg2"></span>
          </dd>
          <dd>
            <div class="addressT">寄件人手机：</div>
            <input class="yanzheng" type="text" id="userPhone" name="mobile"
                val="输入手机号码" value="<?=$apply_return_info['mobile']?>">
            <span id="msg3"></span>
          </dd>
          <dd>
            <div class="addressT">寄件人电话：</div>
            <input class="yanzheng" type="text" id="userTel" name="tel"
                val="输入电话号码" value="<?=$apply_return_info['tel']?>">
            <span id="msg4"></span>
          </dd>
        </dl>
      </div>
      <div id="submit" onclick="enterBtn()">提交</div>
      <div id="del_goods_img"></div>
</form>
</div>
</div>
<script type="text/javascript" src="<?= static_style_url('js/goodsReturn.js')?>"></script>
<script>
    var user_province="<?=$order_info->province_name?>";
    var order_sn="<?=$order_info->order_sn?>";

    /**
     * 删除图片 记录到隐藏域中
     */
    function del_goods_img(obj,del_id,img_name){
        var del_div=$("#del_goods_img")[0];
        var del_div_html=del_div.innerHTML;
        del_div_html+="<input type='hidden' name='"+del_id+"' value='"+img_name+"'>";
        del_div.innerHTML=del_div_html;
        //删除图片
        var div = obj.parentNode.parentNode.parentNode.children[9];
        var n = parseInt(div.getAttribute('value'));
        obj.parentNode.remove();
        div.setAttribute('value', --n);
        $('#'+obj.id.substring(4)).val("");
    }

    $(function(){
            //快递处理
            var sel_shipping=$("#sel_shipping")[0];
            for(var i=0;i<sel_shipping.options.length;i++){
                if(sel_shipping.options[i].value=="<?=$apply_return_info['shipping_name']?>"){
                    sel_shipping.options[i].selected=true;
                    break;
                }
            }
            
            if (sel_shipping.value==-1) {
                var express=$("#expressName")[0];
                express.style.display="block";
                express.value="<?=$apply_return_info['shipping_name']?>";
            }
         });
</script>
