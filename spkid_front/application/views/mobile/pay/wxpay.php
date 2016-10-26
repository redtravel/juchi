<?php

/**
 * JS_API支付
 * ====================================================
 * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。
 * 成功调起支付需要三个步骤：
 * 步骤1：网页授权获取用户openid
 * 步骤2：使用统一支付接口，获取prepay_id
 * 步骤3：使用jsapi调起支付
*/

	header("Content-type:text/html;charset=utf-8");
	
	
?>

<!doctype html>
<html class="no-js">
  <head>
    <meta charset="utf-8">
    <title>演示站</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-sclae=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="description" content="">
    <meta name="keywords" content="">

    

    <style type="text/css">
	.am-form-field { padding: 0.1em; text-align: center; }
	.am-input-group-label { padding: 0.1em 0.5em; cursor: pointer; }
	.item img{
			width:120px;
			height:40px;
			vertical-align:middle;
			margin-left:2px;
		}
	</style>
  </head>
  
  <body>



    

    <div class="am-modal am-modal-loading am-modal-no-btn" tabindex="-1" id="my-modal-loading">
      <div class="am-modal-dialog">
        <div class="am-modal-hd">正在载入...</div>
        <div class="am-modal-bd">
          <span class="am-icon-spinner am-icon-spin"></span>
        </div>
      </div>
    </div>
    
    <div data-am-widget="list_news" class="am-list-news am-list-news-default">
  <!--列表标题-->
  <div class="am-list-news-hd am-cf">
    <!--带更多链接-->
    <a href="##">
      <h2>微信支付</h2>      
    </a>
  </div>
  <div class="am-list-news-bd">
    <ul class="am-list">
    <li class="am-g am-list-item-dated">
        
	<a href="##" class="am-list-item-hd ">商　品：<?php echo $goodsTitle?></a>
        
      </li>
      <li class="am-g am-list-item-dated">
        
	<a href="##" class="am-list-item-hd ">订单号：<?php echo $out_trade_no_1?></a>
        
      </li>
      <li class="am-g am-list-item-dated">
        <a href="##" class="am-list-item-hd ">金　额：<?php echo $total_fee?>元</a>
        
      </li>
      
      <li class="am-g am-list-item-dated">
        <div align="center">
		<button style="margin:10px 0; width:210px; height:30px; background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >确认支付</button>
	</div>
        
        <div align="center">
		<button style="margin:10px 0; width:210px; height:30px; background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="location.href='http://m.yueyawang.com'" >返回首页</button>
		</div>
      </li>
      
    </ul>
  </div>
</div>





   <script type="text/javascript">

		//调用微信JS api 支付
		function jsApiCall()
		{
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				<?php echo $jsApiParameters; ?>,
				function(res){
					WeixinJSBridge.log(res.err_msg);
					//alert(res.err_code+res.err_desc+res.err_msg);
				}
			);
		}

		function callpay()
		{
			
			if (typeof WeixinJSBridge == "undefined"){
			    if( document.addEventListener ){
			        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			    }else if (document.attachEvent){
			        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
			        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			    }
			}else{
			    jsApiCall();
			}
		}

		
	</script>
  </body>
</html>
