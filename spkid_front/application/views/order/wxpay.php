<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv=Content-Type content="text/html;charset=utf-8">
<title>演示站首页</title>
<link href="<?php echo static_style_url('pc/css/bootstrap.css?v=version')?>" rel="stylesheet" type="text/css" media="all">
<link href="<?php echo static_style_url('pc/css/common.css?v=version')?>" rel="stylesheet" type="text/css">
<link href="<?php echo static_style_url('pc/css/main.css?v=version')?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo static_style_url('pc/js/common.js?v=version')?>"></script>
<script src="<?php echo static_style_url('pc/js/search.js?v=version')?>" type="text/javascript"></script>
<script src="<?php echo static_style_url('pc/js/home.js?v=version')?>" type="text/javascript"></script>
<script src="<?php echo static_style_url('pc/js/bootstrap.min.js?v=version')?>" type="text/javascript"></script>
<script src="<?php echo static_style_url('pc/js/comm_tool.js?v=version')?>" type="text/javascript"></script>
<script src="<?php echo static_style_url('pc/js/qrcode.js?v=version')?>" type="text/javascript"></script>
</head>

<body>
<!--pay start-->
<div class="cart-wrapper">
     <div class="gwc-wrapper">
            <div class="home-wrapper pay-scuss">
            <?php 
                $pay_id = 0;
                foreach($order_list as $id => $order): 
                    //if(!$pay_id) $pay_id = $order->pay_id;
            ?>                
                <div class="pay-lb clearfix">
                     <div class="order-collect clearfix">
                         <div class="tip-box">
                               <h3 class="title">订单编号：<?=$order->order_sn?></h3>
                               <!--
                               <p class="tip">请在<strong class="houer">24</strong>小时内完成支付，否则订单将被自动取消</p>
                               -->
                          </div>
                          <div class="money">
                                <div class="ordersn">
                                    <!--
                                   订单编号：<strong><?=$order->order_sn?></strong>
                                    -->
                                    <span class="toggleshow">
                                         <span class="showtext">应付总额：<strong class="highlight">¥<?=$order->order_price+$order->shipping_fee-$order->paid_price?></strong></span>
                                    </span>
                               </div>
                          </div>
                    </div>           
                </div>
                <?php endforeach; ?>

               <div class="weixinpay">
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
                </div>    
           </div>
        
     
     </div>
</div>
<!--pay end-->
</body>
</html>
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
    //var v_weixin_pay_id = '<?=$weixin_pay_id?>';
    var _oResetTimeout, reportCount = 0, _requestTimeout = 1000;
    function _poll() {
        //var pay_id = $(".platform li span.checked").attr('data-id');
        //if (pay_id != v_weixin_pay_id) return;
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
    
    _poll();
</script>
