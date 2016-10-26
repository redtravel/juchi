<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<base href="<?php print base_url(); ?>" target="_self" />
	<title></title>
	<link rel="stylesheet" href="public/style/style.css" type="text/css" media="all" />
	<script type="text/javascript" src="public/js/jquery.js"></script>
	<script type="text/javascript">
		var base_url = '<?php print base_url(); ?>';
	</script>
</head>
<body>
	<div class="message">
		<table cellpadding=0 cellspacing=0 class="form" style="width:60%; margin:50px auto auto auto;" align="center" >
			<tr>
				<td class="topTd"></td>
			</tr>
			<tr class="row" >
				<th class="title_row">
				<?php if ($msg_type == 0): ?>
					信息
				<?php elseif ($msg_type == 1): ?>
					警告
				<?php else: ?>
					提示
				<?php endif; ?>
				</th>
			</tr>
			<tr class="row">
				<td class="message_row"><?php print $msg_detail?></td>
			</tr>
			<tr class="row">
				<td  class="jump_row">
					<?php echo $auto_redirect ? "如果您不做出选择，将在 <span id=\"spanSeconds\">10</span> 秒后跳转到第一个链接地址。" : '' ?>
					<ul style="margin:0; padding:0 10px" class="msg-link" id="link_list">
						<?php foreach ($links as $link): ?>
							<li style="display:block; clear:both;">
								<a href="<?php echo $link['href'] ?>" <?php echo isset($link['target']) ? $link['target'] : '' ?> >
								<?php echo $link['text'] ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</td>
			</tr>
			<tr>
				<td class="bottomTd"></td>
			</tr>

		</table>
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
		var seconds = 10;
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
				if (defaultUrl == 'javascript:history.go(-1)')
				{
					history.go(-1);
					return false;
				}
				if(defaultUrl.substr(0,4)!='http') defaultUrl = base_url+defaultUrl;
				location.href = defaultUrl;
			}
		}
		//-->
	</script>
	<?php endif; ?>
</body>
</html>
