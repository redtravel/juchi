<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width" />
<title>演示站</title>
<meta name="keywords" content=""/> 
<meta name="description" content=""/>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="black" name="apple-mobile-web-app-status-bar-style" />
<meta name="format-detection" content="telephone=no" />
<meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport" />
<script type="text/javascript" src="<?php echo static_style_url('mobile/tuan/jquery-1.4.4.min.js?v=version')?>"></script>
</head>
<body>
	

<div id="content" style="margin: 20px auto 50px auto; width:100%; overflow: hidden">
    
    <div style="margin-left:20px;">
        <h1 style="font-size: 16px; margin-bottom: 20px;">
            <?php if ($msg_type == 0): ?>
				信息
			<?php elseif ($msg_type == 1): ?>
				出错啦！
			<?php else: ?>
				提示
			<?php endif; ?>
        </h1>
        <div id="link_list" style="margin-bottom: 20px;">
            <?php foreach ($links as $link): ?>
                <li style="display:block; clear:both;">
                    <a href="<?php echo $link['href'] ?>" <?php echo isset($link['target']) ? $link['target'] : '' ?> >
                        <?php echo $link['text'] ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </div>
        <div id="redirectionMsg">
            如果您不做出选择，将在 <span id="spanSeconds">3</span> 秒后跳转到第一个链接地址。
        </div>
    </div>
 
<div class="cl"></div>  
</div>
	<script type="text/javascript">
	$(function(){
		$('#link_list a').each(function(){
			if($(this).attr('href').substr(0,11)=='javascript:'){
				$(this).attr('onclick',$(this).attr('href'));
				$(this).attr('href','javascript:void(0);');
			}
		});
	});
	</script>
	<?php if ($auto_redirect): ?>
	<script language="JavaScript">
		<!--
		var seconds = 3;
		var defaultUrl = "<?php echo $default_url ?>";

		onload = function()
		{
			if (defaultUrl == 'javascript:history.go(-1)' && window.history.length == 0)
			{
				document.getElementById('redirectionMsg').innerHTML = '';
				return;
			}

			window.setInterval(redirection, 1000);
		}
		function redirection()
		{
			if (seconds <= 0)
			{
				window.clearInterval();
				return;
			}

			seconds --;
			$('#spanSeconds').html(seconds);

			if (seconds == 0)
			{
				window.clearInterval();
				if(defaultUrl == 'javascript:history.go(-1)'){
					avascript:history.go(-1);
				}else if(defaultUrl.substr(0,7)!='http://'){
					location.href = base_url+defaultUrl;
				}else{
					location.href = defaultUrl;
				}
				
			}
		}
		//-->
	</script>
	<?php endif; ?>
</body>
</html>