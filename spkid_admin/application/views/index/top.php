<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php print base_url(); ?>" target="_self" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="public/style/style.css">
<script type="text/javascript" src="public/js/jquery.js"></script>
<script type="text/javascript">
$(function(){
    $(".cus_s").bind('click',function(){
      $.ajax({
        url:'support/switch_online',
        data:{rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        success:function(result){
          if (result.msg) {alert(result.msg)};
          if (result.err) {return false};
          if (result.status!=undefined) {
            $(".cus_s").removeClass('cus_on').removeClass('cus_off').addClass(result.status?'cus_on':'cus_off');
          };
        }
      });
    });
})
</script>
<title></title>
</head>

<body>
<div class="top">
  <div class="top_tl"><!--进样式By Rock<img src="public/images/logo.png" width="481" height="35" align="absmiddle">--></div>
  <div class="top_tr">
     <ul>
        <li>欢迎您，<?php print $admin_name?>！</li>
        <li class="cus_s <?php print $this->session->userdata('is_online')?'cus_on':'cus_off';?>">&nbsp;</li>
        <li class="refresh" onclick="javascript:window.top.frames['mainFrame'].document.location.reload();"><a>刷新</a></li>
        <li class="home"><a href="<?php print front_url();?>" target="_blank">网站首页</a></li>
        <li class="t_edit"><a href="index/change_password" target="mainFrame">修改密码</a></li>
        <li class="exit"><a href="index/logout" target="_parent">退出</a></li>
     </ul>
  </div>
  <div class="l_title"><!--进样式By Rock<img src="public/images/l_title.png" height="32" />--></div>
</div>
</body>
</html>