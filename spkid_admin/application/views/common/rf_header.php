<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
    <script type="text/javascript" src="/public/js/jquery.js"></script>
    <link rel="stylesheet" href="/public/style/rf_style.css" type="text/css" media="all" />
	<title>RF系统</title>
</head>
<body>
   <div id="div_menu">
       <div class="rfMenuBox">
       <a class="rfMainMenu <?if (isset($cur_menu)&&$cur_menu=='index'):echo 'cur_menu';endif;?>" href="/rf/index">首页</a>|
       <a class="rfMainMenu <?if (isset($cur_menu)&&$cur_menu=='in'):echo 'cur_menu';endif;?>" href="/rf/in">入库</a>|
       <a class="rfMainMenu <?if (isset($cur_menu)&&$cur_menu=='out'):echo 'cur_menu';endif;?>" href="/rf/out">出库</a>|
       <a class="rfMainMenu <?if (isset($cur_menu)&&$cur_menu=='pan'):echo 'cur_menu';endif;?>" href="/rf/pan">盘点</a>|
       <a class="rfMainMenu" href="/rf/login_out">退出</a>
   </div>
<!--<br style="clear:all; height:10px; line-height:10px;"/>-->
<div id="content">
