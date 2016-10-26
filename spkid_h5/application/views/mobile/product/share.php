<html>
	<head>
		<meta charset=utf-8>
		<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/animate.css?v=version')?>">
		<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/swiper.min.css?v=version')?>">
		<link rel="stylesheet" type="text/css" href="<?php echo static_style_url('mobile/css/tabs.css?v=version'); ?>" media="all"  charset="utf-8" />
		<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	</head>

	<body>
		<button onclick="WeiXinShareBtn();">share</button>
		<script>

			wx.config({
			    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
			    appId: "<?=$weixin_config['wx_appId']?>", // 必填，公众号的唯一标识
			    timestamp: <?=$weixin_config['wx_timestamp']?>, // 必填，生成签名的时间戳
			    nonceStr: "<?=$weixin_config['wx_nonceStr']?>", // 必填，生成签名的随机串
			    signature: "<?=$weixin_config['wx_signature']?>",// 必填，签名，见附录1
			    jsApiList: ['checkJsApi',
                'shareTimeline']
                // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
			});

			function WeiXinShareBtn() {
				alert(3333333);
			if (typeof WeixinJSBridge == "undefined") {
			alert ("请先通过微信搜索 wow36kr 添加 36 氪为好友，通过微信分享文章 :) ");
			} else {
			WeixinJSBridge.invoke('shareTimeline', {
			"title": "月牙网",
			"link": "http://m.yueyawang.com",
			"desc": "关注互联网创业",
			"img_url": "http://www.36kr.com/assets/images/apple-touch-icon.png"
			});
			}
			}
		</script>

	</body>
</html>
