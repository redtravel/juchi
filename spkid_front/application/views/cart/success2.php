<?php include APPPATH."views/common/header2.php"; ?>
<script src="<?php echo static_style_url('pc/js/qrcode.js?v=version')?>" type="text/javascript"></script>
<!--pay start-->
<div class="cart-wrapper">
     <div class="gwc-wrapper">
            <div class="home-wrapper pay-scuss">
            <?php 
                $pay_id = 0;
                foreach($order_list as $id => $order): 
                    if(!$pay_id) $pay_id = $order->pay_id;
            ?>                
                <div class="pay-lb clearfix">
                     <div class="order-collect clearfix">
                          <div class="tip-box">
                               <h3 class="title">订单提交成功，请尽快支付！</h3>
                               <p class="tip">请在<strong class="houer">24</strong>小时内完成支付，否则订单将被自动取消</p>
                          </div>
                          <div class="money">
                                <div class="should">应付总额：<strong class="highlight">¥<?=$order->order_price-$order->paid_price?></strong></div>
                                <div class="ordersn">
                                   订单编号：<strong><?=$order->order_sn?></strong>
                                    <span class="toggleshow">
                                         <span class="showtext" onclick="openShutManager(this,'goods-detail',false,'收起','展开')">收起</span>
                                    </span>
                               </div>
                          </div>
                    </div>
                    
                    <div id="goods-detail" class="goods-detail">
                         <div class="pay-buy clearfix"><span class="fl-left">购买商品：</span>
                             <span class="fl-left">
                                 <?php foreach($order->product_list as $k => $product): ?>
                                 <?=$k+1?>.<?=$product->product_name?><br/>
                                 <?php endforeach; ?>                             
                             </span>
                         </div>
                         <p class="pay-ad">收货地址：<span><?=$order->province_name?></span><span><?=$order->city_name?></span><span><?=$order->district_name?></span><span><?=$order->address?></span><span><?=$order->consignee?></span><span><?=$order->mobile?></span></p>
                    </div>               
                </div>
                <?php endforeach; ?>
                <div class="platform">
                    <div class="title">请选择支付平台</div>
                    <ul class="clearfix">
                    <?php foreach ($pay_list as $p): ?>
                    <li class="type"><span class="i-radio<?php if($alipay_pay_id == $p->pay_id): ?> checked<?php endif; ?>" data-id="<?=$p->pay_id?>"></span><img src="<?php print img_url($p->pay_logo); ?>" /></li>
                    <?php endforeach ?>
                    </ul>
                    <a href="#" class="submit-button fl-left" onclick="j_order_pay('<?php print implode('-', array_keys($order_list)); ?>');">立即支付</a>
                </div>

               <div class="weixinpay" style="display: none;">
                    <div class="title"><img src="<?php echo static_style_url('pc/images/weixinpay.jpg')?>" width="108"></div>
                    <div id="qrcode" class="qrcode">
                        <?php
                        //商户根据实际情况设置相应的处理流程
                        if ($wx_result["return_code"] == "FAIL") 
                        {
                                //商户自行增加处理流程
                                echo '<i class="fa fa-times fa-4x" style="color:red"></i>' . "通信出错：".$wx_result['return_msg']."<br>";
                        }
                        elseif($wx_result["result_code"] == "FAIL")
                        {
                                //商户自行增加处理流程
                                echo '<i class="fa fa-times fa-4x" style="color:red"></i>' . "错误代码：".$wx_result['err_code']."<br>";
                                echo "错误代码描述：".$wx_result['err_code_des']."<br>";
                        }
                        elseif($wx_result["code_url"] != NULL)
                        {
                                //从统一支付接口获取到code_url
                                $code_url = $wx_result["code_url"];
                                //商户自行增加处理流程
                                //......
                        }
                        ?>
                    </div>
                    <div id="return_msg" align="center"></div>
                    <p class="tip">请使用微信扫描二维码以完成支付</p>
                    <div class="back"><span>&lt;</span>选择其他支付方式</div>
                </div>    
           </div>
        
     
     </div>
</div>
<!--pay end-->

<!-- 支付弹层1开始 -->
<div id="pay-box1" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header v-close">
              <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">请在新打开的支付平台页面进行支付<br>支付完成前请不要关闭该窗口</h4>
          </div>
          <div class="modal-body v-button">
              <button class="btn btn-lg btn-blue pay_success" type="submit">支付成功</button>
              <button class="btn cancel pay_question" type="submit">遇到问题？</button>
          </div>
        </div>
      </div>
</div>
<!-- 支付弹层1结束 -->

<!-- 支付弹层2开始 -->
<div id="pay-box2" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header v-close">
              <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">支付遇到问题，您可以：<br>1、演示站帮助中心  客服电话：400-9905-920<br>2、支付宝帮助中心  客服电话：95188</h4>
          </div>
        </div>
      </div>
</div>
<!-- 支付弹层2结束 -->
<script>

    <?php if(!empty($wx_result["code_url"])){ ?>
            var url = "<?php echo $code_url;?>";
            //参数1表示图像大小，取值范围1-10；参数2表示质量，取值范围'L','M','Q','H'
            var qr = qrcode(10, 'H');
            qr.addData(url);
            qr.make();
            var code = qr.createImgTag();
            $("#qrcode").html(code);
    <?php } ?>
    //var v_track_sn = '<?php echo $pay_track->track_sn?>';
    var v_track_sn = '<?php echo $pay_track->order_ids?>';
    var v_weixin_pay_id = '<?=$weixin_pay_id?>';
    var _oResetTimeout, reportCount = 0, _requestTimeout = 1000;
    function _poll() {
        var pay_id = $(".platform li span.checked").attr('data-id');
        if (pay_id != v_weixin_pay_id) return;
        var _self = arguments.callee;
        
        $.ajax({
                type:"GET",
                url:"<?=FRONT_HOST?>/pay/wxpay_check/"+v_track_sn,		

                cache:false,
                timeout: 100000,
                success:function(data){
                        if(data == 'success') {
                                $('#return_msg').html('<center><i class="fa fa-check fa-2x" style="color:green"></i><strong>您已经成功支付！！！</strong></center>');
                                clearTimeout(_oResetTimeout);
                        } else {
                                reportCount++;
                                if (reportCount > _requestTimeout) {
                                        $('#return_msg').html('<center><i class="fa fa-times fa-2x" style="color:red"></i><strong>支付发生问题请查看<a href="pc.redtravel.cn/center/order.html">订单支付清单</strong></center>');
                                        clearTimeout(_oResetTimeout);		
                                } else {
                                        _oResetTimeout = setTimeout(_self, 30000);
                                }
                        };
                },
                error:function(){
                        reportCount ++;
                        if (reportCount > _requestTimeout) {
                            $('#return_msg').html('<center><i class="fa fa-times fa-2x" style="color:red"></i><strong>通信发生错误</strong></center>');
                        } else {
                            _oResetTimeout = setTimeout(_self, 30000);
                        }
                }
        });
    }
    
    

    $(".platform li span").click(function(){
        $(".platform li span").removeClass('checked');
        $(this).addClass('checked');
        var pay_id = $(this).attr('data-id');
        if (pay_id == v_weixin_pay_id) {
            $(".platform").hide();
            $(".weixinpay").show();
            _poll();
        }
    });
    
    $(".back").click(function(){
        $(".platform").show();
        $(".weixinpay").hide();
        $(".platform li span").removeClass('checked');
        $(".platform li span:first").addClass('checked');
    });
    //点击支付成功，跳至订单管理中心
    $(".pay_success").click(function(){
        window.location.href='/order/index';
    });
    //点击支付遇到问题
    $(".pay_question").click(function(){
        $('#pay-box1').modal('hide');
        $('#pay-box2').modal('show');
    });
    //点击立即支付
    function j_order_pay(p_order_ids){
        $('#pay-box1').modal('show');
        var pay_id = $(".platform li span.checked").attr('data-id');
        //window.location.href="/order/pay/"+p_order_ids+"/"+pay_id;
        window.open("/order/pay/"+p_order_ids+"/"+pay_id);
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
</script>
<?php include APPPATH.'views/common/footer.php'; ?>