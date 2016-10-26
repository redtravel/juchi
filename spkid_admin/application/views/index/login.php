<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<base href="<?php print base_url(); ?>" target="_self" />
  <script type="text/javascript" src="public/js/jquery.js"></script>
  <link rel="stylesheet" href="public/style/style.css" type="text/css" media="all" />
	<title>管理员登录</title>
</head>
<body bgcolor="#FAFEF0">
<form method="post" action="index/proc_login">
<div id="login">
  <div class="lt">演示站ERP管理系统</div>
  <div class="ld">
     <table width="704" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="214" align="right"><label for="admin_name">用户名</label></td>
    <td colspan="2"><input type="text" class="tl" name="admin_name" value="" /></td>
    </tr>
  <tr>
    <td align="right"><label for="admin_password">密码</label></td>
    <td colspan="2"><input type="password" name="admin_password" value="" class="tl" /></td>
    </tr>
    <?php if ($this->session->userdata('login_err_times') > 2): ?>
      <tr>
        <td align="right">验证码</td>
        <td width="130"><input type="text" class="tm" name="captcha" /></td>
        <td width="340"><img src="index/captcha?v=<?php print time();?>" onclick="this.src=$('base').attr('href')+'index/captcha?v='+new Date().getTime();" style="cursor:pointer;"></td>
      </tr>
    <?php endif ?>
  
  <tr>
    <td>&nbsp;</td>
    <td colspan="2"><input type="submit" class="bm" name="mysubmit" value="登 录" /></td>
    </tr>
</table>
  </div>
</div>
</form>
</body>
</html>
