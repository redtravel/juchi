
<?php if(isReqFromWechat()):?>
<?php
// 这里设置 appid secret
	$appId = '';
	$appsecret = '';

	$timestamp = time();
	$jsapi_ticket = make_ticket($appId,$appsecret);
	$nonceStr = make_nonceStr();
	$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$signature = make_signature($nonceStr,$timestamp,$jsapi_ticket,$url);

?>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript"></script>
<script>
	wx.config({
        debug: false,
        appId: '<?=$appId?>',
        timestamp: <?=$timestamp?>,
        nonceStr: '<?=$nonceStr?>',
        signature: '<?=$signature?>',
        jsApiList: [
            'checkJsApi',
            'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage',
	    'onMenuShareTimeline', 
	    'onMenuShareAppMessage'
          ]
       });
       
       wx.ready(function(){
       
		wx.onMenuShareTimeline({
		title: $$('title').text(),

		link: "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>",
		imgUrl: $$('.swiper-container img').length ? $$('.swiper-container img').attr('src') : $$('img').attr('src'),
		trigger: function (res) {
		console.log('');
		},
		success: function (res) {
		alert('十分感谢您对演示站的支持！！！');
		},
		cancel: function (res) {
        
		},
		fail: function (res) {
		console.log(JSON.stringify(res));
		}
		});	   
	    
	    wx.onMenuShareAppMessage({
	      title: $$('title').text(),
	      desc: $$('meta[name="Description"]').length ? $$('meta[name="Description"]').attr('content') :　$$('title').text(),
	      link: "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>",
	      imgUrl: $$('.swiper-container img').length ? $$('.swiper-container img').attr('src') : $$('img').attr('src'),
	      trigger: function (res) {
	        console.log('');
	      },
	      success: function (res) {
	        alert('十分感谢您对演示站的支持！！！');
	      },
	      cancel: function (res) {
        
	      },
	      fail: function (res) {
	        console.log(JSON.stringify(res));
	      }
	    });
	
	});


	
</script>	
<?php endif;?>

<?php include(APPPATH.'views/common/tongji.php'); ?>
</body>
</html>

